<?php
if (isset($_REQUEST["op"])) {
	$usuario = "root";
	$Pass="T3cn0l0g14*15";
	$op = $_REQUEST["op"];
	if ($op==1)echo Lista_Tablas($usuario,$Pass);
	elseif ($op==2)echo Ejecutar_Consula($usuario,$Pass);
}
function Lista_Tablas($usuario,$Pass){
	$res="";
	$nombre_bd = $_POST['DB'];
	if (!mysql_connect('localhost', $usuario,$Pass)) {
		$res='No se pudo conectar a mysql';
	}else{
		$sql = "SHOW TABLES FROM $nombre_bd";
		$resultado = mysql_query($sql);

		if (!$resultado) {
			$res="Error de BD, no se pudieron listar las tablas<br>";
			$res='Error MySQL: ' . mysql_error();
		}

		while ($fila = mysql_fetch_row($resultado)) {

		$res= $res.'<span class="badge badge-pill badge-light my-span">
                      <b>'.$fila[0].'</b>
                    </span>';
		}
		mysql_free_result($resultado);
	}
	return $res;
}
function Ejecutar_Consula($usuario,$Pass){
	$nombre_bd = $_POST['DB'];
	$enlace = mysqli_connect("localhost", $usuario, $Pass, $nombre_bd);
	/* comprobar la conexión */
	if (mysqli_connect_errno()) {
	    printf("Falló la conexión:<br>", mysqli_connect_error());
	    exit();
	}

	    // mysqli_free_result($resultado);

	  //Activamos todas las notificaciones de error posibles
	  error_reporting(E_ALL);
	 
	  //Definimos el tratamiento de errores no controlados
	  set_error_handler(function () {
	    throw new Exception("Error");
	  });
	  try {
	  		$consulta = $_POST["Consulta"];
     Insertar_Sentencia($enlace,$consulta);
			$resultado = mysqli_query($enlace, $consulta);
			if ($resultado) {
		    		$cont1 = mysqli_num_rows($resultado);
		    		if ($cont1 > 0) {
			    		$info_campo = mysqli_fetch_fields($resultado);
				        $cont=0;
				        echo '<table id="tbl_guia_detalle" class="table table-bordered table-striped"><thead><tr>';
				        foreach ($info_campo as $valor) {
				            $cont++;
				            echo "<th>".utf8_decode($valor->name)."</th>";
				        }
				        echo "</tr></thead><tbody>";
				        while ($row = $resultado->fetch_row()) {
				            echo "<tr>";
				            for ($i=0; $i <$cont ; $i++) { 
				                echo "<td>".utf8_decode($row[$i])."</td>";
				            }
				            echo "</tr>";
				        }
				        echo "</tbody></table>";
			    	}
		    }else{
		    	printf("Error: %s",mysqli_error($enlace));
		    }

	  	} 
	  catch(Exception $e){ //capturamos un posible error
	    //mostramos el texto del error al usuario	  
	    echo "Columnas afectadas ".mysqli_affected_rows($enlace)."<br>";
	    echo "Error:" .$e;
	    $resultado = 0;
	  }finally{
	    mysqli_close($enlace);
	  }
	 	
	  	//Restablecemos el tratamiento de errores
	  	restore_error_handler();
}

function Insertar_Sentencia($enlace,$sql){
	session_start();
	$user = $_SESSION['MM_Username'];
	$text = "SENTENCIA MYSQL";
	$consulta = "INSERT INTO 
	tbl_auditoria(usuario,ip,fecha_accion,detalle_accion,registro_afectado,
	tabla_afectada)
	VALUES ('".$user."','".$_SERVER['REMOTE_ADDR']."','".date("Y-m-d H:i:s")."','".$text."','00','".$sql."')";
	$resultado = mysqli_query($enlace,$consulta);

}

 
?>