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
            <button class="login-btn" onclick="window.location.href='login.php'">Login</button>
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
                            <p>A programming paradigm that organizes code using objects and classes.</p>
                        </article>

                        <article class="topic-card">
                            <div class="topic-icon">
                                <img src="https://www.bit2bytekuet.com/_next/image?url=https%3A%2F%2Fimg.icons8.com%2F%3Fsize%3D100%26id%3DAJ0yU3DEYW2j%26format%3Dpng%26color%3D6b7280&w=32&q=75" alt="DSA icon" />
                            </div>
                            <h3>Data Structures & Algorithms (DSA)</h3>
                            <p>The core of problem-solving in programming, essential data handling.</p>
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
                                <img src="https://www.bit2bytekuet.com/_next/image?url=https%3A%2F%2Fimg.icons8.com%2F%3Fsize%3D100%26id%3D20906%26format%3Dpng%26color%3D6b7280&w=32&q=75" />
                            </div>
                            <h3>Git & GitHub</h3>
                            <p>Essential version control tools for collaboration and code management..</p>
                        </article>

                        <article class="topic-card">
                            <div class="topic-icon">
                                <img src="https://www.bit2bytekuet.com/_next/image?url=https%3A%2F%2Fimg.icons8.com%2F%3Fsize%3D100%26id%3D6S1Y1c6uojWA%26format%3Dpng%26color%3D6b7280&w=32&q=75"  />
                            </div>
                            <h3>Design Patterns</h3>
                            <p>Reusable solutions to common problems in software design, improving code maintainability.</p>
                        </article>

                        <article class="topic-card">
                            <div class="topic-icon">
                                <img src="https://www.bit2bytekuet.com/_next/image?url=https%3A%2F%2Fimg.icons8.com%2F%3Fsize%3D100%26id%3DTG3wNqGOHaIJ%26format%3Dpng%26color%3D6b7280&w=32&q=75" />
                            </div>
                            <h3>Debugging</h3>
                            <p>The art and science of identifying and resolving errors in code</p>
                        </article>

                        <article class="topic-card">
                            <div class="topic-icon">
                                <img src="https://www.bit2bytekuet.com/_next/image?url=https%3A%2F%2Fimg.icons8.com%2F%3Fsize%3D100%26id%3D11400%26format%3Dpng%26color%3D6b7280&w=32&q=75" />
                            </div>
                            <h3>Databases</h3>
                            <p>Systems for storing, managing, and retrieving data efficientlyent data handling.</p>
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
                                <img src="https://www.bit2bytekuet.com/_next/image?url=https%3A%2F%2Fimg.icons8.com%2F%3Fsize%3D100%26id%3D35989%26format%3Dpng%26color%3D6b7280&w=32&q=75" alt="HTML5 icon" />
                            </div>
                            <h3>React Js</h3>
                            <p>React.js is a JavaScript library for building interactive user interfaces using reusable components.</p>
                        </article>

                        <article class="topic-card">
                            <div class="topic-icon">
                                <img src="https://www.bit2bytekuet.com/_next/image?url=https%3A%2F%2Fimg.icons8.com%2F%3Fsize%3D100%26id%3D66746%26format%3Dpng%26color%3D6b7280&w=32&q=75" alt="CSS3 icon" />
                            </div>
                            <h3>Next.Js</h3>
                            <p>Next.js is a React-based framework for building fast, SEO-friendly full-stack web applications with built-in routing and server-side rendering.</p>
                        </article>

                        <article class="topic-card">
                            <div class="topic-icon">
                                <img src=data:image/png;base64,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 alt="JavaScript icon" />
                            </div>
                            <h3>Node.Js</h3>
                            <p>Node.js is a JavaScript runtime environment that allows developers to run JavaScript on the server side to build fast and scalable backend applications.</p>
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
                                <img src="https://www.bit2bytekuet.com/_next/image?url=https%3A%2F%2Fimg.icons8.com%2F%3Fsize%3D100%26id%3D17836%26format%3Dpng%26color%3D6b7280&w=32&q=75" alt="Android icon" />
                            </div>
                            <h3>Android Development</h3>
                            <p>Building mobile applications for the Android platform using Java and Kotlin.</p>
                        </article>

                        <article class="topic-card">
                            <div class="topic-icon">
                                <img src="https://www.bit2bytekuet.com/_next/image?url=https%3A%2F%2Fimg.icons8.com%2F%3Fsize%3D100%26id%3D7I3BjCqe9rjG%26format%3Dpng%26color%3D6b7280&w=32&q=75" alt="Flutter icon" />
                            </div>
                            <h3>Flutter</h3>
                            <p>A cross-platform toolkit for creating natively compiled applications for mobile, web, and desktop</p>
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
                                <img src="https://www.bit2bytekuet.com/_next/image?url=https%3A%2F%2Fimg.icons8.com%2F%3Fsize%3D100%26id%3DUG5EO81XNkPs%26format%3Dpng%26color%3D6b7280&w=32&q=75" alt="Node.js icon" />
                            </div>
                            <h3>Laravel</h3>
                            <p>A PHP framework for building modern web applications with clean, elegant syntax</p>
                        </article>

                        <article class="topic-card">
                            <div class="topic-icon">
                                <img src="https://www.bit2bytekuet.com/_next/image?url=https%3A%2F%2Fimg.icons8.com%2F%3Fsize%3D100%26id%3D90519%26format%3Dpng%26color%3D6b7280&w=32&q=75" alt="Database icon" />
                            </div>
                            <h3>Spring Boot</h3>
                            <p>A Java framework for building robust and scalable backend applications.</p>
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