<?php
// PHP hata ayıklama modunu açalım:
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Oturum başlatma
session_start();

// Oturumdan kullanıcı bilgilerini alalım
$name = $_SESSION['name'] ?? null;
$email = $_SESSION['email'] ?? null;

if ($name === null || $email === null) {
    die('Kullanıcı bilgileri alınamadı.');
}

$ranking = 5;  // Varsayılan yarışma sıralaması
$highest_score = 100;  // Varsayılan en yüksek puan
$total_score = 100;    // Varsayılan toplam puan

// Veritabanı bağlantısını dahil et
require_once __DIR__ . '/../users/db.php';

// Kullanıcıyı veritabanına ekle veya güncelle
$sql = "INSERT INTO users (name, email, last_visit, ranking, highest_score, total_score) 
        VALUES (?, ?, NOW(), ?, ?, ?)
        ON DUPLICATE KEY UPDATE last_visit = NOW(), ranking = ?, highest_score = GREATEST(highest_score, ?), total_score = total_score + ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ssiiiiii", $name, $email, $ranking, $highest_score, $total_score, $ranking, $highest_score, $total_score);
$stmt->execute();

// Kullanıcının son ziyaret zamanını ve sıralamasını çek
$sql = "SELECT last_visit, ranking, highest_score, total_score FROM users WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

$last_visit = strtotime($user['last_visit']);
$ranking = $user['ranking'];
$highest_score = $user['highest_score'];
$total_score = $user['total_score'];

// Zaman farkını hesaplayalım
$current_time = time();
if ($last_visit <= $current_time) {
    $time_diff = $current_time - $last_visit;
} else {
    $time_diff = 0;
}
$days = floor($time_diff / (60 * 60 * 24));
$hours = floor(($time_diff % (60 * 60 * 24)) / (60 * 60));
$minutes = floor(($time_diff % (60 * 60)) / 60);

$conn->close();
?>

<html>
<head>
    <title>Hoşgeldin</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .container {
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        h1 {
            color: #333;
        }
        p {
            font-size: 18px;
            color: #555;
        }
        a {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #28a745;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
        }
        a:hover {
            background-color: #218838;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Hoşgeldin, <?php echo htmlspecialchars($name); ?>!</h1>
        <p>Son gelişinden bu yana <?php echo $days; ?> gün, <?php echo $hours; ?> saat, <?php echo $minutes; ?> dakika geçti.</p>
        <p>Yarışmadaki sıralaman: <?php echo htmlspecialchars($ranking); ?></p>
        <p>En yüksek puanın: <?php echo htmlspecialchars($highest_score); ?></p>
        <p>Toplam puanın: <?php echo htmlspecialchars($total_score); ?></p>
        <a href="/index.html">Ana Sayfa</a>
    </div>
</body>
</html>
