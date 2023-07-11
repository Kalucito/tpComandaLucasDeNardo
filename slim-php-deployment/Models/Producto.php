<?php


    class Producto{
        
        public $id;
        public $sector;
        public $nombre;
        public $precio;

        public function __construct() {}

        public function guardarProducto()
        {
            $accesoDb = AccesoDB::obtenerInstancia();
            $consulta = $accesoDb->prepararConsulta("INSERT INTO productos (sector, nombre, precio) VALUES (:sector, :nombre, :precio)");
            
            $consulta->bindValue(':sector', $this->sector, PDO::PARAM_STR);
            $consulta->bindValue(':nombre', $this->nombre, PDO::PARAM_STR);
            $consulta->bindValue(':precio', $this->precio, PDO::PARAM_STR);
        
            $consulta->execute();
         
        }
    
        public static function obtenerTodos()
        {
            $accesoDb = AccesoDb::obtenerInstancia();
            $consulta = $accesoDb->prepararConsulta("SELECT * FROM productos");
            $consulta->execute();
    
            return $consulta->fetchAll(PDO::FETCH_CLASS, 'Producto');
    
        }

        public static function ObtenerIdPorNombre($nombreProducto)
        {
            $listaProducto = Producto::obtenerTodos();
            $idProducto = -1;


            foreach ($listaProducto as $producto) 
            {
                if(strtolower($producto->nombre) == strtolower($nombreProducto))
                {
                    $idProducto = $producto->id;
                    break;
                }

            }

            return $idProducto;
        }

        public static function ObtenerPrecioPorId($id)
        {
            $listaProducto = Producto::obtenerTodos();
            $precio = -1;


            foreach ($listaProducto as $producto) 
            {
                if($producto->id == $id)
                {
                    $precio = $producto->precio;
                    break;
                }

            }

            return $precio;
        }

        

        
    }



?>