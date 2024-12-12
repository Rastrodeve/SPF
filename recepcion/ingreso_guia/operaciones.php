<?php
require '../../FilePHP/utils.php';
if (isset($_REQUEST['op'])) {
    $dbConn = conectar($db);
    $op = $_REQUEST['op'];
    if ($op == 1) echo Lista_de_Guias_movilizacion($dbConn);
    elseif ($op == 2) echo select_data_especies($dbConn);
    elseif ($op == 3) echo select_data_clientes($dbConn); //get_number_comprobante($dbConn,$_POST["Especie"]);
    elseif ($op == 4) echo select_data_provincias($dbConn);
    elseif ($op == 5) echo inserta_guia_mov($dbConn);
    elseif ($op == 6) {
        $_SESSION['OPCION'] = 1;
        $_SESSION['VARIABLE'] = $_POST["Id"];
        $_SESSION['INICIO'] = date("Y-m-d");
        $_SESSION['FINAL'] = date("Y-m-d");
    } elseif ($op == 7) echo get_estado_detalle($dbConn, $_POST["Id"]);
    elseif ($op == 8) echo get_data_view_detalle($dbConn);

    elseif ($op == 12) echo Comparar_fecha($_POST["Fecha"]);
    elseif ($op == 13) echo get_data_impr($dbConn);
    elseif ($op == 14) echo f_get_data_soli_guia_proceso($dbConn);
    elseif ($op == 15) echo f_insert_solicitud($dbConn);
    elseif ($op == 16) echo f_get_data_soli_guia_proceso_delete($dbConn);
    elseif ($op == 17) echo f_insert_solicitud_delete($dbConn);
    elseif ($op == 18) echo get_data_corral($dbConn);
    elseif ($op == 19) echo f_get_data_new_corral($dbConn);
    elseif ($op == 20) echo f_get_detalle_corral($dbConn, $_POST["Id"]);
    elseif ($op == 21) echo inserta_lugar_corral($dbConn);
    elseif ($op == 22) echo eliminar_lugar_corral($dbConn);
    elseif ($op == 23) echo date("Y-m-");
    elseif ($op == 35) echo get_send_mail($dbConn);
    elseif ($op == 36) echo f_send_mail($dbConn);
    elseif ($op == 37) echo $_SESSION['PDF-ID-GUIA'];
} else {
    // header('location: ../../');
}

function Lista_de_Guias_movilizacion($dbConn)
{
    $Id = $_POST["Id"];
    $resultado = '<table id="tbl_data_table" class="table table-bordered table-striped table-sm ">
            <thead>
                <th>#</th>
                <th>Nro. Guía de movilización</th>
                <th>Nro. Comprobante</th>
                <th>Cliente</th>
                <th>Ganado</th>
                <th>Corral - Marca</th>
                <th>Hembras</th>
                <th>Machos</th>
                <th class="text-center">Acciones</th>
            </thead>
    <tbody>';
    $consulta = "SELECT p.gprId, g.guiNumero, p.gprComprobante ,c.cliNombres, e.espDescripcion,p.gprMacho,p.gprHembra, p.gprEstado, p.gprestadoDetalle 
    FROM tbl_r_guiaproceso p, tbl_r_guiamovilizacion g, tbl_a_especies e, tbl_a_clientes c 
    WHERE p.guiId = g.guiId AND p.espId = e.espId AND p.cliId = c.cliId AND e.espId = :id AND
    g.guiEliminado = 0  AND p.gprEliminado = 0 AND p.gprHabilitado = 1
    ORDER BY p.gprTurno , gprComprobante ASC ";
    $sql = $dbConn->prepare($consulta);
    $sql->bindValue(':id', $Id);
    $sql->execute();
    $cont = 0;
    while ($row = $sql->fetch()) {
        if (f_obtener_procesar($dbConn, $row["gprId"]) == 0) {
            // Estado Detalle = 0; No se detalla la guia => El Estado debe ser 1
            // Estado Detalle = 1; Se detalla la guia => El Estado debe ser 0
            if ($row["gprEstado"] == 0 || $row["gprEstado"] == 1) {
                $cont++;
                $span = '<span class="badge badge-warning">' . $row["gprEstado"] . '</span>';
                if ($row["gprEstado"] == 0) {
                    $span = '<span class="btn btn-danger " onclick="Imprimir(' . $row["gprId"] . ')"   >' . $cont . '</span>';
                } else if ($row["gprEstado"] == 1) {
                    $span = '<span class="btn btn-success" onclick="Imprimir(' . $row["gprId"] . ')">' . $cont . '</span>';
                }
                $btn_soli = '';
                if (f_funcion_solicitud_comprobacion_1($dbConn, $row["gprId"]) == false) {
                    $btn_soli = '
                    <button class="btn btn-info btn-sm" data-toggle="modal" data-target="#Modal"  onclick="f_update_soli_guia_pro(' . $row["gprId"] . ')">
                        <b><i class="fas fa-pencil-alt"></i></b>
                    </button>
                    <button class="btn btn-danger btn-sm"  data-toggle="modal" data-target="#Modal" onclick="f_update_soli_guia_pro_delete(' . $row["gprId"] . ')">
                        <b><i class="fas fa-trash-alt"></i></b>
                    </button>';
                }
                $corral = f_obtener_corrales_guia($dbConn, $row["gprId"]);
                if ($corral == '') $corral = 'Añadir';
                $resultado .= '
                <tr">
                    <td><h4 class="text-center" >' . $span . '</h4></td>
                    <td> <a  href="#">' . utf8_encode($row["guiNumero"]) . '</a></td>
                    <td>' . utf8_encode($row["gprComprobante"]) . '</td>
                    <td>' . utf8_encode($row["cliNombres"]) . '</td>
                    <td>' . utf8_encode($row["espDescripcion"]) . '</td>
                    <td><a href="#" data-toggle="modal" data-target="#Modal" onclick="get_view_corrales(' . $row["gprId"] . ')" >' . $corral . '</a></td>
                    <td>' . $row["gprHembra"] . '</td>
                    <td>' . $row["gprMacho"] . '</td>
                    <td style="max-width:100px;" class="text-center">
                        ' . $btn_soli . '
                        <button class="btn btn-success btn-sm" data-toggle="modal" data-target="#Modal" onclick="get_view_detalle_guia(' . $row["gprId"] . ')">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button class="btn btn-info btn-sm" data-toggle="modal" data-target="#Modal" onclick="get_send_mail(' . $row["gprId"] . ')">
                        <i class="fas fa-envelope"></i>
                    </button>
                    </td>
                </tr>';
            }
        }
    }
    $btn = '<button class="btn btn-info btn-sm float-right" onclick="GET_Guias()"><b>RECARGAR</b></button>';
    $pdf = '<button class="btn btn-danger btn-sm mt-3 " onclick="generar_reporte(' . $Id . ')"><b>GENERAR PDF</b></button>';
    return $btn . $resultado . "</tbody></table>" . $pdf;
}
function f_obtener_procesar($dbConn, $Id)
{
    $cont = 0;
    // $consulta="SELECT * FROM tbl_r_detalle  
    // WHERE dtEstado = 1  AND  ordId is not null AND gprId = :id";
    $consulta = "SELECT * FROM tbl_p_orden WHERE gprId = :id AND ordTipo = 0";
    $sql = $dbConn->prepare($consulta);
    $sql->bindValue(':id', $Id);
    $sql->execute();
    while ($row = $sql->fetch()) {
        $cont++;
    }
    return $cont;
}

function select_data_especies($dbConn)
{
    $resultado = '<option value="0" selected="" >Seleciona el tipo de ganado</option>';
    $consulta = "SELECT * FROM tbl_a_especies WHERE espEstado = 0";
    $sql = $dbConn->prepare($consulta);
    $sql->execute();
    $cont = 0;
    while ($row = $sql->fetch()) {
        if (comprobar_servicios($dbConn, $row["espId"]) > 0) {
            if (comprobar_productos($dbConn, $row["espId"]) > 0) {
                $resultado .= '<option value="' . $row["espId"] . '" >' . utf8_encode($row["espDescripcion"]) . '</option>';
            }
        }
    }
    return  $resultado;
}

function comprobar_servicios($dbConn, $Id)
{
    $consulta = "SELECT * FROM tbl_a_servicios WHERE espId = :id  AND srnEstado = 0";
    $sql = $dbConn->prepare($consulta);
    $sql->bindValue(':id', $Id);
    $sql->execute();
    $cont = 0;
    while ($row = $sql->fetch()) {
        $cont++;
    }
    return $cont;
}
function comprobar_productos($dbConn, $Id)
{
    $consulta = "SELECT * FROM tbl_a_productos WHERE espId = :id  AND proEliminado = 0";
    $sql = $dbConn->prepare($consulta);
    $sql->bindValue(':id', $Id);
    $sql->execute();
    $cont = 0;
    while ($row = $sql->fetch()) {
        $cont++;
    }
    return $cont;
}

function get_number_comprobante($dbConn, $code_especie)
{
    $letra = get_letra_especie($dbConn, $code_especie);
    if ($letra != false) {
        $consulta = "SELECT gprComprobante FROM tbl_r_guiaproceso WHERE espId = :tipo order by gprId DESC";
        $sql = $dbConn->prepare($consulta);
        $sql->bindValue(':tipo', $code_especie);
        $sql->execute();
        $anio = date("Y");
        $numero = 0;
        while ($row = $sql->fetch()) {
            $letraBuscar =  get_letra_comprobante($row["gprComprobante"]);
            if ($letraBuscar == $letra) {
                $array = explode($letra, $row["gprComprobante"]);
                if ($array[0] != $anio) $numero = 1;
                else $numero = $array[1] + 1;
                return $anio . $letra . crearNumero($numero);
            }
        }
        if ($code_especie == 1) return $anio . $letra . crearNumero(1470);
        else if ($code_especie == 2) return $anio . $letra . crearNumero(3561);
        else if ($code_especie == 3) return $anio . $letra . crearNumero(570);
        else return $anio . $letra . crearNumero(1);
    } else {
        return "ERROR-81928"; //La especie no tiene una letra asignada
    }
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

function crearNumero($numero)
{
    $maximo = 5;
    $cantidad = strlen($numero);
    $resultado = "";
    for ($i = $cantidad; $i < $maximo; $i++) {
        $resultado = $resultado . "0";
    }
    return $resultado . $numero;
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
function select_data_provincias($dbConn)
{
    $resultado = '';
    $consulta = "SELECT * FROM tbl_provincias ORDER BY pProvincia ASC";
    $sql = $dbConn->prepare($consulta);
    $sql->execute();
    $cont = 0;
    while ($row = $sql->fetch()) {
        $resultado .= '<option value="' . $row["pId"] . '" >' . utf8_encode($row["pProvincia"]) . '</option>';
    }
    return  $resultado;
}
function inserta_guia_mov($dbConn)
{
    try {
        global $User;
        global $Ip;
        $guia_m = trim($_POST["Guia"]);
        $fecha_guia = $_POST["Fecha_Guia"];
        $fecha_validez = $_POST["Fecha_Validez"];
        $provincia = $_POST["Provincia"];
        $code_vacu = $_POST["Code_Vacuna"];
        $cedula_con = $_POST["Cedula_Condu"];
        $conductor = $_POST["Conductor"];
        $vehiculo = $_POST["Vehiculo"];
        $placa = $_POST["Placa"];
        $cliente = $_POST["Cliente"];
        $Observacion = trim($_POST["Observacion"]);
        if (search_guia_movilizacion($dbConn, $guia_m) == true) {
            exit("Se dectectó una guía registrada con el número (" . $guia_m . ")");
        }

        $consulta = "INSERT INTO tbl_r_guiamovilizacion(guiNumero,guiFechaInicio,guiFechaFinal,guiFechaIngreso,guiVacunacion,guiCiConductor,guiNombreConductor,guiVehiculo,guiVehiculoPlaca,guiObservacion,guiTurno,pId,usuId,ip)
        VALUES(:guiNumero,:guiFechaInicio,:guiFechaFinal,:guiFechaIngreso,:guiVacunacion,:guiCiConductor,:guiNombreConductor,:guiVehiculo,:guiVehiculoPlaca,:guiObservacion,NULL,:pId,:usuId,:ip)";
        $sql = $dbConn->prepare($consulta);
        $sql->bindValue(':guiNumero', utf8_decode($guia_m));
        $sql->bindValue(':guiFechaInicio', transformar_fecha($fecha_guia));
        $sql->bindValue(':guiFechaFinal', transformar_fecha($fecha_validez));
        $sql->bindValue(':guiFechaIngreso', date("Y-m-d H:i:s"));
        $sql->bindValue(':guiVacunacion', $code_vacu);
        $sql->bindValue(':guiCiConductor', $cedula_con);
        $sql->bindValue(':guiNombreConductor', utf8_decode($conductor));
        $sql->bindValue(':guiVehiculo', $vehiculo);
        $sql->bindValue(':guiVehiculoPlaca', $placa);
        $sql->bindValue(':guiObservacion', utf8_decode($Observacion));
        $sql->bindValue(':pId', $provincia);
        $sql->bindValue(':usuId', $User);
        $sql->bindValue(':ip', $Ip);
        if ($sql->execute()) {
            $Id = $dbConn->lastInsertId();
            // $_SESSION['PDF-ID-GUIA'] = $Id;
            $Acion = 'Nueva guía de movilización';
            $detalle = 'Número de la guia <b>' . $guia_m . '</b>';
            if (Insert_Login($Id, 'tbl_r_guiamovilizacion', $Acion, $detalle, '')) {
                $Array = $_POST["Array"];
                $cont = 0;
                for ($i = 0; $i < count($Array); $i++) {
                    $NewArray = [$cliente, $Array[$i][0], $Array[$i][1], $Array[$i][2]];
                    if (insert_guia_proceso($dbConn, $NewArray, $Id) != true) $cont++;
                }
                if ($cont == 0) return true;
                else return 'ERROR-129012';
            } else return 'ERROR-092222';
        } else return "ERROR-665242"; //
    } catch (Exception $e) {
        Insert_Error('ERROR-887222', $e->getMessage(), 'Error al ingresar una guia de movilización');
        exit("ERROR-887222");
    }
}

function transformar_fecha($fecha)
{
    $array = explode("/", $fecha);
    return $array[2] . "-" . $array[1] . "-" . $array[0];
}

function select_data_clientes($dbConn)
{
    $resultado = '<option value="0" >Seleccione un cliente</option>';
    $consulta = "SELECT * FROM tbl_a_clientes WHERE cliEstado = 0 ORDER BY cliNombres ASC ";
    $sql = $dbConn->prepare($consulta);
    $sql->execute();
    $cont = 0;
    while ($row = $sql->fetch()) {
        $cont++;
        $resultado .= '<option value="' . $row["cliId"] . '" >' . utf8_encode($row["cliNombres"]) . '</option>';
    }
    if ($cont == 0) {
        $resultado = '<option value="0" >No se encontraron clientes</option>';
    }
    return  $resultado;
}
function search_guia_movilizacion($dbConn, $guia)
{
    $consulta = "SELECT * FROM tbl_r_guiamovilizacion g, tbl_r_guiaproceso  p WHERE g.guiId = p.guiId AND p.espId = 1 AND g.guiNumero = :guia AND g.guiEliminado = 0";
    $sql = $dbConn->prepare($consulta);
    $sql->bindValue(':guia', $guia);
    $sql->execute();
    while ($row = $sql->fetch()) {
        return true;
    }
    return  false;
}

function insert_guia_proceso($dbConn, $Array, $Id)
{
    try {
        // $Array =  [cliente,ganado,hembra,macho]
        global $User;
        global $Ip;
        $comprobante = get_number_comprobante($dbConn, $Array[1]);
        $detalle = get_estado_detalle($dbConn, $Array[1]); //0 No se detalla; 1 si se detalla
        $estado = 0;
        if ($detalle == 0) $estado = 1;
        $consulta = "INSERT INTO tbl_r_guiaproceso(gprComprobante,gprHembra,gprMacho,gprTurno,gprEstado,gprestadoDetalle,gprHabilitado,guiId,espId,cliId,usuId,ip)
        VALUES(:gprComprobante,:gprHembra,:gprMacho,:gprTurno,:gprEstado,:gprestadoDetalle,:gprHabilitado,:guiId,:espId,:cliId,:usuId,:ip)";
        $sql = $dbConn->prepare($consulta);
        $sql->bindValue(':gprComprobante', utf8_decode($comprobante));
        $sql->bindValue(':gprHembra', $Array[2]);
        $sql->bindValue(':gprMacho', $Array[3]);
        $sql->bindValue(':gprTurno', date("Y-m-d H:i:s"));
        $sql->bindValue(':gprEstado', $estado);
        $sql->bindValue(':gprestadoDetalle', $detalle);
        $sql->bindValue(':gprHabilitado', 1);
        $sql->bindValue(':guiId', $Id);
        $sql->bindValue(':espId', $Array[1]);
        $sql->bindValue(':cliId', $Array[0]);
        $sql->bindValue(':usuId', $User);
        $sql->bindValue(':ip', $Ip);
        if ($sql->execute()) {
            $IdP = $dbConn->lastInsertId();
            $_SESSION['PDF-ID-GUIA'] = $IdP;
            for ($i = 0; $i < ($Array[2]  + $Array[3]); $i++) {
                if (insertar_detalle_automatico($dbConn, $Array[1], $Array[0], $IdP) == false) {
                    return 'No se pudo registrar todos los animales <br><b>Registrados: ' . ($i + 1) . '</b>';
                }
            }
            if (busqueda_de_subproductos($dbConn, $Array[2], $Array[3], $IdP, $Array[1]) == false) return 'No se pudo procesar todos los subproductos';
            $Acion = 'Nueva Guía de proceso ';
            $detalle = 'Número de comprobante <b>' . $comprobante . '</b>';
            if (Insert_Login($IdP, 'tbl_r_guiaproceso', $Acion, $detalle, '')) return true;
            else return 'ERROR-092222';
        } else return "ERROR-665242"; //
    } catch (Exception $e) {
        Insert_Error('ERROR-887222', $e->getMessage(), 'Error al ingresar una guia de proceso');
        exit("ERROR-887222");
    }
}
function busqueda_de_subproductos($dbConn, $hembra, $macho, $Id, $especie)
{
    $consulta = "SELECT * FROM tbl_a_subproductos WHERE espId = :code AND subEstado = 0";
    $sql = $dbConn->prepare($consulta);
    $sql->bindValue(':code', $especie);
    $sql->execute();
    while ($row = $sql->fetch()) {
        $cantidad_new = 0;
        if ($row["subSexo"] == 1) { // Solo hembra
            $cantidad_new = $hembra * $row["subParte"];
        } elseif ($row["subSexo"] == 2) {
            $cantidad_new = $macho * $row["subParte"];
        } elseif ($row["subSexo"] == 0) {
            $cantidad_new = ($macho + $hembra) * $row["subParte"];
        }
        if ($cantidad_new > 0) {
            if (insertar_visceras_automatico($dbConn, $cantidad_new, $row["subParte"], $row["subId"], $Id, $row["subSexo"]) == false) return false;
        }
    }
    return true;
}

function get_estado_detalle($dbConn, $tipo)
{
    $consulta = "SELECT espDetalle FROM tbl_a_especies WHERE espId = :code";
    $sql = $dbConn->prepare($consulta);
    $sql->bindValue(':code', $tipo);
    $sql->execute();
    if ($row = $sql->fetch()) {
        return $row["espDetalle"];
    } else {
        return "ERROR"; //NO SE ENCONTRO LA ESPECIE
    }
}
function insertar_detalle_automatico($dbConn, $especie, $cliente, $Id)
{
    try {
        global $User;
        global $Ip;
        $codigo = get_number_codigo($dbConn, $especie, $cliente);
        if ($codigo == false) return 'No se puedo encontrar el codigo';
        $consulta = "INSERT INTO tbl_r_detalle(dtCodigo,dtFechaRegistro,dtRazon,gprId,usuId,ip)
                    VALUES(:dtCodigo,:dtFechaRegistro,:dtRazon,:gprId,:usuId,:ip)";
        $sql = $dbConn->prepare($consulta);
        $sql->bindValue(':dtCodigo', $codigo);
        $sql->bindValue(':dtFechaRegistro', date("Y-m-d H:i:s"));
        $sql->bindValue(':dtRazon', 'Automatico');
        $sql->bindValue(':gprId', $Id);
        $sql->bindValue(':usuId', $User);
        $sql->bindValue(':ip', $Ip);
        if ($sql->execute()) return true;
        else return false;
    } catch (Exception $e) {
        Insert_Error('ERROR-6622', $e->getMessage(), 'Error al ingresar al registrar automaticamente un animal');
        exit("ERROR-6622");
    }
}
function insertar_visceras_automatico($dbConn, $cantidad, $parte, $suproducto, $Id, $para)
{
    try {
        global $User;
        global $Ip;
        $consulta = "INSERT INTO tbl_r_visceras(vscFecha,vscCantidad,vscParte,vscSexo,subId,gprId,usuId,ip)
                            VALUES(:vscFecha,:vscCantidad,:vscParte,:vscSexo,:subId,:gprId,:usuId,:ip)";
        $sql = $dbConn->prepare($consulta);
        $sql->bindValue(':vscFecha', date("Y-m-d H:i:s"));
        $sql->bindValue(':vscCantidad', $cantidad);
        $sql->bindValue(':vscParte', $parte);
        $sql->bindValue(':vscSexo', $para);
        $sql->bindValue(':subId', $suproducto);
        $sql->bindValue(':gprId', $Id);
        $sql->bindValue(':usuId', $User);
        $sql->bindValue(':ip', $Ip);
        if ($sql->execute()) return true;
        else return false;
    } catch (Exception $e) {
        Insert_Error('ERROR-6612', $e->getMessage(), 'Error al ingresar al registrar automaticamente las visceras');
        exit("ERROR-6612");
    }
}

function get_number_codigo($dbConn, $especie, $cliente)
{
    $letra = get_letra_especie($dbConn, $especie);
    $ident = get_num_cliente($dbConn, $cliente);
    $juliano = CalcularJuliano();
    if ($letra != false && $ident != false) {
        $consulta = "SELECT d.dtCodigo, c.cliNumero, e.espLetra
        FROM tbl_r_detalle d, tbl_r_guiaproceso p, tbl_a_clientes c, tbl_a_especies e 
        WHERE d.gprId = p.gprId AND p.cliId = c.cliId AND p.espId = e.espId AND d.dtTipoRegistro = 0
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
            $arrayCodigo = explode("-", $row["dtCodigo"]);
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

function Comparar_fecha($fecha)
{
    $fecha_actual = strtotime(date("d-m-Y"));
    $fecha_entrada = strtotime($fecha);
    if ($fecha_actual >= $fecha_entrada) return false; //La fecha actual es mayor a la comparada
    else return true; //La fecha comparada es  menor a la actual
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

function get_data_view_detalle($dbConn)
{
    $Id = $_POST["Id"];
    $consulta = "SELECT * FROM tbl_r_guiaproceso p, tbl_a_clientes c  WHERE p.cliId = c.cliId AND  p.gprId = :id";
    $sql = $dbConn->prepare($consulta);
    $sql->bindValue(':id', $Id);
    $sql->execute();
    $cont = 0;
    if ($row = $sql->fetch()) {
        $table_detalle = f_get_table_detalle($dbConn, $Id);
        $table_viscers = f_get_table_visceas($dbConn, $Id);
        $data = '
            <h5 class="text-muted">
                <b>DETALLE DE GUÍA</b>
            </h5>
            <hr>
            <h6 >
                <b>
                    <span class="text-muted">CLIENTE:</span>
                    ' . utf8_encode($row["cliNombres"]) . '
                </b>
            </h6>
            <h6 >
                <b>
                    <span class="text-muted">COMPROBANTE:</span>
                    ' . $row["gprComprobante"] . '
                </b>
            </h6>
            <h6 >
                <b>
                    <span class="text-muted">HEMBRAS:</span>
                    ' . $row["gprHembra"] . '
                </b>
                <b>
                    <span class="text-muted">MACHOS:</span>
                    ' . $row["gprMacho"] . '
                </b>
            </h6>
            <h6 >
                <b>
                    <span class="text-muted">TOTAL:</span>
                    ' . ($row["gprMacho"] + $row["gprHembra"]) . '
                </b>
            </h6>
            <hr>
            <div class="card card-navy card-tabs">
                <div class="card-header p-0 pt-1">
                    <ul class="nav nav-tabs" id="custom-tabs-five-tab" role="tablist">
                        <li class="nav-item d-none">
                            <a class="nav-link " id="custom-tabs-five-overlay-tab" data-toggle="pill"
                                href="#custom-tabs-five-overlay" role="tab"
                                aria-controls="custom-tabs-five-overlay" aria-selected="true">Ganado</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" id="custom-tabs-five-overlay-dark-tab" data-toggle="pill"
                                href="#custom-tabs-five-overlay-dark" role="tab"
                                aria-controls="custom-tabs-five-overlay-dark" aria-selected="false">Subproductos</a>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content" id="custom-tabs-five-tabContent">
                        <div class="tab-pane fade " id="custom-tabs-five-overlay" role="tabpanel"
                            aria-labelledby="custom-tabs-five-overlay-tab">
                            <div class="overlay-wrapper">' . $table_detalle . '</div>
                        </div>
                        <div class="tab-pane fade show active" id="custom-tabs-five-overlay-dark" role="tabpanel"
                            aria-labelledby="custom-tabs-five-overlay-dark-tab">
                            <div class="overlay-wrapper">
                            ' . $table_viscers . '
                            </div>
                        </div>
                    </div>
                </div>
            </div>';
        return return_modal_body($data);
    } else return return_modal_body('ERROR-9192891');
}
function f_get_table_detalle($dbConn, $Id)
{
    $resultado = '<table id="tbl_data_detalle" class="table table-bordered table-striped table-sm ">
                <thead>
                    <th>#</th>
                    <th>CÓDIGO</th>
                    <th>PESO</th>
                </thead>
                <tbody>';
    $consulta = "SELECT * FROM tbl_r_detalle WHERE gprId = :id AND dtEliminado = 0";
    $sql = $dbConn->prepare($consulta);
    $sql->bindValue(':id', $Id);
    $sql->execute();
    $cont = 0;
    while ($row = $sql->fetch()) {
        $cont++;
        $resultado .= '
        <tr>
            <th>' . $cont . '</th>
            <td>' . $row["dtCodigo"] . '</td>
            <td>' . $row["dtPeso"] . '</td>
        </tr>';
    }
    return $resultado . "</tbody></table>";
}
function f_get_table_visceas($dbConn, $Id)
{
    $resultado = '<table id="tbl_data_visceras" class="table table-bordered table-striped table-sm ">
                <thead>
                    <th>#</th>
                    <th>SUBPRODCUTO</th>
                    <th>CANTIDAD POR ANIMAL</th>
                    <th>CANTIDAD TOTAL</th>
                </thead>
                <tbody>';
    $consulta = "SELECT * FROM tbl_r_visceras v, tbl_a_subproductos s WHERE v.subId = s.subId AND v.gprId = :id";
    $sql = $dbConn->prepare($consulta);
    $sql->bindValue(':id', $Id);
    $sql->execute();
    $cont = 0;
    while ($row = $sql->fetch()) {
        $cont++;
        $resultado .= '
        <tr>
            <th>' . $cont . '</th>
            <td>' . utf8_encode($row["subDescripcion"]) . '</td>
            <td>' . $row["vscParte"] . '</td>
            <td>' . $row["vscCantidad"] . '</td>
        </tr>';
    }
    return $resultado . "</tbody></table>";
}

function return_modal_header($titutlo)
{
    return ' <div class="modal-header">
                <h5 class="modal-title">' . $titutlo . '</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>';
}
function return_modal_body($data)
{
    return '<div class="modal-body">' . $data . '</div>';
}
function return_modal_foot($funtion)
{
    return '<div class="modal-footer">
                <button type="button" class="btn btn-primary" onclick="' . $funtion . '"><b>GUARDAR</b></button>
                <button type="button" class="btn btn-danger" data-dismiss="modal" id="btnCerrar" ><b>CANCELAR</b></button>
            </div>';
}
function get_data_corral($dbConn)
{
    $Id = $_POST["Id"];
    $consulta = "SELECT * FROM tbl_r_guiaproceso p, tbl_a_clientes c  WHERE p.cliId = c.cliId AND  p.gprId = :id";
    $sql = $dbConn->prepare($consulta);
    $sql->bindValue(':id', $Id);
    $sql->execute();
    $cont = 0;
    if ($row = $sql->fetch()) {
        $data = '
            <input value=' . $row["gprId"] . ' id="txtIdGuiaCorral" type="hidden">
            <h5 class="text-muted">
                <b>DETALLE DE GUÍA</b>
            </h5>
            <hr>
            <h6 >
                <b>
                    <span class="text-muted">CLIENTE:</span>
                    ' . utf8_encode($row["cliNombres"]) . '
                </b>
            </h6>
            <h6 >
                <b>
                    <span class="text-muted">COMPROBANTE:</span>
                    ' . $row["gprComprobante"] . '
                </b>
            </h6>
            <h6 >
                <b>
                    <span class="text-muted">HEMBRAS:</span>
                    ' . $row["gprHembra"] . '
                </b>
                <b>
                    <span class="text-muted">MACHOS:</span>
                    ' . $row["gprMacho"] . '
                </b>
            </h6>
            <h6 >
                <b>
                    <span class="text-muted">TOTAL:</span>
                    ' . ($row["gprMacho"] + $row["gprHembra"]) . '
                </b>
            </h6>
            <hr><div id="cont-cont_corral"> ' . f_get_detalle_corral($dbConn, $Id) . '</div>';
        return return_modal_body($data);
    } else return return_modal_body('ERROR-9192891');
}
function f_get_detalle_corral($dbConn, $Id)
{
    $resultado = '<table id="tbl_data_detalle_corral" class="table table-bordered table-striped table-sm text-center">
                <thead>
                    <th>#</th>
                    <th>CANTIDAD</th>
                    <th>MARCA</th>
                    <th>CORRAL</th>
                    <th>ACCIONES</th>
                </thead>
                <tbody>';
    $consulta = "SELECT * FROM tbl_r_lugar l, tbl_a_corral c WHERE  l.crrId = c.crrId  AND  l.gprId = :id AND l.lgrEliminado = 0";
    $sql = $dbConn->prepare($consulta);
    $sql->bindValue(':id', $Id);
    $sql->execute();
    $cont = 0;
    $total = get_cantidad_guia_proceso($dbConn, $Id);
    $total_corral = 0;
    while ($row = $sql->fetch()) {
        $cont++;
        $total_corral += $row["lgrCantidad"];
        $resultado .= '
        <tr>
            <th>' . $cont . '</th>
            <td>' . $row["lgrCantidad"] . '</td>
            <td>' . utf8_encode($row["lgrMarca"]) . '</td>
            <td>' . utf8_encode($row["crrDescripcion"]) . '</td>
            <td>
                <button class="btn btn-danger btn-sm"   onclick="function_delete_lugar(' . $row["lgrId"] . ')">
                    <b><i class="fas fa-trash-alt"></i></b>
                </button>
            </td>
        </tr>';
    }
    $button = '';
    if ($total_corral < $total) {
        $button = '<center> <button class="btn btn-danger" onclick="get_new_corrales()"><b>AÑADIR</b></button> </center>';
    }
    return $resultado . "</tbody></table>" . $button;
}
function get_cantidad_guia_proceso($dbConn, $Id)
{
    $consulta = "SELECT * FROM tbl_r_guiaproceso WHERE gprId = :id";
    $sql = $dbConn->prepare($consulta);
    $sql->bindValue(':id', $Id);
    $sql->execute();
    if ($row = $sql->fetch()) return ($row["gprMacho"] + $row["gprHembra"]);
    else return 0;
}
function get_cantidad_guia_proceso_marca($dbConn, $Id)
{
    $consulta = "SELECT c.cliMarca FROM tbl_r_guiaproceso p, tbl_a_clientes c WHERE p.cliId = c.cliId AND p.gprId = :id";
    $sql = $dbConn->prepare($consulta);
    $sql->bindValue(':id', $Id);
    $sql->execute();
    if ($row = $sql->fetch()) return utf8_encode($row["cliMarca"]);
    else 'NULL';
}
function f_obtener_corrales_guia($dbConn, $Id)
{
    $consulta = "SELECT c.crrDescripcion, l.lgrMarca FROM tbl_r_lugar l, tbl_a_corral c WHERE l.crrId = c.crrId AND l.lgrEliminado = 0 AND l.gprId = :id";
    $sql = $dbConn->prepare($consulta);
    $sql->bindValue(':id', $Id);
    $sql->execute();
    $corrales = '';
    while ($row = $sql->fetch()) $corrales .= "(" . utf8_encode($row["crrDescripcion"]) . ' ' . utf8_encode($row["lgrMarca"]) . ' )';
    return $corrales;
}
function get_cantidad_guia_proceso_corral($dbConn, $Id)
{
    $consulta = "SELECT * FROM tbl_r_lugar  WHERE gprId = :id AND lgrEliminado = 0";
    $sql = $dbConn->prepare($consulta);
    $sql->bindValue(':id', $Id);
    $sql->execute();
    $cont = 0;
    while ($row = $sql->fetch()) $cont += $row["lgrCantidad"];
    return $cont;
}
function f_get_data_new_corral($dbConn)
{
    $Id = $_POST["Id"];
    $corral = get_cantidad_guia_proceso_corral($dbConn, $Id);
    $guia = get_cantidad_guia_proceso($dbConn, $Id);
    $marca =  get_cantidad_guia_proceso_marca($dbConn, $Id);
    $saldo =  $guia - $corral;
    if ($saldo > 0) {
        return '
        <div class="row">
            <div class="col-3">
                <label>Cantidad:</label>
                <input class="form-control form-control-sm" id="txtCantidadCorral" value=' . $saldo . '>
            </div>
            <div class="col-3">
                <label>Marca:</label>
                <input class="form-control form-control-sm" id="txtMarca" value=' . $marca . '>
            </div>
            <div class="col-6">
                <label>Corral:</label>
                <select class="form-control form-control-sm" id="slcCorral">
                ' . Lista_corrales($dbConn, 0) . '
                </select>
            </div>
        </div>
        <hr>
        <div class="row">
            <div class="col-6 text-center">
                <button class="btn btn-info " onclick="function_insert_lugar()"><b>GUARDAR</b></button>
            </div>   
            <div class="col-6 text-center">
                <button class="btn btn-danger " onclick="f_get_table_corral()"><b>CANCELAR</b></button>
            </div>   
        </div>';
    }
}
function Lista_corrales($dbConn, $Id)
{
    $resultado = '<option value="0">SELECCIONE UN CORRAL</option>';
    $consulta = "SELECT * FROM tbl_a_corral WHERE crrEstado = 0";
    $sql = $dbConn->prepare($consulta);
    $sql->bindValue(':id', $Id);
    $sql->execute();
    while ($row = $sql->fetch()) {
        if ($Id == $row["crrId"]) $resultado .= '<option value="' . $row["crrId"] . '" selected>' . utf8_encode($row["crrDescripcion"]) . '</option>';
        else $resultado .= '<option value="' . $row["crrId"] . '">' . utf8_encode($row["crrDescripcion"]) . '</option>';
    }
    return $resultado;
}
function inserta_lugar_corral($dbConn)
{
    try {
        global $User;
        global $Ip;
        $Id = $_POST["Id"];
        $cantidad = $_POST["Cantidad"];
        $marca = $_POST["MarcaLugar"];
        $corral = trim($_POST["Corral"]);
        $corrales_in = get_cantidad_guia_proceso_corral($dbConn, $Id);
        $guia = get_cantidad_guia_proceso($dbConn, $Id);
        if ($corral < 1) return '<h5>La cantidad ingresada es incorrecta</h5>';
        if (($corrales_in + $cantidad) > $guia) return '<h5>La cantidad ingresada es incorrecta</h5>';
        $consulta = "INSERT INTO tbl_r_lugar(lgrCantidad,lgrMarca,lgrFecha,crrId,gprId,usuId,ip) 
        VALUES (:lgrCantidad,:lgrMarca,:lgrFecha,:crrId,:gprId,:usuId,:ip)";
        $sql = $dbConn->prepare($consulta);
        $sql->bindValue(':lgrCantidad', $cantidad);
        $sql->bindValue(':lgrMarca', $marca);
        $sql->bindValue(':lgrFecha', date("Y-m-d H:i:s"));
        $sql->bindValue(':crrId', $corral);
        $sql->bindValue(':gprId', $Id);
        $sql->bindValue(':usuId', $User);
        $sql->bindValue(':ip', $Ip);
        if ($sql->execute()) {
            return true;
            // $Id= $dbConn->lastInsertId();
            // $Acion = 'Nuevo corral';
            // $detalle = 'Número de la guia <b>'.$guia_m.'</b>';
            // if(Insert_Login($Id,'tbl_r_guiamovilizacion',$Acion,$detalle,'')){
            //     $Array = $_POST["Array"];
            //     $cont=0;
            //     for ($i=0; $i < count($Array) ; $i++) { 
            //         $NewArray = [$cliente,$Array[$i][0],$Array[$i][1],$Array[$i][2]];
            //         if(insert_guia_proceso($dbConn,$NewArray,$Id) != true)$cont++;
            //     }
            //     if($cont==0)return true;
            //     else return 'ERROR-129012';
            // }else return 'ERROR-092222';
        } else return "ERROR-667242"; //
    } catch (Exception $e) {
        Insert_Error('ERROR-887332', $e->getMessage(), 'Error al ingresar un corral');
        exit("ERROR-887332");
    }
}
function eliminar_lugar_corral($dbConn)
{
    try {
        global $User;
        global $Ip;
        $Id = $_POST["Id"];
        $consulta = "UPDATE tbl_r_lugar SET lgrEliminado = 1 WHERE lgrId = :id";
        $sql = $dbConn->prepare($consulta);
        $sql->bindValue(':id', $Id);
        if ($sql->execute()) return true;
        else return "ERROR-667241"; //
    } catch (Exception $e) {
        Insert_Error('ERROR-887331', $e->getMessage(), 'Error al eliminar un corral');
        exit("ERROR-887331");
    }
}

// FUNCIONES PARA GENERAR LAS SOLCITUDES
function f_get_data_soli_guia_proceso($dbConn)
{
    $Id = $_POST["Id"];
    $consulta = "SELECT c.cliNombres,p.gprHembra,p.gprMacho,p.cliId
    FROM tbl_r_guiaproceso p, tbl_a_clientes c 
    WHERE p.cliId = c.cliId AND  gprId = :id";
    $sql = $dbConn->prepare($consulta);
    $sql->bindValue(':id', $Id);
    $sql->execute();
    if ($row = $sql->fetch()) {
        if (f_funcion_solicitud_comprobacion_1($dbConn, $Id) == false) {
            $header = return_modal_header('<h5 class="modal-title"><b>SOLICITUD DE EDICIÓN</b></h5>');
            $footer = return_modal_foot('f_insert_solicitud()');
            $body = '
            <h5 class="text-muted">COMPROBANTE: <b>2022B0001</b></h5>
            <input type="hidden" value="' . $Id . '" id="txtIdGuia">
            <table width = "100%" class="table" >
                <thead>
                    <tr>
                        <th>#</th>
                        <th>CAMPO</th>
                        <th>ACTUAL</th>
                        <th>NUEVO</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <div class="icheck-primary d-inline">
                                <input type="checkbox" id="cbxCliente" checked="">
                                <label for="cbxCliente"></label>
                            </div>
                        </td>
                        <td>CLIENTE</td>
                        <td>
                            <span class="form-control form-control-sm">' . utf8_encode($row["cliNombres"]) . '</span>
                        </td>
                        <td>
                            <select id="slcClienteEdit" class="form-control form-control-sm">' . select_data_clientes_soli($dbConn, $row["cliId"]) . '</select>
                        </td>
                    </tr>  
                    <tr>
                        <td>
                            <div class="icheck-primary d-inline">
                                <input type="checkbox" id="cbxCantidadHembras" checked="">
                                <label for="cbxCantidadHembras"></label>
                            </div>
                        </td>
                        <td>CANT. HEMBRAS</td>
                        <td>
                            <span class="form-control form-control-sm">' . $row["gprHembra"] . '</span>
                        </td>
                        <td>
                            <input type="text" class="form-control form-control-sm input_disablecopypaste" onKeyPress="f_OnlyCant(event)" maxlength="4" id="txtHembra_ac" placeholder="Solo números">
                        </td>
                    </tr> 
                    <tr>
                        <td>
                            <div class="icheck-primary d-inline">
                                <input type="checkbox" id="cbxCantidadMachos" checked="">
                                <label for="cbxCantidadMachos"></label>
                            </div>
                        </td>
                        <td>CANT. MACHOS</td>
                        <td>
                            <span class="form-control form-control-sm">' . $row["gprMacho"] . '</span>  
                        </td>
                        <td>
                        <input type="text" class="form-control form-control-sm input_disablecopypaste" onKeyPress="f_OnlyCant(event)" maxlength="4" id="txtMacho_ac" placeholder="Solo números">
                        </td>
                    </tr> 
                </tbody>
            </table>
            <hr>
            <div class="row">
                <div class="col-6">
                <label >Autorizado por: (1)</label>
                <select id="slcUsuario1" class="form-control form-control-sm">' . select_data_usuario_soli($dbConn) . '</select>
                </div>
                <div class="col-6">
                    <label >Autorizado por: (2)</label>
                    <select id="slcUsuario2" class="form-control form-control-sm">' . select_data_usuario_soli($dbConn) . '</select>
                </div>
            </div>
            <hr>
            <textarea  id="txtRazon" class="form-control form-control-sm" placeholder="Razon de la modificación" maxlength="250" ></textarea>';
            $body = return_modal_body($body);
            return $header . $body . $footer;
        } else {
            return  return_modal_body('SE GENERO UNA ORDEN DE PRODUCCIÓN DE ESTE REGISTRO POR LO CUAL NO SE PUEDE MODIFICAR');
        }
    }
}
function f_get_data_soli_guia_proceso_delete($dbConn)
{
    $Id = $_POST["Id"];
    $consulta = "SELECT c.cliNombres,p.gprHembra,p.gprMacho,p.cliId
    FROM tbl_r_guiaproceso p, tbl_a_clientes c 
    WHERE p.cliId = c.cliId AND  gprId = :id";
    $sql = $dbConn->prepare($consulta);
    $sql->bindValue(':id', $Id);
    $sql->execute();
    if ($row = $sql->fetch()) {
        if (f_funcion_solicitud_comprobacion_1($dbConn, $Id) == false) {
            $header = return_modal_header('<h5 class="modal-title"><b>SOLICITUD DE ELIMINACIÓN</b></h5>');
            $footer = return_modal_foot('f_insert_solicitud_delete()');
            $body = '
            <h5 class="text-muted">COMPROBANTE A ELIMINAR: <b>2022B0001</b></h5>
            <input type="hidden" value="' . $Id . '" id="txtIdGuia">
            <div class="row">
                <div class="col-6">
                <label >Autorizado por: (1)</label>
                <select id="slcUsuario1" class="form-control form-control-sm">' . select_data_usuario_soli($dbConn) . '</select>
                </div>
                <div class="col-6">
                    <label >Autorizado por: (2)</label>
                    <select id="slcUsuario2" class="form-control form-control-sm">' . select_data_usuario_soli($dbConn) . '</select>
                </div>
            </div>
            <hr>
            <textarea  id="txtRazon" class="form-control form-control-sm" placeholder="Razon de la Eliminación" maxlength="250" ></textarea>';
            $body = return_modal_body($body);
            return $header . $body . $footer;
        } else {
            return  return_modal_body('SE GENERO UNA ORDEN DE PRODUCCIÓN DE ESTE REGISTRO POR LO CUAL NO SE PUEDE MODIFICAR');
        }
    }
}


function select_data_clientes_soli($dbConn, $Id)
{
    $resultado = '<option value="0" >Seleccione un cliente</option>';
    $consulta = "SELECT * FROM tbl_a_clientes WHERE cliEstado = 0 AND cliId != :id  ORDER BY cliNombres ASC ";
    $sql = $dbConn->prepare($consulta);
    $sql->bindValue(':id', $Id);
    $sql->execute();
    $cont = 0;
    while ($row = $sql->fetch()) {
        $cont++;
        $resultado .= '<option value="' . $row["cliId"] . '" >' . utf8_encode($row["cliNombres"]) . '</option>';
    }
    return  $resultado;
}
function select_data_usuario_soli($dbConn)
{
    $resultado = '<option value="0" >Seleccione un usuario</option>';
    $consulta = "SELECT * FROM tbl_a_usuarios WHERE usuId != :id AND  usuEstado = 1 ORDER BY usuNombre DESC";
    $sql = $dbConn->prepare($consulta);
    $sql->bindValue(':id', $_SESSION['MM_Username']);
    $sql->execute();
    $cont = 0;
    while ($row = $sql->fetch()) {
        $cont++;
        $resultado .= '<option value="' . $row["usuId"] . '" >' . utf8_encode($row["usuNombre"]) . '</option>';
    }
    return  $resultado;
}
function f_insert_solicitud($dbConn)
{
    $Id = $_POST["Id"];
    $cliente = trim($_POST["Cliente"]);
    $hembra = trim($_POST["Hembras"]);
    $macho = trim($_POST["Machos"]);
    $usuario1 = trim($_POST["Usuario1"]);
    $usuario2 = trim($_POST["Usuario2"]);
    $razon = trim($_POST["Razon"]);
    $m_cliente = '';
    $m_hembra = '';
    $m_macho = '';
    if ($cliente != '') $m_cliente = 'cliId = ' . $cliente;
    if ($hembra != '') $m_hembra = 'gprHembra = ' . $hembra;
    if ($macho != '') $m_macho = 'gprMacho = ' . $macho;

    if ($m_cliente != '') {
        if ($m_hembra != '') $m_cliente .= ',';
        else if ($m_hembra == '' && $m_macho != '') $m_cliente .= ',';
    }
    if ($m_hembra != '' && $m_macho != '') $m_hembra .= ',';
    if ($m_macho != '') $m_macho .= '';
    $array =  get_data_information($dbConn, $Id);
    $cabecera = 'EDICIÓN DE COMPROBANTE DE INGRESO: <b>' . $array[0] . '</b>';
    $detalle_cliente = '';
    $detalle_hembra = '';
    $detalle_macho = '';
    if ($cliente != '') $detalle_cliente = 'Cliente actual: ' . get_name_cliente($dbConn, $array[3]) . ' =>  Cliente nuevo: <u>' . get_name_cliente($dbConn, $cliente) . '</u><br>';
    if ($hembra != '') $detalle_hembra = 'Cantidad Hembra actual: ' . $array[1] . ' =>  Cantidad Hembra nueva: <u>' . $hembra . '</u><br>';
    if ($macho != '') $detalle_macho = 'Cantidad Macho actual: ' . $array[2] . ' =>  Cantidad Macho nueva: <u>' . $macho . '</u><br>';
    $cuerpo =  'N° DE COMPROBANTE: <b>' . $array[0] . '</b><br>' . $detalle_cliente . $detalle_hembra . $detalle_macho;
    $consulta = 'UPDATE tbl_r_guiaproceso SET ' . $m_cliente . $m_hembra . $m_macho . '  WHERE gprId = ' . $Id;
    return  insert_nueva_solicitud($dbConn, $cabecera, $cuerpo, $razon, $usuario1, $usuario2, $consulta, $Id, 1);
}
function f_insert_solicitud_delete($dbConn)
{
    $Id = $_POST["Id"];
    $usuario1 = $_POST["Usuario1"];
    $usuario2 = $_POST["Usuario2"];
    $razon = $_POST["Razon"];
    $array =  get_data_information($dbConn, $Id);
    $cabecera = 'ELIMINACIÓN DE COMPROBANTE DE INGRESO: <b>' . $array[0] . '</b>';
    $cuerpo =  'N° DEL COMPROBANTE ELIMINADO: <b>' . $array[0] . '</b><br>';
    $consulta = 'UPDATE tbl_r_guiaproceso SET  gprEliminado = 1 WHERE gprId = ' . $Id;
    return  insert_nueva_solicitud($dbConn, $cabecera, $cuerpo, $razon, $usuario1, $usuario2, $consulta, $Id, 1);
}
function get_data_information($dbConn, $Id)
{
    $consulta = "SELECT gprComprobante,gprHembra,gprMacho,cliId FROM tbl_r_guiaproceso WHERE gprId = :id";
    $sql = $dbConn->prepare($consulta);
    $sql->bindValue(':id', $Id);
    $sql->execute();
    if ($row = $sql->fetch()) return [$row['gprComprobante'], $row["gprHembra"], $row["gprMacho"], $row["cliId"]];
}
function get_name_cliente($dbConn, $Id)
{
    $consulta = "SELECT cliNombres FROM tbl_a_clientes WHERE cliId =  :id";
    $sql = $dbConn->prepare($consulta);
    $sql->bindValue(':id', $Id);
    $sql->execute();
    if ($row = $sql->fetch()) return utf8_encode($row['cliNombres']);
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



//// CORREO
function get_send_mail($dbConn)
{
    $Id = $_POST["Id"];
    $consulta = "SELECT c.cliNumero, c.cliNombres, c.cliCorreo, p.gprComprobante,p.gprId, (p.gprMacho + p.gprHembra) AS gprtotal FROM tbl_r_guiaproceso p, tbl_a_clientes c WHERE p.cliId = c.cliId AND p.gprId = :id";
    $sql = $dbConn->prepare($consulta);
    $sql->bindValue(':id', $Id);
    $sql->execute();
    if ($row = $sql->fetch()) {
        $data = '
            <h4 ><b>NOTIFICACIÓN DE INGRESO</b></h4>
            <input type="hidden" id="txtId-Proceso" value="' . $row["gprId"] . '"> 
            <h5 class="text-muted"><b>' . utf8_encode($row["cliNombres"]) . '</b> ' . $row["cliNumero"] . ' </h5>
            <hr>
            <label>Para: </label> <button class="btn btn-sm btn-light "  onclick="ayadir_input(\'#cont-para\')"><i class="fas fa-plus"></i></button>
            <div class="row pl-2 pr-2" id="cont-para">
                <input type="text" class="form-control form-control-sm mt-1 col-md-4 mr-2" value="' . utf8_encode($row["cliCorreo"]) . '" disabled="disabled"  placeholder="Correo Electronico">
            </div>
            <hr>
            <label>Copia: </label> <button class="btn btn-sm btn-light" onclick="ayadir_input(\'#cont-copia\')"><i class="fas fa-plus"></i></button>
            <div class="row pl-2 pr-2" id="cont-copia">
            </div>
            <hr>
            <label>Observación: </label> 
            <textarea id="txtObservacion-mail" class="form-control form-control-sm" row="3"></textarea>
            <br>
            <center>
                <button class="btn btn-info " onclick="f_send_mail()" id="btn-enviar-correo"><b>Enviar correo de notificación</b></button>
            </center>';
        return return_modal_body($data);
    } else return return_modal_body('ERROR-9192891');
}

function f_send_mail($dbConn)
{
    $Id = $_POST["Id"];
    $observaciones = '';
    $consulta = "SELECT * FROM tbl_r_guiamovilizacion m, tbl_r_guiaproceso p, tbl_a_clientes c, tbl_a_especies e WHERE p.guiId = m.guiId AND p.cliId = c.cliId AND p.espId = e.espId AND p.gprId =:id";
    $sql = $dbConn->prepare($consulta);
    $sql->bindValue(':id', $Id);
    $sql->execute();
    if ($row = $sql->fetch()) {
        include '../../FilePHP/send.php';
        $para = [];
        $cont = 0;
        if (isset($_POST["Para"])) {
            if (count($_POST["Para"]) > 0) {
                foreach ($_POST["Para"] as $value) {
                    $mail = explode("@", $value);
                    if (count($mail) == 2) {
                        $cont++;
                        if ($cont == 1) array_push($para, [$value, utf8_encode($row["cliNombres"])]);
                        else array_push($para, [$value, $mail[0]]);
                    }
                }
            }
        } else {
            $para = [[$row["cliCorreo"], utf8_encode($row["cliNombres"])]];
        }
        if (isset($_POST["Observacion"])) {
            $observaciones = $_POST["Observacion"];
        }

        $copia = [];
        if (isset($_POST["Copia"])) {
            if (count($_POST["Copia"]) > 0) {
                foreach ($_POST["Copia"] as $value) {
                    $mail = explode("@", $value);
                    if (count($mail) == 2) {
                        array_push($copia, [$value, $mail[0]]);
                    }
                }
            }
        }
        $existentes_turno =  f_calcular_existentes_and_turno($dbConn, $row["espId"], $row["gprId"]);
        $datos1 = [$existentes_turno[1], $existentes_turno[0], utf8_encode($row["espDescripcion"]), utf8_encode($row["cliNombres"])];
        $fecha = explode(" ", $row["gprTurno"]);
        $datos2 = [($row["gprMacho"] + $row["gprHembra"]), utf8_encode($row["gprComprobante"]), utf8_encode($row["guiNumero"]), $fecha[0], $fecha[1]];
        $datos3 = [$row["gprMacho"], $row["gprHembra"]];
        $datos4 = [utf8_encode($row["guiNombreConductor"]), utf8_encode($row["guiCiConductor"]), utf8_encode($row["guiVehiculoPlaca"])];
        $codigo = generar_codigo($row["cliNumero"]);
        $array_precios = get_precio_servicios($dbConn, $row["espId"]);
        $total_precios = 0;
        $precios = '';
        for ($i = 0; $i < count($array_precios); $i++) {
            $total_precios += $array_precios[$i][1];
            $precios .= '<tr>
                            <td style="text-aling:right;"> Precio por <b> ' . $array_precios[$i][0] . ':</b> </td>
                            <td style="text-aling:center;"> ' . $array_precios[$i][1] . ' $</td>
                        </tr>';
        }
        $precios .= '<tr style="">
                            <td style="text-aling:right;border-top:1px solid #C0C0C0;">  <b>Precio Unitario:</b> </td>
                            <td style="text-aling:center;border-top:1px solid #C0C0C0;"> ' . $total_precios . ' $</td>
                        </tr>
                        <tr>
                            <td colspan="2" style="font-size:20px;"><b>Total a pagar</b><br> 
                            <span style="background:yellow;font-size:30px;padding:10;border-radius:5px;">' . number_format(($total_precios * ($row["gprMacho"] + $row["gprHembra"])), 2, ',', ' ') . ' $</span></td>
                        </tr>';
        $bodyHTML = return_formato_notificacion_ingreso($datos1, $datos2, $datos3, $datos4, $observaciones, $codigo, $precios, $Id);

        $asunto = "Notificación de Ingreso " . date("d/m/Y");
        $titulo_envio = "RASTRO - Notificación de Recepción";
        return metEnviar($para, $copia, $bodyHTML, $asunto, $titulo_envio);
    } else return "Error 131321321";
}




function return_formato_notificacion_ingreso($datos1, $datos2, $datos3, $datos4, $observacion, $codigo, $precios, $id)
{
    $salvoconducto = "";
    $fecha_actual = date($datos2[3]);
    $nueva_fecha = date("Y-m-d", strtotime($fecha_actual . "+ 1 days"));
    $dia  = date("N", strtotime($fecha_actual . "+ 1 days"));
    $numero = $dia;
    if ($numero  == 1 || $numero  == 3 || $numero  == 5) {
        $salvoconducto  = return_salvo_Conducto($datos4[2], $datos4[0], $datos4[1], $nueva_fecha, $id);
    }
    return '
    <center><img src="http://www.epmrq.gob.ec/images/logows2.fw.png" style="display: block;"></center>
    <h3 style="text-align:center">EMPRESA PÚBLICA METROPOLITANA DE RASTRO QUITO</h3>
    ' . $salvoconducto . '
    <h3 style="text-align:center">NOTIFICACIÓN DE INGRESO DE GANADO</h3>
    <div style="text-align:center">
        <span style="font-size:15px;"><u>Existen <b>' . $datos1[0] . '</b> animales antes de empezar su turno</u></span><br><br>
        <span style="background:orange;font-size:30px;padding:10;border-radius:5px;"><b>TURNO #' . $datos1[1] . '</b></span><br><br><br>
        <span style="background:yellow;font-size:30px;padding:10;border-radius:5px;"><b>' . $datos1[2] . '</b></span><br><br>
        <span style="font-size:20px;padding:10;border-radius:5px;"><b>Cliente: ' . $datos1[3] . '</b></span><br><br>
    </div>
    <br>
    <br>
    <div style="border:1px solid #C0C0C0;padding:5px 10px;border-radius: 3px;">
        <table border="0" width="100%" style="border-collapse: collapse;">
            <tr >
                <th>Cantidad:</th>
                <td style="font-size:20px;"><b>' . $datos2[0] . '</b></td>
            </tr>
            <tr >
                <th>Comprobante de Ingreso:</th>
                <td>' . $datos2[1] . '</td>
            </tr>
            <tr >
                <th>Nro. Guía de Movilización:</th>
                <td>' . $datos2[2] . '</td>
            </tr>
            <tr>
                <th>Fecha de Ingreso:</th>
                <td>' . $datos2[3] . '</td>
            </tr>
            <tr>
                <th>Hora de Ingreso:</th>
                <td>' . $datos2[4] . '</td>
            </tr>
        </table>
    </div>
    <br>
    <div style="border:1px solid #C0C0C0;padding:5px 10px;border-radius: 3px;">
        <table border="0" width="100%" style="border-collapse: collapse;text-align:center">
            <tr>
                <td><b>Machos:</b> ' . $datos3[0] . '</td>
                <td><b>Hembras:</b> ' . $datos3[1] . '</td>
            </tr>
        </table>
    </div>
    <br>
    <div style="border:1px solid #C0C0C0;padding:5px 10px;border-radius: 3px;">
        <table border="0" width="100%" style="border-collapse: collapse;text-align:center">
            ' . $precios . '
        </table>
        <b>Datos bancarios para el depósito o transferencia:</b><br>
        RUC: 1768157280001 <br>
        Cta Banco Pichincha: 3478747604<br>
        Sublinea 30200
    </div>
    <br>
    <div style="border:1px solid #C0C0C0;padding:5px 10px;border-radius: 3px;">
        <table border="0" width="100%" style="border-collapse: collapse;text-align:center">
            <tr>
                <td colspan="2"><b>Conductor:</b> ' . $datos4[0] . '</td>
            </tr>
            <tr>
                <td><b>CI:</b> ' . $datos4[1] . '</td>
                <td><b>Placa:</b> ' . $datos4[2] . '</td>
            </tr>
        </table>
    </div>
    <br>
    <div style="font-size:12px;border:1px solid #C0C0C0;padding:5px 10px;border-radius: 3px;">
        <b>Observaciónes: </b> ' . $observacion . '
    </div>
    <p style="font-size:10px;">
        Código de seguridad: ' . $codigo . '
    </p>';
}

function get_precio_servicios($dbConn, $Id)
{
    $datos = [];
    $consulta = "SELECT srnDescripcion, srnPrecio FROM tbl_a_servicios s, tbl_a_especies e WHERE s.espId = e.espId AND e.espId = :id  AND srnEstado = 0";
    $sql = $dbConn->prepare($consulta);
    $sql->bindValue(':id', $Id);
    $sql->execute();
    while ($row = $sql->fetch()) array_push($datos, [utf8_encode($row["srnDescripcion"]), $row["srnPrecio"]]);
    return $datos;
}

function f_calcular_existentes_and_turno($dbConn, $especie, $id)
{
    $consulta = "SELECT p.gprId, g.guiNumero, p.gprComprobante ,c.cliNombres, e.espDescripcion,p.gprMacho,p.gprHembra, p.gprEstado, p.gprestadoDetalle 
    FROM tbl_r_guiaproceso p, tbl_r_guiamovilizacion g, tbl_a_especies e, tbl_a_clientes c 
    WHERE p.guiId = g.guiId AND p.espId = e.espId AND p.cliId = c.cliId AND e.espId = :id AND
    g.guiEliminado = 0  AND p.gprEliminado = 0 
    ORDER BY p.gprTurno , gprComprobante ASC ";
    $sql = $dbConn->prepare($consulta);
    $sql->bindValue(':id', $especie);
    $sql->execute();
    $cont = 1;
    $cantidad = 0;
    while ($row = $sql->fetch()) {
        if (f_obtener_procesar($dbConn, $row["gprId"]) == 0) {
            if ($row["gprEstado"] == 0 || $row["gprEstado"] == 1) {
                if ($row["gprId"] == $id) {
                    return [$cont, $cantidad];
                } else {
                    $cont++;
                    $cantidad = $cantidad + (intval($row["gprHembra"]) + intval($row["gprMacho"]));
                }
            }
        }
    }
}
function generar_codigo($cedula)
{
    $juliano = str_split(CalcularJuliano() . '');
    $arr1 = str_split($cedula . '');
    $total = 0;
    foreach ($arr1 as $value) {
        $total += intval($value);
    }
    foreach ($juliano as $value) {
        $total += intval($value);
    }
    return $total;
}

// -----------------------------------
function return_salvo_Conducto($placa, $conductor, $cedula, $fecha, $id)
{
    $img = createqr($id);
    return '
    <div style="border:1px solid #C0C0C0;padding:5px 10px;border-radius: 3px;">
        <table border="0" width="100%" style="border-collapse: collapse;">
            <tr>
                <td style="width:25%">
                <img src="http://www.epmrq.gob.ec/generateqr/index.php?IMAGEN=' . $img . '" width="100%"></td>
                <td style="width:75%">
                    <h4>Certificado de movilización para la circulación en ejercicio de actividades económicas que abastecen la cadena productiva de cárnicos</h4>
                    Este documento únicamente permite la circulación de las personas identificadas, 
                    en virtud del numeral 11 del artículo 7 del Decreto Ejecutivo N° 110 del 8 de enero 
                    del 2024, bajo prevención de la sanción prevista en el artículo 282 del COIP, 
                    sobre incumplimiento de decisiones legítimas de autoridad competente. 
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <br>
                    <b style="color:#167abf">Placa del vehículo:</b> <font style="color:#223f87">' . $placa . '</font><br>
                    <b style="color:#167abf">Nombre completo del conductor:</b> <font style="color:#223f87">' . $conductor . '</font><br>
                    <b style="color:#167abf">Cédula del conductor:</b> <font style="color:#223f87">' . $cedula . '</font><br>
                    <b style="color:#167abf">Periodo de vigencia:</b> <font style="color:#223f87"> desde las 00h00 hasta las 05h00 del ' . $fecha . '</font><br>
                    <b style="color:#167abf">Lugar de la actividad:</b> <font style="color:#223f87">Empresa Pública Metropolitana de Rastro Quito. </font><br>
                    <b style="color:#167abf">Dirección:</b> <font style="color:#223f87">Cdla. La Ecuatoriana, calle Camilo Orejuela y Secundaria, Quito - Ecuador.</font><br>
                </td>
            </tr> 
        </table>
    </div>
    <br>';
}
function createqr($id)
{
    //Agregamos la libreria para genera códigos QR
    require __DIR__ . "/../../FilePHP/phpqrcode/qrlib.php";

    //Declaramos una carpeta temporal para guardar la imagenes generadas
    $dir = __DIR__ . "/../../FilePHP/temp/";

    //Si no existe la carpeta la creamos
    if (!file_exists($dir))
        mkdir($dir);

    //Declaramos la ruta y nombre del archivo a generar
    $filename = $dir . 'qr-' . $id . '.png';

    //Parametros de Condiguración

    $tamaño = 10; //Tamaño de Pixel
    $level = 'L'; //Precisión Baja
    $framSize = 3; //Tamaño en blanco
    $contenido = "http://www.epmrq.gob.ec/documento_acreditacion/index.php?TOKEN=" . md5($id) . "&&QR=" . basename($filename); //Texto

    //Enviamos los parametros a la Función para generar código QR 
    QRcode::png($contenido, $filename, $level, $tamaño, $framSize);

    //Mostramos la imagen generada
    return  basename($filename);
}
