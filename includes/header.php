<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Auth System</title>
    <link rel="stylesheet" href="/auth-system/assets/css/style.css">
</head>
<body>
    <div class="container">
    <?php if (isset($_SESSION['message'])): ?>
        <div class="alert">
            <?= $_SESSION['message']; ?>
            <?php unset($_SESSION['message']); ?>
        </div>
    <?php endif; ?>