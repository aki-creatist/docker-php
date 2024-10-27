<?php

namespace App\Models;

use PDO;
use PDOException;

class BaseModel
{
    protected $pdo;
    public function __construct()
    {
        $this->connect();
    }

    private function connect()
    {
        try {
            switch (TYPE) {
                case 'mysql':
                    $dsn = 'mysql:host=' . HOST . ';dbname=' . NAME . ';charset=utf8';
                    break;
                case 'pgsql':
                    $dsn = 'pgsql:dbname=' . NAME.' host=' . HOST . ' port=5432';
                    break;
            }
            $this->pdo = new PDO($dsn, USER, PASS);
            //エラー情報を取得するための属性の設定
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            //プリペアドステートメントを利用可能にする
            $this->pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
            $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            die('接続エラー:' . $e->getMessage());
        }
    }
}
