<?php
session_start();

$admin_username = 'admin';
$admin_password = 'admin123';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize input
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($username === $admin_username && $password === $admin_password) {
        $_SESSION['is_admin'] = 0;
        $_SESSION['admin_username'] = $admin_username;
        header('Location: admin_dashboard.php');
        exit; // critical to stop execution after redirect
    } else {
        $error = 'Invalid admin credentials';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Admin Login</title>
    <style>
      /* minimal styling omitted for brevity */
    </style>
</head>
<body>
    <h1>Admin Login</h1>
    <?php if ($error): ?>
        <p style="color:red;"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>
    <form method="POST" action="">
        <label>Username: <input type="text" name="username" required autofocus></label><br>
        <label>Password: <input type="password" name="password" required></label><br>
        <button type="submit">Login</button>
    </form>
</body>
</html>
