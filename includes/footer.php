<?php if (isLoggedIn()): ?>
    </div><!-- /.content-wrapper -->
</div><!-- /.main-content -->
<?php else: ?>
</div><!-- /.auth-wrapper -->
<?php endif; ?>

<!-- Bootstrap 5 JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<!-- Custom JS -->
<script src="<?= baseUrl('assets/js/app.js') ?>"></script>
</body>
</html>