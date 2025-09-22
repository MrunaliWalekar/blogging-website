<?php
require_once 'config.php';
requireLogin();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>About Us - BlogSite</title>
  <style> /* Global Reset */
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

/* Body and Font */
body {
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  background: #f9f7f7;
  color: #161717;
  line-height: 1.6;
  min-height: 100vh;
  padding: 30px;
}

/* Container */
.container {
  max-width: 900px;
  margin: auto;
  background: white;
  padding: 40px;
  border-radius: 20px;
  box-shadow: 0 15px 40px rgba(22, 23, 23, 0.15);
}

/* Heading */
h1, h2 {
  color: #a67777;
  margin-bottom: 1rem;
  font-weight: 700;
  text-align: center;
}

/* Paragraph */
p {
  font-size: 1.1rem;
  color: #3b3b3b;
  margin-bottom: 1.5rem;
  text-align: justify;
  letter-spacing: 0.3px;
}

/* Link */
a {
  color: #a67777;
  text-decoration: none;
  font-weight: 600;
}

a:hover {
  text-decoration: underline;
}

/* Responsive */
@media (max-width: 600px) {
  .container {
    padding: 25px 15px;
  }
  p {
    font-size: 1rem;
  }
}
</style>
  <link rel="stylesheet" href="styles.css" />
</head>
<body>
  <div class="container">
    <h1>About BlogSite</h1>
    <p>
      Welcome to BlogSite, your go-to platform for insightful articles, personal stories, and creative expression. Founded with the vision to empower voices from all walks of life, BlogSite offers a cozy and inspiring space for writers and readers alike.
    </p>
    <p>
      Whether you're here for expert tips, daily inspiration, or just a good read, we strive to provide content that is engaging, meaningful, and authentic. Our dedicated team curates posts across diverse topics including technology, lifestyle, travel, and more.
    </p>
    <p>
      Join our growing community to share your views, learn something new, or simply explore the world through the power of words. At BlogSite, storytelling meets connection.
    </p>
  </div>
</body>
</html>
