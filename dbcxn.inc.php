<?php 
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "store200_db";

    $conn = mysqli_connect($servername, $username, $password, $dbname) or die("Could not connect to database : ".mysqli_error($conn));
    
?>