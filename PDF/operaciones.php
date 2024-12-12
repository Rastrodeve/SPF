<?php
// OPCION 1 "REPORTE DE INGRESO DE ANIMAL"
function Reporte_Ingreso_animal($dbConn)
{
    $variable = $_SESSION['VARIABLE'];
    $F_final = $_SESSION['FINAL'];
    $F_inicio = $_SESSION['INICIO'];
    $Tipo_Ganado_th = '';
    if ($variable == 0) $Tipo_Ganado_th = '<th>Ganado</th>';
    $resultado = '<table class="tbl_detalle" border="1"  cellpadding="5">
    <thead>
        <tr>
            <th>Num</th>
            <th>Fecha de ingreso</th>
            <th>Nro. Guía de movilización</th>
            <th>Nro. Comprobante</th>
            <th>Cliente</th>
            <th>Marca</th>
            ' . $Tipo_Ganado_th . '
            <th>Corral</th>
            <th>Cantidad</th>
        </tr>
    </thead>
    <tbody>';
    $cont = 0;
    $Tipo = 'Todas las especies';
    if ($variable > 0) $Tipo = get_data_especie($dbConn, $variable);
    $total = 0;
    if ($variable > 0) {
        $consulta = "SELECT p.gprId, p.gprTurno, g.guiNumero, p.gprComprobante ,c.cliNombres, e.espDescripcion,(p.gprMacho + p.gprHembra) AS gprCantidad, p.gprEstado, p.gprestadoDetalle, c.cliMarca 
        FROM tbl_r_guiaproceso p, tbl_r_guiamovilizacion g, tbl_a_especies e, tbl_a_clientes c 
        WHERE p.guiId = g.guiId AND p.espId = e.espId AND p.cliId = c.cliId AND e.espId = :id AND
        g.guiEliminado = 0  AND p.gprEliminado = 0 AND p.gprHabilitado = 1 AND p.gprTurno BETWEEN :finicio AND :final
        ORDER BY p.gprTurno ASC";
    } else {
        $consulta = "SELECT p.gprId, p.gprTurno, g.guiNumero, p.gprComprobante ,c.cliNombres, e.espDescripcion,(p.gprMacho + p.gprHembra) AS gprCantidad, p.gprEstado, p.gprestadoDetalle, c.cliMarca 
        FROM tbl_r_guiaproceso p, tbl_r_guiamovilizacion g, tbl_a_especies e, tbl_a_clientes c 
        WHERE p.guiId = g.guiId AND p.espId = e.espId AND p.cliId = c.cliId  AND
        g.guiEliminado = 0  AND p.gprEliminado = 0 AND p.gprHabilitado = 1 AND p.gprTurno BETWEEN :finicio AND :final
        ORDER BY p.gprTurno ASC";
    }
    $sql = $dbConn->prepare($consulta);
    if ($variable > 0) $sql->bindValue(':id', $variable);
    $sql->bindValue(':finicio', $F_inicio . " 00:00:00");
    $sql->bindValue(':final', $F_final . " 23:59:59");
    $sql->execute();
    while ($row = $sql->fetch()) {
        // if (f_obtener_procesar($dbConn, $row["gprId"]) == 0) {
        // Estado Detalle = 0; No se detalla la guia => El Estado debe ser 1
        // Estado Detalle = 1; Se detalla la guia => El Estado debe ser 0
        // if ($row["gprEstado"] == 0 || $row["gprEstado"] == 1) {
        $total += $row["gprCantidad"];
        $cont++;
        $span = 'ERROR' . $row["gprEstado"] . '';
        if ($row["gprEstado"] == 0) {
            $span = '*' . $cont . '';
        } else if ($row["gprEstado"] == 1) {
            $span = '' . $cont . '';
        }
        $corral = f_obtener_corrales_guia($dbConn, $row["gprId"]);
        $td_ganado = '';
        if ($variable == 0) $td_ganado = '<td>' . utf8_encode($row["espDescripcion"]) . '</td>';
        $resultado .= '
                <tr>
                    <th  class="center">' . $span . '</th>
                    <td>' . $row["gprTurno"] . '</td>
                    <td>' . utf8_encode($row["guiNumero"]) . '</td>
                    <td>' . utf8_encode($row["gprComprobante"]) . '</td>
                    <td>' . utf8_encode($row["cliNombres"]) . '</td>
                    <td class="center">' . utf8_encode($row["cliMarca"]) . '</td>
                    ' . $td_ganado . '
                    <td >' . $corral . '</td>
                    <th class="center">' . $row["gprCantidad"] . '</th>
                </tr>';
        // }
        // }
    }
    $table = $resultado . "</tbody></table>";
    $Fecha = "";
    $Numero = "RECEPCIÓN DE GANADO";
    if ($F_final == $F_inicio) {
        $Fecha = '<p style="font-size:15px "><b>INGRESO DE GANADO DEL DIA: </b> ' . Transformar_Fecha($F_inicio) . '</p>';
        if ($variable > 0) $Numero = 'RECEPCIÓN-EMRAQ' . '-' . utf8_encode(get_data_especie_letra($dbConn, $variable)) . '-' . CalcularJuliano($F_inicio);
    } else {
        $Fecha = '<p style="font-size:15px "><b>INGRESO DE GANADO DESDE EL DIA </b> ' . Transformar_Fecha($F_inicio) . ', HASTA EL DIA ' . Transformar_Fecha($F_final) . '</p>';
    }
    $cabecera = '
    <p style="font-size:22px "><b>EMPRESA PÚBLICA METROPOLITANA DE RASTRO QUITO </b></p>
    <p style="font-size:17px "><b>DIRECCIÓN DE PRODUCCIÓN Y FAENAMIENTO</b></p>
    <p style="font-size:15px "><b>TIPO DE GANADO: </b> ' . strtoupper(utf8_encode($Tipo)) . '</p>
    ' . $Fecha . '
    <p style="font-size:15px;text-align:center;margin-top:10px;"><b>' . $Numero . '</b></p><br>';
    $estilos = '
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
            font-size: 11px;
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
            font-size: 12px;
            text-align: center;
        }
        .tbl_total th{
            background: #b3b6b7;
        }';
    $firmas = ConsultarFirmas($dbConn, 1);
    $footer = '<table class="tbl_total" border="1" cellpadding="7">
    <tr><th >TOTAL INGRESADO</th></tr>
    <tr><td>' . $total . '</td></tr>
    </table>' . $firmas;
    $Res = FormatoDocumento('INGRESO DE GANADO', $table, $cabecera, $estilos, $footer);
    return $Res;
}
// OPCION 2 "ORDEN DE PRODUCCION"
function f_orden_produccion($dbConn, $orden = null, $especie = null, $date = null)
{
    $variable = $_SESSION['VARIABLE'];
    $variable2 = $_SESSION['VARIABLE2'];
    $F_final = $_SESSION['FINAL'];
    $F_inicio = $_SESSION['INICIO'];
    // $variable = $orden;
    // $variable2 = $especie;
    // $F_final = $date;
    // $F_inicio = $date;
    $resultado = '<table class="tbl_detalle" border="1"  cellpadding="5">
    <thead>
        <tr>
            <th>Num</th>
            <th>Cliente</th>
            <th>Marca</th>
            <th>Factura</th>
            <th>Corral - Marca</th>
            <th>Cant. Individual</th>
            <th>Cant. Total</th>
        </tr>
    </thead>
    <tbody>';
    $cont = 0;
    $Tipo = get_data_especie($dbConn, $variable2);
    $total = 0;
    $consulta = "SELECT o.ordId,p.gprId,c.cliNombres,c.cliMarca,o.ordCantidad, o.ordProcesado, o.Num_Documento,p.gprestadoDetalle, p.gprMacho, p.gprHembra ,(p.gprMacho + p.gprHembra) AS gprCantidad  
    FROM tbl_p_orden o, tbl_r_guiaproceso p, tbl_a_clientes c 
    WHERE o.gprId = p.gprId AND p.cliId = c.cliId AND o.gprId is not null AND 
    o.ordNumOrden = :orden AND o.espId = :especie AND o.ordEliminado = 0
    ORDER BY o.ordId ASC";
    $sql = $dbConn->prepare($consulta);
    $sql->bindValue(':orden', $variable);
    $sql->bindValue(':especie', $variable2);
    $sql->execute();
    while ($row = $sql->fetch()) {
        $cant = f_obtener_cantidades($dbConn, $row["gprId"]);
        // Estado Detalle = 0; No se detalla la guia => El Estado debe ser 1
        // Estado Detalle = 1; Se detalla la guia => El Estado debe ser 0
        $span = 'ERROR' . $row["gprestadoDetalle"] . '';
        if ($row["gprestadoDetalle"] == 0 || $row["gprestadoDetalle"] == 1) {
            $total += $row["ordCantidad"];
            $cont++;
            if ($row["gprestadoDetalle"] == 1) {
                $span = '*' . $cont . '';
            } else if ($row["gprestadoDetalle"] == 0) {
                $span = '' . $cont . '';
            }
        }
        // <td class="center">'.$row["ordProcesado"].'</td>
        //  <td class="center">'.$row["ordCantidad"].'</td>
        // <td class="center">'.$cant[0].'</td>
        $corral = f_obtener_corrales_guia($dbConn, $row["gprId"]);
        $resultado .= '
            <tr>
                <th  class="center">' . $span . '</th>
                <td>' . utf8_encode($row["cliNombres"]) . '</td>
                <td class="center">' . $row["cliMarca"] . '</td>
                <td>' . $row["Num_Documento"] . '</td>
                <td class="center">' . $corral . '</td>
                <td class="center">(M: ' . $row["gprMacho"] . ') (H: ' . $row["gprHembra"] . ')</td>
                <td class="center">' . $row["ordCantidad"] . '</td>
            </tr>';
    }
    $table = $resultado . "</tbody></table>";
    $Fecha = "";
    if ($F_final == $F_inicio) {
        $Fecha = '<p style="font-size:15px "><b>TURNOS PARA FAENAMIENTO DEL: </b> ' . Transformar_Fecha($F_inicio) . '</p>';
    } else {
        $Fecha = '<p style="font-size:15px "><b>TURNOS PARA FAENAMIENTO DE </b> ' . Transformar_Fecha($F_inicio) . ', HASTA EL DIA ' . Transformar_Fecha($F_final) . '</p>';
    }
    $cabecera = '
    <p style="font-size:22px "><b>EMPRESA PÚBLICA METROPOLITANA DE RASTRO QUITO</b></p>
    <p style="font-size:17px "><b>DIRECCIÓN DE PRODUCCIÓN Y FAENAMIENTO</b></p>
    ' . $Fecha . '
    <p style="font-size:15px "><b>TIPO DE GANADO: </b> ' . strtoupper(utf8_encode($Tipo)) . '</p>
    <p style="font-size:15px;Text-align:center;"><b>ORDEN DE PRODUCCIÓN</b><br>' . $variable . '</p><br>';
    $estilos = '
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
            font-size: 11px;
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
            font-size: 12px;
            text-align: center;
        }
        .tbl_total th{
            background: #b3b6b7;
        }';
    $firmas = ConsultarFirmas($dbConn, 2);
    $footer = '<table class="tbl_total" border="1" cellpadding="7">
    <tr><th >TOTAL A PROCESAR</th></tr>
    <tr><td>' . $total . '</td></tr>
    </table>' . $firmas;
    $Res = FormatoDocumento('ORDEN DE PRODUCCION ' . strtoupper(utf8_encode($Tipo)), $table, $cabecera, $estilos, $footer);
    return $Res;
}
// OPRCION 3 "ORDEN DE PRODUCION EMERGENTE"
function f_orden_produccion_emergente($dbConn)
{
    $variable = $_SESSION['VARIABLE'];
    $variable2 = $_SESSION['VARIABLE2'];
    $F_final = $_SESSION['FINAL'];
    $F_inicio = $_SESSION['INICIO'];
    $resultado = '<table class="tbl_detalle" border="1"  cellpadding="5">
    <thead>
        <tr>
            <th>Num</th>
            <th>Cliente</th>
            <th>Factura</th>
            <th>Cantidad</th>
            <th>Procesado</th>
            <th>Saldo</th>
        </tr>
    </thead>
    <tbody>';
    $cont = 0;
    $Tipo = get_data_especie($dbConn, $variable2);
    $total = 0;
    $consulta = "SELECT o.ordId,p.gprId,c.cliNombres,o.ordCantidad, o.ordProcesado, o.Num_Documento,p.gprestadoDetalle, (p.gprMacho + p.gprHembra) AS gprCantidad
    FROM tbl_p_orden o, tbl_r_guiaproceso p, tbl_a_clientes c 
    WHERE o.gprId = p.gprId AND p.cliId = c.cliId AND o.gprId is not null AND 
    o.espId = :especie AND o.ordNumOrden = :orden  ORDER BY o.ordId ASC";
    $sql = $dbConn->prepare($consulta);
    $sql->bindValue(':orden', trim($variable));
    $sql->bindValue(':especie', $variable2);
    $sql->execute();
    while ($row = $sql->fetch()) {
        $cant = f_obtener_cantidades($dbConn, $row["gprId"]);
        // Estado Detalle = 0; No se detalla la guia => El Estado debe ser 1
        // Estado Detalle = 1; Se detalla la guia => El Estado debe ser 0
        $span = 'ERROR' . $row["gprestadoDetalle"] . '';
        if ($row["gprestadoDetalle"] == 0 || $row["gprestadoDetalle"] == 1) {
            $total += $row["ordCantidad"];
            $cont++;
            if ($row["gprestadoDetalle"] == 1) {
                $span = '*' . $cont . '';
            } else if ($row["gprestadoDetalle"] == 0) {
                $span = '' . $cont . '';
            }
        }
        $resultado .= '
            <tr>
                <th  class="center">' . $span . '</th>
                <td>' . utf8_encode($row["cliNombres"]) . '</td>
                <td>' . $row["Num_Documento"] . '</td>
                <td class="center">' . ($cant[1] + $cant[2]) . '</td>
                <td class="center">' . $row["ordProcesado"] . '</td>
                <td class="center">' . $row["ordCantidad"] . '</td>
            </tr>';
    }
    $table = $resultado . "</tbody></table>";
    $Fecha = "";
    if ($F_final == $F_inicio) {
        $Fecha = '<p style="font-size:15px "><b>TURNOS PARA FAENAMIENTO EMERGENTE DEL </b> ' . Transformar_Fecha($F_inicio) . '</p>';
    } else {
        $Fecha = '<p style="font-size:15px "><b>TURNOS PARA FAENAMIENTO EMERGENTE DE </b> ' . Transformar_Fecha($F_inicio) . ', HASTA EL DIA ' . Transformar_Fecha($F_final) . '</p>';
    }
    $cabecera = ' 
    <p style="font-size:22px "><b>EMPRESA PÚBLICA METROPOLITANA DE RASTRO QUITO</b></p>
    <p style="font-size:17px "><b>DIRECCIÓN DE PRODUCCIÓN Y FAENAMIENTO</b></p>
    ' . $Fecha . '
    <p style="font-size:15px "><b>TIPO DE GANADO </b> ' . strtoupper(utf8_encode($Tipo)) . '</p>
    <p style="font-size:15px;Text-align:center;"><b>ORDEN DE PRODUCCIÓN EMERGENTE</b><br>' . $variable . '</p><br>';
    $estilos = '
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
            font-size: 11px;
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
            font-size: 12px;
            text-align: center;
        }
        .tbl_total th{
            background: #b3b6b7;
        }';
    $firmas = ConsultarFirmas($dbConn, 3);
    $array = get_data_user($dbConn);
    $footer = '<table class="tbl_total" border="1" cellpadding="7">
    <tr><th >TOTAL A PROCESAR</th></tr>
    <tr><td>' . $total . '</td></tr>
    </table>' . $firmas;
    $Res = FormatoDocumento('ORDEN DE PRODUCCION EMERGENTE' . strtoupper(utf8_encode($Tipo)), $table, $cabecera, $estilos, $footer);
    return $Res;
}
// OPRCION 4 "ACTA DE DECOMISO"
function f_acta_decomiso($dbConn)
{
    $variable = $_SESSION['VARIABLE'];
    $productos = '<table class="tbl_detalle" border="1"  cellpadding="5">
    <thead>
        <tr>
            <th>Especie</th>
            <th>Producto</th>
            <th>Cantidad</th>
            <th>Observaciones</th>
            <th>Destino</th>
        </tr>
    </thead>
    <tbody>';
    $subproductos = '<table class="tbl_detalle" border="1"  cellpadding="5">
    <thead>
        <tr>
            <th>Especie</th>
            <th>Subproducto</th>
            <th>Cantidad</th>
            <th>Observaciones</th>
            <th>Destino</th>
        </tr>
    </thead>
    <tbody>';
    $consulta = "SELECT DISTINCT proId FROM tbl_p_decomiso WHERE proId IS NOT NULL AND actId = :id";
    $sql = $dbConn->prepare($consulta);
    $sql->bindValue(':id', $variable);
    $sql->execute();
    while ($row = $sql->fetch()) {
        $especie = f_obtener_nombre_especie_producto($dbConn, $row["proId"]);
        $arrayp = f_obtener_decomisos_por_producto($dbConn, $row["proId"], $variable);
        $productos .= '
        <tr>
            <td style="width:100px;">' . $especie . '</td>
            <td style="width:150px;">' . $arrayp[1] . '</td>
            <td style="Text-align:center;">' . $arrayp[0] . '</td>
            <td>' . $arrayp[2] . '</td>
            <td style="width:100px;"></td>
        </tr>';
    }
    $productos = $productos . "</tbody></table>";


    $consulta = "SELECT DISTINCT d.subId FROM tbl_p_decomiso_detalle d, tbl_p_decomiso de 
    WHERE d.decId = de.decId AND de.actId = :id";
    $sql = $dbConn->prepare($consulta);
    $sql->bindValue(':id', $variable);
    $sql->execute();
    while ($row = $sql->fetch()) {
        $especie = f_obtener_nombre_especie_subproducto($dbConn, $row["subId"]);
        $arrayp = f_obtener_decomisos_por_subproducto($dbConn, $row["subId"], $variable);
        $enfermedades = f_obtener_enfermedades_por_subproducto($dbConn, $row["subId"], $variable);
        $subproductos .= '
        <tr>
            <td style="width:100px;">' . $especie . '</td>
            <td style="width:150px;">' . $arrayp[1] . '</td>
            <td style="Text-align:center;">' . $arrayp[0] . '</td>
            <td>' . $enfermedades . '</td>
            <td style="width:100px;"></td>
        </tr>';
    }
    $subproductos = $subproductos . "</tbody></table>";

    $cero = "";
    if (strlen($variable) < 3) {
        for ($i = 0; $i < (3 - strlen($variable)); $i++) {
            $cero .= "0";
        }
    }
    $body = '
    <p style="font-size:10px;">
    Lorem Ipsum is simply dummy text of the printing and typesetting industry. 
    Lorem Ipsum has been the industrys standard dummy text ever since the 1500s,
    when an unknown printer took a galley of type and scrambled it to make a type specimen book. 
    It has survived not only five centuries, but also the leap into electronic typesetting, 
    remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets 
    containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus 
    PageMaker including versions of Lorem Ipsum.
    <br>
    <input type="checkbox" checked="True"  ><label>Seleccionar</label>
    </p>' .
        '<br><p style="font-size:12px;Text-align:left;"><b>Detalle de los productos decomisados: </b></p>' .
        $productos .
        '<br><p style="font-size:12px;Text-align:left;"><b>Detalle de los subproductos decomisados: </b></p>' . $subproductos;

    $cabecera = '
    <p style="font-size:16px;Text-align:center;"><b>ACTA DE DECOMISO DE PRODUCTOS Y <br>SUBPRODUCTOS CÁRNICOS NO PROCESADOS</b></p>
    <p style="font-size:11px;Text-align:right;"><b>Acta Nro. </b><span>' . $cero . $variable . '</span></p><br>';
    $estilos = '
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
            font-size: 11px;
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
            font-size: 12px;
            text-align: center;
        }
        .tbl_total th{
            background: #b3b6b7;
        }';
    $firmas = ConsultarFirmas($dbConn, 4);
    $array = get_data_user($dbConn);
    $footer = $firmas;
    $Res = FormatoDocumento('Acta Nro. ' . $cero . $variable, $body, $cabecera, $estilos, $footer);
    return $Res;
}
// OPRCION 5 "GUIA DE ORIGEN"
function f_guia_origen($dbConn, $variable, $op)
{
    $consulta = "SELECT * FROM tbl_d_origen WHERE orgId = :id";
    if ($op == 1) $consulta = "SELECT * FROM tbl_d_origen WHERE MD5(orgId)= :id";
    $sql = $dbConn->prepare($consulta);
    $sql->bindValue(':id', $variable);
    $sql->execute();
    $Origen = '';
    if ($row = $sql->fetch()) {
        $dia = 1;
        $fechaareay = explode(" ", $row["orgFecha"]);
        $vigencia = date("Y-m-d", strtotime(date($fechaareay[0]) . "+ " . $dia . " days"));
        $datosarray =  get_data_user_2($dbConn, $row["usuId"]);
        $url = create_Qr($datosarray[0], $fechaareay[0], $fechaareay[1], $row["orgId"]);
        $arraya2 = explode(":", $fechaareay[1]);
        $hora = $arraya2[0] . ':' . $arraya2[1];
        $codigo_destino = $row["orgCodigoProvinciaDestino"];
        if (strlen($row["orgCodigoProvinciaDestino"]) == 1) $codigo_destino = "0" . $row["orgCodigoProvinciaDestino"];
        $producot_movilizar = '';
        if (is_null($row["orgTipoProducto"])) $producot_movilizar = '';
        else if ($row["orgTipoProducto"] == 0) $producot_movilizar = 'Canales sin restricción de uso';
        else if ($row["orgTipoProducto"] == 1) $producot_movilizar = 'Canales para uso industrial';
        $Origen = '
        <p style="font-size:13px;Text-align:center;margin-bottom:20px;" >N° CERTIFICADO: ' . $codigo_destino . '-' . $row["orgDemCodigoCentro"] . '-IND-NAC</p>
        <table>
            <thead>
                <tr>
                    <th colspan ="4" style="text-align: left;font-size:15px;">1. DATOS GENERALES</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <th style="font-size:11px;">Lugar de emisión:</th>
                    <td style="font-size:11px;">' . utf8_encode($row["orgDemProvincia"]) . '</td>
                    <th></th>
                    <td></td>
                </tr>
                <tr>
                    <th style="font-size:11px;" >Fecha de emisón:</th>
                    <td style="font-size:11px;">' . Transformar_Fecha($fechaareay[0]) . ' ' . $hora . '</td>
                    <th style="font-size:11px;">Fecha inicio de vigencia:</th>
                    <td style="font-size:11px;">' . Transformar_Fecha($fechaareay[0]) . ' ' . $hora . '</td>
                </tr>
                <tr>
                    <th colspan ="4" style="font-size:15px;">Fecha Fin Vigencia: ' . Transformar_Fecha($vigencia) . ' ' . $hora . ' </th>
                </tr>
            </tbody>
        </table>
        <table border="1">
            <thead>
                <tr>
                    <th  style="text-align: left;font-size:15px;">2. DATOS DE ORIGEN</th>
                    <th  style="text-align: left;font-size:15px;">3. DATOS DE DESTINO</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="">
                        <table class="boder-cero" width = "100%"  style="margin-top:0px;padding:0px;" > 
                            <tbody>
                                <tr >
                                    <th colspan ="2" style="padding:3px;">Identificación del Centro de Faenamiento (RUC): </th>
                                    <td style="padding:3px;"> ' . $row["orgDemRuc"] . '</td>
                                </tr>
                                <tr >
                                    <td colspan ="3" style="margin-top:0px;padding:3px;"> 
                                        <b>Razón social del Centro de faenamiento:</b> <br>
                                        ' . utf8_encode($row["orgDemRazonSocial"]) . '
                                    </td>
                                </tr>
                                <tr >
                                    <th style="padding:3px;">Nombre del Sitio: </th>
                                    <td style="padding:3px;"> ' . utf8_encode($row["orgDemSitio"]) . '</td>
                                </tr>
                                <tr >
                                    <td style="padding:3px;" >
                                        <b >Provincia:</b> 
                                        ' . utf8_encode($row["orgDemProvincia"]) . '
                                    </td>
                                    <td style="padding:3px;" >
                                        <b >Canton:</b> 
                                        ' . utf8_encode($row["orgDemCanton"]) . '
                                    </td>
                                    <td style="padding:3px;" >
                                        <b >Parroquia:</b> 
                                        ' . utf8_encode($row["orgDemParroquia"]) . '
                                    </td>
                                </tr>
                                <tr >
                                    <td colspan ="3" style="padding:3px;" >
                                        <b>Dirección:</b> 
                                        ' . utf8_encode($row["orgDemDireccion"]) . '
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </td>
                    <td style="border:1px solid black;width:50%;min-width:50%;max-width:50%;">
                        <table class="boder-cero" width = "100%"   style="margin-top:0px;padding:0px;" > 
                            <tbody>
                                <tr >
                                    <th colspan ="2" style="padding:3px;">Identificación del destinatario: </th>
                                    <td style="padding:3px;"> ' . $row["orgCliNumero"] . '</td>
                                </tr>
                                <tr >
                                    <td colspan ="3" style="margin-top:0px;padding:3px;"> 
                                        <b>Razón social del destinatario:</b> <br>
                                        ' . utf8_encode($row["orgCliNombres"]) . '
                                    </td>
                                </tr>
                                <tr >
                                    <td style="padding:3px;" >
                                        <b >Provincia:</b> 
                                        ' . utf8_encode($row["orgProvinciaDestino"]) . '
                                    </td>
                                    <td style="padding:3px;" >
                                        <b >Canton:</b> 
                                        ' . utf8_encode($row["orgCantonDestino"]) . '
                                    </td>
                                    <td style="padding:3px;" >
                                        <b >Parroquia:</b> 
                                        ' . utf8_encode($row["orgParroquiaDestino"]) . '
                                    </td>
                                </tr>
                                <tr >
                                    <td colspan ="3" style="padding:3px;" >
                                        <b>Dirección:</b> 
                                        ' . utf8_encode($row["orgDireccionDestino"]) . '
                                    </td>
                                </tr>
                                <tr >
                                    <td colspan ="3" style="padding:3px;" >
                                        <b>Tipo producto a movilizar: </b>' . $producot_movilizar . ' 
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
            </tbody>
            
        </table>
        <table>
            <thead>
                <tr>
                    <th colspan ="4" style="text-align: left;font-size:15px;">4. DATOS DE MOVILIZACIÓN</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <th>Identificación conductor:</th>
                    <td>' . utf8_encode($row["orgTrnRuc"]) . '</td>
                    <th>Placa de Transporte:</th>
                    <td>' . utf8_encode($row["orgVhcPlaca"]) . '</td>
                </tr>
                <tr>
                    <th >Nombre del condcutor:</th>
                    <td>' . utf8_encode($row["orgTrnRazonSocial"]) . '</td>
                    <th></th>
                    <td></td>
                </tr>
            </tbody>
        </table>
        ' . data_prodcutos_origen($dbConn, $row["orgId"]) . data_subprodcutos_origen($dbConn, $row["orgId"]) . '
        <table >
            <thead>
                <tr>
                    <th colspan ="3"  style="text-align: left;font-size:15px;">7. FIRMAS Y SELLOS DE RESPONSABILIDAD</th>
                </tr>
            </thead>
            <tbody>
                <tr >
                    <td rowspan="2" style="width:22%;text-align:center;border:1px solid black;">
                        <img src="' . $url . '" height="2.5cm" />
                    </td>
                    <td style="text-align:center;border:1px solid black;"><br></td>
                    <td rowspan="2" style="width:22%;text-align:center;border:1px solid black;" >
                        <img src="../recursos/certificado_agrocalidad.png" height="2.5cm" />
                    </td>
                </tr>
                <tr >
                    <td style="text-align:center;border:1px solid black;font-size:15px;">
                        <p style="font-size:13px;">
                            <b>Responsable de emisión</b>
                        </p>
                        <hr style="margin:2px;color:white;">
                        <p >
                            ' . $datosarray[0] . '
                        </p>
                        <hr style="margin:2px;color:white;">
                        <p style="">
                            Identificación: ' . $datosarray[2] . '
                        </p>
                    </td>
                </tr>
            </tbody>
        </table>';
    }
    $cabecera = '<p style="font-size:16px;Text-align:center;margin-bottom:10px;" ><b>CERTIFICACIÓN SANITARIA DE ORIGEN Y MOVILIZACIÓN DE PRODUCTOS Y SUBPRODUCTOS<br>CÁRNICOS EN ESTADO PRIMARIO </b></p>' . $Origen;
    $body = '';
    $estilos = '
    p{
        margin-bottom:0px;
        margin-top:0px;
        color:black;
        font-family: Arial, Helvetica, sans-serif;
    }
    .boder-cero{
        border:0px;
    }
    a{
        color:black;
        text-align: center;
    }
    table{
        width:100%;
        border:1px solid black;
        border-collapse: collapse;
        margin-top:10px;
        font-family: Arial, Helvetica, sans-serif;
    }
    thead tr th,thead tr td{
        background:#C2C2C2;
        color:black;
        padding:5px;
        border:1px solid black;
    }

    tbody tr td, tbody tr th{
        text-align: left;
        font-size:9px;
        padding:5px;
    }
    #table_detalle{
        width:100%;
        border:0px solid black;
    }
    #table_detalle thead tr th,#table_detalle thead tr td{
        background:white;
        border:0px solid black;
        font-size:10px;
    }
    #table_detalle tbody tr th,#table_detalle tbody tr td{
        text-align: center;
        border:1px solid  #d7dbdd;
    }
    ';
    return FormatoDocumento('Guía de Origen.', $body, $cabecera, $estilos, '');
}
function data_prodcutos_origen($dbConn, $Id)
{
    $productos = '<table id="table_detalle">
                <thead>
                        <thead>
                            <tr>
                                <th>Fecha Faenamiento</th>
                                <th>Especie</th>
                                <th>Código Canal</th>
                                <th>Tipo movilización canal</th>
                            </tr>
                        </thead>
                </thead>
                <tbody>';
    $consulta = "SELECT o.ordFechaTurno, e.espDescripcion, p.pesCanal,p.pesPeso,p.pesProPartes 
    FROM tbl_d_pesaje p, tbl_p_orden o, tbl_a_especies e 
    WHERE p.ordId = o.ordId AND o.espId = e.espId AND p.orgId = :id ORDER BY p.pesFecha ASC ";
    $sql = $dbConn->prepare($consulta);
    $sql->bindValue(':id', $Id);
    $sql->execute();
    $cont = 0;
    while ($row = $sql->fetch()) {
        $cont++;
        $array = explode(' ', $row["ordFechaTurno"]);
        $tipo = "";
        if ($row["pesProPartes"] == 1) $tipo = "Entero";
        else if ($row["pesProPartes"] == 2) $tipo = "Medio";
        else  $tipo = "1 / " . $row["pesProPartes"];
        $array2 = explode("-", $row["pesCanal"]);
        $fin = 0;
        if (count($array2) == 2) $fin = 5;
        else $fin = 3;
        $cero = '';
        for ($i = strlen($row["pesCanal"]); $i < $fin; $i++) {
            $cero .= '0';
        }
        $productos .= '
        <tr>
            <td style="text-align: center;">' . $array[0] . '</td>
            <td style="text-align: center;">' . utf8_encode($row["espDescripcion"]) . '</td>
            <td style="text-align: center;">' . $cero . $row["pesCanal"] . '</td>
            <td style="text-align: center;">' . $tipo . '</td>
        </tr>';
    }
    $productos .= '</tbody></table>';
    if ($cont == 0) $productos = '<br><br>';
    return '
    <table>
            <thead>
                <tr>
                    <th  style="text-align: left;font-size:15px;border-right:0px;">5. DETALLE DEL PRODUCTO A MOVILIZAR </th>
                    <td  style="text-align: right;font-size:10px;border-left:0px;">TOTAL PRODUCTOS: ' . $cont . '</td>
                </tr>
            </thead>
    </table>' . $productos;
}
function data_subprodcutos_origen($dbConn, $Id)
{
    $subproductos = '<table id="table_detalle">
                <thead>
                        <thead>
                            <tr>
                                <th>Fecha Faenamiento</th>
                                <th>Especie</th>
                                <th>Subproducto</th>
                                <th>Lote a movilizar</th>
                                <th>cantidad</th>
                            </tr>
                        </thead>
                </thead>
                <tbody>';
    $consulta = "SELECT o.orgFecha,e.espDescripcion ,vo.vsrSubDescripcion,vo.vsrLote,vo.vsrCantidad 
    FROM tbl_d_visceras_origen vo, tbl_r_visceras v, tbl_d_origen o, tbl_a_subproductos s, tbl_a_especies e 
    WHERE vo.vscId = v.vscId AND vo.orgId = o.orgId AND v.subId = s.subId AND s.espId = e.espId AND vo.orgId = :id ";
    $sql = $dbConn->prepare($consulta);
    $sql->bindValue(':id', $Id);
    $sql->execute();
    $cont = 0;
    while ($row = $sql->fetch()) {
        $cont += $row["vsrCantidad"];
        $array = explode(' ', $row["orgFecha"]);
        $cero = '';
        for ($i = strlen($row["vsrLote"]); $i < 3; $i++) {
            $cero .= '0';
        }
        $subproductos .= '
        <tr>
            <td style="text-align: center;">' . $array[0] . '</td>
            <td style="text-align: center;">' . utf8_encode($row["espDescripcion"]) . '</td>
            <td style="text-align: center;">' . utf8_encode($row["vsrSubDescripcion"]) . '</td>
            <td style="text-align: center;">' . $cero . $row["vsrLote"] . '</td>
            <td style="text-align: center;">' . $row["vsrCantidad"] . '</td>
        </tr>';
    }
    $subproductos .= '</tbody></table>';
    if ($cont == 0) $subproductos = '<br><br><br>';
    return '
    <table>
            <thead>
                <tr>
                <th  style="text-align: left;font-size:15px;border-right:0px;">6. DETALLE DEL SUBPRODUCTO A MOVILIZAR </th>
                <td  style="text-align: right;font-size:10px;border-left:0px;">TOTAL SUBPRODUCTOS: ' . $cont . '</td>
                </tr>
            </thead>
    </table>' . $subproductos;
}
function create_Qr($veterinario, $fecha, $hora, $Id)
{
    //Agregamos la libreria para genera códigos QR
    require "phpqrcode/qrlib.php";
    //Declaramos una carpeta temporal para guardar la imagenes generadas
    $dir = 'temp/';
    //Si no existe la carpeta la creamos
    if (!file_exists($dir))
        mkdir($dir);
    //Declaramos la ruta y nombre del archivo a generar
    $filename = $dir . 'test.png';
    //Parametros de Condiguración
    $tamaño = 10; //Tamaño de Pixel
    // L = Baja
    // M = Mediana
    // Q = Alta
    // H= Máxima
    $level = 'H'; //
    $framSize = 3; //Tamaño en blanco
    $contenido = "FIRMADO POR: " . $veterinario . " \n" .
        "RAZON: CERTIFICACION SANITARIA DE ORIGEN Y MOVILIZACION DE PRODUCTOS Y SUBPRODUCTOS CARNICOS EN ESTADO PRIMARIO \n" .
        "LOCALIZACION: CAMAL METROPOLITANO \n" .
        "FECHA: " . $fecha . "T" . $hora . "\n" .
        "VALIDAR CON: www.firmadigital.gob.ec\n" .
        "2.8.0"; //Texto
    $varible1 = 'EPMRQ'; //PARA EL SISTEMA DE PESAJE
    // $Varible4 = 'ID';
    $contenido = 'http://epmrq.gob.ec/certificado_origen/828e0013b8f3bc1bb22b4f57172b019d.php?EMRAQ-EP-ORIGEN=' . md5($Id);
    //Enviamos los parametros a la Función para generar código QR 
    QRcode::png($contenido, $filename, $level, $tamaño, $framSize);
    //Mostramos la imagen generada
    return $dir . basename($filename);
}
//OPCION 6 "REPORTE DE SALDOS"
function Reporte_saldos($dbConn)
{
    $variable = $_SESSION['VARIABLE'];
    $F_final = $_SESSION['FINAL'];
    $F_inicio = $_SESSION['INICIO'];
    $resultado = '<table class="tbl_detalle" border="1"  cellpadding="5">
    <thead>
        <tr>
            <th>Num</th>
            <th>Orden P.</th>
            <th>Cliente</th>
            <th>Marca</th>
            <th>Ganado</th>
            <th>Cantidad</th>
            <th>Faenamiento</th>
            <th>Saldo</th>
        </tr>
    </thead>
    <tbody>';
    $cont = 0;
    $Tipo = 'Todas las especies';
    if ($variable > 0) $Tipo = get_data_especie($dbConn, $variable);
    $total = 0;
    if ($variable > 0) {
        $consulta = "SELECT * FROM tbl_p_orden o, tbl_r_guiaproceso p, tbl_a_clientes c, tbl_a_especies e 
        WHERE o.gprId = p.gprId AND p.cliId = c.cliId AND p.espId = e.espId AND  o.espId = :id
        AND o.gprId IS NOT NULL  AND o.ordFecha  BETWEEN :finicio AND :final ORDER BY o.ordId ASC";
    } else {
        $consulta = "SELECT * FROM tbl_p_orden o, tbl_r_guiaproceso p, tbl_a_clientes c, tbl_a_especies e 
        WHERE o.gprId = p.gprId AND p.cliId = c.cliId AND p.espId = e.espId 
        AND o.gprId IS NOT NULL  AND o.ordFecha  BETWEEN :finicio AND :final ORDER BY o.ordId ASC";
    }
    $sql = $dbConn->prepare($consulta);
    if ($variable > 0) $sql->bindValue(':id', $variable);
    $sql->bindValue(':finicio', $F_inicio . " 00:00:00");
    $sql->bindValue(':final', $F_final . " 23:59:59");
    $sql->execute();
    $total3 = 0;
    $total1 = 0;
    $total2 = 0;
    while ($row = $sql->fetch()) {
        $cont++;
        $total1 += $row["ordCantidad"];
        $procesado = f_obtener_descuentos($dbConn, $row["ordId"]);
        if ($row["ordTipo"] == 1) $procesado = $row["ordCantidad"];
        $total2 += $procesado;
        $saldo = $row["ordCantidad"] - $procesado;
        $total3 += $saldo;
        $resultado .= '
        <tr>
            <th  class="text-center">' . $cont . '</th>
            <td>' . $row["ordNumOrden"] . '</td>
            <td>' . utf8_encode($row["cliNombres"]) . '</td>
            <td class="text-center">' . utf8_encode($row["cliMarca"]) . '</td>
            <td class="text-center">' . utf8_encode($row["espDescripcion"]) . '</td>
            <th class="text-center">' . $row["ordCantidad"] . '</th>
            <th class="text-center">' . $procesado . '</th>
            <th class="text-center">' . $saldo . '</th>
        </tr>';
    }
    $table = $resultado . "</tbody></table>";
    $Fecha = "";
    $Numero = "SALDOS Y FAENAMIENTO DE GANADO";
    if ($F_final == $F_inicio) {
        $Fecha = '<p style="font-size:15px "><b>FECHA: </b> ' . Transformar_Fecha($F_inicio) . '</p>';
        if ($variable > 0) $Numero = 'SALDOS-FAENAMIENTO-EMRAQ' . '-' . utf8_encode(get_data_especie_letra($dbConn, $variable)) . '-' . CalcularJuliano($F_inicio);
    } else {
        $Fecha = '<p style="font-size:15px "><b>DESDE EL DIA </b> ' . Transformar_Fecha($F_inicio) . ', HASTA EL DIA ' . Transformar_Fecha($F_final) . '</p>';
    }
    $cabecera = '
    <p style="font-size:22px "><b>EMPRESA PÚBLICA METROPOLITANA DE RASTRO QUITO</b></p>
    <p style="font-size:17px "><b>DIRECCIÓN DE PRODUCCIÓN Y FAENAMIENTO</b></p>
    <p style="font-size:15px "><b>TIPO DE GANADO: </b> ' . strtoupper(utf8_encode($Tipo)) . '</p>
    ' . $Fecha . '
    <p style="font-size:15px;text-align:center;margin-top:10px;"><b>' . $Numero . '</b></p><br>';
    $estilos = '
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
            font-size: 11px;
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
            margin-left:30%;
            margin-top:25px;
            border-collapse: collapse;
        }

        .tbl_total td,.tbl_total th{
            font-family:Arial;
            font-size: 12px;
            text-align: center;
        }
        .tbl_total th{
            background: #b3b6b7;
        }';
    $firmas = ConsultarFirmas($dbConn, 5);
    $footer = '<table class="tbl_total" border="1" cellpadding="7">
    <tr>
        <th >TOTAL GANADO</th>
        <th >TOTAL FAENADO</th>
        <th >TOTAL SALDO</th>
    </tr>
    <tr>
        <td>' . $total1 . '</td>
        <td>' . $total2 . '</td>
        <td>' . $total3 . '</td>
    </tr>    
    </table>' . $firmas;
    $Res = FormatoDocumento('SALDOS Y FAENAMIENTO DE GANADO', $table, $cabecera, $estilos, $footer);
    return $Res;
}
//OPCION  7"CORRALAJE"
function Reporte_corralaje($dbConn)
{
    $variable = $_SESSION['VARIABLE'];
    $F_final = $_SESSION['FINAL'];
    $F_inicio = $_SESSION['INICIO'];
    $resultado = '<table class="tbl_detalle" border="1"  cellpadding="5">
    <thead>
        <tr>
            <th>Num</th>
            <th>Orden P.</th>
            <th>Cliente</th>
            <th>Ganado</th>
            <th>Factura</th>
            <th>Cobro</th>
            <th>Cantidad</th>
            <th>Estancia</th>
            <th>Corralaje</th>
        </tr>
    </thead>
    <tbody>';
    $cont = 0;
    $Tipo = 'Todas las especies';
    if ($variable > 0) $Tipo = get_data_especie($dbConn, $variable);
    $total = 0;
    if ($variable > 0) {
        $consulta = "SELECT * FROM tbl_p_orden o, tbl_r_guiaproceso p, tbl_a_clientes c, tbl_a_especies e 
        WHERE o.gprId = p.gprId AND p.cliId = c.cliId AND p.espId = e.espId AND  o.espId = :id
        AND o.gprId IS NOT NULL  AND o.ordFecha  BETWEEN :finicio AND :final ORDER BY o.ordId ASC";
    } else {
        $consulta = "SELECT * FROM tbl_p_orden o, tbl_r_guiaproceso p, tbl_a_clientes c, tbl_a_especies e 
        WHERE o.gprId = p.gprId AND p.cliId = c.cliId AND p.espId = e.espId 
        AND o.gprId IS NOT NULL  AND o.ordFecha  BETWEEN :finicio AND :final ORDER BY o.ordId ASC";
    }
    $sql = $dbConn->prepare($consulta);
    if ($variable > 0) $sql->bindValue(':id', $variable);
    $sql->bindValue(':finicio', $F_inicio . " 00:00:00");
    $sql->bindValue(':final', $F_final . " 23:59:59");
    $sql->execute();
    while ($row = $sql->fetch()) {
        $cont++;
        $pago = 'POR RECAUDAR';
        if ($row["ordEstado"] == 1) $pago = 'RECAUDADO';
        $resultado .= '
        <tr>
            <th  class="text-center">' . $cont . '</th>
            <td>' . $row["ordNumOrden"] . '</td>
            <td>' . utf8_encode($row["cliNombres"]) . '(' . utf8_encode($row["cliMarca"]) . ')</td>
            <td class="text-center">' . utf8_encode($row["espDescripcion"]) . '</td>
            <th class="text-center">' . $row["Num_Documento"] . '</th>
            <th class="text-center">' . $pago . '</th>
            <th class="text-center">' . $row["ordCantidad"] . '</th>
            <th class="text-center">' . $row["ordTiempoEstancia"] . '</th>
            <th class="text-center">' . $row["ordTiempoCobrar"] . '</th>
        </tr>';
    }
    $table = $resultado . "</tbody></table>";
    $Fecha = "";
    if ($F_final == $F_inicio) {
        $Fecha = '<p style="font-size:15px "><b>FECHA: </b> ' . Transformar_Fecha($F_inicio) . '</p>';
    } else {
        $Fecha = '<p style="font-size:15px "><b>DESDE EL DIA </b> ' . Transformar_Fecha($F_inicio) . ', HASTA EL DIA ' . Transformar_Fecha($F_final) . '</p>';
    }
    $cabecera = '
    <p style="font-size:22px "><b>EMPRESA PÚBLICA METROPOLITANA DE RASTRO QUITO</b></p>
    <p style="font-size:17px "><b>DIRECCIÓN DE PRODUCCIÓN Y FAENAMIENTO</b></p>
    <p style="font-size:15px "><b>TIPO DE GANADO: </b> ' . strtoupper(utf8_encode($Tipo)) . '</p>
    ' . $Fecha . '
    <p style="font-size:15px;text-align:center;margin-top:10px;"><b>CORRALAJE DE GANADO</b></p><br>';
    $estilos = '
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
            font-size: 11px;
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
            margin-left:35%;
            margin-top:20px;
            border-collapse: collapse;
        }

        .tbl_total td,.tbl_total th{
            font-family:Arial;
            font-size: 12px;
            text-align: center;
        }
        .tbl_total th{
            background: #b3b6b7;
        }';
    $firmas = ConsultarFirmas($dbConn, 6);
    $footer = '' . $firmas;
    $Res = FormatoDocumento('CORRALAJE', $table, $cabecera, $estilos, $footer);
    return $Res;
}
//OPCION  8 "TAZAS"
function Reporte_taza($dbConn)
{
    $variable = $_SESSION['VARIABLE'];
    $F_final = $_SESSION['FINAL'];
    $F_inicio = $_SESSION['INICIO'];
    $Tipo = 'Todas las especies';
    if ($variable > 0) $Tipo = get_data_especie($dbConn, $variable);
    $table = '';
    $total1 = 0;
    $total2 = 0;
    if ($variable == 0) {
        $consulta = "SELECT espId FROM tbl_a_especies";
        $sql = $dbConn->prepare($consulta);
        $sql->execute();
        while ($row = $sql->fetch()) {
            $total1 += f_get_cantidad_ganado($dbConn, $row["espId"], $F_inicio, $F_final);
            $total2 += f_get_cantidad_servicios_especie($dbConn, $row["espId"], $F_inicio, $F_final);
            $total2 += f_get_cantidad_corralaje($dbConn, $row["espId"], $F_inicio, $F_final);
            $table .= f_data_table_especie_tazas($dbConn, $row["espId"], $F_inicio, $F_final) . '<br>';
        }
    } else {
        $total1 += f_get_cantidad_ganado($dbConn, $variable, $F_inicio, $F_final);
        $total2 += f_get_cantidad_servicios_especie($dbConn, $row["espId"], $F_inicio, $F_final);
        $total2 += f_get_cantidad_corralaje($dbConn, $row["espId"], $F_inicio, $F_final);
        $table = f_data_table_especie_tazas($dbConn, $variable, $F_inicio, $F_final);
    }
    $Fecha = "";
    if ($F_final == $F_inicio) {
        $Fecha = '<p style="font-size:15px "><b>FECHA: </b> ' . Transformar_Fecha($F_inicio) . '</p>';
    } else {
        $Fecha = '<p style="font-size:15px "><b>DESDE EL DIA </b> ' . Transformar_Fecha($F_inicio) . ', HASTA EL DIA ' . Transformar_Fecha($F_final) . '</p>';
    }
    $cabecera = '
    <p style="font-size:22px "><b>EMPRESA PÚBLICA METROPOLITANA DE RASTRO QUITO</b></p>
    <p style="font-size:17px "><b>DIRECCIÓN DE PRODUCCIÓN Y FAENAMIENTO</b></p>
    <p style="font-size:15px "><b>TIPO DE GANADO: </b> ' . strtoupper(utf8_encode($Tipo)) . '</p>
    ' . $Fecha . '
    <p style="font-size:15px;text-align:center;margin-top:10px;"><b>TASAS DE GANADO</b></p><br>';
    $estilos = '
        p{
            font-family:Helvetica;
            margin-bottom:0px;
            margin-top:0px;
        }
        .tbl_detalle{
            width: 100%;
            border-collapse: collapse;
            font-family:Arial;
            font-size: 12px;
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
            margin-left:35%;
            margin-top:20px;
            border-collapse: collapse;
        }

        .tbl_total td,.tbl_total th{
            font-family:Arial;
            font-size: 12px;
            text-align: center;
        }
        .tbl_total th{
            background: #b3b6b7;
        }';
    $firmas = ConsultarFirmas($dbConn, 7);
    $footer = '<table class="tbl_total" border="1" cellpadding="7">
    <tr>
        <th >TOTAL ANINAMLES</th>
        <th >TOTAL RECAUDADO</th>
    </tr>
    <tbody>
        <tr>
            <td>' . $total1 . '</td>
            <td><b>' . number_format($total2, 2, ',', ' ') . ' $</b></td>
        </tr>  
    </tbody>  
    </table>' . $firmas;
    $Res = FormatoDocumento('TASAS', $table, $cabecera, $estilos, $footer);
    return $Res;
}






// FUNCIONES  
function ConsultarFirmas($dbConn, $Op)
{
    $resultado = '<table style="font-family:Arial;font-size: 12px;width: 100%;" >';
    $consulta = "SELECT * FROM tbl_a_firma 
    WHERE firTipo = :tipo  AND firEstado = 0 
    ORDER BY firOrden ASC ";
    $sql = $dbConn->prepare($consulta);
    $sql->bindValue(':tipo', $Op);
    $sql->execute();
    $cont = 0;
    while ($row = $sql->fetch()) {
        $cont++;
        $Nombre = $row["firNombre"];
        $Descripc1 = $row["firDescripcion1"];
        if ($row["firInformacion"] == 1) {
            $array = get_data_user($dbConn);
            $Nombre = $array[0];
            $Descripc1 = $array[1];
        }
        $td = '<td>
        <br><br><br><br>
        ------------------------------------------<br>
        ' . utf8_encode($Nombre) . '<br>
        ' . utf8_encode($Descripc1) . '<br>
        ' . utf8_encode($row["firDescripcion2"]) . '<br>
        </td>';
        if (($cont % 2) == 0) {
            $resultado .= $td . '</tr>';
        } else {
            $resultado .= '<tr>' . $td;
        }
    }
    return $resultado . "</table>";
}
function get_data_user($dbConn)
{
    $resultado = [];
    $consulta = "SELECT * FROM tbl_a_usuarios WHERE usuId = :cedula AND usuEstado = 1 AND usuEstado_pass = 0";
    $sql = $dbConn->prepare($consulta);
    $sql->bindValue(':cedula', $_SESSION['MM_Username']);
    $sql->execute();
    if ($row = $sql->fetch()) return [utf8_encode($row["usuNombre"]), utf8_encode($row["usuCargo"])];
    else return false;
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
function FormatoDocumento($titulo, $table, $cabecera, $estilos, $footer)
{
    $resultado = '<!DOCTYPE html>
    <html lang="en" dir="ltr">
    <head>
        <meta charset="utf-8">
        <title>' . $titulo . '</title>
    </head>
        <style> ' . $estilos . '</style>
    <body>' . $cabecera . $table . $footer . '</body>
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
function get_data_especie($dbConn, $Id)
{
    $consulta = "SELECT espDescripcion FROM tbl_a_especies WHERE espId = :id";
    $sql = $dbConn->prepare($consulta);
    $sql->bindValue(':id', $Id);
    $sql->execute();
    if ($row = $sql->fetch()) return $row["espDescripcion"];
    else return "ERROR-122";
}
function get_data_especie_letra($dbConn, $Id)
{
    $consulta = "SELECT espLetra FROM tbl_a_especies WHERE espId = :id";
    $sql = $dbConn->prepare($consulta);
    $sql->bindValue(':id', $Id);
    $sql->execute();
    if ($row = $sql->fetch()) return $row["espLetra"];
    else return "ERROR-122";
}
function f_obtener_procesar($dbConn, $Id)
{
    $cont = 0;
    $consulta = "SELECT * FROM tbl_r_detalle  
    WHERE dtEstado = 1  AND  ordId is not null AND gprId = :id";
    $sql = $dbConn->prepare($consulta);
    $sql->bindValue(':id', $Id);
    $sql->execute();
    while ($row = $sql->fetch()) {
        $cont++;
    }
    return $cont;
}
function f_obtener_cantidades($dbConn, $Id)
{
    $cont = [0, 0, 0];
    $consulta = "SELECT * FROM tbl_p_antemortem WHERE gprId = :id ";
    $sql = $dbConn->prepare($consulta);
    $sql->bindValue(':id', $Id);
    $sql->execute();
    while ($row = $sql->fetch()) {
        if ($row["antDictamen"] == 0) $cont[0] += $row["antCantidad"];
        else if ($row["antDictamen"] == 1) $cont[1] += $row["antCantidad"];
        else if ($row["antDictamen"] == 2) $cont[2] += $row["antCantidad"];
        else return false;
    }
    return $cont;
}
function f_obtener_nombre_especie_producto($dbConn, $Id)
{
    $consulta = "SELECT e.espDescripcion FROM tbl_a_productos p, tbl_a_especies e
    WHERE p.espId = e.espId AND p.proId = :id ";
    $sql = $dbConn->prepare($consulta);
    $sql->bindValue(':id', $Id);
    $sql->execute();
    if ($row = $sql->fetch()) return utf8_encode($row["espDescripcion"]);
    else return 'ERROR-21212';
}
function f_obtener_nombre_especie_subproducto($dbConn, $Id)
{
    $consulta = "SELECT e.espDescripcion FROM tbl_a_subproductos p, tbl_a_especies e
    WHERE p.espId = e.espId AND p.subId = :id ";
    $sql = $dbConn->prepare($consulta);
    $sql->bindValue(':id', $Id);
    $sql->execute();
    if ($row = $sql->fetch()) return utf8_encode($row["espDescripcion"]);
    else return 'ERROR-21212';
}
function f_obtener_decomisos_por_producto($dbConn, $Id, $Acta)
{
    $consulta = "SELECT p.proDescripcion, d.decCantidad,d.decCausa  
    FROM tbl_p_decomiso d, tbl_a_productos p 
    WHERE d.proId = p.proId AND d.proId = :id AND d.actId = :acta";
    $sql = $dbConn->prepare($consulta);
    $sql->bindValue(':id', $Id);
    $sql->bindValue(':acta', $Acta);
    $sql->execute();
    $array = [0, 'ERROR-121', ''];
    while ($row = $sql->fetch()) {
        $array[0] += $row["decCantidad"];
        $array[1] = utf8_encode($row["proDescripcion"]);
        $array[2] .= utf8_encode($row["decCausa"]) . ',';
    }
    return $array;
}
function f_obtener_decomisos_por_subproducto($dbConn, $Id, $Acta)
{
    $consulta = "SELECT d.ddtSubproducto, d.ddtCantidad FROM tbl_p_decomiso_detalle d, tbl_p_decomiso de 
    WHERE d.decId = de.decId AND de.actId = :acta AND d.subId = :id";
    $sql = $dbConn->prepare($consulta);
    $sql->bindValue(':id', $Id);
    $sql->bindValue(':acta', $Acta);
    $sql->execute();
    $array = [0, 'ERROR-121'];
    while ($row = $sql->fetch()) {
        $array[0] += $row["ddtCantidad"];
        $array[1] = utf8_encode($row["ddtSubproducto"]);
    }
    return $array;
}
function f_obtener_enfermedades_por_subproducto($dbConn, $Id, $Acta)
{
    $consulta = "SELECT DISTINCT d.ddeEnfermedad FROM tbl_p_decomiso_enfermedad d, tbl_p_decomiso de 
    WHERE d.decId = de.decId AND de.actId = :acta AND d.subId = :id";
    $sql = $dbConn->prepare($consulta);
    $sql->bindValue(':id', $Id);
    $sql->bindValue(':acta', $Acta);
    $sql->execute();
    $array = "";
    while ($row = $sql->fetch()) {
        $array .= utf8_encode($row["ddeEnfermedad"]) . ',';
    }
    return $array;
}
function f_obtener_corrales_guia($dbConn, $Id)
{
    $consulta = "SELECT c.crrDescripcion, l.lgrMarca FROM tbl_r_lugar l, tbl_a_corral c WHERE l.crrId = c.crrId AND l.lgrEliminado = 0 AND l.gprId = :id";
    $sql = $dbConn->prepare($consulta);
    $sql->bindValue(':id', $Id);
    $sql->execute();
    $corrales = '';
    while ($row = $sql->fetch()) $corrales .= '(' . utf8_encode($row["crrDescripcion"]) . ' ' . utf8_encode($row["lgrMarca"]) . ') ';
    return $corrales;
}
function f_obtener_descuentos($dbConn, $Id)
{
    $consulta = "SELECT faeCantidad FROM tbl_p_faenamiento WHERE ordId = :id";
    $sql = $dbConn->prepare($consulta);
    $sql->bindValue(':id', $Id);
    $sql->execute();
    $cont = 0;
    while ($row = $sql->fetch()) $cont += $row["faeCantidad"];
    return $cont;
}
function f_data_table_especie_tazas($dbConn, $Id, $Inicio, $Final)
{
    $servicios = f_get_array_servicios($dbConn, $Id);
    $th = '';
    $td = '';
    $total = 0;
    $corralaje = f_get_cantidad_corralaje($dbConn, $Id, $Inicio, $Final);
    for ($i = 0; $i < count($servicios); $i++) {
        $th .= '<th>' . $servicios[$i][1] . ' (' . number_format($servicios[$i][2], 2) . ')</th>';
        $cantidad = f_get_cantidad_servicios($dbConn, $servicios[$i][0], $Inicio, $Final);
        $total += $cantidad;
        $cantidad =  number_format($cantidad, 2, ',', ' ');
        $td .= '<td class="c_number">' . $cantidad . ' $</td>';
    }
    $total += $corralaje;
    $ganado = f_get_cantidad_ganado($dbConn, $Id, $Inicio, $Final);
    $total =  number_format($total, 2, ',', ' ');
    $corralaje =  number_format($corralaje, 2, ',', ' ');
    $especie = get_data_especie($dbConn, $Id);
    $precio_corralaje = f_get_especie_corralaje_precio($dbConn, $Id);
    return '<table class="tbl_detalle center" border="1"  cellpadding="5">
    <thead>
        <tr   >
            <th class="text-left" colspan="' . (3 + count($servicios)) . '">' . utf8_encode($especie) . '</th>
        </tr>
        <tr >
            <th>Cantidad</th>
            ' . $th . '
            <th>Corralaje (' . number_format($precio_corralaje, 2) . ')</th>
            <th>Total</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td >' . $ganado . '</td>
            ' . $td . '
            <td >' . $corralaje . ' $</td>
            <th >' . $total . ' $</th>
        </tr>
    <tbody></table>';
}
function f_get_array_servicios($dbConn, $Id)
{
    $array = [];
    $consulta = "SELECT * FROM tbl_a_servicios WHERE espId = :id";
    $sql = $dbConn->prepare($consulta);
    $sql->bindValue(':id', $Id);
    $sql->execute();
    while ($row = $sql->fetch()) array_push($array, [$row["srnId"], $row["srnDescripcion"], $row["srnPrecio"], $row["srnEstado"]]);
    return $array;
}
function f_get_cantidad_servicios($dbConn, $Id, $Inicio, $Final)
{
    $total = 0;
    $consulta = "SELECT * FROM tbl_YP_EGDATA_FAC f, tbl_p_orden o 
    WHERE f.ordId =  o.ordId AND o.ordFecha BETWEEN :inicio AND :final AND f.srnId = :id";
    $sql = $dbConn->prepare($consulta);
    $sql->bindValue(':id', $Id);
    $sql->bindValue(':inicio', $Inicio . ' 00:00:00');
    $sql->bindValue(':final', $Final . ' 23:59:59');
    $sql->execute();
    while ($row = $sql->fetch()) $total += $row["Valor3"];
    return $total;
}
function f_get_cantidad_corralaje($dbConn, $Id, $Inicio, $Final)
{
    $total = 0;
    $consulta = "SELECT * FROM tbl_YP_EGDATA_FAC f, tbl_p_orden o 
    WHERE f.ordId =  o.ordId AND o.ordFecha BETWEEN :inicio AND :final AND  o.espId = :id AND f.srnId IS NULL AND f.Tipo= 'D'";
    $sql = $dbConn->prepare($consulta);
    $sql->bindValue(':id', $Id);
    $sql->bindValue(':inicio', $Inicio . ' 00:00:00');
    $sql->bindValue(':final', $Final . ' 23:59:59');
    $sql->execute();
    while ($row = $sql->fetch()) $total += $row["Valor3"];
    return $total;
}
function f_get_cantidad_servicios_especie($dbConn, $Id, $Inicio, $Final)
{
    $total = 0;
    $consulta = "SELECT * FROM tbl_YP_EGDATA_FAC f, tbl_p_orden o 
    WHERE f.ordId =  o.ordId AND o.ordFecha BETWEEN :inicio AND :final AND  o.espId = :id AND f.srnId IS NOT NULL";
    $sql = $dbConn->prepare($consulta);
    $sql->bindValue(':id', $Id);
    $sql->bindValue(':inicio', $Inicio . ' 00:00:00');
    $sql->bindValue(':final', $Final . ' 23:59:59');
    $sql->execute();
    while ($row = $sql->fetch()) $total += $row["Valor3"];
    return $total;
}
function f_get_cantidad_ganado($dbConn, $Id, $Inicio, $Final)
{
    $total = 0;
    $consulta = "SELECT ordCantidad FROM tbl_p_orden WHERE espId = :id AND ordFecha BETWEEN :inicio AND :final ";
    $sql = $dbConn->prepare($consulta);
    $sql->bindValue(':id', $Id);
    $sql->bindValue(':inicio', $Inicio . ' 00:00:00');
    $sql->bindValue(':final', $Final . ' 23:59:59');
    $sql->execute();
    while ($row = $sql->fetch()) $total += $row["ordCantidad"];
    return $total;
}
function f_get_especie_corralaje_precio($dbConn, $Id)
{
    $total = 0;
    $consulta = "SELECT espPrecioCorralaje FROM tbl_a_especies WHERE espId = :id";
    $sql = $dbConn->prepare($consulta);
    $sql->bindValue(':id', $Id);
    $sql->execute();
    if ($row = $sql->fetch()) return $row["espPrecioCorralaje"];
}
function CalcularJuliano($Fecha)
{
    $total = 0;
    $date = date_create($Fecha);
    $numMes = date_format($date, "n");
    $anio = date_format($date, "Y");
    for ($i = 1; $i < $numMes; $i++) {
        $fecha = date("t", strtotime($anio . "-$i"));
        $total = $total + intval($fecha);
    }
    $total = $total + intval(date_format($date, "d"));
    return "$total" . date_format($date, "y");
}
