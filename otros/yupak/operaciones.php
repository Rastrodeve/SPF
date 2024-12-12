<?php
if (isset($_REQUEST['op'])) {
	require '../../FilePHP/utils.php';
	$dbConn = conectar($db);
	$op=$_REQUEST['op'];
	if ($op==1) echo get_data($dbConn);//Listar_Especie_Animales($dbConn)
    elseif($op==2) echo get_data_new();//Yes -- get_data_new_producto($dbConn)
    elseif($op==3) echo f_insert($dbConn);//Yes -- f_insert_producto($dbConn)
    elseif($op==4) echo get_data_empr($dbConn);//Yes f_update_estado_producto($dbConn)
    elseif($op==5) echo get_data_loca($dbConn);//Yes ---  Consultar_Datos_productos($dbConn,$_POST["Id"])
    elseif($op==6) echo get_data_caja($dbConn);//Yes -- get_data_update_producto($dbConn)
    elseif($op==7) echo f_update_empr($dbConn);//Yes --- 
    elseif($op==8) echo f_update_loca($dbConn);//Yes --- 
    elseif($op==9) echo f_update_caja($dbConn);//Yes --- 
}else {
    header("location: ./");
}

function get_data($dbConn){
    $resultado="";
	$consulta="SELECT * FROM tbl_a_especificacionesyp ORDER BY espyId ASC LIMIT 1";
	$sql= $dbConn->prepare($consulta);
	$sql->execute();
	if ($row = $sql->fetch()) {
        return '
        <p>
            <b>Nota:</b>
            Esta información se aplicará para crear las facturas en el modulo <b>Yupak</b> cuando es generada la <b>Orden de Producción</b>
        </p>
        <hr>
        <h5>Código de la empresa: 
            <b data-toggle="modal" data-target="#modal" style="cursor:pointer" onclick="get_data_op(4,'.$row["espyId"].')">
            '.$row["espyCodigo_Empresa"].'</b>
        </h5>
        <hr>
        <h5>Localidad: 
        <b data-toggle="modal" data-target="#modal" style="cursor:pointer" onclick="get_data_op(5,'.$row["espyId"].')">
            '.$row["espyLocalidad"].'</b>
        </h5>
        <hr>
        <h5>Número de Caja: 
            <b data-toggle="modal" data-target="#modal" style="cursor:pointer" onclick="get_data_op(6,'.$row["espyId"].')">
            '.$row["espyCaja"].'</b>
        </h5>
        <hr>
        <h6><b>Última modificación:</b> '.$row["espyFechaActua"].'</h6>';
    }else{
        return '
        <p>
            <b>Nota:</b>
            No se pudo encontrar la información
        </p>
        <button class="btn btn-info btn-sm" onclick="get_data_new()" data-toggle="modal"
        data-target="#modal"><b>AGREGAR</b></button>';
    }
}
function get_data_new(){
    $data = '
    <div class="row">
        <div class="col-md-12">
            <label for="txtEmpresa">Código de la empresa:</label><span class="text-muted">(max 4)</span>
            <input type="text" class="form-control form-control-sm input_disablecopypaste" maxlength="4" id="txtEmpresa" placeholder="4" onKeyPress="onlynumber(event)">
        </div>
    </div>
    <hr>
    <div class="row">
        <div class="col-md-12">
            <label for="txtLocalidad">Código de Localidad:</label><span class="text-muted">(max 4)</span>
            <input type="text" class="form-control form-control-sm " maxlength="4" id="txtLocalidad" placeholder="1" onKeyPress="onlynumber(event)">
        </div>
    </div>
    <hr>
    <div class="row">
        <div class="col-md-12">
            <label for="txtCaja">Número de caja:</label><span class="text-muted">(max 6)</span>
            <input type="text" class="form-control form-control-sm" maxlength="6" id="txtCaja" placeholder="1015" onKeyPress="onlynumber(event)">
        </div>
    </div>
    <hr>
    <p>
        <b>Nota:</b>
        Esta información se aplicará para crear las facturas en el modulo <b>Yupak</b> cuando es generada la <b>Orden de Producción</b>
    </p>';
    return modal($data,'NUEVA CONFIGURACIÓN','f_new()');
}
function get_data_empr($dbConn){
    $Id = $_POST["Id"];
    $resultado="";
	$consulta="SELECT * FROM tbl_a_especificacionesyp WHERE espyId = :id";
	$sql= $dbConn->prepare($consulta);
    $sql->bindValue(':id',$Id);
	$sql->execute();
	if ($row = $sql->fetch()) {
        $data = '
        <input type="hidden" value="'.$row["espyId"].'" id="txtId">
        <div class="row">
            <div class="col-md-12">
                <label for="txtEmpresa">Código de la empresa actual:</label>
                <span class="text-muted form-control form-control-sm">'.$row["espyCodigo_Empresa"].'</span>
            </div>
        </div>
        <hr>
        <div class="row">
            <div class="col-md-12">
                <label for="txtEmpresa">Nuevo código de la empresa:</label><span class="text-muted">(max 4)</span>
                <input type="text" class="form-control form-control-sm input_disablecopypaste" maxlength="4" id="txtEmpresa" placeholder="4" value="'.$row["espyCodigo_Empresa"].'" onKeyPress="onlynumber(event)">
            </div>
        </div>
        <hr>
        <p>
            <b>Nota:</b>
            Esta información <b>NO</b> se aplicará  a las ordenes de producción ya generadas
        </p>';
        return modal($data,'CAMBIAR CÓDIGO DE LA EMPRESA','f_update_empr()');
    }else{
        return 'ERROR-1281278';
    }
}
function get_data_loca($dbConn){
    $Id = $_POST["Id"];
    $resultado="";
	$consulta="SELECT * FROM tbl_a_especificacionesyp WHERE espyId = :id";
	$sql= $dbConn->prepare($consulta);
    $sql->bindValue(':id',$Id);
	$sql->execute();
	if ($row = $sql->fetch()) {
        $data = '
        <input type="hidden" value="'.$row["espyId"].'" id="txtId">
        <div class="row">
            <div class="col-md-12">
                <label for="txtLocalidad">Código de localidad actual:</label>
                <span class="text-muted form-control form-control-sm">'.$row["espyLocalidad"].'</span>
            </div>
        </div>
        <hr>
        <div class="row">
            <div class="col-md-12">
                <label for="txtLocalidad">Nuevo código de localidad:</label><span class="text-muted">(max 4)</span>
                <input type="text" class="form-control form-control-sm input_disablecopypaste" maxlength="4" id="txtLocalidad" placeholder="4" value="'.$row["espyLocalidad"].'" onKeyPress="onlynumber(event)">
            </div>
        </div>
        <hr>
        <p>
            <b>Nota:</b>
            Esta información <b>NO</b> se aplicará  a las ordenes de producción ya generadas
        </p>';
        return modal($data,'CAMBIAR CÓDIGO DE LA EMPRESA','f_update_loca()');
    }else{
        return 'ERROR-1281278';
    }
}
function get_data_caja($dbConn){
    $Id = $_POST["Id"];
    $resultado="";
	$consulta="SELECT * FROM tbl_a_especificacionesyp WHERE espyId = :id";
	$sql= $dbConn->prepare($consulta);
    $sql->bindValue(':id',$Id);
	$sql->execute();
	if ($row = $sql->fetch()) {
        $data = '
        <input type="hidden" value="'.$row["espyId"].'" id="txtId">
        <div class="row">
            <div class="col-md-12">
                <label for="txtCaja">Número de caja actual:</label>
                <span class="text-muted form-control form-control-sm">'.$row["espyCaja"].'</span>
            </div>
        </div>
        <hr>
        <div class="row">
            <div class="col-md-12">
                <label for="txtCaja">Nuevo número de caja:</label><span class="text-muted">(max 6)</span>
                <input type="text" class="form-control form-control-sm input_disablecopypaste" maxlength="6" id="txtCaja" placeholder="1015" value="'.$row["espyCaja"].'" onKeyPress="onlynumber(event)">
            </div>
        </div>
        <hr>
        <p>
            <b>Nota:</b>
            Esta información <b>NO</b> se aplicará  a las ordenes de producción ya generadas
        </p>';
        return modal($data,'CAMBIAR CÓDIGO DE LA EMPRESA','f_update_caja()');
    }else{
        return 'ERROR-1281278';
    }
}

//Insert
function f_insert($dbConn){
    $Empresa = 1;
    if (is_numeric($_POST["Empresa"])) {
        $nuevo = intval($_POST["Empresa"]);
        if (strlen($nuevo) <= 4) $Empresa = $nuevo;
        else return 'El Código de la empresa ingresado supera el maximo especificado';
    }else return 'El Código de la empresa es incorrecto';

    $Localidad = 1;
    if (is_numeric($_POST["Localidad"])) {
        $nuevo = intval($_POST["Localidad"]);
        if (strlen($nuevo) <= 4) $Localidad = $nuevo;
        else return 'El Código de localidad ingresado supera el maximo especificado';
    }else return 'El Código de localidad es incorrecto';

    $Caja = 1;
    if (is_numeric($_POST["Caja"])) {
        $nuevo = intval($_POST["Caja"]);
        if (strlen($nuevo) <= 4) $Caja = $nuevo;
        else return 'El número de caja ingresado supera el maximo especificado';
    }else return 'El número de caja es incorrecto';

    $consulta = "INSERT INTO tbl_a_especificacionesyp(espyCodigo_Empresa,espyLocalidad,espyCaja,espyFechaActua)
                VALUES (:espyCodigo_Empresa,:espyLocalidad,:espyCaja,:espyFechaActua)";
    $array =  array(array(":espyCodigo_Empresa",":espyLocalidad",":espyCaja",":espyFechaActua"),
                    array($Empresa,$Localidad,$Caja,date("Y-m-d H:i:s")));
    $Error ="Insertar nueva configuración YUPAK";
    $Acion ="Insertar nueva configuración YUPAK";
    $detalle='Código de la empresa <b>'.$Empresa.'</b><br>'.
        'Código de la localidad: '.$Localidad.' $<br>'.
        'Número de caja : '.$Caja.'<br>';
    return f_consulta($dbConn,$array,$consulta,$Error,$detalle,$Acion,0);
}

//UPDATE
function f_update_empr($dbConn){
	$Id = $_POST['Id'];
	$consulta="SELECT * FROM tbl_a_especificacionesyp WHERE espyId = :id";
	$sql= $dbConn->prepare($consulta);
	$sql->bindValue(':id',$Id);
	$sql->execute();
	if($row = $sql->fetch()) {
        $empresa = 1;
        if (is_numeric($_POST["Variable"])) {
            $nuevo = intval($_POST["Variable"]);
            if (strlen($nuevo) <= 4) $empresa = $nuevo;
            else return 'El Código de la empresa ingresado supera el maximo especificado';
        }else return 'El Código de la empresa es incorrecto';

		$array =  array(array(":variable",":fecha",":id"),
						array($empresa,date("Y-m-d H:i:s"),$Id));
		$consulta = "UPDATE tbl_a_especificacionesyp SET espyCodigo_Empresa = :variable, espyFechaActua = :fecha WHERE espyId = :id";
		$Error = "Error al cambiar el código de la empresa Yupak";
        $Acion = "Cambio de código de la empresa Yupak";
        $detalle = $row["espyCodigo_Empresa"]." = > ".$empresa;
        return f_consulta($dbConn,$array,$consulta,$Error,$detalle,$Acion,$Id);
	}else return "ERROR-7765542";//NO SE ENCONTRO EL ID
}
function f_update_loca($dbConn){
	$Id = $_POST['Id'];
	$consulta="SELECT * FROM tbl_a_especificacionesyp WHERE espyId = :id";
	$sql= $dbConn->prepare($consulta);
	$sql->bindValue(':id',$Id);
	$sql->execute();
	if($row = $sql->fetch()) {
        $varibale = 1;
        if (is_numeric($_POST["Variable"])) {
            $nuevo = intval($_POST["Variable"]);
            if (strlen($nuevo) <= 4) $varibale = $nuevo;
            else return 'El Código de localidad ingresado supera el maximo especificado';
        }else return 'El Código de localidad es incorrecto';

		$array =  array(array(":variable",":fecha",":id"),
						array($varibale,date("Y-m-d H:i:s"),$Id));
		$consulta = "UPDATE tbl_a_especificacionesyp SET espyLocalidad = :variable, espyFechaActua = :fecha WHERE espyId = :id";
		$Error = "Error al cambiar el código de localidad";
        $Acion = "Cambio de código de localidad Yupak";
        $detalle = $row["espyLocalidad"]." = > ".$varibale;
        return f_consulta($dbConn,$array,$consulta,$Error,$detalle,$Acion,$Id);
	}else return "ERROR-7765542";//NO SE ENCONTRO EL ID
}
function f_update_caja($dbConn){
	$Id = $_POST['Id'];
	$consulta="SELECT * FROM tbl_a_especificacionesyp WHERE espyId = :id";
	$sql= $dbConn->prepare($consulta);
	$sql->bindValue(':id',$Id);
	$sql->execute();
	if($row = $sql->fetch()) {
        $varibale = 1;
        if (is_numeric($_POST["Variable"])) {
            $nuevo = intval($_POST["Variable"]);
            if (strlen($nuevo) <= 6) $varibale = $nuevo;
            else return 'El número de caja ingresado supera el maximo especificado';
        }else return 'El número de caja es incorrecto';

		$array =  array(array(":variable",":fecha",":id"),
						array($varibale,date("Y-m-d H:i:s"),$Id));
		$consulta = "UPDATE tbl_a_especificacionesyp SET espyCaja = :variable, espyFechaActua = :fecha WHERE espyId = :id";
		$Error = "Error al cambiar el número de caja Yupak" ;
        $Acion = "Cambio de número de caja Yupak";
        $detalle = $row["espyCaja"]." = > ".$varibale;
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
			if(Insert_Login($Id,'tbl_a_especificacionesyp',$Acion,$detalle,'')) return true;
			else return 'ERROR-092222';
		}else return "ERROR-665242";//
	}  catch (Exception $e) {
		Insert_Error('ERROR-887222',$e->getMessage(),$Error);
		exit("ERROR-887222");
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