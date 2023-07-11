<?php

    class AccesoDb{

        private $dbHost = "localhost";
        private $dbUser = "root";
        private $dbPass = "nortesur123";
        private $dbName = "dbrestaurante";
        private static $accesoDb;
        private $objetoPDO;

        private function __construct()
        {
            try{
                $mySqlConnection = "mysql:host=$this->dbHost;dbname=$this->dbName";

                $this->objetoPDO = new PDO($mySqlConnection, $this->dbUser, $this->dbPass);
                
                $this->objetoPDO->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                
            } 
            catch (PDOException $e) {
                print "Error" . $e->getMessage();
                die();
            }
        }

        public static function obtenerInstancia()
        {
            if (!isset(self::$accesoDb)) {
                self::$accesoDb = new AccesoDb();
            }

            return self::$accesoDb;
        }

        public function prepararConsulta($sql)
        {
            return $this->objetoPDO->prepare($sql);
        }



    }



?>