<?php
session_start();
include("../db.php");
if (!isset($_SESSION['username'])) { header("Location: ../index.php"); exit(); }

/* ---------- filters ---------- */
$product = isset($_GET['product']) ? (int)$_GET['product'] : 0;
$type    = isset($_GET['type'])    ? $_GET['type']         : '';
$from    = isset($_GET['from'])    ? $_GET['from']         : '';
$to      = isset($_GET['to'])      ? $_GET['to']           : '';

$where = [];
if ($product) $where[] = "st.product_id = $product";
if ($type)    $where[] = "st.type = '$type'";
if ($from)    $where[] = "DATE(st.created_at) >= '$from'";
if ($to)      $where[] = "DATE(st.created_at) <= '$to'";
$whereSql = $where ? "WHERE " . implode(' AND ', $where) : '';

/* ---------- data ---------- */
$sql = "
SELECT st.id, p.name, st.type, st.quantity, st.created_at, u.username
FROM stock_transactions st
JOIN products p ON p.id = st.product_id
LEFT JOIN users u ON u.id = st.user_id
$whereSql
ORDER BY st.id DESC
LIMIT 500";
$rows = $conn->query($sql);

/* ---------- dropdowns ---------- */
$products = $conn->query("SELECT id, name FROM products ORDER BY name");
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Stock History</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
  <style>
    :root{
      --primary:#6a11cb;
      --secondary:#2575fc;
      --bg:#f4f6f9;
      --card:#ffffff;
      --text:#333;
      --muted:#888;
      --shadow:0 8px 22px rgba(0,0,0,.1);
      --radius:14px;
    }
    *{box-sizing:border-box;font-family:'Poppins',sans-serif;}
    body{margin:0;background:var(--bg);color:var(--text);}
    .top-bar{background:linear-gradient(135deg,var(--primary),var(--secondary));color:#fff;padding:18px 30px;display:flex;align-items:center;justify-content:space-between;}
    .top-bar h1{font-size:1.4rem;font-weight:500;}
    .btn{background:linear-gradient(45deg,var(--primary),var(--secondary));color:#fff;padding:8px 18px;border-radius:8px;font-size:.9rem;border:none;cursor:pointer;transition:.3s;}
    .btn:hover{background:linear-gradient(45deg,var(--secondary),var(--primary));}
    .main{padding:35px;max-width:1200px;margin:auto;}
    .filters{background:var(--card);border-radius:var(--radius);padding:20px;box-shadow:var(--shadow);display:flex;flex-wrap:wrap;gap:15px;margin-bottom:25px;}
    .filters select,.filters input{flex:1 1 200px;padding:8px 10px;border:1px solid #ddd;border-radius:8px;}
    .section{background:var(--card);border-radius:var(--radius);padding:25px;box-shadow:var(--shadow);}
    table{width:100%;border-collapse:collapse;font-size:.9rem;margin-top:15px;}
    th,td{padding:10px 12px;text-align:left;}
    thead tr{border-bottom:2px solid var(--bg);}
    tbody tr:hover{background:var(--bg);}
    .badge{padding:4px 10px;border-radius:12px;font-size:.75rem;font-weight:500;}
    .badge.in{background:#d4f8d4;color:#2a7d2a;}
    .badge.out{background:#ffecec;color:#d62828;}
    @media(max-width:768px){.main{padding:20px;}table{font-size:.85rem;}}
  </style>
</head>
<body>
<div class="top-bar">
  <h1>Stock History</h1>
  <div>
    <a class="btn" href="../dashboard.php">← Dashboard</a>
    <a class="btn" href="move.php">+ New Move</a>
  </div>
</div>

<div class="main">
  <!-- filters -->
  <form class="filters" method="get">
    <select name="product">
      <option value="">All Products</option>
      <?php while ($p = $products->fetch_assoc()): ?>
        <option value="<?= $p['id'] ?>" <?= $product==$p['id']?'selected':'' ?>><?= htmlspecialchars($p['name']) ?></option>
      <?php endwhile; ?>
    </select>

    <select name="type">
      <option value="">All Types</option>
      <option value="in"  <?= $type==='in'?'selected':'' ?>>Stock IN</option>
      <option value="out" <?= $type==='out'?'selected':'' ?>>Stock OUT</option>
    </select>

    <input type="date" name="from" value="<?= htmlspecialchars($from) ?>" placeholder="From">
    <input type="date" name="to"   value="<?= htmlspecialchars($to)   ?>" placeholder="To">
    <button class="btn" type="submit">Filter</button>
  </form>

  <!-- table -->
  <div class="section">
    <table>
      <thead>
        <tr><th>ID</th><th>Product</th><th>Type</th><th>Qty</th><th>By</th><th>Time</th></tr>
      </thead>
      <tbody>
        <?php while ($r = $rows->fetch_assoc()): ?>
        <tr>
          <td><?= $r['id'] ?></td>
          <td><?= htmlspecialchars($r['name']) ?></td>
          <td><span class="badge <?= $r['type'] ?>"><?= strtoupper($r['type']) ?></span></td>
          <td><?= $r['quantity'] ?></td>
          <td><?= htmlspecialchars($r['username'] ?: 'system') ?></td>
          <td><?= date('Y-m-d H:i', strtotime($r['created_at'])) ?></td>
        </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>
</div>
</body>
</html>