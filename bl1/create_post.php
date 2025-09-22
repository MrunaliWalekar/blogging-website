<?php
require_once 'config.php';
requireLogin();

$error = '';
$success = '';

if ($_POST) {
    $title = sanitizeInput($_POST['title']);
    $content = sanitizeInput($_POST['content']);
    $image = null;
    
    if (empty($title) || empty($content)) {
        $error = 'Title and content are required';
    } else {
        // Handle image upload
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
            $file_type = $_FILES['image']['type'];
            
            if (in_array($file_type, $allowed_types)) {
                $upload_dir = 'uploads/';
                if (!file_exists($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }
                
                $file_extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                $image = uniqid() . '.' . $file_extension;
                $upload_path = $upload_dir . $image;
                
                if (!move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
                    $error = 'Error uploading image';
                    $image = null;
                }
            } else {
                $error = 'Only JPG, PNG and GIF images are allowed';
            }
        }
        
        if (empty($error)) {
            $stmt = $pdo->prepare("INSERT INTO posts (user_id, title, content, image) VALUES (?, ?, ?, ?)");
            
            if ($stmt->execute([$_SESSION['user_id'], $title, $content, $image])) {
                $success = 'Post created successfully!';
                $_POST = []; // Clear form
            } else {
                $error = 'Error creating post';
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
    <title>Create Post - BlogSite</title>
    <style>
/* Navigation Bar Styles */
.navbar {
    width: 100%;
    position: sticky;
    top: 0;
    background: rgba(28, 26, 26, 0.95);
    backdrop-filter: blur(10px);
    box-shadow: 0 2px 20px rgba(0, 0, 0, 0.1);
    z-index: 1000;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

.nav-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 20px;
    height: 62px;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

/* Brand / Logo */
.nav-brand a {
    font-size: 1.8rem;
    font-weight: bold;
    text-decoration: none;
    background: linear-gradient(135deg, #e8d1d1ff, #e8d1d1ff);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    cursor: pointer;
}

/* Navigation Links */
.nav-menu {
    display: flex;
    gap: 28px;
}

.nav-link {
    color: #666;
    font-weight: 500;
    padding: 8px 16px;
    border-radius: 20px;
    text-decoration: none;
    transition: background-color 0.3s ease, color 0.3s ease;
    cursor: pointer;
}

.nav-link:hover,
.nav-link.active {
    background: rgba(226, 228, 236, 0.1);
    color: #e7c8ddff;
}


        /* Container styling */
.create-post-container {
    max-width: 600px;
    margin: 40px auto;
    padding: 30px 25px;
    background-color: #e8d1d1ff; /* Soft pink background */
    border-radius: 20px;
    box-shadow: 0 15px 35px rgba(22, 23, 23, 0.22); /* Dark shadows */
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    color: #161717ff; /* Very dark text */
}

/* Form styles */
.create-post-form {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

/* Labels */
.create-post-form label {
    font-weight: 600;
    font-size: 1.1rem;
    color: #161717ff;
    margin-bottom: 6px;
}

/* Inputs and textarea */
.create-post-form input[type="text"],
.create-post-form textarea,
.create-post-form input[type="file"] {
    padding: 14px 16px;
    border: 2px solid #161717ff;
    border-radius: 14px;
    font-size: 1rem;
    background-color: #fff0f0;
    color: #161717ff;
    transition: border-color 0.3s ease, box-shadow 0.3s ease;
    resize: vertical;
    font-family: inherit;
}

.create-post-form input[type="text"]:focus,
.create-post-form textarea:focus,
.create-post-form input[type="file"]:focus {
    outline: none;
    border-color: #a06767f0;
    box-shadow: 0 0 8px #a06767f0;
}

/* Textarea height */
.create-post-form textarea {
    min-height: 140px;
}

/* Submit button */
.create-post-form button[type="submit"] {
    padding: 16px 20px;
    background-color: #161717ff;
    color: #e8d1d1ff;
    font-weight: 700;
    font-size: 1.2rem;
    border: none;
    border-radius: 16px;
    cursor: pointer;
    transition: background-color 0.3s ease, color 0.3s ease, transform 0.2s ease;
    user-select: none;
}

.create-post-form button[type="submit"]:hover {
    background-color: #a06767ff;
    color: #161717ff;
    transform: translateY(-3px);
}

/* Success and error messages */
.message-success {
    background-color: #a0cfa0dd;
    color: #115511;
    padding: 12px 18px;
    border-radius: 12px;
    font-weight: 600;
    margin-bottom: 20px;
}

.message-error {
    background-color: #cf9b9bdd;
    color: #871515;
    padding: 12px 18px;
    border-radius: 12px;
    font-weight: 600;
    margin-bottom: 20px;
}

/* Responsive */
@media (max-width: 480px) {
    .create-post-container {
        padding: 20px 15px;
    }

    .create-post-form button[type="submit"] {
        font-size: 1rem;
        padding: 14px 16px;
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
<div class="create-post-container">
  <form class="create-post-form" method="POST" enctype="multipart/form-data">
    <label for="title">Post Title</label>
    <input type="text" id="title" name="title" required />

    <label for="content">Content</label>
    <textarea id="content" name="content" required></textarea>

    <label for="image">Upload Image</label>
    <input type="file" id="image" name="image" accept="image/*" />

    <form id="createPostForm" method="POST" enctype="multipart/form-data">
  <!-- Your input fields here... -->

  <button type="submit">Create Post</button>
</form>
  </form>
</div>

    
            
            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>
                    </div>
            </form>
        </div>
    </div>

      
 


    <script>
        // File upload preview
        document.getElementById('image').addEventListener('change', function(e) {
            const file = e.target.files[0];
            const label = document.querySelector('.file-upload-label');
            
            if (file) {
                label.textContent = file.name;
            } else {
                label.textContent = 'Choose Image or Drag & Drop';
            }
        });

         document.getElementById('createPostForm').addEventListener('submit', function(event) {
    alert('Post created successfully!');
    // Optional: Remove the alert if you want it after server confirmation instead
  });
    </script>
</body>
</html>