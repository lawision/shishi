<?php
session_start();
include '../connect.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    // STATIC ADMIN ACCOUNT
    $static_admin_email = "admin@hhh.com";      // Put any email you want
    $static_admin_password = "admin123";        // Strong password recommended

    // Check if the login attempt matches the static admin credentials
    if ($email === $static_admin_email && $password === $static_admin_password) {
        
        // Create admin session
        $_SESSION['user'] = [
            'email' => $static_admin_email,
            'role' => 'admin',
            'name' => 'Administrator'
        ];

        $_SESSION['user']['is_admin'] = true;

        header("Location: dashboard.php");
        exit();
    }

    // If not static admin, check normal user database
    $stmt = $conn->prepare("SELECT * FROM `user` WHERE email_address = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res && $res->num_rows === 1) {
        $u = $res->fetch_assoc();

        if (password_verify($password, $u['password'])) {

            if (!$u['is_admin']) {
                $error = "This account does not have admin access.";
            } else {
                $_SESSION['user'] = $u;
                $_SESSION['user']['is_admin'] = true;
                header("Location: index.php");
                exit();
            }
        } else {
            $error = "Invalid credentials.";
        }
    } else {
        $error = "Invalid credentials.";
    }
}
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Admin Login</title>
  <link rel="stylesheet" href="login.css">
</head>
<body class="admin-page">
  <div class="admin-login">
    <h2>Admin Login</h2>
    <?php if ($error): ?><div class="err"><?=htmlspecialchars($error)?></div><?php endif; ?>
    <form method="post">
      <label>Email</label>
      <input type="email" name="email" required>

      <label>Password</label>
      <input type="password" name="password" required>

      <button type="submit">Log In</button>
    </form>
    <p style="font-size:0.9rem;color:#999;margin-top:10px;">Use admin account credentials.</p>
  </div>
</body>
</html>
