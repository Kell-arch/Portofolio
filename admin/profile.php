<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/config/load_settings.php';
$db = $GLOBALS['db'];
$skills = $GLOBALS['skills'];
$experiences = $GLOBALS['experiences'];

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: index.php');
    exit;
}

$flash = $_SESSION['flash_message'] ?? '';
unset($_SESSION['flash_message']);
$flash_error = $_SESSION['flash_error'] ?? '';
unset($_SESSION['flash_error']);
$error = '';

$tab = $_GET['tab'] ?? 'basic';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    try {
        $db->beginTransaction();

        if ($action === 'save_settings') {
            $stmt = $db->prepare('INSERT INTO settings (setting_key, setting_value) VALUES (?, ?) ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)');
            foreach ($_POST as $key => $value) {
                if ($key === 'action') continue;
                $stmt->execute([$key, trim($value)]);
            }
            $db->commit();
            $_SESSION['flash_message'] = 'Pengaturan berhasil disimpan.';
            header('Location: profile.php?tab=' . $tab);
            exit;
        }

        if ($action === 'upload_file') {
            $type = $_POST['file_type'] ?? '';
            if (!empty($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
                if ($_FILES['file']['size'] > MAX_FILE_SIZE) {
                    $_SESSION['flash_error'] = 'File terlalu besar. Maksimal 2MB.';
                } else {
                    $ext = strtolower(pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION));
                    if ($type === 'profile') {
                        $allowed = ['jpg','jpeg','png','webp','svg'];
                        if (in_array($ext, $allowed)) {
                            $new_filename = time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
                            $dest = __DIR__ . '/../assets/img/' . $new_filename;
                            if (move_uploaded_file($_FILES['file']['tmp_name'], $dest)) {
                                $path = 'assets/img/' . $new_filename;
                                $old = setting('profile_image');
                                if ($old && $old !== $path) { @unlink(__DIR__ . '/../' . $old); }
                                $stmt = $db->prepare('INSERT INTO settings (setting_key, setting_value) VALUES (?, ?) ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)');
                                $stmt->execute(['profile_image', $path]);
                                $_SESSION['flash_message'] = 'Foto profil berhasil diperbarui.';
                            } else {
                                $_SESSION['flash_error'] = 'Gagal mengupload file. Coba lagi.';
                            }
                        } else { $_SESSION['flash_error'] = 'Format tidak didukung untuk foto profil. Gunakan JPG, PNG, WEBP, atau SVG.'; }
                    } elseif ($type === 'cv') {
                        $allowed = ['pdf'];
                        if (in_array($ext, $allowed)) {
                            $filename = 'CV_Yosef_Kelfian_Pambut.' . $ext;
                            $dest = __DIR__ . '/../assets/cv/' . $filename;
                            if (move_uploaded_file($_FILES['file']['tmp_name'], $dest)) {
                                $path = 'assets/cv/' . $filename;
                                $old = setting('cv_path');
                                if ($old && $old !== $path) { @unlink(__DIR__ . '/../' . $old); }
                                $stmt = $db->prepare('INSERT INTO settings (setting_key, setting_value) VALUES (?, ?) ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)');
                                $stmt->execute(['cv_path', $path]);
                                $_SESSION['flash_message'] = 'CV berhasil diperbarui.';
                            } else {
                                $_SESSION['flash_error'] = 'Gagal mengupload file. Coba lagi.';
                            }
                        } else { $_SESSION['flash_error'] = 'Format harus PDF.'; }
                    }
                }
            } else {
                $upload_error = $_FILES['file']['error'] ?? -1;
                if ($upload_error === UPLOAD_ERR_INI_SIZE || $upload_error === UPLOAD_ERR_FORM_SIZE) {
                    $_SESSION['flash_error'] = 'File terlalu besar. Maksimal 2MB.';
                } else {
                    $_SESSION['flash_error'] = 'Pilih file terlebih dahulu.';
                }
            }
            $db->commit();
            header('Location: profile.php?tab=files');
            exit;
        }

        if ($action === 'add_skill' || $action === 'edit_skill') {
            $id = (int)($_POST['id'] ?? 0);
            $name = trim($_POST['name'] ?? '');
            $icon = trim($_POST['icon'] ?? '');
            $label = trim($_POST['label'] ?? '');
            $percentage = (int)($_POST['percentage'] ?? 0);
            $sort_order = (int)($_POST['sort_order'] ?? 0);
            if (empty($name)) { $error = 'Nama skill tidak boleh kosong.'; }
            else {
                if ($action === 'add_skill') {
                    $q = $db->prepare('INSERT INTO skills (name, icon, label, percentage, sort_order) VALUES (?,?,?,?,?)');
                    $q->execute([$name, $icon, $label, $percentage, $sort_order]);
                } else {
                    $q = $db->prepare('UPDATE skills SET name=?, icon=?, label=?, percentage=?, sort_order=? WHERE id=?');
                    $q->execute([$name, $icon, $label, $percentage, $sort_order, $id]);
                }
            }
            $db->commit();
            header('Location: profile.php?tab=skills');
            exit;
        }

        if ($action === 'delete_skill') {
            $id = (int)($_POST['id'] ?? 0);
            $db->prepare('DELETE FROM skills WHERE id=?')->execute([$id]);
            $db->commit();
            header('Location: profile.php?tab=skills');
            exit;
        }

        if ($action === 'add_exp' || $action === 'edit_exp') {
            $id = (int)($_POST['id'] ?? 0);
            $type = $_POST['type'] ?? 'experience';
            $title = trim($_POST['title'] ?? '');
            $company = trim($_POST['company'] ?? '');
            $period = trim($_POST['period'] ?? '');
            $desc = trim($_POST['description'] ?? '');
            $sort_order = (int)($_POST['sort_order'] ?? 0);
            if (empty($title)) { $error = 'Judul tidak boleh kosong.'; }
            else {
                if ($action === 'add_exp') {
                    $q = $db->prepare('INSERT INTO experiences (type, title, company, period, description, sort_order) VALUES (?,?,?,?,?,?)');
                    $q->execute([$type, $title, $company, $period, $desc, $sort_order]);
                } else {
                    $q = $db->prepare('UPDATE experiences SET type=?, title=?, company=?, period=?, description=?, sort_order=? WHERE id=?');
                    $q->execute([$type, $title, $company, $period, $desc, $sort_order, $id]);
                }
            }
            $db->commit();
            header('Location: profile.php?tab=timeline');
            exit;
        }

        if ($action === 'delete_exp') {
            $id = (int)($_POST['id'] ?? 0);
            $db->prepare('DELETE FROM experiences WHERE id=?')->execute([$id]);
            $db->commit();
            header('Location: profile.php?tab=timeline');
            exit;
        }

        $db->commit();
    } catch (Exception $e) {
        $db->rollBack();
        $error = 'Error: ' . $e->getMessage();
    }
}

$edit_skill_id = (int)($_GET['edit_skill'] ?? 0);
$edit_skill = null;
if ($edit_skill_id) {
    $q = $db->prepare('SELECT * FROM skills WHERE id=?');
    $q->execute([$edit_skill_id]);
    $edit_skill = $q->fetch();
}

$edit_exp_id = (int)($_GET['edit_exp'] ?? 0);
$edit_exp = null;
if ($edit_exp_id) {
    $q = $db->prepare('SELECT * FROM experiences WHERE id=?');
    $q->execute([$edit_exp_id]);
    $edit_exp = $q->fetch();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Profile Admin | Yosef Kelfian Pambut</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        body { font-family:'Poppins',sans-serif; background:#0a0a1a; color:#e8e8f0; min-height:100vh; }
        .admin-nav { background:#12122a; border-bottom:1px solid #2a2a4e; padding:16px 24px; display:flex; align-items:center; justify-content:space-between; }
        .admin-nav .logo { font-family:'Bebas Neue',sans-serif; font-size:24px; color:#FFC107; }
        .admin-nav .nav-links { display:flex; align-items:center; gap:20px; }
        .admin-nav .nav-links a { color:#8888aa; text-decoration:none; font-size:14px; font-weight:500; transition:color 0.3s; }
        .admin-nav .nav-links a:hover, .admin-nav .nav-links a.active { color:#FFC107; }
        .admin-nav .nav-right { display:flex; align-items:center; gap:16px; }
        .admin-nav .nav-right span { color:#8888aa; font-size:14px; }
        .admin-nav .nav-right a { color:#ff4444; text-decoration:none; font-size:14px; font-weight:500; }
        .container { max-width:900px; margin:0 auto; padding:32px 24px; }

        .page-header { margin-bottom:28px; }
        .page-header h1 { font-family:'Bebas Neue',sans-serif; font-size:36px; letter-spacing:1px; }
        .page-header h1 span { color:#FFC107; }

        .flash { padding:14px 18px; border-radius:8px; margin-bottom:20px; font-size:14px; }
        .flash.success { background:rgba(34,197,94,0.1); border:1px solid #22c55e; color:#22c55e; }
        .flash.error { background:rgba(255,68,68,0.1); border:1px solid #ff4444; color:#ff4444; }

        .tabs { display:flex; gap:4px; margin-bottom:24px; flex-wrap:wrap; border-bottom:1px solid #2a2a4e; padding-bottom:8px; }
        .tabs a { padding:10px 20px; border-radius:8px 8px 0 0; text-decoration:none; color:#8888aa; font-size:14px; font-weight:500; transition:all 0.3s; }
        .tabs a:hover { color:#e8e8f0; background:rgba(255,255,255,0.03); }
        .tabs a.active { color:#FFC107; background:rgba(255,193,7,0.08); border-bottom:2px solid #FFC107; }

        .form-card { background:#12122a; border:1px solid #2a2a4e; border-radius:12px; padding:28px; margin-bottom:28px; }
        .form-card h2 { font-family:'Bebas Neue',sans-serif; font-size:22px; letter-spacing:1px; margin-bottom:20px; }
        .form-card h2 span { color:#FFC107; }

        .form-group { margin-bottom:16px; }
        .form-group label { display:block; font-size:13px; font-weight:500; color:#8888aa; margin-bottom:6px; }
        .form-group input, .form-group textarea, .form-group select { width:100%; padding:12px 14px; background:#0a0a1a; border:1px solid #2a2a4e; border-radius:8px; color:#e8e8f0; font-size:14px; font-family:inherit; outline:none; transition:border-color 0.3s; }
        .form-group input:focus, .form-group textarea:focus, .form-group select:focus { border-color:#FFC107; }
        .form-group textarea { resize:vertical; min-height:70px; }

        .form-grid-2 { display:grid; grid-template-columns:1fr 1fr; gap:14px; }
        .form-grid-3 { display:grid; grid-template-columns:1fr 1fr 1fr; gap:14px; }

        .btn { display:inline-flex; align-items:center; gap:8px; padding:10px 20px; font-family:inherit; font-size:14px; font-weight:600; border-radius:8px; border:none; cursor:pointer; text-decoration:none; transition:all 0.3s ease; }
        .btn-primary { background:linear-gradient(135deg,#FFC107,#c99406); color:#0a0a1a; }
        .btn-primary:hover { transform:translateY(-2px); box-shadow:0 4px 15px rgba(255,193,7,0.3); }
        .btn-unsaved { background:#ff4444 !important; color:#fff !important; border-color:#ff4444 !important; }
        .btn-unsaved:hover { background:#cc0000 !important; }
        .btn-sm { padding:6px 14px; font-size:13px; }
        .btn-edit { background:#1a1a3e; color:#FFC107; border:1px solid #FFC107; }
        .btn-edit:hover { background:#FFC107; color:#0a0a1a; }
        .btn-delete { background:transparent; color:#ff4444; border:1px solid #ff4444; }
        .btn-delete:hover { background:#ff4444; color:#fff; }
        .btn-outline { background:transparent; color:#FFC107; border:2px solid #FFC107; }
        .btn-success { background:rgba(34,197,94,0.15); color:#22c55e; border:1px solid #22c55e; }
        .btn-success:hover { background:#22c55e; color:#fff; }

        .file-upload-area { border:2px dashed #2a2a4e; border-radius:12px; padding:32px; text-align:center; margin-bottom:16px; }
        .file-upload-area p { color:#555577; font-size:14px; margin-top:8px; }
        .file-upload-area img { width:120px; height:120px; border-radius:50%; object-fit:cover; border:3px solid #FFC107; margin-bottom:12px; }
        .file-upload-area .current-file { color:#8888aa; font-size:13px; margin-top:8px; }

        .item-list { display:flex; flex-direction:column; gap:10px; }
        .item-row { display:flex; align-items:center; justify-content:space-between; background:#0a0a1a; border:1px solid #2a2a4e; border-radius:8px; padding:14px 18px; gap:12px; flex-wrap:wrap; }
        .item-row .info { flex:1; min-width:150px; }
        .item-row .info h4 { font-size:14px; font-weight:600; }
        .item-row .info p { font-size:13px; color:#8888aa; }
        .item-row .info small { font-size:12px; color:#555577; }
        .item-row .actions { display:flex; gap:6px; }

        @media (max-width:768px) { .form-grid-2, .form-grid-3 { grid-template-columns:1fr; } .tabs a { padding:8px 14px; font-size:13px; } }
    </style>
</head>
<body>
    <nav class="admin-nav">
        <div class="logo">YP<span style="color:#e8e8f0;"> Admin</span></div>
        <div class="nav-links">
            <a href="dashboard.php"><i class="fas fa-project-diagram"></i> Projects</a>
            <a href="profile.php" class="active"><i class="fas fa-user"></i> Profile</a>
        </div>
        <div class="nav-right">
            <span><i class="fas fa-user"></i> <?= htmlspecialchars($_SESSION['admin_user'] ?? 'Admin') ?></span>
            <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>
    </nav>

    <div class="container">
        <div class="page-header">
            <h1>Profile <span>Settings</span></h1>
        </div>

        <?php if ($flash): ?><div class="flash success"><?= $flash ?></div><?php endif; ?>
        <?php if ($flash_error): ?><div class="flash error"><?= htmlspecialchars($flash_error) ?></div><?php endif; ?>
        <?php if ($error): ?><div class="flash error"><?= htmlspecialchars($error) ?></div><?php endif; ?>

        <div class="tabs">
            <a href="profile.php?tab=basic" class="<?= $tab === 'basic' ? 'active' : '' ?>"><i class="fas fa-info-circle"></i> General</a>
            <a href="profile.php?tab=bio" class="<?= $tab === 'bio' ? 'active' : '' ?>"><i class="fas fa-file-alt"></i> Bio & Hero</a>
            <a href="profile.php?tab=seo" class="<?= $tab === 'seo' ? 'active' : '' ?>"><i class="fas fa-search"></i> SEO</a>
            <a href="profile.php?tab=social" class="<?= $tab === 'social' ? 'active' : '' ?>"><i class="fas fa-share-alt"></i> Social</a>
            <a href="profile.php?tab=files" class="<?= $tab === 'files' ? 'active' : '' ?>"><i class="fas fa-upload"></i> Files</a>
            <a href="profile.php?tab=skills" class="<?= $tab === 'skills' ? 'active' : '' ?>"><i class="fas fa-code"></i> Skills</a>
            <a href="profile.php?tab=timeline" class="<?= $tab === 'timeline' ? 'active' : '' ?>"><i class="fas fa-clock"></i> Timeline</a>
        </div>

        <?php if ($tab === 'basic'): ?>
        <div class="form-card">
            <h2>General <span>Info</span></h2>
            <form method="POST" autocomplete="off">
                <input type="hidden" name="action" value="save_settings">
                <div class="form-grid-2">
                    <div class="form-group">
                        <label>Site Name</label>
                        <input type="text" name="site_name" value="<?= htmlspecialchars(setting('site_name')) ?>">
                    </div>
                    <div class="form-group">
                        <label>Brand Initials</label>
                        <input type="text" name="brand_initials" value="<?= htmlspecialchars(setting('brand_initials')) ?>">
                    </div>
                </div>
                <div class="form-grid-2">
                    <div class="form-group">
                        <label>Full Name (About)</label>
                        <input type="text" name="about_name" value="<?= htmlspecialchars(setting('about_name')) ?>">
                    </div>
                    <div class="form-group">
                        <label>Role / Title</label>
                        <input type="text" name="about_role" value="<?= htmlspecialchars(setting('about_role')) ?>">
                    </div>
                </div>
                <div class="form-grid-2">
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email" value="<?= htmlspecialchars(setting('email')) ?>">
                    </div>
                    <div class="form-group">
                        <label>Phone</label>
                        <input type="text" name="phone" value="<?= htmlspecialchars(setting('phone')) ?>">
                    </div>
                </div>
                <div class="form-grid-2">
                    <div class="form-group">
                        <label>Location</label>
                        <input type="text" name="location" value="<?= htmlspecialchars(setting('location')) ?>">
                    </div>
                    <div class="form-group">
                        <label>Years Experience</label>
                        <input type="number" name="years_experience" value="<?= (int)setting('years_experience', '1') ?>">
                    </div>
                </div>
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button>
            </form>
        </div>
        <?php endif; ?>

        <?php if ($tab === 'bio'): ?>
        <div class="form-card">
            <h2>Hero <span>Section</span></h2>
            <form method="POST" autocomplete="off">
                <input type="hidden" name="action" value="save_settings">
                <div class="form-grid-2">
                    <div class="form-group">
                        <label>Hero Greeting</label>
                        <input type="text" name="hero_greeting" value="<?= htmlspecialchars(setting('hero_greeting')) ?>">
                    </div>
                    <div class="form-group">
                        <label>Hero Name (first part)</label>
                        <input type="text" name="hero_name" value="<?= htmlspecialchars(setting('hero_name')) ?>">
                    </div>
                </div>
                <div class="form-group">
                    <label>Hero Surname (highlighted)</label>
                    <input type="text" name="hero_surname" value="<?= htmlspecialchars(setting('hero_surname')) ?>">
                </div>
                <div class="form-group">
                    <label>Hero Tagline</label>
                    <textarea name="hero_tagline" rows="3"><?= htmlspecialchars(setting('hero_tagline')) ?></textarea>
                </div>
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button>
            </form>
        </div>

        <div class="form-card">
            <h2>About <span>Section</span></h2>
            <form method="POST" autocomplete="off">
                <input type="hidden" name="action" value="save_settings">
                <div class="form-group">
                    <label>Preview Title</label>
                    <input type="text" name="about_preview_title" value="<?= htmlspecialchars(setting('about_preview_title')) ?>">
                </div>
                <div class="form-group">
                    <label>Preview Bio (homepage)</label>
                    <textarea name="about_preview_bio" rows="3"><?= htmlspecialchars(setting('about_preview_bio')) ?></textarea>
                </div>
                <div class="form-group">
                    <label>Bio Paragraph 1</label>
                    <textarea name="about_bio_1" rows="4"><?= htmlspecialchars(setting('about_bio_1')) ?></textarea>
                </div>
                <div class="form-group">
                    <label>Bio Paragraph 2</label>
                    <textarea name="about_bio_2" rows="4"><?= htmlspecialchars(setting('about_bio_2')) ?></textarea>
                </div>
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button>
            </form>
        </div>
        <?php endif; ?>

        <?php if ($tab === 'seo'): ?>
        <div class="form-card">
            <h2>SEO <span>Settings</span></h2>
            <form method="POST" autocomplete="off">
                <input type="hidden" name="action" value="save_settings">
                <div class="form-group">
                    <label>Site Title</label>
                    <input type="text" name="site_title" value="<?= htmlspecialchars(setting('site_title')) ?>">
                </div>
                <div class="form-group">
                    <label>Meta Description</label>
                    <textarea name="site_description" rows="3"><?= htmlspecialchars(setting('site_description')) ?></textarea>
                </div>
                <div class="form-group">
                    <label>Meta Keywords</label>
                    <input type="text" name="site_keywords" value="<?= htmlspecialchars(setting('site_keywords')) ?>">
                </div>
                <div class="form-group">
                    <label>Meta Author</label>
                    <input type="text" name="site_author" value="<?= htmlspecialchars(setting('site_author')) ?>">
                </div>
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button>
            </form>
        </div>
        <?php endif; ?>

        <?php if ($tab === 'social'): ?>
        <div class="form-card">
            <h2>Social <span>Media</span></h2>
            <form method="POST" autocomplete="off">
                <input type="hidden" name="action" value="save_settings">
                <div class="form-group">
                    <label>GitHub URL</label>
                    <input type="url" name="github_url" value="<?= htmlspecialchars(setting('github_url')) ?>">
                </div>
                <div class="form-group">
                    <label>LinkedIn URL</label>
                    <input type="url" name="linkedin_url" value="<?= htmlspecialchars(setting('linkedin_url')) ?>">
                </div>
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button>
            </form>
        </div>
        <?php endif; ?>

        <?php if ($tab === 'files'): ?>
        <div class="form-card">
            <h2>Profile <span>Photo</span></h2>
            <form method="POST" enctype="multipart/form-data" autocomplete="off">
                <input type="hidden" name="action" value="upload_file">
                <input type="hidden" name="file_type" value="profile">
                <div class="file-upload-area">
                    <img src="../<?= htmlspecialchars(setting('profile_image')) ?>" alt="Profile">
                    <p>Format: JPG, PNG, WEBP, SVG. Maks 2MB.</p>
                </div>
                <div class="form-group">
                    <input type="file" name="file" accept=".jpg,.jpeg,.png,.webp,.svg">
                </div>
                <button type="submit" class="btn btn-primary"><i class="fas fa-upload"></i> Upload</button>
            </form>
        </div>

        <div class="form-card">
            <h2>CV / <span>Resume</span></h2>
            <form method="POST" enctype="multipart/form-data" autocomplete="off">
                <input type="hidden" name="action" value="upload_file">
                <input type="hidden" name="file_type" value="cv">
                <div class="file-upload-area">
                    <i class="fas fa-file-pdf" style="font-size:48px;color:#ff4444;margin-bottom:12px;display:block;"></i>
                    <p class="current-file">Current: <?= htmlspecialchars(setting('cv_path')) ?></p>
                    <p>Format: PDF.</p>
                </div>
                <div class="form-group">
                    <input type="file" name="file" accept=".pdf">
                </div>
                <button type="submit" class="btn btn-primary"><i class="fas fa-upload"></i> Upload</button>
            </form>
        </div>
        <?php endif; ?>

        <?php if ($tab === 'skills'): ?>
        <div class="form-card">
            <h2><?= $edit_skill ? 'Edit' : 'Tambah' ?> <span>Skill</span></h2>
            <form method="POST" autocomplete="off">
                <input type="hidden" name="action" value="<?= $edit_skill ? 'edit_skill' : 'add_skill' ?>">
                <?php if ($edit_skill): ?><input type="hidden" name="id" value="<?= $edit_skill['id'] ?>"><?php endif; ?>
                <div class="form-grid-3">
                    <div class="form-group">
                        <label>Nama Skill</label>
                        <input type="text" name="name" value="<?= $edit_skill ? htmlspecialchars($edit_skill['name']) : '' ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Icon (FontAwesome class)</label>
                        <input type="text" name="icon" value="<?= $edit_skill ? htmlspecialchars($edit_skill['icon']) : 'fas fa-code' ?>">
                    </div>
                    <div class="form-group">
                        <label>Persentase</label>
                        <input type="number" name="percentage" min="0" max="100" value="<?= $edit_skill ? (int)$edit_skill['percentage'] : 80 ?>" required>
                    </div>
                </div>
                <div class="form-grid-2">
                    <div class="form-group">
                        <label>Label / Deskripsi</label>
                        <input type="text" name="label" value="<?= $edit_skill ? htmlspecialchars($edit_skill['label'] ?? '') : '' ?>">
                    </div>
                    <div class="form-group">
                        <label>Urutan</label>
                        <input type="number" name="sort_order" value="<?= $edit_skill ? (int)$edit_skill['sort_order'] : 0 ?>">
                    </div>
                </div>
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> <?= $edit_skill ? 'Update' : 'Tambah' ?></button>
                <?php if ($edit_skill): ?><a href="profile.php?tab=skills" class="btn btn-outline"><i class="fas fa-times"></i> Batal</a><?php endif; ?>
            </form>
        </div>

        <div class="form-card">
            <h2>Daftar <span>Skills</span></h2>
            <div class="item-list">
                <?php foreach ($skills as $sk): ?>
                <div class="item-row">
                    <div class="info">
                        <h4><i class="<?= htmlspecialchars($sk['icon'] ?? 'fas fa-code') ?>" style="color:#FFC107;"></i> <?= htmlspecialchars($sk['name']) ?> (<?= (int)$sk['percentage'] ?>%)</h4>
                        <p><?= htmlspecialchars($sk['label'] ?? '') ?></p>
                        <small>Urutan: <?= (int)$sk['sort_order'] ?></small>
                    </div>
                    <div class="actions">
                        <a href="profile.php?tab=skills&edit_skill=<?= $sk['id'] ?>" class="btn btn-edit btn-sm"><i class="fas fa-pen"></i></a>
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="action" value="delete_skill">
                            <input type="hidden" name="id" value="<?= $sk['id'] ?>">
                            <button type="submit" class="btn btn-delete btn-sm" onclick="return confirm('Hapus skill ini?')"><i class="fas fa-trash"></i></button>
                        </form>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <?php if ($tab === 'timeline'): ?>
        <div class="form-card">
            <h2><?= $edit_exp ? 'Edit' : 'Tambah' ?> <span>Timeline</span></h2>
            <form method="POST" autocomplete="off">
                <input type="hidden" name="action" value="<?= $edit_exp ? 'edit_exp' : 'add_exp' ?>">
                <?php if ($edit_exp): ?><input type="hidden" name="id" value="<?= $edit_exp['id'] ?>"><?php endif; ?>
                <div class="form-grid-2">
                    <div class="form-group">
                        <label>Tipe</label>
                        <select name="type">
                            <option value="experience" <?= $edit_exp && $edit_exp['type'] === 'experience' ? 'selected' : '' ?>>Experience</option>
                            <option value="education" <?= $edit_exp && $edit_exp['type'] === 'education' ? 'selected' : '' ?>>Education</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Urutan</label>
                        <input type="number" name="sort_order" value="<?= $edit_exp ? (int)$edit_exp['sort_order'] : 0 ?>">
                    </div>
                </div>
                <div class="form-group">
                    <label>Judul</label>
                    <input type="text" name="title" value="<?= $edit_exp ? htmlspecialchars($edit_exp['title']) : '' ?>" required>
                </div>
                <div class="form-grid-2">
                    <div class="form-group">
                        <label>Perusahaan / Institusi</label>
                        <input type="text" name="company" value="<?= $edit_exp ? htmlspecialchars($edit_exp['company'] ?? '') : '' ?>">
                    </div>
                    <div class="form-group">
                        <label>Periode</label>
                        <input type="text" name="period" value="<?= $edit_exp ? htmlspecialchars($edit_exp['period'] ?? '') : '' ?>" placeholder="2021 - Present">
                    </div>
                </div>
                <div class="form-group">
                    <label>Deskripsi</label>
                    <textarea name="description" rows="3"><?= $edit_exp ? htmlspecialchars($edit_exp['description'] ?? '') : '' ?></textarea>
                </div>
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> <?= $edit_exp ? 'Update' : 'Tambah' ?></button>
                <?php if ($edit_exp): ?><a href="profile.php?tab=timeline" class="btn btn-outline"><i class="fas fa-times"></i> Batal</a><?php endif; ?>
            </form>
        </div>

        <div class="form-card">
            <h2>Daftar <span>Timeline</span></h2>
            <div class="item-list">
                <?php foreach ($experiences as $ex): ?>
                <div class="item-row">
                    <div class="info">
                        <h4><i class="<?= $ex['type'] === 'experience' ? 'fas fa-briefcase' : 'fas fa-graduation-cap' ?>" style="color:#FFC107;"></i> <?= htmlspecialchars($ex['title']) ?></h4>
                        <p><?= htmlspecialchars($ex['company'] ?? '') ?> — <?= htmlspecialchars($ex['period'] ?? '') ?></p>
                        <small>Tipe: <?= $ex['type'] ?> | Urutan: <?= (int)$ex['sort_order'] ?></small>
                    </div>
                    <div class="actions">
                        <a href="profile.php?tab=timeline&edit_exp=<?= $ex['id'] ?>" class="btn btn-edit btn-sm"><i class="fas fa-pen"></i></a>
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="action" value="delete_exp">
                            <input type="hidden" name="id" value="<?= $ex['id'] ?>">
                            <button type="submit" class="btn btn-delete btn-sm" onclick="return confirm('Hapus entri ini?')"><i class="fas fa-trash"></i></button>
                        </form>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
<script>
(function(){
  document.querySelectorAll('form').forEach(function(form){
    var btn = form.querySelector('button[type="submit"]');
    if (!btn) return;
    var inputs = form.querySelectorAll('input:not([type="hidden"]), textarea, select');
    function snapshot(){
      var s = {};
      inputs.forEach(function(el){ s[el.name || el.id] = el.value; });
      return s;
    }
    var initial = snapshot();
    function check(){
      var cur = snapshot();
      var dirty = false;
      for (var k in initial) { if (initial[k] !== cur[k]) { dirty = true; break; } }
      btn.classList.toggle('btn-unsaved', dirty);
    }
    inputs.forEach(function(el){ el.addEventListener('input', check); });
    form.addEventListener('submit', function(){ btn.classList.remove('btn-unsaved'); });
  });
})();
</script>
</body>
</html>
