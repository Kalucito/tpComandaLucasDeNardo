<?php

    class Mesa{

        public $id;
        public $codigo_mesa;
        public $estado_mesa;

        public function __construct() {}

        public function guardarMesa()
        {
            $accesoDb = AccesoDB::obtenerInstancia();
            $consulta = $accesoDb->prepararConsulta("INSERT INTO mesas (codigo_mesa, estado_mesa) VALUES (:codigo_mesa, :estado_mesa)");
            $consulta->bindValue(':codigo_mesa', $this->codigo_mesa, PDO::PARAM_STR);
            $consulta->bindValue(':estado_mesa', $this->estado_mesa, PDO::PARAM_INT);
        
            $consulta->execute();
         
        }
    
        public static function obtenerTodos()
        {
            $accesoDb = AccesoDb::obtenerInstancia();
            $consulta = $accesoDb->prepararConsulta("SELECT * FROM mesas");
            $consulta->execute();
    
            return $consulta->fetchAll(PDO::FETCH_CLASS, 'Mesa');
    
        }

        public static function ObtenerIdPorCodigoDeMesa($codigoMesa)
        {
            $listaMesa = Mesa::obtenerTodos();
            $idMesa = -1;

            foreach ($listaMesa as $mesa) 
            {
                if($mesa->codigo_mesa == $codigoMesa)
                {
                    $idMesa = $mesa->id;
                    break;
                }
            }

            return $idMesa;
        }

        public static function VerificarEstadoMesa($idMesa)
        {
            $listaMesa = Mesa::obtenerTodos();
            $estadoMesa = -1;

            foreach ($listaMesa as $mesa) 
            {
                if($mesa->id == $idMesa)
                {
                    $estadoMesa = $mesa->estado_mesa;
                    break;
                }
            }

            return $estadoMesa;
        }

        public static function CambiarEstadoMesa($idMesa, $estado_mesa)
        {
            $accesoDb = AccesoDB::obtenerInstancia();

            $consulta = $accesoDb->prepararConsulta("UPDATE mesas SET estado_mesa = :estado_mesa WHERE id = :id");
    
            $consulta->bindValue(':id', $idMesa, PDO::PARAM_INT);
            $consulta->bindValue(':estado_mesa', $estado_mesa, PDO::PARAM_INT);
    
            $consulta->execute();
    
            return $consulta->rowCount();
        }



    }

?>