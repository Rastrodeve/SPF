<?php
	include 'confi.php';
    session_start();
    if (isset($_SESSION['MM_Username'])) {
        $User = $_SESSION['MM_Username'];
        $Ip = getRealIP();
    }

	function conectar($db){
		try {
			$conn = new PDO("mysql:host={$db['host']};dbname={$db['db']}",$db['user'],$db['password']);
			$conn->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
			return $conn;
			} catch (PDOException $e) {
				exit("Error en la conección ". $e->getMessage());
			}
	}
    function Insert_Login($Id,$table,$accion,$detalle,$comentario){
        try {
            global $dblLOG;
            global $User;
            global $Ip;
            $dbConn = conectar($dblLOG);
            $Fecha = date("Y-m-d H:i:s");
            $consulta = "INSERT INTO tbl_log(logFecha,logId_Afecado,logTabla,logAccion,logDetalle,logIp,logComentario,usuId)
                VALUES (:logFecha,:logId_Afecado,:logTabla,:logAccion,:logDetalle,:logIp,:logComentario,:usuId)";
            $sql= $dbConn->prepare($consulta);
            $sql->bindValue(':logFecha',$Fecha);
            $sql->bindValue(':logId_Afecado',$Id);
            $sql->bindValue(':logTabla',$table);
            $sql->bindValue(':logAccion',utf8_decode($accion));
            $sql->bindValue(':logDetalle',utf8_decode($detalle));
            $sql->bindValue(':logIp',$Ip);
            $sql->bindValue(':logComentario',utf8_decode($comentario));
            $sql->bindValue(':usuId',$User);
            if ($result = $sql->execute()){
                return true;
            }else return $result;
        } catch (PDOException $e) {
            exit("Error: ". $e->getMessage());
        }
    }
    function Insert_Error($numero,$descripcion,$accion){
        try {
            global $dblLOG;
            global $User;
            global $Ip;
            $dbConn = conectar($dblLOG);
            $Fecha = date("Y-m-d H:i:s");
            $consulta = "INSERT INTO tbl_error(errNumero,errAccion,errDescripcion,errFecha,errIp,usuId)
                VALUES(:errNumero,:errAccion,:errDescripcion,:errFecha,:errIp,:usuId)";
            $sql= $dbConn->prepare($consulta);
            $sql->bindValue(':errNumero',$numero);
            $sql->bindValue(':errAccion',utf8_decode($accion));
            $sql->bindValue(':errDescripcion',utf8_decode($descripcion));
            $sql->bindValue(':errFecha',$Fecha);
            $sql->bindValue(':errIp',$Ip);
            $sql->bindValue(':usuId',$User);
            if ($result = $sql->execute()){
                return true;
            }else return $result;
        } catch (PDOException $e) {
            exit("Error: ". $e->getMessage());
        }
    }
    function getRealIP() {
        if (!empty($_SERVER['HTTP_CLIENT_IP']))
            return $_SERVER['HTTP_CLIENT_IP'];       
        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))
            return $_SERVER['HTTP_X_FORWARDED_FOR'];
        return $_SERVER['REMOTE_ADDR'];
    }
    // FUNCIONES DE COMPROBACION ANTES DE APROBAR LA CONSULTA 
    function insert_nueva_solicitud($dbConn,$cabecera,$cuerpo,$razon,$Aprobado1,$Aprobado2,$n_consulta,$Id,$function){
        try {
            global $User;
            global $Ip;
            $consulta = "INSERT INTO tbl_s_solicitudes(sltFecha,sltCabecera,sltCuerpo,sltError,sltIdSolicitante,sltIpSolicitante, sltIdAprobado1,sltIdAprobado2,sltConsulta,sltIdPrimary,sltFuncion)
            VALUES (:sltFecha,:sltCabecera,:sltCuerpo,:sltError,:sltIdSolicitante,:sltIpSolicitante,:sltIdAprobado1,:sltIdAprobado2,:sltConsulta,:sltIdPrimary,:sltFuncion)";
            $sql= $dbConn->prepare($consulta);
            $sql->bindValue(':sltFecha',date("Y-m-d H:i:s"));
            $sql->bindValue(':sltCabecera',utf8_decode($cabecera));
            $sql->bindValue(':sltCuerpo',utf8_decode($cuerpo));
            $sql->bindValue(':sltError',utf8_decode($razon));
            $sql->bindValue(':sltIdSolicitante',$User);
            $sql->bindValue(':sltIpSolicitante',$Ip);
            $sql->bindValue(':sltIdAprobado1',$Aprobado1);
            $sql->bindValue(':sltIdAprobado2',$Aprobado2);
            $sql->bindValue(':sltConsulta',$n_consulta);
            $sql->bindValue(':sltIdPrimary',$Id);
            $sql->bindValue(':sltFuncion',$function);
            if ($sql->execute())return true;
            else return false;
        }  catch (Exception $e) {
            Insert_Error('ERROR-82129',$e->getMessage(),'Error al registrar la solicitud');
            exit("ERROR-82129");
        }
    }
    function f_funcion_solicitud_comprobacion_1($dbConn,$Id){// COMPROBACION SI EXISTE UNA DE LA ORDEN DE PRODUCCION
        $consulta="SELECT * FROM tbl_p_orden WHERE gprId = :id";
        $sql= $dbConn->prepare($consulta);
        $sql->bindValue(':id',$Id);
        $sql->execute();
        if($row = $sql->fetch()) return true;
        return false;
    }
    
?>
