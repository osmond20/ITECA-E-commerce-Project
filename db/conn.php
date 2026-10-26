<?php 
    // setting up vairables for database connection
    // fill in with your own credentials
    $servername = "";
    $dBUserName = "";
    $dBPassword = '';
    $dbName = "";


    // storing connection in a variable
    $conn = mysqli_connect($servername, $dBUserName, $dBPassword, $dbName);

    // checking connection and returns an error message if the connection fails
    if(!$conn){
        // throwing an error if connection is unsuccessful
        error_log('Database connection failed: ' . mysqli_connect_error());
        http_response_code(500);
        exit('The service is temporarily unavailable.');
    }
    mysqli_set_charset($conn, 'utf8mb4');

?>
