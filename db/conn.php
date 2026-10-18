<?php 
    // setting up vairables for database connection
    // fill in with your own credentials
    $servername = "";
    $dBUserName = "";
    $dBPassword = '';
    $dbName = "";


    // storing connection in a variable
    $conn = mysqli_connect($servername, $dBUserName, $dBPassword, $dbName);

    // checking connection
    if(!$conn){
        // throwing an error if connection is unsuccessful
        die("connection failed: ". mysqli_connect_error());
    }
?>