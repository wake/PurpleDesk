<?php

namespace App\Http\Controllers;

use App\Models\Organization;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class OrganizationController extends Controller
{
    public function index()
    {
        $organizations = Organization::with('users', 'teams')->get();
        return response()->json($organizations);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $avatarPath = null;
        if ($request->hasFile('avatar')) {
            $avatarPath = $request->file('avatar')->store('avatars/organizations', 'public');
        }

        $organization = Organization::create([
            'name' => $request->name,
            'description' => $request->description,
            'avatar' => $avatarPath,
        ]);

        return response()->json($organization->load('users', 'teams'), 201);
    }

    public function show(Organization $organization, Request $request)
    {
        $page = $request->get('page', 1);
        $perPage = $request->get('per_page', 10);
        $perPage = min(max($perPage, 10), 100); // 限制在 10-100 之間
        
        // 分頁載入組織成員
        $users = $organization->users()
            ->with('teams')
            ->paginate($perPage, ['*'], 'users_page', $page);
            
        // 載入所有團隊（通常團隊數量不會太多，所以不分頁）
        $teams = $organization->teams()->with('users')->get();
        
        return response()->json([
            'id' => $organization->id,
            'name' => $organization->name,
            'description' => $organization->description,
            'avatar' => $organization->avatar,
            'logo_url' => $organization->logo_url,
            'created_at' => $organization->created_at,
            'updated_at' => $organization->updated_at,
            'users' => $users,
            'teams' => $teams,
        ]);
    }

    public function update(Request $request, Organization $organization)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'remove_avatar' => 'nullable|string',
        ]);

        $data = [
            'name' => $request->name,
            'description' => $request->description,
        ];

        // 處理移除頭像
        if ($request->input('remove_avatar') === '1') {
            // 刪除舊頭像檔案
            if ($organization->avatar) {
                Storage::disk('public')->delete($organization->avatar);
            }
            $data['avatar'] = null;
        }
        // 處理上傳新頭像
        elseif ($request->hasFile('avatar')) {
            // 刪除舊頭像
            if ($organization->avatar) {
                Storage::disk('public')->delete($organization->avatar);
            }
            
            $data['avatar'] = $request->file('avatar')->store('avatars/organizations', 'public');
        }

        $organization->update($data);

        return response()->json($organization->load('users', 'teams'));
    }

    public function destroy(Organization $organization)
    {
        // 檢查是否有關聯的使用者
        if ($organization->users()->count() > 0) {
            return response()->json([
                'message' => '無法刪除有成員的組織'
            ], 422);
        }

        // 刪除頭像檔案
        if ($organization->avatar) {
            Storage::disk('public')->delete($organization->avatar);
        }

        $organization->delete();

        return response()->json([
            'message' => '組織已成功刪除'
        ]);
    }
    
    // 邀請用戶加入組織
    public function inviteMember(Request $request, Organization $organization)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'role' => 'required|in:member,admin,owner',
        ]);

        $user = User::where('email', $request->email)->first();
        
        // 檢查用戶是否已在組織中
        if ($organization->users()->where('user_id', $user->id)->exists()) {
            return response()->json([
                'message' => '該用戶已經是組織成員'
            ], 422);
        }

        // 將用戶加入組織
        $organization->users()->attach($user->id, [
            'role' => $request->role
        ]);

        return response()->json([
            'message' => '成員邀請成功',
            'user' => $user->load('organizations', 'teams')
        ]);
    }
    
    // 更新組織成員角色
    public function updateMemberRole(Request $request, Organization $organization, User $user)
    {
        $request->validate([
            'role' => 'required|in:member,admin,owner',
        ]);

        // 檢查用戶是否在組織中
        if (!$organization->users()->where('user_id', $user->id)->exists()) {
            return response()->json([
                'message' => '該用戶不是組織成員'
            ], 404);
        }

        // 更新角色
        $organization->users()->updateExistingPivot($user->id, [
            'role' => $request->role
        ]);

        return response()->json([
            'message' => '成員角色已更新'
        ]);
    }
    
    // 移除組織成員
    public function removeMember(Organization $organization, User $user)
    {
        // 檢查用戶是否在組織中
        if (!$organization->users()->where('user_id', $user->id)->exists()) {
            return response()->json([
                'message' => '該用戶不是組織成員'
            ], 404);
        }

        // 移除用戶
        $organization->users()->detach($user->id);

        return response()->json([
            'message' => '成員已移除'
        ]);
    }
    
    // 取得組織的團隊列表（支援分頁）
    public function teams(Organization $organization, Request $request)
    {
        $page = $request->get('page', 1);
        $perPage = 10;
        
        $teams = $organization->teams()
            ->with('users')
            ->paginate($perPage, ['*'], 'teams_page', $page);
            
        return response()->json([
            'teams' => $teams
        ]);
    }
}
