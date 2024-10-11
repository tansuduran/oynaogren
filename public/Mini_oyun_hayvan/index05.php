<?php 
session_start(); // Oturum başlatma

// Eğer 3 yanlış varsa oyun biter ve sonuç ekranını gösteririz
if ($_SESSION['yanlis'] >= 3) {
    $message = "Oyun Bitti! Doğru sayınız: {$_SESSION['dogru']}";
    $showGameOverButtons = true;
    session_destroy();
} else {
    $showGameOverButtons = false;
}

// Eğer bir cevap mesajı varsa göster ve belirli süre sonra yeni soruya geçiş yap
$duration = 4000; // Süreyi sabit olarak 4 saniye belirliyoruz
$message = $message ?? ''; // Eğer yoksa boş kalacak

if (isset($_SESSION['show_feedback'])) {
    $message = $_SESSION['message'];
    // Yeni soruya geçiş yaparken oturumu sıfırlıyoruz
    unset($_SESSION['show_feedback']);
    unset($_SESSION['message']);
} else {
    // Yeni soru oluşturma işlemi
    include 'data.php'; // Hayvan verilerini dahil ediyoruz
    
    // Rastgele hayvan seçiyoruz ve sadece bir kez oturuma soruyu ekliyoruz
    if (!isset($_SESSION['question'])) {
        $currentQuestionIndex = array_rand($animals);
        $_SESSION['question'] = $animals[$currentQuestionIndex]; // Seçilen hayvanı oturuma ekliyoruz
        $_SESSION['correctAnswer'] = $_SESSION['question']['name']; // Doğru cevabı oturuma ekliyoruz
        
        // Rastgele seçenekler hazırlama
        $allNames = array_column($animals, 'name');
        shuffle($allNames);
        $_SESSION['options'] = array_slice($allNames, 0, 3); // 3 rastgele seçenek alıyoruz
        
        // Doğru cevabı seçenekler arasına ekliyoruz
        if (!in_array($_SESSION['correctAnswer'], $_SESSION['options'])) {
            $_SESSION['options'][array_rand($_SESSION['options'])] = $_SESSION['correctAnswer'];
        }
        shuffle($_SESSION['options']); // Seçenekleri karıştırıyoruz
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Mini Hayvan Oyunu</title>
</head>
<body>
    <div class="game-container">
        <h1>Mini Hayvan Oyunu</h1>
        <p>Doğru: <?= $_SESSION['dogru'] ?? 0 ?>, Yanlış: <?= $_SESSION['yanlis'] ?? 0 ?></p>
        
        <form method="POST" action="submit_answer.php">
            <img src="<?= $_SESSION['question']['image'] ?>" alt="Hayvan Görseli" class="animal-image">
            <?php foreach ($_SESSION['options'] as $option): ?>
                <button type="submit" name="answer" value="<?= $option ?>"><?= $option ?></button>
            <?php endforeach; ?>
        </form>

        <!-- Cevap verildikten sonra geri bildirim mesajları -->
        <?php if ($message): ?>
            <div id="message-box" style="margin-top: 20px; background-color: <?= strpos($message, 'Tebrikler') !== false ? '#28a745' : '#dc3545' ?>; color: white; padding: 10px; border-radius: 5px;"><?= $message ?></div>

            <script>
                setTimeout(function() {
                    <?php if (!$showGameOverButtons): ?>
                        // Yeni soruya geçiş yapılıyor
                        <?php unset($_SESSION['question']); ?>
                        window.location.href = 'index.php';
                    <?php endif; ?>
                }, <?= $duration ?>);
            </script>
        <?php endif; ?>

        <!-- Oyun bittiğinde gösterilecek butonlar ve sonuç mesajı -->
        <?php if ($showGameOverButtons): ?>
            <div style="margin-top: 20px; padding: 10px; background-color: #28a745; color: white; border-radius: 5px;">
                <h2>Oyun Bitti! Doğru sayınız: <?= $_SESSION['dogru'] ?? 0 ?></h2>
            </div>
            <div style="margin-top: 20px;">
                <a href="index.php" class="button">Tekrar Oyna</a>
                <a href="homepage.php" class="button">Ana Sayfa</a>
            </div>
        <?php endif; ?>
    </div>

    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f8ff;
            text-align: center;
            padding: 20px;
        }

        .game-container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            border: 2px solid #ddd;
            border-radius: 10px;
            background: #fff;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .animal-image {
            width: 100%;
            max-width: 400px;
            height: auto;
            border: 1px solid #ccc;
            border-radius: 5px;
            margin-bottom: 20px;
        }

        button, .button {
            padding: 10px 20px;
            font-size: 16px;
            cursor: pointer;
            border: none;
            border-radius: 5px;
            transition: background-color 0.3s ease;
            background-color: #007bff;
            color: white;
            margin: 5px;
            display: inline-block;
            text-decoration: none;
        }

        button:hover, .button:hover {
            background-color: #0056b3;
        }

        #message-box {
            margin-top: 20px;
            padding: 10px;
            border-radius: 5px;
            color: white;
        }
    </style>
</body>
</html>
