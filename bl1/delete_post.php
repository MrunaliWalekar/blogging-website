<?php
require_once 'config.php';
requireLogin();

if (!isset($_GET['id'])) {
    header('Location: profile.php');
    exit;
}

$post_id = (int)$_GET['id'];

$stmt = $pdo->prepare("SELECT * FROM posts WHERE id = ? AND user_id = ?");
$stmt->execute([$post_id, $_SESSION['user_id']]);
$post = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$post) {
    header('Location: profile.php');
    exit;
}


$stmt = $pdo->prepare("DELETE FROM posts WHERE id = ?");
if ($stmt->execute([$post_id])) {
    if ($post['image'] && file_exists('uploads/' . $post['image'])) {
        unlink('uploads/' . $post['image']);
    }
}

header('Location: profile.php');
exit;


