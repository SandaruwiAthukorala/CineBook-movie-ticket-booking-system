<?php
require_once __DIR__ . '/../includes/init.php';
require_admin();

$movieModel = new Movie();
$errors = [];
$editMovie = null;
$statusOptions = ['Now Showing', 'Coming Soon', 'Archived'];

if (isset($_GET['edit'])) {
    $editMovie = $movieModel->find((int) $_GET['edit']);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $action = $_POST['action'] ?? '';

    if ($action === 'delete') {
        $movieModel->delete((int) ($_POST['movie_id'] ?? 0));
        set_flash('success', 'Movie deleted.');
        redirect('admin/movies.php');
    }

    if ($action === 'save') {
        $data = [
            'title' => trim((string) ($_POST['title'] ?? '')),
            'genre' => trim((string) ($_POST['genre'] ?? '')),
            'language' => trim((string) ($_POST['language'] ?? '')),
            'duration' => (int) ($_POST['duration'] ?? 0),
            'description' => trim((string) ($_POST['description'] ?? '')),
            'poster' => trim((string) ($_POST['poster'] ?? '')),
            'status' => trim((string) ($_POST['status'] ?? 'Now Showing')),
        ];

        if (strlen($data['title']) < 2) {
            $errors[] = 'Movie title is required.';
        }

        if ($data['genre'] === '') {
            $errors[] = 'Genre is required.';
        }

        if ($data['language'] === '') {
            $errors[] = 'Language is required.';
        }

        if ($data['duration'] < 30 || $data['duration'] > 300) {
            $errors[] = 'Duration must be between 30 and 300 minutes.';
        }

        if ($data['description'] === '') {
            $errors[] = 'Description is required.';
        }

        if (!in_array($data['status'], $statusOptions, true)) {
            $errors[] = 'Choose a valid status.';
        }

        if (!$errors) {
            $movieId = (int) ($_POST['movie_id'] ?? 0);

            if ($movieId > 0) {
                $movieModel->update($movieId, $data);
                set_flash('success', 'Movie updated.');
            } else {
                $movieModel->create($data);
                set_flash('success', 'Movie added.');
            }

            redirect('admin/movies.php');
        }

        $editMovie = $data + ['movie_id' => (int) ($_POST['movie_id'] ?? 0)];
    }
}

$movies = $movieModel->all();
$pageTitle = 'Manage Movies';

include __DIR__ . '/../includes/header.php';
?>

<section class="page-hero compact-hero">
    <div class="container">
        <span class="eyebrow">Admin panel</span>
        <h1>Manage Movies</h1>
    </div>
</section>

<section class="section-pad">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-4">
                <div class="admin-panel">
                    <h2><?= $editMovie ? 'Update Movie' : 'Add Movie' ?></h2>
                    <?php foreach ($errors as $error): ?>
                        <div class="alert alert-danger"><?= e($error) ?></div>
                    <?php endforeach; ?>
                    <form method="post" class="app-form">
                        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                        <input type="hidden" name="action" value="save">
                        <input type="hidden" name="movie_id" value="<?= e($editMovie['movie_id'] ?? 0) ?>">
                        <div class="mb-3">
                            <label class="form-label" for="title">Title</label>
                            <input class="form-control" id="title" name="title" value="<?= e($editMovie['title'] ?? '') ?>" required>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label" for="genre">Genre</label>
                                <input class="form-control" id="genre" name="genre" value="<?= e($editMovie['genre'] ?? '') ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="language">Language</label>
                                <input class="form-control" id="language" name="language" value="<?= e($editMovie['language'] ?? '') ?>" required>
                            </div>
                        </div>
                        <div class="mb-3 mt-3">
                            <label class="form-label" for="duration">Duration Minutes</label>
                            <input class="form-control" id="duration" name="duration" type="number" min="30" max="300" value="<?= e($editMovie['duration'] ?? '') ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="poster">Poster URL or Path</label>
                            <input class="form-control" id="poster" name="poster" value="<?= e($editMovie['poster'] ?? '') ?>" placeholder="assets/images/poster-placeholder.svg">
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="status">Status</label>
                            <select class="form-select" id="status" name="status">
                                <?php foreach ($statusOptions as $status): ?>
                                    <option value="<?= e($status) ?>" <?= selected($editMovie['status'] ?? 'Now Showing', $status) ?>><?= e($status) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="description">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="4" required><?= e($editMovie['description'] ?? '') ?></textarea>
                        </div>
                        <button class="btn btn-warning w-100" type="submit"><?= $editMovie ? 'Update Movie' : 'Add Movie' ?></button>
                        <?php if ($editMovie): ?>
                            <a class="btn btn-outline-secondary w-100 mt-2" href="<?= e(url('admin/movies.php')) ?>">Cancel Edit</a>
                        <?php endif; ?>
                    </form>
                </div>
            </div>

            <div class="col-lg-8">
                <div class="admin-panel">
                    <div class="panel-title">
                        <h2>Movie List</h2>
                    </div>
                    <div class="table-responsive">
                        <table class="table align-middle app-table">
                            <thead>
                                <tr>
                                    <th>Movie</th>
                                    <th>Genre</th>
                                    <th>Language</th>
                                    <th>Status</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($movies as $movie): ?>
                                    <tr>
                                        <td>
                                            <div class="table-media">
                                                <img src="<?= e(poster_src($movie['poster'])) ?>" alt="">
                                                <div>
                                                    <strong><?= e($movie['title']) ?></strong>
                                                    <span><?= e($movie['duration']) ?> min</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td><?= e($movie['genre']) ?></td>
                                        <td><?= e($movie['language']) ?></td>
                                        <td><?= e($movie['status']) ?></td>
                                        <td class="text-end">
                                            <a class="btn btn-sm btn-outline-dark" href="<?= e(url('admin/movies.php?edit=' . $movie['movie_id'])) ?>"><i class="bi bi-pencil"></i></a>
                                            <form method="post" class="d-inline js-confirm" data-message="Delete this movie? Related showtimes and bookings will also be removed.">
                                                <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                                                <input type="hidden" name="action" value="delete">
                                                <input type="hidden" name="movie_id" value="<?= e($movie['movie_id']) ?>">
                                                <button class="btn btn-sm btn-outline-danger" type="submit"><i class="bi bi-trash"></i></button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                                <?php if (!$movies): ?>
                                    <tr><td colspan="5" class="text-center text-muted py-4">No movies found.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include __DIR__ . '/../includes/footer.php'; ?>
