<?php
include 'connect.php';

// Get price per km
$price_per_km = $conn->query("SELECT value FROM settings WHERE key_name='price_per_km'")->fetch_assoc()['value'];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Cities and Shipping</title>
    <script>
    async function fetchCities() {
        let category = document.getElementById('category').value;
        let search = document.getElementById('search').value;

        let res = await fetch(`fetch_cities.php?category=${category}&search=${search}`);
        let data = await res.text();

        document.getElementById('citiesTable').innerHTML = data;
    }

    window.addEventListener('DOMContentLoaded', () => {
        document.getElementById('category').addEventListener('change', fetchCities);
        document.getElementById('search').addEventListener('keyup', fetchCities);
        fetchCities(); // initial load
    });
    </script>
</head>
<body>
<h2>Cities and Shipping</h2>

<!-- Filters -->
<label>Category:</label>
<select id="category">
    <option value="">All</option>
    <option value="Luzon">Luzon</option>
    <option value="Visayas">Visayas</option>
    <option value="Mindanao">Mindanao</option>
</select>

<label style="margin-left:10px;">Search:</label>
<input type="text" id="search" placeholder="Type city or province...">

<br><br>
<a href="add_city.php">Add City</a> | <a href="settings.php">Settings</a>

<!-- Cities Table -->
<table border="1" cellpadding="5">
<tr>
    <th>ID</th>
    <th>Category</th>
    <th>City</th>
    <th>Province</th>
    <th>Distance (km)</th>
    <th>Shipping Cost (PHP)</th>
    <th>Actions</th>
</tr>
<tbody id="citiesTable">
    <!-- AJAX content will load here -->
</tbody>
</table>
</body>
</html>
