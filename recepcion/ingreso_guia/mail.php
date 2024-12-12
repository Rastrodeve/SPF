<?php
include './operaciones.php';
$dbConn = conectar($db);
// $data = f_calcular_existentes_and_turno($dbConn, 1, 35098);
// print_r($data);
// function return_turno($dbConn, $Id = 0)
// {

//     $fecha_actual = date("Y-m-d");
//     $nueva_fecha = date("Y-m-d", strtotime($fecha_actual . "+ 1 days"));
//     $contador = 0;
//     $consulta_id = '';
//     if ($Id != 0) {
//         $consulta_id = "AND gprId = :id";
//     }
//     $consulta = "SELECT numero FROM tbl_r_turno WHERE fecha = :fecha " . $consulta_id;
//     $sql = $dbConn->prepare($consulta);
//     $sql->bindValue(':fecha', $nueva_fecha);
//     if ($Id != 0) {
//         $sql->bindValue(':id', $Id);
//     }
//     $sql->execute();
//     while ($row = $sql->fetch()) {
//         $contador = $row["numero"];
//     }
//     return  $contador;
// }


// include './operaciones.php';
// $dbConn = conectar($db);
// f_send_mail_2($dbConn);
// function f_send_mail_2($dbConn)
// {
//     $Id = 34876;
//     $observaciones = '';
//     $consulta = "SELECT * FROM tbl_r_guiamovilizacion m, tbl_r_guiaproceso p, tbl_a_clientes c, tbl_a_especies e WHERE p.guiId = m.guiId AND p.cliId = c.cliId AND p.espId = e.espId AND p.gprId =:id";
//     $sql = $dbConn->prepare($consulta);
//     $sql->bindValue(':id', $Id);
//     $sql->execute();
//     if ($row = $sql->fetch()) {
//         include '../../FilePHP/send.php';
//         $para = [['jairo.castillo@epmrq.gob.ec', 'Jairo'], ["flavio.valencia@epmrq.gob.ec", "Flavio Valencia"]];
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
//         $bodyHTML = return_formato_notificacion_ingreso($datos1, $datos2, $datos3, $datos4, $observaciones, $codigo, $precios, $Id);

//         $asunto = "Notificación de Ingreso " . date("d/m/Y");
//         $titulo_envio = "RASTRO - Notificación de Recepción";
//         return metEnviar($para, $copia, $bodyHTML, $asunto, $titulo_envio);
//     } else return "Error 131321321";
// }
