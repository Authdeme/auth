<?php
session_start();
require_once __DIR__ . '/includes/functions.php';

if (!isset($_SESSION['verify_email'])) {
    header("Location: signup.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once __DIR__ . '/config/db.php';
    
    $otp = trim($_POST['otp']);
    $email = $_SESSION['verify_email'];
    
    // Check OTP
    $stmt = $pdo->prepare("SELECT * FROM email_verification_otps WHERE email = ? AND otp = ? AND expires_at > NOW()");
    $stmt->execute([$email, $otp]);
    
    if ($stmt->rowCount() > 0) {
        // Mark email as verified
        $stmt = $pdo->prepare("UPDATE users SET email_verified_at = NOW() WHERE email = ?");
        $stmt->execute([$email]);
        
        // Delete used OTP
        $stmt = $pdo->prepare("DELETE FROM email_verification_otps WHERE email = ?");
        $stmt->execute([$email]);
        
        // Get user ID and log them in
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        
        $_SESSION['user_id'] = $user['id'];
        unset($_SESSION['verify_email']);
        
        $_SESSION['message'] = "Email verified successfully!";
        header("Location: dashboard.php");
        exit();
    } else {
        $_SESSION['message'] = "Invalid or expired OTP!";
    }
}
?>

<?php include __DIR__ . '/includes/header.php'; ?>
<link rel="stylesheet" href="assets/css/style.css">
<h1>Verify Email</h1>
<p>We've sent an OTP to <?= htmlspecialchars($_SESSION['verify_email']) ?>. Please check your email.</p>
<form method="POST">
    <div class="form-group">
        <label for="otp">Enter OTP</label>
        <input type="text" id="otp" name="otp" required maxlength="6">
    </div>
    <button type="submit">Verify</button>
</form>
<?php include __DIR__ . '/includes/footer.php'; ?>