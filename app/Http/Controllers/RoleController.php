<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Barryvdh\Debugbar\Facades\Debugbar;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        Debugbar::info('Roles listing requested');
        
        $roles = Role::with('permissions')->get();
        
        return response()->json([
            'roles' => $roles,
            'total' => $roles->count()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|unique:roles,name',
            'permissions' => 'array'
        ]);

        $role = Role::create(['name' => $request->name]);
        
        if ($request->has('permissions')) {
            $role->syncPermissions($request->permissions);
        }

        Debugbar::info('Role created: ' . $role->name);

        return response()->json([
            'message' => 'Role created successfully',
            'role' => $role->load('permissions')
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Role $role): JsonResponse
    {
        return response()->json([
            'role' => $role->load('permissions')
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Role $role): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|unique:roles,name,' . $role->id,
            'permissions' => 'array'
        ]);

        $role->update(['name' => $request->name]);
        
        if ($request->has('permissions')) {
            $role->syncPermissions($request->permissions);
        }

        Debugbar::info('Role updated: ' . $role->name);

        return response()->json([
            'message' => 'Role updated successfully',
            'role' => $role->load('permissions')
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Role $role): JsonResponse
    {
        if ($role->name === 'admin' || $role->name === 'user') {
            return response()->json([
                'message' => 'Cannot delete default roles'
            ], 403);
        }

        $role->delete();

        Debugbar::info('Role deleted: ' . $role->name);

        return response()->json([
            'message' => 'Role deleted successfully'
        ]);
    }

    /**
     * Get all permissions
     */
    public function permissions(): JsonResponse
    {
        $permissions = Permission::all();
        
        return response()->json([
            'permissions' => $permissions
        ]);
    }
}
