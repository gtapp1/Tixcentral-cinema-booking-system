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

Sample screenshots of the TixCentral Cinema Booking System.

<img width="1920" height="1817" alt="image" src="https://github.com/user-attachments/assets/a24df752-1f55-41af-90c0-61784dab0f04" />

<img width="1920" height="2317" alt="image" src="https://github.com/user-attachments/assets/5480f0ac-a492-4c95-8cc1-a512d08c6b47" />

<img width="1920" height="1122" alt="image" src="https://github.com/user-attachments/assets/86471dbc-07cc-47e4-8e40-6a5dda21bc4b" />
