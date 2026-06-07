<?php
require_once 'db.php';

// Get all members for public display
$members = [];
$result = $conn->query("SELECT name, image, designation FROM members ORDER BY id DESC");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $members[] = $row;
    }
}
?>
<!DOCTYPE html>
<html class="dark" lang="en" style="color-scheme: dark;">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bit2Byte | About</title>
    <meta name="description" content="About Bit2Byte, the Software Research and Development Community of KUET.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="about.css?v=2">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body id="top">
    <div class="page-shell">
        <nav class="navbar" aria-label="Primary">
            <span class="brand">BIT2BYTE</span>
            <ul class="nav-center">
                <li><a href="home.html">Home</a></li>
                <li><a href="about.php" class="active" aria-current="page">About</a></li>
                <li><a href="event.php">Event</a></li>
                <li><a href="blogs_list.php">Blog</a></li>
                <li><a href="faq.php">FAQ</a></li>
            </ul>
            <button class="login-btn" onclick="window.location.href='login.html'">Login</button>
        </nav>

        <main class="content-wrap">
            <section class="hero-panel reveal">
                <div class="hero-visual" aria-label="Bit2Byte memories carousel">
                    <div class="hero-carousel" data-carousel>
                        <figure class="carousel-slide">
                            <img src="https://www.bit2bytekuet.com/_next/image?url=%2Fimages%2Fabout_page_image.jpg&w=750&q=75" alt="Bit2Byte about page event" />
                        </figure>
                        <figure class="carousel-slide">
                            <img src="https://www.bit2bytekuet.com/_next/image?url=%2Fimages%2Foldest_android_workshop_2019.jpg&w=750&q=75" alt="Bit2Byte Android workshop 2019" />
                        </figure>
                        <figure class="carousel-slide">
                            <img src="https://www.bit2bytekuet.com/_next/image?url=%2Fimages%2Fbit2byte_with_hashem_sir.jpg&w=750&q=75" alt="Bit2Byte with Hashem Sir" />
                        </figure>
                    </div>
                </div>

                <div class="hero-copy">
                    <h1 class="club-title" style=" font-size: 90px; color: #0a3d62; font-weight: bold; letter-spacing: -0.02em; font-family: 'Oswald', 'Segoe UI', sans-serif; margin-bottom: 16px;">Bit2Byte</h1>
                    <p class="club-desc">A software research and development club focused on building a community of skilled developers. We prepare our members to excel in hackathons, work on real-world projects, secure top jobs, and develop essential soft skills.</p>
                </div>
            </section>

            <section class="members-section reveal" aria-label="Our members">
                <h1 style="text-align: center; font-size: 70px; color: #0a3d62; font-weight: bold; letter-spacing: -0.02em; font-family: 'Oswald', 'Segoe UI', sans-serif; margin-bottom: 16px;">Our Members</h1>
                <p style="font-size: 18px; color: #666; max-width: 600px; margin: 0 auto 48px; text-align: center;">Meet the talented members of BIT2BYTE community</p>

                <div class="members-carousel">
                    <div class="members-grid">
                        <?php if (empty($members)): ?>
                        <p style="text-align: center; color: #666; grid-column: 1 / -1; padding: 40px;">No members have been added yet.</p>
                        <?php else: ?>
                            <?php foreach ($members as $member): ?>
                            <article class="member-card">
                                <?php if (!empty($member['image']) && file_exists($member['image'])): ?>
                                    <img class="member-avatar" src="<?php echo htmlspecialchars($member['image']); ?>" alt="<?php echo htmlspecialchars($member['name']); ?>">
                                <?php else: ?>
                                    <div class="member-avatar placeholder">
                                        <?php echo strtoupper(substr($member['name'], 0, 1)); ?>
                                    </div>
                                <?php endif; ?>
                                <h3><?php echo htmlspecialchars($member['name']); ?></h3>
                                <p class="member-role"><?php echo ucwords(str_replace('_', ' ', $member['designation'])); ?></p>
                            </article>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </section>

            <section class="topics-section" aria-label="Topics we cover">
                <div class="section-header topics-header">
                    <br>
                    <h1 style="text-align: center; font-size: 70px; color: #0a3d62; font-weight: bold; letter-spacing: -0.02em; font-family: 'Oswald', 'Segoe UI', sans-serif; margin-bottom: 16px;">Topics We Cover
                    </h1>
        
                    <br>
                </div>

                <!-- Coding Skills - 6 cards -->
                <div class="topic-category">
                    <h2 class="category-title" style="font-size: 30px;">Coding Skills</h2>
                    <br>
                    <div class="topics-grid topics-grid-6">
                        <article class="topic-card">
                            <div class="topic-icon">
                                <img src="https://www.bit2bytekuet.com/_next/image?url=https%3A%2F%2Fimg.icons8.com%2F%3Fsize%3D100%26id%3D40670%26format%3Dpng%26color%3D6b7280&w=32&q=75" alt="C Programming icon" />
                            </div>
                            <h3>C Programming</h3>
                            <p>Foundation of modern programming, known for efficiency and control over hardware.</p>
                        </article>

                        <article class="topic-card">
                            <div class="topic-icon">
                                <img src="https://www.bit2bytekuet.com/_next/image?url=https%3A%2F%2Fimg.icons8.com%2F%3Fsize%3D100%26id%3DFRRACRKRsw2s%26format%3Dpng%26color%3D6b7280&w=32&q=75" alt="Java icon" />
                            </div>
                            <h3>Java</h3>
                            <p>A versatile language widely used in web, mobile, and enterprise applications.</p>
                        </article>

                        <article class="topic-card">
                            <div class="topic-icon">
                                <img src="https://www.bit2bytekuet.com/_next/image?url=https%3A%2F%2Fimg.icons8.com%2F%3Fsize%3D100%26id%3DPXTY4q2Sq2lG%26format%3Dpng%26color%3D6b7280&w=32&q=75" alt="JavaScript icon" />
                            </div>
                            <h3>JavaScript</h3>
                            <p>The language of the web, enabling interactive and dynamic user experiences.</p>
                        </article>

                        <article class="topic-card">
                            <div class="topic-icon">
                                <img src="https://www.bit2bytekuet.com/_next/image?url=https%3A%2F%2Fimg.icons8.com%2F%3Fsize%3D100%26id%3DD8fI0PGNpq8i%26format%3Dpng%26color%3D6b7280&w=32&q=75" alt="OOP icon" />
                            </div>
                            <h3>Object-Oriented Programming (OOP)</h3>
                            <p>A programming paradigm that organizes code using objects and classes for better structure.</p>
                        </article>

                        <article class="topic-card">
                            <div class="topic-icon">
                                <img src="https://www.bit2bytekuet.com/_next/image?url=https%3A%2F%2Fimg.icons8.com%2F%3Fsize%3D100%26id%3DAJ0yU3DEYW2j%26format%3Dpng%26color%3D6b7280&w=32&q=75" alt="DSA icon" />
                            </div>
                            <h3>Data Structures & Algorithms (DSA)</h3>
                            <p>The core of problem-solving in programming, essential for efficient data handling.</p>
                        </article>

                        <article class="topic-card">
                            <div class="topic-icon">
                                <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQ25kJZLunlPCLb8ltGlp-Qv3gjfEJpnsKqZg&s" />
                            </div>
                            <h3>Python</h3>
                            <p>A high-level, interpreted language known for its simplicity and readability.</p>
                        </article>
                    </div>
                </div>

                <!-- Core Development Skills - 4 cards -->
                <div class="topic-category">
                    <br>
                    <h3 class="category-title" style="font-size: 30px;">Core Development Skills</h3>
                    <div class="topics-grid topics-grid-4">
                        <article class="topic-card">
                            <div class="topic-icon">
                                <img src="https://www.bit2bytekuet.com/_next/image?url=https%3A%2F%2Fimg.icons8.com%2F%3Fsize%3D100%26id%3D40670%26format%3Dpng%26color%3D6b7280&w=32&q=75" alt="C Programming icon" />
                            </div>
                            <h3>C Programming</h3>
                            <p>Foundation of modern programming, known for efficiency and control over hardware.</p>
                        </article>

                        <article class="topic-card">
                            <div class="topic-icon">
                                <img src="https://www.bit2bytekuet.com/_next/image?url=https%3A%2F%2Fimg.icons8.com%2F%3Fsize%3D100%26id%3DFRRACRKRsw2s%26format%3Dpng%26color%3D6b7280&w=32&q=75" alt="Java icon" />
                            </div>
                            <h3>Java</h3>
                            <p>A versatile language widely used in web, mobile, and enterprise applications.</p>
                        </article>

                        <article class="topic-card">
                            <div class="topic-icon">
                                <img src="https://www.bit2bytekuet.com/_next/image?url=https%3A%2F%2Fimg.icons8.com%2F%3Fsize%3D100%26id%3DD8fI0PGNpq8i%26format%3Dpng%26color%3D6b7280&w=32&q=75" alt="OOP icon" />
                            </div>
                            <h3>Object-Oriented Programming (OOP)</h3>
                            <p>A programming paradigm that organizes code using objects and classes for better structure.</p>
                        </article>

                        <article class="topic-card">
                            <div class="topic-icon">
                                <img src="https://www.bit2bytekuet.com/_next/image?url=https%3A%2F%2Fimg.icons8.com%2F%3Fsize%3D100%26id%3DAJ0yU3DEYW2j%26format%3Dpng%26color%3D6b7280&w=32&q=75" alt="DSA icon" />
                            </div>
                            <h3>Data Structures & Algorithms (DSA)</h3>
                            <p>The core of problem-solving in programming, essential for efficient data handling.</p>
                        </article>
                    </div>
                </div>

                <!-- Frontend Development - 3 cards -->
                 <br>
                <div class="topic-category">
                    <h3 class="category-title" style="font-size: 30px;">Frontend Development</h3>
                    <br>
                    <div class="topics-grid topics-grid-3">
                        <article class="topic-card">
                            <div class="topic-icon">
                                <img src="https://www.bit2bytekuet.com/_next/image?url=https%3A%2F%2Fimg.icons8.com%2F%3Fsize%3D100%26id%3D3XRl8fSRBNJL%26format%3Dpng%26color%3D6b7280&w=32&q=75" alt="HTML5 icon" />
                            </div>
                            <h3>HTML5</h3>
                            <p>The standard markup language for creating web pages and web applications.</p>
                        </article>

                        <article class="topic-card">
                            <div class="topic-icon">
                                <img src="https://www.bit2bytekuet.com/_next/image?url=https%3A%2F%2Fimg.icons8.com%2F%3Fsize%3D100%26id%3Ddsz6kVD9h7%26format%3Dpng%26color%3D6b7280&w=32&q=75" alt="CSS3 icon" />
                            </div>
                            <h3>CSS3</h3>
                            <p>Style sheet language used for describing the presentation of a document written in HTML.</p>
                        </article>

                        <article class="topic-card">
                            <div class="topic-icon">
                                <img src="https://www.bit2bytekuet.com/_next/image?url=https%3A%2F%2Fimg.icons8.com%2F%3Fsize%3D100%26id%3DPXTY4q2Sq2lG%26format%3Dpng%26color%3D6b7280&w=32&q=75" alt="JavaScript icon" />
                            </div>
                            <h3>JavaScript</h3>
                            <p>The language of the web, enabling interactive and dynamic user experiences.</p>
                        </article>
                    </div>
                </div>

                <!-- Mobile App Development - 2 cards -->
                <div class="topic-category">
                    <br>
                    <h3 class="category-title" style="font-size: 30px;">Mobile App Development</h3>
                    <br>
                    <div class="topics-grid topics-grid-2">
                        <article class="topic-card">
                            <div class="topic-icon">
                                <img src="https://www.bit2bytekuet.com/_next/image?url=https%3A%2F%2Fimg.icons8.com%2F%3Fsize%3D100%26id%3DFRRACRKRsw2s%26format%3Dpng%26color%3D6b7280&w=32&q=75" alt="Android icon" />
                            </div>
                            <h3>Android Development</h3>
                            <p>Build native mobile applications for the world's most popular mobile platform.</p>
                        </article>

                        <article class="topic-card">
                            <div class="topic-icon">
                                <img src="https://www.bit2bytekuet.com/_next/image?url=https%3A%2F%2Fimg.icons8.com%2F%3Fsize%3D100%26id%3DPXTY4q2Sq2lG%26format%3Dpng%26color%3D6b7280&w=32&q=75" alt="React Native icon" />
                            </div>
                            <h3>Cross-Platform Development</h3>
                            <p>Create mobile apps that work on both iOS and Android with a single codebase.</p>
                        </article>
                    </div>
                </div>

                <!-- Backend Development - 2 cards -->
                <div class="topic-category">
                    <br>
                    <h3 class="category-title" style="font-size: 30px;">Backend Development</h3>
                    <br>
                    <div class="topics-grid topics-grid-2">
                        <article class="topic-card">
                            <div class="topic-icon">
                                <img src="https://www.bit2bytekuet.com/_next/image?url=https%3A%2F%2Fimg.icons8.com%2F%3Fsize%3D100%26id%3DFRRACRKRsw2s%26format%3Dpng%26color%3D6b7280&w=32&q=75" alt="Node.js icon" />
                            </div>
                            <h3>Node.js & Express</h3>
                            <p>Build scalable server-side applications using JavaScript runtime environment.</p>
                        </article>

                        <article class="topic-card">
                            <div class="topic-icon">
                                <img src="https://www.bit2bytekuet.com/_next/image?url=https%3A%2F%2Fimg.icons8.com%2F%3Fsize%3D100%26id%3D40670%26format%3Dpng%26color%3D6b7280&w=32&q=75" alt="Database icon" />
                            </div>
                            <h3>Database Management</h3>
                            <p>Design and manage databases for efficient data storage and retrieval.</p>
                        </article>
                    </div>
                </div>
            </section>

        </main>

        <footer>
            <div class="footer-top">

                <!-- Logo -->
                <div class="footer-logo">
                    <img src="https://www.bit2bytekuet.com/_next/image?url=%2F_next%2Fstatic%2Fmedia%2Fmain-logo.c7a29f9e.png&w=128&q=75" alt="Bit2Byte Logo" />
                </div>

                <!-- About Us -->
                <div class="footer-col">
                    <h4>About Us</h4>
                    <ul>
                        <li><a href="#">About</a></li>
                        <li><a href="#">Syllabus</a></li>
                        <li><a href="#">Blogs</a></li>
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

    <script src="about.js"></script>
</body>
</html>
<?php $conn->close(); ?>