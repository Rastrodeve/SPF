<?php
require '../../FilePHP/utils.php';
if (isset($_REQUEST['op'])) {
    $dbConn = conectar($db);
    $op = $_REQUEST['op'];

    if ($op == 1) echo get_data_cont($dbConn);
    elseif ($op == 2) $_SESSION['FAENAMIENTO'] = $_POST["Id"];
    elseif ($op == 3) echo get_data_table($dbConn, $_SESSION['FAENAMIENTO']);
    elseif ($op == 4) echo get_data_orden($dbConn);
    elseif ($op == 5) $_SESSION['FAENAMIENTO'] = 0;
    elseif ($op == 6) echo f_procesar_data($dbConn);
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
function select_data_especies($dbConn)
{
    $resultado = '';
    $cont = 0;
    $consulta = "SELECT * FROM tbl_a_especies";
    $sql = $dbConn->prepare($consulta);
    $sql->execute();
    while ($row = $sql->fetch()) {
        $cont++;
        $resultado .= '<option value="' . $row["espId"] . '" >' . utf8_encode($row["espDescripcion"]) . '</option>';
    }
    if ($cont > 0) return  $resultado;
    else return '<option value="0" selected="" >No se encontraron especies</option>';
}
function get_data_cont($dbConn)
{
    $opcion = $_SESSION['FAENAMIENTO'];
    if ($opcion == 0) {
        return '
        <select class="form-control form-control-lg select2bs4" onchange="f_mensaje()" id="slcTipo" style="width:100%;">
        ' . select_data_especies($dbConn) . '
        </select>
        <hr>
        <div id="cont-result"></div>';
    } else {
        return '
        <div class="card">
            <div class="card-body">
                <div class="row mb-2">
                    <div class="col-6">
                        <b>
                            <span class="text-muted">GANADO SELECCIONADO:</span>
                            ' . get_data_especie($dbConn, $opcion) . '
                        </b>
                    </div>
                    <div class="col-6">
                        <button class="btn btn-danger btn-sm float-right" onclick="f_regresar()"><b>REGRESAR</b></button>
                    </div>
                </div>
                <hr>
                <button class="btn btn-info btn-sm float-right" onclick="get_data_table()" ><b>RECARGAR TABLA</b></button>
                <div id="cont-table-fae">
                    ' . get_data_table($dbConn, $opcion) . '
                </div>
                <hr>
                <button class="btn btn-danger btn-sm d-none"><b>REPORTE DE SALDOS</b></button>
                <button class="btn btn-danger btn-sm d-none"><b>REPORTE DE FAENAMIENTO</b></button>
            </div>
        </div>';
    }
}
function f_obtener_procesar($dbConn, $orden, $Id)
{
    $cont = 0;
    $consulta = "SELECT faeCantidad FROM tbl_p_faenamiento WHERE ordId = :orden";
    $sql = $dbConn->prepare($consulta);
    $sql->bindValue(':orden', $orden);
    // $sql->bindValue(':id',$Id);
    $sql->execute();
    while ($row = $sql->fetch()) {
        $cont += $row["faeCantidad"];
    }
    return $cont;
}
function f_obtener_procesar_total($dbConn, $Id)
{
    $cont = 0;
    $consulta = "SELECT * FROM tbl_r_detalle 
    WHERE dtEstado = 1  AND  ordId is null AND dtEliminado = 0 AND gprId = :id";
    $sql = $dbConn->prepare($consulta);
    $sql->bindValue(':id', $Id);
    $sql->execute();
    while ($row = $sql->fetch()) {
        $cont++;
    }
    return $cont;
}
function f_buscar_orden($dbConn, $fecha, $Id)
{
    $cont = 0;
    $consulta = "SELECT * FROM tbl_p_orden WHERE gprId = :id AND ordFecha > :fecha AND ordTipo = 0";
    $sql = $dbConn->prepare($consulta);
    $sql->bindValue(':id', $Id);
    $sql->bindValue(':fecha', $fecha);
    $sql->execute();
    while ($row = $sql->fetch()) {
        $cont++;
    }
    return $cont;
}
function get_data_table($dbConn, $Id)
{
    $resultado = '
        <table id="tbl_table"
            class="table table-bordered table-striped table-hover table-bordered">
            <thead class="bg-navy" style="font-size:24px;">
                <th class="text-center">#</th>
                <th>CLIENTE</th>
                <th class="text-center">MARCA</th>
                <th class="text-center">SALDO</th>
                <th class="text-center">RECAUDACIÓN</th>
                <th class="text-center">PROCESAR</th>
            </thead>
            <tbody style="font-size:22px;">';
    $consulta = "SELECT o.ordId,o.gprId,c.cliNombres,o.ordEstado,c.cliMarca,o.ordCantidad,p.gprestadoDetalle,o.ordFecha FROM tbl_p_orden o, tbl_r_guiaproceso p, tbl_a_clientes c
        WHERE o.gprId is not null AND o.gprId = p.gprId AND p.cliId = c.cliId   AND o.espId = :id AND o.ordTipo = 0
        AND o.ordFecha BETWEEN :inicio AND :fin ORDER BY o.ordId ASC";
    $sql = $dbConn->prepare($consulta);
    $sql->bindValue(':id', $Id);
    $sql->bindValue(':inicio', date("Y-m-d") . " 00:00:00");
    $sql->bindValue(':fin', date("Y-m-d") . " 23:59:59");
    $sql->execute();
    $cont = 0;
    while ($row = $sql->fetch()) {
        $procesado = f_obtener_procesar($dbConn, $row["ordId"], $row["gprId"]);
        $saldo = intval($row["ordCantidad"]) - $procesado;
        $mensaje_saldo = "bg-danger";
        $bandera = f_buscar_orden($dbConn, $row["ordFecha"], $row["gprId"]);
        if ($bandera == 0) {
            if ($saldo == 0) $mensaje_saldo = "bg-success";
            else if ($saldo > 0) $mensaje_saldo = "bg-warning";
            // Estado Detalle = 0; No se detalla la guia => El Estado debe ser 1
            // Estado Detalle = 1; Se detalla la guia => El Estado debe ser 0
            $span = 'ERROR' . $row["gprestadoDetalle"] . '';
            if ($row["gprestadoDetalle"] == 0 || $row["gprestadoDetalle"] == 1) {
                $cont++;
                if ($row["gprestadoDetalle"] == 1) {
                    $span = '*' . $cont . '';
                } else if ($row["gprestadoDetalle"] == 0) {
                    $span = '' . $cont . '';
                }
            }
            $pago = 'bg-danger';
            $mensaje_pago = '';
            if ($row["ordEstado"] == 1) {
                $pago = 'bg-success';
                $mensaje_pago = 'RECAUDADO';
            } else {
                $pago = 'bg-warning';
                $mensaje_pago = 'POR RECAUDAR';
            }
            $resultado .= '
                <tr>
                    <th class="text-center">' . $span . '</th>
                    <td>' . utf8_encode($row["cliNombres"]) . ' </td>
                    <td class="text-center">' . utf8_encode($row["cliMarca"]) . '</td>
                    <td class="text-center ' . $mensaje_saldo . '"><b>' . $saldo . '</b></td>
                    <td class="text-center ' . $pago . '"><b>' . $mensaje_pago . '</b></td>
                    <td class="text-center">
                        <button class="btn btn-info" onclick="get_data_procesar(' . $row["ordId"] . ')" data-toggle="modal" data-target="#Modal">
                            <b>PROCESAR</b>
                        </button>
                    </td>
                </tr>';
        }
    }
    return $resultado . '</tbody></table>';
}
function get_data_orden($dbConn)
{
    $Id = $_POST["Id"];
    $consulta = "SELECT * FROM tbl_p_orden o, tbl_r_guiaproceso p, tbl_a_clientes c
    WHERE o.gprId is not null AND o.gprId = p.gprId AND p.cliId = c.cliId   AND o.ordId = :id
    AND o.ordFecha BETWEEN :inicio AND :fin ORDER BY o.ordId ASC";
    $sql = $dbConn->prepare($consulta);
    $sql->bindValue(':id', $Id);
    $sql->bindValue(':inicio', date("Y-m-d") . " 00:00:00");
    $sql->bindValue(':fin', date("Y-m-d") . " 23:59:59");
    $sql->execute();
    $cont = 0;
    if ($row = $sql->fetch()) {
        $procesado = f_obtener_procesar($dbConn, $row["ordId"], $row["gprId"]);
        $saldo = intval($row["ordCantidad"]) - $procesado;
        $bandera = f_buscar_orden($dbConn, $row["ordFecha"], $row["gprId"]);
        $footer = "";
        if ($bandera == 0) {
            $input = '';
            if ($saldo == 0) {
                $input  = '
                <h5 >
                    <b>
                        <span class="text-muted">PROCESADO:</span>
                        ' . $procesado . '
                    </b>
                </h5>';
            } else {
                $input = '
                <div class="input-group input-group-lg mt-3">
                    <div class="input-group-prepend">
                        <span class="input-group-text">
                            <b>
                                <span class="text-muted">SALDO: </span> ' . $saldo . '
                            </b>
                        </span>
                    </div>
                    <input type="text" class="form-control text-center input_disablecopypaste" id="txtCantidad" onkeypress="f_restrincion(event)"  maxlength="4" style="font-size:30px;" value="' . $saldo . '">
                    <span class="input-group-append">
                        <button type="button" class="btn btn-info btn-flat" onclick="f_proccesar(' . $row["ordId"] . ')"><b>PROCESAR</b></button>
                    </span>
                </div>';
            }
            $footer = $input . '<hr>
            <button type="button" class="btn btn-danger" id="btn-cerrar" data-dismiss="modal">
                <b>CANCELAR</b>
            </button>';
        } else {
            $footer = '
            <h5 class="text-center">
                <b>
                    <span class="text-muted">PROCESADO:</span>
                    ' . $procesado . '
                </b>
            </h5>
            <h6><b>No se puede realizar los descuentos porque se acaba de generar otra orde de producción</b></h6>';
        }
        return '
        <h5 class="text-center">
            <b>
                <span class="text-muted">ORDEN DE PRODUCCIÓN</span><br>
                <a href="../../documentos/producion/orden/' . $row["ordNumOrden"] . '.pdf"
                    target="_black">' . $row["ordNumOrden"] . '</a>
            </b>
        </h5>
        <h6 class="row">
            <b class="col-md-6">
                <span id="spanCliente" class="d-none">' . utf8_encode($row["cliNombres"]) . ' (' . utf8_encode($row["cliMarca"]) . ')</span>
                <span class="text-muted" >CLIENTE:</span>
                ' . utf8_encode($row["cliNombres"]) . '
            </b>
            <b class="col-md-6">
                <span class="text-muted">MARCA:</span>
                ' . utf8_encode($row["cliMarca"]) . '
            </b>
        </h6>' . $footer;
    } else return 'ERROR-98219222';
}
function get_data_especie($dbConn, $Id)
{
    $consulta = "SELECT espDescripcion FROM tbl_a_especies WHERE espId = :id";
    $sql = $dbConn->prepare($consulta);
    $sql->bindValue(':id', $Id);
    $sql->execute();
    if ($row = $sql->fetch()) return $row["espDescripcion"];
    else return 'NO ENCONTRADO';
}

function f_procesar_data($dbConn)
{
    try {
        $Id = $_POST['Id'];
        $cantidad = $_POST['Cantidad'];
        if (!is_numeric($cantidad)) return 'La cantidad ingresada es incorrecta';
        $cantidad = intval($cantidad);
        if ($cantidad == 0) return 'La cantidad ingresada es incorrecta';
        $consulta = "SELECT * FROM tbl_p_orden WHERE ordId = :id";
        $sql = $dbConn->prepare($consulta);
        $sql->bindValue(':id', $Id);
        $sql->execute();
        if ($row = $sql->fetch()) {
            $procesado = f_obtener_procesar($dbConn, $row["ordId"], $row["gprId"]);
            $saldo = intval($row["ordCantidad"]) - $procesado;
            $bandera = f_buscar_orden($dbConn, $row["ordFecha"], $row["gprId"]);
            if ($bandera != 0) return 'No se puede realizar los descuentos porque se acaba de generar otra orde de producción';
            if ($cantidad <=  $saldo) {
                $Acion = 'Faenamiento';
                $detalle = '<b>Faenamiento de la orden ' . $row["ordNumOrden"] . ' por ' . $cantidad . ' animales</b>';
                if (insertar_faenamiento($dbConn, $row["ordId"], $cantidad)) {
                    $estado = 1;
                    $total_comprobar = f_obtener_procesar_total($dbConn, $Id);
                    if ($cantidad == $saldo && $total_comprobar = 0) $estado = 2;
                    $sql = $dbConn->prepare('UPDATE tbl_r_guiaproceso SET gprEstado = :estado WHERE gprId = :id');
                    $sql->bindValue(':estado', $estado);
                    $sql->bindValue(':id', $row["gprId"]);
                    if ($sql->execute()) return Insert_Login($Id, 'tbl_p_orden', $Acion, $detalle, '');
                } else return "ERROR-665242";
                // if (f_update_esatdo_detalle($dbConn,$row["gprId"],$row["ordId"],$cantidad)){
                //     $estado = 1;
                //     $total_comprobar = f_obtener_procesar_total($dbConn,$Id);
                //     if ($cantidad == $saldo && $total_comprobar = 0) $estado = 2;
                //     $sql= $dbConn->prepare('UPDATE tbl_r_guiaproceso SET gprEstado = :estado WHERE gprId = :id');
                //     $sql->bindValue(':estado',$estado);
                //     $sql->bindValue(':id',$row["gprId"]);
                //     if ($sql->execute())return Insert_Login($Id,'tbl_p_orden',$Acion,$detalle,'');
                //     else return 'ERROR-27772';
                // }else return "ERROR-665242";
            } else {
                return 'La cantidad ingresada excede el saldo del cliente<br> ' . $cantidad . ' <= ' . $saldo . '';
            }
        } else return "ERROR-6555"; //NO SE ENCONTRO EL ID
    } catch (Exception $e) {
        Insert_Error('ERROR-6555', $e->getMessage(), 'ERROR AL PAGAR LA FACTURA');
        exit("ERROR-6555");
    }
}

function f_update_esatdo_detalle($dbConn, $Id, $Orden, $cantidad)
{
    try {
        $consulta = "SELECT d.dtId,p.espId,p.cliId,e.espLinea FROM tbl_r_detalle d, tbl_r_guiaproceso p, tbl_a_especies e
        WHERE  d.gprId = p.gprId AND p.espId = e.espId AND d.dtEliminado = 0 AND d.dtEstado = 0 AND  d.dtTipoRegistro = 0  AND d.ordId IS NULL AND d.gprId = :id AND d.dtDictamen = 0
        LIMIT " . $cantidad;
        $sql = $dbConn->prepare($consulta);
        $sql->bindValue(':id', $Id);
        // $sql->bindValue(':cantidad',$cantidad);
        $sql->execute();
        while ($row = $sql->fetch()) {
            if (insertar_faenamiento($dbConn, $row["espId"], $row["cliId"], $row["dtId"], $row["espLinea"])) {
                $sql1 = $dbConn->prepare('UPDATE tbl_r_detalle SET dtEstado = 1, ordId = :orden WHERE dtId = :id');
                $sql1->bindValue(':orden', $Orden);
                $sql1->bindValue(':id', $row["dtId"]);
                if ($sql1->execute());
            } else {
                return 'Error-28128';
            }
        }
        return true;
    } catch (Exception $e) {
        Insert_Error('ERROR-6555', $e->getMessage(), 'ERROR AL BUSCAR LOS ANIMALES');
        exit("ERROR-6555");
    }
}

function insertar_faenamiento($dbConn, $Id, $cantidad)
{
    try {
        global $User;
        global $Ip;
        $consulta = "INSERT INTO tbl_p_faenamiento(faeFecha,faeCantidad,ordId,usuId,ip)
        VALUES(:faeFecha,:faeCantidad,:ordId,:usuId,:ip)";
        $sql = $dbConn->prepare($consulta);
        $sql->bindValue(':faeFecha', date("Y-m-d H:i:s"));
        $sql->bindValue(':faeCantidad', $cantidad);
        $sql->bindValue(':ordId', $Id);
        $sql->bindValue(':usuId', $User);
        $sql->bindValue(':ip', $Ip);
        if ($sql->execute()) return true;
        else return false;
    } catch (Exception $e) {
        Insert_Error('ERROR-22112', $e->getMessage(), 'Error al ingresar al registrar de faenamiento');
        exit("ERROR-22112");
    }
}

function get_number_codigo($dbConn, $especie, $cliente)
{
    $letra = get_letra_especie($dbConn, $especie);
    $ident = get_num_cliente($dbConn, $cliente);
    $juliano = CalcularJuliano();
    if ($letra != false && $ident != false) {
        $consulta = "SELECT f.faeCodigo, c.cliNumero, e.espLetra
        FROM tbl_p_faenamiento f, tbl_r_detalle d, tbl_r_guiaproceso p, tbl_a_clientes c, tbl_a_especies e 
        WHERE f.dtId = d.dtId AND d.gprId = p.gprId AND p.cliId = c.cliId AND p.espId = e.espId AND d.dtTipoRegistro = 0
        AND p.cliId = :cliente AND p.espId = :especie AND p.gprTurno BETWEEN :inicio AND :final ORDER BY d.dtId DESC";
        $sql = $dbConn->prepare($consulta);
        $sql->bindValue(':cliente', $cliente);
        $sql->bindValue(':especie', $especie);
        $sql->bindValue(':inicio', date("Y-m-d") . " 00:00:00");
        $sql->bindValue(':final', date("Y-m-d") . " 23:59:59");
        $sql->execute();
        $numero = 1;
        $maximo = 3;
        while ($row = $sql->fetch()) {
            $arrayCodigo = explode("-", $row["faeCodigo"]);
            if (count($arrayCodigo) == 3) {
                if ($arrayCodigo[0] == $ident) {
                    $letraBuscar =  get_letra_comprobante($arrayCodigo[2]);
                    if ($letraBuscar == $letra) {
                        if ($arrayCodigo[1] == $juliano) {
                            $array = explode($letra, $arrayCodigo[2]);
                            $numero = $array[1] + 1;
                            $cantidad = strlen($numero);
                            $resultado = "";
                            for ($i = $cantidad; $i < $maximo; $i++) $resultado .= "0";
                            //RUC O CEDULA - JULIANO - LETRA CONTADOR
                            //12345678900001-36522-B001
                            return $ident . "-" . $juliano . "-" . $letra . $resultado . $numero;
                        }
                    }
                }
            }
        }
        $cantidad = strlen($numero);
        $resultado = "";
        for ($i = $cantidad; $i < $maximo; $i++) $resultado .= "0";
        //RUC O CEDULA - JULIANO - LETRA CONTADOR
        //12345678900001-36522-B001
        return $ident . "-" . $juliano . "-" . $letra . $resultado . $numero;
    } else {
        return false; //La especie no tiene una letra asignada
    }
}
function get_num_cliente($dbConn, $cliente)
{
    $resultado = false;
    $consulta = "SELECT cliNumero FROM tbl_a_clientes WHERE cliId =:code";
    $sql = $dbConn->prepare($consulta);
    $sql->bindValue(':code', $cliente);
    $sql->execute();
    if ($row = $sql->fetch()) {
        $resultado = $row["cliNumero"];
    }
    return  $resultado;
}
function CalcularJuliano()
{
    $numMes = date("n");
    $total = 0;
    $anio = date("Y");
    for ($i = 1; $i < $numMes; $i++) {
        $fecha = date("t", strtotime($anio . "-$i"));
        $total = $total + intval($fecha);
    }
    $total = $total + intval(date("d"));
    return "$total" . date("y");
}

function get_letra_especie($dbConn, $code_especie)
{
    $resultado = false;
    $consulta = "SELECT espLetra FROM tbl_a_especies WHERE espId =:code";
    $sql = $dbConn->prepare($consulta);
    $sql->bindValue(':code', $code_especie);
    $sql->execute();
    $cont = 0;
    if ($row = $sql->fetch()) {
        $resultado = $row["espLetra"];
    }
    return  $resultado;
}
function get_letra_comprobante($comprobante)
{
    for ($i = 65; $i <= 90; $i++) {
        $letraBuscar = chr($i);
        $arrayLetra = explode($letraBuscar, $comprobante);
        if (count($arrayLetra) == 2) return $letraBuscar;
    }
    return false;
}
