<?php
session_start();
include("../db.php");
if (!isset($_SESSION['username'])) { header("Location: ../index.php"); exit(); }

/* -------- date range -------- */
$from = $_GET['from'] ?? date('Y-m-01');
$to   = $_GET['to']   ?? date('Y-m-d');

/* -------- data -------- */
$sql = "
SELECT p.name, p.price, SUM(st.quantity) AS qty_sold,
       SUM(st.quantity * p.price) AS sales_value
FROM stock_transactions st
JOIN products p ON p.id = st.product_id
WHERE st.type = 'out'
  AND DATE(st.created_at) BETWEEN ? AND ?
GROUP BY p.id, p.name, p.price
ORDER BY sales_value DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param('ss', $from, $to);
$stmt->execute();
$rows = $stmt->get_result();
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Sales Report</title>
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
    .filters input{flex:1 1 200px;padding:8px 10px;border:1px solid #ddd;border-radius:8px;}
    .section{background:var(--card);border-radius:var(--radius);padding:25px;box-shadow:var(--shadow);}
    table{width:100%;border-collapse:collapse;font-size:.9rem;margin-top:15px;}
    th,td{padding:10px 12px;text-align:left;}
    thead tr{border-bottom:2px solid var(--bg);}
    tbody tr:hover{background:var(--bg);}
    tfoot{font-weight:bold;background:var(--bg);}
    @media(max-width:768px){.main{padding:20px;}table{font-size:.85rem;}}
  </style>
</head>
<body>
<div class="top-bar">
  <h1>Sales Report</h1>
  <a class="btn" href="../dashboard.php">← Dashboard</a>
</div>

<div class="main">
  <!-- date filter -->
  <form class="filters" method="get">
    <input type="date" name="from" value="<?= htmlspecialchars($from) ?>" required>
    <input type="date" name="to"   value="<?= htmlspecialchars($to)   ?>" required>
    <button class="btn" type="submit">Show</button>
  </form>

  <!-- table -->
  <div class="section">
    <table>
      <thead>
        <tr>
          <th>Product</th><th>Unit Price</th><th>Qty Sold</th><th>Sales Value</th>
        </tr>
      </thead>
      <tbody>
        <?php
        $totalQty = $totalValue = 0;
        while ($r = $rows->fetch_assoc()):
            $totalQty   += $r['qty_sold'];
            $totalValue += $r['sales_value'];
        ?>
        <tr>
          <td><?= htmlspecialchars($r['name']) ?></td>
          <td><?= number_format($r['price'],2) ?></td>
          <td><?= $r['qty_sold'] ?></td>
          <td><?= number_format($r['sales_value'],2) ?></td>
        </tr>
        <?php endwhile; ?>
      </tbody>
      <tfoot>
        <tr>
          <td>Totals</td><td>-</td>
          <td><?= $totalQty ?></td>
          <td><?= number_format($totalValue,2) ?></td>
        </tr>
      </tfoot>
    </table>
  </div>
</div>
</body>
</html>