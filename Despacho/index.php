<?php
if (isset($_REQUEST["EPMRQ-OPCION"])) {

    $op = $_REQUEST["EPMRQ-OPCION"];
    if ($op == md5("1")) echo Fecha_Vista_Servidor();
    elseif($op == md5("2")) CalcularJuliano();
    elseif($op == md5("3"))  echo Numero_Canal();
    elseif($op == md5("4"))  echo date("Y-m-d H:i:s");
    elseif($op == md5("5"))  echo date("d/m/Y");
    elseif($op == md5("6")) echo Consultar_UltimoId();
    elseif($op == md5("7")) echo Numero_Ordinal();
    elseif($op == md5("8")) echo Crear_Canal2();
    elseif($op == md5("9")) echo date("Y-m-d");
    elseif($op == md5("10")) echo return_restante_despacho();
    elseif($op == md5("11")) echo cant_despachados();
    elseif($op == md5("12")) echo Consultar_UltimoId_Exterior();
    elseif($op == md5("13")) {
        require '../FilePHP/utils.php';
        $conn = conectar($db);
        $Codigo = $_GET["EPMRQ-Orden"];//Esta variable trae el id del producto
        echo partes_producto($conn,$Codigo);
    }
    else echo "NIGUNA OPCIONss";
}else{
    
}



function Fecha_Vista_Servidor(){
    $arrayDia = array('1' => 'Lunes',
            '2' => 'Martes',
            '3' => 'Miércoles',
            '4' => 'Jueves',
            '5' => 'Viernes',
            '6' => 'Sábado',
            '7' => 'Domingo');
    $numDia=date("N");
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
    $numMes=date("n");
    $fecha=$arrayDia[$numDia].", ".date("d").
        " de ".$arrayMes[$numMes]." de ".date("Y");
    return $fecha;

}
function CalcularJuliano(){
    $numMes=date("n");
    $total=0;
    $anio = date("Y");
    for ($i=1; $i < $numMes ; $i++) {
        $fecha= date("t", strtotime($anio."-$i"));
        $total = $total + intval($fecha);
    }
    $total = $total + intval(date("d"));
    echo "$total".date("y");
}
function Numero_Canal(){
    require '../FilePHP/utils.php';
    $conn = conectar($db);
    $Cedula = $_GET["EPMRQ-Cedula"];
    $Codigo = $_GET["EPMRQ-Codigo"];
    $privilegio = Privilegio_Usuario($conn,$Cedula,$Codigo);
    $especie = id_Especie($conn,$Codigo);
    $parte = partes_producto($conn,$Codigo);
    if ($privilegio == "0"){
        $consulta = "SELECT p.pesCanal,pr.proPartes FROM tbl_d_pesaje p, tbl_a_productos pr 
        WHERE p.proId = pr.proId AND pr.espId = :id AND p.pesFecha BETWEEN :inicio AND :final 
        AND p.pesEliminado = 0 ORDER BY p.pesId DESC LIMIT 1";
        $sql= $conn->prepare($consulta);
        $sql->bindValue(':id',$especie);
        $sql->bindValue(':inicio',date("Y-m-d")." 00:00:00");
        $sql->bindValue(':final',date("Y-m-d")." 23:59:59");
        $sql->execute();
        if($row = $sql->fetch())return new_canal($row["pesCanal"],$parte,0);//Secuencia
        else return new_canal(0,$parte,0);//PRIMERO
    }else if ($privilegio == "1"){
        $array = array_permisos($conn,$Cedula);
        $consulta = "SELECT p.pesCanal,p.proId ,pr.proPartes
        FROM tbl_d_pesaje p, tbl_p_orden o, tbl_r_guiaproceso g, tbl_a_clientes c, tbl_a_productos pr
        WHERE p.ordId = o.ordId AND o.gprId = g.gprId AND g.cliId = c.cliId AND p.proId = pr.proId AND
        g.cliId = :cl AND p.pesFecha BETWEEN :inicio AND :final AND p.pesEliminado = 0 ORDER BY p.pesId DESC";
        $sql= $conn->prepare($consulta);
        $sql->bindValue(':cl',$Cedula);
        $sql->bindValue(':inicio',date("Y-m-d")." 00:00:00");
        $sql->bindValue(':final',date("Y-m-d")." 23:59:59");
        $sql->execute();
        while($row = $sql->fetch()){
            for ($i=0; $i < count($array) ; $i++) { 
                if ($row["proId"] == $array[$i]) {
                    if ($row["proId"] == $Codigo) return new_canal($row["pesCanal"],$parte,0);//Productos iguales
                    else return new_canal($row["pesCanal"],$parte,0);// Productos diferentes
                }
            }
        }
        return new_canal(0,$parte,1);// primero
    }
}
function new_canal($canal,$parte,$new){
    $ArrayCanales = array('A','B','C','D','E','F','G','H','I','J');
    $arrayCanal = explode("-",$canal);
    if (count($arrayCanal)==2) {
        if ($new == 1)return (intval($arrayCanal[0]) + 1).'-'.$ArrayCanales[0];
        if ($parte > 1) {
            for ($i=0; $i < intval($parte) ; $i++) { 
                if ($ArrayCanales[$i] > $arrayCanal[1]) return $arrayCanal[0].'-'.$ArrayCanales[$i];  
            }
            return  ($arrayCanal[0] + 1).'-'.$ArrayCanales[0];
        }else {
            return $arrayCanal[0] + 1;
        }
    }else{
        if (intval($parte) > 1 ) return (intval($canal) + 1).'-'.$ArrayCanales[0];
        else return intval($canal) + 1;
    }
}
function array_permisos($conn,$Cliente){
    $result = [];
    $consulta = "SELECT proId  FROM tbl_permisos_clientes WHERE cliId = :cl";
    $sql= $conn->prepare($consulta);
    $sql->bindValue(':cl',$Cliente);
    $sql->execute();
    while($row = $sql->fetch()) array_push($result,$row["proId"]);
    return $result;
}
function partes_producto($conn,$producto){
    $consulta = "SELECT proPartes FROM tbl_a_productos WHERE proId = :id";
    $sql= $conn->prepare($consulta);
    $sql->bindValue(':id',$producto);
    $sql->execute();
    if($row = $sql->fetch()) return $row["proPartes"];
    else return 1;
}
function id_Especie($conn,$producto){
    $consulta = "SELECT espId FROM tbl_a_productos WHERE proId = :id";
    $sql= $conn->prepare($consulta);
    $sql->bindValue(':id',$producto);
    $sql->execute();
    if($row = $sql->fetch()) return $row["espId"];
}
function Privilegio_Usuario($conn,$cliente,$producto){
    $result = "0";
    $consulta = "SELECT * FROM tbl_permisos_clientes WHERE proId = :pr AND cliId = :cl";
    $sql= $conn->prepare($consulta);
    $sql->bindValue(':pr',$producto);
    $sql->bindValue(':cl',$cliente);
    $sql->execute();
    if($row = $sql->fetch()) return "1";
    return "0";
}
function Crear_Canal2(){
    require '../FilePHP/utils.php';
    $conn = conectar($db);
    $Codigo = $_GET["EPMRQ-Codigo"];
    $parte = partes_producto($conn,$Codigo);
    $consulta = "SELECT e.pexCanal, p.proPartes FROM tbl_d_pesaje_exterior e, tbl_a_productos p
    WHERE e.proId = p.proId AND  e.proId = :id AND e.pexFecha BETWEEN :inicio AND :final AND e.pexEliminado = 0
    ORDER BY e.pexId DESC LIMIT 1";
    $sql= $conn->prepare($consulta);
    $sql->bindValue(':id',$Codigo);
    $sql->bindValue(':inicio',date("Y-m-d")." 00:00:00");
    $sql->bindValue(':final',date("Y-m-d")." 23:59:59");
    $sql->execute();
    if($row = $sql->fetch())return new_canal($row["pexCanal"],$row["proPartes"],0);//Secuencia
    else return new_canal(0,$parte,0);//PRIMERO
}

function return_restante_despacho(){
    require '../FilePHP/utils.php';
    $conn = conectar($db);
    $Orden = $_GET["EPMRQ-Orden"];
    $cant = cant_total($conn,$Orden);
    $dec = cant_decomiso($conn,$Orden);
    $res = $cant - $dec;
    return number_format($res, 2);
}
function cant_total($conn,$Id){
    $consulta = "SELECT ordCantidad FROM tbl_p_orden WHERE ordId = :id";
    $sql= $conn->prepare($consulta);
    $sql->bindValue(':id',$Id);
    $sql->execute();
    if($row = $sql->fetch())return $row["ordCantidad"];
}
function cant_decomiso($conn,$Id){
    $consulta = "SELECT decCantidad,decPartes FROM tbl_p_decomiso WHERE ordId = :id AND proId IS NOT NULL ";
    $sql= $conn->prepare($consulta);
    $sql->bindValue(':id',$Id);
    $sql->execute();
    $cant = 0;
    while($row = $sql->fetch()){
        $cant += ($row["decCantidad"] / $row["decPartes"]);
    }
    return $cant;
}
function cant_despachados(){
    require '../FilePHP/utils.php';
    $conn = conectar($db);
    $Orden = $_GET["EPMRQ-Orden"];
    $consulta = "SELECT pesProPartes  FROM tbl_d_pesaje WHERE ordId = :id AND pesEliminado = 0";
    $sql= $conn->prepare($consulta);
    $sql->bindValue(':id',$Orden);
    $sql->execute();
    $cant = 0;
    while($row = $sql->fetch()){
        $cant += (1/ $row["pesProPartes"]);
    }
    return number_format($cant, 2);;
}

function Numero_Ordinal(){
    require '../FilePHP/utils.php';
    $conn = conectar($db);
    $Cedula = $_GET["EPMRQ-Cedula"];
    $Codigo = $_GET["EPMRQ-Codigo"];
    $parte = partes_producto($conn,$Codigo);
    $consulta = "SELECT p.pesCanal,pr.proPartes,p.pesOrdinal
        FROM tbl_d_pesaje p, tbl_p_orden o, tbl_r_guiaproceso g, tbl_a_clientes c, tbl_a_productos pr
        WHERE p.ordId = o.ordId AND o.gprId = g.gprId AND g.cliId = c.cliId AND p.proId = pr.proId AND
        g.cliId = :cl AND p.pesFecha BETWEEN :inicio AND :final AND p.pesEliminado = 0 ORDER BY p.pesId DESC";
    $sql= $conn->prepare($consulta);
    $sql->bindValue(':cl',$Cedula);
    $sql->bindValue(':inicio',date("Y-m-d")." 00:00:00");
    $sql->bindValue(':final',date("Y-m-d")." 23:59:59");
    $sql->execute();
    if($row = $sql->fetch())return new_ordinal($row["pesCanal"],$parte,$row["pesOrdinal"]);//Secuencia
    else return new_ordinal(0,$parte,0);//PRIMERO
}
function new_ordinal($canal,$parte,$num){
    $ArrayCanales = array('A','B','C','D','E','F','G','H','I','J');
    $arrayCanal = explode("-",$canal);
    if (count($arrayCanal)==2) {
        for ($i=0; $i < intval($parte) ; $i++) { 
            if ($ArrayCanales[$i] > $arrayCanal[1]) return $num;  
        }
        return  $num + 1;
    }else{
        if (intval($parte) > 1 ) return (intval($num) + 1);
        else return intval($num) + 1;
    }
}
function Consultar_UltimoId(){
    require '../FilePHP/utils.php';
    $conn = conectar($db);
    $Codigo = $_GET["EPMRQ-Codigo"];
    $Cedula = $_GET["EPMRQ-Cedula"];
    $privilegio = Privilegio_Usuario($conn,$Cedula,$Codigo);
    if ($privilegio=="0") {
        $consulta = "SELECT p.pesId FROM tbl_d_pesaje p, tbl_a_productos pr 
        WHERE p.proId = pr.proId AND p.proId = :id AND p.pesFecha BETWEEN :inicio AND :final 
        AND p.pesEliminado = 0 ORDER BY p.pesId DESC LIMIT 1";
        $sql= $conn->prepare($consulta);
        $sql->bindValue(':id',$Codigo);
        $sql->bindValue(':inicio',date("Y-m-d")." 00:00:00");
        $sql->bindValue(':final',date("Y-m-d")." 23:59:59");
        $sql->execute();
        if($row = $sql->fetch())return $row["pesId"];
        else return 0;
    }elseif ($privilegio=="1") {
        $array = array_permisos($conn,$Cedula);
        $consulta = "SELECT p.pesCanal,p.proId ,pr.proPartes, p.pesId
        FROM tbl_d_pesaje p, tbl_p_orden o, tbl_r_guiaproceso g, tbl_a_clientes c, tbl_a_productos pr
        WHERE p.ordId = o.ordId AND o.gprId = g.gprId AND g.cliId = c.cliId AND p.proId = pr.proId AND
        g.cliId = :cl AND p.pesFecha BETWEEN :inicio AND :final AND p.pesEliminado = 0 ORDER BY p.pesId DESC";
        $sql= $conn->prepare($consulta);
        $sql->bindValue(':cl',$Cedula);
        $sql->bindValue(':inicio',date("Y-m-d")." 00:00:00");
        $sql->bindValue(':final',date("Y-m-d")." 23:59:59");
        $sql->execute();
        while($row = $sql->fetch()){
            for ($i=0; $i < count($array) ; $i++) { 
                if ($row["proId"] == $array[$i]) {
                    if ($row["proId"] == $Codigo) return $row["pesId"];
                    else return 0;
                }
            }
        }
        return 0;
    }
}
function Consultar_UltimoId_Exterior(){
    require '../FilePHP/utils.php';
    $conn = conectar($db);
    $Codigo = $_GET["EPMRQ-Codigo"];
    $Cedula = $_GET["EPMRQ-Cedula"];
    $consulta = "SELECT pexId FROM tbl_d_pesaje_exterior 
    WHERE proId = :pro AND pexEliminado = 0 AND pexFecha 
    BETWEEN :inicio AND :final ORDER by pexId DESC LIMIT 1";
    $sql= $conn->prepare($consulta);
    $sql->bindValue(':pro',$Codigo);
    $sql->bindValue(':inicio',date("Y-m-d")." 00:00:00");
    $sql->bindValue(':final',date("Y-m-d")." 23:59:59");
    $sql->execute();
    if($row = $sql->fetch())return $row["pexId"];
    else return 0;
}







// // // // // // // // // // // // // // // // 
function Numero_Canals(){
    $resultado = "";
    $conn = conectar($db);
    $Cedula = $_GET["EPMRQ-Cedula"];
    $Codigo = $_GET["EPMRQ-Codigo"];
    $privilegio = Privilegio_Usuario($conn,$Cedula,$Codigo);
    if ($privilegio == "0") {
        $Finicio= date("Y-m-d")." 00:00:00";
        $Ffinal= date("Y-m-d")." 23:59:59";
        $Tipo = Traer_Tipo_Producto($Codigo,$conn);
        $consulta = "SELECT d.denCanal FROM tbl_despacho_normal_new d, tbl_clientes c
        WHERE d.ruc = c.ruc AND c.privilegio = 0 AND d.proCodigo = :codigo AND 
        d.denFecha BETWEEN :inicio AND :final AND 
        d.denEliminar = 0 ORDER BY d.denFecha DESC LIMIT 1";
        $sql= $conn->prepare($consulta);
        $sql->bindValue(':codigo',$Codigo);
        $sql->bindValue(':inicio',$Finicio);
        $sql->bindValue(':final',$Ffinal);
        $sql->execute();
        $cont=0;
        if($row = $sql->fetch()) {
            $cont++;
            $resultado = Crear_Numero($Tipo,$row["denCanal"]);
        }
        if ($cont==0) {
            $resultado = Crear_Numero($Tipo,"cero");
        }
        echo $resultado;
    }elseif($privilegio == "1") {
        $Finicio= date("Y-m-d")." 00:00:00";
        $Ffinal= date("Y-m-d")." 23:59:59";
        $consulta = "SELECT denCanal,denOrdinal,proCodigo FROM tbl_despacho_normal_new 
        WHERE ruc = :cedula AND denFecha BETWEEN :inicio AND :final AND denEliminar = 0 
        ORDER BY denFecha DESC LIMIT 1";
        $sql= $conn->prepare($consulta);
        $sql->bindValue(':cedula',$Cedula);
        $sql->bindValue(':inicio',$Finicio);
        $sql->bindValue(':final',$Ffinal);
        $sql->execute();
        $bandera = false;
        $tipo_anterior ="";
        $codigo_anterior="";
        $canal ="";
        if($row = $sql->fetch()) {
            $bandera = true;
            $resultado=  $row["denOrdinal"];
            $codigo_anterior=  $row["proCodigo"];
            $canal =$row["denCanal"];
        }
        $tipo_nuevo = Traer_Tipo_Producto($Codigo,$conn);
        if ($bandera) {
            $tipo_anterior = Traer_Tipo_Producto($codigo_anterior,$conn);
            if ($tipo_anterior == "COMPLETO" && $tipo_nuevo=="COMPLETO") {
                $resultado = $resultado + 1;
            }elseif($tipo_anterior=="COMPLETO" && $tipo_nuevo=="MEDIO") {
                $resultado = $resultado + 1;
                $resultado = $resultado.'-A';
            }elseif($tipo_anterior=="MEDIO" && $tipo_nuevo=="COMPLETO") {
                $resultado = $resultado + 1;
            }elseif($tipo_anterior=="MEDIO" && $tipo_nuevo=="MEDIO") {
                if ($codigo_anterior == $Codigo) {
                    $array = explode("-",$canal);
                    if ($array[1]=='A') {
                            $resultado = $resultado.'-B';
                    }else{
                            $resultado = $resultado + 1;
                            $resultado = $resultado.'-A';
                    }
                }else {
                    $resultado = $resultado + 1;
                    $resultado = $resultado.'-A';
                }
            }
        }else{
            if ($tipo_nuevo=="COMPLETO") {
                $resultado = $resultado + 1;
            }elseif ($tipo_nuevo=="MEDIO") {
                $resultado = $resultado + 1;
                $resultado = $resultado.'-A';
            }
            
        } 
        echo $resultado;
    }
    
}
function Traer_Tipo_Producto($code,$conn){
    $result = "INEXISTENTE";
    $consulta = "SELECT proTipo FROM tbl_productos_new WHERE proCodigo=:codigo";
    $sql= $conn->prepare($consulta);
    $sql->bindValue(':codigo',$code);
    $sql->execute();
    if($row = $sql->fetch()) {
        $result = $row["proTipo"];
    }
    return $result;
}


function Consultar_UltimoId2(){
    $resultado = "";
    $conn = Coneccion();
    $Codigo = $_GET["EPMRQ-Codigo"];
    $Cedula = $_GET["EPMRQ-Cedula"];
    $privilegio = Privilegio_Usuario($conn,$Cedula,$Codigo);
    if ($privilegio=="0") {
        $Finicio= date("Y-m-d")." 00:00:00";
        $Ffinal= date("Y-m-d")." 23:59:59";
        $consulta = "SELECT d.denId FROM tbl_despacho_normal_new d, tbl_clientes c
        WHERE d.ruc = c.ruc AND c.privilegio = 0 AND d.proCodigo = :codigo AND 
        d.denFecha BETWEEN :inicio AND :final AND 
        d.denEliminar = 0 ORDER BY d.denFecha DESC LIMIT 1";
        $sql= $conn->prepare($consulta);
        $sql->bindValue(':codigo',$Codigo);
        $sql->bindValue(':inicio',$Finicio);
        $sql->bindValue(':final',$Ffinal);
        $sql->execute();
        $cont=0;
        if($row = $sql->fetch()) {
            $cont++;
            $resultado=  $row["denId"];
        }
        if ($cont==0) {
            $resultado=  0;
        }
        echo $resultado;
    }elseif ($privilegio=="1") {
        $Finicio= date("Y-m-d")." 00:00:00";
        $Ffinal= date("Y-m-d")." 23:59:59";
        $consulta = "SELECT denId FROM tbl_despacho_normal_new WHERE ruc = :cedula AND denFecha BETWEEN :inicio AND :final AND denEliminar = 0 ORDER BY denFecha DESC LIMIT 1";
        $sql= $conn->prepare($consulta);
        $sql->bindValue(':cedula',$Cedula);
        $sql->bindValue(':inicio',$Finicio);
        $sql->bindValue(':final',$Ffinal);
        $sql->execute();
        $cont=0;
        if($row = $sql->fetch()) {
            $cont++;
            $resultado=  $row["denId"];
        }
        if ($cont==0) {
            $resultado=  0;
        }
        echo $resultado;
    }
}
function Crear_Ordinal(){
    $resultado = 0;
    $conn = Coneccion();
    $Cedula = $_GET["EPMRQ-Cedula"];
    $Codigo_nuevo = $_GET["EPMRQ-Codigo"];
    $Finicio= date("Y-m-d")." 00:00:00";
    $Ffinal= date("Y-m-d")." 23:59:59";
    $consulta = "SELECT denCanal,denOrdinal,proCodigo FROM tbl_despacho_normal_new WHERE ruc = :cedula AND denFecha BETWEEN :inicio AND :final AND denEliminar = 0 ORDER BY denFecha DESC LIMIT 1";
    $sql= $conn->prepare($consulta);
    $sql->bindValue(':cedula',$Cedula);
    $sql->bindValue(':inicio',$Finicio);
    $sql->bindValue(':final',$Ffinal);
    $sql->execute();
    $bandera = false;
    $tipo_anterior ="";
    $codigo_anterior="";
    $canal ="";
    if($row = $sql->fetch()) {
        $bandera = true;
        $resultado=  $row["denOrdinal"];
        $codigo_anterior=  $row["proCodigo"];
        $canal =$row["denCanal"];
    }
    if ($bandera) {
        $tipo_anterior = Traer_Tipo_Producto($codigo_anterior,$conn);
        $tipo_nuevo = Traer_Tipo_Producto($Codigo_nuevo,$conn);
        if ($tipo_anterior == "COMPLETO" && $tipo_nuevo=="COMPLETO") {
            $resultado = $resultado + 1;
        }elseif($tipo_anterior=="COMPLETO" && $tipo_nuevo=="MEDIO") {
            $resultado = $resultado + 1;
        }elseif($tipo_anterior=="MEDIO" && $tipo_nuevo=="COMPLETO") {
            $resultado = $resultado + 1;
        }elseif($tipo_anterior=="MEDIO" && $tipo_nuevo=="MEDIO") {
            if ($codigo_anterior == $Codigo_nuevo) {
                $array = explode("-",$canal);
                if ($array[1]=='A') {
                        $resultado = $resultado;
                }else{
                        $resultado = $resultado + 1;
                }
            }else {
                $resultado = $resultado + 1;
            }
        }
    }else{
        $resultado = $resultado + 1;
    } 
    echo $resultado;
}




function Crear_Canals2(){
    $conn = Coneccion();
    $Codigo = $_GET["EPMRQ-Codigo"];
    $Finicio= date("Y-m-d")." 00:00:00";
    $Ffinal= date("Y-m-d")." 23:59:59";
    $Tipo = Traer_Tipo_Producto($Codigo,$conn);
    $consulta = "SELECT deeCanal FROM tbl_despacho_exterior_new WHERE proCodigo = :codigo AND 
    deeFecha BETWEEN :inicio AND :final ORDER BY deeFecha DESC LIMIT 1";
    $sql= $conn->prepare($consulta);
    $sql->bindValue(':codigo',$Codigo);
    $sql->bindValue(':inicio',$Finicio);
    $sql->bindValue(':final',$Ffinal);
    $sql->execute();
    $cont=0;
    if($row = $sql->fetch()) {
        $cont++;
        $resultado = Crear_Numero($Tipo,$row["deeCanal"]);
    }
    if ($cont==0) {
        $resultado = Crear_Numero($Tipo,"cero");
    }
    echo $resultado;
}
?>
