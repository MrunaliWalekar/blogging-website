<?php
require_once 'config.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $full_name = trim($_POST['full_name'] ?? '');

    if (!$username || !$email || !$password || !$confirm_password || !$full_name) {
        $error = 'All fields are required';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Invalid email address';
    } elseif ($password !== $confirm_password) {
        $error = 'Passwords do not match';
    } else {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$username, $email]);
        if ($stmt->rowCount() > 0) {
            $error = 'Username or email already exists';
        } else {

            $stmt = $pdo->prepare("INSERT INTO users (username, email, password, full_name) VALUES (?, ?, ?, ?)");
            if ($stmt->execute([$username, $email, $password, $full_name])) {
                $success = 'Account created successfully! You can now login.';
                $_POST = []; 
            } else {
                $error = 'Error creating account';
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Signup - BlogSite</title>
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
  p {
    max-width: 400px;
    margin: 10px auto;
    text-align: center;
}

p.error {
    color: #1b1919ff;
    font-weight: 600;
}

p.success {
    color: #1c1d1bff;
    font-weight: 600;
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
  <h1>Sign Up</h1>
  <?php if ($error): ?>
    <p class="error"><?=htmlspecialchars($error)?></p>
<?php elseif ($success): ?>
    <p class="success"><?=htmlspecialchars($success)?></p>
<?php endif; ?>
  <form method="POST" autocomplete="off">
    <label for="full_name">Name</label>
    <input type="text" id="full_name" name="full_name" required autofocus />
    <label for="username">Username</label>
    <input type="text" id="username" name="username" required autofocus />
    <label for="email"> Email</label>
    <input type="text" id="email" name="email" required autofocus />
    <label for="password">Password</label>
    <input type="password" id="password" name="password" required />
    <label for="confirm_password">Confirm Password</label>
    <input type="password" id="confirm_password" name="confirm_password" required />
    <button type="submit">Sign Up</button>
  </form>
  <p>Already have an account? <a href="login.php">Login here</a></p>
  <p><a href="admin_login.php">Admin Login</a></p>
</div>
</body>
</html>
