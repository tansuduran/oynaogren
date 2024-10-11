<?php
// PHP hata ayıklama modunu açalım:
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Oturum başlatma
session_start();

// Autoload dosyasını dahil edelim
require_once __DIR__ . '/../vendor/autoload.php';  // Doğru yol

// Google Client ayarları
$client = new Google_Client();
$client->setClientId('812442138956-mrqeem7kjnnn1qtmc81fu09dl8dd0d4n.apps.googleusercontent.com');  // Doğru Client ID
$client->setClientSecret('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');  // Doğru Client Secret
$client->setRedirectUri('https://www.oynaogren.tr/callback.php');  // Geri yönlendirme URL'si
$client->addScope('email');
$client->addScope('profile');

// Google'dan kimlik doğrulama kodu alınıyor mu kontrol edin
if (isset($_GET['code'])) {
    // Google'dan kimlik doğrulama kodunu alın
    $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
    
    // Token geçerliyse, kullanıcı bilgilerini alın
    if (!isset($token['error'])) {
        $client->setAccessToken($token['access_token']);
        
        // Kullanıcı bilgilerini çekin
        $google_oauth = new Google_Service_Oauth2($client);
        $google_account_info = $google_oauth->userinfo->get();
        
        // Kullanıcı bilgilerini oturuma kaydet
        $_SESSION['name'] = $google_account_info->name;
        $_SESSION['email'] = $google_account_info->email;

        // Kullanıcıyı user_update.php sayfasına yönlendirin
        header('Location: /users/user_update.php');
        exit();
    } else {
        echo 'Kimlik doğrulama hatası: ' . $token['error'];
    }
} else {
    echo 'Google kimlik doğrulama kodu alınamadı.';
}
?>
