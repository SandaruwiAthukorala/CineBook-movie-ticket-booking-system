<?php
require_once __DIR__ . '/includes/init.php';

$movieId = (int) ($_GET['id'] ?? 0);
$movieModel = new Movie();
$showtimeModel = new Showtime();
$movie = $movieModel->find($movieId);

if (!$movie) {
    set_flash('warning', 'Movie not found.');
    redirect('movies.php');
}

$pageTitle = $movie['title'];
$showtimes = $showtimeModel->upcomingByMovie($movieId);

include __DIR__ . '/includes/header.php';
?>

<section class="movie-detail-hero">
    <div class="container">
        <div class="row g-4 align-items-center">
            <div class="col-md-4 col-lg-3">
                <img class="detail-poster" src="<?= e(poster_src($movie['poster'])) ?>" alt="<?= e($movie['title']) ?> poster">
            </div>
            <div class="col-md-8 col-lg-9">
                <span class="badge text-bg-warning mb-3"><?= e($movie['status']) ?></span>
                <h1><?= e($movie['title']) ?></h1>
                <div class="movie-meta">
                    <span><i class="bi bi-film"></i> <?= e($movie['genre']) ?></span>
                    <span><i class="bi bi-translate"></i> <?= e($movie['language']) ?></span>
                    <span><i class="bi bi-clock"></i> <?= e($movie['duration']) ?> min</span>
                </div>
                <p><?= nl2br(e($movie['description'])) ?></p>
            </div>
        </div>
    </div>
</section>

<section class="section-pad">
    <div class="container">
        <div class="section-heading">
            <div>
                <span class="eyebrow">Available sessions</span>
                <h2>Choose a Showtime</h2>
            </div>
        </div>

        <div class="row g-3">
            <?php foreach ($showtimes as $showtime): ?>
                <div class="col-md-6 col-xl-4">
                    <article class="showtime-card">
                        <div>
                            <span class="show-date"><?= e(date('M d, Y', strtotime($showtime['show_date']))) ?></span>
                            <h3><?= e(date('h:i A', strtotime($showtime['show_time']))) ?></h3>
                        </div>
                        <p><i class="bi bi-geo-alt"></i> <?= e($showtime['theatre_name']) ?>, <?= e($showtime['location']) ?></p>
                        <p><i class="bi bi-cash-stack"></i> <?= e(format_money($showtime['price'])) ?> per seat</p>
                        <a class="btn btn-dark w-100" href="<?= e(url('book-ticket.php?showtime_id=' . $showtime['showtime_id'])) ?>">Select Seats</a>
                    </article>
                </div>
            <?php endforeach; ?>
            <?php if (!$showtimes): ?>
                <div class="col-12">
                    <div class="empty-state">No upcoming showtimes for this movie.</div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
