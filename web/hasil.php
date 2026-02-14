<?php
$deskripsi = $_POST['deskripsi'];

$data = array("deskripsi" => $deskripsi);
$options = array(
    "http" => array(
        "header"  => "Content-Type: application/json\r\n",
        "method"  => "POST",
        "content" => json_encode($data),
    ),
);

$context  = stream_context_create($options);
$result = file_get_contents("http://localhost:5000/predict", false, $context);

if ($result === FALSE) {
    die("Gagal terhubung ke API.");
}

$response = json_decode($result, true);
echo "Kategori Laptop: " . $response["kategori"];


// Load JSON
$laptops = json_decode(file_get_contents('laptops.json'), true);
// $json_data = file_get_contents('laptops.json');
// $laptops = json_decode($json_data, true);

// Filter sesuai kategori
$hasil = $response["kategori"];
$filtered = array_filter($laptops, function($laptop) use ($hasil){
    return strtolower($laptop['kategori']) === strtolower($hasil);
});

// Menampilkan hasil
if ($filtered) {
    foreach($filtered as $lap) {
        // echo "Nama: " . $lap['nama_laptop'] . "<br>";
        // echo "Umur: " . $lap['harga'] . "<br>";
        echo "<img src='{$lap['gambar']}' alt='{$lap['nama_laptop']}' width='200'><br><br>";
        echo "<p>{$lap['nama_laptop']}" . "<br>{$lap['cpu']}" .
        "<br>{$lap['gpu']}" . "<br>{$lap['ram']}".
        "<br>{$lap['storage']}" . "<br>{$lap['layar']} <br>Rp ". 
        number_format($lap['harga'], 0, ',', '.') . "</p>";
    }
} else {
    echo "<p>Tidak ada laptop yang cocok untuk kategori <b>{$response["kategori"]}</b>.</p>";
}
?>
