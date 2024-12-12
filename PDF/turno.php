<?php
$dbClientes=[
    'host'=>'172.20.134.104',//Direccion de la base de datos
    'user'=>'user_clientes',//Usuario de la base de datos 
    'password'=>'Cl1n3nt3s2021*',//Contraseña del usuario 
    'db'=>'sisprocesos'//Nombre de la base de datos
    //'port'=>'3306'//puerto
];

$dbGuias=[
    'host'=>'localhost',//Direccion de la base de datos
    'user'=>'user_sisprocesos',//Usuario de la base de datos 
    'password'=>'user_sisprocesos',//Contraseña del usuario 
    'db'=>'prueba_sisprocesos'//Nombre de la base de datos .... sisprocesos
    //'port'=>'3306'//puerto
];

$dbTurnero=[
    'host'=>'172.20.134.9',//Direccion de la base de datos
    'user'=>'user_turnos',//Usuario de la base de datos 
    'password'=>'Turn3r0.2021*',//Contraseña del usuario 
    'db'=>'dbTurnos'//Nombre de la base de datos
    //'port'=>'3306'//puerto
];
$Document = "ERROR";
$html="<h1>ERROR 1234</h1>";
if (isset($_GET["Fecha"])) {
    include '../FilePHP/utils.php';
    $tipo = $_GET["Tipo"];
    $fecha = $_GET["Fecha"];
    if ($tipo==1) $Ganado = "G. Bovino";
    elseif ($tipo==2) $Ganado = "G. Porcino";
    elseif ($tipo==3) $Ganado = "G. Ovino";
    $html = lista_turnos($Ganado,$fecha);
    $Document = 'Reporte';
}
function lista_turnos($Ganado,$fecha_POST){
    global $dbTurnero;
    $array= explode("/",$fecha_POST);
    $fecha = $array[2]."-".$array[1]."-".$array[0];
    $Fecha = Transformar_Fecha($fecha);
    $dbConn = conectar($dbTurnero);
    $resultado ='<table class="tbl_detalle" border="1"  cellpadding="5">
    <thead>
        <tr>
            <th>TURNO</th>
            <th>IDENTIFICACIÓN</th>
            <th>NOMBRES</th>
            <th>CANTIDAD</th>
        </tr>
    </thead>
    <tbody>';
    $consulta ="SELECT * FROM tbl_turnos WHERE turFecha_Turno BETWEEN :fecha1 AND :fecha2 AND turGanado = :ganado  ORDER BY turFecha_Turno ASC";//
    $sql= $dbConn->prepare($consulta);
    $sql->bindValue(':fecha1',$fecha." 00:00:00");
    $sql->bindValue(':fecha2',$fecha." 23:59:59");
    $sql->bindValue(':ganado',$Ganado);
    $sql->execute();
    $total = 0;
    $cont = 0;
    $ArrayPositivos = [];
    $ArrayNegativos = [];
    $ArrayNeutro = [];
    while ($row = $sql->fetch()) {
        $cont++;
        $total +=  $row["turCantidad"];
        $estado = get_estado_guia($row["turNumGuia"],$row["turCantidad"],$row["turGanado"]);
        if ($estado==0 || $estado==2) {
            $ArrayNegativos[] = $row["turId"];
        }elseif ($estado==1) {
            $ArrayPositivos[] = $row["turId"];
        }
        // elseif ($estado==2) {
        //     $ArrayNeutro[] = $row["turId"];
        // }
    }
    $num = 0;
    for ($i=0; $i < count($ArrayPositivos) ; $i++) { 
        $num++;
        $resultado .= get_data_tr($dbConn,$ArrayPositivos[$i],"bg-success",$num,"");
    }
    // for ($i=0; $i < count($ArrayNeutro) ; $i++) { 
    //     $num++;
    //     $resultado .= get_data_tr($dbConn,$ArrayNeutro[$i],"bg-warning",$num,"E-");
    // }
    for ($i=0; $i < count($ArrayNegativos) ; $i++) { 
        $num++;
        $resultado .= get_data_tr($dbConn,$ArrayNegativos[$i],"bg-danger",$num,"E-");
    }
    
    $table= $resultado."</tbody></table>";
    $cabecera='<img src="../Recursos/Logo-rastro.png" alt="Rastro" height="70px" >
    <p style="font-size:22px "><b>EMPRESA PÚBLICA METROPOLITANA DE RASTRO QUITO</b></p>
    <p style="font-size:17px "><b>DIRECCIÓN DE PRODUCCIÓN Y COMERCIALIZACIÓN</b></p>
    <p style="font-size:15px;text-align: center;"><b>AGENDAMIENTO DE TURNOS DE SERVICIO </b></p>
    <p style="font-size:12px;"><b>FECHA</b> '.$Fecha.'</p>
    <p style="font-size:12px "><b>TIPO DE GANADO </b> '.strtoupper($Ganado).'</p>';
    $estilos ='
        p{
            font-family:Helvetica;
            margin-bottom:0px;
            margin-top:0px;
        }
        a{
            color:black;
            text-align: center;
        }
        .tbl_detalle{
            width: 100%;
            border-collapse: collapse;
            font-family:Arial;
        }
        .tbl_detalle tr:nth-child(even) {
                background-color: #dddddd;
        }
        .center{
            text-align: center;
        }
        .tbl_detalle thead tr{
            background:#b3b6b7;
        }
        .tbl_total{
            margin-left:42%;
            margin-top:20px;
            border-collapse: collapse;
        }

        .tbl_total td,.tbl_total th{
            font-family:Arial;
            font-size: 15px;
            text-align: center;
        }
        .tbl_total th{
            background: #b3b6b7;
        }';
    $footer = '<table class="tbl_total" border="1" cellpadding="7">
        <tr><th >TOTAL</th></tr>
        <tr><td>'.$total.'</td></tr>
        </table>
        <p style="font-size:12px"><b>Reporte Emitido:</b> '.date("Y-m-d H:i:s").'</p>';
    $Res= FormatoOrden($table,$cabecera,$estilos,$footer);
    return $Res;
}

function FormatoOrden($table,$cabecera,$estilos,$footer){
    $resultado= '<!DOCTYPE html>
    <html lang="en" dir="ltr">
        <head>
        <meta charset="utf-8">
        <title>LISTA DE TURNOS</title>
        </head>
        <style> '.$estilos.'</style>
        <body>'.$cabecera.$table.$footer.'</body>
    </html>';
    return $resultado;
}
function get_data_cliente($cedula){
    global $dbClientes;
    $dbConn = conectar($dbClientes);
    $consulta="SELECT apellidos,nombres FROM tbl_clientes WHERE ruc = :cedula";
	$sql= $dbConn->prepare($consulta);
    $sql->bindValue(':cedula',$cedula);
	$sql->execute();
	if($row = $sql->fetch()) {
        $resultado = '<td> '.utf8_encode($row["apellidos"]).' '.utf8_encode($row["nombres"]).'</td>';
	}
	return $resultado;
}
function Transformar_Fecha($fecha){


    $arrayDia = array('1' => 'Lunes',
                     '2' => 'Martes',
                     '3' => 'Miércoles',
                     '4' => 'Jueves',
                     '5' => 'Viernes',
                     '6' => 'Sábado',
                     '7' => 'Domingo');
    $numDia=date("N",strtotime($fecha));
    $arrayMes = array('1' => 'Enero',
                     '2' => 'Febrero',
                     '3' => 'Marzo',
                     '4' => 'Abril',
                     '5' => 'Mayo',
                     '6' => 'Junio',
                     '7' => 'Julio',
                     '8' => 'Agosto',
                     '9' => 'Septiembre',
                     '10' => 'Octubre',
                     '11' => 'Noviembre',
                     '12' => 'Diciembre',);
    $numMes=date("n",strtotime($fecha));
    return $arrayDia[$numDia].", ".date("d",strtotime($fecha))." de ".$arrayMes[$numMes]." de ".date("Y",strtotime($fecha));
}
function get_data_tr($dbConn,$id,$color,$mun,$letra){
    $resultado = '';
    $consulta="SELECT * FROM tbl_turnos WHERE turId = :id";
	$sql= $dbConn->prepare($consulta);
    $sql->bindValue(':id',$id);
	$sql->execute();
	if($row = $sql->fetch()) {
        $tam = strlen($mun);
        $turno ='';
        for ($i=$tam; $i < 3 ; $i++) { 
            $turno .= '0';
        }
        $resultado .='
        <tr>
            <td>'.$letra.$turno.$row["turOrdinal"].'</td>
            <td>'.$row["ruc"].'</td>
            '.get_data_cliente($row["ruc"]).'
            <td class="'.$color.'">'.$row["turCantidad"].'</td>
        </tr>';
    }
    return $resultado;
}
function get_estado_guia($numero,$cantidad,$tipo){
    global $dbGuias;
    $dbConn = conectar($dbGuias);
    $consulta="SELECT p.ruc,g.cantidad FROM tbl_guiamovilizacion g, tbl_guia_proceso p, tbl_especieanimales e 
    WHERE g.guia_numero = p.guia_numero AND g.codigo_especieanimales = e.codigo_especieanimales AND g.guia_mov_numero = :num AND e.tipo = :code";
	$sql= $dbConn->prepare($consulta);
    $sql->bindValue(':num',$numero);
    $sql->bindValue(':code',$tipo);
	$sql->execute();
	if($row = $sql->fetch()) {
        if ($row["cantidad"]==$cantidad) return 1;
        else return 2;
        
	}else return 0;//No existe el numero de guia para esta especie animal
}

include("MPDF57/mpdf.php");
$mpdf=new mPDF();
$mpdf->WriteHTML($html);
$mpdf->Output($Document.'.pdf', 'D');
// $mpdf->Output();
exit;

//==============================================================
//==============================================================
//==============================================================
