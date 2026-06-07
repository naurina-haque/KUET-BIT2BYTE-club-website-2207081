<?php
session_start();
require_once 'db.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.html');
    exit;
}

// Get all events
$events = [];
$result = $conn->query("SELECT * FROM events ORDER BY date DESC");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $events[] = $row;
    }
}

$totalEvents = count($events);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Events - BIT2BYTE</title>
    <link rel="stylesheet" href="admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Member Cards Grid (reused for events) */
        .members-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 24px;
            margin-top: 20px;
        }

        .member-card {
            background: white;
            border-radius: 14px;
            overflow: hidden;
            box-shadow: 0 10px 24px rgba(10, 61, 98, 0.08);
            transition: transform 0.2s, box-shadow 0.2s;
            position: relative;
            border: 1px solid var(--border-color);
           
        }

        .member-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 14px 30px rgba(10, 61, 98, 0.15);
        }

        .member-image {
            width: 100%;
            height: 280px;
            overflow: hidden;
            background: var(--bg-tertiary);
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
        }

        .member-content {
            padding: 10px 12px;
        }

        .member-content h3 {
            font-size: 16px;
            color: #22384e;
            margin-bottom: 6px;
            font-weight: bold;
        }

        .member-info {
            font-size: 14px;
            color: #414953;
            margin-bottom: 6px;
        }

        .member-info i {
            width: 18px;
            color: #64748b;
            margin-right: 6px;
        }

        .member-actions {
            display: flex;
            gap: 8px;
            margin-top: 10px;
            padding-top: 10px;
            border-top: 1px solid var(--border-color);
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

        /* Add Member Button (reused) */
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
            background: var(--card-bg);
            border-radius: 14px;
            padding: 30px;
            max-width: 500px;
            width: 90%;
            max-height: 90vh;
            overflow-y: auto;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
            border: 1px solid var(--border-color);
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
        }

        .modal-header h2 {
            font-size: 24px;
            color: #f8fafc;
            margin: 0;
        }

        .modal-close {
            background: none;
            border: none;
            font-size: 24px;
            cursor: pointer;
            color: #94a3b8;
            padding: 0;
            line-height: 1;
        }

        .modal-close:hover {
            color: #f8fafc;
        }

        .form-group {
            margin-bottom: 16px;
        }

        .form-group label {
            display: block;
            margin-bottom: 6px;
            font-weight: 500;
            color: #f8fafc;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 10px 14px;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            font-size: 14px;
            box-sizing: border-box;
            font-family: inherit;
            background: var(--bg-tertiary);
            color: #f8fafc;
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #6366f1;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.08);
        }

        .form-group small {
            color: #94a3b8;
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
            background: #10b98133;
            color: #34d399;
            border: 1px solid #10b981;
        }

        .message.error {
            background: #ef444433;
            color: #fca5a5;
            border: 1px solid #ef4444;
        }

        .stats-row {
            display: flex;
            gap: 15px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }

        .stats-row{ display:flex; gap:15px; margin-bottom:20px; flex-wrap:wrap }
.stat-box{ background:var(--card-bg); padding:15px 20px; border-radius:10px; box-shadow:0 2px 10px rgba(0,0,0,0.08); flex:1; min-width:120px; border:1px solid var(--border-color); transition:transform 0.2s, box-shadow 0.2s, border-color 0.2s }
.stat-box:hover{ transform:translateY(-4px); box-shadow:0 8px 25px rgba(99,102,241,0.25); border-color:var(--primary-color) }
.stat-box h4{ margin:0 0 5px 0; color:#94a3b8; font-size:13px }
.stat-box .number{ font-size:24px; font-weight:bold; color:#6366f1 }

        .no-members {
            text-align: center;
            padding: 60px 20px;
            color: #94a3b8;
        }

        .no-members i {
            font-size: 64px;
            margin-bottom: 16px;
            color: #64748b;
        }

        .no-members h3 {
            margin-bottom: 8px;
            color: #94a3b8;
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <!-- Sidebar Navigation (Same as members.php) -->
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
                <li class="nav-item">
                    <a href="members.php">
                        <i class="fas fa-users"></i>
                        <span>Club Members</span>
                    </a>
                </li>
                <li class="nav-item active">
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
                <h1 class="page-title">Club Events</h1>
                <div class="header-actions">
                    <button class="add-member-btn" onclick="openModal()">
                        <i class="fas fa-plus"></i>
                        Add New Event
                    </button>
                </div>
            </header>

            <div class="content-area">
                <!-- Stats -->
                <div class="stats-row">
                    <div class="stat-box">
                        <h4>Total Events</h4>
                        <div class="number"><?php echo $totalEvents; ?></div>
                    </div>
                    
                </div>

                <!-- Events Grid -->
                <div class="members-grid">
                    <?php if (empty($events)): ?>
                    <div class="no-members">
                        <i class="fas fa-calendar"></i>
                        <h3>No Events Yet</h3>
                        <p>Click "Add New Event" to add your first event.</p>
                    </div>
                    <?php else: ?>
                        <?php foreach ($events as $event): ?>
                        <div class="member-card">
                            <div class="member-image">
                                <?php if (!empty($event['image']) && file_exists($event['image'])): ?>
                                    <img src="<?php echo htmlspecialchars($event['image']); ?>" alt="<?php echo htmlspecialchars($event['title']); ?>">
                                <?php else: ?>
                                    <div class="placeholder-avatar">
                                        <i class="fas fa-calendar"></i>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="member-content">
                                <h3><?php echo htmlspecialchars($event['title']); ?></h3>
                                <div class="member-info">
                                    <i class="fas fa-calendar-day"></i>
                                    <span><?php echo date('M j, Y', strtotime($event['date'])); ?></span>
                                </div>
                                <?php if (!empty($event['location'])): ?>
                                <div class="member-info">
                                    <i class="fas fa-map-marker-alt"></i>
                                    <span><?php echo htmlspecialchars($event['location']); ?></span>
                                </div>
                                <?php endif; ?>
                                <div class="member-actions">
                                    <button class="btn-edit" onclick="editEvent(<?php echo $event['id']; ?>, '<?php echo addslashes(htmlspecialchars($event['title'])); ?>', '<?php echo addslashes(htmlspecialchars($event['description'] ?? '')); ?>', '<?php echo addslashes(htmlspecialchars($event['date'])); ?>', '<?php echo addslashes(htmlspecialchars($event['location'] ?? '')); ?>')">
                                        <i class="fas fa-edit"></i> Edit
                                    </button>
                                    <button class="btn-delete" onclick="deleteEvent(<?php echo $event['id']; ?>)">
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

    <!-- Add/Edit Event Modal -->
    <div class="modal-overlay" id="eventModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 id="modalTitle">Add New Event</h2>
                <button class="modal-close" onclick="closeModal()">&times;</button>
            </div>
            <div id="formMessage" class="message"></div>
            <form id="eventForm" enctype="multipart/form-data">
                <input type="hidden" id="eventId" name="id" value="">
                <div class="form-group">
                    <label for="eventTitle">Title *</label>
                    <input type="text" id="eventTitle" name="title" required placeholder="Event title">
                </div>
                <div class="form-group">
                    <label for="eventDescription">Description</label>
                    <textarea id="eventDescription" name="description" rows="4" placeholder="Event description"></textarea>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="eventDate">Date *</label>
                        <input type="datetime-local" id="eventDate" name="date" required>
                    </div>
                    <div class="form-group">
                        <label for="eventLocation">Location</label>
                        <input type="text" id="eventLocation" name="location" placeholder="Event location">
                    </div>
                </div>
                <div class="form-group">
                    <label for="eventImage">Event Image (Optional)</label>
                    <input type="file" id="eventImage" name="image" accept="image/*">
                    <small id="imageHelp">Allowed: JPG, PNG, GIF, WebP (Max 5MB)</small>
                </div>
                <button type="submit" class="btn-submit" id="submitBtn">
                    <i class="fas fa-plus"></i> Add Event
                </button>
            </form>
        </div>
    </div>

    <script>
        let isEditMode = false;

        // Modal functions
        function openModal() {
            isEditMode = false;
            document.getElementById('modalTitle').textContent = 'Add New Event';
            document.getElementById('submitBtn').innerHTML = '<i class="fas fa-plus"></i> Add Event';
            document.getElementById('eventForm').reset();
            document.getElementById('eventId').value = '';
            document.getElementById('formMessage').style.display = 'none';
            document.getElementById('eventModal').classList.add('active');
        }

        function closeModal() {
            document.getElementById('eventModal').classList.remove('active');
            document.getElementById('eventForm').reset();
            document.getElementById('formMessage').style.display = 'none';
            isEditMode = false;
        }

        // Close modal when clicking outside
        document.getElementById('eventModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeModal();
            }
        });

        // Edit Event
        function editEvent(id, title, description, date, location) {
            isEditMode = true;
            document.getElementById('modalTitle').textContent = 'Edit Event';
            document.getElementById('submitBtn').innerHTML = '<i class="fas fa-save"></i> Save Changes';
            document.getElementById('eventId').value = id;
            document.getElementById('eventTitle').value = title;
            document.getElementById('eventDescription').value = description;
            
            // Convert date to local datetime string format (YYYY-MM-DDTHH:MM) safely without timezone shifting or browser parsing issues
            let localDateStr = '';
            if (date) {
                let formattedDate = date.trim().replace(' ', 'T');
                // Handle different lengths to ensure we always get exactly YYYY-MM-DDTHH:MM
                if (formattedDate.length === 10) { // YYYY-MM-DD
                    formattedDate += 'T00:00';
                } else if (formattedDate.length > 16) { // YYYY-MM-DDTHH:MM:SS
                    formattedDate = formattedDate.slice(0, 16);
                }
                localDateStr = formattedDate;
            }
            document.getElementById('eventDate').value = localDateStr;
            
            if (location) {
                document.getElementById('eventLocation').value = location;
            }
            
            document.getElementById('formMessage').style.display = 'none';
            document.getElementById('eventModal').classList.add('active');
        }

        // Event Form Submit (Add or Edit)
        document.getElementById('eventForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Use FormData to automatically capture all inputs, including files and dates correctly
            const formData = new FormData(this);

            const msgEl = document.getElementById('formMessage');
            msgEl.style.display = 'block';
            msgEl.className = 'message';
            
            let url = 'add_event.php';
            if (isEditMode) {
                url = 'update_event.php';
                msgEl.textContent = 'Updating event...';
            } else {
                msgEl.textContent = 'Adding event...';
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

        // Delete Event
        function deleteEvent(id) {
            if (!confirm('Are you sure you want to delete this event?')) return;
            
            fetch('delete_event.php?id=' + id, {
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