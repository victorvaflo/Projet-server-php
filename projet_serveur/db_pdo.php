<?php

if (!isset($index_loaded)) {
    http_response_code(403);
    die('acces direct a ce fichier est interdit');
}
/*
 * class pour base de donne pour tou type avec pdo.
 */
$production = 1;
if (isset($production)) {
    //DB_LOCAL
    define('HOST', 'localhost');
    define('DB_NAME', 'classicmodels');
    define('USER_NAME', 'site_web');
    define('USER_PW', '12345678');
} else {
    //DB INFINITY
    define('HOST', 'localhost');
    define('DB_NAME', 'epiz_26539845_classicmodels');
    define('USER_NAME', 'epiz_26539845');
    define('USER_PW', 'tmaRlPDDhAtDOs');
}
class DB
{
    private $host = HOST;
    private $db_name = DB_NAME;
    private $db_user_name = USER_NAME;
    private $db_user_pw = USER_PW;
    private $pdo; //pdo connection object

    public function __construct()
    {
        $port = 3306;
        $charset = 'utf8mb4';
        $dsn = "mysql:host=$this->host;dbname=$this->db_name;charset=$charset;port=$port";
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];
        try {
            $this->pdo = new PDO($dsn, $this->db_user_name, $this->db_user_pw, $options);
        } catch (\PDOException $e) {
            throw new \PDOException($e->getMessage(), (int) $e->getCode());
        }
        // echo 'connecte a la base de donne ';
    }

    /**
     *  REQUETE AVEC PARAMETRE POUR SE PROTEGER CONTRES INJECTION SQL.
     */
    public function queryParam($sql_str, $params)
    {
        $stmt = $this->pdo->prepare($sql_str);
        $stmt->execute($params);

        return true;
    }

    /**
     *  REQUETE AVEC PARAMETRE POUR SE PROTEGER CONTRES INJECTION SQL.
     */
    public function querySelectParam($sql_str, $params)
    {
        $stmt = $this->pdo->prepare($sql_str);
        $stmt->execute($params);
        $result = $stmt->fetchAll();

        return $result;
    }

    public function query($sql_str)
    {
        try {
            $result = $this->pdo->query($sql_str);
        } catch (\PDOException $e) {
            throw new \PDOException($e->getMessage(), (int) $e->getCode());
        }

        return $result;
    }

    public function querySelect($sql_str)
    {
        $records = $this->query($sql_str)->fetchAll();

        return $records;
    }

    public function table($table_name)
    {
        $records = $this->querySelect('SELECT * FROM '.$table_name);

        return $records;
    }

    public function disconnect($sql_str)
    {
        $this->pdo = null;
    }
}
