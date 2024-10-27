<?php

namespace App\Services;

use InvalidArgumentException;

interface ParamsInterface
{
    /**
     * プロパティを配列形式で取得する
     * @return array プロパティを含む配列
     */
    public function toArray(): array;

    /**
     * enumクラス名を取得する
     * @return string enumクラス名
     */
    public function getEnumClass(): string;

    /**
     * @param string $name プロパティの名前
     * @return mixed プロパティの値
     * @throws InvalidArgumentException プロパティ名が定義されていない場合
     */
    public function __get(string $name): mixed;

    /**
     * @param string $name プロパティの名前
     * @param mixed $value 設定する値
     * @return void
     * @throws InvalidArgumentException プロパティ名が定義されていない場合、または型が一致しない場合
     */
    public function __set(string $name, mixed $value): void;
}
