<?php

namespace App\Http\Controllers;

use App\Factories\ParamsServiceFactory;
use App\Models\MasterModel;

abstract class Controller
{
    protected ParamsServiceFactory $paramsFactory;
    protected MasterModel $masterModel;

    public function __construct(
        ParamsServiceFactory $paramsFactory,
        MasterModel $masterModel
    )
    {
        $this->paramsFactory = $paramsFactory;
        $this->masterModel = $masterModel;
    }
}