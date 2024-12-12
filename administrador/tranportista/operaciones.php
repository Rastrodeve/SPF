<?php
if (isset($_REQUEST['op'])) {
	require '../../FilePHP/utils.php';
	$dbConn = conectar($db);
	$op=$_REQUEST['op'];
	if ($op==1) echo data_table_transportista($dbConn);
    elseif($op==2) echo get_data_new();
    elseif($op==3) echo f_insert($dbConn);
    elseif($op==4) echo f_update_estado($dbConn);
    elseif($op==5) echo get_data_update($dbConn);
    elseif($op==6) echo f_update($dbConn);//Yes -- data_predeterminado($dbConn); 
    elseif($op==7) echo get_data_modal_vehhiculos($dbConn);
    elseif($op==8) echo f_update_estado_2($dbConn);
    elseif($op==9) echo get_data_table_vehiculos($dbConn,$_POST["Id"]);
    elseif($op==10) echo get_data_new_vehiculo($dbConn);
    elseif($op==11) echo f_insert_ve($dbConn);
    elseif($op==12) echo get_data_update_vehiculo($dbConn);
    elseif($op==13) echo f_update_vehi($dbConn);
    
}else header("location: ./");


function data_table_transportista($dbConn){
	$resultado='<table class="mt-2 table table-sm table-bordered table-striped "  id="table-data" style="text-align:center;">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Identificación</th>
                            <th>Razón Social</th>
                            <th>vehículos</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead><tbody>';
    $consulta="SELECT * FROM tbl_a_transportista";
	$sql= $dbConn->prepare($consulta);
	$sql->execute();
	$cont = 0;
	$Total=0;
	while ($row = $sql->fetch()) {
		$cont++;
		$estado ="ERROR";
		if ($row["trnEstado"]==0) {
			$estado ='<button type="button" onclick="f_estado(1,'.$row["trnId"].')" class="btn btn-success btn-sm"><b>ACTIVO</b></button>';
		}else if ($row["trnEstado"]==1){
			$estado ='<button type="button" onclick="f_estado(0,'.$row["trnId"].')" class="btn btn-danger btn-sm"><b>INACTIVO</b></button>';
		}
        $vehi = f_total_vehiculos($dbConn,$row["trnId"]);
		$resultado=$resultado.'
            <tr>
                <td>'.$cont.'</td>
                <td >'.$row["trnRuc"].'</td>
                <td >'.utf8_encode($row["trnRazonSocial"]).'</td>
                <td >'.$vehi.'</td>
                <td >'.$estado.'</td>
                <td>
                    <button  type="button" class="btn btn-sm btn-info" data-toggle="modal"
                    data-target="#modal"  onclick="get_data_update('.$row["trnId"].')" >
                        <b><i class="fas fa-edit"></i></b>
                    </button>
                    <button  type="button" class="btn btn-sm btn-warning" data-toggle="modal"
                    data-target="#modal"  onclick="get_data_vehiculo('.$row["trnId"].')" >
                        <b><i class="fas fa-truck"></i></b>
                    </button>
                </td>
            </tr';
	}

	return $resultado."</tbody></table>";
}
//GET DATA
function get_data_new(){
	$data = '<div class="row">
				<div class="col-md-12">
					<h6 class="text-muted"><b>AÑADIR NUEVO TRANSPORTISTA</b> </h6>
				</div>
			</div>
			<hr>
			<div class="row">
                <div class="col-md-6">
					<label for="txtIdentificacion">Número de identificación: </label><span class="text-muted">Cedula (10) o Ruc (13)</span>
					<input type="text" class="form-control form-control-sm" maxlength="13" id="txtIdentificacion" placeholder="Ruc o cedula"  onkeypress="onlynumber(event)" >
				</div>
				<div class="col-md-6">
					<label for="txtDescripcion">Razón social:</label><span class="text-muted">(max 50)</span>
					<input type="text" class="form-control form-control-sm" maxlength="50" id="txtDescripcion" placeholder="Nombre ">
				</div>
			</div>';
	return modal($data,'AÑADIR NUEVO TRANSPORTISTA','f_new()');
}
function get_data_update($dbConn){
	$Id = $_POST['Id'];
	$consulta=" SELECT * FROM tbl_a_transportista WHERE trnId = :id";
	$sql= $dbConn->prepare($consulta);
	$sql->bindValue(':id',$Id);
	$sql->execute();
	if($row = $sql->fetch()) {
		$data = '
		<input type="hidden" value="'.$row["trnId"].'" id="txtTransportista">
		<div class="row">
			<div class="col-md-12">
				<h6 class="text-muted"><b>EDITAR TRANSPORTISTA</b> </h6>
			</div>
		</div>
		<hr>
		<div class="row">
			<div class="col-md-6">
				<label for="txtDescripcion">Razón social actual:</label>
				<span class="text-muted form-control form-control-sm">'.utf8_encode($row["trnRazonSocial"]).'</span>
			</div>
            <div class="col-md-6">
				<label for="txtDescripcion">Nueva razón social:</label><span class="text-muted">(max 50)</span>
				<input type="text" class="form-control form-control-sm" maxlength="50" id="txtDescripcion" placeholder="Nombre" value="'.utf8_encode($row["trnRazonSocial"]).'">
			</div>
		</div>
		<hr>
		<div class="row">
            <div class="col-md-6">
                <label for="txtIdentificacion">Número de identificación actual: </label><span class="text-muted">Cedula (10) o Ruc (13)</span>
                <span class="form-control form-control-sm">'.$row["trnRuc"].'</span>
            </div>
            <div class="col-md-6">
                <label for="txtIdentificacion">Nuevo número de identificación: </label><span class="text-muted">Cedula (10) o Ruc (13)</span>
                <input type="text" class="form-control form-control-sm" maxlength="13" id="txtIdentificacion" placeholder="Ruc o cedula"  onkeypress="onlynumber(event)" value="'.$row["trnRuc"].'"  >
            </div>
		</div>';
		return modal($data,'ACTUALIZAR TRANSPORTISTA','f_update()');
	}else return modal("ERROR-7765542","ERROR-7765542",'error_f()');//NO SE ENCONTRO EL ID
}
function get_data_modal_vehhiculos($dbConn){
	$Id = $_POST['Id'];
	$consulta=" SELECT * FROM tbl_a_transportista WHERE trnId = :id";
	$sql= $dbConn->prepare($consulta);
	$sql->bindValue(':id',$Id);
	$sql->execute();
	if($row = $sql->fetch()) {
        $table = get_data_table_vehiculos($dbConn,$row["trnId"]);
		$data = '
		<input type="hidden" value="'.$row["trnId"].'" id="txtTransportista">
		<div class="row">
			<div class="col-md-12">
				<h6 class="text-muted"><b>'.utf8_encode($row["trnRazonSocial"]).' - '.$row["trnRuc"].'</b> </h6>
			</div>
		</div>
        <hr>
        <div id="cont-table">
            '.$table.'
        </div>';
		return modal($data,'VEHICULOS','');
	}else return modal("ERROR-7765542","ERROR-7765542",'error_f()');//NO SE ENCONTRO EL ID
}
function get_data_table_vehiculos($dbConn,$Id){
	$consulta=" SELECT * FROM tbl_a_vehiculo WHERE trnId = :id";
	$sql= $dbConn->prepare($consulta);
	$sql->bindValue(':id',$Id);
	$sql->execute();
    $return = '<table class="mt-2 table table-sm table-bordered table-striped "  id="table-data-vehidulo" style="text-align:center;">
    <thead>
        <tr>
            <th>#</th>
            <th>Placa</th>
            <th>Estado</th>
            <th>Editar</th>
        </tr>
    </thead><tbody>';
    $cont= 0;
	while($row = $sql->fetch()) {
        $estado ="ERROR";
		if ($row["vhcEstado"]==0) {
			$estado ='<button type="button" onclick="f_estado_2(1,'.$row["vhcId"].')" class="btn btn-success btn-sm"><b>ACTIVO</b></button>';
		}else if ($row["vhcEstado"]==1){
			$estado ='<button type="button" onclick="f_estado_2(0,'.$row["vhcId"].')" class="btn btn-danger btn-sm"><b>INACTIVO</b></button>';
		}
		$return .= '<tr>
        <td>'.++$cont.'</td>
        <td>'.utf8_encode($row["vhcPlaca"]).'</td>
        <td>'.$estado.'</td>
        <td>
            <button  type="button" class="btn btn-sm btn-info" onclick="get_data_update_vehiculo('.$row["vhcId"].')" >
                <b><i class="fas fa-edit"></i></b>
            </button>
        </td>
        </tr>';
	}
    return '<button class="btn btn-info btn-sm float-right" onclick="Cargar_Datos_vehiculo()" ><i class="fas fa-spinner"></i> </button>'.$return.'</tbody></table><center><button class="btn btn-warning " onclick="get_data_new_vehiculo()" ><b>NUEVO VEHÍCULO</b></button></center>';
}
function get_data_new_vehiculo($dbConn){
    return '
    <div class="row">
        <div class="col-md-12">
            <h6 class="text-muted"><b>NUEVO VEHÍCULO</b> </h6>
        </div>
    </div>
    <div class="row">
        <div class="col-3">
            <label for="txtPlaca">Placa del vehículo: </label>
        </div>
        <div class="col-3">
            <input type="text" class="form-control form-control-sm" maxlength="8" id="txtPlaca" placeholder="AAA-1111" >
        </div>
        <div class="col-4">
            <button class="btn btn-info btn-sm" onclick="f_new_vehiculo()" ><b>GUARDAR VEHICULO</b></button>
        </div>
    </div>
    <hr>
    <center> <button class="btn btn-warning " onclick="Cargar_Datos_vehiculo()"  ><b>REGRESAR</b></button> </center>';
}
function get_data_update_vehiculo($dbConn){
    $Id = $_POST["Id"];
    $consulta=" SELECT * FROM tbl_a_vehiculo WHERE vhcId = :id";
	$sql= $dbConn->prepare($consulta);
	$sql->bindValue(':id',$Id);
	$sql->execute();
    $data = '';
	if($row = $sql->fetch()){
        $data = '
        <div class="row">
            <div class="col-md-12">
            <input type="hidden" value="'.$row["vhcId"].'" id="txtVehiculo">
                <h6 class="text-muted"><b>EDITAR VEHÍCULO</b> </h6>
            </div>
        </div>
        <div class="row">
            <div class="col-4">
                <label >Placa acutal del vehículo: </label>
                <span class="form-control form-control-sm">'.utf8_encode($row["vhcPlaca"]).'</span>
            </div>
            <div class="col-4">
                <label for="txtPlaca">Placa nueva del vehículo: </label>
                <input type="text" class="form-control form-control-sm" maxlength="8" id="txtPlaca" placeholder="AAA-1111" value="'.utf8_encode($row["vhcPlaca"]).'" >
            </div>
            <div class="col-4">
                <label for=""></label>
                <button class="btn btn-info mt-4"  onclick="f_update_heviculo()" ><b>ACTUALIZAR INFORMACIÓN</b></button> 
            </div>
        </div>
        ';
    }else $data = 'ERROR-998333';
    return $data.'<hr><center> <button class="btn btn-warning " onclick="Cargar_Datos_vehiculo()"  ><b>REGRESAR</b></button> </center>';
}


//Insert
function f_insert($dbConn){
	$ident = $_POST['Identificacion'];
	$Nombre = $_POST['Nombre'];
    if (strlen($ident) != 10 && strlen($ident) != 13) {
        return 'El número de identificación debe contar con 10 o 13 caracteres';
    }
	$consulta="SELECT * FROM tbl_a_transportista WHERE trnRuc = :ruc";
	$sql= $dbConn->prepare($consulta);
	$sql->bindValue(':ruc',$ident);
	$sql->execute();
	if($row = $sql->fetch()) {
		return 'El número de identificación ya se encuentra registrado';
	}else{
		$consulta = "INSERT INTO tbl_a_transportista(trnRuc,trnRazonSocial) VALUES(:trnRuc,:trnRazonSocial)";
		$array =  array(array(":trnRuc",":trnRazonSocial"), array($ident,$Nombre));
		$Error ="Insertar nuevo transportista";
		$Acion ="Insertar nuevo transportista";
		$detalle='Identificación: <b>'.$ident.'</b> , Razón social: <b>'.$Nombre.'</b> ';
		return f_consulta($dbConn,$array,$consulta,$Error,$detalle,$Acion,0);
	}
}
function f_insert_ve($dbConn){
	$Id = $_POST['Id'];
    $Placa = $_POST['Placa'];
	$consulta="SELECT * FROM tbl_a_transportista WHERE trnId = :ruc";
	$sql= $dbConn->prepare($consulta);
	$sql->bindValue(':ruc',$Id);
	$sql->execute();
	if($row = $sql->fetch()) {
        if (f_comprobar($dbConn,array(array(":placa"), array($Placa)),'SELECT * FROM tbl_a_vehiculo WHERE vhcPlaca = :placa','ERROR AL COMPROBAR PLACA')) return 'La Placa seleccionada se encuentra registrada';
		$consulta2 = "INSERT INTO tbl_a_vehiculo(vhcPlaca,trnId) VALUES(:vhcPlaca,:trnId)";
		$array =  array(array(":vhcPlaca",":trnId"), array($Placa,$row["trnId"]));
		$Error ="Insertar nuevo Vehiculo";
		$Acion ="Insertar nuevo Vehiculo";
		$detalle='Vehiculo: <b>'.$Placa.'</b> => Transportista: <b>'.utf8_encode($row["trnRazonSocial"]).'</b> ';
		return f_consulta($dbConn,$array,$consulta2,$Error,$detalle,$Acion,0);
	}else return 'ERROR-121';
}

//UPDATE
function f_update_estado($dbConn){
	$Id = $_POST['Id'];
	$Estado = $_POST['Estado'];
	$consulta="SELECT * FROM tbl_a_transportista WHERE trnId = :id";
	$sql= $dbConn->prepare($consulta);
	$sql->bindValue(':id',$Id);
	$sql->execute();
	if($row = $sql->fetch()) {
		$detalle = "";
		$Acion = "";
		if ($Estado==0){
			$Acion = "Estado del transportista";
			$detalle = "Se activo el transportista (".utf8_encode($row["trnRazonSocial"]).")";
		}else if ($Estado==1){
			$Acion = "Estado del transportista";
			$detalle = "Se desactivo  el transportista(".utf8_encode($row["trnRazonSocial"]).")";
		}else {
			return "No se encontro el estado del transportista";
		}
		$array =  array(array(":estado",":id"),array($Estado,$Id));
		$consulta = "UPDATE tbl_a_transportista SET trnEstado = :estado WHERE trnId = :id";
		$Error = "Error al actualizar el estado del transportista (".$Estado.")";
        return f_consulta($dbConn,$array,$consulta,$Error,$detalle,$Acion,$Id);
	}else return "ERROR-7765542";//NO SE ENCONTRO EL ID
}
function f_update($dbConn){
    $Id = $_POST['Id'];
	$ident = $_POST['Identificacion'];
	$Nombre = $_POST['Nombre'];
    if (strlen($ident) != 10 && strlen($ident) != 13) {
        return 'El número de identificación debe contar con 10 o 13 caracteres';
    }
	$consulta="SELECT * FROM tbl_a_transportista WHERE trnId = :id";
	$sql= $dbConn->prepare($consulta);
	$sql->bindValue(':id',$Id);
	$sql->execute();
	if($row = $sql->fetch()) {
		if (f_comprobar($dbConn,array(array(":trnRuc",":id"),array($ident,$Id)),"SELECT * FROM tbl_a_transportista WHERE trnRuc = :trnRuc AND trnId != :id","ERROR-28211")) {
			return 'El número de identificación ingresado ya se encuentra registrado';
		}else{
			$consulta = "UPDATE tbl_a_transportista SET trnRazonSocial = :trnRazonSocial, trnRuc = :trnRuc WHERE trnId = :id ";
			$array =  array(array(":trnRazonSocial",":trnRuc",":id"), array($Nombre,$ident,$Id));
			$Error ="Actualización de Transportista";
			$Acion ="Actualización de Transportista";
			$detalle='Razón social actual <b>'.utf8_encode($row["trnRazonSocial"]).'</b> = > Nueva razón social actual <b>'.$Nombre.'</b><br>'.
            'Identificación actual: <b>'.$row["trnRuc"].'</b> => Nueva identficación: <b>'.$ident.'</b> ';
			return f_consulta($dbConn,$array,$consulta,$Error,$detalle,$Acion,$Id);
		}
	}else return 'ERROR-1212';
}
function f_update_estado_2($dbConn){
	$Id = $_POST['Id'];
	$Estado = $_POST['Estado'];
	$consulta="SELECT * FROM tbl_a_vehiculo WHERE vhcId = :id";
	$sql= $dbConn->prepare($consulta);
	$sql->bindValue(':id',$Id);
	$sql->execute();
	if($row = $sql->fetch()) {
		$detalle = "";
		$Acion = "";
		if ($Estado==0){
			$Acion = "Estado del Vehiculo";
			$detalle = "Se activo el vehículo (".utf8_encode($row["vhcPlaca"]).")";
		}else if ($Estado==1){
			$Acion = "Estado del Vehiculo";
			$detalle = "Se desactivo  el vehículo(".utf8_encode($row["vhcPlaca"]).")";
		}else {
			return "No se encontro el estado del vehiculo";
		}
		$array =  array(array(":estado",":id"),array($Estado,$Id));
		$consulta = "UPDATE tbl_a_vehiculo SET vhcEstado = :estado WHERE vhcId = :id";
		$Error = "Error al actualizar el estado del vehiculo(".$Estado.")";
        return f_consulta($dbConn,$array,$consulta,$Error,$detalle,$Acion,$Id);
	}else return "ERROR-7765542";//NO SE ENCONTRO EL ID
}
function f_update_vehi($dbConn){
    $Id = $_POST['Id'];
    $Placa = $_POST['Placa'];
	$consulta="SELECT * FROM tbl_a_vehiculo WHERE vhcId = :id";
	$sql= $dbConn->prepare($consulta);
	$sql->bindValue(':id',$Id);
	$sql->execute();
	if($row = $sql->fetch()) {
        if (f_comprobar($dbConn,array(array(":placa",":id"), array($Placa,$Id)),'SELECT * FROM tbl_a_vehiculo WHERE vhcPlaca = :placa AND vhcId != :id ','ERROR AL COMPROBAR PLACA')) return 'La Placa seleccionada se encuentra registrada';
		$consulta2 = "UPDATE tbl_a_vehiculo SET vhcPlaca = :vhcPlaca  WHERE vhcId = :vhcId ";
		$array =  array(array(":vhcPlaca",":vhcId"), array($Placa,$row["vhcId"]));
		$Error ="Actualizar Vehiculo";
		$Acion ="Actualizar Vehiculo";
		$detalle='Nueva Placa: <b>'.$Placa.'</b> => Placa anterior: <b>'.utf8_encode($row["vhcPlaca"]).'</b> ';
		return f_consulta($dbConn,$array,$consulta2,$Error,$detalle,$Acion,0);
	}else return 'ERROR-12199';
}

//Envio de consulta INSERT, UPDATE, DELETE
function f_consulta($dbConn,$array,$consulta,$Error,$detalle,$Acion,$Id){
	try {
		$sql= $dbConn->prepare($consulta);
		for ($i=0; $i < count($array[0]); $i++) { 
			$sql->bindValue($array[0][$i],utf8_decode($array[1][$i]));
		}
		if ($sql->execute()){
			if ($Id=0)$Id= $dbConn->lastInsertId();
			if(Insert_Login($Id,'tbl_a_enfermedad',$Acion,$detalle,'')) return true;
			else return 'ERROR-092222';
		}else return "ERROR-665242";//
	}  catch (Exception $e) {
		Insert_Error('ERROR-887222',$e->getMessage(),$Error);
		exit("ERROR-887222");
	}
}

//Comprobar
function f_comprobar($dbConn,$array,$consulta,$Error){
	try {
		$sql= $dbConn->prepare($consulta);
		for ($i=0; $i < count($array[0]); $i++) { 
			$sql->bindValue("".$array[0][$i],utf8_decode($array[1][$i]));
		}
		$sql->execute();
		if($row = $sql->fetch()) return true;
		else return false;
	}  catch (Exception $e) {
		Insert_Error('ERROR-65466',$e->getMessage(),$Error);
		exit("ERROR-65466");
	}
}

function modal($data,$titulo,$function){
	return '
	<div class="modal-header bg-secondary">
		<h5 class="modal-title" id="modalLabel">
			<b>'.$titulo.'</b>
		</h5>
		<button type="button" class="close" data-dismiss="modal" aria-label="Close">
			<span aria-hidden="true">&times;</span>
		</button>
	</div>
	<div class="modal-body">
		'.$data.'
	</div>
	<div class="modal-footer">
		<button type="button" id="btnCerrar" class="btn btn-light"
			data-dismiss="modal"><b>CERRAR</b></button>
		<button type="button" class="btn btn-primary" onclick="'.$function.'">
			<b>GUARDAR</b>
		</button>
	</div>';
}

function f_total_vehiculos($dbConn,$Id){
	try {
		$sql= $dbConn->prepare("SELECT * FROM tbl_a_vehiculo WHERE trnId = :id AND vhcEstado = 0");
		$sql->bindValue(":id",$Id);
		$sql->execute();
        $cont= 0;
		while($row = $sql->fetch()) $cont++;
		return $cont;
	}  catch (Exception $e) {
		Insert_Error('ERROR-65466',$e->getMessage(),$Error);
		exit("ERROR-65466");
	}
}
?>