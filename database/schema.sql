DROP DATABASE IF EXISTS tixcentral2;
CREATE DATABASE tixcentral2 CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE tixcentral2;

-- Users
CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(120) NOT NULL,
  email VARCHAR(190) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Movies
CREATE TABLE movies (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(200) NOT NULL,
  synopsis TEXT,
  runtime_minutes INT,
  genre VARCHAR(120),
  poster_path VARCHAR(255),
  hero_image_path VARCHAR(255),
  featured TINYINT(1) DEFAULT 0
) ENGINE=InnoDB;

-- Branches
CREATE TABLE branches (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(200) NOT NULL,
  address VARCHAR(255),
  city VARCHAR(120)
) ENGINE=InnoDB;

-- Schedules
CREATE TABLE schedules (
  id INT AUTO_INCREMENT PRIMARY KEY,
  movie_id INT NOT NULL,
  branch_id INT NOT NULL,
  show_datetime DATETIME NOT NULL,
  price DECIMAL(10,2) NOT NULL DEFAULT 250.00,
  FOREIGN KEY (movie_id) REFERENCES movies(id) ON DELETE CASCADE,
  FOREIGN KEY (branch_id) REFERENCES branches(id) ON DELETE CASCADE,
  INDEX(movie_id),
  INDEX(branch_id),
  INDEX(show_datetime)
) ENGINE=InnoDB;

-- Seats (global template seats for an auditorium layout A-H x 1-12)
CREATE TABLE seats (
  id INT AUTO_INCREMENT PRIMARY KEY,
  label VARCHAR(10) NOT NULL,
  row_char CHAR(1) NOT NULL,
  col_number INT NOT NULL,
  UNIQUE KEY (label)
) ENGINE=InnoDB;

-- Bookings
CREATE TABLE bookings (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  schedule_id INT NOT NULL,
  reference VARCHAR(20) NOT NULL UNIQUE,
  total_amount DECIMAL(10,2) NOT NULL,
  status ENUM('PENDING','CONFIRMED','CANCELLED') NOT NULL DEFAULT 'PENDING',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (schedule_id) REFERENCES schedules(id) ON DELETE CASCADE,
  INDEX(user_id),
  INDEX(schedule_id)
) ENGINE=InnoDB;

-- Booking to Seats mapping (many-to-many)
CREATE TABLE booking_seats (
  booking_id INT NOT NULL,
  seat_id INT NOT NULL,
  PRIMARY KEY (booking_id, seat_id),
  FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE CASCADE,
  FOREIGN KEY (seat_id) REFERENCES seats(id) ON DELETE RESTRICT
) ENGINE=InnoDB;

-- Payments
CREATE TABLE payments (
  id INT AUTO_INCREMENT PRIMARY KEY,
  booking_id INT NOT NULL,
  method ENUM('GCASH','MAYA','CARD') NOT NULL,
  details_masked VARCHAR(255),
  amount DECIMAL(10,2) NOT NULL,
  status ENUM('PAID','FAILED') NOT NULL DEFAULT 'PAID',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE CASCADE,
  INDEX(booking_id)
) ENGINE=InnoDB;

-- Seed Movies
INSERT INTO movies (title, synopsis, runtime_minutes, genre, poster_path, hero_image_path, featured) VALUES
('Crimson Empire', 'A gripping saga of power, betrayal, and redemption in a futuristic empire.', 128, 'Sci-Fi, Drama', 'assets/images/posters/crimson_empire.jpg', 'assets/images/hero/crimson_empire_hero.jpg', 1),
('Midnight Heist', 'A team of misfits attempt the ultimate casino heist under the cover of night.', 112, 'Action, Thriller', 'assets/images/posters/midnight_heist.jpg', 'assets/images/hero/midnight_heist_hero.jpg', 0),
('Echoes of Tomorrow', 'Time-bending romance where choices ripple through past and future.', 105, 'Romance, Sci-Fi', 'assets/images/posters/echoes_of_tomorrow.jpg', 'assets/images/hero/echoes_of_tomorrow_hero.jpg', 0);

-- Seed Branches
INSERT INTO branches (name, address, city) VALUES
('TixCentral Downtown', '123 Main St', 'Metro City'),
('TixCentral Uptown', '456 High Ave', 'Metro City');

-- Seed Schedules (next few days)
INSERT INTO schedules (movie_id, branch_id, show_datetime, price)
SELECT m.id, b.id, DATE_ADD(CURDATE(), INTERVAL d DAY) + INTERVAL t HOUR, 
CASE WHEN m.title='Crimson Empire' THEN 300.00 ELSE 250.00 END
FROM movies m
CROSS JOIN branches b
CROSS JOIN (SELECT 0 d UNION ALL SELECT 1 UNION ALL SELECT 2) days
CROSS JOIN (SELECT 13 t UNION ALL SELECT 16 UNION ALL SELECT 19 UNION ALL SELECT 21) times;

-- Seed Seats A-H x 1-12
INSERT INTO seats (label, row_char, col_number) VALUES
('A1','A',1),('A2','A',2),('A3','A',3),('A4','A',4),('A5','A',5),('A6','A',6),('A7','A',7),('A8','A',8),('A9','A',9),('A10','A',10),('A11','A',11),('A12','A',12),
('B1','B',1),('B2','B',2),('B3','B',3),('B4','B',4),('B5','B',5),('B6','B',6),('B7','B',7),('B8','B',8),('B9','B',9),('B10','B',10),('B11','B',11),('B12','B',12),
('C1','C',1),('C2','C',2),('C3','C',3),('C4','C',4),('C5','C',5),('C6','C',6),('C7','C',7),('C8','C',8),('C9','C',9),('C10','C',10),('C11','C',11),('C12','C',12),
('D1','D',1),('D2','D',2),('D3','D',3),('D4','D',4),('D5','D',5),('D6','D',6),('D7','D',7),('D8','D',8),('D9','D',9),('D10','D',10),('D11','D',11),('D12','D',12),
('E1','E',1),('E2','E',2),('E3','E',3),('E4','E',4),('E5','E',5),('E6','E',6),('E7','E',7),('E8','E',8),('E9','E',9),('E10','E',10),('E11','E',11),('E12','E',12),
('F1','F',1),('F2','F',2),('F3','F',3),('F4','F',4),('F5','F',5),('F6','F',6),('F7','F',7),('F8','F',8),('F9','F',9),('F10','F',10),('F11','F',11),('F12','F',12),
('G1','G',1),('G2','G',2),('G3','G',3),('G4','G',4),('G5','G',5),('G6','G',6),('G7','G',7),('G8','G',8),('G9','G',9),('G10','G',10),('G11','G',11),('G12','G',12),
('H1','H',1),('H2','H',2),('H3','H',3),('H4','H',4),('H5','H',5),('H6','H',6),('H7','H',7),('H8','H',8),('H9','H',9),('H10','H',10),('H11','H',11),('H12','H',12);
