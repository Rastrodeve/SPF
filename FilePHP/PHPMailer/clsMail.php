<?php



require 'Exception.php';
require 'PHPMailer.php';
require 'SMTP.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class clsMail{

    private $mail = null;
    
    function __construct(){
        $this->mail = new PHPMailer();
        // $this->mail->SMTPDebug = SMTP::DEBUG_SERVER;
        $this->mail->isSMTP();
        $this->mail->SMTPAuth = true;
        $this->mail->SMTPSecure = 'ssl';
        $this->mail->Host = "smtp.gmail.com";
        $this->mail->Port = 465;
        $this->mail->Username = "epmrq.recepcion.ganado@gmail.com";
        $this->mail->Password = "kxdogfeclrjvulgv";
    }


    public function metEnviar($titulo, $nombre, $correo, $asunto, $bodyHTML){
        $this->mail->setFrom("epmrq.recepcion.ganado@gmail.com", $titulo);
        $this->mail->addAddress($correo,$nombre);
        $this->mail->addCC('stalincastillo7899@gmail.com','Jairo Castillo');
        $this->mail->Subject = $asunto;
        $this->mail->Body = $bodyHTML;
        $this->mail->isHTML(true);
        $this->mail->CharSet = "UTF-8";
        return $this->mail->send();
    }
}

?>