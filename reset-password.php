<?php
session_start();
require_once __DIR__ . '/includes/functions.php';
redirectIfLoggedIn();

if (!isset($_GET['token'])) {
    header("Location: login.php");
    exit();
}

require_once __DIR__ . '/config/db.php';

$token = $_GET['token'];

// Check if token is valid
$stmt = $pdo->prepare("SELECT * FROM password_reset_tokens WHERE token = ? AND created_at > DATE_SUB(NOW(), INTERVAL 1 HOUR)");
$stmt->execute([$token]);
$tokenData = $stmt->fetch();

if (!$tokenData) {
    $_SESSION['message'] = "Invalid or expired token!";
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    
    // Update password
    $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE email = ?");
    $stmt->execute([$password, $tokenData['email']]);
    
    // Delete token
    $stmt = $pdo->prepare("DELETE FROM password_reset_tokens WHERE token = ?");
    $stmt->execute([$token]);
    
    $_SESSION['message'] = "Password updated successfully!";
    header("Location: login.php");
    exit();
}
?>

<?php include __DIR__ . '/includes/header.php'; ?>
<link rel="stylesheet" href="assets/css/style.css">
<h1>Reset Password</h1>
<form method="POST">
    <div class="form-group">
        <label for="password">New Password</label>
        <input type="password" id="password" name="password" required minlength="6">
    </div>
    <div class="form-group">
        <label for="confirm_password">Confirm Password</label>
        <input type="password" id="confirm_password" name="confirm_password" required minlength="6">
    </div>
    <button type="submit">Update Password</button>
</form>
<?php include __DIR__ . '/includes/footer.php'; ?>