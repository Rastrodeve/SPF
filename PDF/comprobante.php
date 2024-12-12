<?php
include '../FilePHP/utils.php';
include("MPDF57/mpdf.php");
if (isset($_SESSION['OPCION'])) {
    if ($_SESSION['OPCION'] == 13 || $_SESSION['OPCION'] == 14) {
        $dbConn = conectar($db);
        get_comprobante($_SESSION['VARIABLE'], $dbConn, $_SESSION['OPCION']);
    }
}
if (isset($_GET["Data"])) {
    $dbConn = conectar($db);
    get_comprobante($_GET['Data'], $dbConn, 14);
}

function get_comprobante_2($Id, $dbConn, $op)
{
    $consulta = "SELECT *
    FROM tbl_r_guiaproceso p, tbl_r_guiamovilizacion g, tbl_a_especies e, tbl_a_clientes c 
    WHERE p.guiId = g.guiId AND p.espId = e.espId AND p.cliId = c.cliId AND p.gprId = :id";
    $sql = $dbConn->prepare($consulta);
    $sql->bindValue(':id', $Id);
    $sql->execute();
    if ($row = $sql->fetch()) {
        $usuario = get_data_user_2($dbConn, $row["usuId"]);
        $Datos = '
            <div style="width: 100%;">
                <div style="float:left;width: 75%;">
                    <span style="font-size:14px;margin-bottom:5px;"><b>EMPRESA PÚBLICA METROPOLITANA DE RASTRO QUITO</b></span><br>
                    <span style="font-size:14px;margin-bottom:5px;"><b>COMPROBANTE DE INGRESO</b></span><br>
                    <p style="font-size:12px;line-height: 200%;border:1px solid #C0C0C0;padding:5px 10px;border-radius: 3px;">
                        <span style="font-size:25px;">TURNO: <b>1</b></span><br>
                        Fecha de faenamiento: <b>' . Transformar_Fecha(explode(" ", $row["gprTurno"])[0]) . '</b><br>
                    </p>
                </div>
                <div style="float:left;width: 25%;text-align:center">
                    <img src="../recursos/especies/' . $row["espImagen"] . '" width="110px">
                    <br>
                    <span style="background:yellow;"><b>' . utf8_encode($row["espDescripcion"]) . '</b></span>
                </div>    
            </div>
            <div style="width: 100%;border:1px solid #C0C0C0;border-radius: 3px;padding:5px 10px;">
                <p style="font-size:12px;line-height: 200%;margin:0px;">
                    Guia de movilizacion número: <b>' . utf8_encode($row["guiNumero"]) . '</b><br>
                    Comprobante de Ingreso número: <span style="background:yellow;"><b>' . utf8_encode($row["gprComprobante"]) . '</b></span>
                </p>
                
            </div>
            <br>
            <div style="width: 100%;border:1px solid #C0C0C0;border-radius: 3px;padding:5px 10px;">
                <div style="float:left;width: 60%;">
                    <p style="font-size:12px;line-height: 200%;margin:0px;">
                        Nombre del conductor: <b>' . utf8_encode($row["guiNombreConductor"]) . '</b>
                        <br>
                        Placa del vehiculo: <b>' . utf8_encode($row["guiVehiculoPlaca"]) . '</b>
                    </p>
                </div>
                <div style="float:left;width: 40%;">
                    <p style="font-size:12px;line-height: 200%;margin:0px;">
                        C.C Conductor: <b>' . $row["guiCiConductor"] . '</b>
                    </p>
                </div>    
            </div>
            <br>
            <div style="width: 100%;border:1px solid #C0C0C0;border-radius: 3px;padding:5px 10px;">
                <div>
                    <div style="float:left;width: 60%;">
                        <p style="font-size:12px;line-height: 200%;margin:0px;">
                            Apellidos y Nombres del Introductor:<br> <span style="background:yellow;"><b>' . utf8_encode($row["cliNombres"]) . '</b></span>
                            <br>
                        </p>
                    </div>
                    <div style="float:left;width: 40%;">
                        <p style="font-size:12px;line-height: 200%;margin:0px;">
                            Cédula o RUC:<br> <b>' . $row["cliNumero"] . '</b>
                        </p>
                    </div>   
                </div> 
            </div>
            <br>
            <div style="width: 100%;border:1px solid #C0C0C0;border-radius: 3px;padding:5px 10px;">
                <font style="font-size:12px;">Detalle: </font>
                <div style="padding-bottom:10px;">
                    <table border="0" style="border-collapse: collapse;font-size:11px;" width="100%;" >
                        <tr>
                            <th>Género</th>
                            <th>Cantidad</th>
                        </tr>
                        <tr >
                            <td style="width:50%;text-align:center;padding:5px;">Machos</td>
                            <td style="width:50%;text-align:center;padding:5px;">' . $row["gprMacho"] . '</td>
                        </tr>
                        <tr >
                            <td style="width:50%;text-align:center;padding:5px;">Hembras</td>
                            <td style="width:50%;text-align:center;padding:5px;">' . $row["gprHembra"] . '</td>
                        </tr>
                        <tr >
                            <th style="width:50%;text-align:center;padding:5px;font-size:18px;" >TOTAL</th>
                            <th style="width:50%;text-align:center;padding:5px;font-size:20px;"> <span style="background:yellow;">' . ($row["gprMacho"] + $row["gprHembra"]) . '</span></th>
                        </tr>
                    </table>
                </div>
            </div>
            <br>
            <div style="width: 100%;border:1px solid #C0C0C0;border-radius: 3px;padding:5px 10px;">
                <font style="font-size:12px;">VALOR A PAGAR: </font>
                <div style="padding-bottom:10px;">
                    <table border="0" style="border-collapse: collapse;font-size:11px;" width="100%;" >
                        <tr>
                            <th>Servicio</th>
                            <th>Precio</th>
                        </tr>
                        ' . get_servicios($dbConn, $row["espId"], ($row["gprMacho"] + $row["gprHembra"])) . '
                    </table>
                </div>
            </div>
            <br>';
        $usuario = get_data_user_2($dbConn, $_SESSION['MM_Username']);
        $header = '
        <div style="width: 100%;">
            <div style="float:left;width: 75%;">
                <img src="../recursos/rastro_logo_2.png" width="300px" >
                <img  src="../recursos/Mabio.png" width="55px">
            </div>
            <div style="float:left;width: 25%;text-align:right;font-size: 8px;padding-top:25px;">
                <b>Comprobante de Ingreso:</b> ' . utf8_encode($row["gprComprobante"]) . '
            </div>    
        </div>';
        $footer = '
        <div style="width: 100%;">
            <div style="float:left;width: 50%;font-size: 8px;">
                <b>Generado por:</b> ' . $usuario[0] . '
            </div>
            <div style="float:left;width: 50%;text-align:right;font-size: 8px;">
                <b>Fecha:</b> ' . date("Y-m-d H:i:s") . '
            </div>    
        </div>';
        $estilos = '
        div, table {
            font-family:"Calibri, sans-serif";
        }';
        $array = [15, 15, 40, 15, 10, 10]; // 20(Izquierdo),20(Derecho),45(Top Cuerpo),30(Button-Cuerpo),22(Top Cabecera),16(Bottom Footer)
        $html = FormatoDocumento('', $Datos, $estilos);
        $mpdf = new mPDF('utf-8', 'A4', '', '', $array[0], $array[1], $array[2], $array[3], $array[4], $array[5]); // 20(Izquierdo),20(Derecho),45(Top Cuerpo),30(Button-Cuerpo),22(Top Cabecera),16(Bottom Footer)
        $mpdf->SetHTMLHeader($header, '', true);
        $mpdf->SetHTMLFooter($footer, 'O', true);
        $mpdf->WriteHTML($html);
        if ($op == 14) $mpdf->Output(utf8_encode($row["gprComprobante"]) . '.pdf', 'D'); //DESCARGAR DIRECTAMENTE 
        else $mpdf->Output();
        exit;
    } else {
        echo "HOLA MUNDO";
    }
}






function get_comprobante($Id, $dbConn, $op)
{
    $consulta = "SELECT *
    FROM tbl_r_guiaproceso p, tbl_r_guiamovilizacion g, tbl_a_especies e, tbl_a_clientes c 
    WHERE p.guiId = g.guiId AND p.espId = e.espId AND p.cliId = c.cliId AND p.gprId = :id";
    $sql = $dbConn->prepare($consulta);
    $sql->bindValue(':id', $Id);
    $sql->execute();
    if ($row = $sql->fetch()) {
        $usuario = get_data_user_2($dbConn, $row["usuId"]);
        $Datos = '
            <div style="width: 100%;">
                <div style="float:left;width: 75%;">
                    <span style="font-size:14px;margin-bottom:5px;"><b>EMPRESA PÚBLICA METROPOLITANA DE RASTRO QUITO</b></span><br>
                    <span style="font-size:14px;margin-bottom:5px;"><b>COMPROBANTE DE INGRESO</b></span><br>
                    <p style="font-size:12px;line-height: 200%;border:1px solid #C0C0C0;padding:5px 10px;border-radius: 3px;">
                        Guia de movilizacion número: <b>' . utf8_encode($row["guiNumero"]) . '</b><br>
                        Comprobante de Ingreso número: <span style="background:yellow;"><b>' . utf8_encode($row["gprComprobante"]) . '</b></span>
                        <br>
                        Fecha y Hora de Ingreso: <b>' . $row["gprTurno"] . '</b>
                    </p>
                </div>
                <div style="float:left;width: 25%;text-align:center">
                    <img src="../recursos/especies/' . $row["espImagen"] . '" width="160px">
                    <br>
                    <span style="background:yellow;"><b>' . utf8_encode($row["espDescripcion"]) . '</b></span>
                </div>    
            </div>
            <br>
            <div style="width: 100%;border:1px solid #C0C0C0;border-radius: 3px;padding:5px 10px;">
                <div style="float:left;width: 60%;">
                    <p style="font-size:12px;line-height: 200%;margin:0px;">
                        Nombre del conductor: <b>' . utf8_encode($row["guiNombreConductor"]) . '</b>
                        <br>
                        Placa del vehiculo: <b>' . utf8_encode($row["guiVehiculoPlaca"]) . '</b>
                    </p>
                </div>
                <div style="float:left;width: 40%;">
                    <p style="font-size:12px;line-height: 200%;margin:0px;">
                        C.C Conductor: <b>' . $row["guiCiConductor"] . '</b>
                    </p>
                </div>    
            </div>
            <br>
            <div style="width: 100%;border:1px solid #C0C0C0;border-radius: 3px;padding:5px 10px;">
                <div>
                    <div style="float:left;width: 60%;">
                        <p style="font-size:12px;line-height: 200%;margin:0px;">
                            Apellidos y Nombres: <span style="background:yellow;"><b>' . utf8_encode($row["cliNombres"]) . '</b></span>
                            <br>
                            Marca: <b>' . utf8_encode($row["cliMarca"]) . '</b>
                        </p>
                    </div>
                    <div style="float:left;width: 40%;">
                        <p style="font-size:12px;line-height: 200%;margin:0px;">
                            Cédula o RUC: <b>' . $row["cliNumero"] . '</b>
                        </p>
                    </div>   
                </div> 
                <div style="padding-bottom:10px;">
                    <table border="0" style="border-collapse: collapse;font-size:11px;" width="100%;" >
                        <tr >
                            <td style="width:50%;text-align:center;padding:5px;">Machos</td>
                            <td style="width:50%;text-align:center;padding:5px;">' . $row["gprMacho"] . '</td>
                        </tr>
                        <tr >
                            <td style="width:50%;text-align:center;padding:5px;">Hembras</td>
                            <td style="width:50%;text-align:center;padding:5px;">' . $row["gprHembra"] . '</td>
                        </tr>
                        <tr >
                            <th style="width:50%;text-align:center;padding:5px;" >TOTAL</th>
                            <th style="width:50%;text-align:center;padding:5px;font-size:13px;"> <span style="background:yellow;">' . ($row["gprMacho"] + $row["gprHembra"]) . '</span></th>
                        </tr>
                    </table>
                </div>
            </div>
            <br>
            <div style="width: 100%;border:1px solid #C0C0C0;border-radius: 3px;padding:5px 10px;">
                <font style="font-size:12px;">Recibe Conforme</font>
                <p style="font-size:12px;line-height: 200%;margin:0px;text-align:center;">
                    <br>
                    <br>
                    _______________________________ 
                    <br>
                    <b>' . $usuario[0] . '</b><br>
                    Recibidor y Custodio del ganado
                </p>  
            </div>
            <br>
            <div style="width: 100%;border:1px solid #C0C0C0;border-radius: 3px;padding:5px 10px;">
                <font style="font-size:12px;">Observaciones: </font>
                <p style="font-size:10px;line-height: 200%;margin:0px;text-align:justify;">
                    ' . str_replace("\n", "<br>", utf8_encode($row["guiObservacion"])) . '
                </p>  
            </div>';
        $usuario = get_data_user_2($dbConn, $_SESSION['MM_Username']);
        $header = '
        <div style="width: 100%;">
            <div style="float:left;width: 75%;">
                <img src="../recursos/rastro_logo_2.png" width="300px" >
                <img  src="../recursos/Mabio.png" width="55px">
            </div>
            <div style="float:left;width: 25%;text-align:right;font-size: 8px;padding-top:25px;">
                <b>Comprobante de Ingreso:</b> ' . utf8_encode($row["gprComprobante"]) . '
            </div>    
        </div>';
        $footer = '
        <div style="width: 100%;">
            <div style="float:left;width: 50%;font-size: 8px;">
                <b>Generado por:</b> ' . $usuario[0] . '
            </div>
            <div style="float:left;width: 50%;text-align:right;font-size: 8px;">
                <b>Fecha:</b> ' . date("Y-m-d H:i:s") . '
            </div>    
        </div>';
        $estilos = '
        div, table {
            font-family:"Calibri, sans-serif";
        }';
        $array = [15, 15, 40, 15, 10, 10]; // 20(Izquierdo),20(Derecho),45(Top Cuerpo),30(Button-Cuerpo),22(Top Cabecera),16(Bottom Footer)
        $html = FormatoDocumento('', $Datos, $estilos);
        $mpdf = new mPDF('utf-8', 'A4', '', '', $array[0], $array[1], $array[2], $array[3], $array[4], $array[5]); // 20(Izquierdo),20(Derecho),45(Top Cuerpo),30(Button-Cuerpo),22(Top Cabecera),16(Bottom Footer)
        $mpdf->SetHTMLHeader($header, '', true);
        $mpdf->SetHTMLFooter($footer, 'O', true);
        $mpdf->WriteHTML($html);
        if ($op == 14) $mpdf->Output(utf8_encode($row["gprComprobante"]) . '.pdf', 'D'); //DESCARGAR DIRECTAMENTE 
        else $mpdf->Output();
        exit;
    } else {
        echo "HOLA MUNDO";
    }
}

function get_data_user_2($dbConn, $Id)
{
    $consulta = "SELECT * FROM tbl_a_usuarios WHERE usuId = :cedula AND usuEstado = 1 AND usuEstado_pass = 0";
    $sql = $dbConn->prepare($consulta);
    $sql->bindValue(':cedula', $Id);
    $sql->execute();
    if ($row = $sql->fetch()) return [utf8_encode($row["usuNombre"]), utf8_encode($row["usuCargo"]), $row["usuCedula"]];
    else return false;
}
function get_servicios($dbConn, $Id, $cantidad)
{
    $resultado = '';
    $consulta = "SELECT *  FROM tbl_a_servicios WHERE espId = :id AND srnEstado = 0";
    $sql = $dbConn->prepare($consulta);
    $sql->bindValue(':id', $Id);
    $sql->execute();
    $total = 0;
    while ($row = $sql->fetch()) {
        $valor = round($row["srnPrecio"] * $cantidad, 2);
        $total += $valor;
        $resultado .= '
            <tr >
                <td style="width:50%;text-align:center;padding:5px;" >' . utf8_encode($row["srnDescripcion"]) . '</td>
                <td style="width:50%;text-align:center;padding:5px;font-size:13px;">' . $valor . '</td>
            </tr>';
    }
    return $resultado . '
    <tr >
        <th style="width:50%;text-align:center;padding:5px;font-size:18px;" >TOTAL</th>
        <th style="width:50%;text-align:center;padding:5px;font-size:20px;"> <span style="background:yellow;">' . round($total, 2)  . '</span></th>
    </tr>';
}
function FormatoDocumento($titulo, $body, $estilos)
{
    $resultado = '<!DOCTYPE html>
    <html lang="en" dir="ltr">
    <head>
        <meta charset="utf-8">
        <title>' . $titulo . '</title>
    </head>
        <style> ' . $estilos . '</style>
    <body>' . $body . '</body>
    </html>';
    return $resultado;
}

function Transformar_Fecha($fecha)
{
    $arrayDia = array(
        '1' => 'lunes',
        '2' => 'martes',
        '3' => 'miércoles',
        '4' => 'jueves',
        '5' => 'viernes',
        '6' => 'sábado',
        '7' => 'domingo'
    );
    $numDia = date("N", strtotime($fecha));
    $arrayMes = array(
        '1' => 'Enero',
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
        '12' => 'Diciembre',
    );
    $numMes = date("n", strtotime($fecha));
    return $arrayDia[$numDia] . ", " . date("d", strtotime($fecha)) . " de " . $arrayMes[$numMes] . " de " . date("Y", strtotime($fecha));
}
