<?php 
    require __DIR__  . "/../backend/session.php";

    // releasing all session variables stored
    session_unset();
    // destroying the session
    session_destroy();
    // redirecting the user back to the home page
    header("Location: ../pages/index.php");
?>