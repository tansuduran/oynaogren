<?php 
session_start(); // Start session

// If 3 wrong answers are given, the game ends and the result screen is shown
if ($_SESSION['wrong'] >= 3) {
    $message = "Game Over! Correct answers: {$_SESSION['correct']}";
    $showGameOverButtons = true;
    session_destroy();
} else {
    $showGameOverButtons = false;
}

// If there's a feedback message, display it and proceed to the next question after a certain time
$duration = 4000; // Set duration to 4 seconds
$message = $message ?? ''; // If no message, keep it blank

if (isset($_SESSION['show_feedback'])) {
    $message = $_SESSION['message'];
    // Reset the session when moving to the next question
    unset($_SESSION['show_feedback']);
    unset($_SESSION['message']);
} else {
    // Generate new question
    include 'data.php'; // Include animal data
    
    // Select a random animal and add it to the session
    if (!isset($_SESSION['question'])) {
        $currentQuestionIndex = array_rand($animals);
        $_SESSION['question'] = $animals[$currentQuestionIndex]; // Add selected animal to session
        $_SESSION['correctAnswer'] = $_SESSION['question']['name']; // Add correct answer to session
        
        // Prepare random options
        $allNames = array_column($animals, 'name');
        shuffle($allNames);
        $_SESSION['options'] = array_slice($allNames, 0, 3); // Take 3 random options
        
        // Add the correct answer to the options
        if (!in_array($_SESSION['correctAnswer'], $_SESSION['options'])) {
            $_SESSION['options'][array_rand($_SESSION['options'])] = $_SESSION['correctAnswer'];
        }
        shuffle($_SESSION['options']); // Shuffle options
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Mini Animal Game</title>
</head>
<body>
    <div class="game-container">
        <h1>Mini Animal Game</h1>
        <p>Correct: <?= $_SESSION['correct'] ?? 0 ?>, Wrong: <?= $_SESSION['wrong'] ?? 0 ?></p>
        
        <form method="POST" action="submit_answer.php">
            <img src="<?= $_SESSION['question']['image'] ?>" alt="Animal Image" class="animal-image">
            <?php foreach ($_SESSION['options'] as $option): ?>
                <button type="submit" name="answer" value="<?= $option ?>"><?= $option ?></button>
            <?php endforeach; ?>
        </form>

        <!-- Feedback message after an answer -->
        <?php if ($message): ?>
            <div id="message-box" style="margin-top: 20px; background-color: <?= strpos($message, 'Congratulations') !== false ? '#28a745' : '#dc3545' ?>; color: white; padding: 10px; border-radius: 5px;"><?= $message ?></div>

            <script>
                setTimeout(function() {
                    <?php if (!$showGameOverButtons): ?>
                        // Move to the next question
                        <?php unset($_SESSION['question']); ?>
                        window.location.href = 'index.php';
                    <?php endif; ?>
                }, <?= $duration ?>);
            </script>
        <?php endif; ?>

        <!-- Show buttons and result message when the game ends -->
        <?php if ($showGameOverButtons): ?>
            <div style="margin-top: 20px; padding: 10px; background-color: #28a745; color: white; border-radius: 5px;">
                <h2>Game Over! Correct answers: <?= $_SESSION['correct'] ?? 0 ?></h2>
            </div>
            <div style="margin-top: 20px;">
                <a href="index.php" class="button">Play Again</a>
                <a href="../index.html" class="button">Home Page</a>
            </div>
            <script>
                // Scroll to the bottom of the page when the game ends
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
