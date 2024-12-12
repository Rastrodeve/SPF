<?php
// $_SESSION['SENTENCIA-MYSQL'] = [0,0,0,0];//Base,Tabla,Opcion,Menu
$txtConsulta = '<textarea class="form-control form-control-sm mb-2" id="txtCont" rows="8"></textarea>';
$btnConsulta = '<button id="btn" class="btn btn-info float-right  btn-sm" onclick="f_get_consulta()"><b>EJECUTAR CONSULTAR</b></button> ';
$btnAuxiliar = '<button class="btn btn-sm bnt-light" onclick="f_insert_consulta(0)"><b>SELECT</b></button>
<button class="btn btn-sm bnt-light" onclick="f_insert_consulta(1)"><b>INSERT</b></button>
<button class="btn btn-sm bnt-light" onclick="f_insert_consulta(2)"><b>UPDATE</b></button>';
if (isset($_REQUEST["op"])) {
	require '../../FilePHP/utils.php';
	$op = $_REQUEST["op"];
	if ($op==1)echo f_get_menu_lateral();
	else if ($op==2)echo f_get_menu_superior();
	else if ($op==3)echo f_select_data();
	else if ($op==4)echo f_ejecutar_consulta();
	else if ($op==5)echo f_insert_consulta();
	else if ($op==6)echo f_update();
}else{
}
function conect_1($db){
	try {
		$conn = new PDO("mysql:host={$db['host']}",$db['user'],$db['password']);
		$conn->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
		return $conn;
		} catch (PDOException $e) {
			exit("Error en la conección 1". $e->getMessage());
		}
}
function conect_2($db,$base){
	try {
		$conn = new PDO("mysql:host={$db['host']};dbname={$base}",$db['user'],$db['password']);
		$conn->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
		return $conn;
	} catch (PDOException $e) {
		exit("Error en la conección ". $e->getMessage());
	}
}


function f_get_database(){
	$array = [];
	$file = fopen("../../FilePHP/file.txt", "r");
	while(!feof($file)) array_push($array,trim(fgets($file)));
	fclose($file);
	return $array;
}
function f_get_menu_lateral(){
	global $dblConsultaSql;
	$databases = f_get_database();
	$result= '
			<nav id="menu"> 
				<ul>
				<li class="lib-sub1" ><a onclick="f_select(\'0\',0)"><i class="fas fa-horizontal-rule"></i> Bases de datos</a></li>';
	foreach ($databases as $valor) {
		$mostrar_base = 'style="display:none"';
		if ("".$_SESSION['SENTENCIA-MYSQL'][0]."" ==$valor) $mostrar_base = '';
		$result .= '<li class="lib-sub1">
						<a title="'.$valor.'" onclick="f_select(\''.trim($valor).'\',0)" ><i class="fas fa-database mr-2"></i>'.$valor.'</a>
						<ul '.$mostrar_base.'>';
		$pdo = conect_2($dblConsultaSql,$valor);
		$tables =  f_get_consulta($pdo,'SHOW TABLES FROM '.$valor);
		foreach ($tables as $table) {
			$mostrar_tabla = 'style="display:none"';
			if ("".$_SESSION['SENTENCIA-MYSQL'][1]."" == $table["Tables_in_".$valor]) $mostrar_tabla = '';
			$result .= '<li>
							<a title="'.$table["Tables_in_".$valor].'" onclick="f_select(\''.trim($table["Tables_in_".$valor]).'\',1)"  ><i class="fas fa-table mr-1"></i>'.$table["Tables_in_".$valor].'</a>
							<ul '.$mostrar_tabla.'>';
			$items =  f_get_consulta($pdo,'SHOW COLUMNS FROM '.$table["Tables_in_".$valor]);
			foreach ($items as $colum) {
				$result .= '<li><a title="'.$colum["Field"].'">'.$colum["Field"].'</a></li>';
			}
			$result .='</ul></li>';
		}
		$result .='</ul></li>';
	}
	return  $result.'</ul></nav>';
}
function f_get_menu_superior(){
	// $_SESSION['SENTENCIA-MYSQL'] = [0,0,0,0];//Base,Tabla,Opcion,Menu
	$menu_raiz='<li class="breadcrumb-item active"><a onclick="f_select(\'0\',0)">Base de datos</a></li>';
	$active = ['',''];
	for ($i=0; $i < 3 ; $i++) {
		if ($_SESSION['SENTENCIA-MYSQL'][2] == $i) $active[$i] ='active';
	}

	$menu_opcion = '
	<div class="btn-group  btn-group-toggle  float-right" data-toggle="buttons">
		<label class="btn bg-olive '.$active[0].'" onclick="f_select(0,2)">
			<input type="radio" name="options" id="option3" autocomplete="off">
			<i class="fas fa-database"></i> Base de Datos
		</label>
		<label class="btn bg-olive '.$active[1].'" onclick="f_select(1,2)">
			<input type="radio" name="options" id="option1" autocomplete="off"
				checked="">
			<i class="fas fa-scroll"></i> Consulta
		</label>
	</div>';
	if ($_SESSION['SENTENCIA-MYSQL'][0] != "0") {
		$menu_raiz ='<li class="breadcrumb-item " ><a onclick="f_select(\''.trim($_SESSION['SENTENCIA-MYSQL'][0]).'\',0)" >'.$_SESSION['SENTENCIA-MYSQL'][0].'</a></li>';
		$menu_opcion = '
		<div class="btn-group  btn-group-toggle  float-right" data-toggle="buttons">
			<label class="btn bg-olive '.$active[0].'" onclick="f_select(0,2)">
				<input type="radio" name="options" id="option3" autocomplete="off">
				<i class="fas fa-table"></i> Tablas
			</label>
			<label class="btn bg-olive '.$active[1].'" onclick="f_select(1,2)">
				<input type="radio" name="options" id="option1" autocomplete="off"
					checked="">
				<i class="fas fa-scroll"></i> Consulta
			</label>
		</div>';
		if ($_SESSION['SENTENCIA-MYSQL'][1] != "0") {
			$menu_raiz .= '<li class="breadcrumb-item "><a onclick="f_select(\''.trim($_SESSION['SENTENCIA-MYSQL'][1]).'\',1)" >'.$_SESSION['SENTENCIA-MYSQL'][1].'</a></li>';
			$menu_opcion ='
			<div class="btn-group  btn-group-toggle  float-right" data-toggle="buttons">
				<label class="btn bg-olive '.$active[0].'" onclick="f_select(0,2)">
					<input type="radio" name="options" id="option3" autocomplete="off">
					<i class="fas fa-list-ul"></i> Estructura				
				</label>
				<label class="btn bg-olive '.$active[1].'" onclick="f_select(1,2)">
					<input type="radio" name="options" id="option1" autocomplete="off"
						checked="">
					<i class="fas fa-scroll"></i> Consulta
				</label>
			</div> ';
		}
	}
	$abrir = 'display:none;';
	$cerrar = '';
	if ($_SESSION['SENTENCIA-MYSQL'][3] == 1){
		$abrir = '';
		$cerrar = 'display:none;';
	} 
	return '
	<a id="abrir" style="'.$abrir.'" class="abrir-cerrar" href="javascript:void(0)" onclick="mostrar()"><i class="fas fa-long-arrow-alt-right"></i></a>
	<a id="cerrar" style="'.$cerrar.'" class="abrir-cerrar" href="javascript:void(0)" onclick="ocultar()"><i class="fas fa-long-arrow-alt-left"></i></a>
	<div class="row p-1 mb-2 rounded" style="background:#E9ECEF; " >
		<div class="col-6 " >
			<ol class="breadcrumb p-0 mt-3 mb-0" >
				'.$menu_raiz.'
			</ol>
		</div>
		<div class="col-6 p-2 ">
				'.$menu_opcion.'
		</div>
	</div><div id="cont-result">'.f_return_data_opcion().'</div>';
}
function f_return_data_opcion(){
	global $txtConsulta;
	global $btnConsulta;
	global $btnAuxiliar;
	$databases = f_get_database();
	$bases = '';
	foreach ($databases as $valor){
		$bases .= '
		<tr>
			<td>'.$valor.'</td>
			<td class="text-right"><a class="text-success cursor-pointer" onclick="f_select(\''.trim($valor).'\',0)">Examinar</a></td>
		</tr>';
	}
	$data = '
	<div class="row">
		<div class="col-md-12 mb-2">
			<div class="row d-none">
				<div class="col-6 col-md-3">
					<input type="text" class="form-control form-control-sm" placeholder="Nueva Base" >
				</div>
				<div class="col-6">
					<button class="btn btn-sm btn-info"><b>CREAR</b></button>
				</div>
			</div>
			<table class="table table-sm table-hover p-0 mt-2">
				<tbody>
					'.$bases.'
				</tbody>
			</table>
		</div>
	</div>';
	if ($_SESSION['SENTENCIA-MYSQL'][2] == 1) {
		$data = $txtConsulta.$btnConsulta;
	}
	if ($_SESSION['SENTENCIA-MYSQL'][0] != "0") {
		global $dblConsultaSql;
		$pdo = conect_2($dblConsultaSql,$_SESSION['SENTENCIA-MYSQL'][0]);
		$tables =  f_get_consulta($pdo,'SELECT table_name AS "Tables", round(((data_length + index_length) / 1024 / 1024), 2) "Size_in_MB" FROM information_schema.TABLES WHERE table_schema = "'.$_SESSION['SENTENCIA-MYSQL'][0].'" ORDER BY (data_length + index_length) DESC');
		$tablas = '';
		$cont=0;
		$tamanio = 0;
		foreach ($tables as $table){
			$tamanio += $table["Size_in_MB"]; 
			$tablas  .= '<tr>
			<td>'.++$cont.'</td>
			<td>'.$table["Tables"].' </td>
			<td><font class="text-primary ml-5">'.$table["Size_in_MB"].' MB</font></td>
			<td class="text-right"><a class="text-success cursor-pointer" onclick="f_select(\''.trim($table["Tables"]).'\',1)">Examinar</a></td></tr>';
		}
		
		$data = '
		<div class="row d-none">
			<div class="col-6 col-sm-5 col-md-3">
				<input type="text" class="form-control form-control-sm" placeholder="Nombre de la tabla" >
			</div>
			<div class="col-4 col-sm-3 col-md-1">
				<input type="number" class="form-control form-control-sm" placeholder="1" min="1" max="50" >
			</div>
			<div class="col-6">
				<button class="btn btn-sm btn-info"><b>CREAR</b></button>
			</div>
		</div>
		<div class="row">
			<div class="col-md-12 mb-2">
				<p class="lead text-sm">Tamaño de la Base: '.$tamanio.' MB</p>
				<table class="table table-sm table-hover p-0 mt-2">
					<tbody>
						'.$tablas.'
					</tbody>
				</table>
			</div>
		</div>';
		if ($_SESSION['SENTENCIA-MYSQL'][2]==1) {
			$data = $txtConsulta.$btnConsulta;
		}
		if ($_SESSION['SENTENCIA-MYSQL'][1] != "0") {
			global $dblConsultaSql;
			$pdo = conect_2($dblConsultaSql,$_SESSION['SENTENCIA-MYSQL'][0]);
			$items =  f_get_consulta($pdo,'SHOW FULL COLUMNS FROM '.$_SESSION['SENTENCIA-MYSQL'][1]);
			$result = '';
			$cont=0;
			foreach ($items as $colum){
				$primary = '';
				if($colum["Key"] == "PRI")$primary = '<i class="fas fa-key text-warning"></i> ';
				$defautl = $colum["Default"];
				if($colum["Default"] == "")$defautl = '<i>Ninguno</i>';
				$result  .= '<tr>
					<td>'.++$cont.'</td>
					<td> '.$primary.' '.$colum["Field"].'</td>
					<td>'.$colum["Type"].'</td>
					<td>'.$colum["Collation"].'</td>
					<td>'.$colum["Null"].'</td>
					<td>'.$defautl.'</td>
					<td>'.$colum["Extra"].'</td>
					<td>'.$colum["Comment"].'</td>
				</tr>';
			}
			$data = '
			<div class="row ">
				<div class="col-md-12 mb-2">
					<table class="table table-sm table-hover p-0">
						<thead>
							<tr>
								<th>#</th>
								<th>Nombre</th>
								<th>Tipo</th>
								<th>Cotejamiento</th>
								<th>Nulo</th>
								<th>Predeterminado</th>
								<th>Extra</th>
								<th>Comentario</th>
							</tr>
						</thead>
						<tbody>
							'.$result.'
						</tbody>
					</table>
				</div>
			</div>';
			if ($_SESSION['SENTENCIA-MYSQL'][2]==1) {
				$data = $txtConsulta.$btnAuxiliar.$btnConsulta;
			}else if ($_SESSION['SENTENCIA-MYSQL'][2]==2) {
				$data = '
				<h5>BUSCAR</h5>';
			}
		}
	}
	return $data;
}
function f_select_data(){
	// $_SESSION['SENTENCIA-MYSQL'] = [0,0,0,0];//Base,Tabla,Opcion,Menu
	if (trim($_POST["Opcion"]) == 0){
		$_SESSION['SENTENCIA-MYSQL'][0] = trim($_POST["Variable"]);
		$_SESSION['SENTENCIA-MYSQL'][1]= 0;
		$_SESSION['SENTENCIA-MYSQL'][2]= 0;
	}else if (trim($_POST["Opcion"]) == 1)$_SESSION['SENTENCIA-MYSQL'][1] = trim($_POST["Variable"]);
	else if (trim($_POST["Opcion"]) == 2)$_SESSION['SENTENCIA-MYSQL'][2] = trim($_POST["Variable"]);
	else if (trim($_POST["Opcion"]) == 3)$_SESSION['SENTENCIA-MYSQL'][3] = trim($_POST["Variable"]);
}
function f_ejecutar_consulta(){
	global $dblConsultaSql;
	global $btnConsulta;
	global $btnAuxiliar;
	$consulta = trim($_POST["MYSQL"]);
	$consl = '<textarea class="form-control form-control-sm mb-2" id="txtCont" rows="3">'.$consulta.'</textarea>'.$btnAuxiliar.$btnConsulta.'';
	$pdo = conect_1($dblConsultaSql);
	if($_SESSION['SENTENCIA-MYSQL'][0] != "0")$pdo = conect_2($dblConsultaSql,$_SESSION['SENTENCIA-MYSQL'][0]);
	$result = f_get_consulta($pdo,$consulta);
    Insert_Login('0','SQL','Consulta SQL',$consulta,'');
	if (is_array($result)) {
		$thead = '';
		$item = f_get_consulta($pdo,'SHOW COLUMNS FROM '.$_SESSION['SENTENCIA-MYSQL'][1]);
		$tbody = '';
		$cont=0;
		$primary = '';
		foreach ($item as $valor) {
			if ($valor["Key"]=="PRI") {
				$primary  =$valor["Field"];
			}
		}
		foreach($result as $index => $value){
			$cont++;
			$tbody .= '<tr>';
			$bandera  = false; 
			$id = 0;
			foreach ($value as $key =>  $value1) {
				if (!is_int($key)) {
					$dato_i = utf8_encode($value1);
					if ($value1 == NULL) $dato_i="<i>NULL</i>";
					$tbody .= '<td>'.$dato_i.'</td>';
					if ($primary == $key) {
						$bandera = true;
						$id = $value1;
					}
					if ($cont==1) $thead .= '<th>'.$key.'</th>';
				}
			}
			if ($bandera) {
				$tbody .= '<td><a class="text-primary cursor-pointer" onclick="f_update('.$id.')" >Editar</a></td>';
				if ($cont==1) $thead .= '<th>Acciones</th>';
			}
			$tbody .= '</tr>';
		}
		
		return $consl.'<p class="lead">Total de resultado '.$cont.'</p>
				<table class="table  table-sm table-hover p-0 mt-2" id="table-data">
					<thead>'.$thead.'</tr></thead>
					<tbody>'.$tbody.'</tbody>
				</table>';
	}else return  $consl.$result;
}
function f_insert_consulta(){
	global $dblConsultaSql;
	$pdo = conect_2($dblConsultaSql,$_SESSION['SENTENCIA-MYSQL'][0]);
	$item = f_get_consulta($pdo,'SHOW COLUMNS FROM '.$_SESSION['SENTENCIA-MYSQL'][1]);
	$campos = '';
	$valores =  '';
	$primary = '';
	foreach($item as $valor) {
		if ($_POST["Opcion"]==0){
			if ($valor == end($item)) $campos .= $valor["Field"]; 
			else $campos .= $valor["Field"].' , ';
		}else if ($_POST["Opcion"]==1) {
			if ($valor["Key"]!= 'PRI' ) {
				$prede = $valor["Field"];
				if ($valor["Default"] != '') $prede = $valor["Default"];
				if ($valor == end($item)) {
					$campos .= $valor["Field"]; 
					if(preg_match("/varchar/i", $valor["Type"]) || preg_match("/text/i", $valor["Type"]) || preg_match("/date/i", $valor["Type"]) ) $valores .= '\''.$prede.'\'';
					else $valores .= $prede;
				}else {
					$campos .= $valor["Field"].' , ';
					if(preg_match("/varchar/i", $valor["Type"]) || preg_match("/text/i", $valor["Type"]) || preg_match("/date/i", $valor["Type"]) ) $valores .= '\''.$prede.'\' , ';
					else $valores .= $prede.' , ';
				}
			}
		}else if ($_POST["Opcion"]==2){
			if ($valor["Key"]!= 'PRI' ) {
				$prede = $valor["Field"];
				if ($valor["Default"] != '') $prede = $valor["Default"];
				if ($valor == end($item)) {
					if(preg_match("/varchar/i", $valor["Type"]) || preg_match("/text/i", $valor["Type"]) || preg_match("/date/i", $valor["Type"]) ) $valores .= ''.$valor["Field"].' = \''.$prede.'\'';
					else $valores .= $valor["Field"].' = '.$prede;
				}else {
					if(preg_match("/varchar/i", $valor["Type"]) || preg_match("/text/i", $valor["Type"]) || preg_match("/date/i", $valor["Type"]) ) $valores .= ''.$valor["Field"].' = \''.$prede.'\' , ';
					else $valores .= $valor["Field"].' = '.$prede.' , ';
				}
			}else {
				if(preg_match("/varchar/i", $valor["Type"]) || preg_match("/text/i", $valor["Type"]) || preg_match("/date/i", $valor["Type"]) ) $primary = '\''.$valor["Field"].'\'';
				else $primary = $valor["Field"];
			}
		}
	}	  
	if ($_POST["Opcion"]==0) return 'SELECT '.$campos.' FROM '.$_SESSION['SENTENCIA-MYSQL'][1];
	else if ($_POST["Opcion"]==1) return 'INSERT INTO  '.$_SESSION['SENTENCIA-MYSQL'][1].'('.$campos.')'."\n".'VALUES('.$valores.')';
	else if ($_POST["Opcion"]==2) return 'UPDATE '.$_SESSION['SENTENCIA-MYSQL'][1].' SET'."\n".$valores."\n".'WHERE '.$primary.' = ?'  ;
}

function f_update(){
	global $dblConsultaSql;
	$id = trim($_POST["Id"]);
	$pdo = conect_2($dblConsultaSql,$_SESSION['SENTENCIA-MYSQL'][0]);
	$item = f_get_consulta($pdo,'SHOW COLUMNS FROM '.$_SESSION['SENTENCIA-MYSQL'][1]);
	$campos = '';
	$valores =  '';
	$primary = '';
	$valor_primary = '';
	foreach($item as $valor) {
		if ($valor["Key"] == 'PRI') {
			$primary = $valor["Field"];
			if(preg_match("/varchar/i", $valor["Type"]) || preg_match("/text/i", $valor["Type"]) || preg_match("/date/i", $valor["Type"]) ) $valor_primary = '\''.$id.'\'';
			else $valor_primary = $id;
		}
	}
	$select = f_get_consulta($pdo,'SELECT * FROM '.$_SESSION['SENTENCIA-MYSQL'][1].' WHERE '.$primary.' = '.$valor_primary);
	foreach ($select as $value) {
		foreach($item as $valor){
			if ($valor["Key"] != 'PRI') {
				if ($valor == end($item)) {
					if(preg_match("/varchar/i", $valor["Type"]) || preg_match("/text/i", $valor["Type"]) || preg_match("/date/i", $valor["Type"]) ) $valores .= ''.$valor["Field"].' = \''.utf8_encode($value[$valor["Field"]]).'\'';
					else $valores .= $valor["Field"].' = '.utf8_encode($value[$valor["Field"]]);
				}else {
					if(preg_match("/varchar/i", $valor["Type"]) || preg_match("/text/i", $valor["Type"]) || preg_match("/date/i", $valor["Type"]) ) $valores .= ''.$valor["Field"].' = \''.utf8_encode($value[$valor["Field"]]).'\' , ';
					else $valores .= $valor["Field"].' = '.utf8_encode($value[$valor["Field"]]).' , ';
				}
			}
		}
	}
	return 'UPDATE '.$_SESSION['SENTENCIA-MYSQL'][1].' SET'."\n".$valores."\n".'WHERE '.$primary.' = '.$valor_primary;	
}
function f_get_consulta($pdo,$consulta){
	try {
		$stmt = $pdo->query(utf8_decode($consulta));
		if(preg_match("/SELECT/i", $consulta) || preg_match("/SHOW/i", $consulta)){
			return $stmt->fetchAll();
		} else {
			return '<div class="card bg-success mt-2"><div class="card-body"><b>COLUMNAS AFECTADAS '.$stmt->rowCount().'</b> </div></div>';
		}
	} catch (Exception $e) {
		return '<div class="card bg-danger mt-2"><div class="card-body"><b>ERROR MYSQL</b> <br>'.$e->getMessage().'</div></div>';
		// exit('<div class="card bg-danger mt-2"><div class="card-body"><b>ERROR MYSQL</b> <br>'.$e->getMessage().'</div></div>');
	}
}


?>