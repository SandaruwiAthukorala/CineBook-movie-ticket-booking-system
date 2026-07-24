<?php
require_once __DIR__ . '/includes/init.php';

$pageTitle = 'Home';
$movieModel = new Movie();
$showtimeModel = new Showtime();
$featuredMovies = $movieModel->featured(4);
$upcomingShowtimes = array_slice($showtimeModel->all(), 0, 5);

include __DIR__ . '/includes/header.php';
?>

<section class="hero-section">
    <div class="container hero-content">
        <div class="row align-items-center g-4">
            <div class="col-lg-7">
                <span class="hero-kicker">Online cinema reservation</span>
                <h1>Book the best seats before the lights go down.</h1>
                <p>
                    Browse movies, choose a theatre and showtime, reserve seats, and complete a simple payment simulation in a few steps.
                </p>
                <div class="d-flex flex-wrap gap-2">
                    <a class="btn btn-warning btn-lg" href="<?= e(url('movies.php')) ?>">
                        <i class="bi bi-ticket-perforated"></i> Book Tickets
                    </a>
                    <?php if (!is_logged_in()): ?>
                        <a class="btn btn-outline-light btn-lg" href="<?= e(url('register.php')) ?>">Create Account</a>
                    <?php endif; ?>
                </div>
            </div>
            <div class="col-lg-5">
                <div class="hero-poster-wall">
                    <?php foreach ($featuredMovies as $movie): ?>
                        <img src="<?= e(poster_src($movie['poster'])) ?>" alt="<?= e($movie['title']) ?> poster">
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="section-pad">
    <div class="container">
        <div class="section-heading">
            <div>
                <span class="eyebrow">Now showing</span>
                <h2>Featured Movies</h2>
            </div>
            <a href="<?= e(url('movies.php')) ?>" class="btn btn-outline-dark">View All</a>
        </div>

        <div class="row g-4">
            <?php foreach ($featuredMovies as $movie): ?>
                <div class="col-sm-6 col-lg-3">
                    <article class="movie-card h-100">
                        <img src="<?= e(poster_src($movie['poster'])) ?>" alt="<?= e($movie['title']) ?> poster">
                        <div class="movie-card-body">
                            <span class="badge text-bg-light"><?= e($movie['genre']) ?></span>
                            <h3><?= e($movie['title']) ?></h3>
                            <p><?= e($movie['language']) ?> &middot; <?= e($movie['duration']) ?> min</p>
                            <a class="btn btn-sm btn-dark w-100" href="<?= e(url('movie-details.php?id=' . $movie['movie_id'])) ?>">View Details</a>
                        </div>
                    </article>
                </div>
            <?php endforeach; ?>
            <?php if (!$featuredMovies): ?>
                <div class="col-12">
                    <div class="empty-state">No movies are available yet. Login as admin to add movies.</div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<section class="section-pad bg-soft">
    <div class="container">
        <div class="section-heading">
            <div>
                <span class="eyebrow">Schedule</span>
                <h2>Upcoming Showtimes</h2>
            </div>
        </div>

        <div class="table-responsive app-table-wrap">
            <table class="table align-middle app-table">
                <thead>
                    <tr>
                        <th>Movie</th>
                        <th>Theatre</th>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Price</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($upcomingShowtimes as $showtime): ?>
                        <tr>
                            <td class="fw-semibold"><?= e($showtime['movie_title']) ?></td>
                            <td><?= e($showtime['theatre_name']) ?>, <?= e($showtime['location']) ?></td>
                            <td><?= e(date('M d, Y', strtotime($showtime['show_date']))) ?></td>
                            <td><?= e(date('h:i A', strtotime($showtime['show_time']))) ?></td>
                            <td><?= e(format_money($showtime['price'])) ?></td>
                            <td class="text-end">
                                <a class="btn btn-sm btn-warning" href="<?= e(url('book-ticket.php?showtime_id=' . $showtime['showtime_id'])) ?>">Book</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (!$upcomingShowtimes): ?>
                        <tr><td colspan="6" class="text-center text-muted py-4">No showtimes scheduled.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
