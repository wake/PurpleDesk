<?php

namespace Database\Seeders;

use App\Models\Organization;
use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class CrossOrganizationSeeder extends Seeder
{
    public function run(): void
    {
        $organizations = Organization::all();
        
        // 創建跨組織的顧問和自由工作者
        $crossOrgUsers = [
            [
                'full_name' => '王技術顧問',
                'display_name' => 'TechWang',
                'email' => 'consultant@tech.com',
                'organizations' => [0, 2, 3], // 智慧雲端服務、網路解決方案、創新科技團隊
                'roles' => ['member', 'lead', 'member']
            ],
            [
                'full_name' => '李行銷顧問',
                'display_name' => 'MarketingLee',
                'email' => 'marketing@consultant.com', 
                'organizations' => [1, 6, 9], // 數位行銷公司、教育科技平台、電子商務平台
                'roles' => ['lead', 'member', 'member']
            ],
            [
                'full_name' => '張資深架構師',
                'display_name' => 'ArchZhang',
                'email' => 'architect@multi.com',
                'organizations' => [0, 2, 5, 9], // 智慧雲端服務、網路解決方案、金融科技新創、電子商務平台
                'roles' => ['member', 'member', 'lead', 'member']
            ],
            [
                'full_name' => '陳UI/UX總監',
                'display_name' => 'DesignChen',
                'email' => 'design@director.com',
                'organizations' => [0, 1, 6, 9], // 智慧雲端服務、數位行銷公司、教育科技平台、電子商務平台
                'roles' => ['member', 'member', 'member', 'lead']
            ],
            [
                'full_name' => '黃數據科學家',
                'display_name' => 'DataHuang',
                'email' => 'data@scientist.com',
                'organizations' => [1, 3, 5, 9], // 數位行銷公司、創新科技團隊、金融科技新創、電子商務平台
                'roles' => ['member', 'member', 'member', 'member']
            ],
            [
                'full_name' => '劉產品經理',
                'display_name' => 'ProductLiu',
                'email' => 'pm@multi.com',
                'organizations' => [5, 6, 7], // 金融科技新創、教育科技平台、健康醫療科技
                'roles' => ['member', 'lead', 'member']
            ],
            [
                'full_name' => '吳資安專家',
                'display_name' => 'SecurityWu',
                'email' => 'security@expert.com',
                'organizations' => [2, 5, 8], // 網路解決方案、金融科技新創、智慧物聯網
                'roles' => ['lead', 'member', 'member']
            ],
            [
                'full_name' => '周全端開發者',
                'display_name' => 'FullStackZhou',
                'email' => 'fullstack@dev.com',
                'organizations' => [0, 6, 9], // 智慧雲端服務、教育科技平台、電子商務平台
                'roles' => ['member', 'member', 'member']
            ],
        ];

        // 創建跨組織用戶
        foreach ($crossOrgUsers as $userData) {
            $account = strtolower($userData['display_name']);
            
            $user = User::create([
                'account' => $account,
                'full_name' => $userData['full_name'],
                'display_name' => $userData['display_name'],
                'email' => $userData['email'],
                'password' => Hash::make('password'),
            ]);

            // 分配到多個組織
            foreach ($userData['organizations'] as $index => $orgIndex) {
                $organization = $organizations[$orgIndex];
                $role = $userData['roles'][$index];
                $user->organizations()->attach($organization->id, ['role' => $role]);
            }
        }

        // 創建額外的團隊成員關聯
        $this->createCrossTeamMemberships();
    }

    private function createCrossTeamMemberships()
    {
        $teams = Team::with('organization')->get();
        $crossOrgUsers = User::whereIn('email', [
            'consultant@tech.com',
            'marketing@consultant.com', 
            'architect@multi.com',
            'design@director.com',
            'data@scientist.com',
            'pm@multi.com',
            'security@expert.com',
            'fullstack@dev.com',
        ])->get();

        // 為跨組織用戶分配到相關團隊
        $teamAssignments = [
            'consultant@tech.com' => [
                '後端開發團隊' => 'lead',
                '系統整合團隊' => 'member',
                'AI研發團隊' => 'member',
            ],
            'marketing@consultant.com' => [
                '創意企劃團隊' => 'lead',
                '內容設計團隊' => 'member',
                '數據分析團隊' => 'member',
            ],
            'architect@multi.com' => [
                '基礎建設團隊' => 'member',
                '網路安全團隊' => 'member',
                '區塊鏈開發團隊' => 'lead',
                '電商系統團隊' => 'member',
            ],
            'design@director.com' => [
                '前端開發團隊' => 'member',
                '創意企劃團隊' => 'member',
                '平台開發團隊' => 'member',
                '電商系統團隊' => 'lead',
            ],
            'data@scientist.com' => [
                '數據分析團隊' => 'member',
                'AI研發團隊' => 'member',
                '產品設計團隊' => 'member',
                '數據分析團隊' => 'member', // 電商平台的數據分析團隊
            ],
            'pm@multi.com' => [
                '產品設計團隊' => 'member',
                '內容設計團隊' => 'lead',
                '醫療系統團隊' => 'member',
            ],
            'security@expert.com' => [
                '網路安全團隊' => 'lead',
                '區塊鏈開發團隊' => 'member',
                'IoT開發團隊' => 'member',
            ],
            'fullstack@dev.com' => [
                '前端開發團隊' => 'member',
                '平台開發團隊' => 'member',
                '電商系統團隊' => 'member',
            ],
        ];

        foreach ($teamAssignments as $userEmail => $userTeams) {
            $user = $crossOrgUsers->where('email', $userEmail)->first();
            if (!$user) continue;

            foreach ($userTeams as $teamName => $role) {
                $team = $teams->where('name', $teamName)->first();
                if ($team && $user->organizations->contains($team->organization_id)) {
                    // 檢查是否已經是團隊成員
                    if (!$team->users()->where('user_id', $user->id)->exists()) {
                        $team->users()->attach($user->id, ['role' => $role]);
                    }
                }
            }
        }

        // 為現有用戶增加跨團隊關係
        $this->addCrossTeamMembershipsForExistingUsers();
    }

    private function addCrossTeamMembershipsForExistingUsers()
    {
        // 讓部分現有用戶也參與多個團隊
        $multiTeamAssignments = [
            // Vincent (劉執行長) 參與其他團隊
            'ceo@cloudtech.com' => [
                '前端開發團隊' => 'member',
                '基礎建設團隊' => 'member',
            ],
            // Andy (張技術長) 領導多個技術團隊
            'cto@cloudtech.com' => [
                '基礎建設團隊' => 'lead',
            ],
            // Eric (孫創辦人) 參與多個核心團隊  
            'founder@netsol.com' => [
                '網路安全團隊' => 'member',
            ],
            // Ryan (馬架構師) 跨團隊支援
            'ryan@netsol.com' => [
                '網路安全團隊' => 'lead',
            ],
            // Blake (白區塊鏈開發) 跨產品線支援
            'blake@fintech.com' => [
                '產品設計團隊' => 'member',
            ],
        ];

        foreach ($multiTeamAssignments as $userEmail => $userTeams) {
            $user = User::where('email', $userEmail)->first();
            if (!$user) continue;

            foreach ($userTeams as $teamName => $role) {
                $team = Team::where('name', $teamName)->first();
                if ($team && $user->organizations->contains($team->organization_id)) {
                    // 檢查是否已經是團隊成員
                    if (!$team->users()->where('user_id', $user->id)->exists()) {
                        $team->users()->attach($user->id, ['role' => $role]);
                    }
                }
            }
        }
    }
}