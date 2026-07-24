<?php
require_once __DIR__ . '/includes/init.php';

if (is_logged_in()) {
    redirect('index.php');
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();

    $name = trim((string) ($_POST['name'] ?? ''));
    $email = trim((string) ($_POST['email'] ?? ''));
    $phone = trim((string) ($_POST['phone'] ?? ''));
    $password = (string) ($_POST['password'] ?? '');
    $confirmPassword = (string) ($_POST['confirm_password'] ?? '');

    if (strlen($name) < 2) {
        $errors[] = 'Name must be at least 2 characters.';
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Enter a valid email address.';
    }

    if (!preg_match('/^[0-9+\-\s]{7,20}$/', $phone)) {
        $errors[] = 'Enter a valid phone number.';
    }

    if (strlen($password) < 6) {
        $errors[] = 'Password must be at least 6 characters.';
    }

    if ($password !== $confirmPassword) {
        $errors[] = 'Passwords do not match.';
    }

    if (!$errors) {
        try {
            (new User())->register($name, $email, $password, $phone);
            set_flash('success', 'Account created. Please login.');
            redirect('login.php');
        } catch (InvalidArgumentException $exception) {
            $errors[] = $exception->getMessage();
        }
    }
}

$pageTitle = 'Register';
include __DIR__ . '/includes/header.php';
?>

<section class="auth-section">
    <div class="auth-card">
        <span class="eyebrow">Join CineBook</span>
        <h1>Create Account</h1>
        <?php foreach ($errors as $error): ?>
            <div class="alert alert-danger"><?= e($error) ?></div>
        <?php endforeach; ?>
        <form method="post" class="js-register-form">
            <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
            <div class="mb-3">
                <label class="form-label" for="name">Full Name</label>
                <input class="form-control" id="name" name="name" value="<?= e($_POST['name'] ?? '') ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label" for="email">Email</label>
                <input class="form-control" id="email" name="email" type="email" value="<?= e($_POST['email'] ?? '') ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label" for="phone">Phone</label>
                <input class="form-control" id="phone" name="phone" value="<?= e($_POST['phone'] ?? '') ?>" required>
            </div>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label" for="password">Password</label>
                    <input class="form-control" id="password" name="password" type="password" minlength="6" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label" for="confirm_password">Confirm Password</label>
                    <input class="form-control" id="confirm_password" name="confirm_password" type="password" minlength="6" required>
                </div>
            </div>
            <button class="btn btn-warning w-100 mt-4" type="submit">Register</button>
        </form>
        <p class="auth-note">Already registered? <a href="<?= e(url('login.php')) ?>">Login here</a>.</p>
    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
