<?php
require '../../FilePHP/utils.php';
if (isset($_REQUEST['op'])) {
	$dbConn = conectar($db);
	$op=$_REQUEST['op'];
	if ($op==1)echo get_data_cont($dbConn);
	elseif ($op==2) $_SESSION['ORIGEN'][0] = $_POST["Id"];
	elseif ($op==3)echo get_data_table($dbConn,$_SESSION['DECOMISO'][0]);
	elseif ($op==4)echo $_SESSION['ORIGEN'][1] = $_POST["Id"];
    elseif ($op==5) $_SESSION['ORIGEN'][0] = 0;
    elseif ($op==6) echo f_get_data_transport($dbConn);
    elseif ($op==7)$_SESSION['DATOSORIGEN'] = $_POST["ArrayDatos"];//IdParroquia,Direccion,IdVehiculo,Observaciones,Tipo de producto a movilizar
    elseif ($op==8) {
        $_SESSION['DATOSORIGEN'] = array(0,'',0,'','');//IdParroquia,Direccion,IdVehiculo,Observaciones,Tipo de producto a movilizar
        $_SESSION['PRODCUTOS']= array();
        $_SESSION['SUBPRODUCTOS']= array();
    }
    elseif ($op==9)echo f_modal_productos($dbConn);
    elseif ($op==10)$_SESSION['PRODCUTOS'] = $_POST["ArrayDatos"];
    elseif ($op==11)echo table_prodcutos($dbConn);
    elseif ($op==12)echo delete_prodcuto();
    elseif ($op==13)echo f_modal_subproductos($dbConn);
    elseif ($op==14)$_SESSION['SUBPRODUCTOS'] = $_POST["ArrayDatos"];
    elseif ($op==15)echo table_subprodcutos($dbConn);
    elseif ($op==16)echo delete_subprodcuto();
    elseif ($op==17)echo f_insert_origen($dbConn);
    elseif ($op==18)echo $_SESSION['ORIGEN'][1] = 0;
    elseif ($op==19)echo select_data_cantones($dbConn,$_POST["Id"]);
    elseif ($op==20)echo select_data_parroquias($dbConn,$_POST["Id"]);
    // elseif ($op==18)echo $_SESSION['ORIGEN'][1] = 0;
}else{
	header('location: ../../');
}

function get_data_cont($dbConn){
    $opcion = $_SESSION['ORIGEN'][0];
    if ($opcion == 0) {
        return '
        <select class="form-control form-control-lg select2bs4" onchange="f_mensaje()" id="slcTipo" style="width:100%;">
        '.select_data_especies($dbConn).'
        </select>
        <hr>
        <div id="cont-result"></div>';
    }else{
        if ($_SESSION['ORIGEN'][1]== 0) {
            return '
            <div class="card">
                <div class="card-body">
                    <div class="row mb-2">
                        <div class="col-6">
                            <b>
                                '.get_data_especie($dbConn,$opcion).'
                            </b>
                        </div>
                        <div class="col-6">
                            <button class="btn btn-danger btn-sm float-right" onclick="f_regresar()"><b>REGRESAR</b></button>
                        </div>
                    </div>
                    <hr>
                    <div id="cont-contenido">
                        <div id="cont-table-fae">
                        '.get_data_table($dbConn,$opcion).'
                        </div>
                    </div>
                </div>
            </div>';   
        }else{
            if ($_SESSION['DATOSORIGEN'][0]== 0) {
                return get_data_cliente($dbConn,$_SESSION['ORIGEN'][1]);
            }else{
                return  get_data_cliente_2($dbConn,$_SESSION['ORIGEN'][1]);
            }
        }
    }
}
function get_data_table($dbConn,$Id){
    $resultado = '
        <table id="tbl_table"
            class="table table-bordered table-striped table-hover table-bordered">
            <thead class="bg-navy" style="font-size:18px;">
                <th class="text-center">#</th>
                <th>CLIENTE</th>
                <th class="text-center">SELECCIONAR</th>
            </thead>
            <tbody style="font-size:20px;">';
        $consulta="SELECT DISTINCT p.cliId,cliNombres,cliMarca  FROM tbl_p_orden o, tbl_r_guiaproceso p, tbl_a_clientes c
        WHERE o.gprId is not null AND o.gprId = p.gprId AND p.cliId = c.cliId  AND o.ordEstado = 1 AND 	o.ordTipo = 0   AND o.espId = :id
        AND o.ordFechaTurno BETWEEN :inicio AND :fin ORDER BY o.ordFechaTurno ASC";//
        $sql= $dbConn->prepare($consulta);
        $sql->bindValue(':id',$Id);
        $sql->bindValue(':inicio',date("Y-m-d")." 00:00:00");
        $sql->bindValue(':fin',date("Y-m-d")." 23:59:59");
        $sql->execute();
        $cont = 0;
        while ($row = $sql->fetch()) {
                $resultado .='
                <tr>
                    <th class="text-center" >'.++$cont.'</th>
                    <td>'.utf8_encode($row["cliNombres"]).' <b>'.utf8_encode($row["cliMarca"]).'</b></td>
                    <td class="text-center">
                        <button class="btn btn-info" onclick="get_data_procesar('.$row["cliId"].')"  >
                            <b>GUÍA DE ORIGEN</b>
                        </button>
                    </td>
                </tr>';
        }
    return $resultado.'</tbody></table>';
}

function get_data_cliente($dbConn,$Id){
    $consulta="SELECT o.ordId, c.cliNumero, c.cliNombres, c.cliMarca FROM tbl_p_orden o, tbl_r_guiaproceso p, tbl_a_clientes c
    WHERE o.gprId = p.gprId AND c.cliId = p.cliId AND p.cliId = :id";
    $sql= $dbConn->prepare($consulta);
    $sql->bindValue(':id',$Id);
    $sql->execute();
    $cont = 0;
    if($row = $sql->fetch()) {
        return '
        <div class="card">
        <div class="card-body">
        <div class="row">
            <div class="col-12"> 
                <button class="btn btn-danger btn-sm" onclick="regresar_3()" ><b>REGRESAR</b></button>
            </div>
        </div>
        <h4 class="text-center"><b>NUEVA GUÍA DE ORIGEN</b></h4>
        <h6 class="row mt-2">
            <b class="col-md-6 text-center">
                <span class="text-muted">CLIENTE:</span>
                '.utf8_encode($row["cliNombres"]).' - '.utf8_encode($row["cliMarca"]).'
            </b>
            <b class="col-md-6 text-center">
                <span class="text-muted ">RUC:</span>
                '.utf8_encode($row["cliNumero"]).'
            </b>
        </h6>
        <hr>
        <h5><b>Datos del destino</b></h5>
        <div class="row">
            <div class="col-md-3">
                <label for="txtProvincia" >Provincia</label>
                <select  class="form-control form-control-sm "  style="cursor:pointer" id="txtProvincia"  name="txtProvincia" onchange="select_canton()">
                    '.select_data_provincias($dbConn).'
                </select>
            </div>
            <div class="col-md-3">
                <label for="">Cantón</label>
                <select  class="form-control form-control-sm " style="cursor:pointer"  id="txtCanton" name="txtCanton" onchange="select_parroquia()">
                <option value="0" >PRIMERO SELECIONE UNA PROVINCIA</option>
                </select>
            </div>
            <div class="col-md-3">
                <label for="">Parroquia</label>
                <input class="form-control form-control-sm d-none"    >
                <select  class="form-control form-control-sm " style="cursor:pointer"  id="txtParroquia">
                    <option value="0" >PRIMERO SELECIONE UN CANTON</option>
                </select>
            </div>
            <div class="col-md-3">
                <label for="">Dirección</label>
                <textarea  rows="1" class="form-control form-control-sm" id="txtDireccion" ></textarea>
            </div>
        </div>
        <hr>
        <h5><b>Tipo de producto a movilizar</b></h5>
        <div class="row">
            <div class="col-md-3">
                <select  class="form-control form-control-sm "  style="cursor:pointer" id="slcProductoMovilizar"  >
                    <option value = "0">Canales sin restricción de uso</option>
                    <option value = "1">Canales para uso industrial</option>
                    <option value = "NULL">Subproductos</option>
                </select>
            </div>
        </div>
        <hr>
        <h5><b>Datos de movilización</b></h5>
        <div class="row">
            <div class="col-md-3">
                <label for="">Nombre Transportista</label>
                <select  class="form-control form-control-sm select2bs4" style="cursor:pointer"  id="txtRuc" onchange="f_get_data_trans()">
                '.f_get_data_transportistas($dbConn).'
                </select>
                <div class="input-group input-group-sm d-none">
                    <input type="text" id="txtRucss" class="form-control" placeholder="Numero de ruc" aria-label="" aria-describedby="basic-addon2"  >
                    <div class="input-group-append">
                        <button class="btn btn-info btn-sm"  type="button">Buscar</button>
                    </div>
                </div>
            </div>
            <div id="cont-transpo" class="col-md-9">
                
            </div>
        </div>
        <center> <button class="btn btn-info btn-lg mt-4" onclick="f_continuar_guia()" ><b>CONTINUAR</b></button> </center>';
    }else return 'ERROR-98219222';
}
function select_data_provincias($dbConn){
    $resultado='<option value="0" >SELECCIONE UNA PROVINCIA</option>';
    $consulta="SELECT * FROM tbl_provincias ORDER BY pProvincia ASC";
	$sql= $dbConn->prepare($consulta);
	$sql->execute();
	while($row = $sql->fetch()) {
        $resultado.='<option value="'.$row["pId"].'" >'.utf8_encode($row["pProvincia"]).'</option>';
	}
    return  $resultado;
}
function select_data_cantones($dbConn,$Id){
    $resultado = '<option value="0" >SELECCIONE UN CANTON</option>';
    $consulta="SELECT * FROM tbl_canton WHERE pId = :id";
	$sql= $dbConn->prepare($consulta);
    $sql->bindValue(':id',$Id);
	$sql->execute();
	while($row = $sql->fetch()) {
        $resultado.='<option value="'.$row["cId"].'" >'.utf8_encode($row["cCanton"]).'</option>';
	}
    return  $resultado;
}
function select_data_parroquias($dbConn,$Id){
    $resultado = '<option value="0" >SELECCIONE UNA PARROQUIA</option>';
    $consulta="SELECT * FROM tbl_parroquia WHERE cId = :id";
	$sql= $dbConn->prepare($consulta);
    $sql->bindValue(':id',$Id);
	$sql->execute();
	while($row = $sql->fetch()) {
        $resultado.='<option value="'.$row["qId"].'" >'.utf8_encode($row["qParroquia"]).'</option>';
	}
    return  $resultado;
}
function f_get_data_transport($dbConn){
    $Id = $_POST["Id"];
    $consulta="SELECT * FROM tbl_a_transportista WHERE trnId = :id ";
    $sql= $dbConn->prepare($consulta);
    $sql->bindValue(':id',$Id);
    $sql->execute();
    if($row = $sql->fetch()){
        if ($row["trnEstado"]==0) {
            return '
            <div class="row">
                <div class="col-md-4">
                    <label >RUC:</label>
                    <label class="form-control form-control-sm " >'.utf8_encode($row["trnRuc"]).'</label>
                </div>
                <div class="col-md-4">
                    <label for="slcPlaca">Vehiculo:</label>
                    <select  class="form-control form-control-sm" style="cursor:pointer"  id="slcPlaca">'.f_get_data_vehiculos($dbConn,$row["trnId"]).'</select>
                </div>
                <div class="col-md-4">
                    <label for="">Observaciones:</label>
                    <textarea  rows="1" class="form-control form-control-sm" id="txtObservaciones" ></textarea>
                </div>
            </div>';
        }else{
            return '
            <div class="row">
                <div class="col-md-4">
                    <label >Nombre:</label>
                    <label class="form-control form-control-sm " >Transportista deshabilitado</label>
                </div>
            </div>';
        }
    }else return '
    <div class="row">
        <div class="col-md-4">
            <label >Nombre:</label>
            <label class="form-control form-control-sm " >No se encontro al transportista</label>
        </div>
    </div>';

}
function f_get_data_transportistas($dbConn){
    $result = '<option value="0">SELECCIONE UN TRANSPORTISTA</option>';
    $consulta="SELECT * FROM tbl_a_transportista WHERE trnEstado = 0 ORDER BY trnRazonSocial ASC ";
    $sql= $dbConn->prepare($consulta);
    $sql->execute();
    while($row = $sql->fetch()){
        $result .= '<option value="'.$row["trnId"].'">'.utf8_encode($row["trnRazonSocial"]).'</option>';
    }
    return $result;
}
function f_get_data_vehiculos($dbConn,$Id){
    $result = '<option value="0">SELECCIONE UN VEHICULO</option>';
    $consulta="SELECT * FROM tbl_a_vehiculo WHERE trnId = :id AND vhcEstado = 0";
    $sql= $dbConn->prepare($consulta);
    $sql->bindValue(':id',$Id);
    $sql->execute();
    while($row = $sql->fetch()){
        $result .= '<option value="'.$row["vhcId"].'">'.$row["vhcPlaca"].'</option>';
    }
    return $result;
}
function get_data_cliente_2($dbConn,$Id){
    $consulta="SELECT o.ordId, c.cliNumero, c.cliNombres, c.cliMarca FROM tbl_p_orden o, tbl_r_guiaproceso p, tbl_a_clientes c
    WHERE o.gprId = p.gprId AND c.cliId = p.cliId AND p.cliId = :id";
    $sql= $dbConn->prepare($consulta);
    $sql->bindValue(':id',$Id);
    $sql->execute();
    $cont = 0;
    if($row = $sql->fetch()) {
        $movi = f_get_data_vehiculos_array($dbConn,$_SESSION['DATOSORIGEN'][2]);
        $pcq = get_data_provincia_canton_parroquia($dbConn,$_SESSION['DATOSORIGEN'][0]);
        $producto_movilizar = "Subproducto";
        $tabla = '<div class="col-lg-12">
            <div class="card card-light ">
                <div class="card-header border-transparent" data-card-widget="collapse"
                    style="cursor:pointer;">
                    <h3 class="card-title text-muted"><b>PRODUCTOS</b></h3>
                </div>
                <div class="card-body p-0" >
                    <center>
                        <button class="btn btn-warning mt-2 mb-3" " onclick="f_nuevo_prdocuto()" >
                            <b>AÑADIR</b>
                        </button>
                    </center>
                    <div class="table-responsive" id="cont-table-prodcutos">
                        '.table_prodcutos($dbConn).'
                    </div>
                </div>
            </div>
        </div>';
        if ($_SESSION['DATOSORIGEN'][4] == "0") $producto_movilizar = "Canales sin restricción de uso";
        else if ($_SESSION['DATOSORIGEN'][4] == "1") $producto_movilizar = "Canales para uso industrial";
        else if ($_SESSION['DATOSORIGEN'][4] == "NULL") {
            $tabla = '<div class="col-lg-12">
            <div class="card card-light ">
                <div class="card-header border-transparent" data-card-widget="collapse"
                    style="cursor:pointer;">
                    <h3 class="card-title text-muted"><b>SUBPRODUCTOS</b></h3>
                </div>
                <div class="card-body p-0" >
                    <center>
                        <button class="btn btn-danger mt-2 mb-3"  onclick="f_nuevo_subprdocuto()" >
                            <b>AÑADIR</b>
                        </button>
                    </center>
                    <div class="table-responsive" id="cont-table-subproductos">
                        '.table_subprodcutos($dbConn).'
                    </div>
                </div>
            </div>
        </div>';
        }
        return '
        <div class="card">
        <div class="card-body">
        <div class="row">
            <div class="col-12"> 
                <button class="btn btn-danger btn-sm" onclick="regresar_2()" ><b>REGRESAR</b></button>
            </div>
        </div>
        <h4 class="text-center"><b>NUEVA GUÍA DE ORIGEN</b></h4>
        <h5 class="row mt-2">
            <b class="col-md-6 ">
                <span class="text-muted">CLIENTE:</span>
                '.utf8_encode($row["cliNombres"]).' - '.utf8_encode($row["cliMarca"]).'
            </b>
            <b class="col-md-6 ">
                <span class="text-muted ">RUC:</span>
                '.utf8_encode($row["cliNumero"]).'
            </b>
        </h5>
        <hr>
        <h5 ><b>Datos destino</b></h5>
        <h6 >
            <b class="mr-3">
                <span class="text-muted">PROVINCIA:</span>
                '.$pcq[0].' ('.$pcq[3].')
            </b>
            <b class="mr-3">
                <span class="text-muted">CANTON:</span>
                '.$pcq[1].'
            </b>
            <b class="mr-3">
                <span class="text-muted">PARROQUIA:</span>
                '.$pcq[2].'
            </b>
            <b class="mr-3">
                <span class="text-muted">DIRECCIÓN:</span>
                '.$_SESSION['DATOSORIGEN'][1].'
            </b>
        </h6>
        <hr>
        <h5 ><b>Tipo de producto a movilizar:  <span class="text-muted mr-3" style="font-size:9;" >'.$producto_movilizar.'</span></b></h5>
        <hr>
        <h5 ><b>Datos movilización</b></h5>
        <h6 >
            <b class="mr-3">
                <span class="text-muted">RUC:</span>
                '.$movi[0].'
            </b>
            <b class="mr-3">
                <span class="text-muted">RAZON SOCIAL:</span>
                '.$movi[1].'
            </b>
            <b class="mr-3">
                <span class="text-muted">PLACA:</span>
                '.$movi[2].'
            </b>
            <b class="mr-3">
                <span class="text-muted">OBSERCACIONES:</span>
                '.$_SESSION['DATOSORIGEN'][3].'
            </b>
        </h6>
        <hr>
        <div class="row">
            '.$tabla.'
        </div>
        <center> <button class="btn btn-info btn-lg mt-4" onclick="f_guardar_guia()" ><b>GUARDAR GUÍA</b></button> </center>';
    }else return 'ERROR-98219222';
}
function f_get_data_vehiculos_array($dbConn,$Id){
    $consulta="SELECT t.trnRuc,t.trnRazonSocial, v.vhcPlaca 
    FROM tbl_a_vehiculo v, tbl_a_transportista t WHERE v.trnId = t.trnId AND vhcId = :id ";
    $sql= $dbConn->prepare($consulta);
    $sql->bindValue(':id',$Id);
    $sql->execute();
    if($row = $sql->fetch())return [utf8_encode($row["trnRuc"]),utf8_encode($row["trnRazonSocial"]),utf8_encode($row["vhcPlaca"])];
    else return ['','',''];
}
function get_data_provincia_canton_parroquia($dbConn,$Id){
    $consulta="SELECT p.pProvincia,c.cCanton,q.qParroquia, p.pCodigo 
    FROM tbl_parroquia q, tbl_canton c, tbl_provincias p  
    WHERE q.cId = c.cId AND c.pId = p.pId AND q.qId =  :id";
    $sql= $dbConn->prepare($consulta);
    $sql->bindValue(':id',$Id);
    $sql->execute();
    if($row = $sql->fetch())return [utf8_encode($row["pProvincia"]),utf8_encode($row["cCanton"]),utf8_encode($row["qParroquia"]),$row["pCodigo"]];
    else return ['','','',0];
}
function f_modal_productos($dbConn){
    $data = '';
    $consulta="SELECT o.ordId,o.ordNumOrden, o.ordFecha, o.gprId FROM tbl_p_orden o, tbl_r_guiaproceso p 
    WHERE o.gprId = p.gprId AND o.ordTipo = 0 AND o.ordEstado = 1  AND p.cliId = :id AND o.espId = :especie 
    AND o.ordFechaTurno  BETWEEN :inicio AND :final  ORDER BY o.ordFechaTurno DESC";//
    $sql= $dbConn->prepare($consulta);
    $sql->bindValue(':id',$_SESSION['ORIGEN'][1]);
    $sql->bindValue(':especie',$_SESSION['ORIGEN'][0]);
    $sql->bindValue(':inicio',date("Y-m-d")." 00:00:00");
    $sql->bindValue(':final',date("Y-m-d")." 23:59:59");
    $sql->execute();
    $cont= 0;
    while($row = $sql->fetch()){
        // $bandera = f_buscar_orden($dbConn,$row["ordFecha"],$row["gprId"]);
        // if ($bandera) {
            $despacho = f_get_data_productos_despachados($dbConn,$row["ordId"]);
            if ($despacho != '') {
                $cont++;
                $data .='
                <div class="col-lg-12">
                    <div class="card card-light ">
                        <div class="card-header border-transparent " data-card-widget="collapse"
                            style="cursor:pointer;">
                            <input type="hidden" value="'.$row["ordId"].'" id="inptIdPro-'.$cont.'">
                            <h3 class="card-title text-muted "><b id="lbl-'.$cont.'">'.utf8_encode($row["ordNumOrden"]).'</b></h3>
                        </div>
                        <div class="card-body ">
                            '.$despacho.'
                        </div>
                    </div>
                </div>';
            // }
        }
    }
    if ($cont == 0) {
        $data = '<h5><b>NO SE ENCONTRARON ORDENES DE PRODUCCÓN</b></h5> ';
    }
    return modal('<div class="row">'.$data."</div><input type='hidden' id='inptCantidad' value='".$cont."'> ",'SELECCIONAR PRODUCTOS','f_guardar_productos()');
}
function f_get_data_productos_despachados($dbConn,$Id){
    $consulta="SELECT pesId,pesCanal,pesProDescripcion
    FROM tbl_d_pesaje WHERE ordId = :id AND orgId IS NULL AND pesEliminado = 0";
    $sql= $dbConn->prepare($consulta);
    $sql->bindValue(':id',$Id);
    $sql->execute();
    $cont = 0;
    $data = '<table
    class="table table-sm table-bordered table-striped table-hover table-bordered text-center" id="table-'.$Id.'">
    <thead>
        <tr>
            <th>CANAL</th>
            <th>PRODUCTO</th>
            <th>
                SELECCIONAR
                <div class="icheck-success d-inline">
                    <input type="checkbox"  id="chb-'.$Id.'" >
                    <label for="chb-'.$Id.'" onclick="f_completar('.$Id.')"></label>
                </div>
            </th>
        </tr>
    </thead>
    <tbody>';
    while($row = $sql->fetch()) {
        $chec = '';
        if(buscar_array_productos($row["pesId"]))$chec = "checked";
        $cont++;
        $data .= '
        <tr>
            <td>'.$row["pesCanal"].'</td>
            <td>'.$row["pesProDescripcion"].'</td>
            <td class="text-center">
                <div class="icheck-danger d-inline">
                    <input type="checkbox"  id="chb-'.$Id.'-'.$cont.'" value="'.$row["pesId"].'"  '.$chec.'  >
                    <label for="chb-'.$Id.'-'.$cont.'"  onclick="f_comprobar_todos('.$Id.','.$cont.')"  ></label>
                </div>
            </td>
        </tr>';
    }
    if ($cont == 0) return '';
    else return $data.'</tbody></table>';
}
function table_prodcutos($dbConn){
    $table = '
    <table class="table table-sm table-bordered table-striped table-hover table-bordered text-center" id="table-pro">
        <thead>
            <tr>
                <th>#</th>
                <th>CANAL</th>
                <th>PRODUCTO</th>
                <th>PESO</th>
                <th>ELIMINAR</th>
            </tr>
        </thead>
        <tbody>';
    for ($i=0; $i < count($_SESSION['PRODCUTOS']) ; $i++) { // 
        $array =  f_get_data_despacho_array($dbConn,$_SESSION['PRODCUTOS'][$i]);
        if (count($array) > 0) {
            $table .= '
            <tr>
                <th>'.($i + 1).'</th>
                <td>'.$array[0].'</td>
                <td>'.$array[1].'</td>
                <td>'.$array[2].'</td>
                <td>
                    <button class="btn btn-sm btn-danger" onclick="f_eliminar_producto('.$i.')">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                </td>
            </tr>';
        }
    }
    return $table.'</tbody></table>';
}
function f_get_data_despacho_array($dbConn,$Id){
    $consulta="SELECT pesCanal,pesProDescripcion,pesPeso FROM tbl_d_pesaje WHERE pesId = :id";
    $sql= $dbConn->prepare($consulta);
    $sql->bindValue(':id',$Id);
    $sql->execute();
    if($row = $sql->fetch())return [utf8_encode($row["pesCanal"]),utf8_encode($row["pesProDescripcion"]),$row["pesPeso"]];
    else return [];
}
function delete_prodcuto(){
    $new_array = array();
    for ($i=0; $i < count($_SESSION['PRODCUTOS']) ; $i++) { 
        if ($_POST["Id"] != $i) {
            array_push($new_array,$_SESSION['PRODCUTOS'][$i]);
        }
    }
    unset($_SESSION['PRODCUTOS']);
    $_SESSION['PRODCUTOS'] = $new_array;
    return true;
}
function buscar_array_productos($Id){
    for ($i=0; $i < count($_SESSION['PRODCUTOS']) ; $i++) { 
        if ($_SESSION['PRODCUTOS'][$i] == $Id) {
            return true;
        }
    }
    return false;
}

function f_modal_subproductos($dbConn){
    $data = '';
    $consulta="SELECT o.ordId,o.ordNumOrden,o.gprId,o.ordCantidad, o.ordFecha FROM tbl_p_orden o, tbl_r_guiaproceso p 
    WHERE o.gprId = p.gprId AND o.ordTipo = 0 AND o.ordEstado = 1  AND p.cliId = :id AND o.espId = :especie 
    AND o.ordFechaTurno  BETWEEN :inicio AND :final  ORDER BY o.ordFechaTurno DESC";//
    $sql= $dbConn->prepare($consulta);
    $sql->bindValue(':id',$_SESSION['ORIGEN'][1]);
    $sql->bindValue(':especie',$_SESSION['ORIGEN'][0]);
    $sql->bindValue(':inicio',date("Y-m-d")." 00:00:00");
    $sql->bindValue(':final',date("Y-m-d")." 23:59:59");
    $sql->execute();
    $cont= 0;
    while($row = $sql->fetch()){
        $bandera = f_buscar_orden($dbConn,$row["ordFecha"],$row["gprId"]);
        if ($bandera == 0) {
            $viceras = f_get_data_productos_visceras($dbConn,$row["gprId"],$row["ordCantidad"],$row["ordId"]);
            if ($viceras != '') {
                $cont++;
                $data .='
                <div class="col-lg-12">
                    <div class="card card-light ">
                        <div class="card-header border-transparent " data-card-widget="collapse"
                            style="cursor:pointer;">
                            <input type="hidden" value="'.$row["ordId"].'" id="inptIdPro-'.$cont.'">
                            <h3 class="card-title text-muted "><b id="lbl-'.$cont.'">'.utf8_encode($row["ordNumOrden"]).'</b></h3>
                        </div>
                        <div class="card-body ">
                            '.$viceras.'
                        </div>
                    </div>
                </div>';
            }
        }
    }
    if ($cont == 0) {
        $data = '<h5><b>NO SE ENCONTRARON ORDENES DE PRODUCCÓN</b></h5>';
    }
    return modal('<div class="row">'.$data."</div><input type='hidden' id='inptCantidad' value='".$cont."'> ",'SELECCIONAR SUBPRODUCTOS','f_guardar_subproductos()');
}
function f_get_data_productos_visceras($dbConn,$Id,$cantidad,$Orden){
    $consulta="SELECT 	v.vscId , v.vscParte,v.vscSexo,s.subDescripcion ,v.subId
    FROM tbl_r_visceras v, tbl_a_subproductos s 
    WHERE v.subId  = s.subId AND v.gprId = :id AND v.vscEliminado = 0";
    $sql= $dbConn->prepare($consulta);
    $sql->bindValue(':id',$Id);
    $sql->execute();
    $cont = 0;
    $data = '<table
    class="table table-sm table-bordered table-striped table-hover table-bordered text-center" id="table-'.$Orden.'">
    <thead>
        <tr>
            <th>#</th>
            <th>SUBPRODUCTO</th>
            <th>SALDO</th>
            <th>CANTIDAD</th>
            <th>
                SELECCIONAR
                <div class="icheck-success d-inline">
                    <input type="checkbox"  id="chb-'.$Orden.'" >
                    <label for="chb-'.$Orden.'" onclick="f_completar_2('.$Orden.')"></label>
                </div>
            </th>
        </tr>
    </thead>
    <tbody>';
    $resutl = get_data_antemortem($dbConn,$Id);
    while($row = $sql->fetch()) {
        $total = 0;
        if ($row["vscSexo"]==0) {
            $total += (($resutl[0] + $resutl[1]) * $row["vscParte"] );
        }else if ($row["vscSexo"]==1) {
            $total += ($resutl[0]  * $row["vscParte"] );
        }else if ($row["vscSexo"]==2) {
            $total += ($resutl[1]  * $row["vscParte"] );
        }
        $decomisado = f_obtener_decomisados($dbConn,$row["subId"],$Orden);
        $origen = f_obtener_origen($dbConn,$row["vscId"]);
        $saldo  = ($total - $decomisado) - $origen;
        // $cont++;
        // echo $cont.") ".$origen."-".$row["vscId"]." <br>";
        if ($saldo > 0) {
            $cont++;
            $chec = '';
            $cant = buscar_array_subproductos($row["vscId"]);
            if( $cant != false){
                $chec = "checked";
                if ($cant > $saldo) $cant = $saldo;
            }else $cant = $saldo;
            
            $data .= '
            <tr>
                <td>'.$cont.'</td>
                <td>'.utf8_encode($row["subDescripcion"]).'</td>
                <td>'.$saldo.'</td>
                <td>
                    <input type="text"  class="form-control  form-control-sm text-center" maxlength="4" onkeypress="f_restrincion(event)" placeholder="Cantidad"  value="'.$cant.'" 
                </td>
                <td class="text-center">
                    <div class="icheck-danger d-inline">
                        <input type="checkbox"  id="chb-'.$Orden.'-'.$cont.'" value="'.$row["vscId"].'"  '.$chec.'  >
                        <label for="chb-'.$Orden.'-'.$cont.'"  onclick="f_comprobar_todos_2('.$Orden.','.$cont.')"  ></label>
                    </div>
                </td>
            </tr>';
        }
    }
    if ($cont == 0) return '';
    else return $data.'</tbody></table>';
}
function f_obtener_origen($dbConn,$Id){
    $cont=0;
    $consulta="SELECT vsrCantidad FROM tbl_d_visceras_origen WHERE vscId = :id";
    $sql= $dbConn->prepare($consulta);
    $sql->bindValue(':id',$Id);
    $sql->execute();
    while($row = $sql->fetch()) {
        $cont+= $row["vsrCantidad"];
    }
    return $cont;
}
function table_subprodcutos($dbConn){
    $table = '
    <table class="table table-sm table-bordered table-striped table-hover table-bordered text-center" id="table-subp" >
        <thead>
            <tr>
                <th>#</th>
                <th>SUBPRODUCTO</th>
                <th>CANTIDAD</th>
                <th>ELIMINAR</th>
            </tr>
        </thead>
        <tbody>';

    for ($i=0; $i < count($_SESSION['SUBPRODUCTOS']) ; $i++) { // 
        $array =  f_get_data_visceras_array($dbConn,$_SESSION['SUBPRODUCTOS'][$i][0]);
        if (count($array) > 0) {
            $table .= '
            <tr>
                <th>'.($i + 1).'</th>
                <td>'.$array[0].'</td>
                <td>'.$_SESSION['SUBPRODUCTOS'][$i][1].'</td>
                <td>
                    <button class="btn btn-sm btn-danger" onclick="f_eliminar_subproducto('.$i.')">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                </td>
            </tr>';
        }
    }
    return $table.'</tbody></table>';
}
function f_get_data_visceras_array($dbConn,$Id){
    $consulta="SELECT s.subDescripcion FROM tbl_r_visceras v, tbl_a_subproductos s WHERE v.subId = s.subId AND v.vscId = :id";
    $sql= $dbConn->prepare($consulta);
    $sql->bindValue(':id',$Id);
    $sql->execute();
    if($row = $sql->fetch())return [utf8_encode($row["subDescripcion"])];
    else return [];
}
function buscar_array_subproductos($Id){
    for ($i=0; $i < count($_SESSION['SUBPRODUCTOS']) ; $i++) { 
        if ($_SESSION['SUBPRODUCTOS'][$i][0] == $Id) {
            return $_SESSION['SUBPRODUCTOS'][$i][1];
        }
    }
    return false;
}
function delete_subprodcuto(){
    $new_array = array();
    for ($i=0; $i < count($_SESSION['SUBPRODUCTOS']) ; $i++) { 
        if ($_POST["Id"] != $i) {
            array_push($new_array,[$_SESSION['SUBPRODUCTOS'][$i][0],$_SESSION['SUBPRODUCTOS'][$i][1]]);
        }
    }
    unset($_SESSION['SUBPRODUCTOS']);
    $_SESSION['SUBPRODUCTOS'] = $new_array;
    return true;
}

function f_insert_origen($dbConn){
    try {
        global $User;
        global $Ip;
        $Numero =  Numero_Ordinal($dbConn,$_SESSION['ORIGEN'][0]);
        $empresa = f_get_data_empresa($dbConn,1);
        $cliente = f_get_data_cliente($dbConn,$_SESSION['ORIGEN'][1]);
        $movi = f_get_data_vehiculos_array($dbConn,$_SESSION['DATOSORIGEN'][2]);
        $pcq = get_data_provincia_canton_parroquia($dbConn,$_SESSION['DATOSORIGEN'][0]);
        $producto_moviliazar = '';
        if ($_SESSION['DATOSORIGEN'][4] == "NULL") $producto_moviliazar = null;
        else $producto_moviliazar = $_SESSION['DATOSORIGEN'][4];
        // $_SESSION['DATOSORIGEN'] ////IdParroquia,Direccion,IdVehiculo,Observaciones,Tipo de producto a movilizar
        $consulta = "INSERT INTO tbl_d_origen(orgFecha,orgNumero,orgDemRuc,orgDemRazonSocial,orgDemCodigoCentro,orgCodigoProvinciaDestino,orgDemProvincia,orgDemCanton,orgDemParroquia,orgDemDireccion,orgDemSitio,orgDemArea,orgDemCodigoArea,orgCliNumero,orgCliNombres,orgProvinciaDestino,orgCantonDestino,orgParroquiaDestino,orgDireccionDestino,orgTrnRuc,orgTrnRazonSocial,orgVhcPlaca,orgObservaciones,orgTipoProducto,demId,vhcId,espId,qId,usuId,ip)
        VALUES(:orgFecha,:orgNumero,:orgDemRuc,:orgDemRazonSocial,:orgDemCodigoCentro,:orgCodigoProvinciaDestino,:orgDemProvincia,:orgDemCanton,:orgDemParroquia,:orgDemDireccion,:orgDemSitio,:orgDemArea,:orgDemCodigoArea,:orgCliNumero,:orgCliNombres,:orgProvinciaDestino,:orgCantonDestino,:orgParroquiaDestino,:orgDireccionDestino,:orgTrnRuc,:orgTrnRazonSocial,:orgVhcPlaca,:orgObservaciones,:orgTipoProducto,:demId,:vhcId,:espId,:qId,:usuId,:ip)";
		$sql= $dbConn->prepare($consulta);
        $sql->bindValue(':orgFecha',date("Y-m-d H:i:s"));
        $sql->bindValue(':orgNumero',$Numero);
        $sql->bindValue(':orgDemRuc',utf8_decode($empresa[0]));
        $sql->bindValue(':orgDemRazonSocial',utf8_decode($empresa[8]));
        $sql->bindValue(':orgDemCodigoCentro',utf8_decode($empresa[9]));
        $sql->bindValue(':orgCodigoProvinciaDestino',$pcq[3]);
        $sql->bindValue(':orgDemProvincia',utf8_decode($empresa[1]));
        $sql->bindValue(':orgDemCanton',utf8_decode($empresa[2]));
        $sql->bindValue(':orgDemParroquia',utf8_decode($empresa[3]));
        $sql->bindValue(':orgDemDireccion',utf8_decode($empresa[4]));
        $sql->bindValue(':orgDemSitio',utf8_decode($empresa[5]));
        $sql->bindValue(':orgDemArea',utf8_decode($empresa[6]));
        $sql->bindValue(':orgDemCodigoArea',utf8_decode($empresa[7]));
        $sql->bindValue(':orgCliNumero',$cliente[0]);
        $sql->bindValue(':orgCliNombres',utf8_decode($cliente[1]));
        $sql->bindValue(':orgProvinciaDestino',utf8_decode($pcq[0]));
        $sql->bindValue(':orgCantonDestino',utf8_decode($pcq[1]));
        $sql->bindValue(':orgParroquiaDestino',utf8_decode($pcq[2]));
        $sql->bindValue(':orgDireccionDestino',utf8_decode($_SESSION['DATOSORIGEN'][1]));
        $sql->bindValue(':orgTrnRuc',$movi[0]);
        $sql->bindValue(':orgTrnRazonSocial',utf8_decode($movi[1]));
        $sql->bindValue(':orgVhcPlaca',utf8_decode($movi[2]));
        $sql->bindValue(':orgObservaciones',utf8_decode($_SESSION['DATOSORIGEN'][3]));
        $sql->bindValue(':orgTipoProducto',$producto_moviliazar);
        $sql->bindValue(':demId',1);
        $sql->bindValue(':vhcId',utf8_decode($_SESSION['DATOSORIGEN'][2]));
        $sql->bindValue(':espId',$_SESSION['ORIGEN'][0]);
        $sql->bindValue(':qId',$_SESSION['DATOSORIGEN'][0]);
        $sql->bindValue(':usuId',$User);
        $sql->bindValue(':ip',$Ip);
		if ($sql->execute()){
            $Id = $dbConn->lastInsertId();
            for ($i=0; $i < count($_SESSION['PRODCUTOS']) ; $i++) {
                if (f_update_despacho($dbConn,$Id,$_SESSION['PRODCUTOS'][$i])==false) return 'ERROR-12121555';
            }
            $result='';

            for ($i=0; $i < count($_SESSION['SUBPRODUCTOS']) ; $i++){
                if (f_insert_visceras_origen($dbConn,$Id,$_SESSION['SUBPRODUCTOS'][$i][0],$_SESSION['SUBPRODUCTOS'][$i][1])==false)return 'ERROR-2992';
            }
            $_SESSION['OPCION'] = 5;
            $_SESSION['VARIABLE'] = $Id;
            Insert_Login($Id,'tbl_d_origen','Guía de Origen','Guía N°.'.$Numero,'');
            $_SESSION['DATOSORIGEN'] = array('','','','',0,'');//Provincia,Canton,Parroquia,Direccion,IdVehiculo,Observaciones}
            $_SESSION['PRODCUTOS']= array();
            $_SESSION['SUBPRODUCTOS']= array();
            $_SESSION['ORIGEN'][1] = 0;
            return true;
        }else return 'ERROR-2121332';
	}  catch (Exception $e) {
		Insert_Error('ERROR-21212',$e->getMessage(),'Error al ingresar al registrar LA guía de origen');
		exit("ERROR-21212");
	}
}
function f_get_data_empresa($dbConn,$Id){
    $consulta="SELECT * FROM tbl_a_datos_Empresa WHERE demId = :id";
    $sql= $dbConn->prepare($consulta);
    $sql->bindValue(':id',$Id);
    $sql->execute();
    if($row = $sql->fetch())return [ utf8_encode($row["demRuc"]) ,utf8_encode($row["demProvincia"]),
                                        utf8_encode($row["demCanton"]),utf8_encode($row["demParroquia"]),
                                        utf8_encode($row["demDireccion"]),utf8_encode($row["demSitio"]),
                                        utf8_encode($row["demArea"]),utf8_encode($row["demCodigoArea"]),
                                        utf8_encode($row["demRazonSocial"]),utf8_encode($row["demCodigoCentro"])];
}
function f_get_data_cliente($dbConn,$Id){
    $consulta="SELECT cliNumero, cliNombres FROM tbl_a_clientes WHERE cliId = :id";
    $sql= $dbConn->prepare($consulta);
    $sql->bindValue(':id',$Id);
    $sql->execute();
    if($row = $sql->fetch())return [ $row["cliNumero"] ,utf8_encode($row["cliNombres"])];
}
function Numero_Ordinal($dbConn,$Tipo){
    $anio_actual = date("Y");
    $numero=1;
    $maximo=3;
    $Juliano= CalcularJuliano();
    $consulta="SELECT o.orgNumero,e.espLetra  
    FROM tbl_d_origen o, tbl_a_especies e WHERE o.espId  = e.espId AND o.espId = :id 
    AND o.orgFecha BETWEEN :inicio AND :fin ORDER BY o.orgId DESC ";
    // La consulta trae el ultimo numero de orden de produccion
    $sql= $dbConn->prepare($consulta);
    $sql->bindValue(':id',$Tipo);
    $sql->bindValue(':inicio',$anio_actual."-01-01 00:00:00");
    $sql->bindValue(':fin',$anio_actual."-12-31 23:59:59");
    $sql->execute();
    while($row = $sql->fetch()) {
        $Array1 = explode("-",$row["orgNumero"]);
        if (count($Array1)==2) {
            $letraBuscar =  get_letra_comprobante($Array1[1]);
            if ($letraBuscar==$row["espLetra"]) {
                $Array2 = explode($row["espLetra"],$Array1[1]);
                if ($Array2[0] != $Juliano) $numero = 1;
                else $numero = intval($Array2[1]) + 1;
                $cantidad = strlen($numero);
                $resultado="";
                for ($i=$cantidad; $i < $maximo; $i++) $resultado .= "0";
                return "EMRAQ EP-".$Juliano.$row["espLetra"].$resultado.$numero;
            }
        }
    }
    $letra = Traer_Letra_tipo($dbConn,$Tipo);
    $cantidad = strlen($numero);
    $resultado="";
    for ($i=$cantidad; $i < $maximo; $i++) $resultado .= "0";
    return "EMRAQ EP-".$Juliano.$letra.$resultado.$numero;
}
function CalcularJuliano(){
    $numMes=date("n");
    $total=0;
    $anio = date("Y");
    for ($i=1; $i < $numMes ; $i++) {
        $fecha= date("t", strtotime($anio."-$i"));
        $total = $total + intval($fecha);
    }
    $total = $total + intval(date("d"));
    return "$total".date("y");
}
function get_letra_comprobante($comprobante){
    for($i=65; $i<=90; $i++) {  
        $letraBuscar = chr($i);
        $arrayLetra =explode($letraBuscar,$comprobante);
        if (count($arrayLetra)==2)return $letraBuscar;
    }
    return false;
}
function Traer_Letra_tipo($dbConn,$tipo){
    $consulta="SELECT espLetra FROM tbl_a_especies WHERE espId = :id";
    $sql= $dbConn->prepare($consulta);
    $sql->bindValue(':id',$tipo);
    $sql->execute();
    if($row = $sql->fetch()) return $row['espLetra'];
    return false;
}

function f_while_productos($dbConn,$Orden){
    for ($i=0; $i < count($_SESSION['PRODCUTOS']) ; $i++) {
        if (f_update_despacho($dbConn,$Orden,$_SESSION['PRODCUTOS'][$i])==false) return 'ERROR-12121';
    }
    return true;
}
function f_update_despacho($dbConn,$Orden,$Id){
    try {
        $consulta = "UPDATE tbl_d_pesaje  SET orgId = :orden  WHERE pesId = :id ";
		$sql= $dbConn->prepare($consulta);
        $sql->bindValue(':orden',$Orden);
        $sql->bindValue(':id',$Id);
		if ($sql->execute())return true;
        else return false;
	}  catch (Exception $e) {
		Insert_Error('ERROR-21212',$e->getMessage(),'Error al ingresar al actualizar despacho');
		exit("ERROR-21212");
	}
}


function f_insert_visceras_origen($dbConn,$Orden,$Id,$cantidad){
    try {
        $array = f_get_data_visceras_inser($dbConn,$Id);
        // return $array[0].'-'.$array[1].'-'.$array[2].'-'.$array[3];
        $num = get_number_lote_visceras($dbConn);
        // $array = Codigo, Descripcion, Parte, subId
        $consulta = "INSERT INTO tbl_d_visceras_origen(vsrLote,vsrCantidad,vsrFecha,vsrSubCodigo,vsrSubDescripcion,vsrSubParte,orgId,vscId)
        VALUES(:vsrLote,:vsrCantidad,:vsrFecha,:vsrSubCodigo,:vsrSubDescripcion,:vsrSubParte,:orgId,:vscId)";
		$sql= $dbConn->prepare($consulta);
        $sql->bindValue(':vsrLote',$num);
        $sql->bindValue(':vsrCantidad',$cantidad);
        $sql->bindValue(':vsrFecha',date("Y-m-d H:i:s"));
        $sql->bindValue(':vsrSubCodigo',$array[0]);
        $sql->bindValue(':vsrSubDescripcion',utf8_decode($array[1]));
        $sql->bindValue(':vsrSubParte',$array[2]);
        $sql->bindValue(':orgId',$Orden);
        $sql->bindValue(':vscId',$Id);
		if ($sql->execute())return true;
        else return false;
	}  catch (Exception $e) {
		Insert_Error('ERROR-21212',$e->getMessage(),'Error al ingresar al ingresar la viceras a la orden');
		exit("ERROR-21212");
	}
}
function f_get_data_visceras_inser($dbConn,$Id){
    $consulta="SELECT s.subCodigo,s.subDescripcion,v.vscParte,v.subId FROM tbl_r_visceras v, tbl_a_subproductos s WHERE v.subId = s.subId AND v.vscId = :id";
    $sql= $dbConn->prepare($consulta);
    $sql->bindValue(':id',$Id);
    $sql->execute();
    if($row = $sql->fetch())return [$row["subCodigo"], utf8_encode($row["subDescripcion"]), $row["vscParte"], $row["subId"] ];
    else return [];
}
function get_number_lote_visceras($dbConn){
    $consulta="SELECT vsrLote FROM tbl_d_visceras_origen WHERE vsrFecha 
    BETWEEN :inicio AND :final ORDER BY vsrId DESC LIMIT 1 ";
    $sql= $dbConn->prepare($consulta);
    $sql->bindValue(':inicio',date("Y-m-d")." 00:00:00");
    $sql->bindValue(':final',date("Y-m-d")." 23:59:59");
    $sql->execute();
    if($row = $sql->fetch())return $row["vsrLote"] + 1 ;
    else return 1;
}

////////
function get_data_enfermedades($dbConn){
    $IdGuia = f_get_id_guia($dbConn,$_SESSION['ORDEN']);
    $consulta="SELECT * FROM tbl_r_visceras v, tbl_a_subproductos s 
    WHERE v.subId = s.subId  AND  	v.gprId = :id AND v.vscEliminado = 0 ";
    $sql= $dbConn->prepare($consulta);
    $sql->bindValue(':id',$IdGuia);
    $sql->execute();
    $cont = 0;
    $data = '';
    while($row = $sql->fetch()) {
        if ($row["vscSexo"] == 0 || $row["vscSexo"] == ($_SESSION['DATOSDECOMISO'][3] +1 ) ) {
            $parentesco = get_data_parentesco($dbConn,$row["subId"]);
            $resutl = get_data_antemortem($dbConn,$row["gprId"]);//Hembra, Macho
            $tipo = f_get_tipo_guia($dbConn,$_SESSION['ORDEN']);
            $ante = [0,0];
            if ($tipo== 0) {
                $ante = [$resutl[0],$resutl[1]];
            }else if ($tipo== 1) {
                $ante = [$resutl[2],$resutl[3]];
            }else{
                return '<b>Se encontro un error en el estado de la orden</b>';
            }
            $viseras = 0;
            $viseras2 = $_SESSION['DATOSDECOMISO'][1] * $row["vscParte"];// Cantidad total
            if($row["vscSexo"]==0)$viseras += (($ante[0] + $ante[1]) * $row["vscParte"]);
            else if($row["vscSexo"]==1)$viseras += ($ante[0]  * $row["vscParte"]);
            else if($row["vscSexo"]==2)$viseras += ($ante[1] * $row["vscParte"]);
            
            // $decomisado = f_obtener_decomisados($dbConn,$row["subId"],$_SESSION['ORDEN']);
            //get_data_decomisados_2($dbConn,$_SESSION['ORDEN'],$_SESSION['DATOSDECOMISO'][3]);
            // $saldo = $viseras2 - $decomisado;
            // $saldo2 = $viseras - $decomisado;

            // if ($saldo > 0){

                if ($parentesco != '') {
                    if ($_SESSION['DECOMISO'][1] == 3){
                        if (count($_SESSION['DECOMISOS']) > 0 ) {
                            $bandera = buscar_array_decomisos($row["subId"]);
                            if ( $bandera != false) {
                                $cont++;
                                $total_subproducto = $row["vscParte"] * ($ante[0] + $ante[1] );
                                $decomisado = f_obtener_decomisados($dbConn,$row["subId"],$_SESSION['ORDEN']);
                                $saldoV = $total_subproducto - $decomisado;
                                $data .= '
                                <div class="col-lg-6">
                                    <div class="card card-light ">
                                        <div class="card-header border-transparent " data-card-widget="collapse"
                                            style="cursor:pointer;">
                                            <input type="hidden" value="'.$row["subId"].'" id="inptIdSub-'.$cont.'">
                                            <h3 class="card-title text-muted "><b id="lbl-'.$cont.'">'.utf8_encode($row["subDescripcion"]).'</b></h3>
                                            <span class="text-muted float-right"><b>Total: '.$saldoV.'</b></span>
                                        </div>
                                        <div class="card-body ">
                                            '.$parentesco.'
                                        </div>
                                    </div>
                                </div>';
                            }
                        }else {
                            $data  ='<h5 class="text-muted"><b>DEBE LLENAR LA TABLA DE DECOMISO PARA SELECCIONAR UNA ENFERMEDAD</b></h5>';
                            return modal('<div class="row">'.$data."</div>",'SELECCIONAR ENFERMEDADES','');
                        } 
                    }else{
                        $cont++;
                        $data .= '
                        <div class="col-lg-6">
                            <div class="card card-light ">
                                <div class="card-header border-transparent " data-card-widget="collapse"
                                    style="cursor:pointer;">
                                    <input type="hidden" value="'.$row["subId"].'" id="inptIdSub-'.$cont.'">
                                    <h3 class="card-title text-muted "><b id="lbl-'.$cont.'">'.utf8_encode($row["subDescripcion"]).'</b></h3>
                                    <span class="text-muted float-right"><b>Total: '.$viseras2.'</b></span>
                                </div>
                                <div class="card-body ">
                                    '.$parentesco.'
                                </div>
                            </div>
                        </div>';
                    }
                }
            // }
        }
    }
    $cantidad = '<input value="'.$cont.'" id="inpCantidadSubproductos" type="hidden" >';
    return modal('<div class="row">'.$data."</div>".$cantidad,'SELECCIONAR ENFERMEDADES','f_insert_enfermedades()');
}
function get_data_parentesco($dbConn,$Id){
    $consulta="SELECT * FROM tbl_parentesco p, tbl_a_enfermedad e WHERE p.enfId = e.enfId AND p.subId = :id AND e.enfEstado = 0";
    $sql= $dbConn->prepare($consulta);
    $sql->bindValue(':id',$Id);
    $sql->execute();
    $cont = 0;
    $data = '';
    while($row = $sql->fetch()) {
        $chec = "";
        $cant = '';
        $d_none = "";
        if ($_SESSION['DECOMISO'][1] == 3){
            $bandera = buscar_array_decomisos($row["subId"]);
            if ( $bandera != false){
                $cant = $bandera;
                $d_none = "d-none";
            }
            $bandera = buscar_array($Id,$row["enfId"]);
            if ( $bandera != false) {
                $chec = "checked";
            }
        }else{
            $bandera = buscar_array($Id,$row["enfId"]);
            if ( $bandera != false) {
                $chec = "checked";
                $cant = $bandera;
            }
        }
        
        $cont++;
        $data .= '
        <div class="row">
            <div class="form-group clearfix col-6 pt-1">
                <div class="icheck-danger d-inline">
                    <input type="checkbox"  id="chb-'.$Id.'-'.$cont.'" value="'.$row["enfId"].'" '.$chec.'  >
                    <label for="chb-'.$Id.'-'.$cont.'">'.utf8_encode($row["enfDescripcion"]).'</label>
                </div>
            </div>
            <div class="col-6 '.$d_none.'">
                <input type="text" id="txtCantidad-'.$Id.'-'.$cont.'""  class="form-control  form-control-sm text-center" maxlength="4" onkeypress="f_restrincion(event)" placeholder="Cantidad"  value="'.$cant.'" >
            </div>
        </div>';
    }
    $input = '';
    if ($cont > 0) {
        $input = '<input value="'.$cont.'" id="inpCantidad-'.$Id.'" type="hidden" >';
    }
    return $data.$input;
}
function select_data_especies($dbConn){
    $resultado='';
    $cont = 0;
    $consulta="SELECT * FROM tbl_a_especies";
	$sql= $dbConn->prepare($consulta);
	$sql->execute();
	while($row = $sql->fetch()) {
        $cont++;
        $resultado.='<option value="'.$row["espId"].'" >'.utf8_encode($row["espDescripcion"]).'</option>';
	}
    if ($cont > 0)return  $resultado;
    else return '<option value="0" selected="" >No se encontraron especies</option>';
}
function table_enfermedades($dbConn){
    $table = '
    <table class="table table-sm table-bordered table-striped table-hover table-bordered text-center">
        <thead>
            <tr>
                <th>#</th>
                <th>Subproducto</th>
                <th>Enfermedad</th>
                <th>Cantidad</th>
                <th>Eliminar</th>
            </tr>
        </thead>
        <tbody>';
        $total = 0;
    for ($i=0; $i < count($_SESSION['ENFERMEDADES']) ; $i++) { // [Subproducto, Enfermedad, Cantidad]
        $Subproducto = get_name_subproducto($dbConn,$_SESSION['ENFERMEDADES'][$i][0]);
        $Enfermedad = get_name_enfermedad($dbConn,$_SESSION['ENFERMEDADES'][$i][1]);
        $total += $_SESSION['ENFERMEDADES'][$i][2];
        $table .= '
        <tr>
            <th>'.($i + 1).'</th>
            <td>'.$Subproducto.'</td>
            <td>'.$Enfermedad.'</td>
            <td>'.$_SESSION['ENFERMEDADES'][$i][2].'</td>
            <td>
                <button class="btn btn-sm btn-danger" onclick="f_eliminar_enfermedad('.$i.')">
                    <i class="fas fa-trash-alt"></i>
                </button>
            </td>
        </tr>';
    }
    $foot = '';
    if ($total > 0) {
        $foot = '<tfoot><tr><th colspan="3" >TOTAL</th><th>'.$total.'</th></tr></tfoot>';
    }
    return $table.'</tbody>'.$foot.'</table>';
}
function get_data_select_decomisos($dbConn){
    $table = '<table
    class="table table-sm table-bordered table-striped table-hover table-bordered text-center" id="table-inset-decomisos">
    <thead>
        <tr>
            <th>#</th>
            <th>Subproducto</th>
            <th>Genero</th>
            <th>Saldo</th>
            <th>Cantidad</th>
        </tr>
    </thead>
    <tbody>';
    $condi = '';
    if ($_SESSION['DECOMISO'][1] == 1)$condi = 'AND vscSexo != 0';
    $IdGuia = f_get_id_guia($dbConn,$_SESSION['ORDEN']);
    $consulta="SELECT * FROM tbl_r_visceras v, tbl_a_subproductos s 
    WHERE v.subId = s.subId  AND  v.gprId = :id AND v.vscEliminado = 0 ".$condi;
    $sql= $dbConn->prepare($consulta);
    $sql->bindValue(':id',$IdGuia);
    $sql->execute();
    $cont = 0;
    $data = '';
    while($row = $sql->fetch()) {
        $cont++;
        $resutl = get_data_antemortem($dbConn,$row["gprId"]);//Hembra, Macho
        $tipo = f_get_tipo_guia($dbConn,$_SESSION['ORDEN']);
        $ante = [0,0];
        if ($tipo== 0) {
            $ante = [$resutl[0],$resutl[1]];
        }else if ($tipo== 1) {
            $ante = [$resutl[2],$resutl[3]];
        }else{
            return '<b>Se encontro un error en el estado de la orden</b>';
        }
        $viseras = 0;// Cantidad total
        $Genero = '';
        if($row["vscSexo"]==0){
            $viseras += (($ante[0] + $ante[1]) * $row["vscParte"]);
            $Genero = 'AMBOS';
        }else if($row["vscSexo"]==1){
            $viseras += ($ante[0]  * $row["vscParte"]);
            $Genero = 'HEMBRAS';
        }else if($row["vscSexo"]==2){
            $viseras += ($ante[1] * $row["vscParte"]);
            $Genero = 'MACHOS';
        }
        $decomisado = f_obtener_decomisados($dbConn,$row["subId"],$_SESSION['ORDEN']);
        $saldo = $viseras - $decomisado;
        if ($saldo > 0) {
            $chec = "";
            $cant = '';
            $bandera = buscar_array_decomisos($row["subId"]);
            if ( $bandera != false) {
                $chec = "checked";
                $cant = $bandera;
            }
            $table .= '
            <tr>
                <td>
                    <div class="icheck-warning d-inline">
                        <input type="checkbox" id="'.$cont.'" value="'.$row["subId"].'"  '.$chec.'  >
                        <label for="'.$cont.'"></label>
                    </div>
                </td>
                <td> <label for="'.$cont.'"  style="cursor:pointer" >'.utf8_encode($row["subDescripcion"]).'</label></td>
                <td>'.$Genero.'</td>
                <td>'.$saldo.'</td>
                <td >
                    <input type="text" class="form-control  form-control-sm text-center" maxlength="4" onkeypress="f_restrincion(event)" value="'.$cant.'" placeholder="Cantidad" >
                </td>
                <td class="d-none">'.$row["vscSexo"].'</td>
            </tr>';
        }
    }
    $data = $table.'</tbody></table>';
    return modal('<div class="row">'.$data."</div>",'SELECCIONAR DECOMISOS','f_insert_decomisos()');
}
function table_decomisos($dbConn){
    $td_eliminar = "";
    if ($_SESSION['DECOMISO'][1] != 1) {
        $td_eliminar = "<th>Eliminar</th>";
    }
    $table = '
    <table class="table table-sm table-bordered table-striped table-hover table-bordered text-center">
        <thead>
            <tr>
                <th>#</th>
                <th>Subproducto</th>
                <th>Genero</th>
                <th>Cantidad</th>
                '.$td_eliminar.'
            </tr>
        </thead>
        <tbody>';
        $total = 0;
    for ($i=0; $i < count($_SESSION['DECOMISOS']) ; $i++) { // [Subproducto, Cantidad,Genero]
        $Subproducto = get_name_subproducto($dbConn,$_SESSION['DECOMISOS'][$i][0]);
        $btn = '';
        if($_SESSION['DECOMISOS'][$i][2]==0) $Genero = 'AMBOS';
        else if($_SESSION['DECOMISOS'][$i][2]==1)$Genero = 'HEMBRAS';
        else if($_SESSION['DECOMISOS'][$i][2]==2)$Genero = 'MACHOS';
        if ($_SESSION['DECOMISO'][1] != 1) {
            $btn = '<td><button class="btn btn-sm btn-warning" onclick="f_eliminar_decomiso('.$i.')">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                    </td>';
        }
        $total += $_SESSION['DECOMISOS'][$i][1];
        $table .= '
        <tr>
            <th>'.($i + 1).'</th>
            <td>'.$Subproducto.'</td>
            <td>'.$Genero.'</td>
            <td>'.$_SESSION['DECOMISOS'][$i][1].'</td>
            '.$btn.'
        </tr>';
    }
    $foot = '';
    if ($total > 0) {
        $foot = '<tfoot><tr><th colspan="3" >TOTAL</th><th>'.$total.'</th></tr></tfoot>';
    }
    return $table.'</tbody>'.$foot.'</table>';
}

function completar_decomisos_automa($dbConn){
    unset($_SESSION['DECOMISOS']);
    $_SESSION['DECOMISOS'] = array();
    $IdGuia = f_get_id_guia($dbConn,$_SESSION['ORDEN']);
    $consulta="SELECT * FROM tbl_r_visceras v, tbl_a_subproductos s 
    WHERE v.subId = s.subId  AND  v.gprId = :id AND v.vscEliminado = 0 ";
    $sql= $dbConn->prepare($consulta);
    $sql->bindValue(':id',$IdGuia);
    $sql->execute();
    $cont = 0;
    $data = '';
    while($row = $sql->fetch()){
        // $ante = get_data_antemortem($dbConn,$IdGuia);//Hembra, Macho
        // $viseras = (($ante[0] + $ante[1]) * $row["vscParte"]);
        // $decomisado = f_obtener_decomisados($dbConn,$row["subId"],$_SESSION['ORDEN']);
        // $saldo = $viseras - $decomisado;
        if ($row["vscSexo"]== 0 || $row["vscSexo"] == ($_SESSION['DATOSDECOMISO'][3] +1 )) {
            $cantidad = $_SESSION['DATOSDECOMISO'][1] * $row["vscParte"];
            array_push($_SESSION['DECOMISOS'],[$row["subId"],$cantidad,$row["vscSexo"]]);
        }
    }
}

function f_obtener_procesar($dbConn,$orden,$Id){
    $cont=0;
    $consulta="SELECT faeCantidad FROM tbl_p_faenamiento WHERE ordId = :orden";
    $sql= $dbConn->prepare($consulta);
    $sql->bindValue(':orden',$orden);
    // $sql->bindValue(':id',$Id);
    $sql->execute();
    while($row = $sql->fetch()) {
        $cont+= $row["faeCantidad"];
    }
    return $cont;
}

function select_data_subproductos($dbConn){
    $resultado='<option value="0">Seleccione un producto</option>';
    $cont = 0;
    $consulta="SELECT * FROM tbl_a_productos WHERE espId = :id AND proPartes = 1 AND proEliminado = 0";
	$sql= $dbConn->prepare($consulta);
    $sql->bindValue(':id',$_SESSION['DECOMISO'][0]);
	$sql->execute();
	while($row = $sql->fetch()) {
        $cont++;
        $resultado.='<option value="'.$row["proId"].'" >'.utf8_encode($row["proDescripcion"]).'</option>';
	}
    if ($cont > 0)return  $resultado;
    else return '<option value="0" selected="" >No se encontraron productos</option>';
}
function select_data_subproductos_parcial($dbConn){
    $resultado='<option value="0">Seleccione un producto</option>';
    $cont = 0;
    $consulta="SELECT * FROM tbl_a_productos WHERE espId = :id AND proEliminado = 0";
	$sql= $dbConn->prepare($consulta);
    $sql->bindValue(':id',$_SESSION['DECOMISO'][0]);
	$sql->execute();
	while($row = $sql->fetch()) {
        $cont++;
        $resultado.='<option value="'.$row["proId"].'" >'.utf8_encode($row["proDescripcion"]).'</option>';
	}
    if ($cont > 0)return  $resultado;
    else return '<option value="0" selected="" >No se encontraron productos</option>';
}

function f_obtener_procesar_total($dbConn,$Id){
    $cont=0;
    $consulta="SELECT * FROM tbl_r_detalle 
    WHERE dtEstado = 1  AND  ordId is null AND dtEliminado = 0 AND gprId = :id";
    $sql= $dbConn->prepare($consulta);
    $sql->bindValue(':id',$Id);
    $sql->execute();
    while($row = $sql->fetch()) {
        $cont++;
    }
    return $cont;
}

function f_buscar_orden($dbConn,$fecha,$Id){
    $cont=0;
    $consulta="SELECT * FROM tbl_p_orden WHERE gprId = :id AND ordFecha > :fecha AND ordTipo = 0";
    $sql= $dbConn->prepare($consulta);
    $sql->bindValue(':id',$Id);
    $sql->bindValue(':fecha',$fecha);
    $sql->execute();
    while($row = $sql->fetch()) {
        $cont++;
    }
    return $cont;
}
function f_buscar_orden_1($dbConn,$fecha,$Id){
    $cont=0;
    $consulta="SELECT * FROM tbl_p_orden WHERE gprId = :id AND ordFecha > :fecha AND ordTipo = 1";
    $sql= $dbConn->prepare($consulta);
    $sql->bindValue(':id',$Id);
    $sql->bindValue(':fecha',$fecha);
    $sql->execute();
    while($row = $sql->fetch()) {
        $cont++;
    }
    return $cont;
}
function f_get_id_guia($dbConn,$Orden){
    $cont=0;
    $consulta="SELECT * FROM tbl_p_orden WHERE ordId = :id";
    $sql= $dbConn->prepare($consulta);
    $sql->bindValue(':id',$Orden);
    $sql->execute();
    if($row = $sql->fetch()) return $row["gprId"];
    else return 'ERROR.-212';
}
function f_get_tipo_guia($dbConn,$Orden){
    $cont=0;
    $consulta="SELECT * FROM tbl_p_orden WHERE ordId = :id";
    $sql= $dbConn->prepare($consulta);
    $sql->bindValue(':id',$Orden);
    $sql->execute();
    if($row = $sql->fetch()) return $row["ordTipo"];
    else return 'ERROR.-212';
}

function buscar_array($Subproducto,$Enfermedad){
    for ($i=0; $i < count($_SESSION['ENFERMEDADES']) ; $i++) { // [Subproducto, Enfermedad, Cantidad]
        if ($_SESSION['ENFERMEDADES'][$i][0] == $Subproducto && $_SESSION['ENFERMEDADES'][$i][1] == $Enfermedad) {
            return $_SESSION['ENFERMEDADES'][$i][2];
        }
    }
    return false;
}
function buscar_array_decomisos($Subproducto){
    for ($i=0; $i < count($_SESSION['DECOMISOS']) ; $i++) { // [Subproducto, Cantidad]
        if ($_SESSION['DECOMISOS'][$i][0] == $Subproducto) {
            return $_SESSION['DECOMISOS'][$i][1];
        }
    }
    return false;
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
function pasar_array($dbConn){
    $array = $_POST["ArrayDatos"];
    unset($_SESSION['ENFERMEDADES']);
    $_SESSION['ENFERMEDADES'] = array();//[Subproducto,Enfermedad,Cantidad]
    for ($i=0; $i < count($array) ; $i++) { 
        $bandera1 = f_return_saldo_subprocuto_enfermedad($dbConn,$array[$i][0],$array[$i][2]);
        if ($bandera1 == 1) {
            array_push($_SESSION['ENFERMEDADES'],[$array[$i][0],$array[$i][1],$array[$i][2]]);
        }else return $bandera1;
    }
    return true;
}
function f_return_saldo_subprocuto_enfermedad($dbConn,$Id,$cantidad){
    $consulta="SELECT * FROM tbl_r_visceras  WHERE subId = :id ";
    $sql= $dbConn->prepare($consulta);
    $sql->bindValue(':id',$Id);
    $sql->execute();
    if($row = $sql->fetch()){
        $IdGuia = f_get_id_guia($dbConn,$_SESSION['ORDEN']);
        $resutl = get_data_antemortem($dbConn,$row["gprId"]);//Hembra, Macho
        $tipo = f_get_tipo_guia($dbConn,$_SESSION['ORDEN']);
        $ante = [0,0];
        if ($tipo== 0) {
            $ante = [$resutl[0],$resutl[1]];
        }else if ($tipo== 1) {
            $ante = [$resutl[2],$resutl[3]];
        }else{
            return '<b>Se encontro un error en el estado de la orden</b>';
        }
        // $decomisado = f_obtener_enfermeddes($dbConn,$row["subId"],$_SESSION['ORDEN']);
        // $viseras = 0;
        // if($row["vscSexo"]==0)$viseras += (($ante[0] + $ante[1]) * $row["vscParte"]);
        // else if($row["vscSexo"]==1)$viseras += ($ante[0]  * $row["vscParte"]);
        // else if($row["vscSexo"]==2)$viseras += ($ante[1] * $row["vscParte"]);
        // $saldo = $viseras - $decomisado;
        $saldoV = 0;
        if ($_SESSION['DECOMISO'][1] == 3) {
            $total_subproducto = $row["vscParte"] * ($ante[0] + $ante[1] );
            $decomisado = f_obtener_decomisados($dbConn,$row["subId"],$_SESSION['ORDEN']);
            $saldoV = $total_subproducto - $decomisado;
        }else{
            $saldoV = $_SESSION['DATOSDECOMISO'][1] * $row["vscParte"];
        }
        if ($cantidad <= $saldoV)return true;
        else return '<b>LA CANTIDAD INGRESADA SUPERA EL SALDO DEL SUBPRODCUTO</b>';
    }else return 'ERROR-28272';
}

function pasar_array_decomisos($dbConn){
    $array = $_POST["ArrayDatos"];
    unset($_SESSION['DECOMISOS']);
    $_SESSION['DECOMISOS'] = array();
    for ($i=0; $i < count($array) ; $i++) { 
        $bandera1 = f_return_saldo_subprocuto_decomisos($dbConn,$array[$i][0],$array[$i][1]);
        if ($bandera1 == 1) {
            array_push($_SESSION['DECOMISOS'],[$array[$i][0],$array[$i][1],$array[$i][2]]);
        }else return $bandera1;
    }
    for ($i=0; $i < count($_SESSION['ENFERMEDADES']) ; $i++) { 
        $bandera = buscar_array_decomisos($_SESSION['ENFERMEDADES'][$i][0]);
        if ($bandera != false) {
            $_SESSION['ENFERMEDADES'][$i][2] = $bandera;
        }
    }

    return true;
}
function f_return_saldo_subprocuto_decomisos($dbConn,$Id,$cantidad){
    $consulta="SELECT * FROM tbl_r_visceras  WHERE subId = :id ";
    $sql= $dbConn->prepare($consulta);
    $sql->bindValue(':id',$Id);
    $sql->execute();
    if($row = $sql->fetch()){
        $IdGuia = f_get_id_guia($dbConn,$_SESSION['ORDEN']);
        $resutl = get_data_antemortem($dbConn,$IdGuia);//Hembra, Macho
        $tipo = f_get_tipo_guia($dbConn,$_SESSION['ORDEN']);
        $ante = [0,0];
        if ($tipo== 0) {
            $ante = [$resutl[0],$resutl[1]];
        }else if ($tipo== 1) {
            $ante = [$resutl[2],$resutl[3]];
        }else{
            return '<b>Se encontro un error en el estado de la orden</b>';
        }
        $decomisado = f_obtener_decomisados($dbConn,$row["subId"],$_SESSION['ORDEN']);
        $viseras = 0;
        if($row["vscSexo"]==0)$viseras += (($ante[0] + $ante[1]) * $row["vscParte"]);
        else if($row["vscSexo"]==1)$viseras += ($ante[0]  * $row["vscParte"]);
        else if($row["vscSexo"]==2)$viseras += ($ante[1] * $row["vscParte"]);
        $saldo = $viseras - $decomisado;
        if ($cantidad <= $saldo)return true;
        else return '<b>LA CANTIDAD INGRESADA SUPERA EL SALDO DEL SUBPRODCUTO</b>';
    }else return 'ERROR-28272';
}

function pasar_array_datos($dbConn){
    $array = $_POST["arrayDatos"];
    $IdGuia = f_get_id_guia($dbConn,$_SESSION['ORDEN']);
    $resutl = get_data_antemortem($dbConn,$IdGuia);//Hembra, Macho
    $tipo = f_get_tipo_guia($dbConn,$_SESSION['ORDEN']);
    $ante = [0,0];
    if ($tipo== 0) {
        $ante = [$resutl[0],$resutl[1]];
    }else if ($tipo== 1) {
        $ante = [$resutl[2],$resutl[3]];
    }else{
        return '<b>Se encontro un error en el estado de la orden</b>';
    }
    $total = 0;
    if ($array[3] == 0) {
        $total = $ante[0];
    }elseif ($array[3] == 1) {
        $total = $ante[1];
    }
    $procesado = get_data_decomisados_2($dbConn,$_SESSION['ORDEN'],$array[3]);
    $saldo = $total - $procesado;
    if ($array[1] <= $saldo) {
        $consulta="SELECT * FROM tbl_r_visceras v, tbl_a_subproductos s 
        WHERE v.subId = s.subId AND v.gprId = :id AND v.vscEliminado = 0";
        $sql= $dbConn->prepare($consulta);
        $sql->bindValue(':id',$IdGuia);
        $sql->execute();
        while($row = $sql->fetch()) {    
            // array_push($cont,[,$row["vscSexo"]]);
            $total_subproducto = $row["vscParte"] * ($ante[0] + $ante[1] );
            $decomisado = f_obtener_decomisados($dbConn,$row["subId"],$_SESSION['ORDEN']);
            $saldoV = $total_subproducto - $decomisado;
            $subproductos_necesario = $row["vscParte"] * $array[1];
            if ($subproductos_necesario > $saldoV) {
                return '<h5>No se puede continuar con el decomiso total</h5>'.
                '<h5>Porque el subprodcuto <b>'.utf8_encode($row["subDescripcion"]).'</b>'.
                ' no cuenta con el saldo necesario para decomisar '.$array[1].' animales </h5>'.
                '<h6><b>Saldo del subproducto: '.$saldoV.'</b></h6>'.
                '<h6><b>Saldo necesario: '.$subproductos_necesario.'</b></h6>';
            }

        }
        $_SESSION['DATOSDECOMISO'][0] = $array[0];
        $_SESSION['DATOSDECOMISO'][1] = $array[1];
        $_SESSION['DATOSDECOMISO'][2] = $array[2];
        $_SESSION['DATOSDECOMISO'][3] = $array[3];
        return true;
    }else return '<b>La cantidad ingresada supera el saldo</b>';
}
function f_comprobar_visceras_para_d_total($dbConn,$cantidad){
    
}
function f_inserat_decomiso_pacial($dbConn){
    $array = $_POST["arrayDatos"];
    $IdGuia = f_get_id_guia($dbConn,$_SESSION['ORDEN']);
    $resutl = get_data_antemortem($dbConn,$IdGuia);//Hembra, Macho
    $tipo = f_get_tipo_guia($dbConn,$_SESSION['ORDEN']);
    $ante = [0,0];
    if ($tipo== 0) {
        $ante = [$resutl[0],$resutl[1]];
    }else if ($tipo== 1) {
        $ante = [$resutl[2],$resutl[3]];
    }else{
        return '<b>Se encontro un error en el estado de la orden</b>';
    }
    $total = 0;
    if ($array[3] == 0) {
        $total = $ante[0];
    }elseif ($array[3] == 1) {
        $total = $ante[1];
    }
    $procesado = get_data_decomisados_2($dbConn,$_SESSION['ORDEN'],$array[3]);
    $partes = get_parte_producto($dbConn,$array[0]);
    $saldo = $total - $procesado;
    if (($array[1] / $partes) <= $saldo) {
        $consulta="SELECT * FROM tbl_p_orden WHERE ordId = :id";
        $sql= $dbConn->prepare($consulta);
        $sql->bindValue(':id',$_SESSION['ORDEN']);
        $sql->execute();
        if($row = $sql->fetch()){
            // $array = [Cantidad,Causa,Sexo,Prodcuto,Partes] 
            $return_Decomiso = f_insertar_decomiso($dbConn,[$array[1],$array[2],$array[3],$array[0],$partes]);
            if ($return_Decomiso != false) {
                $detalle  = 'Se genero un Decomiso de la Orden '.$row["ordNumOrden"];
                $_SESSION['DECOMISO'][1] = 0;
                return Insert_Login($return_Decomiso,'tbl_p_decomiso','Decomiso',$detalle,'');
            }else return 'ERROR-98333';
        } else return 'ERROR-122112';// NO SE ENCONTRO LA ORDEN
    }else return '<b>La cantidad ingresada supera el saldo</b>';
}


function delete_enfermedad(){
    $new_array = array();
    for ($i=0; $i < count($_SESSION['ENFERMEDADES']) ; $i++) { 
        if ($_POST["Id"] != $i) {
            array_push($new_array,[$_SESSION['ENFERMEDADES'][$i][0],$_SESSION['ENFERMEDADES'][$i][1],$_SESSION['ENFERMEDADES'][$i][2]]);
        }
    }
    unset($_SESSION['ENFERMEDADES']);
    $_SESSION['ENFERMEDADES'] = $new_array;
    return true;
}
function delete_decomisos(){
    $new_array = array();
    for ($i=0; $i < count($_SESSION['DECOMISOS']) ; $i++) { 
        if ($_POST["Id"] != $i) {
            array_push($new_array,[$_SESSION['DECOMISOS'][$i][0],$_SESSION['DECOMISOS'][$i][1],$_SESSION['DECOMISOS'][$i][2]]);
        }
    }
    unset($_SESSION['DECOMISOS']);
    $_SESSION['DECOMISOS'] = $new_array;

    $new_array2 =  array();
    for ($i=0; $i < count($_SESSION['ENFERMEDADES']) ; $i++) { 
        $bandera = buscar_array_decomisos($_SESSION['ENFERMEDADES'][$i][0]);
        if ($bandera != false) {
            array_push($new_array2,[$_SESSION['ENFERMEDADES'][$i][0],$_SESSION['ENFERMEDADES'][$i][1],$_SESSION['ENFERMEDADES'][$i][2]]);
        }
    }
    unset($_SESSION['ENFERMEDADES']);
    $_SESSION['ENFERMEDADES'] = $new_array2;
    return true;
}
function f_obtener_decomisados($dbConn,$Subproducto,$Orden){
    $cont=0;
    $consulta="SELECT t.ddtCantidad FROM tbl_p_decomiso_detalle t, tbl_p_decomiso d 
                WHERE t.decId = d.decId AND t.subId = :sub AND d.ordId = :id";
    $sql= $dbConn->prepare($consulta);
    $sql->bindValue(':sub',$Subproducto);
    $sql->bindValue(':id',$Orden);
    $sql->execute();
    while($row = $sql->fetch()) {
        $cont+= $row["ddtCantidad"];
    }
    return $cont;
}
function f_obtener_decomisados_2($dbConn,$Subproducto,$Orden){
    $cont=0;
    $consulta="SELECT t.ddtCantidad FROM tbl_p_decomiso_detalle t, tbl_p_decomiso d 
                WHERE t.decId = d.decId AND 1 = 1 AND t.subId = :sub AND d.ordId = :id";
    $sql= $dbConn->prepare($consulta);
    $sql->bindValue(':sub',$Subproducto);
    $sql->bindValue(':id',$Orden);
    $sql->execute();
    while($row = $sql->fetch()) {
        $cont+= $row["ddtCantidad"];
    }
    return $cont;
}

function f_obtener_enfermeddes($dbConn,$Subproducto,$Orden){
    $cont=0;
    $consulta="SELECT ddeCantidad FROM tbl_p_decomiso_enfermedad t, tbl_p_decomiso d 
                WHERE t.decId = d.decId AND t.subId = :sub AND d.ordId = :id";
    $sql= $dbConn->prepare($consulta);
    $sql->bindValue(':sub',$Subproducto);
    $sql->bindValue(':id',$Orden);
    $sql->execute();
    while($row = $sql->fetch()) {
        $cont+= $row["ddeCantidad"];
    }
    return $cont;
}







function get_data_select_tipo_decomisos($dbConn){
    $Id = $_POST["Id"];
    $consulta="SELECT * FROM tbl_p_orden o, tbl_r_guiaproceso p, tbl_a_clientes c
    WHERE o.gprId is not null AND o.gprId = p.gprId AND p.cliId = c.cliId  AND o.ordEstado = 1 AND o.ordId = :id
    AND o.ordFechaTurno BETWEEN :inicio AND :fin ORDER BY o.ordFechaTurno ASC";
    $sql= $dbConn->prepare($consulta);
    $sql->bindValue(':id',$Id);
    $sql->bindValue(':inicio',date("Y-m-d")." 00:00:00");
    $sql->bindValue(':fin',date("Y-m-d")." 23:59:59");
    $sql->execute();
    $cont = 0;
    if($row = $sql->fetch()) {
        if ($row["ordTipo"]==0) {
            $bandera = f_buscar_orden($dbConn,$row["ordFecha"],$row["gprId"]);
        }else{
            $bandera = f_buscar_orden_1($dbConn,$row["ordFecha"],$row["gprId"]);
        }
        $decomisos = f_obtener_procesar($dbConn,$row["ordId"],$row["gprId"]);
        $saldo = intval($row["ordCantidad"]) - $decomisos;
        $footer = "";
        if ($bandera==0) {
            $input = '';
            if ($saldo == 0) {
                $input  = '
                <h5 >
                    <b>
                        <span class="text-muted">PROCESADO:</span>
                        '.$procesado.'
                    </b>
                </h5>';
            }else{
                $input = '
                <div class="input-group input-group-lg mt-3">
                    <div class="input-group-prepend">
                        <span class="input-group-text">
                            <b>
                                <span class="text-muted">SALDO: </span> '.$saldo.'
                            </b>
                        </span>
                    </div>
                    <input type="text" class="form-control text-center input_disablecopypaste" id="txtCantidad" onkeypress="f_restrincion(event)"  maxlength="4" style="font-size:30px;" value="'.$saldo.'">
                    <span class="input-group-append">
                        <button type="button" class="btn btn-info btn-flat" onclick="f_proccesar('.$row["ordId"].')"><b>PROCESAR</b></button>
                    </span>
                </div>';
            }
            $footer = $input.'<hr>
            <button type="button" class="btn btn-danger" id="btn-cerrar" data-dismiss="modal">
                <b>CANCELAR</b>
            </button>';
        }else{
            $footer ='
            <h5 class="text-center">
                <b>
                    <span class="text-muted">PROCESADO:</span>
                    '.$procesado.'
                </b>
            </h5>
            <h6><b>No se puede realizar los decomisos porque se acaba de generar otra orde de producción</b></h6>';
        }
        return '
        <h5 class="text-center">
            <b>
                <span class="text-muted">ORDEN DE PRODUCCIÓN</span><br>
                <a href="../../documentos/producion/orden/'.$row["ordNumOrden"].'.pdf"
                    target="_black">'.$row["ordNumOrden"].'</a>
            </b>
        </h5>
        <h6 class="row">
            <b class="col-md-6">
                <span id="spanCliente" class="d-none">'.utf8_encode($row["cliNombres"]).' ('.utf8_encode($row["cliMarca"]).')</span>
                <span class="text-muted" >CLIENTE:</span>
                '.utf8_encode($row["cliNombres"]).'
            </b>
            <b class="col-md-6">
                <span class="text-muted">MARCA:</span>
                '.utf8_encode($row["cliMarca"]).'
            </b>
        </h6>'.$footer;
    }else return 'ERROR-98219222';
}

function get_data_especie($dbConn,$Id){
    $consulta="SELECT espDescripcion FROM tbl_a_especies WHERE espId = :id";
	$sql= $dbConn->prepare($consulta);
    $sql->bindValue(':id',$Id);
	$sql->execute();
	if($row = $sql->fetch()) return $row["espDescripcion"];
    else return 'NO ENCONTRADO';
}
function get_parte_producto($dbConn,$Id){
    $consulta="SELECT proPartes FROM tbl_a_productos WHERE proId = :id";
	$sql= $dbConn->prepare($consulta);
    $sql->bindValue(':id',$Id);
	$sql->execute();
	if($row = $sql->fetch()) return $row["proPartes"];
    else return 'NO ENCONTRADO';
}

function get_name_producto($dbConn,$Id){
    $consulta="SELECT proDescripcion FROM tbl_a_productos WHERE proId = :id";
	$sql= $dbConn->prepare($consulta);
    $sql->bindValue(':id',$Id);
	$sql->execute();
	if($row = $sql->fetch()) return $row["proDescripcion"];
    else return 'NO ENCONTRADO';
}
function get_name_enfermedad($dbConn,$Id){
    $consulta="SELECT enfDescripcion FROM tbl_a_enfermedad WHERE enfId = :id";
	$sql= $dbConn->prepare($consulta);
    $sql->bindValue(':id',$Id);
	$sql->execute();
	if($row = $sql->fetch()) return utf8_encode($row["enfDescripcion"]);
    else return 'NO ENCONTRADO';
}
function get_name_subproducto($dbConn,$Id){
    $consulta="SELECT subDescripcion FROM tbl_a_subproductos WHERE subId = :id";
	$sql= $dbConn->prepare($consulta);
    $sql->bindValue(':id',$Id);
	$sql->execute();
	if($row = $sql->fetch()) return utf8_encode($row["subDescripcion"]);
    else return 'NO ENCONTRADO';
}
function get_name_genero($dbConn,$Id){
    $consulta="SELECT subSexo FROM tbl_a_subproductos WHERE subId = :id";
	$sql= $dbConn->prepare($consulta);
    $sql->bindValue(':id',$Id);
	$sql->execute();
	if($row = $sql->fetch()) return utf8_encode($row["subSexo"]);
    else return 'NO ENCONTRADO';
}
function get_data_visceras($dbConn,$Id){
    $cont = array();
    $consulta="SELECT * FROM tbl_r_visceras WHERE gprId = :id AND vscEliminado = 0";
	$sql= $dbConn->prepare($consulta);
    $sql->bindValue(':id',$Id);
	$sql->execute();
	while($row = $sql->fetch()) {
        array_push($cont,[$row["vscParte"],$row["vscSexo"]]);
	}   
    return  $cont;
}
function get_data_visceras_decomisadas($dbConn,$Id){
    $cont = array(0,0,0);
    $consulta="SELECT d.ddtCantidad,d.ddtSexo,d.ddtPartes FROM tbl_p_decomiso_detalle d, tbl_p_decomiso e WHERE d.decId = e.decId AND e.ordId = :id";
	$sql= $dbConn->prepare($consulta);
    $sql->bindValue(':id',$Id);
	$sql->execute();
	while($row = $sql->fetch()) {
        // if ($row["ddtSexo"]==0) $cont[0] += ($row["ddtCantidad"] * $row["ddtPartes"]);
        // else if ($row["ddtSexo"]==1) $cont[1] += ($row["ddtCantidad"] * $row["ddtPartes"]);
        // else if ($row["ddtSexo"]==2) $cont[2] += ($row["ddtCantidad"] * $row["ddtPartes"]);
        if ($row["ddtSexo"]==0) $cont[0] += $row["ddtCantidad"];
        else if ($row["ddtSexo"]==1) $cont[1] += $row["ddtCantidad"];
        else if ($row["ddtSexo"]==2) $cont[2] += $row["ddtCantidad"];
	}   
    return  $cont;
}
function get_data_antemortem($dbConn,$Id){
    $cont = [0,0,0,0];//Hembra, Macho
    $consulta="SELECT * FROM tbl_p_antemortem WHERE  gprId = :id";
	$sql= $dbConn->prepare($consulta);
    $sql->bindValue(':id',$Id);
	$sql->execute();
	while($row = $sql->fetch()) {
        if ($row["antDictamen"]==0) {
            if ($row["antSexo"]==0)$cont[0] += $row["antCantidad"];//Hembra
            else if ($row["antSexo"]==1)$cont[1] += $row["antCantidad"];//Macho
        }else{
            if ($row["antSexo"]==0)$cont[2] += $row["antCantidad"];//Hembra
            else if ($row["antSexo"]==1)$cont[3] += $row["antCantidad"];//Macho
        }
	}   
    return  $cont;
}
function get_data_decomisados($dbConn,$Id){
    $cont = 0;
    $consulta="SELECT d.decCantidad, p.proPartes,d.decPartes 
    FROM tbl_p_decomiso d, tbl_a_productos p 
    WHERE d.proId = p.proId AND d.ordId = :id AND d.proId IS NOT NULL ";
	$sql= $dbConn->prepare($consulta);
    $sql->bindValue(':id',$Id);
	$sql->execute();
	while($row = $sql->fetch()){
        $cantidad = $row["decCantidad"] / $row["decPartes"];
        $cont += $cantidad;
    } 
    return  $cont;
}
function get_data_decomisados_2($dbConn,$Id,$tipo){
    $cont = 0;
    $consulta="SELECT d.decCantidad, p.proPartes, d.decPartes 
    FROM tbl_p_decomiso d, tbl_a_productos p 
    WHERE d.proId = p.proId AND d.ordId = :id AND d.proId IS NOT NULL AND d.decSexo = :genero";
	$sql= $dbConn->prepare($consulta);
    $sql->bindValue(':id',$Id);
    $sql->bindValue(':genero',$tipo);
	$sql->execute();
	while($row = $sql->fetch()){
        $cantidad = $row["decCantidad"] / $row["decPartes"];
        $cont += $cantidad;
    } 
    return  $cont;
}

function f_nuevo_decomiso_total($dbConn){
    $consulta="SELECT * FROM tbl_p_orden WHERE ordId = :id";
	$sql= $dbConn->prepare($consulta);
    $sql->bindValue(':id',$_SESSION['ORDEN']);
	$sql->execute();
	if($row = $sql->fetch()){
        //$_SESSION['DATOSDECOMISO'] Producto,cantidad,causa,(0:Hembra,1:Macho)
        $procesado = get_data_decomisados_2($dbConn,$row["ordId"],$_SESSION['DATOSDECOMISO'][3]);
        $resutl = get_data_antemortem($dbConn,$row["gprId"]);//Hembra, Macho
        $ante = [0,0];
        if ($row["ordTipo"]== 0) {
            $ante = [$resutl[0],$resutl[1]];
        }else if ($row["ordTipo"]== 1) {
            $ante = [$resutl[2],$resutl[3]];
        }else{
            return '<b>Se encontro un error en el estado de la orden</b>';
        }
        $saldo =  $ante[$_SESSION['DATOSDECOMISO'][3]] - $procesado;
        if ($_SESSION['DATOSDECOMISO'][3] > $saldo ) return '<b>LA CANITDAD SELECCIONADA SOBREPASA EL SALDO DEL ANIMAL</b>';
        $return_Decomiso = f_insertar_decomiso($dbConn,[$_SESSION['DATOSDECOMISO'][1],$_SESSION['DATOSDECOMISO'][2],$_SESSION['DATOSDECOMISO'][3],$_SESSION['DATOSDECOMISO'][0],1]);
        if ($return_Decomiso != false) {
            for ($i=0; $i < count($_SESSION['ENFERMEDADES']) ; $i++) { // [Subproducto, Enfermedad, Cantidad]
                $subpro = get_name_subproducto($dbConn,$_SESSION['ENFERMEDADES'][$i][0]);
                $enfermedad  = get_name_enfermedad($dbConn,$_SESSION['ENFERMEDADES'][$i][1]);
                if (f_insertar_decomiso_enfermedad($dbConn,[$subpro,$enfermedad,$_SESSION['ENFERMEDADES'][$i][2],$_SESSION['ENFERMEDADES'][$i][0],$_SESSION['ENFERMEDADES'][$i][1],$return_Decomiso]) == false ) {//[Nombre del subprdocuto,Nombre de la enfermedad,Cantidad,Id del subproducto, Id de enfermedad,Id decomiso] 
                    return 'NO SE PUDO INGRESAR TODAS LA ENFERMEDAS';
                }
            }
            for ($i=0; $i < count($_SESSION['DECOMISOS']) ; $i++) { // [Subproducto, Cantidad,Genero]
                // [Cantidad,Nombre subproducto,Id del decomiso,Id del subproducto] 
                $subpro = get_name_subproducto($dbConn,$_SESSION['DECOMISOS'][$i][0]);
                $genero = get_name_genero($dbConn,$_SESSION['DECOMISOS'][$i][0]);
                if (f_insertar_decomiso_detalle($dbConn,[$_SESSION['DECOMISOS'][$i][1],$subpro,$return_Decomiso ,$_SESSION['DECOMISOS'][$i][0],$genero]) == false ) {
                    return 'NO SE PUDO INGRESAR TODOS LOS DECOMISOS';
                }
            }
            $detalle  = 'Se genero un Decomiso de la Orden '.$row["ordNumOrden"];
            $_SESSION['DECOMISO'][1] = 0;
            unset($_SESSION['DECOMISOS']);
            $_SESSION['DECOMISOS'] = array();
            unset($_SESSION['ENFERMEDADES']);
            $_SESSION['ENFERMEDADES'] = array();
            unset($_SESSION['DATOSDECOMISO']);
            $_SESSION['DATOSDECOMISO']=array(0,1,'',0);//Producto,cantidad,causa,(0:Hembra,1:Macho)
            return Insert_Login($return_Decomiso,'tbl_p_decomiso','Decomiso',$detalle,'');
        }else return 'ERROR-98333';
    } else return 'ERROR-122112';// NO SE ENCONTRO LA GUIA
}
function f_nuevo_decomiso_subproductos($dbConn){
    $consulta="SELECT * FROM tbl_p_orden WHERE ordId = :id";
	$sql= $dbConn->prepare($consulta);
    $sql->bindValue(':id',$_SESSION['ORDEN']);
	$sql->execute();
	if($row = $sql->fetch()){
        $procesado = get_data_decomisados_2($dbConn,$row["ordId"],$_SESSION['DATOSDECOMISO'][3]);
        $resutl = get_data_antemortem($dbConn,$row["gprId"]);//Hembra, Macho
        $ante = [0,0];
        if ($row["ordTipo"]== 0) {
            $ante = [$resutl[0],$resutl[1]];
        }else if ($row["ordTipo"]== 1) {
            $ante = [$resutl[2],$resutl[3]];
        }else{
            return '<b>Se encontro un error en el estado de la orden</b>';
        }
        $saldo =  $ante[$_SESSION['DATOSDECOMISO'][3]] - $procesado;
        if ($_SESSION['DATOSDECOMISO'][3] > $saldo ) return '<b>LA CANITDAD SELECCIONADA SOBREPASA EL SALDO DEL ANIMAL</b>';
        // [Cantidad,Causa,Sexo,Prodcuto,Partes] 
        $return_Decomiso = f_insertar_decomiso($dbConn,[0,'',NULL,NULL,0]);
        if ($return_Decomiso != false) {
            for ($i=0; $i < count($_SESSION['ENFERMEDADES']) ; $i++) { // [Subproducto, Enfermedad, Cantidad]
                $subpro = get_name_subproducto($dbConn,$_SESSION['ENFERMEDADES'][$i][0]);
                $enfermedad  = get_name_enfermedad($dbConn,$_SESSION['ENFERMEDADES'][$i][1]);
                if (f_insertar_decomiso_enfermedad($dbConn,[$subpro,$enfermedad,$_SESSION['ENFERMEDADES'][$i][2],$_SESSION['ENFERMEDADES'][$i][0],$_SESSION['ENFERMEDADES'][$i][1],$return_Decomiso]) == false ) {//[Nombre del subprdocuto,Nombre de la enfermedad,Cantidad,Id del subproducto, Id de enfermedad,Id decomiso] 
                    return 'NO SE PUDO INGRESAR TODAS LA ENFERMEDAS';
                }
            }
            for ($i=0; $i < count($_SESSION['DECOMISOS']) ; $i++) { // [Subproducto, Cantidad,Genero]
                // [Cantidad,Nombre subproducto,Id del decomiso,Id del subproducto] 
                $subpro = get_name_subproducto($dbConn,$_SESSION['DECOMISOS'][$i][0]);
                $genero = get_name_genero($dbConn,$_SESSION['DECOMISOS'][$i][0]);
                if (f_insertar_decomiso_detalle($dbConn,[$_SESSION['DECOMISOS'][$i][1],$subpro,$return_Decomiso ,$_SESSION['DECOMISOS'][$i][0],$genero]) == false ) {
                    return 'NO SE PUDO INGRESAR TODOS LOS DECOMISOS';
                }
            }
            $detalle  = 'Se genero un Decomiso de suproductos de la Orden '.$row["ordNumOrden"];
            $_SESSION['DECOMISO'][1] = 0;
            unset($_SESSION['DECOMISOS']);
            $_SESSION['DECOMISOS'] = array();
            unset($_SESSION['ENFERMEDADES']);
            $_SESSION['ENFERMEDADES'] = array();
            unset($_SESSION['DATOSDECOMISO']);
            $_SESSION['DATOSDECOMISO']=array(0,1,'',0);//Producto,cantidad,causa,(0:Hembra,1:Macho)
            return Insert_Login($return_Decomiso,'tbl_p_decomiso','Decomiso',$detalle,'');
        }else return 'ERROR-98333';
    } else return 'ERROR-122112';// NO SE ENCONTRO LA GUIA
}
function f_insertar_decomiso($dbConn,$Array){
    try {
        // $Array = [Cantidad,Causa,Sexo,Prodcuto,Partes] 
        global $User;
        global $Ip;
        $consulta = "INSERT INTO tbl_p_decomiso(decCantidad,decCausa,decFecha,decSexo,decPartes,ordId,proId,usuId,ip)
        VALUES(:decCantidad,:decCausa,:decFecha,:decSexo,:decPartes,:ordId,:proId,:usuId,:ip)";
		$sql= $dbConn->prepare($consulta);
        $sql->bindValue(':decCantidad',$Array[0]);
        $sql->bindValue(':decCausa',utf8_decode($Array[1]));
        $sql->bindValue(':decFecha',date("Y-m-d H:i:s"));
        $sql->bindValue(':decSexo',$Array[2]);
        $sql->bindValue(':decPartes',$Array[4]);
        $sql->bindValue(':ordId',$_SESSION['ORDEN']);
        $sql->bindValue(':proId',$Array[3]);
        $sql->bindValue(':usuId',$User);
        $sql->bindValue(':ip',$Ip);
		if ($sql->execute())return $dbConn->lastInsertId();
		else return false;
	}  catch (Exception $e) {
		Insert_Error('ERROR-22117532',$e->getMessage(),'Error al ingresar al registrar de el decomiso');
		exit("ERROR-22117532");
	}
}
function f_insertar_decomiso_detalle($dbConn,$Array){
    try {
        // $Array = [Cantidad,Nombre subproducto,Id del decomiso,Id del subproducto,Sexo] 
        global $User;
        global $Ip;
        $consulta = "INSERT INTO tbl_p_decomiso_detalle( ddtCantidad,ddtSubproducto,ddtSexo,decId,subId)
        VALUES(:ddtCantidad,:ddtSubproducto,:ddtSexo,:decId,:subId)";
		$sql= $dbConn->prepare($consulta);
        $sql->bindValue(':ddtCantidad',$Array[0]);
        $sql->bindValue(':ddtSubproducto',utf8_decode($Array[1]));
        $sql->bindValue(':ddtSexo',$Array[4]);
        $sql->bindValue(':decId',$Array[2]);
        $sql->bindValue(':subId',$Array[3]);
		if ($sql->execute())return true;
		else return false;
	}  catch (Exception $e) {
		Insert_Error('ERROR-226612',$e->getMessage(),'Error al ingresar al registrar de el detalle decomiso ');
		exit("ERROR-226612");
	}
}
function f_insertar_decomiso_enfermedad($dbConn,$Array){
    try {
        // $Array = [Nombre del subprdocuto,Nombre de la enfermedad,Cantidad,Id del subproducto, Id de enfermedad,Id decomiso] 
        global $User;
        global $Ip;
        $consulta = "INSERT INTO tbl_p_decomiso_enfermedad(ddeSubproducto,ddeEnfermedad,ddeCantidad,subId,enfId,decId)
        VALUES(:ddeSubproducto,:ddeEnfermedad,:ddeCantidad,:subId,:enfId,:decId)";
		$sql= $dbConn->prepare($consulta);
        $sql->bindValue(':ddeSubproducto',utf8_decode($Array[0]));
        $sql->bindValue(':ddeEnfermedad',utf8_decode($Array[1]));
        $sql->bindValue(':ddeCantidad',$Array[2]);
        $sql->bindValue(':subId',$Array[3]);
        $sql->bindValue(':enfId',$Array[4]);
        $sql->bindValue(':decId',$Array[5]);
		if ($sql->execute())return true;
		else return false;
	}  catch (Exception $e) {
		Insert_Error('ERROR-2211082',$e->getMessage(),'Error al ingresar al registrar las enfermedades del decomiso');
		exit("ERROR-2211082");
	}
}

// MIS DECOMISOS
function table_mis_decomisos($dbConn){
    global $User;
    $table = '
    <table class="table table-sm table-bordered table-striped table-hover table-bordered text-center" id="tbl_mis_decomisos">
        <thead>
            <tr>
                <th>#</th>
                <th>CLIENTE</th>
                <th>MARCA</th>
                <th>DECOMISO</th>
                <th>CANTIDAD</th>
                <th>ACCIONES</th>
            </tr>
        </thead>
        <tbody>';
    $consulta="SELECT d.decId ,c.cliNombres,c.cliMarca,d.proId, d.decCantidad FROM tbl_p_decomiso d, tbl_p_orden o, tbl_r_guiaproceso p, tbl_a_clientes c 
    WHERE d.ordId = o.ordId AND o.gprId = p.gprId AND p.cliId = c.cliId AND p.espId = :tipo
    AND d.usuId = :user AND d.actId IS NULL";
    $sql= $dbConn->prepare($consulta);
    $sql->bindValue(':user',$User);
    $sql->bindValue(':tipo',$_SESSION['DECOMISO'][0]);
    $sql->execute();
    $cont = 0;
    while($row = $sql->fetch()){
        $cont++;
        $decomiso = "ERROR--121";
        $cant = f_buscar_demosiado_m($dbConn,$row["decId"]);
        if (is_null($row["proId"])) {
            $decomiso = "<span class='badge badge-warning'><b>SUBPRODUCTOS</b></span>";
        }else{
            if ($cant > 0) {
                $decomiso = "<span class='badge badge-success'><b>TOTAL</b></span>";
            }else{
                $decomiso = "<span class='badge badge-info'><b> PARCIAL</b></span>";
            }
            $cant = $row["decCantidad"];
        }
        $table .= '
        <tr>
            <th>'.$cont.'</th>
            <td>'.utf8_encode($row["cliNombres"]).'</td>
            <td>'.utf8_encode($row["cliMarca"]).'</td>
            <td>'.$decomiso.'</td>
            <td>'.$cant.'</td>
            <td>
                <button class="btn btn-info btn-sm"  onclick="get_data_view_decomiso('.$row["decId"].')"  data-toggle="modal" data-target="#Modal"  ><i class="fas fa-eye"></i></button>
                <button class="btn btn-danger btn-sm d-none"><i class="fas fa-trash-alt"></i></button>
            </td>
        </tr>
        ';
    } 
    $header  = '<h5 class="text-muted text-center"><b>DECOMISOS GENERADOS</b></h5>'; 
    $button = '';
    if ($cont > 0) {
        $button = '<center><button class="btn btn-danger" onclick="Generar_Acta()" ><b>GENERAR ACTA DE DECOMISO</b></button></center>';
    }
    $re = '<button class="btn btn-info btn-sm float-right" onclick="get_data_mis_decomisos()"><b>RECARGAR</b></button>';
    return $header.$re.$table.'</tbody></table>'.$button;
} 

function f_buscar_demosiado_m($dbConn,$Id){
    $consulta="SELECT ddtCantidad FROM tbl_p_decomiso_detalle WHERE decId = :id";
    $sql= $dbConn->prepare($consulta);
    $sql->bindValue(':id',$Id);
    $sql->execute();
    $cont = 0;
    while($row = $sql->fetch())$cont += $row["ddtCantidad"];
    return $cont;
}

function get_data_view_decomiso($dbConn){
    $Id = $_POST["Id"];
    $consulta="SELECT d.decId ,c.cliNombres,c.cliMarca,d.proId, d.decCantidad, o.ordTipo ,o.ordNumOrden, d.decCausa, d.decSexo
    FROM tbl_p_decomiso d, tbl_p_orden o, tbl_r_guiaproceso p, tbl_a_clientes c 
    WHERE d.ordId = o.ordId AND o.gprId = p.gprId AND p.cliId = c.cliId AND p.espId = :tipo AND d.decId = :id";
    $sql= $dbConn->prepare($consulta);
    $sql->bindValue(':id',$Id);
    $sql->bindValue(':tipo',$_SESSION['DECOMISO'][0]);
    $sql->execute();
    $cont = 0;
    if($row = $sql->fetch()){
        $cont++;
        $decomiso = "ERROR--121";
        $detalle = "";
        $cabecera = "";
        $cant = f_buscar_demosiado_m($dbConn,$row["decId"]);
        if (is_null($row["proId"])) {
            $decomiso = "DECOMISO DE SUBPRODUCTOS</b>";
            $detalle = '<hr>
            <div class="row">
                <div class="col-lg-6">'.f_buscar_demosiado_detalle($dbConn,$row["decId"]).'</div>
                <div class="col-lg-6">'.f_buscar_demosiado_enfermedad($dbConn,$row["decId"]).'</div>
            </div>';
        }else{
            if ($cant > 0) {
                $decomiso = "DECOMISO TOTAL";
                $genero = "ERROR";
                if ($row["decSexo"]==0) $genero = "Hembra";
                else if ($row["decSexo"]==1) $genero = "Macho";
                $cabecera = '
                <h6 class="row">
                    <b class="col-md-6">
                        <span class="text-muted">PRODUCTO:</span>
                        '.get_name_producto($dbConn,$row["proId"]).'
                    </b>
                    <b class="col-md-6">
                        <span class="text-muted">CANTIDAD:</span>
                        '.$row["decCantidad"].'
                    </b>
                </h6>
                <h6 class="row">
                    <b class="col-md-6">
                        <span class="text-muted">Causa:</span>
                        '.utf8_encode($row["decCausa"]).'
                    </b>
                    <b class="col-md-6">
                        <span class="text-muted">Tipo:</span>
                        '.$genero.'
                    </b>
                </h6>';
                $detalle = '<hr>
                <div class="row">
                    <div class="col-lg-6">'.f_buscar_demosiado_detalle($dbConn,$row["decId"]).'</div>
                    <div class="col-lg-6">'.f_buscar_demosiado_enfermedad($dbConn,$row["decId"]).'</div>
                </div>';
            }else{
                $decomiso = "DECOMISO  PARCIAL";
                $genero = "ERROR";
                if ($row["decSexo"]==0) $genero = "Hembra";
                else if ($row["decSexo"]==1) $genero = "Macho";
                $cabecera = '
                <h6 class="row">
                    <b class="col-md-6">
                        <span class="text-muted">PRODUCTO:</span>
                        '.get_name_producto($dbConn,$row["proId"]).'
                    </b>
                    <b class="col-md-6">
                        <span class="text-muted">CANTIDAD:</span>
                        '.$row["decCantidad"].'
                    </b>
                </h6>
                <h6 class="row">
                    <b class="col-md-6">
                        <span class="text-muted">Causa:</span>
                        '.utf8_encode($row["decCausa"]).'
                    </b>
                    <b class="col-md-6">
                        <span class="text-muted">Tipo:</span>
                        '.$genero.'
                    </b>
                </h6>';
            }
            $cant = $row["decCantidad"];
        }
        $ruta = '';
        if ($row["ordTipo"]==0) $ruta = 'orden';
        else if ($row["ordTipo"]==1) $ruta = 'emergente';
        return '
                <h4 class="text-muted text-center"><b>'.$decomiso.'</b></h4>
                <h5>
                    <b>
                        <span class="text-muted">ORDEN DE PRODUCCIÓN: </span>
                        <a href="../../documentos/producion/'.$ruta.'/'.$row["ordNumOrden"].'.pdf" target="_black">'.$row["ordNumOrden"].'</a>
                    </b>
                </h5>
                <h6 class="row">
                    <b class="col-md-6">
                        <span class="text-muted">CLIENTE:</span>
                        '.utf8_encode($row["cliNombres"]).'
                    </b>
                    <b class="col-md-6">
                        <span class="text-muted">MARCA:</span>
                        '.utf8_encode($row["cliMarca"]).'
                    </b>
                </h6>'.$cabecera.$detalle;

    }else return 'ERROR-883737373';
}
function f_buscar_demosiado_detalle($dbConn,$Id){
    $table = '<table class="table table-sm table-bordered table-striped table-hover table-bordered text-center">
    <thead>
        <tr>
            <th>#</th>
            <th>Subproducto</th>
            <th>Cantidad</th>
        </tr>
    </thead>
    <tbody>';
    $consulta="SELECT * FROM tbl_p_decomiso_detalle WHERE decId = :id";
    $sql= $dbConn->prepare($consulta);
    $sql->bindValue(':id',$Id);
    $sql->execute();
    $cont = 0;
    $total=0;
    while($row = $sql->fetch()){
        $cont++;
        $table .='
        <tr>
            <th>'.$cont.'</th>
            <td>'.utf8_encode($row["ddtSubproducto"]).'</td>
            <td>'.$row["ddtCantidad"].'</td>
        </tr>';
        $total += $row["ddtCantidad"];
    }
    $foot = '';
    if ($total > 0) {
        $foot = '<tfoot><tr><th colspan="2" >TOTAL</th><th>'.$total.'</th></tr></tfoot>';
    }
    return '<div class="table-responsive">'.$table.'</tbody>'.$foot.'</table></div>';
}
function f_buscar_demosiado_enfermedad($dbConn,$Id){
    $table = '<table class="table table-sm table-bordered table-striped table-hover table-bordered text-center">
    <thead>
        <tr>
            <th>#</th>
            <th>Enfermedad</th>
            <th>Cantidad</th>
        </tr>
    </thead>
    <tbody>';
    $consulta="SELECT DISTINCT enfId FROM tbl_p_decomiso_enfermedad WHERE decId = :id";
    $sql= $dbConn->prepare($consulta);
    $sql->bindValue(':id',$Id);
    $sql->execute();
    $cont = 0;
    while($row = $sql->fetch()){
        $cont++;
        $table .='
        <tr>
            <th>'.$cont.'</th>
            '.get_dat_tr_enfermedades($dbConn,$Id,$row["enfId"]).'
        </tr>';
    }
    return '<div class="table-responsive">'.$table.'</tbody></table></div>';
}
function get_dat_tr_enfermedades($dbConn,$Id,$Enfermedad){
    
    $consulta="SELECT  * FROM tbl_p_decomiso_enfermedad WHERE decId = :id AND enfId = :ef";
    $sql= $dbConn->prepare($consulta);
    $sql->bindValue(':id',$Id);
    $sql->bindValue(':ef',$Enfermedad);
    $sql->execute();
    $cont = 0;
    $Des = "";
    while($row = $sql->fetch()){
        $cont += $row["ddeCantidad"];
        $Des = utf8_encode($row["ddeEnfermedad"]);
    }
    return '<td>'.$Des.'</td><td>'.$cont.'</td>';
}


function f_while_acta($dbConn,$Id){
    global $User;
    $consulta="SELECT d.decId ,c.cliNombres,c.cliMarca,d.proId, d.decCantidad FROM tbl_p_decomiso d, tbl_p_orden o, tbl_r_guiaproceso p, tbl_a_clientes c 
    WHERE d.ordId = o.ordId AND o.gprId = p.gprId AND p.cliId = c.cliId AND p.espId = :tipo
    AND d.usuId = :user AND d.actId IS NULL";
    $sql= $dbConn->prepare($consulta);
    $sql->bindValue(':user',$User);
    $sql->bindValue(':tipo',$_SESSION['DECOMISO'][0]);
    $sql->execute();
    while($row = $sql->fetch()){
        if (f_update_acta($dbConn,$row["decId"],$Id) == false )return '<h5><b>No se pudo agregar un demociso a la acta</b></h5>';
    }
    return true;
} 
function f_update_acta($dbConn,$Id,$Acta){
    try {
        $consulta="UPDATE tbl_p_decomiso SET actId = :acta  WHERE decId = :id";
        $sql= $dbConn->prepare($consulta);
        $sql->bindValue(':acta',$Acta);
        $sql->bindValue(':id',$Id);
        if($sql->execute())return true;
        else return false;
    }  catch (Exception $e) {
        Insert_Error('ERROR-82222',$e->getMessage(),'Error al ingresar al actualizar el decomiso');
        exit("ERROR-82222");
    }
}


?>
