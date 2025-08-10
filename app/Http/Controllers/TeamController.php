<?php

namespace App\Http\Controllers;

use App\Models\Team;
use App\Models\Organization;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TeamController extends Controller
{
    /**
     * 取得組織的所有團隊（支援分頁）
     */
    public function index(Request $request, $organizationId)
    {
        $organization = Organization::findOrFail($organizationId);
        
        $page = $request->get('page', 1);
        $perPage = 10;
        
        $teams = $organization->teams()
            ->with('users')
            ->paginate($perPage, ['*'], 'teams_page', $page);
        
        return response()->json([
            'teams' => $teams
        ]);
    }

    /**
     * 建立新團隊
     */
    public function store(Request $request, $organizationId)
    {
        $organization = Organization::findOrFail($organizationId);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
        
        $data = [
            'name' => $request->name,
            'description' => $request->description,
            'organization_id' => $organization->id,
        ];
        
        if ($request->hasFile('avatar')) {
            $data['avatar'] = $request->file('avatar')->store('avatars/teams', 'public');
        }
        
        $team = Team::create($data);
        
        // 將建立者加入團隊並設為領導者
        if ($request->user()) {
            $team->users()->attach($request->user()->id, ['role' => 'lead']);
        }
        
        return response()->json([
            'message' => '團隊建立成功',
            'team' => $team->load('users')
        ], 201);
    }

    /**
     * 顯示特定團隊
     */
    public function show($organizationId, $teamId)
    {
        $team = Team::where('organization_id', $organizationId)
                    ->with(['users', 'organization'])
                    ->findOrFail($teamId);
        
        return response()->json($team);
    }

    /**
     * 更新團隊資訊
     */
    public function update(Request $request, $organizationId, $teamId)
    {
        $team = Team::where('organization_id', $organizationId)->findOrFail($teamId);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
        
        $data = [
            'name' => $request->name,
            'description' => $request->description,
        ];
        
        if ($request->hasFile('avatar')) {
            // 刪除舊頭像
            if ($team->avatar) {
                Storage::disk('public')->delete($team->avatar);
            }
            
            $data['avatar'] = $request->file('avatar')->store('avatars/teams', 'public');
        }
        
        $team->update($data);
        
        return response()->json([
            'message' => '團隊更新成功',
            'team' => $team->load('users')
        ]);
    }

    /**
     * 刪除團隊
     */
    public function destroy($organizationId, $teamId)
    {
        $team = Team::where('organization_id', $organizationId)->findOrFail($teamId);
        
        // 刪除頭像
        if ($team->avatar) {
            Storage::disk('public')->delete($team->avatar);
        }
        
        $team->delete();
        
        return response()->json([
            'message' => '團隊已刪除'
        ]);
    }

    /**
     * 新增團隊成員
     */
    public function addMember(Request $request, $organizationId, $teamId)
    {
        $team = Team::where('organization_id', $organizationId)->findOrFail($teamId);
        
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'role' => 'in:member,lead'
        ]);
        
        $role = $request->role ?? 'member';
        
        // 檢查使用者是否已在團隊中
        if ($team->users()->where('user_id', $request->user_id)->exists()) {
            return response()->json([
                'message' => '使用者已在團隊中'
            ], 400);
        }
        
        $team->users()->attach($request->user_id, ['role' => $role]);
        
        return response()->json([
            'message' => '成員新增成功',
            'team' => $team->load('users')
        ]);
    }

    /**
     * 移除團隊成員
     */
    public function removeMember($organizationId, $teamId, $userId)
    {
        $team = Team::where('organization_id', $organizationId)->findOrFail($teamId);
        
        $team->users()->detach($userId);
        
        return response()->json([
            'message' => '成員已移除',
            'team' => $team->load('users')
        ]);
    }

    /**
     * 更新成員角色
     */
    public function updateMemberRole(Request $request, $organizationId, $teamId, $userId)
    {
        $team = Team::where('organization_id', $organizationId)->findOrFail($teamId);
        
        $request->validate([
            'role' => 'required|in:member,lead'
        ]);
        
        $team->users()->updateExistingPivot($userId, ['role' => $request->role]);
        
        return response()->json([
            'message' => '角色更新成功',
            'team' => $team->load('users')
        ]);
    }
}