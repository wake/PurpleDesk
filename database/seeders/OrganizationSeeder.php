<?php

namespace Database\Seeders;

use App\Models\Organization;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OrganizationSeeder extends Seeder
{
    public function run(): void
    {
        $organizations = [
            [
                'name' => '智慧雲端服務',
                'description' => 'Cum laboriosam quia omnis nemo nobis necessitatibus architecto. Voluptatem doloremque aut vel eum.',
            ],
            [
                'name' => '數位行銷公司',
                'description' => 'Aut sint libero dignissimos ut vel eum.',
            ],
            [
                'name' => '網路解決方案',
                'description' => 'Qui ipsum dolore dolorem nihil qui non doloremque corruptas. Accusantium deserunt ipsum qui dolorem.',
            ],
            [
                'name' => '創新科技團隊',
                'description' => '專注於AI與機器學習技術研發的前沿科技公司',
            ],
            [
                'name' => '綠能永續企業',
                'description' => '致力於環保科技與永續發展解決方案',
            ],
            [
                'name' => '金融科技新創',
                'description' => '提供區塊鏈與數位支付創新服務',
            ],
            [
                'name' => '教育科技平台',
                'description' => '打造線上學習與教育管理系統',
            ],
            [
                'name' => '健康醫療科技',
                'description' => '結合醫療與資訊技術的健康管理平台',
            ],
            [
                'name' => '智慧物聯網',
                'description' => 'IoT設備與智慧家庭解決方案供應商',
            ],
            [
                'name' => '電子商務平台',
                'description' => '全方位電商解決方案與數據分析服務',
            ],
        ];

        foreach ($organizations as $orgData) {
            Organization::create($orgData);
        }
    }
}
