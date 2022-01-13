<?php
    if (isset($_POST['action'])) {
    	if ($_POST['action'] == "Segui") {
    		segui();
    	} else {
    		elimina();
    	}
    }

    function segui() {
        header('Location: '."https://stackoverflow.com/questions/3780912/sending-post-data-without-form");
    }

    function elimina() {
        echo "The insert function is called.";
    }
?>