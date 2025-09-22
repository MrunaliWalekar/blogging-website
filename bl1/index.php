<?php
// ===============================================
// index.php (Landing Page)
// ===============================================
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to BlogSite</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="auth-container">
        <div class="auth-form" style="max-width: 500px;">
            <div class="logo">
                <h1>BlogSite</h1>
            </div>
            
            <h2>Share Your Stories with the World</h2>
            <p style="text-align: center; color: #666; margin-bottom: 30px;">
                Join our community of writers and readers. Create, explore, and engage with amazing content.
            </p>
            
            <div style="text-align: center;">
                <a href="login.php" class="btn btn-primary" style="margin: 10px; width: auto; display: inline-block;">Login</a>
                <a href="signup.php" class="btn btn-secondary" style="margin: 10px; width: auto; display: inline-block;">Sign Up</a>
            </div>
            
            <div style="margin-top: 40px; text-align: center;">
                <h3>Why Choose BlogSite?</h3>
                <div style="text-align: left; margin-top: 20px;">
                    <div style="margin-bottom: 15px;">
                        <strong>✨ Easy to Use:</strong> Simple, intuitive interface for creating and managing posts
                    </div>
                    <div style="margin-bottom: 15px;">
                        <strong>🎨 Rich Content:</strong> Add images and format your posts beautifully
                    </div>
                    <div style="margin-bottom: 15px;">
                        <strong>👥 Community:</strong> Connect with other writers and readers
                    </div>
                    <div style="margin-bottom: 15px;">
                        <strong>📱 Responsive:</strong> Works perfectly on all devices
                    </div>
                </div>
            </div>
            
            <p class="auth-link" style="margin-top: 30px;">
                <a href="admin_login.php">Admin Access</a>
            </p>
        </div>
    </div>
</body>
</html>">BlogSite</a>
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
                            <input type="checkbox" name="remove_image"> Remove current image
                        </label>
                    </div>
                <?php endif; ?>
                
                <div class="form-group">
                    <label for="image">Update Image (Optional)</label>
                    <div class="file-upload">
                        <input type="file" id="image" name="image" accept="image/*">
                        <label for="image" class="file-upload-label">
                            Choose New Image
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
