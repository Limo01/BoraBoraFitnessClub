<?php
    namespace DB;

    class DBAccess {
        private $connection;

        public function openDBConnection() {
            $host_db = "127.0.0.1";
            $username = "agazi";
            $password = "HeYool1zai2iePoh";
            $database_name = "agazi";

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