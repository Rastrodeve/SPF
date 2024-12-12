<?php
if (isset($_REQUEST['op'])) {
	require '../../FilePHP/utils.php';
	$dbConn = conectar($db);
	$op=$_REQUEST['op'];
	if ($op==1) echo Listar_Especie_Animales($dbConn);//Yes
    elseif($op==2) echo get_data_new_producto($dbConn);//Yes -- data_nuevo($dbConn)
    elseif($op==3) echo f_insert_producto($dbConn);//Yes -- Insertar_Producto($dbConn)
    elseif($op==4) echo f_update_estado_producto($dbConn);//Yes data_edit($dbConn); 
    elseif($op==5) echo Consultar_Datos_productos($dbConn,$_POST["Id"]);//Yes --- Update_Producto($dbConn)
    elseif($op==6) echo get_data_update_producto($dbConn);//Yes -- data_predeterminado($dbConn); 
    elseif($op==7) echo f_update_producto($dbConn);//Yes --- update_predeterminado($dbConn); 
    elseif($op==8) echo f_update_producto_predeterminado($dbConn);//update_estado_producto($dbConn)
    elseif($op==9) echo get_data_gancho($dbConn);
    elseif($op==10) echo get_data_new_gancho();
    elseif($op==11) echo f_insert_gancho($dbConn);
    elseif($op==12) echo get_list_ganchos($dbConn);
    elseif($op==13) echo f_update_estado_gancho($dbConn);
    elseif($op==14) echo get_data_update_gancho($dbConn);
    elseif($op==15) echo f_update_gancho($dbConn);
}else header("location: ./");

function Listar_Especie_Animales($dbConn){
	$resultado="";
	$consulta="SELECT * FROM tbl_a_especies";
	$sql= $dbConn->prepare($consulta);
	$sql->execute();
	while ($row = $sql->fetch()) {
        $table = Consultar_Datos_productos($dbConn,$row["espId"]);
        $resultado .='
            <div class="row">
                <div class="col-md-12">
                    <div class="card collapsed-card">
                        <div class="card-header" data-card-widget="collapse" data-toggle="tooltip" title="Collapse" style="cursor: pointer;" >
                            <h1 class="card-title"  >
								<b>'.strtoupper(utf8_encode($row["espDescripcion"])).' <span class="text-muted">('.$row["espLetra"].')</span></b>
                            </h1>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12" id="conttable-'.$row["espId"].'" >'.$table.'</div>
                            </div>
                            <button type="button"  class="btn btn-info btn-sm" data-toggle="modal"
                            data-target="#modal" onclick="get_data_new_producto('.$row["espId"].')"	>
                                <b><i class="fas fa-plus"></i> AÑADIR PRODUCTO</b>
                            </button>
                        </div>
                    </div>
					<hr>
                </div>
            </div>';
	}
	return $resultado;

}
function Consultar_Datos_productos($dbConn,$Id){
	$resultado='<table class="mt-2 table table-sm table-bordered table-striped " style="text-align:center;">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Código</th>
                            <th>Descripción</th>
                            <th>Partes</th>
                            <th>Vencimiento</th>
                            <th>Conservar</th>
                            <th>Estado</th>
                            <th>Predeterminado</th>
                            <th>Editar</th>
                        </tr>
                    </thead><tbody>';
    $consulta="SELECT * FROM tbl_a_productos WHERE espId = :id";
	$sql= $dbConn->prepare($consulta);
	$sql->bindValue(':id',$Id);
	$sql->execute();
	$cont = 0;
	$Total=0;
	while ($row = $sql->fetch()) {
		$cont++;
		$estado ="ERROR";
        $predetmiando = "ERROR";
		if ($row["proEliminado"]==0) {
			$estado ='<button type="button" onclick="f_estado_producto(1,'.$row["proId"].','.$row["espId"].')" class="btn btn-success btn-sm"><b>ACTIVO</b></button>';
		}else if ($row["proEliminado"]==1){
			$estado ='<button type="button" onclick="f_estado_producto(0,'.$row["proId"].','.$row["espId"].')" class="btn btn-danger btn-sm"><b>INACTIVO</b></button>';
		}
        if ($row["proPredeterminado"]==1) {
			$predetmiando ='<button type="button" onclick="f_predeterminado(0,'.$row["proId"].','.$row["espId"].')" class="btn btn-success btn-sm"><b>SI</b></button>';
		}else if ($row["proPredeterminado"]==0){
			$predetmiando ='<button type="button" onclick="f_predeterminado(1,'.$row["proId"].','.$row["espId"].')" class="btn btn-danger btn-sm"><b>NO</b></button>';
		}
		$resultado=$resultado.'
            <tr>
                <td>'.$cont.'</td>
                <td >'.$row["proCodigo"].'</td>
                <td >'.utf8_encode($row["proDescripcion"]).'</td>
                <td >'.$row["proPartes"].'</td>
                <td >'.$row["proFechaVen"].' días</td>
                <td >'.$row["proConservar"].' °C</td>
                <td >'.$estado.'</td>
                <td>'.$predetmiando.'</td>
                <td>
                    <button  type="button" class="btn btn-sm btn-info" data-toggle="modal"
                    data-target="#modal"  onclick="get_data_update_producto('.$row["proId"].')" >
                        <b><i class="fas fa-edit"></i></b>
                    </button>
                </td>
            </tr';
	}

	return $resultado."</tbody></table>";
}
//GET DATA
function get_data_new_producto($dbConn){
	$Id = $_POST['Id'];
	$consulta="SELECT * FROM tbl_a_especies WHERE espId = :id";
	$sql= $dbConn->prepare($consulta);
	$sql->bindValue(':id',$Id);
	$sql->execute();
	if($row = $sql->fetch()) {
		$data = '
		<input type="hidden" value="'.$row["espId"].'" id="txtIdEspecie">
		<div class="row">
			<div class="col-md-12">
                <h6 class="text-muted"><b>NUEVO PRODUCTO PARA LA ESPECIE '.strtoupper(utf8_encode($row["espDescripcion"])).'</b> </h6>
			</div>
		</div>
		<hr class="mt-2">
		<div class="row">
			<div class="col-md-8">
				<label for="txtProducto">Descripción del producto:</label><span class="text-muted">(max 30)</span>
				<input type="text" class="form-control form-control-sm" maxlength="30" id="txtProducto" placeholder="CANAL BOVINO">
			</div>
            <div class="col-md-4">
				<label for="txtCodigo">Código del producto:</label><span class="text-muted">(max 4)</span>
				<input type="text" class="form-control form-control-sm input_disablecopypaste" maxlength="4" id="txtCodigo" placeholder="1234" onKeyPress="onlynumber(event)">
			</div>
		</div>
        <hr>
        <div class="row">
            <div class="col-md-5">
				<label for="txtFecha">Tiempo de vencimiento:</label><span class="text-muted">(max 4)</span>
                <div class="input-group mb-3 input-group-sm">
                    <input type="text" class="form-control form-control-sm input_disablecopypaste" maxlength="4" id="txtFecha" placeholder="100" onKeyPress="onlynumber(event)">
                    <div class="input-group-append">
                        <label class="input-group-text" for="slcSegundos">Días</label>
                    </div>
                </div>
			</div>
            <div class="col-md-3">
				<label for="slcParte">Partes:</label>
                <div class="input-group mb-3 input-group-sm">
                    <select class="custom-select" id="slcParte">
                        '.selecoption(1).'
                    </select>
                </div>
			</div>
            <div class="col-md-4">
                <label for="txtConservar">Conservar a:</label><span class="text-muted">(max 4)</span>
                <div class="input-group mb-3 input-group-sm">
                    <input type="text" class="form-control form-control-sm input_disablecopypaste" maxlength="4" id="txtConservar" placeholder="-18"  onKeyPress="onlynumber_nega(event)">
                    <div class="input-group-append">
                        <label class="input-group-text" for="slcSegundos">°C</label>
                    </div>
                </div>
			</div>
		</div>
		<hr>
        <label for="txtObservacion">Observación:</label>
        <textarea  class="form-control form-control-sm" id="txtObservacion" cols="3"></textarea>
		';
		return modal($data,'AÑADIR NUEVO PRODUCTO','f_new_producto()');
	}else return modal("ERROR-7765542","ERROR-7765542",'error_f()');//NO SE ENCONTRO EL ID
}
function get_data_update_producto($dbConn){
	$Id = $_POST['Id'];
	$consulta="SELECT * FROM tbl_a_productos p, tbl_a_especies e WHERE p.espId = e.espId AND  p.proId = :id";
	$sql= $dbConn->prepare($consulta);
	$sql->bindValue(':id',$Id);
	$sql->execute();
	if($row = $sql->fetch()) {
		$data = '
		<input type="hidden" value="'.$row["espId"].'" id="txtIdEspecie">
		<input type="hidden" value="'.$row["proId"].'" id="txtIdProducto">
		<div class="row">
			<div class="col-md-12">
			<h6 class="text-muted"><b>EDITAR PRODUCTO PARA LA ESPECIE '.strtoupper(utf8_encode($row["espDescripcion"])).'</b> </h6>
			</div>
		</div>
		<hr class="mt-2">
		<div class="row">
			<div class="col-md-6">
				<label for="txtProducto">Descripción actual del producto:</label>
                <span class="text-muted form-control form-control-sm">'.utf8_encode($row["proDescripcion"]).'</span>
			</div>
            <div class="col-md-6">
				<label for="txtProducto">Nueva descripción del producto:</label><span class="text-muted">(max 30)</span>
				<input type="text" class="form-control form-control-sm" maxlength="30" id="txtProducto" placeholder="CANAL BOVINO" value="'.utf8_encode($row["proDescripcion"]).'">
			</div>
		</div>
        <hr >
        <div class="row">
            <div class="col-md-5">
				<label for="txtCodigo">Código actual del producto:</label>
                <span class="text-muted form-control form-control-sm">'.$row["proCodigo"].'</span>
			</div>
            <div class="col-md-5">
				<label for="txtCodigo">Nuevo código del producto:</label><span class="text-muted">(max 4)</span>
				<input type="text" class="form-control form-control-sm input_disablecopypaste" maxlength="4" id="txtCodigo" placeholder="1234" onKeyPress="onlynumber(event)" value="'.$row["proCodigo"].'">
			</div>
		</div>
        <hr>
        <div class="row">
            <div class="col-md-5">
				<label for="txtFecha">Tiempo actual de caducidad:</label>
                <div class="input-group mb-3 input-group-sm">
                <span class="text-muted form-control form-control-sm">'.$row["proFechaVen"].'</span>
                    <div class="input-group-append">
                        <label class="input-group-text" for="slcSegundos">Días</label>
                    </div>
                </div>
			</div>
            <div class="col-md-5">
                <label for="txtFecha">Nuevo tiempo de caducidad:</label>
				<div class="input-group mb-3 input-group-sm">
                    <input type="text" class="form-control form-control-sm input_disablecopypaste" maxlength="4" id="txtFecha" placeholder="1234" onKeyPress="onlynumber(event)" value="'.$row["proFechaVen"].'">
                    <div class="input-group-append">
                        <label class="input-group-text" for="slcSegundos">Días</label>
                    </div>
                </div>
			</div>
		</div>
        <hr>
        <div class="row">
            <div class="col-md-3">
				<label for="txtCodigo">Parte actual:</label><span class="text-muted">(max 4)</span>
				<span class="text-muted form-control form-control-sm">'.$row["proPartes"].'</span>
			</div>
            <div class="col-md-3">
				<label for="slcParte">Nueva parte:</label>
                <div class="input-group mb-3 input-group-sm">
                    <select class="custom-select" id="slcParte">
                        '.selecoption($row["proPartes"]).'
                    </select>
                </div>
			</div>
            <div class="col-md-3">
				<label for="txtConservar">Actual conservar a :</label>
                <span class="text-muted form-control form-control-sm">'.$row["proConservar"].' °C</span>
			</div>
            <div class="col-md-3">
                <label for="txtConservar">Nuevo conservar a:</label><span class="text-muted">(max 4)</span>
                <div class="input-group mb-3 input-group-sm">
                    <input type="text" class="form-control form-control-sm input_disablecopypaste" maxlength="4" id="txtConservar" placeholder="-18"  onKeyPress="onlynumber_nega(event)" value="'.$row["proConservar"].'">
                    <div class="input-group-append">
                        <label class="input-group-text" for="slcSegundos">°C</label>
                    </div>
                </div>
			</div>
		</div>
		<hr>
        <label for="txtObservacion">Observación:</label>
        <textarea  class="form-control form-control-sm" id="txtObservacion" cols="3">'.utf8_encode($row["proObservaciones"]).'</textarea>
		';
		return modal($data,'ACTUALIZAR PRODUCTO','f_update_producto()');
	}else return modal("ERROR-7765542","ERROR-7765542",'error_f()');//NO SE ENCONTRO EL ID
}
function get_data_gancho($dbConn){
    $table = get_list_ganchos($dbConn);
    $resultado = '
        <div id="cont-data-gancho">'.$table."</div>";
	return modal_2($resultado,'Administración de Ganchos');
}
function get_list_ganchos($dbConn){
    $resultado='<table class="mt-2 table table-sm table-bordered table-striped " style="text-align:center;">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Descripción</th>
                            <th>Descuento</th>
                            <th>Estado</th>
                            <th>Editar</th>
                        </tr>
                    </thead><tbody>';
    $consulta="SELECT * FROM tbl_a_gancho";
	$sql= $dbConn->prepare($consulta);
	$sql->execute();
	$cont = 0;
	$Total=0;
	while ($row = $sql->fetch()) {
		$cont++;
		$estado ="ERROR";
		if ($row["ganEstado"]==0) {
			$estado ='<button type="button" onclick="f_estado_gancho(1,'.$row["ganId"].')" class="btn btn-success btn-sm"><b>ACTIVO</b></button>';
		}else if ($row["ganEstado"]==1){
			$estado ='<button type="button" onclick="f_estado_gancho(0,'.$row["ganId"].')" class="btn btn-danger btn-sm"><b>INACTIVO</b></button>';
		}
		$resultado = $resultado.'
            <tr>
                <td>'.$cont.'</td>
                <td >'.utf8_encode($row["ganDescripcion"]).'</td>
                <td >'.$row["ganDescuento"].'</td>
                <td >'.$estado.'</td>
                <td>
                    <button  type="button" class="btn btn-sm btn-info" onclick="get_data_update_gancho('.$row["ganId"].')" >
                        <b><i class="fas fa-edit"></i></b>
                    </button>
                </td>
            </tr';
	}
    $button = '<button class="btn btn-info btn-sm mb-3" id="btn-nuevo" ><b onclick="get_data_new_gancho()">AÑDIR GANCHO</b></button>';
    return $button.$resultado."</tbody></table>";
}
function get_data_new_gancho(){
    return '
            <button class="btn btn-info btn-sm mb-3" ><b onclick="Cargar_Datos_table_gancho()">VER TABLA</b></button>
            <div class="row">
                <div class="col-md-6">
                    <label for="txtDescripcion">Descripción: </label><span class="text-muted">(max 30)</span>
                    <input type="text" class="form-control form-control-sm " id="txtDescripcion" maxlength="30" placeholder="Gancho 1">
                </div>
                <div class="col-md-6">
                    <label for="txtDescuento">Descuento: </label><span class="text-muted">(-2,23)</span>
                    <input type="text" class="form-control form-control-sm" id="txtDescuento" maxlength="5"  placeholder="0,00" onKeyPress="return handleNumber(event, \'{-5,2}\')">
                </div>
            </div>
            <hr>
            <label for="txtObservacion">Observación:</label>
            <textarea  class="form-control form-control-sm" id="txtObservacion" cols="1"></textarea>
            <hr>
            <button class="btn btn-info btn-sm mb-3 float-right" onclick="f_new_gancho()" ><b>GUARDAR</b></button>';
}
function get_data_update_gancho($dbConn){
    $Id = $_POST['Id'];
	$consulta="SELECT * FROM tbl_a_gancho WHERE ganId = :id";
	$sql= $dbConn->prepare($consulta);
	$sql->bindValue(':id',$Id);
	$sql->execute();
	if($row = $sql->fetch()) {
		return '
            <input type="hidden" value = "'.$row["ganId"].'" id="txtIdGancho" >
            <button class="btn btn-info btn-sm mb-3" ><b onclick="Cargar_Datos_table_gancho()">VER TABLA</b></button>
            <div class="row">
                <div class="col-md-6">
                    <label for="txtDescripcion">Descripción actual: </label>
                    <span class="text-muted form-control form-control-sm ">'.utf8_encode($row["ganDescripcion"]).'</span>
                </div>
                <div class="col-md-6">
                    <label for="txtDescripcion">Nueva descripción: </label><span class="text-muted">(max 30)</span>
                    <input type="text" class="form-control form-control-sm " id="txtDescripcion" maxlength="30" placeholder="Gancho 1" value="'.utf8_encode($row["ganDescripcion"]).'">
                </div>
            </div>
            <hr>
            <div class="row">
                <div class="col-md-6">
                    <label for="txtDescuento">Descuento: </label>
                    <span class="text-muted form-control form-control-sm ">'.number($row["ganDescuento"]).'</span>
                </div>
                <div class="col-md-6">
                    <label for="txtDescuento">Descuento: </label><span class="text-muted">(-2,23)</span>
                    <input type="text" class="form-control form-control-sm" id="txtDescuento" maxlength="5"  placeholder="0,00" onKeyPress="return handleNumber(event, \'{-5,2}\')" value="'.number($row["ganDescuento"]).'">
                </div>
            </div>
            <hr>
            <label for="txtObservacion">Observación:</label>
            <textarea  class="form-control form-control-sm" id="txtObservacion" cols="1">'.utf8_encode($row["ganObservacion"]).'</textarea>
            <hr>
            <button class="btn btn-info btn-sm mb-3 float-right" onclick="f_update_gancho()"  ><b>GUARDAR</b></button>';
	}else return "ERROR-7765542";//NO SE ENCONTRO EL ID
}
//Insert
function f_insert_producto($dbConn){
	$Id = $_POST['Id'];
	$consulta="SELECT * FROM tbl_a_especies WHERE espId = :id";
	$sql= $dbConn->prepare($consulta);
	$sql->bindValue(':id',$Id);
	$sql->execute();
	if($row = $sql->fetch()) {
        $codigo = 0;
        if (is_numeric(trim($_POST["Codigo"]))) {
            $dato = intval(trim($_POST["Codigo"]));
            if ($dato > 0 ) $codigo = $dato;
            else return 'El código ingresado es incorrecto';
        }else{
            return 'El código ingresado es incorrecto';
        }
        if (strlen($codigo) < 5) {
            $arrayEs = array(array(":variable"),array($codigo));
            if (f_comprobar($dbConn,$arrayEs,"SELECT * FROM tbl_a_productos WHERE proCodigo = :variable ",'Comprobar producto repetido')==false) {
                $tiempo = 10;
                if (is_numeric(trim($_POST["Tiempo"]))) {
                    $tiempo = intval(trim($_POST["Tiempo"]));
                }else{
                    return 'El tiempo de caducidad ingresado es incorrecto';
                }
                if (strlen($tiempo) < 5) {
                    $parte = trim($_POST["Parte"]);
                    if ($parte > 0 && $parte < 10 ) {
                        $conservar = trim($_POST["Conservar"]);
                        if (!is_numeric($conservar)) return 'La informacion de conservar es incorrecta';
                        $observaciones = trim($_POST["Observaciones"]);
                        $producto = trim($_POST["Producto"]);
                        $consulta = "INSERT INTO tbl_a_productos(proCodigo,proDescripcion,proPartes,proFechaVen,proConservar,proObservaciones,espId)
                                    VALUES (:proCodigo,:proDescripcion,:proPartes,:proFechaVen,:proConservar,:proObservaciones,:espId)";
                        $array =  array(array(":proCodigo",":proDescripcion",":proPartes",":proFechaVen",":proConservar",":proObservaciones",":espId"),
                                        array($codigo,$producto,$parte,$tiempo,$conservar,$observaciones,$Id));
                        $Error ="Insertar nuevo producto";
                        $Acion ="Insertar nuevo producto";
                        $detalle='Código <b>'.$codigo.'</b><br>'.
                            'Descripción: '.$producto.' $<br>'.
                            'Parte  : '.$parte.'<br>'.
                            'Vencimiento  : '.$tiempo.' días<br>'.
                            'Obsercaciones  : '.$observaciones.'<br>';
                        return f_consulta($dbConn,$array,$consulta,$Error,$detalle,$Acion,0);
                    }else{
                        return 'La parte del producto es incorrecta';
                    }
                }else{
                    return 'El tiempo de caducidad es incorrecto';
                }
            }else{
                return 'El <b>Código '.$codigo.'</b> ya se encuentra registrado';
            }
        }else{
            return "Código del producto incorrecto";
        }
	}else return "ERROR-7765542";//NO SE ENCONTRO EL ID
}
function f_insert_gancho($dbConn){
    $resultado = str_replace(".", "", trim($_POST["Descuento"]));
    $resultado = str_replace(",", ".", $resultado);
    $descuento = 0;
    if (is_numeric($resultado)) {
        $descuento = $resultado;
    }else{
        return 'El descuento es incorrecto';
    }
    $gancho = trim($_POST['Gancho']);
	$consulta="SELECT * FROM tbl_a_gancho WHERE ganDescuento = :descuento OR ganDescripcion = :descripcion";
	$sql= $dbConn->prepare($consulta);
	$sql->bindValue(':descuento',$descuento);
	$sql->bindValue(':descripcion',$gancho);
	$sql->execute();
    $cont1 = 0;
    $cont2 = 0;
	while($row = $sql->fetch()) {
        if ($row["ganDescuento"] == $descuento)$cont1++;
        if ($row["ganDescripcion"] == $gancho)$cont2++;
	}
    if ($cont1 == 0 && $cont2 == 0 ){
        $observaciones = trim($_POST["Observaciones"]);
        $consulta = "INSERT INTO tbl_a_gancho(ganDescuento,ganDescripcion,ganObservacion)
                    VALUES (:ganDescuento,:ganDescripcion,:ganObservacion)";
        $array =  array(array(":ganDescuento",":ganDescripcion",":ganObservacion"),
                        array($descuento,$gancho,$observaciones));
        $Error ="Nuevo gancho";
        $Acion ="Nuevo gancho";
        $detalle='Gancho <b>'.$gancho.'</b><br>'.
            'Descuento: '.$descuento.' <br>'.
            'Observaciones: '.$observaciones.' <br>';
        return f_consulta($dbConn,$array,$consulta,$Error,$detalle,$Acion,0);
    }else{
        if ($cont1 > 0)return 'Ya existe un gancho con el descuento '.$descuento.'';
        if ($cont2 > 0)return 'Ya existe un gancho con la descripción '.$gancho.'';
    }
    
}
//UPDATE
function f_update_estado_producto($dbConn){
	$Id = $_POST['Id'];
	$Estado = $_POST['Estado'];
	$consulta="SELECT * FROM tbl_a_productos WHERE proId = :id";
	$sql= $dbConn->prepare($consulta);
	$sql->bindValue(':id',$Id);
	$sql->execute();
	if($row = $sql->fetch()) {
		$detalle = "";
		$Acion = "";
		if ($Estado==0){
			$Acion = "Estado del producto";
			$detalle = "Se activo el producto (".utf8_encode($row["proDescripcion"]).")";
		}else if ($Estado==1){
			$Acion = "Estado del producto";
			$detalle = "Se desactivo el producto(".utf8_encode($row["proDescripcion"]).")";
		}else {
			return "No se encontro el estado del producto";
		}
		$array =  array(array(":estado",":id"),
						array($Estado,$Id));
		$consulta = "UPDATE tbl_a_productos SET proEliminado = :estado WHERE proId = :id";
		$Error = "Error al actualizar el estado del producto (".$Estado.")";
        return f_consulta($dbConn,$array,$consulta,$Error,$detalle,$Acion,$Id);
	}else return "ERROR-7765542";//NO SE ENCONTRO EL ID
}
function f_update_producto($dbConn){
	$Id = $_POST['Id'];
	$consulta="SELECT * FROM tbl_a_productos WHERE proId = :id";
	$sql= $dbConn->prepare($consulta);
	$sql->bindValue(':id',$Id);
	$sql->execute();
	if($row = $sql->fetch()) {
        $codigo = 0;
        if (is_numeric(trim($_POST["Codigo"]))) {
            $dato = intval(trim($_POST["Codigo"]));
            if ($dato > 0 ) $codigo = $dato;
            else return 'El código ingresado es incorrecto';
        }else{
            return 'El código ingresado es incorrecto';
        }
        if (strlen($codigo) < 5) {
            $arrayEs = array(array(":variable",":id"),array($codigo,$Id));
            if (f_comprobar($dbConn,$arrayEs,"SELECT * FROM tbl_a_productos WHERE proCodigo = :variable AND proId != :id",'Comprobar producto repetido')==false) {
                $tiempo = 10;
                if (is_numeric(trim($_POST["Tiempo"]))) {
                    $tiempo = intval(trim($_POST["Tiempo"]));
                }else{
                    return 'El tiempo de caducidad ingresado es incorrecto';
                }
                if (strlen($tiempo) < 5) {
                    $parte = trim($_POST["Parte"]);
                    if ($parte > 0 && $parte < 10 ) {
                        $conservar = trim($_POST["Conservar"]);
                        if (!is_numeric($conservar)) return 'La informacion de conservar es incorrecta';
                        $observaciones = trim($_POST["Observaciones"]);
                        $producto = trim($_POST["Producto"]);
                        $consulta = "UPDATE tbl_a_productos SET proCodigo = :proCodigo  , proDescripcion = :proDescripcion, proPartes = :proPartes,proFechaVen = :proFechaVen ,proConservar = :proConservar  ,proObservaciones = :proObservaciones
                                    WHERE proId = :id ";
                        $array =  array(array(":proCodigo",":proDescripcion",":proPartes",":proFechaVen",":proConservar",":proObservaciones",":id"),
                                        array($codigo,$producto,$parte,$tiempo,$conservar,$observaciones,$Id));
                        $Error ="Actualizar producto";
                        $Acion ="Actualizar producto";
                        $detalle='Código [ '.$row["proCodigo"].' ] = > [ '.$codigo.' ]<br>'.
                            'Descripción [ '.utf8_encode($row["proDescripcion"]).' ]  = > [ '.$producto.' ] <br>'.
                            'Parte  [ '.$row["proPartes"].' ] => [ '.$parte.' ] <br>'.
                            'Vencimiento  [ '.$row["proFechaVen"].' días ] => [ '.$tiempo.' días ] <br>'.
                            'Conservar a  [ '.$row["proConservar"].' °C ] => [ '.$conservar.' °C ] <br>'.
                            'Obsercaciones  [ '.utf8_encode($row["proObservaciones"]).' ] =>  [ '.$observaciones.' ]<br>';
                        return f_consulta($dbConn,$array,$consulta,$Error,$detalle,$Acion,$Id);
                    }else{
                        return 'La parte del producto es incorrecta';
                    }
                }else{
                    return 'El tiempo de caducidad es incorrecto';
                }
            }else{
                return 'El <b>Código '.$codigo.'</b> ya se encuentra registrado';
            }
        }else{
            return "Código del producto incorrecto";
        }
	}else return "ERROR-7765542";//NO SE ENCONTRO EL ID
}
function f_update_producto_predeterminado($dbConn){
	$Id = $_POST['Id'];
	$Estado = $_POST['Estado'];
	$consulta="SELECT * FROM tbl_a_productos WHERE proId = :id";
	$sql= $dbConn->prepare($consulta);
	$sql->bindValue(':id',$Id);
	$sql->execute();
	if($row = $sql->fetch()) {
		$detalle = "";
		$Acion = "";
		if ($Estado==0){
			$Acion = "Producto predeterminado";
			$detalle = "Se elimino como predeterminado al producto (".utf8_encode($row["proDescripcion"]).")";
		}else if ($Estado==1){
			$Acion = "Producto predeterminado";
			$detalle = "Se definio como predeterminado al producto (".utf8_encode($row["proDescripcion"]).")";
		}else {
			return "No se encontro el estado del producto";
		}
		$array =  array(array(":estado",":id"),
						array($Estado,$Id));
		$consulta = "UPDATE tbl_a_productos SET proPredeterminado = :estado , proEliminado = 0 WHERE proId = :id";
		$Error = "Error al actualizar el estado del producto (".$Estado.")";
        $resul = f_consulta($dbConn,$array,$consulta,$Error,$detalle,$Acion,$Id);
        if ($resul) {
            if ($Estado==1) {
                $sql= $dbConn->prepare('UPDATE tbl_a_productos SET proPredeterminado = 0 WHERE proId != :id');
                $sql->bindValue(":id",$Id);
                if ($sql->execute())return true;
                else return "ERROR-2128";
            }else return true;
        }else return $resul;
	}else return "ERROR-7765542";//NO SE ENCONTRO EL ID
}
function f_update_estado_gancho($dbConn){
	$Id = $_POST['Id'];
	$Estado = $_POST['Estado'];
	$consulta="SELECT * FROM tbl_a_gancho WHERE ganId = :id";
	$sql= $dbConn->prepare($consulta);
	$sql->bindValue(':id',$Id);
	$sql->execute();
	if($row = $sql->fetch()) {
		$detalle = "";
		$Acion = "";
		if ($Estado==0){
			$Acion = "Estado de gancho";
			$detalle = "Se activo el gancho (".utf8_encode($row["ganDescripcion"]).")";
		}else if ($Estado==1){
			$Acion = "Estado de gancho";
			$detalle = "Se inactivo el gancho (".utf8_encode($row["ganDescripcion"]).")";
		}else {
			return "No se encontro el estado del producto";
		}
		$array =  array(array(":estado",":id"),
						array($Estado,$Id));
		$consulta = "UPDATE tbl_a_gancho SET ganEstado = :estado WHERE ganId = :id";
		$Error = "Error al actualizar el estado del gancho (".$Estado.")";
        return f_consulta($dbConn,$array,$consulta,$Error,$detalle,$Acion,$Id);
	}else return "ERROR-7765542";//NO SE ENCONTRO EL ID
}
function f_update_gancho($dbConn){
    $Id = $_POST["Id"];
	$consulta="SELECT * FROM tbl_a_gancho WHERE ganId = :id";
	$sql= $dbConn->prepare($consulta);
	$sql->bindValue(':id',$Id);
	$sql->execute();
	if($row = $sql->fetch()) {
        $resultado = str_replace(".", "", trim($_POST["Descuento"]));
        $resultado = str_replace(",", ".", $resultado);
        $descuento = 0;
        if (is_numeric($resultado)) {
            $descuento = $resultado;
        }else{
            return 'El descuento es incorrecto';
        }
        $array =  array(array(":descuento",":id"),
						array($descuento,$Id));
        if (f_comprobar($dbConn,$array,'SELECT * FROM tbl_a_gancho WHERE ganDescuento = :descuento  AND ganId != :id','Comprobar Descuento')==false) {
            $gancho = trim($_POST['Gancho']);
            $arrayE =  array(array(":gancho",":id"),
						array($gancho,$Id));
            if (f_comprobar($dbConn,$arrayE,'SELECT * FROM tbl_a_gancho WHERE ganDescripcion = :gancho  AND ganId != :id','Comprobar Gancho')==false) {
                $observaciones = trim($_POST["Observaciones"]);
                $consulta = "UPDATE tbl_a_gancho SET ganDescuento = :ganDescuento ,ganDescripcion = :ganDescripcion , ganObservacion = :ganObservacion
                            WHERE ganId = :id";
                $array =  array(array(":ganDescuento",":ganDescripcion",":ganObservacion",":id"),
                                array($descuento,$gancho,$observaciones,$Id));
                $Error ="Actualización de gancho";
                $Acion ="Actualización de gancho";
                $detalle='Gancho [ '.utf8_encode($row["ganDescripcion"]).' ] = > [ '.$gancho.' ]<br>'.
                    'Descuento [ '.$row["ganDescuento"].' ] = >  [ '.$descuento.' ]<br>'.
                    'Observaciones [ '.utf8_encode($row["ganObservacion"]).' ] = > [ '.$observaciones.' ]<br>';
                return f_consulta($dbConn,$array,$consulta,$Error,$detalle,$Acion,$Id);
            }else {
                return 'Ya existe otro gancho que tiene la descripción '.$gancho.'';
            }
        }else {
            return 'Ya existe otro gancho que tiene el descuento '.$descuento.'';
        }
	}else return 'ERROR-21222';//NO SE ECONTRO EL ID
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
			if(Insert_Login($Id,'tbl_a_productos',$Acion,$detalle,'')) return true;
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
			$sql->bindValue("".$array[0][$i],$array[1][$i]);
		}
		$sql->execute();
		if($row = $sql->fetch()) return true;
		else return false;
	}  catch (Exception $e) {
		Insert_Error('ERROR-65466',$e->getMessage(),$Error);
		exit("ERROR-65466");
	}
}

function selecoption($number_select){
	$resultado = '';
	for ($i=1; $i < 5 ; $i++) {
        if ($i != 3) {
            if ($number_select == $i) $selected = 'selected';
		    else $selected = '';
		    $resultado .= '<option value="'.$i.'" '.$selected.'>'.$i.'</option>';
        }
	}
	return $resultado;
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
function modal_2($data,$titulo){
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
	</div>';
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