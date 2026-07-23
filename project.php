<?php
$pageTitle = 'Projects';
$activePage = 'project';
include __DIR__ . '/header.php';
include __DIR__ . '/navigation/navbar.php';
require_once __DIR__ . '/admin/config/db.php';

$db = Database::getInstance();
$projects = $db->query('SELECT * FROM projects ORDER BY created_at DESC, id DESC')->fetchAll();
?>

<main>
    <section class="page-header fade-in">
        <h1>My <span>Projects</span></h1>
        <p>Showcasing my work in data engineering and software development.</p>
    </section>

    <section class="container">

        <div class="filter-bar fade-in-up">
            <div class="filter-search">
                <i class="fas fa-search search-icon"></i>
                <input type="text" id="projectSearch" placeholder="Search projects..." autocomplete="off">
            </div>
            <div class="filter-tags">
                <?php
                $allTags = [];
                foreach ($projects as $p) {
                    foreach (explode(',', $p['tags'] ?? '') as $t) {
                        $t = trim($t);
                        if ($t) $allTags[$t] = ($allTags[$t] ?? 0) + 1;
                    }
                }
                foreach ($allTags as $tag => $count): ?>
                    <button class="filter-tag" data-tag="<?= htmlspecialchars(strtolower($tag)) ?>"><?= htmlspecialchars($tag) ?> (<?= $count ?>)</button>
                <?php endforeach; ?>
            </div>
        </div>

        <?php if (empty($projects)): ?>
        <div style="text-align:center;padding:80px 20px;color:#555577;">
            <i class="fas fa-code" style="font-size:48px;margin-bottom:16px;color:#2a2a4e;"></i>
            <p style="font-size:15px;">No projects yet.</p>
        </div>
        <?php else: ?>
        <div class="projects-grid">
            <?php foreach ($projects as $i => $p): ?>
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
        </div>
        <?php endif; ?>
    </section>
</main>

<?php
include __DIR__ . '/navigation/footer.php';
?>
