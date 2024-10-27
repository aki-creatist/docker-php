<?php

namespace App\Http\Controllers;

class MasterController extends Controller
{
    public function create($options = [])
    {
        var_dump($this->masterModel->all());
    }
}
