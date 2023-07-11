<?php
    require_once("./Models/Producto.php");

    class ProductoController extends Producto{

        public function cargarUno($request, $response, $args)
        {
            $parametros = $request->getParsedBody();
            
            $sector = $parametros['sector'];
            $nombre = $parametros['nombre'];
            $precio = $parametros['precio'];

            //Creo el user
            
            $usuario = new Producto();
            $usuario->sector = $sector;
            $usuario->nombre = $nombre;
            $usuario->precio = $precio;

            $usuario->guardarProducto();

            $payload = json_encode(array("mensaje"=>"Producto creado con exito"));

            $response->getBody()->write($payload);

            return $response->withHeader('Content-Type', 'application/json');

        }

        public function TraerTodos($request, $response, $args)
        {
            $lista = Producto::obtenerTodos();
            $payload = json_encode(array("listaProducto"=> $lista));

            $response->getBody()->write($payload);
            
            return $response->withHeader('Content-Type', 'application/json');

        }




    }


?>