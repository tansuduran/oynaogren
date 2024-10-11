<?php
session_start();
include('db.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $correct_answer = $_POST['correct_answer'];
    $user_answer = $_POST['answer'];

    if ($user_answer === $correct_answer) {
        $_SESSION['score']++;
        $message = "Tebrikler! Doğru cevap verdiniz!";
        $next_action = "Bir sonraki soruya geçiliyor...";
        $color = "#4CAF50"; // Yeşil renk, doğru cevap için
        $highlight_correct = ""; // Doğru cevap için vurgulama yok
    } else {
        $_SESSION['wrong_count']++;
        $message = "Yanlış cevap verdiniz!";
        $highlight_correct = "<span class='highlight'>Doğru cevap: $correct_answer</span>";
        $next_action = ($_SESSION['wrong_count'] < 3) ? "Bir sonraki soruya geçiliyor..." : "Mini sınav bitti.";
        $color = "#f44336"; // Kırmızı renk, yanlış cevap için
    }

    // Quiz sona erdiğinde, skoru göster
    if ($_SESSION['wrong_count'] >= 3) {
        $next_action = "Mini sınav bitti. Doğru sayınız: " . $_SESSION['score'];
        session_destroy(); // Oturumu sonlandırarak sayacı sıfırlıyoruz
    } else {
        // Eğer sınav bitmediyse otomatik başka bir soruya geç
        header("refresh:3;url=index.php");
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quiz Sonucu</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background-color: #f4f4f9;
            text-align: center;
        }
        .message-box {
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            max-width: 400px;
        }
        .message {
            font-size: 18px;
            margin-bottom: 10px;
            color: <?php echo $color; ?>;
        }
        .highlight {
            display: inline-block;
            background-color: #ffeb3b;
            color: #333;
            font-weight: bold;
            padding: 5px 10px;
            border-radius: 5px;
            margin-top: 10px;
        }
        .next-action {
            font-size: 16px;
            color: #333;
            margin-top: 10px;
        }
        .buttons {
            margin-top: 20px;
        }
        .btn {
            padding: 10px 20px;
            margin: 5px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            color: #fff;
            transition: background-color 0.3s ease;
        }
        .btn-retry {
            background-color: #4CAF50;
        }
        .btn-retry:hover {
            background-color: #45a049;
        }
        .btn-home {
            background-color: #2196F3;
        }
        .btn-home:hover {
            background-color: #0b7dda;
        }
    </style>
</head>
<body>
    <div class="message-box">
        <p class="message"><?php echo $message; ?></p>
        <?php if (isset($highlight_correct)) { echo $highlight_correct; } ?>
        <p class="next-action"><?php echo $next_action; ?></p>
        
        <?php if ($_SESSION['wrong_count'] >= 3): ?>
        <!-- Mini sınav bittiğinde gösterilecek seçenekler -->
        <div class="buttons">
            <button class="btn btn-retry" onclick="window.location.href='index.php'">Yeniden Oyna</button>
            <button class="btn btn-home" onclick="window.location.href='https://www.oynaogren.tr'">Ana Sayfaya Dön</button>
        </div>
        <?php endif; ?>
    </div>
</body>
</html>
