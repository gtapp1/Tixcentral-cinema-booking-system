# TixCentral – Deployment Guide

A self-contained cinema booking site built with pure PHP, MySQL, Bootstrap, and vanilla JS.

Overview
- Default dark theme with Netflix-inspired styling and a light theme toggle.
- Full booking flow: select showtime, interactive seat map, simulated payments (GCash, Maya, Card), confirmation, and booking history.
- Database name: tixcentral2

Requirements
- PHP 8.0+ with mysqli enabled
- MySQL 5.7+/MariaDB 10+ 
- Apache (or any web server that can run PHP)
- Internet access for Bootstrap and Google Fonts CDNs

Quick start (Windows with XAMPP)
1) Install and start services
- Install XAMPP.
- Open XAMPP Control Panel and start Apache and MySQL.

2) Place project files
- Copy the entire project folder to:
  c:\xampp\htdocs\draft2
- Keep the folder name as draft2 (paths in the app use /draft2/).

3) Create database and seed data
- Open http://localhost/phpmyadmin
- Click Import, choose c:\xampp\htdocs\draft2\schema.sql, and run. This creates the tixcentral2 database with tables and seed data.

4) Configure DB credentials (if needed)
- Default XAMPP credentials are root with empty password.
- If different, update c:\xampp\htdocs\draft2\config.php:
  $DB_HOST='127.0.0.1'; $DB_USER='root'; $DB_PASS=''; $DB_NAME='tixcentral2';

5) Add poster and hero images
- Replace the placeholder images with your own to match the Netflix-like look:
  - assets/images/posters/crimson_empire.jpg
  - assets/images/posters/midnight_heist.jpg
  - assets/images/posters/echoes_of_tomorrow.jpg
  - assets/images/hero/crimson_empire_hero.jpg
  - assets/images/hero/midnight_heist_hero.jpg
  - assets/images/hero/echoes_of_tomorrow_hero.jpg

6) Run the site
- Open http://localhost/draft2
- Register a user, then book tickets.

macOS/Linux (Apache + PHP + MySQL)
1) Install stack
- macOS: Install Homebrew, then brew install httpd php mysql
- Linux (Ubuntu/Debian): sudo apt install apache2 php php-mysqli mysql-server

2) Place project files
- Copy the folder to your web root:
  - macOS (Homebrew): /opt/homebrew/var/www/draft2 or /usr/local/var/www/draft2
  - Ubuntu/Debian: /var/www/html/draft2
- Ensure the folder name is draft2.

3) Set permissions (Linux)
- sudo chown -R www-data:www-data /var/www/html/draft2
- sudo find /var/www/html/draft2 -type d -exec chmod 755 {} \;
- sudo find /var/www/html/draft2 -type f -exec chmod 644 {} \;

4) Start services
- MySQL: sudo service mysql start
- Apache: sudo service apache2 start (or httpd start)

5) Create database and seed data
- Using phpMyAdmin, import schema.sql
  or CLI:
  mysql -u root -p < /var/www/html/draft2/schema.sql

6) Configure DB credentials
- Edit draft2/config.php with your MySQL user/password.

7) Open the site
- Visit http://localhost/draft2

How to use
- Register an account: Login -> Sign Up.
- Browse Now Showing, pick a schedule, select seats on the seat map.
- Proceed to Payment:
  - GCash/Maya: enter mobile number (simulated).
  - Card: enter cardholder name, number, expiry (MM/YY), and CVV (simulated).
- Confirm to receive a booking reference.
- View your bookings in Booking History.

Customization
- Featured hero: Set movies.featured=1 in the database to change the homepage hero.
- Prices: schedules.price per showtime.
- Styling: assets/css/styles.css (colors, sizes, effects).
- Images: Replace files under assets/images as listed above.

Common issues
- 404 or broken links:
  - Ensure the folder is exactly /draft2. If you rename the folder, update absolute paths “/draft2/” throughout PHP files.
- Database connection failed:
  - Check MySQL is running, credentials in config.php, and that schema.sql was imported.
- Seat map not updating:
  - Ensure Apache can reach http://localhost/draft2/api/booked_seats.php
  - Confirm bookings table has CONFIRMED entries after payment.
- Ports in use:
  - Apache: change the Listen port in httpd.conf or free port 80.
  - MySQL: ensure port 3306 is free.

Reset database
- Re-import schema.sql using phpMyAdmin or:
  mysql -u root -p < path/to/schema.sql

Security note
- Payments are simulated for demo purposes. Do not use for production.
