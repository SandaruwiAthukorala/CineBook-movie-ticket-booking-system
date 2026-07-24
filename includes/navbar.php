<nav class="navbar navbar-expand-lg navbar-dark app-navbar sticky-top">
    <div class="container">
        <a class="navbar-brand fw-bold d-flex align-items-center gap-2" href="<?= e(url('index.php')) ?>">
            <span class="brand-mark"><i class="bi bi-camera-reels-fill"></i></span>
            <?= e(APP_NAME) ?>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar" aria-controls="mainNavbar" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="mainNavbar">
            <ul class="navbar-nav ms-auto align-items-lg-center">
                <li class="nav-item"><a class="nav-link" href="<?= e(url('index.php')) ?>">Home</a></li>
                <li class="nav-item"><a class="nav-link" href="<?= e(url('movies.php')) ?>">Movies</a></li>
                <?php if (is_logged_in()): ?>
                    <li class="nav-item"><a class="nav-link" href="<?= e(url('my-bookings.php')) ?>">My Bookings</a></li>
                <?php endif; ?>
                <?php if (is_admin()): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">Admin</a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="<?= e(url('admin/dashboard.php')) ?>">Dashboard</a></li>
                            <li><a class="dropdown-item" href="<?= e(url('admin/movies.php')) ?>">Manage Movies</a></li>
                            <li><a class="dropdown-item" href="<?= e(url('admin/theatres.php')) ?>">Manage Theatres</a></li>
                            <li><a class="dropdown-item" href="<?= e(url('admin/showtimes.php')) ?>">Manage Showtimes</a></li>
                            <li><a class="dropdown-item" href="<?= e(url('admin/bookings.php')) ?>">Bookings</a></li>
                        </ul>
                    </li>
                <?php endif; ?>
                <?php if (is_logged_in()): ?>
                    <li class="nav-item ms-lg-2">
                        <a class="btn btn-sm btn-light nav-action" href="<?= e(url('logout.php')) ?>">
                            <i class="bi bi-box-arrow-right"></i> Logout
                        </a>
                    </li>
                <?php else: ?>
                    <li class="nav-item ms-lg-2"><a class="nav-link" href="<?= e(url('login.php')) ?>">Login</a></li>
                    <li class="nav-item ms-lg-2">
                        <a class="btn btn-sm btn-warning nav-action" href="<?= e(url('register.php')) ?>">Register</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>
