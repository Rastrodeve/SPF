<?php
if (isset($_GET["DOCUMENTO"])) {
    include '../FilePHP/utils.php';
    include 'operaciones.php';
    $dbConn = conectar($db);
    $html = f_guia_origen($dbConn,$_GET["DOCUMENTO"],1);
    $Document = '../documentos/origen/'.$_GET["DOCUMENTO"]  .'.pdf';
    $agrocalidad =true;
    $array = [7, 7, 28, 5, 6, 6];// 20(Izquierdo),20(Derecho),45(Top Cuerpo),30(Button-Cuerpo),22(Top Cabecera),16(Bottom Footer)
    include("MPDF57/mpdf.php");
    $mpdf = new mPDF('utf-8', 'A4', '', '', $array[0], $array[1], $array[2], $array[3], $array[4], $array[5]);// 20(Izquierdo),20(Derecho),45(Top Cuerpo),30(Button-Cuerpo),22(Top Cabecera),16(Bottom Footer)
    $mpdf->SetHTMLHeader('<img src="../recursos/logo_republica_ecuador.png"  style="margin-left:39px;" height="2cm" ><img src="../recursos/agencia_regulacion.png"   width="7.7cm" style="float: right;margin-top:35px;margin-right:38px;" >', '', true);
    $mpdf->SetHTMLFooter('<div style="Text-align:right;"><img src="../recursos/logo_gobierno_del_encuentro.png" style="margin-right:30px;"  height="2.2cm"> </div>','O', true);
    $mpdf->WriteHTML($html);
    $mpdf->Output($Document, 'F'); //guarda a ruta
    exit;
}
?>