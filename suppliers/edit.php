<?php
session_start();
include("../db.php");
if (!isset($_SESSION['username'])) { header("Location: ../index.php"); exit(); }

$id = (int)$_GET['id'];
$stmt = $conn->prepare("SELECT * FROM suppliers WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$supplier = $stmt->get_result()->fetch_assoc();
if (!$supplier) { die("Supplier not found"); }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name    = trim($_POST['name']);
    $contact = trim($_POST['contact']);
    $address = trim($_POST['address']);

    $stmt = $conn->prepare("UPDATE suppliers SET name=?, contact=?, address=? WHERE id=?");
    $stmt->bind_param("sssi", $name, $contact, $address, $id);
    $stmt->execute();
    header("Location: list.php");
    exit();
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Edit Supplier</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
  <style>
    :root{--primary:#6a11cb;--secondary:#2575fc;--bg:#f4f6f9;--card:#ffffff;--text:#333;--muted:#888;--shadow:0 8px 22px rgba(0,0,0,.1);--radius:14px;}
    body{margin:0;font-family:'Poppins',sans-serif;background:var(--bg);color:var(--text);}
    .top-bar{background:linear-gradient(135deg,var(--primary),var(--secondary));color:#fff;padding:18px 30px;}
    .top-bar h1{font-size:1.4rem;font-weight:500;}
    .main{max-width:600px;margin:35px auto;padding:0 20px;}
    .form-card{background:var(--card);border-radius:var(--radius);padding:25px;box-shadow:var(--shadow);}
    .form-group{margin-bottom:18px;}
    label{display:block;margin-bottom:6px;font-size:.9rem;font-weight:500;}
    input,textarea{width:100%;padding:10px 12px;border:1px solid #ddd;border-radius:8px;font-size:.95rem;transition:.3s;}
    input:focus,textarea:focus{outline:none;border-color:var(--secondary);box-shadow:0 0 8px rgba(37,117,252,.3);}
    textarea{resize:vertical;min-height:80px;}
    .btn{background:linear-gradient(45deg,var(--primary),var(--secondary));color:#fff;padding:10px 24px;border:none;border-radius:8px;font-size:.95rem;cursor:pointer;transition:.3s;}
    .btn:hover{background:linear-gradient(45deg,var(--secondary),var(--primary));}
    .cancel{background:#ccc;color:#fff;margin-left:10px;}
  </style>
</head>
<body>
<div class="top-bar"><h1>Edit Supplier</h1></div>

<div class="main">
  <div class="form-card">
    <form method="post">
      <div class="form-group">
        <label>Name *</label>
        <input name="name" value="<?= htmlspecialchars($supplier['name']) ?>" required>
      </div>
      <div class="form-group">
        <label>Contact</label>
        <input name="contact" value="<?= htmlspecialchars($supplier['contact']) ?>" placeholder="Phone / Email">
      </div>
      <div class="form-group">
        <label>Address</label>
        <textarea name="address" placeholder="Full address"><?= htmlspecialchars($supplier['address']) ?></textarea>
      </div>
      <button class="btn" type="submit">Update Supplier</button>
      <a class="btn cancel" href="list.php">Cancel</a>
    </form>
  </div>
</div>
</body>
</html>