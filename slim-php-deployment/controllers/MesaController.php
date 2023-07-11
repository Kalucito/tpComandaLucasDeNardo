<?php
    require_once("./Models/Mesa.php");

    class MesaController extends Mesa{

        public function cargarUno($request, $response, $args)
        {
            $parametros = $request->getParsedBody();
            
            $codigoMesa = $parametros['codigo_mesa'];
            $estadoMesa = $parametros['estado_mesa'];

            //Creo el user
    
            $usuario = new Mesa();
            $usuario->codigo_mesa = $codigoMesa;
            $usuario->estado_mesa = $estadoMesa;

            $usuario->guardarMesa();

            $payload = json_encode(array("mensaje"=>"Mesa creado con exito"));

            $response->getBody()->write($payload);

            return $response->withHeader('Content-Type', 'application/json');

        }

        public function TraerTodos($request, $response, $args)
        {
            $lista = Mesa::obtenerTodos();
            $payload = json_encode(array("listaMesa"=> $lista));

            $response->getBody()->write($payload);
            
            return $response->withHeader('Content-Type', 'application/json');

        }




    }


?>