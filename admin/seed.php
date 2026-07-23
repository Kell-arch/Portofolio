<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/config/db.php';

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: index.php');
    exit;
}

$db = Database::getInstance();
$msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $rows = [
        ['site_name', 'Yosef Kelfian Pambut'],
        ['site_title', 'Portfolio Yosef Kelfian Pambut — Data Engineering specialist. Python, Node.js, SQL, dan pengembangan web.'],
        ['site_description', 'Portfolio Yosef Kelfian Pambut — Data Engineering specialist. Python, Node.js, SQL, dan pengembangan web.'],
        ['site_keywords', 'data engineering, python, node.js, portfolio, yosef kelfian pambut, sql'],
        ['site_author', 'Yosef Kelfian Pambut'],
        ['brand_initials', 'YP'],
        ['hero_greeting', 'Hello, I\'m'],
        ['hero_name', 'Yosef Kelfian'],
        ['hero_surname', 'Pambut'],
        ['hero_tagline', 'Data Engineering enthusiast with expertise in Python, Node.js, SQL, and modern web technologies. Building robust data pipelines and scalable solutions.'],
        ['about_name', 'Yosef Kelfian Pambut'],
        ['about_role', 'Data Engineering Enthusiast'],
        ['about_preview_title', 'Turning Data into Insights'],
        ['about_preview_bio', 'I am a Data Engineering enthusiast with a strong foundation in building scalable data pipelines, data analysis, and software development. Passionate about transforming raw data into actionable insights using modern tools and technologies.'],
        ['about_bio_1', 'I am a passionate Data Engineering enthusiast with a strong foundation in computer science and data processing technologies. My journey in tech started with curiosity about how data drives decision-making, leading me to specialize in building robust data pipelines and analytical systems.'],
        ['about_bio_2', 'With hands-on experience in Python, Node.js, SQL, and web technologies, I enjoy creating solutions that bridge the gap between raw data and meaningful insights. I am constantly learning and adapting to new tools and methodologies in the ever-evolving data landscape.'],
        ['email', 'yosef.kelfian@example.com'],
        ['phone', '+62 812-3456-7890'],
        ['location', 'Indonesia'],
        ['github_url', 'https://github.com/Kell-arch'],
        ['linkedin_url', 'https://linkedin.com/in/'],
        ['profile_image', 'assets/img/profil.png'],
        ['cv_path', 'assets/cv/CV_Yosef_Kelfian_Pambut.pdf'],
    ];

    $db->beginTransaction();
    try {
        $stmt = $db->prepare('INSERT INTO settings (setting_key, setting_value) VALUES (?, ?) ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)');
        foreach ($rows as $r) {
            $stmt->execute([$r[0], $r[1]]);
        }

        $skills = [
            ['Python', 'fab fa-python', 'Data Processing, Automation', 90, 1],
            ['Node.js', 'fab fa-node-js', 'Backend, API Development', 80, 2],
            ['SQL', 'fas fa-database', 'MySQL, PostgreSQL, Query Optimization', 85, 3],
            ['HTML & CSS', 'fab fa-html5', 'Responsive Design, UI Development', 85, 4],
            ['JavaScript', 'fab fa-js', 'Interactive Web, DOM Manipulation', 75, 5],
        ];
        $db->exec('TRUNCATE TABLE skills');
        $sstmt = $db->prepare('INSERT INTO skills (name, icon, label, percentage, sort_order) VALUES (?, ?, ?, ?, ?)');
        foreach ($skills as $s) {
            $sstmt->execute($s);
        }

        $exps = [
            ['experience', 'Data Engineering Intern', 'Tech Company', '2025 - Present', 'Building and maintaining ETL pipelines, data cleaning and transformation, and creating dashboards for business intelligence reporting.', 1],
            ['experience', 'Junior Data Analyst', 'Startup Inc.', '2024 - 2025', 'Analyzed large datasets to provide actionable insights, created automated reporting systems, and collaborated with cross-functional teams.', 2],
            ['experience', 'Freelance Web Developer', 'Self-employed', '2023 - 2024', 'Developed responsive websites and web applications for various clients using modern technologies.', 3],
            ['education', 'Bachelor in Computer Science', 'University', '2021 - Present', 'Focusing on data structures, algorithms, and database systems with practical projects in data analysis.', 1],
            ['education', 'High School Diploma', 'Senior High School', '2018 - 2021', 'Graduated with honors and developed a strong interest in programming and technology.', 2],
        ];
        $db->exec('TRUNCATE TABLE experiences');
        $estmt = $db->prepare('INSERT INTO experiences (type, title, company, period, description, sort_order) VALUES (?, ?, ?, ?, ?, ?)');
        foreach ($exps as $e) {
            $estmt->execute($e);
        }

        $db->commit();
        $msg = '<div class="alert success">Berhasil! Semua data awal telah di-seed ke database.</div>';
    } catch (Exception $e) {
        $db->rollBack();
        $msg = '<div class="alert error">Error: ' . $e->getMessage() . '</div>';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Seed Data | Admin</title>
    <style>
        body { font-family: Poppins, sans-serif; background: #0a0a1a; color: #e8e8f0; display: flex; align-items: center; justify-content: center; min-height: 100vh; margin: 0; }
        .card { background: #12122a; border: 1px solid #2a2a4e; border-radius: 12px; padding: 40px; max-width: 480px; width: 90%; text-align: center; }
        h1 { font-family: "Bebas Neue", sans-serif; font-size: 28px; letter-spacing: 1px; }
        h1 span { color: #FFC107; }
        p { color: #8888aa; font-size: 14px; margin: 16px 0 24px; line-height: 1.6; }
        .btn { display: inline-flex; align-items: center; gap: 8px; padding: 12px 28px; font-family: inherit; font-size: 14px; font-weight: 600; border-radius: 8px; border: none; cursor: pointer; text-decoration: none; transition: all 0.3s ease; }
        .btn-primary { background: linear-gradient(135deg, #FFC107, #c99406); color: #0a0a1a; }
        .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 4px 15px rgba(255,193,7,0.3); }
        .btn-outline { background: transparent; color: #FFC107; border: 2px solid #FFC107; }
        .alert { padding: 14px 18px; border-radius: 8px; margin-bottom: 20px; font-size: 14px; }
        .alert.success { background: rgba(34,197,94,0.1); border: 1px solid #22c55e; color: #22c55e; }
        .alert.error { background: rgba(255,68,68,0.1); border: 1px solid #ff4444; color: #ff4444; }
        a { color: #FFC107; }
    </style>
</head>
<body>
    <div class="card">
        <h1>Seed <span>Data</span></h1>
        <?= $msg ?>
        <?php if (empty($msg)): ?>
        <p>Tombol ini akan mengisi tabel <strong>settings</strong>, <strong>skills</strong>, dan <strong>experiences</strong> dengan data default. Data yang sudah ada akan ditimpa.</p>
        <form method="POST">
            <button type="submit" class="btn btn-primary"><i class="fas fa-database"></i> Seed Data</button>
            <a href="dashboard.php" class="btn btn-outline"><i class="fas fa-arrow-left"></i> Kembali</a>
        </form>
        <?php else: ?>
        <a href="profile.php" class="btn btn-primary"><i class="fas fa-user"></i> Ke Profile</a>
        <?php endif; ?>
    </div>
</body>
</html>
