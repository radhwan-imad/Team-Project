<?php
//Server name is localhost
$servername = "localhost";
//In my case, User name is root
$username = "root";
//password is empty
$password = "";
//database name is aura
$database = "aura";
// Creating a connection
$conn = new mysqli($servername,$username,$password,$database);
// check connection
if ($conn->connect_error){
    die("Connection failure: ". $conn->connect_error);
}
?>