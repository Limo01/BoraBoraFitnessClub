<?php
    namespace DB;

    class DBAccess {
        /*private const HOST_DB = "127.0.0.1"; // == localhost
        private const DATABASE_NAME = "borabora"; // username laboratorio
        private const USERNAME = "root"; // username laboratorio
        private const PASSWORD = ""; // password per phpmyadmin

        private $connection;

        public function openDBConnection() {
            $this->connection = new \mysqli(DBAccess::HOST_DB, DBAccess::USERNAME, DBAccess::PASSWORD, DBAccess::DATABASE_NAME);
            return !$this->connection->connect_errno;
        }*/

        private $connection;

        public function openDBConnection() {
            $file = fopen("db.conf", "r") or die("Impossibile aprire il file di configurazione del database");
            
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
        public function doReadQuery($query, $paramsType, ...$params) {
            $stmt = $this->connection->prepare($query);

            $stmt->bind_param($paramsType, ...$params);

            $stmt->execute();
            $result = $stmt->get_result();

            return $result->fetch_all(MYSQLI_ASSOC);
        }

        public function isUsernameCorrect($submitted) {
            if($this->openDBConnection()){
                $result= $this->doReadQuery("SELECT username FROM utente WHERE username = ?", "s", $submitted);
                $this->closeConnection();
                
                return count($result) != 0;
            }
            else die("Errore in isUsernameCorrect(): " . $this->connection->mysql_error());
        }

        public function isPasswordCorrect($name, $password) {
            if($this->openDBConnection()){
                $result= $this->doReadQuery("SELECT password FROM utente WHERE username = ?", "s", $name);

                if(count($result)==0)
                    return false;

                return $result[0]['password'] === $password;
            }
            else die("Errore in isPasswordCorrect(): " . $this->connection->mysql_error());
        }


        public function doWriteQuery($query, $paramsType=0, ...$params) {
            $stmt = $this->connection->prepare($query);

            if(count($params)>0){
                $stmt->bind_param($paramsType, ...$params);
                echo count($params);
            }

            return $stmt->execute();;
        }
    }
?>