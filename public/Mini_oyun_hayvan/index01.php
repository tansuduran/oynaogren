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
        <?php
        session_start(); // Oturum başlatma

        // Eğer oturum değişkeni yoksa veya form gönderimi yapılmadıysa yeni bir soru oluştur
        if (!isset($_SESSION['question']) || $_SERVER['REQUEST_METHOD'] != 'POST') {
            // data.php dosyasından veriyi çekiyoruz
            include 'data.php';

            // Rastgele bir soru seçiyoruz ve oturumda saklıyoruz
            $currentQuestionIndex = array_rand($animals);
            $_SESSION['question'] = $animals[$currentQuestionIndex];
            $_SESSION['correctAnswer'] = $_SESSION['question']['name'];

            // Rastgele diğer seçenekleri oluşturuyoruz
            $allNames = array_column($animals, 'name');
            shuffle($allNames);
            $_SESSION['options'] = array_slice($allNames, 0, 3);

            // Doğru cevabı seçenekler arasına ekliyoruz
            if (!in_array($_SESSION['correctAnswer'], $_SESSION['options'])) {
                $_SESSION['options'][array_rand($_SESSION['options'])] = $_SESSION['correctAnswer'];
            }
            shuffle($_SESSION['options']);
        }

        // Eğer form gönderimi yapıldıysa cevap kontrolü yapılır
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $selectedAnswer = $_POST['answer'];
            if ($selectedAnswer == $_SESSION['correctAnswer']) {
                echo '<p class="feedback">Doğru!</p>';
            } else {
                echo '<p class="feedback">Yanlış, doğru cevap: ' . $_SESSION['correctAnswer'] . '</p>';
            }
            echo '<button onclick="window.location.href=\'index.php\'">Sonraki Soru</button>';
            session_destroy(); // Oturumu sona erdirerek yeni soruya geçişi sağlıyoruz
            exit(); // Mevcut işlemi bitiriyoruz
        }
        ?>

        <form method="post" action="">
            <img src="<?php echo $_SESSION['question']['image']; ?>" alt="Hayvan" class="animal-image">
            <?php foreach ($_SESSION['options'] as $option): ?>
                <button type="submit" name="answer" value="<?php echo $option; ?>"><?php echo $option; ?></button>
            <?php endforeach; ?>
        </form>
    </div>
</body>
</html>
