<?php
require_once 'config.php';
requireLogin();

if (!isset($_GET['id'])) {
    header('Location: dashboard.php');
    exit;
}

$post_id = (int)$_GET['id'];

$stmt = $pdo->prepare("
    SELECT p.*, u.username, u.full_name 
    FROM posts p 
    JOIN users u ON p.user_id = u.id 
    WHERE p.id = ?
");
$stmt->execute([$post_id]);
$post = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$post) {
    header('Location: dashboard.php');
    exit;
}

// Handle like action
if (isset($_POST['like_action'])) {
    $stmt = $pdo->prepare("SELECT id FROM likes WHERE post_id = ? AND user_id = ?");
    $stmt->execute([$post_id, $_SESSION['user_id']]);
    $existing_like = $stmt->fetch();
    
    // Remove any existing dislike first
    $stmt = $pdo->prepare("SELECT id FROM dislikes WHERE post_id = ? AND user_id = ?");
    $stmt->execute([$post_id, $_SESSION['user_id']]);
    $existing_dislike = $stmt->fetch();
    
    if ($existing_dislike) {
        $stmt = $pdo->prepare("DELETE FROM dislikes WHERE post_id = ? AND user_id = ?");
        $stmt->execute([$post_id, $_SESSION['user_id']]);
        
        $stmt = $pdo->prepare("UPDATE posts SET dislikes_count = dislikes_count - 1 WHERE id = ?");
        $stmt->execute([$post_id]);
    }
    
    if ($existing_like) {
        $stmt = $pdo->prepare("DELETE FROM likes WHERE post_id = ? AND user_id = ?");
        $stmt->execute([$post_id, $_SESSION['user_id']]);
       
        $stmt = $pdo->prepare("UPDATE posts SET likes_count = likes_count - 1 WHERE id = ?");
        $stmt->execute([$post_id]);
    } else {
        $stmt = $pdo->prepare("INSERT INTO likes (post_id, user_id) VALUES (?, ?)");
        $stmt->execute([$post_id, $_SESSION['user_id']]);
        
        $stmt = $pdo->prepare("UPDATE posts SET likes_count = likes_count + 1 WHERE id = ?");
        $stmt->execute([$post_id]);
    }
    
    header("Location: view_post.php?id=$post_id");
    exit;
}

// Handle dislike action
if (isset($_POST['dislike_action'])) {
    $stmt = $pdo->prepare("SELECT id FROM dislikes WHERE post_id = ? AND user_id = ?");
    $stmt->execute([$post_id, $_SESSION['user_id']]);
    $existing_dislike = $stmt->fetch();
    
    // Remove any existing like first
    $stmt = $pdo->prepare("SELECT id FROM likes WHERE post_id = ? AND user_id = ?");
    $stmt->execute([$post_id, $_SESSION['user_id']]);
    $existing_like = $stmt->fetch();
    
    if ($existing_like) {
        $stmt = $pdo->prepare("DELETE FROM likes WHERE post_id = ? AND user_id = ?");
        $stmt->execute([$post_id, $_SESSION['user_id']]);
        
        $stmt = $pdo->prepare("UPDATE posts SET likes_count = likes_count - 1 WHERE id = ?");
        $stmt->execute([$post_id]);
    }
    
    if ($existing_dislike) {
        $stmt = $pdo->prepare("DELETE FROM dislikes WHERE post_id = ? AND user_id = ?");
        $stmt->execute([$post_id, $_SESSION['user_id']]);
       
        $stmt = $pdo->prepare("UPDATE posts SET dislikes_count = dislikes_count - 1 WHERE id = ?");
        $stmt->execute([$post_id]);
    } else {
        $stmt = $pdo->prepare("INSERT INTO dislikes (post_id, user_id) VALUES (?, ?)");
        $stmt->execute([$post_id, $_SESSION['user_id']]);
        
        $stmt = $pdo->prepare("UPDATE posts SET dislikes_count = dislikes_count + 1 WHERE id = ?");
        $stmt->execute([$post_id]);
    }
    
    header("Location: view_post.php?id=$post_id");
    exit;
}

if (isset($_POST['comment_text'])) {
    $comment_text = sanitizeInput($_POST['comment_text']);
    
    if (!empty($comment_text)) {
        $stmt = $pdo->prepare("INSERT INTO comments (post_id, user_id, comment_text) VALUES (?, ?, ?)");
        if ($stmt->execute([$post_id, $_SESSION['user_id'], $comment_text])) {
        
            $stmt = $pdo->prepare("UPDATE posts SET comments_count = comments_count + 1 WHERE id = ?");
            $stmt->execute([$post_id]);
            
            header("Location: view_post.php?id=$post_id");
            exit;
        }
    }
}

if (isset($_POST['delete_comment'])) {
    $comment_id = (int)$_POST['comment_id'];
    
    $stmt = $pdo->prepare("SELECT user_id FROM comments WHERE id = ?");
    $stmt->execute([$comment_id]);
    $comment = $stmt->fetch();
    
    if ($comment && ($comment['user_id'] == $_SESSION['user_id'] || $post['user_id'] == $_SESSION['user_id'])) {
        $stmt = $pdo->prepare("DELETE FROM comments WHERE id = ?");
        $stmt->execute([$comment_id]);
        
        $stmt = $pdo->prepare("UPDATE posts SET comments_count = comments_count - 1 WHERE id = ?");
        $stmt->execute([$post_id]);
        
        header("Location: view_post.php?id=$post_id");
        exit;
    }
}

// Check if user liked the post
$stmt = $pdo->prepare("SELECT id FROM likes WHERE post_id = ? AND user_id = ?");
$stmt->execute([$post_id, $_SESSION['user_id']]);
$user_liked = $stmt->fetch() ? true : false;

// Check if user disliked the post
$stmt = $pdo->prepare("SELECT id FROM dislikes WHERE post_id = ? AND user_id = ?");
$stmt->execute([$post_id, $_SESSION['user_id']]);
$user_disliked = $stmt->fetch() ? true : false;

$stmt = $pdo->prepare("
    SELECT c.*, u.username, u.full_name 
    FROM comments c 
    JOIN users u ON c.user_id = u.id 
    WHERE c.post_id = ? 
    ORDER BY c.created_at DESC
");
$stmt->execute([$post_id]);
$comments = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($post['title']); ?> - BlogSite</title>
    <style> 
        
body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background-color: #e8d1d1ff; 
    color: #161717ff; 
    margin: 0;
    min-height: 100vh;
}

.navbar {
    position: sticky;
    top: 0;
    width: 100%;
    background-color: #e8d1d1ff;
    box-shadow: 0 2px 12px rgba(22, 23, 23, 0.12);
    backdrop-filter: blur(8px);
    z-index: 1000;
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
    letter-spacing: 1.8px;
    cursor: pointer;
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
    outline: none;
}

.nav-search input[type="search"]:focus {
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

.main-container {
    max-width: 900px;
    margin: 40px auto;
    background: #fff0f0;
    border-radius: 18px;
    box-shadow: 0 15px 40px rgba(22, 23, 23, 0.15);
    padding: 30px 28px;
    color: #161717ff;
}

.post-detail {
    background: white;
    border-radius: 18px;
    padding: 28px 30px;
    box-shadow: 0 10px 30px rgba(22, 23, 23, 0.1);
    margin-bottom: 28px;
}

.post-detail h1 {
    font-size: 2.5rem;
    font-weight: 700;
    margin-bottom: 18px;
    color: #161717ff;
}

.post-detail-meta {
    display: flex;
    justify-content: space-between;
    color: #6d5a5a;
    font-size: 0.9rem;
    margin-bottom: 22px;
    font-weight: 600;
}

.post-detail-image {
    width: 100%;
    max-height: 400px;
    object-fit: cover;
    border-radius: 14px;
    margin-bottom: 28px;
}

.post-detail-content {
    font-size: 1.1rem;
    line-height: 1.75;
    color: #3f3a3a;
}

.like-section {
    margin-bottom: 30px;
    border-bottom: 2px solid #e8d1d1ff;
    padding-bottom: 20px;
    display: flex;
    gap: 15px;
    align-items: center;
}

.like-btn, .dislike-btn {
    background: transparent;
    border: 2px solid #161717ff;
    color: #161717ff;
    padding: 10px 24px;
    border-radius: 30px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
}

.like-btn:hover,
.like-btn.liked {
    background-color: #161717ff;
    color: #e8d1d1ff;
    transform: translateY(-2px);
}

.dislike-btn:hover,
.dislike-btn.disliked {
    background-color: #8b0000;
    border-color: #8b0000;
    color: #fff;
    transform: translateY(-2px);
}

.comments-section {
    margin-top: 20px;
}

.comments-section h3 {
    margin-bottom: 20px;
    color: #161717ff;
}

.comment-form {
    background: #f6e7e7;
    padding: 20px 25px;
    border-radius: 16px;
    margin-bottom: 28px;
}

.comment-form textarea {
    width: 100%;
    border-radius: 14px;
    border: 2px solid #161717ff;
    padding: 14px;
    font-size: 1rem;
    font-family: inherit;
    resize: vertical;
    min-height: 100px;
    color: #161717ff;
}

.comment-form textarea:focus {
    border-color: #a67a7a;
    outline: none;
    box-shadow: 0 0 8px #a67a7a;
}

.comment {
    background: #fff0f0;
    padding: 20px 25px;
    border-radius: 16px;
    border-left: 5px solid #161717ff;
    margin-bottom: 18px;
}

.comment-meta {
    display: flex;
    justify-content: space-between;
    font-size: 0.85rem;
    color: #6d5a5a;
    margin-bottom: 8px;
    font-weight: 600;
}

.comment-author {
    color: #161717ff;
}

@media (max-width: 768px) {
    .nav-container {
        flex-wrap: wrap;
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
    .main-container {
        margin: 20px 15px;
        padding: 24px 20px;
    }
    .like-section {
        flex-direction: column;
        align-items: stretch;
    }
}

@media (max-width: 480px) {
    .comment-form textarea {
        min-height: 80px;
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
            
            <div class="nav-menu">
                <a href="dashboard.php" class="nav-link">Home</a>
                <a href="explore.php" class="nav-link">Explore</a>
                <a href="profile.php" class="nav-link">Profile</a>
                <a href="about.php" class="nav-link">About</a>
                <a href="logout.php" class="nav-link">Logout</a>
            </div>
        </div>
    </nav>

    <div class="main-container">
        <div class="post-detail">
            <h1><?php echo htmlspecialchars($post['title']); ?></h1>
            
            <div class="post-detail-meta">
                <div>
                    <span class="author">By <?php echo htmlspecialchars($post['full_name']); ?></span>
                    <span class="date"><?php echo formatTimeAgo($post['created_at']); ?></span>
                </div>
                
                <?php if ($post['user_id'] == $_SESSION['user_id']): ?>
                    <div class="post-actions">
                        <a href="edit_post.php?id=<?php echo $post['id']; ?>" class="btn btn-secondary">Edit</a>
                        <a href="delete_post.php?id=<?php echo $post['id']; ?>" class="btn btn-danger" 
                           onclick="return confirm('Are you sure you want to delete this post?')">Delete</a>
                    </div>
                <?php endif; ?>
            </div>
            
            <?php if ($post['image']): ?>
                <img src="uploads/<?php echo htmlspecialchars($post['image']); ?>" alt="Post Image" class="post-detail-image">
            <?php endif; ?>
            
            <div class="post-detail-content">
                <?php echo nl2br(htmlspecialchars($post['content'])); ?>
            </div>
        </div>
        
        <div class="post-interactions">
            <div class="like-section">
                <form method="POST" style="display: inline;">
                    <button type="submit" name="like_action" class="like-btn <?php echo $user_liked ? 'liked' : ''; ?>">
                        <?php echo $user_liked ? '❤️' : '🤍'; ?> <?php echo $post['likes_count']; ?> Likes
                    </button>
                </form>
                
                <form method="POST" style="display: inline;">
                    <button type="submit" name="dislike_action" class="dislike-btn <?php echo $user_disliked ? 'disliked' : ''; ?>">
                        <?php echo $user_disliked ? '💔' : '🤍'; ?> <?php echo $post['dislikes_count'] ?? 0; ?> Dislikes
                    </button>
                </form>
            </div>
            
            <div class="comments-section">
                <h3>Comments (<?php echo count($comments); ?>)</h3>
                
                <div class="comment-form">
                    <form method="POST">
                        <textarea name="comment_text" placeholder="Write a comment..." required></textarea>
                        <button type="submit" class="btn btn-primary">Post Comment</button>
                    </form>
                </div>
                
                <div class="comments-list">
                    <?php if (empty($comments)): ?>
                        <p>No comments yet. Be the first to comment!</p>
                    <?php else: ?>
                        <?php foreach ($comments as $comment): ?>
                            <div class="comment">
                                <div class="comment-meta">
                                    <span class="comment-author"><?php echo htmlspecialchars($comment['full_name']); ?></span>
                                    <div>
                                        <span><?php echo formatTimeAgo($comment['created_at']); ?></span>
                                        <?php if ($comment['user_id'] == $_SESSION['user_id'] || $post['user_id'] == $_SESSION['user_id']): ?>
                                            <form method="POST" style="display: inline; margin-left: 10px;">
                                                <input type="hidden" name="comment_id" value="<?php echo $comment['id']; ?>">
                                                <button type="submit" name="delete_comment" class="btn btn-danger" 
                                                        style="padding: 2px 8px; font-size: 12px;"
                                                        onclick="return confirm('Delete this comment?')">Delete</button>
                                            </form>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <p><?php echo nl2br(htmlspecialchars($comment['comment_text'])); ?></p>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</body>
</html>