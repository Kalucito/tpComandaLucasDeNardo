<?php 

use Firebase\JWT\JWT;

class AutentificadorJWT
{
    private static $claveSecreta = 'lucasdn';
    private static $tipoEncriptacion = ['HS256'];
    private static $aud = null;
    
    public static function CrearToken($datos)
    {
        $ahora = time();

        $payload = array(
        	'iat'=>$ahora,
            'exp' => $ahora + (60*60),
            'aud' => self::Aud(),
            'data' => $datos,
            'app'=> "PAR2 2023"
        );
     
        return JWT::encode($payload, self::$claveSecreta);
    }
    
    public static function VerificarToken($token)
    {
       
        if(empty($token)|| $token=="")
        {
            throw new Exception("El token esta vacio.");
        } 
        // las siguientes lineas lanzan una excepcion, de no ser correcto o de haberse terminado el tiempo       
        try 
        {
            $decodificado = JWT::decode(
            $token,
            self::$claveSecreta,
            self::$tipoEncriptacion
            );

            if($decodificado->aud !== self::Aud())
            {
                throw new Exception("No es el usuario valido");
            }
        } 
        catch (Exception $e) 
        {
            if ($e->getMessage() === 'Expired token') 
            {
                throw new Exception("Clave Expirada");
            }

            if ($e->getMessage() === 'Unknown JSON error: 5') 
            {
                throw new Exception("TOKEN Invalido");
            }

            throw $e;

        }
        
    }
    
   
     
    public static function ObtenerPayLoad($token)
    {
        return JWT::decode(
            $token,
            self::$claveSecreta,
            self::$tipoEncriptacion
        );
    }
     
    public static function ObtenerData($token)
    {
        return JWT::decode(
            $token,
            self::$claveSecreta,
            self::$tipoEncriptacion
        )->data;
    }

    private static function Aud()
    {
        $aud = '';
        
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $aud = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $aud = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $aud = $_SERVER['REMOTE_ADDR'];
        }
        
        $aud .= @$_SERVER['HTTP_USER_AGENT'];
        $aud .= gethostname();
        
        return sha1($aud);
    }
}






?>