<?php
require_once 'config.php';
requireLogin();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit();
}

$user_id = isset($_POST['user_id']) ? (int)$_POST['user_id'] : 0;
$action = isset($_POST['action']) ? $_POST['action'] : '';

// Validate inputs
if (!$user_id || !in_array($action, ['follow', 'unfollow'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid parameters']);
    exit();
}

// Can't follow yourself
if ($user_id === $_SESSION['user_id']) {
    echo json_encode(['success' => false, 'message' => 'You cannot follow yourself']);
    exit();
}

// Check if user exists
$stmt = $pdo->prepare("SELECT id FROM users WHERE id = ?");
$stmt->execute([$user_id]);
if (!$stmt->fetch()) {
    echo json_encode(['success' => false, 'message' => 'User not found']);
    exit();
}

try {
    if ($action === 'follow') {
        // Check if already following
        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM follows WHERE follower_id = ? AND following_id = ?");
        $stmt->execute([$_SESSION['user_id'], $user_id]);
        
        if ($stmt->fetch()['count'] > 0) {
            echo json_encode(['success' => false, 'message' => 'Already following this user']);
            exit();
        }
        
        // Insert follow relationship
        $stmt = $pdo->prepare("INSERT INTO follows (follower_id, following_id, created_at) VALUES (?, ?, NOW())");
        $stmt->execute([$_SESSION['user_id'], $user_id]);
        
    } else { // unfollow
        // Delete follow relationship
        $stmt = $pdo->prepare("DELETE FROM follows WHERE follower_id = ? AND following_id = ?");
        $stmt->execute([$_SESSION['user_id'], $user_id]);
    }
    
    // Get updated followers count
    $stmt = $pdo->prepare("SELECT COUNT(*) as followers FROM follows WHERE following_id = ?");
    $stmt->execute([$user_id]);
    $followers_count = $stmt->fetch()['followers'];
    
    echo json_encode([
        'success' => true,
        'followers_count' => $followers_count,
        'action' => $action
    ]);
    
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error occurred']);
}
?>
