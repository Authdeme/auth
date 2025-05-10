<?php
session_start();
require_once __DIR__ . '/includes/functions.php';
redirectIfLoggedIn();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once __DIR__ . '/config/db.php';
    
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    
    // Check if email already exists
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    
    if ($stmt->rowCount() > 0) {
        $_SESSION['message'] = "Email already registered!";
    } else {
        // Generate OTP
        $otp = generateOTP();
        $expiresAt = date('Y-m-d H:i:s', strtotime('+10 minutes'));
        
        // Store OTP in database
        $stmt = $pdo->prepare("INSERT INTO email_verification_otps (email, otp, expires_at) VALUES (?, ?, ?)");
        $stmt->execute([$email, $otp, $expiresAt]);
        
        // Store user in database (not verified yet)
        $stmt = $pdo->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
        $stmt->execute([$name, $email, $password]);
        
        // Send OTP email
        sendOTPEmail($email, $otp);
        
        // Store email in session for verification
        $_SESSION['verify_email'] = $email;
        
        header("Location: verify-otp.php");
        exit();
    }
}
?>

<?php include __DIR__ . '/includes/header.php'; ?>
<link rel="stylesheet" href="assets/css/style.css">
<h1>Sign Up</h1>
<form method="POST">
    <div class="form-group">
        <label for="name">Full Name</label>
        <input type="text" id="name" name="name" required>
    </div>
    <div class="form-group">
        <label for="email">Email</label>
        <input type="email" id="email" name="email" required>
    </div>
    <div class="form-group">
        <label for="password">Password</label>
        <input type="password" id="password" name="password" required minlength="6">
    </div>
    <button type="submit">Sign Up</button>
    <p>Already have an account? <a href="login.php">Login</a></p>
</form>
<?php include __DIR__ . '/includes/footer.php'; ?>