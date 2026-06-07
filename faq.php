<?php
// faq.php — BIT2BYTE Club FAQ Page
$faqs = [
    [
        "category" => "About the Club",
        "items" => [
            ["q" => "What is BIT2BYTE?", "a" => "BIT2BYTE is the Software Research and Development Community of KUET (Khulna University of Engineering and Technology). We are a student-driven club focused on building skilled developers, fostering innovation, and preparing members for real-world tech challenges."],
            ["q" => "When was BIT2BYTE founded?", "a" => "BIT2BYTE has been active for several years at KUET, running workshops, hackathons, and development sessions to grow the tech culture on campus."],
            ["q" => "Who can join BIT2BYTE?", "a" => "Any student of KUET can apply to join BIT2BYTE. We welcome students from all departments — not just CSE — who have a passion for technology and software development."],
        ]
    ],
    [
        "category" => "Membership",
        "items" => [
            ["q" => "How do I become a member?", "a" => "Membership is open through recruitment drives held each semester. Watch our Facebook page and notice board for announcements. The process typically involves a written test and/or interview round."],
            ["q" => "Is there a membership fee?", "a" => "There may be a nominal membership fee to cover club activities and resources. The exact amount is announced during each recruitment cycle."],
            ["q" => "Can I be a member without attending every event?", "a" => "While we encourage active participation, we understand academic commitments. However, consistent engagement is expected to retain active membership status."],
        ]
    ],
    [
        "category" => "Activities & Events",
        "items" => [
            ["q" => "What kind of events does BIT2BYTE organize?", "a" => "We organize a wide range of events including tech workshops, hackathons, competitive programming contests, web development bootcamps, Android development sessions, project showcases, and guest speaker sessions."],
            ["q" => "How often are workshops held?", "a" => "Workshops are held regularly throughout the semester, typically once or twice a month depending on the schedule and availability of resources."],
            ["q" => "Does BIT2BYTE participate in national competitions?", "a" => "Yes! BIT2BYTE actively encourages and supports members to participate in national-level hackathons, ICPC regional contests, and other inter-university tech competitions."],
        ]
    ],
    [
        "category" => "Learning & Resources",
        "items" => [
            ["q" => "What topics does BIT2BYTE cover?", "a" => "We cover a broad curriculum including C/C++, Java, Python, JavaScript, Data Structures & Algorithms, OOP, Web Development (HTML, CSS, JS), Android Development, Backend Development, Database Management, and more."],
            ["q" => "Do I need prior programming experience to join?", "a" => "No prior experience is required for beginners. BIT2BYTE offers foundational sessions to help newcomers start from scratch. However, some advanced tracks may require basic knowledge."],
            ["q" => "Are learning materials provided?", "a" => "Yes, we share curated study materials, slides, and resources through our internal channels after workshops and sessions."],
        ]
    ],
    [
        "category" => "Contact & Social",
        "items" => [
            ["q" => "How can I contact BIT2BYTE?", "a" => "You can reach us via email at bit2bytekuet@gmail.com or through our official Facebook page at facebook.com/bittwobyte."],
            ["q" => "Where can I follow BIT2BYTE online?", "a" => "Follow us on Facebook (facebook.com/bittwobyte) and LinkedIn (linkedin.com/company/bit2byte-kuet) for the latest news, event announcements, and member spotlights."],
        ]
    ],
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BIT2BYTE | FAQ</title>
    <meta name="description" content="Frequently asked questions about BIT2BYTE — the Software Research and Development Community of KUET.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@600;700&family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="faq.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

    <!-- Navbar -->
    <nav class="navbar">
        <span class="brand">BIT2BYTE</span>
        <ul class="nav-center">
            <li><a href="home.html">Home</a></li>
            <li><a href="about.php">About</a></li>
            <li><a href="event.php">Event</a></li>
            <li><a href="blogs_list.php">Blog</a></li>
            <li><a href="faq.php" class="active" aria-current="page">FAQ</a></li>
        </ul>
        <button class="login-btn" onclick="window.location.href='login.html'">Login</button>
    </nav>

    <!-- Hero -->
    <header class="faq-hero">
        <div class="hero-inner">
            <span class="hero-badge"><i class="fas fa-circle-question"></i> Help Center</span>
            <h1>Frequently Asked<br><span class="highlight">Questions</span></h1>
            <p>Everything you need to know about BIT2BYTE — the Software Research & Development Community of KUET.</p>
        </div>
    </header>

    <!-- FAQ Content -->
    <main class="faq-main">
        <div class="faq-container">

            <!-- Sidebar category nav -->
            <aside class="faq-sidebar">
                <p class="sidebar-label">Jump to section</p>
                <ul class="category-nav">
                    <?php foreach ($faqs as $i => $section): ?>
                    <li>
                        <a href="#cat-<?= $i ?>" class="cat-link <?= $i === 0 ? 'active' : '' ?>">
                            <span class="cat-dot"></span>
                            <?= htmlspecialchars($section['category']) ?>
                        </a>
                    </li>
                    <?php endforeach; ?>
                </ul>
                <div class="sidebar-cta">
                    <p>Still have questions?</p>
                    <a href="mailto:bit2bytekuet@gmail.com" class="cta-btn">
                        <i class="fas fa-envelope"></i> Email Us
                    </a>
                </div>
            </aside>

            <!-- FAQ sections -->
            <div class="faq-content">
                <?php foreach ($faqs as $i => $section): ?>
                <section class="faq-section" id="cat-<?= $i ?>">
                    <div class="section-label">
                        <span class="section-num"><?= str_pad($i + 1, 2, '0', STR_PAD_LEFT) ?></span>
                        <h2><?= htmlspecialchars($section['category']) ?></h2>
                    </div>
                    <div class="accordion">
                        <?php foreach ($section['items'] as $j => $item): ?>
                        <div class="accordion-item" data-index="<?= $i . '-' . $j ?>">
                            <button class="accordion-trigger" aria-expanded="false">
                                <span class="q-text"><?= htmlspecialchars($item['q']) ?></span>
                                <span class="accordion-icon"><i class="fas fa-plus"></i></span>
                            </button>
                            <div class="accordion-body">
                                <p><?= htmlspecialchars($item['a']) ?></p>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </section>
                <?php endforeach; ?>
            </div>

        </div>
    </main>

    <!-- Footer -->
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
                    <li><a href="blog_list.php">Blogs</a></li>
                </ul>
            </div>
            <div class="footer-col">
                <h4>Social</h4>
                <ul>
                    <li><a href="mailto:bit2bytekuet@gmail.com">Email</a></li>
                    <li><a href="https://www.facebook.com/bittwobyte" target="_blank">Facebook</a></li>
                    <li><a href="https://www.linkedin.com/company/bit2byte-kuet" target="_blank">LinkedIn</a></li>
                </ul>
            </div>
            <div class="footer-col">
                <h4>Support</h4>
                <ul>
                    
                    <li><a href="faq.php">FAQs</a></li>
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

    <script src="faq.js"></script>
</body>
</html>
