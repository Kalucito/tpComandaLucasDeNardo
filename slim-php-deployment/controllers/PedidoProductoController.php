<?php
    require_once("./Models/PedidoProducto.php");
    require_once("./Models/Producto.php");
    require_once("./Models/Pedido.php");
    require_once("./ClasesDeAutenticacion/AutentificadorJWT.php");

    class PedidoProductoController extends PedidoProducto{

        public function cargarUno($request, $response, $args)
        {
            $parametros = $request->getParsedBody();
            
            $codigoPedido = $parametros['codigo_pedido'];
            $nombreProducto = $parametros['nombre_producto'];
            $id_producto = Producto::ObtenerIdPorNombre($nombreProducto);

            if(Pedido::ValidarPedidoPorCodigo($codigoPedido) != -1)
            {

                if($id_producto != -1)
                {
                    //Creo el PedidoProducto
                    
                    $pedido = new PedidoProducto();
                    $pedido->codigo_pedido = $codigoPedido;
                    $pedido->id_producto = $id_producto;
                    $pedido->tiempo_estimado = -1;
                    $pedido->estado = 0;

                    Pedido::SumaPrecio(Pedido::ValidarPedidoPorCodigo($codigoPedido), Producto::ObtenerPrecioPorId($id_producto));

        
                    $pedido->guardarPedidoProducto();
        
                    $payload = json_encode(array("mensaje"=>"Producto cargado al pedido con exito."));
                    
                }
                else
                {
                    $payload = json_encode(array("mensaje"=>"Producto Inexistente"));
                }


            }
            else
            {
                $payload = json_encode(array("mensaje"=>"Codigo de Pedido Inexistente"));
            }


            $response->getBody()->write($payload);

            return $response->withHeader('Content-Type', 'application/json');

        }

        public function TraerTodos($request, $response, $args)
        {
            $lista = PedidoProducto::obtenerTodos();
            $payload = json_encode(array("listaPedido"=> $lista));

            $response->getBody()->write($payload);
            
            return $response->withHeader('Content-Type', 'application/json');

        }

        public function TraerPedidosPendientes($request, $response, $args)
        {

            $header = $request->getHeaderLine('Authorization');
            $token = trim(explode("Bearer", $header)[1]);
    
            $payload = AutentificadorJWT::ObtenerData($token);

            switch (strtolower($payload->rol)) {
                case "socio":
                    $todosLosPedidos = PedidoProducto::TraerPendientes("socio");
                    break;
                case "mozo":
                    $todosLosPedidos = PedidoProducto::TraerPendientes("mozo");
                    break;
                case "bartender":
                    $todosLosPedidos = PedidoProducto::TraerPendientes("bartender");
                    break;
                case "cervezero":
                    $todosLosPedidos = PedidoProducto::TraerPendientes("cervezero");
                    break;
                case "cocinero":
                    $todosLosPedidos = PedidoProducto::TraerPendientes("cocinero");
                    break;
                default:
                    $todosLosPedidos =  "ERROR, el usuario no es de los esperados.";
                    break;
            }
    

            $payload = json_encode(array("listaPedido"=> $todosLosPedidos));

            $response->getBody()->write($payload);


            return $response->withHeader('Content-Type', 'application/json');

        }

        public function CambiarEstadoPedido($request, $response, $args)
        {

            $header = $request->getHeaderLine('Authorization');
            $token = trim(explode("Bearer", $header)[1]);
    
            $payload = AutentificadorJWT::ObtenerData($token);

            $retorno = 0;

            $parametros = $request->getParsedBody();
            
            $codigoPedido = $parametros['codigo_pedido'];
            $nombreProducto = $parametros['nombre_producto'];
            $tiempoEstimado = $parametros['tiempo_estimado'];
            $estado = $parametros['estado'];


            $listaProductos = Producto::obtenerTodos();

            foreach($listaProductos as $producto)
            {
                if($producto->nombre == $nombreProducto)
                {

                    if($producto->sector == $payload->rol)
                    {

                        $retorno = PedidoProducto::CambiarEstadoDelPedido($codigoPedido, Producto::ObtenerIdPorNombre($nombreProducto),$tiempoEstimado, $estado);
                        break;
                        $payload = json_encode(array("mensaje"=>"Estado cambiado a en prepación."));
                    }
                    else
                    {
                        $payload = json_encode(array("mensaje"=>"El empleo es incorrecto"));
                    }
                }
                else
                {
                    $payload = json_encode(array("mensaje"=>"Error en el nombre del producto"));
                }
            }

            if($retorno == 0)
            {
                $payload = json_encode(array("mensaje"=>"Codigo De pedido incorrecto."));
            }

            $response->getBody()->write($payload);


            return $response->withHeader('Content-Type', 'application/json');

        }

        
        public function TiempoEstimadoPedido($request, $response, $args)
        {

            $parametros = $request->getParsedBody();
            
            $codigoPedido = $request->getQueryParams()["codigo_pedido"];
            $codigoMesa = $request->getQueryParams()["codigo_mesa"];


            $lista = PedidoProducto::VerTiempoPedido($codigoPedido, Mesa::ObtenerIdPorCodigoDeMesa($codigoMesa));
            $payload = json_encode(array("listaPedido"=> $lista));

            $response->getBody()->write($payload);
            
            return $response->withHeader('Content-Type', 'application/json');

        }

        public function VerPedidosConTiempoDueño($request, $response, $args)
        {

            $parametros = $request->getParsedBody();
            

            $lista = PedidoProducto::VerPedidosDueño();
            $payload = json_encode(array("listaPedido"=> $lista));

            $response->getBody()->write($payload);
            
            return $response->withHeader('Content-Type', 'application/json');

        }




    }


?>