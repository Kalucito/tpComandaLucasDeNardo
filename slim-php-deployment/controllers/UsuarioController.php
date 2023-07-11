<?php
    require_once("./Models/Usuario.php");

    class UsuarioController extends Usuario{

        public function cargarUno($request, $response, $args)
        {
            $parametros = $request->getParsedBody();
            
            $nombre = $parametros['nombre'];
            $clave = $parametros['clave'];
            $mail = $parametros['mail'];
            $rol = $parametros['rol'];

            //Creo el user
            
            $usuario = new Usuario();
            $usuario->nombre = $nombre;
            $usuario->clave = $clave;
            $usuario->mail = $mail;
            $usuario->rol = $rol;

            $usuario->guardarUsuario();

            $payload = json_encode(array("mensaje"=>"Usuario creado con exito"));

            $response->getBody()->write($payload);

            return $response->withHeader('Content-Type', 'application/json');

        }

        public function TraerTodos($request, $response, $args)
        {
            $lista = Usuario::obtenerTodos();
            $payload = json_encode(array("listaUsuario"=> $lista));

            $response->getBody()->write($payload);
            
            return $response->withHeader('Content-Type', 'application/json');

        }

        public function LoginUsuario($request, $response, $args)
        {
          $ArrayDeParametros = $request->getParsedBody();
      
          $mail = $ArrayDeParametros['mail'];
          $clave = $ArrayDeParametros['clave'];
      
          $user = new Usuario();
          $user->mail = $mail;
          $user->clave = $clave;
      
          $respuesta = Usuario::VerificarUsuarioDB($user);
      
          switch ($respuesta) 
          {
            case -1:
            $payload = json_encode(array("mensaje"=>"No existe el usuario"));

              break;
            case 0:
            $payload = json_encode(array("mensaje"=>"Mail correcto pero clave incorrecta"));

              break;
            case 1:
              $datos = ["rol" => $user->rol, "mail" => $user->mail];
            $payload = json_encode(array("mensaje"=>AutentificadorJWT::CrearToken($datos)));
      
              break;

          };

        $response->getBody()->write($payload);
        
        return $response->withHeader('Content-Type', 'application/json');





    }



    }


?>