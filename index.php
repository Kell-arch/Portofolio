<?php
$pageTitle = 'Home';
$activePage = 'home';
include __DIR__ . '/header.php';
include __DIR__ . '/navigation/navbar.php';
$db = $GLOBALS['db'];
$skills = $GLOBALS['skills'];
$recent = $db->query('SELECT * FROM projects ORDER BY created_at DESC, id DESC LIMIT 3')->fetchAll();

$allTagRows = $db->query('SELECT tags FROM projects')->fetchAll(PDO::FETCH_COLUMN);
$uniqueTags = [];
foreach ($allTagRows as $tags) {
    foreach (explode(',', $tags ?? '') as $t) {
        $t = trim($t);
        if ($t) $uniqueTags[$t] = true;
    }
}
$uniqueTechCount = count($uniqueTags);
$skillCount = count($skills);
?>

<main>
    <section class="hero" id="home">
        <div class="hero-bg">
            <video class="hero-video" autoplay muted loop playsinline preload="metadata">
                <source src="assets/video/hero-bg.mp4" type="video/mp4">
            </video>
        </div>
        <div class="hero-content">
            <p class="hero-greeting fade-in"><?= htmlspecialchars(setting('hero_greeting')) ?></p>
            <h1 class="hero-name fade-in" style="transition-delay: 0.1s;">
                <?= htmlspecialchars(setting('hero_name')) ?> <span class="highlight"><?= htmlspecialchars(setting('hero_surname')) ?></span>
            </h1>
            <p class="hero-role fade-in" style="transition-delay: 0.2s;">
                <span id="typedText"></span><span class="cursor"></span>
            </p>
            <p class="hero-tagline fade-in" style="transition-delay: 0.3s;">
                <?= htmlspecialchars(setting('hero_tagline')) ?>
            </p>
            <div class="hero-actions fade-in" style="transition-delay: 0.4s;">
                <a href="project.php" class="btn btn-primary">
                    <i class="fas fa-code"></i> View Projects
                </a>
                <a href="contact.php" class="btn btn-outline">
                    <i class="fas fa-envelope"></i> Contact Me
                </a>
            </div>
            <div class="hero-social fade-in" style="transition-delay: 0.5s;">
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
    </section>

    <section id="about" class="container">
        <div class="hero-about-preview fade-in-up">
            <div class="hero-about-grid">
                <div class="hero-about-text">
                    <p class="section-label">About Me</p>
                    <?php $title = setting('about_preview_title'); $words = explode(' ', trim($title)); $last = array_pop($words); ?>
                    <h2 class="hap-title"><?= implode(' ', $words) ?> <span style="color: var(--accent);"><?= htmlspecialchars($last) ?></span></h2>
                    <p>
                        <?= htmlspecialchars(setting('about_preview_bio')) ?>
                    </p>
                    <div class="hero-skills-mini">
                        <?php foreach ($skills as $sk): ?>
                        <span><i class="<?= htmlspecialchars($sk['icon'] ?? 'fas fa-code') ?>"></i> <?= htmlspecialchars($sk['name']) ?></span>
                        <?php endforeach; ?>
                    </div>
                    <a href="about.php" class="btn btn-outline btn-sm" style="margin-top: 20px;">
                        More About Me <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
                <div class="hero-about-image" style="text-align: center;">
                    <div style="width: 220px; height: 220px; border-radius: 50%; overflow: hidden; border: 3px solid var(--accent); margin: 0 auto; background: var(--bg-card); display: flex; align-items: center; justify-content: center;">
                        <img src="<?= htmlspecialchars(setting('profile_image')) ?>" alt="<?= htmlspecialchars(setting('about_name')) ?>" style="width: 100%; height: 100%; object-fit: cover;">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="skills" class="container">
        <p class="section-label fade-in-up">Expertise</p>
        <h2 class="section-title fade-in-up">Technical Skills</h2>
        <p class="section-subtitle fade-in-up">
            Technologies and tools I work with to build data-driven solutions.
        </p>

        <div class="skills-grid">
            <?php $i = 0; foreach ($skills as $sk): $i++; ?>
            <div class="skill-card fade-in-up" style="transition-delay: <?= 0.1 * $i ?>s;">
                <div class="skill-header">
                    <div class="skill-icon"><i class="<?= htmlspecialchars($sk['icon'] ?? 'fas fa-code') ?>"></i></div>
                    <div class="skill-info">
                        <h4><?= htmlspecialchars($sk['name']) ?></h4>
                        <span><?= htmlspecialchars($sk['label'] ?? '') ?></span>
                    </div>
                </div>
                <div class="skill-bar">
                    <div class="fill" data-width="<?= (int)$sk['percentage'] ?>"></div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </section>

    <section id="stats" class="container">
        <div class="stats-grid fade-in-up">
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-code"></i></div>
                <div class="stat-number" data-target="<?= count($recent) ?>">0</div>
                <p class="stat-label">Projects Built</p>
            </div>
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-cubes"></i></div>
                <div class="stat-number" data-target="<?= $skillCount ?>">0</div>
                <p class="stat-label">Technologies Used</p>
            </div>
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-clock"></i></div>
                <div class="stat-number" data-target="<?= (int)setting('years_experience', '1') ?>">0</div>
                <p class="stat-label">Years Experience</p>
            </div>
        </div>
    </section>

    <section id="projects" class="container">
        <p class="section-label fade-in-up">Portfolio</p>
        <h2 class="section-title fade-in-up">Recent Projects</h2>
        <p class="section-subtitle fade-in-up">
            A selection of projects I have worked on recently.
        </p>

        <div class="projects-grid">
            <?php if (empty($recent)): ?>
                <div style="text-align:center;padding:60px 20px;color:#555577;grid-column:1/-1;">
                    <p>No projects yet.</p>
                </div>
            <?php else: ?>
                <?php foreach ($recent as $i => $p): ?>
                <div class="project-card fade-in-up" style="transition-delay: <?= 0.1 * ($i + 1) ?>s;">
                    <div class="project-thumb">
                        <?php if (!empty($p['thumbnail'])): ?>
                            <img src="<?= htmlspecialchars($p['thumbnail']) ?>" alt="<?= htmlspecialchars($p['title']) ?>" style="width:100%;height:100%;object-fit:cover;">
                        <?php else: ?>
                            <div class="thumb-placeholder">
                                <i class="fas fa-code" style="font-size:48px;color:var(--accent);"></i>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="project-body">
                        <div class="project-tags">
                            <?php foreach (explode(',', $p['tags'] ?? '') as $tag): ?>
                                <?php if (trim($tag)): ?>
                                    <span><?= htmlspecialchars(trim($tag)) ?></span>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </div>
                        <h3><?= htmlspecialchars($p['title']) ?></h3>
                        <p><?= htmlspecialchars($p['description'] ?? '') ?></p>
                        <div class="project-date"><i class="far fa-calendar-alt"></i> <?= date('M d, Y', strtotime($p['created_at'])) ?></div>
                        <div class="project-links">
                            <?php if (!empty($p['source_url'])): ?>
                                <a href="<?= htmlspecialchars($p['source_url']) ?>"><i class="fab fa-github"></i> Source</a>
                            <?php endif; ?>
                            <?php if (!empty($p['demo_url'])): ?>
                                <a href="<?= htmlspecialchars($p['demo_url']) ?>"><i class="fas fa-external-link-alt"></i> Demo</a>
                            <?php endif; ?>
                            <?php if (!empty($p['youtube_url'])): ?>
                                <a href="<?= htmlspecialchars($p['youtube_url']) ?>" target="_blank" rel="noopener noreferrer"><i class="fab fa-youtube"></i> Docs</a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <div style="text-align: center; margin-top: 40px;">
            <a href="project.php" class="btn btn-outline">
                View All Projects <i class="fas fa-arrow-right"></i>
            </a>
        </div>
    </section>

    <section class="container">
        <div class="cta-section fade-in-up">
            <h2>Let's Work <span style="color: var(--accent);">Together</span></h2>
            <p>
                Have a project in mind or just want to say hello? I would love to hear from you.
            </p>
            <div class="cta-actions">
                <a href="contact.php" class="btn btn-primary">
                    <i class="fas fa-paper-plane"></i> Get In Touch
                </a>
                <a href="about.php" class="btn btn-outline">
                    <i class="fas fa-user"></i> Learn More
                </a>
            </div>
        </div>
    </section>
</main>

<?php
include __DIR__ . '/navigation/footer.php';
?>
