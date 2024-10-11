<?php
session_start(); // Oturum başlatma

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $selectedAnswer = $_POST['answer']; // Kullanıcının seçtiği cevap

    // Mesaj ve süre ayarları
    if ($selectedAnswer == $_SESSION['correctAnswer']) {
        $message = "Tebrikler, Doğru Cevap!";
        $duration = 2000; // 2 saniye
        $_SESSION['dogru']++; // Doğru sayısını artır
    } else {
        $message = "Yanlış, doğru cevap: " . $_SESSION['correctAnswer'];
        $duration = 4000; // 4 saniye
        $_SESSION['yanlis']++; // Yanlış sayısını artır
    }
    
    // Cevap mesajını oturumda sakla
    $_SESSION['message'] = $message;
    $_SESSION['duration'] = $duration;

    // Ana sayfaya geri dön
    header('Location: index.php');
    exit();
}
