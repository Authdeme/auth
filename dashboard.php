<?php
session_start();
require_once __DIR__ . '/includes/functions.php';
redirectIfNotLoggedIn();

require_once __DIR__ . '/config/db.php';

// Get user data
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();
?>

<?php include __DIR__ . '/includes/header.php'; ?>
<link rel="stylesheet" href="assets/css/style.css">
<div class="dashboard">
    <h1>Welcome, <?= htmlspecialchars($user['name']) ?>!</h1>
    <p>Email: <?= htmlspecialchars($user['email']) ?></p>
    <p>Account created on: <?= date('F j, Y', strtotime($user['created_at'])) ?></p>
    
    <a href="logout.php" class="btn">Logout</a>
</div>
<?php include __DIR__ . '/includes/footer.php'; ?>