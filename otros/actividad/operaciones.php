<?php
if (isset($_REQUEST['op'])) {
	require '../../FilePHP/utils.php';
	$dbConn1 = conectar($db);
	$dbConn2 = conectar($dblLOGView);
	$op=$_REQUEST['op'];
	if ($op==1) echo select_data($dbConn1);
	else if ($op==2) echo f_data_table($dbConn2,$dbConn1);
    
}else header("location: ./");

function select_data($dbConn){
    $resultado='<option value="0">Todas los usuarios</option>';
    $consulta="SELECT usuId ,usuNombre  FROM tbl_a_usuarios";
	$sql= $dbConn->prepare($consulta);
	$sql->execute();
    $cont = 0;
	while($row = $sql->fetch()) {
        $resultado.='<option value="'.$row["usuId"].'" >'.utf8_encode($row["usuNombre"]).'</option>';
	}
    return  $resultado;
}
function f_data_table($dbConn,$dbConn1){
    $table = '<table class="table table-bordered table-striped table-sm" id="table-data">
    <thead>
        <tr>
            <th class="d-none">#</th>
            <th>Fecha</th>
            <th>Usuario</th>
            <th>Ip</th>
            <th>Acción</th>
            <th>Detalle</th>
            <th>Comentario</th>
        </tr>
    </thead>
    <tbody>';
    $usuario = '';
    if ($_POST["Id"] != 0) $usuario = ' AND  usuId = '.$_POST["Id"];
    $Ip='';
    if (trim($_POST["IpUsu"]) != '') $Ip = ' AND  logIp = \''.trim($_POST["IpUsu"]).'\'';
    $Accion='';
    if (trim($_POST["Accion"]) != '') $Accion = ' AND  logAccion LIKE \'%'.trim($_POST["Accion"]).'%\'';
    $Detalle='';
    if (trim($_POST["Detalle"]) != '') $Detalle = ' AND  logDetalle LIKE \'%'.trim($_POST["Detalle"]).'%\'';
    
    $Comentario='';
    if (trim($_POST["Comentario"]) != '') $Comentario = ' AND  logComentario LIKE \'%'.trim($_POST["Comentario"]).'%\'';

    $consulta="SELECT * FROM tbl_log WHERE logFecha BETWEEN :inicio AND :final ".$usuario.$Ip.$Accion.$Detalle.$Comentario.' ORDER BY logFecha DESC';
	$sql= $dbConn->prepare($consulta);
    $sql->bindValue(':inicio',transformar_fecha($_POST["Inicio"]).' 00:00:00');
    $sql->bindValue(':final',transformar_fecha($_POST["Final"]).' 23:59:59');
	$sql->execute();
    $cont= 0;
	while($row = $sql->fetch()) {
        $cont++;
        $table .= '
        <tr>
            <td class="d-none">'.$cont.'</td>  
            <td>'.$row["logFecha"].'</td>  
            <td>'.f_get_data_user($dbConn1,$row["usuId"]).'</td>  
            <td>'.$row["logIp"].'</td>  
            <td>'.utf8_encode($row["logAccion"]).'</td>  
            <td>'.utf8_encode($row["logDetalle"]).'</td>  
            <td>'.utf8_encode($row["logComentario"]).'</td>  
        </tr>';
	}
    
    return  $table.'</tbody></table>';
}
function f_get_data_user($dbConn,$user){
    $consulta="SELECT usuNombre  FROM tbl_a_usuarios WHERE usuId= :id";
	$sql= $dbConn->prepare($consulta);
    $sql->bindValue(':id',$user);
	$sql->execute();
	if($row = $sql->fetch()) return utf8_encode($row["usuNombre"]);
	else return 'ERROR-2212';
}
function transformar_fecha($fecha){
    $array = explode("/",$fecha);
    return $array[2]."-".$array[1]."-".$array[0];
}
?>