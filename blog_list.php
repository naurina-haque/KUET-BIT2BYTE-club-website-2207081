<?php
include 'db.php';

$posts = [];
$res = $conn->query("SELECT id, title, content, created_at FROM blogs ORDER BY created_at DESC");
if ($res) {
    while ($row = $res->fetch_assoc()) $posts[] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Blogs - BIT2BYTE</title>
    <link rel="stylesheet" href="admin.css">
    <link rel="stylesheet" href="blog_public.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>.page-title{color:#f8fafc}</style>
</head>
<body>
    <div class="admin-container">
        <nav class="sidebar">
            <div class="sidebar-header">
                <span class="brand">BIT2BYTE</span>
                <span class="admin-badge">Site</span>
            </div>
            <ul class="nav-menu">
                <li class="nav-item">
                    <a href="login.html">
                        <i class="fas fa-sign-in-alt"></i>
                        <span>Admin Login</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="members.php">
                        <i class="fas fa-users"></i>
                        <span>Members</span>
                    </a>
                </li>
                <li class="nav-item active">
                    <a href="blog_list.php">
                        <i class="fas fa-newspaper"></i>
                        <span>Blogs</span>
                    </a>
                </li>
            </ul>
            <div class="sidebar-footer">
                <a href="login.html" class="logout-btn">
                    <i class="fas fa-sign-in-alt"></i>
                    <span>Admin Login</span>
                </a>
            </div>
        </nav>

        <main class="main-content">
            <header class="top-header">
                <h1 class="page-title">Blog Posts</h1>
            </header>

            <div class="content-area">
                <section class="blog-list container">
                    <?php if (empty($posts)): ?>
                        <div class="no-posts">
                            <h3>No posts yet</h3>
                            <p>Check back later for updates.</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($posts as $p): ?>
                            <article class="post-card">
                                <h2 class="post-title"><a href="blog_view.php?id=<?php echo $p['id']; ?>"><?php echo htmlspecialchars($p['title']); ?></a></h2>
                                <div class="post-meta">Posted on <?php echo date('F j, Y', strtotime($p['created_at'])); ?></div>
                                <div class="post-excerpt"><?php echo nl2br(htmlspecialchars(substr($p['content'], 0, 600))); ?><?php if (strlen($p['content'])>600) echo '...'; ?></div>
                                <a class="read-more" href="blog_view.php?id=<?php echo $p['id']; ?>">Read more</a>
                            </article>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </section>
            </div>
        </main>
    </div>
</body>
</html>
