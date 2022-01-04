<?php
    namespace DB;

    class DBAccess {
        private const HOST_DB = "127.0.0.1"; // == localhost
        private const DATABASE_NAME = "agazi"; // username laboratori
        private const USERNAME = "agazi"; // username laboratori
        private const PASSWORD = ""; // password per phpmyadmin

        private $connection;

        public function openDBConnection() {
            $this->connection = mysqli_connect(DBAccess::HOST_DB, 
                                               DBAccess::USERNAME,
                                               DBAccess::PASSWORD,
                                               DBAccess::DATABASE_NAME
                                               );
            if(mysqli_errno($this->connection)) {
                return false;
            } else {
                return true;
            }
        }

        public function closeConnection() {
            mysqli_close($this->connection);
        }
    
        public function getClienti() {
            $query = "SELECT * FROM cliente";
            $queryResult = mysqli_query($this->connection, $query) or die("Errore in getClienti(): " . mysqli_error($this->connection));

            if(mysqli_num_rows($queryResult) != 0) {
                $result = array();
                while ($row = mysqli_fetch_assoc($queryResult)) {
                    array_push($result, $row);
                }
                $queryResult->free();
                return $result;
            } else {
                return null;
            }
        }
    }
?>