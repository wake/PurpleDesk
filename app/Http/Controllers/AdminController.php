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
        if (!$request->user()->is_admin) {
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
        if (!$request->user()->is_admin) {
            abort(403, '無權限存取');
        }
        
        $perPage = $request->get('per_page', 10);
        $perPage = min(max($perPage, 10), 100); // 限制在 10-100 之間
        
        $organizations = Organization::withCount('users')
            ->with(['users' => function($query) {
                $query->select('users.id', 'users.display_name', 'users.full_name', 'users.avatar');
            }])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
            
        return response()->json($organizations);
    }
    
    public function systemStats()
    {
        if (!request()->user()->is_admin) {
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
        if (!request()->user()->is_admin) {
            abort(403, '無權限存取');
        }
        
        $query = $request->get('q', '');
        $excludeOrganization = $request->get('exclude_organization');
        
        if (strlen($query) < 2) {
            return response()->json(['users' => []]);
        }
        
        $usersQuery = User::where(function($q) use ($query) {
                $q->where('account', 'like', "%{$query}%")
                  ->orWhere('full_name', 'like', "%{$query}%")
                  ->orWhere('display_name', 'like', "%{$query}%")
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
        if (!request()->user()->is_admin) {
            abort(403, '無權限存取');
        }
        
        $request->validate([
            'account' => 'required|string|max:255|unique:users|regex:/^[a-zA-Z0-9_]+$/',
            'display_name' => 'nullable|string|max:255',
            'full_name' => 'nullable|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'account' => $request->account,
            'display_name' => $request->display_name,
            'full_name' => $request->full_name,
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
        if (!request()->user()->is_admin) {
            abort(403, '無權限存取');
        }
        
        $request->validate([
            'account' => 'required|string|max:255|unique:users,account,' . $user->id . '|regex:/^[a-zA-Z0-9_]+$/',
            'display_name' => 'nullable|string|max:255',
            'full_name' => 'nullable|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
        ]);

        $user->update([
            'account' => $request->account,
            'display_name' => $request->display_name,
            'full_name' => $request->full_name,
            'email' => $request->email,
        ]);

        return response()->json([
            'message' => '使用者更新成功',
            'user' => $user->load('organizations', 'teams'),
        ]);
    }
    
    public function checkAccountAvailable(Request $request)
    {
        if (!request()->user()->is_admin) {
            abort(403, '無權限存取');
        }
        
        $account = $request->get('account');
        $excludeUserId = $request->get('exclude_user_id');
        
        if (!$account) {
            return response()->json(['available' => false, 'message' => '帳號不能為空']);
        }
        
        if (!preg_match('/^[a-zA-Z0-9_]+$/', $account)) {
            return response()->json(['available' => false, 'message' => '帳號只能包含英數字和底線']);
        }
        
        $query = User::where('account', $account);
        if ($excludeUserId) {
            $query->where('id', '!=', $excludeUserId);
        }
        
        $exists = $query->exists();
        
        return response()->json([
            'available' => !$exists,
            'message' => $exists ? '此帳號已被使用' : '帳號可用'
        ]);
    }
}
