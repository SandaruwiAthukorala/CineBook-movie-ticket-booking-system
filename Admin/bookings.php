<?php
require_once __DIR__ . '/../includes/init.php';
require_admin();

$bookingModel = new Booking();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'cancel') {
    verify_csrf();
    $bookingModel->cancel((int) ($_POST['booking_id'] ?? 0));
    set_flash('success', 'Booking cancelled.');
    redirect('admin/bookings.php');
}

$bookings = $bookingModel->all(200);
$pageTitle = 'Manage Bookings';

include __DIR__ . '/../includes/header.php';
?>

<section class="page-hero compact-hero">
    <div class="container">
        <span class="eyebrow">Admin panel</span>
        <h1>Manage Bookings</h1>
    </div>
</section>

<section class="section-pad">
    <div class="container">
        <div class="admin-panel">
            <div class="panel-title">
                <h2>Booking Records</h2>
            </div>
            <div class="table-responsive">
                <table class="table align-middle app-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Customer</th>
                            <th>Movie</th>
                            <th>Theatre</th>
                            <th>Date & Time</th>
                            <th>Seats</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($bookings as $booking): ?>
                            <tr>
                                <td>#<?= e($booking['booking_id']) ?></td>
                                <td>
                                    <strong><?= e($booking['customer_name']) ?></strong>
                                    <span class="d-block small text-muted"><?= e($booking['email']) ?></span>
                                </td>
                                <td><?= e($booking['movie_title']) ?></td>
                                <td><?= e($booking['theatre_name']) ?></td>
                                <td><?= e(date('M d, Y', strtotime($booking['show_date']))) ?><br><span class="text-muted"><?= e(date('h:i A', strtotime($booking['show_time']))) ?></span></td>
                                <td><?= e($booking['seat_numbers']) ?></td>
                                <td><?= e(format_money($booking['total_amount'])) ?></td>
                                <td><span class="status-pill status-<?= e(strtolower($booking['booking_status'])) ?>"><?= e($booking['booking_status']) ?></span></td>
                                <td class="text-end">
                                    <?php if ($booking['booking_status'] !== 'Cancelled'): ?>
                                        <form method="post" class="d-inline js-confirm" data-message="Cancel this booking?">
                                            <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                                            <input type="hidden" name="action" value="cancel">
                                            <input type="hidden" name="booking_id" value="<?= e($booking['booking_id']) ?>">
                                            <button class="btn btn-sm btn-outline-danger" type="submit">Cancel</button>
                                        </form>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (!$bookings): ?>
                            <tr><td colspan="9" class="text-center text-muted py-4">No bookings found.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>

<?php include __DIR__ . '/../includes/footer.php'; ?>
