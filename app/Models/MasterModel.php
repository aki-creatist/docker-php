<?php

namespace App\Models;

use App\Enums\TableName;

class MasterModel extends Database
{
    public function __construct()
    {
        parent::__construct();
    }

    public function all()
    {
        return $this->select(TableName::TEST_TABLE);
    }
}
