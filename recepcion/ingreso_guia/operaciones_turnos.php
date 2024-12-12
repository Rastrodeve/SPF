<?php
require_once '../../FilePHP/utils.php';
$cliente_encontrado = '';
if (isset($_REQUEST['op'])) {
    $dbConn = conectar($db);
    $dbConn2 =  $dbConn;
    $dbConn3 =  $dbConn2;
    $op = $_REQUEST['op'];
    if ($op == 1) echo buscar_guia($dbConn, $dbConn3, $_POST["Guia"]);
    // if ($op == 2) echo buscar_guia_datos($dbConn, $_POST["Guia"]);
    if ($op == 3) echo get_data_guiaproceso($dbConn, $dbConn2, $dbConn3, $_POST["Guia"]);
    if ($op == 4) echo select_corral($dbConn, $dbConn3);
    if ($op == 5) echo guardar_guia_completo($dbConn, $dbConn3);
}

function buscar_guia($dbConn, $dbConn2, $guia)
{
    $resultado = '<h3>No se encontró el Número de Guía ingresado</h3>';
    // $nueva_fecha = date("Y-m-d", strtotime(date("Y-m-d") . "- 1 days"));
    // $nueva_fecha = date("Y-m-d");
    $consulta = "SELECT * FROM tbl_r_guiamovilizacion WHERE guiNumero = :id AND guiTurno IS NOT NULL";
    $sql = $dbConn->prepare($consulta);
    // $sql->bindValue(':inicio', $nueva_fecha . ' 00:00:00');
    // $sql->bindValue(':final', $nueva_fecha . ' 23:59:59');
    $sql->bindValue(':id', $guia);
    $sql->execute();
    if ($row = $sql->fetch()) {
        $d_none = '';
        if ($row["guiTurno"] == 1) $d_none = 'disabled';
        $resultado = return_data_guia_complete($row, $d_none, $row["guiId"], $dbConn);
    }
    return $resultado;
}

function return_data_guia_complete($row, $d_none, $Id, $dbConn)
{
    $fecha1 = explode("-", $row["guiFechaInicio"]);
    $fecha2 = explode("-", $row["guiFechaInicio"]);
    $m_marcar = ["CAMION", "CAMIONETA", "AUTOMOVIL", "OTRO"];
    $num_car = 4;
    for ($i = 0; $i < count($m_marcar); $i++) {
        if (trim($row["guiVehiculo"]) == $m_marcar[$i]) {
            $num_car =  $i + 1;
        }
    }
    $vehiculo = '
    <select  id="slcVehiculo2" class="form-control form-control-sm select2bs4 select2-hidden-accessible d-none" style="width: 100%;" data-select2-id="slcVehiculo2" tabindex="-1" aria-hidden="true">
        <option value="1" >CAMION</option>
        <option value="2">CAMIONETA</option>
        <option value="3">AUTOMOVIL</option>
        <option value="4">OTRO</option>
    </select>';
    $nueva_guia_con = '
        <div class="row">
                <div class="col-md-12">
                    <label> ¿El número ingresado corresponde al número de predio ?</label>
                    <div class="custom-control custom-checkbox d-inline" >
                        <input class="custom-control-input"  type="checkbox" id="cbxPredio"   onclick="select_checkbox()"  style="cursor:pointer;" >
                        <label for="cbxPredio" class="custom-control-label" style="cursor:pointer;">SI</label>
                    </div>
                </div>
                <div class="col-md-12" id="cont-nuevo_numero_guia" style="display:none">
                    <label for="txt-nuevo_guia_numero">Ingrese el número de guia</label>
                    <input type="text" class="form-control text-sm-center text-md-left" id="txt-nuevo_guia_numero" data-np-intersection-state="visible">
                </div>
            </div>
            <hr>';
    $provincia = '
    <select  id="slcProvincia2" class="form-control form-control-sm select2bs4 select2-hidden-accessible d-none" style="width: 100%;margin-top:10px;" data-select2-id="slcProvincia" tabindex="-1" aria-hidden="true">
        <option value="1" >AZUAY</option>
        <option value="2">BOLÍVAR</option>
        <option value="3">CAÑAR</option>
        <option value="4">CARCHI</option>
        <option value="5">CHIMBORAZO</option>
        <option value="6">COTOPAXI</option>
        <option value="7">EL ORO</option>
        <option value="8">ESMERALDAS</option>
        <option value="10">GUAYAS</option>
        <option value="11">IMBABURA</option>
        <option value="12">LOJA</option>
        <option value="13">LOS RIOS</option>
        <option value="14">MANABÍ</option>
        <option value="15">MORONA SANTIAGO</option>
        <option value="16">NAPO</option>
        <option value="17">ORELLANA</option>
        <option value="18">PASTAZA</option>
        <option value="19">PICHINCHA</option>
        <option value="20">SANTA ELENA</option>
        <option value="21">SANTO DOMINGO DE LOS TSÁCHILAS</option>
        <option value="22">SUCUMBÍOS</option>
        <option value="23">TUNGURAHUA</option>
        <option value="24">ZAMORA CHINCHIPE</option>
    </select>';
    $script_d_none = '
    $("#slcProvincia2").val(' . $row["pId"] . ');
            $("#slcVehiculo2").val(' . $row["guiVehiculo"] . ');
            my_select_style_mobile("slcVehiculo2",0,"sm");
            my_select_style_mobile("slcProvincia2",0,"sm");';
    if ($d_none == "disabled") {
        $vehiculo = '<input ' . $d_none . ' type="text" value="' . $row["guiVehiculo"] . '" class="form-control form-control-sm " id="slcVehiculo2" >';
        $provincia = '<input ' . $d_none . ' type="text" value="' . search_provincia($dbConn, $row["pId"]) . '" class="form-control form-control-sm " id="slcProvincia2" >';
        $script_d_none = '';
        $nueva_guia_con = "";
    }
    return '
        <div id="cont-result" style="">
            <input type="hidden" id="txtGuia-Ingreso" value="' . $Id . '" >
            ' . $nueva_guia_con . '
            <div class="row ">
                <div class="col-12 col-lg-4 mt-2">
                    <label for="txtFechaGuia2">* Fecha guía:</label>
                    <div class="input-group" id="reservationdate3" data-target-input="nearest">
                        <input ' . $d_none . ' type="text" placeholder="dd/mm/yyyy" id="txtFechaGuia2" class="form-control form-control-sm datetimepicker-input" data-target="#reservationdate3" data-inputmask-alias="datetime" data-inputmask-inputformat="dd/mm/yyyy" data-mask="" im-insert="false" value="' . $fecha1[2] . '/' . $fecha1[1] . '/' . $fecha1[0] . '">
                        <div class="input-group-append" data-target="#reservationdate3" data-toggle="datetimepicker">
                            <div class="input-group-text bg-success"><i class="fa fa-calendar"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-4 mt-2">
                    <label for="txtFechaValidez2">* Fecha Validez:</label>
                    <div class="input-group " id="reservationdate4" data-target-input="nearest">
                        <input ' . $d_none . ' type="text" placeholder="dd/mm/yyyy" id="txtFechaValidez2" class="form-control form-control-sm datetimepicker-input" data-target="#reservationdate4" data-inputmask-alias="datetime" data-inputmask-inputformat="dd/mm/yyyy" data-mask="" im-insert="false" value="' . $fecha2[2] . '/' . $fecha2[1] . '/' . $fecha2[0] . '">
                        <div class="input-group-append " data-target="#reservationdate4" data-toggle="datetimepicker">
                            <div class="input-group-text bg-danger"><i class="fa fa-calendar"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-4 mt-2">
                    <label for="slcProvincia2">* Provincia origen:</label>
                    ' . $provincia . '
                </div>
                <div class="col-12 col-lg-4 mt-2">
                    <label for="txtVacunacion2">* Código vacunación:</label>
                    <input ' . $d_none . ' type="text" class="form-control form-control-sm" value="' . $row["guiVacunacion"] . '" id="txtVacunacion2" placeholder="00000">
                </div>
            </div>
            <hr>
            <div class="row ">
                <div class="col-12 col-lg-4 mt-2">
                    <label for="txtCI2">* C.I. Conductor:</label> <span class="text-muted ml-2">(max 10)</span>
                    <input ' . $d_none . ' type="text" class="form-control form-control-sm " id="txtCI2" placeholder="1234567890" maxlength="10" onkeypress="f_OnlyCant(event)" value="' . $row["guiCiConductor"] . '">
                </div>
                <div class="col-12 col-lg-4 mt-2">
                    <label for="txtConductor2">* Nombre Conductor:</label> <span class="text-muted ml-2">(max 50)</span>
                    <input ' . $d_none . ' type="text" class="form-control form-control-sm " id="txtConductor2" placeholder="" maxlength="50" value="' . $row["guiNombreConductor"] . '">
                </div>
                <div class="col-12 col-lg-4 mt-2" sty="">
                    <label for="slcVehiculo2">* Vehiculo:</label>
                    ' . $vehiculo . '
                </div>
                <div class="col-12 col-lg-4 mt-2">
                    <label for="txtPlaca2">* Placa Vehiculo:</label> <span class="text-muted ml-2">(max 8)</span>
                    <input ' . $d_none . ' type="text" class="form-control form-control-sm " id="txtPlaca2" placeholder="Placa del vehiculo" maxlength="8" value="' . $row["guiVehiculoPlaca"] . '">
                </div>
            </div>
            <hr>
            <center><button class="btn btn-info mt-2" onclick="continuar_f()"> <b>CONTINUAR</b> </button></center>
            <script>
            $("#reservationdate3").datetimepicker({
                format: "DD/MM/Y"
            });
            $("#reservationdate4").datetimepicker({
                format: "DD/MM/Y"
            });
            $("#txtFechaGuia2").inputmask("dd/mm/yyyy", {
                "placeholder": "dd/mm/yyyy"
            });
            $("#txtFechaValidez2").inputmask("dd/mm/yyyy", {
                "placeholder": "dd/mm/yyyy"
            });
            ' . $script_d_none . '
            </script>
        </div>';
}


function get_data_guia_g($dbConn, $dbConn2, $id)
{
    $consulta = "SELECT * FROM tbl_r_guiamovilizacion WHERE guiId = :id";
    $sql = $dbConn->prepare($consulta);
    $sql->bindValue(':id', $id);
    $sql->execute();
    if ($row = $sql->fetch()) {
        if ($row["guiTurno"] == 0) {
            $data = $_POST["Data"];
            $guia_nueva = "";
            if (isset($data[8])) {
                $guia_nueva = $data[8];
                if ($guia_nueva == "") exit("ERROR-55444:  Pongase en contacto con el administrador");
            }
            $Nuevo_Id = insert_guia($dbConn2, $row, $data[0], $data[1], $data[3], $data[4], $data[5], $data[6], $data[7], $data[2], $guia_nueva);
            return $Nuevo_Id;
        } else {
            $consulta2 = "SELECT * FROM tbl_r_guiamovilizacion WHERE guiId = :id";
            $sql2 = $dbConn2->prepare($consulta2);
            $sql2->bindValue(':id', $row["guiId"]);
            $sql2->execute();
            if ($row2 = $sql2->fetch()) return $row2["guiId"];
            else return 0;
        }
    }
}


function insert_guia($dbConn, $row,  $fecha_guia, $fecha_validez, $code_vacu, $cedula_con, $conductor, $vehiculo, $placa, $provincia, $guia_nueva)
{
    $predio_Consulta = "";
    if ($guia_nueva != "") {
        $predio_Consulta = " , guiNumero = :guiNumero,guiNumeroPredio = :guiNumeroPredio ";
    }
    $m_marcar = ["CAMION", "CAMIONETA", "AUTOMOVIL", "OTRO"];
    global $User;
    global $Ip;
    $consulta = "UPDATE tbl_r_guiamovilizacion SET 
    guiFechaInicio = :guiFechaInicio,guiFechaFinal = :guiFechaFinal,guiVacunacion = :guiVacunacion,guiCiConductor = :guiCiConductor,guiNombreConductor= :guiNombreConductor,guiVehiculo= :guiVehiculo,guiVehiculoPlaca= :guiVehiculoPlaca,guiObservacion= :guiObservacion,guiTurno= :guiTurno,pId= :pId,usuId= :usuId,ip= :ip
    " . $predio_Consulta . " where guiId = :guiId";
    $sql = $dbConn->prepare($consulta);
    $sql->bindValue(':guiFechaInicio', transformar_fecha($fecha_guia));
    $sql->bindValue(':guiFechaFinal', transformar_fecha($fecha_validez));
    $sql->bindValue(':guiVacunacion', $code_vacu);
    $sql->bindValue(':guiCiConductor', $cedula_con);
    $sql->bindValue(':guiNombreConductor', utf8_decode($conductor));
    $sql->bindValue(':guiVehiculo', utf8_decode($m_marcar[intval($vehiculo) - 1]));
    $sql->bindValue(':guiVehiculoPlaca', utf8_decode($placa));
    $sql->bindValue(':guiObservacion', 'Turno Online');
    $sql->bindValue(':guiTurno', 1);
    $sql->bindValue(':pId', $provincia);
    $sql->bindValue(':usuId', $User);
    $sql->bindValue(':ip', $Ip);
    if ($guia_nueva != "") {
        $sql->bindValue(':guiNumero', utf8_decode($guia_nueva));
        $sql->bindValue(':guiNumeroPredio', $row["guiNumero"]);
    }
    $sql->bindValue(':guiId', $row["guiId"]);
    if ($sql->execute()) {
        return $row["guiId"];
    }
    return 0;
}


function get_data_guiaproceso($dbConn, $dbConn2, $dbConn3, $id)
{
    $Id_n = get_data_guia_g($dbConn,  $dbConn3, $id);
    if ($Id_n < 1) {
        return '<h5 class="error">Pongase en contacto con el administrador</h5> ';
    }
    $resultado = '<input type="hidden" value="' . $Id_n . '" id="txt_input_guia_nueva"> <table id="tbl_data_table_procesos" class="table table-bordered table-striped table-sm ">
            <thead>
                <th>Turno</th>
                <th>Cliente</th>
                <th>Ganado</th>
                <th>Hembra</th>
                <th>Macho</th>
                <th>Valor</th>
                <th>Habilitar</th>
            </thead>
        <tbody>';
    // $nueva_fecha = date("Y-m-d");
    // $nueva_fecha = date("Y-m-d", strtotime(date("Y-m-d") . "- 1 days"));
    $consulta = "SELECT * FROM tbl_r_guiaproceso WHERE guiId = :id ORDER BY gprId ASC";
    $sql = $dbConn->prepare($consulta);
    $sql->bindValue(':id', $id);
    $sql->execute();
    while ($row = $sql->fetch()) {
        $habilitar = '<span class="bg-success"><b>Habilitado</b></span>';
        if ($row["gprHabilitado"] == 0) {
            $habilitar = '<button class="btn btn-info btn-sm" onclick="corral_data(' . $row["gprId"] . ')">HABILITAR TURNO</button>';
        }
        $pago = '';
        if ($row["gprEstadoPagado"] == 0) {
            $pago = 'table-danger';
        } else {
            $pago = 'table-success';
        }
        $resultado .= '
        <tr>
            <td>' . return_turno_lista($dbConn, $row["gprId"]) . '</td>
            <td>' . get_data_cliente($dbConn2, $row["cliId"])  . '</td>
            <td>' . get_data_especie($dbConn2, $row["espId"]) . '</td>
            <td>' . $row["gprHembra"] . '</td>
            <td>' . $row["gprMacho"] . '</td>
            <td class="' . $pago . '">' . round((get_servicios($dbConn2, $row["espId"]) * ($row["gprHembra"] + $row["gprMacho"])), 2) . '</td>
            <td> ' . $habilitar . ' </td>
        </tr>';
    }
    return $resultado . '</tbody></table>';
}
function get_estado_turno($dbConn, $Id)
{
    $consulta = "SELECT pagado FROM tbl_r_turno WHERE gprId = :id ";
    $sql = $dbConn->prepare($consulta);
    $sql->bindValue(':id', $Id);
    $sql->execute();
    if ($row = $sql->fetch()) return $row["pagado"];
    return 0;
}
function search_turno($dbConn, $Id)
{
    $consulta = "SELECT * FROM tbl_r_guiaproceso WHERE gprId = :id";
    $sql = $dbConn->prepare($consulta);
    $sql->bindValue(':id', $Id);
    $sql->execute();
    if ($row = $sql->fetch()) return true;
    else return false;
}

function search_provincia($dbConn, $Id)
{

    $consulta = "SELECT * FROM tbl_provincias WHERE pId = :id";
    $sql = $dbConn->prepare($consulta);
    $sql->bindValue(':id', $Id);
    $sql->execute();
    if ($row = $sql->fetch()) return $row["pProvincia"];
    else return "SIN PROVINCIA";
}


function get_turno($dbConn, $Id)
{
    $cont = 1;
    // $nueva_fecha = date("Y-m-d");
    $nueva_fecha = date("Y-m-d", strtotime(date("Y-m-d") . "- 1 days"));
    $consulta = "SELECT gprId FROM tbl_r_guiaproceso WHERE gprTurno BETWEEN :inicio AND :final AND gprEstado != 2 ORDER BY gprId ASC";
    $sql = $dbConn->prepare($consulta);
    $sql->bindValue(':inicio', $nueva_fecha . ' 00:00:00');
    $sql->bindValue(':final', $nueva_fecha . ' 23:59:59');
    $sql->execute();
    $data = '';
    while ($row = $sql->fetch()) {
        if ($row["gprId"] == $Id) {
            return $cont;
        } else {
            $cont++;
        }
    }
    return $cont;
}
function get_servicios($dbConn, $Id)
{
    $cont = 0;
    $consulta = "SELECT SUM(srnPrecio) as total FROM tbl_a_servicios WHERE espId = :id AND srnEstado = 0";
    $sql = $dbConn->prepare($consulta);
    $sql->bindValue(':id', $Id);
    $sql->execute();
    if ($row = $sql->fetch()) return $row["total"];
    else return 0;
}

function get_data_cliente($dbConn, $id)
{
    $cliente = '';
    $consulta = "SELECT cliNombres FROM tbl_a_clientes WHERE cliId = :id";
    $sql = $dbConn->prepare($consulta);
    $sql->bindValue(':id', $id);
    $sql->execute();
    if ($row = $sql->fetch()) $cliente = utf8_encode($row["cliNombres"]);
    return $cliente;
}
function get_data_especie($dbConn, $id)
{
    $especie = '';
    $consulta = "SELECT espDescripcion FROM tbl_a_especies WHERE espId = :id";
    $sql = $dbConn->prepare($consulta);
    $sql->bindValue(':id', $id);
    $sql->execute();
    if ($row = $sql->fetch()) $especie = utf8_encode($row["espDescripcion"]);
    return $especie;
}


function select_corral($dbConn, $dbConn2)
{
    $proceso = $_POST["Guia"];
    $Id2 = $_POST["GuiaSave"];
    // $Corral = $_POST["Corral"];

    $consulta = "SELECT * FROM tbl_r_guiaproceso WHERE gprId = :id ";
    $sql = $dbConn->prepare($consulta);
    $sql->bindValue(':id', $_POST["Guia"]);
    $sql->execute();
    $Id_proceso = 0;
    $CANT  = 0;
    if ($row = $sql->fetch()) {
        $CANT = $row["gprHembra"] + $row["gprMacho"];
        $Id_proceso = guardar_guia_proceso($dbConn2, $proceso);
        update_estado_turno($dbConn2,  $proceso);
    }
    return true;
    return '
    <input type="hidden" value="' . $Id_proceso . '"  id="txtInputGuiaProcesoCorral">
    <input type="hidden" value="' . $CANT . '"  id="slcCorralNuevoCantidad">
    <h5>Seleccione el corral</h5>
    <select class="form-control form-control-sm" id="slcCorralNuevo">
    ' . Lista_corrales($dbConn2, 0) . '
    </select>
    <br>
    <button class="btn btn-info" onclick="f_guardar_turno()" ><b>GUARDAR CORRAL</b></button>';
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




function guardar_guia_proceso($dbConn,  $proceso)
{

    global $User;
    global $Ip;
    $consulta = "UPDATE tbl_r_guiaproceso set gprHabilitado = :gprHabilitado, gprEstado = :gprEstado, usuId =:usuId, ip =:ip where gprId = :id";
    $sql = $dbConn->prepare($consulta);
    $sql->bindValue(':gprHabilitado', 1);
    $sql->bindValue(':gprEstado', 1);
    $sql->bindValue(':usuId', $User);
    $sql->bindValue(':ip', $Ip);
    $sql->bindValue(':id', $proceso);
    if ($sql->execute()) return $proceso;
    else return 0;
}
function update_estado_turno($dbConn,  $proceso)
{
    $consulta = "UPDATE tbl_r_turno set tstId = :numero where gprId = :id";
    $sql = $dbConn->prepare($consulta);
    $sql->bindValue(':numero', 3);
    $sql->bindValue(':id', $proceso);
    if ($sql->execute()) return true;
    else return false;
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
        return $anio . $letra . crearNumero(1);
    } else {
        return "ERROR-81928"; //La especie no tiene una letra asignada
    }
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


function guardar_guia_completo($dbConn2, $dbConn)
{
    try {
        global $User;
        global $Ip;
        $Id = $_POST["Id"];
        $cantidad = $_POST["Cantidad"];
        $corral = trim($_POST["Corral"]);
        $consulta = "INSERT INTO tbl_r_lugar(lgrCantidad,lgrFecha,crrId,gprId,usuId,ip) 
        VALUES (:lgrCantidad,:lgrFecha,:crrId,:gprId,:usuId,:ip)";
        $sql = $dbConn->prepare($consulta);
        $sql->bindValue(':lgrCantidad', $cantidad);
        $sql->bindValue(':lgrFecha', date("Y-m-d H:i:s"));
        $sql->bindValue(':crrId', $corral);
        $sql->bindValue(':gprId', $Id);
        $sql->bindValue(':usuId', $User);
        $sql->bindValue(':ip', $Ip);
        if ($sql->execute()) {
            return true;
        } else return "ERROR-667242"; //
    } catch (Exception $e) {
        exit("ERROR-887332 " . $e->getMessage());
    }
}



// function buscar_guia($dbConn, $guia)
// {
//     $resultado = '<table id="tbl_data_table" class="table table-bordered table-striped table-sm ">
//             <thead>
//                 <th>Turno</th>
//                 <th>Guía</th>
//                 <th>Cliente</th>
//                 <th>Ganado</th>
//                 <th>Total</th>
//                 <th>Valor</th>
//                 <th>Seleccionar</th>
//             </thead>
//     <tbody>';
//     $nueva_fecha = date("Y-m-d", strtotime(date("Y-m-d") . "- 1 days"));
//     $consulta = "SELECT * FROM vst_r_turno 
//     WHERE fecha BETWEEN :inicio AND :final  AND tstId !=4 AND  tstId !=5 AND guiNumero = :id";
//     $sql = $dbConn->prepare($consulta);
//     $sql->bindValue(':inicio', $nueva_fecha . ' 00:00:00');
//     $sql->bindValue(':final', $nueva_fecha . ' 23:59:59');
//     $sql->bindValue(':id', $guia);
//     $sql->execute();
//     while ($row = $sql->fetch()) {
//         $resultado .= '
//         <tr>
//             <td>' . $row["numero"] . '</td>
//             <td>' . utf8_encode($row["guiNumero"]) . '</td>
//             <td>' . utf8_encode($row["cliNombres"]) . '</td>
//             <td>' . $row["especie"] . '</td>
//             <td>' . $row["total"] . '</td>
//             <td>' . $row["precio"] . '</td>
//             <td> <button class="btn btn-info btn-sm" onclick="select_data_guia(\' ' . $row["guiId"] . ' \',' . $row["numero"] . ' ,\'' . utf8_encode($row["cliNombres"]) . '\')"><b>CONTINUAR</b></button> </td>
//         </tr>';
//     }
//     return $resultado . '</tbody></table>';
// }
function buscar_guia_datos($dbConn, $guia)
{
    $resultado = '';
    $consulta = "SELECT * FROM tbl_r_guiamovilizacion WHERE guiId = :id";
    $sql = $dbConn->prepare($consulta);
    $sql->bindValue(':id', $guia);
    $sql->execute();
    if ($row = $sql->fetch()) {
        $fecha1 = explode("-", $row["guiFechaInicio"]);
        $fecha2 = explode("-", $row["guiFechaInicio"]);
        $resultado .= '
        <div id="cont-result" style="">
            <h5>Turno: ' . $_POST["Turno"] . '</h5>
            <h5>' . $_POST["Cliente"] . '</h5>
            <div class="row ">
                <div class="col-12 col-lg-4 mt-2">
                    <label for="txtFechaGuia">* Fecha guía:</label>
                    <div class="input-group" id="reservationdate3" data-target-input="nearest">
                        <input type="text" placeholder="dd/mm/yyyy" id="txtFechaGuia2" class="form-control form-control-sm datetimepicker-input" data-target="#reservationdate3" data-inputmask-alias="datetime" data-inputmask-inputformat="dd/mm/yyyy" data-mask="" im-insert="false" value="' . $fecha1[2] . '/' . $fecha1[1] . '/' . $fecha1[0] . '">
                        <div class="input-group-append" data-target="#reservationdate3" data-toggle="datetimepicker">
                            <div class="input-group-text bg-success"><i class="fa fa-calendar"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-4 mt-2">
                    <label for="txtFechaValidez">* Fecha Validez:</label>
                    <div class="input-group " id="reservationdate4" data-target-input="nearest">
                        <input type="text" placeholder="dd/mm/yyyy" id="txtFechaValidez2" class="form-control form-control-sm datetimepicker-input" data-target="#reservationdate4" data-inputmask-alias="datetime" data-inputmask-inputformat="dd/mm/yyyy" data-mask="" im-insert="false" value="' . $fecha2[2] . '/' . $fecha2[1] . '/' . $fecha2[0] . '">
                        <div class="input-group-append " data-target="#reservationdate4" data-toggle="datetimepicker">
                            <div class="input-group-text bg-danger"><i class="fa fa-calendar"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-lg-4 mt-2">
                    <label for="slcProvincia2">* Provincia origen:</label>
                    <select id="slcProvincia2" class="form-control form-control-sm select2bs4 select2-hidden-accessible d-none" style="width: 100%;margin-top:10px;" data-select2-id="slcProvincia" tabindex="-1" aria-hidden="true">
                        <option value="1" >AZUAY</option>
                        <option value="2">BOLÍVAR</option>
                        <option value="3">CAÑAR</option>
                        <option value="4">CARCHI</option>
                        <option value="5">CHIMBORAZO</option>
                        <option value="6">COTOPAXI</option>
                        <option value="7">EL ORO</option>
                        <option value="8">ESMERALDAS</option>
                        <option value="9">GALÁPAGOS</option>
                        <option value="10">GUAYAS</option>
                        <option value="11">IMBABURA</option>
                        <option value="12">LOJA</option>
                        <option value="13">LOS RIOS</option>
                        <option value="14">MANABÍ</option>
                        <option value="15">MORONA SANTIAGO</option>
                        <option value="16">NAPO</option>
                        <option value="17">ORELLANA</option>
                        <option value="18">PASTAZA</option>
                        <option value="19">PICHINCHA</option>
                        <option value="20">SANTA ELENA</option>
                        <option value="21">SANTO DOMINGO DE LOS TSÁCHILAS</option>
                        <option value="22">SUCUMBÍOS</option>
                        <option value="23">TUNGURAHUA</option>
                        <option value="24">ZAMORA CHINCHIPE</option>
                        <option value="25">ZONAS NO LIMITADAS</option>
                    </select>
                </div>
                <div class="col-12 col-lg-4 mt-2">
                    <label for="txtVacunacion2">* Código vacunación:</label>
                    <input type="text" class="form-control form-control-sm" value="' . $row["guiVacunacion"] . '" id="txtVacunacion2" placeholder="00000">
                </div>
            </div>
            <hr>
            <div class="row ">
                <div class="col-12 col-lg-4 mt-2">
                    <label for="txtCI2">* C.I. Conductor:</label> <span class="text-muted ml-2">(max 10)</span>
                    <input type="text" class="form-control form-control-sm " id="txtCI2" placeholder="' . $row["guiCiConductor"] . '" maxlength="10" onkeypress="f_OnlyCant(event)" value="1234567890">
                </div>
                <div class="col-12 col-lg-4 mt-2">
                    <label for="txtConductor2">* Nombre Conductor:</label> <span class="text-muted ml-2">(max 50)</span>
                    <input type="text" class="form-control form-control-sm " id="txtConductor2" placeholder="' . $row["guiNombreConductor"] . '" maxlength="50" value="PRUEBA">
                </div>
                <div class="col-12 col-lg-4 mt-2" sty="">
                    <label for="slcVehiculo2">* Vehiculo:</label>
                    <select id="slcVehiculo2" class="form-control form-control-sm select2bs4 select2-hidden-accessible d-none" style="width: 100%;" data-select2-id="slcVehiculo2" tabindex="-1" aria-hidden="true">
                        <option value="CAMION" >CAMION</option>
                        <option value="CAMIONETA">CAMIONETA</option>
                        <option value="AUTOMOVIL">AUTOMOVIL</option>
                        <option value="OTRO">OTRO</option>
                    </select>
                </div>
                <div class="col-12 col-lg-4 mt-2">
                    <label for="txtPlaca2">* Placa Vehiculo:</label> <span class="text-muted ml-2">(max 8)</span>
                    <input type="text" class="form-control form-control-sm " id="txtPlaca2" placeholder="Placa del vehiculo" maxlength="8" value="' . $row["guiVehiculoPlaca"] . '">
                </div>
            </div>
            <hr>
            <div class="row">
                <div class="col-md-6 mt-2">
                    <div class="input-group" id="reservationdate" data-target-input="nearest">
                        <input type="text" onkeypress="f_OnlyCant(event)" maxlength="4" class="form-control input_disablecopypaste" id="txtHembra" placeholder="Cantidad de ganado" value="1">
                        <div class="input-group-append">
                            <label class="input-group-text " for="txtHembra">
                                HEMBRAS
                            </label>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mt-2">
                    <div class="input-group" id="reservationdate" data-target-input="nearest">
                        <input type="text" onkeypress="f_OnlyCant(event)" maxlength="4" class="form-control input_disablecopypaste" id="txtMacho" placeholder="Cantidad de ganado" value="0">
                        <div class="input-group-append">
                            <label class="input-group-text " for="txtMacho">
                                MACHOS
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            <center><button class="btn btn-info mt-2"> <b>GUARDAR TURNO</b> </button></center>
            <script>
            $("#reservationdate3").datetimepicker({
                format: "DD/MM/Y"
            });
            $("#reservationdate4").datetimepicker({
                format: "DD/MM/Y"
            });
            $("#txtFechaGuia2").inputmask("dd/mm/yyyy", {
                "placeholder": "dd/mm/yyyy"
            });
            $("#txtFechaValidez2").inputmask("dd/mm/yyyy", {
                "placeholder": "dd/mm/yyyy"
            });

            $("#slcProvincia2").val(' . $row["pId"] . ');
            $("#slcVehiculo2").val("' . $row["guiVehiculo"] . '");
            my_select_style_mobile("slcVehiculo2",0,"sm");
            my_select_style_mobile("slcProvincia2",0,"sm");
            </script>
        </div>';
    }
    return $resultado . '</tbody></table>';
}

function transformar_fecha($fecha)
{
    $array = explode("/", $fecha);
    return $array[2] . "-" . $array[1] . "-" . $array[0];
}


function return_turno_lista($dbConn, $Id = 0)
{

    $fecha_actual = date("Y-m-d");
    $nueva_fecha = date("Y-m-d", strtotime($fecha_actual . "+ 1 days"));
    $contador = 0;
    $consulta_id = '';
    if ($Id != 0) {
        $consulta_id = "AND gprId = :id";
    }
    $consulta = "SELECT numero FROM tbl_r_turno WHERE fecha = :fecha " . $consulta_id;
    $sql = $dbConn->prepare($consulta);
    $sql->bindValue(':fecha', $nueva_fecha);
    if ($Id != 0) {
        $sql->bindValue(':id', $Id);
    }
    $sql->execute();
    while ($row = $sql->fetch()) {
        $contador = $row["numero"];
    }
    return  $contador;
}
