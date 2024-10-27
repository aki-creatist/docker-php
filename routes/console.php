<?php

use App\Http\Controllers\MasterController;
use Framework\Console\Route;

/**
 * Laravel 風のルート定義を使用
 */
Route::registerRoute('master', MasterController::class);
Route::get('master/hoge', [MasterController::class, 'hoge']);
Route::get('custom_task', function ($options = []) {
    echo "カスタムタスクを実行します。\n";
    // 追加のロジック
});