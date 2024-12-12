<?php

exit;
// require 'utils.php';
// $dbConn = conectar($db);
// echo f_send_mail($dbConn, 34799);
// function f_send_mail($dbConn, $Id)
// {
//     $observaciones = '';
//     $consulta = "SELECT * FROM tbl_r_guiamovilizacion m, tbl_r_guiaproceso p, tbl_a_clientes c, tbl_a_especies e WHERE p.guiId = m.guiId AND p.cliId = c.cliId AND p.espId = e.espId AND p.gprId =:id";
//     $sql = $dbConn->prepare($consulta);
//     $sql->bindValue(':id', $Id);
//     $sql->execute();
//     if ($row = $sql->fetch()) {
//         include 'send.php';
//         $para = [["jairo.castillo@epmrq.gob.ec", "Jairo Castillo"], ["flavio.valencia@epmrq.gob.ec", "Flavio Valencia"]];
//         $copia = [];

//         $existentes_turno =  f_calcular_existentes_and_turno($dbConn, $row["espId"], $row["gprId"]);
//         $datos1 = [$existentes_turno[1], $existentes_turno[0], utf8_encode($row["espDescripcion"]), utf8_encode($row["cliNombres"])];
//         $fecha = explode(" ", $row["gprTurno"]);
//         $datos2 = [($row["gprMacho"] + $row["gprHembra"]), utf8_encode($row["gprComprobante"]), utf8_encode($row["guiNumero"]), $fecha[0], $fecha[1]];
//         $datos3 = [$row["gprMacho"], $row["gprHembra"]];
//         $datos4 = [utf8_encode($row["guiNombreConductor"]), utf8_encode($row["guiCiConductor"]), utf8_encode($row["guiVehiculoPlaca"])];
//         $codigo = generar_codigo($row["cliNumero"]);
//         $array_precios = get_precio_servicios($dbConn, $row["espId"]);
//         $total_precios = 0;
//         $precios = '';
//         for ($i = 0; $i < count($array_precios); $i++) {
//             $total_precios += $array_precios[$i][1];
//             $precios .= '<tr>
//                             <td style="text-aling:right;"> Precio por <b> ' . $array_precios[$i][0] . ':</b> </td>
//                             <td style="text-aling:center;"> ' . $array_precios[$i][1] . ' $</td>
//                         </tr>';
//         }
//         $precios .= '<tr style="">
//                             <td style="text-aling:right;border-top:1px solid #C0C0C0;">  <b>Precio Unitario:</b> </td>
//                             <td style="text-aling:center;border-top:1px solid #C0C0C0;"> ' . $total_precios . ' $</td>
//                         </tr>
//                         <tr>
//                             <td colspan="2" style="font-size:20px;"><b>Total a pagar</b><br> 
//                             <span style="background:yellow;font-size:30px;padding:10;border-radius:5px;">' . number_format(($total_precios * ($row["gprMacho"] + $row["gprHembra"])), 2, ',', ' ') . ' $</span></td>
//                         </tr>';
//         $bodyHTML = return_formato_notificacion_ingreso($datos1, $datos2, $datos3, $datos4, 'prueba', $codigo, $precios, $Id);

//         $asunto = "Notificación de Ingreso " . date("d/m/Y");
//         $titulo_envio = "RASTRO - Notificación de Recepción";
//         return metEnviar($para, $copia, $bodyHTML, $asunto, $titulo_envio);
//     } else return "Error 131321321";
// }

// function f_calcular_existentes_and_turno($dbConn, $especie, $id)
// {
//     $consulta = "SELECT p.gprId, g.guiNumero, p.gprComprobante ,c.cliNombres, e.espDescripcion,p.gprMacho,p.gprHembra, p.gprEstado, p.gprestadoDetalle 
//     FROM tbl_r_guiaproceso p, tbl_r_guiamovilizacion g, tbl_a_especies e, tbl_a_clientes c 
//     WHERE p.guiId = g.guiId AND p.espId = e.espId AND p.cliId = c.cliId AND e.espId = :id AND
//     g.guiEliminado = 0  AND p.gprEliminado = 0 
//     ORDER BY p.gprTurno , gprComprobante ASC ";
//     $sql = $dbConn->prepare($consulta);
//     $sql->bindValue(':id', $especie);
//     $sql->execute();
//     $cont = 1;
//     $cantidad = 0;
//     while ($row = $sql->fetch()) {
//         if (f_obtener_procesar($dbConn, $row["gprId"]) == 0) {
//             if ($row["gprEstado"] == 0 || $row["gprEstado"] == 1) {
//                 if ($row["gprId"] == $id) {
//                     return [$cont, $cantidad];
//                 } else {
//                     $cont++;
//                     $cantidad = $cantidad + (intval($row["gprHembra"]) + intval($row["gprMacho"]));
//                 }
//             }
//         }
//     }
// }

// function generar_codigo($cedula)
// {
//     $juliano = str_split(CalcularJuliano() . '');
//     $arr1 = str_split($cedula . '');
//     $total = 0;
//     foreach ($arr1 as $value) {
//         $total += intval($value);
//     }
//     foreach ($juliano as $value) {
//         $total += intval($value);
//     }
//     return $total;
// }

// function get_precio_servicios($dbConn, $Id)
// {
//     $datos = [];
//     $consulta = "SELECT srnDescripcion, srnPrecio FROM tbl_a_servicios s, tbl_a_especies e WHERE s.espId = e.espId AND e.espId = :id  AND srnEstado = 0";
//     $sql = $dbConn->prepare($consulta);
//     $sql->bindValue(':id', $Id);
//     $sql->execute();
//     while ($row = $sql->fetch()) array_push($datos, [utf8_encode($row["srnDescripcion"]), $row["srnPrecio"]]);
//     return $datos;
// }

// function return_formato_notificacion_ingreso($datos1, $datos2, $datos3, $datos4, $observacion, $codigo, $precios, $id)
// {
//     $salvoconducto = "";
//     $numero = date("N");
//     if ($numero  == 7 || $numero  == 2 || $numero  == 4) {
//         $salvoconducto  = return_salvo_Conducto($datos4[2], $datos4[0], $datos4[1], $datos2[3], $id);
//     }
//     return '
//     <center><img src="http://www.epmrq.gob.ec/images/logows2.fw.png" style="display: block;"></center>
//     <h3 style="text-align:center">EMPRESA PÚBLICA METROPOLITANA DE RASTRO QUITO</h3>
//     ' . $salvoconducto . '
//     <h3 style="text-align:center">NOTIFICACIÓN DE INGRESO DE GANADO</h3>
//     <div style="text-align:center">
//         <span style="font-size:15px;"><u>Existen <b>' . $datos1[0] . '</b> animales antes de empezar su turno</u></span><br><br>
//         <span style="background:orange;font-size:30px;padding:10;border-radius:5px;"><b>TURNO #' . $datos1[1] . '</b></span><br><br><br>
//         <span style="background:yellow;font-size:30px;padding:10;border-radius:5px;"><b>' . $datos1[2] . '</b></span><br><br>
//         <span style="font-size:20px;padding:10;border-radius:5px;"><b>Cliente: ' . $datos1[3] . '</b></span><br><br>
//     </div>
//     <br>
//     <br>
//     <div style="border:1px solid #C0C0C0;padding:5px 10px;border-radius: 3px;">
//         <table border="0" width="100%" style="border-collapse: collapse;">
//             <tr >
//                 <th>Cantidad:</th>
//                 <td style="font-size:20px;"><b>' . $datos2[0] . '</b></td>
//             </tr>
//             <tr >
//                 <th>Comprobante de Ingreso:</th>
//                 <td>' . $datos2[1] . '</td>
//             </tr>
//             <tr >
//                 <th>Nro. Guía de Movilización:</th>
//                 <td>' . $datos2[2] . '</td>
//             </tr>
//             <tr>
//                 <th>Fecha de Ingreso:</th>
//                 <td>' . $datos2[3] . '</td>
//             </tr>
//             <tr>
//                 <th>Hora de Ingreso:</th>
//                 <td>' . $datos2[4] . '</td>
//             </tr>
//         </table>
//     </div>
//     <br>
//     <div style="border:1px solid #C0C0C0;padding:5px 10px;border-radius: 3px;">
//         <table border="0" width="100%" style="border-collapse: collapse;text-align:center">
//             <tr>
//                 <td><b>Machos:</b> ' . $datos3[0] . '</td>
//                 <td><b>Hembras:</b> ' . $datos3[1] . '</td>
//             </tr>
//         </table>
//     </div>
//     <br>
//     <div style="border:1px solid #C0C0C0;padding:5px 10px;border-radius: 3px;">
//         <table border="0" width="100%" style="border-collapse: collapse;text-align:center">
//             ' . $precios . '
//         </table>
//         <b>Datos bancarios para el depósito o transferencia:</b><br>
//         RUC: 1768157280001 <br>
//         Cta Banco Pichincha: 3478747604<br>
//         Sublinea 30200
//     </div>
//     <br>
//     <div style="border:1px solid #C0C0C0;padding:5px 10px;border-radius: 3px;">
//         <table border="0" width="100%" style="border-collapse: collapse;text-align:center">
//             <tr>
//                 <td colspan="2"><b>Conductor:</b> ' . $datos4[0] . '</td>
//             </tr>
//             <tr>
//                 <td><b>CI:</b> ' . $datos4[1] . '</td>
//                 <td><b>Placa:</b> ' . $datos4[2] . '</td>
//             </tr>
//         </table>
//     </div>
//     <br>
//     <div style="font-size:12px;border:1px solid #C0C0C0;padding:5px 10px;border-radius: 3px;">
//         <b>Observaciónes: </b> ' . $observacion . '
//     </div>
//     <p style="font-size:10px;">
//         Código de seguridad: ' . $codigo . '
//     </p>';
// }

// function f_obtener_procesar($dbConn, $Id)
// {
//     $cont = 0;
//     // $consulta="SELECT * FROM tbl_r_detalle  
//     // WHERE dtEstado = 1  AND  ordId is not null AND gprId = :id";
//     $consulta = "SELECT * FROM tbl_p_orden WHERE gprId = :id AND ordTipo = 0";
//     $sql = $dbConn->prepare($consulta);
//     $sql->bindValue(':id', $Id);
//     $sql->execute();
//     while ($row = $sql->fetch()) {
//         $cont++;
//     }
//     return $cont;
// }
// function CalcularJuliano()
// {
//     $numMes = date("n");
//     $total = 0;
//     $anio = date("Y");
//     for ($i = 1; $i < $numMes; $i++) {
//         $fecha = date("t", strtotime($anio . "-$i"));
//         $total = $total + intval($fecha);
//     }
//     $total = $total + intval(date("d"));
//     return "$total" . date("y");
// }


// // -----------------------------------
// function return_salvo_Conducto($placa, $conductor, $cedula, $fecha, $id)
// {
//     $img = createqr($id);
//     return '
//     <div style="border:1px solid #C0C0C0;padding:5px 10px;border-radius: 3px;">
//         <table border="0" width="100%" style="border-collapse: collapse;">
//             <tr>
//                 <td style="width:25%">
//                 <img src="http://www.epmrq.gob.ec/generateqr/index.php?IMAGEN=' . $img . '" width="100%"></td>
//                 <td style="width:75%">
//                     <h4>Salvoconducto para circulación en ejercicio de actividades económicas que abastecen la cadena productiva de cárnicos</h4>
//                     Este documento únicamente permite la circulación de las personas identificadas, 
//                     en virtud del numeral 11 del artículo 7 del Decreto Ejecutivo N° 110 del 8 de enero 
//                     del 2024, bajo prevención de la sanción prevista en el artículo 282 del COIP, 
//                     sobre incumplimiento de decisiones legítimas de autoridad competente. 
//                 </td>
//             </tr>
//             <tr>
//                 <td colspan="2">
//                     <br>
//                     <b style="color:#167abf">Placa del vehículo:</b> <font style="color:#223f87">' . $placa . '</font><br>
//                     <b style="color:#167abf">Nombre completo del conductor:</b> <font style="color:#223f87">' . $conductor . '</font><br>
//                     <b style="color:#167abf">Cédula del conductor:</b> <font style="color:#223f87">' . $cedula . '</font><br>
//                     <b style="color:#167abf">Periodo de vigencia:</b> <font style="color:#223f87"> desde las 00h00 hasta las 05h00 del ' . $fecha . '</font><br>
//                     <b style="color:#167abf">Lugar de la actividad:</b> <font style="color:#223f87">Empresa Pública Metropolitana de Rastro Quito. </font><br>
//                 </td>
//             </tr> 
//         </table>
//     </div>
//     <br>';
// }

// function createqr($id)
// {
//     //Agregamos la libreria para genera códigos QR
//     require "phpqrcode/qrlib.php";

//     //Declaramos una carpeta temporal para guardar la imagenes generadas
//     $dir = 'temp/';

//     //Si no existe la carpeta la creamos
//     if (!file_exists($dir))
//         mkdir($dir);

//     //Declaramos la ruta y nombre del archivo a generar
//     $filename = $dir . 'qr-' . $id . '.png';

//     //Parametros de Condiguración

//     $tamaño = 10; //Tamaño de Pixel
//     $level = 'L'; //Precisión Baja
//     $framSize = 3; //Tamaño en blanco
//     $contenido = "http://www.epmrq.gob.ec/documento_acreditacion/index.php?Id=" . $id; //Texto

//     //Enviamos los parametros a la Función para generar código QR 
//     QRcode::png($contenido, $filename, $level, $tamaño, $framSize);

//     //Mostramos la imagen generada
//     return  basename($filename);
// }
