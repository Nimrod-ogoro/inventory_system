<?php
session_start();
include("../db.php");
if (!isset($_SESSION['username'])) { header("Location: ../index.php"); exit(); }

$rows = $conn->query("SELECT * FROM suppliers ORDER BY name");
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Suppliers</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
  <style>
    :root{--primary:#6a11cb;--secondary:#2575fc;--danger:#ff4757;--bg:#f4f6f9;--card:#ffffff;--text:#333;--muted:#888;--shadow:0 8px 22px rgba(0,0,0,.1);--radius:14px;}
    body{margin:0;font-family:'Poppins',sans-serif;background:var(--bg);color:var(--text);}
    .top-bar{background:linear-gradient(135deg,var(--primary),var(--secondary));color:#fff;padding:18px 30px;display:flex;align-items:center;justify-content:space-between;}
    .top-bar h1{font-size:1.4rem;font-weight:500;}
    .btn{background:linear-gradient(45deg,var(--primary),var(--secondary));color:#fff;padding:8px 18px;border-radius:8px;font-size:.9rem;border:none;cursor:pointer;transition:.3s;}
    .btn:hover{background:linear-gradient(45deg,var(--secondary),var(--primary));}
    .main{padding:35px;max-width:1200px;margin:auto;}
    .section{background:var(--card);border-radius:var(--radius);padding:25px;box-shadow:var(--shadow);}
    table{width:100%;border-collapse:collapse;font-size:.9rem;margin-top:15px;}
    th,td{padding:10px 12px;text-align:left;}
    thead tr{border-bottom:2px solid var(--bg);}
    tbody tr:hover{background:var(--bg);}
    .actions a{margin-right:10px;color:var(--secondary);font-weight:500;}
    .actions a.del{color:var(--danger);}
    @media(max-width:768px){.main{padding:20px;}table{font-size:.85rem;}}
  </style>
</head>
<body>
<div class="top-bar">
  <h1>Suppliers</h1>
  <a class="btn" href="../dashboard.php">Dashboard</a>
  <a class="btn" href="add.php">+ Add Supplier</a>
</div>

<div class="main">
  <div class="section">
    <table>
      <thead>
        <tr><th>ID</th><th>Name</th><th>Contact</th><th>Address</th><th>Actions</th></tr>
      </thead>
      <tbody>
        <?php while ($r = $rows->fetch_assoc()): ?>
        <tr>
          <td><?= $r['id'] ?></td>
          <td><?= htmlspecialchars($r['name']) ?></td>
          <td><?= htmlspecialchars($r['contact']) ?></td>
          <td><?= nl2br(htmlspecialchars($r['address'])) ?></td>
          <td class="actions">
            <a href="edit.php?id=<?= $r['id'] ?>">Edit</a>
            <a class="del" href="delete.php?id=<?= $r['id'] ?>" onclick="return confirm('Delete this supplier?')">Delete</a>
          </td>
        </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>
</div>
</body>
</html>