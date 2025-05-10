<?php
require_once __DIR__ . '/../config/db.php';

function generateOTP() {
    return strval(rand(100000, 999999));
}

function sendOTPEmail($email, $otp) {
    // In a real application, you would send an actual email
    // This is a simulation for demonstration purposes
    $subject = "Your Verification OTP";
    $message = "Your OTP is: $otp";
    $headers = "From: no-reply@yourauthsystem.com";
    
    // For testing, we'll just log it
    error_log("Email to $email: $message");
    return true;
}

function sendPasswordResetEmail($email, $token) {
    $resetLink = "http://localhost/auth-system/reset-password.php?token=$token";
    $subject = "Password Reset Request";
    $message = "Click the following link to reset your password: $resetLink";
    $headers = "From: no-reply@yourauthsystem.com";
    
    // For testing, we'll just log it
    error_log("Password reset email to $email: $message");
    return true;
}

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function redirectIfLoggedIn() {
    if (isLoggedIn()) {
        header("Location: dashboard.php");
        exit();
    }
}

function redirectIfNotLoggedIn() {
    if (!isLoggedIn()) {
        header("Location: login.php");
        exit();
    }
}
?>