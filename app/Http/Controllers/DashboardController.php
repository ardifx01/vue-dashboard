<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\User;
use Barryvdh\Debugbar\Facades\Debugbar;

class DashboardController extends Controller
{
    /**
     * Get dashboard statistics
     */
    public function stats(): JsonResponse
    {
        Debugbar::info('Dashboard stats requested');
        
        $stats = [
            'users' => \App\Models\User::count(),
            'roles' => \Spatie\Permission\Models\Role::count(),
            'permissions' => \Spatie\Permission\Models\Permission::count(),
            'growth' => 12.5
        ];

        return response()->json($stats);
    }

    /**
     * Get dashboard analytics data
     */
    public function analytics(): JsonResponse
    {
        Debugbar::startMeasure('analytics', 'Analytics Data Generation');
        
        // Simulate some processing time
        usleep(100000); // 100ms
        
        $analytics = [
            'page_views' => rand(1000, 5000),
            'unique_visitors' => rand(500, 2000),
            'bounce_rate' => rand(20, 80) . '%',
            'avg_session_duration' => rand(120, 600) . 's',
            'conversion_rate' => rand(1, 10) . '%'
        ];
        
        Debugbar::stopMeasure('analytics');
        Debugbar::addMessage($analytics, 'Analytics Data');
        
        return response()->json($analytics);
    }
}
