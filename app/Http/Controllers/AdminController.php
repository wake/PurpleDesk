<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Organization;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }
    
    public function users()
    {
        // 簡單的權限檢查（實際應用中應該使用更完善的權限系統）
        if (request()->user()->email !== 'admin@purpledesk.com') {
            abort(403, '無權限存取');
        }
        
        $users = User::with('organization')
            ->orderBy('created_at', 'desc')
            ->get();
            
        return response()->json($users);
    }
    
    public function organizations()
    {
        if (request()->user()->email !== 'admin@purpledesk.com') {
            abort(403, '無權限存取');
        }
        
        $organizations = Organization::withCount('users')
            ->orderBy('created_at', 'desc')
            ->get();
            
        return response()->json($organizations);
    }
    
    public function systemStats()
    {
        if (request()->user()->email !== 'admin@purpledesk.com') {
            abort(403, '無權限存取');
        }
        
        return response()->json([
            'total_users' => User::count(),
            'total_organizations' => Organization::count(),
            'users_this_month' => User::whereMonth('created_at', now()->month)->count(),
            'organizations_this_month' => Organization::whereMonth('created_at', now()->month)->count(),
        ]);
    }
}
