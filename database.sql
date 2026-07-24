CREATE DATABASE IF NOT EXISTS movie_ticket_booking
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE movie_ticket_booking;

SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS payments;
DROP TABLE IF EXISTS bookings;
DROP TABLE IF EXISTS showtimes;
DROP TABLE IF EXISTS theatres;
DROP TABLE IF EXISTS movies;
DROP TABLE IF EXISTS users;
SET FOREIGN_KEY_CHECKS = 1;

CREATE TABLE users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(30) NOT NULL,
    role ENUM('admin', 'user') NOT NULL DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE movies (
    movie_id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(160) NOT NULL,
    genre VARCHAR(80) NOT NULL,
    language VARCHAR(80) NOT NULL,
    duration INT NOT NULL,
    description TEXT NOT NULL,
    poster VARCHAR(255),
    status ENUM('Now Showing', 'Coming Soon', 'Archived') NOT NULL DEFAULT 'Now Showing',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE theatres (
    theatre_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(120) NOT NULL,
    location VARCHAR(160) NOT NULL,
    total_seats INT NOT NULL,
    status ENUM('Active', 'Inactive') NOT NULL DEFAULT 'Active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE showtimes (
    showtime_id INT AUTO_INCREMENT PRIMARY KEY,
    movie_id INT NOT NULL,
    theatre_id INT NOT NULL,
    show_date DATE NOT NULL,
    show_time TIME NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    status ENUM('Open', 'Closed') NOT NULL DEFAULT 'Open',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_showtime_movie
        FOREIGN KEY (movie_id) REFERENCES movies(movie_id)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_showtime_theatre
        FOREIGN KEY (theatre_id) REFERENCES theatres(theatre_id)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT uq_showtime UNIQUE (movie_id, theatre_id, show_date, show_time)
) ENGINE=InnoDB;

CREATE TABLE bookings (
    booking_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    showtime_id INT NOT NULL,
    seat_numbers VARCHAR(255) NOT NULL,
    total_amount DECIMAL(10,2) NOT NULL,
    booking_status ENUM('Pending', 'Confirmed', 'Cancelled') NOT NULL DEFAULT 'Pending',
    booking_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_booking_user
        FOREIGN KEY (user_id) REFERENCES users(user_id)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_booking_showtime
        FOREIGN KEY (showtime_id) REFERENCES showtimes(showtime_id)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

CREATE TABLE payments (
    payment_id INT AUTO_INCREMENT PRIMARY KEY,
    booking_id INT NOT NULL UNIQUE,
    card_name VARCHAR(120) NOT NULL,
    payment_method VARCHAR(50) NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    payment_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('Completed', 'Failed') NOT NULL DEFAULT 'Completed',
    CONSTRAINT fk_payment_booking
        FOREIGN KEY (booking_id) REFERENCES bookings(booking_id)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

INSERT INTO users (name, email, password, phone, role) VALUES
('System Admin', 'admin@cinebook.local', '$2y$10$iIc448Vs3a5i79ZydFZiYuHiIe5th2XVFNi739WK/Faz9E8bQBZVy', '+94770000000', 'admin'),
('Demo Customer', 'user@cinebook.local', '$2y$10$uPdxt13vbZxXeYykvMRgg.98yKHXS77GhfTQJ0u9oNljX97PLVOF2', '+94771112233', 'user');

INSERT INTO movies (title, genre, language, duration, description, poster, status) VALUES
('Midnight Horizon', 'Adventure', 'English', 128, 'A rescue pilot crosses a stormy ocean to save a research team before sunrise.', 'assets/images/poster-placeholder.svg', 'Now Showing'),
('Colombo Lights', 'Drama', 'Sinhala', 116, 'A young musician follows one impossible performance through the busy heart of the city.', 'assets/images/poster-placeholder.svg', 'Now Showing'),
('Quantum Chase', 'Action', 'English', 142, 'Two rival agents race through parallel timelines to stop a stolen energy device.', 'assets/images/poster-placeholder.svg', 'Now Showing'),
('Laugh Track', 'Comedy', 'Tamil', 104, 'A radio host accidentally becomes the voice of a town-wide mystery.', 'assets/images/poster-placeholder.svg', 'Now Showing'),
('Ocean Archive', 'Documentary', 'English', 92, 'A cinematic journey through coral restoration, deep-sea mapping, and coastal communities.', 'assets/images/poster-placeholder.svg', 'Coming Soon');

INSERT INTO theatres (name, location, total_seats, status) VALUES
('Theatre One', 'Colombo City Centre', 80, 'Active'),
('Liberty Screen', 'Bambalapitiya', 100, 'Active'),
('Galaxy Hall', 'Nugegoda', 90, 'Active');

INSERT INTO showtimes (movie_id, theatre_id, show_date, show_time, price, status) VALUES
(1, 1, '2026-08-01', '10:30:00', 1200.00, 'Open'),
(1, 2, '2026-08-01', '18:30:00', 1500.00, 'Open'),
(2, 1, '2026-08-02', '14:00:00', 1100.00, 'Open'),
(2, 3, '2026-08-03', '19:00:00', 1300.00, 'Open'),
(3, 2, '2026-08-02', '20:30:00', 1600.00, 'Open'),
(3, 3, '2026-08-04', '16:15:00', 1450.00, 'Open'),
(4, 1, '2026-08-05', '11:00:00', 1000.00, 'Open'),
(4, 2, '2026-08-05', '17:45:00', 1250.00, 'Open');
