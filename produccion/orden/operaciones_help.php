<?php
if (isset($_REQUEST["op"])) {
    require '../../FilePHP/utils.php';
    $dbConn = conectar($db);
    $op = $_REQUEST['op'];
    if ($op == 1) echo recuperar($dbConn);
}
function recuperar($dbConn)
{
    $guia = $_POST["Numero"];
    if (buscar($guia) == true) exit("La orden de producción si existe");
    $data = data_guia($dbConn, $guia);
    $_SESSION['OPCION'] = 2;
    $_SESSION['VARIABLE'] = $guia;
    $_SESSION['VARIABLE2'] = $data[0];
    $_SESSION['INICIO'] = $_SESSION['FINAL']  = $data[1];
    return  true;
}
function buscar($numero)
{
    // Ruta de la carpeta donde quieres buscar
    $carpeta = '../../documentos/producion/orden/';

    // Patrón para buscar archivos PDF
    $patron = $carpeta . $numero . '.pdf';

    // Usamos glob para buscar los archivos que coincidan con el patrón
    $archivos = glob($patron);

    // Verificamos si se encontró al menos un archivo
    if (empty($archivos)) return false;
    else return true;
}
function data_guia($dbConn, $numero)
{
    $consulta = "SELECT * FROM  tbl_p_orden WHERE ordNumOrden = :orden  AND ordEliminado = 0 ORDER BY ordFecha DESC LIMIT 1";
    $sql = $dbConn->prepare($consulta);
    $sql->bindValue(':orden', $numero);
    $sql->execute();
    if ($row = $sql->fetch()) {
        $fecha = explode("-", explode(" ", $row['ordFecha'])[0]);
        return [$row['espId'], $fecha[2] . '-' . $fecha[1] . '-' . $fecha[0]];
    } else exit("No se encontro la orden de producción");
}
