<?php

namespace App\Http\Controllers;

use App\Enums\CsvParamsKey;

class MasterController extends Controller
{
    /**
     * php cli.php --uri=master/create --some-option=1
     * @param $options
     * @return void
     */
    public function create($options = [])
    {
        $params = $this->paramsFactory->make(['1', '2', '3', 'あわ'], CsvParamsKey::class);
        var_dump($params);
        var_dump($options);die;
        var_dump($this->masterModel->all());
    }
}
