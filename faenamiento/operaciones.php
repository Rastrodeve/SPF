<?php
$ArrayCanales = array('A','B','C','D','E','F','G','H','I','J');
if (isset($_REQUEST["op"])) {
    require '../FilePHP/utils.php';
	$dbConn = conectar($db);
	$op=$_REQUEST['op'];
	if ($op==1)echo insert($dbConn);
}
function insert($dbConn){
    $Id  = $_POST["Codigo"];
    // $Id  = "V001";
    $cont = 0;
    $Codigo_Producto = "";
    $Partes_Producto = "";
    $Id_Detalle = "";
    $Linea = "";
    $Id_contador = "";
    $Ruc = "";
    $Guia_numero = "";
    $tipo = "";
    $procesado = "";
    $consulta="SELECT d.dtId,d.dtCodigo,pr.proPartes,pr.proId,de.daeLinea,p.id_contador,p.ruc,p.guia_numero,p.tipo,p.procesado FROM tbl_detalle_guia_new d
    JOIN tbl_guia_proceso p  ON d.id_contador = p.id_contador
    JOIN tbl_productos_new pr ON d.proId = pr.proId
    JOIN tbl_datos_especieanimales_new de ON pr.codigo_especieanimales = de.codigo_especieanimales
    WHERE d.dtCodigo = :id AND d.dtProceso = 0";
    $sql= $dbConn->prepare($consulta);
    $sql->bindValue(':id',$Id);
    $sql->execute();
    while($row = $sql->fetch()) {
        $cont++;
        $Codigo_Producto = $row["proId"];
        $Partes_Producto = $row["proPartes"];
        $Id_Detalle = $row["dtId"];
        $Linea = $row["daeLinea"];
        $Id_contador = $row["id_contador"];
        $Ruc = $row["ruc"];
        $Guia_numero = $row["guia_numero"];
        $tipo = $row["tipo"];
        $procesado = $row["procesado"];
    }
    if ($cont==0) {
        return "No se econtraron resultados";
    }elseif($cont==1){
        global $ArrayCanales;
        $contador2 =0;
        $Fecha = date("Y-m-d H:i:s");
        $Ordinal = get_Ordinal($Linea,$dbConn);
        $Codigo_epmrq  = $Linea.'EPMRQ'.$Id.'-'.CalcularJuliano().'-'.$Ordinal;//1EPMRQA001-1920211A -> La letra "A" corresponde al canal que es definido dentro del ciclo for
        for ($i=0; $i < $Partes_Producto; $i++) { 
            if (!InsertEntradaFaenamiento($dbConn,$Fecha,$Codigo_epmrq.$ArrayCanales[$i],$Ordinal,$ArrayCanales[$i],$Linea,$Codigo_Producto,$Id_Detalle)) {
                $contador2++;
            }
        }
        if ($contador2==0) {
            if(update_detalle_guia($dbConn,$Id_Detalle)){
                if (update_guia_proceso($dbConn,$Id_contador,($procesado + 1))) {
                    $factura =  get_OrdenEmergente($dbConn,$Id_Detalle);
                    $Orden_normal = 0;
                    if ($factura==0) {
                        $result = get_Orden($dbConn,$Id_contador);
                        if ($result!=0) {
                            $Arrayresult = explode("&&",$result);
                            $factura = $Arrayresult[0];   
                            $Orden_normal = $Arrayresult[1];     
                        }else $factura = $result;
                    }
                    if ($factura!=0) {
                        if(InsertFaenamiento($dbConn,$Ruc,$factura,$Guia_numero,$tipo)){
                            if ($Orden_normal!=0) {
                                if (update_orden_detalle_guia($dbConn,$Id_Detalle,$Orden_normal)) {
                                    return "Registro de faenamiento Correcto 1";
                                }else{
                                    return "Error al incluir la orden de produccion en el animal";
                                }
                            }else return "Registro de faenamiento Correcto 2";
                        }else return "Error al realizar los descuentos";
                    }else return "Registro de Faenamiento Correcto --- El animal aun no cuenta con una orden de produccion";
                }else return "Error al actualizar la guia de proceso";
            }else return "Error al actualizar el estado del Animal";
        }else return "Hubo un erro al insertar un canal";
    }else{
        return "Error se econtraron mas de un resultado";
    }
}
function InsertFaenamiento($dbConn,$Ruc,$factura,$Guia_numero,$tipo){
    global $User;
    global $Ip;
    $consulta="INSERT INTO tbl_faenamiento(ruc,fecha_faenamiento,factura,guia_proceso,cantidad,tipo,usuario,ip,fact_corral,num_fact_corral)
    VALUES (:ruc,:fecha_faenamiento,:factura,:guia_proceso,:cantidad,:tipo,:usuario,:ip,:fact_corral,:num_fact_corral)";
    $sql= $dbConn->prepare($consulta);
    $sql->bindValue(':ruc',$Ruc);
    $sql->bindValue(':fecha_faenamiento',date("Y-m-d H:i:s"));
    $sql->bindValue(':factura',$factura);
    $sql->bindValue(':guia_proceso',$Guia_numero);
    $sql->bindValue(':cantidad',1);
    $sql->bindValue(':tipo',$tipo);
    $sql->bindValue(':usuario',$User);
    $sql->bindValue(':ip',$Ip);
    $sql->bindValue(':fact_corral','N');
    $sql->bindValue(':num_fact_corral','EPMRQ-TR');
    if ($sql->execute()) return true;
    else return false;
}
function get_OrdenEmergente($dbConn,$Id){
    $factura = 0;
    $consulta="SELECT Num_DocumentoYp FROM tbl_orden_emergente_new WHERE dtId  = :id";
    $sql= $dbConn->prepare($consulta);
    $sql->bindValue(':id',$Id);
    $sql->execute();
    if($row = $sql->fetch()) {
        $factura = $row["Num_DocumentoYp"];
    }
    return $factura;
}
function get_Orden($dbConn,$Id){
    $factura = 0;
    $consulta="SELECT Num_DocumentoYp,ordId FROM tbl_orden_new WHERE id_contador= :id";
    $sql= $dbConn->prepare($consulta);
    $sql->bindValue(':id',$Id);
    $sql->execute();
    if($row = $sql->fetch()) {
        $factura = $row["Num_DocumentoYp"].'&&'.$row["ordId"];
    }
    return $factura;
}
function get_Ordinal($Linea,$dbConn){
    $cont = 0;
    $consulta="SELECT efnOrdinal FROM tbl_entrada_faenamiento_new WHERE enfLinea = :linea AND efnFecha BETWEEN :inicio AND :final  ORDER BY efnFecha DESC";
    $sql= $dbConn->prepare($consulta);
    $sql->bindValue(':linea',$Linea);
    $sql->bindValue(':inicio',date("Y-m-d")." 00:00:00");
    $sql->bindValue(':final',date("Y-m-d")." 23:59:59");
    $sql->execute();
    if($row = $sql->fetch()) {
        $cont = $row["efnOrdinal"];
    }
    return $cont + 1;
}
function InsertEntradaFaenamiento($dbConn,$Fecha,$Codigo_epmrq,$Ordinal,$Canal,$Linea,$Codigo_Producto,$Id_Detalle){
    $consulta="INSERT INTO tbl_entrada_faenamiento_new(efnFecha,efnCodigo,efnOrdinal,efnCanal,enfLinea,proId,dtId)
    VALUES(:fecha,:codigo,:ordinal,:canal,:linea,:producto,:id)";
    $sql= $dbConn->prepare($consulta);
    $sql->bindValue(':fecha',$Fecha);
    $sql->bindValue(':codigo',$Codigo_epmrq);
    $sql->bindValue(':ordinal',$Ordinal);
    $sql->bindValue(':canal',$Canal);
    $sql->bindValue(':linea',$Linea);
    $sql->bindValue(':producto',$Codigo_Producto);
    $sql->bindValue(':id',$Id_Detalle);
    if ($sql->execute()) return true;
    else return false;
}

function update_detalle_guia($dbConn,$Id){
    $consulta="UPDATE tbl_detalle_guia_new SET dtProceso = 1 WHERE dtId=:id";
    $sql= $dbConn->prepare($consulta);
    $sql->bindValue(':id',$Id);
    if ($sql->execute()) return true;
    else return false;
}
function update_guia_proceso($dbConn,$Id,$procesado){
    $consulta="UPDATE tbl_guia_proceso SET procesado = :proces WHERE id_contador=:id";
    $sql= $dbConn->prepare($consulta);
    $sql->bindValue(':proces',$procesado);
    $sql->bindValue(':id',$Id);
    if ($sql->execute()) return true;
    else return false;
}
function update_orden_detalle_guia($dbConn,$Id,$ordId){
    $consulta="UPDATE tbl_detalle_guia_new SET ordId = :orden  WHERE dtId=:id";
    $sql= $dbConn->prepare($consulta);
    $sql->bindValue(':orden',$ordId);
    $sql->bindValue(':id',$Id);
    if ($sql->execute()) return true;
    else return false;
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
    return "$total".date("y");
}
?>
