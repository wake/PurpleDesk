<?php

namespace App\Icon\Types;

use App\Icon\Color;
use App\Icon\IconTypeInterface;

class TextIcon implements IconTypeInterface
{
    /**
     * 隨機文字範例
     */
    private array $sampleTexts = [
        'AB', 'CD', 'EF', 'GH', 'JK', 'LM', 'NP', 'QR', 'ST', 'UV', 'WX', 'YZ',
        '測試', '範例', '專案', '團隊', '組織', '使用', '管理', '系統',
    ];
    
    public function getType(): string
    {
        return 'text';
    }
    
    public function getRequiredFields(): array
    {
        return ['type', 'text', 'backgroundColor', 'textColor'];
    }
    
    public function getOptionalFields(): array
    {
        return [];
    }
    
    public function validate(array $data): bool
    {
        // 檢查類型
        if (!isset($data['type']) || $data['type'] !== 'text') {
            return false;
        }
        
        // 檢查必要欄位
        foreach ($this->getRequiredFields() as $field) {
            if (!isset($data[$field]) || empty($data[$field])) {
                return false;
            }
        }
        
        // 檢查文字長度（1-2 個字符）
        $textLength = mb_strlen($data['text']);
        if ($textLength < 1 || $textLength > 2) {
            return false;
        }
        
        // 檢查背景色是否有效
        if (!Color::isAllowedBackgroundColor($data['backgroundColor'])) {
            return false;
        }
        
        // 檢查文字顏色是否為允許的顏色（白色或深灰色）
        $textColor = Color::normalizeHexColor($data['textColor']);
        if (!in_array($textColor, ['#ffffff', '#1f2937'], true)) {
            return false;
        }
        
        return true;
    }
    
    public function generateRandom(): array
    {
        $backgroundColor = Color::randomStandardColor();
        $textColor = Color::getContrastColor($backgroundColor);
        
        return [
            'type' => 'text',
            'text' => $this->sampleTexts[array_rand($this->sampleTexts)],
            'backgroundColor' => $backgroundColor,
            'textColor' => $textColor,
        ];
    }
    
    /**
     * 取得使用者的預設圖標
     */
    public function getDefaultForUser(string $fullName): array
    {
        $text = $this->generateInitials($fullName);
        
        return [
            'type' => 'text',
            'text' => $text,
            'backgroundColor' => '#6366f1', // indigo-500
            'textColor' => '#ffffff',
        ];
    }
    
    /**
     * 從名字生成首字母或前兩個字
     */
    private function generateInitials(string $fullName): string
    {
        $fullName = trim($fullName);
        
        if (empty($fullName)) {
            return '?';
        }
        
        // 檢查是否包含 CJK 字符（中日韓文字）
        if (preg_match('/[\p{Han}\p{Hiragana}\p{Katakana}\p{Hangul}]/u', $fullName)) {
            // 取前兩個字符
            return mb_substr($fullName, 0, 2);
        } else {
            // 西方名字，取首字母
            $parts = explode(' ', $fullName);
            $initials = '';
            
            foreach ($parts as $part) {
                $part = trim($part);
                if (!empty($part)) {
                    $initials .= mb_strtoupper(mb_substr($part, 0, 1));
                    if (mb_strlen($initials) >= 2) {
                        break;
                    }
                }
            }
            
            // 如果只有一個字母，就只返回一個
            return mb_substr($initials, 0, 2);
        }
    }
}