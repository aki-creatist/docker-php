<?php

namespace Framework\Enums;

use InvalidArgumentException;

/**
 * xxxParams作成時のプロパティと型の列挙
 */
interface ParamsPropertyName
{
    public function getPropertyType(): ParamsPropertyType;

    /**
     * プロパティに設定する値の初期値
     * 備考: 必須のプロパティについては例外を投げること
     * @return mixed
     * @throws InvalidArgumentException 必須の値、及びオブジェクトの場合
     */
    public function getPropertyDefaultValue(): mixed;
}
