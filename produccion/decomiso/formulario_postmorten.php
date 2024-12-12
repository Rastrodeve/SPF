<?php
require '../../fpdf/fpdf.php';
require '../../FilePHP/utils.php';
$dbConn = conectar($db);
$dir_img = "/var/www/html/SIF/recepcion/antemortem/img";

// var_dump($_GET);
// $table = Lista_de_Guias_movilizacion($dbConn, $_SESSION['ANTEMORTEM'][0]);

$consulta = "SELECT v.*
FROM vst_rep_antemorten_fin v
WHERE  v.fecha = :fecha;";
$sql = $dbConn->prepare($consulta);
$sql->bindValue(':fecha', $_GET['fechaFormulario']);
$sql->execute();
$cont = 0;
$sql->setFetchMode(PDO::FETCH_NUM);
while ($row = $sql->fetch()) {
    $data[$cont] = $row;
    $cont ++;
}
// var_dump($data);


$pdf = new FPDF('L','mm','A4');
$pdf->SetTopMargin(20);
$pdf->SetLeftMargin(5);
$pdf->SetRightMargin(5);
$pdf->SetAutoPageBreak(true, 5);
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
$pdf->SetFillColor(255, 255,0);

$pdf->SetFont($font,'',$tam_font);

$pdf->Cell(287,$alto,mb_convert_encoding('A. IDENTIFICACIÓN DEL MATADERO',"ISO-8859-1", "UTF-8"),0,1,'C',true,'');
$pdf->Cell(0,$alto ,'',0,1,'',false,'');

$pdf->Cell(30,$alto,'1. PROVINCIA:',0,0,'R',false,'');
$pdf->Cell(20,$alto,'PICHINCHA',1,0,$align,false,'');
$pdf->Cell(50,$alto,'2. NOMBRE DEL MATADERO:',0,0,'R',false,'');
$pdf->Cell(25,$alto,'METROPOLITANO',1,0,$align,false,'');
$pdf->Cell(60,$alto,mb_convert_encoding('3. Médicos Veterinario Oficial o autorizado:',"ISO-8859-1", "UTF-8"),0,0,'R',false,'');
$pdf->Cell(50,$alto,$data[0][26],1,0,$align,false,'');
$pdf->Cell(25,$alto,'4. Fecha: ',0,0,'R',false,'');
$pdf->Cell(0,$alto,$_GET['fechaFormulario'],1,1,$align,false,'');

$pdf->Cell(0,$alto ,'',0,1,'',false,'');
$pdf->SetFillColor(0, 0,255);

$pdf->Cell(287,$alto,mb_convert_encoding('B. IDENTIFICACIÓN DEL MATADERO',"ISO-8859-1", "UTF-8"),0,1,$align,true,'');
$pdf->Cell(0,$alto ,'',0,1,'',false,'');

$alto = 3;
$ancho_cuadro_1 = 15;
$tam_font = 5.5;
$ancho_col_1 = 17;
$ancho_col_2 = 15;
$align = 'C';
$color_cuadro_verde = array(13, 205,41);
$color_cuadro_rojo = array(255, 99,99);

// Cuadro 1
$x = $pdf->GetX();
$y = $pdf->GetY();
$pdf->SetFont($font,'',$tam_font);
$pdf->Cell($ancho_cuadro_1,$alto,'',0,0,'',false,'');
$pdf->Cell($ancho_col_1,$alto,'Nro. de CSMI:',0,0,'R',false,'');
$pdf->Cell($ancho_col_2*2,$alto,'93',1,0,$align,false,'');
$pdf->Cell($ancho_col_2*3,$alto,'',0,0,'',false,'');
$pdf->Cell($ancho_col_2,$alto,'No. Hembras:',0,0,'R',false,'');
$pdf->Cell($ancho_col_2*2,$alto,'265',1,0,$align,false,'');
$pdf->Cell($ancho_col_2*2,$alto,'Etapa Productiva:',0,0,'R',false,'');
$pdf->Cell($ancho_col_2*7,$alto,'VACAS-vaconas','B',1,$align,false,'');

$pdf->Cell($ancho_cuadro_1+$ancho_col_1+$ancho_col_2*2,$alto,'',0,0,'',false,'');
$pdf->Cell($ancho_col_2*2,$alto,'No. total de animales:',0,0,'R',false,'');
$pdf->Cell($ancho_col_2,$alto,'423',1,1,$align,false,'');

$pdf->Cell($ancho_cuadro_1,$alto,'',0,0,'',false,'');
$pdf->Cell($ancho_col_1,$alto,'Hora',0,0,'R',false,'');
$pdf->Cell($ancho_col_2*2,$alto,'00:00-08:00',1,0,$align,false,'');
$pdf->Cell($ancho_col_2*3,$alto,'',0,0,'',false,'');
$pdf->Cell($ancho_col_2,$alto,'No. Machos:',0,0,'R',false,'');
$pdf->Cell($ancho_col_2*2,$alto,'158',1,0,$align,false,'');
$pdf->Cell($ancho_col_2*2,$alto,'Etapa Productiva:',0,0,'R',false,'');
$pdf->Cell($ancho_col_2*7,$alto,'TOROS-toretes','B',1,$align,false,'');
$pdf->Cell(0,$alto,'',0,1,'',false,'');

$pdf->Cell($ancho_cuadro_1,$alto,'',0,0,'',false,'');
$pdf->SetFillColor($color_cuadro_rojo[0], $color_cuadro_rojo[1],$color_cuadro_rojo[2]);
$pdf->Cell(0,$alto,mb_convert_encoding('B 1. HALLAZGOS DIAGNOSTICADOS AL EXAMEN POST MORTEM BOVINOS',"ISO-8859-1", "UTF-8"),0,1,$align,true,'');

$pdf->SetAligns(array($align,$align,$align,$align,$align,$align,$align,$align,$align,$align,$align,$align,$align,$align,$align,$align,$align,$align));
$pdf->SetWidths(array($ancho_col_1,$ancho_col_2*3,$ancho_col_2*3,$ancho_col_2*3,$ancho_col_2*3,$ancho_col_2*4,$ancho_col_2));
$titulos = array('ENFERMEDAD','ENF. VESICULAR','TUBERCULOSIS','PARATUBERCULOSIS',mb_convert_encoding('LEUCOSIS',"ISO-8859-1", "UTF-8"),mb_convert_encoding('BRUCELOSIS',"ISO-8859-1", "UTF-8"),'METRITIS');
$pdf->Cell($ancho_cuadro_1,$alto,'',0,0,'',false,'');
$pdf->Row($titulos, $alto);

$pdf->SetWidths(array($ancho_col_1,$ancho_col_2,$ancho_col_2,$ancho_col_2,$ancho_col_2,$ancho_col_2,$ancho_col_2,$ancho_col_2,$ancho_col_2,$ancho_col_2,$ancho_col_2,$ancho_col_2,$ancho_col_2,$ancho_col_2*2,$ancho_col_2*2,$ancho_col_2));
$titulos = array('LOCALIZACION','Lengua','Patas','Ubre', mb_convert_encoding('Pulmón',"ISO-8859-1", "UTF-8"),
mb_convert_encoding('Hígado',"ISO-8859-1", "UTF-8"),'General','Intestino','Ganglios','Otros','Ganglios',
'S. Reproductivo','Otros','S.Reproductivo','Articulaciones','Utero');
$pdf->Cell($ancho_cuadro_1,$alto,'',0,0,'',false,'');
$pdf->Row($titulos, $alto);


$pdf->SetWidths(array($ancho_col_1,$ancho_col_2,$ancho_col_2,$ancho_col_2,$ancho_col_2,$ancho_col_2,$ancho_col_2,$ancho_col_2,$ancho_col_2,$ancho_col_2,$ancho_col_2,$ancho_col_2,$ancho_col_2,$ancho_col_2,$ancho_col_2,$ancho_col_2,$ancho_col_2,$ancho_col_2));
$titulos = array('PRESENCIA','-','-','-','-','-','-','-','-','-','-','-','-','-','-','-','-','05');
$pdf->Cell($ancho_cuadro_1,$alto,'',0,0,'',false,'');
$pdf->Row($titulos, $alto);

$pdf->SetWidths(array($ancho_col_1,$ancho_col_2,$ancho_col_2,$ancho_col_2,$ancho_col_2,$ancho_col_2,$ancho_col_2,$ancho_col_2,$ancho_col_2,$ancho_col_2,$ancho_col_2,$ancho_col_2,$ancho_col_2,$ancho_col_2,$ancho_col_2,$ancho_col_2*2,$ancho_col_2));
$titulos = array(mb_convert_encoding('% DE AFECTACIÓN',"ISO-8859-1", "UTF-8"),'-','-','-','-','-','-','-','-','-','-','-','-','-','-','-','-');
$pdf->Cell($ancho_cuadro_1,$alto,'',0,0,'',false,'');
$pdf->Row($titulos, $alto);

$pdf->SetWidths(array($ancho_col_1,$ancho_col_2*3,$ancho_col_2*3,$ancho_col_2*2,$ancho_col_2*4,$ancho_col_2*5));
$titulos = array('ENFERMEDAD','DISTOMATOSIS','HIDATIDOSIS','LEPTOSPIROSIS',mb_convert_encoding('ESTADO DE LOS NÓDULOS LINFÁTICOS',"ISO-8859-1", "UTF-8"),'OTROS');
$pdf->Cell($ancho_cuadro_1,$alto,'',0,0,'',false,'');
$pdf->Row($titulos, $alto);

$pdf->SetWidths(array($ancho_col_1,$ancho_col_2,$ancho_col_2,$ancho_col_2,$ancho_col_2,$ancho_col_2,$ancho_col_2,$ancho_col_2,$ancho_col_2,$ancho_col_2*4,$ancho_col_2*5));
$titulos = array('LOCALIZACION',
    mb_convert_encoding('Hígado',"ISO-8859-1", "UTF-8"),
    mb_convert_encoding('Pulmón',"ISO-8859-1", "UTF-8"),'Otros',
    mb_convert_encoding('Hígado',"ISO-8859-1", "UTF-8"),
    mb_convert_encoding('Pulmón',"ISO-8859-1", "UTF-8"),'Otros','Mucosas',
    mb_convert_encoding('Riñón',"ISO-8859-1", "UTF-8"),'',
    mb_convert_encoding('CIRROSIS HEP. (13) ABSCESO HEPTICO(08)',"ISO-8859-1", "UTF-8"));
$pdf->Cell($ancho_cuadro_1,$alto,'',0,0,'',false,'');
$pdf->Row($titulos, $alto);

$titulos = array('PRESENCIA ','28','-','-','-','-','-','-','-','',mb_convert_encoding('TELEANGIECTASIAS (08)',"ISO-8859-1", "UTF-8"));
$pdf->Cell($ancho_cuadro_1,$alto,'',0,0,'',false,'');
$pdf->Row($titulos, $alto);

$titulos = array(mb_convert_encoding('% DE AFECTACIÓN',"ISO-8859-1", "UTF-8"),'-','-','-','-','-','-','-','-','',
    mb_convert_encoding('MASTITIS (12) ENTERITIS(01) NEUMONIA (02)',"ISO-8859-1", "UTF-8"));
$pdf->Cell($ancho_cuadro_1,$alto,'',0,0,'',false,'');
$pdf->Row($titulos, $alto);

$pdf->SetXY($x, $y);
$pdf->SetFillColor($color_cuadro_verde[0], $color_cuadro_verde[1],$color_cuadro_verde[2]);
$pdf->Cell($ancho_cuadro_1,$alto * 16,'BOVINOS',1,1,'',true,'');
$pdf->Cell(0,$alto,'',0,1,'',false,'');

// Cuadro 2
$x = $pdf->GetX();
$y = $pdf->GetY();
$pdf->SetFont($font,'',$tam_font);
$pdf->Cell($ancho_cuadro_1,$alto,'',0,0,'',false,'');
$pdf->Cell($ancho_col_1,$alto,'Nro. de CSMI:',0,0,'R',false,'');
$pdf->Cell($ancho_col_2*2,$alto,'13',1,0,$align,false,'');
$pdf->Cell($ancho_col_2*3,$alto,'',0,0,'',false,'');
$pdf->Cell($ancho_col_2,$alto,'No. Hembras:',0,0,'R',false,'');
$pdf->Cell($ancho_col_2*2,$alto,'145',1,0,$align,false,'');
$pdf->Cell($ancho_col_2*2,$alto,'Etapa Productiva:',0,0,'R',false,'');
$pdf->Cell($ancho_col_2*7,$alto,'MADRES-LEVANTE-ENGORDE','B',1,$align,false,'');

$pdf->Cell($ancho_cuadro_1+$ancho_col_1+$ancho_col_2*2,$alto,'',0,0,'',false,'');
$pdf->Cell($ancho_col_2*2,$alto,'No. total de animales:',0,0,'R',false,'');
$pdf->Cell($ancho_col_2,$alto,'291',1,1,$align,false,'');

$pdf->Cell($ancho_cuadro_1,$alto,'',0,0,'',false,'');
$pdf->Cell($ancho_col_1,$alto,'Hora',0,0,'R',false,'');
$pdf->Cell($ancho_col_2*2,$alto,'00:00-08:00',1,0,$align,false,'');
$pdf->Cell($ancho_col_2*3,$alto,'',0,0,'',false,'');
$pdf->Cell($ancho_col_2,$alto,'No. Machos:',0,0,'R',false,'');
$pdf->Cell($ancho_col_2*2,$alto,'146',1,0,$align,false,'');
$pdf->Cell($ancho_col_2*2,$alto,'Etapa Productiva:',0,0,'R',false,'');
$pdf->Cell($ancho_col_2*7,$alto,'VERRACOS-LEVANTE-ENGORDE-LECHONES','B',1,$align,false,'');
$pdf->Cell(0,$alto,'',0,1,'',false,'');

$pdf->Cell($ancho_cuadro_1,$alto,'',0,0,'',false,'');
$pdf->SetFillColor($color_cuadro_rojo[0], $color_cuadro_rojo[1],$color_cuadro_rojo[2]);
$pdf->Cell(0,$alto,mb_convert_encoding('B 2. HALLAZGOS DIAGNOSTICADOS AL EXAMEN POST MORTEM PORCINOS',"ISO-8859-1", "UTF-8"),0,1,$align,true,'');

$pdf->SetAligns(array($align,$align,$align,$align,$align,$align,$align,$align,$align,$align,$align,$align,$align,$align,$align,$align,$align,$align));
$pdf->SetWidths(array($ancho_col_1,$ancho_col_2*3,$ancho_col_2*3,$ancho_col_2*3,$ancho_col_2*3,$ancho_col_2*5));
$titulos = array('ENFERMEDAD','DISTOMATOSIS','TUBERCULOSIS','HIDATIDOSIS','E VESICULARES','CISTICERCOSIS');
$pdf->Cell($ancho_cuadro_1,$alto,'',0,0,'',false,'');
$pdf->Row($titulos, $alto);

$pdf->SetWidths(array($ancho_col_1,$ancho_col_2,$ancho_col_2,$ancho_col_2,$ancho_col_2,$ancho_col_2,$ancho_col_2,$ancho_col_2,$ancho_col_2,$ancho_col_2,$ancho_col_2,$ancho_col_2,$ancho_col_2,$ancho_col_2*2,$ancho_col_2*3));
$titulos = array('LOCALIZACION',
    mb_convert_encoding('Hígado',"ISO-8859-1", "UTF-8"),
    mb_convert_encoding('Pulmón',"ISO-8859-1", "UTF-8"),'Otros',
    mb_convert_encoding('Hígado',"ISO-8859-1", "UTF-8"),
    mb_convert_encoding('Pulmón',"ISO-8859-1", "UTF-8"),'General',
    mb_convert_encoding('Hígado',"ISO-8859-1", "UTF-8"),
    mb_convert_encoding('Pulmón',"ISO-8859-1", "UTF-8"),'Otros','Lengua','Patas','Ubre','Cerebro',
    mb_convert_encoding('Músculo',"ISO-8859-1", "UTF-8"));
$pdf->Cell($ancho_cuadro_1,$alto,'',0,0,'',false,'');
$pdf->Row($titulos, $alto);

$pdf->SetWidths(array($ancho_col_1,$ancho_col_2,$ancho_col_2,$ancho_col_2,$ancho_col_2,$ancho_col_2,$ancho_col_2,$ancho_col_2,$ancho_col_2,$ancho_col_2,$ancho_col_2,$ancho_col_2,$ancho_col_2,$ancho_col_2*2,$ancho_col_2*3));
$titulos = array('PRESENCIA','-','-','-','-','-','-','-','-','-','-','-','-','-','-');
$pdf->Cell($ancho_cuadro_1,$alto,'',0,0,'',false,'');
$pdf->Row($titulos, $alto);

$pdf->SetWidths(array($ancho_col_1,$ancho_col_2,$ancho_col_2,$ancho_col_2,$ancho_col_2,$ancho_col_2,$ancho_col_2,$ancho_col_2,$ancho_col_2,$ancho_col_2,$ancho_col_2,$ancho_col_2,$ancho_col_2,$ancho_col_2*2,$ancho_col_2*3));
$titulos = array(mb_convert_encoding('% DE AFECTACIÓN',"ISO-8859-1", "UTF-8"),'-','-','-','-','-','-','-','-','-','-','-','-','-','-');
$pdf->Cell($ancho_cuadro_1,$alto,'',0,0,'',false,'');
$pdf->Row($titulos, $alto);

$pdf->SetWidths(array($ancho_col_1,$ancho_col_2*3,$ancho_col_2*3,$ancho_col_2*6,$ancho_col_2*5));
$titulos = array('ENFERMEDAD',mb_convert_encoding('CÓLERA PORCINO',"ISO-8859-1", "UTF-8"),
mb_convert_encoding('NEUMONÍA',"ISO-8859-1", "UTF-8"),
mb_convert_encoding('ESTADO DE LOS NÓDULOS LINFÁTICOS',"ISO-8859-1", "UTF-8"),'OTROS');
$pdf->Cell($ancho_cuadro_1,$alto,'',0,0,'',false,'');
$pdf->Row($titulos, $alto);

$pdf->SetWidths(array($ancho_col_1,$ancho_col_2,$ancho_col_2,$ancho_col_2,$ancho_col_2,$ancho_col_2,$ancho_col_2,$ancho_col_2*6,$ancho_col_2*5));
$titulos = array('LOCALIZACION',
    mb_convert_encoding('Hígado',"ISO-8859-1", "UTF-8"),
    mb_convert_encoding('Pulmón',"ISO-8859-1", "UTF-8"),'Otros',
    mb_convert_encoding('Hígado',"ISO-8859-1", "UTF-8"),
    mb_convert_encoding('Pulmón',"ISO-8859-1", "UTF-8"),'General','','');
$pdf->Cell($ancho_cuadro_1,$alto,'',0,0,'',false,'');
$pdf->Row($titulos, $alto);

$titulos = array('PRESENCIA ','-','-','-','-','-','-','','');
$pdf->Cell($ancho_cuadro_1,$alto,'',0,0,'',false,'');
$pdf->Row($titulos, $alto);

$titulos = array(mb_convert_encoding('% DE AFECTACIÓN',"ISO-8859-1", "UTF-8"),'-','-','-','-','-','-','','');
$pdf->Cell($ancho_cuadro_1,$alto,'',0,0,'',false,'');
$pdf->Row($titulos, $alto);

$pdf->SetXY($x, $y);
$pdf->SetFillColor($color_cuadro_verde[0], $color_cuadro_verde[1],$color_cuadro_verde[2]);
$pdf->Cell($ancho_cuadro_1,$alto * 15,'PORCINOS',1,1,'',true,'');
$pdf->Cell(0,$alto,'',0,1,'',false,'');

// Cuadro 3
$x = $pdf->GetX();
$y = $pdf->GetY();
$pdf->SetFont($font,'',$tam_font);
$pdf->Cell($ancho_cuadro_1,$alto,'',0,0,'',false,'');
$pdf->Cell($ancho_col_1,$alto,'Nro. de CSMI:',0,0,'R',false,'');
$pdf->Cell($ancho_col_2*2,$alto,'10',1,0,$align,false,'');
$pdf->Cell($ancho_col_2*3,$alto,'',0,0,'',false,'');
$pdf->Cell($ancho_col_2,$alto,'No. Hembras:',0,0,'R',false,'');
$pdf->Cell($ancho_col_2*2,$alto,'3',1,0,$align,false,'');
$pdf->Cell($ancho_col_2*2,$alto,'Etapa Productiva:',0,0,'R',false,'');
$pdf->Cell($ancho_col_2*7,$alto,'OVEJAS','B',1,$align,false,'');

$pdf->Cell($ancho_cuadro_1+$ancho_col_1+$ancho_col_2*2,$alto,'',0,0,'',false,'');
$pdf->Cell($ancho_col_2*2,$alto,'No. total de animales:',0,0,'R',false,'');
$pdf->Cell($ancho_col_2,$alto,'121',1,1,$align,false,'');

$pdf->Cell($ancho_cuadro_1,$alto,'',0,0,'',false,'');
$pdf->Cell($ancho_col_1,$alto,'Hora',0,0,'R',false,'');
$pdf->Cell($ancho_col_2*2,$alto,'00:00-08:00',1,0,$align,false,'');
$pdf->Cell($ancho_col_2*3,$alto,'',0,0,'',false,'');
$pdf->Cell($ancho_col_2,$alto,'No. Machos:',0,0,'R',false,'');
$pdf->Cell($ancho_col_2*2,$alto,'118',1,0,$align,false,'');
$pdf->Cell($ancho_col_2*2,$alto,'Etapa Productiva:',0,0,'R',false,'');
$pdf->Cell($ancho_col_2*7,$alto,'CARNERO-CORDERO, ADULTO','B',1,$align,false,'');
$pdf->Cell(0,$alto,'',0,1,'',false,'');

$pdf->Cell($ancho_cuadro_1,$alto,'',0,0,'',false,'');
$pdf->SetFillColor($color_cuadro_rojo[0], $color_cuadro_rojo[1],$color_cuadro_rojo[2]);
$pdf->Cell(0,$alto,mb_convert_encoding('B 3. HALLAZGOS DIAGNOSTICADOS AL EXAMEN POST MORTEM OVINOS/CAPRINOS',"ISO-8859-1", "UTF-8"),0,1,$align,true,'');

$pdf->SetAligns(array($align,$align,$align,$align,$align,$align,$align,$align,$align,$align,$align,$align,$align,$align,$align,$align,$align,$align));
$pdf->SetWidths(array($ancho_col_1,$ancho_col_2*6,$ancho_col_2*2,$ancho_col_2*2,$ancho_col_2*4,$ancho_col_2*3));
$titulos = array('ENFERMEDAD',mb_convert_encoding('ENDOPARÁSITOS',"ISO-8859-1", "UTF-8"),'CETOSIS','ACIDOSIS RUMINAL',
mb_convert_encoding('ESTADO DE LOS NÓDULOS LINFÁTICOS',"ISO-8859-1", "UTF-8"),'OTROS');
$pdf->Cell($ancho_cuadro_1,$alto,'',0,0,'',false,'');
$pdf->Row($titulos, $alto);

$pdf->SetWidths(array($ancho_col_1,$ancho_col_2*6,$ancho_col_2,$ancho_col_2,$ancho_col_2,$ancho_col_2,$ancho_col_2*4,$ancho_col_2*3));
$titulos = array('LOCALIZACION','',
    mb_convert_encoding('Hígado',"ISO-8859-1", "UTF-8"),'Rumen','Rumen','Contenido ruminal','','');
$pdf->Cell($ancho_cuadro_1,$alto,'',0,0,'',false,'');
$pdf->Row($titulos, $alto);

$titulos = array('PRESENCIA','','-','-','-','-','','');
$pdf->Cell($ancho_cuadro_1,$alto,'',0,0,'',false,'');
$pdf->Row($titulos, $alto);

$titulos = array(mb_convert_encoding('% DE AFECTACIÓN',"ISO-8859-1", "UTF-8"),'','-','-','-','-','','');
$pdf->Cell($ancho_cuadro_1,$alto,'',0,0,'',false,'');
$pdf->Row($titulos, $alto);

$pdf->SetXY($x, $y);
$pdf->SetFillColor($color_cuadro_verde[0], $color_cuadro_verde[1],$color_cuadro_verde[2]);
$pdf->SetWidths(array($ancho_cuadro_1));
// $pdf->Multicell($ancho_cuadro_1,$alto * 5,mb_convert_encoding('OVINOS/ CAPRINOS/ CAMÉLIDOS',"ISO-8859-1", "UTF-8"),1,'C');
$pdf->Row(array(mb_convert_encoding('OVINOS/ CAPRINOS/ CAMÉLIDOS',"ISO-8859-1", "UTF-8")), $alto*3.33, true, true);

$pdf->Cell(0,$alto,'',0,1,'',false,'');

$pdf->AddPage();


// Cuadro 4
$x = $pdf->GetX();
$y = $pdf->GetY();
$pdf->SetFont($font,'',$tam_font);
$pdf->Cell($ancho_cuadro_1,$alto,'',0,0,'',false,'');
$pdf->Cell($ancho_col_1,$alto,'Nro. de CSMI:',0,0,'R',false,'');
$pdf->Cell($ancho_col_2*2,$alto,'10',1,0,$align,false,'');
$pdf->Cell($ancho_col_2*3,$alto,'',0,0,'',false,'');
$pdf->Cell($ancho_col_2,$alto,'No. Hembras:',0,0,'R',false,'');
$pdf->Cell($ancho_col_2*2,$alto,'3',1,0,$align,false,'');
$pdf->Cell($ancho_col_2*2,$alto,'Etapa Productiva:',0,0,'R',false,'');
$pdf->Cell($ancho_col_2*7,$alto,'OVEJAS','B',1,$align,false,'');

$pdf->Cell($ancho_cuadro_1+$ancho_col_1+$ancho_col_2*2,$alto,'',0,0,'',false,'');
$pdf->Cell($ancho_col_2*2,$alto,'No. total de animales:',0,0,'R',false,'');
$pdf->Cell($ancho_col_2,$alto,'121',1,1,$align,false,'');

$pdf->Cell($ancho_cuadro_1,$alto,'',0,0,'',false,'');
$pdf->Cell($ancho_col_1,$alto,'Hora',0,0,'R',false,'');
$pdf->Cell($ancho_col_2*2,$alto,'00:00-08:00',1,0,$align,false,'');
$pdf->Cell($ancho_col_2*3,$alto,'',0,0,'',false,'');
$pdf->Cell($ancho_col_2,$alto,'No. Machos:',0,0,'R',false,'');
$pdf->Cell($ancho_col_2*2,$alto,'118',1,0,$align,false,'');
$pdf->Cell($ancho_col_2*2,$alto,'Etapa Productiva:',0,0,'R',false,'');
$pdf->Cell($ancho_col_2*7,$alto,'CARNERO-CORDERO, ADULTO','B',1,$align,false,'');
$pdf->Cell(0,$alto,'',0,1,'',false,'');

$pdf->Cell($ancho_cuadro_1,$alto,'',0,0,'',false,'');
$pdf->SetFillColor($color_cuadro_rojo[0], $color_cuadro_rojo[1],$color_cuadro_rojo[2]);
$pdf->Cell(0,$alto,mb_convert_encoding('B 3. HALLAZGOS DIAGNOSTICADOS AL EXAMEN POST MORTEM OVINOS/CAPRINOS',"ISO-8859-1", "UTF-8"),0,1,$align,true,'');

$pdf->SetAligns(array($align,$align,$align,$align,$align,$align,$align,$align,$align,$align,$align,$align,$align,$align,$align,$align,$align,$align));
$pdf->SetWidths(array($ancho_col_1,$ancho_col_2*6,$ancho_col_2*2,$ancho_col_2*2,$ancho_col_2*4,$ancho_col_2*3));
$titulos = array('ENFERMEDAD',mb_convert_encoding('ENDOPARÁSITOS',"ISO-8859-1", "UTF-8"),'CETOSIS','ACIDOSIS RUMINAL',
mb_convert_encoding('ESTADO DE LOS NÓDULOS LINFÁTICOS',"ISO-8859-1", "UTF-8"),'OTROS');
$pdf->Cell($ancho_cuadro_1,$alto,'',0,0,'',false,'');
$pdf->Row($titulos, $alto);

$pdf->SetWidths(array($ancho_col_1,$ancho_col_2*6,$ancho_col_2,$ancho_col_2,$ancho_col_2,$ancho_col_2,$ancho_col_2*4,$ancho_col_2*3));
$titulos = array('LOCALIZACION','',
    mb_convert_encoding('Hígado',"ISO-8859-1", "UTF-8"),'Rumen','Rumen','Contenido ruminal','','');
$pdf->Cell($ancho_cuadro_1,$alto,'',0,0,'',false,'');
$pdf->Row($titulos, $alto);

$titulos = array('PRESENCIA','','-','-','-','-','','');
$pdf->Cell($ancho_cuadro_1,$alto,'',0,0,'',false,'');
$pdf->Row($titulos, $alto);

$titulos = array(mb_convert_encoding('% DE AFECTACIÓN',"ISO-8859-1", "UTF-8"),'','-','-','-','-','','');
$pdf->Cell($ancho_cuadro_1,$alto,'',0,0,'',false,'');
$pdf->Row($titulos, $alto);

$pdf->SetXY($x, $y);
$pdf->SetFillColor($color_cuadro_verde[0], $color_cuadro_verde[1],$color_cuadro_verde[2]);
$pdf->SetWidths(array($ancho_cuadro_1));
// $pdf->Multicell($ancho_cuadro_1,$alto * 5,mb_convert_encoding('OVINOS/ CAPRINOS/ CAMÉLIDOS',"ISO-8859-1", "UTF-8"),1,'C');
$pdf->Row(array(mb_convert_encoding('CUYES/ CONEJOS',"ISO-8859-1", "UTF-8")), $alto*6, true, true);

$pdf->Cell(0,$alto,'',0,1,'',false,'');

// Cuadro 5
$x = $pdf->GetX();
$y = $pdf->GetY();
$pdf->SetFont($font,'',$tam_font);
$pdf->Cell($ancho_cuadro_1,$alto,'',0,0,'',false,'');
$pdf->Cell($ancho_col_1,$alto,'Nro. de CSMI:',0,0,'R',false,'');
$pdf->Cell($ancho_col_2*2,$alto,'10',1,0,$align,false,'');
$pdf->Cell($ancho_col_2*3,$alto,'',0,0,'',false,'');
$pdf->Cell($ancho_col_2,$alto,'No. Hembras:',0,0,'R',false,'');
$pdf->Cell($ancho_col_2*2,$alto,'3',1,0,$align,false,'');
$pdf->Cell($ancho_col_2*2,$alto,'Etapa Productiva:',0,0,'R',false,'');
$pdf->Cell($ancho_col_2*7,$alto,'OVEJAS','B',1,$align,false,'');

$pdf->Cell($ancho_cuadro_1+$ancho_col_1+$ancho_col_2*2,$alto,'',0,0,'',false,'');
$pdf->Cell($ancho_col_2*2,$alto,'No. total de animales:',0,0,'R',false,'');
$pdf->Cell($ancho_col_2,$alto,'121',1,1,$align,false,'');

$pdf->Cell($ancho_cuadro_1,$alto,'',0,0,'',false,'');
$pdf->Cell($ancho_col_1,$alto,'Hora',0,0,'R',false,'');
$pdf->Cell($ancho_col_2*2,$alto,'00:00-08:00',1,0,$align,false,'');
$pdf->Cell($ancho_col_2*3,$alto,'',0,0,'',false,'');
$pdf->Cell($ancho_col_2,$alto,'No. Machos:',0,0,'R',false,'');
$pdf->Cell($ancho_col_2*2,$alto,'118',1,0,$align,false,'');
$pdf->Cell($ancho_col_2*2,$alto,'Etapa Productiva:',0,0,'R',false,'');
$pdf->Cell($ancho_col_2*7,$alto,'CARNERO-CORDERO, ADULTO','B',1,$align,false,'');
$pdf->Cell(0,$alto,'',0,1,'',false,'');

$pdf->Cell($ancho_cuadro_1,$alto,'',0,0,'',false,'');
$pdf->SetFillColor($color_cuadro_rojo[0], $color_cuadro_rojo[1],$color_cuadro_rojo[2]);
$pdf->Cell(0,$alto,mb_convert_encoding('B 3. HALLAZGOS DIAGNOSTICADOS AL EXAMEN POST MORTEM OVINOS/CAPRINOS',"ISO-8859-1", "UTF-8"),0,1,$align,true,'');

$pdf->SetAligns(array($align,$align,$align,$align,$align,$align,$align,$align,$align,$align,$align,$align,$align,$align,$align,$align,$align,$align));
$pdf->SetWidths(array($ancho_col_1,$ancho_col_2*6,$ancho_col_2*2,$ancho_col_2*2,$ancho_col_2*4,$ancho_col_2*3));
$titulos = array('ENFERMEDAD',mb_convert_encoding('ENDOPARÁSITOS',"ISO-8859-1", "UTF-8"),'CETOSIS','ACIDOSIS RUMINAL',
mb_convert_encoding('ESTADO DE LOS NÓDULOS LINFÁTICOS',"ISO-8859-1", "UTF-8"),'OTROS');
$pdf->Cell($ancho_cuadro_1,$alto,'',0,0,'',false,'');
$pdf->Row($titulos, $alto);

$pdf->SetWidths(array($ancho_col_1,$ancho_col_2*6,$ancho_col_2,$ancho_col_2,$ancho_col_2,$ancho_col_2,$ancho_col_2*4,$ancho_col_2*3));
$titulos = array('LOCALIZACION','',
    mb_convert_encoding('Hígado',"ISO-8859-1", "UTF-8"),'Rumen','Rumen','Contenido ruminal','','');
$pdf->Cell($ancho_cuadro_1,$alto,'',0,0,'',false,'');
$pdf->Row($titulos, $alto);

$titulos = array('PRESENCIA','','-','-','-','-','','');
$pdf->Cell($ancho_cuadro_1,$alto,'',0,0,'',false,'');
$pdf->Row($titulos, $alto);

$titulos = array(mb_convert_encoding('% DE AFECTACIÓN',"ISO-8859-1", "UTF-8"),'','-','-','-','-','','');
$pdf->Cell($ancho_cuadro_1,$alto,'',0,0,'',false,'');
$pdf->Row($titulos, $alto);

$pdf->SetXY($x, $y);
$pdf->SetFillColor($color_cuadro_verde[0], $color_cuadro_verde[1],$color_cuadro_verde[2]);
$pdf->SetWidths(array($ancho_cuadro_1));
$pdf->Cell($ancho_cuadro_1,$alto * 15,'AVES',1,1,'',true,'');


$pdf->Cell(0,$alto,'',0,1,'',false,'');






/*

$pdf->Cell(0,$alto * 9,'',1,1,'',false,'');


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
foreach($data as $fila){
    $fila_datos = array_slice($fila, 0, 26);
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
*/
// $pdf->Contenido();
$pdf->Output();


?>