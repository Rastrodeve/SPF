<?php
require '../../FilePHP/utils.php';
if (isset($_REQUEST['op'])) {
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
	elseif ($op==10)echo GET_DataEdit($dbConn);
	elseif ($op==11)echo Update_Detalle($dbConn);
	elseif ($op==12)echo Get_Data_New_Code($dbConn);
	elseif ($op==13)echo Get_Data_New_Peso($dbConn);
	elseif ($op==14)echo Comprobar_detalle_all($dbConn);
}else{
	header('location: ../../');
}
function Lista_de_Guias($dbConn){
	$resultado='<table id="tbl_detalle_guia" class="table table-sm table-bordered table-striped">
    <thead style="font-size:15px;">
        <tr>
            <th>#</th>
            <th>COMPROBANTE</th>
            <th>RUC</th>
            <th>CLIENTE</th>
            <th>TIPO</th>
            <th>CANTIDAD</th>
            <th>FECHA PROCESO</th>
            <th>SELECCIONAR</th>
        </tr>
    </thead>
    <tbody>';
    $consulta="SELECT p.gprId, g.guiNumero, p.gprComprobante, c.cliNumero ,c.cliNombres, e.espDescripcion,p.gprCantidad,p.gprTurno 
    FROM tbl_r_guiaproceso p, tbl_r_guiamovilizacion g, tbl_a_especies e, tbl_a_clientes c 
    WHERE p.guiId = g.guiId AND p.espId = e.espId AND p.cliId = c.cliId  AND
    g.guiEliminado = 0 AND  p.gprProcesado = 0 AND p.gprEliminado = 0 AND  p.gprestadoDetalle = 1 AND  p.gprEstado = 0
    ORDER BY p.gprTurno ASC ";
	$sql= $dbConn->prepare($consulta);
	$sql->execute();
	$cont = 0;
	while ($row = $sql->fetch()) {
		$cont++;
		$resultado .='
        <tr">
            <th>'.$cont.'</th>
            <td>'.$row["gprComprobante"].'</td>
            <td>'.$row["cliNumero"].'</td>
            <td>'.utf8_encode($row["cliNombres"]).'</b></td>
            <td>'.utf8_encode($row["espDescripcion"]).'</td>
            <td>'.$row["gprCantidad"].'</td>
            <td>'.$row["gprTurno"].'</td>
            <td >
                <button class="btn btn-info btn-sm" onclick="Mostrar('.$row["gprId"].')">
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
    $consulta="SELECT p.gprId, g.guiNumero, p.gprComprobante, c.cliNumero ,c.cliMarca ,c.cliNombres, e.espDescripcion,p.gprCantidad,p.gprTurno 
    FROM tbl_r_guiaproceso p, tbl_r_guiamovilizacion g, tbl_a_especies e, tbl_a_clientes c 
    WHERE p.guiId = g.guiId AND p.espId = e.espId AND p.cliId = c.cliId  AND
    g.guiEliminado = 0 AND  p.gprProcesado = 0 AND p.gprEliminado = 0 AND  p.gprestadoDetalle = 1 AND  p.gprEstado = 0
    ORDER BY p.gprTurno ASC ";
	$sql= $dbConn->prepare($consulta);
    $sql->bindValue(':id',$Id);
	$sql->execute();
	if ($row = $sql->fetch()) {
        $cliente = utf8_encode($row["cliNombres"]).' <b>'.utf8_encode($row["cliMarca"]).'</b>';
		$ecnabezado = Return_Encabezado($row["gprCantidad"],utf8_encode($row["espDescripcion"]),$row["gprComprobante"],$cliente,$row["cliNumero"]);
        $Tabla = Get_Data_Detalle_Normal($dbConn,$row["gprId"]);
        $Manual = GET_DataManual($dbConn,$row["gprId"]);
        $total_temporal = GET_DataTemporal($dbConn,$row["gprId"]);
        $restantes = $row["gprCantidad"] - $Manual - $total_temporal;
        $Body = Return_Body($Tabla,$Manual,$row["gprId"],$restantes);
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
function Return_Body($tabla,$manual,$id,$restantes){
    // <label for="txtCoidgo" class="mb-1 ml-1 ">Código del Animal</label>
    return '<div class="row">
    <input type="hidden" value="'.$id.'" id="inputId" >
    <div class="col-md-7">
        <div class="row">
            <div class="col-sm-4">
                <div class="input-group input-group-sm">
                    <input type="text" class="form-control input-sm" id="txtCoidgo" onKeyPress="enter_pesar(event)" 
                        placeholder="Código del Animal">
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <i class="fas fa-microchip"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="input-group input-group-sm">
                    <input type="text" class="form-control " id="txtPeso" disabled="disabled"
                        placeholder="Peso del Animal (Kg)">
                    <div class="input-group-append ">
                        <div class="input-group-text btn" id="btn-pesar" onclick="Pesar()">
                            <span><b>PESAR</b></span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-4 ">
                <button class="btn btn-success btn-sm" onclick="Ayadir_Temporal()" id="ayadir_tem">
                    <b>Añadir <i class="fas fa-plus ml-1"></i></b>
                </button>
            </div>
        </div>
        <div class="row">
            <div class="col-12 mt-4" >
                <h6><b>Lista del detallado normal</b></h6>
            </div>
            <div class="col-12 mt-0" id="cont-table-detalle">
            '.$tabla.'
            </div>
            <div class="col-12 mt-3">
                <button class="btn btn-info" onclick="Guardar_Detalle_completo()" id="guardar_detalle" > <b>GUARDAR DETALLE DE GUÍA</b> </button>
            </div>
        </div>
    </div>
    <div class="col-md-5">
        <div class="row">
            <div class="col-sm-6">
                <div class="info-box bg-danger">
                    <div class="info-box-content" id="div_manual">
                        <span class="info-box-text text-center">
                            DETALLADO MANUAL 
                        </span>
                        <span class="info-box-number text-center mb-0">'.$manual.'<span>
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
            <th>EDITAR</th>
        </tr>
    </thead>
    <tbody id="bd_tabla">';
    $consulta="SELECT dtpId,dtpCodigo,dtpPeso FROM tbl_r_detalle_temporal WHERE gprId = :id ORDER BY dtpFecha ASC";
	$sql= $dbConn->prepare($consulta);
    $sql->bindValue(':id',$Id);
	$sql->execute();
    $cont=0;
	while ($row = $sql->fetch()) {
        $cont++;
        $resultado .= '<tr>
                <td >'.$cont.'</td>
                <td id="td-code-'.$row["dtpId"].'">'.$row["dtpCodigo"].'</td>
                <td id="td-peso-'.$row["dtpId"].'">'.$row["dtpPeso"].'</td>
                <td>
                    <span class="badge badge-info p-2 my_badge" onclick="editar('.$row["dtpId"].')">
                        <i class="fas fa-pen"></i>
                    </span>
                </td>
            </tr>';
	}   
	return $resultado.'</tbody></table>';
}

function GET_DataManual($dbConn,$Id){
    $consulta="SELECT * FROM tbl_r_detalle WHERE gprId = :id ";
	$sql= $dbConn->prepare($consulta);
    $sql->bindValue(':id',$Id);
	$sql->execute();
    $cont=0;
	while ($row = $sql->fetch()) {
        $cont++;
	}   
	return $cont;
}
function GET_DataTemporal($dbConn,$Id){
    $consulta="SELECT * FROM tbl_r_detalle_temporal WHERE gprId = :id";
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
	$consulta="SELECT dtCodigo FROM tbl_r_detalle WHERE dtCodigo=:code AND dtProceso=0";
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
	$consulta="SELECT dtpCodigo FROM tbl_r_detalle_temporal WHERE dtpCodigo = :code";
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
    $consulta="SELECT gprCantidad FROM tbl_r_guiaproceso WHERE gprId = :id";
	$sql= $dbConn->prepare($consulta);
    $sql->bindValue(':id',$Id);
	$sql->execute();
    $cantidad_total=0;
	if($row = $sql->fetch()) {
        $cantidad_total= $row["gprCantidad"];
	}   
    if ($cantidad_total > 0) {
        $Manual = GET_DataManual($dbConn,$Id);
        $total_temporal = GET_DataTemporal($dbConn,$Id);
        $cantidad_ingresada =  $Manual + $total_temporal;
        if ($cantidad_ingresada < $cantidad_total) return true;
        else return false;
    }else{
        return "ERRROR:20201";
    }
}



function GuardarDetalle_Temporal($dbConn){
    try {
        global $User;
        global $Ip;
        $guia = $_POST['Id_Contador'];
        $Buscar="SELECT gprComprobante FROM tbl_r_guiaproceso WHERE gprId = :id";
        $sql1 = $dbConn->prepare($Buscar);
        $sql1->bindValue(':id',$guia);
        $sql1->execute();
        if($row = $sql1->fetch()) {
            $code = $_POST['Codigo'];
            $peso = $_POST['Peso'];
            $Fecha = date("Y-m-d H:i:s");
            $consulta = "INSERT INTO tbl_r_detalle_temporal(dtpCodigo,dtpPeso,dtpFecha,gprId,usuId,ip)
            VALUES(:code,:peso,:fecha,:guia,:cedula,:ip)";
            $sql= $dbConn->prepare($consulta);
            $sql->bindValue(':code',$code);
            $sql->bindValue(':peso',$peso);
            $sql->bindValue(':fecha',$Fecha);
            $sql->bindValue(':guia',$guia);
            $sql->bindValue(':cedula',$User);
            $sql->bindValue(':ip',$Ip);
            if ($result = $sql->execute()){
                $Id= $dbConn->lastInsertId();
                $Acion = "Ingreso de animal";
                $detalle = "<b>".$row["gprComprobante"]."</b><br>".
                "Código : ".$code."<br>".
                "Peso : ".$peso."";
                return Insert_Login($Id,'tbl_r_detalle_temporal',$Acion,$detalle,'');
            }else return $result;
        }else return 'ERROR-1212';   
    } catch (Exception $e) {
		Insert_Error('ERROR-887222',$e->getMessage(),'Error al ingresar un detalle temporal de guia');
		exit("ERROR-887222");
	}
}
function Get_Data_Detalle_Body($dbConn){
    $resultado ='';
    $Id = $_POST["Id"];
    $consulta="SELECT dtpId,dtpCodigo,dtpPeso FROM tbl_r_detalle_temporal WHERE gprId = :id  ORDER BY dtpFecha ASC";
	$sql= $dbConn->prepare($consulta);
    $sql->bindValue(':id',$Id);
	$sql->execute();
    $cont=0;
	while ($row = $sql->fetch()) {
        $cont++;
        $resultado .= '<tr>
                <td>'.$cont.'</td>
                <td id="td-code-'.$row["dtpId"].'">'.$row["dtpCodigo"].'</td>
                <td id="td-peso-'.$row["dtpId"].'">'.$row["dtpPeso"].'</td>
                <td>
                    <span class="badge badge-info p-2 my_badge" onclick="editar('.$row["dtpId"].')">
                        <i class="fas fa-pen"></i>
                    </span>
                </td>
            </tr>';
	}   
	return $resultado.'</tbody></table>';
}

function Info_box_manual($dbConn){
    $Id = $_POST["Id"];
    $manual = GET_DataManual($dbConn,$Id);
    return '<span class="info-box-text text-center">
        DETALLADO MANUAL 
        </span>
        <span class="info-box-number text-center mb-0">'.$manual.'<span>';
}
function Info_box_temporal($dbConn){
    $Id = $_POST["Id"];
    $restante = GET_DataTemporal($dbConn,$Id);
    $manual = GET_DataManual($dbConn,$Id);
    $consulta="SELECT gprCantidad FROM tbl_r_guiaproceso WHERE gprId = :id ";
	$sql= $dbConn->prepare($consulta);
    $sql->bindValue(':id',$Id);
	$sql->execute();
    $total = 0;
	if($row = $sql->fetch()) {
        $total = $row["gprCantidad"] - $restante - $manual;
	}   
    return '<span class="info-box-text text-center">
        POR REGISTRAR 
        </span>
        <span class="info-box-number text-center mb-0">'.$total.'<span>';
}
function GET_DataEdit($dbConn){
    $Id = $_POST["Id"];
    $consulta="SELECT dtpId,dtpCodigo,dtpPeso FROM tbl_r_detalle_temporal WHERE dtpId = :id ";
	$sql= $dbConn->prepare($consulta);
    $sql->bindValue(':id',$Id);
	$sql->execute();
    $result = false;
	if($row = $sql->fetch()) {
        $result = '<h5 class="text-center"><b> Editar Código del Animal</b></h5>
        <input type="hidden" id="txtId_Editar" value="'.$row["dtpId"].'">
        <div class="row">
            <div class="form-group col-6 m-0">
                <span>Código actual:</span>
                <div class="input-group ">
                    <input type="text" class="form-control" disabled="disabled" id="txtCodigoActual" value="'.$row["dtpCodigo"].'">
                </div>
            </div>
            <div class="form-group col-6 m-0">
                <span>Código nuevo:</span>
                <div class="input-group ">
                    <input type="text" class="form-control" id="txtCodigoEditar" value="'.$row["dtpCodigo"].'">
                </div>
            </div>
        </div>
        <hr>
        <h5 class="text-center"><b> Editar Peso del Animal</b></h5>
        <div class="row">
            <div class="form-group col-12">
                <span>Peso del Animal (Kg):</span>
                <div class="input-group input-group">
                    <input type="text" class="form-control " id="txtPesoNuevo" disabled="disabled" value="'.$row["dtpPeso"].'">
                    <div class="input-group-append ">
                        <div class="input-group-text btn" onclick="Pesar_nuevo()">
                            <span><b>NUEVO PESO</b></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <hr>
        <h5 class="text-center"><b> Motivo de la Edición</b></h5>
        <textarea  id="txtMotivo" class="form-control"  rows="3" placeholder="¿Por que se esta editando este registro?"></textarea>';
	}   
    return $result;
}

function Update_Detalle($dbConn){
    try {
        $code = $_POST['Codigo'];
        $peso = $_POST['Peso'];
        $razon = $_POST['Razon'];
        $Id = $_POST["Id"];
        $consulta="SELECT p.gprComprobante, t.dtpCodigo,t.dtpPeso,t.gprId FROM tbl_r_detalle_temporal t, tbl_r_guiaproceso p WHERE t.gprId = p.gprId AND t.dtpId = 1 ";
        $sql= $dbConn->prepare($consulta);
        $sql->bindValue(':id',$Id);
        $sql->execute();
        if($row = $sql->fetch()) {
            $consulta1 = "UPDATE tbl_r_detalle_temporal set dtpCodigo = :code , dtpPeso = :peso WHERE dtpId=:id";
            $sql1= $dbConn->prepare($consulta1);
            $sql1->bindValue(':code',$code);
            $sql1->bindValue(':peso',$peso);
            $sql1->bindValue(':id',$Id);
            if ($result = $sql1->execute()){
                $Acion = "Edición de animal";
                $detalle = "<b>".$row["gprComprobante"]."</b><br>".
                "Codigo: [ ".utf8_encode($row["dtpCodigo"])."] = > [ ".$code. " ]<br>".
                "Peso: [ ".utf8_encode($row["dtpPeso"])."] = > [ ".$peso. " ]<br>".
                "Razon:  ".$razon; 
                return Insert_Login($Id,'tbl_r_detalle_temporal',$Acion,$detalle,'');
            }else return $result;
        }else return false;
    } catch (Exception $e) {
        Insert_Error('ERROR-887222',$e->getMessage(),'Error al editar un detalle temporal de guia');
        exit("ERROR-887222");
    }
}
function Get_Data_New_Code($dbConn){
    $Id = $_POST["Id"];
    $resultado ='';
    $consulta="SELECT dtpCodigo,dtpPeso FROM tbl_r_detalle_temporal WHERE dtpId = :id";
	$sql= $dbConn->prepare($consulta);
    $sql->bindValue(':id',$Id);
	$sql->execute();
	if ($row = $sql->fetch()) {
        $resultado = $row["dtpCodigo"];
	}   
	return $resultado;
}
function Get_Data_New_Peso($dbConn){
    $Id = $_POST["Id"];
    $resultado ='';
    $consulta="SELECT dtpPeso FROM tbl_r_detalle_temporal WHERE dtpId = :id";
	$sql= $dbConn->prepare($consulta);
    $sql->bindValue(':id',$Id);
	$sql->execute();
	if ($row = $sql->fetch()) {
        $resultado = $row["dtpPeso"];
	}   
	return $resultado;
}
function Comprobar_detalle_all($dbConn){
    $Id = $_POST["Id"];
    $consulta="SELECT dtpCodigo FROM tbl_r_detalle_temporal WHERE gprId = :id";
	$sql= $dbConn->prepare($consulta);
    $sql->bindValue(':id',$Id);
	$sql->execute();
    $cont = 0;
    $Errores = '<b>Errores</b><br>';
	while($row = $sql->fetch()) {
        $bandera = Comprobar_detalle_all2($dbConn,$row["dtpCodigo"]);
        if ($bandera > 0) {
            $cont++;
            $Errores .= $cont .') '.$row["dtpCodigo"].' - '.$bandera.', ';
        }
	}   
    if ($cont == 0) return Insertar_detalle_Completo($dbConn,$Id);
    else return $Errores;
}
function Comprobar_detalle_all2($dbConn,$Codigo){
    $consulta="SELECT dtCodigo FROM tbl_r_detalle WHERE dtCodigo = :code AND dtProceso = 0";
	$sql= $dbConn->prepare($consulta);
    $sql->bindValue(':code',$Codigo);
	$sql->execute();
    $cont = 0;
	while($row = $sql->fetch()) {
        $cont++;
	}   
	return $cont;
}
function Insertar_detalle_Completo($dbConn,$Id){
    try {    
        $cont = 0;
        $Fecha = date("Y-m-d H:i:s");
        $Errores = '';
        $producto = selecionar_producto_predeterminado($dbConn,$Id);
        $Detalle = "";
        $Acion = "Detalle de guía";
        $consulta="SELECT * FROM tbl_r_detalle_temporal WHERE gprId = :id";
        $sql= $dbConn->prepare($consulta);
        $sql->bindValue(':id',$Id);
        $sql->execute();
        while($row = $sql->fetch()) {
            $bandera = Insert_detalle($dbConn,$row["dtpCodigo"],$row["dtpPeso"],$Fecha,$producto,$row["gprId"]);
            $Detalle .= "[ ".$row["dtpCodigo"]." => ".$row["dtpPeso"]." ]<br>";
            if ($bandera != true) {
                $cont++;
                $Errores .= $bandera.', ';
            }
        } 
        if ($cont == 0) {
            if (UpdateDetalle_Guia($dbConn,$Id)) {
                $compro = Get_comprobate_insert($dbConn,$Id);
                return Insert_Login($Id,'tbl_r_guiaproceso',$Acion,"<b>". $compro."</b><br>".$Detalle,'');
            }else return "ERROR-1212";
        }else return $Errores;
    } catch (Exception $e) {
		Insert_Error('ERROR-887222',$e->getMessage(),'Error al detallar la guia');
		exit("ERROR-887222");
	}
}
function Insert_detalle($dbConn,$code,$peso,$Fecha,$producto,$id_contador){
    try {    
        global $User;
        global $Ip;
        $consulta = "INSERT INTO tbl_r_detalle(dtCodigo,dtPeso,dtFecha,dtRegistro,proId,gprId,usuId,ip)
        VALUES (:code,:peso,:fecha,:registro,:producto,:gprId,:cedula,:ip)";
        $sql= $dbConn->prepare($consulta);
        $sql->bindValue(':code',$code);
        $sql->bindValue(':peso',$peso);
        $sql->bindValue(':fecha',$Fecha);
        $sql->bindValue(':registro',"0");
        $sql->bindValue(':producto',$producto);
        $sql->bindValue(':gprId',$id_contador);
        $sql->bindValue(':cedula',$User);
        $sql->bindValue(':ip',$Ip);
        if ($result = $sql->execute()){
            return true;
        }else return $result;
    } catch (Exception $e) {
		Insert_Error('ERROR-887244',$e->getMessage(),'Error al insertar detalle');
		exit("ERROR-887244");
	}
}
function selecionar_producto_predeterminado($dbConn,$Id){
    $consulta="SELECT espId FROM tbl_r_guiaproceso where gprId = :id";
	$sql= $dbConn->prepare($consulta);
    $sql->bindValue(':id',$Id);
	$sql->execute();
    $cont = 0;
	if($row = $sql->fetch()) {
        $consulta2="SELECT proId FROM tbl_a_productos Where espId = :code ";
        $sql2= $dbConn->prepare($consulta2);
        $sql2->bindValue(':code',$row["espId"]);
        $sql2->execute();
        if($row2 = $sql2->fetch()) {
            return $row2["proId"];
        }else return 'ER01';
	} else return 'ER01';
}
function UpdateDetalle_Guia($dbConn,$Id){
	$consulta = "UPDATE tbl_r_guiaproceso SET gprEstado = 1  where gprId = :id";
	$sql= $dbConn->prepare($consulta);
	$sql->bindValue(':id',$Id);
	if ($result = $sql->execute()){
		return DELETEdetalle_Guia($Id,$dbConn);
	}else return $result;
}
function DELETEdetalle_Guia($Id,$dbConn2){
    global $dbEl;
    $dbConn = conectar($dbEl);
	$consulta = "DELETE FROM tbl_r_detalle_temporal WHERE gprId = :id";
	$sql= $dbConn->prepare($consulta);
	$sql->bindValue(':id',$Id);
	if ($result = $sql->execute())return true;
    else return $result;
}
function Get_comprobate_insert($dbConn,$Id){
    $Id = $_POST["Id"];
    $resultado ='';
    $consulta="SELECT gprComprobante FROM tbl_r_guiaproceso WHERE gprId = :id";
	$sql= $dbConn->prepare($consulta);
    $sql->bindValue(':id',$Id);
	$sql->execute();
	if ($row = $sql->fetch()) {
        $resultado = $row["gprComprobante"];
	}   
	return $resultado;
}
?>
