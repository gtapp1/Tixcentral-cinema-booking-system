# TixCentral – Windows Deployment Guide (XAMPP)

A self-contained cinema booking site built with pure PHP, MySQL, Bootstrap, and vanilla JS. Dark, Netflix-inspired interface with Bebas Neue headings and smooth interactions.

Key features
- Homepage
  - Full-width hero featuring the “featured” movie.
  - Book Now anchors directly to the same movie card in Now Showing and highlights it.
  - Horizontal Now Showing carousel.
- Now Showing
  - Compact poster grid with consistent sizing.
  - Synopsis opens in an overlay via a button (hovers away to close).
  - Branch headers are buttons; clicking reveals that branch’s specific showtimes in an overlay.
  - 4x4 time grid layout for easy scanning.
- Booking
  - Select movie/branch/showtime or deep-link from Now Showing.
  - Seat selection with cinematic accents:
    - Red aisle lines and per-row “guide” lights with labels (A–H).
    - Already-booked seats are disabled.
- Payment
  - GCash, Maya, or Card (simulated, masked details).
  - Cancel button to return to seat selection.
- Confirmation and History
  - Confirmation page shows all booking details and reference.
  - Booking History lists past bookings.
  - Cancel Booking for eligible future, confirmed bookings; redirects to a structured “Booking Cancelled” page with a refund message.
- Footer
  - Branch locations linked to Google Maps, plus email and contact number.

Requirements (Windows)
- XAMPP with:
  - PHP 8.0+ (mysqli enabled)
  - MySQL 5.7+/MariaDB 10+
  - Apache HTTP Server
- Internet access for Bootstrap CDN and Google Fonts (Bebas Neue)

Quick deploy (Windows with XAMPP)
1) Install and start services
- Install XAMPP from apachefriends.org.
- Open XAMPP Control Panel and Start Apache and MySQL.

2) Get the project into the web root
- Clone the GitHub or download ZIP, then place into:
  c:\xampp\htdocs\draft2
- Important: Keep the folder name draft2 (app uses absolute paths like /draft2/).

3) Create and seed the database
- Open http://localhost/phpmyadmin
- Click Import and choose:
  c:\xampp\htdocs\draft2\schema.sql
- This creates database tixcentral2 with required tables and seed data.

4) Configure DB credentials (only if you changed defaults)
- Default XAMPP credentials: user root, empty password.
- If different, edit:
  c:\xampp\htdocs\draft2\config.php
  $DB_HOST='127.0.0.1'; $DB_USER='root'; $DB_PASS=''; $DB_NAME='tixcentral2';

5) Add/replace assets
- Brand logo (required by navbar):
  assets/images/logo.svg
- Replace posters and hero images to suit your branding:
  - assets/images/posters/sample1.jpg
  - assets/images/posters/sample2.jpg
  - assets/images/posters/sample3.jpg
  - assets/images/hero/sample_hero1.jpg
  - assets/images/hero/sample_hero2.jpg
  - assets/images/hero/sample_hero3.jpg

6) Run the site
- Visit http://localhost/draft2
- Register an account and try booking seats.

How to use
- Register -> Login.
- Now Showing: open Synopsis or a Branch to view showtimes; click a time to start booking.
- Book: select seats; disabled seats are already booked.
- Payment: choose GCash/Maya/Card; submit to confirm; or Cancel to return to seats.
- History: view bookings; cancel eligible future bookings to free seats and see the structured cancellation page.

Customization

Featured hero: Set movies.featured=1 in the database to change the homepage hero.
Prices: schedules.price per showtime.
Styling: assets/css/styles.css (colors, sizes, effects).
Images: Replace files under assets/images as listed above.