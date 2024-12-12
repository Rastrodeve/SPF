<?php

include '../FilePHP/utils.php';
include 'operaciones.php';
$Document = "ERROR";
$DocumentD = "ERROR";
$html = "<h1>ERROR 1234</h1>";
$Url = "../documentos/";
$agrocalidad = false;
$vertical = true;
$rastro_vertical = true;
$nombre_cabecera = '';
$array = [10, 10, 18, 10, 5, 5]; // 20(Izquierdo),20(Derecho),45(Top Cuerpo),30(Button-Cuerpo),22(Top Cabecera),16(Bottom Footer)
if (isset($_SESSION['OPCION'])) {
    $op = $_SESSION['OPCION'];
    $dbConn = conectar($db);
    if ($op == 1) {
        $DocumentD = "Reporte de ingreso";
        // $rastro_vertical = false;
        $html = Reporte_Ingreso_animal($dbConn);
        $nombre_cabecera = 'Reporte de ingreso de ganado';
    } else if ($op == 2) {
        $html = f_orden_produccion($dbConn);
        $DocumentD = 'Orden de produccion';
        $Document = 'producion/orden/' . $_SESSION['VARIABLE'] . '.pdf';
    } else if ($op == 3) {
        $html = f_orden_produccion_emergente($dbConn);
        $DocumentD = 'Orden de produccion emergente';
        $Document = 'producion/emergente/' . $_SESSION['VARIABLE'] . '.pdf';
    } else if ($op == 4) {
        $html = f_acta_decomiso($dbConn);
        $DocumentD = 'Acta decomiso';
        $Document = 'decomiso/acta/' . $_SESSION['VARIABLE'] . '.pdf';
        $agrocalidad = true;
    } else if ($op == 5) {
        $html = f_guia_origen($dbConn, $_SESSION['VARIABLE'], 0);
        $DocumentD = 'Guía de Origen';
        $Document = 'origen/' . md5($_SESSION['VARIABLE']) . '.pdf';
        $agrocalidad = true;
        $array = [7, 7, 28, 15, 6, 6]; // 7(Izquierdo),7(Derecho),28(Top Cuerpo),15(Button-Cuerpo),22(Top Cabecera),16(Bottom Footer)
    } else if ($op == 6) {
        $DocumentD = "Reporte de saldos y faenamiento";
        // $rastro_vertical = false;
        $html = Reporte_saldos($dbConn);
        $nombre_cabecera = 'Reporte de saldos y faenamiento';
    } else if ($op == 7) {
        $DocumentD = "Reporte de corralaje";
        $rastro_vertical = false;
        $html = Reporte_corralaje($dbConn);
    } else if ($op == 8) {
        $DocumentD = "Reporte de taza";
        $html = Reporte_taza($dbConn);
        $nombre_cabecera = 'Reporte de taza';
    }
    // else if ($op == 8){
    //     $html = '<h1>HOLA</h1><h1>HOLA</h1><h1>HOLA</h1><h1>HOLA</h1><h1>HOLA</h1><h1>HOLA</h1><h1>HOLA</h1><h1>HOLA</h1><h1>HOLA</h1><h1>HOLA</h1><h1>HOLA</h1><h1>HOLA</h1><h1>HOLA</h1><h1>HOLA</h1><h1>HOLA</h1><h1>HOLA</h1><h1>HOLA</h1>';
    //     $DocumentD = 'Guía de Origen Prueba';
    //     $agrocalidad =true;
    //     $array = [7, 7, 28, 5, 6, 6];// 20(Izquierdo),20(Derecho),45(Top Cuerpo),30(Button-Cuerpo),22(Top Cabecera),16(Bottom Footer)
    // }
}
$descagar_2 = false;
if (isset($_GET["ORDEN"])) {
    $dbConn = conectar($db);
    $html = f_orden_produccion($dbConn, $orden = trim($_GET["ORDEN"]), $especie = $_GET["ESPECIE"], $date = date("02-07-2024"));
    $DocumentD = 'Orden de produccion';
    $Document = 'producion/orden/' . $_GET["ORDEN"] . '.pdf';
    $descagar_2 = true;
}
include("MPDF57/mpdf.php");
$mpdf = new mPDF('utf-8', 'A4', '', '', $array[0], $array[1], $array[2], $array[3], $array[4], $array[5]); // 20(Izquierdo),20(Derecho),45(Top Cuerpo),30(Button-Cuerpo),22(Top Cabecera),16(Bottom Footer)
if ($vertical == false) {
    $mpdf = new mPDF('utf-8', 'A4-L', '', '', 33, 33, 40, 30, 14, 16); // 20(Izquierdo),20(Derecho),45(Top Cuerpo),30(Button-Cuerpo),22(Top Cabecera),16(Bottom Footer)
}
if ($rastro_vertical == false) {
    $mpdf = new mPDF('utf-8', 'A4-L', '', '', $array[0], $array[1], $array[2], $array[3], $array[4], $array[5]); // 20(Izquierdo),20(Derecho),45(Top Cuerpo),30(Button-Cuerpo),22(Top Cabecera),16(Bottom Footer)
}


if ($agrocalidad) {
    // $mpdf->SetHTMLHeader('<img src="../recursos/logo_republica_ecuador.png"  style="margin-left:39px;" height="2cm" ><img src="../recursos/empresa_publica_rastro.png"   width="7.7cm" style="float: right;margin-top:35px;margin-right:38px;" >', '', true);
    //Quitado 14 10 2022 $mpdf->SetHTMLHeader('<img src="../recursos/logo_republica_ecuador.png"  style="margin-left:39px;" height="2cm" ><img src="../recursos/agencia_regulacion.png"   width="7.7cm" style="float: right;margin-top:35px;margin-right:38px;" >', '', true);
    // $mpdf->SetHTMLFooter('<div style="Text-align:right;"><img src="../recursos/logo_gobierno_del_encuentro.png" style="margin-right:30px;"  height="2.2cm"> </div>','O', true);
    //Quitado 14 10 2022 $mpdf->SetHTMLFooter('<img src="../recursos/empresa_publica_rastro_left.png" style="float: left;margin-top:35px"   height="1.3cm" ><div style="Text-align:right;"><img src="../recursos/logo_gobierno_del_encuentro.png" style="margin-right:30px;"  height="2.2cm"> </div>','O', true);
    $mpdf->SetHTMLFooter('<img src="../recursos/empresa_publica_rastro_left.png" style="float: left;margin-top:35px"   height="1.3cm" >', 'O', true);
    if ($vertical == false) {
        //Quitado 14 10 2022 $mpdf->SetHTMLHeader('<img src="../recursos/logo_republica_ecuador.png"   height="2.7cm" ><img src="../recursos/agencia_regulacion.png"  height="1cm" width="11cm" style="float: right;margin-top:45px;" >', '', true);
        //Quitado 14 10 2022 $mpdf->SetHTMLFooter('<div style="Text-align:right;"><img src="../recursos/logo_gobierno_del_encuentro.png" style="margin-right:20px;"  height="2.3cm"> </div>','O', true);
    }
} else {
    $header = '<table style="width: 100%;font-family:Arial;font-size: 9x;margin-top:0px;">
        <tr>
            <td>
                <img src="../recursos/rastro_logo_2.png"  height="1.4cm" >
            </td>
            <td style="text-align: right;">
                ' . $nombre_cabecera . '
            </td>
        </tr>
    </table>';
    $mpdf->SetHTMLHeader($header, '', true);
    $array = get_data_user($dbConn);
    $informacion = '
    <table style="width: 100%;font-family:Arial;font-size: 9px;margin-top:0px;">
        <tr>
            <td>
                <b>Generado por:</b>  ' . $array[0] . '
            </td>
            <td style="text-align: right;">
                <b>Fecha de reporte:</b>  ' . date("Y-m-d H:i:s") . '
            </td>
        </tr>
    </table>';
    $mpdf->SetHTMLFooter($informacion, 'O', true);
}
$mpdf->WriteHTML($html);
if (isset($_SESSION['OPCION'])) {
    if ($_SESSION['OPCION'] == 2 || $_SESSION['OPCION'] == 3 || $_SESSION['OPCION'] == 5) $mpdf->Output($Url . $Document, 'F'); //guarda a ruta
}
if ($descagar_2) {
    $mpdf->Output($Url . $Document, 'F'); //guarda a ruta
}

$mpdf->Output($DocumentD . '.pdf', 'D'); //DESCARGAR DIRECTAMENTE 
// $mpdf->Output(); //MOSTRAR EN LA WEB
exit;

//==============================================================
//==============================================================
//==============================================================
