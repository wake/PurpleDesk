<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\EmojiService;

class EmojiController extends Controller
{
    private $emojiService;
    
    public function __construct(EmojiService $emojiService)
    {
        $this->emojiService = $emojiService;
    }
    
    /**
     * 取得所有 emoji 資料
     */
    public function all()
    {
        return response()->json($this->emojiService->getAllEmojis());
    }
}