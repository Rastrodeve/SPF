<?php
$link = mssql_connect('172.20.134.7', 'sa', 'sa');

if (!$link || !mssql_select_db('YP_EPMRQ_INV_NEW', $link)) {
    die('No se puede conectar o seleccionar una base de datos!');
}

?>
