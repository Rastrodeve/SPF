<?php
require '../../FilePHP/utils.php';
if (isset($_REQUEST['op'])) {
    $dbConn = conectar($db);
    $op = $_REQUEST['op'];
    if ($op == 1) echo f_get_start($dbConn);
    elseif ($op == 2) $_SESSION['ANTEMORTEM'][0] = $_POST["Id"];
    elseif ($op == 3) $_SESSION['ANTEMORTEM'][1] = $_POST["Id"];
    elseif ($op == 4) echo get_data_new_inspeccion($dbConn);
    elseif ($op == 5) echo f_get_data_dictamenes($dbConn, $_SESSION['ANTEMORTEM'][1]);
    elseif ($op == 6) echo f_data_cantidad($dbConn);
    elseif ($op == 7) echo f_inster_dictamen($dbConn);
    elseif ($op == 8) $_SESSION['ANTEMORTEM'][0] = 0;
    elseif ($op == 9) echo Lista_de_Guias_movilizacion($dbConn, $_SESSION['ANTEMORTEM'][0]);
    elseif ($op == 10) echo Lista_de_Guias_movilizacion($dbConn, $_SESSION['ANTEMORTEM'][0]);
} else {
    header('location: ../../');
}

function f_get_start($dbConn)
{
    if ($_SESSION['ANTEMORTEM'][0] == 0) {
        $array = select_data_especies($dbConn);
        $select = '';
        for ($i = 0; $i < count($array); $i++) {
            $select .= '<option value="' . $array[$i][0] . '">' . $array[$i][1] . '</option>';
        }
        $formulario = 
        '<div class="row">
            <div class="col-12 mt-2 text-center">
                <form action="formulario_antemorten.php" target="_blank" method="GET">
                    <label>Fecha Formulario</label>
                    <input type="date" id="idFechaFormulario" required name="fechaFormulario">
                    <br>
                    <div class="row">
                        <div class="col-3">
                            <label>Especie</label>
                        </div>
                        <div class="col-9">
                            <select id="slcEspecie" class="form-control form-control-sm " name="slcEspecie">'. $select . '</select>
                        </div>
                    </div>
                    <br>
                    <center>
                        <input type="submit" class="btn btn-info btn-lg pl-5 pr-5 pt-3 pb-3" name="GENERAR" value="GENERAR">
                    </center>
                </form>
            </div>
        </div>';
        $buscar = '
        <div class="row">
            <div class="col-12 mt-2 text-center">
                <select id="slcBuscar" class="form-control form-control-sm select2bs4" style="width:100%">' . $select . '</select>
                <br>
                <center>
                    <button class="btn btn-info btn-lg pl-5 pr-5 pt-3 pb-3" onclick="GET_Guias()">
                        <b>CONTINUAR</b>
                    </button>
                </center>
            </div>
        </div>';

        return $formulario . $buscar;
    } else {
        if ($_SESSION['ANTEMORTEM'][1] == 0) {
            $table = Lista_de_Guias_movilizacion($dbConn, $_SESSION['ANTEMORTEM'][0]);
            return '
            <div class="card"><div class="card-body" >
            <div class="row mb-2">
                    <div class="col-6">
                        <b>
                            <span class="text-muted">GANADO SELECCIONADO:</span>
                            ' . get_data_especie($dbConn, $_SESSION['ANTEMORTEM'][0]) . '
                        </b>
                    </div>
                    <div class="col-6">
                        <button class="btn btn-danger btn-sm float-right" onclick="f_regresar()"><b>REGRESAR</b></button>
                    </div>
            </div>
            <hr>
            <div id="cont-table-data">' . $table . '</div></div></div>';
        } else {
            return '<div class="card"><div class="card-body" >' . get_data_guia($dbConn, $_SESSION['ANTEMORTEM'][1]) . '</div></div>';
        }
    }
}

function Lista_de_Guias_movilizacion($dbConn, $Id)
{
    $resultado = '<table id="tbl_data_table" class="table table-bordered table-striped table-sm" >
            <thead class="bg-navy" style="font-size:18px;">
                <th>#</th>
                <th>COMPROBANTE</th>
                <th>CLIENTE</th>
                <th>F.NORMAL</th>
                <th>MATANZA DE EMERGENCIA</th>
                <th>S.SANITARIO</th>
                <th>MATANZA BAJO PRECAUCIONES ESPECIALES</th>
                <th>APLAZAMIENTO MATANZA</th>
                <th>RESTANTES</th>
                <th class="text-center">SELECCIONAR</th>
            </thead>
    <tbody style="font-size:15px;">';
    // $consulta = "SELECT p.gprId, g.guiNumero, p.gprComprobante ,c.cliNombres, e.espDescripcion,p.gprMacho,p.gprHembra, p.gprEstado, p.gprestadoDetalle 
    // FROM tbl_r_guiaproceso p, tbl_r_guiamovilizacion g, tbl_a_especies e, tbl_a_clientes c 
    // WHERE p.guiId = g.guiId AND p.espId = e.espId AND p.cliId = c.cliId AND e.espId = :id AND
    // g.guiEliminado = 0  AND p.gprEliminado = 0 AND p.gprHabilitado = 1
    // ORDER BY p.gprTurno , gprComprobante ASC ";


    $consulta = "SELECT * FROM (SELECT p.gprId, g.guiNumero, p.gprComprobante, c.cliNombres, e.espDescripcion,
	SUM(p.gprMacho) gprMacho, SUM(p.gprHembra) gprHembra, SUM(p.gprMacho) + SUM(p.gprHembra) total,
    SUM(IFNULL(o.ordCantidad,0)) ordCantidad
    , p.gprEstado, p.gprestadoDetalle
    FROM tbl_r_guiamovilizacion g, tbl_r_guiaproceso p LEFT JOIN 
		(SELECT o.ordId, o.ordNumOrden, o.ordFecha, SUM(o.ordCantidad) ordCantidad, o.gprId 
			FROM tbl_p_orden o 
            WHERE o.ordEliminado = 0 GROUP BY o.gprId) AS o ON (p.gprId = o.gprId), 
		tbl_a_especies e, tbl_a_clientes c 
    WHERE g.guiId = p.guiId AND p.espId = e.espId AND p.cliId = c.cliId AND e.espId = :id 
    AND g.guiEliminado = 0  AND p.gprEliminado = 0 AND p.gprHabilitado = 1 
    GROUP BY p.gprId) t WHERE t.total != t.ordCantidad
    ORDER BY t.gprId, t.gprComprobante ASC";
    $sql = $dbConn->prepare($consulta);
    $sql->bindValue(':id', $Id);
    $sql->execute();
    $normal = $urgencia = $sanitario = $total_restante =  $cont = $suma3 = $suma4 =0;
    while ($row = $sql->fetch()) {
        // var_dump($row);
        // echo "<br>";
        // echo "EEEEE<br>";
        // echo "<br>";
        // echo "<br>";
        $orden = f_buscar_cantidad_orden($dbConn, $row["gprId"]); //BUSQUEDA DE CANTIDAD CON ORDEN DE PRODUCCION
        // var_dump($orden);
        // echo "<br>";
        // echo "EEEEE<br>";
        // echo "<br>";
        // echo "<br>";
        $cantidad = $row["gprMacho"] + $row["gprHembra"]; //CANTIDAD GLOBAL DE LA GUIA D EPROCESO
        $total_orden = $orden[0] + $orden[1];
        if ($cantidad != $total_orden) {
            // ECHO "HERE";
            $cont++;
            $array = f_obtener_cantidades($dbConn, $row["gprId"]);
            if ($array == false) return 'ERROR-81728712';
            $restante = ($row["gprMacho"] + $row["gprHembra"]) - $array[0] - $array[1] - $array[2] - $array[3] - $array[4];
            $normal += $array[0];
            $urgencia += $array[1];
            $sanitario += $array[2];
            $suma3 += $array[3];
            $suma4 += $array[4];
            $total_restante += $restante;
            $verde = '';
            if ($restante == 0) $verde = 'table-success';
            $resultado .= '
                <tr>
                    <td><h4 class="text-center" >' . $cont . '</h4></td>
                    <td class="text-center">' . utf8_encode($row["gprComprobante"]) . '</td>
                    <td >' . utf8_encode($row["cliNombres"]) . '</td>
                    <td class="text-center">' . $array[0] . '</td>
                    <td class="text-center">' . $array[1] . '</td>
                    <td class="text-center">' . $array[2] . '</td>
                    <td class="text-center">' . $array[3] . '</td>
                    <td class="text-center">' . $array[4] . '</td>
                    <td class="text-center ' . $verde . '"><b>' . $restante . '</b></td>
                    <td style="max-width:100px;" class="text-center">
                        <button class="btn btn-info btn-sm" onclick="get_data_guia_ant(' . $row["gprId"] . ')">
                            <b>SELECCIONAR</b>
                        </button>
                    </td>
                </tr>';
        }
    }
    $fotter = '';
    if ($cont > 0) {
        $danger = '';
        if ($total_restante > 0) $danger = 'table-danger';
        $fotter = '<tfoot class="table-secondary text-center" style="font-size:15px;">
        <tr>
            <th colspan = "3">TOTALES</th>
            <th >' . $normal . '</th>
            <th >' . $urgencia . '</th>
            <th >' . $sanitario . '</th>
            <th >' . $suma3 . '</th>
            <th >' . $suma4 . '</th>
            <th class="' . $danger . '" >' . $total_restante . '</th>
            <th ></th>
        </tr>
    </tfoot>';
    }
    $btn = '<button class="btn btn-info btn-sm float-right" onclick="GET_Guias_table()"><b>RECARGAR</b></button>';
    return $btn . $resultado . "</tbody>" . $fotter . "</table>";
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
function f_buscar_cantidad_orden($dbConn, $Id)
{
    $cont = [0, 0];
    $consulta = "SELECT * FROM tbl_p_orden WHERE gprId = :id AND ordEliminado = 0";
    $sql = $dbConn->prepare($consulta);
    $sql->bindValue(':id', $Id);
    $sql->execute();
    while ($row = $sql->fetch()) {
        if ($row["ordTipo"] == 0) $cont[0] += $row["ordCantidad"]; //ORDEN NORMAL 
        else if ($row["ordTipo"] == 1) $cont[1] += $row["ordCantidad"]; // ORDEN EMERGENTE
    }
    return $cont;
}
function f_obtener_cantidades($dbConn, $Id)
{
    $cont = [0, 0, 0,0,0,0];
    $consulta = "SELECT * FROM tbl_p_antemortem WHERE gprId = :id ";
    $sql = $dbConn->prepare($consulta);
    $sql->bindValue(':id', $Id);
    $sql->execute();
    while ($row = $sql->fetch()) {
        if ($row["antDictamen"] == 0) $cont[0] += $row["antCantidad"];
        else if ($row["antDictamen"] == 1) $cont[1] += $row["antCantidad"];
        else if ($row["antDictamen"] == 2) $cont[2] += $row["antCantidad"];
        else if ($row["antDictamen"] == 3) $cont[3] += $row["antCantidad"];
        else if ($row["antDictamen"] == 4) $cont[4] += $row["antCantidad"];
        else return false;
    }
    return $cont;
}
function f_obtener_cantidades_2($dbConn, $Id, $tipo)
{
    $cont = [0, 0, 0,0,0];
    $consulta = "SELECT * FROM tbl_p_antemortem WHERE gprId = :id AND antSexo = :ge";
    $sql = $dbConn->prepare($consulta);
    $sql->bindValue(':id', $Id);
    $sql->bindValue(':ge', $tipo);
    $sql->execute();
    while ($row = $sql->fetch()) {
        if ($row["antDictamen"] == 0) $cont[0] += $row["antCantidad"];
        else if ($row["antDictamen"] == 1) $cont[1] += $row["antCantidad"];
        else if ($row["antDictamen"] == 2) $cont[2] += $row["antCantidad"];
        else if ($row["antDictamen"] == 3) $cont[3] += $row["antCantidad"];
        else if ($row["antDictamen"] == 4) $cont[4] += $row["antCantidad"];
        else return false;
    }
    return $cont;
}

function select_data_especies($dbConn)
{
    $resultado = array();
    $consulta = "SELECT * FROM tbl_a_especies WHERE espEstado = 0";
    $sql = $dbConn->prepare($consulta);
    $sql->execute();
    $cont = 0;
    while ($row = $sql->fetch()) {
        if (comprobar_servicios($dbConn, $row["espId"]) > 0) {
            if (comprobar_productos($dbConn, $row["espId"]) > 0) {
                array_push($resultado, array($row["espId"], utf8_encode($row["espDescripcion"])));
            }
        }
    }
    return  $resultado;
}

function comprobar_servicios($dbConn, $Id)
{
    $consulta = "SELECT * FROM tbl_a_servicios WHERE espId = :id  AND srnEstado = 0";
    $sql = $dbConn->prepare($consulta);
    $sql->bindValue(':id', $Id);
    $sql->execute();
    $cont = 0;
    while ($row = $sql->fetch()) {
        $cont++;
    }
    return $cont;
}

function comprobar_productos($dbConn, $Id)
{
    $consulta = "SELECT * FROM tbl_a_productos WHERE espId = :id  AND proEliminado = 0";
    $sql = $dbConn->prepare($consulta);
    $sql->bindValue(':id', $Id);
    $sql->execute();
    $cont = 0;
    while ($row = $sql->fetch()) {
        $cont++;
    }
    return $cont;
}

function get_data_guia($dbConn, $Id)
{
    $consulta = "SELECT *  FROM tbl_r_guiaproceso p, tbl_r_guiamovilizacion g, tbl_a_especies e, tbl_a_clientes c 
    WHERE p.guiId = g.guiId AND p.espId = e.espId AND p.cliId = c.cliId AND p.gprId = :id AND g.guiEliminado = 0  AND p.gprEliminado = 0 ";
    $sql = $dbConn->prepare($consulta);
    $sql->bindValue(':id', $Id);
    $sql->execute();
    $cont = 0;
    if ($row = $sql->fetch()) {
        $orden = f_buscar_cantidad_orden($dbConn, $row["gprId"]); //BUSQUEDA DE CANTIDAD CON ORDEN DE PRODUCCION
        $cantidad = $row["gprMacho"] + $row["gprHembra"]; //CANTIDAD GLOBAL DE LA GUIA D EPROCESO
        $total_orden = $orden[0] + $orden[1];
        if ($cantidad != $total_orden) {
            $hembra = f_obtener_cantidades_2($dbConn, $Id, 0);
            $macho = f_obtener_cantidades_2($dbConn, $Id, 1);
            if ($macho == false) return 'ERROR-81728712';
            if ($hembra == false) return 'ERROR-81728712';
            $restantemacho = $row["gprMacho"]  - $macho[0] - $macho[1] - $macho[2];
            $restantehembra = $row["gprHembra"] - $hembra[0] - $hembra[1] - $hembra[2];
            print_r($hembra);
            return '<center>
                        <Button class="btn btn-info btn-sm mb-3" onclick="get_data_guia_ant(0)"><b>REGRESAR</b></Button>
                    </center>
                    <div class="card">
                        <div class="card-header " data-card-widget="collapse" style="cursor:pointer">
                            <h6 class="text-muted card-title"><b>DATOS DE LA GUÍA</b></h6>
                        </div>
                        <div class="card-body">
                            <h6 class="row">
                                <b class="col-lg-6 mt-2">
                                    <span class="text-muted mr-2">Guía de movilización:</span>
                                    ' . $row["guiNumero"] . '
                                </b>
                                <b class="col-lg-6 mt-2">
                                    <span class="text-muted mr-2">Nro. Comprobante:</span>
                                    ' . $row["gprComprobante"] . '
                                </b>
                            </h6>
                            <h6 class="row">
                                <b class="col-lg-6 mt-2">
                                    <span class="text-muted mr-2">Introductor:</span>
                                    ' . utf8_encode($row["cliNombres"]) . '
                                </b>
                                <b class="col-lg-6 mt-2">
                                    <span class="text-muted mr-2">Machos:</span>
                                    ' . $row["gprMacho"] . '
                                    <span class="text-muted mr-2">- Hembras:</span>
                                    ' . $row["gprHembra"] . '
                                </b>
                            </h6>
                            <h6 class="row">
                                <b class="col-lg-6 mt-2">
                                    <span class="text-muted mr-2">Ganado:</span>
                                    ' . utf8_encode($row["espDescripcion"]) . '
                                </b>
                                <b class="col-lg-6 mt-2">
                                    <span class="text-muted mr-2">Total:</span>
                                    ' . ($row["gprMacho"] + $row["gprHembra"]) . '
                                </b>
                            </h6>
                            <h6 class="row">
                                <b class="col-lg-6 mt-2">
                                    <span class="text-muted mr-2">Restante:</span>
                                    Machos: ' . $restantemacho . ' -  Hembras: ' . $restantehembra . '
                                </b>
                            </h6>
                        </div>
                    </div>
                    <hr>
                    <div id="contenedor-resultados">
                        ' . f_get_data_dictamenes($dbConn, $Id) . '
                    </div>';
        } else {
            $_SESSION['ANTEMORTEM'][1] = 0;
            return f_get_start($dbConn);
        }
    } else "ERROR-212-212"; //NO SE ENCONTRO LA GUIA
}
function f_get_data_dictamenes($dbConn, $Id)
{
    $resultado = '<table id="tbl_data_table" class="table table-bordered table-striped table-sm">
                <thead class="bg-navy" style="font-size:18px;">
                    <th class="text-center">#</th>
                    <th>AUTOR</th>
                    <th class="text-center">DICTAMEN</th>
                    <th class="text-center">CANTIDAD</th>
                    <th class="text-center">GENERO</th>
                    <th class="text-center">ACCIONES</th>
                </thead>
                <tbody style="font-size:15px;">';
    $consulta = "SELECT a.antId,u.usuNombre,a.antDictamen,a.antCantidad,a.antSexo FROM tbl_p_antemortem a, tbl_a_usuarios u 
    WHERE  a.usuId = u.usuId AND a.gprId = :id";
    $sql = $dbConn->prepare($consulta);
    $sql->bindValue(':id', $Id);
    $sql->execute();
    $cont = 0;
    while ($row = $sql->fetch()) {
        $cont++;
        $orden = f_buscar_cantidad_orden($dbConn, $Id); //BUSQUEDA DE CANTIDAD CON ORDEN DE PRODUCCION
        $cantidad_total = f_obtener_cantidades($dbConn, $Id);


        $genero = "ERROR";
        if ($row["antSexo"] == 0) $genero = "HEMBRA";
        else if ($row["antSexo"] == 1) $genero = "MACHO";

        $dictamen = "ERROR";
        $buttom = "";
        $color = "bg-dark";
        if ($row["antDictamen"] == 0) {
            if ($orden[0] != $cantidad_total[0]) {
                $buttom = '
                <button class="btn btn-danger btn-sm">
                    <b><i class="fas fa-pencil-alt"></i></b>
                </button>';
            }
            $dictamen = "FAENAMIENTO NORMAL";
            $color = "bg-success";
        } else if ($row["antDictamen"] == 1) {
            if ($orden[1] != $cantidad_total[1]) {
                $buttom = '
                <button class="btn btn-danger btn-sm">
                    <b><i class="fas fa-pencil-alt"></i></b>
                </button>';
            }
            $dictamen = "MATANZA DE EMERGENCIA";//$dictamen = "SACRIFICIO URGENTE";
            $color = "bg-danger";
        } else if ($row["antDictamen"] == 2) {
            if ($orden[1] != $row["antCantidad"]) {
                $buttom = '
                <button class="btn btn-danger btn-sm">
                    <b><i class="fas fa-pencil-alt"></i></b>
                </button>';
            }
            $dictamen = "SACRIFICIO SANITARIO";
            $color = "bg-warning";
        } else if ($row["antDictamen"] == 3) {
            if ($orden[1] != $row["antCantidad"]) {
                $buttom = '
                <button class="btn btn-danger btn-sm">
                    <b><i class="fas fa-pencil-alt"></i></b>
                </button>';
            }
            $dictamen = "MATANZA BAJO PRECAUCIONES ESPECIALES";
            $color = "bg-warning";
        } else if ($row["antDictamen"] == 4) {
            if ($orden[1] != $row["antCantidad"]) {
                $buttom = '
                <button class="btn btn-danger btn-sm">
                    <b><i class="fas fa-pencil-alt"></i></b>
                </button>';
            }
            $dictamen = "APLAZAMIENTO MATANZA";
            $color = "bg-warning";
        }

        $resultado .= '
        <tr>
            <th class="text-center">' . $cont . '</th>
            <td>' . utf8_encode($row["usuNombre"]) . '</td>
            <td class="text-center ' . $color . '" ><b>' . $dictamen . '</b></td>
            <td class="text-center">' . $row["antCantidad"] . '</td>
            <td class="text-center ">' . $genero . '</td>
            <td>TRABAJANDO</td>
            <td class="text-center d-none">
                ' . $buttom . '
                <button class="btn btn-info btn-sm" data-toggle="modal" data-target="#Modal">
                    <i class="fas fa-eye"></i>
                </button>
            </td>
        </tr>';
    }
    return '<button class="btn btn-sm btn-info float-right" onclick="f_get_data_table()"><b>RECARGAR</b></button>
    <button class="btn btn-danger mb-4" onclick="f_get_data_new_inspeccion()"><b>NUEVA INSPECCIÓN</b></button>' . $resultado . "</tbody></table>";
}

function get_data_new_inspeccion($dbConn)
{
    $consulta = "SELECT *  FROM tbl_r_guiaproceso p, tbl_r_guiamovilizacion g, tbl_a_especies e, tbl_a_clientes c 
    WHERE p.guiId = g.guiId AND p.espId = e.espId AND p.cliId = c.cliId AND p.gprId = :id AND g.guiEliminado = 0  AND p.gprEliminado = 0 ";
    $sql = $dbConn->prepare($consulta);
    $sql->bindValue(':id', $_SESSION['ANTEMORTEM'][1]);
    $sql->execute();
    $cont = 0;
    if ($row = $sql->fetch()) {
        $orden = f_buscar_cantidad_orden($dbConn, $row["gprId"]); //BUSQUEDA DE CANTIDAD CON ORDEN DE PRODUCCION
        $cantidad = $row["gprMacho"] + $row["gprHembra"]; //CANTIDAD GLOBAL DE LA GUIA D EPROCESO
        $total_orden = $orden[0] + $orden[1];
        if ($cantidad != $total_orden) {
            $macho = f_obtener_cantidades_2($dbConn, $_SESSION['ANTEMORTEM'][1], 1);
            $hembra = f_obtener_cantidades_2($dbConn, $_SESSION['ANTEMORTEM'][1], 0);
            if ($macho == false) return 'ERROR-81728712';
            $restantemacho = $row["gprMacho"]  - $macho[0] - $macho[1] - $macho[2];
            $restantehembra = $row["gprHembra"]  - $hembra[0] - $hembra[1] - $hembra[2];
            $items = f_cabecera_item($dbConn);
            $checked1 = '';
            $checked2 = '';
            $disabled1 = '';
            $disabled2 = '';
            if ($restantehembra > 0) $checked1 = 'checked';
            else $disabled1 = 'disabled';
            if ($restantemacho > 0) $checked2 = 'checked';
            else $disabled2 = 'disabled';
            return '<h5>
                        <b>INSPECCIÓN ANTEMORTEM</b>
                        <button class="btn btn-danger btn-sm float-right" onclick="f_get_data_table()" ><b>REGRESAR</b></button>
                    </h5>
                    <div class="row">
                        <div class="col-lg-6 col-sm-12 mt-2">
                            <div class="row">
                                <div class="col-12 text-center text-primary">
                                    <label for="cbhxMacho" >MACHOS</label>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-2 mt-2 text-center">
                                    <label > </label>
                                    <div class="icheck-primary mt-2">
                                        <input type="checkbox" id="cbhxMacho" ' . $checked2 . ' ' . $disabled2 . '>
                                        <label for="cbhxMacho"></label>
                                    </div>
                                </div>
                                <div class="col-5  mt-2">
                                    <label for="txtCantidad">RESTANTES:</label>
                                    <span class="text-muted form-control text-center"><b id="spnRestante">' . $restantemacho . '</b></span>
                                </div>
                                <div class="col-5 mt-2">
                                    <label for="txtCantidad">CANTIDAD:</label>
                                    <input type="text"  ' . $disabled2 . ' class="form-control text-center input_disablecopypaste" onkeypress="f_OnlyCant(event)" id="txtCantidad" value="' . $restantemacho . '" placeholder="2" maxlength="4">
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6 mt-2">
                            <div class="row">
                                <div class="col-12 text-center text-success">
                                    <label for="cbhxhembra" >HEMBRAS</label>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-2 mt-2 text-center">
                                    <label > </label>
                                    <div class="icheck-success mt-2">
                                        <input type="checkbox" id="cbhxhembra" ' . $checked1 . ' ' . $disabled1 . ' >
                                        <label for="cbhxhembra"></label>
                                    </div>
                                </div>
                                <div class="col-5  mt-2">
                                    <label for="txtCantidad">RESTANTES:</label>
                                    <span class="text-muted form-control text-center"><b id="spnRestante">' . $restantehembra . '</b></span>
                                </div>
                                <div class="col-5 mt-2">
                                    <label for="txtCantidad">CANTIDAD:</label>
                                    <input type="text"  ' . $disabled1 . ' class="form-control text-center input_disablecopypaste" onkeypress="f_OnlyCant(event)" id="txtCantidad2" value="' . $restantehembra . '" placeholder="2" maxlength="4">
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="form-group clearfix col-lg-6">
                            <label for="etpProductiva">
                                ETAPA PRODUCTIVA
                            </label>
                            <input type="text" id="txtEtpProductiva" pattern="(a-zA-Z\-)" class="form-control"> 
                        </div>
                        <div class="col-3 mt-3">
                            <div class="icheck-danger mt-3">
                                <input type="checkbox" id="chbDecomiso" class="mt-3">
                                <label for="chbDecomiso" class="mt-3">
                                    DECOMISO
                                </label>
                            </div>
                        </div>
                        <div class="col-3 mt-3">
                            <div class="icheck-danger mt-3">
                                <input type="checkbox" id="chbAprovechamiento" class="mt-3">
                                <label for="chbAprovechamiento" class="mt-3">
                                    APROVECHAMIENTO
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="form-group clearfix col-lg-6">
                            <div class="icheck-danger d-inline">
                                <input type="checkbox" id="chbAnimal">
                                <label for="chbAnimal">
                                    ANIMAL MUERTO
                                </label>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <textarea cols="1" rows="1" id="txtCausa" class="form-control"
                                placeholder="Posible causa de muerte"></textarea>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        ' . $items . '
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <div class="card ">
                                <div class="card-header bg-light" data-card-widget="collapse"
                                    style="cursor:pointer">
                                    <h3 class="card-title">
                                        <b>DICTAMEN</b>
                                    </h3>
                                </div>
                                <div class="card-body">
                                    <div class="form-group clearfix row">
                                        <div class="icheck-danger d-inline col-lg-3">
                                            <input type="radio" name="radioDictamen" checked="" value="0" id="radio1">
                                            <label for="radio1">
                                                FAENAMIENTO NORMAL
                                            </label>
                                        </div>
                                        <div class="icheck-danger d-inline col-lg-3">
                                            <input type="radio" name="radioDictamen" value="3" id="radio2">
                                            <label for="radio2">
                                                MATANZA BAJO PRECAUCIONES ESPECIALES
                                            </label>
                                        </div>
                                        <div class="icheck-danger d-inline col-lg-3">
                                            <input type="radio" name="radioDictamen" value="1" id="radio3">
                                            <label for="radio3">
                                                MATANZA DE EMERGENCIA
                                            </label>
                                        </div>
                                        <div class="icheck-danger d-inline col-lg-3">
                                            <input type="radio" name="radioDictamen" value="4" id="radio4">
                                            <label for="radio4">
                                                APLAZAMIENTO MATANZA
                                            </label>
                                        </div>
                                    </div>
                                    <textarea id="txtObservacion" cols="1" rows="4" class="form-control"
                                        placeholder="Ingrese una observación"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <center>
                        <Button class="btn btn-info" onclick="f_get_data_dictamen()"><b>GUARDAR INSPECCIÓN</b></Button>
                    </center>';
        } else return 'La guía seleccionada no contiene saldos'; //NO 
    } else "ERROR-212-212"; //NO SE ENCONTRO LA GUIA
}
function f_cabecera_item($dbConn)
{
    $resultado = "";
    $consulta = "SELECT * FROM tbl_a_cabeceraAM WHERE camEstado = 0";
    $sql = $dbConn->prepare($consulta);
    $sql->execute();
    $cont = 0;
    while ($row = $sql->fetch()) {
        $items = f_item($dbConn, $row["camId"]);
        if ($items != "") {
            $cont++;
            $resultado .= '<div class="col-lg-6">
                        <div class="card collapsed-card">
                            <div class="card-header bg-light" data-card-widget="collapse"
                                style="cursor:pointer">
                                <h3 class="card-title"  >
                                    <b id="h3Cabecera-' . $cont . '">' . utf8_encode($row["camDescripcion"]) . '</b>
                                    <input type="hidden" value="' . $row["camId"] . '" id="txtCabecera-' . $cont . '">
                                </h3>
                            </div>
                            <div class="card-body">
                            ' . $items . '
                            </div>
                        </div>
                    </div>';
        }
    }
    return $resultado . '<input type="hidden" value="' . $cont . '" id="cantidadcabeceras">';
}

function f_item($dbConn, $cabecera)
{
    $resultado = "";
    $consulta = "SELECT * FROM tbl_a_itemAM WHERE  iamEstado = 0 AND camId = :id";
    $sql = $dbConn->prepare($consulta);
    $sql->bindValue(':id', $cabecera);
    $sql->execute();
    $cont = 0;
    while ($row = $sql->fetch()) {
        // checked=""
        $cont++;
        $resultado .= '
        <div class="form-group clearfix">
            <div class="icheck-success d-inline">
                <input type="checkbox" id="chb-' . $cabecera . '-' . $cont . '" value="' . $row["iamId"] . '" >
                <label for="chb-' . $cabecera . '-' . $cont . '">' . utf8_encode($row["iamDescripcion"]) . '</label>
            </div>
        </div>';
    }
    return $resultado . '<input type="hidden" value="' . $cont . '" id="cantidaditem-' . $cabecera . '">';
}
function f_data_cantidad($dbConn)
{
    $genero = $_POST["Tipo"];
    $consulta = "SELECT *  FROM tbl_r_guiaproceso  WHERE gprId = :id   AND gprEliminado = 0 ";
    $sql = $dbConn->prepare($consulta);
    $sql->bindValue(':id', $_SESSION['ANTEMORTEM'][1]);
    $sql->execute();
    if ($row = $sql->fetch()) {
        if (f_obtener_procesar($dbConn, $row["gprId"]) == 0) {
            $total = 0;
            if ($genero == 0) $total = $row["gprHembra"];
            else if ($genero == 1) $total = $row["gprMacho"];
            else return 'ERROR-212';
            $array = f_obtener_cantidades_2($dbConn, $row["gprId"], $genero);
            if ($array == false) return 'ERROR-81728712';
            $restante = $total - $array[0] - $array[1] - $array[2];
            if ($restante == 0) return 'Se alcanzo el número maximo para este genero';
            else return $restante;
        } else return 'La guía seleccionada no contiene saldos'; //NO 
    } else "ERROR-212-212"; //NO SE ENCONTRO LA GUIA
}










function f_inster_dictamen($dbConn)
{
    try {
        $hembra = $_POST["Hembra"];
        $macho = $_POST["Macho"];
        if ($hembra == 0 && $macho == 0) return 'Las dos cantidades no pueden ser 0';
        $etapaProductiva = $_POST["EtapaProductiva"];
        
        $dictamen = $_POST["Dictamen"];
        if ($dictamen == 0 || $dictamen == 1 || $dictamen == 2 || $dictamen == 3 || $dictamen == 4);
        else return 'El dictamen especificado no se encuentra registrado';
        $animal = $_POST["Animal"];
        if ($animal == 0 || $animal == 1);
        else return 'El estado de animal es incorrecto';
        $causa = null;
        if ($animal == 1) {
            $causa = trim($_POST["Causa"]);
            if ($causa == '') return 'Ingrese la posible causa';
        }
        $decomiso = $_POST["EtapaProductiva"];
        $aprovechamiento = $_POST["Aprovechamiento"];
        $observacion = trim($_POST["Observaciones"]);
        $ArrayDetalle = $_POST["ArrayDeta"];
        $consulta = "SELECT *  FROM tbl_r_guiaproceso  WHERE gprId = :id   AND gprEliminado = 0 ";
        $sql = $dbConn->prepare($consulta);
        $sql->bindValue(':id', $_SESSION['ANTEMORTEM'][1]);
        $sql->execute();
        if ($row = $sql->fetch()) {
            if (f_obtener_procesar($dbConn, $row["gprId"]) == 0) {
                $total = 0;
                if ($macho != 0)

                    // else if ($tipo == 1) $total = $row["gprMacho"];


                    // $array1 = f_obtener_cantidades($dbConn,$row["gprId"]);
                    // if ($array1 == false)return 'ERROR-81728712';
                    // $restante2 = ($row["gprMacho"] + $row["gprHembra"]) - $array1[0] - $array1[1] -$array1[2];
                    // $array_detalle = buscar_animal($dbConn,$row["gprId"]);
                    // if($restante2 != count($array_detalle))return 'Error de paretenezo de Restantes '.count($array_detalle)." ".$restante2 ;
                    if ($macho > 0) {
                        $tipo = 1;
                        $total = $row["gprMacho"];
                        $array = f_obtener_cantidades_2($dbConn, $_SESSION['ANTEMORTEM'][1], $tipo);
                        if ($array == false) return 'ERROR-81728712';
                        $restante = $total - $array[0] - $array[1] - $array[2];
                        if ($macho > $restante) return 'La cantidad de <b>Machos</b> ingresada sobrepasa la cantidad restante';
                        if (!F_insertar_inspeccion($dbConn, $macho, $etapaProductiva, $tipo, $animal, $causa, $decomiso, $aprovechamiento, $dictamen, $observacion, $row["gprId"], $ArrayDetalle, $row["gprComprobante"])) return 'No se puedo ingresar el registro antemortem de Machos';
                    }
                if ($hembra > 0) {
                    $tipo = 0;
                    $total = $row["gprHembra"];
                    $array = f_obtener_cantidades_2($dbConn, $_SESSION['ANTEMORTEM'][1], $tipo);
                    if ($array == false) return 'ERROR-81728712';
                    $restante = $total - $array[0] - $array[1] - $array[2];
                    if ($hembra > $restante) return 'La cantidad de <b>Hembras</b> ingresada sobrepasa la cantidad restante';
                    if (!F_insertar_inspeccion($dbConn, $hembra, $etapaProductiva, $tipo, $animal, $causa, $decomiso, $aprovechamiento, $dictamen, $observacion, $row["gprId"], $ArrayDetalle, $row["gprComprobante"])) return 'No se puedo ingresar el registro antemortem de Hembras';
                }
                return true;
            } else return 'La guía seleccionada no contiene saldos'; //NO 
        } else "ERROR-212-212"; //NO SE ENCONTRO LA GUIA
    } catch (Exception $e) {
        Insert_Error('ERROR-23333', $e->getMessage(), 'Error al ingresar la inspeción de ANTEMORTEM');
        exit("ERROR-23333");
    }
}
function F_insertar_inspeccion($dbConn, $cantidad, $etapaProductiva, $tipo, $animal, $causa, $decomiso, $aprovechamiento, $dictamen, $observacion, $Id, $ArrayDetalle, $comprobante)
{
    global $User;
    global $Ip;
    $consulta = "INSERT INTO tbl_p_antemortem(antCantidad,antEtapaProductiva,antFecha,antSexo,antEstadoAnimal,antCausa,antDecomiso,antAprovechamiento,antDictamen,antObservaciones,gprId,usuId,ip)
    VALUES(:antCantidad,:antEtapaProductiva,:antFecha,:antSexo,:antEstadoAnimal,:antCausa,:antDecomiso,:antAprovechamiento,:antDictamen,:antObservaciones,:gprId,:usuId,:ip)";
    $sql1 = $dbConn->prepare($consulta);
    $sql1->bindValue(':antCantidad', $cantidad);
    $sql1->bindValue(':antEtapaProductiva', $etapaProductiva);
    $sql1->bindValue(':antFecha', date("Y-m-d H:i:s"));
    $sql1->bindValue(':antSexo', $tipo);
    $sql1->bindValue(':antEstadoAnimal', $animal);
    $sql1->bindValue(':antCausa', $causa);
    $sql1->bindValue(':antDecomiso', $decomiso);
    $sql1->bindValue(':antAprovechamiento', $aprovechamiento);
    $sql1->bindValue(':antDictamen', $dictamen);
    $sql1->bindValue(':antObservaciones', utf8_decode($observacion));
    $sql1->bindValue(':gprId', $Id);
    $sql1->bindValue(':usuId', $User);
    $sql1->bindValue(':ip', $Ip);
    if ($sql1->execute()) {
        $Id = $dbConn->lastInsertId();
        // for ($i = 0; $i < $cantidad ; $i++) { 
        //     if(f_update_animal($dbConn,$dictamen,$array_detalle[$i])==false)return 'NO SE PUDO ACTUALIZAR EL DICTAMEN DEL ANIMAL';
        // }
        if ($ArrayDetalle != "false") {
            for ($i = 0; $i < count($ArrayDetalle); $i++) {
                if (buscar_item($dbConn, $ArrayDetalle[$i], $Id) == false) return 'NO SE PUEDO INGRESAR TODOS LOS ITEMS';
            }
        }
        $mensaje_dictamen = "";
        // if ($dictamen == 0) $mensaje_dictamen = "FAENAMIENTO NORMAL";
        if ($dictamen == 0) $mensaje_dictamen = "MATANZA NORMAL";
        // else if ($dictamen == 1) $mensaje_dictamen = "SACRIFICIO URGENTE";
        else if ($dictamen == 1) $mensaje_dictamen = "MATANZA DE EMERGENCIA";
        else if ($dictamen == 2) $mensaje_dictamen = "SACRIFICIO SANITARIO";
        else if ($dictamen == 3) $mensaje_dictamen = "MATANZA BAJO PRECAUCIONES ESPECIALES";
        else if ($dictamen == 4) $mensaje_dictamen = "APLAZAMIENTO DE MATANZA";

        $mensaje_tipo = '';
        if ($tipo == 0) $mensaje_tipo = 'HEMBRA';
        else if ($tipo == 0) $mensaje_tipo = 'MACHO';

        $Acion = 'Nueva inspección ANTEMORTEM';
        $detalle = 'Dictamen <b>' . $mensaje_dictamen . '</b><br> Comprobante <b>' . $comprobante . '</b><br> Cantidad <b>' . $cantidad . '</b><br> Tipo: <b>' . $mensaje_tipo . '</b> ';
        return Insert_Login($Id, 'tbl_p_antemortem', $Acion, $detalle, '');
    } else return "ERROR-665242"; //
}


function buscar_item($dbConn, $Item, $Id)
{
    $consulta = "SELECT * FROM tbl_a_itemAM i, tbl_a_cabeceraAM c  WHERE i.camId = c.camId AND i.iamId = :id";
    $sql = $dbConn->prepare($consulta);
    $sql->bindValue(':id', $Item);
    $sql->execute();
    if ($row = $sql->fetch()) {
        return f_insertar_detalle($dbConn, utf8_encode($row["iamDescripcion"]), utf8_encode($row["camDescripcion"]), $Item, $Id);
    } else "ERROR-212-212"; //NO SE ENCONTRO LA GUIA
}

function buscar_animal($dbConn, $Id)
{
    $Aray = array();
    $consulta = "SELECT * FROM tbl_r_detalle WHERE gprId = :id AND dtDictamen IS NULL ORDER BY dtId ASC";
    $sql = $dbConn->prepare($consulta);
    $sql->bindValue(':id', $Id);
    $sql->execute();
    $cont = 0;
    while ($row = $sql->fetch()) {
        array_push($Aray, $row["dtId"]);
    }
    return $Aray;
}
function get_data_especie($dbConn, $Id)
{
    $consulta = "SELECT espDescripcion FROM tbl_a_especies WHERE espId = :id";
    $sql = $dbConn->prepare($consulta);
    $sql->bindValue(':id', $Id);
    $sql->execute();
    if ($row = $sql->fetch()) return $row["espDescripcion"];
    else return 'NO ENCONTRADO';
}



function f_insertar_detalle($dbConn, $descipion, $Cabecera, $Item, $Id)
{
    try {
        $consulta = "INSERT INTO tbl_p_detalle_ANT(dtaDescripcion,dtaCabecera,iamId,antId) 
        VALUES (:dtaDescripcion,:dtaCabecera,:iamId,:antId)";
        $sql = $dbConn->prepare($consulta);
        $sql->bindValue(':dtaDescripcion', utf8_decode($descipion));
        $sql->bindValue(':dtaCabecera', utf8_decode($Cabecera));
        $sql->bindValue(':iamId', $Item);
        $sql->bindValue(':antId', $Id);
        if ($sql->execute()) return true;
        else return false;
    } catch (Exception $e) {
        Insert_Error('ERROR-887222', $e->getMessage(), 'ERROR AL INGRESAR EL DETALLA');
        exit("ERROR-887222");
    }
}

function f_update_animal($dbConn, $dictamen, $Id)
{
    try {
        $consulta = "UPDATE tbl_r_detalle SET dtDictamen = :dictamen WHERE dtId  = :id";
        $sql = $dbConn->prepare($consulta);
        $sql->bindValue(':dictamen', $dictamen);
        $sql->bindValue(':id', $Id);
        if ($sql->execute()) return true;
        else return false;
    } catch (Exception $e) {
        Insert_Error('ERROR-887222', $e->getMessage(), 'ERROR AL INGRESAR EL DETALLA');
        exit("ERROR-887222");
    }
}


















function return_modal_header($titutlo)
{
    return ' <div class="modal-header">
                <h5 class="modal-title">' . $titutlo . '</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>';
}
function return_modal_body($data)
{
    return '<div class="modal-body">' . $data . '</div>';
}
function return_modal_foot($funtion)
{
    return '<div class="modal-footer">
                <button type="button" class="btn btn-primary" onclick="' . $funtion . '"><b>GUARDAR</b></button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal" id="btnCerrar" >CANCELAR</button>
            </div>';
}
