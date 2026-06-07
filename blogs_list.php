<?php
require_once 'db.php';

// Get all blog posts for public display
$posts = [];
$result = $conn->query("SELECT id, title, content, created_at FROM blogs ORDER BY created_at DESC");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $posts[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blogs - BIT2BYTE</title>
    <meta name="description" content="Read the latest blogs from Bit2Byte, the Software Research and Development Community of KUET.">
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
    <button class="login-btn" onclick="window.location.href='login.html'">Login</button>
  </nav>

        <main class="content-wrap">
            <section class="blog-section" aria-label="Our blogs">
                <div class="section-header blog-header">
                    <h2>Our <span style="color: #3ea4b6 ;">Blogs</span></h2>
                </div>

                <div class="blog-container">
                    <?php if (empty($posts)): ?>
                    <p style="text-align: center; color: #666; grid-column: 1 / -1; padding: 40px;">No blog posts have been published yet.</p>
                    <?php else: ?>
                        <?php foreach ($posts as $post): ?>
                        <article class="blog-card">
                            <h3><?php echo htmlspecialchars($post['title']); ?></h3>
                            <div class="blog-meta">
                                Posted on <?php echo date('F j, Y', strtotime($post['created_at'])); ?>
                            </div>
                            <div class="blog-excerpt">
                                <?php echo nl2br(htmlspecialchars(substr($post['content'], 0, 600))); ?><?php if (strlen($post['content']) > 600) echo '...'; ?>
                            </div>
                            <a class="read-more" href="blogs_view.php?id=<?php echo $post['id']; ?>">Read more</a>
                        </article>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </section>
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
                        <li><a href="event.php">Events</a></li>
                         <li><a href="blogs_list.php">Blogs</a></li>
                    </ul>
                </div>
                 <div class="footer-col">
        <h4>Social</h4>
        <ul>
          <li><a href="mailto:bit2bytekuet@gmail.com" target="_blank">Email</a></li>
          <li><a href="https://www.facebook.com/bittwobyte" target="_blank">Facebook</a></li>
          <li><a href="https://www.linkedin.com/company/bit2byte-kuet" target="_blank">LinkedIn</a></li>
        </ul>
      </div>
      <div class="footer-col">
        <h4>Support</h4>
        <ul>
          <li><a href="faq.php">FAQ</a></li>
          <li><a href="terms.php">Terms</a></li>
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