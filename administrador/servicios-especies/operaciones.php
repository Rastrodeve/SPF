<?php

if (isset($_REQUEST['op'])) {
	require '../../FilePHP/utils.php';
	$dbConn = conectar($db);
	$op=$_REQUEST['op'];
	if ($op==1) echo Listar_Especie_Animales($dbConn);
	else if ($op==2) echo get_data_new_especie();
	else if ($op==3) echo f_insert_especie($dbConn);
	else if ($op==4) echo f_update_estado_especie($dbConn);
	else if ($op==5) echo f_update_detalle_especie($dbConn);
	else if ($op==6) echo get_data_imagen_especie($dbConn);
	else if ($op==7) echo f_update_imagen($dbConn);
	else if ($op==8) echo get_data_update_especie($dbConn);
	else if ($op==9) echo f_update_especie($dbConn);
	else if ($op==10) echo get_data_corralaje_especie($dbConn);
	else if ($op==11) echo f_update_corralaje($dbConn);
	else if ($op==12) echo get_data_new_servicio($dbConn);
	else if ($op==13) echo f_new_servicio($dbConn);
	else if ($op==14) echo f_update_estado_servicio($dbConn);
	else if ($op==15) echo Consultar_Servicios($dbConn,$_POST["Id"]);
	else if ($op==16) echo get_data_update_servicio($dbConn);
	else if ($op==17) echo f_update_servicio($dbConn);
	
}else{
}

function Listar_Especie_Animales($dbConn){
	$resultado="";
	$consulta="SELECT * FROM tbl_a_especies";
	$sql= $dbConn->prepare($consulta);
	$sql->execute();
	while ($row = $sql->fetch()) {
        $estadocorralaje = "ERROR-8771222";
        $color = "btn-warning";
        if ($row["espEstadoCorralaje"]==0){
            $estadocorralaje = "CORRALAJE DESACTIVADO";
            $color=" btn-danger";
        }else if ($row["espEstadoCorralaje"]==1){
            $estadocorralaje = "CORRALAJE ACTIVADO";
            $color=" btn-success";
        }
		$estadoespecie = "ERROR-12122";
		$color1="red";
		$nuevo_estado = 0;
		if ($row["espEstado"]==0) {
			$estadoespecie = "ESPECIE ACTIVA";
			$color1=" #196f3d";
			$nuevo_estado = 1;
		}elseif ($row["espEstado"]==1) {
			$estadoespecie = "ESPECIE INACTIVA";
			$color1=" #a93226";
			$nuevo_estado = 0;
		}
		$estadodetalle = "ERROR-12122";
		$color2="red";
		$nuevo_detalle = 0;
		if ($row["espDetalle"]==0) {
			$estadodetalle = "NO, DETALLAR";
			$color2=" #a93226";
			$nuevo_detalle = 1;
		}elseif ($row["espDetalle"]==1) {
			$estadodetalle = "SI, DETALLAR";
			$color2=" #196f3d";
			$nuevo_detalle = 0;
		}

        $table = Consultar_Servicios($dbConn,$row["espId"]);
        $resultado .='
            <div class="row">
                <div class="col-md-12">
                    <div class="card collapsed-card">
                        <div class="card-header"  data-card-widget="collapse" data-toggle="tooltip" title="Collapse" style="cursor: pointer;" >
                            <h1 class="card-title" >
								<b>'.strtoupper(utf8_encode($row["espDescripcion"])).' <span class="text-muted">('.strtoupper($row["espLetra"]).')</span></b>
                            </h1>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
							<div class="row">
								<div class="col-md-12">
									<div class="float-right">
										<button type="button"  class="btn btn-info btn-sm float-right" 
										onclick="get_data_edit_especie('.$row["espId"].')" data-toggle="modal" data-target="#modal">
											<b><i class="fas fa-pencil-alt"></i> EDITAR</b>
										</button>
										<button type="button" class="btn '.$color.' btn-sm float-right mr-2"
                                            onclick="get_data_edit_corralaje('.$row["espId"].')" data-toggle="modal" data-target="#modal" >
											<b><i class="fas fa-cog"></i> '.$estadocorralaje.'</b>
										</button>
										<br>
										<p class="text-right mt-4">
											<span class="badge" onclick="f_estado_especie('.$row["espId"].','.$nuevo_estado.')" style="cursor:pointer;color:'.$color1.'"><b>'.$estadoespecie.'</b></span>
											<br>
											<span class="badge" onclick="f_detalle_especie('.$row["espId"].','.$nuevo_detalle.')"  style="cursor:pointer;color:'.$color2.'"><b>'.$estadodetalle.'</b></span>
										</p>	
									</div>
									<div style="width:100px;box-shadow: rgba(0, 0, 0, 0.35) 0px 5px 15px;border-radius:5px;">
										<img onclick="get_data_img('.$row["espId"].')" src="../../recursos/especies/'.$row["espImagen"].'" alt="" width="100%"  style="cursor:pointer" data-toggle="modal" data-target="#modalN" >
									</div>
								</div>
							</div>
                            <div class="row">
								<div class="col-md-12">
									<div class="row"><div class="col-md-12" id="conttable-'.$row["espId"].'" >'.$table.'</div></div>
									<button type="button"  class="btn btn-info btn-sm" 
                                        onclick="get_data_new_servicio('.$row["espId"].')" data-toggle="modal" data-target="#modal">
										<b><i class="fas fa-plus"></i> NUEVO SERVICIO</b>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
					<hr>
                </div>
            </div>';
	}
	return $resultado;

}

function Consultar_Servicios($dbConn,$id){
	$resultado='<table class="mt-2 table table-sm table-bordered table-striped " style="text-align:center;">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Descripción</th>
                            <th>Código Yupak</th>
                            <th>Descripción Yupak</th>
                            <th>Precio</th>
                            <th>Estado</th>
                            <th>Editar</th>
                        </tr>
                    </thead>
                    <tbody>';
    $consulta="SELECT * FROM tbl_a_servicios WHERE espId = :id ";
	$sql= $dbConn->prepare($consulta);
	$sql->bindValue(':id',$id);
	$sql->execute();
	$cont = 0;
	$TotalActivo=0;
	$Total=0;
	while ($row = $sql->fetch()) {
		$cont++;
		$estado ="";
		$Total += $row["srnPrecio"];
		if ($row["srnEstado"]==0) {
			$estado ='<button type="button" onclick="f_estado_servivio(1,'.$row["srnId"].','.$row["espId"].')" class="btn btn-success btn-sm"><b>ACTIVO</b></button>';
            $TotalActivo += $row["srnPrecio"];
		}else{
			$estado ='<button type="button" onclick="f_estado_servivio(0,'.$row["srnId"].','.$row["espId"].')" class="btn btn-danger btn-sm"><b>INACTIVO</b></button>';
		}
		$resultado=$resultado . '
			<tr>
				<td>'.$cont.'</td>
				<td >'.utf8_encode($row["srnDescripcion"]).'</td>
				<td >'.$row["srnCodigoYupak"].'</td>
				<td >'.utf8_encode($row["srnDescripcionYupak"]).'</td>
				<td >'.number($row["srnPrecio"]).' $</td>
				<td >'.$estado.'</td>
				<td>
					<button  type="button" class="btn btn-sm btn-info" 
                    onclick="get_data_update_servicio('.$row["srnId"].')" data-toggle="modal" data-target="#modal">
						<b><i class="fas fa-pencil-alt"></i></b>
					</button>
				</td>
			</tr';
	}
	$footer = '<tfoot><tr>
		<th colspan="4" style="text-align:right;">NINGUN RESULTADO</th>
		<td></td>
	</tr></tfoot>';
	if ($Total > 0) {
		$footer = '
		<tfoot>
			<tr>
				<th colspan="4" style="text-align:right;">TOTAL ACTIVOS</th>
				<td>'.number($TotalActivo).' $</td>
			</tr>
			<tr>
				<th colspan="4" style="text-align:right;">TOTAL</th>
				<td>'.number($Total).' $</td>
			</tr>
		</tfoot>';
	}

	return $resultado."</tbody>".$footer."</table>";
}
//GET DATA
function get_data_new_especie(){
	$data = '
			<div class="row">
				<div class="col-md-6">
					<label>Nombre de la Especie:</label> <span class="text-muted">(max 30)</span>
					<input type="text" class="form-control form-control-sm" maxlength="30" id="txtEscpecie"
						placeholder="G. BOVINO">
				</div>
				<div class="col-md-4">
					<label>Tipo de Ganado: </label>
					<select id="slcGanado" class="form-control form-control-sm select2bs4"
						style="width:100%;cursor:pointer">
						<option value="1">Ganado Menor</option>
						<option value="0">Ganado Mayor</option>
					</select>
				</div>
				<div class="col-md-2">
					<label>Letra: </label>
					<input type="text" class="form-control text-center form-control-sm input_disablecopypaste" id="txtLetraEspecie"
						onkeyup="javascript:this.value=this.value.toUpperCase();"  onkeypress="f_restrincion_1(event)"  maxlength="1" placeholder="B">
				</div>
			</div>
			<hr>
			<label class="mb-0">Estancia Mínima:</label>
			<span style="font-weight: lighter;font-size:13px;" class="text-muted">
				Corresponde al tiempo que debe superar un animal para ser faenado, cuando se supere este tiempo
				el animal aparecerá en la orden de producción
			</span>
			<div class="row">
				<div class="col-4">
					<label for="txtHorasEstancia">Horas:</label>
					<div class="input-group input-group-sm">
						<input type="text" class="form-control text-center input_disablecopypaste" maxlength="3" id="txtHorasEstancia" value="0" onkeypress="f_restrincion(event)">
						<div class="input-group-append">
							<label class="input-group-text" for="txtHorasEstancia">H</label>
						</div>
					</div>
				</div>
				<div class="col-4">
					<label for="slcMinutos">Minutos:</label>
					<div class="input-group mb-3 input-group-sm">
						<select class="custom-select" id="slcMinutos">
						'.selecoption(0).'
						</select>
						<div class="input-group-append">
							<label class="input-group-text" for="slcMinutos">M</label>
						</div>
					</div>
				</div>
				<div class="col-4">
					<label for="slcSegundos">Segundos:</label>
					<div class="input-group mb-3 input-group-sm">
						<select class="custom-select" id="slcSegundos">
						'.selecoption(0).'
						</select>
						<div class="input-group-append">
							<label class="input-group-text" for="slcSegundos">S</label>
						</div>
					</div>
				</div>
			</div>
			<hr>
			<label>Corralaje:</label>
			<div class="row">
				<div class="col-md-6">
					<div class="form-check">
						<input class="form-check-input" style="cursor:pointer;" type="radio" name="radio"
							id="rd1" value="1" checked onclick="open_ac()">
						<label class="form-check-label" style="cursor:pointer;" for="rd1">Activado</label>
					</div>
				</div>
				<div class="col-md-6">
					<div class="form-check">
						<input class="form-check-input" style="cursor:pointer;" type="radio" name="radio"
							id="rd2" value="0" onclick="close_ac()">
						<label class="form-check-label" style="cursor:pointer;" for="rd2">Desactivado</label>
					</div>
				</div>
			</div>
			<hr>
			<div id="cont-active">
				<label>Tiempo permitido antes de empezar a cobrar corralaje:</label>
				<div class="row">
					<div class="col-4">
						<label for="txtHorasCorralaje">Horas:</label>
						<div class="input-group input-group-sm">
							<input type="text" class="form-control text-center input_disablecopypaste" id="txtHorasCorralaje" maxlength="3"
								value="0" onkeypress="f_restrincion(event)">
							<div class="input-group-append">
								<label class="input-group-text" for="txtHorasCorralaje">H</label>
							</div>
						</div>
					</div>
					<div class="col-4">
						<label for="slcMinutos_corralaje">Minutos:</label>
						<div class="input-group mb-3 input-group-sm">
							<select class="custom-select" id="slcMinutos_corralaje">
							'.selecoption(0).'
							</select>
							<div class="input-group-append">
								<label class="input-group-text" for="slcMinutos_corralaje">M</label>
							</div>
						</div>
					</div>
					<div class="col-4">
						<label for="slcSegundos_corralaje">Segundos:</label>
						<div class="input-group mb-3 input-group-sm">
							<select class="custom-select" id="slcSegundos_corralaje">
							'.selecoption(0).'
							</select>
							<div class="input-group-append">
								<label class="input-group-text" for="slcSegundos_corralaje">S</label>
							</div>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-md-4">
						<label>Taza de Corralaje:</label>
						<div class="input-group ">
							<input type="text" class="form-control form-control-sm input_disablecopypaste"  id="txtTaza"
								placeholder="0,00" onKeyPress="handleNumber(event,\'{6,2}\')">
							<div class="input-group-append">
								<span class="input-group-text"><i class="fas fa-dollar-sign"></i></span>
							</div>
						</div>
					</div>
					<div class="col-md-8">
						<label for="slcYupack">Código Yupak para el corralaje:</label><span class="text-muted ml-2" id="spnCodigo"></span>
						<select id="slcYupack" class="form-control select2bs4 " style="width: 100%;" onchange="select_yupak()">
							'.Data_Yupak(-1).'
						</select>
					</div>
				</div>
				<hr>
			</div>
			<label >Detalle de especie</label>
			<p>
				SELECCIONE <b>SI</b> o <b>NO</b> detallar indiviudalmente una guia de proceso.
				<span class="text-muted">Esta información será utilizada al momento de generar la <b>Orden de
						Producción</b></span>
			</p>
			<select id="slcDetalle" class="form-control form-control-sm select2bs4"  style="width:100%;cursor:pointer;">
				<option style="cursor:pointer;" value="0" selected="true" >NO, DETALLAR</option>
				<option style="cursor:pointer;" value="1" >SI, DETALLAR</option>
			</select>
			<hr>
			<div class="row">
				<div class="col-md-6">
					<label for="file-upload" class="btn btn-success">
						Seleccionar imagen
					</label>
				</div>
			</div>
			<div id="preview" style="width:100%;text-align:center;" ></div>
			<form id="form-file" class="d-none">
				<input id="file-upload"  type="file"/>
			</form>	';
	return modal($data,'AÑADIR UNA NUEVA ESPECIE','f_insert_new_especie()');
}
function get_data_update_especie($dbConn){
	$Id = $_POST['Id'];
	$consulta="SELECT * FROM tbl_a_especies WHERE espId = :id";
	$sql= $dbConn->prepare($consulta);
	$sql->bindValue(':id',$Id);
	$sql->execute();
	if($row = $sql->fetch()) {
		$array = explode(":",$row["espEstanciaPermitida"]);
		$horas = 0;
		$minutos = 0;
		$segundos = 0;
		if (count($array) == 3) {
			$horas = $array[0];
			$minutos = $array[1];
			$segundos = $array[2];
		}
		$tipo_ganado = "ERRPOR-1212";
		$selected1 = "";
		$selected2= "";
		if ($row["espLinea"]==0){
			$tipo_ganado = "G. Mayor";
			$selected1 = "selected";
		}
		else if ($row["espLinea"]==1){
			$tipo_ganado = "G. Menor";
			$selected2 = "selected";
		}
		$data = '
			<input type="hidden" id="txtIdEspecie" value="'.$row["espId"].'"> 
			<div class="row">
				<div class="col-md-6">
					<label>Nombre actual de la Especie:</label> 
					<span class="text-muted form-control form-control-sm">'.strtoupper(utf8_encode($row["espDescripcion"])).'</span>
				</div>
				<div class="col-md-6">
					<label>Nuevo nombre de la Especie:</label> <span class="text-muted">(max 30)</span>
					<input type="text" class="form-control form-control-sm" maxlength="30" id="txtEscpecie"
						placeholder="G. BOVINO" value="'.strtoupper(utf8_encode($row["espDescripcion"])).'">
				</div>
			</div>
			<hr>
			<div class="row">
				<div class="col-md-4">
					<label>Tipo de Ganado actual: </label>
					<span class="text-muted form-control form-control-sm">'.$tipo_ganado.'</span>
				</div>
				<div class="col-md-4">
					<label>Nuevo tipo de Ganado: </label>
					<select id="slcGanado" class="form-control form-control-sm select2bs4"
						style="width:100%;cursor:pointer">
						<option value="1" '.$selected1.'>Ganado Menor</option>
						<option value="0" '.$selected2.' >Ganado Mayor</option>
					</select>
				</div>
			</div>
			<hr>
			<div class="row">
				<div class="col-md-2">
					<label>Letra actual: </label>
					<span class="text-muted form-control form-control-sm text-center">'.strtoupper(utf8_encode($row["espLetra"])).'</span>
				</div>
				<div class="col-md-2">
					<label>Nueva letra: </label>
					<input type="text" class="form-control text-center form-control-sm input_disablecopypaste" id="txtLetraEspecie" value="'.strtoupper(utf8_encode($row["espLetra"])).'"
						onkeyup="javascript:this.value=this.value.toUpperCase();"  onkeypress="f_restrincion_1(event)"  maxlength="1" placeholder="B">
				</div>
				<div class="col-md-8">
					<span class="text-muted">Se recomienda no cambiar la Letra, porque puede ocacionar comprobantes repetidos</span>
				</div>
			</div>
			<hr>
			<label class="mb-0">Estancia Mínima:</label>
			<span style="font-weight: lighter;font-size:13px;" class="text-muted">
				Corresponde al tiempo que debe superar un animal para ser faenado, cuando se supere este tiempo
				el animal aparecerá en la orden de producción<br>
				<b>Estancia actual: '.$row["espEstanciaPermitida"].'</b>
			</span>
			<div class="row">
				<div class="col-4">
					<label for="txtHorasEstancia">Horas:</label>
					<div class="input-group input-group-sm">
						<input type="text" class="form-control text-center input_disablecopypaste" maxlength="3" id="txtHorasEstancia" value="'.$horas.'" onkeypress="f_restrincion(event)">
						<div class="input-group-append">
							<label class="input-group-text" for="txtHorasEstancia">H</label>
						</div>
					</div>
				</div>
				<div class="col-4">
					<label for="slcMinutos">Minutos:</label>
					<div class="input-group mb-3 input-group-sm">
						<select class="custom-select" id="slcMinutos">
						'.selecoption($minutos).'
						</select>
						<div class="input-group-append">
							<label class="input-group-text" for="slcMinutos">M</label>
						</div>
					</div>
				</div>
				<div class="col-4">
					<label for="slcSegundos">Segundos:</label>
					<div class="input-group mb-3 input-group-sm">
						<select class="custom-select" id="slcSegundos">
						'.selecoption($segundos).'
						</select>
						<div class="input-group-append">
							<label class="input-group-text" for="slcSegundos">S</label>
						</div>
					</div>
				</div>
			</div>';		
		return modal($data,'EDITAR ESPECIE','f_update_especie()');
	}else return modal('ERROR-88721','ERROR-88721','f_error()');
}
function get_data_imagen_especie($dbConn){
	$Id = $_POST['Id'];
	$consulta="SELECT * FROM tbl_a_especies WHERE espId = :id";
	$sql= $dbConn->prepare($consulta);
	$sql->bindValue(':id',$Id);
	$sql->execute();
	if($row = $sql->fetch()) {
		$data = '
		<input type="hidden" id="txtIdEspecie" value="'.$row["espId"].'"> 
		<div class="row">
			<div class="col-12">
				<h6 class="text-muted">'.strtoupper(utf8_encode($row["espDescripcion"])).'</h6>
			</div>
		</div>
		<hr class="mt-2">
		<div id="cont-img">
			<img id="img-view"  src="../../recursos/especies/'.$row["espImagen"].'" alt="NULL" width ="100%" >
		</div>
		<input type="file" class="d-none" id="file-img-new" >';
		return modal($data,'EDITAR IMAGEN','f_update_img()');
	}else return modal('ERROR-88721','ERROR-88721','f_error()');
}
function get_data_corralaje_especie($dbConn){
	$Id = $_POST['Id'];
	$consulta="SELECT * FROM tbl_a_especies WHERE espId = :id";
	$sql= $dbConn->prepare($consulta);
	$sql->bindValue(':id',$Id);
	$sql->execute();
	if($row = $sql->fetch()) {
		$array = explode(":",$row["espEstanciaCorralaje"]);
		$horas = 0;
		$minutos = 0;
		$segundos = 0;
		if (count($array) == 3) {
			$horas = $array[0];
			$minutos = $array[1];
			$segundos = $array[2];
		}
        $taza = "0,00";
        if (is_numeric($row["espPrecioCorralaje"]))$taza = $row["espPrecioCorralaje"];
        $c_yupak = -1;
        $d_yupak = f_descripcion_yupak($row["espCodigoYupak"]);
        if ($d_yupak != false)$c_yupak = $row["espCodigoYupak"];
        else $d_yupak ="NO SE PUDO ENCONTRAR EL SERVICIO YUPAK";
		$checked1 = "";
		$checked2= "";
        $corralaje = "ERROR-128127";
        $style='';
		if ($row["espEstadoCorralaje"]==0){
            $checked2= "checked";
            $corralaje = "corralaje actual desactivado";
            $style='style="display:none"';
		}
		else if ($row["espEstadoCorralaje"]==1){
			$checked1 = "checked";
            $corralaje = "corralaje actual activado";
		}
        // 
        $data = '
            <h5 class="text-muted">
                CORRALAJE PARA LA ESPECIE <b>'.strtoupper(utf8_encode($row["espDescripcion"])).'</b>
                <input type="hidden" id="txtIdEspecie" value="'.$row["espId"].'"> 
            </h5>
            <hr>
			<label>Corralaje: </label>
            <span class="text-muted"><b>'.$corralaje.'</b></span>
			<div class="row">
				<div class="col-md-6">
					<div class="form-check">
						<input class="form-check-input" style="cursor:pointer;" type="radio" name="radio"
							id="rd1" value="1" '.$checked1.'  onclick="open_ac()">
						<label class="form-check-label" style="cursor:pointer;" for="rd1">Activado</label>
					</div>
				</div>
				<div class="col-md-6">
					<div class="form-check">
						<input class="form-check-input" style="cursor:pointer;" type="radio" name="radio"
							id="rd2" value="0" '.$checked2.' onclick="close_ac()">
						<label class="form-check-label" style="cursor:pointer;" for="rd2">Desactivado</label>
					</div>
				</div>
			</div>
			<hr>
			<div id="cont-active" '.$style.'>
				<label>Tiempo permitido antes de empezar a cobrar corralaje:</label><br>
                <span class="text-muted"><b>Tiempo actual '.$row["espEstanciaCorralaje"].'</b></span>
				<div class="row">
					<div class="col-4">
						<label for="txtHorasCorralaje">Horas:</label>
						<div class="input-group input-group-sm">
							<input type="text" class="form-control text-center input_disablecopypaste" id="txtHorasCorralaje" maxlength="3"
								value="'.$horas.'" onkeypress="f_restrincion(event)">
							<div class="input-group-append">
								<label class="input-group-text" for="txtHorasCorralaje">H</label>
							</div>
						</div>
					</div>
					<div class="col-4">
						<label for="slcMinutos_corralaje">Minutos:</label>
						<div class="input-group mb-3 input-group-sm">
							<select class="custom-select" id="slcMinutos_corralaje">
							'.selecoption($minutos).'
							</select>
							<div class="input-group-append">
								<label class="input-group-text" for="slcMinutos_corralaje">M</label>
							</div>
						</div>
					</div>
					<div class="col-4">
						<label for="slcSegundos_corralaje">Segundos:</label>
						<div class="input-group mb-3 input-group-sm">
							<select class="custom-select" id="slcSegundos_corralaje">
							'.selecoption($segundos).'
							</select>
							<div class="input-group-append">
								<label class="input-group-text" for="slcSegundos_corralaje">S</label>
							</div>
						</div>
					</div>
				</div>
                <hr>
				<div class="row">
					<div class="col-md-4">
                        <span class="text-muted"><b>Taza actual: '.number($taza).'</b></span><br>
						<label>Nueva taza de corralaje:</label>
						<div class="input-group ">
							<input type="text" class="form-control form-control-sm input_disablecopypaste"  id="txtTaza"
								placeholder="0,00" onKeyPress="handleNumber(event,\'{6,2}\')" value="'.number($taza).'">
							<div class="input-group-append">
								<span class="input-group-text"><i class="fas fa-dollar-sign"></i></span>
							</div>
						</div>
					</div>
					<div class="col-md-8">
                        <span class="text-muted"><b>Servicio yupak actual: '.$d_yupak.'</b></span><br>
						<label for="slcYupack">Nuevo código yupak para el corralaje:</label><span class="text-muted ml-2" id="spnCodigo"></span>
						<select id="slcYupack" class="form-control select2bs4 " style="width: 100%;" onchange="select_yupak()">
							'.Data_Yupak($c_yupak).'
						</select>
					</div>
				</div>
				<hr>
			</div>';
		return modal($data,'EDITAR CORRALAJE','f_update_corralaje()');
	}else return modal('ERROR-88721','ERROR-88721','f_error()');
}
function get_data_new_servicio($dbConn){
        $Id = $_POST['Id'];
        $consulta="SELECT * FROM tbl_a_especies WHERE espId = :id";
        $sql= $dbConn->prepare($consulta);
        $sql->bindValue(':id',$Id);
        $sql->execute();
        if($row = $sql->fetch()) {
            $data = '
                <input type="hidden" id="txtIdEspecie" value="'.$row["espId"].'"> 
                <h5 class="text-muted">NUEVO SERVICIO PARA <b>'.strtoupper(utf8_encode($row["espDescripcion"])).'</b></h5>
                <hr>
                <div class="row">
                    <div class="col-md-6">
                        <label>Descripción del servicio:</label> <span class="text-muted">(max 30)</span>
                        <input type="text" class="form-control form-control-sm" maxlength="30" id="txtServicio"
                            placeholder="Faenamiento" >
                    </div>
                </div>
                <hr>
                <div class="row">
					<div class="col-md-4">
						<label>Precio:</label>
						<div class="input-group ">
							<input type="text" class="form-control form-control-sm input_disablecopypaste"  id="txtTaza"
								placeholder="0,00" onKeyPress="handleNumber(event,\'{6,2}\')" >
							<div class="input-group-append">
								<span class="input-group-text"><i class="fas fa-dollar-sign"></i></span>
							</div>
						</div>
					</div>
					<div class="col-md-8">
						<label for="slcYupack">Servicio Yupak:</label><span class="text-muted ml-2" id="spnCodigo"></span>
						<select id="slcYupack" class="form-control select2bs4 " style="width: 100%;" onchange="select_yupak()">
							'.Data_Yupak(-1).'
						</select>
					</div>
				</div>
                <hr>
                <label>Observación: </label>
                <textarea  class="form-control form-control-sm"  id="txtObservacion" cols="3"></textarea>';		
            return modal($data,'NUEVO SERVICIO','f_new_servicio()');
        }else return modal('ERROR-88721','ERROR-88721','f_error()');
}
function get_data_update_servicio($dbConn){
    $Id = $_POST['Id'];
    $consulta="SELECT * FROM tbl_a_servicios s, tbl_a_especies e WHERE s.espId = e.espId AND s.srnId = :id";
    $sql= $dbConn->prepare($consulta);
    $sql->bindValue(':id',$Id);
    $sql->execute();
    if($row = $sql->fetch()) {
        $taza = "0,00";
        if (is_numeric($row["srnPrecio"])) $taza = number($row["srnPrecio"]);
        $c_yupak = -1;
        $d_yupak = f_descripcion_yupak($row["srnCodigoYupak"]);
        if ($d_yupak != false)$c_yupak = $row["srnCodigoYupak"];
        else $d_yupak ="NO SE PUDO ENCONTRAR EL SERVICIO YUPAK";

        $data = '
            <input type="hidden" id="txtEspecie" value="'.$row["espId"].'"> 
            <input type="hidden" id="txtIdServicio" value="'.$row["srnId"].'"> 
            <h5 class="text-muted">EDITAR SERVICIO PARA LA ESPECIE<b>'.strtoupper(utf8_encode($row["espDescripcion"])).'</b></h5>
            <hr>
            <div class="row">
                <div class="col-md-6">
                    <label>Descripción actual del servicio:</label>
                    <span class="text-muted form-control form-control-sm">'.utf8_encode($row["srnDescripcion"]).'</span>
                </div>
                <div class="col-md-6">
                    <label>Nueva descripción del servicio:</label> <span class="text-muted">(max 30)</span>
                    <input type="text" class="form-control form-control-sm" maxlength="30" id="txtServicio" value="'.utf8_encode($row["srnDescripcion"]).'"
                        placeholder="Faenamiento" >
                </div>
            </div>
            <hr>
            <div class="row">
                <div class="col-md-4">
                    <label>Precio actual:</label>
                    <div class="input-group ">
                        <span class="text-muted form-control form-control-sm">'.$taza.'</span>
                        <div class="input-group-append">
                            <span class="input-group-text"><i class="fas fa-dollar-sign"></i></span>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <label>Nuevo precio:</label>
                    <div class="input-group ">
                        <input type="text" class="form-control form-control-sm input_disablecopypaste"  id="txtTaza"
                            placeholder="0,00" onKeyPress="handleNumber(event,\'{6,2}\')" value="'.$taza.'" >
                        <div class="input-group-append">
                            <span class="input-group-text"><i class="fas fa-dollar-sign"></i></span>
                        </div>
                    </div>
                </div>
            </div>
            <hr>
            <div class="row">
                <div class="col-md-4">
                    <label >Actual servicio Yupak:</label>
                    <span class="text-muted form-control form-control-sm">'.$d_yupak.' ('.$c_yupak.')</span>
                </div>
                <div class="col-md-4">
                    <label for="slcYupack">Nuevo servicio Yupak:</label><span class="text-muted ml-2" id="spnCodigo"></span>
                    <select id="slcYupack" class="form-control select2bs4 " style="width: 100%;" onchange="select_yupak()">
                        '.Data_Yupak($c_yupak).'
                    </select>
                </div>
            </div>
            <hr>
            <label>Observación: </label>
            <span class="text-muted">'.utf8_encode($row["srnObservaciones"]).'</span>
            <textarea  class="form-control form-control-sm"  id="txtObservacion" cols="3">'.utf8_encode($row["srnObservaciones"]).'</textarea>';		
        return modal($data,'EDITAR SERVICIO','f_update_servicio()');
    }else return modal('ERROR-88721','ERROR-88721','f_error()');
}
//INSERT
function f_insert_especie($dbConn){
	$nameImg = "null.png";
	if (isset($_FILES['file'])) {
		if (($_FILES["file"]["type"] == "image/pjpeg")
		|| ($_FILES["file"]["type"] == "image/jpeg")
		|| ($_FILES["file"]["type"] == "image/png")
		|| ($_FILES["file"]["type"] == "image/gif")) {
			$imgsize=filesize($_FILES["file"]["tmp_name"]);
			$imgsize =  ($imgsize / 1024) / 1024;
			if ($imgsize > 1) {
				return "El documento no puede superar 1 MB";
			}else{
				$name = obtener_estructura_directorios('../../recursos/especies/');
				$ext = explode("/",$_FILES["file"]["type"]);
				$name = $name.'.'.$ext[1];
				if (move_uploaded_file($_FILES["file"]["tmp_name"], "../../recursos/especies/".$name)) {
					$nameImg = $name;
				} else {
					return "No se pudo subir la imagen";
				}
			}
		} else {
			return 'El archivo selecionado no es una imagen';
		}
	}
	$especie = strtoupper(trim($_POST["Especie"]));
    if ($especie=="")return 'Ingrese una descipcion de la Especie';
	if (f_buscar($dbConn,array(array(":descripcion"),array($especie)),"SELECT * FROM tbl_a_especies WHERE espDescripcion = :descripcion"))return 'Ya se encuentra registrada una especie con esta descripción';
	$letra = strtoupper(trim($_POST["Letra"]));
	if(!preg_match("/^[A-Z]$/", $letra))return 'La letra seleccionada es incorrecta';
	if (f_buscar($dbConn,array(array(":letra"),array($letra)),"SELECT * FROM tbl_a_especies WHERE espLetra = :letra"))return 'La letra seleccionada ya se encuentra registrada';
	$ganado = trim($_POST["Ganado"]);
	if ($ganado == "0"  || $ganado == "1" );
	else return 'EL GANADO SELEECIONADO ES INCORRECTO';
	$estancia = "0:00:00";
	if (is_numeric($_POST["HorasEstancia"]) && is_numeric($_POST["MinutosEstancia"]) && is_numeric($_POST["SegundosEstancia"]) ) {
		$estancia = intval($_POST["HorasEstancia"]).":".intval($_POST["MinutosEstancia"]).":".intval($_POST["SegundosEstancia"]);
	}else return 'El tiempo de Estancia Minima es incorrecto';
	$corralaje = $_POST["Corralaje"];
	if ($corralaje == "0"  || $corralaje == "1" ) ;
	else return 'Estado de corralaje incorrecto';
	$estanciacorralaje = "0:00:00";
	if ($corralaje == 1) {// Activado
		if (is_numeric($_POST["HorasCorralaje"]) && is_numeric($_POST["MinutosCorralaje"]) && is_numeric($_POST["SegundosCorralaje"]) ) {
			$estanciacorralaje = intval($_POST["HorasCorralaje"]).":".intval($_POST["MinutosCorralaje"]).":".intval($_POST["SegundosCorralaje"]);
		}else return 'El tiempo de Corralaje es incorrecto';
	}
	$taza = trim($_POST["Taza"]);
	$taza = str_replace(".", "", $taza);
	$taza = str_replace(",", ".", $taza);
	if (!is_numeric($taza))return 'El precio de corralaje es incorrecto';
	$c_yupak = $_POST["CodigoYupak"];
	$d_yupak = f_descripcion_yupak($c_yupak);
	if ($d_yupak == false) return 'Código yupak incorrecto';
	$detalle = $_POST["Detalle"];
	if ($detalle == "0"  || $detalle == "1" );
	else return 'El detalle es incorrecto';
	$consulta = "INSERT INTO tbl_a_especies(espDescripcion,espLetra,espLinea,espEstanciaPermitida,espEstadoCorralaje,espEstanciaCorralaje,espPrecioCorralaje,espCodigoYupak,espCodigoYupakDescripcion,espDetalle,espImagen) 
	VALUES (:espDescripcion,:espLetra,:espLinea,:espEstanciaPermitida,:espEstadoCorralaje,:espEstanciaCorralaje,:espPrecioCorralaje,:espCodigoYupak,:espCodigoYupakDescripcion,:espDetalle,:espImagen)";
	$array =  array(array(":espDescripcion",":espLetra",":espLinea",":espEstanciaPermitida",":espEstadoCorralaje",":espEstanciaCorralaje",":espPrecioCorralaje",":espCodigoYupak",":espCodigoYupakDescripcion",":espDetalle",":espImagen"),
					array($especie,$letra,$ganado,$estancia,$corralaje,$estanciacorralaje,$taza,$c_yupak,$d_yupak,$detalle,$nameImg));
	$Error ="Nueva especie";
	$Acion ="Nueva especie";
	$mensaje_ganado = "GANADO MENOR";
	if ($ganado == 1)$mensaje_ganado = "GANADO MAYOR";
	$mensaje_corralaje = "DESACTIVADO";
	if ($corralaje==1)$mensaje_corralaje = "DACTIVADO";
	$mensaje_detalle = "NO";
	if ($detalle == 1)$mensaje_detalle = "SI";
	$detalle='<b>'.$especie.'</b><br>'.
		'Letra: '.$letra.' <br>'.
		'Ganado  : '.$mensaje_ganado.'<br>'.
		'Estancia  : '.$estancia.'<br>'.
		'Corralaje  : '.$mensaje_corralaje.' <br>'.
		'Tiempo corralaje  : '.$estanciacorralaje.' <br>'.
		'Precio corralaje : '.$taza.' <br>'.
		'Servicio Yupak  : '.$d_yupak.' ('.$c_yupak.')<br>'.
		'Detalle  : '.$d_yupak.' ('.$c_yupak.')<br>'.
		'Detalle  : '.$mensaje_detalle.'<br>'.
		'<a href="../recursos/especies/'.$nameImg.'" target="_blank">Imagen</a>';
	return f_consulta($dbConn,$array,$consulta,$Error,$detalle,$Acion,0,'tbl_a_especies');
}
function obtener_estructura_directorios($ruta){
	// Imagen-0
	$cont = 0;
    // Se comprueba que realmente sea la ruta de un directorio
    if (is_dir($ruta)){
        // Abre un gestor de directorios para la ruta indicada
        $gestor = opendir($ruta);
        // Recorre todos los elementos del directorio
        while (($archivo = readdir($gestor)) !== false)  {
            $ruta_completa = $ruta . $archivo;
            // Se muestran todos los archivos y carpetas excepto "." y ".."
            if ($archivo != "." && $archivo != "..") {
                if (!is_dir($ruta_completa)) {
					$array = explode(".",$archivo);
					try {
						$array2 = explode("-",$array[0]);
						if ($array2[0]=="Imagen") {
							$cont++;
						}
					} catch (Exception $e) {}
                }
            }
        }
        closedir($gestor);
    } else {
        return "No se econtro la ruta del directorio";
    }
	return "Imagen-".($cont+1);
}
function f_update_corralaje($dbConn){
	$Id = $_POST['Id'];
	$consulta="SELECT * FROM tbl_a_especies WHERE espId = :id";
	$sql= $dbConn->prepare($consulta);
	$sql->bindValue(':id',$Id);
	$sql->execute();
	if($row = $sql->fetch()) {
        $corralaje = $_POST["Corralaje"];
        if ($corralaje == "0"  || $corralaje == "1" ) ;
        else return 'Estado de corralaje incorrecto';
        $taza = trim($_POST["Taza"]);
        $taza = str_replace(".", "", $taza);
        $taza = str_replace(",", ".", $taza);
        if (!is_numeric($taza))return 'El precio de corralaje es incorrecto';
        $c_yupak = $_POST["CodigoYupak"];
        $d_yupak = f_descripcion_yupak($c_yupak);
        if ($d_yupak == false) return 'Código yupak incorrecto';
        $estanciacorralaje = "0:00:00";
        $detalle =  '<b>'. utf8_encode($row["espDescripcion"]).'</b><br>Corralaje desactivado';
        if ($corralaje == 1) {// Activado
            if (is_numeric($_POST["HorasCorralaje"]) && is_numeric($_POST["MinutosCorralaje"]) && is_numeric($_POST["SegundosCorralaje"]) ) {
                $estanciacorralaje = intval($_POST["HorasCorralaje"]).":".intval($_POST["MinutosCorralaje"]).":".intval($_POST["SegundosCorralaje"]);
                $detalle =  '<b>'. utf8_encode($row["espDescripcion"]).'</b><br>'.
                    'CORRALAJE ACTIVADO <br>'.
                    'Taza: '. $taza.' <br>'.
                    'Tiempo Corralaje :'. $estanciacorralaje.'';
            }else return 'El tiempo de Corralaje es incorrecto';
        }
		$Acion = "Actualización del corralaje de la Especie";
        $Error = "Error al actualizar el corralaje de la especie";
		$consulta = "UPDATE tbl_a_especies SET espEstadoCorralaje = :espEstadoCorralaje,espEstanciaCorralaje = :espEstanciaCorralaje,espPrecioCorralaje = :espPrecioCorralaje,espCodigoYupak = :espCodigoYupak , espCodigoYupakDescripcion = :espCodigoYupakDescripcion
                    WHERE espId = :id";
        $array =  array(array(":espEstadoCorralaje",":espEstanciaCorralaje",":espPrecioCorralaje",":espCodigoYupak",":espCodigoYupakDescripcion",":id"),
					array($corralaje,$estanciacorralaje,$taza,$c_yupak,$d_yupak,$Id));
        return f_consulta($dbConn,$array,$consulta,$Error,$detalle,$Acion,$Id,'tbl_a_especies');
	}else return "ERROR-7765542";//NO SE ENCONTRO EL ID
}
function f_new_servicio($dbConn){
	$Id = $_POST['Id'];
	$consulta="SELECT * FROM tbl_a_especies WHERE espId = :id";
	$sql= $dbConn->prepare($consulta);
	$sql->bindValue(':id',$Id);
	$sql->execute();
	if($row = $sql->fetch()) {
        $servicio = trim($_POST["Servicio"]);
        if ($servicio=="")return 'Ingrese una descipcion del servicio';
	    if (f_buscar($dbConn,array(array(":srnDescripcion",":id"),array($servicio,$Id)),"SELECT * FROM tbl_a_servicios WHERE srnDescripcion = :srnDescripcion AND espId = :id"))return 'Ya se encuentra registrada una especie con esta descripción';
        $taza = trim($_POST["Taza"]);
        $taza = str_replace(".", "", $taza);
        $taza = str_replace(",", ".", $taza);
        if (!is_numeric($taza))return 'El precio del servicio es incorrecto';
        $c_yupak = $_POST["CodigoYupak"];
        $d_yupak = f_descripcion_yupak($c_yupak);
        if ($d_yupak == false) return 'Código yupak incorrecto';
        $observaciones = trim($_POST["Observaciones"]);
		$Acion = "Nuevo servicio";
        $Error = "Nuevo servicio";
        $detalle ='Servicio <b>'.$servicio.'</b><br>'.
                    'Precio: '.$taza.'<br>'.
                    'Servicio Yupak: '.$d_yupak.' ('.$c_yupak.')<br>'.
                    'Obseraciones: '.$observaciones.'<br>';
		$consulta = "INSERT INTO tbl_a_servicios(srnDescripcion,srnPrecio,srnCodigoYupak,srnDescripcionYupak,srnObservaciones,espId)
                    VALUES(:srnDescripcion,:srnPrecio,:srnCodigoYupak,:srnDescripcionYupak,:srnObservaciones,:espId)";
        $array =  array(array(":srnDescripcion",":srnPrecio",":srnCodigoYupak",":srnDescripcionYupak",":srnObservaciones",":espId"),
					array($servicio,$taza,$c_yupak,$d_yupak,$observaciones,$Id));
        return f_consulta($dbConn,$array,$consulta,$Error,$detalle,$Acion,0,'tbl_a_servicios');
	}else return "ERROR-7765542";//NO SE ENCONTRO EL ID
}
//UPDATE
function f_update_estado_especie($dbConn){
	$Id = $_POST['Id'];
	$Estado = $_POST['Estado'];
	$consulta="SELECT * FROM tbl_a_especies WHERE espId = :id";
	$sql= $dbConn->prepare($consulta);
	$sql->bindValue(':id',$Id);
	$sql->execute();
	if($row = $sql->fetch()) {
		$detalle = "";
		$Acion = "Estado de la Especie";
		$Error = "Error al actualizar el estado de la especie (".$Estado.")";
		if ($Estado==0){
			$detalle = "Se activo la especie (".utf8_encode($row["espDescripcion"]).")";
		}else if ($Estado==1){
			$Acion = "Estado del producto";
			$detalle = "Se desactivo la especie (".utf8_encode($row["espDescripcion"]).")";
		}else {
			return "No se encontro el estado de la especie";
		}
		$array =  array(array(":estado",":id"),array($Estado,$Id));
		$consulta = "UPDATE tbl_a_especies SET espEstado = :estado WHERE espId = :id";
        return f_consulta($dbConn,$array,$consulta,$Error,$detalle,$Acion,$Id,'tbl_a_especies');
	}else return "ERROR-7765542";//NO SE ENCONTRO EL ID
}
function f_update_detalle_especie($dbConn){
	$Id = $_POST['Id'];
	$Estado = $_POST['Estado'];
	$consulta="SELECT * FROM tbl_a_especies WHERE espId = :id";
	$sql= $dbConn->prepare($consulta);
	$sql->bindValue(':id',$Id);
	$sql->execute();
	if($row = $sql->fetch()) {
		$detalle = "";
		$Acion = "Detalle de especie";
		$Error = "Error al actualizar el detalle de la especie (".$Estado.")";
		if ($Estado==0){
			$detalle = "No se detalla la especie (".utf8_encode($row["espDescripcion"]).")";
		}else if ($Estado==1){
			$Acion = "Estado del producto";
			$detalle = "Se detalla la especie  (".utf8_encode($row["espDescripcion"]).")";
		}else {
			return "No se encontro el estado del detalle de la especie";
		}
		$array =  array(array(":estado",":id"),array($Estado,$Id));
		$consulta = "UPDATE tbl_a_especies SET espDetalle = :estado WHERE espId = :id";
        return f_consulta($dbConn,$array,$consulta,$Error,$detalle,$Acion,$Id,'tbl_a_especies');
	}else return "ERROR-7765542";//NO SE ENCONTRO EL ID
}
function f_update_imagen($dbConn){
	if (isset($_FILES['file'])) {
		if (($_FILES["file"]["type"] == "image/pjpeg")
		|| ($_FILES["file"]["type"] == "image/jpeg")
		|| ($_FILES["file"]["type"] == "image/png")
		|| ($_FILES["file"]["type"] == "image/gif")) {
			$imgsize=filesize($_FILES["file"]["tmp_name"]);
			$imgsize =  ($imgsize / 1024) / 1024;
			if ($imgsize > 1) {
				return "El documento no puede superar 1 MB";
			}else{
				$Id = $_POST['Id'];
				$consulta="SELECT * FROM tbl_a_especies WHERE espId = :id";
				$sql= $dbConn->prepare($consulta);
				$sql->bindValue(':id',$Id);
				$sql->execute();
				if($row = $sql->fetch()) {
					$name = "null.png";
					$ext = explode("/",$_FILES["file"]["type"]);
					if ($row["espImagen"]=="null.png") {
						$name = obtener_estructura_directorios('../../recursos/especies/');
						$name = $name.'.'.$ext[1];
					}else{
						$array= explode(".",$row["espImagen"]);
						$name = $array[0].'.'.$ext[1];
					}
					if (move_uploaded_file($_FILES["file"]["tmp_name"], "../../recursos/especies/".$name)) {
						$detalle = '<a href="../recursos/especies/'.$name.'" target="_blank">Nueva Imagen</a>';;
						$Acion = "Edición de Imagen de Especie";
						$Error = "Error al actualizar la imagen de la especie";
						$array =  array(array(":imagen",":id"),array($name,$Id));
						$consulta = "UPDATE tbl_a_especies SET espImagen = :imagen WHERE espId = :id";
						return f_consulta($dbConn,$array,$consulta,$Error,$detalle,$Acion,$Id,'tbl_a_especies');
					} else {
						return "No se pudo subir la imagen";
					}
				}else return "ERROR-28722";

			}
		} else {
			return 'El archivo selecionado no es una imagen';
		}
	}
}
function f_update_especie($dbConn){
	$Id = $_POST['Id'];
	$consulta="SELECT * FROM tbl_a_especies WHERE espId = :id";
	$sql= $dbConn->prepare($consulta);
	$sql->bindValue(':id',$Id);
	$sql->execute();
	if($row = $sql->fetch()) {
        $especie = strtoupper(trim($_POST["Especie"]));
        if ($especie=="")return 'Ingrese una descipcion de la Especie';
        if (f_buscar($dbConn,array(array(":descripcion",":id"),array($especie,$Id)),"SELECT * FROM tbl_a_especies WHERE espDescripcion = :descripcion AND espId != :id"))return 'Ya se encuentra registrada una especie con esta descripción';
        $letra = strtoupper(trim($_POST["Letra"]));
        if(!preg_match("/^[A-Z]$/", $letra))return 'La letra seleccionada es incorrecta';
        if (f_buscar($dbConn,array(array(":letra",":id"),array($letra,$Id)),"SELECT * FROM tbl_a_especies WHERE espLetra = :letra AND espId != :id"))return 'La letra seleccionada ya se encuentra registrada';
        $ganado = trim($_POST["Ganado"]);
        if ($ganado == "0"  || $ganado == "1" );
        else return 'EL GANADO SELEECIONADO ES INCORRECTO';
        $estancia = "0:00:00";
        if (is_numeric($_POST["HorasEstancia"]) && is_numeric($_POST["MinutosEstancia"]) && is_numeric($_POST["SegundosEstancia"]) ) {
            $estancia = intval($_POST["HorasEstancia"]).":".intval($_POST["MinutosEstancia"]).":".intval($_POST["SegundosEstancia"]);
        }else return 'El tiempo de Estancia Minima es incorrecto';

        $mensaje_ganado_old = "GANADO MENOR";
        if ($row["espLinea"] == 1)$mensaje_ganado_old = "GANADO MAYOR";

        $mensaje_ganado_new = "GANADO MENOR";
        if ($ganado == 1)$mensaje_ganado_new = "GANADO MAYOR";

		$detalle =  'Especie [ '. utf8_encode($row["espDescripcion"]).' ] => [ '.$especie.' ]<br>'.
                    'Letra [ '. utf8_encode($row["espLetra"]).' ] => [ '.$letra.' ]<br>'.
                    'Ganado [ '. $mensaje_ganado_old.' ] => [ '.$mensaje_ganado_new.' ]<br>'.
                    'Tiempo Estancia [ '. utf8_encode($row["espEstanciaPermitida"]).' ] => [ '.$estancia.' ]<br>';
		$Acion = "Actualización de la Especie";
        $Error = "Error al actualizar la especie";
		$consulta = "UPDATE tbl_a_especies SET espDescripcion = :espDescripcion,espLetra = :espLetra,espLinea = :espLinea,espEstanciaPermitida = :espEstanciaPermitida 
                    WHERE espId = :id";
        $array =  array(array(":espDescripcion",":espLetra",":espLinea",":espEstanciaPermitida",":id"),
					array($especie,$letra,$ganado,$estancia,$Id));
        return f_consulta($dbConn,$array,$consulta,$Error,$detalle,$Acion,$Id,'tbl_a_especies');
	}else return "ERROR-7765542";//NO SE ENCONTRO EL ID
}
function f_update_estado_servicio($dbConn){
	$Id = $_POST['Id'];
	$Estado = $_POST['Estado'];
	$consulta="SELECT * FROM tbl_a_servicios WHERE srnId = :id";
	$sql= $dbConn->prepare($consulta);
	$sql->bindValue(':id',$Id);
	$sql->execute();
	if($row = $sql->fetch()) {
		$detalle = "";
		$Acion = "Estado del servicio";
		$Error = "Error al actualizar el estado del servicio (".$Estado.")";
		if ($Estado==0){
			$detalle = "Se activo el servicio (".utf8_encode($row["srnDescripcion"]).")";
		}else if ($Estado==1){
			$Acion = "Estado del producto";
			$detalle = "Se desactivo el servicio (".utf8_encode($row["srnDescripcion"]).")";
		}else {
			return "No se encontro el estado del servicio";
		}
		$array =  array(array(":estado",":id"),array($Estado,$Id));
		$consulta = "UPDATE tbl_a_servicios SET srnEstado = :estado WHERE srnId = :id";
        return f_consulta($dbConn,$array,$consulta,$Error,$detalle,$Acion,$Id,'tbl_a_servicios');
	}else return "ERROR-7765542";//NO SE ENCONTRO EL ID
}
function f_update_servicio($dbConn){
	$Id = $_POST['Id'];
	$consulta="SELECT * FROM tbl_a_servicios s, tbl_a_especies e WHERE s.espId = e.espId AND s.srnId = :id";
	$sql= $dbConn->prepare($consulta);
	$sql->bindValue(':id',$Id);
	$sql->execute();
	if($row = $sql->fetch()) {
		$servicio = trim($_POST["Servicio"]);
        if ($servicio=="")return 'Ingrese una descipcion del servicio';
	    if (f_buscar($dbConn,array(array(":srnDescripcion",":id","id2"),array($servicio,$row["espId"],$Id)),"SELECT * FROM tbl_a_servicios WHERE srnDescripcion = :srnDescripcion AND espId = :id AND srnId = :id2"))return 'Ya se encuentra registrada una especie con esta descripción';
        $taza = trim($_POST["Taza"]);
        $taza = str_replace(".", "", $taza);
        $taza = str_replace(",", ".", $taza);
        if (!is_numeric($taza))return 'El precio del servicio es incorrecto';
        $c_yupak = $_POST["CodigoYupak"];
        $d_yupak = f_descripcion_yupak($c_yupak);
        if ($d_yupak == false) return 'Código yupak incorrecto';
        $observaciones = trim($_POST["Observaciones"]);
		$Acion = "Actualización de servicio";
        $Error = "Actualización de servicio";
        $detalle ='Servicio [ '.utf8_encode($row["srnDescripcion"]).' ] = > [ '.$servicio.' ]<br>'.
                    'Precio [ '.$row["srnPrecio"].' ] = > [ '.$taza.' ]<br>'.
                    'Servicio Yupak [ '.utf8_encode($row["srnDescripcionYupak"]).' ('.$row["srnCodigoYupak"].') ] = > [ '.$d_yupak.' ('.$c_yupak.') ]<br>'.
                    'Obseraciones [ '.utf8_encode($row["srnObservaciones"]).' ] = > ['.$observaciones.']<br>';
		$consulta = "UPDATE tbl_a_servicios SET srnDescripcion = :srnDescripcion,
                    srnPrecio = :srnPrecio,srnCodigoYupak = :srnCodigoYupak,
                    srnDescripcionYupak = :srnDescripcionYupak, srnObservaciones = :srnObservaciones
                    WHERE srnId = :id";
        $array =  array(array(":srnDescripcion",":srnPrecio",":srnCodigoYupak",":srnDescripcionYupak",":srnObservaciones",":id"),
					array($servicio,$taza,$c_yupak,$d_yupak,$observaciones,$Id));
        return f_consulta($dbConn,$array,$consulta,$Error,$detalle,$Acion,$Id,'tbl_a_servicios');
	}else return "ERROR-7765542";//NO SE ENCONTRO EL ID
}
//MODAL 
function modal($data,$titulo,$function){
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
		<button type="button" id="btnCerrar" class="btn btn-light"
			data-dismiss="modal"><b>CERRAR</b></button>
		<button type="button" class="btn btn-primary" onclick="'.$function.'">
			<b>GUARDAR</b>
		</button>
	</div>';
}
//Segundos y números 
function selecoption($number_select){
	$resultado = '';
	for ($i=0; $i < 60 ; $i++) { 
		if ($number_select == $i) $selected = 'selected';
		else $selected = '';
		if (strlen($i) == 1)$resultado .= '<option  value="0'.$i.'" '.$selected.'>0'.$i.'</option>';
		else $resultado .= '<option value="'.$i.'" '.$selected.'>'.$i.'</option>';
	}
	return $resultado;
}
//SERVICIOS YUPAK
function Data_Yupak($number_select){
	include '../../FilePHP/consql.php';
	if ($number_select == -1)$resultado ='<option value="NULL" >SELECCIONE UNO</option>';
	else$resultado ='<option value="NULL" >SELECCIONE UNO</option>';
	$sql = mssql_query('SELECT * FROM YP_FAC_SERVICE');
	while($row = mssql_fetch_array($sql)){
		if ($number_select == $row["Codigo"])$selected = 'selected';
		else $selected = '';

		$resultado .='<option value="'.$row["Codigo"].'" '.$selected.' >'.utf8_encode($row["Descripcion"]).'</option>';
	}
	return $resultado;
}
function f_descripcion_yupak($codigo){
	include '../../FilePHP/consql.php';
	$sql = mssql_query('SELECT * FROM YP_FAC_SERVICE WHERE Codigo = '.$codigo);
	if($row = mssql_fetch_array($sql)) return utf8_encode($row["Descripcion"]);
	else return false;
}

//Envio de consulta INSERT, UPDATE, DELETE
function f_consulta($dbConn,$array,$consulta,$Error,$detalle,$Acion,$Id,$tabla){
	try {
		$sql= $dbConn->prepare($consulta);
		for ($i=0; $i < count($array[0]); $i++) { 
			$sql->bindValue($array[0][$i],utf8_decode($array[1][$i]));
		}
		if ($sql->execute()){
			if ($Id=0)$Id= $dbConn->lastInsertId();
			if(Insert_Login($Id,$tabla,$Acion,$detalle,'')) return true;
			else return 'ERROR-092222';
		}else return "ERROR-665242";//
	}  catch (Exception $e) {
		Insert_Error('ERROR-887222',$e->getMessage(),$Error);
		exit("ERROR-887222");
	}
}
//Envio de BUSCAR
function f_buscar($dbConn,$array,$consulta){
	try {
		$sql= $dbConn->prepare($consulta);
		for ($i=0; $i < count($array[0]); $i++) { 
			$sql->bindValue($array[0][$i],utf8_decode($array[1][$i]));
		}
		$sql->execute();
		if ($row = $sql->fetch())return true;
		else return false;
	}  catch (Exception $e) {
		Insert_Error('ERROR-887222',$e->getMessage(),'ERROR EN LA BUSQUEDA');
		exit("ERROR-887222");
	}
}
function number($str){
	try{
		$array = explode(".",$str);
		$enteros = $array[0];
		$decimales="";
		if (isset($array[1])) {
			$decimales = $array[1];
		}else{
			$decimales = "00";
		}
		$arryenteros = str_split($enteros);
		$tam = count($arryenteros);
		$numero = "";
		$cont1 = 0;
		for ($i = ($tam-1); $i >=0 ; $i--) { 
			$numero .= $arryenteros[$i];
			$cont1++;
			if ($cont1==3) {
				$numero .= '.';    
				$cont1=0;
			}
		}
		$nuevo_array =str_split($numero);
		$tam_n = count($nuevo_array);
		$nuevo_numero ="";
		for ($i=($tam_n-1); $i >=0 ; $i--) { 
			$id=$tam_n-1;
			if ($i == $id) {
				if ($nuevo_array[$i]!=".") {
					$nuevo_numero .= $nuevo_array[$i];  
				}
			}else{
				$nuevo_numero .= $nuevo_array[$i];  
			}
		}
		return $nuevo_numero.",".$decimales;
	}
	catch (Exception $e){
			return "Erro1 ".$e ;
	}
}

?>