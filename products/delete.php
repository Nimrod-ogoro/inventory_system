<?php
session_start();
include("../db.php");
if (!isset($_SESSION['username'])) {
    header("Location: ../index.php");
    exit();
}

$id = (int)$_GET['id'];
$stmt = $conn->prepare("DELETE FROM products WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();

header("Location: list.php");
exit();
