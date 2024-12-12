<?php
require '../../FilePHP/utils.php';
if (isset($_REQUEST['op'])) {
	$dbConn = conectar($db);
	$op=$_REQUEST['op'];
	if ($op==1)echo select_data_especies($dbConn);
	elseif ($op==2)echo return_cont_card($dbConn);
	elseif ($op==3)echo return_data_orden($_POST["Id"]);
	elseif ($op==4)echo get_data_table_orde($dbConn,$_POST["Id"]);
	elseif ($op==5)echo get_data_factura($dbConn);
	elseif ($op==6)echo f_turno_orden($dbConn);
	elseif ($op==7)echo get_data_otras($dbConn);
	elseif ($op==8)echo get_data_trunos_actual($dbConn,$_POST["Id"]);
	elseif ($op==9)echo get_data_table_orde_emergente($dbConn,$_POST["Id"]);
    elseif ($op==10)echo return_data_orden_emergente($_POST["Id"]);
    elseif ($op==11)echo get_data_trunos_urgencias($dbConn,$_POST["Id"]);

}else{
	header('location: ../../');
}
function Transformar_Fecha($fecha){
    $arrayDia = array('1' => 'Lunes',
                    '2' => 'Martes',
                    '3' => 'Miércoles',
                    '4' => 'Jueves',
                    '5' => 'Viernes',
                    '6' => 'Sábado',
                    '7' => 'Domingo');
    $numDia=date("N",strtotime($fecha));
    $arrayMes = array('1' => 'Enero',
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
                    '12' => 'Diciembre',);
    $numMes=date("n",strtotime($fecha));
    return $arrayDia[$numDia].", ".date("d",strtotime($fecha))." de ".$arrayMes[$numMes]." de ".date("Y",strtotime($fecha));
}
function select_data_especies($dbConn){
    $resultado='';
    $cont = 0;
    $consulta="SELECT * FROM tbl_a_especies";
	$sql= $dbConn->prepare($consulta);
	$sql->execute();
	while($row = $sql->fetch()) {
        $cont++;
        $resultado.='<option value="'.$row["espId"].'" >'.utf8_encode($row["espDescripcion"]).'</option>';
	}
    if ($cont > 0)return  $resultado;
    else return '<option value="0" selected="" >No se encontraron especies</option>';
}

function get_data_orden($dbConn,$Id){
    $ArrayOrden = array();
    $consulta="SELECT DISTINCT ordNumOrden FROM tbl_p_orden 
    WHERE gprId is not null AND espId = :id AND ordTipo = 0 
    AND ordFecha BETWEEN :inicio AND :fin ORDER BY ordId ASC";
	$sql= $dbConn->prepare($consulta);
    $sql->bindValue(':id',$Id);
    $sql->bindValue(':inicio',date("Y-m-d")." 00:00:00");
    $sql->bindValue(':fin',date("Y-m-d")." 23:59:59");
	$sql->execute();
	while ($row = $sql->fetch()) {
		array_push($ArrayOrden,$row["ordNumOrden"]);
	}
    $select = "";
    for ($i=0; $i < count($ArrayOrden) ; $i++) { 
        $select.='<option value="'.$ArrayOrden[$i].'" >'.$ArrayOrden[$i].'</option>';
    }
    $info= "";
    $table = ""; 
    if (count($ArrayOrden) == 0) {
        $select = '<option value="0" selected="" >No se encontraron datos</option>';
        $data = "No se encontraron resultados";
    }else{
        $info = '<div class="col-md-9" id="cont-info-actual">'.return_data_orden($ArrayOrden[0]).'</div>';
        $table = get_data_table_orde($dbConn,$ArrayOrden[0]);
    }
    return '
    <div class="row">
        '.$info.'
        <div class="col-md-3">
            <select class="form-control form-control-sm float-right" onchange="get_data_orden()"
            id="slcDataOrden" style="width:100%;cursor:pointer;">'.$select.'</select>
        </div> 
    </div>
    <hr>
    <button class="btn btn-info btn-sm float-right" onclick="get_data_orden_table()" ><i class="fas fa-spinner" ></i></button>
    <div id="cont-table-actual">
    '.$table.'
    </div>';
}
function get_data_orden_emergente($dbConn,$Id){
    $ArrayOrden = array();
    $consulta="SELECT DISTINCT ordNumOrden FROM tbl_p_orden 
    WHERE gprId is not null AND espId = :id AND ordTipo = 1
    AND ordFecha BETWEEN :inicio AND :fin ORDER BY ordId ASC";
	$sql= $dbConn->prepare($consulta);
    $sql->bindValue(':id',$Id);
    $sql->bindValue(':inicio',date("Y-m-d")." 00:00:00");
    $sql->bindValue(':fin',date("Y-m-d")." 23:59:59");
	$sql->execute();
	while ($row = $sql->fetch()) {
		array_push($ArrayOrden,$row["ordNumOrden"]);
	}
    $select = '';
    for ($i=0; $i < count($ArrayOrden) ; $i++) { 
        $select.='<option value="'.$ArrayOrden[$i].'" >'.$ArrayOrden[$i].'</option>';
    }
    $info= "";
    $table = ""; 
    if (count($ArrayOrden) == 0) {
        $select = '<option value="0" selected="" >No se encontraron datos</option>';
        $data = "No se encontraron resultados";
    }else{
        $info = '<div class="col-md-9" id="cont-info-actual">'.return_data_orden_emergente($ArrayOrden[0]).'</div>';
        $table = get_data_table_orde_emergente($dbConn,$ArrayOrden[0]);
    }
    return '
    <div class="row">
        '.$info.'
        <div class="col-md-3">
            <select class="form-control form-control-sm float-right" onchange="get_data_orden_emer()"
            id="slcDataOrdenEmergente" style="width:100%;cursor:pointer;">'.$select.'</select>
        </div> 
    </div>
    <hr>
    <button class="btn btn-info btn-sm float-right" onclick="get_data_orden_table_emergente()" ><i class="fas fa-spinner" ></i></button>
    <div id="cont-table-emergente">
    '.$table.'
    </div>';
}
function return_data_orden($Orden){
    return '
    <h6>
        <b>
            <span class="text-muted">ORDEN DE PRODUCCIÓN: </span>
            <a href="../../documentos/producion/orden/'.$Orden.'.pdf" target="_black">'.$Orden.'</a>
        </b>
    </h6>
    <h6>
        <b>
            <span class="text-muted">FECHA: </span> '.Transformar_Fecha(date("Y-m-d")).'
        </b>
    </h6>';
}
function return_data_orden_emergente($Orden){
    return '
    <h6>
        <b>
            <span class="text-muted">ORDEN DE PRODUCCIÓN: </span>
            <a href="../../documentos/producion/emergente/'.$Orden.'.pdf" target="_black">'.$Orden.'</a>
        </b>
    </h6>
    <h6>
        <b>
            <span class="text-muted">FECHA: </span> '.Transformar_Fecha(date("Y-m-d")).'
        </b>
    </h6>';
}
function get_data_table_orde($dbConn,$Orden){
    $resultado = '
    <table id="tbl_orden_actual"
        class="table table-bordered table-striped table-hover table-sm">
        <thead>
            <th class="text-center">NUM.</th>
            <th>CLIENTE</th>
            <th>FACTURA</th>
            <th class="text-center">CANTIDAD</th>
            <th class="text-center d-none">CANTIDAD</th>
            <th class="text-center">PROCESADO</th>
            <th class="text-center">SALDO</th>
            <th class="text-center">ACCIONES</th>
        </thead>
        <tbody>';
    $consulta="SELECT o.ordId,o.ordTipo,p.gprId,c.cliNombres,o.ordCantidad, o.ordProcesado, o.Num_Documento,p.gprestadoDetalle, (p.gprMacho + p.gprHembra) AS gprCantidad
    FROM tbl_p_orden o, tbl_r_guiaproceso p, tbl_a_clientes c 
    WHERE o.gprId = p.gprId AND p.cliId = c.cliId AND p.gprId is not null AND 
    o.ordNumOrden = :orden AND o.ordEstado = 0  ORDER BY o.ordId ASC";
	$sql= $dbConn->prepare($consulta);
    $sql->bindValue(':orden',$Orden);
	$sql->execute();
    $cont = 0;
	while ($row = $sql->fetch()) {
        // Estado Detalle = 0; No se detalla la guia => El Estado debe ser 1
        // Estado Detalle = 1; Se detalla la guia => El Estado debe ser 0
        $span = 'ERROR'.$row["gprestadoDetalle"].'';
        if ($row["gprestadoDetalle"]==0 || $row["gprestadoDetalle"]==1) {
            $cont++;
            if ($row["gprestadoDetalle"]==1){
                $span = '*'.$cont.'';
            }else if ($row["gprestadoDetalle"]==0){
                $span = ''.$cont.'';
            }
        }
        $cantidad = f_obtener_cantidades($dbConn,$row["gprId"]);
        $total = 0;
        $resultado .='
        <tr>
            <th class="text-center">'.$span.'</th>
            <td id="cli-'.$row["ordId"].'">'.utf8_encode($row["cliNombres"]).'</td>
            <td id="fac-'.$row["ordId"].'">'.$row["Num_Documento"].'</td>
            <td class="text-center d-none">'.$cantidad[0].'</td>
            <td class="text-center">'.$row["ordCantidad"].'</td>
            <td class="text-center">'.$row["ordProcesado"].'</td>
            <td class="text-center">'.$row["ordCantidad"].'</td>
            <td class="text-center">
                <button class="btn btn-info btn-sm" data-toggle="modal"
                    data-target="#modal" onclick="get_data_factura('.$row["ordId"].')">
                    <i class="fas fa-money-check-alt"></i>
                </button>
                <button class="btn btn-danger btn-sm" onclick="f_pagar('.$row["ordId"].')">
                    <i class="fas fa-money-bill-wave"></i>
                </button>
            </td>
        </tr>';
	}
	return $resultado.'</tbody></table>';
}
function get_data_table_orde_emergente($dbConn,$Orden){
    $resultado = '
    <table id="tbl_orden_emergente"
        class="table table-bordered table-striped table-hover table-sm">
        <thead>
            <th class="text-center">NUM.</th>
            <th>CLIENTE</th>
            <th>FACTURA</th>
            <th class="text-center d-none">CANTIDAD</th>
            <th class="text-center">CANTIDAD</th>
            <th class="text-center">PROCESADO</th>
            <th class="text-center">SALDO</th>
            <th class="text-center">ACCIONES</th>
        </thead>
        <tbody>';
    $consulta="SELECT o.ordId,o.ordTipo,p.gprId,c.cliNombres,o.ordCantidad, o.ordProcesado, o.Num_Documento,p.gprestadoDetalle, (p.gprMacho + p.gprHembra) AS gprCantidad
    FROM tbl_p_orden o, tbl_r_guiaproceso p, tbl_a_clientes c 
    WHERE o.gprId = p.gprId AND p.cliId = c.cliId AND p.gprId is not null AND 
    o.ordNumOrden = :orden AND o.ordEstado = 0  ORDER BY o.ordId ASC";
	$sql= $dbConn->prepare($consulta);
    $sql->bindValue(':orden',$Orden);
	$sql->execute();
    $cont = 0;
	while ($row = $sql->fetch()) {
        // Estado Detalle = 0; No se detalla la guia => El Estado debe ser 1
        // Estado Detalle = 1; Se detalla la guia => El Estado debe ser 0
        $span = 'ERROR'.$row["gprestadoDetalle"].'';
        if ($row["gprestadoDetalle"]==0 || $row["gprestadoDetalle"]==1) {
            $cont++;
            if ($row["gprestadoDetalle"]==1){
                $span = '*'.$cont.'';
            }else if ($row["gprestadoDetalle"]==0){
                $span = ''.$cont.'';
            }
        }
        $cantidad = f_obtener_cantidades($dbConn,$row["gprId"]);
        $total = 0;
        $resultado .='
        <tr>
            <th class="text-center">'.$span.'</th>
            <td id="cli-'.$row["ordId"].'">'.utf8_encode($row["cliNombres"]).'</td>
            <td id="fac-'.$row["ordId"].'">'.$row["Num_Documento"].'</td>
            <td class="text-center d-none">'.$cantidad[0].'</td>
            <td class="text-center">'.$row["ordCantidad"].'</td>
            <td class="text-center">'.$row["ordProcesado"].'</td>
            <td class="text-center">'.$row["ordCantidad"].'</td>
            <td class="text-center">
                <button class="btn btn-info btn-sm" data-toggle="modal"
                    data-target="#modal" onclick="get_data_factura('.$row["ordId"].')">
                    <i class="fas fa-money-check-alt"></i>
                </button>
                <button class="btn btn-danger btn-sm" onclick="f_pagar('.$row["ordId"].')">
                    <i class="fas fa-money-bill-wave"></i>
                </button>
            </td>
        </tr>';
	}
	return $resultado.'</tbody></table>';
}

function f_obtener_cantidades($dbConn,$Id){
    $cont= [0,0,0];
    $consulta="SELECT * FROM tbl_p_antemortem WHERE gprId = :id ";
    $sql= $dbConn->prepare($consulta);  
    $sql->bindValue(':id',$Id);
    $sql->execute();
    while($row = $sql->fetch()) {
        if ($row["antDictamen"]==0) $cont[0] += $row["antCantidad"];
        else if($row["antDictamen"]==1) $cont[1] += $row["antCantidad"];
        else if($row["antDictamen"]==2) $cont[2] += $row["antCantidad"];
        else return false;
    }
    return $cont;
}
function return_cont_card($dbConn){
    $Id = $_POST["Id"];
    $data_actual = get_data_orden($dbConn,$Id);
    $data_emergente = get_data_orden_emergente($dbConn,$Id);
    $otra_fecha = get_data_otras($dbConn);
    $turno = get_data_trunos_actual($dbConn,$Id);
    $urgencias = get_data_trunos_urgencias($dbConn,$Id);
    return '
    <div class="card card-navy card-tabs">
        <div class="card-header p-0 pt-1">
            <ul class="nav nav-tabs" id="custom-tabs-five-tab" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="custom-tabs-five-overlay-tab" data-toggle="pill"
                        href="#custom-tabs-five-overlay" role="tab" aria-controls="custom-tabs-five-overlay"
                        aria-selected="true">Producción</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link " id="custom-tabs-five-overlay-tab-2" data-toggle="pill"
                        href="#custom-tabs-five-overlay-2" role="tab" aria-controls="custom-tabs-five-overlay"
                        aria-selected="true">Emergente</a>
                </li>
                
                <li class="nav-item">
                    <a class="nav-link" id="custom-tabs-five-overlay-dark-tab" data-toggle="pill"
                        href="#custom-tabs-five-overlay-dark" role="tab"
                        aria-controls="custom-tabs-five-overlay-dark" aria-selected="false">
                        Otras Fechas</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="custom-tabs-five-normal-tab" data-toggle="pill"
                        href="#custom-tabs-five-normal" role="tab" aria-controls="custom-tabs-five-normal"
                        aria-selected="false">Turno</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="custom-tabs-five-normal-tab-2" data-toggle="pill"
                        href="#custom-tabs-five-normal-2" role="tab" aria-controls="custom-tabs-five-normal"
                        aria-selected="false">Camal de urgencias</a>
                </li>
            </ul>
        </div>
        <div class="card-body">
            <div class="tab-content" id="custom-tabs-five-tabContent">
                <div class="tab-pane fade active show" id="custom-tabs-five-overlay" role="tabpanel"
                    aria-labelledby="custom-tabs-five-overlay-tab">
                    <div class="overlay-wrapper" style="min-height: 100px;">
                        '.$data_actual.'
                    </div>
                </div>
                <div class="tab-pane fade" id="custom-tabs-five-overlay-2" role="tabpanel"
                    aria-labelledby="custom-tabs-five-overlay-tab-2">
                    <div class="overlay-wrapper" style="min-height: 100px;">
                        '.$data_emergente.'
                    </div>
                </div>
                <div class="tab-pane fade" id="custom-tabs-five-overlay-dark" role="tabpanel"
                    aria-labelledby="custom-tabs-five-overlay-dark-tab" style="min-height: 100px;">
                    <div class="overlay-wrapper">
                        '.$otra_fecha.'
                    </div>
                </div>
                <div class="tab-pane fade" id="custom-tabs-five-normal" role="tabpanel"
                    aria-labelledby="custom-tabs-five-normal-tab" style="min-height: 100px;">
                    '.$turno.'
                </div>
                <div class="tab-pane fade" id="custom-tabs-five-normal-2" role="tabpanel"
                    aria-labelledby="custom-tabs-five-normal-tab-2" style="min-height: 100px;">
                    '.$urgencias.'
                </div>
            </div>
        </div>
    </div>';
}

function get_data_factura($dbConn){
    $Id = $_POST["Id"];
    $consulta="SELECT * FROM tbl_YP_EGDATA_FAC WHERE ordId = :id AND Tipo = 'C'";
	$sql= $dbConn->prepare($consulta);
    $sql->bindValue(':id',$Id);
	$sql->execute();
	if($row = $sql->fetch()) {
        $ident = "NO ENCONTRADO";
        if ($row["Codigo_Id"]==1)$ident = "RUC";
        else if ($row["Codigo_Id"]==2)$ident = "CÉDULA";
        return '
        <div class="row">
            <div class="col-md-12">
                <h6>
                    <b>
                        <span class="text-muted">CLIENTE: </span>
                        '.utf8_encode($row["Detalle1"]).'
                    </b>
                </h6>
            </div>
            <div class="col-md-6">
                <h6><b>
                        <span class="text-muted">'.$ident.': </span>
                        '.$row["Codigo_Id3"].'
                    </b></h6>
            </div>
            <div class="col-md-6">
                <h6><b>
                        <span class="text-muted">CONTACTO: </span>
                        '.utf8_encode($row["Detalle3"]).'
                    </b></h6>
            </div>
            <div class="col-md-12">
                <h6><b>
                        <span class="text-muted">DIRECCIÓN: </span>
                        '.utf8_encode($row["Detalle2"]).'
                    </b></h6>
            </div>
        </div>
        <hr>
        <table class="table table-bordered table-striped table-hover table-sm ">
            <thead class="bg-navy">
                <th>DESCRIPCIÓN</th>
                <th class="text-center">P. UNITARIO</th>
                <th class="text-center">CANTIDAD</th>
                <th class="text-center">TOTAL</th>
            </thead>
            <tbody>
            '.get_data_factura_detalle($dbConn,$Id).'
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="3" class="text-right">Subtotal</th>
                    <th class="text-right">'.number_format($row["Valor1"],2).' $</th>
                </tr>
                <tr>
                    <th colspan="3" class="text-right">IVA</th>
                    <th class="text-right">'.number_format($row["Valor2"],2).' $</th>
                </tr>
                <tr class="bg-navy">
                    <th colspan="3" class="text-right">Total</th>
                    <th class="text-right">'.number_format($row["Valor3"],2).' $</th>
                </tr>
            </tfoot>
        </table>';
	}
}

function get_data_factura_detalle($dbConn,$Id){
    $resultado = '';
    $consulta="SELECT * FROM tbl_YP_EGDATA_FAC WHERE ordId = :id AND Tipo = 'D'";
	$sql= $dbConn->prepare($consulta);
    $sql->bindValue(':id',$Id);
	$sql->execute();
	while($row = $sql->fetch()) {
        $d_descripcion = f_descripcion_yupak($row["Codigo_Id"]);
        $servicio = "";
        if (is_null($row["srnId"] ))$servicio = "Corralaje";
        else $servicio  = get_data_servicio($dbConn,$row["srnId"]);
        $resultado .= '
            <tr>
                <td>'.utf8_encode($d_descripcion).' ('.utf8_encode($servicio).')</td>
                <td class="text-center">'.number_format($row["Valor1"],2).' $</td>
                <td class="text-center">'.intval($row["Valor4"]).'</td>
                <td class="text-right">'.number_format($row["Valor3"],2).' $</td>
            </tr>
        ';
	}
    return $resultado;
}
function f_descripcion_yupak($codigo){
    include '../../FilePHP/consql.php';
    $sql = mssql_query('SELECT * FROM YP_FAC_SERVICE WHERE Codigo = '.$codigo);
    if($row = mssql_fetch_array($sql)) return utf8_encode($row["Descripcion"]);
    else return false;
}
function get_data_servicio($dbConn,$Id){
    $consulta="SELECT srnDescripcion FROM tbl_a_servicios WHERE srnId = :id";
	$sql= $dbConn->prepare($consulta);
    $sql->bindValue(':id',$Id);
	$sql->execute();
	if($row = $sql->fetch()) return $row["srnDescripcion"];
    else return 'NO ENCONTRADO';
}
function f_turno_orden($dbConn){
    try {
        $Id = $_POST['Id'];
        $consulta="SELECT Num_Documento,ordNumOrden,ordEstado FROM tbl_p_orden WHERE ordId = :id";
        $sql= $dbConn->prepare($consulta);
        $sql->bindValue(':id',$Id);
        $sql->execute();
        if($row = $sql->fetch()) {
            if ($row["ordEstado"]==0) {
                $Acion = 'Pago de Factura';
                $detalle =  'Factura: <b>'.$row["Num_Documento"].'</b><br>Orden: <b>'.$row["ordNumOrden"].'</b>';
                $sql= $dbConn->prepare('UPDATE tbl_p_orden SET ordEstado = 1, ordFechaTurno = :fecha WHERE ordId = :id');
                $sql->bindValue(':fecha',date("Y-m-d H:i:s"));
                $sql->bindValue(':id',$Id);
                if ($sql->execute())return Insert_Login($Id,'tbl_p_orden',$Acion,$detalle,'');
                else return "ERROR-665242";
            }else{
                return 'Ya se registro el pago de esta factura';
            }
        }else return "ERROR-6555";//NO SE ENCONTRO EL ID
    }  catch (Exception $e) {
        Insert_Error('ERROR-6555',$e->getMessage(),'ERROR AL PAGAR LA FACTURA');
        exit("ERROR-6555");
    }
	
}

function get_data_otras($dbConn){
    $Id = $_POST["Id"];
    $resultado = '
    <table id="tbl_orden_fecha"
        class="table table-bordered table-striped table-hover table-sm">
        <thead>
            <th class="text-center">NUM.</th>
            <th>CLIENTE</th>
            <th>ORDEN DE PRODUCCIÓN</th>
            <th>FACTURA</th>
            <th >FECHA</th>
            <th class="text-center">CANTIDAD</th>
            <th class="text-center">PROCESADO</th>
            <th class="text-center">SALDO</th>
            <th class="text-center">ACCIONES</th>
        </thead>
        <tbody>';
    $consulta="SELECT * FROM tbl_p_orden o, tbl_r_guiaproceso p, tbl_a_clientes c
    WHERE o.gprId is not null AND o.gprId = p.gprId  AND p.cliId = c.cliId  AND o.ordEstado = 0 AND o.espId = :id AND o.ordTipo = 0
    AND o.ordFecha < :fecha ORDER BY o.ordId ASC";
    $sql= $dbConn->prepare($consulta);
    $sql->bindValue(':fecha',date("Y-m-d"));
    $sql->bindValue(':id',$Id);
	$sql->execute();
    $cont = 0;
	while ($row = $sql->fetch()) {
        // Estado Detalle = 0; No se detalla la guia => El Estado debe ser 1
        // Estado Detalle = 1; Se detalla la guia => El Estado debe ser 0
        $span = 'ERROR'.$row["gprestadoDetalle"].'';
        if ($row["gprestadoDetalle"]==0 || $row["gprestadoDetalle"]==1) {
            $cont++;
            if ($row["gprestadoDetalle"]==1){
                $span = '*'.$cont.'';
            }else if ($row["gprestadoDetalle"]==0){
                $span = ''.$cont.'';
            }
        }
        $cantidad = f_obtener_cantidades($dbConn,$row["gprId"]);
        $total = 0;
        $carpeta = "";
        if ($row["ordTipo"]==0){
            $carpeta = "orden";
            $total = $cantidad[0];
        }
        else if ($row["ordTipo"]==1){
            $carpeta = "emergente";
            $total = $cantidad[1] + $cantidad[2];
        }

        $resultado .='
        <tr>
            <th class="text-center">'.$span.'</th>
            <td id="cli-'.$row["ordId"].'">'.utf8_encode($row["cliNombres"]).'</td>
            <td id="ord-'.$row["ordId"].'">
                <a href="../../documentos/producion/'.$carpeta.'/'.$row["ordNumOrden"].'.pdf" target="_black">
                    '.$row["ordNumOrden"].'
                </a>
            </td>
            <td id="fac-'.$row["ordId"].'">'.$row["Num_Documento"].'</td>
            <td class="text-center">'.$row["ordFecha"].'</td>
            <td class="text-center">'.$total.'</td>
            <td class="text-center">'.$row["ordProcesado"].'</td>
            <td class="text-center">'.$row["ordCantidad"].'</td>
            <td class="text-center">
                <button class="btn btn-info btn-sm" data-toggle="modal"
                    data-target="#modal" onclick="get_data_factura('.$row["ordId"].')">
                    <i class="fas fa-money-check-alt"></i>
                </button>
                <button class="btn btn-danger btn-sm" onclick="f_pagar_otras('.$row["ordId"].')">
                    <i class="fas fa-money-bill-wave"></i>
                </button>
            </td>
        </tr>';
	}
	return '<div id="cont-table-otras"><button class="btn btn-info btn-sm float-right" onclick="get_data_orden_fecha()" ><i class="fas fa-spinner" ></i></button>'.$resultado.'</tbody></table></div>';
}
function get_data_trunos_actual($dbConn,$Id){
    $resultado = '
    <table id="tbl_orden_turno"
        class="table table-bordered table-striped table-hover table-sm">
        <thead>
            <th class="text-center">NUM.</th>
            <th>CLIENTE</th>
            <th>ORDEN DE PRODUCCIÓN</th>
            <th>FACTURA</th>
            <th >FECHA DE TURNO</th>
            <th class="text-center">CANTIDAD</th>
            <th class="text-center">PROCESADO</th>
            <th class="text-center">SALDO</th>
        </thead>
        <tbody>';
    $consulta="SELECT * FROM tbl_p_orden o, tbl_r_guiaproceso p, tbl_a_clientes c
    WHERE o.gprId is not null AND o.gprId = p.gprId AND p.cliId = c.cliId  AND o.ordEstado = 1 AND o.espId = :id AND o.ordTipo = 0
    AND o.ordFechaTurno BETWEEN :inicio AND :fin ORDER BY o.ordFechaTurno ASC";
    $sql= $dbConn->prepare($consulta);
    $sql->bindValue(':id',$Id);
    $sql->bindValue(':inicio',date("Y-m-d")." 00:00:00");
    $sql->bindValue(':fin',date("Y-m-d")." 23:59:59");
	$sql->execute();
    $cont = 0;
	while ($row = $sql->fetch()) {
        // Estado Detalle = 0; No se detalla la guia => El Estado debe ser 1
        // Estado Detalle = 1; Se detalla la guia => El Estado debe ser 0
        $span = 'ERROR'.$row["gprestadoDetalle"].'';
        if ($row["gprestadoDetalle"]==0 || $row["gprestadoDetalle"]==1) {
            $cont++;
            if ($row["gprestadoDetalle"]==1){
                $span = '*'.$cont.'';
            }else if ($row["gprestadoDetalle"]==0){
                $span = ''.$cont.'';
            }
        }
        $cantidad = f_obtener_cantidades($dbConn,$row["gprId"]);
        $resultado .='
        <tr>
            <th class="text-center">'.$span.'</th>
            <td>'.utf8_encode($row["cliNombres"]).'</td>
            <td>
                <a  href="../../documentos/producion/orden/'.$row["ordNumOrden"].'.pdf" target="_black">
                    '.$row["ordNumOrden"].'
                </a>
            </td>
            <td> 
                <a  href="" data-toggle="modal" data-target="#modal" onclick="get_data_factura('.$row["ordId"].')" >'.$row["Num_Documento"].'</a>
            </td>
            <td >'.$row["ordFechaTurno"].'</td>
            <td class="text-center">'.$cantidad[0].'</td>
            <td class="text-center">'.$row["ordProcesado"].'</td>
            <td class="text-center">'.$row["ordCantidad"].'</td>
        </tr>';
	}
    $header ='
    <h5 class="text-muted text-center">TURNO DE FAENAMIENTO PARA <br><b>'.Transformar_Fecha(date("Y-m-d")).'</b></h5>
    <button class="btn btn-info btn-sm float-right" onclick="get_data_turno()" ><i class="fas fa-spinner" ></i></button>';
	return $header.$resultado.'</tbody></table>';
}
function get_data_trunos_urgencias($dbConn,$Id){
    $resultado = '
    <table id="tbl_orden_urgencias"
        class="table table-bordered table-striped table-hover table-sm">
        <thead>
            <th class="text-center">NUM.</th>
            <th>CLIENTE</th>
            <th>ORDEN DE PRODUCCIÓN</th>
            <th>FACTURA</th>
            <th >FECHA DE TURNO</th>
            <th class="text-center">CANTIDAD</th>
            <th class="text-center">PROCESADO</th>
            <th class="text-center">SALDO</th>
        </thead>
        <tbody>';
    $consulta="SELECT * FROM tbl_p_orden o, tbl_r_guiaproceso p, tbl_a_clientes c
    WHERE o.gprId is not null AND o.gprId = p.gprId AND p.cliId = c.cliId  AND o.ordEstado = 1 AND o.espId = :id AND o.ordTipo = 1
    AND o.ordFechaTurno BETWEEN :inicio AND :fin ORDER BY o.ordFechaTurno ASC";//
    $sql= $dbConn->prepare($consulta);
    $sql->bindValue(':id',$Id);
    $sql->bindValue(':inicio',date("Y-m-d")." 00:00:00");
    $sql->bindValue(':fin',date("Y-m-d")." 23:59:59");
	$sql->execute();
    $cont = 0;
	while ($row = $sql->fetch()) {
        // Estado Detalle = 0; No se detalla la guia => El Estado debe ser 1
        // Estado Detalle = 1; Se detalla la guia => El Estado debe ser 0
        $span = 'ERROR'.$row["gprestadoDetalle"].'';
        if ($row["gprestadoDetalle"]==0 || $row["gprestadoDetalle"]==1) {
            $cont++;
            if ($row["gprestadoDetalle"]==1){
                $span = '*'.$cont.'';
            }else if ($row["gprestadoDetalle"]==0){
                $span = ''.$cont.'';
            }
        }
        $cantidad = f_obtener_cantidades($dbConn,$row["gprId"]);
        $resultado .='
        <tr>
            <th class="text-center">'.$span.'</th>
            <td>'.utf8_encode($row["cliNombres"]).'</td>
            <td>
                <a  href="../../documentos/producion/emergente/'.$row["ordNumOrden"].'.pdf" target="_black">
                    '.$row["ordNumOrden"].'
                </a>
            </td>
            <td> 
                <a  href="" data-toggle="modal" data-target="#modal" onclick="get_data_factura('.$row["ordId"].')" >'.$row["Num_Documento"].'</a>
            </td>
            <td >'.$row["ordFechaTurno"].'</td>
            <td class="text-center">'.$cantidad[0].'</td>
            <td class="text-center">'.$row["ordProcesado"].'</td>
            <td class="text-center">'.$row["ordCantidad"].'</td>
        </tr>';
	}
    $header ='
    <h5 class="text-muted text-center">CAMAL URGENCIAS <br><b>'.Transformar_Fecha(date("Y-m-d")).'</b></h5>
    <button class="btn btn-info btn-sm float-right" onclick="get_data_urgencias()" ><i class="fas fa-spinner" ></i></button>';
	return $header.$resultado.'</tbody></table>';
}
?>
