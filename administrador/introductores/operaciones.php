<?php
if (isset($_REQUEST["op"])) {
	require '../../FilePHP/utils.php';
	$dbConn = conectar($db);
	$op=$_REQUEST['op'];
	if ($op==1)echo Listar_clientes($dbConn);
	else if ($op==2)echo update_estado($dbConn);
	else if ($op==3)echo get_data_edit_client($dbConn);
	else if ($op==4)echo update_data_client($dbConn);
	else if ($op==5)echo get_data_new_client($dbConn);
	else if ($op==6)echo insert_user_data($dbConn);
	else if ($op==7)echo get_data_num_client($dbConn);
	else if ($op==8)echo update_number_client($dbConn);
	else if ($op==9)echo get_data_cliente_permiso($dbConn);
	else if ($op==10)echo f_update_permisos($dbConn);
	else if ($op==11)echo get_data_producto($dbConn,$_POST["Especie"],$_POST["Cliente"]);
	else if ($op==12)echo get_estado_reporte($_POST["Id"],$dbConn);
	else if ($op==13)echo update_estado_reporte($dbConn);
}

	function Listar_clientes($dbConn){
		$resultado='<table id="tbl_view_user" class="table table-sm table-bordered table-striped text-center text-sm">
        <thead style="font-size:15px;">
            <tr>
                <th>#</th>
                <th>IDE.</th>
                <th>CLIENTE</th>
                <th>MARCA</th>
                <th>TELÉFONO</th>
                <th>EMAIL</th>
                <th>ESTADO</th>
                <th>ACCIONES</th>
            </tr>
        </thead>
        <tbody>';
		$consulta="SELECT * FROM tbl_a_clientes ORDER BY cliNombres ASC";
		$sql= $dbConn->prepare($consulta);
		$sql->execute();
        $cont = 0;
		while($row = $sql->fetch()) {
            $cont++;
            if ($row["cliEstado"]==0) { 
                $btnEstado = '<button class="btn btn-success btn-sm" onclick="cambiar_estado('.$row["cliId"].',1)"  ><b>activo</b></button>';
            }else if ($row["cliEstado"]==1) {
                $btnEstado = '<button class="btn btn-danger btn-sm" onclick="cambiar_estado('.$row["cliId"].',0)" ><b>inactivo</b></button>';
            }else {
                $btnEstado = '<button class="btn btn-warning btn-sm"><b>error</b></button>';
            }
			$resultado .='
                <tr>
                    <th>'.$cont.'</th> 
                    <td>'.$row["cliNumero"].'</td>
                    <td>'.utf8_encode($row["cliNombres"]).'</td>
                    <td>'.utf8_encode($row["cliMarca"]).'</td>
                    <td>'.utf8_encode($row["cliTelefono"]).'</td>
                    <td>'.utf8_encode($row["cliCorreo"]).'</td>
                    <td>'.$btnEstado.'</td>
                    <td>
                        <button class="btn btn-sm btn-info" title="Editar cliente" data-toggle="modal" data-target="#modal" onclick="get_data_user_edit('.$row["cliId"].')" ><i class="fas fa-pencil-alt"></i></button>
                        <button class="btn btn-sm btn-info" title="Permisos cliente" data-toggle="modal" data-target="#modal" onclick="get_data_permisos('.$row["cliId"].')" ><i class="fas fa-user-shield"></i></button>
                        <button class="btn btn-sm btn-info" title="Número de identificación" data-toggle="modal" data-target="#modal" onclick="get_data_number('.$row["cliId"].')" ><i class="fas fa-address-card"></i></button>
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
            $consulta1="UPDATE tbl_a_clientes SET cliEstado=:estado WHERE cliId = :id";
            $sql1= $dbConn->prepare($consulta1);
            $sql1->bindValue(':estado',$estado);
            $sql1->bindValue(':id',trim($cedula));
            if ($sql1->execute()){
                $Acion = "Actualización de datos <b>Clientes</b>";// NO SE ENCONTRO EL ESTADO DEL USUARIO
                $Nombre  = utf8_encode(get_name_client($cedula,$dbConn));
                if($estado ==0){
                    $detalle = "Activación del cliente <i>".$Nombre."</i>";
                }else if($estado ==1){
                    $detalle = "Desactivación del cliente <i>".$Nombre."</i>";
                }
                if(Insert_Login($cedula,'tbl_a_clientes',$Acion,$detalle,''))return true;
                else return "ERROR-654552";//ERROR AL AGREGAR EL LOGS
            }else return "ERROR-0099921";//NO SE PUEDDO ACTUALIZAR EL USUARIO    
        }  catch (Exception $e) {
            Insert_Error('ERROR-288812',$e->getMessage(),'Actualizar estado del cliente');
            exit("ERROR-288812");
        }
    }
    function get_data_edit_client($dbConn){
        $resultado = '';
        $cedula = $_POST["Ruc"];
        $consulta="SELECT * FROM tbl_a_clientes WHERE cliId = :id";
		$sql= $dbConn->prepare($consulta);
        $sql->bindValue(':id',$cedula);
		$sql->execute();
		if($row = $sql->fetch()) {
            $estado = "Activo";
            if ($row["cliEstado"]==1) $estado = "Inactivo";
            $resultado = '
                    <input type="hidden" id="txtNumero" value="'.utf8_encode($row["cliId"]).'">
                    <h6 class="text-muted">
                        Fecha de ingreso: <b>'.$row["cliFechaIngreso"].'</b>
                    </h6>  
                    <h6 class="text-muted">
                        Número de identifiación: <b>'.utf8_encode($row["cliNumero"]).'</b>
                        <br>
                        <span style="font-weight: lighter;font-size:13px;">El sistema reconcio su número de identifación como <b>cédula valida</b></span>
                    </h6>
                    <hr>
                    <div class="row">
                        <div class="col-md-12">
                            <label >Nombre del cliente:</label> 
                            <span class="text-muted">'.utf8_encode($row["cliNombres"]).' <b>'.utf8_encode($row["cliMarca"]).'</b></span>
                        </div>
                        <div class="col-md-8">
                            <label for="txtNameCliente">Nuevo nombre del cliente:</label> <span class="text-muted">(Apellidos y Nombres)</span>
                            <input type="text"  id="txtNameCliente" maxlength="80" class="form-control form-control-sm" value="'.utf8_encode($row["cliNombres"]).'">
                        </div>
                        <div class="col-md-4">
                            <label for="txtMarca">Nueva marca:</label> <span class="text-muted">(max 10)</span>
                            <input type="text"  id="txtMarca" maxlength="10" class="form-control form-control-sm" value="'.utf8_encode($row["cliMarca"]).'">
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-md-6 ">
                            <label>Teléfono:</label> <span class="text-muted">'.utf8_encode($row["cliTelefono"]).'</span>
                            <br>
                            <label for="txtTelefono">Nuevo Teléfono:</label> 
                            <input type="text"  id="txtTelefono" maxlength="45" class="form-control form-control-sm" value="'.utf8_encode($row["cliTelefono"]).'">
                        </div>
                        <div class="col-md-6 ">
                            <label>Correo:</label> <span class="text-muted">'.utf8_encode($row["cliCorreo"]).'</span>
                            <br>
                            <label for="txtCorreo">Nuevo Correo:</label> <span class="text-muted">(max 45)</span>
                            <input type="text"  id="txtCorreo" maxlength="45" class="form-control form-control-sm" value="'.utf8_encode($row["cliCorreo"]).'">
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-md-6">
                            <label>Número de contrato:</label> <span class="text-muted">'.$row["cliContrato"].'</span>
                            <br>
                            <label for="txtContrato">Nuevo contrato:</label> <span class="text-muted">(max 3)</span>
                            <input type="text"  id="txtContrato" maxlength="4" class="form-control form-control-sm" value="'.$row["cliContrato"].'">
                        </div>
                        <div class="col-md-6 ">
                            <label>Dirección:</label> <span class="text-muted">'.utf8_encode($row["cliDireccion"]).'</span>
                            <br>
                            <label for="txtDireccion">Nueva Dirección:</label> <span class="text-muted">(max 45)</span>
                            <input type="text"  id="txtDireccion" maxlength="45" class="form-control form-control-sm" value="'.utf8_encode($row["cliDireccion"]).'">
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-md-12">
                            <label>Observación: </label> <span class="text-muted">'.utf8_encode($row["cliObservaciones"]).'</span>
                            <br>
                            <label for="txtObservacion">Nueva Observacion:</label> 
                            <textarea  class="form-control form-control-sm"  id="txtObservacion" cols="3">'.utf8_encode($row["cliObservaciones"]).'</textarea>
                        </div>
                    </div>
                    <hr>';
        }
        return Modal('Información del cliente',$resultado,'f_update_client()');
    }
    function get_data_new_client($dbConn){
        $resultado = '
                <div class="row">
                    <div class="col-md-6">
                        <label for="txtNumero">Número de identificación: </label> <span class="text-muted">Cédula(10) o Ruc(13)</span>
                        <input type="text"  id="txtNumero" maxlength="13" class="form-control form-control-sm" placeholder="1234567890" >
                    </div>
                </div>
                <hr>        
                <div class="row">
                    <div class="col-md-8">
                        <label for="txtNameCliente">Nombre del cliente:</label> <span class="text-muted">(Apellidos y Nombres)</span>
                        <input type="text"  id="txtNameCliente" maxlength="80" class="form-control form-control-sm" placeholder="APELLIDOS  NOMBRES" >
                    </div>
                    <div class="col-md-4">
                        <label for="txtMarca">Marca:</label> <span class="text-muted">(max 10)</span>
                        <input type="text"  id="txtMarca" maxlength="10" class="form-control form-control-sm" placeholder="ABC" >
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-md-6 ">
                        <label for="txtTelefono">Teléfono:</label> 
                        <input type="text"  id="txtTelefono" maxlength="45" class="form-control form-control-sm" placeholder="0987654321/2345678" >
                    </div>
                    <div class="col-md-6 ">
                        <label for="txtCorreo">Correo:</label> <span class="text-muted">(max 45)</span>
                        <input type="text"  id="txtCorreo" maxlength="45" class="form-control form-control-sm" placeholder="mail@gmail.com">
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-md-6">
                        <label for="txtContrato">Número de contrato:</label> <span class="text-muted">(max 3)</span>
                        <input type="text"  id="txtContrato" maxlength="4" class="form-control form-control-sm" placeholder="1" >
                    </div>
                    <div class="col-md-6 ">
                        <label for="txtDireccion">Dirección:</label> <span class="text-muted">(max 45)</span>
                        <input type="text"  id="txtDireccion" maxlength="45" class="form-control form-control-sm" placeholder="QUITO" >
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-md-12">
                        <label for="txtObservacion">Observacion:</label> 
                        <textarea  class="form-control form-control-sm"  id="txtObservacion" cols="3"></textarea>
                    </div>
                </div>';
        return Modal('Información del cliente',$resultado,'f_new_client()');
    }
    function insert_user_data($dbConn){
        try {
            $numero = trim($_POST["Numero"]);
            $nombre = strtoupper(trim($_POST["Nombre"]));
            $marca = trim($_POST["Marca"]);
            $telefono = trim($_POST["Telefono"]);
            $correo = trim($_POST["Correo"]);
            $contrato = trim($_POST["Contrato"]);
            $direcion = trim($_POST["Direcion"]);
            $observacion= trim($_POST["Observa"]);
            $consulta="SELECT * FROM tbl_a_clientes WHERE cliNumero = :id";
            $sql= $dbConn->prepare($consulta);
            $sql->bindValue(':id',trim($numero));
            $sql->execute();
            if($row = $sql->fetch()){
                return '<b>El número de identificación seleccionado, se encuentra registrado</b> ';
            }else{
                $consulta1="INSERT INTO tbl_a_clientes(cliNumero,cliNombres,cliMarca,cliTelefono,cliCorreo,cliDireccion,cliContrato,cliFechaIngreso,cliObservaciones)
                VALUES (:cliNumero,:cliNombres,:cliMarca,:cliTelefono,:cliCorreo,:cliDireccion,:cliContrato,:cliFechaIngreso,:cliObservaciones)";
                $sql1= $dbConn->prepare($consulta1);
                $sql1->bindValue(':cliNumero', utf8_decode($numero));
                $sql1->bindValue(':cliNombres',utf8_decode($nombre));
                $sql1->bindValue(':cliMarca',utf8_decode($marca));
                $sql1->bindValue(':cliTelefono',utf8_decode($telefono));
                $sql1->bindValue(':cliCorreo',utf8_decode($correo));
                $sql1->bindValue(':cliDireccion',utf8_decode($direcion));
                $sql1->bindValue(':cliContrato',utf8_decode($contrato));
                $sql1->bindValue(':cliFechaIngreso',date("Y-m-d H:i:s"));
                $sql1->bindValue(':cliObservaciones',utf8_decode($observacion));
                if ($sql1->execute()){
                    $Id= $dbConn->lastInsertId();
                    $cargo_old = $row["cargo"];
                    $Acion = 'Nuevo cliente';
                    $detalle = '<b>'.$numero.'</b><br>'.'['.$nombre.'] '.'['.$marca.'] '.'<br>['.$telefono.'] '.'['.$correo.'] '.'['.$contrato.'] '.'<br>['.$direcion.']'.'<br>['.$observacion.']';
                    if(Insert_Login($Id,'tbl_a_clientes',$Acion,$detalle,'')) return true;
                    else return "ERROR-654552";//ERROR AL AGREGAR EL LOGS
                }else return "ERROR-0099921";//NO SE PUEDDO ACTUALIZAR EL USUARIO    
            }
        } catch (Exception $e) {
            Insert_Error('ERROR-877442',$e->getMessage(),'Nuevo cliente');
            exit("ERROR-877442");
        }
    }
    function update_data_client($dbConn){
        try {
            $id = trim($_POST["Id"]);
            $nombre = strtoupper(trim($_POST["Nombre"]));
            $marca = trim($_POST["Marca"]);
            $telefono = trim($_POST["Telefono"]);
            $correo = trim($_POST["Correo"]);
            $contrato = trim($_POST["Contrato"]);
            $direcion = trim($_POST["Direcion"]);
            $observacion= trim($_POST["Observa"]);
            $consulta="SELECT * FROM tbl_a_clientes WHERE cliId = :id";
            $sql= $dbConn->prepare($consulta);
            $sql->bindValue(':id',trim($id));
            $sql->execute();
            if($row = $sql->fetch()){
                $consulta1="UPDATE tbl_a_clientes 
                SET cliNombres = :cliNombres, cliMarca = :cliMarca, cliTelefono = :cliTelefono, cliCorreo = :cliCorreo, cliDireccion = :cliDireccion, cliContrato = :cliContrato, cliObservaciones = :cliObservaciones
                WHERE cliId = :id";
                $sql1= $dbConn->prepare($consulta1);
                $sql1->bindValue(':cliNombres', utf8_decode($nombre));
                $sql1->bindValue(':cliMarca',utf8_decode($marca));
                $sql1->bindValue(':cliTelefono',utf8_decode($telefono));
                $sql1->bindValue(':cliCorreo',utf8_decode($correo));
                $sql1->bindValue(':cliDireccion',utf8_decode($direcion));
                $sql1->bindValue(':cliContrato',utf8_decode($contrato));
                $sql1->bindValue(':cliObservaciones',utf8_decode($observacion));
                $sql1->bindValue(':id',$id);
                if ($sql1->execute()){
                    $Acion = 'Actualización de la información del cliente';
                    $detalle = 'Cliente <b> '.utf8_encode($row["cliNumero"]).'</b><br>'.
                                '['.utf8_encode($row["cliNombres"]).'] => ['.$nombre.']<br>'.
                                '['.utf8_encode($row["cliMarca"]).'] => ['.$marca.']<br>'.
                                '['.utf8_encode($row["cliTelefono"]).'] => ['.$telefono.']<br>'.
                                '['.utf8_encode($row["cliCorreo"]).'] => ['.$correo.']<br>'.
                                '['.utf8_encode($row["cliDireccion"]).'] => ['.$direcion.']<br>'.
                                '['.utf8_encode($row["cliContrato"]).'] => ['.$contrato.']<br>'.
                                '['.utf8_encode($row["cliObservaciones"]).'] => ['.$observacion.']<br>';
                    if(Insert_Login($id,'tbl_a_clientes',$Acion,$detalle,''))return true;
                    else return "ERROR-654552";//ERROR AL AGREGAR EL LOGS
                }else return "ERROR-0099921";//NO SE PUEDDO ACTUALIZAR EL USUARIO    
            }else return 'ERROR-178712';//NO SE ENCONTRO AL USUARIO
        } catch (Exception $e) {
            Insert_Error('ERROR-827222',$e->getMessage(),'Actualizar información del cliente');
            exit("ERROR-827222");
        }
    }
    function get_data_num_client($dbConn){
        $id = $_POST["Id"];
        $resultado = '';
        $consulta="SELECT * FROM tbl_a_clientes WHERE cliId = :id";
		$sql= $dbConn->prepare($consulta);
        $sql->bindValue(':id',$id);
		$sql->execute();
		if($row = $sql->fetch()) {
            $resultado = '
                <input type="hidden" id="txtNumero" value="'.utf8_encode($row["cliId"]).'">
                <h6 class="text-muted">
                    <b>'.utf8_encode($row["cliNombres"]).'</b><br>
                    <span style="font-weight: lighter;font-size:13px;"><b>Nota:</b> El sistema reconcio su número de identifación como <b>cédula valida</b></span>
                </h6>
                <div class="row">
                    <div class="col-md-6">
                        <label >Número de identificación: </label>
                        <span class="form-control form-control-sm">'.utf8_encode($row["cliNumero"]).'</span>
                    </div>
                    <div class="col-md-6">
                        <label for="txtNumero_new">Nuevo número de identificación: </label> <span class="text-muted">Cédula(10) o Ruc(13)</span>
                        <input type="text"  id="txtNumero_new" maxlength="13" class="form-control form-control-sm" value="'.utf8_encode($row["cliNumero"]).'" >
                    </div>
                </div>';
        }else{
            $resultado = 'ERROR-87722';
        }
        return Modal('Identificación del cliente',$resultado,'f_number_client()');
    }
    function update_number_client($dbConn){
        try {
            $id = trim($_POST["Id"]);
            $numero =trim($_POST["Numero"]);
            $consulta="SELECT * FROM tbl_a_clientes WHERE cliNumero = :numb AND cliId != :id";
            $sql= $dbConn->prepare($consulta);
            $sql->bindValue(':numb',$numero);
            $sql->bindValue(':id',$id);
            $sql->execute();
            if($row = $sql->fetch()){
                return '<b>El número de identificación seleccionado, se encuentra registrado</b> ';
            }else{
                $consulta="SELECT * FROM tbl_a_clientes WHERE cliId = :id";
                $sql= $dbConn->prepare($consulta);
                $sql->bindValue(':id',trim($id));
                $sql->execute();
                if($row = $sql->fetch()){
                    $consulta1="UPDATE tbl_a_clientes SET cliNumero = :cliNumero WHERE cliId = :id";
                    $sql1= $dbConn->prepare($consulta1);
                    $sql1->bindValue(':cliNumero', utf8_decode($numero));
                    $sql1->bindValue(':id',$id);
                    if ($sql1->execute()){
                        $Acion = 'Actualización de número de identificación del cliente';
                        $detalle = utf8_encode($row["cliNumero"]).' => '.$numero;
                        if(Insert_Login($id,'tbl_a_clientes',$Acion,$detalle,''))return true;
                        else return "ERROR-654552";//ERROR AL AGREGAR EL LOGS
                    }else return "ERROR-0099921";//NO SE PUEDDO ACTUALIZAR EL USUARIO    
                }else return 'ERROR-178712';//NO SE ENCONTRO AL USUARIO
            }
        } catch (Exception $e) {
            Insert_Error('ERROR-766622',$e->getMessage(),'Actualizar número de identificación del cliente');
            exit("ERROR-766622");
        }
    }

    ////////


    function get_data_cliente_permiso($dbConn){
        $Id = trim($_POST["Id"]);
        $consulta="SELECT * FROM tbl_a_clientes WHERE cliId =:id";
		$sql= $dbConn->prepare($consulta);
        $sql->bindValue(':id',$Id);
		$sql->execute();
		if($row = $sql->fetch()){
            $data = get_data_producto_cliente($dbConn,$row["cliId"]);
            return '
            <div class="modal-header bg-secondary">
                <h5 class="modal-title" id="modalLabel">
                    <b>Permisos del cliente</b>
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <h6 class="text-muted">
                    Permisos para el cliente '.utf8_encode($row["cliNombres"]).'
                    <input type="hidden" value="'.$row["cliId"].'" id="txtIdCliente">
                </h6>
                <hr>
                <div class="row" id="cont-estado-reporte">
                    '.get_estado_reporte($Id,$dbConn).'
                </div>
                <hr>
                <p style="font-weight: lighter;font-size:13px;" class="mb-3">
                    <b>Seleccione los productos que desea que tenga una secuencia unica.</b>
                </p>
                '.$data.'
            </div>';
        }else return 'ERROR-178712';//NO SE ENCONTRO AL USUARIO
    }

    function get_data_producto_cliente($dbConn,$Cliente){
        $resultado = '';
        $consulta="SELECT * FROM tbl_a_especies";
		$sql= $dbConn->prepare($consulta);
		$sql->execute();
		while($row = $sql->fetch()){
            $conte = get_data_producto($dbConn,$row["espId"],$Cliente);
            if ($conte != '') {
                $resultado .= '
                <div class="card collapsed-card" id="contablees-'.$row["espId"].'">
                '.$conte.'
                </div>';
            }
        }
        return $resultado;
    }
    function get_data_producto($dbConn,$Especie,$Cliente){
        $ganado = 'ERROR';
        $resultado = '';
        $consulta="SELECT * FROM tbl_a_productos p, tbl_a_especies e WHERE p.espId = e.espId  AND e.espId = :id";
		$sql= $dbConn->prepare($consulta);
        $sql->bindValue(':id',$Especie);
		$sql->execute();
        $cont=0;
        $con2= 0;
		while($row = $sql->fetch()){
            $cont++;
            $selec = '';
            $estado = 0;
            $ganado = $row["espDescripcion"];
            if (get_data_estado_permiso_producto($dbConn,$Cliente,$row["proId"])==1){
                $selec = 'checked=""';
                $estado = 1;
                $con2++;
            }
            $resultado .= '
            <tr>
                <th>'.$cont.'</th>
                <td>'.$row["proCodigo"].'</td>
                <td id="tddes-'.$row["espId"].'-'.$cont.'" >'.utf8_encode($row["proDescripcion"]).'</td>
                <td>
                    <div class="custom-control custom-checkbox">
                        <input type="hidden" value="'.$estado.'" id="inpEstado-'.$row["espId"].'-'.$cont.'">
                        <input class="custom-control-input" value="'.$row["proId"].'" type="checkbox" id="inpChex-'.$row["espId"].'-'.$cont.'"  '.$selec.' >
                        <label for="inpChex-'.$row["espId"].'-'.$cont.'" class="custom-control-label">Secuencia única</label>
                    </div>
                </td>
            </tr>
            ';
        }
        if ($cont > 0) {
            return '
            <div class="card-header" data-card-widget="collapse" data-toggle="tooltip" title="Collapse" style="cursor: pointer;">
                <h1 class="card-title"><b id="h1Especie-'.$Especie.'">'.strtoupper(utf8_encode($ganado)).'</span></b></h1>
                <div class="card-tools">
                    <span class="text-muted"  style="font-weight: lighter;font-size:13px;">'.$con2.' de '.$cont.'</span>
                </div>
            </div>
            <div class="card-body">
                <table class="mt-2 table table-sm table-bordered table-striped ">
                    <thead>
                        <tr>
                            <th>Nro.</th>
                            <th>Producto</th>
                            <th>Código</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                <tbody>'.$resultado.'</tbody></table>
                <button class="btn btn-info btn-sm float-right mt-2" onclick="f_update_permisos('.$Especie.','.$cont.')">
                    <b>GUARDAR CAMBIOS</b>
                </button>
            </div>';
        }else return '';
    }

    function get_data_estado_permiso_producto($dbConn,$Cliente,$Producto){
        $consulta="SELECT * FROM tbl_permisos_clientes WHERE proId = :pro  AND cliId = :cli";
		$sql= $dbConn->prepare($consulta);
        $sql->bindValue(':pro',$Producto);
        $sql->bindValue(':cli',$Cliente);
		$sql->execute();
		if($row = $sql->fetch())return 1;
        return 0;
    }

    function f_update_permisos($dbConn){
        $Id = $_POST["Id"];
        $array = $_POST["Array"];
        $detalle = '';
        $Error = '';
        for($i=0;$i < count($array);$i++){
            if ($array[$i][1]==0) {
                if (eliminar_permiso($Id,$array[$i][0]) == true) {
                    $detalle .=  utf8_encode(get_name_product($array[$i][0],$dbConn)).'  <b>Activado</b> <br>';
                }else{
                    $Error .= 'Error en el permiso '.utf8_encode(get_name_product($array[$i][0],$dbConn))."<br>";
                }
            }
            if ($array[$i][1]==1) {
                if (ayadir_permiso($dbConn,$Id,$array[$i][0]) == true) {
                    $detalle .=  utf8_encode(get_name_product($array[$i][0],$dbConn)).'  <b>Activado</b> <br>';
                }else{
                    $Error .= 'Error en el permiso '.utf8_encode(get_name_product($array[$i][0],$dbConn))."<br>";
                }
            }
        }
        if ($Error=='') {
            $Acion = 'Permisos de cliente <b>'.utf8_encode(get_name_client($Id,$dbConn)).'</b>';
            if(Insert_Login($Id,'tbl_a_clientes',$Acion,$detalle,'')) return true;
            else return "ERROR-654552";//ERROR AL AGREGAR EL LOGS   
        }else return $Error;
    }

    function eliminar_permiso($cliente,$producto){
        global $dbEl;
        $dbConn = conectar($dbEl);
        $consulta1="DELETE FROM tbl_permisos_clientes WHERE proId = :pro AND cliId = :cli";
        $sql1= $dbConn->prepare($consulta1);
        $sql1->bindValue(':pro', $producto);
        $sql1->bindValue(':cli',$cliente);
        if ($sql1->execute())return true;
        else return "ERROR-0099921";//NO SE PUEDDO ACTUALIZAR EL USUARIO    
    }

    function ayadir_permiso($dbConn,$cliente,$producto){
        $consulta1="INSERT INTO tbl_permisos_clientes(proId,cliId) VALUES(:pro,:cli)";
        $sql1= $dbConn->prepare($consulta1);
        $sql1->bindValue(':pro', $producto);
        $sql1->bindValue(':cli',$cliente);
        if ($sql1->execute())return true;
        else return "ERROR-0099921";//NO SE PUEDDO ACTUALIZAR EL USUARIO    
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
            <button type="button" class="btn btn-primary" onclick="'.$function.'">
                <b>GUARDAR</b>
            </button>
        </div>';
    }
    
    function get_name_client($id,$dbConn){
        $consulta="SELECT cliNombres FROM tbl_a_clientes WHERE cliId = :id";
		$sql= $dbConn->prepare($consulta);
        $sql->bindValue(':id',$id);
		$sql->execute();
		if($row = $sql->fetch()){
            return $row["cliNombres"];
        }else return "ERROR-100542";//NO SE ENCONTRO EL NOMBRE DE USUARIO
    }
    function get_name_product($id,$dbConn){
        $consulta="SELECT proCodigo,proDescripcion FROM tbl_a_productos WHERE proId = :id";
		$sql= $dbConn->prepare($consulta);
        $sql->bindValue(':id',$id);
		$sql->execute();
		if($row = $sql->fetch()){
            return $row["proDescripcion"]. ' ('.$row["proCodigo"].')';
        }else return "ERROR-100542";//NO SE ENCONTRO EL NOMBRE DE USUARIO
    }
    function get_estado_reporte($Id,$dbConn){
        $consulta="SELECT * FROM tbl_a_clientes WHERE cliId =:id";
		$sql= $dbConn->prepare($consulta);
        $sql->bindValue(':id',$Id);
		$sql->execute();
		if($row = $sql->fetch()){
            $selected1 = "";
            $selected2 = "";
            if ($row["cliReporte"]==0) $selected1 = "selected";
            if ($row["cliReporte"]==1) $selected2 = "selected";
            return '
                <div class="col-md-2">
                    <label>Tipo de reporte:</label>
                </div>
                <div class="col-md-4">
                    <select  id="slcTipoReporte" onchange="update_reporte()" class="form-control form-control-sm select2bs4 ">
                        <option value="0" '.$selected1.' >Normal</option>
                        <option value="1" '.$selected2.' >Especial</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <p  style="font-weight: lighter;font-size:13px;" class="mb-0 pb-0">
                        <b>Normal:</b> Aparecera el logo de rastro en el reporte <br>
                        <b>Especial:</b> El logo no rastro no se muestra en el reporte
                    </p>
                </div>';
        }else return "ERROR-100542";//NO SE ENCONTRO EL NOMBRE DE USUARIO
    }
    function update_estado_reporte($dbConn){
        try {
            $estado = $_POST["Estado"];
            $Id = $_POST["Id"];
            $consulta1="UPDATE tbl_a_clientes SET cliReporte = :estado WHERE cliId = :id";
            $sql1= $dbConn->prepare($consulta1);
            $sql1->bindValue(':estado',$estado);
            $sql1->bindValue(':id',trim($Id));
            if ($sql1->execute()){
                $Acion = "Tipo de reporte";// NO SE ENCONTRO EL ESTADO DEL USUARIO
                if($estado ==0){
                    $detalle = "Reporte de tipo <b>Normal</b> ";
                }else if($estado ==1){
                    $detalle = "Reporte de tipo <b>Especial</b> ";
                }
                if(Insert_Login($Id,'tbl_a_clientes',$Acion,$detalle,''))return true;
                else return "ERROR-654552";//ERROR AL AGREGAR EL LOGS
            }else return "ERROR-0099921";//NO SE PUEDDO ACTUALIZAR EL USUARIO    
        }  catch (Exception $e) {
            Insert_Error('ERROR-288812',$e->getMessage(),'Actualizar tipo de reporte');
            exit("ERROR-288812");
        }
    }
?> 