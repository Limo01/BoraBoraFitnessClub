<?php
namespace DB;

class DBAccess {
    private const HOST_DB = "127.0.0.1";
    private const DATABASE_NAME = "user";
    private const USERNAME = "user";
    private const PASSWORD = "pwd";

    private $connection;

    private function openDBConnection() {
        $this->connection = mysqli_connect(
            DBAccess::HOST_DB,
            DBAccess::USERNAME,
            DBAccess::PASSWORD,
            DBAccess::DATABASE_NAME
        );
        return mysqli_errno($this->connection) ? false : true;
    }

    private function closeConnection() {
        mysqli_close($this->connection);
    }

	public function getUserName() {
		if ($this->openDBConnection()) {
            $stringa = "ciao";
			$this->closeConnection();
		}
        else {
            $stringa = "errore";
        }
        return $stringa;
	}
}

$pippo = new DBAccess();

$paginaHTML = file_get_contents("home.html");
echo str_replace("<maxPersone />", $pippo.getUserName(), $paginaHTML);

?>
