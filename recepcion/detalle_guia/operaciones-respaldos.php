<?php
if (isset($_REQUEST['op'])) {
	require '../../FilePHP/utils.php';
	$dbConn = conectar($db);
	$op=$_REQUEST['op'];
	if ($op==1)echo Listar_Guias($dbConn);
	else if ($op==2)echo GuardarDetalle($dbConn);
	else if ($op==3)echo Comprobar_Codigo($dbConn);
	else if ($op==4)echo Consultar_Fecha();
}else{
	header('location: ../');
}
function Listar_Guias($dbConn){
	$resultado=' <table id="tbl_guia_proceso" class="table table-bordered table-striped">
              <thead>
                <tr>
                  <th>N° DE GUÍA</th>
                  <th>RUC</th>
                  <th>CLIENTE</th>
                  <th>TIPO</th>
                  <th>CANTIDAD</th>
                  <th>FECHA PROCESO</th>
                  <th class="d-none">Observaciones</th>
                  <th>SELECCIONAR</th>
                </tr>
              </thead>
              <tbody>';
    $consulta="SELECT p.guia_numero,p.ruc,c.apellidos,c.nombres,c.observaciones,e.tipo,p.cantidad,p.fecha_proceso FROM tbl_guia_proceso p JOIN tbl_clientes c ON p.ruc=c.ruc JOIN tbl_especieanimales e ON p.tipo=e.codigo_especieanimales WHERE p.procesado=0 AND p.detallada=0 ORDER BY p.fecha_proceso DESC";
	$sql= $dbConn->prepare($consulta);
	$sql->execute();
	$cont = 0;
	while ($row = $sql->fetch()) {
		$cont++;
		$resultado=$resultado . "
	    	<tr id='tr-".$cont."'>
	          <td>".$row['guia_numero']."</td>
	          <td>".$row['ruc']."</td>
	          <td>".$row['apellidos']." ".$row['nombres']."</td>
	          <td>".$row['tipo']."</td>
	          <td>".$row['cantidad']."</td>
	          <td>".$row['fecha_proceso']."</td>
	          <td class='d-none'>".$row['observaciones']."</td>
	          <td><button class='btn btn-info' onclick='Mostrar(".$cont.")'><b>SELECIONAR</b></button></td>
	        </tr>";
	}
	$encabezado='<div class="col-md-12 text-center"><h4>RESULTADOS <b>'.$cont.'</b></h4></div>';
	return $resultado."</tbody></table>";
   }
function GuardarDetalle($dbConn){
	$code = $_POST['Codigo'];
	$peso = $_POST['Peso'];
	$guia = $_POST['NGuia'];
	$Fecha = $_POST['Fecha'];
	$consulta = "INSERT INTO tbl_detalle_guia_new(dtCodigo,dtPeso,dtFecha,guia_numero)
	 VALUES(:code,:peso,:fecha,:guia)";
	$sql= $dbConn->prepare($consulta);
	$sql->bindValue(':code',$code);
	$sql->bindValue(':peso',$peso);
	$sql->bindValue(':fecha',$Fecha);
	$sql->bindValue(':guia',$guia);
	if ($result = $sql->execute()){
		if ($r = Update_Guia_Proceso($dbConn,$guia))return true;
		else return $r;
	}else return $result;
}

function Update_Guia_Proceso($dbConn,$guia){
	$consulta = "UPDATE tbl_guia_proceso SET detallada=1 WHERE guia_numero=:guia AND procesado=0";
	$sql= $dbConn->prepare($consulta);
	$sql->bindValue(':guia',$guia);
	if ($result = $sql->execute()) return true;
	else return $result;
}
function Comprobar_Codigo($dbConn){
	$comp=false;
	$code = $_POST["Code"];
	$consulta="SELECT dtCodigo FROM tbl_detalle_guia_new WHERE dtCodigo=:code AND dtProceso=0";
	$sql= $dbConn->prepare($consulta);
	$sql->bindValue(':code',$code);
	$sql->execute();
	if($row = $sql->fetch()) {
		$comp= true;
	}
	return $comp;
}
function Consultar_Fecha(){
	return date('Y-m-7 H:i:s');
}

?>