<?php
     session_start();
     // echo "test";
     session_destroy();
     header('location:index.php');
?>