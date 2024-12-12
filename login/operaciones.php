<?php
	if (isset($_REQUEST["op"])) {
        $op = $_REQUEST["op"];
        if ($op==1)echo login();
        elseif ($op==2)echo md5("Tunombre2020*");
    }
    function login(){
        include '../FilePHP/confi.php';
        $user = $_POST["EPMRQ_Usuario"];
        $pass = $_POST["EPMRQ_Pass"];
        $dbConn = conectar($db);        
        $pass= md5($pass);
        $sql= $dbConn->prepare('SELECT * FROM tbl_a_usuarios WHERE usuCedula= :cedula AND usuPasswd=:passwordd AND usuEstado=1 LIMIT 1');
        $sql->bindValue(':cedula',$user);
        $sql->bindValue(':passwordd',$pass);
        $sql->execute();
        if($row = $sql->fetch()) {
            session_start();
            $_SESSION['PDF-ID-GUIA'] = 0;
            $_SESSION['MM_Username']= $row["usuId"];
            $_SESSION['MM_UserGroup']= $row["usuDepartamento"];
            $_SESSION['FAENAMIENTO']= 0;
            $_SESSION['ANTEMORTEM']= [0,0];//Especie, Comprobante
            $_SESSION['DECOMISO']= array(0,0);
            $_SESSION['ENFERMEDADES']=array();
            $_SESSION['DECOMISOS']=array();
            $_SESSION['DATOSDECOMISO']=array(0,1,'',0);//Producto,cantidad,causa,(0:Hembra,1:Macho)
            $_SESSION['ORIGEN'][0] = 0;
            $_SESSION['ORIGEN'][1] = 0;
            $_SESSION['DATOSORIGEN'] = array(0,'',0,'','');//IdParroquia,Direccion,IdVehiculo,Observaciones,Tipo de producto a movilizar
            $_SESSION['PRODCUTOS']= array();
            $_SESSION['SUBPRODUCTOS'] = array();
            ////
            $_SESSION['SENTENCIA-MYSQL'] = [0,0,0,0];//Base,Tabla,Opcion,Menu
            return true;
        }else{
            return false;
        }
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
