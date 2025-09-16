<?php
session_start();
include("../db.php");
if (!isset($_SESSION['username'])) { header("Location: ../index.php"); exit(); }

$result = $conn->query("SELECT * FROM products");
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Products - Inventory System</title>
  <style>
    /* ---------- plain css ---------- */
    *{box-sizing:border-box;margin:0;padding:0;}
    body{font-family:Arial,Helvetica,sans-serif;font-size:14px;background:#f2f2f2;color:#333;}
    .top-bar{background:#444;color:#fff;padding:15px 20px;display:flex;align-items:center;justify-content:space-between;}
    .top-bar h1{font-size:18px;font-weight:600;}
    .nav{margin-top:5px;}
    .nav a{background:#555;color:#fff;padding:6px 12px;margin-right:6px;border-radius:4px;text-decoration:none;font-size:13px;}
    .nav a:hover{background:#666;}
    .main{padding:20px;}
    table{width:100%;border-collapse:collapse;background:#fff;border:1px solid #ccc;margin-top:10px;}
    th,td{padding:8px 10px;text-align:left;border:1px solid #ddd;}
    th{background:#eee;font-weight:600;}
    tr:hover{background:#f9f9f9;}
    .actions a{color:#035397;margin-right:8px;text-decoration:none;}
    .actions a:hover{text-decoration:underline;}
    .actions a.del{color:#d00;}
  </style>
</head>
<body>
<div class="top-bar">
  <h1>Products</h1>
  <div class="nav">
    <a href="add.php">+ Add Product</a>
    <a href="../dashboard.php">Dashboard</a>
  </div>
</div>

<div class="main">
  <table>
    <thead>
      <tr>
        <th>ID</th><th>Name</th><th>SKU</th><th>Category</th><th>Quantity</th><th>Price</th><th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php while ($row = $result->fetch_assoc()): ?>
      <tr>
        <td><?= $row['id'] ?></td>
        <td><?= htmlspecialchars($row['name']) ?></td>
        <td><?= htmlspecialchars($row['sku']) ?></td>
        <td><?= htmlspecialchars($row['category']) ?></td>
        <td><?= $row['quantity'] ?></td>
        <td><?= number_format($row['price'],2) ?></td>
        <td class="actions">
          <a href="edit.php?id=<?= $row['id'] ?>">Edit</a>
          <a class="del" href="delete.php?id=<?= $row['id'] ?>" onclick="return confirm('Delete this product?')">Delete</a>
        </td>
      </tr>
      <?php endwhile; ?>
    </tbody>
  </table>
</div>
</body>
</html>
