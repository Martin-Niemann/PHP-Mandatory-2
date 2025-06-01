<?php

require_once 'DBCredentials.php';
require_once 'Logger.php';

Class Database extends DBCredentials
{
    protected ?PDO $pdo;

    public function __construct()
    {
        $dsn = "mysql:host={$this->host};dbname={$this->dbname};charset=utf8mb4";
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ];
        
        try {
            $this->pdo = new PDO($dsn, $this->user, $this->password, $options);
        } catch (\PDOException $e) {
            Logger::logText('Error opening connection to database: ', $e);
        }
    }

    public function __destruct()
    {
        $this->pdo = null;
    }

    public function fetch($sql, $bindValues): array {
        try {
            $stmt = $this->pdo->prepare($sql);
            if ($bindValues !== null) {
                foreach ($bindValues as $bind) {
                    $stmt->bindValue(':' . $bind->bindName, $bind->bindValue);
                }
            }
            $stmt->execute();

            // I wish I had defer for this line
            $result = array_change_key_case($stmt->fetchAll());

            if (count($result) === 0) {
                throw new EmptyFetch();
            }

            return $result;
        } catch (PDOException $e) {
            Logger::logText($e);
            throw $e;
        }
    }

    public function queryRowCount($sql, $bindValues): int {
        try {
            $stmt = $this->pdo->prepare($sql);
            if ($bindValues !== null) {
                foreach ($bindValues as $bind) {
                    $stmt->bindValue(':' . $bind->bindName, $bind->bindValue);
                }
            }
            $stmt->execute();
            
            return $stmt->rowCount();
        } catch (PDOException $e) {
            Logger::logText($e);
            throw $e;
        }
    }

    public function queryInsertId($sql, $bindValues): string {
        try {
            $stmt = $this->pdo->prepare($sql);
            if ($bindValues !== null) {
                foreach ($bindValues as $bind) {
                    $stmt->bindValue(':' . $bind->bindName, $bind->bindValue);
                }
            }
            $stmt->execute();
            
            return $this->pdo->lastInsertId();
        } catch (PDOException $e) {
            Logger::logText($e);
            throw $e;
        }
    }
}

Class BindValues {
    public $bindName;
    public $bindValue;

    public function __construct($name, $value) {
        $this->bindName = $name;
        $this->bindValue = $value;
    }
}