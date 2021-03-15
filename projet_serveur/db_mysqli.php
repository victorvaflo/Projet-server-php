<?php

if (!isset($index_loaded)) {
    http_response_code(403);
    die('acces direct a ce fichier est interdit');
}
/**
 * CLASSE POUR BD AVEC LIBRAIRIE MYSQLI.
 */
class DB
{
    private $host = 'localhost';
    private $db_name = 'classicmodels';
    private $db_user_name = 'site_web';
    private $db_user_pw = '12345678';

    private $connection;

    /**
     * constructeur.  SE CONNECTE AUTOMATIQUEMENT A LABASE DE DONNE.
     */
    public function __construct()
    {
        $this->connection = new mysqli($this->host, $this->db_user_name, $this->db_user_pw, $this->db_name);

        //pour charactere francais tel que e avec accent
        mysqli_set_charset($this->connection, 'utf8');

        //CHECK FOR CONNECTION ERRUER
        if (mysqli_connect_errno()) {
            http_response_code(400); //bad request
            die('ERREUR  de connection a la base de done ');
        } else {
            // echo 'connecte a la base de donne ';
        }
    }

    /**
     * function universelle pour tout type de requete.
     * 1) returns.
     */
    public function myQuery($sql_str)
    {
        $result = $this->connection->query($sql_str);
        if (!$result) {
            http_response_code(400);
            die('ERREUR  REQUETE SQL :'.$this->connection->erreur);
        }

        return $result;
    }

    public function querySelect($sql_str)
    {
        $result = $this->myQuery($sql_str);
        $records = [];
        //fetch_array CONVERTIS CHAQUE ENREGISTREMENT DE LA TABLE EN UN ARRAY KEY=>VALEUR
        while ($un_record = $result->fetch_array()) {
            array_push($records, $un_record);
        }

        return $records;
    }

    public function disconnect()
    {
        //FIN DE LA CONNECTION
        mysqli_close($this->connection);
    }

    public function table($nom_table)
    {
        return $this->querySelect('SELECT * FROM '.$nom_table);
    }
}
