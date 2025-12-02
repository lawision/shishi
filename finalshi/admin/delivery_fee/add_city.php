<?php
include 'connect.php';

if (isset($_POST['add_city'])) {
    $category = $_POST['category'];
    $province = $_POST['province'];
    $city_name = $_POST['name'];
    $distance = $_POST['distance_from_cebu_km'];

    $stmt = $conn->prepare("INSERT INTO cities (category, province, name, distance_from_cebu_km) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("sssd", $category, $province, $city_name, $distance);
    $stmt->execute();

    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add City</title>
</head>
<body>
<h2>Add City</h2>

<form method="post">
    <!-- Category -->
    <label>Category:</label><br>
    <select name="category" required>
        <option value="">Select Category</option>
        <option value="Luzon">Luzon</option>
        <option value="Visayas">Visayas</option>
        <option value="Mindanao">Mindanao</option>
    </select><br><br>

    <!-- Province (text input instead of dropdown) -->
    <label>Province:</label><br>
    <input type="text" name="province" placeholder="Enter province" required><br><br>

    <!-- City Name -->
    <label>City Name:</label><br>
    <input type="text" name="name" placeholder="Enter city name" required><br><br>

    <!-- Distance -->
    <label>Distance from Cebu (km):</label><br>
    <input type="number" step="0.01" name="distance_from_cebu_km" placeholder="Distance in km" required><br><br>

    <button type="submit" name="add_city">Add City</button>
</form>

<br>
<a href="index.php">Back to Cities</a>
</body>
</html>
