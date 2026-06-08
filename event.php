<?php
require_once 'db.php';

// Get all events for public display
$events = [];
$result = $conn->query("SELECT id, title, description, date, location, image FROM events ORDER BY date DESC");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $events[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Events - BIT2BYTE</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="event.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
  <nav class="navbar">
    <span class="brand">BIT2BYTE</span>
    <ul class="nav-center">
      <li><a href="home.html">Home</a></li>
      <li><a href="about.php">About</a></li>
      <li><a href="event.php" class="active" aria-current="page">Event</a></li>
      <li><a href="blogs_list.php">Blog</a></li>
      <li><a href="faq.php">FAQ</a></li>
    </ul>
    <button class="login-btn" onclick="window.location.href='login.php'">Login</button>
  </nav>

  <main class="events-section">
    <h1>Our <span style="color: #3ea4b6;">Events</span></h1>
    <p class="events-intro">Discover the exciting events organized by BIT2BYTE community</p>
    <div class="events-grid">
      <?php if (empty($events)): ?>
      <p style="text-align: center; color: #666; grid-column: 1 / -1; padding: 40px;">No events have been added yet.</p>
      <?php else: ?>
        <?php foreach ($events as $event): ?>
        <article class="event-card">
          <div class="event-image">
            <?php if (!empty($event['image']) && file_exists($event['image'])): ?>
                <img src="<?php echo htmlspecialchars($event['image']); ?>" alt="<?php echo htmlspecialchars($event['title']); ?>">
            <?php else: ?>
                <div class="event-image-placeholder">
                    <i class="fas fa-calendar"></i>
                </div>
            <?php endif; ?>
          </div>
<div class="event-content">
             <h3><?php echo htmlspecialchars($event['title']); ?></h3>
             <p class="event-date"><strong>Date:</strong> <?php echo date('F j, Y', strtotime($event['date'])); ?> | <strong>Time:</strong> <?php echo date('g:i A', strtotime($event['date'])); ?></p>
             <p class="event-location"><strong>Location:</strong> <?php echo htmlspecialchars($event['location']); ?></p>
             <p><?php echo htmlspecialchars($event['description']); ?></p>
           </div>
        </article>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
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
      <a href="#" class="back-to-top">Back to top ↑</a>
    </div>
  </footer>

</body>
</html>
<?php $conn->close(); ?>