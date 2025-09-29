<?php
include "config.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch current user data
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $bio = $_POST['bio'];

    // Handle profile image upload
    $profile_image = $user['profile_image']; // keep old if not changed
    if (!empty($_FILES['profile_image']['name'])) {
        $targetDir = "uploads/";
        $fileName = time() . "_" . basename($_FILES['profile_image']['name']);
        $targetFile = $targetDir . $fileName;

        if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $targetFile)) {
            $profile_image = $fileName;
        }
    }

    // Update user info
    $stmt = $pdo->prepare("UPDATE users SET bio = ?, profile_image = ? WHERE id = ?");
    $stmt->execute([$bio, $profile_image, $user_id]);

    // Refresh session data (optional, if you want instant updates without refetching)
    $_SESSION['profile_image'] = $profile_image;
    $_SESSION['bio'] = $bio;

    // Redirect back to profile page to reflect changes
    header("Location: profile.php");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit Profile</title>
    <link rel="stylesheet" href="assets/style.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f6f8;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 60%;
            max-width: 600px;
            margin: 40px auto;
            background: #fff;
            border-radius: 10px;
            padding: 25px;
            box-shadow: 0 3px 6px rgba(0,0,0,0.1);
        }
        h2 {
            text-align: center;
            color: #2c3e50;
        }
        label {
            font-weight: bold;
            display: block;
            margin-top: 15px;
        }
        input[type="file"], textarea {
            width: 100%;
            padding: 10px;
            margin-top: 8px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }
        textarea {
            height: 100px;
            resize: none;
        }
        button {
            margin-top: 20px;
            background: #2c3e50;
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
        }
        button:hover {
            background: #e6b4e6ff;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Edit Profile</h2>
        <form method="POST" enctype="multipart/form-data">
            <label>Username:</label>
            <textarea name="username"><?= htmlspecialchars($user['username']) ?></textarea>

            <label>Bio:</label>
            <textarea name="bio"><?= htmlspecialchars($user['bio']) ?></textarea>

            <label>Profile Image:</label>
            <input type="file" name="profile_image">

            <?php if (!empty($user['profile_image'])): ?>
                <p>Current Image:</p>
                <img src="uploads/<?= htmlspecialchars($user['profile_image']) ?>" width="120" style="border-radius: 8px;">
            <?php endif; ?>

            <button type="submit">Save Changes</button>
        </form>
    </div>
</body>
</html>
