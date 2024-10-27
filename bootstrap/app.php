<?php

/**
 * コンテナのインスタンスを作成し、サービスプロバイダで依存関係を登録します。
 * このファイルは CLI と Web の両方から読み込まれ、共通の初期化処理を行います。
 */
use Framework\Container\Container;
use App\Providers\AppServiceProvider;

require_once __DIR__ . '/../vendor/autoload.php';

$container = new Container();

$serviceProvider = new AppServiceProvider($container);
$serviceProvider->register();

// コンテナを返す
return $container;