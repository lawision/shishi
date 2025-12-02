<?php
include 'connect.php';

$stmt = $conn->prepare("SELECT DISTINCT province FROM cities ORDER BY province ASC");
$stmt->execute();
$result = $stmt->get_result();

$provinces = [];
while ($row = $result->fetch_assoc()) {
    $provinces[] = ['province' => $row['province']];
}

header('Content-Type: application/json');
echo json_encode($provinces);