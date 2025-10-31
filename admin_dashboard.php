<?php
require_once 'config.php';

if (isset($_GET['type'], $_GET['id'])) {
    $type = $_GET['type'];
    $id = (int)$_GET['id'];
    $allowedTypes = ['user', 'post', 'comment'];

    if (in_array($type, $allowedTypes)) {
        switch ($type) {
            case 'user':
                $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
                $stmt->execute([$id]);
                break;
            case 'post':
                $stmt = $pdo->prepare("DELETE FROM posts WHERE id = ?");
                $stmt->execute([$id]);
                break;
            case 'comment':
                $stmt = $pdo->prepare("DELETE FROM comments WHERE id = ?");
                $stmt->execute([$id]);
                break;
        }
        header('Location: admin_dashboard.php');
        exit;
    }
}

$users = $pdo->query("SELECT id, username, email, created_at FROM users ORDER BY created_at DESC")->fetchAll();
$posts = $pdo->query("SELECT id, title, user_id, created_at FROM posts ORDER BY created_at DESC")->fetchAll();
$comments = $pdo->query("SELECT id, comment_text, post_id, user_id, created_at FROM comments  ORDER BY created_at DESC")->fetchAll();

?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Admin Dashboard</title>
<style>
  body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background: #e8d1d1ff;
    color: #161717ff;
    margin: 0;
    padding: 0;
  }
  .navbar {
    position: sticky;
    top: 0;
    background: #e8d1d1ff;
    padding: 16px 32px;
    box-shadow: 0 2px 12px rgba(22, 23, 23, 0.15);
    display: flex;
    justify-content: space-between;
    align-items: center;
  }
  .navbar a {
    color: #161717ff;
    font-weight: 600;
    margin-left: 20px;
    text-decoration: none;
    transition: color 0.25s;
  }
  .navbar a:hover {
    color: #a67a7a;
  }
  .container {
    max-width: 1100px;
    margin: 40px auto 60px;
    background: #fff0f0;
    padding: 40px;
    border-radius: 24px;
    box-shadow: 0 16px 48px rgba(22, 23, 23, 0.15);
  }
  h1 {
    font-weight: 900;
    font-size: 2.8rem;
    margin-bottom: 48px;
    color: #161717ff;
  }
  h2 {
    margin-top: 48px;
    margin-bottom: 20px;
    font-weight: 700;
    color: #161717cc;
  }
  table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 40px;
  }
  th, td {
    padding: 14px 12px;
    text-align: left;
    border-bottom: 1px solid #e8d1d1cc;
  }
  th {
    background-color: #f8e8e8;
    color: #161717ff;
  }
  tr:hover {
    background-color: #f1d1d1cc;
  }
  .btn-delete {
    background-color: #a67a7a;
    color: #e8d1d1ff;
    border: none;
    border-radius: 20px;
    padding: 8px 16px;
    cursor: pointer;
    font-weight: 700;
    transition: background-color 0.3s;
  }
  .btn-delete:hover {
    background-color: #161717ff;
  }

  .btn-view {
  background-color: #a67a7a;
  color: #e8d1d1ff;
  padding: 6px 14px;
  border-radius: 14px;
  font-weight: 600;
  text-decoration: none;
  margin-right: 10px;
  transition: background-color 0.3s;
}

.btn-view:hover {
  background-color: #161717ff;
  color: #e8d1d1ff;
}

  @media (max-width: 900px) {
    .container {
      padding: 30px 20px;
    }
    table {
      font-size: 0.9rem;
    }
    h1 {
      font-size: 2rem;
    }
  }
  @media (max-width: 480px) {
    .navbar {
      padding: 12px 20px;
    }
    .navbar a {
      margin-left: 14px;
      font-size: 0.9rem;
    }
  }
</style>
</head>
<body>

<nav class="navbar">
  <div><strong>Admin Dashboard</strong></div>
  <div>
    <a href="admin_dashboard.php">Dashboard</a>
    <a href="logout.php">Logout</a>
  </div>
</nav>

<div class="container">

  <h1>Admin Dashboard</h1>

  <section>
    <h2>Users</h2>
    <table>
      <thead>
        <tr><th>ID</th><th>Username</th><th>Email</th><th>Created At</th><th>Actions</th></tr>
      </thead>
      <tbody>
        <?php foreach ($users as $user): ?>
          <tr>
            <td><?= htmlspecialchars($user['id']) ?></td>
            <td><?= htmlspecialchars($user['username']) ?></td>
            <td><?= htmlspecialchars($user['email']) ?></td>
            <td><?= htmlspecialchars($user['created_at']) ?></td>
            <td><a href="?type=user&id=<?= $user['id'] ?>" class="btn-delete" onclick="return confirm('Delete this user?')">Delete</a></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </section>

  
  <h2>Posts</h2>
  <table>
    <thead>
      <tr>
        <th>ID</th>
        <th>Title</th>
        <th>User ID</th>
        <th>Created At</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($posts as $post): ?>
        <tr>
          <td><?= htmlspecialchars($post['id']) ?></td>
          <td><?= htmlspecialchars($post['title']) ?></td>
          <td><?= htmlspecialchars($post['user_id']) ?></td>
          <td><?= htmlspecialchars($post['created_at']) ?></td>
          <td>
            <a href="admin_view_post.php?id=<?= $post['id'] ?>" class="btn-view" target="_blank">View</a>
            <a href="?type=post&id=<?= $post['id'] ?>" class="btn-delete" onclick="return confirm('Delete this post?')">Delete</a>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</section>

      </tbody>
    </table>
  </section>

  <section>
    <h2>Comments</h2>
    <table>
      <thead>
        <tr><th>ID</th><th>Content</th><th>Post ID</th><th>User</th><th>Created At</th><th>Actions</th></tr>
      </thead>
      <tbody>
        <?php foreach ($comments as $comment): ?>
          <tr>
            <td><?= htmlspecialchars($comment['id']) ?></td>
            <td><?= htmlspecialchars($comment['comment_text']) ?></td>
            <td><?= htmlspecialchars($comment['post_id']) ?></td>
            <td><?= htmlspecialchars($comment['user_id'] ?? 'Unknown') ?></td>
            <td><?= htmlspecialchars($comment['created_at']) ?></td>
            <td><a href="?type=comment&id=<?= $comment['id'] ?>" class="btn-delete" onclick="return confirm('Delete this comment?')">Delete</a></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </section>

</div>

</body>
</html>
