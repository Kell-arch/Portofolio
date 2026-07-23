<?php
$pageTitle = 'About';
$activePage = 'about';
include __DIR__ . '/header.php';
include __DIR__ . '/navigation/navbar.php';
$skills = $GLOBALS['skills'];
$experiences = $GLOBALS['experiences'];
$exps = array_filter($experiences, fn($e) => $e['type'] === 'experience');
$eds = array_filter($experiences, fn($e) => $e['type'] === 'education');
?>

<main>
    <section class="page-header fade-in">
        <h1>About <span>Me</span></h1>
        <p>Get to know more about my background, experience, and skills.</p>
    </section>

    <section class="container">
        <div class="about-grid">
            <div class="about-image fade-in-left">
                <div class="img-wrapper">
                    <div class="img-placeholder">
                        <img src="<?= htmlspecialchars(setting('profile_image')) ?>" alt="<?= htmlspecialchars(setting('about_name')) ?>" style="width: 100%; height: 100%; object-fit: cover;">
                    </div>
                </div>
                <div class="experience-badge">
                    <span class="number">0<?= (int)setting('years_experience', '1') ?>+</span>
                    <span class="label">Years Experience</span>
                </div>
            </div>

            <div class="about-content fade-in-right">
                <h2 class="about-name"><?= htmlspecialchars(setting('about_name')) ?></h2>
                <p class="about-role"><?= htmlspecialchars(setting('about_role')) ?></p>

                <p class="about-text">
                    <?= htmlspecialchars(setting('about_bio_1')) ?>
                </p>
                <p class="about-text">
                    <?= htmlspecialchars(setting('about_bio_2')) ?>
                </p>

                <div class="about-details">
                    <div class="detail-item">
                        <i class="fas fa-user"></i>
                        <span><?= htmlspecialchars(setting('about_name')) ?></span>
                    </div>
                    <div class="detail-item">
                        <i class="fas fa-map-marker-alt"></i>
                        <span><?= htmlspecialchars(setting('location')) ?></span>
                    </div>
                    <div class="detail-item">
                        <i class="fas fa-envelope"></i>
                        <span><?= htmlspecialchars(setting('email')) ?></span>
                    </div>
                    <div class="detail-item">
                        <i class="fas fa-code"></i>
                        <span><?= htmlspecialchars(setting('about_role')) ?></span>
                    </div>
                </div>

                <a href="<?= htmlspecialchars(setting('cv_path')) ?>" download class="btn btn-primary" style="margin-top: 8px;">
                    <i class="fas fa-download"></i> Download CV
                </a>
            </div>
        </div>
    </section>

    <section class="container">
        <p class="section-label fade-in-up" style="margin-top: 40px;">Experience</p>
        <h2 class="section-title fade-in-up">My Journey</h2>
        <p class="section-subtitle fade-in-up">
            Professional experience and projects that shaped my career.
        </p>

        <div class="timeline fade-in-up">
            <?php foreach ($exps as $ex): ?>
            <div class="timeline-item">
                <div class="tl-date"><?= htmlspecialchars($ex['period'] ?? '') ?></div>
                <h3 class="tl-title"><?= htmlspecialchars($ex['title']) ?></h3>
                <p class="tl-company"><?= htmlspecialchars($ex['company'] ?? '') ?></p>
                <p class="tl-desc"><?= htmlspecialchars($ex['description'] ?? '') ?></p>
            </div>
            <?php endforeach; ?>
        </div>

        <?php if (!empty($eds)): ?>
        <div class="timeline-divider"><span>Education</span></div>

        <div class="timeline fade-in-up">
            <?php foreach ($eds as $ed): ?>
            <div class="timeline-item">
                <div class="tl-date"><?= htmlspecialchars($ed['period'] ?? '') ?></div>
                <h3 class="tl-title"><?= htmlspecialchars($ed['title']) ?></h3>
                <p class="tl-company"><?= htmlspecialchars($ed['company'] ?? '') ?></p>
                <p class="tl-desc"><?= htmlspecialchars($ed['description'] ?? '') ?></p>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </section>

    <section class="container">
        <p class="section-label fade-in-up">Skills</p>
        <h2 class="section-title fade-in-up">Technical Expertise</h2>
        <p class="section-subtitle fade-in-up">
            Technologies I work with daily.
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
</main>

<?php
include __DIR__ . '/navigation/footer.php';
?>
