<?php

namespace Framework\Providers;

use Framework\Container\Container;

abstract class ServiceProvider
{
    protected $app;

    public function __construct(Container $container) {
        $this->app = $container;
    }

    public function register() {
        // 子クラスで実装
    }
}
