<?php
// Oturum başlatma
session_start();

// Oturumdan verileri al
$name = isset($_SESSION['name']) ? $_SESSION['name'] : 'Misafir';
$ranking = isset($_SESSION['ranking']) ? $_SESSION['ranking'] : 'Bilinmiyor';
$high_score = isset($_SESSION['high_score']) ? $_SESSION['high_score'] : 0;
$total_score = isset($_SESSION['total_score']) ? $_SESSION['total_score'] : 0;

// Son ziyaret bilgisi (gün, saat, dakika)
$last_visit = strtotime($_SESSION['last_visit'] ?? 'now');
$current_time = time();
$time_diff = $current_time - $last_visit;
$days = floor($time_diff / (60 * 60 * 24));
$hours = floor(($time_diff % (60 * 60 * 24)) / (60 * 60));
$minutes = floor(($time_diff % (60 * 60)) / 60);

// HTML içerik
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hoşgeldiniz</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            padding: 20px;
            background-color: #f4f4f4;
        }
        h1 {
            color: #2c3e50;
        }
        p {
            font-size: 18px;
            color: #34495e;
        }
        a {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #3498db;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }
        a:hover {
            background-color: #2980b9;
        }
    </style>
</head>
<body>
    <h1>Hoşgeldin, <?php echo htmlspecialchars($name); ?>!</h1>
    <p>Son gelişinden bu yana <?php echo $days; ?> gün, <?php echo $hours; ?> saat, <?php echo $minutes; ?> dakika geçti.</p>
    <p>Yarışmadaki sıralaman: <?php echo htmlspecialchars($ranking); ?></p>
    <p>En yüksek puanın: <?php echo htmlspecialchars($high_score); ?></p>
    <p>Toplam puanın: <?php echo htmlspecialchars($total_score); ?></p>
    <a href="/index.html">Ana Sayfa</a>
</body>
</html>
