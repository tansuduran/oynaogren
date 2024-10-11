<?php 
session_start(); // Sitzung starten

// Wenn 3 falsche Antworten gegeben werden, endet das Spiel und der Ergebnisbildschirm wird angezeigt
if ($_SESSION['wrong'] >= 3) {
    $message = "Spiel vorbei! Richtige Antworten: {$_SESSION['correct']}";
    $showGameOverButtons = true;
    session_destroy();
} else {
    $showGameOverButtons = false;
}

// Wenn es eine Feedback-Nachricht gibt, zeige sie an und fahre nach einer bestimmten Zeit mit der nächsten Frage fort
$duration = 4000; // Die Dauer auf 4 Sekunden festlegen
$message = $message ?? ''; // Wenn keine Nachricht vorhanden ist, leer lassen

if (isset($_SESSION['show_feedback'])) {
    $message = $_SESSION['message'];
    // Sitzung zurücksetzen, wenn zur nächsten Frage übergegangen wird
    unset($_SESSION['show_feedback']);
    unset($_SESSION['message']);
} else {
    // Neue Frage generieren
    include 'data.php'; // Tiertabelle einbinden
    
    // Zufälliges Tier auswählen und in der Sitzung speichern
    if (!isset($_SESSION['question'])) {
        $currentQuestionIndex = array_rand($animals);
        $_SESSION['question'] = $animals[$currentQuestionIndex]; // Ausgewähltes Tier in der Sitzung speichern
        $_SESSION['correctAnswer'] = $_SESSION['question']['name']; // Richtige Antwort speichern
        
        // Zufällige Antwortmöglichkeiten vorbereiten
        $allNames = array_column($animals, 'name');
        shuffle($allNames);
        $_SESSION['options'] = array_slice($allNames, 0, 3); // 3 zufällige Optionen auswählen
        
        // Richtige Antwort zu den Optionen hinzufügen
        if (!in_array($_SESSION['correctAnswer'], $_SESSION['options'])) {
            $_SESSION['options'][array_rand($_SESSION['options'])] = $_SESSION['correctAnswer'];
        }
        shuffle($_SESSION['options']); // Optionen mischen
    }
}
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Mini-Tierspiel</title>
</head>
<body>
    <div class="game-container">
        <h1>Mini-Tierspiel</h1>
        <p>Richtig: <?= $_SESSION['correct'] ?? 0 ?>, Falsch: <?= $_SESSION['wrong'] ?? 0 ?></p>
        
        <form method="POST" action="submit_answer.php">
            <img src="<?= $_SESSION['question']['image'] ?>" alt="Tierbild" class="animal-image">
            <?php foreach ($_SESSION['options'] as $option): ?>
                <button type="submit" name="answer" value="<?= $option ?>"><?= $option ?></button>
            <?php endforeach; ?>
        </form>

        <!-- Feedback-Nachricht nach einer Antwort -->
        <?php if ($message): ?>
            <div id="message-box" style="margin-top: 20px; background-color: <?= strpos($message, 'Herzlichen Glückwunsch') !== false ? '#28a745' : '#dc3545' ?>; color: white; padding: 10px; border-radius: 5px;"><?= $message ?></div>

            <script>
                setTimeout(function() {
                    <?php if (!$showGameOverButtons): ?>
                        // Zur nächsten Frage wechseln
                        <?php unset($_SESSION['question']); ?>
                        window.location.href = 'index.php';
                    <?php endif; ?>
                }, <?= $duration ?>);
            </script>
        <?php endif; ?>

        <!-- Buttons und Ergebnisnachricht, wenn das Spiel endet -->
        <?php if ($showGameOverButtons): ?>
            <div style="margin-top: 20px; padding: 10px; background-color: #28a745; color: white; border-radius: 5px;">
                <h2>Spiel vorbei! Richtige Antworten: <?= $_SESSION['correct'] ?? 0 ?></h2>
            </div>
            <div style="margin-top: 20px;">
                <a href="index.php" class="button">Nochmal spielen</a>
                <a href="../index.html" class="button">Startseite</a>
            </div>
            <script>
                // Automatisch zum Seitenende scrollen, wenn das Spiel endet
                window.onload = function() {
                    window.scrollTo(0, document.body.scrollHeight);
                };
            </script>
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
