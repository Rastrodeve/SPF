<?php



if (isset($_REQUEST['op'])) {
	require '../../FilePHP/utils.php';
	$dbConn = conectar($db);
	$op=$_REQUEST['op'];
	if ($op==1)echo Lista_de_Guias($dbConn);
	elseif ($op==2)echo Datos_Detalle($dbConn);
	elseif ($op==3)echo Comprobar_Codigo($dbConn);
	elseif ($op==4)echo Comprobar_Codigo_temporal($dbConn);
	elseif ($op==5)echo GuardarDetalle_Temporal($dbConn);
	elseif ($op==6)echo Get_Data_Detalle_Body($dbConn);
	elseif ($op==7)echo Comprobar_Total($dbConn);
	elseif ($op==8)echo Info_box_manual($dbConn);
	elseif ($op==9)echo Info_box_temporal($dbConn);
	elseif ($op==14)echo Comprobar_detalle_all($dbConn);
}else{
	header('location: ../../');
}
function Lista_de_Guias($dbConn){
	$resultado='<table id="tbl_detalle_guia" class="table table-bordered table-striped">
    <thead style="font-size:15px;">
        <tr>
            <th>#</th>
            <th>N° DE GUÍA</th>
            <th>RUC</th>
            <th>CLIENTE</th>
            <th>TIPO</th>
            <th>CANTIDAD</th>
            <th>FECHA PROCESO</th>
            <th>SELECCIONAR</th>
        </tr>
    </thead>
    <tbody>';
    $consulta="SELECT p.id_contador,p.guia_numero,p.ruc,c.apellidos,c.nombres,c.observaciones,e.tipo,p.cantidad, p.fecha_proceso FROM tbl_guia_proceso p JOIN tbl_clientes c ON p.ruc=c.ruc JOIN tbl_especieanimales e ON p.tipo=e.codigo_especieanimales JOIN tbl_datos_especieanimales_new d ON p.tipo = d.codigo_especieanimales WHERE p.procesado=0 AND p.detallada=0 AND d.daeDetalle=1 ORDER BY p.fecha_proceso ASC ";
	$sql= $dbConn->prepare($consulta);
	$sql->execute();
	$cont = 0;
	while ($row = $sql->fetch()) {
		$cont++;
		$resultado .='
        <tr">
            <th>'.$cont.'</th>
            <td>'.$row["guia_numero"].'</td>
            <td>'.$row["ruc"].'</td>
            <td>'.$row["apellidos"].' '.$row["nombres"].' <b>'.$row["observaciones"].'</b></td>
            <td>'.$row["tipo"].'</td>
            <td>'.$row["cantidad"].'</td>
            <td>'.$row["fecha_proceso"].'</td>
            <td >
                <button class="btn btn-info btn-sm" onclick="Mostrar('.$row["id_contador"].')">
                    <b>DETALLAR </b>
                </button>
            </td>
        </tr>';
	}
	return $resultado."</tbody></table>";
}
function Datos_Detalle($dbConn){
	$resultado='';
    $Id = $_POST["Id"];
    $consulta="SELECT p.id_contador,p.guia_numero,p.ruc,c.apellidos,c.nombres,c.observaciones,e.tipo,p.cantidad FROM tbl_guia_proceso p JOIN tbl_clientes c ON p.ruc = c.ruc JOIN tbl_especieanimales e ON p.tipo = e.codigo_especieanimales WHERE id_contador = :id";
	$sql= $dbConn->prepare($consulta);
    $sql->bindValue(':id',$Id);
	$sql->execute();
	if ($row = $sql->fetch()) {
        $cliente = $row["apellidos"].' '.$row["nombres"].' <b>'.$row["observaciones"].'</b>';
		$ecnabezado = Return_Encabezado($row["cantidad"],$row["tipo"],$row["guia_numero"],$cliente,$row["ruc"]);
        $Tabla = Get_Data_Detalle_Normal($dbConn,$row["id_contador"]);
        $total_temporal = GET_DataTemporal($dbConn,$row["id_contador"]);
        $total_registrados =  GET_DataRegistrados($dbConn,$row["id_contador"]);
        $restantes = $row["cantidad"] - $total_registrados - $total_temporal;
        $Body = Return_Body($Tabla,$total_registrados,$row["id_contador"],$restantes);
        $resultado = $ecnabezado.$Body;
	}
	return $resultado;
}
function Return_Encabezado($cantidad,$tipo,$guia,$cliente,$cedula){
    return '<div class="row">
    <div class="col-md-7">
        <div class="row">
            <div class="col-sm-4">
                <div class="info-box bg-light">
                    <div class="info-box-content">
                        <span class="info-box-text text-center text-muted">
                            Cantidad Total
                        </span>
                        <span class="info-box-number text-center text-muted mb-0">'.$cantidad.'</span>
                    </div>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="info-box bg-light">
                    <div class="info-box-content">
                        <span class="info-box-text text-center text-muted">
                            Tipo de Ganado
                        </span>
                        <span class="info-box-number text-center text-muted mb-0">
                        '.$tipo.'
                        </span>
                    </div>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="info-box bg-light">
                    <div class="info-box-content">
                        <span class="info-box-text text-center text-muted">
                            Número de Guía
                        </span>
                        <span
                            class="info-box-number text-center text-muted mb-0">
                            '.$guia.'
                            <span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-5">
        <div class="row">
            <div class="col-12">
                <div class="info-box bg-light">
                    <div class="info-box-content">
                        <span class="info-box-text text-center text-muted">
                            '.$cliente.'
                        </span>
                        <span
                            class="info-box-number text-center text-muted mb-0">'.$cedula.'<span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>';
}
function Return_Body($tabla,$normal,$id,$restantes){
    // <label for="txtCoidgo" class="mb-1 ml-1 ">Código del Animal</label>
    return '<div class="row">
    <input type="hidden" value="'.$id.'" id="inputId" >
    <div class="col-md-7">
        <div class="row">
            <div class="col-sm-6">
                <div class="info-box bg-warning">
                    <div class="info-box-content" id="div_manual">
                        <span class="info-box-text text-center">
                            REGISTRADOS
                        </span>
                        <span class="info-box-number text-center mb-0">'.$normal.'<span>
                    </div>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="info-box bg-success">
                    <div class="info-box-content" id="div_restante">
                        <span class="info-box-text text-center ">
                            POR REGISTRAR 
                        </span>
                        <span class="info-box-number text-center mb-0">'.$restantes.'<span>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12 mt-1" >
                <h6><b>Listado de animales ingresados</b></h6>
            </div>
            <div class="col-12 mt-0" id="cont-table-detalle">
            '.$tabla.'
            </div>
            <div class="col-12 mt-3">
                <button class="btn btn-info d-none" onclick="Guardar_Detalle_completo()" id="guardar_detalle" > <b>GUARDAR DETALLE DE GUÍA</b> </button>
            </div>
        </div>
    </div>
    <div class="col-md-5">
        <div class="row">
            <div class="col-12">
                <label>Registro manual</label>  
            </div>
        </div>
        <div class="row">
            <div class="col-sm-6">
                <div class="input-group input-group-sm">
                    <input type="text" class="form-control input-sm" id="txtCoidgo" onKeyPress="enter_codigo(event)" 
                        placeholder="Código del Animal">
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <i class="fas fa-microchip"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="input-group input-group-sm mb-3">
                    <input type="text" class="form-control " id="txtPeso" onKeyPress="enter_pesar(event)"
                        placeholder="Peso del Animal (Kg)">
                    <div class="input-group-append ">
                        <div class="input-group-text" >
                            <span><i class="fas fa-weight-hanging"></i></span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-12">
                <textarea  id="textRazon" class="form-control form-control-sm"  rows="3" placeholder="¿Por que se esta guardando este animal manualmente?"></textarea>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-6">
                <button class="btn btn-info btn-block mt-3" onclick="Ayadir_Temporal()" id="ayadir_tem">
                    <b>Añadir <i class="fas fa-plus ml-1"></i></b>
                </button>
            </div>
        </div>
    </div>
</div>';
}
function Get_Data_Detalle_Normal($dbConn,$Id){
    $resultado ='<table width="100%"
    class="table-bordered table-striped table-hover table-sm text-center p-0">
    <thead class="table-info">
        <tr>
            <th>#</th>
            <th>CÓDIGO</th>
            <th>PESO</th>
            <th>REGISTRO</th>
        </tr>
    </thead>
    <tbody id="bd_tabla">';
    $consulta="SELECT dtCodigo,dtPeso,dtRegistro FROM tbl_detalle_guia_new WHERE id_contador = :id ORDER BY dtFecha ASC";
	$sql= $dbConn->prepare($consulta);
    $sql->bindValue(':id',$Id);
	$sql->execute();
    $cont=0;
	while ($row = $sql->fetch()) {
        $cont++;
        $resultado .= '<tr>
                <td >'.$cont.'</td>
                <td>'.$row["dtCodigo"].'</td>
                <td>'.$row["dtPeso"].'</td>
                <td>'.$row["dtRegistro"].'</td>
            </tr>';
	}   
	return $resultado.'</tbody></table>';
}


function GET_DataTemporal($dbConn,$Id){
    $consulta="SELECT * FROM tbl_detalle_temporal_new WHERE id_contador = :id ";
	$sql= $dbConn->prepare($consulta);
    $sql->bindValue(':id',$Id);
	$sql->execute();
    $cont=0;
	while ($row = $sql->fetch()) {
        $cont++;
	}   
	return $cont;
}
function GET_DataRegistrados($dbConn,$Id){
    $consulta="SELECT * FROM tbl_detalle_guia_new WHERE id_contador = :id";
	$sql= $dbConn->prepare($consulta);
    $sql->bindValue(':id',$Id);
	$sql->execute();
    $cont=0;
	while ($row = $sql->fetch()) {
        $cont++;
	}   
	return $cont;
}
function Comprobar_Codigo($dbConn){
	$comp=false;
	$code = $_POST["Code"];
	$consulta="SELECT dtCodigo FROM tbl_detalle_guia_new WHERE dtCodigo=:code AND dtProceso=0";
	$sql= $dbConn->prepare($consulta);
	$sql->bindValue(':code',$code);
	$sql->execute();
	if($row = $sql->fetch()) {
		$comp= true;
	}
	return $comp;
}
function Comprobar_Codigo_temporal($dbConn){
	$comp=false;
	$code = $_POST["Code"];
	$consulta="SELECT dtpCodigo FROM tbl_detalle_temporal_new WHERE dtpCodigo = :code";
	$sql= $dbConn->prepare($consulta);
	$sql->bindValue(':code',$code);
	$sql->execute();
	if($row = $sql->fetch()) {
		$comp= true;
	}
	return $comp;
}

function Comprobar_Total($dbConn){
    $Id = $_POST["Id"];
    $consulta="SELECT cantidad FROM tbl_guia_proceso WHERE id_contador = :id";
	$sql= $dbConn->prepare($consulta);
    $sql->bindValue(':id',$Id);
	$sql->execute();
    $cantidad_total=0;
	if($row = $sql->fetch()) {
        $cantidad_total= $row["cantidad"];
	}   
    if ($cantidad_total > 0) {
        $registrados = GET_DataRegistrados($dbConn,$Id);
        $total_temporal = GET_DataTemporal($dbConn,$Id);
        $cantidad_ingresada =  $registrados + $total_temporal;
        if ($cantidad_ingresada < $cantidad_total) return true;
        else return false;
    }else{
        return "ERRROR:20201";
    }
}

function GuardarDetalle_Temporal($dbConn){
    global $User;
    global $Ip;
    global $Equipo;
	$code = $_POST['Codigo'];
	$peso = $_POST['Peso'];
	$id = $_POST['Id_Contador'];
	$razon = $_POST['Razon'];
    $Fecha = date("Y-m-d H:i:s");
    $bandera = Comprobar_detalle_all2($dbConn,$code);
    if ($bandera==0) {
        $producto = selecionar_producto_predeterminado($dbConn,$id);
        if (Insert_detalle($dbConn,$code,$peso,$Fecha,1,$producto,$id,$razon)) {
            return ComprobarTotalGuia($dbConn,$id);
        }else return false;
    }
    
}
function Get_Data_Detalle_Body($dbConn){
    $resultado ='';
    $Id = $_POST["Id"];
    $consulta="SELECT dtCodigo,dtPeso,dtRegistro FROM tbl_detalle_guia_new WHERE id_contador = :id ORDER BY dtFecha ASC";
	$sql= $dbConn->prepare($consulta);
    $sql->bindValue(':id',$Id);
	$sql->execute();
    $cont=0;
	while ($row = $sql->fetch()) {
        $cont++;
        $resultado .= '<tr>
            <td >'.$cont.'</td>
            <td>'.$row["dtCodigo"].'</td>
            <td>'.$row["dtPeso"].'</td>
            <td>'.$row["dtRegistro"].'</td>
        </tr>';
	}   
	return $resultado.'</tbody></table>';
}

function Info_box_manual($dbConn){
    $Id = $_POST["Id"];
    $registrados = GET_DataRegistrados($dbConn,$Id);
    return '<span class="info-box-text text-center">
            REGISTRADOS
        </span>
        <span class="info-box-number text-center mb-0">'.$registrados.'<span>';
}
function Info_box_temporal($dbConn){
    $Id = $_POST["Id"];
    $restante = GET_DataTemporal($dbConn,$Id);
    $registrados = GET_DataRegistrados($dbConn,$Id);
    $consulta="SELECT cantidad FROM tbl_guia_proceso WHERE id_contador = :id ";
	$sql= $dbConn->prepare($consulta);
    $sql->bindValue(':id',$Id);
	$sql->execute();
    $total = 0;
	if($row = $sql->fetch()) {
        $total = $row["cantidad"] - $restante - $registrados;
	}   
    return '<span class="info-box-text text-center">
        POR REGISTRAR 
        </span>
        <span class="info-box-number text-center mb-0">'.$total.'<span>';
}


function Comprobar_detalle_all2($dbConn,$Codigo){
    $consulta="SELECT dtCodigo FROM tbl_detalle_guia_new WHERE dtCodigo = :code AND dtProceso = 0";
	$sql= $dbConn->prepare($consulta);
    $sql->bindValue(':code',$Codigo);
	$sql->execute();
    $cont = 0;
	while($row = $sql->fetch()) {
        $cont++;
	}   
	return $cont;
}
function Insert_detalle($dbConn,$code,$peso,$Fecha,$registro,$producto,$id_contador,$razon){
    global $User;
    global $Ip;
    global $Equipo;
	$consulta = "INSERT INTO tbl_detalle_guia_new(dtCodigo,dtPeso,dtFecha,dtRegistro,dtRazon,proCodigo,id_contador,cedula,ip,maquina)
    VALUES (:code,:peso,:fecha,:registro,:razon,:producto,:id_contador,:cedula,:ip,:maquina)";
	$sql= $dbConn->prepare($consulta);
	$sql->bindValue(':code',$code);
    $sql->bindValue(':peso',$peso);
    $sql->bindValue(':fecha',$Fecha);
    $sql->bindValue(':registro',$registro);
    $sql->bindValue(':razon',$razon);
	$sql->bindValue(':producto',$producto);
	$sql->bindValue(':id_contador',$id_contador);
	$sql->bindValue(':cedula',$User);
	$sql->bindValue(':ip',$Ip);
	$sql->bindValue(':maquina',$Equipo);
	if ($result = $sql->execute()){
		return true;
	}else return $result;
}
function selecionar_producto_predeterminado($dbConn,$Id){
    $consulta="SELECT tipo FROM tbl_guia_proceso where id_contador = :id";
	$sql= $dbConn->prepare($consulta);
    $sql->bindValue(':id',$Id);
	$sql->execute();
    $cont = 0;
	if($row = $sql->fetch()) {
        $consulta2="SELECT 	proId FROM tbl_datos_especieanimales_new Where codigo_especieanimales = :code ";
        $sql2= $dbConn->prepare($consulta2);
        $sql2->bindValue(':code',$row["tipo"]);
        $sql2->execute();
        if($row2 = $sql2->fetch()) {
            return $row2["proId"];
        }else return 'ER01';
	} else return 'ER01';
}
function ComprobarTotalGuia($dbConn,$Id){
    $consulta="SELECT cantidad FROM tbl_guia_proceso where id_contador = :id";
	$sql= $dbConn->prepare($consulta);
    $sql->bindValue(':id',$Id);
	$sql->execute();
    $cont = 0;
    if($row = $sql->fetch()) {
        $cont = $row["cantidad"];
	} else return 'ER01';
    $registrados = GET_DataRegistrados($dbConn,$Id);
    if ($cont == $registrados) UpdateDetalle_Guia($dbConn,$Id);
    else return true;
    
}

function UpdateDetalle_Guia($dbConn,$Id){
	$consulta = "UPDATE tbl_guia_proceso SET detallada = 1  where id_contador = :id";
	$sql= $dbConn->prepare($consulta);
	$sql->bindValue(':id',$Id);
	if ($result = $sql->execute()){
		return true;
	}else return $result;
}

?>
