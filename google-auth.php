<?php
session_start();
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/config/google_auth.php';

if (isset($_GET['code'])) {
    $token = $googleClient->fetchAccessTokenWithAuthCode($_GET['code']);
    
    if (!isset($token['error'])) {
        $googleClient->setAccessToken($token);
        $googleService = new Google_Service_Oauth2($googleClient);
        $data = $googleService->userinfo->get();
        
        require_once __DIR__ . '/config/db.php';
        
        // Check if user exists
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$data['email']]);
        $user = $stmt->fetch();
        
        if (!$user) {
            // Register new user
            $stmt = $pdo->prepare("INSERT INTO users (name, email, google_id, email_verified_at) VALUES (?, ?, ?, NOW())");
            $stmt->execute([$data['name'], $data['email'], $data['id']]);
            $userId = $pdo->lastInsertId();
        } else {
            // Update google_id if not set
            if (!$user['google_id']) {
                $stmt = $pdo->prepare("UPDATE users SET google_id = ? WHERE id = ?");
                $stmt->execute([$data['id'], $user['id']]);
            }
            $userId = $user['id'];
        }
        
        $_SESSION['user_id'] = $userId;
        $_SESSION['message'] = "Logged in successfully with Google!";
        header("Location: dashboard.php");
        exit();
    }
}

header("Location: login.php");
exit();
?>