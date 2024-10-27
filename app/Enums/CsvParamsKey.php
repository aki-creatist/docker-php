<?php

namespace App\Enums;

use InvalidArgumentException;

enum CsvParamsKey: int implements ParamsPropertyName
{
    case FOOD_GROUP = 0;
    case FOOD_NUMBER = 1;
    case INDEX_NUMBER = 2;
    case FOOD_NAME = 3;

    public function getPropertyType(): ParamsPropertyType
    {
        return ParamsPropertyType::STRING;
    }

    public function getPropertyDefaultValue(): string
    {
        return throw new InvalidArgumentException("いずれの値も必須です。");
    }
}
