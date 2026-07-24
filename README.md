# CineBook - Movie Ticket Booking System

CineBook is a PHP and MySQL web application for browsing movies, selecting theatres and showtimes, booking seats, simulating payment, and managing cinema data through an admin panel.

## Technology Stack

- PHP 8 with Object-Oriented Programming
- MySQL
- HTML5, CSS3, JavaScript
- Bootstrap 5 and Bootstrap Icons
- XAMPP or WAMP

## Main Features

- User registration and login
- Movie catalogue with search and filters
- Movie details with available showtimes
- Seat selection with booked-seat blocking
- Payment simulation
- Booking history and cancellation
- Admin dashboard
- Admin CRUD for movies, theatres, and showtimes
- Admin booking management
- Responsive interface for desktop, tablet, and mobile

## Folder Structure

```text
MovieTicketBooking/
├── admin/
│   ├── dashboard.php
│   ├── movies.php
│   ├── theatres.php
│   ├── showtimes.php
│   └── bookings.php
├── assets/
│   ├── css/style.css
│   ├── js/app.js
│   └── images/poster-placeholder.svg
├── classes/
│   ├── Booking.php
│   ├── Database.php
│   ├── Movie.php
│   ├── Payment.php
│   ├── Showtime.php
│   ├── Theatre.php
│   └── User.php
├── config/config.php
├── docs/ER_DIAGRAM.mmd
├── includes/
│   ├── footer.php
│   ├── header.php
│   ├── init.php
│   └── navbar.php
├── uploads/
├── book-ticket.php
├── database.sql
├── index.php
├── login.php
├── logout.php
├── movie-details.php
├── movies.php
├── my-bookings.php
├── payment.php
├── PROJECT_REPORT.md
└── register.php
```

## Setup Instructions

1. Copy the `MovieTicketBooking` folder into your XAMPP `htdocs` folder.
2. Start Apache and MySQL from the XAMPP control panel.
3. Open phpMyAdmin and import `database.sql`.
4. Check `config/config.php` and update the database username or password if needed.
5. Open `http://localhost/MovieTicketBooking/` in your browser.

## Demo Accounts

Admin:

```text
Email: admin@cinebook.local
Password: admin123
```

Customer:

```text
Email: user@cinebook.local
Password: user123
```

## Notes

- The payment page is a simulation for academic use only.
- Poster fields can store a local path such as `assets/images/poster-placeholder.svg` or an external image URL.
- The seed showtimes use dates in August 2026. If you use the project after those dates, add new showtimes from the admin panel.
