<?php
require_once 'config.php';
requireLogin();

// Get user's posts
$stmt = $pdo->prepare("
    SELECT * FROM posts 
    WHERE user_id = ? 
    ORDER BY created_at DESC
");
$stmt->execute([$_SESSION['user_id']]);
$user_posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get user stats
$stmt = $pdo->prepare("SELECT COUNT(*) as total_posts FROM posts WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$total_posts = $stmt->fetch()['total_posts'];

$stmt = $pdo->prepare("SELECT SUM(likes_count) as total_likes FROM posts WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$total_likes = $stmt->fetch()['total_likes'] ?? 0;

$stmt = $pdo->prepare("SELECT SUM(comments_count) as total_comments FROM posts WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$total_comments = $stmt->fetch()['total_comments'] ?? 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - BlogSite</title>
    <style>
        /* General reset */
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

body {
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  background-color: #e8d1d1ff; /* Soft pink background */
  color: #161717ff; /* Dark charcoal text */
  min-height: 100vh;
  padding: 30px 20px;
}

/* Navigation Bar */
.navbar {
  position: sticky;
  top: 0;
  width: 100%;
  background-color: #e8d1d1ff;
  box-shadow: 0 2px 12px rgba(22, 23, 23, 0.12);
  backdrop-filter: blur(8px);
  z-index: 1000;
  font-family: inherit;
}

.nav-container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 24px;
  height: 64px;
  display: flex;
  justify-content: space-between;
  align-items: center;
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

/* Profile Container */
.profile-container {
  max-width: 900px;
  margin: 40px auto;
  background: #fff0f0;
  padding: 30px 28px;
  border-radius: 18px;
  box-shadow: 0 20px 40px rgba(22, 23, 23, 0.15);
  color: #161717ff;
}

/* Profile Header */
.profile-header {
  display: flex;
  align-items: center;
  gap: 25px;
  margin-bottom: 35px;
}

.profile-avatar {
  width: 110px;
  height: 110px;
  border-radius: 50%;
  background: linear-gradient(135deg, #0d0d0eff, #5f506eff);
  color: white;
  font-size: 42px;
  font-weight: 700;
  display: flex;
  justify-content: center;
  align-items: center;
  user-select: none;
}

.profile-name {
  font-size: 2rem;
  font-weight: 700;
}

/* Profile Stats */
.profile-stats {
  display: flex;
  gap: 40px;
  justify-content: center;
  margin-top: 20px;
}

.profile-stat {
  background: #f9f7f7;
  border: 2px solid #161717ff;
  padding: 14px 28px;
  border-radius: 15px;
  text-align: center;
}

.profile-stat-number {
  font-size: 2.2rem;
  font-weight: 700;
  color: #161717ff;
  margin-bottom: 4px;
}

.profile-stat-label {
  font-weight: 600;
  color: #5a4a4a;
}

/* Posts Section */
.posts-section {
  margin-top: 45px;
}

.posts-section h2 {
  font-size: 1.85rem;
  font-weight: 600;
  margin-bottom: 20px;
  text-align: center;
  color: #161717ff;
}

/* Post Cards */
.posts-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
  gap: 25px;
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

.post-title {
  font-size: 1.28rem;
  font-weight: 700;
  color: #161717ff;
  margin-bottom: 10px;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.post-image {
  width: 100%;
  height: 200px; /* fixed height for uniformity */
  object-fit: cover; /* maintain aspect ratio, crop overflow */
  border-radius: 12px; /* optional: rounded corners */
  display: block;
}


.post-excerpt {
  font-size: 0.95rem;
  color: #573f3f;
  line-height: 1.4;
  height: 65px; /* Approx. 3 lines */
  overflow: hidden;
  text-overflow: ellipsis;
}

/* Responsive */
@media (max-width: 768px) {
  .nav-container {
    flex-wrap: wrap;
    padding: 12px 24px;
  }
  .nav-menu {
    flex-wrap: wrap;
    justify-content: center;
    gap: 15px;
    width: 100%;
    order: 3;
  }
  .profile-header {
    flex-direction: column;
    align-items: center;
    gap: 18px;
    text-align: center;
  }
  .profile-stats {
    justify-content: center;
    gap: 25px;
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
  .profile-name {
    font-size: 1.6rem;
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
                <a href="explore.php" class="nav-link">Explore</a>
                <a href="profile.php" class="nav-link active">Profile</a>
                <a href="about.php" class="nav-link">About</a>
                <a href="logout.php" class="nav-link">Logout</a>
            </div>
        </div>
    </nav>

    <div class="main-container">
        <div class="profile-header">
            <div class="profile-avatar">
                <?php echo strtoupper(substr($_SESSION['full_name'], 0, 1)); ?>
            </div>
            
            <h1><?php echo htmlspecialchars($_SESSION['full_name']); ?></h1>
            <p>@<?php echo htmlspecialchars($_SESSION['username']); ?></p>
            
            <div class="profile-stats">
                <div class="profile-stat">
                    <span class="profile-stat-number"><?php echo $total_posts; ?></span>
                    <span>Posts</span>
                </div>
            </div>
            
            <div style="margin-top: 25px;">
                <a href="create_post.php" class="btn btn-primary">Create New Post</a>
            </div>
        </div>

        <div class="recent-posts">
            <h2>Your Posts</h2>
            <div class="posts-grid">
                <?php if (empty($user_posts)): ?>
                    <div class="no-posts">
                        <p>You haven't created any posts yet. <a href="create_post.php">Create your first post!</a></p>
                    </div>
                <?php else: ?>
                    <?php foreach ($user_posts as $post): ?>
                        <div class="post-card">
                            <?php if ($post['image']): ?>
                                <img src="uploads/<?php echo htmlspecialchars($post['image']); ?>" alt="Post Image" class="post-image">
                            <?php endif; ?>
                            <div class="post-content">
                                <h3><a href="view_post.php?id=<?php echo $post['id']; ?>"><?php echo htmlspecialchars($post['title']); ?></a></h3>
                                <p class="post-excerpt"><?php echo substr(htmlspecialchars($post['content']), 0, 150) . '...'; ?></p>
                                <div class="post-meta">
                                    <span class="date"><?php echo formatTimeAgo($post['created_at']); ?></span>
                                    <div class="post-stats">
                                        <span><?php echo $post['likes_count']; ?> likes</span>
                                        <span><?php echo $post['comments_count']; ?> comments</span>
                                    </div>
                                </div>
                                <div class="post-actions" style="margin-top: 15px;">
                                    <a href="edit_post.php?id=<?php echo $post['id']; ?>" class="btn btn-secondary" style="padding: 8px 15px; font-size: 14px;">Edit</a>
                                    <a href="delete_post.php?id=<?php echo $post['id']; ?>" class="btn btn-danger" style="padding: 8px 15px; font-size: 14px;" 
                                       onclick="return confirm('Are you sure you want to delete this post?')">Delete</a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>