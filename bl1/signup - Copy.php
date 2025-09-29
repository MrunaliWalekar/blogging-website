<?php
require_once 'config.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and get inputs safely
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $full_name = trim($_POST['full_name'] ?? '');

    // Validate all fields are filled
    if (!$username || !$email || !$password || !$confirm_password || !$full_name) {
        $error = 'All fields are required';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Invalid email address';
    } elseif ($password !== $confirm_password) {
        $error = 'Passwords do not match';
    } else {
        // Check if username or email already exists
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$username, $email]);
        if ($stmt->rowCount() > 0) {
            $error = 'Username or email already exists';
        } else {

            // Insert new user record
            $stmt = $pdo->prepare("INSERT INTO users (username, email, password, full_name) VALUES (?, ?, ?, ?)");
            if ($stmt->execute([$username, $email, $password, $full_name])) {
                $success = 'Account created successfully! You can now login.';
                $_POST = []; // clear POST data for forms
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
<title>Sign Up</title>
<style>
  *{margin: 0;
    padding: 0;
    box-sizing: border-box;
  body {
    background-color: #e8d1d1ff;
    color: #272424ff;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    margin: 20px;
}
h1 {
    font-size: 2.5rem;
    font-weight: 700;
    color: #161717ff;
    margin-bottom: 38px;
    text-align: center;
  }
form {
    background-color: #fff0f0;
    padding: 20px;
    border-radius: 8px;
    max-width: 400px;
    margin: 0 auto;
    box-shadow: 0 4px 12px rgba(232, 209, 209, 0.4);
}

label {
    display: block;
    margin-bottom: 8px;
    font-weight: 600;
}

input[type="text"],
input[type="email"],
input[type="password"],
textarea {
    width: 100%;
    padding: 10px;
    margin-bottom: 16px;
    border: 1px solid #e8d1d1ff;
    border-radius: 4px;
    background-color: #f4ddf1ff;
    color: #877474ff;
    font-size: 14px;
    box-sizing: border-box;
    transition: border-color 0.3s ease;
}

input[type="text"],
input[type="email"],
input[type="password"],
textarea {
    border-color: #272424ff;
    outline: none;
}

button {
    background-color: #e8d1d1ff;
    color: #161717ff;
    padding: 12px 20px;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    font-weight: 700;
    font-size: 16px;
    transition: background-color 0.3s ease;
    width: 100%;
}

button:hover {
    background-color: #f8ebf0ff;
    color: #161717ff;
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
}}
</style>
</head>
<body>

<?php if ($error): ?>
    <p class="error"><?=htmlspecialchars($error)?></p>
<?php elseif ($success): ?>
    <p class="success"><?=htmlspecialchars($success)?></p>
<?php endif; ?>

<form action="" method="post">
       <h1>Sign up</h1>
    <label>Username:</label>
    <input type="text" name="username" value="<?=htmlspecialchars($_POST['username'] ?? '')?>" required />

    <label>Email:</label>
    <input type="email" name="email" value="<?=htmlspecialchars($_POST['email'] ?? '')?>" required />

    <label>Password:</label>
    <input type="password" name="password" required />

    <label>Confirm Password:</label>
    <input type="password" name="confirm_password" required />

    <label>Full Name:</label>
    <input type="text" name="full_name" value="<?=htmlspecialchars($_POST['full_name'] ?? '')?>" required />

    <button type="submit">Sign Up</button>
    <p>have an account? <a href="login.php">Login here</a></p>
    <p><a href="admin_login.php">Admin Login</a></p>
</form>

</body>
</html>
