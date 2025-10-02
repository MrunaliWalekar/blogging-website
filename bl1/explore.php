<?php
require_once 'config.php';
requireLogin();

$stmt = $pdo->prepare("
    SELECT p.*, u.username, u.full_name 
    FROM posts p 
    JOIN users u ON p.user_id = u.id 
    WHERE p.user_id != ?
    ORDER BY p.created_at DESC
");
$stmt->execute([$_SESSION['user_id']]);
$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Explore - BlogSite</title>
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
  padding: 30px 20px;
}

.navbar {
  position: sticky;
  top: 0;
  width: 100%;
  background-color: #e8d1d1ff;
  box-shadow: 0 2px 15px rgba(22, 23, 23, 0.1);
  backdrop-filter: blur(8px);
  z-index: 1000;
}

.nav-container {
  max-width: 1200px;
  margin: 0 auto;
  height: 64px;
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 0 24px;
}

.nav-brand a {
  font-size: 2rem;
  font-weight: 700;
  color: #161717ff;
  text-decoration: none;
  user-select: none;
}

.nav-brand a:hover {
  color: #a67a7a;
}

.nav-menu {
  display: flex;
  gap: 28px;
}

.nav-link {
  color: #161717ff;
  font-weight: 600;
  padding: 10px 18px;
  border-radius: 24px;
  text-decoration: none;
  cursor: pointer;
  transition: background-color 0.25s ease, color 0.25s ease;
}

.nav-link:hover,
.nav-link.active {
  background-color: #161717ff;
  color: #e8d1d1ff;
}

.main-container {
  max-width: 1100px;
  margin: 40px auto;
  background: #fff0f0;
  padding: 30px 28px;
  border-radius: 18px;
  box-shadow: 0 20px 40px rgba(22, 23, 23, 0.15);
  color: #161717ff;
}

.explore-header {
  font-size: 2rem;
  font-weight: 700;
  margin-bottom: 24px;
  text-align: center;
  color: #161717ff;
}

.posts-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
  gap: 28px;
}

.post-card {
  background: #fff8f8;
  border-radius: 20px;
  box-shadow: 0 6px 18px rgba(22, 23, 23, 0.12);
  padding: 22px 25px;
  transition: all 0.3s ease;
  cursor: pointer;
}

.post-card:hover {
  box-shadow: 0 14px 38px rgba(22, 23, 23, 0.18);
  transform: translateY(-6px);
}

.post-image {
  width: 100%;
  height: 200px;
  object-fit: cover;
  border-radius: 14px;
  margin-bottom: 18px;
}

.post-title {
  font-size: 1.3rem;
  font-weight: 700;
  color: #161717ff;
  margin-bottom: 10px;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.post-excerpt {
  font-size: 1rem;
  color: #573f3f;
  line-height: 1.4;
  height: 65px; 
  overflow: hidden;
  text-overflow: ellipsis;
}

.no-posts-message {
  font-size: 1.15rem;
  color: #6a5858;
  text-align: center;
  padding: 60px 20px;
  background: #fdf5f5;
  border-radius: 20px;
  box-shadow: inset 0 0 16px rgba(22, 23, 23, 0.06);
}
@media (max-width: 768px) {
  .nav-container {
    flex-wrap: wrap;
    padding: 12px 24px;
  }
  .nav-menu {
    width: 100%;
    justify-content: center;
    gap: 18px;
    order: 3;
  }
    .main-container {
    margin: 20px 15px;
    padding: 24px 20px;
  }
  .posts-grid {
    grid-template-columns: 1fr;
  }
}

@media (max-width: 480px) {
  .nav-link {
    padding: 8px 12px;
    font-size: 14px;
  }
}

     </style>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <nav class="navbar">
        <div class="nav-container">
            <div class="nav-brand">
                <a href="dashboard.php">BlogSite</a>
            </div>
            
            <div class="nav-search">
                <form method="GET" action="dashboard.php">
                    <input type="text" name="search" placeholder="Search posts...">
                    <button type="submit">Search</button>
                </form>
            </div>
            
            <div class="nav-menu">
                <a href="dashboard.php" class="nav-link">Home</a>
                <a href="explore.php" class="nav-link active">Explore</a>
                <a href="profile.php" class="nav-link">Profile</a>
                <a href="about.php" class="nav-link">About</a>
                <a href="logout.php" class="nav-link">Logout</a>
            </div>
        </div>
    </nav>

    <div class="main-container">
        <div class="dashboard-header">
            <h1>Explore Posts</h1>
            <p>Discover amazing content from other bloggers</p>
        </div>

        <div class="posts-grid">
            <?php if (empty($posts)): ?>
                <div class="no-posts">
                    <p>No posts from other users yet. Check back later!</p>
                </div>
            <?php else: ?>
                <?php foreach ($posts as $post): ?>
                    <div class="post-card">
                        <?php if ($post['image']): ?>
                            <img src="uploads/<?php echo htmlspecialchars($post['image']); ?>" alt="Post Image" class="post-image">
                        <?php endif; ?>
                        <div class="post-content">
                            <h3><a href="view_post.php?id=<?php echo $post['id']; ?>"><?php echo htmlspecialchars($post['title']); ?></a></h3>
                            <p class="post-excerpt"><?php echo substr(htmlspecialchars($post['content']), 0, 150) . '...'; ?></p>
                            <div class="post-meta">
                                <span class="author">By <?php echo htmlspecialchars($post['full_name']); ?></span>
                                <span class="date"><?php echo formatTimeAgo($post['created_at']); ?></span>
                                <div class="post-stats">
                                    <span><?php echo $post['likes_count']; ?> likes</span>
                                    <span><?php echo $post['comments_count']; ?> comments</span>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>