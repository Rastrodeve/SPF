<?php
if (isset($_REQUEST['op'])) {
	require '../../FilePHP/utils.php';
	$dbConn = conectar($db);
	$op=$_REQUEST['op'];
	if ($op==1) echo data_table_enfermedades($dbConn);
    elseif($op==2) echo get_data_new();
    elseif($op==3) echo f_insert($dbConn);
    elseif($op==4) echo f_update_estado($dbConn);
    elseif($op==5) echo get_data_update($dbConn);
    elseif($op==6) echo f_update($dbConn);//Yes -- data_predeterminado($dbConn); 
    
}else header("location: ./");


function data_table_enfermedades($dbConn){
	$resultado='<table class="mt-2 table table-sm table-bordered table-striped "  id="table-data" style="text-align:center;">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Enfermedad</th>
                            <th>Estado</th>
                            <th>Editar</th>
                        </tr>
                    </thead><tbody>';
    $consulta="SELECT * FROM tbl_a_enfermedad";
	$sql= $dbConn->prepare($consulta);
	$sql->execute();
	$cont = 0;
	$Total=0;
	while ($row = $sql->fetch()) {
		$cont++;
		$estado ="ERROR";
		if ($row["enfEstado"]==0) {
			$estado ='<button type="button" onclick="f_estado(1,'.$row["enfId"].')" class="btn btn-success btn-sm"><b>ACTIVO</b></button>';
		}else if ($row["enfEstado"]==1){
			$estado ='<button type="button" onclick="f_estado(0,'.$row["enfId"].')" class="btn btn-danger btn-sm"><b>INACTIVO</b></button>';
		}
		$resultado=$resultado.'
            <tr>
                <td>'.$cont.'</td>
                <td >'.utf8_encode($row["enfDescripcion"]).'</td>
                <td >'.$estado.'</td>
                <td>
                    <button  type="button" class="btn btn-sm btn-info" data-toggle="modal"
                    data-target="#modal"  onclick="get_data_update('.$row["enfId"].')" >
                        <b><i class="fas fa-edit"></i></b>
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
					<h6 class="text-muted"><b>AÑADIR NUEVA ENFERMEDAD</b> </h6>
				</div>
			</div>
			<hr>
			<div class="row">
				<div class="col-md-8">
					<label for="txtDescripcion">Nombre de la enfermedad:</label><span class="text-muted">(max 30)</span>
					<input type="text" class="form-control form-control-sm" maxlength="30" id="txtDescripcion" placeholder="Brucelosis">
				</div>
			</div>';
	return modal($data,'AÑADIR NUEVA ENFERMEDAD','f_new()');
}
function get_data_update($dbConn){
	$Id = $_POST['Id'];
	$consulta=" SELECT * FROM tbl_a_enfermedad WHERE enfId = :id";
	$sql= $dbConn->prepare($consulta);
	$sql->bindValue(':id',$Id);
	$sql->execute();
	if($row = $sql->fetch()) {
		$data = '
		<input type="hidden" value="'.$row["enfId"].'" id="txtIdEnfermedad">
		<div class="row">
			<div class="col-md-12">
				<h6 class="text-muted"><b>EDITAR ENFERMEDAD</b> </h6>
			</div>
		</div>
		<hr>
		<div class="row">
			<div class="col-md-8">
				<label for="txtDescripcion">Nombre actual de la enfermedad:</label>
				<span class="text-muted form-control form-control-sm">'.utf8_encode($row["enfDescripcion"]).'</span>
			</div>
		</div>
		<hr>
		<div class="row">
			<div class="col-md-8">
				<label for="txtDescripcion">Nuevo nombre de la enfermedad:</label><span class="text-muted">(max 30)</span>
				<input type="text" class="form-control form-control-sm" maxlength="30" id="txtDescripcion" placeholder="Brucelosis" value="'.utf8_encode($row["enfDescripcion"]).'">
			</div>
		</div>';
		return modal($data,'ACTUALIZAR ENFERMEDAD','f_update()');
	}else return modal("ERROR-7765542","ERROR-7765542",'error_f()');//NO SE ENCONTRO EL ID
}

//Insert
function f_insert($dbConn){
	$Enfermedad = $_POST['Enfermedad'];
	$consulta="SELECT * FROM tbl_a_enfermedad WHERE enfDescripcion = :enfermedad";
	$sql= $dbConn->prepare($consulta);
	$sql->bindValue(':enfermedad',$Enfermedad);
	$sql->execute();
	if($row = $sql->fetch()) {
		return 'El nombre de la enfermedad ya se encuentra registrada';
	}else{
		$consulta = "INSERT INTO tbl_a_enfermedad(enfDescripcion) VALUES(:enfDescripcion)";
		$array =  array(array(":enfDescripcion"), array($Enfermedad));
		$Error ="Insertar nueva enfermedad";
		$Acion ="Insertar nueva enfermedad";
		$detalle='Nombre de la enfermedad <b>'.$Enfermedad.'</b>';
		return f_consulta($dbConn,$array,$consulta,$Error,$detalle,$Acion,0);
	}
}

//UPDATE
function f_update_estado($dbConn){
	$Id = $_POST['Id'];
	$Estado = $_POST['Estado'];
	$consulta="SELECT * FROM tbl_a_enfermedad WHERE enfId = :id";
	$sql= $dbConn->prepare($consulta);
	$sql->bindValue(':id',$Id);
	$sql->execute();
	if($row = $sql->fetch()) {
		$detalle = "";
		$Acion = "";
		if ($Estado==0){
			$Acion = "Estado de la enfermedad";
			$detalle = "Se activo la enfermedad (".utf8_encode($row["enfDescripcion"]).")";
		}else if ($Estado==1){
			$Acion = "Estado de la enfermedad";
			$detalle = "Se desactivo la enfermedad (".utf8_encode($row["enfDescripcion"]).")";
		}else {
			return "No se encontro el estado de la enfermedad";
		}
		$array =  array(array(":estado",":id"),array($Estado,$Id));
		$consulta = "UPDATE tbl_a_enfermedad SET enfEstado = :estado WHERE enfId = :id";
		$Error = "Error al actualizar el estado de la enfermedad (".$Estado.")";
        return f_consulta($dbConn,$array,$consulta,$Error,$detalle,$Acion,$Id);
	}else return "ERROR-7765542";//NO SE ENCONTRO EL ID
}
function f_update($dbConn){
	$Enfermedad = $_POST['Enfermedad'];
	$Id = $_POST['Id'];
	$consulta="SELECT * FROM tbl_a_enfermedad WHERE enfId = :id";
	$sql= $dbConn->prepare($consulta);
	$sql->bindValue(':id',$Id);
	$sql->execute();
	if($row = $sql->fetch()) {
		if (f_comprobar($dbConn,array(array(":enfDescripcion",":id"),array($Enfermedad,$Id)),"SELECT * FROM tbl_a_enfermedad WHERE  enfDescripcion = :enfDescripcion  AND enfId != :id","ERROR-28211")) {
			return 'El nombre de la enfermedad seleccionado ya se encuentra registrado';
		}else{
			$consulta = "UPDATE tbl_a_enfermedad SET enfDescripcion = :enfDescripcion WHERE enfId = :id ";
			$array =  array(array(":enfDescripcion",":id"), array($Enfermedad,$Id));
			$Error ="Actualización de enfermedad";
			$Acion ="Actualización de enfermedad";
			$detalle='Nombre de la enfermedad actual <b>'.utf8_encode($row["enfDescripcion"]).'</b><br>
					Nuevo nombre de la enfermedad <b>'.$Enfermedad.'</b>';
			return f_consulta($dbConn,$array,$consulta,$Error,$detalle,$Acion,$Id);
		}
	}else return 'ERROR-1212';
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

?>