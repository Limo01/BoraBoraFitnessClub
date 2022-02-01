<?php
    namespace DB;

    class DBAccess {
        private $connection;

        public function openDBConnection() {
            $file = fopen("db.conf", "r") or $file = fopen("../db.conf", "r") or die("Impossibile aprire il file di configurazione del database");
            
            $host_db = trim(fgets($file));
            $username = trim(fgets($file));
            $password = trim(fgets($file));
            $database_name = trim(fgets($file));
            
            fclose($file);

            if ($password == '""') $password = "";

            $this->connection = new \mysqli($host_db, $username, $password, $database_name);
            return !$this->connection->connect_errno;
        }

        public function closeConnection() {
            $this->connection->close();
        }

        /*
        * ParamsType: s => string, i => int, d => double, b => blob
        */
        public function doReadQuery($query, $paramsType="", ...$params) {
            $stmt = $this->connection->prepare($query) or die('Errore in doReadQuery(): prepare() fallito: ' . mysqli_error($this->connection));

            if ($paramsType != "") {
                $stmt->bind_param($paramsType, ...$params) or die('Errore in doReadQuery(): bind() fallito: ' . mysqli_error($this->connection));
            }

            $stmt->execute() or die('Errore in doReadQuery(): execute() fallito: ' . mysqli_error($this->connection));

            $result = $stmt->get_result();

            $val = $result->fetch_all(MYSQLI_ASSOC);
            $result->free();

            return $val;
        }

        /*
        * ParamsType: s => string, i => int, d => double, b => blob
        */
        public function doWriteQuery($query, $paramsType="", ...$params) {
            $stmt = $this->connection->prepare($query) or die('Errore in doWriteQuery(): prepare() fallito: ' . mysqli_error($this->connection));

            if(count($params)>0){
                $stmt->bind_param($paramsType, ...$params) or die('Errore in doWriteQuery(): bind() fallito: ' . mysqli_error($this->connection));
            }

            return $stmt->execute() or die('Errore in doWriteQuery(): execute() fallito: ' . mysqli_error($this->connection));
        }
    }
?>