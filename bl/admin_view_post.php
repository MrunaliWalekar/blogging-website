<?php
require_once 'config.php';


if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: admin_dashboard.php'); // Redirect if invalid
    exit;
}

$post_id = (int)$_GET['id'];

// Fetch post details joined with author's username
$stmt = $pdo->prepare("SELECT p.title, p.content, p.image, p.created_at, u.username 
                       FROM posts p 
                       JOIN users u ON p.user_id = u.id 
                       WHERE p.id = ?");
$stmt->execute([$post_id]);
$post = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$post) {
    echo "Post not found.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>View Post - Admin Panel</title>
  <style>
    /* Base styles */
    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background: #e8d1d1ff;
      color: #161717ff;
      padding: 20px;
      min-height: 100vh;
    }
    .container {
      max-width: 750px;
      background: #fff0f0;
      margin: 30px auto;
      padding: 32px;
      border-radius: 20px;
      box-shadow: 0 14px 44px rgba(22,23,23,0.15);
    }
    h1 {
      font-weight: 900;
      font-size: 2.6rem;
      margin-bottom: 18px;
      color: #161717ff;
      user-select: none;
    }
    .post-meta {
      font-weight: 600;
      color: #826969;
      margin-bottom: 24px;
    }
    .post-content {
      font-size: 1.1rem;
      line-height: 1.5;
      white-space: pre-wrap;
      margin-bottom: 28px;
      color: #4c3c3c;
    }
    img.post-image {
      max-width: 100%;
      border-radius: 14px;
      margin-bottom: 28px;
      box-shadow: 0 5px 18px #e8d1d1cc;
      object-fit: cover;
    }
    .btn-back {
      display: inline-block;
      padding: 14px 28px;
      background: #161717ff;
      color: #e8d1d1ff;
      font-weight: 700;
      border-radius: 24px;
      text-decoration: none;
      transition: background-color 0.3s;
      user-select: none;
    }
    .btn-back:hover {
      background: #a67a7a;
      color: #161717ff;
    }
    @media (max-width: 600px) {
      .container {
        margin: 20px;
        padding: 24px;
      }
      h1 {
        font-size: 2rem;
      }
      .btn-back {
        padding: 12px 22px;
      }
    }
  </style>
</head>
<body>
  <div class="container">
    <a href="admin_dashboard.php" class="btn-back">← Back to Dashboard</a>
    <h1><?= htmlspecialchars($post['title']) ?></h1>
    <div class="post-meta">
      By <strong><?= htmlspecialchars($post['username']) ?></strong> on <em><?= date('M j, Y', strtotime($post['created_at'])) ?></em>
    </div>
    <?php if (!empty($post['image'])): ?>
      <img src="uploads/<?= htmlspecialchars($post['image']) ?>" alt="Post image" class="post-image" />
    <?php endif; ?>
    <div class="post-content"><?= nl2br(htmlspecialchars($post['content'])) ?></div>
  </div>
</body>
</html>
<?php

