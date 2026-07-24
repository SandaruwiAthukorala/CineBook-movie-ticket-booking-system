<?php
require_once __DIR__ . '/includes/init.php';
require_login();

$showtimeId = (int) ($_GET['showtime_id'] ?? $_POST['showtime_id'] ?? 0);
$showtimeModel = new Showtime();
$bookingModel = new Booking();
$showtime = $showtimeModel->find($showtimeId);

if (!$showtime || $showtime['status'] !== 'Open') {
    set_flash('warning', 'This showtime is not available for booking.');
    redirect('movies.php');
}

$allSeats = generate_seats((int) $showtime['total_seats']);
$bookedSeats = $bookingModel->bookedSeats($showtimeId);
$errors = [];
$selectedSeats = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();

    $postedSeats = $_POST['seats'] ?? [];
    $selectedSeats = array_values(array_intersect($allSeats, array_map('trim', (array) $postedSeats)));

    if (!$selectedSeats) {
        $errors[] = 'Please select at least one seat.';
    }

    if (count($selectedSeats) > 8) {
        $errors[] = 'You can book a maximum of 8 seats at once.';
    }

    if (!$bookingModel->seatsAvailable($showtimeId, $selectedSeats)) {
        $errors[] = 'One or more selected seats were just booked. Please choose again.';
    }

    if (!$errors) {
        $total = count($selectedSeats) * (float) $showtime['price'];
        $bookingId = $bookingModel->create((int) $_SESSION['user_id'], $showtimeId, $selectedSeats, $total);
        redirect('payment.php?booking_id=' . $bookingId);
    }
}

$pageTitle = 'Book Ticket';
include __DIR__ . '/includes/header.php';
?>

<section class="page-hero compact-hero">
    <div class="container">
        <span class="eyebrow">Seat selection</span>
        <h1><?= e($showtime['movie_title']) ?></h1>
        <p><?= e($showtime['theatre_name']) ?>, <?= e($showtime['location']) ?> &middot; <?= e(date('M d, Y', strtotime($showtime['show_date']))) ?> at <?= e(date('h:i A', strtotime($showtime['show_time']))) ?></p>
    </div>
</section>

<section class="section-pad">
    <div class="container">
        <?php foreach ($errors as $error): ?>
            <div class="alert alert-danger"><?= e($error) ?></div>
        <?php endforeach; ?>

        <form method="post" class="booking-layout js-seat-form" data-price="<?= e($showtime['price']) ?>">
            <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
            <input type="hidden" name="showtime_id" value="<?= e($showtimeId) ?>">

            <div class="seat-panel">
                <div class="screen">SCREEN</div>
                <div class="seat-grid">
                    <?php foreach ($allSeats as $seat): ?>
                        <?php $isBooked = in_array($seat, $bookedSeats, true); ?>
                        <label class="seat <?= $isBooked ? 'booked' : '' ?>">
                            <input type="checkbox" name="seats[]" value="<?= e($seat) ?>" <?= $isBooked ? 'disabled' : '' ?> <?= checked(in_array($seat, $selectedSeats, true)) ?>>
                            <span><?= e($seat) ?></span>
                        </label>
                    <?php endforeach; ?>
                </div>
                <div class="seat-legend">
                    <span><i class="legend available"></i> Available</span>
                    <span><i class="legend selected"></i> Selected</span>
                    <span><i class="legend booked"></i> Booked</span>
                </div>
            </div>

            <aside class="booking-summary">
                <h2>Booking Summary</h2>
                <dl>
                    <div><dt>Movie</dt><dd><?= e($showtime['movie_title']) ?></dd></div>
                    <div><dt>Theatre</dt><dd><?= e($showtime['theatre_name']) ?></dd></div>
                    <div><dt>Price</dt><dd><?= e(format_money($showtime['price'])) ?></dd></div>
                    <div><dt>Seats</dt><dd class="js-selected-seats">None</dd></div>
                    <div><dt>Total</dt><dd class="js-total">LKR 0.00</dd></div>
                </dl>
                <button class="btn btn-warning btn-lg w-100" type="submit">
                    <i class="bi bi-credit-card"></i> Continue to Payment
                </button>
            </aside>
        </form>
    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
