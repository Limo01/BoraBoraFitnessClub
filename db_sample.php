<?php
    namespace DB;

    class DBAccess {
        private const HOST_DB = "127.0.0.1"; // == localhost
        private const DATABASE_NAME = ""; // username laboratorio
        private const USERNAME = ""; // username laboratorio
        private const PASSWORD = ""; // password per phpmyadmin

        private $connection;

        public function openDBConnection() {
            $this->connection = mysqli_connect(DBAccess::HOST_DB, DBAccess::USERNAME, DBAccess::PASSWORD, DBAccess::DATABASE_NAME );
            return !mysqli_errno($this->connection);
        }

        public function closeConnection() {
            mysqli_close($this->connection);
        }

        public function doReadQuery($query) {
            $queryResult = mysqli_query($this->connection, $query) or die("Errore in doReadQuery(): " . mysqli_error($this->connection));

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


        public function doWriteQuery($query) {
            $queryResult = msqli_query($this->connection, $query) or die("Errore in doReadQuery(): " . msqli_error($this->connection));
            return msqli_affected_rows($this->connection) > 0;
        }
    }
?>