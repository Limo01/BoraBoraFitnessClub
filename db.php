<?php
    namespace DB;

    class DBAccess {
        private const HOST_DB = "127.0.0.1"; // == localhost
        private const DATABASE_NAME = "fprotopa"; // username laboratori
        private const USERNAME = "fprotopa"; // username laboratori
        private const PASSWORD = "aodoThohYe2choom"; // password per phpmyadmin

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

        public function isUsernameCorrect($submitted) {
            //TODO: anti injection
            $query = "SELECT username FROM cliente WHERE username = '".$submitted."'";
            $queryResult = mysqli_query($this->connection, $query) or die("Errore in isUsernameCorrect(): " . mysqli_error($this->connection));
    
            if(mysqli_num_rows($queryResult) != 0) {
                return true;
            } else {
                return false;
            }
        }

        public function isPasswordCorrect($name, $password) {
            //TODO: anti injection
            $query = "SELECT password FROM cliente WHERE username = '".$name."'";
            $queryResult = mysqli_query($this->connection, $query) or die("Errore in isPasswordCorrect(): " . mysqli_error($this->connection));
    
            $row = mysqli_fetch_row($queryResult);

            if(isset($row[0]) && $row[0] == $password) {
                return true;
            } else {
                return false;
            }
        }


    }
?>