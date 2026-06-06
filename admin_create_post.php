<?php
require_once 'admin_auth.php';
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $content = trim($_POST['content'] ?? '');
    if ($title === '' || $content === '') {
        $message = 'Please provide both title and content.';
    } else {
        $stmt = $conn->prepare("INSERT INTO blogs (title, content) VALUES (?, ?)");
        $stmt->bind_param("ss", $title, $content);
        if ($stmt->execute()) {
            header('Location: admin_create_post.php?success=1');
            exit;
        } else {
            $message = 'Database error: ' . $stmt->error;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Write Blog (Admin)</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .container{max-width:900px;margin:30px auto;padding:0 16px}
        .success{color:green}
        .error{color:red}
        form div{margin:12px 0}
    </style>
</head>
<body>
<div class="container">
    <h2>Write a Blog Post</h2>
    <?php if (isset($_GET['success'])): ?>
        <p class="success">Blog post published successfully.</p>
    <?php endif; ?>
    <?php if ($message): ?>
        <p class="error"><?php echo htmlspecialchars($message); ?></p>
    <?php endif; ?>
    <form method="post" action="admin_create_post.php">
        <div>
            <label for="title">Title</label><br>
            <input type="text" id="title" name="title" required style="width:100%;max-width:600px;">
        </div>
        <div>
            <label for="content">Content</label><br>
            <textarea id="content" name="content" rows="10" required style="width:100%;max-width:800px;"></textarea>
        </div>
        <div>
            <button type="submit">Publish</button>
        </div>
    </form>
    <hr>
    <h3>Recent Posts</h3>
    <?php
    $res = $conn->query("SELECT id, title, content, created_at FROM blogs ORDER BY created_at DESC LIMIT 10");
    if ($res && $res->num_rows > 0) {
        while ($row = $res->fetch_assoc()) {
            echo '<article>';
            echo '<h4>' . htmlspecialchars($row['title']) . '</h4>';
            echo '<div class="meta">Posted: ' . $row['created_at'] . '</div>';
            echo '<p>' . nl2br(htmlspecialchars(substr($row['content'], 0, 400))) . '</p>';
            echo '<hr>';
            echo '</article>';
        }
    } else {
        echo '<p>No posts yet.</p>';
    }
    ?>
</div>
</body>
</html>
