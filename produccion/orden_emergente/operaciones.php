<?php
if (isset($_REQUEST["op"])) {
	require '../../FilePHP/utils.php';
	$dbConn = conectar($db);
	$op=$_REQUEST['op'];
	if ($op==1)echo Listar_Especies($dbConn);
	elseif ($op==2)echo f_vista_previa($dbConn);
	elseif ($op==3)echo ListaTazas($dbConn);
	elseif ($op==4)echo f_insert_orden($dbConn);
	elseif ($op==5)echo view_cliente_guia($dbConn);
	elseif ($op==6)echo view_detalle_guia($dbConn);
	elseif ($op==7){
        echo Numero_Ordinal($dbConn,1);
    }
}else{
}

// Opcion 1 -- Funcion que lista las espcies del select
	function Listar_Especies($dbConn){
		$resultado='';
        $consulta="SELECT * FROM tbl_a_especies WHERE espEstado = 0";
        $sql= $dbConn->prepare($consulta);
        $sql->execute();
        $cont = 0;
        while($row = $sql->fetch()) {
            if( comprobar_servicios($dbConn,$row["espId"]) > 0){
                if (comprobar_productos($dbConn,$row["espId"]) > 0) {
                    $resultado.='<option value="'.$row["espId"].'" >'.utf8_encode($row["espDescripcion"]).'</option>';
                }
            }
        }
        return  $resultado;
	}
    function comprobar_servicios($dbConn,$Id){
        $consulta="SELECT * FROM tbl_a_servicios WHERE espId = :id  AND srnEstado = 0";
        $sql= $dbConn->prepare($consulta);
        $sql->bindValue(':id',$Id);
        $sql->execute();
        $cont = 0;
        while($row = $sql->fetch()) {
            $cont++;
        }
        return $cont;
    }
    function comprobar_productos($dbConn,$Id){
        $consulta="SELECT * FROM tbl_a_productos WHERE espId = :id  AND proEliminado = 0";
        $sql= $dbConn->prepare($consulta);
        $sql->bindValue(':id',$Id);
        $sql->execute();
        $cont = 0;
        while($row = $sql->fetch()) {
            $cont++;
        }
        return $cont;
    }

// Opcion 2 -- Funcion que Crea la Vista Previa de la orden de produccion
    function f_vista_previa($dbConn){
		if (comprobar_yupak($dbConn)==false) return '<h6 class="text-center" style="cursor:pointer;" onclick="recargar_vista_previa()">
		No se pudo generar la vista previa de la <b>Orden de Producción</b> debido a que <b>no se encontro las Especificaciones YUPAK</b></h6>';
        $especie = $_POST["Tipo"];
        $arrayServicios = Busqueda_Servicios($dbConn,$especie);// Id, Precio, Codigo Yupak, Descripcion
		if (count($arrayServicios)==0)return '<h6 class="text-center" style="cursor:pointer;" onclick="recargar_vista_previa()">
		No se pudo generar la vista previa de la <b>Orden de Producción</b> debido a que <b>no se encontraron servicios </b></h6>';
		$comprobar_codigo_yupak= '';
		$contador_codigo_yupak = 0;
		for ($i=0; $i < count($arrayServicios) ; $i++) { 
			if (f_descripcion_yupak($arrayServicios[$i][2])==false) {
				$contador_codigo_yupak++;
				$comprobar_codigo_yupak .= 'Servicio: '.utf8_encode($arrayServicios[$i][3]).'-> Codigo Yupak: '.$arrayServicios[$i][2].'<br>';
			}
		}
		if ($contador_codigo_yupak > 0) return '<h6 class="text-center" style="cursor:pointer;" onclick="recargar_vista_previa()">
		No se pudo generar la vista previa de la <b>Orden de Producción</b> debido a que el <b>código yupak</b> de los siguientes servicios <b> es incorrecto </b><br>'.$comprobar_codigo_yupak.'</h6>';
        $Cabecera = DataEncabezado($arrayServicios);
		$cont =0; // Contador que indicara el turno de faenamiento
		$Subtotal1Global = 0;//Se almacena la suma de todos los Subtotal1 (Servicios)
		$CantidaGlobal = 0;//Se almacena la suma de las cantidades
		$SubtotalCorralaje=0;//Se almacena la suma total de todos los subtotal2 (corralaje)
		$TotalPagarGlobal=0;//Se almacena la suma total de todos los total a pagar
		$ATotalServicios=[];//Array que almacena la suma de los servicios
		$Resultado = "";
		$Ganado = "RECARGAR";
		for ($i=0; $i < count($arrayServicios); $i++) {//se crea los array necesarios para los servicios
			$ATotalServicios[$i]=0;//Se declara en cero
		}
        $consulta="SELECT p.gprId,p.gprComprobante,(p.gprMacho + p.gprHembra) AS gprCantidad, p.gprestadoDetalle, p.gprTurno,
                        c.cliNombres,c.cliNumero,
                        e.espId, e.espEstanciaPermitida ,e.espEstadoCorralaje, e.espEstanciaCorralaje, e.espPrecioCorralaje, e.espDescripcion, e.espCodigoYupak 
                FROM tbl_r_guiaproceso p, tbl_a_clientes c, tbl_a_especies e 
                WHERE p.espId = e.espId AND c.cliId = p.cliId  AND p.espId = :id AND p.gprEliminado = 0 
                ORDER BY p.gprTurno , p.gprComprobante ASC ";
        $sql= $dbConn->prepare($consulta);
        $sql->bindValue(':id',$especie);
        $sql->execute();
        while ($row = $sql->fetch()) {
            if(!is_numeric($row["cliNumero"]))return '<h6 class="text-center" style="cursor:pointer;" onclick="recargar_vista_previa()">
                            No se pudo generar la vista previa de la <b>Orden de Producción</b> debido a que el introductor <b>'.utf8_encode($row["cliNombres"]).'</b> tiene su número de identificacion incorrecto  </b><br>Ruc o Cedula: '.$row["cliNumero"].'</h6>';
            if (strlen($row["cliNumero"]) == 10 || strlen($row["cliNumero"]) == 13);
            else return '<h6 class="text-center" style="cursor:pointer;" onclick="recargar_vista_previa()">
                            No se pudo generar la vista previa de la <b>Orden de Producción</b> debido a que el introductor <b>'.utf8_encode($row["cliNombres"]).'</b> tiene su número de identificacion incorrecto  </b><br>Ruc o Cedula: '.$row["cliNumero"].'</h6>';
            $array = explode(":",$row["espEstanciaPermitida"]);
            if (count($array)==3) {
                if (is_numeric($array[0]) && is_numeric($array[1]) && is_numeric($array[2])) {
                    $estancia_enviar =  intval($array[0]).":".intval($array[1]).":".intval($array[2]);
                    $td = "";
                    $FechaIngreso = $row["gprTurno"];
                    if ($row["gprestadoDetalle"] == 1){//0 No se detalla; 1 Se detalla
                        $FechaIngreso = Fecha_Ingreso_Detallada($dbConn,$row["gprId"]);//"Otras Funciones 1" /// 
                        $td = "*"; // Cuando las guias son detalladas por animal aparece en un *
                    }
                    $procesado = f_obtener_procesar($dbConn,$row["gprId"]); // 0
                    $cant = f_obtener_cantidades($dbConn,$row["gprId"]);
                    $saldo = (intval($cant[1]) + intval($cant[2])) -  intval($procesado); //24
                    if ($saldo > 0) {
                        // SERVICIOS 
                        if (comprobar_orden($dbConn,$row["gprId"])) {
                            // $Servicios .= '<td colspan="'.count($arrayServicios).'" class="text-center" style="cursor:pointer;"><b>RECAUDADO</b></td>';
                        }else{
                            $Ganado = utf8_encode($row["espDescripcion"]);
                            $cont++;// CONTADOR 
                            $Servicios = '';
                            $Subtotal1= 0;
                            $totalPagar = 0;
                            for ($i=0; $i < count($arrayServicios) ; $i++) { 
                                $total_servicio = $arrayServicios[$i][1] * $saldo;
                                $Subtotal1 += $total_servicio;
                                $Subtotal1Global += $total_servicio;
                                $ATotalServicios[$i] += $total_servicio;
                                $Servicios .= '<td class="text-right" style="cursor:pointer;">$ '.number_format($total_servicio, 2).'</td>';
                            }
                            //CORRALAJE
                            $arraycorral = explode(":",$row["espEstanciaCorralaje"]);
                            if (count($arraycorral) != 3) return '<h6 class="text-center" style="cursor:pointer;" onclick="recargar_vista_previa()">
                            No se pudo generar la vista previa de la <b>Orden de Producción</b> debido a que la estructura del <b>Tiempo de corralaje es incorrecta </b><br>Tiempo de corralaje : "'.$row["espEstanciaCorralaje"].'"</h6>';
                            $estancia_corral= "0:0:0";
                            if (is_numeric($arraycorral[0]) && is_numeric($arraycorral[1]) && is_numeric($arraycorral[2])) $estancia_corral = intval($arraycorral[0]).":".intval($arraycorral[1]).":".intval($arraycorral[2]);
                            else return '<h6 class="text-center" style="cursor:pointer;" onclick="recargar_vista_previa()">
                            No se pudo generar la vista previa de la <b>Orden de Producción</b> debido a que la estructura del <b>Tiempo de corralaje es incorrecta </b><br>Tiempo de corralaje : "'.$row["espEstanciaCorralaje"].'"</h6>';
                            if (f_descripcion_yupak($row["espCodigoYupak"])==false) return '<h6 class="text-center" style="cursor:pointer;" onclick="recargar_vista_previa()">
                            No se pudo generar la vista previa de la <b>Orden de Producción</b> debido a que el Código del servicio de <b>CORRALAJE </b> de YUPAK es incorrecto <br>Código corralaje Yupak : "'.$row["espCodigoYupak"].'"</h6>';

                            $TiempoEstancia= Calcular_Estancia($FechaIngreso,date("Y-m-d H:i:s"));//"Otras funciones 2"
                            $ArrayCorralaje = Calcular_Corralaje($estancia_corral,$row["espPrecioCorralaje"],$TiempoEstancia);//"Funcion 6" Retorna el [pecio total de cobro] y [ tiempo a cobrar]"
                            $total_corralaje = 0;
                            $Corralaje = '<td >'.$TiempoEstancia.'</td> <td class="table-success text-right"><b>DESACTIVADO</b></td>';
                            if ($row["espEstadoCorralaje"] == 1 ) {//1 El corralaje esta activado
                                $total_corralaje = $ArrayCorralaje[0] * $saldo;
                                $SubtotalCorralaje += $total_corralaje;
                                $Corralaje = '<td >'.$TiempoEstancia.'</td> <td class="table-success text-right"><b>$ '.number_format($total_corralaje, 2).'</b></td>';
                            }
                            $totalPagar = $Subtotal1 + $total_corralaje;
                            $TotalPagarGlobal += $totalPagar;
                            $CantidaGlobal += $saldo;
                            $Resultado .='<tr>
                                <th>'.$td.$cont.'</th>
                                <td  class="text-left" style="cursor:pointer;">
                                '.utf8_encode($row["cliNombres"]).'
                                </td>
                                <td class="table-success" style="cursor:pointer;" >
                                    <b>'.$saldo.'</b>
                                </td>
                                '.$Servicios.'
                                <td class="table-success text-right" ><b>$ '.number_format($Subtotal1, 2).'</b></td>
                                '.$Corralaje.'
                                <td class="table-success text-right"><b>$ '.number_format($totalPagar, 2).' </b></td>
                            </tr>';
                        }
                        }
                } else{
                    return '<h6 class="text-center" style="cursor:pointer;" onclick="recargar_vista_previa()">
						No se pudo generar la vista previa de la <b>Orden de Producción </b> debido a que la estructura del <b>Tiempo Minimo es incorrecta </b><br>Tiempo Minimo : "'.$row["espEstanciaPermitida"].'"</h6>';
                }
            }else{
                return '<h6 class="text-center" style="cursor:pointer;" onclick="recargar_vista_previa()">
						No se pudo generar la vista previa de la <b>Orden de Producción</b> debido a que la estructura del <b>Tiempo Minimo es incorrecta </b><br>Tiempo Minimo : "'.$row["espEstanciaPermitida"].'"</h6>';
            }
        }
		$button ='<button class="btn btn-danger mt-3 "  id="btn-generar" onclick="Generar()">
				<b>GENERAR ORDEN EMERGENTE</b>
		</button>';

		$footer="";//Declaracion de la variable footer que almacena todos los resultados globales
		if ($cont > 0) {//Muetsra el footer y el boton si el contador es mayor a 0
			$FtotalesServicios = "";//Se almacena todos los resultados glables de los servicos
			for ($i=0; $i < count($arrayServicios); $i++) {
				$FtotalesServicios = $FtotalesServicios .'<th class="text-right ">$ '.number_format($ATotalServicios[$i], 2).'</th>';
			}
			$CorralTh='<th class=" table-success text-right" colspan="2" >$ '.number_format($SubtotalCorralaje, 2).'</th>';//Se declara el corralaje como No dispobile
			$footer = '<tfoot>
				<tr>
					<th colspan="2" >TOTALES</th>
					<th class="table-success">'.$CantidaGlobal.'</th>
					'.$FtotalesServicios.'
					<th class="text-right table-success">$ '.number_format($Subtotal1Global, 2).'</th>
					'.$CorralTh.'
					<th class="text-right table-success">$ '.number_format($TotalPagarGlobal, 2).'</th>
				</tr>
			</tfoot>';
		}
		$titulo = '<h5 class="mb-2 text-center" style="cursor:pointer;" onclick="recargar_vista_previa()">
							<b>'.strtoupper($Ganado).'</b> TOTAL EMERGENTE A PROCESAR:  <b style="font-size:23px;">'.$CantidaGlobal	.'</b>
							</h5>'; 
		$Tabla = '<table id="tbl_view_orden" class="table table-bordered table-striped table-sm table-hover text-center"> '.$Cabecera.'<tbody>'.$Resultado.'</tbody>'.$footer.'</table>';
		return $titulo.$Tabla.$button;
    }
	
// Funciones Necesarias para Vista Previa de la Orden de Produccion
// "Funcion 1" fucion que trae el precio y descripcon de los servicios
	function Busqueda_Servicios($dbConn,$tipo){
        $resultado=[];
        $consulta="SELECT * FROM tbl_a_servicios WHERE srnEstado = 0 AND espId = :id";
        $sql= $dbConn->prepare($consulta);
        $sql->bindValue(':id',$tipo);
        $sql->execute();
        $cont = 0;
        while($row = $sql->fetch()) {
            array_push($resultado,[$row["srnId"],$row["srnPrecio"],$row["srnCodigoYupak"],$row["srnDescripcion"]]);
        }
        return  $resultado;
	}
// "Funcion 2" funcion que Retorna el encabezado de la tabla
	function DataEncabezado($arrayServicios){
		$Total_Servicios = count($arrayServicios);//Se calcula el tamaño del array
		$Servicios ="";//Variable donde se alacenaran los th con los nombres de los servicios
		for ($i=0; $i < $Total_Servicios; $i++) {//El inico del cilo empieza en "1" debido que el primer campo del array es un "/"
			$Servicios = $Servicios."<th >".utf8_encode($arrayServicios[$i][3])."</th>";//Se guarda en la variable el th con la descripcion
		}
		// La variable "$Total_Servicios" ayuda a definir el numero de columnas que se van a unir para formar el apartado de servicios
		return '<thead>
			<tr style="text-align:center;">
				<th colspan="3" class="table-success">DATOS GENERALES</th>
				<th colspan="'.($Total_Servicios + 1).'" class="table-success">SERVICIOS</th>
				<th colspan="2" class="table-success">CORRALAJE</th>
				<th rowspan="2" class="table-success" style="max-width:60px;">TOTAL</th>
			</tr>
			<tr>
				<th class="table-success" style="max-width:20px;">#</th>
				<th class="table-success" style="min-width:200px;">CLIENTE</th>
				<th class="table-success" style="max-width:35px;">CANT.</th>
				'.$Servicios.'
				<th class="table-success" style="max-width:70px;">Subtotal 1</th>
				<th style="max-width:100px;">Tiempo de Estancia</th>
				<th class="table-success" style="max-width:70px;">Subtotal 2</th>
			</tr>
		</thead>';
	}
// "Funcion 3" funcion que comprueba las especificaciones YUPAK
	function comprobar_yupak($dbConn){
		$consulta="SELECT * FROM tbl_a_especificacionesyp ORDER BY espyId ASC LIMIT 1";
		$sql= $dbConn->prepare($consulta);
		$sql->execute();
		if($row = $sql->fetch()) return [$row["espyCodigo_Empresa"],$row["espyLocalidad"],$row["espyCaja"]];
		else return false;
	}
// "Funcion 4" funcion que Retorna True si el animal el tiempo minimo y False si aun no alcanza el tiempo minimo
	function f_tiempo_minimo($FechaIngreso,$estado,$FechaPermitida){
		$FechaActual = date("Y-m-d H:i:s");//Variable que almacena la fecha actual
		$TiempoEstancia= Calcular_Estancia($FechaIngreso,$FechaActual);//"Otras funciones 2"
		$ArrayTiempoEstancia = explode(":",$TiempoEstancia);
		$ArrayFechaPermitida = explode(":",$FechaPermitida);
		if ($ArrayTiempoEstancia[0] > $ArrayFechaPermitida[0]) {//Si el tiempo de estancia es mayor al tiempo permitido se muestra la guia
			return true;
		}elseif ($ArrayTiempoEstancia[0] == $ArrayFechaPermitida[0]){//Si el tiempo de estancia es igual al tiempo permitido se muestra la guia
			if ($ArrayTiempoEstancia[1] > $ArrayFechaPermitida[1]) {
				return true;
			}elseif ($ArrayTiempoEstancia[1] == $ArrayFechaPermitida[1]){
				if ($ArrayTiempoEstancia[2] > $ArrayFechaPermitida[2]) {
					return true;
				}else return false;
			}else return false;
		}else {	
			$tiempo_faltante = $ArrayFechaPermitida[0] - $ArrayTiempoEstancia[0] ;
			$numero = date("N");
			$fin = "11:30:00";// Los dias impares acaban el dia a las 11:30
			if ($numero % 2 == 0)$fin = "14:30:00";//Los dias pares acaban el dia a las 14:30
			$incio = date("H:i:s");
			$date1 = new DateTime($incio);//Se genera un objeto DateTime para la fecha actual
			$date2 = new DateTime($fin);//Se genera un objeto DateTime para la fecha total
			$diff = $date1->diff($date2);//Se resta a la fecha actual la fecha total
			$hora = $diff->h;//Capturar hora
			if ($incio > $fin) return false;
			else {
				if ($tiempo_faltante < $hora) return true;
				else return false;
			}
		}
	}	
//"Funcion 5" Funcion que comprueba en la base de YUPAK
	function f_descripcion_yupak($codigo){
		include '../../FilePHP/consql.php';
		$sql = mssql_query('SELECT * FROM YP_FAC_SERVICE WHERE Codigo = '.$codigo);
		if($row = mssql_fetch_array($sql)) return utf8_encode($row["Descripcion"]);
		else return false;
	}
// "Funcion 6" Funcion calcula el tiempo a cobrar de corralaje
	function Calcular_Corralaje($tiempo_permitido,$precio,$Tiempo_estancia){
		$arrayCorral = explode(":",$Tiempo_estancia);
		$arrayPermitido = explode(":",$tiempo_permitido);
		$horasResutaldo = $arrayCorral[0] - $arrayPermitido[0];
        $MinutosResultado = $arrayCorral[1] - $arrayPermitido[1];
        $SegundosResultado = $arrayCorral[2] - $arrayPermitido[2];
		$totalCobrar = 0;
		$TiempoCobrar = "00:00:00";
        if ( $horasResutaldo < 0)  {
            $ResultadoArray=[0,0,0];
            $ArrayRestar=[0,0,0];
            for ($i=0; $i < 3 ; $i++) {
                if ($arrayCorral[$i] > $arrayPermitido[$i] ) {
                    $ResultadoArray[$i] = ($arrayPermitido[$i]  -$arrayCorral[$i]) + 60 ;
                    $ArrayRestar[$i]= 1;
                }else{
                    $ResultadoArray[$i] =   $arrayPermitido[$i] - $arrayCorral[$i];
                }
            }
            $ResultadoArray[0] =  $ResultadoArray[0] - $ArrayRestar[1];
            $ResultadoArray[1] =  $ResultadoArray[1] - $ArrayRestar[2];
            $Tiempo_faltante= "$ResultadoArray[0]:$ResultadoArray[1]:$ResultadoArray[2]";
            $totalCobrar = 0;
        }
		else{
            if ($horasResutaldo == 0 && $MinutosResultado < 0 ) {
                $totalCobrar = 0;
            }elseif($MinutosResultado == 0 && $SegundosResultado < 0 ) {
                $totalCobrar = 0;
            }else{
                $minutosalcanzados=0;
                $horasAlcanzadas = 0;
                if ($SegundosResultado < 0) {
                    $SegundosResultado = 60 + $SegundosResultado;
                    $minutosalcanzados=1;
                }
                if ($MinutosResultado < 0) {
                    $MinutosResultado = 60 + $MinutosResultado;
                    $horasAlcanzadas = 1;
                }elseif ($MinutosResultado == 0 ) {
                    if ($minutosalcanzados != 0) {
                        $MinutosResultado = 60 + $MinutosResultado ;
                        $minutosalcanzados = 1;
                    }
                }
                $MinutosResultado = $MinutosResultado -$minutosalcanzados;
                $horasResutaldo  = $horasResutaldo - $horasAlcanzadas;
                $TiempoCobrar = "$horasResutaldo:$MinutosResultado:$SegundosResultado";
                $diasCobrar= $horasResutaldo / 24;
                $diasCobrar = intval($diasCobrar);
                if ($diasCobrar == 0) {
                    if ($MinutosResultado==0 && $SegundosResultado==0) {
                        $totalCobrar = 0;
                    }else{
                        $totalCobrar = $precio;//$array[0];
                    }
                }else{
                    if (($MinutosResultado == 0 && $SegundosResultado > 0) || $MinutosResultado > 0) {
                        $totalCobrar = ($diasCobrar  * $precio) + $precio;
                    }else{
                        $totalCobrar = $diasCobrar  *  $precio;
                    }
                }
            }
		}
		$arraydoscifras = explode(":",$TiempoCobrar);
		$mostrarTiempoCobrar="";
		for ($i=0; $i <3 ; $i++) {
			if (strlen($arraydoscifras[$i])==1) {
					$arraydoscifras[$i] = "0".$arraydoscifras[$i];
			}
		}
		$TiempoCobrar = $arraydoscifras[0].":".$arraydoscifras[1].":".$arraydoscifras[2];
		return  [$totalCobrar,$TiempoCobrar];//Ejem "30:00:00 - 1,66 "
	}
// "Funcion 7" Retorna el procesado 
    function f_obtener_procesar($dbConn,$Id){
        $cont=0;
        $consulta="SELECT faeCantidad FROM tbl_p_faenamiento f, tbl_p_orden o
            WHERE  f.ordId = o.ordId AND o.ordTipo = 1 AND o.gprId = :id";
        $sql= $dbConn->prepare($consulta);
        $sql->bindValue(':id',$Id);
        $sql->execute();
        while($row = $sql->fetch()) {
            $cont += $row["faeCantidad"];
        }
        return $cont;
    }
    function f_obtener_cantidades($dbConn,$Id){
        $cont= [0,0,0];
        $consulta="SELECT * FROM tbl_p_antemortem WHERE gprId = :id ";
        $sql= $dbConn->prepare($consulta);  
        $sql->bindValue(':id',$Id);
        $sql->execute();
        while($row = $sql->fetch()) {
            if ($row["antDictamen"]==0) $cont[0] += $row["antCantidad"];
            else if($row["antDictamen"]==1) $cont[1] += $row["antCantidad"];
            else if($row["antDictamen"]==2) $cont[2] += $row["antCantidad"];
            else return false;
        }
        return $cont;
    }

    function f_obtener_cantidades_orden($dbConn,$Id){
        $cont=0;
        $consulta="SELECT ordCantidad FROM tbl_p_orden WHERE gprId = :id";
        $sql= $dbConn->prepare($consulta);  
        $sql->bindValue(':id',$Id);
        $sql->execute();
        while($row = $sql->fetch()) {
            $cont += $row["ordCantidad"];
        }
        return $cont;
    }


// Opcion 3
	function ListaTazas($dbConn){
		$Tipo = $_POST["Tipo"];
		$Descripcion="";
		$Tazas="";
        $Corralaje = false;
        $dato_corralaje="ERROR-121";
		$consulta="SELECT * FROM tbl_a_servicios s, tbl_a_especies e 
        WHERE s.espId = e.espId AND s.espId = :code AND s.srnEstado = 0  ";
		$sql= $dbConn->prepare($consulta);
		$sql->bindValue(':code',$Tipo);
		$sql->execute();
        $Resultado='<table class=" table-bordered table-striped table-sm">
            <thead class="table-info">
                <th>SERVICIOS</th>
                <th>TAZA</th>
            </thead>
            <tbody>';
		while($row = $sql->fetch()) {
                $dato_corralaje="DESACTIVADO";
            if ($row["espEstadoCorralaje"] == 1){
                $Corralaje = true;
                $dato_corralaje  = "$ ".$row["espPrecioCorralaje"];
            }
            $Resultado.='
            <tr>
                <td class="text-center">'.utf8_encode($row["srnDescripcion"]).'</td>  
                <td class="text-center" >$ '.$row["srnPrecio"].'</td>
            </tr>
            ';
			// $Descripcion=$Descripcion.'<td class="table-info">'.$row["srnDescripcion"].'</td>';
			// $Tazas=$Tazas.'<td class="text-center" >$ '.$row["srnPrecio"].'</td>';
		}
        // if ($Corralaje==false) {
        //     $dato_corralaje="DESACTIVADO";
        // }
		// $Estado = Estado_Corralaje($dbConn,$Tipo);// "Funcion 3"
		// $Corralaje = "";
		// if ($Estado==0) {
			
		// }else{
		// 	$Especificaciones= Especificaciones_Corralaje($dbConn);// "Otras funciones 4"
		// 	$ArrayCorralaje = explode("/",$Especificaciones);
		// 	$Corralaje = "$ ".$ArrayCorralaje[0];
		// }
        $trC =  '<tr>
                    <td class="text-center">Corralaje</td>
                    <td class="text-center">'.$dato_corralaje.'</td>
                </tr>';
		return $Resultado.$trC.'</tbody>';
	}
// PRUEBA INSERTAR
function f_insert_orden($dbConn){
    $ArrayYupak = comprobar_yupak($dbConn);//espyCodigo_Empresa,espyLocalidad,espyCaja;
	if (comprobar_yupak($dbConn)==false) return '<h6 class="text-center" style="cursor:pointer;" onclick="recargar_vista_previa()">
	No se pudo generar la vista previa de la <b>Orden de Producción</b> debido a que <b>no se encontro las Especificaciones YUPAK</b></h6>';
	$especie = $_POST["Tipo"];
	$arrayServicios = Busqueda_Servicios($dbConn,$especie);// Id, Precio, Codigo Yupak, Descripcion
	if (count($arrayServicios)==0)return '<h6 class="text-center" style="cursor:pointer;" onclick="recargar_vista_previa()">
	No se pudo generar la vista previa de la <b>Orden de Producción</b> debido a que <b>no se encontraron servicios </b></h6>';
	$comprobar_codigo_yupak= '';
	$contador_codigo_yupak = 0;
	for ($i=0; $i < count($arrayServicios) ; $i++) { 
		if (f_descripcion_yupak($arrayServicios[$i][2])==false) {
			$contador_codigo_yupak++;
			$comprobar_codigo_yupak .= 'Servicio: '.utf8_encode($arrayServicios[$i][3]).'-> Codigo Yupak: '.$arrayServicios[$i][2].'<br>';
		}
	}
	if ($contador_codigo_yupak > 0) return '<h6 class="text-center" style="cursor:pointer;" onclick="recargar_vista_previa()">
	No se pudo generar la vista previa de la <b>Orden de Producción</b> debido a que el <b>código yupak</b> de los siguientes servicios <b> es incorrecto </b><br>'.$comprobar_codigo_yupak.'</h6>';

	$Num_Orden = Numero_Ordinal($dbConn,$especie);//"F Insterar 1" Genera el numero de la orden de Produccion
    $ArrayDatosOrden = array();
	$consulta="SELECT p.gprId,p.gprComprobante,(p.gprMacho + p.gprHembra) AS gprCantidad, p.gprestadoDetalle, p.gprTurno,
				c.cliId,c.cliNombres,c.cliNumero,c.	cliTelefono, c.cliCorreo, c.cliDireccion,
				e.espId, e.espEstanciaPermitida ,e.espEstadoCorralaje, e.espEstanciaCorralaje, e.espPrecioCorralaje, e.espDescripcion, e.espCodigoYupak 
	FROM tbl_r_guiaproceso p, tbl_a_clientes c, tbl_a_especies e 
	WHERE p.espId = e.espId AND c.cliId = p.cliId   AND p.espId = :id AND p.gprEliminado = 0 
	ORDER BY p.gprTurno , p.gprComprobante ASC ";
	$sql= $dbConn->prepare($consulta);
	$sql->bindValue(':id',$especie);
	$sql->execute();
    $cont = 0;
	while ($row = $sql->fetch()) {
        $tipo_numero = 2;
        if(!is_numeric($row["cliNumero"]))return '<h6 class="text-center" style="cursor:pointer;" onclick="recargar_vista_previa()">
            No se pudo generar la vista previa de la <b>Orden de Producción</b> debido a que el introductor <b>'.utf8_encode($row["cliNombres"]).'</b> tiene su número de identificacion incorrecto  </b><br>Ruc o Cedula: '.$row["cliNumero"].'</h6>';
        if (strlen($row["cliNumero"]) == 10 || strlen($row["cliNumero"]) == 13){
            if (strlen($row["cliNumero"]) == 10)$tipo_numero = 2;
            else if (strlen($row["cliNumero"]) == 13)$tipo_numero = 1;
        }else return '<h6 class="text-center" style="cursor:pointer;" onclick="recargar_vista_previa()">
            No se pudo generar la vista previa de la <b>Orden de Producción</b> debido a que el introductor <b>'.utf8_encode($row["cliNombres"]).'</b> tiene su número de identificacion incorrecto  </b><br>Ruc o Cedula: '.$row["cliNumero"].'</h6>';
		$array = explode(":",$row["espEstanciaPermitida"]);
		if (count($array)==3) {
			if (is_numeric($array[0]) && is_numeric($array[1]) && is_numeric($array[2])) {
				$estancia_enviar =  intval($array[0]).":".intval($array[1]).":".intval($array[2]);
				$FechaIngreso = $row["gprTurno"];
				if ($row["gprestadoDetalle"] == 1){//0 No se detalla; 1 Se detalla
					$FechaIngreso = Fecha_Ingreso_Detallada($dbConn,$row["gprId"]);//"Otras Funciones 1" /// 
					$td = "*"; // Cuando las guias son detalladas por animal aparece en un *
				}
				// $bandera = f_tiempo_minimo($row["gprTurno"],$row["gprestadoDetalle"],$estancia_enviar);//Fecha de ingreso de la guia, Estado del detalle, Fecha minima a cumplir 
				// if ($bandera==true) {
                    $procesado = f_obtener_procesar($dbConn,$row["gprId"]); // 0
                    $cant = f_obtener_cantidades($dbConn,$row["gprId"]);
                    $saldo = (intval($cant[1]) + intval($cant[2])) -  intval($procesado); //24
					if ($saldo > 0) {
                        if (comprobar_orden($dbConn,$row["gprId"])){
                        }else{
                            //CORRALAJE
                            $arraycorral = explode(":",$row["espEstanciaCorralaje"]);
                            if (count($arraycorral) != 3) return '<h6 class="text-center" style="cursor:pointer;" onclick="recargar_vista_previa()">
                            No se pudo generar la vista previa de la <b>Orden de Producción</b> debido a que la estructura del <b>Tiempo de corralaje es incorrecta </b><br>Tiempo de corralaje : "'.$row["espEstanciaCorralaje"].'"</h6>';
                            $estancia_corral= "0:0:0";
                            if (is_numeric($arraycorral[0]) && is_numeric($arraycorral[1]) && is_numeric($arraycorral[2])) $estancia_corral = intval($arraycorral[0]).":".intval($arraycorral[1]).":".intval($arraycorral[2]);
                            else return '<h6 class="text-center" style="cursor:pointer;" onclick="recargar_vista_previa()">
                            No se pudo generar la vista previa de la <b>Orden de Producción</b> debido a que la estructura del <b>Tiempo de corralaje es incorrecta </b><br>Tiempo de corralaje : "'.$row["espEstanciaCorralaje"].'"</h6>';
                            if (f_descripcion_yupak($row["espCodigoYupak"])==false) return '<h6 class="text-center" style="cursor:pointer;" onclick="recargar_vista_previa()">
                            No se pudo generar la vista previa de la <b>Orden de Producción</b> debido a que el Código del servicio de <b>CORRALAJE </b> de YUPAK es incorrecto <br>Código corralaje Yupak : "'.$row["espCodigoYupak"].'"</h6>';

                            $TiempoEstancia = Calcular_Estancia($FechaIngreso,date("Y-m-d H:i:s"));//"Otras funciones 2"
                            $ArrayCorralaje = Calcular_Corralaje($estancia_corral,$row["espPrecioCorralaje"],$TiempoEstancia);//"Funcion 6" Retorna el [pecio total de cobro(Unitario)] y [ tiempo a cobrar]"
                            $total_corralaje = 0;
                            if ($row["espEstadoCorralaje"] == 1 ) {//1 El corralaje esta activado
                                $total_corralaje = $ArrayCorralaje[0] * $saldo;
                            }
                            $cont++;
                            $bandera_servicios = 0;// Es la primera orden generada con esta guia 
                            if (comprobar_orden($dbConn,$row["gprId"])) $bandera_servicios = 1;// Es la segunda orden generada 
                            array_push($ArrayDatosOrden , 
                                    array(
                                        $Num_Orden,//Numero de Orden "0"
                                        date("Y-m-d H:i:s"),//Fecha Actual "1"
                                        $saldo,//Saldo "2"
                                        $procesado,//Procesado "3"
                                        $TiempoEstancia,//Estancia en el corral "4"
                                        $estancia_enviar,//Estancia permitida  "5"
                                        $row["espEstadoCorralaje"],//Estado del corralaje "6"
                                        $row["espEstanciaCorralaje"],//Estancia de corralaje "7"
                                        $ArrayCorralaje[0],// Corralaje precio unitario "8"
                                        $row["espPrecioCorralaje"],//Corralaje taza "9"
                                        $total_corralaje,//Total corralaje "10"
                                        $row["gprId"], // "11"
                                        $row["cliId"],// "12"
                                        $row["cliNumero"],// "13"
                                        $tipo_numero,// "14"
                                        utf8_encode($row["cliNombres"]),// "15"
                                        utf8_encode($row["cliDireccion"]),// "16"
                                        utf8_encode($row["cliTelefono"]),// "17"
                                        // utf8_encode($row["cliTelefono"]).' | '.utf8_encode($row["cliCorreo"]),// "17"
                                        $row["espCodigoYupak"],// "18"
                                        $bandera_servicios,// 19
                                        $row["gprCantidad"]//20
                                        ));
                        }
					}
				// }
			} else{
				return '<h6 class="text-center" style="cursor:pointer;" onclick="recargar_vista_previa()">
					No se pudo generar la vista previa de la <b>Orden de Producción</b> debido a que la estructura del <b>Tiempo Minimo es incorrecta </b><br>Tiempo Minimo : "'.$row["espEstanciaPermitida"].'"</h6>';
			}
		}else{
			return '<h6 class="text-center" style="cursor:pointer;" onclick="recargar_vista_previa()">
					No se pudo generar la vista previa de la <b>Orden de Producción</b> debido a que la estructura del <b>Tiempo Minimo es incorrecta </b><br>Tiempo Minimo : "'.$row["espEstanciaPermitida"].'"</h6>';
		}
	}
    if ($cont == 0) {
        $datos_especie = datos_Especie($dbConn,$especie);
        // [$row["espEstanciaPermitida"],$row["espEstadoCorralaje"],$row["espEstanciaCorralaje"],$row["espPrecioCorralaje"]];
        if($datos_especie != false){
            $Array = array(
                $Num_Orden,
                date("Y-m-d H:i:s"),//Fecha Actual "1"
                0,//Cantidad
                0,//Procesado "3"
                '0:00:00',//Estancia en el corral "4"
                $datos_especie[0],//Estancia permitida  "5"
                $datos_especie[1],//Estado del corralaje "6"
                $datos_especie[2],//Estancia de corralaje "7"
                0,// Corralaje precio unitario "8"
                $datos_especie[3],//Corralaje taza "9"
                0,//Total corralaje "10"
                null // "11"
            );
            if (InsertarOrdenTabla($dbConn,$Array,null,$especie)==false)return 'NO SE PUEDO CREAR LA ORDEN DE PRODUCCION';
            else{
                $_SESSION['OPCION'] = 3;
                $_SESSION['VARIABLE'] = $Num_Orden;
                $_SESSION['VARIABLE2'] = $especie;
                $_SESSION['INICIO'] = date("Y-m-d");
                $_SESSION['FINAL'] = date("Y-m-d");
                return  true;
            }
        }        
    }else {
        for ($i=0; $i < count($ArrayDatosOrden); $i++) {
            $doccumentoYupak = Numero_DocumentoYupack($ArrayYupak[2]);
            $IdOrden = InsertarOrdenTabla($dbConn,$ArrayDatosOrden[$i],$doccumentoYupak,$especie);
            if ($IdOrden != false) {
                $TotalPagar = 0;
                $toal_corralaje = 0;
                
                //insertar servicios
                if ($ArrayDatosOrden[$i][19] == 0) {// Se cobra los servicios
                    for ($j=0; $j < count($arrayServicios) ; $j++) { //Id, Precio, Codigo Yupak, Descripcion
                        // Datos Para el detalle
                        $Total = $arrayServicios[$j][1] * $ArrayDatosOrden[$i][2];
                        $TotalPagar += $Total;
                        $ArrayDetalle = array(
                            $ArrayYupak[0],// Codigo_Empresa = Codigo_Empresa
                            $ArrayYupak[1],// Localidad = Localidad
                            'D',// Tipo = C
                            $ArrayYupak[2],// Caja	= Caja de Facturación
                            $doccumentoYupak,// Num_Documento = Número de Comprobante
                            $arrayServicios[$j][2],// Codigo_Id = Código de Servicio {Ref Tabla YP_FAC_SERVICE de Yupak}
                            0,// Codigo_Id3 =  para el detalle este campo tiene que estar en "0" (varchar)
                            '',// Detalle1 = Nombre Cliente 
                            '',// Detalle2 = Dirección Cliente
                            '',// Detalle3	= Telefono | Email
                            '',// Fecha = Fecha de Comprobante
                            $arrayServicios[$j][1],// Valor1 = Costo Unitario
                            0, // Valor2 = Valor Descuento
                            $Total,// Valor3 = Valor Total
                            $ArrayDatosOrden[$i][2],// Valor4 = Cantidad Vendida
                            $IdOrden, // Id de la orden de produccion 
                            $arrayServicios[$j][0],// Id del servicio
                            null // Id del cliente, como es el detalle el id del cliente es null
                        );
                        if (InsertarFacturaYupakLocal($dbConn,$ArrayDetalle)){
                            if (InstertarSQLYupak($ArrayDetalle)==false) return 'YUPAK - No se pudo agregar el servicio '.$arrayServicios[$j][3].' para el cliente '.$ArrayDatosOrden[$i][15];
                        }
                        else {
                            return 'No se pudo agregar el servicio '.$arrayServicios[$j][3].' para el cliente '.$ArrayDatosOrden[$i][15];
                        }
                    }
                }
                //Insertar Corralaje
                if ($ArrayDatosOrden[$i][6] == 1) {
                    $Total = $ArrayDatosOrden[$i][8] * $ArrayDatosOrden[$i][2];
                    $toal_corralaje = $Total;
                    $ArrayDetalle = array(
                        $ArrayYupak[0],// Codigo_Empresa = Codigo_Empresa
                        $ArrayYupak[1],// Localidad = Localidad
                        'D',// Tipo = C
                        $ArrayYupak[2],// Caja	= Caja de Facturación
                        $doccumentoYupak,// Num_Documento = Número de Comprobante
                        $ArrayDatosOrden[$i][18],// Codigo_Id = Código de Servicio {Ref Tabla YP_FAC_SERVICE de Yupak}
                        0,// Codigo_Id3 =  para el detalle este campo tiene que estar en "0" (varchar)
                        '',// Detalle1 = Nombre Cliente 
                        '',// Detalle2 = Dirección Cliente
                        '',// Detalle3	= Telefono | Email
                        '',// Fecha = Fecha de Comprobante
                        $ArrayDatosOrden[$i][8],// Valor1 = Costo Unitario
                        0, // Valor2 = Valor Descuento
                        $Total,// Valor3 = Valor Total
                        $ArrayDatosOrden[$i][2],// Valor4 = Cantidad Vendida
                        $IdOrden, // Id de la orden de produccion 
                        null,// Id del servicio, como el servicio es corralaje va null
                        null // Id del cliente, como es el detalle el id del cliente es null
                    );
                    if (InsertarFacturaYupakLocal($dbConn,$ArrayDetalle)){
                        if (InstertarSQLYupak($ArrayDetalle) == false)return 'YUPAK - No se pudo agregar el corralaje para el cliente '.$ArrayDatosOrden[$i][15];
                    }else return 'No se pudo agregar el corralaje para el cliente '.$ArrayDatosOrden[$i][15];
                }
                //Ingresar cabecera 
                $TotalPagar += $toal_corralaje;
                $ArrayCabecera = array(
                    $ArrayYupak[0],// Codigo_Empresa = Codigo_Empresa
                    $ArrayYupak[1],// Localidad = Localidad
                    'C',// Tipo = C
                    $ArrayYupak[2],// Caja	= Caja de Facturación
                    $doccumentoYupak,// Num_Documento = Número de Comprobante
                    $ArrayDatosOrden[$i][14],// Codigo_Id = 1 Ruc 2 Cédula 3 Pasporte 7 Consumidor Final
                    $ArrayDatosOrden[$i][13],// Codigo_Id3 = "Número de Identificación correspondiente a Codigo_Id
                    utf8_encode($ArrayDatosOrden[$i][15]),// Detalle1 = Nombre Cliente 
                    utf8_encode($ArrayDatosOrden[$i][16]),// Detalle2 = Dirección Cliente
                    $ArrayDatosOrden[$i][17],// Detalle3	= Telefono | Email
                    date("Y-m-d H:i:s"),// Fecha = Fecha de Comprobante
                    $TotalPagar,//Valor1 = SubTotal
                    0, // Valor2 = Iva
                    $TotalPagar,// Valor3 = Total
                    1,// Valor4 = 0 Pago de Contado (Se asume cobro en efectivo) 1 Crédito
                    $IdOrden, // Id de la orden de produccion
                    null,// Id del servicio, como es la cabecera el id del servicio es null
                    $ArrayDatosOrden[$i][12] // Id del cliente
                );
                if (InsertarFacturaYupakLocal($dbConn,$ArrayCabecera)){
                    if (InstertarSQLYupak($ArrayCabecera)){
                        if(!UpdateTableGuiaProceso($dbConn,$ArrayDatosOrden[$i][11],$ArrayDatosOrden[$i][20]))return 'YUPAK - No se pudo actualizar la guia para el cliente '.$ArrayDatosOrden[$i][15]; 
                    }else return 'YUPAK - No se pudo agregar la cabecera para el cliente '.$ArrayDatosOrden[$i][15]; 
                }else return 'No se pudo agregar la cabecera para el cliente '.$ArrayDatosOrden[$i][15];
            }
        }
        $_SESSION['OPCION'] = 3;
        $_SESSION['VARIABLE'] = $Num_Orden;
        $_SESSION['VARIABLE2'] = $especie;
        $_SESSION['INICIO'] = date("Y-m-d");
        $_SESSION['FINAL'] = date("Y-m-d");
        return  true;
    }
}



//Opcion 4
	
// Funciones Necesarias para insertar la orden de Produccion
// "F Insterar 1" Esta funcion crea el numero que llevara la orden de produccion
	function Numero_Ordinal($dbConn,$Tipo){
		$anio_actual = date("Y");
		$numero=1;
		$maximo=3;
        $Juliano= CalcularJuliano();
		$consulta="SELECT o.ordNumOrden,e.espLetra 
        FROM tbl_p_orden o, tbl_a_especies e 
        WHERE o.espId = e.espId AND e.espId = :id  AND o.ordTipo = 1 AND o.ordFecha
        BETWEEN :inicio AND :fin ORDER BY o.ordId DESC ";
		// La consulta trae el ultimo numero de orden de produccion
		$sql= $dbConn->prepare($consulta);
		$sql->bindValue(':id',$Tipo);
		$sql->bindValue(':inicio',$anio_actual."-01-01 00:00:00");
		$sql->bindValue(':fin',$anio_actual."-12-31 23:59:59");
		$sql->execute();
		while($row = $sql->fetch()) {
            $Array1 = explode("-",$row["ordNumOrden"]);
            if (count($Array1)==2) {
                $letraBuscar =  get_letra_comprobante($Array1[1]);
                if ($letraBuscar == $row["espLetra"]) {
                    $Array2 = explode($row["espLetra"],$Array1[1]);
                    if ($Array2[0] != $Juliano) $numero = 1;
                    else $numero = intval($Array2[1]) + 1;
                    $cantidad = strlen($numero);
                    $resultado="";
                    for ($i=$cantidad; $i < $maximo; $i++) $resultado .= "0";
                    return "EOREMRAQ-".$Juliano.$row["espLetra"].$resultado.$numero;
                }
            }
		}
		$letra = Traer_Letra_tipo($dbConn,$Tipo);
        $cantidad = strlen($numero);
        $resultado="";
        for ($i=$cantidad; $i < $maximo; $i++) $resultado .= "0";
		return "EOREMRAQ-".$Juliano.$letra.$resultado.$numero;
	}
    function get_letra_comprobante($comprobante){
        for($i=65; $i<=90; $i++) {  
            $letraBuscar = chr($i);
            $arrayLetra =explode($letraBuscar,$comprobante);
            if (count($arrayLetra)==2)return $letraBuscar;
        }
        return false;
    }
// "F Insertar 2" Esta funcion crea el numero de documento Yupak
function Numero_DocumentoYupack($Codigo_Caja){
    $Codigo_Caja = intval($Codigo_Caja);
    include '../../FilePHP/consql.php';
    $new_Num_Documento = 1;
    $sql2 = mssql_query("SELECT TOP (1) * from YP_FAC_INVOICE WHERE Caja = $Codigo_Caja AND TipoCaja = 0 ORDER BY Numero_Factura DESC");
    if($row = mssql_fetch_array($sql2))$new_Num_Documento = $row["Numero_Factura"];
    if (f_buscar_Numero_DocumentoYupack($new_Num_Documento)) {
        $sql2 = mssql_query("SELECT TOP 1 Num_Documento FROM YP_EGDATA_FAC WHERE Caja = $Codigo_Caja  AND Tipo='C' ORDER BY Num_Documento DESC ");
        if($row = mssql_fetch_array($sql2)) return doubleval($row["Num_Documento"])  +1 ;
        else return $Codigo_Caja."000000001";
    }else {
        $i = 1;
        while (f_buscar_Numero_DocumentoYupack(doubleval($new_Num_Documento) + $i)) $i++;
        return (doubleval($new_Num_Documento) + $i);
    }
}
function f_buscar_Numero_DocumentoYupack($numero){
    include '../../FilePHP/consql.php';
    $sql2 = mssql_query("SELECT  Num_Documento FROM YP_EGDATA_FAC WHERE Num_Documento = $numero");
    if($row = mssql_fetch_array($sql2))return true;
    else return false;
}

// "F Insertar 5" Funcion de insertar la orden de producción
	function InsertarOrdenTabla($dbConn,$Array,$doccumentoYupak,$especie){
		try {
			$consulta="INSERT INTO tbl_p_orden(	ordTipo,ordNumOrden,ordFecha,ordCantidad,ordProcesado,ordTiempoEstancia,ordEstanciaPermitida,ordEstadoCorralaje,ordEstanciaCorralaje,ordTiempoCobrar,ordPrecioCorralaje,ordTotalCorralaje,Num_Documento,gprId,espId)
			VALUES (:ordTipo,:ordNumOrden,:ordFecha,:ordCantidad,:ordProcesado,:ordTiempoEstancia,:ordEstanciaPermitida,:ordEstadoCorralaje,:ordEstanciaCorralaje,:ordTiempoCobrar,:ordPrecioCorralaje,:ordTotalCorralaje,:Num_Documento,:gprId,:espId)";
				$sql= $dbConn->prepare($consulta);
			$sql->bindValue(':ordTipo',1);
			$sql->bindValue(':ordNumOrden',$Array[0]);
			$sql->bindValue(':ordFecha',$Array[1]);
			$sql->bindValue(':ordCantidad',$Array[2]);
			$sql->bindValue(':ordProcesado',$Array[3]);
			$sql->bindValue(':ordTiempoEstancia',$Array[4]);
			$sql->bindValue(':ordEstanciaPermitida',$Array[5]);
			$sql->bindValue(':ordEstadoCorralaje',$Array[6]);
			$sql->bindValue(':ordEstanciaCorralaje',$Array[7]);
			$sql->bindValue(':ordTiempoCobrar',$Array[8]);
			$sql->bindValue(':ordPrecioCorralaje',$Array[9]);
			$sql->bindValue(':ordTotalCorralaje',$Array[10]);
			$sql->bindValue(':Num_Documento',$doccumentoYupak);
			$sql->bindValue(':gprId',$Array[11]);
			$sql->bindValue(':espId',$especie);
			if ($sql->execute())return $dbConn->lastInsertId();
			else return false;
		}  catch (Exception $e) {
			Insert_Error('ERROR-817272',$e->getMessage(),'ERROR AL INGRESAR LA ORDEN DE PRODUCCION');
			exit("ERROR-817272");
		}
	}
    function InsertarFacturaYupakLocal($dbConn,$ArrayDatos){
		try {
			$consulta="INSERT INTO tbl_YP_EGDATA_FAC(Codigo_Empresa,Localidad,Tipo,Documento,Caja,Num_Documento,Codigo_Id,Codigo_Id2,Codigo_Id3,Detalle1,Detalle2,Detalle3,Fecha,Valor1,Valor2,Valor3,Valor4,ordId,srnId,cliId)
			VALUES (:Codigo_Empresa,:Localidad,:Tipo,:Documento,:Caja,:Num_Documento,:Codigo_Id,:Codigo_Id2,:Codigo_Id3,:Detalle1,:Detalle2,:Detalle3,:Fecha,:Valor1,:Valor2,:Valor3,:Valor4,:ordId,:srnId,:cliId)";
			$sql= $dbConn->prepare($consulta);
			$sql->bindValue(':Codigo_Empresa',$ArrayDatos[0]);
			$sql->bindValue(':Localidad',$ArrayDatos[1]);
			$sql->bindValue(':Tipo',$ArrayDatos[2]);
			$sql->bindValue(':Documento',0);//0 Factura 1 Nota de Venta, tanto par C y D son 0
			$sql->bindValue(':Caja',$ArrayDatos[3]);
			$sql->bindValue(':Num_Documento',$ArrayDatos[4]);
			$sql->bindValue(':Codigo_Id',$ArrayDatos[5]);
			$sql->bindValue(':Codigo_Id2',0);//(1 = Consumidor Final; 0 = Cliente) (smallint)
			$sql->bindValue(':Codigo_Id3',utf8_decode($ArrayDatos[6]));
            $sql->bindValue(':Detalle1',utf8_decode($ArrayDatos[7])); 
			$sql->bindValue(':Detalle2',utf8_decode($ArrayDatos[8]));
			$sql->bindValue(':Detalle3',utf8_decode($ArrayDatos[9]));
			$sql->bindValue(':Fecha',$ArrayDatos[10]);
			$sql->bindValue(':Valor1',$ArrayDatos[11]);
			$sql->bindValue(':Valor2',$ArrayDatos[12]);
			$sql->bindValue(':Valor3',$ArrayDatos[13]);
			$sql->bindValue(':Valor4',$ArrayDatos[14]);
			$sql->bindValue(':ordId',$ArrayDatos[15]);
			$sql->bindValue(':srnId',$ArrayDatos[16]);
			$sql->bindValue(':cliId',$ArrayDatos[17]);
			if ($sql->execute())return true;
			else return false;
		}  catch (Exception $e) {
			Insert_Error('ERROR-817272',$e->getMessage(),'ERROR AL INGRESAR LA ORDEN DE PRODUCCION');
			exit("ERROR-817272");
		}
	}
// "F Insert 6" Funcion que trae los datos Yupak
	function Datos_Yupa($dbConn){
		$resultado=[];
		$consulta="SELECT * FROM tbl_especificacionesYP_new WHERE espyId=1";
		$sql= $dbConn->prepare($consulta);
		$sql->execute();
		if($row = $sql->fetch()) {
			$resultado = [$row['espyCodigo_Empresa'],$row['espyLocalidad'],$row['espyCaja']];
		}
		return $resultado;
	}
// "F Insert 7" Funcion insertar Yupak
	
// "Otras Funciones 1" Funcion que trae la fecha de ingreso correspondiente a la guia detallada
	function Fecha_Ingreso_Detallada($dbConn,$Guia){
		$resultado="0";
		$consulta="SELECT dtFecha FROM tbl_r_detalle WHERE gprId = :guia AND dtProceso = 0 LIMIT 1";
		// Consulta retorna la fecha de ingreso
		$sql= $dbConn->prepare($consulta);
		$sql->bindValue(':guia',$Guia);
		$sql->execute();
		if($row = $sql->fetch()) {
			$resultado=$row["dtFecha"];
		}
		return $resultado;
	}
// "Otras funciones 2" Funcion que calcula el tiempo que el animal paso en el corral
	function Calcular_Estancia($Ingreso,$Hoy){
		$arrayFecha = explode(" ",$Ingreso." ");//Se hace un array de la fecha de ingreso
		$fecha = $arrayFecha[0];//Del "$arrayFecha" se obtiene solo el año-mes-dia
		$arrayHora = explode(".",$arrayFecha[1]);//Del "$arrayFecha" se realiza otro array separado por "."
		$hora = $arrayHora[0];//Se obtien la hora del $arrayHora
		$completo = $fecha." ".$hora;//Se obtiene la fecha total
		$date1 = new DateTime($Hoy);//Se genera un objeto DateTime para la fecha actual
		$date2 = new DateTime($completo);//Se genera un objeto DateTime para la fecha total
		$diff = $date1->diff($date2);//Se resta a la fecha actual la fecha total
		$ayo = $diff->y;//Capturar año
		$ayo= $ayo * 8760; //Transformar de año a horas
		$mes = $diff->m;//Capturar mes
		$mes = $mes * 730;//Transformar de mes a horas
		$dia = $diff->d;//Capturar dia
		$dia = $dia * 24;//Transformar de dia a horas
		$hora = $diff->h;//Capturar dia
		$minutos = $diff->i;//Capturar minutos
		if ( strlen($minutos)==1)$minutos="0".$minutos;//aumentar 0 si solo el numero es de una cifra
		$segundos = $diff->s;//Capturar segundos
		if ( strlen($segundos)==1) $segundos="0".$segundos;//aumentar 0 si solo el numero es de una cifra
		$Total= ($ayo + $mes + $dia + $hora);//Suma de todas la horas obtenidas
		return $Total .":".$minutos.":".$segundos;//Retorno de las horas:minutos:segundos (Tiempo que el animal paso en el corral)
	}
//"Otras funciones 3" Funcion que retorna el tiempo minimo que tiene que permanecer el animal antes de ser faenado
	function Tiempo_Establecido($dbConn,$Tipo){
		$resultado="0";
		$consulta="SELECT espEstanciaPermitida FROM tbl_a_especies WHERE espId = :tipo";
		//la consulta trae el tiempo minimo dependiendo de cada especie
		$sql= $dbConn->prepare($consulta);
		$sql->bindValue(':tipo',$Tipo);
		$sql->execute();
		if($row = $sql->fetch()) {
			$resultado=$row["espEstanciaPermitida"];
		}
		return $resultado;
	}
    function datos_Especie($dbConn,$Tipo){
		$consulta="SELECT espEstanciaPermitida,	espEstadoCorralaje,espEstanciaCorralaje,espPrecioCorralaje FROM tbl_a_especies WHERE espId = :tipo";
		$sql= $dbConn->prepare($consulta);
		$sql->bindValue(':tipo',$Tipo);
		$sql->execute();
		if($row = $sql->fetch())return [$row["espEstanciaPermitida"],$row["espEstadoCorralaje"],$row["espEstanciaCorralaje"],$row["espPrecioCorralaje"]];
		return false;
	}


// "Otras Funciones 6" Funcion que trae la fecha de ingreso correspondiente a la guia procesada
	function Fecha_Ingreso_Guia_Procesada($dbConn,$Guia){
		$resultado=$Guia;
		$consulta="SELECT gprTurno FROM tbl_r_guiaproceso WHERE gprId = :guia";
		// Consulta retorna la fecha de ingreso
		$sql= $dbConn->prepare($consulta);
		$sql->bindValue(':guia',$Guia);
		$sql->execute();
		if($row = $sql->fetch()) {
			$resultado=$row["gprTurno"];
		}
		return $resultado;
	}
//"Otras funciones 7" Esta funcion trae la Letra para generar la orden de Produccion
	function Traer_Letra_tipo($dbConn,$tipo){
		$consulta="SELECT espLetra FROM tbl_a_especies WHERE espId = :id";
		$sql= $dbConn->prepare($consulta);
		$sql->bindValue(':id',$tipo);
		$sql->execute();
		if($row = $sql->fetch()) return $row['espLetra'];
		return false;
	}
// "Otras funciones 8" Esta funcion actualiza el procesado
	function UpdateTableGuiaProceso($dbConn,$Id,$total){
        try {
            $total_orden = f_obtener_cantidades_orden($dbConn,$Id);
            if ($total_orden == $total ) {
                $consulta="UPDATE tbl_r_guiaproceso SET gprEstado = 2 WHERE gprId = :id";
                $sql= $dbConn->prepare($consulta);
                $sql->bindValue(':id',$Id);
                if ($sql->execute()) return true;
                else return false;
            }else return true;
        }   catch (Exception $e) {
			Insert_Error('ERROR-821111',$e->getMessage(),'ERROR AL CAMBIAR EL ESTADO DE LA GUIA A 2');
			exit("ERROR-821111");
		}
	}
    

// "Otras funciones 9" Esta funcion calcula el Numero Juliano
	function CalcularJuliano(){
        $numMes=date("n");
        $total=0;
        $anio = date("Y");
        for ($i=1; $i < $numMes ; $i++) {
            $fecha= date("t", strtotime($anio."-$i"));
            $total = $total + intval($fecha);
        }
        $total = $total + intval(date("d"));
        return "$total".date("y");
	}
    // "Otras funciones 9" Esta funcion agrega la orden de produccion en el caso que un animal ya haya sido procesado sin haber generado la orden de produccion
	function UpdateTableGuiaDetalle($dbConn,$Orden,$Id){
        $cont1=0;
        $cont2=0;
        $cont3=0;
		$consulta="SELECT dtId,dtProceso FROM tbl_detalle_guia_new where id_contador = :id";
		$sql= $dbConn->prepare($consulta);
		$sql->bindValue(':id',$Id);
		$sql->execute();
		while($row = $sql->fetch()) {
			if ($row["dtProceso"]!=0) {
                if (Comprobar_Orden_Guia($dbConn,$row["dtId"])==1) {
                    if (update_detalle_guia($dbConn,$row["dtId"],$Orden)==true) {
                        $cont3++;
                    }
                }else $cont2++;
            }else $cont1++;
		}
        return true;
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

    //Complemento de "otras funciones  9"
    function update_detalle_2($dbConn,$id,$idOrden){
        $consulta="UPDATE tbl_r_detalle SET ordId =  :orden WHERE  gprId = :id AND dtDictamen != 0 ";
        $sql= $dbConn->prepare($consulta);
        $sql->bindValue(':orden',$idOrden);
        $sql->bindValue(':id',$id);
        if ($sql->execute()) return true;
        else return false;
    }


	function InstertarSQLYupak($Array){
        try {
            $Codigo_Empresa = $Array[0]; //Codigo de la empresa por lo general es 41 ;(Integer)
            $Localidad = $Array[1]; //Localidad por lo general es 1;(Integer)
            $Tipo = $Array[2]; //Se define que se insertara una cabecera (varchar)
            $Documento = 0;//(0 =Factura; 1 = Nota de Venta) por lo general es 0; (smallint)
            $Caja = $Array[3];// (Caja de Facturación) Tanto para Cabecera como para detalle se define con "1020" (float)
            $Num_Documento = $Array[4]; // Se calcula el numero de comprobante el cual debe ser el mismo para la cabecera como para todos sus detalles (float)
            $Codigo_Id =  $Array[5];// (1 = Ruc; 2 = Cédula; 3 = Pasporte; 7 =  Consumidor Final) (smallint)
            $Codigo_Id2 = 0;//(1 = Consumidor Final; 0 = Cliente) (smallint)
            $Codigo_Id3 = $Array[6]; // Corresponde a lo selecionado en el $Codigo_Id (varchar)
            $Detalle1 = $Array[7];// Nombre del cliente (varchar)
            $Detalle2 = $Array[8];// Direccion del cliente (varchar)
            $Detalle3 = $Array[9];// Telefono | Email del cliente (varchar)
            $Fecha = '';
            if ($Tipo == 'C') $Fecha = date("m/d/Y H:i:s");// La fecha solo sera insertada en el encabezado dateTime
            $Valor1 =$Array[11];//SubTotal de todo lo detallado (float)
            $Valor2 = $Array[12];// Corresponde al iva que siempre va hacer 0;  (Numeric(28,10))
            $Valor3 = $Array[13];// es el valor final a pagar (Numeric(28,10))
            $Valor4 = $Array[14];// (0 = Pago de Contado (Se asume cobro en efectivo); 1 = Crédito) se asume que siempre va ir "1" (Numeric(28,10))
            $Estado = 0;//

            include '../../FilePHP/consql.php';
            $campos=" Codigo_Empresa, Localidad, Tipo, Documento, Caja, Num_Documento, Codigo_Id,
            Codigo_Id2, Codigo_Id3, Detalle1,Detalle2,Detalle3,Fecha,Valor1, Valor2, Valor3,Valor4,Estado";
            $valores ="$Codigo_Empresa,$Localidad,'$Tipo','$Documento',$Caja,$Num_Documento,$Codigo_Id,
            $Codigo_Id2,'$Codigo_Id3','$Detalle1','$Detalle2','$Detalle3','$Fecha',$Valor1,$Valor2,$Valor3,$Valor4,$Estado";
            $sql3 = mssql_query("INSERT INTO YP_EGDATA_FAC($campos) VALUES($valores)");
            return $sql3;
        }catch (Exception $e) {
			Insert_Error('ERROR-28192',$e->getMessage(),'ERROR AL AGREGAR YUPAK');
			exit("ERROR-28192");
		}
	}
	function InstertarSQLYupak_Cabecera($empresa,$localidad,$num_documento,$Codigo_Caja,$cedula,$cliente,$total){
		$Codigo_Empresa = $empresa; //Codigo de la empresa por lo general es 41 ;(Integer)
	 	$Localidad = $localidad; //Localidad por lo general es 1;(Integer)
		$Tipo = 'C'; //Se define que se insertara una cabecera (varchar)
		$Documento = 0;//(0 =Factura; 1 = Nota de Venta) por lo general es 0; (smallint)
		$Caja = $Codigo_Caja;// (Caja de Facturación) Tanto para Cabecera como para detalle se define con "1020" (float)
		$Num_Documento = $num_documento; // Se calcula el numero de comprobante el cual debe ser el mismo para la cabecera como para todos sus detalles (float)
		$Codigo_Id =  1;// (1 = Ruc; 2 = Cédula; 3 = Pasporte; 7 =  Consumidor Final) (smallint)
		$Codigo_Id2 = 0;//(1 = Consumidor Final; 0 = Cliente) (smallint)
		$Codigo_Id3 = $cedula; // Corresponde a lo selecionado en el $Codigo_Id (varchar)
		$Detalle1 = utf8_decode($cliente);// Nombre del cliente (varchar)
		$Detalle2 = '';// Direccion del cliente (varchar)
		$Detalle3 = '';// Telefono | Email del cliente (varchar)
		$Fecha =date("m/d/Y H:i:s");// La fecha solo sera insertada en el encabezado dateTime
		$Valor1 = $total;//SubTotal de todo lo detallado (float)
		$Valor2 = 0;// Corresponde al iva que siempre va hacer 0;  (Numeric(28,10))
		$Valor3 = $total;// es el valor final a pagar (Numeric(28,10))
		$Valor4 = 1;// (0 = Pago de Contado (Se asume cobro en efectivo); 1 = Crédito) se asume que siempre va ir "1" (Numeric(28,10))
		$Estado = 0;//

		include '../../FilePHP/consql.php';
		$campos=" Codigo_Empresa, Localidad, Tipo, Documento, Caja, Num_Documento, Codigo_Id,
		Codigo_Id2, Codigo_Id3, Detalle1,Detalle2,Detalle3,Fecha,Valor1, Valor2, Valor3,Valor4,Estado";
		$valores ="$Codigo_Empresa,$Localidad,'$Tipo','$Documento',$Caja,$Num_Documento,$Codigo_Id,
		$Codigo_Id2,'$Codigo_Id3','$Detalle1','$Detalle2','$Detalle3','$Fecha',$Valor1,$Valor2,$Valor3,$Valor4,$Estado";
		$sql3 = mssql_query("INSERT INTO YP_EGDATA_FAC($campos) VALUES($valores)");
		return $sql3;
	}

    // Funciones de vista 
    function view_cliente_guia($dbConn){
        $resultado="ERROR";
        $Id  = $_POST["Id_contador"];
		$consulta="SELECT p.guia_numero,p.fecha_proceso,p.cantidad,p.procesado,g.guia_mov_numero,c.apellidos,c.nombres,c.ruc,p.estado_detalle FROM tbl_guia_proceso p 
        JOIN tbl_clientes c ON p.ruc=c.ruc
        JOIN tbl_guiamovilizacion g ON p.guia_numero = g.guia_numero
        WHERE p.id_contador = :id";
		$sql= $dbConn->prepare($consulta);
		$sql->bindValue(':id',$Id);
		$sql->execute();
		if($row = $sql->fetch()) {
			$detalle = "ERROR";
			if ($row["estado_detalle"]==0) {
				$detalle = "DETTALE NO";
			}else if ($row["estado_detalle"]==1){
				$detalle = "DETTALE SI";
			}
			$resultado = '<div class="row">
            <div class="col-md-4 text-center">
                <h1 class="text-center" style="font-size:100px;color:#dc7633">
                    <i class="fas fa-user-tie"></i>
                </h1>
                <span class="text-muted">'.utf8_encode($row["apellidos"]).' '.utf8_encode($row["nombres"]).'</span><br>
                <span class="text-muted">'.$row["ruc"].'</span>
            </div>
            <div class="col-md-8">
                <h5><b>Datos de la guía</b></h5>
                <h6 class="text-muted" > 
                    <b> N° guía de movilización:</b>
                    '.$row["guia_mov_numero"].'
                </h6>
                <h6 class="text-muted" > 
                    <b> N° guía de proceso:</b>
                    '.$row["guia_numero"].'
                </h6>
                <h6 class="text-muted" > 
                    <b> Fecha:</b>
                    '.$row["fecha_proceso"].'
                </h6>
                <h6 class="text-muted" > 
                    <b> Cantidad total:</b>
                    '.$row["cantidad"].'
                </h6>
				<h6 class="text-muted" > 
                    <b>'.$detalle.'</b>
                </h6>
            </div>
        </div>';
		}
		return $resultado;

    }
    function view_detalle_guia($dbConn){
        $resultado='<table id="tbl_table_detalle" class="table table-bordered table-striped table-sm table-hover text-center">
        <thead>
            <th>#</th>
            <th>CÓDIGO</th>
            <th>PESO</th>
            <th>PRODUCTO</th>
        </thead>
        <tbody>';
        $Id  = $_POST["Id_contador"];
		$consulta="SELECT d.dtCodigo,d.dtPeso,pr.proDescripcion,d.dtId FROM tbl_guia_proceso p 
        JOIN tbl_detalle_guia_new d  ON d.id_contador = p.id_contador
        JOIN tbl_productos_new pr ON d.proId = pr.proId
        WHERE p.id_contador = :id  AND d.ordId is NULL";
		$sql= $dbConn->prepare($consulta);
		$sql->bindValue(':id',$Id);
		$sql->execute();
        $cont = 0;
		while($row = $sql->fetch()) {
            if (Comprobar_Orden_Guia($dbConn,$row["dtId"])==1) {
                $cont++;
                $resultado .= '<tr>
                            <td>'.$cont.'</td>
                            <td>'.$row["dtCodigo"].'</td>
                            <td>'.$row["dtPeso"].'</td>
                            <td>'.$row["proDescripcion"].'</td>
                        </tr>';
            }
		}
		return $resultado.'</tbody></table>';

    }
// "Otras funciones 8" Esta funcion comprueba si existen alguna orden
function comprobar_orden($dbConn,$Id){
    $consulta="SELECT * FROM tbl_p_orden WHERE gprId = :id AND ordTipo = 1";
    $sql= $dbConn->prepare($consulta);
    $sql->bindValue(':id',$Id);
    $sql->execute();
    if($row = $sql->fetch()) return true;
    else return  false;
}


?>
