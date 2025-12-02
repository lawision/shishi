<?php
include 'connect.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    $conn->query("DELETE FROM cities WHERE id = $id");
}

header("Location: index.php");
exit();
?>
