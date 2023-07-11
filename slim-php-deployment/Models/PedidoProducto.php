<?php 



class PedidoProducto{

    public $id;
    public $codigo_pedido;
    public $id_producto;
    public $tiempo_estimado;
    public $estado;

    public function __construct() {}

    public function guardarPedidoProducto()
    {
        $accesoDb = AccesoDB::obtenerInstancia();
        $consulta = $accesoDb->prepararConsulta("INSERT INTO pedidos_productos (codigo_pedido, tiempo_estimado, id_producto, estado) VALUES (:codigo_pedido, :tiempo_estimado, :id_producto, :estado)");
        
        $consulta->bindValue(':codigo_pedido', $this->codigo_pedido, PDO::PARAM_STR);
        $consulta->bindValue(':tiempo_estimado', $this->tiempo_estimado, PDO::PARAM_INT);
        $consulta->bindValue(':id_producto', $this->id_producto, PDO::PARAM_INT);
        $consulta->bindValue(':estado', $this->estado, PDO::PARAM_INT);

    
        $consulta->execute();
     
    }

    public static function obtenerTodos()
    {
        $accesoDb = AccesoDb::obtenerInstancia();
        $consulta = $accesoDb->prepararConsulta("SELECT * FROM pedidos_productos");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'PedidoProducto');

    }

    public static function TraerPendientes($empleo)
    {

        $accesoDb = AccesoDb::obtenerInstancia();

        
        if($empleo == "mozo")
        {
            $consulta = $accesoDb->prepararConsulta("SELECT a.codigo_pedido , b.nombre  FROM pedidos_productos AS a INNER JOIN productos AS b ON a.id_producto = b.id WHERE LOWER(b.sector) = LOWER(:empleo) AND a.estado = 2");
        }
        else
        {
            $consulta = $accesoDb->prepararConsulta("SELECT a.codigo_pedido , b.nombre  FROM pedidos_productos AS a INNER JOIN productos AS b ON a.id_producto = b.id WHERE LOWER(b.sector) = LOWER(:empleo) AND a.estado = 0");
        }

        $consulta->bindValue(':empleo', $empleo, PDO::PARAM_STR);

        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_ASSOC);

    }

    public static function CambiarEstadoDelPedido($codigo_pedido, $idProducto,$tiempo_estimado, $estado)
    {

        $accesoDb = AccesoDB::obtenerInstancia();


        $consulta = $accesoDb->prepararConsulta("UPDATE pedidos_productos SET estado = :estado, tiempo_estimado = :tiempo_estimado WHERE codigo_pedido = :codigo_pedido AND id_producto = :idProducto");
        
        $consulta->bindValue(':codigo_pedido', $codigo_pedido, PDO::PARAM_STR);
        $consulta->bindValue(':tiempo_estimado', $tiempo_estimado, PDO::PARAM_STR);
        $consulta->bindValue(':idProducto', $idProducto, PDO::PARAM_STR);
        $consulta->bindValue(':estado', $estado, PDO::PARAM_INT);

        $consulta->execute();

        return $consulta->rowCount();

    }

    public static function VerTiempoPedido($codigo_pedido, $id_mesa)
    {
        $accesoDb = AccesoDb::obtenerInstancia();
        $consulta = $accesoDb->prepararConsulta("SELECT a.codigo_pedido, b.nombre, a.tiempo_estimado FROM pedidos_productos AS a INNER JOIN productos AS b ON a.id_producto = b.id INNER JOIN pedidos AS c ON c.id_mesa = :id_mesa  WHERE a.codigo_pedido = :codigo_pedido AND a.estado = 0");

        $consulta->bindValue(':codigo_pedido', $codigo_pedido, PDO::PARAM_STR);
        $consulta->bindValue(':id_mesa', $id_mesa, PDO::PARAM_STR);

        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function VerPedidosDueño()
    {
        $accesoDb = AccesoDb::obtenerInstancia();
        $consulta = $accesoDb->prepararConsulta("SELECT a.codigo_pedido, b.nombre, a.tiempo_estimado FROM pedidos_productos AS a INNER JOIN productos AS b ON a.id_producto = b.id");


        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }




}


?>