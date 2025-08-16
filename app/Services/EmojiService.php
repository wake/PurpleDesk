<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;

class EmojiService
{
    /**
     * 取得所有 emoji 資料（一次性載入）
     */
    public function getAllEmojis()
    {
        return Cache::remember('all_emojis', 86400, function () {
            $result = [
                'categories' => [],
                'stats' => [
                    'total_emojis' => 0,
                    'total_categories' => 0,
                ],
            ];
            
            // 載入索引檔案
            $indexPath = config_path('icon/emoji/index.php');
            if (!File::exists($indexPath)) {
                return $result;
            }
            
            $index = require $indexPath;
            $categories = $index['categories'] ?? [];
            
            // 載入每個分類的 emoji
            foreach ($categories as $categoryId => $categoryInfo) {
                $filePath = config_path('icon/emoji/' . $categoryInfo['file']);
                
                if (!File::exists($filePath)) {
                    continue;
                }
                
                $data = require $filePath;
                
                // 轉換為前端格式
                $categoryEmojis = [];
                foreach ($data as $subgroupKey => $subgroupData) {
                    $categoryEmojis[$subgroupKey] = [
                        'name' => $subgroupData['name'],
                        'emojis' => $subgroupData['emojis']
                    ];
                    
                    $result['stats']['total_emojis'] += count($subgroupData['emojis']);
                }
                
                $result['categories'][$categoryId] = [
                    'id' => $categoryId,
                    'name' => $categoryInfo['name'],
                    'icon' => $categoryInfo['icon'],
                    'priority' => $categoryInfo['priority'],
                    'subgroups' => $categoryEmojis
                ];
            }
            
            $result['stats']['total_categories'] = count($result['categories']);
            
            return $result;
        });
    }
    
    /**
     * 清除快取
     */
    public function clearCache()
    {
        Cache::forget('all_emojis');
    }
}