<?php
require_once __DIR__ . '/includes/init.php';
require_login();

$bookingId = (int) ($_GET['booking_id'] ?? $_POST['booking_id'] ?? 0);
$bookingModel = new Booking();
$paymentModel = new Payment();
$booking = $bookingModel->find($bookingId);

if (!$booking || ((int) $booking['user_id'] !== (int) $_SESSION['user_id'] && !is_admin())) {
    set_flash('warning', 'Booking not found.');
    redirect('my-bookings.php');
}

if ($booking['booking_status'] === 'Cancelled') {
    set_flash('warning', 'Cancelled bookings cannot be paid.');
    redirect('my-bookings.php');
}

if ($paymentModel->byBooking($bookingId)) {
    set_flash('info', 'Payment has already been completed.');
    redirect('my-bookings.php');
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();

    $cardName = trim((string) ($_POST['card_name'] ?? ''));
    $method = trim((string) ($_POST['payment_method'] ?? 'Card'));
    $cardNumber = preg_replace('/\D+/', '', (string) ($_POST['card_number'] ?? ''));
    $expiry = trim((string) ($_POST['expiry'] ?? ''));
    $cvv = preg_replace('/\D+/', '', (string) ($_POST['cvv'] ?? ''));

    if ($cardName === '') {
        $errors[] = 'Card holder name is required.';
    }

    if (strlen($cardNumber) < 12 || strlen($cardNumber) > 19) {
        $errors[] = 'Card number must contain 12 to 19 digits.';
    }

    if (!preg_match('/^(0[1-9]|1[0-2])\/\d{2}$/', $expiry)) {
        $errors[] = 'Expiry must use MM/YY format.';
    }

    if (strlen($cvv) < 3 || strlen($cvv) > 4) {
        $errors[] = 'CVV must contain 3 or 4 digits.';
    }

    if (!$errors) {
        $paymentModel->create($bookingId, $cardName, $method, (float) $booking['total_amount']);
        $bookingModel->markConfirmed($bookingId);
        set_flash('success', 'Payment completed and booking confirmed.');
        redirect('my-bookings.php');
    }
}

$pageTitle = 'Payment';
include __DIR__ . '/includes/header.php';
?>

<section class="page-hero compact-hero">
    <div class="container">
        <span class="eyebrow">Payment simulation</span>
        <h1>Complete Your Booking</h1>
    </div>
</section>

<section class="section-pad">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-7">
                <?php foreach ($errors as $error): ?>
                    <div class="alert alert-danger"><?= e($error) ?></div>
                <?php endforeach; ?>

                <form method="post" class="app-form js-payment-form">
                    <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                    <input type="hidden" name="booking_id" value="<?= e($bookingId) ?>">

                    <div class="mb-3">
                        <label class="form-label" for="payment_method">Payment Method</label>
                        <select class="form-select" id="payment_method" name="payment_method">
                            <option value="Card">Credit/Debit Card</option>
                            <option value="Online Wallet">Online Wallet</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="card_name">Card Holder Name</label>
                        <input class="form-control" id="card_name" name="card_name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="card_number">Card Number</label>
                        <input class="form-control js-card-number" id="card_number" name="card_number" placeholder="4242 4242 4242 4242" required>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label" for="expiry">Expiry</label>
                            <input class="form-control" id="expiry" name="expiry" placeholder="MM/YY" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="cvv">CVV</label>
                            <input class="form-control" id="cvv" name="cvv" maxlength="4" required>
                        </div>
                    </div>
                    <button class="btn btn-warning btn-lg mt-4" type="submit">
                        <i class="bi bi-lock-fill"></i> Pay <?= e(format_money($booking['total_amount'])) ?>
                    </button>
                </form>
            </div>

            <div class="col-lg-5">
                <aside class="booking-summary sticky-summary">
                    <h2>Order Details</h2>
                    <dl>
                        <div><dt>Movie</dt><dd><?= e($booking['movie_title']) ?></dd></div>
                        <div><dt>Theatre</dt><dd><?= e($booking['theatre_name']) ?></dd></div>
                        <div><dt>Date</dt><dd><?= e(date('M d, Y', strtotime($booking['show_date']))) ?></dd></div>
                        <div><dt>Time</dt><dd><?= e(date('h:i A', strtotime($booking['show_time']))) ?></dd></div>
                        <div><dt>Seats</dt><dd><?= e($booking['seat_numbers']) ?></dd></div>
                        <div><dt>Total</dt><dd><?= e(format_money($booking['total_amount'])) ?></dd></div>
                    </dl>
                </aside>
            </div>
        </div>
    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
