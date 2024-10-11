<?php 
session_start(); // Oturum başlatma

// Eğer oturumda doğru sayısı varsa göster
if (isset($_SESSION['dogru'])) {
    $dogruSayisi = $_SESSION['dogru'];
} else {
    $dogruSayisi = 0;
}

// Oturumu temizle
session_destroy();
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Oyun Bitti!</title>
</head>
<body>
    <div class="game-over-container">
        <h1>Oyun Bitti!</h1>
        <p>Doğru sayınız: <strong><?= $dogruSayisi ?></strong></p>
        <a href="index.php" class="play-again-button">Tekrar Oyna</a>
    </div>

    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f8ff;
            text-align: center;
            padding: 20px;
        }

        .game-over-container {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #f8f9fa;
            text-align: center;
        }

        .game-over-container h1 {
            font-size: 48px;
            color: #dc3545;
        }

        .game-over-container p {
            font-size: 24px;
            color: #28a745;
            margin-bottom: 20px;
        }

        .play-again-button {
            padding: 12px 25px;
            font-size: 20px;
            color: #fff;
            background-color: #007bff;
            text-decoration: none;
            border-radius: 5px;
        }

        .play-again-button:hover {
            background-color: #0056b3;
        }
    </style>
</body>
</html>
