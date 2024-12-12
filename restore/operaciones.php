<?php
if (isset($_REQUEST["op"])) {
	require '../FilePHP/menu.php';
	$dbConn = conectar($db);
	$op=$_REQUEST['op'];
	if ($op==1)echo update_pass($dbConn);
}
function update_pass($dbConn){
    $pass= trim($_POST["Password"]);
    $consulta1="UPDATE tbl_a_usuarios SET usuPasswd = :pass, usuEstado_pass = 0
    WHERE usuId = :id";
    $sql1= $dbConn->prepare($consulta1);
    $sql1->bindValue(':pass',md5($pass));
    $sql1->bindValue(':id',$_SESSION["MM_Username"]);
    if ($sql1->execute()){
        $Acion = 'Cambio de contraseña';
        $detalle = "Cambio personal de contraseña";
        if(Insert_Login($_SESSION["MM_Username"],'tbl_a_usuarios',$Acion,$detalle,''))return true;
        else return "ERROR-654552";//ERROR AL AGREGAR EL LOGS
    }
    else return "ERROR-665242";//NO SE PUEDO ACTUALIZAR LOS DATOS
}


?>