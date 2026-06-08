<?php
session_start();
require_once 'db.php';

// Check for remember me cookie if session not set
if (!isset($_SESSION['admin_logged_in']) && isset($_COOKIE['remember_admin'])) {
    $cookie_data = base64_decode($_COOKIE['remember_admin']);
    $parts = explode(':', $cookie_data);
    if (count($parts) === 2) {
        $admin_id = $parts[0];
        $password_hash = $parts[1];
        
        $stmt = $conn->prepare("SELECT id, email, password, name FROM admin WHERE id = ?");
        $stmt->bind_param("i", $admin_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $admin = $result->fetch_assoc();
            if (hash('sha256', $admin['password']) === $password_hash) {
                $_SESSION['admin_id'] = $admin['id'];
                $_SESSION['admin_email'] = $admin['email'];
                $_SESSION['admin_name'] = $admin['name'];
                $_SESSION['admin_logged_in'] = true;
            } else {
                setcookie('remember_admin', '', time() - 3600, '/');
            }
        } else {
            setcookie('remember_admin', '', time() - 3600, '/');
        }
        $stmt->close();
    }
}

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

// Get all members
$members = [];
$result = $conn->query("SELECT * FROM members ORDER BY id DESC");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $members[] = $row;
    }
}

// Get stats
$totalMembers = count($members);
$statsQuery = $conn->query("SELECT designation, COUNT(*) as count FROM members GROUP BY designation");
$designationCounts = [];
while ($row = $statsQuery->fetch_assoc()) {
    $designationCounts[$row['designation']] = $row['count'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Members - BIT2BYTE</title>
    <link rel="stylesheet" href="admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Member Cards Grid */
        .members-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 24px;
            margin-top: 20px;
        }

        .member-card {
            background: white;
            border-radius: 14px;
            border: 1px solid #9db6c2;
            overflow: hidden;
            box-shadow: 0 10px 24px rgba(10, 61, 98, 0.08);
            transition: transform 0.2s, box-shadow 0.2s;
            position: relative;
        }

        .member-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 14px 30px rgba(10, 61, 98, 0.15);
        }

        .member-image {
            width: 100%;
            height: 280px;
            overflow: hidden;
            background: #f0f0f0;
        }

        .member-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s;
        }

        .member-card:hover .member-image img {
            transform: scale(1.05);
        }

        .member-image .placeholder-avatar {
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            font-size: 64px;
            font-weight: bold;
        }

        .member-content {
            padding: 10px 12px;
        }

        .member-content h3 {
            font-size: 16px;
            color: #0a3d62;
            margin-bottom: 6px;
            font-weight: bold;
        }

        .member-designation {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            text-transform: capitalize;
            margin-bottom: 8px;
            font-weight: 600;
        }

        .badge-president { background: #fee2e2; color: #dc2626; }
        .badge-vice_president { background: #fef3c7; color: #d97706; }
        .badge-general_secretary { background: #dbeafe; color: #2563eb; }
        .badge-treasurer { background: #d1fae5; color: #059669; }
        .badge-organizer { background: #e0e7ff; color: #4f46e5; }
        .badge-member { background: #f3f4f6; color: #6b7280; }

        .member-info {
            font-size: 14px;
            color: #666;
            margin-bottom: 6px;
        }

        .member-info i {
            width: 18px;
            color: #999;
            margin-right: 6px;
        }

        .member-actions {
            display: flex;
            gap: 8px;
            margin-top: 10px;
            padding-top: 10px;
            border-top: 1px solid #eee;
        }

        .btn-edit {
            flex: 1;
            padding: 8px;
            background: #3b82f6;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 13px;
            transition: background 0.2s;
        }

        .btn-edit:hover {
            background: #2563eb;
        }

        .btn-delete {
            flex: 1;
            padding: 8px;
            background: #ef4444;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 13px;
            transition: background 0.2s;
        }

        .btn-delete:hover {
            background: #dc2626;
        }

        /* Add Member Button */
         .add-member-btn{ 
        background:#6366f1; 
        color:white; 
        border:none;
        padding:10px 20px; 
        border-radius:8px; 
        cursor:pointer; 
        font-size:14px; 
        font-weight:500;
        display:inline-flex; 
        align-items:center; 
        gap:8px; 
        transition: background 0.2s 
    }
    .add-member-btn:hover{ 
    background:#4f46e5
     }
     .add-member-btn i{ 
    font-size:16px 
     }

        /* Modal Overlay */
        .modal-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            align-items: center;
            justify-content: center;
        }

        .modal-overlay.active {
            display: flex;
        }

        .modal-content {
            background: white;
            border-radius: 14px;
            padding: 30px;
            max-width: 500px;
            width: 90%;
            max-height: 90vh;
            overflow-y: auto;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
        }

        .modal-header h2 {
            font-size: 24px;
            color: #0a3d62;
            margin: 0;
        }

        .modal-close {
            background: none;
            border: none;
            font-size: 24px;
            cursor: pointer;
            color: #999;
            padding: 0;
            line-height: 1;
        }

        .modal-close:hover {
            color: #333;
        }

        .form-group {
            margin-bottom: 16px;
        }

        .form-group label {
            display: block;
            margin-bottom: 6px;
            font-weight: 500;
            color: #333;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 10px 14px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 14px;
            box-sizing: border-box;
        }

        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: #6366f1;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
        }

        .form-group small {
            color: #666;
            font-size: 12px;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }

        .btn-submit {
            width: 100%;
            padding: 12px;
            background: #6366f1;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 500;
            margin-top: 8px;
            transition: background 0.2s;
        }

        .btn-submit:hover {
            background: #4f46e5;
        }

        .message {
            padding: 10px 15px;
            border-radius: 8px;
            margin-bottom: 16px;
            display: none;
        }

        .message.success {
            background: #d1fae5;
            color: #059669;
            border: 1px solid #6ee7b7;
        }

        .message.error {
            background: #fee2e2;
            color: #dc2626;
            border: 1px solid #fca5a5;
        }

        .stats-row{ display:flex; gap:15px; margin-bottom:20px; flex-wrap:wrap }
.stat-box{ background:var(--card-bg); padding:15px 20px; border-radius:10px; box-shadow:0 2px 10px rgba(0,0,0,0.08); flex:1; min-width:120px; border:1px solid var(--border-color); transition:transform 0.2s, box-shadow 0.2s, border-color 0.2s }
.stat-box:hover{ transform:translateY(-4px); box-shadow:0 8px 25px rgba(99,102,241,0.25); border-color:var(--primary-color) }
.stat-box h4{ margin:0 0 5px 0; color:#94a3b8; font-size:13px }
.stat-box .number{ font-size:24px; font-weight:bold; color:#6366f1 }

        .no-members {
            text-align: center;
            padding: 60px 20px;
            color: #999;
        }

        .no-members i {
            font-size: 64px;
            margin-bottom: 16px;
            color: #ddd;
        }

        .no-members h3 {
            margin-bottom: 8px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <!-- Sidebar Navigation -->
        <nav class="sidebar">
            <div class="sidebar-header">
                <span class="brand">BIT2BYTE</span>
                <span class="admin-badge">Admin</span>
            </div>
            <ul class="nav-menu">
                <li class="nav-item">
                    <a href="admin.php">
                        <i class="fas fa-tachometer-alt"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li class="nav-item active">
                    <a href="members.php">
                        <i class="fas fa-users"></i>
                        <span>Club Members</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="admin_events.php">
                        <i class="fas fa-calendar-alt"></i>
                        <span>Events</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="admin_blog_list.php">
                        <i class="fas fa-newspaper"></i>
                        <span>Blogs</span>
                    </a>
                </li>
            </ul>
            <div class="sidebar-footer">
                <a href="logout.php" class="logout-btn">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Logout</span>
                </a>
            </div>
        </nav>

        <!-- Main Content Area -->
        <main class="main-content">
            <header class="top-header">
                <h1 class="page-title">Club Members</h1>
                <div class="header-actions">
                    <button class="add-member-btn" onclick="openModal()">
                        <i class="fas fa-user-plus"></i>
                        Add New Member
                    </button>
                </div>
            </header>

            <div class="content-area">
                <!-- Stats -->
                <div class="stats-row">
                    <div class="stat-box">
                        <h4>Total Members</h4>
                        <div class="number"><?php echo $totalMembers; ?></div>
                    </div>
                    <div class="stat-box">
                        <h4>President</h4>
                        <div class="number"><?php echo $designationCounts['president'] ?? 0; ?></div>
                    </div>
                    <div class="stat-box">
                        <h4>Vice President</h4>
                        <div class="number"><?php echo $designationCounts['vice_president'] ?? 0; ?></div>
                    </div>
                    <div class="stat-box">
                        <h4>General Secretary</h4>
                        <div class="number"><?php echo $designationCounts['general_secretary'] ?? 0; ?></div>
                    </div>
                    <div class="stat-box">
                        <h4>Treasurer</h4>
                        <div class="number"><?php echo $designationCounts['treasurer'] ?? 0; ?></div>
                    </div>
                    <div class="stat-box">
                        <h4>Organizer</h4>
                        <div class="number"><?php echo $designationCounts['organizer'] ?? 0; ?></div>
                    </div>
                </div>

                <!-- Members Grid -->
                <div class="members-grid">
                    <?php if (empty($members)): ?>
                    <div class="no-members">
                        <i class="fas fa-users"></i>
                        <h3>No Members Yet</h3>
                        <p>Click "Add New Member" to add your first member.</p>
                    </div>
                    <?php else: ?>
                        <?php foreach ($members as $member): ?>
                        <div class="member-card">
                            <div class="member-image">
                                <?php if (!empty($member['image']) && file_exists($member['image'])): ?>
                                    <img src="<?php echo htmlspecialchars($member['image']); ?>" alt="<?php echo htmlspecialchars($member['name']); ?>">
                                <?php else: ?>
                                    <div class="placeholder-avatar">
                                        <?php echo strtoupper(substr($member['name'], 0, 1)); ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="member-content">
                                <h3><?php echo htmlspecialchars($member['name']); ?></h3>
                                <span class="member-designation badge-<?php echo $member['designation']; ?>">
                                    <?php echo ucwords(str_replace('_', ' ', $member['designation'])); ?>
                                </span>
                                <div class="member-actions">
                                    <button class="btn-edit" onclick="editMember(<?php echo $member['id']; ?>, '<?php echo addslashes(htmlspecialchars($member['name'])); ?>', '<?php echo addslashes(htmlspecialchars($member['email'])); ?>', '<?php echo addslashes(htmlspecialchars($member['phone'] ?? '')); ?>', '<?php echo $member['designation']; ?>')">
                                        <i class="fas fa-edit"></i> Edit
                                    </button>
                                    <button class="btn-delete" onclick="deleteMember(<?php echo $member['id']; ?>)">
                                        <i class="fas fa-trash"></i> Delete
                                    </button>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>

    <!-- Add/Edit Member Modal -->
    <div class="modal-overlay" id="memberModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 id="modalTitle">Add New Member</h2>
                <button class="modal-close" onclick="closeModal()">&times;</button>
            </div>
            <div id="formMessage" class="message"></div>
            <form id="memberForm" enctype="multipart/form-data">
                <input type="hidden" id="memberId" name="id" value="">
                <div class="form-group">
                    <label for="memberName">Full Name *</label>
                    <input type="text" id="memberName" name="name" required placeholder="Enter full name">
                </div>
                <div class="form-group">
                    <label for="memberEmail">Email *</label>
                    <input type="email" id="memberEmail" name="email" required placeholder="Enter email address">
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="memberPhone">Phone</label>
                        <input type="text" id="memberPhone" name="phone" placeholder="Enter phone number">
                    </div>
                    <div class="form-group">
                        <label for="memberDesignation">Designation</label>
                        <select id="memberDesignation" name="designation">
                            <option value="member">Member</option>
                            <option value="president">President</option>
                            <option value="vice_president">Vice President</option>
                            <option value="general_secretary">General Secretary</option>
                            <option value="treasurer">Treasurer</option>
                            <option value="organizer">Organizer</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label for="memberImage">Profile Image (Optional)</label>
                    <input type="file" id="memberImage" name="image" accept="image/*">
                    <small id="imageHelp">Allowed: JPG, PNG, GIF, WebP (Max 5MB)</small>
                </div>
                <button type="submit" class="btn-submit" id="submitBtn">
                    <i class="fas fa-plus"></i> Add Member
                </button>
            </form>
        </div>
    </div>

    <script>
        let isEditMode = false;

        // Modal functions
        function openModal() {
            isEditMode = false;
            document.getElementById('modalTitle').textContent = 'Add New Member';
            document.getElementById('submitBtn').innerHTML = '<i class="fas fa-plus"></i> Add Member';
            document.getElementById('memberForm').reset();
            document.getElementById('memberId').value = '';
            document.getElementById('formMessage').style.display = 'none';
            document.getElementById('memberModal').classList.add('active');
        }

        function closeModal() {
            document.getElementById('memberModal').classList.remove('active');
            document.getElementById('memberForm').reset();
            document.getElementById('formMessage').style.display = 'none';
            isEditMode = false;
        }

        // Close modal when clicking outside
        document.getElementById('memberModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeModal();
            }
        });

        // Edit Member
        function editMember(id, name, email, phone, designation) {
            isEditMode = true;
            document.getElementById('modalTitle').textContent = 'Edit Member';
            document.getElementById('submitBtn').innerHTML = '<i class="fas fa-save"></i> Save Changes';
            document.getElementById('memberId').value = id;
            document.getElementById('memberName').value = name;
            document.getElementById('memberEmail').value = email;
            document.getElementById('memberPhone').value = phone;
            document.getElementById('memberDesignation').value = designation;
            document.getElementById('formMessage').style.display = 'none';
            document.getElementById('memberModal').classList.add('active');
        }

        // Member Form Submit (Add or Edit)
        document.getElementById('memberForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData();
            formData.append('name', document.getElementById('memberName').value);
            formData.append('email', document.getElementById('memberEmail').value);
            formData.append('phone', document.getElementById('memberPhone').value);
            formData.append('designation', document.getElementById('memberDesignation').value);
            
            const imageFile = document.getElementById('memberImage').files[0];
            if (imageFile) {
                formData.append('image', imageFile);
            }

            const msgEl = document.getElementById('formMessage');
            msgEl.style.display = 'block';
            msgEl.className = 'message';
            
            let url = 'add_member.php';
            if (isEditMode) {
                formData.append('id', document.getElementById('memberId').value);
                url = 'update_member.php';
                msgEl.textContent = 'Updating member...';
            } else {
                msgEl.textContent = 'Adding member...';
            }

            fetch(url, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                msgEl.className = 'message ' + (data.success ? 'success' : 'error');
                msgEl.textContent = data.message;
                
                if (data.success) {
                    setTimeout(() => location.reload(), 1500);
                }
            })
            .catch(error => {
                msgEl.className = 'message error';
                msgEl.textContent = 'Error: ' + error;
            });
        });

        // Delete Member
        function deleteMember(id) {
            if (!confirm('Are you sure you want to delete this member?')) return;
            
            fetch('delete_member.php?id=' + id, {
                method: 'DELETE'
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert(data.message);
                }
            });
        }
        // Auto-open modal if action=add
        <?php if (isset($_GET['action']) && $_GET['action'] === 'add'): ?>
        window.addEventListener('DOMContentLoaded', function() { openModal(); });
        <?php endif; ?>
    </script>
</body>
</html>
<?php $conn->close(); ?>