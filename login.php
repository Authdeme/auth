<?php
session_start();
require_once __DIR__ . '/includes/functions.php';
redirectIfLoggedIn();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once __DIR__ . '/config/db.php';
    
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    
    if ($user && password_verify($password, $user['password'])) {
        if ($user['email_verified_at']) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['message'] = "Logged in successfully!";
            header("Location: dashboard.php");
            exit();
        } else {
            // Generate new OTP if not verified
            $otp = generateOTP();
            $expiresAt = date('Y-m-d H:i:s', strtotime('+10 minutes'));
            
            // Store OTP in database
            $stmt = $pdo->prepare("INSERT INTO email_verification_otps (email, otp, expires_at) VALUES (?, ?, ?)");
            $stmt->execute([$email, $otp, $expiresAt]);
            
            // Send OTP email
            sendOTPEmail($email, $otp);
            
            // Store email in session for verification
            $_SESSION['verify_email'] = $email;
            
            $_SESSION['message'] = "Your email is not verified. We've sent a new OTP.";
            header("Location: verify-otp.php");
            exit();
        }
    } else {
        $_SESSION['message'] = "Invalid email or password!";
    }
}

// Include Google Auth config
require_once __DIR__ . '/config/google_auth.php';
$googleAuthUrl = $googleClient->createAuthUrl();
?>

<?php include __DIR__ . '/includes/header.php'; ?>
<link rel="stylesheet" href="assets/css/style.css">
<h1>Login</h1>
<form method="POST">
    <div class="form-group">
        <label for="email">Email</label>
        <input type="email" id="email" name="email" required>
    </div>
    <div class="form-group">
        <label for="password">Password</label>
        <input type="password" id="password" name="password" required>
    </div>
    <button type="submit">Login</button>
    <p>Don't have an account? <a href="signup.php">Sign Up</a></p>
    <p>Forgot your password? <a href="forgot-password.php">Reset Password</a></p>
    
    <a href="<?= htmlspecialchars($googleAuthUrl) ?>" class="btn google-btn">Login with Google</a>
</form>
<?php include __DIR__ . '/includes/footer.php'; ?>