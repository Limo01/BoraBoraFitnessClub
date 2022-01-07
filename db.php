<?php
	namespace DB;

	class DBAccess {
		private $connection;

		public function openDBConnection() {
			$file = fopen("db.conf", "r") or die("Impossibile aprire il file di configurazione del database");
			$host_db = fgets($file);
			$username = fgets($file);
			$password = fgets($file);
			$database_name = fgets($file);
			fclose($file);

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

        public function doWriteQuery($query) {
            $queryResult = mysqli_query($this->connection, $query) or die("Errore in doReadQuery(): " . mysqli_error($this->connection));
            return mysqli_affected_rows($this->connection) > 0;
        }
    }
?>