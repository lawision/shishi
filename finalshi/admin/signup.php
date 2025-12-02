<?php
// admin/signup.php
session_start();
include '../connect.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first = $_POST['first_name'];
    $last = $_POST['last_name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm = $_POST['confirm_password'];

    if ($password !== $confirm) {
        $error = "Passwords do not match!";
    } else {
        // check if email exists
        $check = $conn->prepare("SELECT * FROM `user` WHERE email_address = ?");
        $check->bind_param("s", $email);
        $check->execute();
        $result = $check->get_result();

        if ($result->num_rows > 0) {
            $error = "Email is already in use.";
        } else {
            // hash password
            $hashed = password_hash($password, PASSWORD_BCRYPT);

            // admin accounts are marked as is_admin=1
            $stmt = $conn->prepare("
                INSERT INTO `user` (first_name, last_name, email_address, password, is_admin)
                VALUES (?, ?, ?, ?, 1)
            ");
            $stmt->bind_param("ssss", $first, $last, $email, $hashed);

            if ($stmt->execute()) {
                $success = "Admin account created successfully!";
            } else {
                $error = "Something went wrong.";
            }
        }
    }
}
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Admin Sign Up</title>
  <link rel="stylesheet" href="../CSS/admin_style.css">
</head>
<body class="admin-page">
  <div class="admin-login">
    <h2>Admin Sign Up</h2>

    <?php if ($error): ?>
      <div class="err"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <?php if ($success): ?>
      <div class="success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <form method="post">
      <label>First Name</label>
      <input type="text" name="first_name" required>

      <label>Last Name</label>
      <input type="text" name="last_name" required>

      <label>Email</label>
      <input type="email" name="email" required>

      <label>Password</label>
      <input type="password" name="password" required>

      <label>Confirm Password</label>
      <input type="password" name="confirm_password" required>

      <button type="submit">Create Admin</button>
    </form>

    <p><a href="login.php">Back to Login</a></p>
  </div>
</body>
</html>
