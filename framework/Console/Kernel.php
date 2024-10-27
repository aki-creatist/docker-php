<?php

namespace Framework\Console;

use Framework\Container\Container;

class Kernel
{
    private Router $router;
    private Container $container;


    public function __construct(Container $container)
    {
        $this->container = $container;

        $this->router = new Router();
        Route::setRouter($this->router);
        require_once './routes/console.php';
    }

    public function handle()
    {
        $parser = new CommandLineParser($_SERVER['argv']);

        if ($parser->uri === null) {
            die("使用法: php cli.php --uri=URI [--option=value ...]\n");
        }

        $uri = $parser->uri;
        $options = $parser->options;

        // ルート解決
        $result = $this->router->resolve($uri);

        if ($result === null) {
            die("ルートが見つかりません: $uri\n");
        }

        $action = $result['action'];
        $uriParams = $result['params'];

        if ($action instanceof \Closure) {
            // クロージャの場合は直接実行
            $methodParams = $uriParams;
            $methodParams[] = $options;

            call_user_func_array($action, $methodParams);
        } elseif (is_array($action)) {
            // コントローラクラスとメソッド名を取得
            $controllerClass = $action[0];
            $methodName = $action[1];

            if (class_exists($controllerClass) && method_exists($controllerClass, $methodName)) {
                // コントローラのインスタンスを作成
                $controller = $this->container->make($controllerClass);

                // メソッドに渡す引数を準備
                $methodParams = $uriParams;
                $methodParams[] = $options;

                // メソッドを実行
                call_user_func_array([$controller, $methodName], $methodParams);
            } else {
                echo "コントローラまたはメソッドが見つかりません: {$controllerClass}::{$methodName}\n";
            }
        } else {
            die("無効なルートアクションが指定されています。\n");
        }
    }
}