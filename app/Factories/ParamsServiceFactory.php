<?php

namespace App\Factories;

use InvalidArgumentException;
use App\Enums\ParamsPropertyName;
use App\Enums\ParamsPropertyType;
use App\Services\ParamsService;

class ParamsServiceFactory
{
    public function make(
        array  $array,
        string $enumClass,
        array  $excludeEnumCases = []
    ): ParamsService {
        // Enumクラスが正しいかどうかを検証
        if (!is_subclass_of($enumClass, \UnitEnum::class) &&
            !is_subclass_of($enumClass, ParamsPropertyName::class)
        ) {
            throw new InvalidArgumentException("Invalid enum class: $enumClass");
        }
        $properties = [];

        /**
         * #1 除外対象のキーの配列に含まれる要素は処理から除外する
         * #2 配列中にEnumのケースに該当するキーが無い場合、Enumに定義したデフォルト値を入れる
         * #3 必須のキーが配列中に含まれていない場合はここでエラーがthrowされる
         * #4 プロパティに設定したい値の想定する型をEnumから取得
         * #5 渡された値がEnumに定義された型と一致するかチェック
         * #6 存在しないクラスが指定されている場合は例外を投げる
         * #7 OBJECTが期待されたOBJECTではない場合は例外を投げる
         */
        foreach ($enumClass::cases() as $case) {
            if (in_array($case, $excludeEnumCases)) { // #1
                continue;
            }
            if (!array_key_exists($case->value, $array)) { #2
                $array[$case->value] = $case->getPropertyDefaultValue(); #3
            }
            $propertyValue = $array[$case->value];

            $type = $case->getPropertyType(); #4
            if (self::validateType($propertyValue, $type)) { #5
                // クラスインスタンスの検証
                if ($type === ParamsPropertyType::OBJECT) {
                    $className = $case->getClass();
                    if (!class_exists($className)) { #6
                        throw new InvalidArgumentException("The class $className for $case->name does not exist.");
                    }
                    if (!$propertyValue instanceof $className) { #7
                        $actualClass = is_object($propertyValue) ? get_class($propertyValue) : gettype($propertyValue);
                        throw new InvalidArgumentException("Expected instance of $className for $case->name, $actualClass given.");
                    }
                }
                $properties[$case->name] = $propertyValue;
            } else {
                $actual = gettype($propertyValue);
                throw new InvalidArgumentException("Expected type for $case->name is $type->value, $actual given.");
            }
        }
        return new ParamsService($enumClass, $properties);
    }

    /**
     * プロパティの型を検証する静的メソッド
     *
     * このメソッドでは、指定されたプロパティの名前とその値が
     * 期待される型と一致するかどうかを検証します。型が一致しない場合には
     * InvalidArgumentException をスローします。
     *
     * @param mixed $value プロパティの値
     * @param ParamsPropertyType $expectedType 期待される型
     * @return bool
     * @throws InvalidArgumentException 型が一致しない場合
     */
    private static function validateType(mixed $value, ParamsPropertyType $expectedType): bool
    {
        $expectedType = $expectedType->value;
        $actualType = gettype($value);
        // nullable 型への対応
        $nullable = false;
        if (str_contains($expectedType, 'null')) {
            $nullable = true;
            $expectedType = str_replace('|null', '', $expectedType);
        }
        if ($actualType !== $expectedType) {
            if ($nullable && $actualType === 'NULL') {
                return true;
            }
            return false;
        }
        return true;
    }
}
