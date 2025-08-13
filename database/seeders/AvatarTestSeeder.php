<?php

namespace Database\Seeders;

use App\Helpers\AvatarHelper;
use App\Models\User;
use App\Models\Organization;
use App\Models\Team;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AvatarTestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $testAvatars = AvatarHelper::generateTestAvatarData();
        
        // 清理現有測試數據
        User::where('email', 'like', 'test%@example.com')->delete();
        Organization::where('name', 'like', '測試組織%')->delete();
        
        // 創建測試組織
        $org1 = Organization::create([
            'name' => '測試組織1 - 預設頭像',
            'description' => '使用預設紫色建築圖標的組織'
        ]);
        
        $org2 = Organization::create([
            'name' => '測試組織2 - 自訂圖標',
            'avatar' => $testAvatars['bs_fill'],
            'description' => '使用自訂 Bootstrap 圖標的組織'
        ]);
        
        $org3 = Organization::create([
            'name' => '測試組織3 - Emoji',
            'avatar' => $testAvatars['emoji_simple'],
            'description' => '使用 Emoji 頭像的組織'
        ]);
        
        // 創建測試團隊
        $team1 = Team::create([
            'name' => '測試團隊1 - 預設頭像',
            'description' => '使用預設藍色團隊圖標的團隊',
            'organization_id' => $org1->id
        ]);
        
        $team2 = Team::create([
            'name' => '測試團隊2 - Hero圖標',
            'avatar' => $testAvatars['hero_solid'],
            'description' => '使用 Hero 圖標的團隊',
            'organization_id' => $org1->id
        ]);
        
        $team3 = Team::create([
            'name' => '測試團隊3 - Emoji膚色',
            'avatar' => $testAvatars['emoji_skin_tone'],
            'description' => '使用帶膚色 Emoji 的團隊',
            'organization_id' => $org2->id
        ]);
        
        // 創建測試用戶
        $users = [
            [
                'full_name' => '張小明',
                'email' => 'test1@example.com',
                'avatar' => null, // 使用預設頭像
                'description' => '預設個人頭像 - 名字第一字母+隨機色'
            ],
            [
                'full_name' => '李小美',
                'email' => 'test2@example.com', 
                'avatar' => $testAvatars['text_custom'],
                'description' => '自訂文字頭像'
            ],
            [
                'full_name' => '王大華',
                'email' => 'test3@example.com',
                'avatar' => $testAvatars['emoji_simple'],
                'description' => 'Emoji 頭像'
            ],
            [
                'full_name' => '陳小婷',
                'email' => 'test4@example.com',
                'avatar' => $testAvatars['emoji_skin_tone'],
                'description' => 'Emoji + 膚色頭像'
            ],
            [
                'full_name' => '林志強',
                'email' => 'test5@example.com',
                'avatar' => $testAvatars['hero_outline'],
                'description' => 'Hero Icon outline 頭像'
            ],
            [
                'full_name' => '吳雅婷',
                'email' => 'test6@example.com',
                'avatar' => $testAvatars['hero_solid'], 
                'description' => 'Hero Icon solid 頭像'
            ],
            [
                'full_name' => '許志明',
                'email' => 'test7@example.com',
                'avatar' => $testAvatars['bs_outline'],
                'description' => 'Bootstrap Icon outline 頭像'
            ],
            [
                'full_name' => '黃美玲',
                'email' => 'test8@example.com',
                'avatar' => $testAvatars['bs_fill'],
                'description' => 'Bootstrap Icon fill 頭像'
            ],
            [
                'full_name' => 'John Smith',
                'email' => 'test9@example.com',
                'avatar' => $testAvatars['image_sample'],
                'description' => '圖片頭像 (示例路徑)'
            ],
        ];
        
        foreach ($users as $userData) {
            $user = User::create([
                'account' => explode('@', $userData['email'])[0],
                'full_name' => $userData['full_name'],
                'email' => $userData['email'],
                'password' => 'password123',
                'avatar' => $userData['avatar'],
                'email_verified_at' => now(),
            ]);
            
            // 將用戶加入到組織
            $user->organizations()->attach($org1->id, ['role' => 'member']);
            if ($user->id % 2 === 0) {
                $user->organizations()->attach($org2->id, ['role' => 'member']);
            }
            
            // 將用戶加入到團隊
            $user->teams()->attach($team1->id, ['role' => 'member']);
            if ($user->id % 3 === 0) {
                $user->teams()->attach($team2->id, ['role' => 'member']);
            }
        }
        
        $this->command->info('頭像測試資料創建完成!');
        $this->command->info('已創建:');
        $this->command->info('- 3 個測試組織（不同頭像類型）');
        $this->command->info('- 3 個測試團隊（不同頭像類型）');
        $this->command->info('- 9 個測試用戶（涵蓋所有頭像類型）');
    }
}
