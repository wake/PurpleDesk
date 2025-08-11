<?php

namespace Database\Seeders;

use App\Models\Organization;
use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;

class TestDataSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('zh_TW');
        
        // 建立額外的組織
        $organizations = [];
        $orgNames = [
            '創新軟體科技', '智慧雲端服務', '數位行銷公司', '網路解決方案',
            '資訊安全顧問', '大數據分析公司', '人工智慧研究所', '區塊鏈技術團隊'
        ];
        
        foreach ($orgNames as $name) {
            $organizations[] = Organization::create([
                'name' => $name,
                'description' => $faker->sentence(10),
            ]);
        }
        
        // 加入現有的組織
        $organizations[] = Organization::where('name', '原型科技有限公司')->first();
        $organizations[] = Organization::where('name', '紫色設計工作室')->first();
        
        // 建立 35 個測試用戶
        $roles = ['member', 'admin', 'owner'];
        $departments = ['工程部', '設計部', '行銷部', '業務部', '人資部', '財務部', '客服部'];
        
        for ($i = 1; $i <= 35; $i++) {
            $firstName = $faker->lastName;
            $lastName = $faker->firstName;
            $fullName = $firstName . $lastName;
            $englishName = $faker->firstNameMale;
            
            $user = User::create([
                'full_name' => $fullName,
                'display_name' => $englishName,
                'email' => 'test.user' . $i . '@purpledesk.com',
                'password' => Hash::make('password'),
                'locale' => $faker->randomElement(['zh_TW', 'en_US']),
                'timezone' => $faker->randomElement(['Asia/Taipei', 'Asia/Tokyo', 'America/New_York']),
                'email_notifications' => $faker->boolean(70),
                'browser_notifications' => $faker->boolean(50),
                'theme' => $faker->randomElement(['light', 'dark', 'auto']),
            ]);
            
            // 隨機分配到 1-3 個組織
            $orgCount = $faker->numberBetween(1, 3);
            $selectedOrgs = $faker->randomElements($organizations, $orgCount);
            
            foreach ($selectedOrgs as $org) {
                if ($org) {
                    $user->organizations()->attach($org->id, [
                        'role' => $faker->randomElement($roles),
                        'created_at' => $faker->dateTimeBetween('-2 years', 'now'),
                    ]);
                    
                    // 為每個組織建立團隊
                    $teamCount = $faker->numberBetween(1, 3);
                    for ($t = 1; $t <= $teamCount; $t++) {
                        $team = Team::firstOrCreate([
                            'name' => $faker->randomElement($departments) . ' - ' . $org->name,
                            'organization_id' => $org->id,
                        ], [
                            'description' => $faker->sentence(8),
                        ]);
                        
                        // 隨機加入團隊（檢查是否已存在）
                        if ($faker->boolean(60) && !$user->teams()->where('team_id', $team->id)->exists()) {
                            $user->teams()->attach($team->id, [
                                'role' => $faker->randomElement(['member', 'lead']),
                                'created_at' => $faker->dateTimeBetween('-1 year', 'now'),
                            ]);
                        }
                    }
                }
            }
        }
        
        $this->command->info('測試資料建立完成！');
        $this->command->info('- 建立了 ' . count($orgNames) . ' 個新組織');
        $this->command->info('- 建立了 35 個測試用戶');
        $this->command->info('- 建立了多個團隊和關聯');
    }
}