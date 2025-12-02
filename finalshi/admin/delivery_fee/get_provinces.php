<?php
include 'connect.php';

$category = $_GET['category'] ?? '';
if(!$category) {
    echo json_encode([]);
    exit;
}

$stmt = $conn->prepare("SELECT DISTINCT province FROM cities WHERE category=? ORDER BY province ASC");
$stmt->bind_param("s", $category);
$stmt->execute();
$result = $stmt->get_result();

$provinces = [];
while($row = $result->fetch_assoc()){
    $provinces[] = $row;
}
echo json_encode($provinces);
