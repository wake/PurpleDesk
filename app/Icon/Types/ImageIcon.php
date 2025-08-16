<?php

namespace App\Icon\Types;

use App\Icon\IconTypeInterface;

class ImageIcon implements IconTypeInterface
{
    public function getType(): string
    {
        return 'image';
    }
    
    public function getRequiredFields(): array
    {
        return ['type', 'path'];
    }
    
    public function getOptionalFields(): array
    {
        return [];
    }
    
    public function validate(array $data): bool
    {
        // 檢查類型
        if (!isset($data['type']) || $data['type'] !== 'image') {
            return false;
        }
        
        // 檢查必要欄位
        if (!isset($data['path']) || empty($data['path'])) {
            return false;
        }
        
        // 檢查是否為有效的路徑或 base64 資料
        $path = $data['path'];
        
        // 檢查是否為 base64 資料
        if (str_starts_with($path, 'data:image/')) {
            // 基本的 base64 格式驗證
            if (!preg_match('/^data:image\/(png|jpg|jpeg|gif|webp);base64,/', $path)) {
                return false;
            }
            
            // 取得 base64 部分
            $base64Part = substr($path, strpos($path, ',') + 1);
            
            // 驗證 base64 編碼
            if (!$this->isValidBase64($base64Part)) {
                return false;
            }
        } elseif (!str_starts_with($path, '/') && !str_starts_with($path, 'http')) {
            // 如果不是 base64，應該是路徑或 URL
            return false;
        }
        
        return true;
    }
    
    public function generateRandom(): array
    {
        // 生成範例圖片路徑
        $randomId = rand(1, 999);
        $types = ['avatars', 'logos', 'icons'];
        $type = $types[array_rand($types)];
        
        return [
            'type' => 'image',
            'path' => "/storage/{$type}/image_{$randomId}.jpg",
        ];
    }
    
    /**
     * 驗證 base64 字串
     */
    private function isValidBase64(string $string): bool
    {
        // 移除可能的空白字符
        $string = trim($string);
        
        // 檢查是否為有效的 base64 字串
        if (!preg_match('/^[a-zA-Z0-9\/\r\n+]*={0,2}$/', $string)) {
            return false;
        }
        
        // 嘗試解碼
        $decoded = base64_decode($string, true);
        
        // 如果解碼失敗，返回 false
        if ($decoded === false) {
            return false;
        }
        
        // 重新編碼並比較，確保原始字串是有效的 base64
        if (base64_encode($decoded) !== $string) {
            return false;
        }
        
        return true;
    }
    
    /**
     * 從檔案路徑生成 base64 資料 URL
     * 
     * @param string $filePath 檔案路徑
     * @return string|null base64 資料 URL 或 null（如果檔案不存在）
     */
    public function fileToBase64(string $filePath): ?string
    {
        if (!file_exists($filePath)) {
            return null;
        }
        
        $fileContent = file_get_contents($filePath);
        if ($fileContent === false) {
            return null;
        }
        
        $mimeType = mime_content_type($filePath);
        if (!str_starts_with($mimeType, 'image/')) {
            return null;
        }
        
        $base64 = base64_encode($fileContent);
        
        return "data:{$mimeType};base64,{$base64}";
    }
    
    /**
     * 檢查圖片檔案大小是否在限制內（2MB）
     * 
     * @param string $filePath 檔案路徑
     * @return bool
     */
    public function isFileSizeValid(string $filePath): bool
    {
        if (!file_exists($filePath)) {
            return false;
        }
        
        $fileSize = filesize($filePath);
        $maxSize = 2 * 1024 * 1024; // 2MB
        
        return $fileSize <= $maxSize;
    }
    
    /**
     * 取得支援的圖片格式
     * 
     * @return array
     */
    public function getSupportedFormats(): array
    {
        return ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    }
}