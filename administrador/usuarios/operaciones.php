<?php
if (isset($_REQUEST["op"])) {
	require '../../FilePHP/utils.php';
	$dbConn = conectar($db);
	$op=$_REQUEST['op'];
	if ($op==1)echo Listar_usuarios($dbConn);
	else if ($op==2)echo update_estado($dbConn);
	else if ($op==3)echo get_data_edit($dbConn);
	else if ($op==4)echo update_user_data($dbConn);
	else if ($op==5)echo get_data_password($dbConn);
	else if ($op==6)echo update_user_pass($dbConn);
	else if ($op==7)echo get_data_permise($dbConn);
	else if ($op==8)echo update_user_permis($dbConn);
	else if ($op==9)echo get_data_new_user();
	else if ($op==10)echo new_user_data($dbConn);
	else if ($op==11)echo get_data_update_cedula($dbConn);
	else if ($op==12)echo update_cedula($dbConn);
}

	function Listar_usuarios($dbConn){
		$resultado='<table id="tbl_view_user" class="table table-sm table-bordered table-striped text-center">
        <thead style="font-size:15px;">
            <tr>
                <th>#</th>
                <th>USUARIO</th>
                <th>APELLIDO Y NOMBRE</th>
                <th>CARGO</th>
                <th>DEPARTAMENTO</th>
                <th>ESTADO</th>
                <th>ACCIONES</th>
            </tr>
        </thead>
        <tbody>';
		$consulta="SELECT * FROM tbl_a_usuarios WHERE usuId != :id ORDER BY usuNombre ASC";
		$sql= $dbConn->prepare($consulta);
        $sql->bindValue(':id',$_SESSION["MM_Username"]);
		$sql->execute();
        $cont = 0;
		while($row = $sql->fetch()) {
            $cont++;
            if ($row["usuEstado"]==1) { 
                $btnEstado = '<button class="btn btn-success btn-sm" onclick="cambiar_estado('.$row["usuId"].',0)"  ><b>activo</b></button>';
            }else if ($row["usuEstado"]==0) {
                $btnEstado = '<button class="btn btn-warning btn-sm" onclick="cambiar_estado('.$row["usuId"].',1)" ><b>inactivo</b></button>';
            }else {
                $btnEstado = '<button class="btn btn-danger btn-sm"><b>ERROR-100045</b></button>';
            }
			$resultado .='
                <tr>
                    <th>'.$cont.'</th> 
                    <td>'.$row["usuCedula"].'</td>
                    <td>'.utf8_encode($row["usuNombre"]).'</td>
                    <td>'.utf8_encode($row["usuCargo"]).'</td>
                    <td>'.utf8_encode($row["usuDepartamento"]).'</td>
                    <td>'.$btnEstado.'</td>
                    <td>
                        <button title="Editar usuario" class="btn btn-sm btn-info" data-toggle="modal" data-target="#modal" onclick="get_data_user('.$row["usuId"].')" ><i class="fas fa-pencil-alt"></i></button>
                        <button title="Restablecer contraseña" class="btn btn-sm btn-info" data-toggle="modal" data-target="#modal" onclick="get_data_pass('.$row["usuId"].')" ><i class="fas fa-unlock-alt"></i></button>
                        <button title="Permisos" class="btn btn-sm btn-info" data-toggle="modal" data-target="#modal" onclick="get_data_permisos('.$row["usuId"].')" ><i class="fas fa-user-shield"></i></button>
                        <button title="Usuario" class="btn btn-sm btn-info" data-toggle="modal" data-target="#modal" onclick="get_data_cedula_user('.$row["usuId"].')" ><i class="fas fa-user"></i></button>
                    </td>
                </tr>  
            ';
		}
		return $resultado;
	}
    function update_estado($dbConn){
        try {
            $estado = $_POST["Estado"];
            $cedula = $_POST["Cedula"];
            $consulta1="UPDATE tbl_a_usuarios SET usuEstado=:estado WHERE usuId = :id";
            $sql1= $dbConn->prepare($consulta1);
            $sql1->bindValue(':estado',$estado);
            $sql1->bindValue(':id',trim($cedula));
            if ($sql1->execute()){
                $user = get_name_user($cedula,$dbConn);
                $Acion = "ERROR-100075";// NO SE ENCONTRO EL ESTADO DEL USUARIO
                $detalle = "ERROR-100076";//NO SE ECONTRO EL ESTADO DEL USUARIO
                if($estado ==0){
                    $Acion = "Desactivación de usuario";
                    $detalle = 'El usario ('.$user.') fue desactivado';
                }else if($estado ==1){
                    $Acion = "Activación de usuario";
                    $detalle = 'El usario ('.$user.') fue activado';
                }
                if(Insert_Login($cedula,'tbl_a_usuarios',$Acion,$detalle,''))return true;
                else return "ERROR-100085";//ERROR AL AGREGAR  LOGS
            }else return "ERROR-100086";//NO SE ENCONTRO AL USARIO
        } catch (Exception $e) {
            Insert_Error('ERROR-156266',$e->getMessage(),'Actualizar estado del usuario');
            exit("ERROR-156266");
        }
    }
    function update_user_data($dbConn){
        try {
            $cedula = trim($_POST["Cedula"]);
            $nombre = trim($_POST["Nombre"]);
            $cargo = trim($_POST["Cargo"]);
            $departamento = trim($_POST["Departamento"]);
            $consulta="SELECT * FROM tbl_a_usuarios WHERE usuId = :id";
            $sql= $dbConn->prepare($consulta);
            $sql->bindValue(':id',trim($cedula));
            $sql->execute();
            if($row = $sql->fetch()){
                $consulta1="UPDATE tbl_a_usuarios SET usuNombre=:nombre, usuCargo = :cargo, usuDepartamento = :departamento WHERE usuId = :id";
                $sql1= $dbConn->prepare($consulta1);
                $sql1->bindValue(':nombre', utf8_decode($nombre));
                $sql1->bindValue(':cargo',utf8_decode($cargo));
                $sql1->bindValue(':departamento',utf8_decode($departamento));
                $sql1->bindValue(':id',utf8_decode($cedula));
                if ($sql1->execute()){
                    $user = get_name_user($cedula,$dbConn);
                    $Acion = 'Actualización de Usuario';
                    $Anterior = '['.utf8_encode($row["usuNombre"]).'] ['.utf8_encode($row["usuCargo"]).'] ['.utf8_encode($row["usuDepartamento"]).']';
                    $Nuevo = '['.$nombre.'] ['.$cargo.'] ['.$departamento.']';
                    $detalle = '<b>DATOS DE '.$user.'</b><br> Actuales: '.$Anterior.'<br>Nuevos: '.$Nuevo;
                    if(Insert_Login($cedula,'tbl_a_usuarios',$Acion,$detalle,''))return true;
                    else return "ERROR-100116";//ERROR AL AGREGAR EL LOGS
                }else return "ERROR-100117";//NO SE PUEDDO ACTUALIZAR EL USUARIO    
            }else return 'ERROR-100118';//NO SE ENCONTRO AL USUARIO
        } catch (Exception $e) {
            Insert_Error('ERROR-188765',$e->getMessage(),'Actualizar informacion del usuario');
            exit("ERROR-188765");
        }
    }

    function update_user_pass($dbConn){
        try {
            $cedula = trim($_POST["Cedula"]);
            $password= trim($_POST["Pass"]);
            $consulta1="UPDATE tbl_a_usuarios SET usuPasswd = :pass, usuEstado_pass = 1 WHERE usuId = :id";
            $sql1= $dbConn->prepare($consulta1);
            $sql1->bindValue(':pass',md5($password));
            $sql1->bindValue(':id',$cedula);
            if ($sql1->execute()){
                $user = get_name_user($cedula,$dbConn);
                $Acion  ='Restablecimiento de contraseña';
                $detalle = "Se cambio la contraseña del usuario ".$user;
                if(Insert_Login($cedula,'tbl_a_usuarios',$Acion,$detalle,''))return true;
                else return "ERROR-100137";//ERROR AL AGREGAR EL LOGS
            }else return "ERROR-100138";//NO SE PUEDDO ACTUALIZAR LA CONTRASEÑA
        } catch (Exception $e) {
            Insert_Error('ERROR-184663',$e->getMessage(),'Actualizar contraseña del usuario');
            exit("ERROR-184663");
        }
    }
    function update_user_permis($dbConn){
        try {
            $cedula = trim($_POST["Cedula"]);
            $Array = $_POST["Array"];
            $Errores = '';
            $Nuevo = '';
            $Eliminar = '';
            $consulta="SELECT * FROM tbl_a_usuarios WHERE usuId = :id";
            $sql= $dbConn->prepare($consulta);
            $sql->bindValue(':id',$cedula);
            $sql->execute();
            if($row = $sql->fetch()){
                for ($i=0; $i <count($Array) ; $i++) {
                    $nameM = get_data_modulo($dbConn,$Array[$i][0]);
                    if ($Array[$i][2]==1){
                        $bandera = ayadir_permiso($dbConn,$cedula,$Array[$i][0],$nameM);
                        if ($bandera != true)$Errores .='No se pudo añadir el permiso '.$nameM.' Error:'.$bandera;
                        else $Nuevo .= '['.$nameM.'] ';
                    }elseif ($Array[$i][2]==0){
                        $bandera = eliminar_permiso($cedula,$Array[$i][0],$nameM);
                        if ($bandera != true)$Errores .='No se pudo eliminar el permiso '.$nameM.' Error:'.$bandera;
                        else $Eliminar .= '['.$nameM.'] ';
                    }
                }
                if ($Errores == ''){
                    $Acion = 'Desginación de permisos';
                    $user = get_name_user($cedula,$dbConn);
                    $detalle='<b>Permisos para '.$user.'</b><br>Nuevos Permisos '.$Nuevo.'<br>Permisos Eliminados '.$Eliminar;
                    if(Insert_Login($cedula,'tbl_a_usuarios',$Acion,$detalle,''))return true; 
                    else return 'ERROR-100172';
                }else return $Errores;
            }else return 'ERROR-100174';//NO SE ENCONTRO AL USUARIO
        } catch (Exception $e) {
            Insert_Error('ERROR-199865',$e->getMessage(),'Actualizar permisos');
            exit("ERROR-199865");
        }
    }
    function eliminar_permiso($cedula,$modulo,$nameM){
        try {
            global $dbEl;
            $dbConn = conectar($dbEl);
            $consulta1="DELETE FROM tbl_permisos_new WHERE usuId = :cedula AND mdoId = :mod ";
            $sql1= $dbConn->prepare($consulta1);
            $sql1->bindValue(':mod', $modulo);
            $sql1->bindValue(':cedula',$cedula);
            if ($sql1->execute())return true;
            else return "ERROR-0099921";//NO SE PUEDDO ACTUALIZAR EL USUARIO
        }  catch (Exception $e) {
            Insert_Error('ERROR-184332',$e->getMessage(),'Eliminar permiso');
            exit("ERROR-184332");
        }
    }
    function ayadir_permiso($dbConn,$cedula,$modulo,$nameM){
        try {
            $consulta1="INSERT INTO tbl_permisos_new(usuId,mdoId) VALUES(:cedula,:mod)";
            $sql1= $dbConn->prepare($consulta1);
            $sql1->bindValue(':mod', $modulo);
            $sql1->bindValue(':cedula',$cedula);
            if ($sql1->execute())return true;
            else return "ERROR-0099921";//NO SE PUEDDO ACTUALIZAR EL USUARIO    
        } catch (Exception $e) {
            Insert_Error('ERROR-109432',$e->getMessage(),'Añadir los permisos');
            exit("ERROR-109432");
        }
    }
    function new_user_data($dbConn){
        try {
            $cedula = trim($_POST["Cedula"]);
            $nombre = trim($_POST["Nombre"]);
            $cargo = trim($_POST["Cargo"]);
            $departamento = trim($_POST["Departamento"]);
            $consulta="SELECT * FROM tbl_a_usuarios WHERE usuCedula = :id";
            $sql= $dbConn->prepare($consulta);
            $sql->bindValue(':id',trim($cedula));
            $sql->execute();
            if($row = $sql->fetch()){
                return '<b>EL Usuario ya se encuentra registrado</b>';
            }else{
                $consulta1="INSERT INTO tbl_a_usuarios(usuCedula,usuNombre,usuCargo,usuDepartamento) 
                VALUE(:cedula,:nombre,:cargo,:departamento)";
                $sql1= $dbConn->prepare($consulta1);
                $sql1->bindValue(':cedula', utf8_decode($cedula));
                $sql1->bindValue(':nombre', utf8_decode($nombre));
                $sql1->bindValue(':cargo',utf8_decode($cargo));
                $sql1->bindValue(':departamento',utf8_decode($departamento));
                if ($sql1->execute()){
                    $Id= $dbConn->lastInsertId();
                    $Acion ='Nuevo Usuario';
                    $detalle ='Se agrego a '.$cedula;
                    if(Insert_Login($Id,'tbl_a_usuarios',$Acion,$detalle,''))return true;
                    else return "ERROR-654552";//ERROR AL AGREGAR EL LOGS
                }else return "ERROR-0099921";//NO SE PUEDDO ACTUALIZAR EL USUARIO   
            }
        } catch (Exception $e) {
            Insert_Error('ERROR-188219',$e->getMessage(),'Nuevo Usuario');
            exit("ERROR-188219");
        }
    }



    function get_data_modulo($dbConn,$modulo){
        $result ='';
        $consulta="SELECT modDescripcion FROM  tbl_modulos_new where mdoId = :id";
		$sql= $dbConn->prepare($consulta);
        $sql->bindValue(':id',$modulo);
		$sql->execute();
		if($row = $sql->fetch()){
            $result = utf8_encode($row["modDescripcion"]);
        }
        return $result;
    }
    function get_data_edit($dbConn){
        $resultado = '';
        $cedula = $_POST["Cedula"];
        $consulta="SELECT * FROM tbl_a_usuarios WHERE usuId = :id";
		$sql= $dbConn->prepare($consulta);
        $sql->bindValue(':id',$cedula);
		$sql->execute();
		if($row = $sql->fetch()) {
            $resultado = '
        <input type="hidden" id="txtCedula_edit" value="'.$row["usuId"].'">
        <div class="row">
            <div class="col-md-12">
                <label for="txtNameUser-edit">Nombre de usuario:</label> 
                <input type="text"  id="txtNameUser-edit" maxlength="100" class="form-control form-control-sm" value="'.utf8_encode($row["usuNombre"]).'">
            </div>
        </div>
        <hr>
        <div class="row">
            <div class="col-md-8">
                <label for="txtCargo-edit">Cargo en la Empresa:</label>
                <input type="text"  id="txtCargo-edit" maxlength="100" class="form-control form-control-sm" value="'.utf8_encode($row["usuCargo"]).'">
            </div>
        </div>
        <hr>
        <div class="row">
            <div class="col-md-8">
                <label for="txtDepartamento-edit" >Departamento:</label> 
                <input type="text"  id="txtDepartamento-edit" maxlength="100" class="form-control form-control-sm" value="'.utf8_encode($row["usuDepartamento"]).'">
            </div>
        </div>';
        }
        $cabecera = get_data_user($dbConn,$cedula,'Editar información para el usuario');
        return Modal('Editar Información de usuario',$cabecera.$resultado,'update_user()');
    }
    function get_data_password($dbConn){
        $cedula = trim($_POST["Cedula"]);
        $resultado = '
        <input type="hidden" id="txtCedula_pass" value="'.$cedula.'" >
        <div class="row">
            <div class="col-md-12">
                <label for="txtpassrestor">Contraseña de restablecimiento:</label> 
                <div class="input-group input-group-sm">
                <input type="password" class="form-control" id="txtpassrestor" placeholder="Contraseña">
                <span class="input-group-append">
                    <button type="button" class="btn btn-secondary" onclick="mostrar_contrasenia()">
                        <i class="fas fa-eye" id="icon"></i>
                    </button>
                </span>
            </div>
            </div>
        </div>';
        $cabecera = get_data_user($dbConn,$cedula,'Restablecer contraseña para el usuario ');
        return Modal('Restablecer contraseña',$cabecera.$resultado,'update_password()');
    }
    function select_departamento($dbConn){
        $consulta="SELECT * FROM  tbl_departamentos";
		$sql= $dbConn->prepare($consulta);
        $option ='';
		$sql->execute();
		while($row = $sql->fetch()){
            $option .= '<option value="'.$row["id"].'">'.utf8_encode($row["nombre"]).'</option>';
        }
        return $option;
    }
    function get_data_permise($dbConn){
        $aplicativos = '';
        $recepcion = '';
        $produccion = '';
        $consultas = '';
        $administrador = '';
        $otros= '';
        $cedula = $_POST["Cedula"];
        $cont = 0;
        $consulta="SELECT * FROM tbl_modulos_new";
		$sql= $dbConn->prepare($consulta);
		$sql->execute();
		while($row = $sql->fetch()) {
            $cont++;
            $esatdo_chb = permiso_modulo($dbConn,$cedula,$row["mdoId"]);
            $value = '0';
            $checked = '';
            $estado = 'ERROR';//0 Inhabilitado, 1 Habilitado
            if ($esatdo_chb == false) {
                $value = $row["mdoId"];
                $estado = '0';
            }else{
                $value = $row["mdoId"];
                $checked = 'checked';
                $estado = '1';
            }
            if ($row["modMenu"]==0) {
                $aplicativos .= '
                    <div class="col-6">
                        <div class="custom-control custom-checkbox">
                            <input type="hidden" id="chbst'.$cont.'" value="'.$estado.'">
                            <input class="custom-control-input" type="checkbox" id="chb'.$cont.'" value="'.$row["mdoId"].'" '.$checked.' >
                            <label for="chb'.$cont.'" id="lblch'.$row["mdoId"].'" class="custom-control-label">'.utf8_encode($row["modDescripcion"]).'</label>
                        </div>
                    </div>';
            }elseif ($row["modMenu"]==1) {
                $recepcion .= '
                    <div class="col-6">
                        <div class="custom-control custom-checkbox">
                            <input type="hidden" id="chbst'.$cont.'" value="'.$estado.'">
                            <input class="custom-control-input" type="checkbox" id="chb'.$cont.'" value="'.$row["mdoId"].'" '.$checked.' >
                            <label for="chb'.$cont.'" id="lblch'.$row["mdoId"].'" class="custom-control-label">'.utf8_encode($row["modDescripcion"]).'</label>
                        </div>
                    </div>';
            }elseif ($row["modMenu"]==2) {
                $produccion .= '
                    <div class="col-6">
                        <div class="custom-control custom-checkbox">
                            <input type="hidden" id="chbst'.$cont.'" value="'.$estado.'">
                            <input class="custom-control-input" type="checkbox" id="chb'.$cont.'" value="'.$row["mdoId"].'" '.$checked.' >
                            <label for="chb'.$cont.'" id="lblch'.$row["mdoId"].'" class="custom-control-label">'.utf8_encode($row["modDescripcion"]).'</label>
                        </div>
                    </div>';
            }elseif ($row["modMenu"]==3) {
                $consultas .= '
                    <div class="col-6">
                        <div class="custom-control custom-checkbox">
                            <input type="hidden" id="chbst'.$cont.'" value="'.$estado.'">
                            <input class="custom-control-input" type="checkbox" id="chb'.$cont.'" value="'.$row["mdoId"].'" '.$checked.' >
                            <label for="chb'.$cont.'" id="lblch'.$row["mdoId"].'" class="custom-control-label">'.utf8_encode($row["modDescripcion"]).'</label>
                        </div>
                    </div>';
            }elseif ($row["modMenu"]==4) {
                $administrador .= '
                    <div class="col-6">
                        <div class="custom-control custom-checkbox">
                            <input type="hidden" id="chbst'.$cont.'" value="'.$estado.'">
                            <input class="custom-control-input" type="checkbox" id="chb'.$cont.'" value="'.$row["mdoId"].'" '.$checked.' >
                            <label for="chb'.$cont.'" id="lblch'.$row["mdoId"].'" class="custom-control-label">'.utf8_encode($row["modDescripcion"]).'</label>
                        </div>
                    </div>';
            }elseif ($row["modMenu"]==5) {
                $otros .= '
                    <div class="col-6">
                        <div class="custom-control custom-checkbox">
                            <input type="hidden" id="chbst'.$cont.'" value="'.$estado.'">
                            <input class="custom-control-input" type="checkbox" id="chb'.$cont.'" value="'.$row["mdoId"].'" '.$checked.' >
                            <label for="chb'.$cont.'" id="lblch'.$row["mdoId"].'" class="custom-control-label">'.utf8_encode($row["modDescripcion"]).'</label>
                        </div>
                    </div>';
            }
        }
        $resultado = '<div class="row"><div class="col-12"><h6 class="text-muted"><b> <u> Aplicativos</u></b></h6></div>'. $aplicativos.'</div>'.
        '<div class="row"><div class="col-12"><h6 class="text-muted mt-3"><b><u>Recepción</u></b></h6></div>'. $recepcion.'</div>'.
        '<div class="row"><div class="col-12"><h6 class="text-muted mt-3"><b><u>Producción</u></b></h6></div>'. $produccion.'</div>'.
        '<div class="row"><div class="col-12"><h6 class="text-muted mt-3"><b><u>Consultas</u></b></h6></div>'. $consultas.'</div>'.
        '<div class="row"><div class="col-12"><h6 class="text-muted mt-3"><b><u>Administrador</u></b></h6></div>'. $administrador.'</div>'.
        '<div class="row"><div class="col-12"><h6 class="text-muted mt-3"><b><u>Otros</u></b></h6></div>'. $otros.'</div>';
        $cabecera = get_data_user($dbConn,$cedula,'Permisos para el usuario').'<input type="hidden" value="'.$cedula.'" id="txtCedula_permisos">';
        $cantidad = '<input type="hidden" value="'.$cont.'" id="cant-permisos">';
        return Modal('Desiganción de permisos',$cabecera.$resultado.$cantidad,'update_permisos()');
    }
    function permiso_modulo($dbConn,$cedula,$modulo){
        $consulta="SELECT prmId FROM  tbl_permisos_new where usuId = :id AND mdoId = :mod";
		$sql= $dbConn->prepare($consulta);
        $sql->bindValue(':id',$cedula);
        $sql->bindValue(':mod',$modulo);
		$sql->execute();
		if($row = $sql->fetch()){
            return true;
        }
        return false;
    }
    function get_data_new_user(){
        $data = '<div class="row">
        <div class="col-md-6">
            <label for="txtCedula">Usuario:</label><span class="text-muted"> máximo 10 caracteres</span>  
            <input type="text"  id="txtCedula" maxlength="10" class="form-control form-control-sm">
        </div>
        <div class="col-md-6">
            <label for="txtNameUser">Nombre y Apellido:</label> 
            <input type="text"  id="txtNameUser" maxlength="50" class="form-control form-control-sm">
        </div>
        </div>
        <hr>
        <div class="row">
            <div class="col-md-6">
                <label for="txtCargo-edit">Cargo en la Empresa:</label><span class="text-muted"> máximo 30</span>
                <input type="text"  id="txtCargo" maxlength="30" class="form-control form-control-sm">
            </div>
            <div class="col-md-6">
                <label for="txtDepartamento-edit" >Departamento:</label><span class="text-muted"> máximo 30</span>
                <input type="text"  id="txtDepartamento" maxlength="30" class="form-control form-control-sm">
            </div>
        </div>';
        return Modal('NUEVO USUARIO',$data,'new_user()');
    }


    function Modal($titulo,$data,$function){
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
            <button type="button" id="btnCerrar"  class="btn btn-light" data-dismiss="modal"><b>CERRAR</b></button>
            <button type="button" class="btn btn-primary" id="btn-action" onclick="'.$function.'">
                <b>GUARDAR</b>
            </button>
        </div>';
    }
    function get_data_user($dbConn,$cedula,$titulo){
        $result ='';
        $consulta="SELECT usuNombre FROM  tbl_a_usuarios where usuId = :id";
		$sql= $dbConn->prepare($consulta);
        $sql->bindValue(':id',$cedula);
		$sql->execute();
		if($row = $sql->fetch()){
            $result = '<div class="row"><div class="col-12"><h6 class="text-muted">'.$titulo.' '.utf8_encode($row["usuNombre"]).'</h6></div> </div> <hr class="mt-2">';
        }
        return $result;
    }

    

    function get_data_update_cedula($dbConn){
        $cedula = $_POST["Cedula"];
        $cabecera = get_data_user($dbConn,$cedula,'Cambiar usuario para').'<input type="hidden" value="'.$cedula.'" id="txtCedula_user">';
        $result ='';
        $consulta="SELECT usuCedula FROM  tbl_a_usuarios where usuId = :id";
		$sql= $dbConn->prepare($consulta);
        $sql->bindValue(':id',$cedula);
		$sql->execute();
		if($row = $sql->fetch()){
            $data = '<div class="row">
            <div class="col-md-6">
                <label >Usuario actual:</label>
                <span class="form-control form-control-sm">'.$row["usuCedula"].'</span>    
            </div>
            <div class="col-md-6">
                <label for="txtNewCedula">Usuario Nuevo:</label> 
                <input type="text"  id="txtNewCedula" maxlength="10" class="form-control form-control-sm">
            </div>
            </div>';
            return Modal('CAMBIO DEL NOMBRE USUARIO',$cabecera.$data,'update_user_cedula()');
        }
    }

    function update_cedula($dbConn){
        try {
            $cedula_OLD = trim($_POST["Id"]);
            $cedula_NEW = trim($_POST["Cedula"]);
            $consulta="SELECT * FROM tbl_a_usuarios WHERE usuCedula = :id";
            $sql= $dbConn->prepare($consulta);
            $sql->bindValue(':id',trim($cedula_NEW));
            $sql->execute();
            if($row = $sql->fetch()){ 
                return '<b>EL Usuario ya se encuentra registrado</b>';
            }else{
                $user = get_name_user($cedula_OLD,$dbConn);
                $consulta1="UPDATE tbl_a_usuarios SET usuCedula = :cedula WHERE usuId = :id";
                $sql1= $dbConn->prepare($consulta1);
                $sql1->bindValue(':cedula', utf8_decode($cedula_NEW));
                $sql1->bindValue(':id', utf8_decode($cedula_OLD));
                if ($sql1->execute()){
                    $Acion = "Cambio de nombre de usuario";
                    $detalle = "Actual nombre de usuario ".$user."<br>Nuevo nombre de usuario ".$cedula_NEW;
                    if(Insert_Login($cedula_OLD,'tbl_a_usuarios',$Acion,$detalle,'Inicio de sesión'))return true;
                    else return "ERROR-654552";//ERROR AL AGREGAR EL LOGS
                }else return "ERROR-0099921";//NO SE PUEDDO ACTUALIZAR EL USUARIO   
            }
        } catch (Exception $e) {
            Insert_Error('ERROR-188219',$e->getMessage(),'Nuevo Usuario');
            exit("ERROR-188219");
        }
    }
    function get_name_user($id,$dbConn){
        $consulta="SELECT usuCedula FROM  tbl_a_usuarios where usuId = :id";
		$sql= $dbConn->prepare($consulta);
        $sql->bindValue(':id',$id);
		$sql->execute();
		if($row = $sql->fetch()){
            return $row["usuCedula"];
        }else return "ERROR-100542";//NO SE ENCONTRO EL NOMBRE DE USUARIO
    }
?>