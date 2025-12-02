<?php
session_start();
include '../connect.php';

// Allow only admin
if (!isset($_SESSION['user']) || $_SESSION['user']['is_admin'] != 1) {
    die("Access denied. Admins only.");
}

// Fetch promo info
$sql = "SELECT * FROM sale_promo WHERE id = 1";
$result = $conn->query($sql);
$promo = $result->fetch_assoc();

// Update Sale Promo
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];

    $update = $conn->prepare("UPDATE sale_promo SET title=?, description=? WHERE id=1");
    $update->bind_param("ss", $title, $description);
    $update->execute();

    echo "<script>alert('Sale promo updated successfully!'); window.location='editpromo.php';</script>";
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit Sale Promo</title>
</head>
<body>

<h2>Edit Sale Promo</h2>

<form method="POST">
    <label>Promo Title:</label><br>
    <input type="text" name="title" value="<?php echo $promo['title']; ?>" style="width:400px"><br><br>

    <label>Description:</label><br>
    <textarea name="description" rows="4" cols="50"><?php echo $promo['description']; ?></textarea><br><br>

    <button type="submit">Save Changes</button>
</form>

</body>
</html>
