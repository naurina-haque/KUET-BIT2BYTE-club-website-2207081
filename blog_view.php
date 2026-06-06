<?php
include 'db.php';

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
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title><?php echo htmlspecialchars($post['title']); ?> - BIT2BYTE</title>
    <link rel="stylesheet" href="admin.css">
    <link rel="stylesheet" href="blog_public.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>.page-title{color:#f8fafc}.post-content a{color:#6366f1}</style>
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
                <h1 class="page-title"><?php echo htmlspecialchars($post['title']); ?></h1>
                <div class="header-actions">
                    <div class="admin-profile">
                        <span class="admin-name"><?php echo date('F j, Y', strtotime($post['created_at'])); ?></span>
                    </div>
                </div>
            </header>

            <div class="content-area">
                <div class="container post-view">
                    <article class="post-full">
                        <div class="post-content">
                            <?php echo nl2br(htmlspecialchars($post['content'])); ?>
                        </div>
                    </article>
                    <p><a href="blog_list.php">&larr; Back to posts</a></p>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
