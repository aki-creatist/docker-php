<?php

namespace App\Models;

use App\Enums\TableName;

class MasterModel extends Database
{
    const TABLE = TableName::Masters->value;

    public function __construct()
    {
        parent::__construct();
    }

    public function insertFoodNumber(array $record)
    {
        $values = array_values($record);
        $this->bulkInsertData(self::TABLE, $values);
    }

    public function insertData(array $record)
    {
        $columns = array_keys($record);
        $values = array_values($record);
        $this->insert(self::TABLE, $values, $columns);
    }

    public function updateFlag($records)
    {
        foreach ($records as $foodNumber => $record) {
            $where = 'food_number=' . $foodNumber;
            $this->update(self::TABLE, $record, $where);
        }
    }

    public function updateDetail($records)
    {
        foreach ($records as $foodNumber => $record) {
            $where = 'food_number=' . $foodNumber;
            $this->update(self::TABLE, $record, $where);
        }
    }

    public function updateData($records)
    {
        foreach ($records as $foodNumber => $record) {
            $set = array_filter($record);
            $where = 'food_number=' . $foodNumber;
            $this->update(self::TABLE, $set, $where);
        }
    }
}
