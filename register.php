<?php
session_start();
include("db.php");

/* ---------- if you want only admins to create accounts ----------
   replace this block with role check:  if($_SESSION['role']!=='admin')...
--------------------------------------------------------------------*/
$allowPublicRegister = true;   // flip to false when you want to lock sign-ups

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $confirm  = trim($_POST['confirm']);

    /* -------- basic validation -------- */
    if ($password !== $confirm) {
        $error = "Passwords do not match!";
    } elseif (strlen($password) < 6) {
        $error = "Password must be ≥ 6 characters.";
    } else {
        /* -------- check unique username -------- */
        $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows) {
            $error = "Username already taken.";
        } else {
            /* -------- insert -------- */
            $hash = password_hash($password, PASSWORD_BCRYPT);
            $role = 'staff';               // default role
            $stmt = $conn->prepare("INSERT INTO users (username, password, role) VALUES (?,?,?)");
            $stmt->bind_param("sss", $username, $hash, $role);
            if ($stmt->execute()) {
                $_SESSION['username'] = $username;
                $_SESSION['role']     = $role;
                $_SESSION['user_id']  = $conn->insert_id;
                header("Location: dashboard.php");
                exit();
            } else {
                $error = "Could not create account.";
            }
        }
    }
}
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Register - Inventory System</title>
<style>
/* =================  SAME CSS  ================= */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: 'Poppins', sans-serif;
}
body {
    height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
    background: linear-gradient(135deg, #6a11cb, #2575fc);
}
.login-card {
    background: rgba(255, 255, 255, 0.95);
    padding: 40px 50px;
    border-radius: 15px;
    box-shadow: 0 20px 50px rgba(0,0,0,0.2);
    width: 350px;
    text-align: center;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}
.login-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 25px 60px rgba(0,0,0,0.25);
}
.login-card h2 {
    margin-bottom: 30px;
    color: #333;
    font-weight: 600;
    letter-spacing: 1px;
}
.login-card input {
    width: 100%;
    padding: 12px 15px;
    margin: 10px 0;
    border-radius: 8px;
    border: 1px solid #ddd;
    transition: 0.3s;
    font-size: 16px;
}
.login-card input:focus {
    outline: none;
    border-color: #2575fc;
    box-shadow: 0 0 8px rgba(37, 117, 252, 0.3);
}
.login-card button {
    width: 100%;
    padding: 12px;
    margin-top: 20px;
    border: none;
    border-radius: 8px;
    background: linear-gradient(45deg, #6a11cb, #2575fc);
    color: white;
    font-size: 16px;
    cursor: pointer;
    transition: 0.3s;
}
.login-card button:hover {
    background: linear-gradient(45deg, #2575fc, #6a11cb);
}
.error-msg {
    color: #ff4d4f;
    margin-bottom: 15px;
    font-weight: 500;
    animation: shake 0.3s ease;
}
@keyframes shake {
    0% { transform: translateX(0px); }
    25% { transform: translateX(-5px); }
    50% { transform: translateX(5px); }
    75% { transform: translateX(-5px); }
    100% { transform: translateX(0px); }
}
@media(max-width: 400px) {
    .login-card {
        width: 90%;
        padding: 30px 20px;
    }
}
/* ============================================== */
</style>
</head>
<body>
<div class="login-card">
    <h2>Create Account</h2>

    <?php if (!empty($error)) echo "<div class='error-msg'>$error</div>"; ?>

    <form method="post">
        <input type="text" name="username" placeholder="Username" required autocomplete="off">
        <input type="password" name="password" placeholder="Password" required autocomplete="new-password">
        <input type="password" name="confirm"  placeholder="Confirm Password" required autocomplete="new-password">
        <button type="submit">Register</button>
    </form>

    <p style="margin-top:20px;font-size:14px;color:#555">
        Already have an account? <a href="index.php">Login</a>
    </p>
</div>
</body>
</html>