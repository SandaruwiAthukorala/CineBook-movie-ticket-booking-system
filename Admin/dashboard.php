<?php
require_once __DIR__ . '/../includes/init.php';
require_admin();

$movieModel = new Movie();
$theatreModel = new Theatre();
$showtimeModel = new Showtime();
$bookingModel = new Booking();
$userModel = new User();

$stats = [
    'movies' => $movieModel->countAll(),
    'theatres' => $theatreModel->countAll(),
    'showtimes' => $showtimeModel->countAll(),
    'bookings' => $bookingModel->countAll(),
    'users' => $userModel->countAll(),
    'revenue' => $bookingModel->totalRevenue(),
];

$recentBookings = $bookingModel->all(6);
$latestUsers = $userModel->latest(5);
$pageTitle = 'Admin Dashboard';

include __DIR__ . '/../includes/header.php';
?>

<section class="page-hero compact-hero">
    <div class="container">
        <span class="eyebrow">Admin panel</span>
        <h1>Dashboard</h1>
    </div>
</section>

<section class="section-pad">
    <div class="container">
        <div class="admin-stats">
            <article><i class="bi bi-film"></i><span>Movies</span><strong><?= e($stats['movies']) ?></strong></article>
            <article><i class="bi bi-building"></i><span>Theatres</span><strong><?= e($stats['theatres']) ?></strong></article>
            <article><i class="bi bi-calendar-event"></i><span>Showtimes</span><strong><?= e($stats['showtimes']) ?></strong></article>
            <article><i class="bi bi-ticket"></i><span>Bookings</span><strong><?= e($stats['bookings']) ?></strong></article>
            <article><i class="bi bi-people"></i><span>Users</span><strong><?= e($stats['users']) ?></strong></article>
            <article><i class="bi bi-cash-coin"></i><span>Revenue</span><strong><?= e(format_money($stats['revenue'])) ?></strong></article>
        </div>

        <div class="row g-4 mt-1">
            <div class="col-lg-8">
                <div class="admin-panel">
                    <div class="panel-title">
                        <h2>Recent Bookings</h2>
                        <a href="<?= e(url('admin/bookings.php')) ?>">View all</a>
                    </div>
                    <div class="table-responsive">
                        <table class="table align-middle app-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Customer</th>
                                    <th>Movie</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recentBookings as $booking): ?>
                                    <tr>
                                        <td>#<?= e($booking['booking_id']) ?></td>
                                        <td><?= e($booking['customer_name']) ?></td>
                                        <td><?= e($booking['movie_title']) ?></td>
                                        <td><?= e(format_money($booking['total_amount'])) ?></td>
                                        <td><span class="status-pill status-<?= e(strtolower($booking['booking_status'])) ?>"><?= e($booking['booking_status']) ?></span></td>
                                    </tr>
                                <?php endforeach; ?>
                                <?php if (!$recentBookings): ?>
                                    <tr><td colspan="5" class="text-center text-muted py-4">No bookings yet.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="admin-panel">
                    <div class="panel-title">
                        <h2>Latest Users</h2>
                    </div>
                    <div class="list-group list-group-flush">
                        <?php foreach ($latestUsers as $user): ?>
                            <div class="list-group-item px-0 d-flex justify-content-between align-items-center">
                                <div>
                                    <strong><?= e($user['name']) ?></strong>
                                    <span class="d-block small text-muted"><?= e($user['email']) ?></span>
                                </div>
                                <span class="badge text-bg-light"><?= e($user['role']) ?></span>
                            </div>
                        <?php endforeach; ?>
                        <?php if (!$latestUsers): ?>
                            <div class="text-muted py-3">No users found.</div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include __DIR__ . '/../includes/footer.php'; ?>

