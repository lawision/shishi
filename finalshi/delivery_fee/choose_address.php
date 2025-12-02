<?php
include 'connect.php';

// Get price per km
$price_per_km = $conn->query("SELECT value FROM settings WHERE key_name='price_per_km'")->fetch_assoc()['value'];

// Fetch all categories for dropdown
$categories = ["Luzon", "Visayas", "Mindanao"];
?>
<!DOCTYPE html>
<html>
<head>
    <title>Choose Address</title>
    <script>
    async function loadProvinces(){
        let category = document.getElementById('category').value;
        if(!category) {
            document.getElementById('province').innerHTML = '<option value="">Select Province</option>';
            document.getElementById('city').innerHTML = '<option value="">Select City</option>';
            return;
        }

        let res = await fetch('get_provinces.php?category=' + category);
        let data = await res.json();

        let provinceSelect = document.getElementById('province');
        provinceSelect.innerHTML = '<option value="">Select Province</option>';
        data.forEach(p => {
            provinceSelect.innerHTML += `<option value="${p.province}">${p.province}</option>`;
        });

        document.getElementById('city').innerHTML = '<option value="">Select City</option>';
        document.getElementById('shipping').innerText = '';
    }

    async function loadCities(){
        let province = document.getElementById('province').value;
        if(!province) return;

        let res = await fetch('get_cities.php?province=' + province);
        let data = await res.json();

        let citySelect = document.getElementById('city');
        citySelect.innerHTML = '<option value="">Select City</option>';
        data.forEach(c => {
            citySelect.innerHTML += `<option value="${c.id}" data-distance="${c.distance_from_cebu_km}">${c.name}</option>`;
        });
        document.getElementById('shipping').innerText = '';
    }

    function calculateShippingCost(){
        let selectedCity = document.getElementById('city').selectedOptions[0];
        if(!selectedCity || !selectedCity.dataset.distance) return;
        let distance = parseFloat(selectedCity.dataset.distance);
        let pricePerKm = <?= $price_per_km ?>;
        let base = 50;
        let shippingCost = distance <= 10 ? base : base + (distance - 10) * pricePerKm;
        document.getElementById('shipping').innerText = `Distance from Cebu: ${distance} km | Shipping Fee: PHP ${shippingCost.toFixed(2)}`;
    }
    </script>
</head>
<body>
<h2>Select Your Address</h2>
<form>
    <!-- Category -->
    <label>Category:</label>
    <select id="category" name="category" onchange="loadProvinces()">
        <option value="">Select Category</option>
        <?php foreach($categories as $c): ?>
            <option value="<?= $c ?>"><?= $c ?></option>
        <?php endforeach; ?>
    </select>
    <br><br>

    <!-- Province -->
    <label>Province:</label>
    <select id="province" name="province" onchange="loadCities()">
        <option value="">Select Province</option>
    </select>
    <br><br>

    <!-- City -->
    <label>City/Municipality:</label>
    <select id="city" name="city" onchange="calculateShippingCost()">
        <option value="">Select City</option>
    </select>
    <br><br>

    <div id="shipping" style="font-weight:bold;"></div>
</form>
</body>
</html>
