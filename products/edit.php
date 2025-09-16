<?php
session_start();
include("../db.php");
if (!isset($_SESSION['username'])) {
    header("Location: ../index.php");
    exit();
}

$id = (int)$_GET['id'];
$stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$product = $result->fetch_assoc();

if (!$product) {
    die("Product not found");
}

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $name = $_POST['name'];
    $sku = $_POST['sku'];
    $category = $_POST['category'];
    $quantity = (int)$_POST['quantity'];
    $price = (float)$_POST['price'];

    $stmt = $conn->prepare("UPDATE products SET name=?, sku=?, category=?, quantity=?, price=? WHERE id=?");
    $stmt->bind_param("sssiii", $name, $sku, $category, $quantity, $price, $id);

    if ($stmt->execute()) {
        header("Location: list.php");
        exit();
    } else {
        $error = "Error updating product: " . $stmt->error;
    }
}
?>
<html>
<head>
    <title>Edit Product</title>
</head>
<body>
    <h2>Edit Product</h2>
    <a href="list.php">Back to Products</a><br><br>
    <?php if (!empty($error)) echo "<p style='color:red'>$error</p>"; ?>
    <form method="post">
        <input type="text" name="name" value="<?= $product['name'] ?>" required><br><br>
        <input type="text" name="sku" value="<?= $product['sku'] ?>" required><br><br>
        <input type="text" name="category" value="<?= $product['category'] ?>"><br><br>
        <input type="number" name="quantity" min="0" value="<?= $product['quantity'] ?>" required><br><br>
        <input type="number" step="0.01" name="price" value="<?= $product['price'] ?>" required><br><br>
        <button type="submit">Update Product</button>
    </form>
</body>
</html>
