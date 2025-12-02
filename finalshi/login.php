<?php
include 'connect.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['username'];
    $password = $_POST['password'];

    // FIX: Correct table + correct field name
    $stmt = $conn->prepare("SELECT * FROM user WHERE email_address = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {

            $_SESSION['user'] = $user;

            // FIX: Correct first_name field
            echo "<script>alert('Welcome, {$user['first_name']}!'); window.location='index.php';</script>";
        } else {
            echo "<script>alert('Invalid password.'); window.history.back();</script>";
        }
    } else {
        echo "<script>alert('Email not found.'); window.history.back();</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - HHH</title>
    <link rel="stylesheet" href="CSS/login.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="login-container">
        <h2>Login</h2>
        <form action="login.php" method="POST">
            <div class="input-group">
                <label>Email Address</label>
                <input type="text" name="username" placeholder="Enter your email" required>
            </div>

            <div class="input-group">
                <label>Password</label>
                <input type="password" name="password" placeholder="Enter your password" required>
            </div>

            <button type="submit">Login</button>

            <div class="signup-link">
                Don't have an account? <a href="signup.php">Sign up</a>
            </div>
        </form>

        <div class="back-button">
            <a href="guestindex.php"><i class="fas fa-arrow-left"></i> Back</a>
        </div>
    </div>
</body>
</html>
