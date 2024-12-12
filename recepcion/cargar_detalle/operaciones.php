<?php
session_start();
$User = $_SESSION['MM_Username'];
$Equipo=  "WEB";
$Ip = $_SERVER['REMOTE_ADDR'];
$FIle_Name = $User."-data";
if (isset($_REQUEST['op'])) {
	require '../../FilePHP/utils.php';
	$dbConn = conectar($db);
	$op=$_REQUEST['op'];
	if ($op==1)echo save_file();
	elseif ($op==2)echo read_data($dbConn);
	elseif ($op==3)echo Get_Data_Excel($dbConn);
	elseif ($op==4)echo Delete_data_excel($dbConn);
	elseif ($op==5)echo GET_DataEdit($dbConn);
	elseif ($op==6)echo Update_Detalle_excel($dbConn);
    elseif ($op==7) {
        $code = $_POST["Code"];
        echo Comprobar_Codigo_tbl($dbConn,$code);
    }
    elseif ($op==8) {
        $code = $_POST["Code"];
        echo Comprobar_Codigo_Temporal_tbl($dbConn,$code);
    }elseif ($op==9)echo Comprobar_Codigo_Temporal_Excel_post($dbConn);

}else{
	header('location: ../../');
}
function save_file(){
    global $FIle_Name;
    $nombre_temporal = $_FILES['archivo']['tmp_name'];
    $nombre = $_FILES['archivo']['name'];
    $file = new SplFileInfo($nombre);
    $extension  = $file->getExtension();
    $FIle_Name .= '.'.$extension;
    move_uploaded_file($nombre_temporal, 'files/' .$FIle_Name);
    $_SESSION['File_Name']= $FIle_Name;
}
function read_data($dbConn){
    require_once 'PHPExcel/Classes/PHPExcel.php';
    $archivo = 'files/'.$_SESSION['File_Name'];
    $inputFileType = PHPExcel_IOFactory::identify($archivo);
    $objReader = PHPExcel_IOFactory::createReader($inputFileType);
    $objPHPExcel = $objReader->load($archivo);
    $sheet = $objPHPExcel->getSheet(0); 
    $highestRow = $sheet->getHighestRow(); 
    $highestColumn = $sheet->getHighestColumn();
    for ($row = 2; $row <= $highestRow; $row++){
        $codigo =$sheet->getCell("B".$row)->getValue();
        $peso =$sheet->getCell("C".$row)->getValue();
        $bandera_code = Verificar_codigo($dbConn,$codigo);
        // $mensaje_code = 'Puede proceder';
        $nivel = 0;
        if ($bandera_code != false) {
            // $mensaje_code =$bandera_code;
            // $adver_code = 'table-danger';
            $nivel = 2;
        }
        if (!is_numeric($peso)) {
            // $mensaje_code .= 'El <b>Peso</b> debe ser un <b>número</b>';
            if ($bandera_code == false) {
                // $adver_code = 'table-warning';
                // $mensaje_code ='El <b>Peso</b> debe ser un <b>número</b>';
                $nivel = 1;
            }
        }
        Insert_data_Excel($dbConn,$codigo,$peso,$nivel);
        }
        
        $datos_return = Get_Data_Excel($dbConn);
        return $datos_return;
}
function Get_Data_Excel($dbConn){
    global $User;
    $resultado = '<table width="100%" id="tbl_data_excel"
        class="table-bordered table-striped table-hover table-sm text-center p-0">
        <thead>
            <th>#</th>
            <th>Código</th>
            <th>Peso</th>
            <th>Obsrvaciones</th>
        </thead>
        <tbody>';
    $consulta="SELECT * FROM tbl_detalle_temporal_excel_new WHERE cedula = :cedula";
	$sql= $dbConn->prepare($consulta);
    $sql->bindValue(':cedula',$User);
	$sql->execute();
	$cont = 0;
	while ($row = $sql->fetch()) {
		$cont++;
        $codigo =$row["excCodigo"];
        $peso =$row["excPeso"];
        $bandera_code = Verificar_codigo($dbConn,$codigo);
        $mensaje_code = 'Puede proceder';
        $adver_code ='';
        if ($bandera_code != false) {
            $mensaje_code =$bandera_code;
            $adver_code = 'table-danger';
        }
        if (!is_numeric($peso)) {
            $mensaje_code .= 'El <b>Peso</b> debe ser un <b>número</b>';
            if ($bandera_code == false) {
                $adver_code = 'table-warning';
                $mensaje_code ='El <b>Peso</b> debe ser un <b>número</b>';
            }
        }
		$resultado .='
        <tr  onclick="editar('.$row["excId"].')" class="'.$adver_code.'">
            <th>'.$cont.'</th>
            <td id="td-code-'.$row["excId"].'">'.$codigo.'</td>
            <td id="td-peso-'.$row["excId"].'">'.$peso.'</td>
            <td>'.$mensaje_code.'</td>
        </tr>';
	}
    $retornar = '
                <h5 >
                    <button class="btn btn-danger " onclick="regresar()">
                        <b>REGRESAR</b> 
                    </button>
                </h5>
                <hr>'.$resultado.'';
	if ($cont == 0) {
        return false;
        unlink('files/'.$_SESSION['File_Name']);
    }else {
        return $retornar;
    }
}
function Insert_data_Excel($dbConn,$Code,$Peso,$nivel){
    global $User;
    global $Ip;
    global $Equipo;
    $consulta = "INSERT INTO tbl_detalle_temporal_excel_new(excCodigo,excPeso,excNivel,cedula,ip,maquina)
    VALUES(:code,:peso,:nivel,:cedula,:ip,:maquina)";
	$sql= $dbConn->prepare($consulta);
	$sql->bindValue(':code',$Code);
	$sql->bindValue(':peso',$Peso);
	$sql->bindValue(':nivel',$nivel);
	$sql->bindValue(':cedula',$User);
	$sql->bindValue(':ip',$Ip);
	$sql->bindValue(':maquina',$Equipo);
	if ($result = $sql->execute()){
		return true;
	}else return $result;
}
function Verificar_codigo($dbConn,$codigo){
    $bandera_codigo = Comprobar_Codigo_tbl($dbConn,$codigo);
    $bandera_codigo_temporal = Comprobar_Codigo_Temporal_tbl($dbConn,$codigo);
    $bandera_codigo_temporal_excel = Comprobar_Codigo_Temporal_Excel($dbConn,$codigo);
    $observacion_codigo = '';
    $cont = 0;
    if ($bandera_codigo == true) {
        $observacion_codigo .= 'El <b>Código</b> esta ocupado por un animal <b>No Procesado</b><br>';
    }else $cont++;
    if ($bandera_codigo_temporal == true) {
        $observacion_codigo .= 'Este <b>Código</b> esta utilizado en el detalle Temporal de otra <b>Guía</b><br>';
    }else $cont++;
    if ($bandera_codigo_temporal_excel == true) {
        $observacion_codigo .= 'Este <b>Código</b> esta temporalmente <b>ocupado</b><br>';
    }else $cont++;

    if ($cont==0) return false;
    else return $observacion_codigo;
}
function Delete_data_excel($dbConn){
    global $User;
	$consulta = "DELETE FROM tbl_detalle_temporal_excel_new WHERE cedula = :cedula";
	$sql= $dbConn->prepare($consulta);
	$sql->bindValue(':cedula',$User);
	if ($result = $sql->execute()){
		return true;
	}else return $result;
}
function Comprobar_Codigo_tbl($dbConn,$code){
	$comp=false;
	$consulta="SELECT dtCodigo FROM tbl_detalle_guia_new WHERE dtCodigo=:code AND dtProceso=0";
	$sql= $dbConn->prepare($consulta);
	$sql->bindValue(':code',$code);
	$sql->execute();
	if($row = $sql->fetch()) {
		$comp= true;
	}
	return $comp;
}
function Comprobar_Codigo_Temporal_tbl($dbConn,$code){
	$comp=false;
	$consulta="SELECT dtpCodigo FROM tbl_detalle_temporal_new WHERE dtpCodigo = :code";
	$sql= $dbConn->prepare($consulta);
	$sql->bindValue(':code',$code);
	$sql->execute();
	if($row = $sql->fetch()) {
		$comp= true;
	}
	return $comp;
}
function Comprobar_Codigo_Temporal_Excel($dbConn,$code){
	$comp=false;
	$consulta="SELECT excCodigo FROM tbl_detalle_temporal_excel_new WHERE excCodigo = :code";
	$sql= $dbConn->prepare($consulta);
	$sql->bindValue(':code',$code);
	$sql->execute();
    $cont=0;
	while($row = $sql->fetch()) {
        $cont++;
	}
    if ($cont == 1) {
        return false;
    }elseif ($cont > 1) {
        return true;
    }
}
function Comprobar_Codigo_Temporal_Excel_post($dbConn){
    $code = $_POST["Code"];
    $id = $_POST["Id"];
    $comp = false;
	$consulta="SELECT excCodigo FROM tbl_detalle_temporal_excel_new WHERE excCodigo = :code AND excId != :id";
	$sql= $dbConn->prepare($consulta);
	$sql->bindValue(':code',$code);
	$sql->bindValue(':id',$id);
	$sql->execute();
    $cont=0;
	if($row = $sql->fetch()) {
        $comp = true;
	}
    return $comp;
}
function Update_Detalle_excel($dbConn){
    $code = $_POST['Codigo'];
	$peso = $_POST['Peso'];
    $Id = $_POST["Id"];
    $consulta = "UPDATE tbl_detalle_temporal_excel_new set excCodigo = :code , excPeso = :peso WHERE excId=:id";
	$sql= $dbConn->prepare($consulta);
	$sql->bindValue(':code',$code);
	$sql->bindValue(':peso',$peso);
	$sql->bindValue(':id',$Id);
	if ($result = $sql->execute()){
		return true;
	}else return $result;
}
function GET_DataEdit($dbConn){
    $Id = $_POST["Id"];
    $consulta="SELECT excId,excCodigo,excPeso FROM tbl_detalle_temporal_excel_new WHERE excId = :id";
	$sql= $dbConn->prepare($consulta);
    $sql->bindValue(':id',$Id);
	$sql->execute();
    $result = false;
	if($row = $sql->fetch()) {
        $result = '<h5 class="text-center"><b> Editar Código del Animal</b></h5>
        <input type="hidden" id="txtId_Editar" value="'.$row["excId"].'">
        <div class="row">
            <div class="form-group col-6 m-0">
                <span>Código actual:</span>
                <div class="input-group ">
                    <input type="text" class="form-control" disabled="disabled" id="txtCodigoActual" value="'.$row["excCodigo"].'">
                </div>
            </div>
            <div class="form-group col-6 m-0">
                <span>Código nuevo:</span>
                <div class="input-group ">
                    <input type="text" class="form-control" id="txtCodigoEditar" value="'.$row["excCodigo"].'">
                </div>
            </div>
        </div>
        <hr>
        <h5 class="text-center"><b> Editar Peso del Animal</b></h5>
        <div class="row">
        <div class="form-group col-6">
            <span>Peso del actual:</span>
            <div class="input-group input-group">
                <input type="text" class="form-control "  disabled="disabled" value="'.$row["excPeso"].'">
                <div class="input-group-append ">
                    <div class="input-group-text " >
                    <span><i class="fas fa-weight-hanging"></i></span>
                    </div>
                </div>
            </div>
        </div>
        <div class="form-group col-6">
            <span>Peso del nuevo:</span>
            <div class="input-group input-group">
                <input type="text" class="form-control " id="txtPesoNuevo"  value="'.$row["excPeso"].'">
                <div class="input-group-append ">
                    <div class="input-group-text " >
                        <span><i class="fas fa-weight-hanging"></i></span>
                    </div>
                </div>
            </div>
        </div>
    </div>';
	}   
    return $result;
}







?>
