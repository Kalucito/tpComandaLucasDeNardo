<?php 

class Pedido{

    public $id;
    public $codigo_pedido;
    public $id_cliente;
    public $id_mesa;
    public $id_empleado;
    public $fecha;
    public $foto_mesa;
    public $precio_final;
    public function __construct() {}

    public function guardarPedido()
    {
        $accesoDb = AccesoDB::obtenerInstancia();
        $consulta = $accesoDb->prepararConsulta("INSERT INTO pedidos (codigo_pedido, id_mesa, id_empleado, id_cliente, fecha, foto_mesa, precio_final) VALUES (:codigo_pedido, :id_mesa, :id_empleado, :id_cliente, :fecha, :foto_mesa, :precio_final)");
        
        $consulta->bindValue(':codigo_pedido', $this->codigo_pedido, PDO::PARAM_STR);
        $consulta->bindValue(':id_mesa', $this->id_mesa, PDO::PARAM_INT);
        $consulta->bindValue(':id_empleado', $this->id_empleado, PDO::PARAM_INT);
        $consulta->bindValue(':id_cliente', $this->id_cliente, PDO::PARAM_INT);
        $consulta->bindValue(':fecha', $this->fecha, PDO::PARAM_STR);
        $consulta->bindValue(':foto_mesa', $this->foto_mesa, PDO::PARAM_STR);
        $consulta->bindValue(':precio_final', $this->precio_final, PDO::PARAM_STR);
    
        $consulta->execute();
     
    }

    public static function obtenerTodos()
    {
        $accesoDb = AccesoDb::obtenerInstancia();
        $consulta = $accesoDb->prepararConsulta("SELECT * FROM pedidos");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Pedido');

    }

    public static function AlfanumericoRandom($length)
    {
        $chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        return substr(str_shuffle($chars), 0, $length);
    }

    public static function ValidarPedidoPorCodigo($codigo)
    {
        $listaPedido = Pedido::obtenerTodos();
        $esValido = -1;

        foreach ($listaPedido as $pedido) 
        {
            if($pedido->codigo_pedido == $codigo)
            {
                $esValido = $pedido->id;
                break;
            }

        }

        return $esValido;
    }

    public function SubirFoto()
    {
        $accesoDb = AccesoDB::obtenerInstancia();

        $consulta = $accesoDb->prepararConsulta("UPDATE pedidos SET foto_mesa = :foto_mesa WHERE id = :id");

        $consulta->bindValue(':id', $this->id, PDO::PARAM_INT);
        $consulta->bindValue(':foto_mesa', $this->foto_mesa, PDO::PARAM_STR);

        $consulta->execute();

        return $consulta->rowCount();

    }


    public static function SumaPrecio($id, $monto)
    {
        $accesoDb = AccesoDB::obtenerInstancia();

        $consulta = $accesoDb->prepararConsulta("UPDATE pedidos SET precio_final = precio_final + :monto WHERE id = :id");
        
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        $consulta->bindValue(':monto', $monto, PDO::PARAM_STR);

        $consulta->execute();

        return $consulta->rowCount();

    }


    public function GuardarImagen($ruta, $imagen)
    {
        $destino = $ruta . ".jpg";

        $this->foto_mesa = $destino;
        
        if(!move_uploaded_file($imagen["tmp_name"], $destino))
        {
            $this->foto_mesa = "Error";
        }

    }




    // public static function TraerPendientes($empleo)
    // {

    //     switch ($empleo) {
    //         case "Socio":

    //             $consulta = Capsule::select('SELECT idPedido, nombre, id_estado, cantidad, tiempo_estimado FROM `pedidos` INNER JOIN producto ON pedidos.id_producto = producto.idProducto');
    //             break;
    //         case "Mozo":
    //             $consulta = Capsule::select('SELECT idPedido, nombre, id_estado, cantidad, tiempo_estimado FROM `pedidos` INNER JOIN producto ON pedidos.id_producto = producto.idProducto where id_estado = 2');
    //             break;
    //         case "Bartender":
    //             $consulta = Capsule::select('SELECT idPedido, nombre, id_estado, cantidad FROM `pedidos` INNER JOIN producto ON pedidos.id_producto = producto.idProducto WHERE producto.tipo = "bar" AND id_estado IN (0,1)');
    //             break;
    //         case "Cervezero":
    //             $consulta = Capsule::select('SELECT idPedido, nombre, id_estado, cantidad FROM `pedidos` INNER JOIN producto ON pedidos.id_producto = producto.idProducto WHERE producto.tipo = "cerveza" AND id_estado IN (0,1)');
    //             break;
    //         case "Cocinero":
    //             $consulta = Capsule::select("SELECT idPedido, nombre, id_estado, cantidad FROM `pedidos` INNER JOIN producto ON pedidos.id_producto = producto.idProducto WHERE producto.tipo = 'cocina' and id_estado IN(0,1)");
    //             break;
    //         default:
    //             echo "ERROR, el usuario no es de los esperados.";
    //             break;
    //     }

    //     return $consulta;
    // }

}


?>