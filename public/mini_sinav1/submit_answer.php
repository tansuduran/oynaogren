<?php
session_start();
include('db.php');

// Çıktı tamponlamayı başlat
ob_start();

// Kullanıcı geçiş yapıyorsa, `navigating` kontrolü eklenir
if (isset($_POST['navigating']) && $_POST['navigating'] == 1) {
    $_SESSION['navigating'] = true; // Soru geçişini işaretler
} else {
    $_SESSION['navigating'] = false;
}

// Sayfa yenilendiğinde yanlış cevap olarak işlenmesi için kontrol
if (!isset($_SESSION['answered']) && !$_SESSION['navigating']) {
    // Eğer kullanıcı önceki soruya yanıt vermeden sayfayı yenilediyse yanlış cevap sayısını arttır
    if (isset($_SESSION['last_question'])) {
        $_SESSION['wrong_count']++;
    }
    $_SESSION['answered'] = true; // Cevaplanmış olarak işaretle
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $correct_answer = $_POST['correct_answer'];
    $user_answer = $_POST['answer'];

    // Kullanıcı cevap verdiyse oturumda işaretle
    $_SESSION['answered'] = true;
    $_SESSION['last_question'] = true; // Kullanıcının en son hangi soruda olduğunu işaretler

    if ($user_answer === $correct_answer) {
        $_SESSION['score']++;
        $message = "Tebrikler! Doğru cevap verdiniz!";
        $next_action = "Bir sonraki soruya geçiliyor...";
        $color = "#4CAF50"; // Yeşil renk, doğru cevap için
        $highlight_correct = ""; // Doğru cevap için vurgulama yok
        $redirect_time = 1; // Tebrikler mesajı 1 saniye kalacak
    } else {
        $_SESSION['wrong_count']++;
        $message = "Yanlış cevap verdiniz!";
        $highlight_correct = "<span class='highlight'>Doğru cevap: $correct_answer</span>";
        $next_action = ($_SESSION['wrong_count'] < 3) ? "Bir sonraki soruya geçiliyor..." : "Mini sınav bitti.";
        $color = "#f44336"; // Kırmızı renk, yanlış cevap için
        $redirect_time = 3; // Yanlış cevap mesajı 3 saniye kalacak
    }

    // Quiz sona erdiğinde, skoru göster
    if ($_SESSION['wrong_count'] >= 3) {
        $next_action = "Mini sınav bitti. Doğru sayınız: " . $_SESSION['score'];
        session_destroy(); // Oturumu sonlandırarak sayacı sıfırlıyoruz
    } else {
        // Eğer sınav bitmediyse belirtilen süre kadar mesajı gösterip sonra başka bir soruya geç
        header("refresh:$redirect_time;url=index.php");
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
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background-color: #eef2f7;
            text-align: center;
        }
        .message-box {
            background: #ffffff;
            padding: 25px 30px;
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            max-width: 450px;
            width: 90%;
            transition: all 0.3s ease;
        }
        .message {
            font-size: 20px;
            margin-bottom: 15px;
            color: <?php echo $color; ?>;
            font-weight: bold;
        }
        .highlight {
            display: inline-block;
            background-color: #ffeeba;
            color: #856404;
            font-weight: bold;
            padding: 8px 12px;
            border-radius: 8px;
            margin-top: 10px;
        }
        .next-action {
            margin-top: 12px;
            font-size: 16px;
            color: #555;
        }
        .buttons {
            display: flex;
            flex-direction: column;
            gap: 10px;
            margin-top: 20px;
        }
        .btn {
            padding: 12px 20px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            cursor: pointer;
            color: #fff;
            transition: background-color 0.3s ease, transform 0.2s;
            text-decoration: none;
            display: inline-block;
        }
        .btn-retry {
            background-color: #28a745;
        }
        .btn-retry:hover {
            background-color: #218838;
            transform: translateY(-3px);
        }
        .btn-home {
            background-color: #007bff;
        }
        .btn-home:hover {
            background-color: #0056b3;
            transform: translateY(-3px);
        }
    </style>
</head>
<body>
    <div class="message-box">
        <p class="message"><?php echo $message; ?></p>
        <?php if (isset($highlight_correct)) { echo $highlight_correct; } ?>
        <p class="next-action"><?php echo $next_action; ?></p>

        <!-- Butonlar görünür olduğunda -->
        <?php if ($_SESSION['wrong_count'] >= 3): ?>
        <div class="buttons">
            <a href="index.php" class="btn btn-retry">Yeniden Oyna</a>
            <a href="https://www.oynaogren.tr" class="btn btn-home">Ana Sayfaya Dön</a>
        </div>
        <?php endif; ?>
    </div>
</body>
</html>

<?php
// Çıktı tamponlamayı sonlandır
ob_end_flush();
?>
