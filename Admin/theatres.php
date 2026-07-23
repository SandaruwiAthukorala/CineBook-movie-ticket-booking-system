<?php
require_once __DIR__ . '/../includes/init.php';
require_admin();

$theatreModel = new Theatre();
$errors = [];
$editTheatre = null;
$statusOptions = ['Active', 'Inactive'];

if (isset($_GET['edit'])) {
    $editTheatre = $theatreModel->find((int) $_GET['edit']);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $action = $_POST['action'] ?? '';

    if ($action === 'delete') {
        $theatreModel->delete((int) ($_POST['theatre_id'] ?? 0));
        set_flash('success', 'Theatre deleted.');
        redirect('admin/theatres.php');
    }

    if ($action === 'save') {
        $data = [
            'name' => trim((string) ($_POST['name'] ?? '')),
            'location' => trim((string) ($_POST['location'] ?? '')),
            'total_seats' => (int) ($_POST['total_seats'] ?? 0),
            'status' => trim((string) ($_POST['status'] ?? 'Active')),
        ];

        if (strlen($data['name']) < 2) {
            $errors[] = 'Theatre name is required.';
        }

        if ($data['location'] === '') {
            $errors[] = 'Location is required.';
        }

        if ($data['total_seats'] < 20 || $data['total_seats'] > 300) {
            $errors[] = 'Total seats must be between 20 and 300.';
        }

        if (!in_array($data['status'], $statusOptions, true)) {
            $errors[] = 'Choose a valid status.';
        }

        if (!$errors) {
            $theatreId = (int) ($_POST['theatre_id'] ?? 0);

            if ($theatreId > 0) {
                $theatreModel->update($theatreId, $data);
                set_flash('success', 'Theatre updated.');
            } else {
                $theatreModel->create($data);
                set_flash('success', 'Theatre added.');
            }

            redirect('admin/theatres.php');
        }

        $editTheatre = $data + ['theatre_id' => (int) ($_POST['theatre_id'] ?? 0)];
    }
}

$theatres = $theatreModel->all();
$pageTitle = 'Manage Theatres';

include __DIR__ . '/../includes/header.php';
?>

<section class="page-hero compact-hero">
    <div class="container">
        <span class="eyebrow">Admin panel</span>
        <h1>Manage Theatres</h1>
    </div>
</section>

<section class="section-pad">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-4">
                <div class="admin-panel">
                    <h2><?= $editTheatre ? 'Update Theatre' : 'Add Theatre' ?></h2>
                    <?php foreach ($errors as $error): ?>
                        <div class="alert alert-danger"><?= e($error) ?></div>
                    <?php endforeach; ?>
                    <form method="post" class="app-form">
                        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                        <input type="hidden" name="action" value="save">
                        <input type="hidden" name="theatre_id" value="<?= e($editTheatre['theatre_id'] ?? 0) ?>">
                        <div class="mb-3">
                            <label class="form-label" for="name">Theatre Name</label>
                            <input class="form-control" id="name" name="name" value="<?= e($editTheatre['name'] ?? '') ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="location">Location</label>
                            <input class="form-control" id="location" name="location" value="<?= e($editTheatre['location'] ?? '') ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="total_seats">Total Seats</label>
                            <input class="form-control" id="total_seats" name="total_seats" type="number" min="20" max="300" value="<?= e($editTheatre['total_seats'] ?? '') ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="status">Status</label>
                            <select class="form-select" id="status" name="status">
                                <?php foreach ($statusOptions as $status): ?>
                                    <option value="<?= e($status) ?>" <?= selected($editTheatre['status'] ?? 'Active', $status) ?>><?= e($status) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <button class="btn btn-warning w-100" type="submit"><?= $editTheatre ? 'Update Theatre' : 'Add Theatre' ?></button>
                        <?php if ($editTheatre): ?>
                            <a class="btn btn-outline-secondary w-100 mt-2" href="<?= e(url('admin/theatres.php')) ?>">Cancel Edit</a>
                        <?php endif; ?>
                    </form>
                </div>
            </div>
            <div class="col-lg-8">
                <div class="admin-panel">
                    <div class="panel-title"><h2>Theatre List</h2></div>
                    <div class="table-responsive">
                        <table class="table align-middle app-table">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Location</th>
                                    <th>Seats</th>
                                    <th>Status</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($theatres as $theatre): ?>
                                    <tr>
                                        <td class="fw-semibold"><?= e($theatre['name']) ?></td>
                                        <td><?= e($theatre['location']) ?></td>
                                        <td><?= e($theatre['total_seats']) ?></td>
                                        <td><?= e($theatre['status']) ?></td>
                                        <td class="text-end">
                                            <a class="btn btn-sm btn-outline-dark" href="<?= e(url('admin/theatres.php?edit=' . $theatre['theatre_id'])) ?>"><i class="bi bi-pencil"></i></a>
                                            <form method="post" class="d-inline js-confirm" data-message="Delete this theatre? Related showtimes and bookings will also be removed.">
                                                <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                                                <input type="hidden" name="action" value="delete">
                                                <input type="hidden" name="theatre_id" value="<?= e($theatre['theatre_id']) ?>">
                                                <button class="btn btn-sm btn-outline-danger" type="submit"><i class="bi bi-trash"></i></button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                                <?php if (!$theatres): ?>
                                    <tr><td colspan="5" class="text-center text-muted py-4">No theatres found.</td></tr>
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
