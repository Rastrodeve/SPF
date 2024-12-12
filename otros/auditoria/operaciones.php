<?php
if (isset($_REQUEST['op'])) {
	require '../../FilePHP/utils.php';
	$dbConn = conectar($db);
	$op=$_REQUEST['op'];
	if ($op==1) echo select_data_especies($dbConn);
	else if ($op==2) echo f_data_table($dbConn);
    
}else header("location: ./");

function select_data_especies($dbConn){
    $resultado='<option value="0">Todas las especies</option>';
    $consulta="SELECT * FROM tbl_a_especies ";
	$sql= $dbConn->prepare($consulta);
	$sql->execute();
    $cont = 0;
	while($row = $sql->fetch()) {
        $resultado.='<option value="'.$row["espId"].'" >'.utf8_encode($row["espDescripcion"]).'</option>';
	}
    return  $resultado;
}
function f_data_table($dbConn){
    $table = '<table class="table table-bordered table-striped table-sm" id="table-data">
    <thead>
        <tr>
            <th class="d-none">n</th>
            <th>N° Guía</th>
            <th>N° Machos</th>
            <th>N° Hembras</th>
            <th>A. Muertos</th>
            <th>F. Normal</th>
            <th>S. Urgencia</th>
            <th>S. Sanitario</th>
        </tr>
    </thead>
    <tbody>';
    $especie = '';
    if ($_POST["Id"] != 0) $especie = 'AND espId = '.$_POST["Id"];
    $consulta="SELECT g.guiNumero, p.gprMacho, p.gprHembra, p.gprId 
    FROM tbl_r_guiaproceso p, tbl_r_guiamovilizacion g
    WHERE p.guiId = g.guiId AND p.gprTurno BETWEEN :inicio  AND :final AND p.gprEliminado = 0  ".$especie." ORDER BY p.gprTurno ASC";
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
            <td>'.$row["guiNumero"].'</td>  
            <td>'.$row["gprMacho"].'</td>  
            <td>'.$row["gprHembra"].'</td>  
            <td>1</td>  
            <td>1</td>  
            <td>1</td>  
            <td>1</td>  
        </tr>';
	}
    
    return  $table.'</tbody></table>';
}
function transformar_fecha($fecha){
    $array = explode("/",$fecha);
    return $array[2]."-".$array[1]."-".$array[0];
}
?>