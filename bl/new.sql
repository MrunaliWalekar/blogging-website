-- ===============================================
-- SQL Setup for BlogSite Admin Login System
-- ===============================================

-- Use your existing database
USE exp;

-- Create users table (if not exists)
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    profile_image VARCHAR(255) DEFAULT 'default-avatar.png',
    bio TEXT,
    is_admin TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Create posts table (if not exists)
CREATE TABLE IF NOT EXISTS posts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    image VARCHAR(255) NULL,
    likes_count INT DEFAULT 0,
    comments_count INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Create comments table (if not exists)
CREATE TABLE IF NOT EXISTS comments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    post_id INT NOT NULL,
    user_id INT NOT NULL,
    comment_text TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Create likes table (if not exists)
CREATE TABLE IF NOT EXISTS likes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    post_id INT NOT NULL,
    user_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_like (post_id, user_id),
    FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- ===============================================
-- Insert Admin User
-- ===============================================

-- Delete existing admin user if exists (to avoid duplicates)
DELETE FROM users WHERE username = 'admin' OR email = 'admin@blogsite.com';

-- Insert new admin user
-- Password: admin123 (hashed using PHP password_hash function)
INSERT INTO users (username, email, password, full_name, is_admin) VALUES 
('admin', 'admin@blogsite.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Admin User', 1);

-- ===============================================
-- Insert Sample Regular Users (Optional)
-- ===============================================

-- Sample users for testing (password: password123)
INSERT IGNORE INTO users (username, email, password, full_name, is_admin) VALUES 
('john_doe', 'john@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'John Doe', 0),
('jane_smith', 'jane@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Jane Smith', 0),
('mike_wilson', 'mike@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mike Wilson', 0);

-- ===============================================
-- Insert Sample Posts (Optional)
-- ===============================================

-- Get user IDs for sample posts
SET @john_id = (SELECT id FROM users WHERE username = 'john_doe');
SET @jane_id = (SELECT id FROM users WHERE username = 'jane_smith');
SET @mike_id = (SELECT id FROM users WHERE username = 'mike_wilson');

-- Insert sample posts
INSERT INTO posts (user_id, title, content, likes_count, comments_count) VALUES 
(@john_id, 'Getting Started with Web Development', 'Web development is an exciting field that combines creativity with technical skills. In this post, I will share my journey learning HTML, CSS, and JavaScript...', 5, 2),
(@jane_id, 'The Art of Photography', 'Photography is more than just capturing moments - it is about telling stories through images. Here are some tips I have learned over the years...', 8, 4),
(@mike_id, 'Healthy Cooking Tips', 'Eating healthy does not mean sacrificing taste. Here are some simple recipes and tips for maintaining a balanced diet while enjoying delicious meals...', 12, 7),
(@john_id, 'Learning PHP and MySQL', 'Database-driven web applications are the backbone of modern websites. Let me share what I have learned about PHP and MySQL integration...', 3, 1),
(@jane_id, 'Travel Photography Guide', 'Traveling opens our eyes to new cultures and experiences. Here is how to capture those memories with stunning photography...', 15, 9);

-- ===============================================
-- Insert Sample Comments (Optional)
-- ===============================================

-- Get post IDs for sample comments
SET @post1_id = (SELECT id FROM posts WHERE title = 'Getting Started with Web Development');
SET @post2_id = (SELECT id FROM posts WHERE title = 'The Art of Photography');
SET @post3_id = (SELECT id FROM posts WHERE title = 'Healthy Cooking Tips');

-- Insert sample comments
INSERT INTO comments (post_id, user_id, comment_text) VALUES 
(@post1_id, @jane_id, 'Great post! I am also learning web development and this was very helpful.'),
(@post1_id, @mike_id, 'Thanks for sharing your experience. Do you have any book recommendations?'),
(@post2_id, @john_id, 'Amazing photography tips! I will definitely try these techniques.'),
(@post2_id, @mike_id, 'Your photos are always inspiring. Keep up the great work!'),
(@post3_id, @john_id, 'These recipes look delicious and healthy. Will try them this weekend!'),
(@post3_id, @jane_id, 'Love the healthy cooking approach. More posts like this please!');

-- ===============================================
-- Insert Sample Likes (Optional)
-- ===============================================

-- Insert sample likes
INSERT INTO likes (post_id, user_id) VALUES 
(@post1_id, @jane_id),
(@post1_id, @mike_id),
(@post2_id, @john_id),
(@post2_id, @mike_id),
(@post3_id, @john_id),
(@post3_id, @jane_id);

-- ===============================================
-- Verify Installation
-- ===============================================

-- Check if admin user was created successfully
SELECT 'Admin user created successfully!' as message, 
       id, username, email, full_name, is_admin, created_at
FROM users 
WHERE is_admin = 1;

-- Show all users count
SELECT 'Total users created:' as message, COUNT(*) as count FROM users;

-- Show all posts count
SELECT 'Total posts created:' as message, COUNT(*) as count FROM posts;

-- Show all comments count
SELECT 'Total comments created:' as message, COUNT(*) as count FROM comments;

-- Show all likes count
SELECT 'Total likes created:' as message, COUNT(*) as count FROM likes;