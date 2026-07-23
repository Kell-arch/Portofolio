<?php
$pageTitle = '404 - Not Found';
$activePage = '';
include __DIR__ . '/header.php';
include __DIR__ . '/navigation/navbar.php';
?>

<main>
    <section class="page-404 fade-in">
        <div>
            <div class="error-code">404</div>
            <h2>Page Not Found</h2>
            <p>The page you are looking for does not exist or has been moved.</p>
            <a href="index.php" class="btn btn-primary">
                <i class="fas fa-arrow-left"></i> Back to Home
            </a>
        </div>
    </section>
</main>

<?php
include __DIR__ . '/navigation/footer.php';
?>
