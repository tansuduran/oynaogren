<?php
$servername = "localhost";
$username = "tansu";
$password = "Td!0707!!";
$dbname = "oyna01";

// Veritabanına bağlan
$conn = new mysqli($servername, $username, $password, $dbname);

// Bağlantı kontrolü
if ($conn->connect_error) {
    die("Veritabanına bağlanılamadı: " . $conn->connect_error);
}

// Karakter setini ayarla
$conn->set_charset("utf8");
?>
