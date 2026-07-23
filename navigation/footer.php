<footer class="footer">
    <div class="container">
        <div class="footer-content">
            <div class="footer-copyright">
                &copy; <?= date('Y') ?> <span class="brand"><?= htmlspecialchars(setting('site_name')) ?></span>. All rights reserved.
            </div>
            <div class="footer-social">
                <a href="<?= htmlspecialchars(setting('github_url')) ?>" target="_blank" rel="noopener noreferrer" aria-label="GitHub">
                    <i class="fab fa-github"></i>
                </a>
                <a href="<?= htmlspecialchars(setting('linkedin_url')) ?>" target="_blank" rel="noopener noreferrer" aria-label="LinkedIn">
                    <i class="fab fa-linkedin-in"></i>
                </a>
                <a href="mailto:<?= htmlspecialchars(setting('email')) ?>" aria-label="Email">
                    <i class="fas fa-envelope"></i>
                </a>
            </div>
        </div>
    </div>
</footer>

<button class="back-to-top" id="backToTop" aria-label="Back to top">
    <i class="fas fa-arrow-up"></i>
</button>

<script src="js/particles.js"></script>
<script src="js/script.js"></script>
</body>
</html>
