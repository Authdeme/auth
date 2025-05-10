<?php
session_start();
require_once __DIR__ . '/includes/functions.php';
redirectIfLoggedIn();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once __DIR__ . '/config/db.php';
    
    $email = trim($_POST['email']);
    
    // Check if email exists
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    
    if ($stmt->rowCount() > 0) {
        // Generate token
        $token = bin2hex(random_bytes(32));
        $expiresAt = date('Y-m-d H:i:s', strtotime('+1 hour'));
        
        // Store token in database
        $stmt = $pdo->prepare("INSERT INTO password_reset_tokens (email, token) VALUES (?, ?)");
        $stmt->execute([$email, $token]);
        
        // Send reset email
        sendPasswordResetEmail($email, $token);
        
        $_SESSION['message'] = "Password reset link has been sent to your email!";
        header("Location: login.php");
        exit();
    } else {
        $_SESSION['message'] = "Email not found!";
    }
}
?>

<?php include __DIR__ . '/includes/header.php'; ?>
<link rel="stylesheet" href="assets/css/style.css">
<h1>Forgot Password</h1>
<form method="POST">
    <div class="form-group">
        <label for="email">Email</label>
        <input type="email" id="email" name="email" required>
    </div>
    <button type="submit">Reset Password</button>
    <p>Remember your password? <a href="login.php">Login</a></p>
</form>
<?php include __DIR__ . '/includes/footer.php'; ?>