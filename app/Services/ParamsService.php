<?php

namespace App\Services;

use InvalidArgumentException;

class ParamsService implements ParamsInterface
{
    private array $properties = [];
    private string $enumClass;
    /**
     * fromArray()からインスタンス化することを前提としているためnew()は不可とします。
     * @param string $enumClass
     * @param array $properties
     */
    public function __construct(string $enumClass, array $properties = [])
    {
        // enumClassの型チェック
        if (!is_subclass_of($enumClass, \UnitEnum::class) &&
            !is_subclass_of($enumClass, ParamsPropertyName::class)
        ) {
            throw new InvalidArgumentException("The provided class must be an enum.");
        }
        // プロパティの名前をローワーキャメルケースに変換
        foreach ($properties as $key => $value) {
            $camelCaseKey = self::toLowerCamelCase($key);
            $this->properties[$camelCaseKey] = $value;
        }
        $this->enumClass = $enumClass;
    }

    /**
     * プロパティを配列形式で取得する
     * @return array プロパティを含む配列
     */
    public function toArray(): array
    {
        $array = [];
        foreach ($this->enumClass::cases() as $case) {
            $propertyName = self::toLowerCamelCase($case->name);
            // プロパティが存在する場合のみ追加
            if (array_key_exists($propertyName, $this->properties)) {
                $array[$case->value] = $this->properties[$propertyName];
            }
        }
        return $array;
    }

    /**
     * スネークケースの文字列をローワーキャメルケース（lowerCamelCase）に変換します。
     * ParamsPropertyNameを継承するEnumのケースの名前はスネークケースを想定しているためです。
     *
     * このメソッドは、入力されたスネークケース（SNAKE_CASE）の文字列を小文字のキャメルケース（lowerCamelCase）に変換します。
     * 具体的には、全ての文字を小文字に変換し、アンダースコアで区切られた各単語の 2 番目以降の最初の文字を大文字に変換します。
     *
     * @param string $input スネークケース形式の文字列。
     * @return string ローワーキャメルケースに変換された文字列。
     */
    private static function toLowerCamelCase(string $input): string
    {
        $input = strtolower($input);
        $words = explode('_', $input);
        $firstWord = array_shift($words);
        $words = array_map('ucfirst', $words);
        return $firstWord . implode('', $words);
    }

    /**
     * @param string $name プロパティの名前
     * @param mixed $value 設定する値
     * @return void
     * @throws InvalidArgumentException プロパティ名が定義されていない場合、または型が一致しない場合
     */
    public function __set(string $name, mixed $value): void
    {
        foreach ($this->enumClass::cases() as $case) {
            $propertyName = self::toLowerCamelCase($case);
            if ($propertyName === $name) {
                $this->properties[$name] = $value;
                return;
            }
        }

        throw new InvalidArgumentException("Undefined property: $name");
    }

    /**
     * @param string $name プロパティの名前
     * @return mixed プロパティの値
     * @throws InvalidArgumentException プロパティ名が定義されていない場合
     */
    public function __get(string $name): mixed
    {
        if (array_key_exists($name, $this->properties)) {
            return $this->properties[$name];
        }

        throw new InvalidArgumentException("Undefined property: $name");
    }

    /**
     * enumクラス名を取得する
     * @return string enumクラス名
     */
    public function getEnumClass(): string
    {
        return $this->enumClass;
    }
}
