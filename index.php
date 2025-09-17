<?php


session_start();
include("db.php")


if ($_SERVER['REQUEST_METHOD']== "POST") {
$username = trim($_POST['username']);
$password = trim($_POST['password']);


$stmt = $conn -> prepare("SELECT id, username, password, role FROM users WHERE username =?")

$stmt -> binf_param("s", u$username)}?>