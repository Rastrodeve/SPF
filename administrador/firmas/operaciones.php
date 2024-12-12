<?php
$ArrayModulos  = array(0 => 'Sin definir',
1 => 'Recepción de ganado',
2 => 'Orden de producción',
3 => 'Orden de producción Emergente',
4 => 'Acta de decomiso',
5 => 'Saldo y Faenamiento',
6 => 'Corralaje',
7 => 'Tasas');
if (isset($_REQUEST['op'])) {
	require '../../FilePHP/utils.php';
	$dbConn = conectar($db);
	$op=$_REQUEST['op'];
	if ($op==1) echo list_data_firmas($dbConn);//Yes
    elseif($op==2) echo get_data_new_firma($dbConn);//Yes 
    elseif($op==3) echo  f_insert_firma($dbConn);//Yes 
    elseif($op==4) echo get_data_update_firma($dbConn);//Yes 
    elseif($op==5) echo f_update_firma($dbConn);//Yes
    elseif($op==6) echo f_update_estado_firma($dbConn);//Yes 
    elseif($op==7) echo Consultar_Datos_Firmas($dbConn,$_POST["Id"]);//Yes 
    elseif($op==8) echo f_update_estado_ignorar($dbConn);//
    elseif($op==9) echo get_data_modi_firma($dbConn);//
    elseif($op==10) echo f_update_tipo_frima($dbConn);//
}else header("location: ./");

function list_data_firmas($dbConn){
	$resultado="";
    $Titulo = "NO SE ENCONTRO EL MODULO";
    global $ArrayModulos;
    for ($i=0; $i < count($ArrayModulos) ; $i++) { 
        $Titulo = $ArrayModulos[$i];
        $table = Consultar_Datos_Firmas($dbConn,$i);
        $resultado .='
            <div class="row">
                <div class="col-md-12">
                    <div class="card collapsed-card">
                        <div class="card-header" data-card-widget="collapse" data-toggle="tooltip" title="Collapse" style="cursor: pointer;" >
                            <h1 class="card-title"  >
                                <b>'.$Titulo.'</b>
                            </h1>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12" id="conttable-'.$i.'" >'.$table.'</div>
                            </div>
                            <button type="button"  class="btn btn-info btn-sm" data-toggle="modal"
                            data-target="#modal" onclick="get_data_new_firma('.$i.')"	>
                                <b><i class="fas fa-plus"></i> AÑADIR FIRMAR</b>
                            </button>
                        </div>
                    </div>
                    <hr>
                </div>
            </div>';
    }
	return $resultado;

}
function Consultar_Datos_Firmas($dbConn,$Id){
	$resultado='<table class="mt-2 table table-sm table-bordered table-striped " style="text-align:center;">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Nombre</th>
                            <th>Descripción 1</th>
                            <th>Descripción 2</th>
                            <th>Estado</th>
                            <th>Ignorar</th>
                            <th>Editar</th>
                        </tr>
                    </thead><tbody>';
    $consulta="SELECT * FROM tbl_a_firma WHERE firTipo = :id ORDER BY firOrden ASC";
	$sql= $dbConn->prepare($consulta);
	$sql->bindValue(':id',$Id);
	$sql->execute();
	$cont = 0;
	$Total=0;
	while ($row = $sql->fetch()) {
		$cont++;
		$estado ="ERROR";
        $predetmiando = "ERROR";
		if ($row["firEstado"]==0) {
			$estado ='<button type="button" onclick="f_estado_firma(1,'.$row["firId"].','.$row["firTipo"].')" class="btn btn-success btn-sm"><b>ACTIVO</b></button>';
		}else if ($row["firEstado"]==1){
			$estado ='<button type="button" onclick="f_estado_firma(0,'.$row["firId"].','.$row["firTipo"].')" class="btn btn-danger btn-sm"><b>INACTIVO</b></button>';
		}
        if ($row["firInformacion"]==1) {
			$predetmiando ='<button type="button" onclick="f_estado_ignorar(0,'.$row["firId"].','.$row["firTipo"].')" class="btn btn-danger btn-sm"><b>SI</b></button>';
		}else if ($row["firInformacion"]==0){
			$predetmiando ='<button type="button" onclick="f_estado_ignorar(1,'.$row["firId"].','.$row["firTipo"].')" class="btn btn-success btn-sm"><b>NO</b></button>';
		}
		$resultado=$resultado.'
            <tr>
                <td>
                    <span class="btn btn-dark btn-sm" data-toggle="modal"
                    data-target="#modal"  onclick="get_data_update_mover('.$row["firId"].')" >
                    '.$cont.'
                    </span>    
                </td>
                <td >'.utf8_encode($row["firNombre"]).'</td>
                <td >'.utf8_encode($row["firDescripcion1"]).'</td>
                <td >'.utf8_encode($row["firDescripcion2"]).'</td>
                <td >'.$estado.'</td>
                <td>'.$predetmiando.'</td>
                <td>
                    <button  type="button" class="btn btn-sm btn-info" data-toggle="modal"
                    data-target="#modal"  onclick="get_data_update_firma('.$row["firId"].')" >
                        <b><i class="fas fa-edit"></i></b>
                    </button>
                </td>
            </tr';
	}

	return $resultado."</tbody></table>";
}
//GET DATA
function get_data_new_firma($dbConn){
    $Id = $_POST["Id"];
    $Titulo = "NO SE ENCONTRO EL MODULO";
    global $ArrayModulos;
    $cont = 0; 
    for ($i=0; $i < count($ArrayModulos) ; $i++) { 
        if ($i== $Id){
            $Titulo = $ArrayModulos[$i];
            $cont++;
        }
    }
    if ($cont==1) {
        $data = '
		<input type="hidden" value="'.$Id.'" id="txtId">
		<div class="row">
			<div class="col-md-12">
                <h6 class="text-muted"><b>Nueva firma para "'.$Titulo.'" </b> </h6>
			</div>
		</div>
		<hr class="mt-2">
		<div class="row">
			<div class="col-md-12">
				<label for="txtNombre">Nombre a mostrar: </label><span class="text-muted ml-1">(max 60)</span>
				<input type="text" class="form-control form-control-sm" maxlength="60" id="txtNombre" placeholder="Ing. Nombre y apellido">
			</div>
		</div>
        <hr>
        <div class="row">
			<div class="col-md-12">
				<label for="txtDescripxion1">Descripción 1: </label><span class="text-muted ml-1">(max 60)</span>
				<input type="text" class="form-control form-control-sm" maxlength="60" id="txtDescripxion1" placeholder="CARGO EN LA EMPRESA">
			</div>
		</div>
        <hr>
        <div class="row">
            <div class="col-md-12">
                <label for="txtDescripxion2">Descripción 2: </label><span class="text-muted ml-1">(max 100)</span>
                <input type="text" class="form-control form-control-sm" maxlength="100" id="txtDescripxion2" placeholder="OTRA INFORMACIÓN">
            </div>
		</div>
        <hr>
        <div class="row">
            <div class="col-md-6">
				<label for="slcOrden">Orden de la firma:</label>
                <div class="input-group mb-3 input-group-sm">
                    <select class="form-control form-control-sm select2bs4" style="width:100%"  id="slcOrden">
                        '.selecoption($dbConn,$Id,-1).'
                    </select>
                </div>
			</div>
		</div>';
		return modal($data,'AÑADIR NUEVA FIRMA','f_new_firma()');
    }else return modal("ERROR-7765542","ERROR-7765542",'error_f()');//NO SE ENCONTRO EL ID
}
function get_data_update_firma($dbConn){
    $Id = $_POST['Id'];
	$consulta="SELECT * FROM tbl_a_firma WHERE firId = :id";
	$sql= $dbConn->prepare($consulta);
	$sql->bindValue(':id',$Id);
	$sql->execute();
    $bandera = false;
	if($row = $sql->fetch()) {
        $Titulo = "NO SE ENCONTRO EL MODULO";
        global $ArrayModulos;
        $cont = 0; 
        for ($i=0; $i < count($ArrayModulos) ; $i++) { 
            if ($i==$row["firTipo"])$Titulo = $ArrayModulos[$i];
        }
        $data = '
		<input type="hidden" value="'.$row["firId"].'" id="txtId">
		<input type="hidden" value="'.$row["firTipo"].'" id="txtIdTipo">
		<div class="row">
			<div class="col-md-12">
                <h6 class="text-muted"><b>Firma de "'.$Titulo.'" </b> </h6>
			</div>
		</div>
		<hr class="mt-2">
		<div class="row">
			<div class="col-md-12">
				<label for="txtNombre">Nombre actual a mostrar: </label>
                <span class="text-muted form-control form-control-sm">'.utf8_encode($row["firNombre"]).'</span>
			</div>
		</div>
        <hr>
        <div class="row">
			<div class="col-md-12">
				<label for="txtNombre">* Nuevo nombre a mostrar: </label><span class="text-muted ml-1">(max 60)</span>
				<input type="text" class="form-control form-control-sm" maxlength="60" id="txtNombre" placeholder="Ing. Nombre y apellido" value="'.utf8_encode($row["firNombre"]).'">
			</div>
		</div>
        <hr>
        <div class="row">
            <div class="col-md-12">
                <label for="txtDescripxion1">Descripción 1 actual: </label>
                <span class="text-muted form-control form-control-sm">'.utf8_encode($row["firDescripcion1"]).'</span>
            </div>
        </div>
        <hr>
        <div class="row">
			<div class="col-md-12">
				<label for="txtDescripxion1">* Descripción 1: </label><span class="text-muted ml-1">(max 60)</span>
				<input type="text" class="form-control form-control-sm" maxlength="60" id="txtDescripxion1" placeholder="CARGO EN LA EMPRESA" value="'.utf8_encode($row["firDescripcion1"]).'">
			</div>
		</div>
        <hr>
        <div class="row">
            <div class="col-md-12">
                <label for="txtDescripxion2">Descripción 2: </label>
                <span class="text-muted form-control form-control-sm">'.utf8_encode($row["firDescripcion2"]).'</span>
            </div>
		</div>
        <hr>
        <div class="row">
            <div class="col-md-12">
                <label for="txtDescripxion2">* Descripción 2: </label><span class="text-muted ml-1">(max 100)</span>
                <input type="text" class="form-control form-control-sm" maxlength="100" id="txtDescripxion2" placeholder="OTRA INFORMACIÓN" value="'.utf8_encode($row["firDescripcion2"]).'">
            </div>
        </div>
        <hr>
        <div class="row">
            <div class="col-md-6">
                <label for="slcOrden">Orden actual de la firma:</label>
                <div class="input-group mb-3 input-group-sm">
                    <span class="text-muted form-control form-control-sm">'.$row["firOrden"].'</span>
                </div>
            </div>
            <div class="col-md-6">
				<label for="slcOrden">* Nuevo orden de la firma:</label>
                <div class="input-group mb-3 input-group-sm">
                    <select class="form-control form-control-sm select2bs4" style="width:100%" id="slcOrden">
                        '.selecoption($dbConn,$row["firTipo"],$row["firOrden"]).'
                    </select>
                </div>
			</div>
		</div>';
		return modal($data,'ACTUALIZAR FIRMA','f_update_firma()');
    }else return modal("ERROR-7765542","ERROR-7765542",'error_f()');//NO SE ENCONTRO EL ID
}
function get_data_modi_firma($dbConn){
    $Id = $_POST['Id'];
	$consulta="SELECT * FROM tbl_a_firma WHERE firId = :id";
	$sql= $dbConn->prepare($consulta);
	$sql->bindValue(':id',$Id);
	$sql->execute();
    $bandera = false;
	if($row = $sql->fetch()) {
        $Titulo = "NO SE ENCONTRO EL MODULO";
        global $ArrayModulos;
        $option="";
        for ($i=0; $i < count($ArrayModulos) ; $i++) { 
            if ($i == $row["firTipo"]){
                $selected = 'selected';
                $Titulo = $ArrayModulos[$i];
            }else $selected = '';
            $option .= '<option value="'.$i.'" '.$selected.'>'.$ArrayModulos[$i].'</option>';
        }
        $data = '
		<div class="row">
			<div class="col-md-12 text-muted">
                <h6><b>INFORMACIÓN DE LA FIRMA</b> </h6>
                <h6>Firma de "'.$Titulo.'"</h6>
                <h6>Nombre: '.utf8_encode($row["firNombre"]).'</h6>
                <h6>Descripción 1: '.utf8_encode($row["firDescripcion1"]).'</h6>
                <h6>Descripción 2: '.utf8_encode($row["firDescripcion2"]).'</h6>
			</div>
		</div>
		<hr class="mt-2">
		<div class="row">
            <div class="col-md-12">
                <label for="slcTipo">Seleccione uno para mover la firma:</label>
                <div class="input-group mb-3 input-group-sm">
                    <select class="form-control form-control-sm select2bs4" style="width:100%" id="slcTipo" onchange="f_move_firma('.$row["firId"].')">
                        '.$option.'
                    </select>
                </div>
            </div>
		</div>';
		return '
        <div class="modal-header bg-secondary">
            <h5 class="modal-title" id="modalLabel">
                <b>MOVER FIRMA</b>
            </h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body">
            '.$data.'
        </div>
        <button type="button" id="btnCerrar" class="btn btn-light d-none"
			data-dismiss="modal"><b>CERRAR</b></button>';
    }else return modal("ERROR-7765542","ERROR-7765542",'error_f()');//NO SE ENCONTRO EL ID
}

//Insert
function f_insert_firma($dbConn){
	$Id = $_POST['Id'];
	$Nombre = trim($_POST['Nombre']);
	$Descp1 = trim($_POST['Descrip1']);
	$Descp2 = trim($_POST['Descrip2']);
	$Orden = trim($_POST['Orden']);
	$consulta="SELECT * FROM tbl_a_firma WHERE firTipo = :id";
	$sql= $dbConn->prepare($consulta);
	$sql->bindValue(':id',$Id);
	$sql->execute();
    $bandera = false;
	while($row = $sql->fetch()) {
        if ($Orden == $row["firOrden"]) $bandera = true;
	}
    $res = f_update_orden($dbConn,$Orden,$Id);
    if ($res == true) {
        $consulta = "INSERT INTO tbl_a_firma(firTipo,firOrden,firNombre,firDescripcion1,firDescripcion2)
                VALUES (:firTipo,:firOrden,:firNombre,:firDescripcion1,:firDescripcion2)";
        $array =  array(array(":firTipo",":firOrden",":firNombre",":firDescripcion1",":firDescripcion2"),
                        array($Id,$Orden,$Nombre,$Descp1,$Descp2));
        $Error ="Nueva firma";
        $Acion ="Nueva firma";
        $Titulo = "NO SE ENCONTRO EL MODULO";
        global $ArrayModulos;
        for ($i=0; $i < count($ArrayModulos) ; $i++) { 
            if ($i== $Id)$Titulo = $ArrayModulos[$i];
        }
        $detalle='<b>'.$Titulo.'</b><br>'.
            'Nombre: '.$Nombre.' <br>'.
            'Descripción 1  : '.$Descp1.'<br>'.
            'Descripción 2  : '.$Descp1.'<br>'.
            'Orden  : '.$Orden.'<br>';
        return f_consulta($dbConn,$array,$consulta,$Error,$detalle,$Acion,0);
    }else return $res;
}

//UPDATE
function f_update_firma($dbConn){
	$Id = $_POST['Id'];
	$Nombre = trim($_POST['Nombre']);
	$Descp1 = trim($_POST['Descrip1']);
	$Descp2 = trim($_POST['Descrip2']);
	$Orden = trim($_POST['Orden']);
	$consulta="SELECT * FROM tbl_a_firma WHERE firId = :id";
	$sql= $dbConn->prepare($consulta);
	$sql->bindValue(':id',$Id);
	$sql->execute();
    $bandera = false;
	if($row = $sql->fetch()) {
        $res = f_update_orden($dbConn,$Orden,$Id);
        if ($res == true) {
            $consulta = "UPDATE tbl_a_firma SET firOrden = :firOrden, firNombre = :firNombre,firDescripcion1 = :firDescripcion1 ,firDescripcion2 = :firDescripcion2
            WHERE firId = :id ";
            $array =  array(array(":firOrden",":firNombre",":firDescripcion1",":firDescripcion2",":id"),
                            array($Orden,$Nombre,$Descp1,$Descp2,$Id));
            $Error ="Actualizar firma";
            $Acion ="Actualizar firma";
            $Titulo = "NO SE ENCONTRO EL MODULO";
            global $ArrayModulos;
            for ($i=0; $i < count($ArrayModulos) ; $i++) { 
                if ($i== $row["firOrden"])$Titulo = $ArrayModulos[$i];
            }
            $detalle='<b>'.$Titulo.'</b><br>'.
                'Nombre [ '.utf8_encode($row["firNombre"]).' ] = > [ ' .$Nombre.'  ] <br>'.
                'Descripción 1  [ '.utf8_encode($row["firDescripcion1"]).' ] = > [ '.$Descp1.' ] <br>'.
                'Descripción 2  [ '.utf8_encode($row["firDescripcion2"]).' ] = > [ '.$Descp1.' ]<br>'.
                'Orden  [ '.$row["firOrden"].' ] = > [ '.$Orden.' ]<br>';
            return f_consulta($dbConn,$array,$consulta,$Error,$detalle,$Acion,$Id);
        }else return $res;
	}else return 'ERRORR-12981';
}
function f_update_estado_firma($dbConn){
	$Id = $_POST['Id'];
	$Estado = $_POST['Estado'];
	$consulta="SELECT * FROM tbl_a_firma WHERE firId = :id";
	$sql= $dbConn->prepare($consulta);
	$sql->bindValue(':id',$Id);
	$sql->execute();
	if($row = $sql->fetch()) {
        $Titulo = "NO SE ENCONTRO EL MODULO";
        global $ArrayModulos;
        for ($i=0; $i < count($ArrayModulos) ; $i++) { 
            if ($i== $row["firOrden"])$Titulo = $ArrayModulos[$i];
        }
		$detalle = "";
		$Acion = "";
		if ($Estado==0){
			$Acion = "Estado de la firma";
			$detalle = "Se activo la firma (".utf8_encode($row["firNombre"]).") => ".$Titulo;
		}else if ($Estado==1){
			$Acion = "Estado de la firma";
			$detalle = "Se desactivo la firma(".utf8_encode($row["firNombre"]).") => ".$Titulo;
		}else {
			return "No se encontro el estado del producto";
		}
		$array =  array(array(":estado",":id"),
						array($Estado,$Id));
		$consulta = "UPDATE tbl_a_firma SET firEstado = :estado WHERE firId = :id";
		$Error = "Error al actualizar el estado de la firma (".$Estado.")";
        return f_consulta($dbConn,$array,$consulta,$Error,$detalle,$Acion,$Id);
	}else return "ERROR-7765542";//NO SE ENCONTRO EL ID
}
function f_update_estado_ignorar($dbConn){
	$Id = $_POST['Id'];
	$Estado = $_POST['Estado'];
	$consulta="SELECT * FROM tbl_a_firma WHERE firId = :id";
	$sql= $dbConn->prepare($consulta);
	$sql->bindValue(':id',$Id);
	$sql->execute();
	if($row = $sql->fetch()) {
        $Titulo = "NO SE ENCONTRO EL MODULO";
        global $ArrayModulos;
        for ($i=0; $i < count($ArrayModulos) ; $i++) { 
            if ($i== $row["firOrden"])$Titulo = $ArrayModulos[$i];
        }
		$detalle = "";
		$Acion = "";
		if ($Estado==0){
			$Acion = "Ignorar firma";
			$detalle = "La firma  de (".utf8_encode($row["firNombre"]).") => ".$Titulo." <b>NO</b> será ignorada";
		}else if ($Estado==1){
			$Acion = "Ignorar firma";
			$detalle = "La firma  de (".utf8_encode($row["firNombre"]).") => ".$Titulo." <b>SI</b> será ignorada";
		}else {
			return "No se encontro el estado del producto";
		}
		$array =  array(array(":estado",":id"),
						array($Estado,$Id));
		$consulta = "UPDATE tbl_a_firma SET firInformacion = :estado WHERE firId = :id";
		$Error = "Error al actualizar el estado de ignorar la firma (".$Estado.")";
        return f_consulta($dbConn,$array,$consulta,$Error,$detalle,$Acion,$Id);
	}else return "ERROR-7765542";//NO SE ENCONTRO EL ID
}
function f_update_tipo_frima($dbConn){
	$Id = $_POST['Id'];
	$Estado = $_POST['Tipo'];
	$consulta="SELECT * FROM tbl_a_firma WHERE firId = :id";
	$sql= $dbConn->prepare($consulta);
	$sql->bindValue(':id',$Id);
	$sql->execute();
	if($row = $sql->fetch()) {
        $Titulo = "NO SE ENCONTRO EL MODULO";
        $Titulo2 = "NO SE ENCONTRO EL MODULO";
        global $ArrayModulos;
        for ($i=0; $i < count($ArrayModulos) ; $i++) { 
            if ($i== $row["firTipo"])$Titulo = $ArrayModulos[$i];
            if ($i== $Estado)$Titulo2 = $ArrayModulos[$i];
        }
		$Acion = "Mover firma";
        $orden = get_num_orden($dbConn,$Estado);
		$detalle = "La firma  de (".utf8_encode($row["firNombre"]).") => ".$Titulo." se movio a <b>".$Titulo2."</b>";
		$array =  array(array(":estado",":orden",":id"),
						array($Estado,$orden,$Id));
		$consulta = "UPDATE tbl_a_firma SET firTipo = :estado, firOrden = :orden WHERE firId = :id";
		$Error = "Error al actualizar al mover la firma (".$Estado.")";
        return f_consulta($dbConn,$array,$consulta,$Error,$detalle,$Acion,$Id);
	}else return "ERROR-7765542";//NO SE ENCONTRO EL ID
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
			if(Insert_Login($Id,'tbl_a_firma',$Acion,$detalle,'')) return true;
			else return 'ERROR-092222';
		}else return "ERROR-665242";//
	}  catch (Exception $e) {
		Insert_Error('ERROR-887222',$e->getMessage(),$Error);
		exit("ERROR-887222");
	}
}

function selecoption($dbConn,$id,$number_select){
    $resultado = '';
    $sql= $dbConn->prepare('SELECT * FROM tbl_a_firma WHERE firTipo = :id ORDER BY firOrden ASC');
    $sql->bindValue(":id",$id);
    $sql->execute();
    $cont = 0;
    while($row = $sql->fetch()){
        $cont++;
        if ($number_select == $row["firOrden"]) $selected = 'selected';
        else $selected = '';
        $resultado .= '<option value="'.$cont.'" '.$selected.'>'.$cont.'</option>';
    }
    if ($number_select == -1) $ultimo  = '<option value="'.($cont + 1).'" selected>Último</option>';
    else $ultimo  = '';
	return $resultado.$ultimo;
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
function f_update_orden($dbConn,$Num,$Id){
	$consulta="SELECT * FROM tbl_a_firma WHERE firTipo = :id";
	$sql= $dbConn->prepare($consulta);
	$sql->bindValue(':id',$Id);
	$sql->execute();
    $bandera = false;
    $cont = 0;
    $Error = "";
	while($row = $sql->fetch()) {
        if ($Num <= $row["firOrden"]){
            $consulta1="UPDATE tbl_a_firma SET  firOrden = :num WHERE firId = :id";
            $sql1= $dbConn->prepare($consulta1);
            $sql1->bindValue(':num', ($row["firOrden"] + 1));
            $sql1->bindValue(':id', $row["firId"]);
            if ($sql1->execute());
            else $cont++;//NO SE PUEDDO ACTUALIZAR EL USUARIO  
        }
	}
    if ($cont==0)return true;
    else return 'ERROR-99222';//NO SE PUDO ACTUALIZAR TODO
}
function get_num_orden($dbConn,$Tipo){
	$consulta="SELECT * FROM tbl_a_firma WHERE firTipo = :id";
	$sql= $dbConn->prepare($consulta);
	$sql->bindValue(':id',$Tipo);
	$sql->execute();
    $bandera = false;
    $cont = 0;
    $Error = "";
	while($row = $sql->fetch()) {
        $cont++;
	}
    return $cont + 1;
}
function number($str){
	try{
		$array = explode(".",$str);
		$enteros = $array[0];
		$decimales="";
		if (isset($array[1])) {
			$decimales = $array[1];
		}else{
			$decimales = "00";
		}
		$arryenteros = str_split($enteros);
		$tam = count($arryenteros);
		$numero = "";
		$cont1 = 0;
		for ($i = ($tam-1); $i >=0 ; $i--) { 
			$numero .= $arryenteros[$i];
			$cont1++;
			if ($cont1==3) {
				$numero .= '.';    
				$cont1=0;
			}
		}
		$nuevo_array =str_split($numero);
		$tam_n = count($nuevo_array);
		$nuevo_numero ="";
		for ($i=($tam_n-1); $i >=0 ; $i--) { 
			$id=$tam_n-1;
			if ($i == $id) {
				if ($nuevo_array[$i]!=".") {
					$nuevo_numero .= $nuevo_array[$i];  
				}
			}else{
				$nuevo_numero .= $nuevo_array[$i];  
			}
		}
		return $nuevo_numero.",".$decimales;
	}
	catch (Exception $e){
			return "Erro1 ".$e ;
	}
}
?>