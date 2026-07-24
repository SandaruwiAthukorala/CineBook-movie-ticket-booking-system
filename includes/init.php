<?php
declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../classes/User.php';
require_once __DIR__ . '/../classes/Movie.php';
require_once __DIR__ . '/../classes/Theatre.php';
require_once __DIR__ . '/../classes/Showtime.php';
require_once __DIR__ . '/../classes/Booking.php';
require_once __DIR__ . '/../classes/Payment.php';

function app_base(): string
{
    static $base = null;

    if ($base !== null) {
        return $base;
    }

    $script = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '');
    $dir = rtrim(str_replace('\\', '/', dirname($script)), '/');

    if (str_ends_with($dir, '/admin')) {
        $dir = dirname($dir);
    }

    $base = ($dir === '/' || $dir === '\\' || $dir === '.') ? '' : $dir;
    return $base;
}

function url(string $path = ''): string
{
    return app_base() . '/' . ltrim($path, '/');
}

function asset(string $path): string
{
    return url('assets/' . ltrim($path, '/'));
}

function poster_src(?string $poster): string
{
    $poster = trim((string) $poster);

    if ($poster === '') {
        return asset('images/poster-placeholder.svg');
    }

    if (str_starts_with($poster, 'http://') || str_starts_with($poster, 'https://') || str_starts_with($poster, '/')) {
        return $poster;
    }

    return url($poster);
}

function e(mixed $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function redirect(string $path): never
{
    header('Location: ' . url($path));
    exit;
}

function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

function verify_csrf(): void
{
    $token = $_POST['csrf_token'] ?? '';

    if (!hash_equals($_SESSION['csrf_token'] ?? '', (string) $token)) {
        set_flash('danger', 'Security check failed. Please try again.');
        redirect('index.php');
    }
}

function set_flash(string $type, string $message): void
{
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function get_flash(): ?array
{
    if (!isset($_SESSION['flash'])) {
        return null;
    }

    $flash = $_SESSION['flash'];
    unset($_SESSION['flash']);

    return $flash;
}

function is_logged_in(): bool
{
    return isset($_SESSION['user_id']);
}

function current_user(): ?array
{
    static $cachedUser = null;

    if (!is_logged_in()) {
        return null;
    }

    if ($cachedUser === null) {
        $cachedUser = (new User())->find((int) $_SESSION['user_id']);
    }

    return $cachedUser;
}

function is_admin(): bool
{
    $user = current_user();
    return $user !== null && $user['role'] === 'admin';
}

function require_login(): void
{
    if (!is_logged_in()) {
        set_flash('warning', 'Please login to continue.');
        redirect('login.php');
    }
}

function require_admin(): void
{
    require_login();

    if (!is_admin()) {
        set_flash('danger', 'You do not have permission to open that page.');
        redirect('index.php');
    }
}

function format_money(float|int|string $amount): string
{
    return 'LKR ' . number_format((float) $amount, 2);
}

function selected(mixed $current, mixed $expected): string
{
    return (string) $current === (string) $expected ? 'selected' : '';
}

function checked(bool $condition): string
{
    return $condition ? 'checked' : '';
}

function generate_seats(int $totalSeats): array
{
    $seats = [];

    for ($i = 0; $i < $totalSeats; $i++) {
        $row = seat_row_label(intdiv($i, 10));
        $number = ($i % 10) + 1;
        $seats[] = $row . $number;
    }

    return $seats;
}

function seat_row_label(int $index): string
{
    $label = '';

    do {
        $label = chr(65 + ($index % 26)) . $label;
        $index = intdiv($index, 26) - 1;
    } while ($index >= 0);

    return $label;
}
