<?php

$servidor = "mysql:dbname=crudphp;host=127.0.0.1";
$usuario = "root";
$password = "";

try {

    $pdo = new PDO($servidor, $usuario, $password);
    //echo "CONECTADO AO BANCO DE DADOS";
} catch (PDOException $e) {

    echo "ERRO: (" . $e->getmessage();
}
