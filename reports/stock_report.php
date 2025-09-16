<?php
session_start();
include("../db.php");
if (!isset($_SESSION['username'])) { header("Location: ../index.php"); exit(); }

/* ---------- filters ---------- */
$cat = isset($_GET['category']) ? $conn->real_escape_string($_GET['category']) : '';
$low = isset($_GET['low']) ? 'AND quantity < 10' : '';

$where = '1=1';
if ($cat) $where .= " AND category = '$cat'";
if ($low) $where .= " $low";

$sql = "SELECT id, name, sku, category, quantity, price, (quantity * price) AS stock_value
        FROM products
        WHERE $where
        ORDER BY category, name";
$rows = $conn->query($sql);

$categories = $conn->query("SELECT DISTINCT category FROM products WHERE category<>'' ORDER BY category");
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Stock Report</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
  <style>
    :root{
      --primary:#6a11cb;
      --secondary:#2575fc;
      --danger:#ff4757;
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
    .low{background:#ffecec;color:var(--danger);}
    tfoot{font-weight:bold;background:var(--bg);}
    @media(max-width:768px){.main{padding:20px;}table{font-size:.85rem;}}
  </style>
</head>
<body>
<div class="top-bar">
  <h1>Stock Report</h1>
  <a class="btn" href="../dashboard.php">← Dashboard</a>
</div>

<div class="main">
  <!-- filters -->
  <form class="filters" method="get">
    <select name="category">
      <option value="">All Categories</option>
      <?php while ($c = $categories->fetch_assoc()): ?>
        <option value="<?= htmlspecialchars($c['category']) ?>" <?= $cat===$c['category']?'selected':'' ?>>
          <?= htmlspecialchars($c['category']) ?>
        </option>
      <?php endwhile; ?>
    </select>

    <label><input type="checkbox" name="low" <?= $low?'checked':'' ?>> Low stock only (&lt;10)</label>
    <button class="btn" type="submit">Filter</button>
  </form>

  <!-- table -->
  <div class="section">
    <table>
      <thead>
        <tr>
          <th>ID</th><th>SKU</th><th>Name</th><th>Category</th>
          <th>Qty</th><th>Unit Price</th><th>Stock Value</th>
        </tr>
      </thead>
      <tbody>
        <?php
        $totalValue = 0;
        while ($r = $rows->fetch_assoc()):
            $totalValue += $r['stock_value'];
            $rowClass = $r['quantity'] < 10 ? 'low' : '';
        ?>
        <tr class="<?= $rowClass ?>">
          <td><?= $r['id'] ?></td>
          <td><?= htmlspecialchars($r['sku']) ?></td>
          <td><?= htmlspecialchars($r['name']) ?></td>
          <td><?= htmlspecialchars($r['category']) ?></td>
          <td><?= $r['quantity'] ?></td>
          <td><?= number_format($r['price'],2) ?></td>
          <td><?= number_format($r['stock_value'],2) ?></td>
        </tr>
        <?php endwhile; ?>
      </tbody>
      <tfoot>
        <tr><td colspan="6">Total Stock Value</td><td><?= number_format($totalValue,2) ?></td></tr>
      </tfoot>
    </table>
  </div>
</div>
</body>
</html>