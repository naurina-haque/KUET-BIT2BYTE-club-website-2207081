# BIT2BYTE Club Website

This is a website for **BIT2BYTE — Software Research and Development Community** at Khulna University of Engineering and Technology (KUET).

Built as part of a Web Programming Lab project.

---

## Features

### Public Frontend
- **Home** — Club overview, hero section, highlights
- **Events** — Browse club events
- **Blog** — Read posts and articles published by club admin
- **About** — Club mission, Club members, and contact information

### Admin Panel
- Secure login with session-based authentication
- **Member Management** — Add, update, and delete members
- **Event Management** — Create, edit, and remove events (with image upload)
- **Blog Management** — Write, publish, and manage blog posts

---

## Tech Stack

| Layer      | Technology          |
|------------|---------------------|
| Backend    | PHP                 |
| Database   | MySQL               |
| Frontend   | HTML, CSS, JavaScript |
| Server     | Apache (XAMPP) |

---

## Project Structure

```
/
├── home.html                # Public home page
├── event.html               # Public events page
├── about.html               # About page
├── login.html / login.php   # Admin login
├── admin.html               # Admin dashboard
├── admin_auth.php           # Session auth check
├── logout.php               # Logout handler
│
├── members.php              # Fetch members (API)
├── add_member.php           # Add new member
├── update_member.php        # Edit member
├── delete_member.php        # Delete member
│
├── events.php               # Fetch events (API)
├── add_event.php            # Add new event
├── update_event.php         # Edit event
├── delete_event.php         # Delete event
│
├── blog_list.php            # Public blog listing
├── blog_view.php            # Single blog post view
├── admin_create_post.php    # Create blog post (admin)
├── admin_manage_posts.php   # Manage blog posts (admin)
│
├── db.php                   # Database connection
├── database.sql             # SQL schema & seed data
│
├── style.css / components.css / admin.css / ...
├── img/                     # Static images
└── uploads/                 # User-uploaded files
```

---

## Local Setup

### Prerequisites
- XAMPP or WAMP (PHP 7.4+ and MySQL)
- A browser

### Steps

1. **Clone the repository**
   ```bash
   git clone https://github.com/naurina-haque/KUET-BIT2BYTE-club-website-2207081.git
   ```

2. **Move to your server root**
   - Copy the folder to `htdocs/` (XAMPP) or `www/` (WAMP)

3. **Import the database**
   - Open `phpMyAdmin`
   - Create a new database (e.g., `bit2byte`)
   - Import `database.sql`

4. **Configure the database connection**
   - Open `db.php`
   - Update credentials:
     ```php
     $host = "localhost";
     $user = "root";
     $password = "your_password";
     $dbname = "bit2byte";
     ```

5. **Run the project**
   - Start Apache and MySQL from XAMPP/WAMP
   - Visit: `http://localhost/KUET-BIT2BYTE-club-website-2207081/home.html`
   - Admin panel: `http://localhost/KUET-BIT2BYTE-club-website-2207081/login.html`

---


## Author

**Naurina Haque**
Student, CSE — KUET
Roll: 2207081

---
