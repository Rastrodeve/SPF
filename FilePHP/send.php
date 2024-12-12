<?php
    require 'PHPMailer/Exception.php';
    require 'PHPMailer/PHPMailer.php';
    require 'PHPMailer/SMTP.php';
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;
function metEnviar($para,$copia,$bodyHTML,$asunto,$titulo_envio){
    try {
        $correo = "epmrq.recepcion.ganado@gmail.com";
        $mail = new PHPMailer();
        $mail->isSMTP();
        $mail->SMTPAuth = true;
        $mail->SMTPSecure = 'ssl';
        $mail->Host = "smtp.gmail.com";
        $mail->Port = 465;
        $mail->Username = $correo;
        $mail->Password = "kxdogfeclrjvulgv";
        //ENVIAR CORREO
        $mail->setFrom($correo, $titulo_envio);
        foreach ($para as $value) {
            $mail->addAddress($value[0],$value[1]);
        }
        if (count($copia) > 0) {
            foreach ($copia as $value) {
                $mail->addCC($value[0],$value[1]);
            }
        }
        $mail->Subject = $asunto;
        $mail->Body = $bodyHTML;
        $mail->isHTML(true);
        $mail->CharSet = "UTF-8";
        return $mail->send();
    } catch (Throwable $e) {
        return "Error al enviar el correo: " .$e->getMessage();
    }
}

?>