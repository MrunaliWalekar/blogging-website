<?php
// ===============================================
// edit_post.php
// ===============================================
require_once 'config.php';
requireLogin();

if (!isset($_GET['id'])) {
    header('Location: profile.php');
    exit;
}

$post_id = (int)$_GET['id'];

// Get post details and verify ownership
$stmt = $pdo->prepare("SELECT * FROM posts WHERE id = ? AND user_id = ?");
$stmt->execute([$post_id, $_SESSION['user_id']]);
$post = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$post) {
    header('Location: profile.php');
    exit;
}

$error = '';
$success = '';

if ($_POST) {
    $title = sanitizeInput($_POST['title']);
    $content = sanitizeInput($_POST['content']);
    $remove_image = isset($_POST['remove_image']);
    $image = $post['image'];
    
    if (empty($title) || empty($content)) {
        $error = 'Title and content are required';
    } else {
        // Handle image upload
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
            $file_type = $_FILES['image']['type'];
            
            if (in_array($file_type, $allowed_types)) {
                // Delete old image if exists
                if ($image && file_exists('uploads/' . $image)) {
                    unlink('uploads/' . $image);
                }
                
                $upload_dir = 'uploads/';
                if (!file_exists($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }
                
                $file_extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                $image = uniqid() . '.' . $file_extension;
                $upload_path = $upload_dir . $image;
                
                if (!move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
                    $error = 'Error uploading image';
                    $image = $post['image']; // Keep old image
                }
            } else {
                $error = 'Only JPG, PNG and GIF images are allowed';
            }
        } elseif ($remove_image) {
            // Remove existing image
            if ($image && file_exists('uploads/' . $image)) {
                unlink('uploads/' . $image);
            }
            $image = null;
        }
        
        if (empty($error)) {
            $stmt = $pdo->prepare("UPDATE posts SET title = ?, content = ?, image = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?");
            
            if ($stmt->execute([$title, $content, $image, $post_id])) {
                $success = 'Post updated successfully!';
                // Refresh post data
                $stmt = $pdo->prepare("SELECT * FROM posts WHERE id = ?");
                $stmt->execute([$post_id]);
                $post = $stmt->fetch(PDO::FETCH_ASSOC);
            } else {
                $error = 'Error updating post';
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
    <title>Edit Post - BlogSite</title>
    <style>
    /* Global reset and base styles */
* {
  box-sizing: border-box;
  margin: 0;
  padding: 0;
}

body {
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  background-color: #e8d1d1ff;
  color: #161717ff;
  min-height: 100vh;
  line-height: 1.6;
  padding: 32px 20px;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
}

/* Navigation bar */
.navbar {
  position: sticky;
  top: 0;
  width: 100%;
  background-color: #e8d1d1ff;
  box-shadow: 0 2px 16px rgba(22,23,23,0.10);
  backdrop-filter: blur(7px);
  z-index: 1000;
}

.nav-container {
  max-width: 1200px;
  height: 64px;
  margin: 0 auto;
  padding: 0 24px;
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.nav-brand a {
  font-size: 2rem;
  font-weight: 700;
  color: #161717ff;
  text-decoration: none;
  letter-spacing: 2px;
  user-select: none;
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
  font-weight: 600;
  color: #161717ff;
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

/* Main content container */
.main-container {
  max-width: 700px;
  margin: 48px auto;
  background-color: #fff;
  border-radius: 22px;
  box-shadow: 0 16px 48px rgba(22,23,23,0.12);
  padding: 48px 50px;
}

/* Headings */
h1, h2 {
  text-align: center;
  font-weight: 700;
  color: #161717ff;
  margin-bottom: 36px;
}

/* Form styles */
form {
  display: flex;
  flex-direction: column;
  gap: 20px;
}

label {
  font-weight: 600;
  font-size: 1.1rem;
  color: #161717cc;
  margin-bottom: 6px;
}

input[type="text"],
input[type="email"],
input[type="password"],
textarea,
input[type="file"] {
  padding: 16px 20px;
  border: 2px solid #161717ff;
  border-radius: 14px;
  background-color: #fbeaea;
  color: #161717ff;
  font-family: inherit;
  font-size: 1rem;
  transition: box-shadow 0.3s ease, border-color 0.3s ease;
  outline: none;
  resize: vertical;
}

input[type="text"]:focus,
input[type="email"]:focus,
input[type="password"]:focus,
textarea:focus,
input[type="file"]:focus {
  border-color: #a67a7a;
  box-shadow: 0 0 8px #a67a7a;
}

/* Textarea min height */
textarea {
  min-height: 200px;
  width: 600px;  
}

/* Buttons */
button[type="submit"] {
  background-color: #161717ff;
  color: #e8d1d1ff;
  border: none;
  border-radius: 20px;
  padding: 20px 0;
  font-size: 1rem;
  font-weight: 300;
  cursor: pointer;
  box-shadow: 0 10px 32px rgba(22,23,23,0.3);
  user-select: none;
  transition: background-color 0.3s ease, transform 0.2s ease;
  width: 100px;
}

button[type="submit"]:hover {
  background-color: #a67a7a;
  color: #161717ff;
  transform: translateY(-3px);
}

/* Feedback messages */
.message-success,
.message-error {
  border-radius: 14px;
  padding: 14px 18px;
  margin-bottom: 24px;
  font-weight: 600;
  text-align: center;
}

.message-success {
  background-color: #e2fae2;
  color: #126216;
}

.message-error {
  background-color: #f9c8c8;
  color: #a02121;
}

/* Responsive */
@media (max-width: 900px) {
  .main-container {
    margin: 38px 25px;
    padding: 36px 28px;
  }
  .nav-container {
    padding: 0 20px;
    flex-wrap: wrap;
    height: auto;
  }
  .nav-menu {
    width: 100%;
    justify-content: center;
    gap: 18px;
    order: 3;
  }
}

@media (max-width: 480px) {
  h1, h2 {
    font-size: 1.6rem;
  }
  button[type="submit"] {
    font-size: 1rem;
    padding: 14px 0;
  }
  .nav-link {
    padding: 8px 12px;
    font-size: 15px;
  }
  .main-container {
    margin: 25px 16px;
    padding: 28px 20px;
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
        <div class="form-container">
            <h1>Edit Post</h1>
            
            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>
            
            <form method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="title">Post Title</label>
                    <input type="text" id="title" name="title" required 
                           value="<?php echo htmlspecialchars($post['title']); ?>">
                </div>
                
                <div class="form-group">
                    <label for="content">Content</label>
                    <textarea id="content" name="content" required><?php echo htmlspecialchars($post['content']); ?></textarea>
                </div>
                
                <?php if ($post['image']): ?>
                    <div class="form-group">
                        <label>Current Image</label>
                        <img src="uploads/<?php echo htmlspecialchars($post['image']); ?>" alt="Current Image" 
                             style="max-width: 200px; border-radius: 10px;">
                        <label>
                            <input type="checkbox" name="remove_image"><br> Remove current image
                        </label>
                    </div>
                <?php endif; ?>
                
                <div class="form-group">
                    <label for="image">Update Image (Optional)</label>
                    <div class="file-upload">
                        <input type="file" id="image" name="image" accept="image/*">
                        <label for="image" class="file-upload-label">
                        
                        </label>
                    </div>
                </div>
                
                <div class="form-group">
                    <button type="submit" class="btn btn-primary">Update Post</button>
                    <a href="profile.php" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>

<?php