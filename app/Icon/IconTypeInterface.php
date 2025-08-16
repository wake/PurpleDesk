<?php

namespace App\Icon;

interface IconTypeInterface
{
    /**
     * 驗證圖標資料是否有效
     * 
     * @param array $data 圖標資料
     * @return bool
     */
    public function validate(array $data): bool;
    
    /**
     * 生成隨機圖標資料
     * 
     * @return array
     */
    public function generateRandom(): array;
    
    /**
     * 取得圖標類型名稱
     * 
     * @return string
     */
    public function getType(): string;
    
    /**
     * 取得必要欄位列表
     * 
     * @return array
     */
    public function getRequiredFields(): array;
    
    /**
     * 取得選填欄位列表
     * 
     * @return array
     */
    public function getOptionalFields(): array;
}