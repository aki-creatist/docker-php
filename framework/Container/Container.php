<?php

namespace Framework\Container;

use Exception;

class Container {
    protected $bindings = [];
    protected $singletons = [];

    public function bind($abstract, $concrete) {
        $this->bindings[$abstract] = $concrete;
    }

    public function singleton($abstract, $concrete) {
        $this->singletons[$abstract] = $concrete;
    }

    public function make($abstract) {
        // シングルトンに登録されている場合はそれを返す
        if (isset($this->singletons[$abstract])) {
            if (is_callable($this->singletons[$abstract])) {
                // callableならインスタンスを生成して保存
                $this->singletons[$abstract] = ($this->singletons[$abstract])($this);
            }
            return $this->singletons[$abstract];
        }

        // バインドに登録されている場合は都度インスタンスを生成
        if (isset($this->bindings[$abstract])) {
            return ($this->bindings[$abstract])($this);
        }

        throw new Exception("No binding for {$abstract}");
    }
}
