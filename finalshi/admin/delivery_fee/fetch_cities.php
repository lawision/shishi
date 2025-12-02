<?php
include 'connect.php';

// Get price per km
$price_per_km = $conn->query("SELECT value FROM settings WHERE key_name='price_per_km'")->fetch_assoc()['value'] ?? 0;

// Get filter/search inputs
$selectedCategory = $_GET['category'] ?? "";
$searchTerm = $_GET['search'] ?? "";

// Build SQL query
$sql = "SELECT * FROM cities WHERE 1=1";
$params = [];
$types = "";

// Filter by category
if ($selectedCategory) {
    $sql .= " AND category = ?";
    $params[] = $selectedCategory;
    $types .= "s";
}

// Search by city or province letters
if ($searchTerm) {
    $sql .= " AND (name LIKE ? OR province LIKE ?)";
    $params[] = "%$searchTerm%";
    $params[] = "%$searchTerm%";
    $types .= "ss";
}

$sql .= " ORDER BY category, province, name";

$stmt = $conn->prepare($sql);
if ($params) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$cities = $stmt->get_result();

// Output table rows
if ($cities && $cities->num_rows > 0) {
    while($row = $cities->fetch_assoc()) {
        $distance = $row['distance_from_cebu_km'];
        $shipping = ($distance <= 10) ? 50 : 50 + ($distance - 10) * $price_per_km;
        echo "<tr>
            <td>{$row['id']}</td>
            <td>{$row['category']}</td>
            <td>{$row['name']}</td>
            <td>{$row['province']}</td>
            <td>{$distance}</td>
            <td>".number_format($shipping,2)."</td>
            <td>
                <a href='edit_city.php?id={$row['id']}'>Edit</a> |
                <a href='delete_city.php?id={$row['id']}' onclick=\"return confirm('Are you sure?')\">Delete</a>
            </td>
        </tr>";
    }
} else {
    echo "<tr><td colspan='7'>No cities found.</td></tr>";
}
