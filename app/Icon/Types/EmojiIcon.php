<?php

namespace App\Icon\Types;

use App\Icon\Color;
use App\Icon\IconTypeInterface;

class EmojiIcon implements IconTypeInterface
{
    
    public function getType(): string
    {
        return 'emoji';
    }
    
    public function getRequiredFields(): array
    {
        return ['type', 'emoji'];
    }
    
    public function getOptionalFields(): array
    {
        return ['backgroundColor'];
    }
    
    public function validate(array $data): bool
    {
        // 檢查類型
        if (!isset($data['type']) || $data['type'] !== 'emoji') {
            return false;
        }
        
        // 檢查必要欄位
        if (!isset($data['emoji']) || empty($data['emoji'])) {
            return false;
        }
        
        // 如果有背景色，檢查是否有效
        if (isset($data['backgroundColor'])) {
            if (!Color::isAllowedBackgroundColor($data['backgroundColor'])) {
                return false;
            }
        }
        
        return true;
    }
    
    public function generateRandom(): array
    {
        // 使用成對的顏色組合（優先使用淡色背景）
        $colorCombination = Color::randomColorCombination();
        
        return [
            'type' => 'emoji',
            'emoji' => $this->getRandomEmojiFromConfig(),
            'backgroundColor' => $colorCombination['background'],
        ];
    }
    
    /**
     * 取得隨機 emoji（不含背景色）
     */
    public function getRandomEmoji(): string
    {
        return $this->getRandomEmojiFromConfig();
    }
    
    /**
     * 從配置取得隨機 emoji
     */
    private function getRandomEmojiFromConfig(): string
    {
        $all = $this->getAllEmojis();
        return $all[array_rand($all)];
    }
    
    /**
     * 檢查 emoji 是否在安全列表中
     */
    public function isSafeEmoji(string $emoji): bool
    {
        return in_array($emoji, $this->getAllEmojis(), true);
    }
    
    /**
     * 取得所有 emoji（用於隨機生成和驗證）
     * 這是一個精選的常用 emoji 列表，用於測試和隨機生成
     */
    private function getAllEmojis(): array
    {
        // 精選的常用 emoji 列表
        return [
            // 表情
            '😀', '😃', '😄', '😁', '😊', '😍', '🥰', '😘', '🤗', '🤩',
            '😎', '🤓', '😇', '🙂', '😉', '😌', '😋', '😛', '😜', '🤪',
            // 手勢
            '👍', '👎', '👌', '✌️', '🤞', '🤟', '👏', '🙌', '👐', '🤝',
            // 愛心
            '❤️', '🧡', '💛', '💚', '💙', '💜', '🖤', '🤍', '🤎', '💕',
            // 動物
            '🐶', '🐱', '🐭', '🐹', '🐰', '🦊', '🐻', '🐼', '🐨', '🐯',
            // 自然
            '🌸', '🌺', '🌻', '🌷', '🌹', '🌿', '🍀', '🌳', '🌲', '🌴',
            // 食物
            '🍎', '🍊', '🍋', '🍌', '🍇', '🍓', '🍑', '🍒', '🥑', '🍅',
            // 活動
            '⚽', '🏀', '🏈', '⚾', '🎾', '🏐', '🎯', '🎮', '🎲', '🎨',
            // 物品
            '💼', '📚', '📖', '📝', '✏️', '📌', '📎', '🔖', '📅', '📆',
            // 符號
            '✅', '❌', '⭐', '🌟', '✨', '💫', '🔥', '💧', '⚡', '☀️'
        ];
    }
}