<?php
session_start();
include("db.php");
if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}

/* ---------- dashboard counts ---------- */
$totalProducts  = $conn->query("SELECT COUNT(*) FROM products")->fetch_row()[0];
$lowStock       = $conn->query("SELECT COUNT(*) FROM products WHERE quantity < 10")->fetch_row()[0];
$totalSuppliers = $conn->query("SELECT COUNT(*) FROM suppliers")->fetch_row()[0];
$last7Moves     = $conn->query(
        "SELECT COUNT(*) FROM stock_transactions WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)"
)->fetch_row()[0];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Dashboard - Inventory System</title>
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
*{
    box-sizing:border-box;
    font-family:'Poppins',sans-serif;
}
body{
    margin:0;
    background:var(--bg);
    color:var(--text);
}
a{
    text-decoration:none;
    color:inherit;
}
/* ---------- top-bar ---------- */
.top-bar{
    background:linear-gradient(135deg,var(--primary),var(--secondary));
    color:#fff;
    padding:18px 30px;
    display:flex;
    align-items:center;
    justify-content:space-between;
}
.top-bar h1{
    font-size:1.4rem;
    font-weight:500;
}
.user-info{
    font-size:.9rem;
}
.user-info a{
    margin-left:12px;
    background:rgba(255,255,255,.2);
    padding:6px 14px;
    border-radius:20px;
    transition:background .3s;
}
.user-info a:hover{
    background:rgba(255,255,255,.3);
}
/* ---------- side nav ---------- */
.side-nav{
    width:240px;
    background:var(--card);
    height:calc(100vh - 62px);
    box-shadow:var(--shadow);
    position:fixed;
    padding-top:20px;
}
.side-nav a{
    display:block;
    padding:14px 30px;
    font-size:.95rem;
    transition:.2s;
    border-left:4px solid transparent;
}
.side-nav a:hover{
    background:var(--bg);
    border-left-color:var(--secondary);
}
.side-nav a.active{
    background:var(--bg);
    border-left-color:var(--primary);
    font-weight:500;
}
/* ---------- main content ---------- */
.main{
    margin-left:240px;
    padding:35px;
}
.page-title{
    font-size:1.8rem;
    margin-bottom:8px;
}
.breadcrumb{
    font-size:.85rem;
    color:var(--muted);
    margin-bottom:30px;
}
/* ---------- metric cards ---------- */
.metrics{
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(220px,1fr));
    gap:25px;
    margin-bottom:40px;
}
.metric-card{
    background:var(--card);
    border-radius:var(--radius);
    padding:25px;
    box-shadow:var(--shadow);
    display:flex;
    align-items:center;
    justify-content:space-between;
    transition:transform .3s;
}
.metric-card:hover{
    transform:translateY(-5px);
}
.metric-card .num{
    font-size:2.2rem;
    font-weight:600;
}
.metric-card .label{
    font-size:.9rem;
    color:var(--muted);
}
.metric-card .icon{
    font-size:2.5rem;
    opacity:.15;
}
.metric-card.danger{
    color:var(--danger);
}
/* ---------- recent table ---------- */
.section{
    background:var(--card);
    border-radius:var(--radius);
    padding:25px;
    box-shadow:var(--shadow);
}
.section h3{
    margin-bottom:20px;
    font-weight:500;
}
table{
    width:100%;
    border-collapse:collapse;
    font-size:.9rem;
}
th,td{
    padding:10px 12px;
    text-align:left;
}
thead tr{
    border-bottom:2px solid var(--bg);
}
tbody tr:hover{
    background:var(--bg);
}
.status{
    padding:4px 10px;
    border-radius:12px;
    font-size:.75rem;
    font-weight:500;
}
.status.low{
    background:#ffecec;
    color:var(--danger);
}
/* ---------- responsiveness ---------- */
@media(max-width:768px){
    .side-nav{
        width:100%;
        height:auto;
        position:relative;
    }
    .main{
        margin-left:0;
        padding:20px;
    }
}
</style>
</head>
<body>

<!-- ================= TOP BAR ================= -->
<div class="top-bar">
    <h1>Inventory System</h1>
    <div class="user-info">
        <span>👋 <?= htmlspecialchars($_SESSION['username']) ?></span>
        <a href="logout.php">Logout</a>
    </div>
</div>

<!-- ================= SIDE NAV ================= -->
<nav class="side-nav">
    <a href="dashboard.php" class="active">📊 Dashboard</a>
    <a href="products/list.php">📦 Products</a>
    <a href="suppliers/list.php">🏷 Suppliers</a>
    <a href="stock/history.php">📋 Stock History</a>
    <a href="reports/stock_report.php">📈 Reports</a>
</nav>

<!-- ================= MAIN CONTENT ================= -->
<div class="main">
    <h2 class="page-title">Dashboard Overview</h2>
    <div class="breadcrumb">Home / Dashboard</div>

    <!-- metric cards -->
    <div class="metrics">
        <div class="metric-card">
            <div>
                <div class="num"><?= $totalProducts ?></div>
                <div class="label">Total Products</div>
            </div>
            <div class="icon">📦</div>
        </div>

        <div class="metric-card danger">
            <div>
                <div class="num"><?= $lowStock ?></div>
                <div class="label">Low Stock Items</div>
            </div>
            <div class="icon">⚠️</div>
        </div>

        <div class="metric-card">
            <div>
                <div class="num"><?= $totalSuppliers ?></div>
                <div class="label">Suppliers</div>
            </div>
            <div class="icon">🏷</div>
        </div>

        <div class="metric-card">
            <div>
                <div class="num"><?= $last7Moves ?></div>
                <div class="label">Moves (7 days)</div>
            </div>
            <div class="icon">📋</div>
        </div>
    </div>

    <!-- quick stock table -->
    <div class="section">
        <h3>Low-Stock Products</h3>
        <?php
        $lows = $conn->query(
            "SELECT id, name, sku, quantity FROM products WHERE quantity < 10 ORDER BY quantity ASC LIMIT 10"
        );
        if ($lows->num_rows):
        ?>
        <table>
            <thead>
                <tr><th>ID</th><th>SKU</th><th>Name</th><th>Quantity</th><th>Status</th></tr>
            </thead>
            <tbody>
                <?php while ($p = $lows->fetch_assoc()): ?>
                <tr>
                    <td><?= $p['id'] ?></td>
                    <td><?= htmlspecialchars($p['sku']) ?></td>
                    <td><?= htmlspecialchars($p['name']) ?></td>
                    <td><?= $p['quantity'] ?></td>
                    <td><span class="status low">Low</span></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <?php else: ?>
        <p style="color:#888">No low-stock items 🎉</p>
        <?php endif; ?>
    </div>
</div>

</body>
</html>