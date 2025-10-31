<?php
require_once 'config.php';
requireLogin();

$stmt = $pdo->prepare("
    SELECT p.*, u.username, u.full_name 
    FROM posts p 
    JOIN users u ON p.user_id = u.id 
    ORDER BY p.created_at DESC 
    LIMIT 20
");
$stmt->execute();
$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

$search_posts = [];
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search_term = '%' . sanitizeInput($_GET['search']) . '%';
    $stmt = $pdo->prepare("
        SELECT p.*, u.username, u.full_name 
        FROM posts p 
        JOIN users u ON p.user_id = u.id 
        WHERE p.title LIKE ? OR p.content LIKE ?
        ORDER BY p.created_at DESC
    ");
    $stmt->execute([$search_term, $search_term]);
    $search_posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Monotone Navbar Example</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
       
  .navbar {
    width: 100%;
    position: sticky;
    top: 0;
    background-color: #e8d1d1ff;
    box-shadow: 0 2px 15px rgba(22, 23, 23, 0.1); 
    backdrop-filter: blur(8px);
    z-index: 1000;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
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
    letter-spacing: 2px;
    cursor: pointer;
    transition: color 0.3s ease;
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
    transition: background-color 0.25s ease, color 0.25s ease;
    cursor: pointer;
  }

  .nav-link:hover,
  .nav-link.active {
    background-color: #161717ff; 
    color: #e8d1d1ff; 
  }

  .nav-search {
    flex: 1;
    max-width: 400px;
    margin-left: 30px;
  }

  .nav-search form {
    display: flex;
    gap: 10px;
  }

  .nav-search input[type="search"] {
    flex: 1;
    padding: 10px 16px;
    border: 2px solid #161717ff;
    border-radius: 24px;
    font-size: 14px;
    background: #fcecec;
    color: #161717ff;
    transition: border-color 0.3s ease;
  }

  .nav-search input[type="search"]:focus {
    outline: none;
    border-color: #a67a7a;
    box-shadow: 0 0 8px #a67a7a;
  }

  .nav-search button {
    background-color: #161717ff;
    border: none;
    border-radius: 24px;
    padding: 10px 24px;
    cursor: pointer;
    font-weight: 600;
    color: #e8d1d1ff;
    transition: background-color 0.3s ease;
  }

  .nav-search button:hover {
    background-color: #a67a7a;
  }
  
a {
  color: #a67777;
  text-decoration: none;
  font-weight: 600;
}

a:hover {
  text-decoration: underline;
}

  @media (max-width: 900px) {
    .nav-container {
      flex-wrap: wrap;
      height: auto;
      gap: 12px;
      padding: 12px 24px;
    }
    .nav-menu {
      width: 100%;
      justify-content: center;
      gap: 15px;
      order: 3;
    }
    .nav-search {
      max-width: 100%;
      margin-left: 0;
      order: 2;
    }
    .nav-brand {
      flex: 1 1 100%;
      text-align: center;
      order: 1;
    }
  }
        body {
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  background-color: #e8d1d1ff; 
  color: #161717ff; 
  margin: 0;
  min-height: 100vh;
  padding: 30px 20px;
}

.container {
  max-width: 900px;
  margin: 0 auto;
  background: white;
  border-radius: 18px;
  box-shadow: 0 20px 40px rgba(22, 23, 23, 0.15);
  padding: 30px 25px;
}

h1, h2 {
  color: #161717ff;
  font-weight: 700;
  text-align: center;
  margin-bottom: 30px;
}

.search-bar {
  display: flex;
  justify-content: center;
  gap: 12px;
  margin-bottom: 40px;
}

.search-bar input[type="text"] {
  width: 70%;
  padding: 14px 18px;
  border-radius: 15px;
  border: 2px solid #161717ff;
  font-size: 1rem;
  color: #161717ff;
  background: #fff0f0;
  transition: border-color 0.3s ease;
}

.search-bar input[type="text"]:focus {
  outline: none;
  border-color: #a36666;
  box-shadow: 0 0 8px #a36666;
}

.search-bar button {
  background-color: #161717ff;
  color: #e8d1d1ff;
  border: none;
  border-radius: 15px;
  padding: 14px 24px;
  font-weight: 700;
  cursor: pointer;
  transition: background-color 0.3s ease;
}

.search-bar button:hover {
  background-color: #a36666;
}

.posts-list {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
  gap: 25px;
}

.post-card {
  background: #fef4f4;
  border: 2px solid #161717ff;
  border-radius: 18px;
  padding: 22px 20px;
  box-shadow: 0 6px 20px rgba(22, 23, 23, 0.1);
  cursor: pointer;
  transition: transform 0.25s ease, box-shadow 0.25s ease;
}

.post-card:hover {
  transform: translateY(-6px);
  box-shadow: 0 12px 36px rgba(22, 23, 23, 0.3);
}

.post-title {
  color: #161717ff;
  font-weight: 700;
  font-size: 1.2rem;
  margin-bottom: 12px;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.post-image {
  width: 100%;
  height: 200px; 
  object-fit: cover; 
  border-radius: 12px; 
  display: block;
}

.post-excerpt {
  color: #6b5b5b;
  font-size: 0.95rem;
  line-height: 1.4;
  height: 63px; 
  overflow: hidden;
  text-overflow: ellipsis;
  margin-bottom: 15px;
}

.post-meta {
  font-size: 0.85rem;
  color: #3f2f2f;
  display: flex;
  justify-content: space-between;
  font-weight: 600;
}

    </style>
</head>
<body>
    <nav class="navbar">
  <div class="nav-container">
    <div class="nav-brand">
      <a href="dashboard.php">MyBlog</a>
    </div>
    <div class="nav-search">
      <form action="" method="GET">
        <input type="search" name="search" placeholder="Search posts...">
        <button type="submit">Go</button>
      </form>
    </div>
    <div class="nav-menu">
      <a href="dashboard.php" class="nav-link active">Dashboard</a>
      <a href="explore.php" class="nav-link">Explore</a>
      <a href="profile.php" class="nav-link">Profile</a>
      <a href="logout.php" class="nav-link">Logout</a>
    </div>
  </div>
</nav>
    <div class="main-container">
        <div class="dashboard-header">
            <h1>Welcome back, <?php echo htmlspecialchars($_SESSION['full_name']); ?>!</h1>
        </div>

        <?php if (!empty($search_posts)): ?>
            <div class="search-results">
                <h2>Search Results for "<?php echo htmlspecialchars($_GET['search']); ?>"</h2>
                <div class="posts-grid">
                    <?php foreach ($search_posts as $post): ?>
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
                </div>
            </div>
        <?php else: ?>
            <div class="recent-posts">
                <h2>Recent Posts</h2>
                <div class="post-card">
                <div class="posts-grid">
                    <?php if (empty($posts)): ?>
                        <p class="no-posts">No posts yet. <a href="create_post.php">Create the first one!</a></p>
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
        <?php endif; ?>
    </div>

</body>
</html>
