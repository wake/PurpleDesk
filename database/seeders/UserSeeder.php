<?php

namespace Database\Seeders;

use App\Models\Organization;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $prototypeOrg = Organization::where('name', '原型科技有限公司')->first();
        $designOrg = Organization::where('name', '紫色設計工作室')->first();

        // 管理員用戶
        $admin = User::create([
            'full_name' => '王小明',
            'display_name' => 'Ming',
            'email' => 'admin@purpledesk.com',
            'password' => Hash::make('password'),
        ]);
        
        if ($prototypeOrg) {
            $admin->organizations()->attach($prototypeOrg->id);
        }

        // 一般用戶
        $user1 = User::create([
            'full_name' => '李美華',
            'display_name' => 'Lily',
            'email' => 'user1@purpledesk.com',
            'password' => Hash::make('password'),
        ]);
        
        if ($prototypeOrg) {
            $user1->organizations()->attach($prototypeOrg->id);
        }

        $designer = User::create([
            'full_name' => '張志傑',
            'display_name' => 'Jack',
            'email' => 'designer@purpledesk.com',
            'password' => Hash::make('password'),
        ]);
        
        if ($designOrg) {
            $designer->organizations()->attach($designOrg->id);
        }

        // 獨立用戶（沒有所屬單位）
        User::create([
            'full_name' => '陳自由',
            'display_name' => 'Free',
            'email' => 'freelancer@purpledesk.com',
            'password' => Hash::make('password'),
        ]);
    }
}
