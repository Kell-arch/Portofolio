<?php $activePage = $activePage ?? 'home'; ?>
<body>
    <canvas id="particleCanvas"></canvas>
    <div id="bg-blobs">
      <div class="blob blob-1"></div>
      <div class="blob blob-2"></div>
      <div class="blob blob-3"></div>
      <div class="blob blob-4"></div>
    </div>
    <nav class="navbar" id="navbar">
        <div class="nav-container">
            <a href="index.php" class="nav-logo"><?= htmlspecialchars(setting('brand_initials')) ?><span>.</span></a>
            <button class="nav-toggle" id="navToggle" aria-label="Toggle navigation">
                <i class="fas fa-bars"></i>
            </button>
            <ul class="nav-links" id="navLinks">
                <li><a href="index.php" class="<?= $activePage === 'home' ? 'active' : '' ?>">Home</a></li>
                <li><a href="about.php" class="<?= $activePage === 'about' ? 'active' : '' ?>">About</a></li>
                <li><a href="project.php" class="<?= $activePage === 'project' ? 'active' : '' ?>">Projects</a></li>
                <li><a href="contact.php" class="<?= $activePage === 'contact' ? 'active' : '' ?>">Contact</a></li>
                <li><button class="theme-toggle" id="themeToggle" aria-label="Toggle theme"><i class="fas fa-moon"></i></button></li>
            </ul>
        </div>
    </nav>
    <div class="nav-overlay" id="navOverlay"></div>
