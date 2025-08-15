<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Organization;
use App\Models\Team;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class RealisticTestDataSeeder extends Seeder
{
    /**
     * 真實的頭像配置資料
     */
    private function getAvatarConfigs(): array
    {
        return [
            // Emoji 頭像
            ['type' => 'emoji', 'icon' => '😊', 'bg' => '#fef08a'],
            ['type' => 'emoji', 'icon' => '🚀', 'bg' => '#dbeafe'],
            ['type' => 'emoji', 'icon' => '💻', 'bg' => '#e9d5ff'],
            ['type' => 'emoji', 'icon' => '🎨', 'bg' => '#f5d0fe'],
            ['type' => 'emoji', 'icon' => '📈', 'bg' => '#bbf7d0'],
            ['type' => 'emoji', 'icon' => '⚡', 'bg' => '#fed7aa'],
            ['type' => 'emoji', 'icon' => '🔥', 'bg' => '#fecaca'],
            ['type' => 'emoji', 'icon' => '💡', 'bg' => '#fde68a'],
            
            // Bootstrap Icons
            ['type' => 'bootstrap', 'icon' => 'bi-person-fill', 'bg' => '#6366f1'],
            ['type' => 'bootstrap', 'icon' => 'bi-briefcase-fill', 'bg' => '#8b5cf6'],
            ['type' => 'bootstrap', 'icon' => 'bi-code-slash', 'bg' => '#3b82f6'],
            ['type' => 'bootstrap', 'icon' => 'bi-palette-fill', 'bg' => '#ec4899'],
            ['type' => 'bootstrap', 'icon' => 'bi-gear-fill', 'bg' => '#10b981'],
            ['type' => 'bootstrap', 'icon' => 'bi-lightning-fill', 'bg' => '#f59e0b'],
            
            // Hero Icons  
            ['type' => 'heroicons', 'icon' => 'outline:UserIcon', 'bg' => '#ef4444'],
            ['type' => 'heroicons', 'icon' => 'solid:StarIcon', 'bg' => '#84cc16'],
            ['type' => 'heroicons', 'icon' => 'outline:CogIcon', 'bg' => '#06b6d4'],
            
            // 字母縮寫
            ['type' => 'initials', 'icon' => 'AM', 'bg' => '#a855f7'],
            ['type' => 'initials', 'icon' => 'JS', 'bg' => '#d946ef'],
            ['type' => 'initials', 'icon' => 'LW', 'bg' => '#0ea5e9'],
        ];
    }

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $avatarConfigs = $this->getAvatarConfigs();
        
        // 創建真實的組織
        $organizations = [
            [
                'name' => '台北科技創新',
                'description' => '專注於金融科技與企業數位轉型的創新公司',
                'logo_data' => [
                    'type' => 'initials',
                    'icon' => 'TTC',
                    'backgroundColor' => '#3b82f6'
                ]
            ],
            [
                'name' => '綠能永續顧問',
                'description' => '提供企業永續發展與綠能解決方案',
                'logo_data' => [
                    'type' => 'emoji',
                    'icon' => '🌱',
                    'backgroundColor' => '#bbf7d0'
                ]
            ],
            [
                'name' => '數位行銷工作室',
                'description' => '專業的數位行銷與品牌策略服務',
                'logo_data' => [
                    'type' => 'bootstrap',
                    'icon' => 'bi-megaphone-fill',
                    'backgroundColor' => '#f59e0b'
                ]
            ],
            [
                'name' => '智慧醫療科技',
                'description' => '運用AI技術改善醫療服務品質',
                'logo_data' => [
                    'type' => 'emoji',
                    'icon' => '🏥',
                    'backgroundColor' => '#c7d2fe'
                ]
            ]
        ];

        $createdOrgs = [];
        foreach ($organizations as $orgData) {
            $createdOrgs[] = Organization::create($orgData);
        }

        // 創建真實的用戶資料
        $users = [
            // 系統管理員
            ['account' => 'admin', 'full_name' => '系統管理員', 'email' => 'admin@purpledesk.local', 'org' => null, 'is_admin' => true],
            
            // 台北科技創新的員工
            ['full_name' => '陳志明', 'email' => 'chen.zhiming@taipeitech.com', 'org' => 0],
            ['full_name' => '林雅婷', 'email' => 'lin.yating@taipeitech.com', 'org' => 0],
            ['full_name' => '王建華', 'email' => 'wang.jianhua@taipeitech.com', 'org' => 0],
            ['full_name' => '張美玲', 'email' => 'zhang.meiling@taipeitech.com', 'org' => 0],
            
            // 綠能永續顧問的員工
            ['full_name' => '李俊傑', 'email' => 'li.junjie@greensustain.com', 'org' => 1],
            ['full_name' => '黃淑芬', 'email' => 'huang.shufen@greensustain.com', 'org' => 1],
            ['full_name' => '劉志強', 'email' => 'liu.zhiqiang@greensustain.com', 'org' => 1],
            
            // 數位行銷工作室的員工
            ['full_name' => '吳佩君', 'email' => 'wu.peijun@digitalstudio.tw', 'org' => 2],
            ['full_name' => '許文傑', 'email' => 'xu.wenjie@digitalstudio.tw', 'org' => 2],
            ['full_name' => '蔡雅雯', 'email' => 'cai.yawen@digitalstudio.tw', 'org' => 2],
            ['full_name' => '鄭家豪', 'email' => 'zheng.jiahao@digitalstudio.tw', 'org' => 2],
            
            // 智慧醫療科技的員工
            ['full_name' => '范志偉', 'email' => 'fan.zhiwei@smartmedtech.com', 'org' => 3],
            ['full_name' => '沈雅芳', 'email' => 'shen.yafang@smartmedtech.com', 'org' => 3],
            
            // 跨組織合作夥伴
            ['full_name' => '謝明達', 'email' => 'xie.mingda@consultant.com', 'org' => null],
            ['full_name' => '孫麗華', 'email' => 'sun.lihua@freelancer.tw', 'org' => null],
        ];

        $createdUsers = [];
        foreach ($users as $index => $userData) {
            $avatarConfig = $avatarConfigs[$index % count($avatarConfigs)];
            
            $user = User::create([
                'account' => isset($userData['account']) ? $userData['account'] : strtolower(str_replace(['@', '.'], ['_', '_'], $userData['email'])),
                'full_name' => $userData['full_name'],
                'email' => $userData['email'],
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
                'is_admin' => $userData['is_admin'] ?? false,
                'avatar_data' => [
                    'type' => $avatarConfig['type'],
                    'icon' => $avatarConfig['icon'],
                    'backgroundColor' => $avatarConfig['bg']
                ]
            ]);
            
            $createdUsers[] = $user;
            
            // 將用戶分配到對應組織
            if ($userData['org'] !== null) {
                $createdOrgs[$userData['org']]->users()->attach($user->id, [
                    'role' => $index % 4 === 0 ? 'admin' : 'member',
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
        }

        // 創建團隊
        $teams = [
            // 台北科技創新的團隊
            [
                'name' => '前端開發團隊',
                'description' => '負責用戶介面與體驗設計開發',
                'org' => 0,
                'members' => [0, 1], // 陳志明, 林雅婷
                'avatar_data' => ['type' => 'emoji', 'icon' => '💻', 'backgroundColor' => '#dbeafe']
            ],
            [
                'name' => '後端架構團隊',
                'description' => '系統架構設計與API開發',
                'org' => 0,
                'members' => [2, 3], // 王建華, 張美玲
                'avatar_data' => ['type' => 'bootstrap', 'icon' => 'bi-server', 'backgroundColor' => '#e9d5ff']
            ],
            
            // 綠能永續顧問的團隊
            [
                'name' => '永續策略組',
                'description' => '企業永續發展策略規劃',
                'org' => 1,
                'members' => [4, 5], // 李俊傑, 黃淑芬
                'avatar_data' => ['type' => 'emoji', 'icon' => '🌿', 'backgroundColor' => '#bbf7d0']
            ],
            [
                'name' => '技術實施組',
                'description' => '綠能技術導入與實施',
                'org' => 1,
                'members' => [6], // 劉志強
                'avatar_data' => ['type' => 'bootstrap', 'icon' => 'bi-lightning-charge-fill', 'backgroundColor' => '#fde68a']
            ],
            
            // 數位行銷工作室的團隊
            [
                'name' => '創意設計團隊',
                'description' => '視覺設計與創意發想',
                'org' => 2,
                'members' => [7, 8], // 吳佩君, 許文傑
                'avatar_data' => ['type' => 'emoji', 'icon' => '🎨', 'backgroundColor' => '#f5d0fe']
            ],
            [
                'name' => '數據分析組',
                'description' => '行銷數據分析與優化',
                'org' => 2,
                'members' => [9, 10], // 蔡雅雯, 鄭家豪
                'avatar_data' => ['type' => 'bootstrap', 'icon' => 'bi-graph-up', 'backgroundColor' => '#a5f3fc']
            ],
            
            // 智慧醫療科技的團隊
            [
                'name' => 'AI演算法研發',
                'description' => '醫療AI演算法開發與訓練',
                'org' => 3,
                'members' => [11, 12], // 范志偉, 沈雅芳
                'avatar_data' => ['type' => 'emoji', 'icon' => '🤖', 'backgroundColor' => '#c7d2fe']
            ]
        ];

        foreach ($teams as $teamData) {
            $team = Team::create([
                'name' => $teamData['name'],
                'description' => $teamData['description'],
                'organization_id' => $createdOrgs[$teamData['org']]->id,
                'icon_data' => $teamData['avatar_data']
            ]);
            
            // 分配團隊成員
            foreach ($teamData['members'] as $userIndex) {
                $team->users()->attach($createdUsers[$userIndex]->id, [
                    'role' => $userIndex === $teamData['members'][0] ? 'lead' : 'member',
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
        }

        // 建立跨組織合作關係
        // 讓顧問和自由接案者加入多個組織
        $createdOrgs[0]->users()->attach($createdUsers[13]->id, ['role' => 'consultant']);
        $createdOrgs[2]->users()->attach($createdUsers[13]->id, ['role' => 'consultant']);
        $createdOrgs[1]->users()->attach($createdUsers[14]->id, ['role' => 'contractor']);

        $this->command->info('✅ 已成功創建擬真測試資料：');
        $this->command->info("   - 1 位系統管理員 (帳號: admin, 信箱: admin@purpledesk.local)");
        $this->command->info("   - {$createdOrgs[0]->users()->count()} 位台北科技創新員工");
        $this->command->info("   - {$createdOrgs[1]->users()->count()} 位綠能永續顧問員工");
        $this->command->info("   - {$createdOrgs[2]->users()->count()} 位數位行銷工作室員工");
        $this->command->info("   - {$createdOrgs[3]->users()->count()} 位智慧醫療科技員工");
        $this->command->info("   - " . Team::count() . " 個專業團隊");
        $this->command->info("   - 包含完整的頭像樣式：Emoji、Bootstrap Icons、Hero Icons、字母縮寫");
        $this->command->info("   - 所有帳號預設密碼：password");
    }
}