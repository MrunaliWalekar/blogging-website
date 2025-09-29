<?php
session_start();

$admin_username = 'admin';
$admin_password = 'admin123';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($username === $admin_username && $password === $admin_password) {
        $_SESSION['is_admin'] = 0;
        $_SESSION['admin_username'] = $admin_username;
        header('Location: admin_dashboard.php');
        exit; 
    } else {
        $error = 'Invalid admin credentials';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Admin Login - BlogSite</title>
<style>
  * {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
  }
  body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background-color: #e8d1d1ff;
    color: #161717ff;
    min-height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
    padding: 40px 20px;
  }
  .login-container {
    background: #fff0f0;
    border-radius: 20px;
    padding: 48px 40px;
    max-width: 420px;
    width: 100%;
    box-shadow: 0 18px 36px rgba(22, 23, 23, 0.15);
    user-select: none;
  }
  h1 {
    font-size: 2.5rem;
    font-weight: 700;
    color: #161717ff;
    margin-bottom: 38px;
    text-align: center;
  }
  form {
    display: flex;
    flex-direction: column;
    gap: 26px;
  }
  label {
    font-size: 1rem;
    font-weight: 600;
    color: #161717cc;
  }
  input[type="text"],
  input[type="password"] {
    padding: 14px 18px;
    border-radius: 12px;
    border: 2px solid #161717ff;
    background: #faf2f2;
    color: #161717ff;
    font-size: 1rem;
    font-family: inherit;
    outline: none;
    transition: border-color 0.3s ease, box-shadow 0.3s ease;
  }
  input[type="text"]:focus,
  input[type="password"]:focus {
    border-color: #a67777;
    box-shadow: 0 0 8px #a67777;
  }
  button[type="submit"] {
    padding: 17px 0;
    border-radius: 16px;
    border: none;
    background-color: #161717ff;
    color: #e8d1d1ff;
    font-size: 1.2rem;
    font-weight: 700;
    cursor: pointer;
    box-shadow: 0 9px 32px rgba(22, 23, 23, 0.5);
    transition: background-color 0.3s ease, transform 0.2s ease;
  }
  button[type="submit"]:hover {
    background-color: #a67777;
    color: #161717ff;
    transform: translateY(-3px);
  }
  .error-message {
    margin-top: 18px;
    text-align: center;
    font-weight: 600;
    color: #a67777;
  }
  p {
    margin-top: 20px;
    text-align: center;
    font-size: 1rem;
    color: #5e5e5e;
  }
  p a {
    color: #161717ff;
    font-weight: 600;
    text-decoration: none;
    transition: color 0.3s ease;
  }
  p a:hover {
    color: #a67777;
    text-decoration: underline;
  }
  @media (max-width: 480px) {
    .login-container {
      padding: 36px 28px;
    }
    button[type="submit"] {
      font-size: 1rem;
    }
  }
</style>
</head>
<body>
<div class="login-container">
  <h1>Admin Login</h1>
  <?php if ($error): ?>
    <div class="error-message"><?= htmlspecialchars($error) ?></div>
  <?php endif; ?>
  <form method="POST" autocomplete="off">
    <label for="username">Username</label>
    <input type="text" id="username" name="username" required autofocus />
    <label for="password">Password</label>
    <input type="password" id="password" name="password" required />
    <button type="submit">Login</button>
  </form>
  <p>User account? <a href="login.php"></a></p>
</div>
</body>
</html>
