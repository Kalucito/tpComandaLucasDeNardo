<?php
    require_once("./Models/Pedido.php");
    require_once("./Models/Mesa.php");

    class PedidoController extends Pedido{

        public function cargarUno($request, $response, $args)
        {
            $parametros = $request->getParsedBody();
            
            $codigoPedido = Pedido::AlfanumericoRandom(5);
            $codigoMesa = $parametros['codigo_mesa'];
            $mailMozo = $parametros['mail_mozo'];
            $mailCliente = $parametros['mail_cliente'];
            $fecha = date("Y-m-d H:i:s");
            
            if(isset($parametros['foto_mesa']) && $parametros['foto_mesa'] != null)
            {
                $fotoMesa = $parametros['foto_mesa'];
            }
            else
            {
                $fotoMesa = "No hay imagen.";
            }


            $id_mesa = Mesa::ObtenerIdPorCodigoDeMesa($codigoMesa);
            $id_empleado = Usuario::ObetenerIdPorMail($mailMozo);
            $id_cliente = Usuario::ObetenerIdPorMail($mailCliente);

            if($id_mesa != -1)
            {
                if(Mesa::VerificarEstadoMesa($id_mesa) == 0)
                {
                    if($id_empleado != -1)
                    {
                        if($id_cliente != -1)
                        {
                            //Creo el Pedido
                            
                            $pedido = new Pedido();
                            $pedido->codigo_pedido = $codigoPedido;
                            $pedido->id_mesa = $id_mesa;
                            $pedido->id_empleado =  $id_empleado;
                            $pedido->id_cliente = $id_cliente;
                            $pedido->fecha = $fecha;
                            $pedido->foto_mesa = $fotoMesa;
                            $pedido->precio_final = 0;
                            
                            //Cambio estado de la mesa a ESPERANDO PEDIDO
                            Mesa::CambiarEstadoMesa($id_mesa, 1);
                            
                            $pedido->guardarPedido();
                
                            $payload = json_encode(array("mensaje"=>"Pedido creado con exito: {$codigoPedido}"));
                        }
                        else
                        {
                            $payload = json_encode(array("mensaje"=>"El mail del cliente no existe."));
                        }
                    }
                    else
                    {
                        $payload = json_encode(array("mensaje"=>"El mail del mozo es incorrecto."));
                    }
                }
                else
                {
                    $payload = json_encode(array("mensaje"=>"La mesa ingresada esta ocupada."));
                }
            }
            else
            {
                $payload = json_encode(array("mensaje"=>"La mesa ingresada no existe."));
            }


            $response->getBody()->write($payload);

            return $response->withHeader('Content-Type', 'application/json');

        }

        public function TraerTodos($request, $response, $args)
        {
            $lista = Pedido::obtenerTodos();
            $payload = json_encode(array("listaPedido"=> $lista));

            $response->getBody()->write($payload);
            
            return $response->withHeader('Content-Type', 'application/json');

        }

        
        public function AgregarFoto($request, $response, $args)
        {

            $parametros = $request->getParsedBody();


            $codigo_pedido = $parametros['codigo_pedido'];

            $idPedido = Pedido::ValidarPedidoPorCodigo($codigo_pedido);

            if($idPedido != -1)
            {

                $pedidoModificado = new Pedido();
    
                $pedidoModificado->id = $idPedido;
                $pedidoModificado->GuardarImagen( "Fotos/{$codigo_pedido}", $_FILES['foto']);
    
                if($pedidoModificado->SubirFoto() > 0)
                {
                    $payload = json_encode(array("mensaje" => "Foto Agregada."));
                }
                else
                {
                    $payload = json_encode(array("mensaje" => "No se pudo agregar la imagen."));
                }

            }
            else
            {
                $payload = json_encode(array("mensaje" => "No existe el pedido ingresado."));
            }



            $response->getBody()->write($payload);

            return $response->withHeader('Content-Type', 'application/json');
        

        }

        









    }


?>