</main>

<footer class="site-footer mt-auto">
    <div class="container-xxl py-5">
        <p class="footer-brand d-flex align-items-center gap-2 mb-1">
            <svg class="brand-mark" viewBox="0 0 34 34" aria-hidden="true" focusable="false">
                <circle cx="17" cy="17" r="16" fill="#F6F3EA" />
                <path d="M17 25.5V15.5" stroke="#1E5B45" stroke-width="2.4" stroke-linecap="round" />
                <path d="M17 16.6c.2-4.8-2.9-7.8-7.4-7.9.1 4.8 3 7.7 7.4 7.9Z" fill="#2E7D5B" />
                <path d="M17 16.6c-.2-4.8 2.9-7.8 7.4-7.9-.1 4.8-3 7.7-7.4 7.9Z" fill="#8FC7A6" />
            </svg>
            <span>StudyGrove</span>
        </p>
        <p class="mb-0 small">A small place where students grow answers together.</p>
    </div>
</footer>

<!-- Bootstrap's JavaScript bundle (includes Popper). It powers the
     collapsible navigation menu on small screens. -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous">
</script>

<script src="<?= PUBLIC_FOLDER ?>/js/main.js?v=<?= filemtime(ROOT_PATH . '/public/js/main.js') ?>"></script>
</body>

</html>