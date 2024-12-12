<?php
require 'utils.php';
function Array_Menu($dbConn,$name_user){
    $resultado =[];
	$consulta="SELECT m.mdoId, m.modDescripcion, m.modMenu,m.modCarpeta FROM tbl_permisos_new p, tbl_modulos_new m WHERE p.mdoId = m.mdoId and p.usuId = :cedula AND m.modMenu > 0 ";
	$sql= $dbConn->prepare($consulta);
    $sql->bindValue(':cedula',$name_user);
	$sql->execute();
	while($row = $sql->fetch()) {
		$resultado[]=[$row["modMenu"],utf8_encode($row["modDescripcion"]),$row["modCarpeta"],$row["mdoId"]];
	}
	return $resultado;
}
function get_info_user_restore($id){
    global $db;
    $dbConn = conectar($db);
    $resultado =[];
	$consulta="SELECT usuNombre,usuEstado_pass FROM tbl_a_usuarios  WHERE usuId = :cedula AND usuEstado = 1";
	$sql= $dbConn->prepare($consulta);
    $sql->bindValue(':cedula',$id);
	$sql->execute();
	if($row = $sql->fetch()) {
        array_push($resultado, utf8_encode($row["usuNombre"]),$row["usuEstado_pass"]);
        return  $resultado;
	}else return false;
}

function get_info_user($dbConn,$id){
    $resultado =[];
	$consulta="SELECT * FROM tbl_a_usuarios WHERE usuId = :cedula AND usuEstado = 1 AND usuEstado_pass = 0";
	$sql= $dbConn->prepare($consulta);
    $sql->bindValue(':cedula',$id);
	$sql->execute();
	if($row = $sql->fetch()) {
        array_push($resultado, utf8_encode($row["usuNombre"]),utf8_encode($row["usuCargo"]),utf8_encode($row["usuDepartamento"]));
        return  $resultado;
	}else return false;
}
function get_out(){
    session_destroy();
    header("location: ../login/");
}

function data_menu($id){
    //Si se modifica, tambien se debe modificar en el perfil del usuario
    global $db;
    $dbConn = conectar($db);
    $array = Array_Menu($dbConn,$_SESSION['MM_Username']);
    $Recepcion='<li class="nav-item dropdown">
    <a id="dropdownSubMenu1" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="nav-link dropdown-toggle">Recepción</a>
    <ul aria-labelledby="dropdownSubMenu1" class="dropdown-menu border-0 shadow">';
    $Recepcion_cont=0;

    $Produccion='<li class="nav-item dropdown">
    <a id="dropdownSubMenu1" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="nav-link dropdown-toggle">Producción</a>
    <ul aria-labelledby="dropdownSubMenu1" class="dropdown-menu border-0 shadow">';
    $Produccion_cont=0;

    $Consultas='<li class="nav-item dropdown">
    <a id="dropdownSubMenu1" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="nav-link dropdown-toggle">Reportes</a>
    <ul aria-labelledby="dropdownSubMenu1" class="dropdown-menu border-0 shadow">';
    $Consultas_cont=0;

    $Admin='<li class="nav-item dropdown">
    <a id="dropdownSubMenu1" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="nav-link dropdown-toggle">Administrador</a>
    <ul aria-labelledby="dropdownSubMenu1" class="dropdown-menu border-0 shadow">';
    $Admin_cont=0;

    $Otros='<li class="nav-item dropdown">
    <a id="dropdownSubMenu1" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="nav-link dropdown-toggle">Otros</a>
    <ul aria-labelledby="dropdownSubMenu1" class="dropdown-menu border-0 shadow">';
    $Otros_cont=0;

    $cont=0;
    if (count($array) > 0) {
        for($i=0;$i<count($array);$i++){
            if ($array[$i][3]==$id) $cont++;
            if ($array[$i][0]==1) {
                $Recepcion .='<li><a href="'.$array[$i][2].'" class="dropdown-item">'.$array[$i][1].'</a></li>';
                $Recepcion_cont++;
            }elseif ($array[$i][0]==2) {
                $Produccion .='<li><a href="'.$array[$i][2].'" class="dropdown-item">'.$array[$i][1].'</a></li>';
                $Produccion_cont++;
            }elseif ($array[$i][0]==3) {
                $Consultas .='<li><a href="'.$array[$i][2].'" class="dropdown-item">'.$array[$i][1].'</a></li>';
                $Consultas_cont++;
            }elseif ($array[$i][0]==4) {
                $Admin .='<li><a href="'.$array[$i][2].'" class="dropdown-item">'.$array[$i][1].'</a></li>';
                $Admin_cont++;
            }else{
                $Otros .='<li><a href="'.$array[$i][2].'" class="dropdown-item">'.$array[$i][1].'</a></li>';
                $Otros_cont++;
            }
        }

        if ($Recepcion_cont > 0) $Recepcion .= '</ul></li>';
        else $Recepcion = '';

        if ($Produccion_cont > 0) $Produccion .= '</ul></li>';
        else $Produccion = '';

        if ($Consultas_cont > 0) $Consultas .= '</ul></li>';
        else $Consultas = '';

        if ($Admin_cont > 0) $Admin .= '</ul></li>';
        else $Admin = '';

        if ($Otros_cont > 0) $Otros .= '</ul></li>';
        else $Otros = '';

        if ($cont>0) {
            $name = get_info_user($dbConn,$_SESSION['MM_Username']);
            if ($name==false) {
                session_destroy();
                header("Location: ../../login/");
            }else{
                $menu= '<ul class="navbar-nav">
                <li class="nav-item">
                    <a href="http://172.20.134.6/SIF/proceso/" class="nav-link">PROCESO</a>
                </li>'.$Recepcion.$Produccion.$Consultas.$Admin.$Otros.'</ul>';
                echo return_cabecera($menu,$name);
            }
        }else{
            session_destroy();
            header("Location: ../../login/");
        }
    }else {
        session_destroy();
        header("Location: ../../login/");
    }
}


    function return_cabecera($menu,$name){
        global $Ip;
        return '
        <nav class="main-header navbar navbar-expand-lg navbar-dark navbar-navy text-sm p-1">
            <div class="container-fluid">
                <a class="navbar-brand">
                    <img src="../../recursos/Logo-R.png" alt="Rastro Logo" class="brand-image  elevation-3">
                    <span class="brand-text font-weight-light">EMRAQ-EP</span>
                </a>

                <button class="navbar-toggler order-1" type="button" data-toggle="collapse"
                    data-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false"
                    aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse order-3" id="navbarCollapse">'.$menu.'</div>
                <ul class="order-1 order-md-3 navbar-nav navbar-no-expand ml-auto pt-2">
                    <li class="nav-item dropdown user-menu">
                        <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown" style=""
                            aria-expanded="false">
                            <span class="d-none d-md-inline">'.$name[0].'</span>
                            <img src="../../recursos/user-rastro.png" class="user-image img-circle elevation-2"
                                alt="User Image">
                        </a>
                        <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-right"
                            style="left: inherit; right: 0px;">
                            <!-- User image -->
                            <li class="user-header bg-gradient-navy">
                                <img src="../../recursos/user-rastro.png" class="img-circle elevation-2"
                                    alt="User Image">
                                <p>
                                    '.$name[0].'
                                    <small>'.$name[1].' - '.$name[2].'</small>
                                    <small>'.$Ip.'</small>
                                </p>
                            </li>
                            <!-- Menu Footer-->
                            <li class="user-footer">
                                <a href="../../perfil/" class="btn btn-default btn-flat">Perfil</a>
                                <a href="../../FilePHP/cerrar.php" class="btn btn-default btn-flat float-right">Salir</a>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
            </nav>';
    }
    function return_cabecera_proceso($menu,$name){
        global $Ip;
        return '
        <nav class="main-header navbar navbar-expand-lg navbar-dark navbar-navy text-sm p-1">
            <div class="container-fluid">
                <a class="navbar-brand">
                    <img src="../recursos/Logo-R.png" alt="Rastro Logo" class="brand-image  elevation-3">
                    <span class="brand-text font-weight-light">EMRAQ-EP</span>
                </a>

                <button class="navbar-toggler order-1" type="button" data-toggle="collapse"
                    data-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false"
                    aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse order-3" id="navbarCollapse">'.$menu.'</div>
                <ul class="order-1 order-md-3 navbar-nav navbar-no-expand ml-auto pt-2">
                    <li class="nav-item dropdown user-menu">
                        <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown" style=""
                            aria-expanded="false">
                            <span class="d-none d-md-inline">'.$name[0].'</span>
                            <img src="../recursos/user-rastro.png" class="user-image img-circle elevation-2"
                                alt="User Image">
                        </a>
                        <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-right"
                            style="left: inherit; right: 0px;">
                            <!-- User image -->
                            <li class="user-header bg-gradient-navy">
                                <img src="../recursos/user-rastro.png" class="img-circle elevation-2"
                                    alt="User Image">
                                <p>
                                    '.$name[0].'
                                    <small>'.$name[1].' - '.$name[2].'</small>
                                    <small>'.$Ip.'</small>
                                </p>
                            </li>
                            <!-- Menu Footer-->
                            <li class="user-footer">
                                <a href="../perfil/" class="btn btn-default btn-flat">Perfil</a>
                                <a href="../FilePHP/cerrar.php" class="btn btn-default btn-flat float-right">Salir</a>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
            </nav>';
    }


    // FUNCIONES PARA EL PERFIL DE USUARIO
    function data_menu_peril(){
        //
        global $db;
        $dbConn = conectar($db);
        $array = Array_Menu($dbConn,$_SESSION['MM_Username']);
        $Recepcion='<li class="nav-item dropdown">
        <a id="dropdownSubMenu1" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="nav-link dropdown-toggle">Recepción</a>
        <ul aria-labelledby="dropdownSubMenu1" class="dropdown-menu border-0 shadow">';
        $Recepcion_cont=0;
    
        $Produccion='<li class="nav-item dropdown">
        <a id="dropdownSubMenu1" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="nav-link dropdown-toggle">Producción</a>
        <ul aria-labelledby="dropdownSubMenu1" class="dropdown-menu border-0 shadow">';
        $Produccion_cont=0;
    
        $Consultas='<li class="nav-item dropdown">
        <a id="dropdownSubMenu1" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="nav-link dropdown-toggle">Reportes</a>
        <ul aria-labelledby="dropdownSubMenu1" class="dropdown-menu border-0 shadow">';
        $Consultas_cont=0;
    
        $Admin='<li class="nav-item dropdown">
        <a id="dropdownSubMenu1" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="nav-link dropdown-toggle">Administrador</a>
        <ul aria-labelledby="dropdownSubMenu1" class="dropdown-menu border-0 shadow">';
        $Admin_cont=0;
    
        $Otros='<li class="nav-item dropdown">
        <a id="dropdownSubMenu1" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="nav-link dropdown-toggle">Otros</a>
        <ul aria-labelledby="dropdownSubMenu1" class="dropdown-menu border-0 shadow">';
        $Otros_cont=0;
    
        $cont=0;
        $name = get_info_user($dbConn,$_SESSION['MM_Username']);
        if (count($array) > 0) {
            for($i=0;$i<count($array);$i++){
                if ($array[$i][0]==1) {
                    $Recepcion .='<li><a href="'.$array[$i][2].'" class="dropdown-item">'.$array[$i][1].'</a></li>';
                    $Recepcion_cont++;
                }elseif ($array[$i][0]==2) {
                    $Produccion .='<li><a href="'.$array[$i][2].'" class="dropdown-item">'.$array[$i][1].'</a></li>';
                    $Produccion_cont++;
                }elseif ($array[$i][0]==3) {
                    $Consultas .='<li><a href="'.$array[$i][2].'" class="dropdown-item">'.$array[$i][1].'</a></li>';
                    $Consultas_cont++;
                }elseif ($array[$i][0]==4) {
                    $Admin .='<li><a href="'.$array[$i][2].'" class="dropdown-item">'.$array[$i][1].'</a></li>';
                    $Admin_cont++;
                }else{
                    $Otros .='<li><a href="'.$array[$i][2].'" class="dropdown-item">'.$array[$i][1].'</a></li>';
                    $Otros_cont++;
                }
            }
            if ($Recepcion_cont > 0) $Recepcion .= '</ul></li>';
            else $Recepcion = '';
    
            if ($Produccion_cont > 0) $Produccion .= '</ul></li>';
            else $Produccion = '';
    
            if ($Consultas_cont > 0) $Consultas .= '</ul></li>';
            else $Consultas = '';
    
            if ($Admin_cont > 0) $Admin .= '</ul></li>';
            else $Admin = '';
    
            if ($Otros_cont > 0) $Otros .= '</ul></li>';
            else $Otros = '';

            if ($name==false) {
                session_destroy();
                header("Location: ../login/");
            }else{
                $menu= '<ul class="navbar-nav">
                <li class="nav-item">
                    <a href="http://172.20.134.6/SIF/proceso/" class="nav-link">PROCESO</a>
                </li>'.$Recepcion.$Produccion.$Consultas.$Admin.$Otros.'</ul>';
                echo return_cabecera_user($menu,$name);
            }
        }else {
            $menu= '<ul class="navbar-nav">
                <li class="nav-item">
                    <a href="http://172.20.134.6/SIF/proceso/" class="nav-link">PROCESO</a>
                </li></ul>';
            echo return_cabecera_user($menu,$name);
        }
    }
    function data_menu_peril_proceso(){
        //
        global $db;
        $dbConn = conectar($db);
        $array = Array_Menu($dbConn,$_SESSION['MM_Username']);
        $Recepcion='<li class="nav-item dropdown">
        <a id="dropdownSubMenu1" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="nav-link dropdown-toggle">Recepción</a>
        <ul aria-labelledby="dropdownSubMenu1" class="dropdown-menu border-0 shadow">';
        $Recepcion_cont=0;
    
        $Produccion='<li class="nav-item dropdown">
        <a id="dropdownSubMenu1" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="nav-link dropdown-toggle">Producción</a>
        <ul aria-labelledby="dropdownSubMenu1" class="dropdown-menu border-0 shadow">';
        $Produccion_cont=0;
    
        $Consultas='<li class="nav-item dropdown">
        <a id="dropdownSubMenu1" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="nav-link dropdown-toggle">Reportes</a>
        <ul aria-labelledby="dropdownSubMenu1" class="dropdown-menu border-0 shadow">';
        $Consultas_cont=0;
    
        $Admin='<li class="nav-item dropdown">
        <a id="dropdownSubMenu1" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="nav-link dropdown-toggle">Administrador</a>
        <ul aria-labelledby="dropdownSubMenu1" class="dropdown-menu border-0 shadow">';
        $Admin_cont=0;
    
        $Otros='<li class="nav-item dropdown">
        <a id="dropdownSubMenu1" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="nav-link dropdown-toggle">Otros</a>
        <ul aria-labelledby="dropdownSubMenu1" class="dropdown-menu border-0 shadow">';
        $Otros_cont=0;
    
        $cont=0;
        $name = get_info_user($dbConn,$_SESSION['MM_Username']);
        if (count($array) > 0) {
            for($i=0;$i<count($array);$i++){
                if ($array[$i][0]==1) {
                    $Recepcion .='<li><a href="'.$array[$i][2].'" class="dropdown-item">'.$array[$i][1].'</a></li>';
                    $Recepcion_cont++;
                }elseif ($array[$i][0]==2) {
                    $Produccion .='<li><a href="'.$array[$i][2].'" class="dropdown-item">'.$array[$i][1].'</a></li>';
                    $Produccion_cont++;
                }elseif ($array[$i][0]==3) {
                    $Consultas .='<li><a href="'.$array[$i][2].'" class="dropdown-item">'.$array[$i][1].'</a></li>';
                    $Consultas_cont++;
                }elseif ($array[$i][0]==4) {
                    $Admin .='<li><a href="'.$array[$i][2].'" class="dropdown-item">'.$array[$i][1].'</a></li>';
                    $Admin_cont++;
                }else{
                    $Otros .='<li><a href="'.$array[$i][2].'" class="dropdown-item">'.$array[$i][1].'</a></li>';
                    $Otros_cont++;
                }
            }
            if ($Recepcion_cont > 0) $Recepcion .= '</ul></li>';
            else $Recepcion = '';
    
            if ($Produccion_cont > 0) $Produccion .= '</ul></li>';
            else $Produccion = '';
    
            if ($Consultas_cont > 0) $Consultas .= '</ul></li>';
            else $Consultas = '';
    
            if ($Admin_cont > 0) $Admin .= '</ul></li>';
            else $Admin = '';
    
            if ($Otros_cont > 0) $Otros .= '</ul></li>';
            else $Otros = '';

            if ($name==false) {
                session_destroy();
                header("Location: ../login/");
            }else{
                $menu= '<ul class="navbar-nav">
                <li class="nav-item">
                    <a href="http://172.20.134.6/SIF/proceso/" class="nav-link">PROCESO</a>
                </li>'.$Recepcion.$Produccion.$Consultas.$Admin.$Otros.'</ul>';
                echo return_cabecera_proceso($menu,$name);
            }
        }else {
            $menu= '<ul class="navbar-nav">
                <li class="nav-item">
                    <a href="http://172.20.134.6/SIF/proceso/" class="nav-link">PROCESO</a>
                </li></ul>';
            echo return_cabecera_proceso($menu,$name);
        }
    }
    
    function return_cabecera_user($menu,$name){
        global $Ip;
        return '
        <nav class="main-header navbar navbar-expand-md navbar-dark navbar-navy text-sm p-1">
            <div class="container-fluid">
                <a class="navbar-brand">
                    <img src="../recursos/Logo-R.png" alt="Rastro Logo" class="brand-image  elevation-3">
                    <span class="brand-text font-weight-light">EMRAQ-EP</span>
                </a>
    
                <button class="navbar-toggler order-1" type="button" data-toggle="collapse"
                    data-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false"
                    aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
    
                <div class="collapse navbar-collapse order-3" id="navbarCollapse">'.$menu.'</div>
            </div>
            </nav>';
    }

?>