<?php

namespace Framework\Console;

/**
 * ルートを登録し、指定されたルート文字列に基づいて対応するコントローラとメソッドを解決します。
 */
class Router
{
    protected $routes = [];

    public function register($uri, $action)
    {
        $this->routes[] = ['uri' => $uri, 'action' => $action];
    }

    public function resolve($uri)
    {
        foreach ($this->routes as $route) {
            // パラメータを考慮したパターンマッチング
            $pattern = "@^" . preg_replace('/\{[^\/]+\}/', '([^/]+)', $route['uri']) . "$@";
            if (preg_match($pattern, $uri, $matches)) {
                array_shift($matches); // $matches[0] は完全マッチなので除外
                return ['action' => $route['action'], 'params' => $matches];
            }
        }
        return null;
    }
}