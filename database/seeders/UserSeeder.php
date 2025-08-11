<?php

namespace Database\Seeders;

use App\Models\Organization;
use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $organizations = Organization::all();
        
        // 系統管理員
        $admin = User::create([
            'full_name' => '系統管理員',
            'display_name' => 'Admin',
            'email' => 'admin@purpledesk.com',
            'password' => Hash::make('password'),
        ]);

        // 創建豐富的測試用戶資料
        $testUsers = [
            // 智慧雲端服務 - 7人團隊
            ['full_name' => '劉執行長', 'display_name' => 'Vincent', 'email' => 'ceo@cloudtech.com', 'org_index' => 0, 'role' => 'admin'],
            ['full_name' => '張技術長', 'display_name' => 'Andy', 'email' => 'cto@cloudtech.com', 'org_index' => 0, 'role' => 'lead'],
            ['full_name' => '王資深工程師', 'display_name' => 'Kevin', 'email' => 'kevin@cloudtech.com', 'org_index' => 0, 'role' => 'member'],
            ['full_name' => '李前端工程師', 'display_name' => 'Emma', 'email' => 'emma@cloudtech.com', 'org_index' => 0, 'role' => 'member'],
            ['full_name' => '陳後端工程師', 'display_name' => 'Chris', 'email' => 'chris@cloudtech.com', 'org_index' => 0, 'role' => 'member'],
            ['full_name' => '黃UI設計師', 'display_name' => 'Sophia', 'email' => 'sophia@cloudtech.com', 'org_index' => 0, 'role' => 'member'],
            ['full_name' => '林專案經理', 'display_name' => 'Michael', 'email' => 'pm@cloudtech.com', 'org_index' => 0, 'role' => 'member'],
            
            // 數位行銷公司 - 5人團隊
            ['full_name' => '吳營運長', 'display_name' => 'Grace', 'email' => 'coo@marketing.com', 'org_index' => 1, 'role' => 'admin'],
            ['full_name' => '周行銷經理', 'display_name' => 'David', 'email' => 'david@marketing.com', 'org_index' => 1, 'role' => 'lead'],
            ['full_name' => '鄭社群經理', 'display_name' => 'Sarah', 'email' => 'sarah@marketing.com', 'org_index' => 1, 'role' => 'member'],
            ['full_name' => '趙廣告優化師', 'display_name' => 'Tom', 'email' => 'tom@marketing.com', 'org_index' => 1, 'role' => 'member'],
            ['full_name' => '錢數據分析師', 'display_name' => 'Alice', 'email' => 'alice@marketing.com', 'org_index' => 1, 'role' => 'member'],
            
            // 網路解決方案 - 8人團隊
            ['full_name' => '孫創辦人', 'display_name' => 'Eric', 'email' => 'founder@netsol.com', 'org_index' => 2, 'role' => 'admin'],
            ['full_name' => '馬架構師', 'display_name' => 'Ryan', 'email' => 'ryan@netsol.com', 'org_index' => 2, 'role' => 'lead'],
            ['full_name' => '許資安工程師', 'display_name' => 'Jason', 'email' => 'jason@netsol.com', 'org_index' => 2, 'role' => 'member'],
            ['full_name' => '郭網路工程師', 'display_name' => 'Lisa', 'email' => 'lisa@netsol.com', 'org_index' => 2, 'role' => 'member'],
            ['full_name' => '何運維工程師', 'display_name' => 'Mark', 'email' => 'mark@netsol.com', 'org_index' => 2, 'role' => 'member'],
            ['full_name' => '蕭系統工程師', 'display_name' => 'Jenny', 'email' => 'jenny@netsol.com', 'org_index' => 2, 'role' => 'member'],
            ['full_name' => '盧測試工程師', 'display_name' => 'Peter', 'email' => 'peter@netsol.com', 'org_index' => 2, 'role' => 'member'],
            ['full_name' => '賴技術文件', 'display_name' => 'Amy', 'email' => 'amy@netsol.com', 'org_index' => 2, 'role' => 'member'],
            
            // 創新科技團隊 - 4人團隊
            ['full_name' => '羅博士', 'display_name' => 'Dr.Luo', 'email' => 'dr.luo@aitech.com', 'org_index' => 3, 'role' => 'admin'],
            ['full_name' => '宋AI工程師', 'display_name' => 'Steven', 'email' => 'steven@aitech.com', 'org_index' => 3, 'role' => 'lead'],
            ['full_name' => '韓數據科學家', 'display_name' => 'Hannah', 'email' => 'hannah@aitech.com', 'org_index' => 3, 'role' => 'member'],
            ['full_name' => '薛ML工程師', 'display_name' => 'Oscar', 'email' => 'oscar@aitech.com', 'org_index' => 3, 'role' => 'member'],
            
            // 綠能永續企業 - 3人團隊
            ['full_name' => '葉環保長', 'display_name' => 'Ivy', 'email' => 'ivy@green.com', 'org_index' => 4, 'role' => 'admin'],
            ['full_name' => '袁永續顧問', 'display_name' => 'Daniel', 'email' => 'daniel@green.com', 'org_index' => 4, 'role' => 'member'],
            ['full_name' => '夏能源工程師', 'display_name' => 'Summer', 'email' => 'summer@green.com', 'org_index' => 4, 'role' => 'member'],
            
            // 金融科技新創 - 6人團隊  
            ['full_name' => '康金融長', 'display_name' => 'Connor', 'email' => 'cfo@fintech.com', 'org_index' => 5, 'role' => 'admin'],
            ['full_name' => '白區塊鏈開發', 'display_name' => 'Blake', 'email' => 'blake@fintech.com', 'org_index' => 5, 'role' => 'lead'],
            ['full_name' => '毛支付工程師', 'display_name' => 'Max', 'email' => 'max@fintech.com', 'org_index' => 5, 'role' => 'member'],
            ['full_name' => '嚴風控專員', 'display_name' => 'Yan', 'email' => 'yan@fintech.com', 'org_index' => 5, 'role' => 'member'],
            ['full_name' => '溫產品經理', 'display_name' => 'Wendy', 'email' => 'wendy@fintech.com', 'org_index' => 5, 'role' => 'member'],
            ['full_name' => '費合規專員', 'display_name' => 'Felix', 'email' => 'felix@fintech.com', 'org_index' => 5, 'role' => 'member'],
            
            // 教育科技平台 - 5人團隊
            ['full_name' => '方教育長', 'display_name' => 'Fiona', 'email' => 'fiona@edutech.com', 'org_index' => 6, 'role' => 'admin'],
            ['full_name' => '石課程設計師', 'display_name' => 'Stone', 'email' => 'stone@edutech.com', 'org_index' => 6, 'role' => 'lead'],
            ['full_name' => '程前端工程師', 'display_name' => 'Programmer', 'email' => 'dev@edutech.com', 'org_index' => 6, 'role' => 'member'],
            ['full_name' => '卜內容企劃', 'display_name' => 'Bob', 'email' => 'bob@edutech.com', 'org_index' => 6, 'role' => 'member'],
            ['full_name' => '丁學習顧問', 'display_name' => 'Ding', 'email' => 'ding@edutech.com', 'org_index' => 6, 'role' => 'member'],
            
            // 健康醫療科技 - 4人團隊
            ['full_name' => '樂醫師', 'display_name' => 'Dr.Joy', 'email' => 'dr.joy@medtech.com', 'org_index' => 7, 'role' => 'admin'],
            ['full_name' => '尤醫療工程師', 'display_name' => 'You', 'email' => 'you@medtech.com', 'org_index' => 7, 'role' => 'lead'],
            ['full_name' => '任護理師', 'display_name' => 'Ren', 'email' => 'ren@medtech.com', 'org_index' => 7, 'role' => 'member'],
            ['full_name' => '水數據分析師', 'display_name' => 'Water', 'email' => 'water@medtech.com', 'org_index' => 7, 'role' => 'member'],
            
            // 智慧物聯網 - 3人團隊
            ['full_name' => '金硬體工程師', 'display_name' => 'Golden', 'email' => 'golden@iot.com', 'org_index' => 8, 'role' => 'admin'],
            ['full_name' => '木韌體工程師', 'display_name' => 'Wood', 'email' => 'wood@iot.com', 'org_index' => 8, 'role' => 'member'],
            ['full_name' => '火感測器專家', 'display_name' => 'Fire', 'email' => 'fire@iot.com', 'org_index' => 8, 'role' => 'member'],
            
            // 電子商務平台 - 6人團隊
            ['full_name' => '土營運經理', 'display_name' => 'Earth', 'email' => 'earth@ecom.com', 'org_index' => 9, 'role' => 'admin'],
            ['full_name' => '風前端工程師', 'display_name' => 'Wind', 'email' => 'wind@ecom.com', 'org_index' => 9, 'role' => 'lead'],
            ['full_name' => '雨後端工程師', 'display_name' => 'Rain', 'email' => 'rain@ecom.com', 'org_index' => 9, 'role' => 'member'],
            ['full_name' => '雷數據工程師', 'display_name' => 'Thunder', 'email' => 'thunder@ecom.com', 'org_index' => 9, 'role' => 'member'],
            ['full_name' => '電商品經理', 'display_name' => 'Electric', 'email' => 'electric@ecom.com', 'org_index' => 9, 'role' => 'member'],
            ['full_name' => '光UI設計師', 'display_name' => 'Light', 'email' => 'light@ecom.com', 'org_index' => 9, 'role' => 'member'],
        ];

        // 創建用戶並分配到組織
        foreach ($testUsers as $userData) {
            $user = User::create([
                'full_name' => $userData['full_name'],
                'display_name' => $userData['display_name'],
                'email' => $userData['email'],
                'password' => Hash::make('password'),
            ]);
            
            // 分配到組織
            $organization = $organizations[$userData['org_index']];
            $user->organizations()->attach($organization->id, ['role' => $userData['role']]);
        }

        // 創建團隊資料
        $this->createTeamsAndAssignMembers($organizations);
    }

    private function createTeamsAndAssignMembers($organizations)
    {
        // 為每個組織創建 1-3 個團隊
        $teamData = [
            // 智慧雲端服務的團隊
            0 => [
                ['name' => '後端開發團隊', 'description' => '負責服務器端開發與API設計'],
                ['name' => '前端開發團隊', 'description' => '負責用戶界面與用戶體驗設計'],
                ['name' => '基礎建設團隊', 'description' => '負責雲端架構與運維管理'],
            ],
            // 數位行銷公司的團隊
            1 => [
                ['name' => '創意企劃團隊', 'description' => '負責廣告創意與行銷策略規劃'],
                ['name' => '數據分析團隊', 'description' => '負責行銷數據分析與優化建議'],
            ],
            // 網路解決方案的團隊
            2 => [
                ['name' => '系統整合團隊', 'description' => '負責企業級系統整合與客製化'],
                ['name' => '網路安全團隊', 'description' => '負責資訊安全與風險評估'],
            ],
            // 創新科技團隊
            3 => [
                ['name' => 'AI研發團隊', 'description' => '負責人工智慧演算法研發'],
            ],
            // 綠能永續企業
            4 => [
                ['name' => '永續顧問團隊', 'description' => '負責企業永續發展諮詢'],
            ],
            // 金融科技新創
            5 => [
                ['name' => '區塊鏈開發團隊', 'description' => '負責區塊鏈技術開發與應用'],
                ['name' => '產品設計團隊', 'description' => '負責金融產品設計與用戶體驗'],
            ],
            // 教育科技平台
            6 => [
                ['name' => '平台開發團隊', 'description' => '負責學習平台開發與維護'],
                ['name' => '內容設計團隊', 'description' => '負責課程內容設計與製作'],
            ],
            // 健康醫療科技
            7 => [
                ['name' => '醫療系統團隊', 'description' => '負責醫療管理系統開發'],
            ],
            // 智慧物聯網
            8 => [
                ['name' => 'IoT開發團隊', 'description' => '負責物聯網設備開發與整合'],
            ],
            // 電子商務平台
            9 => [
                ['name' => '電商系統團隊', 'description' => '負責電商平台開發與維護'],
                ['name' => '數據分析團隊', 'description' => '負責商業數據分析與決策支援'],
            ],
        ];

        foreach ($teamData as $orgIndex => $teams) {
            $organization = $organizations[$orgIndex];
            $orgUsers = $organization->users;
            
            foreach ($teams as $teamInfo) {
                $team = Team::create([
                    'organization_id' => $organization->id,
                    'name' => $teamInfo['name'],
                    'description' => $teamInfo['description'],
                ]);

                // 為每個團隊分配成員
                $userCount = $orgUsers->count();
                if ($userCount > 0) {
                    // 分配團隊領導（通常是組織中的 lead 或 admin）
                    $leader = $orgUsers->where('pivot.role', 'lead')->first() 
                           ?? $orgUsers->where('pivot.role', 'admin')->first();
                    
                    if ($leader) {
                        $team->users()->attach($leader->id, ['role' => 'lead']);
                    }

                    // 分配部分成員到團隊
                    $members = $orgUsers->where('pivot.role', 'member')->take(rand(1, 3));
                    foreach ($members as $member) {
                        if (!$team->users->contains($member->id)) {
                            $team->users()->attach($member->id, ['role' => 'member']);
                        }
                    }
                }
            }
        }
    }
}
