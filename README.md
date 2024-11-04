# 概要

## 事前準備

```bash
docker-compose up -d
docker exec -it docker-php bash
cd php_libs/
composer require piece/stagehand-testrunner
```

```bash
curl http://localhost:8080/test.json
```

```shell
php cli.php --uri=master/create
```

```
project_root/
├── app/
│   ├── Controllers/
│   │   └── PageController.php
│   ├── Facades/
│   │   └── Log.php
│   └── Logger.php
├── core/
│   ├── Container.php
│   ├── Facade.php
│   └── Router.php
├── config/
│   └── services.php
└── public/
    └── index.php
```

```
<?php
// core/Router.php

class Router
{
    protected $routes = [];

    public function get($uri, $action)
    {
        $this->addRoute('GET', $uri, $action);
    }

    public function post($uri, $action)
    {
        $this->addRoute('POST', $uri, $action);
    }

    protected function addRoute($method, $uri, $action)
    {
        $uri = $this->trim($uri);
        $uri = $this->convertUriToRegex($uri);
        $this->routes[$method][$uri] = $action;
    }

    protected function trim($uri)
    {
        return trim($uri, '/');
    }

    protected function convertUriToRegex($uri)
    {
        // パスパラメータを正規表現に変換
        return preg_replace('#\{([a-zA-Z0-9_]+)\}#', '(?P<\1>[a-zA-Z0-9_-]+)', $uri);
    }

    public function dispatch($requestUri, $requestMethod)
    {
        $uri = $this->trim(parse_url($requestUri, PHP_URL_PATH));
        $method = $requestMethod;

        if (isset($this->routes[$method])) {
            foreach ($this->routes[$method] as $route => $action) {
                $pattern = '#^' . $route . '$#';
                if (preg_match($pattern, $uri, $matches)) {
                    // パスパラメータを抽出
                    $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);

                    if (is_callable($action)) {
                        call_user_func_array($action, $params);
                    } elseif (is_string($action)) {
                        list($controller, $method) = explode('@', $action);
                        $controller = "App\\Controllers\\" . $controller;
                        $controller = new $controller();
                        call_user_func_array([$controller, $method], $params);
                    }
                    return;
                }
            }
        }
        echo "404 Not Found";
    }
}
```

```
<?php
// app/Controllers/PageController.php

namespace App\Controllers;

class PageController
{
    public function home()
    {
        echo "ホームページへようこそ！";
    }

    public function about()
    {
        echo "これはAboutページです。";
    }

    public function showUser($id)
    {
        echo "ユーザーID: $id の情報を表示します。";
    }

    public function search()
    {
        $query = $_GET['q'] ?? '';
        echo "検索キーワード: $query";
    }
}
```

```
<?php
// public/index.php

// クラスファイルの読み込み
require_once __DIR__ . '/../core/Router.php';
require_once __DIR__ . '/../core/Container.php';
require_once __DIR__ . '/../app/Controllers/PageController.php';
require_once __DIR__ . '/../app/Logger.php';
require_once __DIR__ . '/../app/Facades/Log.php';

// サービスコンテナの初期化
$container = new Container();

// Facadeにコンテナを設定
Facade::setFacadeContainer($container);

// サービスのバインディングを読み込み
require_once __DIR__ . '/../config/services.php';

// ルーターの初期化
$router = new Router();

// ルートの定義
$router->get('/', 'PageController@home');
$router->get('about', 'PageController@about');
$router->get('user/{id}', 'PageController@showUser'); // パスパラメータの例
$router->get('search', 'PageController@search');      // GETパラメータの例
$router->get('log', function() {
    Log::log('これはテストメッセージです。');
});

// リクエストのディスパッチ
$router->dispatch($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD']);
```
