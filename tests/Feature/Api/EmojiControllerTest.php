<?php

namespace Tests\Feature\Api;

use Tests\TestCase;

class EmojiControllerTest extends TestCase
{
    /**
     * 測試取得所有 emoji 資料的 API
     */
    public function test_can_get_all_emojis()
    {
        $response = $this->getJson('/api/config/icon/emoji');
        
        $response->assertStatus(200)
            ->assertJsonStructure([
                'categories' => [
                    '*' => [
                        'id',
                        'name',
                        'icon',
                        'priority',
                        'subgroups' => [
                            '*' => [
                                'name',
                                'emojis' => [
                                    '*' => [
                                        'emoji',
                                        'name'
                                    ]
                                ]
                            ]
                        ]
                    ]
                ],
                'stats' => [
                    'total_categories',
                    'total_emojis'
                ]
            ]);
    }
    
    /**
     * 測試 emoji 資料包含所有必要的分類
     */
    public function test_emoji_data_contains_expected_categories()
    {
        $response = $this->getJson('/api/config/icon/emoji');
        
        $data = $response->json();
        
        // 驗證包含所有主要分類
        $expectedCategories = [
            'smileys_emotion',
            'people_body',
            'animals_nature',
            'food_drink',
            'travel_places',
            'activities',
            'objects',
            'symbols',
            'flags'
        ];
        
        foreach ($expectedCategories as $category) {
            $this->assertArrayHasKey($category, $data['categories']);
        }
    }
    
    /**
     * 測試 emoji 統計資料
     */
    public function test_emoji_statistics()
    {
        $response = $this->getJson('/api/config/icon/emoji');
        
        $data = $response->json();
        
        // 驗證統計資料
        $this->assertArrayHasKey('stats', $data);
        $this->assertArrayHasKey('total_categories', $data['stats']);
        $this->assertArrayHasKey('total_emojis', $data['stats']);
        
        // 驗證分類數量（應該是 9 個）
        $this->assertEquals(9, $data['stats']['total_categories']);
        
        // 驗證總 emoji 數量合理（已過濾不相容的）
        $this->assertGreaterThan(3000, $data['stats']['total_emojis']);
        $this->assertLessThan(4000, $data['stats']['total_emojis']);
    }
    
    /**
     * 測試 emoji 資料格式正確
     */
    public function test_emoji_data_format()
    {
        $response = $this->getJson('/api/config/icon/emoji');
        
        $data = $response->json();
        
        // 取得第一個分類的第一個子群組的第一個 emoji 來驗證格式
        $firstCategory = array_values($data['categories'])[0];
        $this->assertIsArray($firstCategory['subgroups']);
        
        if (!empty($firstCategory['subgroups'])) {
            $firstSubgroup = array_values($firstCategory['subgroups'])[0];
            $this->assertArrayHasKey('name', $firstSubgroup);
            $this->assertArrayHasKey('emojis', $firstSubgroup);
            
            if (!empty($firstSubgroup['emojis'])) {
                $firstEmoji = $firstSubgroup['emojis'][0];
                $this->assertArrayHasKey('emoji', $firstEmoji);
                $this->assertArrayHasKey('name', $firstEmoji);
                $this->assertIsString($firstEmoji['emoji']);
                $this->assertIsString($firstEmoji['name']);
            }
        }
    }
    
    /**
     * 測試 API 回應時間合理
     */
    public function test_api_response_time()
    {
        $startTime = microtime(true);
        
        $response = $this->getJson('/api/config/icon/emoji');
        
        $endTime = microtime(true);
        $responseTime = ($endTime - $startTime) * 1000; // 轉換為毫秒
        
        $response->assertStatus(200);
        
        // API 回應時間應該小於 500ms
        $this->assertLessThan(500, $responseTime, 'API response time is too slow');
    }
    
    /**
     * 測試優先級設定正確
     */
    public function test_emoji_priority_settings()
    {
        $response = $this->getJson('/api/config/icon/emoji');
        
        $data = $response->json();
        
        // 驗證優先級值
        $validPriorities = ['immediate', 'high', 'medium', 'low'];
        
        foreach ($data['categories'] as $category) {
            $this->assertContains($category['priority'], $validPriorities);
        }
        
        // 驗證 smileys_emotion 應該是 immediate 優先級
        $this->assertEquals('immediate', $data['categories']['smileys_emotion']['priority']);
    }
}