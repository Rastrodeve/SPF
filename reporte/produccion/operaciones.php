<?php
if (isset($_REQUEST['op'])) {
    require '../../FilePHP/utils.php';
    $dbConn = conectar($db);
    $op = $_REQUEST['op'];
    $btn = '<center> <button class="btn btn-danger" onclick="f_generar_pdf()" ><b>GENEAR EN PDF</b></button> </center>';
    if ($op == 1) echo select_data_especies($dbConn);
    else if ($op == 2) echo f_data_table_1($dbConn) . $btn;
    else if ($op == 3) echo f_data_table_2($dbConn) . $btn;
    else if ($op == 4) echo f_data_table_3($dbConn) . $btn;
    else if ($op == 5) echo f_data_table_4($dbConn) . $btn;
    else if ($op == 6) {
        $_SESSION['OPCION'] = 1;
        $_SESSION['VARIABLE'] = $_POST["Id"];
        $_SESSION['INICIO'] = transformar_fecha($_POST["Inicio"]);
        $_SESSION['FINAL'] = transformar_fecha($_POST["Final"]);
    } else if ($op == 7) {
        $_SESSION['OPCION'] = 6;
        $_SESSION['VARIABLE'] = $_POST["Id"];
        $_SESSION['INICIO'] = transformar_fecha($_POST["Inicio"]);
        $_SESSION['FINAL'] = transformar_fecha($_POST["Final"]);
    } else if ($op == 8) {
        $_SESSION['OPCION'] = 8;
        $_SESSION['VARIABLE'] = $_POST["Id"];
        $_SESSION['INICIO'] = transformar_fecha($_POST["Inicio"]);
        $_SESSION['FINAL'] = transformar_fecha($_POST["Final"]);
    } else if ($op == 9) {
        $_SESSION['OPCION'] = 7;
        $_SESSION['VARIABLE'] = $_POST["Id"];
        $_SESSION['INICIO'] = transformar_fecha($_POST["Inicio"]);
        $_SESSION['FINAL'] = transformar_fecha($_POST["Final"]);
    } else if ($op == 10) echo get_data_impr($dbConn);
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


function f_data_table_1($dbConn)
{
    $table = '<table class="table table-bordered table-striped table-sm" id="table-data">
    <thead>
        <tr>
            <th>Num</th>
            <th>Fecha de ingreso</th>
            <th>Nro. Guía de movilización</th>
            <th>Nro. Comprobante</th>
            <th>Cliente</th>
            <th>Marca</th>
            <th>Ganado</th>
            <th>Corral</th>
            <th>Cantidad</th>
        </tr>
    </thead>
    <tbody>';
    $especie = '';
    if ($_POST["Id"] != 0) $especie = 'AND e.espId = ' . $_POST["Id"];
    $consulta = "SELECT p.gprId, p.gprTurno, g.guiNumero, p.gprComprobante ,c.cliNombres, e.espDescripcion,(p.gprMacho + p.gprHembra) AS gprCantidad, p.gprEstado, p.gprestadoDetalle, c.cliMarca 
    FROM tbl_r_guiaproceso p, tbl_r_guiamovilizacion g, tbl_a_especies e, tbl_a_clientes c 
    WHERE p.guiId = g.guiId AND p.espId = e.espId AND p.cliId = c.cliId " . $especie . " AND
    g.guiEliminado = 0  AND p.gprEliminado = 0 AND p.gprHabilitado = 1 AND p.gprTurno BETWEEN :finicio AND :final
    ORDER BY p.gprTurno ASC";
    $sql = $dbConn->prepare($consulta);
    $sql->bindValue(':finicio', transformar_fecha($_POST["Inicio"]) . ' 00:00:00');
    $sql->bindValue(':final', transformar_fecha($_POST["Final"]) . ' 23:59:59');
    $sql->execute();
    $cont = 0;
    $total = 0;
    while ($row = $sql->fetch()) {
        $cont++;
        $corral = f_obtener_corrales_guia($dbConn, $row["gprId"]);
        $total += $row["gprCantidad"];
        $table .= '
        <tr>
            <th  class="text-center">' . $cont . '</th>
            <td>' . $row["gprTurno"] . '</td>
            <td>' . utf8_encode($row["guiNumero"]) . '</td>
            <td> <a  href="#" onclick="Imprimir(' . $row["gprId"] . ')">' . utf8_encode($row["gprComprobante"]) . '</a></td>
            <td>' . utf8_encode($row["cliNombres"]) . '</td>
            <td class="text-center">' . utf8_encode($row["cliMarca"]) . '</td>
            <td class="text-center">' . utf8_encode($row["espDescripcion"]) . '</td>
            <td >' . $corral . '</td>
            <th class="text-center">' . $row["gprCantidad"] . '</th>
        </tr>';
    }
    $foter = '';
    if ($cont > 0) {
        $foter = '<tfoot >
            <tr>
                <th class="text-right" colspan = "8">TOTAL DE GANADO</th>
                <th class="text-center">' . $total . '</th>
            </tr>
        </tfoot>';
    }
    return  $table . '</tbody>' . $foter . '</table>';
}
function f_data_table_2($dbConn)
{
    $table = '<table class="table table-bordered table-striped table-sm" id="table-data">
    <thead>
        <tr>
            <th>Num</th>
            <th>Orden P.</th>
            <th>Cliente</th>
            <th>Marca</th>
            <th>Ganado</th>
            <th>Cantidad</th>
            <th>Faenado</th>
            <th>Saldo</th>
        </tr>
    </thead>
    <tbody>';
    $especie = '';
    if ($_POST["Id"] != 0) $especie = 'AND o.espId = ' . $_POST["Id"];
    $consulta = "SELECT * FROM tbl_p_orden o, tbl_r_guiaproceso p, tbl_a_clientes c, tbl_a_especies e 
    WHERE o.gprId = p.gprId AND p.cliId = c.cliId AND p.espId = e.espId " . $especie .
        "  AND o.gprId IS NOT NULL  AND o.ordFecha BETWEEN :finicio AND :final AND o.ordEliminado = 0 ORDER BY o.ordId ASC";
    $sql = $dbConn->prepare($consulta);
    $sql->bindValue(':finicio', transformar_fecha($_POST["Inicio"]) . ' 00:00:00');
    $sql->bindValue(':final', transformar_fecha($_POST["Final"]) . ' 23:59:59');
    $sql->execute();
    $cont = 0;
    $total3 = 0;
    $total1 = 0;
    $total2 = 0;
    while ($row = $sql->fetch()) {
        $cont++;
        $total1 += $row["ordCantidad"];
        $procesado = f_obtener_descuentos($dbConn, $row["ordId"]);
        if ($row["ordTipo"] == 1) $procesado = $row["ordCantidad"];
        $total2 += $procesado;
        $saldo = $row["ordCantidad"] - $procesado;
        $total3 += $saldo;
        $table .= '
        <tr>
            <th  class="text-center">' . $cont . '</th>
            <td>' . $row["ordNumOrden"] . '</td>
            <td>' . utf8_encode($row["cliNombres"]) . '</td>
            <td class="text-center">' . utf8_encode($row["cliMarca"]) . '</td>
            <td class="text-center">' . utf8_encode($row["espDescripcion"]) . '</td>
            <th class="text-center">' . $row["ordCantidad"] . '</th>
            <th class="text-center">' . $procesado . '</th>
            <th class="text-center">' . $saldo . '</th>
        </tr>';
    }
    $foter = '';
    if ($cont > 0) {
        $foter = '<tfoot >
            <tr>
                <th class="text-right" colspan = "5">TOTALES</th>
                <th class="text-center">' . $total1 . '</th>
                <th class="text-center">' . $total2 . '</th>
                <th class="text-center">' . $total3 . '</th>
            </tr>
        </tfoot>';
    }
    return  $table . '</tbody>' . $foter . '</table>';
}
function f_data_table_4($dbConn)
{
    $table = '<table class="table table-bordered table-striped table-sm" id="table-data">
    <thead>
        <tr>
            <th>Num</th>
            <th>Orden P.</th>
            <th>Cliente</th>
            <th>Ganado</th>
            <th>Factura</th>
            <th>Cobro</th>
            <th>Cantidad</th>
            <th>Estancia</th>
            <th>Corralaje</th>
        </tr>
    </thead>
    <tbody>';
    $especie = '';
    if ($_POST["Id"] != 0) $especie = 'AND o.espId = ' . $_POST["Id"];
    $consulta = "SELECT * FROM tbl_p_orden o, tbl_r_guiaproceso p, tbl_a_clientes c, tbl_a_especies e 
    WHERE o.gprId = p.gprId AND p.cliId = c.cliId AND p.espId = e.espId " . $especie .
        " AND o.gprId IS NOT NULL AND o.ordFecha  BETWEEN :finicio AND :final AND o.ordEliminado = 0 ORDER BY o.ordId ASC";
    $sql = $dbConn->prepare($consulta);
    $sql->bindValue(':finicio', transformar_fecha($_POST["Inicio"]) . ' 00:00:00');
    $sql->bindValue(':final', transformar_fecha($_POST["Final"]) . ' 23:59:59');
    $sql->execute();
    $cont = 0;
    while ($row = $sql->fetch()) {
        $cont++;
        $pago = 'POR RECAUDAR';
        if ($row["ordEstado"] == 1) $pago = 'RECAUDADO';
        $table .= '
        <tr>
            <th  class="text-center">' . $cont . '</th>
            <td>' . $row["ordNumOrden"] . '</td>
            <td>' . utf8_encode($row["cliNombres"]) . '(' . utf8_encode($row["cliMarca"]) . ')</td>
            <td class="text-center">' . utf8_encode($row["espDescripcion"]) . '</td>
            <th class="text-center">' . $row["Num_Documento"] . '</th>
            <th class="text-center">' . $pago . '</th>
            <th class="text-center">' . $row["ordCantidad"] . '</th>
            <th class="text-center">' . $row["ordTiempoEstancia"] . '</th>
            <th class="text-center">' . $row["ordTiempoCobrar"] . '</th>
        </tr>';
    }
    $foter = '';
    if ($cont > 0) {
        $foter = '<tfoot >
            <tr>
                <th class="text-right" colspan = "5">TOTALES</th>
            </tr>
        </tfoot>';
    }
    return  $table . '</tbody></table>';
}
function f_data_table_especie_tazas($dbConn, $Id, $incio, $final)
{
    $servicios = f_get_array_servicios($dbConn, $Id);
    $th = '';
    $td = '';
    $total = 0;
    $Inicio = transformar_fecha($incio);
    $Final = transformar_fecha($final);
    $corralaje = f_get_cantidad_corralaje($dbConn, $Id, $Inicio, $Final);
    for ($i = 0; $i < count($servicios); $i++) {
        $th .= '<th>' . $servicios[$i][1] . ' (' . number_format($servicios[$i][2], 2) . ')</th>';
        $cantidad = f_get_cantidad_servicios($dbConn, $servicios[$i][0], $Inicio, $Final);
        $total += $cantidad;
        $cantidad =  number_format($cantidad, 2, ',', ' ');
        $td .= '<td class="c_number">' . $cantidad . ' $</td>';
    }
    $total += $corralaje;
    $ganado = f_get_cantidad_ganado($dbConn, $Id, $Inicio, $Final);
    $total =  number_format($total, 2, ',', ' ');
    $corralaje =  number_format($corralaje, 2, ',', ' ');
    $especie = f_get_especie($dbConn, $Id);
    $precio_corralaje = f_get_especie_corralaje_precio($dbConn, $Id);
    return '<table class="table table-bordered  table-sm text-center table-hover" >
    <thead>
        <tr  class="thead-light " >
            <th class="text-left" colspan="' . (3 + count($servicios)) . '">' . $especie . '</th>
        </tr>
        <tr class="thead-light">
            <th>C. Animales</th>
            ' . $th . '
            <th>Corralaje (' . number_format($precio_corralaje, 2) . ')</th>
            <th>Total</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td class="c_number">' . $ganado . '</td>
            ' . $td . '
            <td class="c_number">' . $corralaje . ' $</td>
            <td class="c_number">' . $total . ' $</td>
        </tr>
    <tbody></table>';
}
function f_data_table_3($dbConn)
{
    if ($_POST["Id"] == 0) {
        $data = '';
        $consulta = "SELECT espId FROM tbl_a_especies";
        $sql = $dbConn->prepare($consulta);
        $sql->execute();
        while ($row = $sql->fetch()) $data .= f_data_table_especie_tazas($dbConn, $row["espId"], $_POST["Inicio"], $_POST["Final"]);
        return $data;
    } else {
        return f_data_table_especie_tazas($dbConn, $_POST["Id"], $_POST["Inicio"], $_POST["Final"]);
    }
}
function transformar_fecha($fecha)
{
    $array = explode("/", $fecha);
    return $array[2] . "-" . $array[1] . "-" . $array[0];
}
function f_obtener_corrales_guia($dbConn, $Id)
{
    $consulta = "SELECT c.crrDescripcion FROM tbl_r_lugar l, tbl_a_corral c WHERE l.crrId = c.crrId AND l.lgrEliminado = 0 AND l.gprId = :id";
    $sql = $dbConn->prepare($consulta);
    $sql->bindValue(':id', $Id);
    $sql->execute();
    $corrales = '';
    while ($row = $sql->fetch()) $corrales .= utf8_encode($row["crrDescripcion"]) . '; ';
    return $corrales;
}
function f_obtener_descuentos($dbConn, $Id)
{
    $consulta = "SELECT faeCantidad FROM tbl_p_faenamiento WHERE ordId = :id";
    $sql = $dbConn->prepare($consulta);
    $sql->bindValue(':id', $Id);
    $sql->execute();
    $cont = 0;
    while ($row = $sql->fetch()) $cont += $row["faeCantidad"];
    return $cont;
}
function f_obtener_cantidad_total($dbConn, $Id, $Inicio, $Final, $especie)
{
    $consulta = "SELECT * FROM tbl_r_guiaproceso WHERE cliId = :id " . $especie;
    $sql = $dbConn->prepare($consulta);
    $sql->bindValue(':finicio', $Inicio);
    $sql->bindValue(':final', $Final);
    $sql->bindValue(':id', $Id);
    $sql->execute();
    $corrales = '';
    while ($row = $sql->fetch()) $corrales .= utf8_encode($row["crrDescripcion"]) . '; ';
    return $corrales;
}
function f_get_array_servicios($dbConn, $Id)
{
    $array = [];
    $consulta = "SELECT * FROM tbl_a_servicios WHERE espId = :id";
    $sql = $dbConn->prepare($consulta);
    $sql->bindValue(':id', $Id);
    $sql->execute();
    while ($row = $sql->fetch()) array_push($array, [$row["srnId"], $row["srnDescripcion"], $row["srnPrecio"], $row["srnEstado"]]);
    return $array;
}
function f_get_cantidad_servicios($dbConn, $Id, $Inicio, $Final)
{
    $total = 0;
    $consulta = "SELECT * FROM tbl_YP_EGDATA_FAC f, tbl_p_orden o 
    WHERE f.ordId =  o.ordId AND o.ordFecha BETWEEN :inicio AND :final AND o.ordEliminado = 0 AND  f.srnId = :id";
    $sql = $dbConn->prepare($consulta);
    $sql->bindValue(':id', $Id);
    $sql->bindValue(':inicio', $Inicio . ' 00:00:00');
    $sql->bindValue(':final', $Final . ' 23:59:59');
    $sql->execute();
    while ($row = $sql->fetch()) $total += $row["Valor3"];
    return $total;
}
function f_get_cantidad_corralaje($dbConn, $Id, $Inicio, $Final)
{
    $total = 0;
    $consulta = "SELECT * FROM tbl_YP_EGDATA_FAC f, tbl_p_orden o 
    WHERE f.ordId =  o.ordId AND o.ordFecha BETWEEN :inicio AND :final AND o.ordEliminado = 0  AND  o.espId = :id AND f.srnId IS NULL AND f.Tipo= 'D'";
    $sql = $dbConn->prepare($consulta);
    $sql->bindValue(':id', $Id);
    $sql->bindValue(':inicio', $Inicio . ' 00:00:00');
    $sql->bindValue(':final', $Final . ' 23:59:59');
    $sql->execute();
    while ($row = $sql->fetch()) $total += $row["Valor3"];
    return $total;
}
function f_get_cantidad_ganado($dbConn, $Id, $Inicio, $Final)
{
    $total = 0;
    $consulta = "SELECT ordCantidad FROM tbl_p_orden WHERE espId = :id AND ordEliminado = 0 AND ordFecha BETWEEN :inicio AND :final ";
    $sql = $dbConn->prepare($consulta);
    $sql->bindValue(':id', $Id);
    $sql->bindValue(':inicio', $Inicio . ' 00:00:00');
    $sql->bindValue(':final', $Final . ' 23:59:59');
    $sql->execute();
    while ($row = $sql->fetch()) $total += $row["ordCantidad"];
    return $total;
}
function f_get_especie($dbConn, $Id)
{
    $total = 0;
    $consulta = "SELECT espDescripcion FROM tbl_a_especies WHERE espId = :id";
    $sql = $dbConn->prepare($consulta);
    $sql->bindValue(':id', $Id);
    $sql->execute();
    if ($row = $sql->fetch()) return utf8_encode($row["espDescripcion"]);
}
function f_get_especie_corralaje_precio($dbConn, $Id)
{
    $total = 0;
    $consulta = "SELECT espPrecioCorralaje FROM tbl_a_especies WHERE espId = :id";
    $sql = $dbConn->prepare($consulta);
    $sql->bindValue(':id', $Id);
    $sql->execute();
    if ($row = $sql->fetch()) return $row["espPrecioCorralaje"];
}
function get_data_impr($dbConn)
{
    $Id = $_POST["Id"];
    $consulta = "SELECT *
    FROM tbl_r_guiaproceso p, tbl_r_guiamovilizacion g, tbl_a_especies e, tbl_a_clientes c 
    WHERE p.guiId = g.guiId AND p.espId = e.espId AND p.cliId = c.cliId AND p.gprId = :id";
    $sql = $dbConn->prepare($consulta);
    $sql->bindValue(':id', $Id);
    $sql->execute();
    if ($row = $sql->fetch()) {
        $usuario = get_data_user_2($dbConn, $row["usuId"]);
        // '<br><span style="font-size:12px;margin-bottom:5px;"><b>QUITO-EMRAQ-EP<br>CAMAL METROPOLITANO</b></span>'.
        $Datos = '<div style="margin-left: 10px;margin-right: 10px;font: caption">' .
            '<center>' .
            '<img src="../../recursos/rastro_logo_2.png" width="30%" >' .
            '<br><span style="font-size:12px;margin-bottom:5px;"><b>COMPROBANTE DE INGRESO</b></span>' .
            '<br><span style="font-size:12px;margin-bottom:5px;"><b>EMPRESA PÚBLICA METROPOLITANA DE RASTRO QUITO</b></span>' .
            '<br><img src="../../recursos/especies/' . $row["espImagen"] . '" width="90px">' .
            '<br><span style="font-size: 11px"><b style="font-size: 10">' . utf8_encode($row["espDescripcion"]) . '</b>' .
            '<br><b>Guia de movilizacion número: </b><font style="font-size: 13px">' . utf8_encode($row["guiNumero"]) . '</font>' .
            '<br><b>Comprobante de Ingreso número: </b><font style="font-size: 13px">' . utf8_encode($row["gprComprobante"]) . '</font>' .
            '<br><b>Fecha y Hora de Ingreso: </b>' . $row["gprTurno"] . '' .
            '<br><br><b>Nombre del conductor: </b>' . utf8_encode($row["guiNombreConductor"]) . '' .
            '<br><b>C.C Conductor: </b>' . $row["guiCiConductor"] . '' .
            '<br><b>Placa del vehiculo: </b>' . utf8_encode($row["guiVehiculoPlaca"]) . '</span>' .
            '</center>' .
            '<span style="font-size: 10px"><hr>' .
            '<center>' .
            '<table style="font-size: 10px">' .
            '<tr>' .
            '<td><b>Apellidos y Nombres</b></td>' .
            '<td>' . utf8_encode($row["cliNombres"]) . '</td>' .
            '</tr>' .
            '<tr>' .
            '<td><b>Marca</b></td>' .
            '<td>' . utf8_encode($row["cliMarca"]) . '</td>' .
            '</tr>' .
            '<tr>' .
            '<td><b>Cédula</b></td>' .
            '<td>' . $row["cliNumero"] . '</td>' .
            '</tr>' .
            '<tr>' .
            '<td><b>Hembras</b></td>' .
            '<td style="font-size: 12px">' . $row["gprHembra"] . '</td>' .
            '</tr>' .
            '<tr>' .
            '<td><b>Machos</b></td>' .
            '<td style="font-size: 12px">' . $row["gprMacho"] . '</td>' .
            '</tr>' .
            '<tr>' .
            '<td><b>Total</b></td>' .
            '<td style="font-size: 12px">' . ($row["gprMacho"] + $row["gprHembra"]) . '</td>' .
            '</tr>' .
            '</table>' .
            '</center>' .
            '</span>' .
            '<div style="position:relative;margin-top: 10px">' .
            '<span style="font-size: 12px;margin-bottom:5px"><b>Observaciones:</b><br>' . utf8_encode($row["guiObservacion"]) . '</span>' .
            '<img style="position: absolute;top:-30px;right:0px;" src="../../recursos/Mabio.png" width="55px">' .
            '</div>' .
            '<br><br><span style="font-size: 12px;margin-bottom:5px"><b>Firmas:</b></span>' .
            '<table style="font-size: 10px;width:100%;text-align: center;">' .
            '<tr>' .
            '<td>Recibe Conforme</td>' .
            '</tr>' .
            '<tr>' .
            '<td><br><br><br><b>__________________________________________________</b></td>' .
            '</tr>' .
            '<tr>' .
            '<td><b>' . $usuario[0] . '</b></td>' .
            '</tr>' .
            '<tr>' .
            '<td>Recibidor y Custodio del ganado</td>' .

            '</tr>' .
            '</table>' .

            '<span style="font-size: 8px" >impreso: ' . date("Y-m-d H:i:s") . '</span>' .
            '</div>';
    }
    return $Datos;
}
function get_data_user_2($dbConn, $Id)
{
    $consulta = "SELECT * FROM tbl_a_usuarios WHERE usuId = :cedula AND usuEstado = 1 AND usuEstado_pass = 0";
    $sql = $dbConn->prepare($consulta);
    $sql->bindValue(':cedula', $Id);
    $sql->execute();
    if ($row = $sql->fetch()) return [utf8_encode($row["usuNombre"]), utf8_encode($row["usuCargo"]), $row["usuCedula"]];
    else return false;
}
