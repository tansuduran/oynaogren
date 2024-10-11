<?php
session_start(); // Oturum başlatma

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $selectedAnswer = $_POST['answer']; // Kullanıcının seçtiği cevap

    // Mesaj ve süre ayarları
    if ($selectedAnswer == $_SESSION['correctAnswer']) {
        $_SESSION['message'] = "Tebrikler, Doğru Cevap!";
    } else {
        $_SESSION['message'] = "Yanlış, doğru cevap: " . $_SESSION['correctAnswer'];
        $_SESSION['yanlis']++; // Yanlış sayısını artır
    }

    // Doğru cevabı say
    if ($selectedAnswer == $_SESSION['correctAnswer']) {
        $_SESSION['dogru']++; // Doğru sayısını artır
    }

    // Tüm cevaplar için süreyi 4 saniye olarak ayarla
    $_SESSION['duration'] = 4000; // 4 saniye (4000 milisaniye)

    // Aynı sayfada geri bildirim mesajını göster
    $_SESSION['show_feedback'] = true;

    // Ana sayfaya geri dön
    header('Location: index.php');
    exit();
}
