<?php

namespace Framework\Console;

class Route
{
    private static Router $router;

    public static function setRouter(Router $router): void
    {
        self::$router = $router;
    }

    public static function registerRoute($uri, $controllerClass): void
    {
        self::$router->register($uri . '/create', [$controllerClass, 'create']);
        self::$router->register($uri . '/delete', [$controllerClass, 'delete']);
        self::$router->register($uri . '/store', [$controllerClass, 'store']); // 新規作成
        self::$router->register($uri . '/{id}', [$controllerClass, 'show']); // 詳細表示
        self::$router->register($uri . '/{id}/edit', [$controllerClass, 'edit']); // 編集フォーム表示
        self::$router->register($uri . '/{id}/update', [$controllerClass, 'update']); // 更新
        self::$router->register($uri . '/{id}/destroy', [$controllerClass, 'destroy']); // 削除
    }

    public static function get($uri, $action): void
    {
        self::$router->register($uri, $action);
    }
}