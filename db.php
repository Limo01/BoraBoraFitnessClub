<?php
	namespace DB;

	class DBAccess {
		private $connection;

		public function openDBConnection() {
			$file = fopen("db.conf", "r") or die("Impossibile aprire il file di configurazione del database");
<<<<<<< HEAD
			$host_db = fgets($file);
			$username = fgets($file);
			$password = fgets($file);
			$database_name = fgets($file);
			fclose($file);
=======
			$host_db = trim(fgets($file));
			$username = trim(fgets($file));
			$password = trim(fgets($file));
			$database_name = trim(fgets($file));
			fclose($file);
            if ($password == '""') $password = "";
>>>>>>> cdb62e457d19602a1039dbc9cbfad83d522a7b46

			$this->connection = mysqli_connect($host_db, $username, $password, $database_name);
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

<<<<<<< HEAD
        public function doWriteQuery($query) {
            $queryResult = mysqli_query($this->connection, $query) or die("Errore in doReadQuery(): " . mysqli_error($this->connection));
            return mysqli_affected_rows($this->connection) > 0;
=======
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
>>>>>>> cdb62e457d19602a1039dbc9cbfad83d522a7b46
        }
    }
?>