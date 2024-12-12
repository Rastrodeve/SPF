<?php  
// include 'ejecutar.php';
// sleep(4);
$url="C://Rastro/STP/Serial/pesaje.txt";
$archivo = fopen($url, "r");
// Recorremos todas las lineas del archivo
while(!feof($archivo)){
    // Leyendo una linea
    $traer = fgets($archivo);
    // Imprimiendo una linea
    echo nl2br($traer);
}
 
// Cerrando el archivo
fclose($archivo);
?>