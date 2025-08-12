<?php

use Illuminate\Support\Facades\Route;

// 圖標系統測試頁面
Route::get('/test-icons', function () {
    return view('test-icons');
});

// Vue SPA 路由 - 所有前端路由都指向同一個 Blade 模板
Route::get('/{any}', function () {
    return view('app');
})->where('any', '.*');
