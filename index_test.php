<?php

$index_loaded = true;
require_once 'db_pdo.php';
$DB = new DB();
$records = $DB->query('DELETE * FROM offices WHERE officeCode="1"');
$records = $DB->queryParam('DELETE * FROM offices WHERE officeCode=:code', ['code' => '1']);
var_dump($records);
