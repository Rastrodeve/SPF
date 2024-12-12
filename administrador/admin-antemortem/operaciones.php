<?php
if (isset($_REQUEST['op'])) {
	require '../../FilePHP/utils.php';
	$dbConn = conectar($db);
	$op=$_REQUEST['op'];
	if ($op==1) echo Listar_Especie_Animales($dbConn);//Yes
    elseif($op==2) echo  get_data_new_cabecera($dbConn);
    elseif($op==3) echo  f_insert_cabecera($dbConn);
    elseif($op==4) echo  get_data_update_cabecera($dbConn);
    elseif($op==5) echo  f_update_cabecera($dbConn);
    elseif($op==6) echo  f_update_estado_cabecera($dbConn);
    elseif($op==7) echo  get_data_new_item($dbConn);
    elseif($op==8) echo  f_new_item($dbConn);
    elseif($op==9) echo  f_update_estado_item($dbConn);
    elseif($op==10) echo  get_data_update_item($dbConn);
    elseif($op==11) echo f_update_item($dbConn);


}else header("location: ./");

function Listar_Especie_Animales($dbConn){
	$resultado="";
	$consulta="SELECT * FROM tbl_a_cabeceraAM";
	$sql= $dbConn->prepare($consulta);
	$sql->execute();
	while ($row = $sql->fetch()) {
        $table = Consultar_Datos_productos($dbConn,$row["camId"]);
        $estado = "ERROR";
        $card = "";
        if ($row["camEstado"]==0) {
			$estado ='<button type="button" onclick="f_estado(1,'.$row["camId"].')" class="btn btn-success btn-sm"><b>ACTIVO</b></button>';
		}else if ($row["camEstado"]==1){
            $card = "card-danger";
			$estado ='<button type="button" onclick="f_estado(0,'.$row["camId"].')" class="btn btn-danger btn-sm"><b>INACTIVO</b></button>';
		}
        $resultado .='
            <div class="row">
                <div class="col-md-12">
                    <div class="card '.$card.'  collapsed-card">
                        <div class="card-header " data-card-widget="collapse" data-toggle="tooltip" title="Collapse" style="cursor: pointer;" >
                            <h1 class="card-title"  >
								<b>'.strtoupper(utf8_encode($row["camDescripcion"])).'</b>
                            </h1>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12" >
                                    <button type="button"  class="btn btn-info btn-sm" data-toggle="modal" data-target="#modal" onclick="get_update('.$row["camId"].')"	>
                                        <b>EDITAR CABECERA</b>
                                    </button>
                                    '.$estado.'
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-md-12" >'.$table.'</div>
                            </div>
                            <button type="button"  class="btn btn-info btn-sm" data-toggle="modal"
                            data-target="#modal" onclick="get_new_item('.$row["camId"].')"	>
                                <b><i class="fas fa-plus"></i> AÑADIR ITEM</b>
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
                            <th>Descripción</th>
                            <th>Estado</th>
                            <th>Editar</th>
                        </tr>
                    </thead><tbody>';
    $consulta="SELECT * FROM tbl_a_itemAM WHERE camId = :id";
	$sql= $dbConn->prepare($consulta);
	$sql->bindValue(':id',$Id);
	$sql->execute();
	$cont = 0;
	$Total=0;
	while ($row = $sql->fetch()) {
		$cont++;
		$estado ="ERROR";
		if ($row["iamEstado"]==0) {
			$estado ='<button type="button" onclick="f_estado_item(1,'.$row["iamId"].')" class="btn btn-success btn-sm"><b>ACTIVO</b></button>';
		}else if ($row["iamEstado"]==1){
			$estado ='<button type="button" onclick="f_estado_item(0,'.$row["iamId"].')" class="btn btn-danger btn-sm"><b>INACTIVO</b></button>';
		}
		$resultado=$resultado.'
            <tr>
                <td>'.$cont.'</td>
                <td >'.utf8_encode($row["iamDescripcion"]).'</td>
                <td >'.$estado.'</td>
                <td>
                    <button  type="button" class="btn btn-sm btn-info" data-toggle="modal"
                    data-target="#modal"  onclick="get_data_update_item('.$row["iamId"].')" >
                        <b><i class="fas fa-edit"></i></b>
                    </button>
                </td>
            </tr';
	}

	return $resultado."</tbody></table>";
}
//GET DATA
    function get_data_new_cabecera($dbConn){	
        $data = '
        <div class="row">
            <div class="col-md-12">
                <h6 class="text-muted"><b>NUEVA CABECERA PARA LAS INSPECCIONES ANTEMORTEM </b> </h6>
            </div>
        </div>
        <hr class="mt-2">
        <div class="row">
            <div class="col-md-12">
                <label for="txtCabecera">Descripción:</label><span class="text-muted">(max 30)</span>
                <input type="text" class="form-control form-control-sm" maxlength="30" id="txtCabecera" placeholder="LOCOMOCIÓN">
            </div>
        </div>';
        return modal($data,'AÑADIR NUEVA CABECERA','f_new_cabecera()');
    }

    function get_data_update_cabecera($dbConn){
        $Id = $_POST['Id'];
        $consulta="SELECT * FROM tbl_a_cabeceraAM WHERE camId = :id";
        $sql= $dbConn->prepare($consulta);
        $sql->bindValue(':id',$Id);
        $sql->execute();
        if($row = $sql->fetch()) {
            $data = '
                <input type="hidden" value="'.$row["camId"].'" id="txtIdCabecera">
                <div class="row">
                    <div class="col-md-12">
                        <h6 class="text-muted"><b>EDITAR CABECERA PARA LAS INSPECCIONES ANTEMORTEM </b> </h6>
                    </div>
                </div>
                <hr class="mt-2">
                <div class="row">
                    <div class="col-md-12">
                        <label for="txtCabecera">Descripción:</label>
                        <span class="text-muted form-control form-control-sm">'.utf8_encode($row["camDescripcion"]).'</span>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-md-12">
                        <label for="txtCabecera">Descripción:</label><span class="text-muted">(max 30)</span>
                        <input type="text" class="form-control form-control-sm" maxlength="30" id="txtCabecera" value="'.utf8_encode($row["camDescripcion"]).'" placeholder="LOCOMOCIÓN">
                    </div>
                </div>';
                return modal($data,'EDITAR CABECERA','f_update_cabecera()');
        }else return modal('ERROR-21','ERROR-21','');

    }
    function get_data_new_item($dbConn){
        $Id = $_POST['Id'];
        $consulta="SELECT * FROM tbl_a_cabeceraAM WHERE camId = :id";
        $sql= $dbConn->prepare($consulta);
        $sql->bindValue(':id',$Id);
        $sql->execute();
        if($row = $sql->fetch()) {
            $data = '
                <input type="hidden" value="'.$row["camId"].'" id="txtIdCabecera">
                <div class="row">
                    <div class="col-md-12">
                        <h6 class="text-muted"><b>NUEVO ITEM PARA LA CABECERA '.utf8_encode($row["camDescripcion"]).' </b> </h6>
                    </div>
                </div>
                <hr class="mt-2">
                <div class="row">
                    <div class="col-md-12">
                        <label for="txtItem">Descripción del Item:</label><span class="text-muted">(max 30)</span>
                        <input type="text" class="form-control form-control-sm" maxlength="30" id="txtItem"  placeholder="SINDROME DIGESTIVO">
                    </div>
                </div>';
                return modal($data,'NUEVA ITEM DE CABECERA','f_new_item()');
        }else return modal('ERROR-21','ERROR-21','');
    }
    function get_data_update_item($dbConn){
        $Id = $_POST['Id'];
        $consulta="SELECT * FROM tbl_a_itemAM i, tbl_a_cabeceraAM c WHERE i.camId = c.camId AND  i.iamId = :id";
        $sql= $dbConn->prepare($consulta);
        $sql->bindValue(':id',$Id);
        $sql->execute();
        if($row = $sql->fetch()) {
                $data = '
                <input type="hidden" value="'.$row["iamId"].'" id="txtIdItem">
                <div class="row">
                    <div class="col-md-12">
                        <h6 class="text-muted"><b>EDIRAR ITEM PARA LA CABECERA '.utf8_encode($row["camDescripcion"]).' </b> </h6>
                    </div>
                </div>
                <hr class="mt-2">
                <div class="row">
                    <div class="col-md-12">
                        <label for="txtItem">Descripción acctual del Item:</label>
                        <span class="text-muted form-control form-control-sm">'.utf8_encode($row["iamDescripcion"]).'</span>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-md-12">
                        <label for="txtItem">Nueva Descripción del Item:</label><span class="text-muted">(max 30)</span>
                        <input type="text" class="form-control form-control-sm" maxlength="30" id="txtItem" value="'.utf8_encode($row["iamDescripcion"]).'"  placeholder="SINDROME DIGESTIVO">
                    </div>
                </div>';
                return modal($data,'NUEVA ITEM DE CABECERA','f_update_item()');
        }else return modal('ERROR-21','ERROR-21','');   
    }

//Insert
    function f_insert_cabecera($dbConn){
        $Descripcion = trim($_POST['Descripcion']);
        $arrayEs = array(array(":variable"),array($Descripcion));
        if (f_comprobar($dbConn,$arrayEs,"SELECT * FROM tbl_a_cabeceraAM WHERE camDescripcion = :variable ",'Comprobar cabecera repetida')==true)return 'La descripcion ingresada ya se encuentra agregada';
        $consulta = "INSERT INTO tbl_a_cabeceraAM(camDescripcion) VALUES(:camDescripcion) ";
        $array =  array(array(":camDescripcion"), array($Descripcion));
        $Error ="Insertar nueva cabecera";
        $Acion ="Insertar nueva cabecera";
        $detalle='Descripcion de la cabecera <b>'.$Descripcion.'</b>';
        return f_consulta($dbConn,$array,$consulta,$Error,$detalle,$Acion,0);
    }
    function f_update_cabecera($dbConn){
        $Id = $_POST['Id'];
        $consulta="SELECT * FROM tbl_a_cabeceraAM WHERE camId = :id";
        $sql= $dbConn->prepare($consulta);
        $sql->bindValue(':id',$Id);
        $sql->execute();
        if($row = $sql->fetch()) {
            $Descripcion = trim($_POST['Descripcion']);
            $arrayEs = array(array(":variable",":id"),array($Descripcion,$Id));
            if (f_comprobar($dbConn,$arrayEs,"SELECT * FROM tbl_a_cabeceraAM WHERE camDescripcion = :variable AND camId != :id",'Comprobar cabecera repetida')==true)return 'La descripcion ingresada ya se encuentra agregada';
            $consulta = "UPDATE tbl_a_cabeceraAM SET camDescripcion = :camDescripcion WHERE  camId = :id";
            $array =  array(array(":camDescripcion",":id"), array($Descripcion,$Id));
            $Error ="Editar cabecera";
            $Acion ="Editar cabecera";
            $detalle=''.utf8_encode($row["camDescripcion"]).' => <b>'.$Descripcion.'</b>';
            return f_consulta($dbConn,$array,$consulta,$Error,$detalle,$Acion,$Id);
        }else return 'ERROR-2121';
    }
    function f_new_item($dbConn){
        $Id = $_POST['Id'];
        $consulta="SELECT * FROM tbl_a_cabeceraAM WHERE camId = :id";
        $sql= $dbConn->prepare($consulta);
        $sql->bindValue(':id',$Id);
        $sql->execute();
        if($row = $sql->fetch()) {
            $Descripcion = trim($_POST['Descripcion']);
            $arrayEs = array(array(":variable"),array($Descripcion));
            if (f_comprobar($dbConn,$arrayEs,"SELECT * FROM tbl_a_itemAM WHERE iamDescripcion = :variable ",'Comprobar item repetida')==true)return 'La descripcion del item ingresada ya se encuentra agregada';
            $consulta = "INSERT INTO tbl_a_itemAM(iamDescripcion,camId) VALUES(:iamDescripcion,:id)";
            $array =  array(array(":iamDescripcion",":id"), array($Descripcion,$Id));
            $Error ="Nuevo Item";
            $Acion ="Nuevo Item";
            $detalle='NUEVO ITEM "'.$Descripcion.'" PARA LA CABECERA'.utf8_encode($row["camDescripcion"]);
            return f_consulta_2($dbConn,$array,$consulta,$Error,$detalle,$Acion,0);
        }else return 'ERROR-28632';
    }
//UPDATE
    function f_update_estado_cabecera($dbConn){
        $Id = $_POST['Id'];
        $Estado = $_POST['Estado'];
        $consulta="SELECT * FROM tbl_a_cabeceraAM WHERE camId = :id";
        $sql= $dbConn->prepare($consulta);
        $sql->bindValue(':id',$Id);
        $sql->execute();
        if($row = $sql->fetch()) {
            $detalle = "";
            $Acion = "";
            if ($Estado==0){
                $Acion = "Estado de la cabecera";
                $detalle = "Se activo la cabecera (".utf8_encode($row["camDescripcion"]).")";
            }else if ($Estado==1){
                $Acion = "Estado la cabecera";
                $detalle = "Se inactivo la cabecera (".utf8_encode($row["camDescripcion"]).")";
            }else {
                return "No se encontro el estado de la cabecera";
            }
            $array =  array(array(":estado",":id"),
                            array($Estado,$Id));
            $consulta = "UPDATE tbl_a_cabeceraAM SET camEstado = :estado WHERE camId = :id";
            $Error = "Error al actualizar el estado de la cabecera (".$Estado.")";
            return f_consulta($dbConn,$array,$consulta,$Error,$detalle,$Acion,$Id);
        }else return 'ERROR-22221';
    }
    function f_update_estado_item($dbConn){
        $Id = $_POST['Id'];
        $Estado = $_POST['Estado'];
        $consulta="SELECT * FROM tbl_a_itemAM WHERE iamId = :id";
        $sql= $dbConn->prepare($consulta);
        $sql->bindValue(':id',$Id);
        $sql->execute();
        if($row = $sql->fetch()) {
            $detalle = "";
            $Acion = "";
            if ($Estado==0){
                $Acion = "Estado del Item";
                $detalle = "Se activo el item (".utf8_encode($row["iamDescripcion"]).")";
            }else if ($Estado==1){
                $Acion = "Estado del item";
                $detalle = "Se inactivo el item (".utf8_encode($row["iamDescripcion"]).")";
            }else {
                return "No se encontro el estado del item";
            }
            $array =  array(array(":estado",":id"),
                            array($Estado,$Id));
            $consulta = "UPDATE tbl_a_itemAM SET iamEstado = :estado WHERE iamId = :id";
            $Error = "Error al actualizar el estado del item (".$Estado.")";
            return f_consulta($dbConn,$array,$consulta,$Error,$detalle,$Acion,$Id);
        }else return 'ERROR-22221';
    }
    function f_update_item($dbConn){
        $Id = $_POST['Id'];
        $consulta="SELECT * FROM tbl_a_itemAM WHERE iamId = :id";
        $sql= $dbConn->prepare($consulta);
        $sql->bindValue(':id',$Id);
        $sql->execute();
        if($row = $sql->fetch()) {
            $Descripcion = trim($_POST['Descripcion']);
            $arrayEs = array(array(":variable",":id"),array($Descripcion,$Id));
            if (f_comprobar($dbConn,$arrayEs,"SELECT * FROM tbl_a_itemAM WHERE iamDescripcion = :variable AND iamId != :id ",'Comprobar item repetida')==true)return 'La descripcion del item ingresada ya se encuentra agregada';
            $consulta = "UPDATE tbl_a_itemAM SET iamDescripcion =:iamDescripcion WHERE iamId = :id";
            $array =  array(array(":iamDescripcion",":id"), array($Descripcion,$Id));
            $Error ="Editar Itema";
            $Acion ="Editar Item";
            $detalle=''.utf8_encode($row["iamDescripcion"]).' => <b>'.$Descripcion.'</b>';
            return f_consulta_2($dbConn,$array,$consulta,$Error,$detalle,$Acion,$Id);
        }else return 'ERROR-22221';
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
			if(Insert_Login($Id,'tbl_a_cabeceraAM',$Acion,$detalle,'')) return true;
			else return 'ERROR-092222';
		}else return "ERROR-665242";//
	}  catch (Exception $e) {
		Insert_Error('ERROR-887222',$e->getMessage(),$Error);
		exit("ERROR-887222");
	}
}
function f_consulta_2($dbConn,$array,$consulta,$Error,$detalle,$Acion,$Id){
	try {
		$sql= $dbConn->prepare($consulta);
		for ($i=0; $i < count($array[0]); $i++) { 
			$sql->bindValue($array[0][$i],utf8_decode($array[1][$i]));
		}
		if ($sql->execute()){
			if ($Id=0)$Id= $dbConn->lastInsertId();
			if(Insert_Login($Id,'tbl_a_itemAM',$Acion,$detalle,'')) return true;
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

function selecoption($number_select){
	$resultado = '';
	for ($i=1; $i < 3 ; $i++) { 
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