<?php
require_once __DIR__ . '/../includes/init.php';
require_admin();

$showtimeModel = new Showtime();
$movieModel = new Movie();
$theatreModel = new Theatre();
$errors = [];
$editShowtime = null;
$statusOptions = ['Open', 'Closed'];

if (isset($_GET['edit'])) {
    $editShowtime = $showtimeModel->find((int) $_GET['edit']);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $action = $_POST['action'] ?? '';

    if ($action === 'delete') {
        $showtimeModel->delete((int) ($_POST['showtime_id'] ?? 0));
        set_flash('success', 'Showtime deleted.');
        redirect('admin/showtimes.php');
    }

    if ($action === 'save') {
        $data = [
            'movie_id' => (int) ($_POST['movie_id'] ?? 0),
            'theatre_id' => (int) ($_POST['theatre_id'] ?? 0),
            'show_date' => trim((string) ($_POST['show_date'] ?? '')),
            'show_time' => trim((string) ($_POST['show_time'] ?? '')),
            'price' => (float) ($_POST['price'] ?? 0),
            'status' => trim((string) ($_POST['status'] ?? 'Open')),
        ];

        if ($data['movie_id'] <= 0) {
            $errors[] = 'Choose a movie.';
        }

        if ($data['theatre_id'] <= 0) {
            $errors[] = 'Choose a theatre.';
        }

        if ($data['show_date'] === '') {
            $errors[] = 'Show date is required.';
        }

        if ($data['show_time'] === '') {
            $errors[] = 'Show time is required.';
        }

        if ($data['price'] <= 0) {
            $errors[] = 'Price must be greater than zero.';
        }

        if (!in_array($data['status'], $statusOptions, true)) {
            $errors[] = 'Choose a valid status.';
        }

        if (!$errors) {
            $showtimeId = (int) ($_POST['showtime_id'] ?? 0);

            if ($showtimeId > 0) {
                $showtimeModel->update($showtimeId, $data);
                set_flash('success', 'Showtime updated.');
            } else {
                $showtimeModel->create($data);
                set_flash('success', 'Showtime added.');
            }

            redirect('admin/showtimes.php');
        }

        $editShowtime = $data + ['showtime_id' => (int) ($_POST['showtime_id'] ?? 0)];
    }
}

$movies = $movieModel->all();
$theatres = $theatreModel->all();
$showtimes = $showtimeModel->all();
$pageTitle = 'Manage Showtimes';

include __DIR__ . '/../includes/header.php';
?>

<section class="page-hero compact-hero">
    <div class="container">
        <span class="eyebrow">Admin panel</span>
        <h1>Manage Showtimes</h1>
    </div>
</section>

<section class="section-pad">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-4">
                <div class="admin-panel">
                    <h2><?= $editShowtime ? 'Update Showtime' : 'Add Showtime' ?></h2>
                    <?php foreach ($errors as $error): ?>
                        <div class="alert alert-danger"><?= e($error) ?></div>
                    <?php endforeach; ?>
                    <form method="post" class="app-form">
                        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                        <input type="hidden" name="action" value="save">
                        <input type="hidden" name="showtime_id" value="<?= e($editShowtime['showtime_id'] ?? 0) ?>">
                        <div class="mb-3">
                            <label class="form-label" for="movie_id">Movie</label>
                            <select class="form-select" id="movie_id" name="movie_id" required>
                                <option value="">Choose movie</option>
                                <?php foreach ($movies as $movie): ?>
                                    <option value="<?= e($movie['movie_id']) ?>" <?= selected($editShowtime['movie_id'] ?? '', $movie['movie_id']) ?>><?= e($movie['title']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="theatre_id">Theatre</label>
                            <select class="form-select" id="theatre_id" name="theatre_id" required>
                                <option value="">Choose theatre</option>
                                <?php foreach ($theatres as $theatre): ?>
                                    <option value="<?= e($theatre['theatre_id']) ?>" <?= selected($editShowtime['theatre_id'] ?? '', $theatre['theatre_id']) ?>><?= e($theatre['name']) ?> - <?= e($theatre['location']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label" for="show_date">Date</label>
                                <input class="form-control" id="show_date" name="show_date" type="date" value="<?= e($editShowtime['show_date'] ?? '') ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="show_time">Time</label>
                                <input class="form-control" id="show_time" name="show_time" type="time" value="<?= e($editShowtime['show_time'] ?? '') ?>" required>
                            </div>
                        </div>
                        <div class="row g-3 mt-1">
                            <div class="col-md-6">
                                <label class="form-label" for="price">Price</label>
                                <input class="form-control" id="price" name="price" type="number" min="1" step="0.01" value="<?= e($editShowtime['price'] ?? '') ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="status">Status</label>
                                <select class="form-select" id="status" name="status">
                                    <?php foreach ($statusOptions as $status): ?>
                                        <option value="<?= e($status) ?>" <?= selected($editShowtime['status'] ?? 'Open', $status) ?>><?= e($status) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <button class="btn btn-warning w-100 mt-4" type="submit"><?= $editShowtime ? 'Update Showtime' : 'Add Showtime' ?></button>
                        <?php if ($editShowtime): ?>
                            <a class="btn btn-outline-secondary w-100 mt-2" href="<?= e(url('admin/showtimes.php')) ?>">Cancel Edit</a>
                        <?php endif; ?>
                    </form>
                </div>
            </div>
            <div class="col-lg-8">
                <div class="admin-panel">
                    <div class="panel-title"><h2>Showtime List</h2></div>
                    <div class="table-responsive">
                        <table class="table align-middle app-table">
                            <thead>
                                <tr>
                                    <th>Movie</th>
                                    <th>Theatre</th>
                                    <th>Date</th>
                                    <th>Time</th>
                                    <th>Price</th>
                                    <th>Status</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($showtimes as $showtime): ?>
                                    <tr>
                                        <td class="fw-semibold"><?= e($showtime['movie_title']) ?></td>
                                        <td><?= e($showtime['theatre_name']) ?></td>
                                        <td><?= e(date('M d, Y', strtotime($showtime['show_date']))) ?></td>
                                        <td><?= e(date('h:i A', strtotime($showtime['show_time']))) ?></td>
                                        <td><?= e(format_money($showtime['price'])) ?></td>
                                        <td><?= e($showtime['status']) ?></td>
                                        <td class="text-end">
                                            <a class="btn btn-sm btn-outline-dark" href="<?= e(url('admin/showtimes.php?edit=' . $showtime['showtime_id'])) ?>"><i class="bi bi-pencil"></i></a>
                                            <form method="post" class="d-inline js-confirm" data-message="Delete this showtime? Related bookings will also be removed.">
                                                <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                                                <input type="hidden" name="action" value="delete">
                                                <input type="hidden" name="showtime_id" value="<?= e($showtime['showtime_id']) ?>">
                                                <button class="btn btn-sm btn-outline-danger" type="submit"><i class="bi bi-trash"></i></button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                                <?php if (!$showtimes): ?>
                                    <tr><td colspan="7" class="text-center text-muted py-4">No showtimes found.</td></tr>
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
