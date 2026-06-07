<?php
session_start();
include 'db.php';

$isAdmin = isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;

// AJAX handlers
if ($isAdmin && $_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    $action = $_POST['action'] ?? '';

    if ($action === 'create') {
        $title = trim($_POST['title'] ?? '');
        $content = trim($_POST['content'] ?? '');
        if ($title === '' || $content === '') {
            echo json_encode(['success' => false, 'message' => 'Title and content are required.']);
            exit;
        }
        $stmt = $conn->prepare('INSERT INTO blogs (title, content) VALUES (?, ?)');
        $stmt->bind_param('ss', $title, $content);
        if ($stmt->execute()) echo json_encode(['success' => true, 'message' => 'Post created.']); else echo json_encode(['success' => false, 'message' => $stmt->error]);
        $stmt->close();
        exit;
    }

    if ($action === 'update') {
        $id = intval($_POST['id'] ?? 0);
        $title = trim($_POST['title'] ?? '');
        $content = trim($_POST['content'] ?? '');
        if ($id <= 0 || $title === '' || $content === '') {
            echo json_encode(['success' => false, 'message' => 'Invalid data.']);
            exit;
        }
        $stmt = $conn->prepare('UPDATE blogs SET title = ?, content = ? WHERE id = ?');
        $stmt->bind_param('ssi', $title, $content, $id);
        if ($stmt->execute()) echo json_encode(['success' => true, 'message' => 'Post updated.']); else echo json_encode(['success' => false, 'message' => $stmt->error]);
        $stmt->close();
        exit;
    }

    if ($action === 'delete') {
        $id = intval($_POST['id'] ?? 0);
        if ($id <= 0) { echo json_encode(['success' => false, 'message' => 'Invalid id.']); exit; }
        $stmt = $conn->prepare('DELETE FROM blogs WHERE id = ?');
        $stmt->bind_param('i', $id);
        if ($stmt->execute()) echo json_encode(['success' => true, 'message' => 'Post deleted.']); else echo json_encode(['success' => false, 'message' => $stmt->error]);
        $stmt->close();
        exit;
    }

    echo json_encode(['success' => false, 'message' => 'Unknown action']);
    exit;
}

// Load posts
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
    <link rel="stylesheet" href="blog.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>.page-title{color:#f8fafc}.read-more-btn{display:inline-block;padding:6px 12px;background:var(--primary-color);color:#fff;border-radius:4px;text-decoration:none;font-size:13px}</style>
</head>
<body>
    <div class="admin-container">
        <nav class="sidebar">
            <div class="sidebar-header">
                <span class="brand">BIT2BYTE</span>
                <span class="admin-badge">Admin</span>
            </div>
            <ul class="nav-menu">
                <li class="nav-item"><a href="admin.php"><i class="fas fa-tachometer-alt"></i><span>Dashboard</span></a></li>
                <li class="nav-item"><a href="members.php"><i class="fas fa-users"></i><span>Club Members</span></a></li>
                <li class="nav-item"><a href="events.php"><i class="fas fa-calendar-alt"></i><span>Events</span></a></li>
                <li class="nav-item active"><a href="blog_list.php"><i class="fas fa-newspaper"></i><span>Blogs</span></a></li>
            </ul>
            <div class="sidebar-footer">
                <?php if ($isAdmin): ?>
                    <a href="logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i><span>Logout</span></a>
                <?php else: ?>
                    <a href="login.html" class="logout-btn"><i class="fas fa-sign-in-alt"></i><span>Admin Login</span></a>
                <?php endif; ?>
            </div>
        </nav>

        <main class="main-content">
            <header class="top-header">
                <h1 class="page-title">Blog Posts</h1>
                <?php if ($isAdmin): ?>
                    <div class="header-actions"><button class="add-member-btn" onclick="openModal()"><i class="fas fa-plus"></i> Add Post</button></div>
                <?php endif; ?>
            </header>

            <div class="content-area">
                <div class="stats-row"><div class="stat-box"><h4>Total Posts</h4><div class="number"><?php echo count($posts);?></div></div></div>
                <section class="blog-list container">
                    <?php if (empty($posts)): ?>
                        <div class="no-posts">
                            <h3>No posts yet</h3>
                            <p>Check back later for updates.</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($posts as $p): ?>
                            <article class="post-card" data-id="<?php echo $p['id']; ?>" data-title="<?php echo htmlspecialchars($p['title'], ENT_QUOTES); ?>" data-content="<?php echo htmlspecialchars($p['content'], ENT_QUOTES); ?>">
                                <h2 class="post-title"><a href="blog_view.php?id=<?php echo $p['id']; ?>"><?php echo htmlspecialchars($p['title']); ?></a></h2>
                                <div class="post-meta">Posted on <?php echo date('F j, Y', strtotime($p['created_at'])); ?></div>
                                <div class="post-excerpt"><?php echo nl2br(htmlspecialchars(substr($p['content'], 0, 600))); ?><?php if (strlen($p['content'])>600) echo '...'; ?></div>
                                <?php if ($isAdmin): ?>
                                    <div class="member-actions" style="justify-content:space-between">
                                        <a class="read-more-btn" href="blog_view.php?id=<?php echo $p['id']; ?>"><i class="fas fa-eye"></i> Preview</a>
                                        <div>
                                            <button class="btn-edit"><i class="fas fa-edit"></i> Edit</button>
                                            <button class="btn-delete"><i class="fas fa-trash"></i> Delete</button>
                                        </div>
                                    </div>
                                <?php else: ?>
                                    <a class="read-more" href="blog_view.php?id=<?php echo $p['id']; ?>">Read more</a>
                                <?php endif; ?>
                            </article>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </section>
            </div>
        </main>
    </div>

    <?php if ($isAdmin): ?>
    <div class="modal-overlay" id="postModal">
        <div class="modal-content">
            <div class="modal-header"><h2 id="modalTitle">Add Post</h2><button class="modal-close" onclick="closeModal()">&times;</button></div>
            <div id="formMessage" class="message"></div>
            <form id="postForm">
                <input type="hidden" id="postId" name="id" value="">
                <div class="form-group"><label>Title *</label><input type="text" id="postTitle" name="title" required></div>
                <div class="form-group"><label>Content *</label><textarea id="postContent" name="content" rows="8" required></textarea></div>
                <button type="submit" class="btn-submit" id="submitBtn"><i class="fas fa-plus"></i> Create</button>
            </form>
        </div>
    </div>

<script>
        let isEdit=false;
        function openModal(){isEdit=false;document.getElementById('modalTitle').textContent='Add Post';document.getElementById('submitBtn').innerHTML='<i class="fas fa-plus"></i> Create';document.getElementById('postForm').reset();document.getElementById('postId').value='';document.getElementById('formMessage').style.display='none';document.getElementById('postModal').classList.add('active');}
        function closeModal(){document.getElementById('postModal').classList.remove('active');document.getElementById('postForm').reset();document.getElementById('formMessage').style.display='none';isEdit=false}
        document.getElementById('postModal').addEventListener('click',e=>{if(e.target===document.getElementById('postModal'))closeModal();});
        function editPost(id,title,content){isEdit=true;document.getElementById('modalTitle').textContent='Edit Post';document.getElementById('submitBtn').innerHTML='<i class="fas fa-save"></i> Save';document.getElementById('postId').value=id;document.getElementById('postTitle').value=title;document.getElementById('postContent').value=content;document.getElementById('formMessage').style.display='none';document.getElementById('postModal').classList.add('active');}
        // Auto-open modal if ?action=add
        <?php if ($isAdmin && isset($_GET['action']) && $_GET['action'] === 'add'): ?>
        window.addEventListener('DOMContentLoaded', function() { openModal(); });
        <?php endif; ?>
        // Event delegation for edit/delete buttons
        document.addEventListener('click', function(e) {
            if (e.target.closest('.btn-edit')) {
                const card = e.target.closest('.post-card');
                editPost(card.dataset.id, card.dataset.title, card.dataset.content);
            }
            if (e.target.closest('.btn-delete')) {
                const card = e.target.closest('.post-card');
                deletePost(card.dataset.id);
            }
        });
        document.getElementById('postForm').addEventListener('submit',function(e){e.preventDefault();const fd=new FormData(this);fd.append('action',isEdit?'update':'create');const msg=document.getElementById('formMessage');msg.style.display='block';msg.className='message';msg.textContent=isEdit?'Saving...':'Creating...';fetch('blog_list.php',{method:'POST',body:fd}).then(r=>r.json()).then(data=>{msg.className='message '+(data.success?'success':'error');msg.textContent=data.message;if(data.success) setTimeout(()=>location.reload(),700);}).catch(err=>{msg.className='message error';msg.textContent='Error: '+err});});
        function deletePost(id){if(!confirm('Delete this post?'))return;const fd=new FormData();fd.append('action','delete');fd.append('id',id);fetch('blog_list.php',{method:'POST',body:fd}).then(r=>r.json()).then(d=>{if(d.success) location.reload(); else alert(d.message);});}
    </script>
    <?php endif; ?>
</body>
</html>