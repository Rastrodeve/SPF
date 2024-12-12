<?php
$to = "jairo.castillo@epmrq.gob.ec";
$subject = "Prueba";
$message = "Prueba de correo";
$headers = "From: recepcion.epmrq@gmail.com"."\r\n";
// $headers .= "Reply-To: stalincastillo7899@gmail.com"."\r\n";
$headers .= "X-Mailer: PHP/".phpversion();
try {
    $variable = mail($to, $subject, $message, $headers);
    if ($variable) {
        echo "Enviado";
    }
} catch (Exception $e) {
    echo $e;
}

?>