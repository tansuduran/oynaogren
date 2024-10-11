<?php
include('db.php');
session_start();

// Sayfa yenilendi mi kontrolü
if (isset($_SESSION['last_loaded']) && $_SESSION['last_loaded'] === true && $_SESSION['navigating'] === false) {
    $_SESSION['wrong_count']++; // Eğer sayfa yenilendiyse yanlış sayısını artır
}

// İlk yükleme veya yeni soru geçişi olduğunda oturum değişkenlerini ayarla
$_SESSION['last_loaded'] = true; 
$_SESSION['navigating'] = false; // Yeniden yükleme için değişkeni sıfırla

if (!isset($_SESSION['wrong_count'])) $_SESSION['wrong_count'] = 0;
if (!isset($_SESSION['score'])) $_SESSION['score'] = 0;

echo "<p class='score-board'>Doğru: " . $_SESSION['score'] . " | Yanlış: " . $_SESSION['wrong_count'] . "</p>";

if ($_SESSION['wrong_count'] >= 3) {
    echo "<div class='question-container'><p>Mini sınav bitti. Doğru sayınız: " . $_SESSION['score'] . "</p>
          <button onclick=\"window.location.href='index.php'\" class='option-button'>Yeniden Oyna</button>
          <button onclick=\"window.location.href='https://www.oynaogren.tr'\" class='option-button'>Ana Sayfaya Dön</button></div>";
    session_destroy();
    exit();
}

// Veritabanından rastgele bir eş anlamlı kelime çifti çekmek için sorgu
$sql = "SELECT * FROM es_anlamli_kelime ORDER BY RAND() LIMIT 1";
$result = $conn->query($sql);
$row = $result->fetch_assoc();

// Kelime yönü için rastgele bir seçim yap (kelime1 -> kelime2 veya kelime2 -> kelime1)
$flip = rand(0, 1);
if ($flip == 0) {
    // Kelime1 sorulacak, Kelime2 doğru cevap olacak
    $correct_word = $row['kelime1'];
    $correct_answer = $row['kelime2'];
    $wrong_column = 'kelime2'; // Yanlış cevaplar bu sütundan gelecek
} else {
    // Kelime2 sorulacak, Kelime1 doğru cevap olacak
    $correct_word = $row['kelime2'];
    $correct_answer = $row['kelime1'];
    $wrong_column = 'kelime1'; // Yanlış cevaplar bu sütundan gelecek
}

// Yanlış cevaplar için kelimeler
$wrong_words = [];
$sql_wrong = "SELECT $wrong_column FROM es_anlamli_kelime WHERE $wrong_column != '$correct_answer' ORDER BY RAND() LIMIT 3";
$result_wrong = $conn->query($sql_wrong);
while ($wrong_row = $result_wrong->fetch_assoc()) {
    $wrong_words[] = $wrong_row[$wrong_column];
}

$options = array_merge([$correct_answer], $wrong_words);
shuffle($options);
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eş Anlamlı Kelimeler</title>
    <link rel="stylesheet" href="../style.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f0f8ff;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 20px;
        }

        h1 {
            color: #4a4a8a;
            font-size: 28px;
            margin-bottom: 10px;
        }

        p {
            font-size: 18px;
            color: #333;
        }

        .score-board {
            font-size: 20px;
            font-weight: bold;
            margin: 10px 0;
            color: #006400;
        }

        .question-container {
            background: #ffffff;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            margin: 20px 0;
            width: 90%;
            max-width: 600px;
            text-align: center;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .timer {
            font-size: 16px;
            margin-bottom: 10px;
            color: #ff4500;
        }

        .grid-container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            grid-template-rows: 1fr 1fr;
            gap: 15px;
            width: 100%;
            max-width: 400px;
        }

        .option-button {
            padding: 20px;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            transition: transform 0.2s, background-color 0.3s ease;
        }

        .option-button:hover {
            background-color: #0056b3;
            transform: scale(1.05);
        }
    </style>
</head>
<body>
    <h1>Eş Anlamlı Kelimeyi Bul</h1>

    <div class="question-container">
        <p><strong><?php echo $correct_word; ?></strong></p>
        <div class="timer">Kalan Süre: <span id="timer">10</span> saniye</div>

        <form id="timeoutForm" action="submit_answer.php" method="POST" style="display:none;">
            <input type="hidden" name="correct_answer" value="<?php echo $correct_answer; ?>">
            <input type="hidden" name="answer" value="">
            <input type="hidden" name="navigating" value="1"> <!-- Soru geçişi olduğunu işaretle -->
        </form>

        <!-- Ses Dosyaları -->
        <audio id="correctSound" src="../sounds/dogru.mp3" preload="auto"></audio>
        <audio id="wrongSound" src="../sounds/yanlis.mp3" preload="auto"></audio>

        <div class="grid-container">
            <?php foreach ($options as $option): ?>
                <form action="submit_answer.php" method="POST" onsubmit="return playSound('<?php echo $option; ?>', '<?php echo $correct_answer; ?>', this);">
                    <input type="hidden" name="correct_answer" value="<?php echo $correct_answer; ?>">
                    <input type="hidden" name="answer" value="<?php echo $option; ?>">
                    <input type="hidden" name="navigating" value="1">
                    <button type="submit" class="option-button"><?php echo $option; ?></button>
                </form>
            <?php endforeach; ?>
        </div>
    </div>

    <script>
        function playSound(selectedOption, correctAnswer, form) {
            let correctSound = document.getElementById('correctSound');
            let wrongSound = document.getElementById('wrongSound');

            if (selectedOption === correctAnswer) {
                correctSound.play();
            } else {
                wrongSound.play();
            }

            // Soru geçişinde 1 saniye gecikme
            setTimeout(function() {
                form.submit(); // Form gönderimi 1 saniye sonra gerçekleşir
            }, 1000);

            return false; // Formun hemen gönderilmesini durdur
        }

        document.addEventListener('DOMContentLoaded', function() {
            let timer = 10;
            const interval = setInterval(function() {
                if (timer > 0) {
                    document.getElementById('timer').innerText = timer;
                    timer--;
                } else {
                    clearInterval(interval);
                    document.getElementById('timer').innerText = '0';
                    document.getElementById('timeoutForm').submit();
                }
            }, 1000);
        });
    </script>
</body>
</html>
