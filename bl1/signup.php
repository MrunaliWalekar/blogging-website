<?php
require_once 'config.php';

$error = '';
$success = '';

if ($_POST) {
    $username = sanitizeInput($_POST['username']);
    $email = sanitizeInput($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $full_name = sanitizeInput($_POST['full_name']);
    
    if (empty($username) || empty($email) || empty($password) || empty($full_name)) {
        $error = 'All fields are required';
    } elseif ($password !== $confirm_password) {
        $error = 'Passwords do not match';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters';
    } else {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$username, $email]);
        
        if ($stmt->rowCount() > 0) {
            $error = 'Username or email already exists';
        } else {
            $stmt = $pdo->prepare("INSERT INTO users (username, email, password, full_name) VALUES (?, ?, ?, ?)");
            
            if ($stmt->execute([$username, $email, $password, $full_name])) {
                $success = 'Account created successfully! You can now login.';
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
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - BlogSite</title>
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


.signup-container {
  background: #fff;
  border-radius: 20px;
  padding: 50px 45px;
  max-width: 420px;
  width: 100%;
  box-shadow: 0 18px 36px rgba(22, 23, 23, 0.12);
}


.signup-container h1 {
  font-size: 2.5em;
  cursor: default;
  color: #161717ff;
  font-weight: 700;
  text-align: center;
  margin-bottom: 40px;
  letter-spacing: 1.3px;
}

.signup-form {
  display: flex;
  flex-direction: column;
  gap: 24px;
}

.signup-form label {
  font-size: 1rem;
  font-weight: 600;
  color: #161717cc;
}

.signup-form input[type="text"],
.signup-form input[type="email"],
.signup-form input[type="password"] {
  padding: 14px 18px;
  font-size: 1rem;
  border-radius: 12px;
  border: 2px solid #161717ff;
  background: #faf3f3;
  color: #161717ff;
  transition: border-color 0.3s ease, box-shadow 0.3s ease;
  outline: none;
  font-family: inherit;
}

.signup-form input[type="text"]:focus,
.signup-form input[type="email"]:focus,
.signup-form input[type="password"]:focus {
  border-color: #a67777;
  box-shadow: 0 0 8px #a67777;
}

.signup-form button {
  padding: 16px 0;
  font-size: 1.2rem;
  font-weight: 700;
  border-radius: 16px;
  border: none;
  cursor: pointer;
  background: #161717ff;
  color: #e8d1d1ff;
  box-shadow: 0 8px 28px rgba(22, 23, 23, 0.5);
  user-select: none;
  transition: background-color 0.3s ease, transform 0.2s ease;
}

.signup-form button:hover {
  background: #a67777;
  color: #161717ff;
  transform: translateY(-3px);
}

.message {
  text-align: center;
  font-weight: 600;
  font-size: 1rem;
  margin-top: 20px;
  color: #a67777;
}

.signup-link {
  text-align: center;
  margin-top: 30px;
  color: #5e5e5e;
}

.signup-link a {
  color: #161717ff;
  font-weight: 600;
  text-decoration: none;
  transition: color 0.3s ease;
}

.signup-link a:hover {
  color: #a67777;
  text-decoration: underline;
}

@media (max-width: 480px) {
  .signup-container {
    padding: 35px 25px;
  }
  .signup-form button {
    font-size: 1rem;
    padding: 14px 0;
  }
}

    </style>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
  <div class="signup-container">
    <h1>Sign Up</h1>

    <!-- Display error message if any -->
    <?php if (!empty($error)): ?>
      <div class="message error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <!-- Display success message if any -->
    <?php if (!empty($success)): ?>
      <div class="message success"><?php echo htmlspecialchars($success); ?></div>
    <?php endif; ?>

    <form method="POST" class="signup-form" action="">
      <label for="fullname">Full Name</label>
      <input type="text" id="fullname" name="fullname" required />

      <label for="username">Username</label>
      <input type="text" id="username" name="username" required />

      <label for="email">Email</label>
      <input type="email" id="email" name="email" required />

      <label for="password">Password</label>
      <input type="password" id="password" name="password" required />

      <label for="confirmpassword">Confirm Password</label>
      <input type="password" id="confirmpassword" name="confirmpassword" required />

      <button type="submit">Sign Up</button>
    </form>

    <p class="signup-link">
      Already have an account? <a href="login.php">Login here</a>
    </p>
  </div>
</body>
</html>