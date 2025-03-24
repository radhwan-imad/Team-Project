<?php
//Server name is localhost
$servername = "localhost";
//In my case, User name is root
$username = "cs2team40";
//password is empty
$password = "q6jxdg3oFxNuvVi";
//database name is aura
$database = "cs2team40_aura";
// Creating a connection
$conn = new mysqli($servername,$username,$password,$database);
// check connection
if ($conn->connect_error){
    die("Connection failure: ". $conn->connect_error);
}
?>