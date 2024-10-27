<?php

namespace backend;

$container = require_once __DIR__ . '/bootstrap/app.php';

/**
 * php cli.php --uri=master/create
 */

use Framework\Console\Kernel;

require_once './environment.php';
require_once './vendor/autoload.php';

$kernel = new Kernel($container);
$kernel->handle();