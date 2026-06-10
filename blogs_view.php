<?php
require_once 'db.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id <= 0) {
    http_response_code(404);
    echo 'Post not found.';
    exit;
}

$stmt = $conn->prepare('SELECT id, title, content, created_at FROM blogs WHERE id = ? LIMIT 1');
$stmt->bind_param('i', $id);
$stmt->execute();
$res = $stmt->get_result();
if ($res->num_rows === 0) {
    http_response_code(404);
    echo 'Post not found.';
    exit;
}
$post = $res->fetch_assoc();
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($post['title']); ?> - BIT2BYTE</title>
    <meta name="description" content="<?php echo htmlspecialchars(substr($post['content'], 0, 150)); ?>">
    <link rel="stylesheet" href="blogs_public.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>.read-more{background:#0a3d62;color:white;border:none;padding:10px 20px;border-radius:6px;cursor:pointer;font-weight:bold;text-decoration:none;display:inline-block}.read-more:hover{background:#0d4f7e}</style>
</head>
<body id="top">
    <div class="page-shell">
        <nav class="navbar">
    <span class="brand">BIT2BYTE</span>
    <ul class="nav-center">
      <li><a href="home.html">Home</a></li>
      <li><a href="about.php">About</a></li>
      <li><a href="event.php">Event</a></li>
      <li><a href="blogs_list.php" class="active" aria-current="page">Blog</a></li>
      <li><a href="faq.php">FAQ</a></li>
    </ul>
    <button class="login-btn" onclick="window.location.href='login.php'">Login</button>
  </nav>

        <main class="content-wrap">
            <article class="blog-post">
                <div class="blog-post-header">
                    <h1><?php echo htmlspecialchars($post['title']); ?></h1>
                    <div class="blog-post-meta">Posted on <?php echo date('F j, Y', strtotime($post['created_at'])); ?></div>
                </div>
                <div class="blog-post-content">
                    <?php echo nl2br(htmlspecialchars($post['content'])); ?>
                </div>
                <div style="margin-top: 24px;">
                    <a class="read-more" href="blogs_list.php">&larr; Back to all posts</a>
                </div>
            </article>
        </main>

        <footer>
            <div class="footer-top">
                <div class="footer-logo">
                    <img src="https://www.bit2bytekuet.com/_next/image?url=%2F_next%2Fstatic%2Fmedia%2Fmain-logo.c7a29f9e.png&w=128&q=75" alt="Bit2Byte Logo" />
                </div>
                <div class="footer-col">
                    <h4>About Us</h4>
                    <ul>
                        <li><a href="about.php">About</a></li>
                        <li><a href="#">Syllabus</a></li>
                        <li><a href="#">Events</a></li>
                    </ul>
                </div>
                <div class="footer-col">
                    <h4>Support</h4>
                    <ul>
                        <li><a href="#">Contact</a></li>
                        <li><a href="#">FAQs</a></li>
                        <li><a href="#">Terms</a></li>
                    </ul>
                </div>
                <div class="footer-col">
                    <h4>Social</h4>
                    <ul>
                        <li><a href="#">Email</a></li>
                        <li><a href="#">Facebook</a></li>
                        <li><a href="#">LinkedIn</a></li>
                    </ul>
                </div>
            </div>
            <div class="footer-bottom">
                <span>© 2025 Bit2Byte</span>
                <span><a href="terms.php">Terms of Service</a></span>
                <a href="#top" class="back-to-top">Back to top ↑</a>
            </div>
        </footer>
    </div>
</body>
</html>
<?php $conn->close(); ?>