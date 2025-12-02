<?php
include 'connect.php';

$id = $_GET['id'] ?? null;
if (!$id) die("City not found.");

// Fetch city data
$stmt = $conn->prepare("SELECT * FROM cities WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$city = $stmt->get_result()->fetch_assoc();
if (!$city) die("Invalid ID.");

// Handle update
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $category = $_POST['category'];
    $province = $_POST['province'];
    $city_name = $_POST['name'];
    $distance = $_POST['distance_from_cebu_km'];

    $update = $conn->prepare("UPDATE cities SET category=?, province=?, name=?, distance_from_cebu_km=? WHERE id=?");
    $update->bind_param("sssdi", $category, $province, $city_name, $distance, $id);
    $update->execute();

    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit City</title>
</head>
<body>
<h2>Edit City</h2>

<form method="post">
    <!-- Category -->
    <label>Category:</label><br>
    <select name="category" required>
        <option value="Luzon" <?= $city['category']=="Luzon"?"selected":"" ?>>Luzon</option>
        <option value="Visayas" <?= $city['category']=="Visayas"?"selected":"" ?>>Visayas</option>
        <option value="Mindanao" <?= $city['category']=="Mindanao"?"selected":"" ?>>Mindanao</option>
    </select><br><br>

    <!-- Province (text input now) -->
    <label>Province:</label><br>
    <input type="text" name="province" value="<?= htmlspecialchars($city['province']) ?>" required><br><br>

    <!-- City Name -->
    <label>City Name:</label><br>
    <input type="text" name="name" value="<?= htmlspecialchars($city['name']) ?>" required><br><br>

    <!-- Distance -->
    <label>Distance from Cebu (km):</label><br>
    <input type="number" step="0.01" name="distance_from_cebu_km" value="<?= $city['distance_from_cebu_km'] ?>" required><br><br>

    <button type="submit">Save Changes</button>
</form>

<br>
<a href="index.php">Back</a>
</body>
</html>
