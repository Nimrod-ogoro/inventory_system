<?php
session_start();
include("../db.php");
if (!isset($_SESSION['username'])) { header("Location: ../index.php"); exit(); }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = (int)$_POST['product_id'];
    $type       = in_array($_POST['type'], ['in','out']) ? $_POST['type'] : exit('Bad type');
    $qty        = (int)$_POST['quantity'];
    if ($qty <= 0) exit('Quantity must be > 0');

    $conn->begin_transaction();
    try {
        // lock row so parallel requests can’t race
        $stmt = $conn->prepare("SELECT quantity FROM products WHERE id = ? FOR UPDATE");
        $stmt->bind_param('i', $product_id);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        if (!$row) throw new Exception('Product not found');

        $newQty = $type === 'in'
                  ? $row['quantity'] + $qty
                  : $row['quantity'] - $qty;

        if ($newQty < 0) throw new Exception('Insufficient stock');

        // update product
        $stmt = $conn->prepare("UPDATE products SET quantity = ? WHERE id = ?");
        $stmt->bind_param('ii', $newQty, $product_id);
        $stmt->execute();

        // record transaction
        $user_id = $_SESSION['user_id'] ?? 0;   // we will add user_id to session in next step
        $stmt = $conn->prepare(
            "INSERT INTO stock_transactions (product_id, type, quantity, user_id)
             VALUES (?,?,?,?)"
        );
        $stmt->bind_param('isii', $product_id, $type, $qty, $user_id);
        $stmt->execute();

        $conn->commit();
        header("Location: history.php?moved=1");
        exit();
    } catch (Exception $e) {
        $conn->rollback();
        $error = $e->getMessage();
    }
}

// dropdown for form
$products = $conn->query("SELECT id, name, quantity FROM products ORDER BY name");
?>
<!doctype html>
<html>
<head>
    <title>Stock Move</title>
    <style>body{font-family:Arial;margin:40px;}label{display:block;margin:10px 0;}</style>
</head>
<body>
<h2>Stock In / Out</h2>
<?php if (!empty($error)) echo "<p style='color:red'>$error</p>"; ?>
<form method="post">
    <label>Product<br>
        <select name="product_id" required>
            <option value="">-- choose --</option>
            <?php while ($p = $products->fetch_assoc()): ?>
                <option value="<?= $p['id'] ?>">
                    <?= $p['name'] ?> (current: <?= $p['quantity'] ?>)
                </option>
            <?php endwhile; ?>
        </select>
    </label>

    <label>Type<br>
        <select name="type" required>
            <option value="in">Stock IN</option>
            <option value="out">Stock OUT</option>
        </select>
    </label>

    <label>Quantity<br><input type="number" name="quantity" min="1" required></label>

    <button type="submit">Save</button>
</form>
<a href="history.php">↵ History</a>
</body>
</html>