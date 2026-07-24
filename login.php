<?php
require_once __DIR__ . '/includes/init.php';

if (is_logged_in()) {
    redirect('index.php');
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();

    $email = trim((string) ($_POST['email'] ?? ''));
    $password = (string) ($_POST['password'] ?? '');

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Enter a valid email address.';
    }

    if ($password === '') {
        $errors[] = 'Password is required.';
    }

    if (!$errors) {
        $user = (new User())->login($email, $password);

        if ($user) {
            $_SESSION['user_id'] = $user['user_id'];
            set_flash('success', 'Welcome back, ' . $user['name'] . '.');
            redirect($user['role'] === 'admin' ? 'admin/dashboard.php' : 'index.php');
        }

        $errors[] = 'Invalid email or password.';
    }
}

$pageTitle = 'Login';
include __DIR__ . '/includes/header.php';
?>

<section class="auth-section">
    <div class="auth-card">
        <span class="eyebrow">Welcome back</span>
        <h1>Login</h1>
        <?php foreach ($errors as $error): ?>
            <div class="alert alert-danger"><?= e($error) ?></div>
        <?php endforeach; ?>
        <form method="post" class="js-auth-form">
            <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
            <div class="mb-3">
                <label class="form-label" for="email">Email</label>
                <input class="form-control" id="email" name="email" type="email" required>
            </div>
            <div class="mb-3">
                <label class="form-label" for="password">Password</label>
                <input class="form-control" id="password" name="password" type="password" required>
            </div>
            <button class="btn btn-warning w-100" type="submit">Login</button>
        </form>
        <p class="auth-note">No account? <a href="<?= e(url('register.php')) ?>">Register here</a>.</p>
        <p class="small text-muted mb-0">Admin demo: admin@cinebook.local / admin123</p>
    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
