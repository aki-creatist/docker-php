<?php

namespace Framework\Enums;

/**
 * Paramsオブジェクトのプロパティの型の列挙
 */
enum ParamsPropertyType: string
{
    case STRING = 'string';
    case INTEGER = 'integer';
    case BOOLEAN = 'boolean';
    case ARRAY = 'array';
    case OBJECT = 'object';

    case NULLABLE_STRING = 'string|null';
    case NULLABLE_INTEGER = 'integer|null';
    case NULLABLE_ARRAY = 'array|null';
    case NULLABLE_OBJECT = 'object|null';
}
