<?php
require '../../FilePHP/utils.php';
if (isset($_REQUEST['op'])) {
    $dbConn = conectar($db);
    $op = $_REQUEST['op'];
    if ($op == 1) echo get_option_search($dbConn);
    elseif ($op == 2) echo get_data_table($dbConn);
    elseif ($op == 3) echo update_estado($dbConn);
} else {
    header('location: ../../');
}
function Transformar_Fecha($fecha)
{
    $arrayDia = array(
        '1' => 'Lunes',
        '2' => 'Martes',
        '3' => 'Miércoles',
        '4' => 'Jueves',
        '5' => 'Viernes',
        '6' => 'Sábado',
        '7' => 'Domingo'
    );
    $numDia = date("N", strtotime($fecha));
    $arrayMes = array(
        '1' => 'Enero',
        '2' => 'Febrero',
        '3' => 'Marzo',
        '4' => 'Abril',
        '5' => 'Mayo',
        '6' => 'Junio',
        '7' => 'Julio',
        '8' => 'Agosto',
        '9' => 'Septiembre',
        '10' => 'Octubre',
        '11' => 'Noviembre',
        '12' => 'Diciembre',
    );
    $numMes = date("n", strtotime($fecha));
    return $arrayDia[$numDia] . ", " . date("d", strtotime($fecha)) . " de " . $arrayMes[$numMes] . " de " . date("Y", strtotime($fecha));
}
function get_option_search($dbConn)
{
    $function = "f_buscar_data()";
    $data_prepeterminada = '<div class="input-group input-group-sm">
                                <input type="text" class="form-control" id="txtBuscartext">
                                <span class="input-group-append">
                                    <button type="button" class="btn btn-info btn-flat" onclick ="' . $function . '">BUSCAR</button>
                                </span>
                            </div>';
    if (isset($_POST["tipo"])) {
        if ($_POST["tipo"] == 3) {
            $resultado = '<option value="0" selected="" >Seleccione un cliente</option>';
            $consulta = "SELECT * FROM tbl_a_clientes ORDER BY  cliNombres ASC";
            $sql = $dbConn->prepare($consulta);
            $sql->execute();
            while ($row = $sql->fetch()) {
                $resultado .= '<option value="' . $row["cliId"] . '" >' . utf8_encode($row["cliNombres"]) . '</option>';
            }
            $data_prepeterminada = '<select class="form-control form-control-sm select2" id="slcClientes_search" onchange="' . $function . '" >' . $resultado . '</select> <script> $("#slcClientes_search").select2();</script>';
        }
    }
    return $data_prepeterminada;
}

function get_data_table($dbConn)
{
    $opcion = $_POST["Opcion"];
    $valor = $_POST["Valor"];
    $resultado = "<table class='table table-bordered table-striped table-hover table-sm' id='table'>
    <thead>
        <tr>
            <th>#</th>
            <th>Guía Movilización</th>
            <th>Comprobante</th>
            <th>Cliente</th>
            <th>Especie</th>
            <th>Cantidad</th>
            <th>Faenamiento</th>
            <th>Recepción</th>
            <th>Pago</th>
        </tr>
    </thead>
    <tbody>";
    $nueva_Sql = "";
    if ($opcion == 1) $nueva_Sql = "AND g.guiNumero = '" . trim($valor) . "'";
    if ($opcion == 2) $nueva_Sql = "AND p.gprComprobante = '" . trim($valor) . "'";
    if ($opcion == 3) $nueva_Sql = "AND c.cliId = " . trim($valor);
    if ($opcion == 4) $nueva_Sql = "AND p.gprEstadoPagado = 0";
    if ($opcion == 5) $nueva_Sql = "AND p.gprEstadoPagado = 1";
    $consulta = "SELECT p.gprId ,g.guiNumero, p.gprComprobante, c.cliNombres, e.espDescripcion ,p.gprMacho, p.gprHembra, p.gprTurno, p.gprHabilitado, p.gprEstadoPagado, g.guiTurno
    FROM tbl_r_guiaproceso p, tbl_r_guiamovilizacion g, tbl_a_clientes c, tbl_a_especies e 
    WHERE p.guiId = g.guiId AND p.cliId = c.cliId AND p.espId = e.espId AND g.guiTotal IS NOT NULL AND g.guiHembra IS NOT NULL AND g.guiMacho IS NOT NULL AND g.guiTurno IS NOT NULL " . $nueva_Sql;
    $sql = $dbConn->prepare($consulta);
    $sql->execute();
    $cont = 0;
    while ($row = $sql->fetch()) {
        ++$cont;
        $estado = "<span class='badge bg-dark'>ERROR</span>";
        $estado_pago = "<span class='bg-dark'>ERROR</span>";
        if ($row["guiTurno"] == 0) {
            $estado = "<span class='badge bg-danger'>NO RECIBIDO</span>";
        }
        if ($row["gprHabilitado"] == 1) {
            $estado = "<span class='badge bg-success'>HABILITADO</span>";
        }
        if ($row["gprEstadoPagado"] == 0) {
            $estado_pago = "<button class='btn btn-danger btn-sm' onclick='f_registrar(0," . $row["gprId"] . "," . $cont . ")'>NO PAGADO</button>";
        }
        if ($row["gprEstadoPagado"] == 1) {
            $estado_pago = "<button class='btn btn-success btn-sm' onclick='f_registrar(1," . $row["gprId"] . "," . $cont . ")'>PAGADO</button>";
        }
        $resultado .= "
        <tr id='td-" . $cont . "'>
            <td>" . $cont . "</td>
            <td>" . utf8_encode($row["guiNumero"]) . "</td>
            <td>" . utf8_encode($row["gprComprobante"]) . "</td>
            <td>" . utf8_encode($row["cliNombres"]) . "</td>
            <td>" . utf8_encode($row["espDescripcion"]) . "</td>
            <td>(M:" . $row["gprMacho"] . " + H:" . $row["gprHembra"] . ") <b>" . ($row["gprMacho"] + $row["gprHembra"]) . "</b></td>
            <td>" . $row["gprTurno"] . "</td>
            <td>" . $estado . "</td>
            <td>" . $estado_pago . "</td>
        </tr>";
    }
    return $resultado . '</tbody></table>';
}
function update_estado($dbConn)
{
    $estado = $_POST["Estado"];
    if ($estado == 0) $estado = 1;
    else if ($estado == 1) $estado = 0;
    $id = $_POST["Id"];
    $consulta = "UPDATE tbl_r_guiaproceso SET gprEstadoPagado = :estado  WHERE gprId = :id";
    $sql = $dbConn->prepare($consulta);
    $sql->bindValue(':estado', $estado);
    $sql->bindValue(':id', $id);
    if ($sql->execute()) return true;
    return 'ERROR-1616555';
}
