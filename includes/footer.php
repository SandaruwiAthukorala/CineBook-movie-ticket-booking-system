</main>
<footer class="site-footer">
    <div class="container d-flex flex-column flex-md-row justify-content-between gap-3">
        <div>
            <strong><?= e(APP_NAME) ?></strong>
            <span class="text-white-50 d-block">Movie ticket booking made simple.</span>
        </div>
        <div class="text-white-50">
            &copy; <?= date('Y') ?> <?= e(APP_NAME) ?>. Academic project.
        </div>
    </div>
</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= e(asset('js/app.js')) ?>"></script>
</body>
</html>
