<?php
    session_start();
    $_SESSION["loggedin"] = false;
    unset($_SESSION["username"]);
    header("location: ../index.html");
?>