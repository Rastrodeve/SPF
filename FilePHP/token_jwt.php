<?php
require_once __DIR__ . './vendor/autoload.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class token
{
    private $url_privatekey;
    private $url_publickey;
    private $passphrase;
    private $token;
    private $token_error;
    public function __construct()
    {
        $this->setUrl_Private();
        $this->setUrl_Public();
        $this->setPassphrase();
        $this->token = ["data" => []];
    }
    public function setUrl_Private($url = "") //Obtener la ruta de la clave privada
    {
        if ($url == "") $this->url_privatekey = parse_ini_file(__DIR__ . '/../config/token.conf', true)["private"];
        else $this->url_privatekey = $url;
    }
    public function setUrl_Public($url = "") // Otener la ruta de clave publica
    {
        if ($url == "") $this->url_publickey = parse_ini_file(__DIR__ . '/../config/token.conf', true)["public"];
        else $this->url_publickey = $url;
    }
    public function setPassphrase($passphrase = "") // Obtener la constraseña de la clave
    {
        if ($passphrase == "") $this->passphrase = parse_ini_file(__DIR__ . '/../config/token.conf', true)["passphrase"];
        else $this->passphrase = $passphrase;
    }


    public function create_token($data)
    {
        try {

            $privateKey = openssl_pkey_get_private(file_get_contents($this->url_privatekey), $this->passphrase);
            $this->token["data"] = $data;
            $jwt =  JWT::encode($this->token, $privateKey, 'RS256');
            return $jwt;
        } catch (Exception $exception) {
            $this->token_error .= "\nError de cración de token: " . $exception;
        }
    }
    public function read_token($jwt)
    {
        try {
            $privateKey = openssl_pkey_get_private(file_get_contents($this->url_privatekey), $this->passphrase);
            $publicKey = openssl_pkey_get_details($privateKey)['key'];
            $decoded = JWT::decode($jwt, new Key($publicKey, 'RS256'));
            return $decoded;
            // echo "<br>Decode:\n" . print_r((array) $decoded, true) . "\n";
        } catch (Exception $exception) {
            // Tell the user that their JWT has expired
            $this->token_error .= "\nError de lectura de token: " . $exception;
            return "";
        }
    }
    public function exp($time)
    {
        //Predeterminadamente no tiene tiempo de expiración
        $this->token["exp"] = $time; //time()+ 60 * 60 * 24 * 365;
    }
    public function return_token_error()
    {
        return $this->token_error;
    }
}

// $obj = new token();
// $obj->exp(time() + 60);
// $token = $obj->create_token(["identification" => md5(1), "profile" => md5(1)]);
// print_r($token);
// echo "<h1>Token: </h1><br>" . $token . "<br>";
// $result = $obj->read_token("eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJkYXRhIjp7ImlkIjoiTnVldm8ifSwiZXhwIjoxNzAxNzIzNDcxfQ.JoppFBcYv4gt0KuhBr6XTRhdCDLAxlbvb8-yYBVE8hD46XkaM9AwmfxiuhUQA9FYp73AoBdKfrNFi8Te1MACkmHszdeMpzY20a5iFhpiFdpBZxBTGXr5Saw9cMDbBqFaUEN_X-3FpaCGxWzTWMZYIKxDm_TVfgYdiXp8oLLi3wNlQPilLdeRn7CL90SaWttQnF2Z0Ll7zqZPBpVKpUZSnaI5lZoW8pnZw6cxQ6fWP2Szi-oUC1rmaBpNv8pfRRSNcf0nkM8X7PjiTeRG-DISgdHlj760eYkxqFyRnXoNdq5HgU8FlHBPAxa_dGU-pfE7AnkHZcHIcPDONHPmgvp2uw");
// $result = $obj->read_token($token);
// echo "<h2>Lectura token</h2><br>";
// if (empty($result)) {
//     echo "Token Ivalido";
// } else print_r($decoded);
// print_r($obj->return_token_error());
// print_r($result->data->id);



// ----------------------------------
