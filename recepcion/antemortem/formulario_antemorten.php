<?php
require '../../fpdf/fpdf.php';
require '../../FilePHP/utils.php';

// phpinfo();
// exit;
$dbConn = conectar($db);
// $dir_img = "/var/www/html/SIF/recepcion/antemortem/img";
$dir_img = "./img";

// var_dump($_GET);
// $table = Lista_de_Guias_movilizacion($dbConn, $_SESSION['ANTEMORTEM'][0]);
// var_dump($_GET["slcEspecie"]);
$consulta = "SELECT v.*
FROM vst_rep_antemorten_fin v
WHERE  v.fecha = :fecha AND v.espId = :especie";
$sql = $dbConn->prepare($consulta);
$sql->bindValue(':fecha', $_GET['fechaFormulario']);
$sql->bindValue(':especie', $_GET['slcEspecie']);
$sql->execute();
$cont = 0;
$sql->setFetchMode(PDO::FETCH_NUM);
while ($row = $sql->fetch()) {
    $data[$cont] = $row;
    $cont ++;
}
// var_dump($data);


$pdf = new FPDF('L');
$pdf->SetTopMargin(20);
$pdf->SetLeftMargin(5);
$pdf->SetRightMargin(5);
$pdf->AddPage();
$pdf->Image($dir_img.'/Republica_del_ecuador_logo.png',5,5,27,0,'PNG');
$pdf->Image($dir_img.'/formulario_antemorten_logo.png',45,11,0,9,'PNG');
$pdf->Image($dir_img.'/Agencia_regulacion_y_control_logo.png',234,5,0,6,'PNG');
$pdf->Cell(0,4,'',0,1,'',false,'');
$marco = true;
$tam_font = 7;
// $font = 'Courier';
$font = 'Arial';
// $font = $pdf->GetFontFamily();
$ancho_1 = 10;
$ancho_2 = 12;
$align = 'C';
$alto = 4;

$pdf->SetFont($font,'',$tam_font);

$x = $pdf->GetX();
$y = $pdf->GetY();

$pdf->Cell(0,$alto * 9,'',1,1,'',false,'');

// echo count($data);

$pdf->SetXY($x, $y);
$pdf->Cell(0,$alto ,'',0,1,'',false,'');
$pdf->Cell(287,$alto,mb_convert_encoding('A. IDENTIFICACIÓN DEL MATADERO',"ISO-8859-1", "UTF-8"),0,1,'',false,'');
$pdf->Cell(95,$alto,'1. PROVINCIA',0,0,$align,false,'');
$pdf->Cell(95,$alto,'2. CANTON',0,0,$align,false,'');
$pdf->Cell(0,$alto,'3. PARROQUIA',0,1,$align,false,'');
$pdf->Cell(95,$alto,'PICHINCHA',1,0,$align,false,'');
$pdf->Cell(95,$alto,'QUITO',1,0,$align,false,'');
$pdf->Cell(0,$alto,'LA ECUATORIANA',1,1,$align,false,'');
$pdf->Cell(143,$alto,'4. NOMBRE DEL MATADERO',0,0,$align,false,'');
$pdf->Cell(0,$alto,'5. MEDICO VETERINARIO OFICIAL O AUTORIZADO',0,1,$align,false,'');
$pdf->Cell(143,$alto,'EMPRESA PUBLICA METROPOLITANA DE RASTRO QUITO',1,0,$align,false,'');
if(isset($data)){
    $pdf->Cell(0,$alto,$data[count($data)-1][26],1,1,$align,false,'');
}
$pdf->Cell(0,$alto,"",1,1,$align,false,'');

$pdf->Cell(0,$alto ,'',0,1,'',false,'');
$pdf->Cell(287,$alto,mb_convert_encoding('B. IDENTIFICACIÓN DEL MATADERO',"ISO-8859-1", "UTF-8"),0,1,'',false,'');
$pdf->Cell(0,$alto ,'',0,1,'',false,'');

$tam_font = 4;

$pdf->SetFont($font,'',$tam_font);
$pdf->SetWidths(array(10,8,14,17,16,8,$ancho_2,9,9,$ancho_1,$ancho_1,$ancho_1, $ancho_1,$ancho_1,
$ancho_2,$ancho_2,13,$ancho_2,$ancho_1,$ancho_1,$ancho_1,$ancho_1,13,$ancho_2,$ancho_1,$ancho_1));
$pdf->SetAligns(array('C','C','C','C','C','C','C','C','C','C','C','C','C','C','C','C','C','C','C','C','C','C','C','C','C','C'));
$titulos = array('FECHA','HORA','ESPECIE','LUGAR DE PROCEDENCIA','Nro. DE CSMI','Nro. DE LOTE','ETAPA PRODUCTIVA (CATEGORIA ETAPA)','Nro. MACHOS','Nro. HEMBRAS','Nro. TOTAL DE ANIMALES', 'Nro. ANIMALES MUERTOS', 'CAUSA PROBABLE', 'DECOMISO', 'APROVECHAMIENTO','Nro. ANIMALES CON SINDROME NERVIOSO','Nro. ANIMALES CON SINDROME DIGESTIVO','Nro. ANIMALES CON SINDROME RESPIRATORIO', 'Nro. ANIMALES CON SINDROME VESICULAR', 'TIPO DE SECRECION', 'ANIMALES CON COJERA', 'ANIMALES NO AMBULATORIOS', 'MATANZA NORMAL', 'MATANZA BAJO PRECAUCIONES ESPECIALES', 'MATANZA DE EMERGENCIA', 'APLAZAMIENTO DE MATANZA', 'OBSERVACIONES');
$pdf->Row($titulos, 2);
$ttl_machos = 0;
$ttl_hembras = 0;
$ttl_animales = 0;
$ttl_muertos = 0;
$ttl_decomisos = 0;
$ttl_aprovechamiento = 0;
$ttl_s_nervioso = 0;
$ttl_s_digestivo = 0;
$ttl_s_respiratorio = 0;
$ttl_s_vesicular = 0;
$ttl_t_secrecion = 0;
$ttl_cojera = 0;
$ttl_no_ambulatorio = 0;
$ttl_normal = 0;
$ttl_p_especiales = 0;
$ttl_emergente = 0;
$ttl_aplazamiento = 0;
$contador = 0;
if(isset($data)){

    foreach($data as $fila){
        $fila_datos = array_slice($fila, 0, 26);
        $contador++;
        $fila_datos[5] = $contador;
        $ttl_machos += $fila[7];
        $ttl_hembras += $fila[8];
        $ttl_animales += $fila[9];
        $ttl_muertos += $fila[10];
        $ttl_decomisos += $fila[12];
        $ttl_aprovechamiento += $fila[13];
        $ttl_s_nervioso += $fila[14];
        $ttl_s_digestivo += $fila[15];
        $ttl_s_respiratorio += $fila[16];
        $ttl_s_vesicular += $fila[17];
        $ttl_t_secrecion += $fila[18];
        $ttl_cojera += $fila[19];
        $ttl_no_ambulatorio += $fila[20];
        $ttl_normal += $fila[21];
        $ttl_p_especiales += $fila[22];
        $ttl_emergente += $fila[23];
        $ttl_aplazamiento += $fila[24];
        $pdf->Row($fila_datos, 2);
    }
}
$pdf->SetFont($font,'B',$tam_font);
$pdf->SetWidths(array(85,9,9,$ancho_1,$ancho_1,$ancho_1, $ancho_1,$ancho_1,
$ancho_2,$ancho_2,13,$ancho_2,$ancho_1,$ancho_1,$ancho_1,$ancho_1,13,$ancho_2,$ancho_1,$ancho_1));
$titulos = array('TOTAL', $ttl_machos, $ttl_hembras, $ttl_animales, $ttl_muertos,'', $ttl_decomisos, $ttl_aprovechamiento, $ttl_s_nervioso, $ttl_s_digestivo, $ttl_s_respiratorio, $ttl_s_vesicular, $ttl_t_secrecion, $ttl_cojera, $ttl_no_ambulatorio, $ttl_normal, $ttl_p_especiales, $ttl_emergente, $ttl_aplazamiento,'');
$pdf->Row($titulos, 2);
$tam_font = 7;
$pdf->SetFont($font,'',$tam_font);
$x = $pdf->GetX();
$y = $pdf->GetY();
$pdf->Image($dir_img.'/nuevo_ecuador_logo.png',235,$y + 8,0,15,'PNG');
$pdf->Cell(0,$alto * 7,'',1,1,'',false,'');
$pdf->SetXY($x, $y);
$pdf->Cell(0,$alto ,'',0,1,'',false,'');
$pdf->Cell(287,$alto,mb_convert_encoding('C. OBSERVACIONES',"ISO-8859-1", "UTF-8"),0,1,'',false,'');

// $pdf->Contenido();
$pdf->Output();


?>