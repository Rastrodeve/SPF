<?php
if (isset($_REQUEST["op"])) {
	require '../FilePHP/menu.php';
	$dbConn = conectar($db);
	$op=$_REQUEST['op'];
	if ($op==1)echo view_info_user($dbConn);
	else if ($op==2)echo data_password();
	else if ($op==3)echo data_user($dbConn);
	else if ($op==4)echo update_user_data($dbConn);
	else if ($op==5)echo update_user_pass($dbConn);
	else if ($op==6)echo list_actividad();
	else if ($op==7)echo list_solicitudes($dbConn);
	else if ($op==8)echo return_data_solicitud($dbConn);
	else if ($op==9)echo f_eliminar_solicitud($dbConn);
	else if ($op==10)echo f_respuesta_solicitud($dbConn);
	else if ($op==11){
        // $_SESSION['OPCION'] = 2;
        // $_SESSION['VARIABLE'] = 'EMRAQ-27022B001';
        // $_SESSION['VARIABLE2'] = '1';
        // $_SESSION['INICIO'] = date("Y-m-d");
        // $_SESSION['FINAL'] = date("Y-m-d");
    }
}
function view_info_user($dbConn){
    $info = get_info_user($dbConn,$_SESSION['MM_Username']);
    return' 
            <div class="text-center" style="min-heigh:100px;">
                <img class="profile-user-img img-fluid img-circle" src="../recursos/user-rastro.png" alt="User profile picture" style="min-heigh:100px;">
            </div>
            <h3 class="profile-username text-center">'.$info[0].'</h3>
            <p class="text-muted text-center">
                '.$info[2].' - '.$info[1].'
            </p>
            <p class="text-muted text-center">
                <a  class="btn bg-navy" data-toggle="modal" data-target="#modal" onclick="data_password()" ><b>Cambiar contraseña</b></a>
                <a  class="btn bg-navy" data-toggle="modal" data-target="#modal" onclick="data_update_user()" ><b>Editar usuario</b></a>
            </p>
            <p class="text-muted text-center">
                <a  class="btn bg-danger btn-block" href="../FilePHP/cerrar.php" ><b>Cerrar Sesión</b></a>
            </p>
            <button class="btn btn-info d-none" onclick="f_prueba()"><b>GENERAR</b></button>';
}
function data_password(){
    $data = '
    <div class="row">
        <div class="col-md-8">
            <label for="txtpassold-1">Contraseña actual</label>
            <div class="input-group input-group-sm">
                <input type="password" class="form-control" id="txtpassold-1" placeholder="Contraseña">
                <span class="input-group-append">
                    <button type="button" class="btn btn-secondary" onclick="mostrar_contrasenia(\'txtpassold-1\',\'icon1\')">
                        <i id="icon1" class="fas fa-eye"></i>
                    </button>
                </span>
            </div>
        </div>
    </div>
    <hr>
    <div class="row">
        <div class="col-md-8">
            <label for="txtpass">Nueva contraseña:</label>
            <div class="input-group input-group-sm">
                <input type="password" class="form-control" id="txtpass" placeholder="Nueva contraseña">
                <span class="input-group-append">
                    <button type="button" class="btn btn-secondary" onclick="mostrar_contrasenia(\'txtpass\',\'icon2\')">
                        <i id="icon2" class="fas fa-eye"></i>
                    </button>
                </span>
            </div>
        </div>
    </div>
    <div class="row mt-2">
        <div class="col-md-8">
            <label for="txtpassconfi">Repita la nueva contraseña:</label>
            <div class="input-group input-group-sm">
                <input type="password" class="form-control" id="txtpassconfi"
                    placeholder="Repita la nueva contraseña">
                <span class="input-group-append">
                    <button type="button" class="btn btn-secondary" onclick="mostrar_contrasenia(\'txtpassconfi\',\'icon3\')">
                        <i id="icon3" class="fas fa-eye"></i>
                    </button>
                </span>
            </div>
        </div>
    </div>';
    return return_modal('Actualización de contraseña',$data,'update_pass()');
}
function data_user($dbConn){
    $info = get_info_user($dbConn,$_SESSION['MM_Username']);
    $data = '  <div class="row">
                <div class="col-md-12">
                    <label for="txtUser_update">*Nombre de usuario:</label>
                    <input type="text" id="txtUser_update" class="form-control form-control-sm" value="'.$info[0].'" >
                </div>
            </div>
            <hr>
            <div class="row">
                <div class="col-md-12">
                    <label for="txtCargo_update">*Cargo en la empresa:</label>
                    <input type="text" id="txtCargo_update" class="form-control form-control-sm" value="'.$info[1].'" >
                </div>
            </div>';
    return return_modal('Actualización de datos del usuario',$data,'update_user()');
}

function return_modal($titulo,$data,$funtion){
    return '
    <div class="modal-header bg-navy">
        <h5 class="modal-title" id="exampleModalLabel">'.$titulo.'</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    <div class="modal-body text-sm">'.$data.'</div>
    <div class="modal-footer">
        <button type="button" id="btnCerrar"  class="btn btn-light" data-dismiss="modal"><b>CERRAR</b></button>
        <button type="button" class="btn btn-primary" onclick="'.$funtion.'">
            <b>GUARDAR</b>
        </button>
    </div>';
}
function update_user_data($dbConn){
    try {
        $cedula = $_SESSION["MM_Username"];
        $nombre = trim($_POST["Nombre"]);
        $cargo = trim($_POST["Cargo"]);
        $consulta="SELECT usuNombre,usuCargo FROM tbl_a_usuarios  WHERE usuId = :id";
        $sql= $dbConn->prepare($consulta);
        $sql->bindValue(':id',trim($cedula));
        $sql->execute();
        if($row = $sql->fetch()){
            $consulta1="UPDATE tbl_a_usuarios SET usuNombre=:nombre, usuCargo = :cargo WHERE usuId = :id";
            $sql1= $dbConn->prepare($consulta1);
            $sql1->bindValue(':nombre', utf8_decode($nombre));
            $sql1->bindValue(':cargo',utf8_decode($cargo));
            $sql1->bindValue(':id',$cedula);
            if ($sql1->execute()){
                $Acion = "Actualización de información personal";
                $detalle = "<b>Mi información</b><br>[".utf8_encode($row["usuNombre"]).' => '.$nombre.']<br>['. utf8_encode($row["usuCargo"]).' => '.$cargo.']';
                if(Insert_Login($cedula,'tbl_a_usuarios',$Acion,$detalle,'')) return true;
                else return "ERROR-654552";//ERROR AL AGREGAR EL LOGS
            }else return "ERROR-0099921";//NO SE PUEDDO ACTUALIZAR EL USUARIO    
        }else return 'ERROR-178712';//NO SE ENCONTRO AL USUARIO
    }  catch (Exception $e) {
        Insert_Error('ERROR-156266',$e->getMessage(),'Actualizar mi información personal'); 
        exit("ERROR-156266");
    }
}
function update_user_pass($dbConn){
    try {
        $password = trim($_POST["Pass"]);
        $passwordOld= trim($_POST["Passold"]);
        $cedula = $_SESSION["MM_Username"];
        $consulta="SELECT usuPasswd FROM tbl_a_usuarios  WHERE usuId = :id";
        $sql= $dbConn->prepare($consulta);
        $sql->bindValue(':id',trim($cedula));
        $sql->execute();
        if($row = $sql->fetch()){
            if ($row["usuPasswd"] == md5($passwordOld)) {
                $consulta1="UPDATE tbl_a_usuarios SET 	usuPasswd = :pass WHERE usuId = :id";
                $sql1= $dbConn->prepare($consulta1);
                $sql1->bindValue(':pass',md5($password));
                $sql1->bindValue(':id',$cedula);
                if ($sql1->execute()){
                    $Acion = 'Cambio de contraseña';
                    $detalle ='Cambio de contraseña personal';
                    if(Insert_Login($cedula,'tbl_a_usuarios',$Acion,$detalle,''))return true;
                    else return "ERROR-654552";//ERROR AL AGREGAR EL LOGS
                }else return "ERROR-0099921";//NO SE PUEDDO ACTUALIZAR LA CONTRASEÑA
            }else{
                $Acion = 'Cambio de contraseña';
                $detalle ='La contraseña acutal es incorrecta para proceder con el cambio';
                if(Insert_Login($cedula,'tbl_a_usuarios',$Acion,$detalle,''))return '<b>La contraseña actual no es correcta</b>';
                else return "ERROR-654552";//ERROR AL AGREGAR EL LOGS
            } 
        }else return 'ERROR-178712';//NO SE ENCONTRO AL USUARIO
    }catch (Exception $e) {
        Insert_Error('ERROR-52331',$e->getMessage(),'Cambio de contraseña personal'); 
        exit("ERROR-52331");
    }
}

function list_actividad(){
    global $dblLOGView;
    $dbConn = conectar($dblLOGView);
    $resultado='<table id="tbl_view_log" class="table table-sm table-bordered table-striped text-center">
    <thead style="font-size:15px;">
        <tr>
            <th class="d-none">#</th>
            <th>Fecha</th>
            <th>Acción</th>
            <th>Detalle</th>
        </tr>
    </thead>
    <tbody>';
    $consulta="SELECT * FROM tbl_log WHERE usuId=:id ORDER BY logFecha DESC";
    $sql= $dbConn->prepare($consulta);
    $sql->bindValue(':id',$_SESSION["MM_Username"]);
    $sql->execute();
    $cont = 0;
    while($row = $sql->fetch()) {
        $cont++;
        $resultado .='
            <tr>
                <td class="d-none">'.$cont.'</td>
                <td>'.$row["logFecha"].'</td>
                <td>'.utf8_encode($row["logAccion"]).'</td>
                <td>'.utf8_encode($row["logDetalle"]).'</td>
            </tr>  
        ';
    }
    return $resultado;
}

function list_solicitudes($dbConn){
    $resultado='<table id="tbl_view_solici" class="table table-sm table-bordered table-striped text-center">
    <thead style="font-size:15px;">
        <tr>
            <th >#</th>
            <th>Fecha</th>
            <th >Solicitud</th>
            <th >Solicitante</th>
            <th colspan="2">Autorizado 1</th>
            <th colspan="2">Autorizado 2</th>
            <th class="d-none">ACCIONES</th>
        </tr>
    </thead>
    <tbody>';
    $consulta="SELECT * FROM tbl_s_solicitudes WHERE sltIdSolicitante = :id OR sltIdAprobado1 = :id1 OR sltIdAprobado2 = :id2 ORDER BY sltFecha DESC LIMIT 50 ";
    $sql= $dbConn->prepare($consulta);
    $sql->bindValue(':id',$_SESSION["MM_Username"]);
    $sql->bindValue(':id1',$_SESSION["MM_Username"]);
    $sql->bindValue(':id2',$_SESSION["MM_Username"]);
    $sql->execute();
    $cont = 0;
    while($row = $sql->fetch()) {
        if ($row["sltEliminado"] == 0) {
            if ($row["sltIdSolicitante"] == $_SESSION["MM_Username"] && $row["sltIdAprobado1"] == $_SESSION["MM_Username"] && $row["sltIdAprobado2"] == $_SESSION["MM_Username"]) ;
            else{
                $cont++;
                $acciones = '';
                $estado1 = 'ERROR-15555';
                $estado2 = 'ERROR-15555';
                if ($row["sltIdSolicitante"] == $_SESSION["MM_Username"]) { //data-toggle="modal" data-target="#Modal"
                    if (is_null($row["sltEstado1"]))$estado1 = '<span style="color:#979a9a ; "><i>ESPERA</i></span> ';
                    else if ($row["sltEstado1"] == 0)$estado1 = '<span style="color: #c0392b; "><i>NEGADO</i></span> ';
                    else if ($row["sltEstado1"] == 1)$estado1 = '<span style="color: #27ae60; "><i>APROBADO</i></span> ';
                    if (is_null($row["sltEstado2"]))$estado2 = '<span style="color:#979a9a ; "><i>ESPERA</i></span> ';
                    else if ($row["sltEstado2"] == 0)$estado2 = '<span style="color: #c0392b; "><i>NEGADO</i></span> ';
                    else if ($row["sltEstado2"] == 1)$estado2 = '<span style="color: #27ae60; "><i>APROBADO</i></span> ';
                }
                
                if ($row["sltIdAprobado1"] == $_SESSION["MM_Username"]) { //
                    if (is_null($row["sltEstado1"]))$acciones = '';    
                    if (is_null($row["sltEstado1"]))$estado1 = '<a href="#" data-toggle="modal" data-target="#Modal" onclick="data_solicutud('.$row["sltId"].')"  >APROBAR</a>';
                    else if ($row["sltEstado1"] == 0)$estado1 = '<span style="color: #c0392b; "><i>NEGADO</i></span> ';
                    else if ($row["sltEstado1"] == 1)$estado1 = '<span style="color: #27ae60; "><i>APROBADO</i></span> ';

                    if (is_null($row["sltEstado2"]))$estado2 = '<span style="color:#979a9a ; "><i>ESPERA</i></span> ';
                    else if ($row["sltEstado2"] == 0)$estado2 = '<span style="color: #c0392b; "><i>NEGADO</i></span> ';
                    else if ($row["sltEstado2"] == 1)$estado2 = '<span style="color: #27ae60; "><i>APROBADO</i></span> ';
                }
                
                if ($row["sltIdAprobado2"] == $_SESSION["MM_Username"]) { //data-toggle="modal" data-target="#Modal"
                    if (is_null($row["sltEstado2"]))$acciones = '';
                    if (is_null($row["sltEstado2"]) && $row["sltEstado1"] == 1 )$estado2 = '<a href="#" data-toggle="modal" data-target="#Modal" onclick="data_solicutud('.$row["sltId"].')"  >APROBAR</a>';
                    else if (is_null($row["sltEstado2"]) && is_null($row["sltEstado1"]))$estado2 = '<span style="color:#979a9a ; "><i>ESPERA</i></span> ';
                    else if ($row["sltEstado2"] == 0)$estado2 = '<span style="color: #c0392b; "><i>NEGADO</i></span> ';
                    else if ($row["sltEstado2"] == 1)$estado2 = '<span style="color: #27ae60; "><i>APROBADO</i></span> ';
                    
                    if (is_null($row["sltEstado1"]))$estado1 = '<span style="color:#979a9a ; "><i>ESPERA</i></span> ';
                    else if ($row["sltEstado1"] == 0)$estado1 = '<span style="color: #c0392b; "><i>NEGADO</i></span> ';
                    else if ($row["sltEstado1"] == 1)$estado1 = '<span style="color: #27ae60; "><i>APROBADO</i></span> ';
                }
                $resultado .='
                    <tr>
                        <td  ><a href="#" data-toggle="modal" data-target="#Modal" onclick="data_solicutud('.$row["sltId"].')"   > '.$cont.'</a></td>
                        <td>'.$row["sltFecha"].'</td>
                        <td>'.utf8_encode($row["sltCabecera"]).'</td>
                        <td>'.get_name_user_soli($dbConn,$row["sltIdSolicitante"]).'</td>
                        <td>'.get_name_user_soli($dbConn,$row["sltIdAprobado1"]).'</td>
                        <td>'.$estado1.'</td>
                        <td>'.get_name_user_soli($dbConn,$row["sltIdAprobado2"]).'</td>
                        <td>'.$estado2.'</td>
                        <td class="d-none" ></td>
                    </tr>  
                ';
            }
        }
    }
    return "<button class='btn btn-info  btn-sm float-right' onclick='data_soli()'><b>RECARGAR</b></button>".$resultado;
}
function get_name_user_soli($dbConn,$Id){
    $consulta="SELECT usuNombre FROM tbl_a_usuarios WHERE usuId = :id";
	$sql= $dbConn->prepare($consulta);
    $sql->bindValue(':id',$Id);
	$sql->execute();
	if($row = $sql->fetch()) return utf8_encode($row['usuNombre']);
}
function return_data_solicitud($dbConn){
    $Id = $_POST["Id"];
    $consulta="SELECT * FROM tbl_s_solicitudes WHERE sltId = :id";
	$sql= $dbConn->prepare($consulta);
    $sql->bindValue(':id',$Id);
	$sql->execute();
	if($row = $sql->fetch()){
        $eliminar = '';
        $aprobar = '';
        if ($row["sltIdSolicitante"] == $_SESSION["MM_Username"] &&  is_null($row["sltEstado1"]) && is_null($row["sltEstado2"]) ) $eliminar = '<button class=" btn btn-danger" onclick="f_eliminar_solicitud('.$row["sltId"].')" ><b>ELIMINAR</b></button>';
        if ($row["sltIdAprobado1"] == $_SESSION["MM_Username"] &&  is_null($row["sltEstado1"])  ) $aprobar = '<button class=" btn btn-info" onclick="f_resul_solicitud('.$row["sltId"].',1)"  ><b>APROBAR</b></button><button class=" btn btn-danger ml-3"  onclick="f_resul_solicitud('.$row["sltId"].',0)" ><b>RECHAZAR</b></button>';
        if ($row["sltIdAprobado2"] == $_SESSION["MM_Username"] && is_null($row["sltEstado2"]) &&  $row["sltEstado1"] == 1 ) $aprobar = '<button class=" btn btn-info" onclick="f_resul_solicitud('.$row["sltId"].',1)"  ><b>APROBAR</b></button><button class=" btn btn-danger ml-3"  onclick="f_resul_solicitud('.$row["sltId"].',0)" ><b>RECHAZAR</b></button>';
        $fechaarray = explode(" ",$row["sltFecha"]);
        $array1 = get_info_user_soli_array($dbConn,$row["sltIdSolicitante"]);
        $array2 = get_info_user_soli_array($dbConn,$row["sltIdAprobado1"]);
        $array3 = get_info_user_soli_array($dbConn,$row["sltIdAprobado2"]);
        $estado1 = '';
        $estado2 = '';
        if (is_null($row["sltEstado1"])) $estado1 = '<i style="color:#454545" ><b>EN ESPERA</b></i>';
        else if ($row["sltEstado1"] == 0) $estado1 = '<i style="color:#FF0000" ><b>NEGADO</b></i>';
        else if ($row["sltEstado1"] == 1) $estado1 = '<i style="color:#0000FF" ><b>APROBADO</b></i>';

        if (is_null($row["sltEstado2"])) $estado2 = '<i style="color:#454545" ><b>EN ESPERA</b></i>';
        else if ($row["sltEstado2"] == 0) $estado2 = '<i style="color:#FF0000" ><b>NEGADO</b></i>';
        else if ($row["sltEstado2"] == 1) $estado2 = '<i style="color:#0000FF" ><b>APROBADO</b></i>';
        $imprimir = '';
        if (!is_null($row["sltEstado2"]) && !is_null($row["sltEstado1"])) $imprimir = '<button class=" btn btn-warning" onclick="f_print_solici('.$row["sltId"].')"  ><b>IMPRIMIR</b></button>';
        return '
        <h6>Fecha: <b>'.Transformar_Fecha($fechaarray[0]).'</b></h6>
        <table class="table table-bordered">
            <tr>
                <td colspan="3"><b>TIPO DE SOLICITUD: </b> '.utf8_encode($row["sltCabecera"]).'</td>
            </tr>
            <tr>
                <td colspan="3" > 
                    <b>DETALLE DE LA SOLICITUD</b><br>
                    <p class="text-justify">'.utf8_encode($row["sltCuerpo"]).'</p>
                </td>
            </tr>
            <tr>
                <td colspan="3" > 
                    <b>RAZÓN:</b> '.utf8_encode($row["sltError"]).'
                </td>
            </tr>
            <tr>
                <td>
                    Solicitante
                    <br>
                    <br>
                    <i style="color:#0000FF" ><b>RESPONSABLE</b></i>
                    <br>
                    '.$array1[0].'<br>'.$array1[1].'
                </td>
                <td>
                    Autorizado (1)
                    <br>
                    <br>
                    '.$estado1.'
                    <br>
                    '.$array2[0].'<br>'.$array2[1].'
                </td>
                <td>
                    Autorizado (2)
                    <br>
                    <br>
                    '.$estado2.'
                    <br>
                    '.$array3[0].'<br>'.$array3[1].'
                </td>
            </tr>
        </table>
        <button type="button" class="btn btn-danger d-none" data-dismiss="modal" id="btnCerrar" ><b>CANCELAR</b></button>
        <center>
            '.$aprobar.$eliminar.$imprimir.'
        </center>';
    }
}
function get_info_user_soli_array($dbConn,$Id){
    $consulta="SELECT usuNombre,usuCargo FROM tbl_a_usuarios WHERE usuId = :id";
	$sql= $dbConn->prepare($consulta);
    $sql->bindValue(':id',$Id);
	$sql->execute();
	if($row = $sql->fetch()) return [utf8_encode($row['usuNombre']), utf8_encode($row["usuCargo"])];
}

function Transformar_Fecha($fecha){
    $arrayDia = array('1' => 'Lunes',
                    '2' => 'Martes',
                    '3' => 'Miércoles',
                    '4' => 'Jueves',
                    '5' => 'Viernes',
                    '6' => 'Sábado',
                    '7' => 'Domingo');
    $numDia=date("N",strtotime($fecha));
    $arrayMes = array('1' => 'Enero',
                    '2' => 'Febrero',
                    '3' => 'Marzo',
                    '4' => 'Abril',
                    '5' => 'Mayo',
                    '6' => 'Junio',
                    '7' => 'Julio',
                    '8' => 'Agosto',
                    '9' => 'Septiembre',
                    '10' => 'Octubre',
                    '11' => 'Noviembre',
                    '12' => 'Diciembre',);
    $numMes=date("n",strtotime($fecha));
    return $arrayDia[$numDia].", ".date("d",strtotime($fecha))." de ".$arrayMes[$numMes]." de ".date("Y",strtotime($fecha));
}
function f_eliminar_solicitud($dbConn){
    $Id = $_POST["Id"];
    $consulta="SELECT * FROM tbl_s_solicitudes WHERE sltId = :id";
	$sql= $dbConn->prepare($consulta);
    $sql->bindValue(':id',$Id);
	$sql->execute();
	if($row = $sql->fetch()){
        if ($row["sltIdSolicitante"] == $_SESSION["MM_Username"] &&  is_null($row["sltEstado1"]) && is_null($row["sltEstado2"]) ){
            $consulta1="UPDATE tbl_s_solicitudes SET sltEliminado = 1 WHERE sltId = :id";
            $sql1= $dbConn->prepare($consulta1);
            $sql1->bindValue(':id',$row["sltId"]);
            if ($sql1->execute())return true;
            else return false;
        }else return 'ERROR-21827812';
    }
}
function f_respuesta_solicitud($dbConn){
    global $Ip;
    $Id = $_POST["Id"];
    $Estado = $_POST["Estado"];
    $consulta="SELECT * FROM tbl_s_solicitudes WHERE sltId = :id";
	$sql= $dbConn->prepare($consulta);
    $sql->bindValue(':id',$Id);
	$sql->execute();
	if($row = $sql->fetch()){
        if ($row["sltIdAprobado1"] == $_SESSION["MM_Username"] &&  is_null($row["sltEstado1"])) {
            if ($row["sltEliminado"]==0) {
                $consulta1="UPDATE tbl_s_solicitudes SET sltIp1 = :ip,sltFecha1 = :fecha, sltEstado1 = :estado  WHERE sltId = :id";
                $sql1= $dbConn->prepare($consulta1);
                $sql1->bindValue(':id',$row["sltId"]);
                $sql1->bindValue(':ip',$Ip);
                $sql1->bindValue(':fecha',date("Y-m-d H:i:s"));
                $sql1->bindValue(':estado',$Estado);
                if ($sql1->execute())return true;
                else return false;
            }else return 'ESTA SOLICITUD ACABA DE SER ELIMINADA';
        }else if ($row["sltIdAprobado2"] == $_SESSION["MM_Username"] && is_null($row["sltEstado2"]) &&  $row["sltEstado1"] == 1 ){
            if ($row["sltEliminado"]==0) {
                $consulta1="UPDATE tbl_s_solicitudes SET sltIp2 = :ip,sltFecha2 = :fecha, sltEstado2 = :estado  WHERE sltId = :id";
                $sql1= $dbConn->prepare($consulta1);
                $sql1->bindValue(':id',$row["sltId"]);
                $sql1->bindValue(':ip',$Ip);
                $sql1->bindValue(':fecha',date("Y-m-d H:i:s"));
                $sql1->bindValue(':estado',$Estado);
                if ($sql1->execute()){
                    if ($row["sltFuncion"] == 1) {
                        if (f_funcion_solicitud_comprobacion_1($dbConn,$row["sltIdPrimary"]) == false) {
                            if (f_update_consulta($row["sltConsulta"],$dbConn) == true) return f_actualizar_cantidad_visceras($dbConn,$row["sltIdPrimary"]);
                        }else {
                            $consulta2="UPDATE tbl_s_solicitudes SET sltIp2 = :ip,sltFecha2 = :fecha, sltEstado2 = 0  WHERE sltId = :id";
                            $sql2= $dbConn->prepare($consulta2);
                            $sql2->bindValue(':id',$row["sltId"]);
                            $sql2->bindValue(':ip',$Ip);
                            $sql2->bindValue(':fecha',date("Y-m-d H:i:s"));
                            if ($sql2->execute())return 'Es imposible actualizar la información de la solicitud, debido a que se encuentro una orden de producción';
                        }
                    }
                    
                }else return false;
            }else return 'ESTA SOLICITUD ACABA DE SER ELIMINADA';
        }else return 'ERROR-28776';

    }
}

function f_update_consulta($consulta,$dbConn){
    try {
        $sql= $dbConn->prepare($consulta);
        if ($sql->execute())return true;
        else return false;
    }catch (Exception $e) {
		Insert_Error('ERROR-87222',$e->getMessage(),'Error al ejecutar la consulta');
		exit("ERROR-87222");
	}
}
function f_actualizar_cantidad_visceras($dbConn,$Id){
    $consulta="SELECT g.gprHembra,g.gprMacho, v.vscSexo, v.vscParte, v.vscId   FROM tbl_r_visceras v, tbl_r_guiaproceso g WHERE v.gprId = g.gprId AND g.gprId = :id";
	$sql= $dbConn->prepare($consulta);
    $sql->bindValue(':id',$Id);
	$sql->execute();
	while($row = $sql->fetch()){
        $Cantidad = 0;
        if ($row["vscSexo"]==0) $Cantidad = (($row["gprHembra"] + $row["gprMacho"]) * $row["vscParte"]);
        elseif ($row["vscSexo"]==1) $Cantidad = ($row["gprHembra"]  * $row["vscParte"]);
        else if ($row["vscSexo"]==2) $Cantidad =  ($row["gprMacho"] * $row["vscParte"]);
        if(update_visceras_cantidad($dbConn,$row["vscId"],$Cantidad)==false)return 'ERROR AL MOMENTO DE ACTUALIZAR LAS NUEVAS CANTIDADES';
    }
    return true;
}
function update_visceras_cantidad($dbConn,$Id,$Cantidad){
    $consulta="UPDATE tbl_r_visceras SET vscFecha = :fecha, vscCantidad = :cantidad WHERE vscId = :id";
	$sql= $dbConn->prepare($consulta);
    $sql->bindValue(':fecha',date("Y-m-d H:i:s"));
    $sql->bindValue(':cantidad',$Cantidad);
    $sql->bindValue(':id',$Id);
	if ($sql->execute())return true;
    else return false;
}

?>