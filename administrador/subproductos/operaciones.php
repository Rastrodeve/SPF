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
    elseif($op==8) echo get_data_update_parentesco($dbConn);
    elseif($op==9) echo update_sub_parentesco($dbConn);
    
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
                                <b><i class="fas fa-plus"></i> AÑADIR SUBPRODUCTO</b>
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
                            <th>Para</th>
                            <th>Partes</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead><tbody>';
    $consulta="SELECT * FROM tbl_a_subproductos WHERE espId = :id";
	$sql= $dbConn->prepare($consulta);
	$sql->bindValue(':id',$Id);
	$sql->execute();
	$cont = 0;
	$Total=0;
	while ($row = $sql->fetch()) {
		$cont++;
		$estado ="ERROR";
		if ($row["subEstado"]==0) {
			$estado ='<button type="button" onclick="f_estado_producto(1,'.$row["subId"].','.$row["espId"].')" class="btn btn-success btn-sm"><b>ACTIVO</b></button>';
		}else if ($row["subEstado"]==1){
			$estado ='<button type="button" onclick="f_estado_producto(0,'.$row["subId"].','.$row["espId"].')" class="btn btn-danger btn-sm"><b>INACTIVO</b></button>';
		}
        $m_sxo = "EROROR--".$row["subSexo"];
        if ($row["subSexo"]==0)$m_sxo ="AMBOS";
        else if ($row["subSexo"]==1)$m_sxo ="SOLO HEMBRAS";
        else if ($row["subSexo"]==2)$m_sxo ="SOLO MACHOS";
		$resultado=$resultado.'
            <tr>
                <td>'.$cont.'</td>
                <td >'.$row["subCodigo"].'</td>
                <td >'.utf8_encode($row["subDescripcion"]).'</td>
                <td >'.$m_sxo.'</td>
                <td >'.$row["subParte"].'</td>
                <td >'.$estado.'</td>
                <td>
                    <button  type="button" class="btn btn-sm btn-info" data-toggle="modal"
                    data-target="#modal"  onclick="get_data_update_producto('.$row["subId"].')" >
                        <b><i class="fas fa-edit"></i></b>
                    </button>
                    <button  type="button" class="btn btn-sm btn-info" data-toggle="modal"
                    data-target="#modal"  onclick="get_data_parentesco('.$row["subId"].')" >
                        <i class="fas fa-disease"></i>
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
                <h6 class="text-muted"><b>NUEVO SUBPRODUCTO PARA LA ESPECIE '.strtoupper(utf8_encode($row["espDescripcion"])).'</b> </h6>
			</div>
		</div>
		<hr class="mt-2">
		<div class="row">
			<div class="col-md-8">
				<label for="txtProducto">Descripción del subproducto:</label><span class="text-muted">(max 30)</span>
				<input type="text" class="form-control form-control-sm" maxlength="30" id="txtProducto" placeholder="Higado">
			</div>
            <div class="col-md-4">
				<label for="txtCodigo">Código del subproducto:</label><span class="text-muted">(max 4)</span>
				<input type="text" class="form-control form-control-sm input_disablecopypaste" maxlength="4" id="txtCodigo" placeholder="1234" onKeyPress="onlynumber(event)">
			</div>
		</div>
        <hr>
        <div class="row">
            <div class="col-md-3">
				<label for="slcParte">Partes por animal:</label>
                <div class="input-group mb-3 input-group-sm">
                    <select class="custom-select" id="slcParte">
                        '.selecoption(1).'
                    </select>
                </div>
			</div>
            <div class="col-md-4">
				<label for="slcSexo">Para:</label>
                <div class="input-group mb-3 input-group-sm">
                    <select class="custom-select" id="slcSexo">
                        <option value="0">Ambos</option>
                        <option value="1">Solo hembras</option>
                        <option value="2">Solo machos</option>
                    </select>
                </div>
			</div>
		</div>
		<hr>
        <label for="txtObservacion">Observación:</label>
        <textarea  class="form-control form-control-sm" id="txtObservacion" cols="3"></textarea>
		';
		return modal($data,'AÑADIR NUEVO SUBPRODUCTO','f_new_producto()');
	}else return modal("ERROR-7765542","ERROR-7765542",'error_f()');//NO SE ENCONTRO EL ID
}
function get_data_update_producto($dbConn){
	$Id = $_POST['Id'];
	$consulta="SELECT * FROM tbl_a_subproductos p, tbl_a_especies e WHERE p.espId = e.espId AND  p.subId = :id";
	$sql= $dbConn->prepare($consulta);
	$sql->bindValue(':id',$Id);
	$sql->execute();
	if($row = $sql->fetch()) {
        $m_sxo = "EROROR--".$row["subSexo"];
        $check1 = "";
        $check2 = "";
        $check3 = "";
        if ($row["subSexo"]==0){
            $m_sxo ="AMBOS";
            $check1 = "selected";
        }
        else if ($row["subSexo"]==1){
            $m_sxo ="SOLO HEMBRAS";
            $check2 = "selected";
        }
        else if ($row["subSexo"]==2){
            $m_sxo ="SOLO MACHOS";
            $check3 = "selected";
        }
		$data = '
		<input type="hidden" value="'.$row["espId"].'" id="txtIdEspecie">
		<input type="hidden" value="'.$row["subId"].'" id="txtIdProducto">
		<div class="row">
			<div class="col-md-12">
			<h6 class="text-muted"><b>EDITAR SUBPRODUCTO PARA LA ESPECIE '.strtoupper(utf8_encode($row["espDescripcion"])).'</b> </h6>
			</div>
		</div>
		<hr class="mt-2">
		<div class="row">
			<div class="col-md-6">
				<label for="txtProducto">Descripción actual del subproducto:</label>
                <span class="text-muted form-control form-control-sm">'.utf8_encode($row["subDescripcion"]).'</span>
			</div>
            <div class="col-md-6">
				<label for="txtProducto">Nueva descripción del subproducto:</label><span class="text-muted">(max 30)</span>
				<input type="text" class="form-control form-control-sm" maxlength="30" id="txtProducto" placeholder="CANAL BOVINO" value="'.utf8_encode($row["subDescripcion"]).'">
			</div>
		</div>
        <hr >
        <div class="row">
            <div class="col-md-5">
				<label for="txtCodigo">Código actual del subproducto:</label>
                <span class="text-muted form-control form-control-sm">'.$row["subCodigo"].'</span>
			</div>
            <div class="col-md-5">
				<label for="txtCodigo">Nuevo código del subproducto:</label><span class="text-muted">(max 4)</span>
				<input type="text" class="form-control form-control-sm input_disablecopypaste" maxlength="4" id="txtCodigo" placeholder="1234" onKeyPress="onlynumber(event)" value="'.$row["subCodigo"].'">
			</div>
		</div>
        <hr>
        
        <hr>
        <div class="row">
            <div class="col-md-3">
				<label for="txtCodigo">Parte actual:</label><span class="text-muted">(max 4)</span>
				<span class="text-muted form-control form-control-sm">'.$row["subParte"].'</span>
			</div>
            <div class="col-md-3">
				<label for="slcParte">Nueva parte:</label>
                <div class="input-group mb-3 input-group-sm">
                    <select class="custom-select" id="slcParte">
                        '.selecoption($row["subParte"]).'
                    </select>
                </div>
			</div>
            <div class="col-md-3">
                <label for="slcSexo">Para actual:</label>
                <span class="text-muted form-control form-control-sm">'.$m_sxo.'</span>
			</div>
            <div class="col-md-3">
                <label for="slcSexo">Nuevo para:</label>
                <div class="input-group mb-3 input-group-sm">
                    <select class="custom-select" id="slcSexo">
                        <option value="0" '.$check1.'>Ambos</option>
                        <option value="1" '.$check2.'>Solo hembras</option>
                        <option value="2" '.$check3.'>Solo machos</option>
                    </select>
                </div>
			</div>
		</div>
		<hr>
        <label for="txtObservacion">Observación:</label>
        <textarea  class="form-control form-control-sm" id="txtObservacion" cols="3">'.utf8_encode($row["subObservacion"]).'</textarea>
		';
		return modal($data,'ACTUALIZAR SUBPRODUCTO','f_update_producto()');
	}else return modal("ERROR-7765542","ERROR-7765542",'error_f()');//NO SE ENCONTRO EL ID
}
function get_data_update_parentesco($dbConn){
	$Id = $_POST['Id'];
    $titulo = '';
    $consulta="SELECT * FROM tbl_a_subproductos  WHERE subId = :id";
	$sql= $dbConn->prepare($consulta);
	$sql->bindValue(':id',$Id);
	$sql->execute();
	if($row = $sql->fetch()) {
        $titulo = '
        <input type="hidden" value="'.$row["subId"].'" id="txtIdProducto">
		<div class="row">
			<div class="col-md-12">
			<h6 class="text-muted"><b>AÑADIR ENFERMEDADES PARA EL SUBPRODUCTO "'.utf8_encode($row["subDescripcion"]).'"</b> </h6>
			</div>
		</div>
		<hr>';
    }else return modal("ERROR-7765542","ERROR-7765542",'error_f()');//NO SE ENCONTRO EL ID
    $cont=0;
	$consulta="SELECT * FROM tbl_a_enfermedad WHERE enfEstado = 0 ORDER BY enfDescripcion ASC ";
	$sql= $dbConn->prepare($consulta);
	$sql->bindValue(':id',$Id);
	$sql->execute();
    $data= '';
	while($row = $sql->fetch()) {
        $cont++;
        $value = 0;
        $checked = '';
        if (get_parentesco($dbConn,$Id,$row["enfId"])) {
            $value = 1;
            $checked = 'checked';
        }
		$data .= '
        <div class="col-6">
            <div class="custom-control custom-checkbox">
                <input type="hidden" id="chbst-'.$cont.'" value="'.$value.'">
                <input class="custom-control-input" type="checkbox" id="chb-'.$cont.'" value="'.$row["enfId"].'" '.$checked.'  style="cursor:pointer" >
                <label for="chb-'.$cont.'" id="lblch'.$row["enfId"].'" class="custom-control-label" style="cursor:pointer"  >'.utf8_encode($row["enfDescripcion"]).'</label>
            </div>
        </div>';
		
	}
    $resultado =  $titulo.'<div class="row">'.$data.'</div><input type="hidden" value="'.$cont.'" id="cant-enfermedades">';
    return modal($resultado,'EDITAR ENFERMEDADES','f_update_parentesco()');
}
function get_parentesco($dbConn,$Subproducto,$Enfermedad){
    $consulta="SELECT * FROM tbl_parentesco WHERE enfId =:enfer AND subId = :subpro";
    $sql= $dbConn->prepare($consulta);
    $sql->bindValue(':enfer',$Enfermedad);
    $sql->bindValue(':subpro',$Subproducto);
    $sql->execute();
    if($row = $sql->fetch())return true;
    return false;
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
        $para = $_POST["Para"];
        if ($para == "0" || $para == "1" || $para == "2");
        else return 'La opcion seleccionada es incorrecta';

        if (strlen($codigo) < 5) {
            $arrayEs = array(array(":variable"),array($codigo));
            if (f_comprobar($dbConn,$arrayEs,"SELECT * FROM tbl_a_subproductos WHERE subCodigo = :variable ",'Comprobar subproducto repetido')==false) {
                $parte = trim($_POST["Parte"]);
                if ($parte > 0 && $parte < 10 ) {
                    $m_sxo = "EROROR--988";
                    if ($para==0)$m_sxo ="AMBOS";
                    else if ($para==1)$m_sxo ="SOLO HEMBRAS";
                    else if ($para==2)$m_sxo ="SOLO MACHOS";
                    $observaciones = trim($_POST["Observaciones"]);
                    $producto = trim($_POST["Producto"]);
                    $consulta = "INSERT INTO tbl_a_subproductos(subCodigo,subDescripcion,subParte,subSexo,subObservacion,espId)
                                    VALUES(:subCodigo,:subDescripcion,:subParte,:subSexo,:subObservacion,:espId)";
                    $array =  array(array(":subCodigo",":subDescripcion",":subParte",":subSexo",":subObservacion",":espId"),
                                    array($codigo,$producto,$parte,$para,$observaciones,$Id));
                    $Error ="Insertar nuevo subproducto";
                    $Acion ="Insertar nuevo subproducto";
                    $detalle='Código <b>'.$codigo.'</b><br>'.
                        'Descripción: '.$producto.' $<br>'.
                        'Parte  : '.$parte.'<br>'.
                        'Para  : '.$m_sxo.'<br>'.
                        'Obsercaciones  : '.$observaciones.'<br>';
                    return f_consulta($dbConn,$array,$consulta,$Error,$detalle,$Acion,0);
                }else{
                    return 'La parte del subproducto es incorrecta';
                }
            }else{
                return 'El <b>Código '.$codigo.'</b> ya se encuentra registrado';
            }
        }else{
            return "Código del producto incorrecto";
        }
	}else return "ERROR-7765542";//NO SE ENCONTRO EL ID
}
function update_sub_parentesco($dbConn){
    try {
        $Subproducto = trim($_POST["SubProd"]);
        $Array = $_POST["Array"];
        $Errores = '';
        $Nuevo = '';
        $Eliminar = '';
        $consulta="SELECT * FROM tbl_a_subproductos WHERE subId = :id";
        $sql= $dbConn->prepare($consulta);
        $sql->bindValue(':id',$Subproducto);
        $sql->execute();
        if($row = $sql->fetch()){
            for ($i=0; $i <count($Array) ; $i++) {
                $nameM = get_data_enfermeda($dbConn,$Array[$i][0]);
                if ($Array[$i][2]==1){
                    $bandera = ayadir_parentesco($dbConn,$Subproducto,$Array[$i][0]);
                    if ($bandera != true)$Errores .='No se pudo añadir el parentesco '.$nameM.' Error:'.$bandera;
                    else $Nuevo .= '['.$nameM.'] ';
                }elseif ($Array[$i][2]==0){
                    $bandera = eliminar_parentesco($Subproducto,$Array[$i][0]);
                    if ($bandera != true)$Errores .='No se pudo eliminar el parentesco '.$nameM.' Error:'.$bandera;
                    else $Eliminar .= '['.$nameM.'] ';
                }
            }
            if ($Errores == ''){
                $Acion = 'Desginación de Enfermedades';
                $detalle='<b>Enfermedades para  '.utf8_encode($row["subDescripcion"]).'</b><br>Enfermedades añadidas '.$Nuevo.'<br>Enfermedades eliminadas '.$Eliminar;
                if(Insert_Login($Subproducto,'tbl_a_subproductos',$Acion,$detalle,''))return true; 
                else return 'ERROR-100172';
            }else return $Errores;
        }else return 'ERROR-100174';//NO SE ENCONTRO EL SUBPRODUCTO
    } catch (Exception $e) {
        Insert_Error('ERROR-199865',$e->getMessage(),'Actualizar Parentesco');
        exit("ERROR-199865");
    }
}
function get_data_enfermeda($dbConn,$Id){
    $result ='';
    $consulta="SELECT enfDescripcion FROM  tbl_a_enfermedad where enfId = :id";
    $sql= $dbConn->prepare($consulta);
    $sql->bindValue(':id',$Id);
    $sql->execute();
    if($row = $sql->fetch()){
        $result = utf8_encode($row["enfDescripcion"]);
    }
    return $result;
}
function eliminar_parentesco($Subproducto,$Enfermedad){
    try {
        global $dbEl;
        $dbConn = conectar($dbEl);
        $consulta1="DELETE FROM tbl_parentesco WHERE enfId = :enfermedad AND subId = :subproducto ";
        $sql1= $dbConn->prepare($consulta1);
        $sql1->bindValue(':enfermedad', $Enfermedad);
        $sql1->bindValue(':subproducto',$Subproducto);
        if ($sql1->execute())return true;
        else return "ERROR-0099921";//NO SE PUEDDO ACTUALIZAR EL USUARIO
    }  catch (Exception $e) {
        Insert_Error('ERROR-184332',$e->getMessage(),'Eliminar parentesco');
        exit("ERROR-184332");
    }
}
function ayadir_parentesco($dbConn,$Subproducto,$Enfermedad){
    try {
        $consulta1="INSERT INTO tbl_parentesco(enfId,subId) VALUES(:enfermedad,:subproducto)";
        $sql1= $dbConn->prepare($consulta1);
        $sql1->bindValue(':enfermedad', $Enfermedad);
        $sql1->bindValue(':subproducto',$Subproducto);
        if ($sql1->execute())return true;
        else return "ERROR-0099921";//NO SE PUEDDO ACTUALIZAR EL USUARIO    
    } catch (Exception $e) {
        Insert_Error('ERROR-109432',$e->getMessage(),'Añadir los parentesco');
        exit("ERROR-109432");
    }
}
//UPDATE
function f_update_estado_producto($dbConn){
	$Id = $_POST['Id'];
	$Estado = $_POST['Estado'];
	$consulta="SELECT * FROM tbl_a_subproductos WHERE subId = :id";
	$sql= $dbConn->prepare($consulta);
	$sql->bindValue(':id',$Id);
	$sql->execute();
	if($row = $sql->fetch()) {
		$detalle = "";
		$Acion = "";
		if ($Estado==0){
			$Acion = "Estado del subproducto";
			$detalle = "Se activo el subproducto (".utf8_encode($row["subDescripcion"]).")";
		}else if ($Estado==1){
			$Acion = "Estado del subproducto";
			$detalle = "Se desactivo el subproducto(".utf8_encode($row["subDescripcion"]).")";
		}else {
			return "No se encontro el estado del producto";
		}
		$array =  array(array(":estado",":id"),
						array($Estado,$Id));
		$consulta = "UPDATE tbl_a_subproductos SET subEstado = :estado WHERE subId = :id";
		$Error = "Error al actualizar el estado del subproducto (".$Estado.")";
        return f_consulta($dbConn,$array,$consulta,$Error,$detalle,$Acion,$Id);
	}else return "ERROR-7765542";//NO SE ENCONTRO EL ID
}
function f_update_producto($dbConn){
	$Id = $_POST['Id'];
	$consulta="SELECT * FROM tbl_a_subproductos WHERE subId = :id";
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
            $para = $_POST["Para"];
            if ($para == "0" || $para == "1" || $para == "2");
            else return 'La opcion seleccionada es incorrecta';
            
            $m_sxo ="ERROR";
            if ($para==0)$m_sxo ="AMBOS";
            else if ($para==1)$m_sxo ="SOLO HEMBRAS";
            else if ($para==2)$m_sxo ="SOLO MACHOS";

            $m_sxo_2 ="ERROR";
            if ($row["subSexo"]==0)$m_sxo_2 ="AMBOS";
            else if ($row["subSexo"]==1)$m_sxo_2 ="SOLO HEMBRAS";
            else if ($row["subSexo"]==2)$m_sxo_2 ="SOLO MACHOS";
            
            $arrayEs = array(array(":variable",":id"),array($codigo,$Id));
            if (f_comprobar($dbConn,$arrayEs,"SELECT * FROM tbl_a_subproductos WHERE subCodigo = :variable AND subId != :id",'Comprobar subproducto repetido')==false) {
                $parte = trim($_POST["Parte"]);
                if ($parte > 0 && $parte < 10 ) {
                    $observaciones = trim($_POST["Observaciones"]);
                    $producto = trim($_POST["Producto"]);
                    $consulta = "UPDATE tbl_a_subproductos SET subCodigo = :subCodigo  , subDescripcion = :subDescripcion, subParte = :subParte,subSexo = :subSexo ,subObservacion = :subObservacion
                                WHERE subId = :id ";
                    $array =  array(array(":subCodigo",":subDescripcion",":subParte",":subSexo",":subObservacion",":id"),
                                    array($codigo,$producto,$parte,$para,$observaciones,$Id));
                    $Error ="Actualizar subproducto";
                    $Acion ="Actualizar subproducto";
                    $detalle='Código [ '.$row["subCodigo"].' ] = > [ '.$codigo.' ]<br>'.
                        'Descripción [ '.utf8_encode($row["subDescripcion"]).' ]  = > [ '.$producto.' ] <br>'.
                        'Parte  [ '.$row["subParte"].' ] => [ '.$parte.' ] <br>'.
                        'Parte  [ '.$m_sxo_2.' ] => [ '.$m_sxo.' ] <br>'.
                        'Obsercaciones  [ '.utf8_encode($row["subObservacion"]).' ] =>  [ '.$observaciones.' ]<br>';
                    return f_consulta($dbConn,$array,$consulta,$Error,$detalle,$Acion,$Id);
                }else{
                    return 'La parte del subproducto es incorrecta';
                }
            }else{
                return 'El <b>Código '.$codigo.'</b> ya se encuentra registrado';
            }
        }else{
            return "Código del subproducto incorrecto";
        }
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
			if(Insert_Login($Id,'tbl_a_subproductos',$Acion,$detalle,'')) return true;
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
	for ($i=1; $i < 6 ; $i++) { 
		if ($number_select == $i) $selected = 'selected';
		else $selected = '';
		$resultado .= '<option value="'.$i.'" '.$selected.'>'.$i.'</option>';
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