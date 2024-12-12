<?php
if (isset($_REQUEST['op'])) {
    require '../../FilePHP/utils.php';
    $dbConn = conectar($db);
    $op = $_REQUEST['op'];
    if ($op == 1) echo select_data_especies($dbConn);
    else if ($op == 2) echo f_data_table($dbConn);
} else header("location: ./");

function select_data_especies($dbConn)
{
    $resultado = '<option value="0">Todas las especies</option>';
    $consulta = "SELECT * FROM tbl_a_especies ";
    $sql = $dbConn->prepare($consulta);
    $sql->execute();
    $cont = 0;
    while ($row = $sql->fetch()) {
        $resultado .= '<option value="' . $row["espId"] . '" >' . utf8_encode($row["espDescripcion"]) . '</option>';
    }
    return  $resultado;
}
function f_data_table($dbConn)
{
    $table = '<table class="table table-bordered table-striped table-sm" id="table-data">
    <thead>
        <tr>
            <th >#</th>
            <th>Número de Orden</th>
            <th>Fecha</th>
            <th>Ganado</th>
            <th>Cantidad</th>
        </tr>
    </thead>
    <tbody>';
    $especie = '';
    if ($_POST["Id"] != 0) $especie = 'AND espId = ' . $_POST["Id"];
    // $consulta="SELECT DISTINCT ordNumOrden FROM tbl_p_orden WHERE ordFecha BETWEEN :inicio AND :final 
    // AND ordTipo = :tipo AND gprId IS NOT NULL ".$especie." ORDER BY ordId ASC";
    $consulta = "SELECT DISTINCT ordNumOrden FROM tbl_p_orden WHERE ordFecha BETWEEN :inicio AND :final 
    AND ordTipo = :tipo  AND ordEliminado = 0 " . $especie . " ORDER BY ordId ASC";
    $sql = $dbConn->prepare($consulta);
    $sql->bindValue(':inicio', transformar_fecha($_POST["Inicio"]) . ' 00:00:00');
    $sql->bindValue(':final', transformar_fecha($_POST["Final"]) . ' 23:59:59');
    $sql->bindValue(':tipo', $_POST["Tipo"]);
    $sql->execute();
    $cont = 0;
    while ($row = $sql->fetch()) {
        $cont++;
        $link = 'orden';
        if ($_POST["Tipo"] == 1) $link = 'emergente';
        $array = get_data_orden($dbConn, $row["ordNumOrden"], $_POST["Inicio"], $_POST["Final"]);
        $table .= '
        <tr>
            <td >' . $cont . '</td>  
            <td><a href="../../documentos/producion/' . $link . '/' . $row["ordNumOrden"] . '.pdf" target="_blank" >' . $row["ordNumOrden"] . '</a></td>  
            <td>' . $array[1] . '</td>
            <td>' . $array[2] . '</td>  
            <td>' . $array[0] . '</td>  
        </tr>';
    }
    return  $table . '</tbody></table>';
}
function get_data_orden($dbConn, $numero, $inicio, $final)
{
    $Array = [0, '', ''];
    $consulta = "SELECT o.ordCantidad,o.ordFecha,e.espDescripcion FROM tbl_p_orden o, tbl_a_especies e
    WHERE o.espId = e.espId AND o.ordNumOrden = :orden AND o.ordFecha BETWEEN :inicio AND :final AND o.ordEliminado = 0";
    $sql = $dbConn->prepare($consulta);
    $sql->bindValue(':orden', $numero);
    $sql->bindValue(':inicio', transformar_fecha($inicio) . ' 00:00:00');
    $sql->bindValue(':final', transformar_fecha($final) . ' 23:59:59');
    $sql->execute();
    while ($row = $sql->fetch()) {
        $Array[0] += $row["ordCantidad"];
        $fecha = explode(' ', $row["ordFecha"]);
        $Array[1] = $fecha[0];
        $Array[2] = utf8_encode($row["espDescripcion"]);
    }
    return $Array;
}
function transformar_fecha($fecha)
{
    $array = explode("/", $fecha);
    return $array[2] . "-" . $array[1] . "-" . $array[0];
}
