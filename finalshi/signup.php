<?php
include 'connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $address = $_POST['address'];
    $contact_number = $_POST['contact_number'];
    $email_address = $_POST['email_address'];
    $password = $_POST['password'];

    if (empty($first_name) || empty($last_name) || empty($address) || 
        empty($contact_number) || empty($email_address) || empty($password)) {
        echo "<script>alert('Please fill in all fields.'); window.history.back();</script>";
        exit;
    }

    // FIX: check email in correct table
    $check = $conn->prepare("SELECT * FROM user WHERE email_address = ?");
    $check->bind_param("s", $email_address);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows > 0) {
        echo "<script>alert('Email already registered!'); window.history.back();</script>";
        exit;
    }

    // HASH PASSWORD
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // FIX: Insert into correct table + correct field names
    $stmt = $conn->prepare("INSERT INTO user 
        (first_name, last_name, address, contact_number, email_address, password)
        VALUES (?, ?, ?, ?, ?, ?)");

    $stmt->bind_param("ssssss", $first_name, $last_name, $address, $contact_number, $email_address, $hashedPassword);

    if ($stmt->execute()) {
        echo "<script>alert('Account created successfully!'); window.location='login.php';</script>";
    } else {
        echo "<script>alert('Error creating account. Please try again.'); window.history.back();</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - HHH</title>
    <link rel="stylesheet" href="CSS/signup.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="signup-container">
        <h2>Create HHH Account</h2>
        <form action="signup.php" method="POST">
            
            <div class="form-group">
                <label>First Name</label>
                <input type="text" name="first_name" required placeholder="Enter your first name">
            </div>

            <div class="form-group">
                <label>Last Name</label>
                <input type="text" name="last_name" required placeholder="Enter your last name">
            </div>

            <div class="form-group">
                <label>Address</label>
                <input type="text" name="address" required placeholder="Province/Municipality/Barangay/Street/House No.">
            </div>

            <div class="form-group">
                <label>Contact Number</label>
                <input type="tel" name="contact_number" required placeholder="Enter your contact number">
            </div>

            <div class="form-group">
                <label>Email Address</label>
                <input type="email" name="email_address" required placeholder="Enter your email address">
            </div>

            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" required placeholder="Create your password">
            </div>

            <button type="submit" class="signup-btn">Sign Up</button>
        </form>

        <p class="login-link">Already have an account? <a href="login.php">Login here</a></p>

        <div class="back-button">
            <a href="guestindex.php"><i class="fas fa-arrow-left"></i> Back</a>
        </div>
    </div>
</body>
</html>
