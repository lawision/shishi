<?php
include 'connect.php';

$province = $_GET['province'] ?? '';
if(!$province){
    echo json_encode([]);
    exit;
}

$stmt = $conn->prepare("SELECT id, name, distance_from_cebu_km FROM cities WHERE province=? ORDER BY name ASC");
$stmt->bind_param("s", $province);
$stmt->execute();
$result = $stmt->get_result();

$cities = [];
while($row = $result->fetch_assoc()){
    $cities[] = $row;
}
echo json_encode($cities);
