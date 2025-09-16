<?php
$host = "localhost";
$user = "root";
$pass = "";
$db = "inventory-system";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error){
    die("database connection failed:". $conn->connect_error);
}
?>