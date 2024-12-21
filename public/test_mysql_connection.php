<?php

/*DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=laravel
DB_USERNAME=sail
DB_PASSWORD=password */
$mysqli = new mysqli("127.0.0.1", "sail", "password");

if ($mysqli->connect_error) {
    die("Conexão falhou: " . $mysqli->connect_error);
}

echo "Conectado com sucesso! Versão do MySQL: " . $mysqli->server_info;

$mysqli->close();
?>
