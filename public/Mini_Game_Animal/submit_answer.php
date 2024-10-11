<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $selectedAnswer = $_POST['answer'];

    // Check if the selected answer is correct
    if ($selectedAnswer === $_SESSION['correctAnswer']) {
        $_SESSION['correct'] = ($_SESSION['correct'] ?? 0) + 1;
        $_SESSION['message'] = "Congratulations! Correct answer: " . $_SESSION['correctAnswer'];
    } else {
        $_SESSION['wrong'] = ($_SESSION['wrong'] ?? 0) + 1;
        $_SESSION['message'] = "Wrong, the correct answer is: " . $_SESSION['correctAnswer'];
    }

    // Show feedback for the next question
    $_SESSION['show_feedback'] = true;

    // Redirect to the game page after answering
    header('Location: index.php');
    exit();
}