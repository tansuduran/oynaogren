<?php include('db.php'); ?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mini Sınav 1</title>
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

        .button-group {
            display: flex;
            flex-direction: column;
            gap: 15px;
            width: 100%;
            max-width: 300px;
        }

        .option-button {
            flex: 1;
            padding: 15px 20px;
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

        .correct-answer {
            color: #28a745;
            font-weight: bold;
            margin-top: 10px;
        }

        .wrong-answer {
            color: #dc3545;
            font-weight: bold;
            margin-top: 10px;
        }

        .next-action {
            margin-top: 10px;
            color: #333;
        }
    </style>
</head>
<body>
    <h1>Mini Sınav 1</h1>
    
    <?php
    session_start();
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

    $sql = "SELECT * FROM Kelimeler001 ORDER BY RAND() LIMIT 1";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    $correct_word = $row['Kelime'];
    $correct_answer = $row['Anlam001'];

    $wrong_words = [];
    $sql_wrong = "SELECT Kelime FROM Kelimeler001 WHERE Kelime != '$correct_word' ORDER BY RAND() LIMIT 3";
    $result_wrong = $conn->query($sql_wrong);
    while ($wrong_row = $result_wrong->fetch_assoc()) {
        $wrong_words[] = $wrong_row['Kelime'];
    }

    $options = array_merge([$correct_word], $wrong_words);
    shuffle($options);
    ?>

    <div class="question-container">
        <p><strong>Anlam:</strong> <?php echo $correct_answer; ?></p>
        <div class="timer">Kalan Süre: <span id="timer">15</span> saniye</div>

        <form id="timeoutForm" action="submit_answer.php" method="POST" style="display:none;">
            <input type="hidden" name="correct_answer" value="<?php echo $correct_word; ?>">
            <input type="hidden" name="answer" value="">
        </form>

        <div class="button-group">
            <?php foreach ($options as $option): ?>
                <form action="submit_answer.php" method="POST" style="display:block;">
                    <input type="hidden" name="correct_answer" value="<?php echo $correct_word; ?>">
                    <input type="hidden" name="answer" value="<?php echo $option; ?>">
                    <button type="submit" class="option-button"><?php echo $option; ?></button>
                </form>
            <?php endforeach; ?>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let timer = 15;
            const interval = setInterval(function() {
                if (timer > 0) {
                    document.getElementById('timer').innerText = timer;
                    timer--;
                } else {
                    clearInterval(interval);
                    document.getElementById('timer').innerText = '0'; // Süre dolduğunda 0 gösterir
                    document.getElementById('timeoutForm').submit(); // Süre dolduğunda formu otomatik gönderir
                }
            }, 1000);
        });
    </script>
</body>
</html>
