<?php
session_start();
include("../db.php");
if (!isset($_SESSION['username'])) { header("Location: ../index.php"); exit(); }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name     = trim($_POST['name']);
    $sku      = trim($_POST['sku']);
    $category = trim($_POST['category']);
    $quantity = (int)$_POST['quantity'];
    $price    = (float)$_POST['price'];

    $stmt = $conn->prepare("INSERT INTO products (name, sku, category, quantity, price) VALUES (?,?,?,?,?)");
    $stmt->bind_param("sssii", $name, $sku, $category, $quantity, $price);
    if ($stmt->execute()) {
        header("Location: list.php");
        exit();
    } else {
        $error = "Error adding product: " . $stmt->error;
    }
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Add Product</title>
  <style>
    /* ---------- plain css ---------- */
    *{box-sizing:border-box;margin:0;padding:0;}
    body{font-family:Arial,Helvetica,sans-serif;font-size:14px;background:#f2f2f2;color:#333;}
    .top-bar{background:#444;color:#fff;padding:15px 20px;display:flex;align-items:center;justify-content:space-between;}
    .top-bar h1{font-size:18px;font-weight:600;}
    .btn{background:#555;color:#fff;padding:8px 16px;border:none;border-radius:4px;font-size:14px;cursor:pointer;}
    .btn:hover{background:#666;}
    .cancel{background:#888;margin-left:10px;}
    .main{max-width:600px;margin:30px auto;padding:0 15px;}
    .form-card{background:#fff;border:1px solid #ddd;border-radius:6px;padding:20px;}
    .form-group{margin-bottom:15px;}
    label{display:block;margin-bottom:5px;font-weight:600;font-size:13px;}
    input,select{width:100%;padding:8px 10px;border:1px solid #ccc;border-radius:4px;font-size:14px;}
    input:focus,select:focus{outline:none;border-color:#888;}
    .error{color:#d00;font-size:12px;margin-bottom:10px;}
  </style>
</head>
<body>
<div class="top-bar">
  <h1>Add Product</h1>
  <a class="btn" href="list.php">← Back to Products</a>
</div>

<div class="main">
  <div class="form-card">
    <?php if (!empty($error)) echo "<div class='error'>$error</div>"; ?>
    <form method="post">
      <div class="form-group">
        <label>Product Name *</label>
        <input name="name" placeholder="e.g. Wireless Mouse" required>
      </div>
      <div class="form-group">
        <label>SKU *</label>
        <input name="sku" placeholder="Unique code" required>
      </div>
      <div class="form-group">
        <label>Category</label>
        <input name="category" placeholder="e.g. Electronics">
      </div>
      <div class="form-group">
        <label>Quantity *</label>
        <input type="number" name="quantity" min="0" required>
      </div>
      <div class="form-group">
        <label>Price *</label>
        <input type="number" step="0.01" name="price" required>
      </div>
      <button class="btn" type="submit">Save Product</button>
      <a class="btn cancel" href="list.php">Cancel</a>
    </form>
  </div>
</div>
</body>
</html>