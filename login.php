<?php
require_once __DIR__ . '/auth_helpers.php';

start_auth_session();

$error = '';
$email = '';

if (!empty($_SESSION['user']) && is_array($_SESSION['user'])) {
    $isAdmin = ($_SESSION['user']['role'] ?? '') === 'admin';

    header('Location: ' . ($isAdmin ? 'admin/index.php' : 'index.php'));
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = strtolower(trim($_POST['email'] ?? ''));
    $password = $_POST['password'] ?? '';

    if ($email === 'admin@pawnet.com' && $password === 'admin123') {
        session_regenerate_id(true);

        $_SESSION['user'] = [
            'id' => 1,
            'name' => 'Admin',
            'email' => 'admin@pawnet.com',
            'role' => 'admin',
            'phone' => '',
            'address' => ''
        ];

        $_SESSION['admin'] = true;

        header('Location: admin/index.php');
        exit;
    }

    $error = 'Invalid email or password. Please try again.';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign in · Paw Net</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="auth.css">
</head>
<body>
    <main class="auth-shell">
        <section class="auth-story">
            <a class="auth-brand" href="welcome.php"><span class="brand-mark">🐾</span><span>Paw Net<small>Find your kind of love.</small></span></a>
            <div class="story-copy">
                <span class="eyebrow">A BETTER BEGINNING</span>
                <h1>Good things happen when you meet your match.</h1>
                <p>Find a companion, follow your adoption journey, and make a home a little happier.</p>
            </div>
            <div class="story-note"><span>“</span><p>Some of our best days begin with a paw at the door.</p></div>
            <div class="story-decoration decoration-one">🐈</div><div class="story-decoration decoration-two">🐕</div>
        </section>
        <section class="auth-content">
            <form class="auth-card" method="post" action="login.php">
                <span class="eyebrow">WELCOME BACK</span>
                <h2>Sign in to Paw Net</h2>
                <p class="auth-subtitle">Your next best friend is waiting.</p>
                <?php if ($error !== ''): ?><div class="form-alert" role="alert"><?= auth_escape($error) ?></div><?php endif; ?>
                <label>Email address<input type="email" name="email" value="<?= auth_escape($email) ?>" autocomplete="email" placeholder="you@example.com" required></label>
                <label>Password<input type="password" name="password" autocomplete="current-password" placeholder="Enter your password" required></label>
                <button class="auth-submit" type="submit">Sign in <span>→</span></button>
                <p class="auth-switch">New to Paw Net? <a href="register.php">Create an account</a></p>
                <a class="back-home" href="register.php">Start your adoption journey <span>↗</span></a>
            </form>
            <footer>By continuing, you agree to use Paw Net kindly and responsibly.</footer>
        </section>
    </main>
</body>
</html>