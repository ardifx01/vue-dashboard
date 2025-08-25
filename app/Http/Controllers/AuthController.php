<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    /**
     * Create user
     *
     * @param  [string] name
     * @param  [string] email
     * @param  [string] password
     * @param  [string] password_confirmation
     * @return [string] message
     */
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|string|unique:users',
            'password' => 'required|string',
            'c_password' => 'required|same:password'
        ]);

        $user = new User([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        if ($user->save()) {
            $tokenResult = $user->createToken('Personal Access Token');
            $token = $tokenResult->plainTextToken;

            // Get user abilities/permissions for frontend
            $userAbilityRules = $this->getUserAbilities($user);

            return response()->json([
                'message' => 'Successfully created user!',
                'accessToken' => $token,
                'userData' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'avatar' => $user->avatar,
                    'role' => $user->getRoleNames()->first() ?? 'user',
                ],
                'userAbilityRules' => $userAbilityRules,
            ], 201);
        } else {
            return response()->json(['error' => 'Provide proper details'], 400);
        }
    }

    /**
     * Login user and create token
     *
     * @param  [string] email
     * @param  [string] password
     * @param  [boolean] remember_me
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
            'remember_me' => 'boolean'
        ]);

        $credentials = request(['email', 'password']);
        
        if (!Auth::attempt($credentials)) {
            return response()->json([
                'message' => 'Unauthorized',
                'errors' => [
                    'email' => ['Invalid email or password']
                ]
            ], 401);
        }

        $user = $request->user();
        $tokenResult = $user->createToken('Personal Access Token');
        $token = $tokenResult->plainTextToken;

        // Get user abilities/permissions for frontend
        $userAbilityRules = $this->getUserAbilities($user);

        return response()->json([
            'accessToken' => $token,
            'token_type' => 'Bearer',
            'userData' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'avatar' => $user->avatar,
                'role' => $user->getRoleNames()->first() ?? 'user',
            ],
            'userAbilityRules' => $userAbilityRules,
        ]);
    }

    /**
     * Get the authenticated User
     *
     * @return [json] user object
     */
    public function user(Request $request)
    {
        $user = $request->user();
        $userAbilityRules = $this->getUserAbilities($user);

        return response()->json([
            'userData' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'avatar' => $user->avatar,
                'role' => $user->getRoleNames()->first() ?? 'user',
            ],
            'userAbilityRules' => $userAbilityRules,
        ]);
    }

    /**
     * Logout user (Revoke the token)
     *
     * @return [string] message
     */
    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json([
            'message' => 'Successfully logged out'
        ]);
    }

    /**
     * Get user abilities based on roles and permissions
     *
     * @param User $user
     * @return array
     */
    private function getUserAbilities($user)
    {
        // Default abilities for authenticated users
        $abilities = [
            [
                'action' => 'read',
                'subject' => 'dashboard'
            ]
        ];

        // Get user roles and permissions using Spatie Permission
        if ($user->hasRole('admin')) {
            $abilities[] = [
                'action' => 'manage',
                'subject' => 'all'
            ];
        } elseif ($user->hasRole('manager')) {
            $abilities = array_merge($abilities, [
                [
                    'action' => 'read',
                    'subject' => 'user-management'
                ],
                [
                    'action' => 'create',
                    'subject' => 'user'
                ],
                [
                    'action' => 'update',
                    'subject' => 'user'
                ]
            ]);
        } else {
            // Regular user abilities
            $abilities = array_merge($abilities, [
                [
                    'action' => 'read',
                    'subject' => 'profile'
                ],
                [
                    'action' => 'update',
                    'subject' => 'profile'
                ]
            ]);
        }

        return $abilities;
    }
}
