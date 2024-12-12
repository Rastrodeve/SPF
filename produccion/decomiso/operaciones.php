<?php
require '../../FilePHP/utils.php';
if (isset($_REQUEST['op'])) {
	$dbConn = conectar($db);
	$op=$_REQUEST['op'];
	if ($op==1)echo get_data_cont($dbConn);
	elseif ($op==2) $_SESSION['DECOMISO'][0] = $_POST["Id"];
	elseif ($op==3)echo get_data_table($dbConn,$_SESSION['DECOMISO'][0]);
	elseif ($op==4)echo get_data_orden($dbConn);
    elseif ($op==5) $_SESSION['DECOMISO'][0] = 0;
    elseif ($op==6)echo f_procesar_data($dbConn);
    elseif ($op==7){
        $_SESSION['ORDEN'] = $_POST["Id"];
        $_SESSION['DECOMISO'][1] = $_POST["OPCION"];
    }
    elseif ($op==8)echo get_data_enfermedades($dbConn);
    elseif ($op==9)echo pasar_array($dbConn);
    elseif ($op==10)echo table_enfermedades($dbConn);
    elseif ($op==11)echo delete_enfermedad();
    elseif ($op==12) {
        $_SESSION['DECOMISO'][1] = 0;
        unset($_SESSION['DECOMISOS']);
        $_SESSION['DECOMISOS'] = array();
        unset($_SESSION['ENFERMEDADES']);
        $_SESSION['ENFERMEDADES'] = array();
        unset($_SESSION['DATOSDECOMISO']);
        $_SESSION['DATOSDECOMISO']=array(0,1,'',0);//Producto,cantidad,causa,(0:Hembra,1:Macho)
    }
    elseif ($op==13)echo get_data_select_decomisos($dbConn);
    elseif ($op==14)echo pasar_array_decomisos($dbConn);
    elseif ($op==15)echo table_decomisos($dbConn);
    elseif ($op==16)echo delete_decomisos();
    elseif ($op==17)echo pasar_array_datos($dbConn);
    elseif ($op==18) {
        unset($_SESSION['DECOMISOS']);
        $_SESSION['DECOMISOS'] = array();
        unset($_SESSION['ENFERMEDADES']);
        $_SESSION['ENFERMEDADES'] = array();
        unset($_SESSION['DATOSDECOMISO']);
        $_SESSION['DATOSDECOMISO']=array(0,1,'',0);//Producto,cantidad,causa,(0:Hembra,1:Macho)
    }elseif ($op==19)echo f_nuevo_decomiso_total($dbConn);
    elseif ($op==20)echo f_inserat_decomiso_pacial($dbConn);
    elseif ($op==21)echo f_nuevo_decomiso_subproductos($dbConn);
    elseif ($op==22)echo table_mis_decomisos($dbConn);
    elseif ($op==23)echo get_data_table_emergente($dbConn,$_SESSION['DECOMISO'][0]);
    elseif ($op==24)echo get_data_view_decomiso($dbConn);
    elseif ($op==25)echo f_insert_acta($dbConn);

}else{
	header('location: ../../');
}



function get_data_cont($dbConn){
    $opcion = $_SESSION['DECOMISO'][0];
    if ($opcion == 0) {
        $formulario = 
        '<div class="row">
            <div class="col-12 mt-2 text-center">
                <form action="formulario_postmorten.php" target="_blank" method="GET">
                    <label>Fecha Formulario</label>
                    <input type="date" id="idFechaFormulario" required name="fechaFormulario">
                    <br>
                    <center>
                        <input type="submit" class="btn btn-info btn-lg pl-5 pr-5 pt-3 pb-3" name="GENERAR">
                    </center>
                </form>
            </div>
        </div>';
        $seleccion = '
        <div class="row">
            <div class="col-12 mt-2 text-center">
                <select class="form-control form-control-lg select2bs4" onchange="f_mensaje()" id="slcTipo" style="width:100%;">
                    '.select_data_especies($dbConn).'
                </select>
            </div>
        </div>
        <hr>
        <div id="cont-result"></div>';
        return $formulario.$seleccion;
    }else{
        if ($_SESSION['DECOMISO'][1] == 0) {
            return '
            <div class="card">
                <div class="card-body">
                    <div class="row mb-2">
                        <div class="col-6">
                            <b>
                                <span class="text-muted">GANADO SELECCIONADO:</span>
                                '.get_data_especie($dbConn,$opcion).'
                            </b>
                        </div>
                        <div class="col-6">
                            <button class="btn btn-danger btn-sm float-right" onclick="f_regresar()"><b>REGRESAR</b></button>
                        </div>
                    </div>
                    <hr>
                    <div id="cont-contenido">
                        <div class="card  card-navy card-tabs">
                            <div class="card-header   p-0 pt-1">
                                <ul class="nav nav-tabs" id="custom-tabs-five-tab" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link active" id="custom-tabs-five-overlay-tab" data-toggle="pill" href="#custom-tabs-five-overlay" role="tab" aria-controls="custom-tabs-five-overlay" aria-selected="true">
                                            <b>PRODUCCIÓN</b>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" id="custom-tabs-five-overlay-dark-tab" data-toggle="pill" href="#custom-tabs-five-overlay-dark" role="tab" aria-controls="custom-tabs-five-overlay-dark" aria-selected="false">
                                            <b>EMERGENTE</b>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" id="custom-tabs-five-overlay-dark-tab" data-toggle="pill" href="#custom-tabs-five-overlay-dark-2" role="tab" aria-controls="custom-tabs-five-overlay-dark" aria-selected="false">
                                            <b>MIS DECOMISOS</b>
                                        </a>
                                    </li>
                                </ul>
                                </div>
                                <div class="card-body">
                                <div class="tab-content" id="custom-tabs-five-tabContent">
                                    <div class="tab-pane fade show active" id="custom-tabs-five-overlay" role="tabpanel" aria-labelledby="custom-tabs-five-overlay-tab">
                                        <div class="overlay-wrapper">
                                            <button class="btn btn-info btn-sm float-right" onclick="get_data_table()" ><b>RECARGAR TABLA</b></button>
                                            <div id="cont-table-fae">
                                                '.get_data_table($dbConn,$opcion).'
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane fade" id="custom-tabs-five-overlay-dark" role="tabpanel" aria-labelledby="custom-tabs-five-overlay-dark-tab">
                                        <div class="overlay-wrapper">
                                            <div id="con-table-emer">
                                                '.get_data_table_emergente($dbConn,$opcion).'
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane fade" id="custom-tabs-five-overlay-dark-2" role="tabpanel" aria-labelledby="custom-tabs-five-overlay-dark-tab">
                                        <div class="overlay-wrapper">
                                            '.table_mis_decomisos($dbConn).'
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>';   
        }
        else if ($_SESSION['DECOMISO'][1] == 1) {
            return get_data_decomiso_total($dbConn,$_SESSION['ORDEN']);
        }else if ($_SESSION['DECOMISO'][1] == 2) {
            return get_data_decomiso_parcial($dbConn,$_SESSION['ORDEN']);
        }else if ($_SESSION['DECOMISO'][1] == 3) {
            return get_data_decomiso_subprodcutos($dbConn,$_SESSION['ORDEN']);
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
                <th class="text-center">MARCA</th>
                <th class="text-center">GANADO</th>
                <th class="text-center">SUBPRODUCTOS</th>
                <th class="text-center">DECOMISAR</th>
            </thead>
            <tbody style="font-size:16px;">';
        $consulta="SELECT o.ordId,o.gprId,c.cliNombres,c.cliMarca,o.ordCantidad,p.gprestadoDetalle,o.ordFecha FROM tbl_p_orden o, tbl_r_guiaproceso p, tbl_a_clientes c
        WHERE o.gprId is not null AND o.gprId = p.gprId AND p.cliId = c.cliId   AND o.ordTipo = 0   AND o.espId = :id
        AND o.ordFecha BETWEEN :inicio AND :fin ORDER BY o.ordId ASC";//
        $sql= $dbConn->prepare($consulta);
        $sql->bindValue(':id',$Id);
        $sql->bindValue(':inicio',date("Y-m-d")." 00:00:00");
        $sql->bindValue(':fin',date("Y-m-d")." 23:59:59");
        $sql->execute();
        $cont = 0;
        while ($row = $sql->fetch()) {
            $procesado = f_obtener_procesar($dbConn,$row["ordId"],$row["gprId"]);
            $saldo = intval($row["ordCantidad"]) - $procesado;
            $mensaje_saldo="bg-danger";
            $bandera = f_buscar_orden($dbConn,$row["ordFecha"],$row["gprId"]);
            if ($bandera == 0) {
                if ($saldo == 0)$mensaje_saldo = "bg-success";
                else if ($saldo > 0)$mensaje_saldo="bg-warning";
                // Estado Detalle = 0; No se detalla la guia => El Estado debe ser 1
                // Estado Detalle = 1; Se detalla la guia => El Estado debe ser 0
                $span = 'ERROR'.$row["gprestadoDetalle"].'';
                if ($row["gprestadoDetalle"]==0 || $row["gprestadoDetalle"]==1) {
                    $cont++;
                    if ($row["gprestadoDetalle"]==1){
                        $span = '*'.$cont.'';
                    }else if ($row["gprestadoDetalle"]==0){
                        $span = ''.$cont.'';
                    }
                }
                $ante = get_data_antemortem($dbConn,$row["gprId"]);//Hembra, Macho
                $ArrayVisceras = get_data_visceras($dbConn,$row["gprId"]);//Parte ,Sexo
                $viseras = 0;
                for ($i = 0; $i < count($ArrayVisceras) ; $i++) { 
                    if ($ArrayVisceras[$i][1]==0)$viseras += (($ante[0] + $ante[1]) * $ArrayVisceras[$i][0]);
                    else if ($ArrayVisceras[$i][1]==1)$viseras += ($ante[0] * $ArrayVisceras[$i][0] );
                    else if ($ArrayVisceras[$i][1]==2)$viseras += ($ante[1] * $ArrayVisceras[$i][0]);
                }
                $resultado .='
                <tr>
                    <th class="text-center">'.$span.'</th>
                    <td>'.utf8_encode($row["cliNombres"]).'</td>
                    <td class="text-center">'.utf8_encode($row["cliMarca"]).'</td>
                    <td class="text-center "><b>'.$row["ordCantidad"].'</b></td>
                    <td class="text-center "><b>'.$viseras.'</b></td>
                    <td class="text-center">
                        <button class="btn btn-danger" onclick="get_data_procesar('.$row["ordId"].')"  data-toggle="modal" data-target="#Modal">
                            <b>DECOMISAR</b>
                        </button>
                    </td>
                </tr>';
            }
        }
    return $resultado.'</tbody></table>';
}
function get_data_table_emergente($dbConn,$Id){
    $resultado = '
        <table id="tbl_table_emergente"
            class="table table-bordered table-striped table-hover table-bordered">
            <thead class="bg-navy" style="font-size:18px;">
                <th class="text-center">#</th>
                <th>CLIENTE</th>
                <th class="text-center">MARCA</th>
                <th class="text-center">GANADO</th>
                <th class="text-center">SUBPRODUCTOS</th>
                <th class="text-center">DECOMISAR</th>
            </thead>
            <tbody style="font-size:16px;">';
        $consulta="SELECT o.ordId,o.gprId,c.cliNombres,c.cliMarca,o.ordCantidad,p.gprestadoDetalle,o.ordFecha FROM tbl_p_orden o, tbl_r_guiaproceso p, tbl_a_clientes c
        WHERE o.gprId is not null AND o.gprId = p.gprId AND p.cliId = c.cliId  AND o.ordTipo = 1 AND o.espId = :id
        AND o.ordFecha BETWEEN :inicio AND :fin ORDER BY o.ordId ASC";
        $sql= $dbConn->prepare($consulta);
        $sql->bindValue(':id',$Id);
        $sql->bindValue(':inicio',date("Y-m-d")." 00:00:00");
        $sql->bindValue(':fin',date("Y-m-d")." 23:59:59");
        $sql->execute();
        $cont = 0;
        while ($row = $sql->fetch()) {
            $procesado = f_obtener_procesar($dbConn,$row["ordId"],$row["gprId"]);
            $saldo = intval($row["ordCantidad"]) - $procesado;
            $mensaje_saldo="bg-danger";
            $bandera = f_buscar_orden_1($dbConn,$row["ordFecha"],$row["gprId"]);
            if ($bandera == 0) {
                if ($saldo == 0)$mensaje_saldo = "bg-success";
                else if ($saldo > 0)$mensaje_saldo="bg-warning";
                // Estado Detalle = 0; No se detalla la guia => El Estado debe ser 1
                // Estado Detalle = 1; Se detalla la guia => El Estado debe ser 0
                $span = 'ERROR'.$row["gprestadoDetalle"].'';
                if ($row["gprestadoDetalle"]==0 || $row["gprestadoDetalle"]==1) {
                    $cont++;
                    if ($row["gprestadoDetalle"]==1){
                        $span = '*'.$cont.'';
                    }else if ($row["gprestadoDetalle"]==0){
                        $span = ''.$cont.'';
                    }
                }
                $ante = get_data_antemortem($dbConn,$row["gprId"]);//Hembra, Macho
                $ArrayVisceras = get_data_visceras($dbConn,$row["gprId"]);//Parte ,Sexo
                $viseras = 0;
                for ($i = 0; $i < count($ArrayVisceras) ; $i++) { 
                    if ($ArrayVisceras[$i][1]==0)$viseras += (($ante[2] + $ante[3]) * $ArrayVisceras[$i][0]);
                    else if ($ArrayVisceras[$i][1]==1)$viseras += ($ante[2] * $ArrayVisceras[$i][0] );
                    else if ($ArrayVisceras[$i][1]==2)$viseras += ($ante[3] * $ArrayVisceras[$i][0]);
                }
                $resultado .='
                <tr>
                    <th class="text-center">'.$span.'</th>
                    <td>'.utf8_encode($row["cliNombres"]).'</td>
                    <td class="text-center">'.utf8_encode($row["cliMarca"]).'</td>
                    <td class="text-center "><b>'.$row["ordCantidad"].'</b></td>
                    <td class="text-center "><b>'.$viseras.'</b></td>
                    <td class="text-center">
                        <button class="btn btn-danger" onclick="get_data_procesar('.$row["ordId"].')"  data-toggle="modal" data-target="#Modal">
                            <b>DECOMISAR</b>
                        </button>
                    </td>
                </tr>';
            }
        }
    $button = '<button class="btn btn-sm btn-info float-right" onclick="get_data_emergente()"><b>RECARGAR</b></button>';
    return $button.$resultado.'</tbody></table>';
}
function get_data_decomiso_total($dbConn,$orden){
    $cont=0;
    $consulta="SELECT o.ordNumOrden ,c.cliNombres, c.cliMarca, o.gprId,o.ordCantidad,o.ordId, o.ordTipo 
    FROM tbl_p_orden o, tbl_r_guiaproceso p, tbl_a_clientes c  
    WHERE o.gprId = p.gprId AND p.cliId = c.cliId  AND o.ordId = :id";
    $sql= $dbConn->prepare($consulta);
    $sql->bindValue(':id',$orden);
    $sql->execute();
    if($row = $sql->fetch()) {
        $procesado = get_data_decomisados($dbConn,$row["ordId"],$row["gprId"]);
        $saldo = intval($row["ordCantidad"]) - $procesado;
        $select = select_data_subproductos($dbConn);
        $ArrayVisceras = get_data_visceras($dbConn,$row["gprId"]);//Parte ,Sexo
        $viseras = 0;
        $hembra = get_data_decomisados_2($dbConn,$row["ordId"],0);
        $macho = get_data_decomisados_2($dbConn,$row["ordId"],1);

        $resutl = get_data_antemortem($dbConn,$row["gprId"]);//Hembra, Macho
        $ante = [0,0];
        if ($row["ordTipo"]== 0) {
            $ante = [$resutl[0],$resutl[1]];
        }else if ($row["ordTipo"]== 1) {
            $ante = [$resutl[2],$resutl[3]];
        }else{
            return '<b>Se encontro un error en el estado de la orden</b>';
        }
        $r_hembra =  $ante[0] - $hembra;
        $r_macho =  $ante[1] - $macho;
        $s_hemnras = 0;
        $s_machos = 0;
        $ambos = 0;
        for ($i = 0; $i < count($ArrayVisceras) ; $i++) { 
            if ($ArrayVisceras[$i][1]==0){
                $ambos += (($ante[0] + $ante[1]) * $ArrayVisceras[$i][0]);
                $viseras += (($ante[0] + $ante[1]) * $ArrayVisceras[$i][0]);
            }else if ($ArrayVisceras[$i][1]==1){
                $s_hemnras += ($ante[0] * $ArrayVisceras[$i][0]);
                $viseras += ($ante[0] * $ArrayVisceras[$i][0]);
            }else if ($ArrayVisceras[$i][1]==2){
                $viseras += ($ante[1] * $ArrayVisceras[$i][0]);
                $s_machos += ($ante[1] * $ArrayVisceras[$i][0]);
            }
        }
        $v_procesadas = get_data_visceras_decomisadas($dbConn,$row["ordId"]);
        $visceras_procesadas = $v_procesadas[0] + $v_procesadas[1] + $v_procesadas[2];
        $visceras_restante = $viseras - $visceras_procesadas;
        $restante_s_hembras = $s_hemnras - $v_procesadas[1];
        $restante_s_machos = $s_machos - $v_procesadas[2];
        $restante_s_ambos =  $ambos  - $v_procesadas[0];

        $mensaje_viceras = '
        <h6 class="row">
                    <b class="col-md-6">
                        <span class="text-muted">TOTAL DE VISCERAS :</span>
                        '.$viseras .'
                    </b>
                    <b class="col-md-6">
                        <span class="text-muted ">SOLO HEMBRAS:</span>
                        '.$s_hemnras.'
                        <span class="text-muted ml-3">SOLO MACHOS:</span>
                        '.$s_machos.'
                        <span class="text-muted ml-3">AMBOS:</span>
                        '.$ambos.'
                    </b>
                </h6>
                <h6 class="row">
                    <b class="col-md-6">
                        <span class="text-muted">VISCERAS RESTANTES:</span>
                        '.$visceras_restante.'
                    </b>
                    <b class="col-md-6">
                        <span class="text-muted ">RES. SOLO HEMBRAS:</span>
                        '.$restante_s_hembras.'
                        <span class="text-muted ml-3">RES. SOLO MACHOS:</span>
                        '.$restante_s_machos.'
                        <span class="text-muted ml-3">RES. AMBOS:</span>
                        '.$restante_s_ambos.'
                    </b>
                </h6>';
        $ruta = '';
        if ($row["ordTipo"]==0) $ruta = 'orden';
        else if ($row["ordTipo"]==1) $ruta = 'emergente';
        return '
        <div class="card">
            <div class="card-body">
                <button class="btn btn-danger float-right" onclick="f_cancelar_1()" ><b>CANCELAR DECOMISO</b></button>
                <h4 class="text-muted text-center"><b>DECOMISO TOTAL</b></h4>
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
                </h6>
                <h6 class="row">
                    <b class="col-md-6">
                        <span class="text-muted">GANADO TOTAL:</span>
                        '.$row["ordCantidad"].'
                    </b>
                    <b class="col-md-6">
                        <span class="text-muted ">MACHO:</span>
                        '.$ante[1].'
                        <span class="text-muted ml-3">HEMBRA:</span>
                        '.$ante[0].'
                    </b>
                </h6>
                <h6 class="row">
                    <b class="col-md-6">
                        <span class="text-muted">GANDO RESTANTE:</span>
                        '.$saldo.'
                    </b>
                    <b class="col-md-6">
                    <span class="text-muted ">MACHO RESTANTE:</span>
                        '.$r_macho.'
                        <span class="text-muted ml-3">HEMBRA RESTANTE:</span>
                        '.$r_hembra.'
                    </b>
                </h6>
                <h6 class="row d-none">
                    <b class="col-md-6">
                        <span class="text-muted">TOTAL DE VISCERAS :</span>
                        '.$viseras .'
                    </b>
                    <b class="col-md-6">
                        <span class="text-muted ">SOLO HEMBRAS:</span>
                        '.$s_hemnras.'
                        <span class="text-muted ml-3">SOLO MACHOS:</span>
                        '.$s_machos.'
                        <span class="text-muted ml-3">AMBOS:</span>
                        '.$ambos.'
                    </b>
                </h6>
                <h6 class="row d-none">
                    <b class="col-md-6">
                        <span class="text-muted">VISCERAS RESTANTES:</span>
                        '.$visceras_restante.'
                    </b>
                    <b class="col-md-6">
                        <span class="text-muted ">RES. SOLO HEMBRAS:</span>
                        '.$restante_s_hembras.'
                        <span class="text-muted ml-3">RES. SOLO MACHOS:</span>
                        '.$restante_s_machos.'
                        <span class="text-muted ml-3">RES. AMBOS:</span>
                        '.$restante_s_ambos.'
                    </b>
                </h6>
                <hr>
                <div id="cont-data-info">'.f_data_proceso_1($select,$dbConn,$mensaje_viceras).'</div>
            </div>
        </div>';
    }else return 'ERROR-828212';
}
function get_data_decomiso_parcial($dbConn,$orden){
    $cont=0;
    $consulta="SELECT o.ordNumOrden ,c.cliNombres, c.cliMarca, o.gprId,o.ordCantidad,o.ordId, o.ordTipo 
    FROM tbl_p_orden o, tbl_r_guiaproceso p, tbl_a_clientes c  
    WHERE o.gprId = p.gprId AND p.cliId = c.cliId  AND o.ordId = :id";
    $sql= $dbConn->prepare($consulta);
    $sql->bindValue(':id',$orden);
    $sql->execute();
    if($row = $sql->fetch()) {
        $procesado = get_data_decomisados($dbConn,$row["ordId"],$row["gprId"]);
        $saldo = intval($row["ordCantidad"]) - $procesado;
        $select =  select_data_subproductos_parcial($dbConn);
        
        $ArrayVisceras = get_data_visceras($dbConn,$row["gprId"]);//Parte ,Sexo
        $viseras = 0;
        $hembra = get_data_decomisados_2($dbConn,$row["ordId"],0);
        $macho = get_data_decomisados_2($dbConn,$row["ordId"],1);
        $resutl = get_data_antemortem($dbConn,$row["gprId"]);//Hembra, Macho
        $ante = [0,0];
        if ($row["ordTipo"]== 0) {
            $ante = [$resutl[0],$resutl[1]];
        }else if ($row["ordTipo"]== 1) {
            $ante = [$resutl[2],$resutl[3]];
        }else{
            return '<b>Se encontro un error en el estado de la orden</b>';
        }
        $r_hembra =  $ante[0] - $hembra;
        $r_macho =  $ante[1] - $macho;
        $s_hemnras = 0;
        $s_machos = 0;
        $ambos = 0;
        for ($i = 0; $i < count($ArrayVisceras) ; $i++) { 
            if ($ArrayVisceras[$i][1]==0){
                $ambos = (($ante[0] + $ante[1]) * $ArrayVisceras[$i][0]);
                $viseras += (($ante[0] + $ante[1]) * $ArrayVisceras[$i][0]);
            }else if ($ArrayVisceras[$i][1]==1){
                $s_hemnras = ($ante[0] * $ArrayVisceras[$i][0]);
                $viseras += ($ante[0] * $ArrayVisceras[$i][0]);
            }else if ($ArrayVisceras[$i][1]==2){
                $viseras += ($ante[1] * $ArrayVisceras[$i][0]);
                $s_machos = ($ante[1] * $ArrayVisceras[$i][0]);
            }
        }
        $v_procesadas = get_data_visceras_decomisadas($dbConn,$row["ordId"]);
        $visceras_procesadas = $v_procesadas[0] + $v_procesadas[1] + $v_procesadas[2];
        $visceras_restante = $viseras - $visceras_procesadas;
        $restante_s_hembras = $s_hemnras - $v_procesadas[1];
        $restante_s_machos = $s_machos - $v_procesadas[2];
        $restante_s_ambos =  $ambos  - $v_procesadas[0];
        $ruta = '';
        if ($row["ordTipo"]==0) $ruta = 'orden';
        else if ($row["ordTipo"]==1) $ruta = 'emergente';
        return '
        <div class="card">
            <div class="card-body">
                <button class="btn btn-danger float-right" onclick="f_cancelar_1()" ><b>CANCELAR DECOMISO</b></button>
                <h4 class="text-muted text-center"><b>DECOMISO PARCIAL</b></h4>
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
                </h6>
                <h6 class="row">
                    <b class="col-md-6">
                        <span class="text-muted">GANADO TOTAL:</span>
                        '.$row["ordCantidad"].'
                    </b>
                    <b class="col-md-6">
                        <span class="text-muted ">MACHO:</span>
                        '.$ante[1].'
                        <span class="text-muted ml-3">HEMBRA:</span>
                        '.$ante[0].'
                    </b>
                </h6>
                <h6 class="row">
                    <b class="col-md-6">
                        <span class="text-muted">GANDO RESTANTE:</span>
                        '.$saldo.'
                    </b>
                    <b class="col-md-6">
                    <span class="text-muted ">MACHO RESTANTE:</span>
                        '.$r_macho.'
                        <span class="text-muted ml-3">HEMBRA RESTANTE:</span>
                        '.$r_hembra.'
                    </b>
                </h6>
                <hr>
                <div id="cont-data-info">
                    <div class="row ">
                        <div class="col-md-6">
                            <select class="form-control form-control-lg  mt-2" id="slcProducto"
                                style="cursor:pointer">
                                '.$select.'
                            </select>
                        </div>
                        <div class="col-md-6">
                            <div class="input-group input-group-lg mt-2">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        <b>
                                            <span class="text-muted">CANTIDAD: </span>
                                        </b>
                                    </span>
                                </div>
                                <input type="text" class="form-control text-center input_disablecopypaste"
                                    id="txtCantidad" onkeypress="f_restrincion(event)" maxlength="4"
                                    style="font-size:30px;" value="1" placeholder="1">
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-md-6">
                            <textarea id="txtCausa" class="form-control form-control-lg" cols="2"
                                placeholder="Causa"></textarea>
                        </div>
                        <div class="col-md-6">
                            <div class="row">
                                <div class="form-group clearfix col-6 mt-1">
                                    <div class="icheck-success d-inline">
                                        <input type="radio"  value="0" id="cbxHembra" checked name="radio">
                                        <label for="cbxHembra">HEMBRA</label>
                                    </div>
                                </div>
                                <div class="form-group clearfix col-6 mt-1">
                                    <div class="icheck-success d-inline">
                                        <input type="radio"  id="cbxMacho"  name="radio" value="1">
                                        <label for="cbxMacho">MACHO</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row ">
                        <div class="col-12 mt-3">
                        <center>
                            <button class="btn btn-info bnt-lg pl-5 pr-5 " onclick="f_guardar_parcial()" >
                                <b>GUARDAR DECOMISO PARCIAL</b>
                            </button>
                        </center>
                        </div>
                    </div>
                </div>
            </div>
        </div>';
    }else return 'ERROR-828212';
}
function get_data_decomiso_subprodcutos($dbConn,$orden){
    $cont=0;
    $consulta="SELECT o.ordNumOrden ,c.cliNombres, c.cliMarca, o.gprId,o.ordCantidad,o.ordId,o.ordTipo 
    FROM tbl_p_orden o, tbl_r_guiaproceso p, tbl_a_clientes c  
    WHERE o.gprId = p.gprId AND p.cliId = c.cliId  AND o.ordId = :id";
    $sql= $dbConn->prepare($consulta);
    $sql->bindValue(':id',$orden);
    $sql->execute();
    if($row = $sql->fetch()) {
        $procesado = get_data_decomisados($dbConn,$row["ordId"],$row["gprId"]);
        $saldo = intval($row["ordCantidad"]) - $procesado;
        $select = select_data_subproductos($dbConn);
        $ArrayVisceras = get_data_visceras($dbConn,$row["gprId"]);//Parte ,Sexo
        $viseras = 0;
        $hembra = get_data_decomisados_2($dbConn,$row["ordId"],0);
        $macho = get_data_decomisados_2($dbConn,$row["ordId"],1);
        $resutl = get_data_antemortem($dbConn,$row["gprId"]);//Hembra, Macho
        $ante = [0,0];
        if ($row["ordTipo"]== 0) {
            $ante = [$resutl[0],$resutl[1]];
        }else if ($row["ordTipo"]== 1) {
            $ante = [$resutl[2],$resutl[3]];
        }else{
            return '<b>Se encontro un error en el estado de la orden</b>';
        }
        $r_hembra =  $ante[0] - $hembra;
        $r_macho =  $ante[1] - $macho;
        $s_hemnras = 0;
        $s_machos = 0;
        $ambos = 0;
        for ($i = 0; $i < count($ArrayVisceras) ; $i++) { 
            if ($ArrayVisceras[$i][1]==0){
                $ambos += (($ante[0] + $ante[1]) * $ArrayVisceras[$i][0]);
                $viseras += (($ante[0] + $ante[1]) * $ArrayVisceras[$i][0]);
            }else if ($ArrayVisceras[$i][1]==1){
                $s_hemnras += ($ante[0] * $ArrayVisceras[$i][0]);
                $viseras += ($ante[0] * $ArrayVisceras[$i][0]);
            }else if ($ArrayVisceras[$i][1]==2){
                $viseras += ($ante[1] * $ArrayVisceras[$i][0]);
                $s_machos += ($ante[1] * $ArrayVisceras[$i][0]);
            }
        }
        $v_procesadas = get_data_visceras_decomisadas($dbConn,$row["ordId"]);
        $visceras_procesadas = $v_procesadas[0] + $v_procesadas[1] + $v_procesadas[2];
        $visceras_restante = $viseras - $visceras_procesadas;
        $restante_s_hembras = $s_hemnras - $v_procesadas[1];
        $restante_s_machos = $s_machos - $v_procesadas[2];
        $restante_s_ambos =  $ambos  - $v_procesadas[0];
        $_SESSION['DATOSDECOMISO'][1]=  $saldo;
        $ruta = '';
        if ($row["ordTipo"]==0) $ruta = 'orden';
        else if ($row["ordTipo"]==1) $ruta = 'emergente';
        return '
        <div class="card">
            <div class="card-body">
                <button class="btn btn-danger float-right" onclick="f_cancelar_1()" ><b>CANCELAR DECOMISO</b></button>
                <h4 class="text-muted text-center"><b>DECOMISO DE SUBPRODUCTOS</b></h4>
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
                </h6>
                <h6 class="row">
                    <b class="col-md-6">
                        <span class="text-muted">TOTAL DE VISCERAS :</span>
                        '.$viseras .'
                    </b>
                    <b class="col-md-6">
                        <span class="text-muted ">SOLO HEMBRAS:</span>
                        '.$s_hemnras.'
                        <span class="text-muted ml-3">SOLO MACHOS:</span>
                        '.$s_machos.'
                        <span class="text-muted ml-3">AMBOS:</span>
                        '.$ambos.'
                    </b>
                </h6>
                <h6 class="row">
                    <b class="col-md-6">
                        <span class="text-muted">VISCERAS RESTANTES:</span>
                        '.$visceras_restante.'
                    </b>
                    <b class="col-md-6">
                        <span class="text-muted ">RES. SOLO HEMBRAS:</span>
                        '.$restante_s_hembras.'
                        <span class="text-muted ml-3">RES. SOLO MACHOS:</span>
                        '.$restante_s_machos.'
                        <span class="text-muted ml-3">RES. AMBOS:</span>
                        '.$restante_s_ambos.'
                    </b>
                </h6>
                <hr>
                <div id="cont-data-info">
                    <div class="row">
                        <div class="col-lg-6">
                                <div class="card card-light ">
                                    <div class="card-header border-transparent" data-card-widget="collapse"
                                        style="cursor:pointer;">
                                        <h3 class="card-title text-muted"><b>Tabla de Decomisos</b></h3>
                                    </div>
                                    <div class="card-body p-0" >
                                        <center>
                                        <button class="btn btn-warning mt-2 mb-3" data-toggle="modal"
                                        data-target="#modal2" onclick="f_nuevo_decomiso()" >
                                            <b>AÑADIR</b>
                                        </button>
                                        </center>
                                        <div class="table-responsive" id="cont-table-decomisos">
                                        '.table_decomisos($dbConn).'
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <div class="col-lg-6">
                            <div class="card card-light ">
                                <div class="card-header border-transparent" data-card-widget="collapse"
                                    style="cursor:pointer;">
                                    <h3 class="card-title text-muted"><b>Tabla de Enfermedades</b></h3>
                                </div>
                                <div class="card-body p-0" >
                                    <center>
                                        <button class="btn btn-danger mt-2 mb-3" data-toggle="modal"
                                            data-target="#modal2" onclick="f_nueva_enfermedad()">
                                            <b>AÑADIR</b>
                                        </button>
                                    </center>
                                    <div class="table-responsive" id="cont-table-enfermedades">
                                        '.table_enfermedades($dbConn).'
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                    </div>
                    <div class="row ">
                        <div class="col-12 mt-3">
                        <center>
                            <button class="btn btn-info bnt-lg pl-5 pr-5 " onclick="f_guardar_decomiso_suprodcutos()" >
                                <b>GUARDAR DECOMISO</b>
                            </button>
                        </center>
                        </div>
                    </div>
                </div>
            </div>
        </div>';
    }else return 'ERROR-828212';
}

function get_data_orden($dbConn){
    $Id = $_POST["Id"];
    $consulta="SELECT * FROM tbl_p_orden o, tbl_r_guiaproceso p, tbl_a_clientes c
    WHERE o.gprId is not null AND o.gprId = p.gprId AND p.cliId = c.cliId   AND o.ordId = :id
    ORDER BY o.ordId ASC";//AND o.ordFechaTurno BETWEEN :inicio AND :fin
    $sql= $dbConn->prepare($consulta);
    $sql->bindValue(':id',$Id);
    // $sql->bindValue(':inicio',date("Y-m-d")." 00:00:00");
    // $sql->bindValue(':fin',date("Y-m-d")." 23:59:59");
    $sql->execute();
    $cont = 0;
    if($row = $sql->fetch()) {
        if ($row["ordTipo"]==0) {
            $bandera = f_buscar_orden($dbConn,$row["ordFecha"],$row["gprId"]);
        }else{
            $bandera = f_buscar_orden_1($dbConn,$row["ordFecha"],$row["gprId"]);
        }

        if ($bandera==0) {
            $procesado = get_data_decomisados($dbConn,$row["ordId"],$row["gprId"]);
            $saldo = intval($row["ordCantidad"]) - $procesado;
            if ($saldo == 0) {
                return 'No se encontro resultados para generar los decomisos '.$saldo;
            }else{
                return '<h4 class="text-center text-muted">
                        <b>SELECCIONE UN TIPO DE DECOMISO </b>
                    </h4>
                    <hr>
                    <div class="row">
                        <div class="col-4">
                            <button type="button" onclick="f_get_decomiso(1,'.$row["ordId"].')" class="btn btn-success btn-block"><b>TOTAL</b></button>
                        </div>
                        <div class="col-4">
                            <button type="button" onclick="f_get_decomiso(2,'.$row["ordId"].')" class="btn btn-info btn-block"><b>PARCIAL</b></button>
                        </div>
                        <div class="col-4">
                            <button type="button"  onclick="f_get_decomiso(3,'.$row["ordId"].')" class="btn btn-warning btn-block"><b>SUBPRODUCTOS</b></button>
                        </div>
                    </div>
                    <button type="button" id="btn-cerrar" class="btn btn-secondary d-none" data-dismiss="modal">
                        <b>CANCELAR</b>
                    </button>';
            }
        }else{
            return 'Se acaba de generar otra orden de produccion para esta guía de proceso ';
        }
    }else return 'ERROR-98219222';
}
function f_data_proceso_1($select,$dbConn,$mensaje_viceras){
    if ($_SESSION['DATOSDECOMISO'][0]==0) {//Producto, Cantidad,Causa,Hembra,Macho
        $hembra = "";
        $macho = "";
        if ($_SESSION['DATOSDECOMISO'][3] == 0) $hembra = "checked";
        if ($_SESSION['DATOSDECOMISO'][3] == 1) $macho = "checked";
        return '
        <div class="row ">
            <div class="col-md-6">
                <select class="form-control form-control-lg  mt-2" id="slcProducto"
                    style="cursor:pointer">
                    '.$select.'
                </select>
            </div>
            <div class="col-md-6">
                <div class="input-group input-group-lg mt-2">
                    <div class="input-group-prepend">
                        <span class="input-group-text">
                            <b>
                                <span class="text-muted">CANTIDAD: </span>
                            </b>
                        </span>
                    </div>
                    <input type="text" class="form-control text-center input_disablecopypaste"
                        id="txtCantidad" onkeypress="f_restrincion(event)" maxlength="4"
                        style="font-size:30px;" value="'.$_SESSION['DATOSDECOMISO'][1].'" placeholder="1">
                </div>
            </div>
        </div>
        <hr>
        <div class="row">
            <div class="col-md-6">
                <textarea id="txtCausa" class="form-control form-control-lg" cols="2"
                    placeholder="Causa">'.$_SESSION['DATOSDECOMISO'][2].'</textarea>
            </div>
            <div class="col-md-6">
                <div class="row">
                    <div class="form-group clearfix col-6 mt-1">
                        <div class="icheck-success d-inline">
                            <input type="radio"  value="0" id="cbxHembra" '.$hembra .' name="radio">
                            <label for="cbxHembra">HEMBRA</label>
                        </div>
                    </div>
                    <div class="form-group clearfix col-6 mt-1">
                        <div class="icheck-success d-inline">
                            <input type="radio"  id="cbxMacho" '.$macho.' name="radio" value="1">
                            <label for="cbxMacho">MACHO</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row ">
            <div class="col-12 mt-3">
            <center>
                <button class="btn btn-info bnt-lg pl-5 pr-5 " onclick="f_siguiente()" >
                    <b>SIGUIENTE</b>
                </button>
            </center>
            </div>
        </div>';
    }else{
        $producto =  get_name_producto($dbConn,$_SESSION['DATOSDECOMISO'][0]);
        $tipo = "ERROR-21";
        if ($_SESSION['DATOSDECOMISO'][3]==0) $tipo = "HEMBRA";
        else if ($_SESSION['DATOSDECOMISO'][3]==1) $tipo = "MACHO";
        $IdGuia = f_get_id_guia($dbConn,$_SESSION['ORDEN']);
        $tipo = f_get_tipo_guia($dbConn,$_SESSION['ORDEN']);
        $resutl = get_data_antemortem($dbConn,$IdGuia);//Hembra, Macho
        $ante = [0,0];
        if ($tipo== 0) {
            $ante = [$resutl[0],$resutl[1]];
        }else if ($tipo== 1) {
            $ante = [$resutl[2],$resutl[3]];
        }else{
            return '<b>Se encontro un error en el estado de la orden</b>';
        }
        $ArrayVisceras = get_data_visceras($dbConn,$IdGuia);//Parte ,Sexo
        $viseras = 0;
        for ($i = 0; $i < count($ArrayVisceras) ; $i++) { 
            if ($_SESSION['DATOSDECOMISO'][3] == 0) {
                if ($ArrayVisceras[$i][1]==0 ||  $ArrayVisceras[$i][1]== 1  ){
                    $viseras +=  ($ante[0] * $ArrayVisceras[$i][0]);
                }
            }else if ($_SESSION['DATOSDECOMISO'][3] == 1){
                if ($ArrayVisceras[$i][1]==0 ||  $ArrayVisceras[$i][1]== 2  ){
                    $viseras +=  ($ante[1] * $ArrayVisceras[$i][0]);
                }
            }
        }
        completar_decomisos_automa($dbConn);
        $bnt_ayadir = "";
        if ($_SESSION['DECOMISO'][1] != 1){
            $bnt_ayadir = '<button class="btn btn-warning mt-2 mb-3" data-toggle="modal"
            data-target="#modal2" onclick="f_nuevo_decomiso()" >
                <b>AÑADIR</b>
            </button>';
        }
        return '
        <h5 class="text-muted row">
            <b class="col-6">DATOS DEL DECOMISO</b>
            <span class="col-6">
                <button class="btn btn-warning float-right" onclick="f_cancelar_2()" ><b>REGRESAR</b></button>
            </span>
        </h5>
        <h6 class="row">
            <b class="col-md-6">
                <span class="text-muted">PRODUCTO SELECCIONADO:</span>
                '.utf8_encode($producto).'
            </b>
            <b class="col-md-6">
                <span class="text-muted">CANTIDAD SELECCIONADO:</span>
                '.$_SESSION['DATOSDECOMISO'][1].'
            </b>
        </h6>
        <h6 class="row">
            <b class="col-md-6">
                <span class="text-muted">TIPO:</span>
                '.$tipo .'
            </b>
            <b class="col-md-6">
                <span class="text-muted">CAUSA:</span>
                '.$_SESSION['DATOSDECOMISO'][2].'
            </b>
        </h6>
        <hr>
        <div class="row">
                <div class="col-lg-6">
                    <div class="card card-light ">
                        <div class="card-header border-transparent" data-card-widget="collapse"
                            style="cursor:pointer;">
                            <h3 class="card-title text-muted"><b>Tabla de Enfermedades</b></h3>
                        </div>
                        <div class="card-body p-0" >
                            <center>
                                <button class="btn btn-danger mt-2 mb-3" data-toggle="modal"
                                    data-target="#modal2" onclick="f_nueva_enfermedad()">
                                    <b>AÑADIR</b>
                                </button>
                            </center>
                            <div class="table-responsive" id="cont-table-enfermedades">
                                '.table_enfermedades($dbConn).'
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="card card-light ">
                        <div class="card-header border-transparent" data-card-widget="collapse"
                            style="cursor:pointer;">
                            <h3 class="card-title text-muted"><b>Tabla de Decomisos</b></h3>
                        </div>
                        <div class="card-body p-0" >
                            <center>
                            '.$bnt_ayadir.'
                            </center>
                            <div class="table-responsive" id="cont-table-decomisos">
                            '.table_decomisos($dbConn).'
                            </div>
                        </div>
                    </div>
                </div>
        </div>
        <div class="row ">
            <div class="col-12 mt-3">
            <center>
                <button class="btn btn-info bnt-lg pl-5 pr-5 " onclick="f_guardar_decomiso()" >
                    <b>GUARDAR DECOMISO</b>
                </button>
            </center>
            </div>
        </div>';
    }

}
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
function f_insert_acta($dbConn){
    try {
        global $User;
        global $Ip;
        $consulta = "INSERT INTO tbl_p_acta(actFecha,usuId,ip) VALUES (:actFecha,:usuId,:ip)";
		$sql= $dbConn->prepare($consulta);
        $sql->bindValue(':actFecha',date("Y-m-d H:i:s"));
        $sql->bindValue(':usuId',$User);
        $sql->bindValue(':ip',$Ip);
		if ($sql->execute()){
            $Id = $dbConn->lastInsertId();
            $resul = f_while_acta($dbConn,$Id);
            if ($resul){
                $_SESSION['OPCION'] = 4;
                $_SESSION['VARIABLE'] = $Id;
                Insert_Login($Id,'tbl_p_acta','Acta de decomiso','Acta Nro.'.$Id,'');
                return true;
            }else return $resul;
        }else return 'ERROR-21212';
	}  catch (Exception $e) {
		Insert_Error('ERROR-21212',$e->getMessage(),'Error al ingresar al registrar EL ACTA');
		exit("ERROR-21212");
	}
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
