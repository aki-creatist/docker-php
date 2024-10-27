<?php

namespace Framework\Console;

class CommandLineParser
{
    public $uri;
    public $options;

    public function __construct($argv)
    {
        $this->parseArguments($argv);
    }

    private function parseArguments($argv)
    {
        // スクリプト名を除外
        array_shift($argv);

        $this->options = [];
        $this->uri = null;

        foreach ($argv as $arg) {
            if (strpos($arg, '--') === 0) {
                $eqPos = strpos($arg, '=');
                if ($eqPos !== false) {
                    $key = substr($arg, 2, $eqPos - 2);
                    $value = substr($arg, $eqPos + 1);
                } else {
                    $key = substr($arg, 2);
                    $value = true; // 値がない場合は true とする
                }

                if ($key === 'uri') {
                    $this->uri = $value;
                } else {
                    $this->options[$key] = $value;
                }
            }
        }
    }
}
