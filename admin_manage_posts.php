<?php
require_once 'admin_auth.php';
requireAdminAuth();

// AJAX handlers
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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
        // delete
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
$res = $conn->query('SELECT id, title, content, created_at FROM blogs ORDER BY created_at DESC');
if ($res) { while ($r = $res->fetch_assoc()) $posts[] = $r; }

?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Manage Posts - Admin</title>
  <link rel="stylesheet" href="admin.css">
  <link rel="stylesheet" href="blog.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>.page-title{color:#f8fafc}</style>
</head>
<body>
  <div class="admin-container">
    <nav class="sidebar">
      <div class="sidebar-header"><span class="brand">BIT2BYTE</span><span class="admin-badge">Admin</span></div>
      <ul class="nav-menu">
        <li class="nav-item"><a href="admin.html"><i class="fas fa-tachometer-alt"></i><span>Dashboard</span></a></li>
        <li class="nav-item"><a href="members.php"><i class="fas fa-users"></i><span>Members</span></a></li>
        <li class="nav-item"><a href="events.php"><i class="fas fa-calendar-alt"></i><span>Events</span></a></li>
        <li class="nav-item active"><a href="admin_manage_posts.php"><i class="fas fa-newspaper"></i><span>Blogs</span></a></li>
      </ul>
      <div class="sidebar-footer"><a href="logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i><span>Logout</span></a></div>
    </nav>

    <main class="main-content">
      <header class="top-header"><h1 class="page-title">Manage Blog Posts</h1></header>
      <div class="content-area">
        <div class="stats-row"><div class="stat-box"><h4>Total Posts</h4><div class="number"><?php echo count($posts);?></div></div></div>
        <button class="add-member-btn" onclick="openModal()"><i class="fas fa-plus"></i> Add Post</button>
        <div class="members-grid">
          <?php if(empty($posts)): ?>
            <div class="no-members"><i class="fas fa-newspaper"></i><h3>No posts yet</h3><p>Create one using Add Post.</p></div>
          <?php else: foreach($posts as $p): ?>
            <div class="member-card">
              <div class="member-content">
                <h3><?php echo htmlspecialchars($p['title']);?></h3>
                <div class="member-info"><?php echo date('F j, Y', strtotime($p['created_at']));?></div>
                <p><?php echo nl2br(htmlspecialchars(substr($p['content'],0,300)));?></p>
                <div class="member-actions">
                  <button class="btn-edit" onclick="editPost(<?php echo $p['id'];?>, '<?php echo addslashes(htmlspecialchars($p['title']));?>', '<?php echo addslashes(htmlspecialchars($p['content']));?>')"><i class="fas fa-edit"></i> Edit</button>
                  <button class="btn-delete" onclick="deletePost(<?php echo $p['id'];?>)"><i class="fas fa-trash"></i> Delete</button>
                </div>
              </div>
            </div>
          <?php endforeach; endif; ?>
        </div>
      </div>
    </main>
  </div>

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
    document.getElementById('postForm').addEventListener('submit',function(e){e.preventDefault();const fd=new FormData(this);fd.append('action',isEdit?'update':'create');const msg=document.getElementById('formMessage');msg.style.display='block';msg.className='message';msg.textContent=isEdit?'Saving...':'Creating...';fetch('admin_manage_posts.php',{method:'POST',body:fd}).then(r=>r.json()).then(data=>{msg.className='message '+(data.success?'success':'error');msg.textContent=data.message;if(data.success) setTimeout(()=>location.reload(),700);} ).catch(err=>{msg.className='message error';msg.textContent='Error: '+err});});
    function deletePost(id){if(!confirm('Delete this post?'))return;const fd=new FormData();fd.append('action','delete');fd.append('id',id);fetch('admin_manage_posts.php',{method:'POST',body:fd}).then(r=>r.json()).then(d=>{if(d.success) location.reload(); else alert(d.message);});}
  </script>
</body>
</html>
