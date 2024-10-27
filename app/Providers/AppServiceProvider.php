<?php

namespace App\Providers;

use App\Factories\ParamsServiceFactory;
use App\Http\Controllers\MasterController;
use App\Models\MasterModel;
use Framework\Container\Container;
use Framework\Providers\ServiceProvider;

class AppServiceProvider extends ServiceProvider {
    protected Container $container;

    public function __construct($container) {
        parent::__construct($container);
        $this->container = $container;
    }

    public function register(): void {
        // MasterControllerのバインディングを追加
        $this->container->bind(MasterController::class, function($c) {
            return new MasterController(
                $c->make(ParamsServiceFactory::class),
                $c->make(MasterModel::class)
            );
        });

        // 依存するサービスをシングルトンで登録
        $this->container->singleton(ParamsServiceFactory::class, function () {
            return new ParamsServiceFactory();
        });
        $this->container->singleton(MasterModel::class, function () {
            return new MasterModel();
        });
    }
}
