<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Organization;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AdminController extends Controller
{
    // 認證已在路由中處理，無需在 constructor 中重複設定
    
    public function users(Request $request)
    {
        // 簡單的權限檢查（實際應用中應該使用更完善的權限系統）
        if ($request->user()->email !== 'admin@purpledesk.com') {
            abort(403, '無權限存取');
        }
        
        $perPage = $request->get('per_page', 10);
        $perPage = min(max($perPage, 10), 100); // 限制在 10-100 之間
        
        $users = User::with('organizations', 'teams')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
            
        return response()->json($users);
    }
    
    public function organizations(Request $request)
    {
        if ($request->user()->email !== 'admin@purpledesk.com') {
            abort(403, '無權限存取');
        }
        
        $perPage = $request->get('per_page', 10);
        $perPage = min(max($perPage, 10), 100); // 限制在 10-100 之間
        
        $organizations = Organization::withCount('users')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
            
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
    
    public function searchUsers(Request $request)
    {
        if (request()->user()->email !== 'admin@purpledesk.com') {
            abort(403, '無權限存取');
        }
        
        $query = $request->get('q', '');
        $excludeOrganization = $request->get('exclude_organization');
        
        if (strlen($query) < 2) {
            return response()->json(['users' => []]);
        }
        
        $usersQuery = User::where(function($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('email', 'like', "%{$query}%");
            })
            ->with('organizations', 'teams');
        
        // 排除已在指定組織中的用戶
        if ($excludeOrganization) {
            $usersQuery->whereDoesntHave('organizations', function($q) use ($excludeOrganization) {
                $q->where('organizations.id', $excludeOrganization);
            });
        }
        
        $users = $usersQuery->limit(20)->get();
        
        return response()->json(['users' => $users]);
    }
    
    public function createUser(Request $request)
    {
        if (request()->user()->email !== 'admin@purpledesk.com') {
            abort(403, '無權限存取');
        }
        
        $request->validate([
            'display_name' => 'required|string|max:255',
            'full_name' => 'nullable|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'display_name' => $request->display_name,
            'full_name' => $request->full_name ?: $request->display_name, // 如果沒有提供 full_name，使用 display_name
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return response()->json([
            'message' => '使用者建立成功',
            'user' => $user->load('organizations', 'teams'),
        ], 201);
    }
    
    public function updateUser(Request $request, User $user)
    {
        if (request()->user()->email !== 'admin@purpledesk.com') {
            abort(403, '無權限存取');
        }
        
        $request->validate([
            'display_name' => 'required|string|max:255',
            'full_name' => 'nullable|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
        ]);

        $user->update([
            'display_name' => $request->display_name,
            'full_name' => $request->full_name ?: $request->display_name, // 如果沒有提供 full_name，使用 display_name
            'email' => $request->email,
        ]);

        return response()->json([
            'message' => '使用者更新成功',
            'user' => $user->load('organizations', 'teams'),
        ]);
    }
}
