<?php
require_once __DIR__ . '/includes/init.php';

$pageTitle = 'Movies';
$movieModel = new Movie();
$search = trim((string) ($_GET['search'] ?? ''));
$genre = trim((string) ($_GET['genre'] ?? ''));
$language = trim((string) ($_GET['language'] ?? ''));
$movies = $movieModel->all($search, $genre, $language, 'Now Showing');
$genres = $movieModel->genres();
$languages = $movieModel->languages();

include __DIR__ . '/includes/header.php';
?>

<section class="page-hero compact-hero">
    <div class="container">
        <span class="eyebrow">Movie catalogue</span>
        <h1>Find a film and reserve your seats.</h1>
    </div>
</section>

<section class="section-pad">
    <div class="container">
        <form class="filter-bar" method="get" action="<?= e(url('movies.php')) ?>">
            <div>
                <label class="form-label" for="search">Search</label>
                <input class="form-control" id="search" name="search" value="<?= e($search) ?>" placeholder="Movie title or keyword">
            </div>
            <div>
                <label class="form-label" for="genre">Genre</label>
                <select class="form-select" id="genre" name="genre">
                    <option value="">All genres</option>
                    <?php foreach ($genres as $item): ?>
                        <option value="<?= e($item) ?>" <?= selected($genre, $item) ?>><?= e($item) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label class="form-label" for="language">Language</label>
                <select class="form-select" id="language" name="language">
                    <option value="">All languages</option>
                    <?php foreach ($languages as $item): ?>
                        <option value="<?= e($item) ?>" <?= selected($language, $item) ?>><?= e($item) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="d-flex align-items-end gap-2">
                <button class="btn btn-dark" type="submit"><i class="bi bi-search"></i> Filter</button>
                <a class="btn btn-outline-secondary" href="<?= e(url('movies.php')) ?>">Reset</a>
            </div>
        </form>

        <div class="row g-4">
            <?php foreach ($movies as $movie): ?>
                <div class="col-sm-6 col-lg-3">
                    <article class="movie-card h-100">
                        <img src="<?= e(poster_src($movie['poster'])) ?>" alt="<?= e($movie['title']) ?> poster">
                        <div class="movie-card-body">
                            <span class="badge text-bg-light"><?= e($movie['genre']) ?></span>
                            <h3><?= e($movie['title']) ?></h3>
                            <p><?= e($movie['language']) ?> &middot; <?= e($movie['duration']) ?> min</p>
                            <a class="btn btn-sm btn-warning w-100" href="<?= e(url('movie-details.php?id=' . $movie['movie_id'])) ?>">Details & Showtimes</a>
                        </div>
                    </article>
                </div>
            <?php endforeach; ?>
            <?php if (!$movies): ?>
                <div class="col-12">
                    <div class="empty-state">No matching movies found.</div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
