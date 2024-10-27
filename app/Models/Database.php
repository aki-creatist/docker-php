<?php

namespace App\Models;

use App\Enums\TableName;
use Exception;
use PDOException;

class Database extends BaseModel
{
    protected $pdo;
    private string $order = '';
    private string $limit   = '';
    private string $offset  = '';
    private string $groupby = '';

    public function select(TableName $table, string $column = '', string $where = ''): array
    {
        $sql = $this->getSql($table->value, $where, $column);
        $stmt = $this->pdo->query($sql);
        $data = [];
        foreach ($stmt as $key => $value) {
            array_push($data, $value);
        }
        return $data;
    }

    private function getSql(string $table, string $where = '', string $column = ''): string
    {
        $column_key = ($column !=='') ? $column : "*";
        $where_sql = ($where !== '') ? ' WHERE  ' . $where : '';
        $other = $this->groupby . " " . $this->order . " " . $this->limit . " " . $this->offset;
        return " SELECT " . $column_key . " FROM " . $table . $where_sql . $other;
    }

    public function bulkInsertData(string $table, array $data = []): bool
    {
        // データが空の場合は処理を終了
        if (empty($data)) {
            throw new Exception('データが空です');
        }
        // 各行のデータを準備する
        foreach ($data as &$row) {
            $row['food_number'] = $this->pdo->quote($row[1]);  // 文字列としてエスケープ
            $row['index_number'] = $this->pdo->quote($row[2]); // 文字列としてエスケープ
            $row['food_group'] = (int) $row[0];  // 数値として扱う
        }
        unset($row);

        // 各レコードの値をカンマで区切る
        $valueRows = [];
        foreach ($data as $row) {
            $valueRows[] = "({$row['food_group']}, {$row['food_number']}, {$row['index_number']})";
        }

        // SQL文を組み立てる
        $sql = "INSERT INTO " . $table . " (food_group, food_number, index_number) VALUES "
            . implode(',', $valueRows);

        try {
            // クエリの準備と実行
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            return true;  // 成功した場合はtrueを返す
        } catch (PDOException $e) {
            echo $e->getMessage() . "\n";
            return false;  // 失敗した場合はfalseを返す
        }
    }

    public function insert(string $table, array $ins_data = [], array $columns = [])
    {
        foreach ($ins_data as $key => $value) {
            if (is_string($value)) {
                $ins_data[$key] = $this->pdo->quote($value);
            }
        }
        $sql = " INSERT INTO " . $table . " ("
            .    implode(',', $columns)
            . ") VALUES ("
            .    implode(',', $ins_data)
            . ") "
        ;
        try {
            $stmt = $this->pdo->prepare($sql);
        } catch (PDOException $e) {
            echo $e->getMessage() . "\n";die;
        }
        $stmt->execute();
    }

    public function update(string $table, array $set, string $where)
    {
        $values = [];
        foreach ($set as $key => $value) {
            if (is_string($value)) {
                $value = $this->pdo->quote($this->codeRegex($value));
            }
//            if ($value === null) {
//                $value = 'NULL';
//            }
            $values[] = implode('=', [$key, $value]);
        }
        $values = implode(',', $values);
        $sql = " UPDATE "
            .    $table
            . " SET "
            .    $values
            . " WHERE "
            .    $where
        ;
        echo $sql . "\n";
        try {
            $stmt = $this->pdo->prepare($sql);
        } catch (PDOException $e) {
            echo $e->getMessage() . "\n";die;
        }
//        $stmt->execute();
    }

    /**
     * 含まれている記号を削除する
     * @param string|null $str
     * @return string|null
     */
    private function codeRegex(?string $str): ?string
    {
        if (is_null($str)) return null;
        $arr = ['(', ')', '＜', '＞', '（', '）', '［', '］'];
        foreach ($arr as $replaceTarget) {
            $str = str_replace($replaceTarget, '', $str);
        }
        return $str;
    }
}
