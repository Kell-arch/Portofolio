<?php
require_once __DIR__ . '/config.php';

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: index.php');
    exit;
}

$db = Database::getInstance();
$edit_item = null;

$flash_message = $_SESSION['flash_message'] ?? '';
unset($_SESSION['flash_message']);
$message = '';

if (isset($_GET['edit'])) {
    $stmt = $db->prepare('SELECT * FROM projects WHERE id = ?');
    $stmt->execute([(int)$_GET['edit']]);
    $edit_item = $stmt->fetch();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'add' || $action === 'edit') {
        $title = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $tags = trim($_POST['tags'] ?? '');
        $source_url = trim($_POST['source_url'] ?? '');
        $demo_url = trim($_POST['demo_url'] ?? '');
        $youtube_url = trim($_POST['youtube_url'] ?? '');
        $edit_id = (int) ($_POST['id'] ?? 0);
        $thumbnail_path = '';

        if (empty($title)) {
            $message = '<div class="alert error">Judul tidak boleh kosong.</div>';
        } else {
            if ($action === 'edit' && $edit_item) {
                $thumbnail_path = $edit_item['thumbnail'] ?? '';
            }

            if (!empty($_FILES['thumbnail']) && $_FILES['thumbnail']['error'] === UPLOAD_ERR_OK) {
                $file = $_FILES['thumbnail'];
                $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

                if ($file['size'] > MAX_FILE_SIZE) {
                    $message = '<div class="alert error">File terlalu besar. Maksimal 2MB.</div>';
                } elseif (!in_array($ext, ALLOWED_EXT)) {
                    $message = '<div class="alert error">Format file tidak didukung. Gunakan jpg, png, atau webp.</div>';
                } else {
                    if (!is_dir(UPLOAD_DIR)) mkdir(UPLOAD_DIR, 0755, true);

                    if (!empty($thumbnail_path)) {
                        $old_file = __DIR__ . '/../' . $thumbnail_path;
                        if (file_exists($old_file)) unlink($old_file);
                    }

                    $new_filename = time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
                    if (move_uploaded_file($file['tmp_name'], UPLOAD_DIR . $new_filename)) {
                        $thumbnail_path = UPLOAD_URL . $new_filename;
                    }
                }
            }

            if (empty($message)) {
                if ($action === 'add') {
                    $stmt = $db->prepare('INSERT INTO projects (title, description, tags, thumbnail, source_url, demo_url, youtube_url, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, CURDATE())');
                    $stmt->execute([$title, $description, $tags, $thumbnail_path ?: null, $source_url, $demo_url, $youtube_url ?: null]);
                    $_SESSION['flash_message'] = '<div class="alert success">Project berhasil ditambahkan.</div>';
                    header('Location: dashboard.php');
                    exit;
                } else {
                    $stmt = $db->prepare('UPDATE projects SET title = ?, description = ?, tags = ?, thumbnail = ?, source_url = ?, demo_url = ?, youtube_url = ? WHERE id = ?');
                    $stmt->execute([$title, $description, $tags, $thumbnail_path ?: null, $source_url, $demo_url, $youtube_url ?: null, $edit_id]);
                    $_SESSION['flash_message'] = '<div class="alert success">Project berhasil diperbarui.</div>';
                    header('Location: dashboard.php');
                    exit;
                }
            }
        }
    }

    if ($action === 'delete') {
        $delete_id = (int) ($_POST['id'] ?? 0);
        $stmt = $db->prepare('SELECT thumbnail FROM projects WHERE id = ?');
        $stmt->execute([$delete_id]);
        $old = $stmt->fetch();
        if ($old && !empty($old['thumbnail'])) {
            $old_file = __DIR__ . '/../' . $old['thumbnail'];
            if (file_exists($old_file)) unlink($old_file);
        }
        $stmt = $db->prepare('DELETE FROM projects WHERE id = ?');
        $stmt->execute([$delete_id]);
        $_SESSION['flash_message'] = '<div class="alert success">Project berhasil dihapus.</div>';
        header('Location: dashboard.php');
        exit;
    }
}

$data = $db->query('SELECT * FROM projects ORDER BY created_at DESC, id DESC')->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin | Yosef Kelfian Pambut</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        body { font-family:'Poppins',sans-serif; background:#0a0a1a; color:#e8e8f0; min-height:100vh; }
        .admin-nav { background:#12122a; border-bottom:1px solid #2a2a4e; padding:16px 24px; display:flex; align-items:center; justify-content:space-between; }
        .admin-nav .logo { font-family:'Bebas Neue',sans-serif; font-size:24px; color:#FFC107; }
        .admin-nav .nav-right { display:flex; align-items:center; gap:16px; }
        .admin-nav .nav-right span { color:#8888aa; font-size:14px; }
        .admin-nav .nav-right a { color:#ff4444; text-decoration:none; font-size:14px; font-weight:500; }
        .admin-nav .nav-right a:hover { text-decoration:underline; }
        .container { max-width:1100px; margin:0 auto; padding:32px 24px; }

        .page-header { display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:16px; margin-bottom:28px; }
        .page-header h1 { font-family:'Bebas Neue',sans-serif; font-size:36px; letter-spacing:1px; }
        .page-header h1 span { color:#FFC107; }

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

        .alert { padding:14px 18px; border-radius:8px; margin-bottom:20px; font-size:14px; }
        .alert.success { background:rgba(34,197,94,0.1); border:1px solid #22c55e; color:#22c55e; }
        .alert.error { background:rgba(255,68,68,0.1); border:1px solid #ff4444; color:#ff4444; }

        .form-card { background:#12122a; border:1px solid #2a2a4e; border-radius:12px; padding:28px; margin-bottom:28px; }
        .form-card h2 { font-family:'Bebas Neue',sans-serif; font-size:24px; letter-spacing:1px; margin-bottom:20px; }
        .form-card h2 span { color:#FFC107; }

        .form-group { margin-bottom:18px; }
        .form-group label { display:block; font-size:13px; font-weight:500; color:#8888aa; margin-bottom:6px; }
        .form-group input, .form-group textarea { width:100%; padding:12px 14px; background:#0a0a1a; border:1px solid #2a2a4e; border-radius:8px; color:#e8e8f0; font-size:14px; font-family:inherit; outline:none; transition:border-color 0.3s ease; }
        .form-group input:focus, .form-group textarea:focus { border-color:#FFC107; }
        .form-group textarea { resize:vertical; min-height:80px; }
        .form-group.file-input input { padding:10px; }
        .form-group .current-thumb { margin-top:8px; font-size:13px; color:#8888aa; display:flex; align-items:center; gap:8px; }
        .form-group .current-thumb img { width:60px; height:40px; border-radius:4px; object-fit:cover; }
        .form-row { display:flex; gap:12px; flex-wrap:wrap; }
        .form-row .btn { flex:1; justify-content:center; }
        .form-grid-2 { display:grid; grid-template-columns:1fr 1fr; gap:14px; }

        .table-wrapper { overflow-x:auto; }
        table { width:100%; border-collapse:collapse; font-size:14px; }
        thead th { text-align:left; padding:12px 14px; background:#12122a; color:#8888aa; font-weight:600; font-size:13px; text-transform:uppercase; letter-spacing:1px; border-bottom:1px solid #2a2a4e; }
        tbody td { padding:14px; border-bottom:1px solid #1a1a3e; vertical-align:middle; }
        tbody tr:hover { background:rgba(255,255,255,0.02); }
        .td-thumb { width:60px; }
        .td-thumb img { width:50px; height:34px; border-radius:4px; object-fit:cover; }
        .td-thumb .no-thumb { width:50px; height:34px; background:#1a1a3e; border-radius:4px; display:flex; align-items:center; justify-content:center; color:#555577; font-size:12px; }
        .td-title { font-weight:500; }
        .td-meta { color:#8888aa; font-size:13px; max-width:200px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
        .td-actions { white-space:nowrap; }
        .td-date { color:#555577; font-size:13px; }

        .empty-state { text-align:center; padding:60px 20px; color:#555577; }
        .empty-state i { font-size:48px; margin-bottom:16px; color:#2a2a4e; }
        .empty-state p { font-size:15px; }

        @media (max-width:768px) { .form-grid-2 { grid-template-columns:1fr; } table { font-size:13px; } thead th, tbody td { padding:10px; } }
    </style>
</head>
<body>
    <nav class="admin-nav">
        <div class="logo" style="display:flex;align-items:center;gap:20px;">
            YP<span style="color:#e8e8f0;"> Admin</span>
            <div style="display:flex;gap:12px;font-family:Poppins,sans-serif;font-size:14px;font-weight:400;">
                <a href="dashboard.php" style="color:#FFC107;text-decoration:none;"><i class="fas fa-project-diagram"></i> Projects</a>
                <a href="profile.php" style="color:#8888aa;text-decoration:none;transition:color 0.3s;" onmouseover="this.style.color='#FFC107'" onmouseout="this.style.color='#8888aa'"><i class="fas fa-user"></i> Profile</a>
            </div>
        </div>
        <div class="nav-right">
            <span><i class="fas fa-user"></i> <?= htmlspecialchars($_SESSION['admin_user'] ?? 'Admin') ?></span>
            <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>
    </nav>

    <div class="container">
        <?php
        $show_form = $edit_item || isset($_GET['add']);
        $form_action = $edit_item ? 'edit' : 'add';
        $form_id = $edit_item ? (int)$edit_item['id'] : 0;
        ?>

        <div class="page-header">
            <h1>Projects <span>Panel</span></h1>
            <?php if (!$show_form): ?>
                <a href="dashboard.php?add=1" class="btn btn-primary"><i class="fas fa-plus"></i> Tambah Baru</a>
            <?php endif; ?>
        </div>

        <?= $flash_message ?>
        <?= $message ?>

        <?php if ($show_form): ?>
        <div class="form-card">
            <h2><?= $edit_item ? 'Edit' : 'Tambah' ?> <span>Project</span></h2>
            <form method="POST" enctype="multipart/form-data" autocomplete="off">
                <input type="hidden" name="action" value="<?= $form_action ?>">
                <?php if ($edit_item): ?>
                    <input type="hidden" name="id" value="<?= $form_id ?>">
                <?php endif; ?>

                <div class="form-group">
                    <label for="title">Judul Project</label>
                    <input type="text" id="title" name="title" value="<?= $edit_item ? htmlspecialchars($edit_item['title']) : '' ?>" placeholder="Nama project" required>
                </div>

                <div class="form-group">
                    <label for="description">Deskripsi</label>
                    <textarea id="description" name="description" placeholder="Deskripsi project"><?= $edit_item ? htmlspecialchars($edit_item['description'] ?? '') : '' ?></textarea>
                </div>

                <div class="form-grid-2">
                    <div class="form-group">
                        <label for="tags">Tags <span style="color:#555577;font-weight:400;">(pisahkan dengan koma)</span></label>
                        <input type="text" id="tags" name="tags" value="<?= $edit_item ? htmlspecialchars($edit_item['tags'] ?? '') : '' ?>" placeholder="Python, Node.js, SQL">
                    </div>
                    <div class="form-group file-input">
                        <label for="thumbnail">Thumbnail <span style="color:#555577;font-weight:400;">(opsional)</span></label>
                        <input type="file" id="thumbnail" name="thumbnail" accept=".jpg,.jpeg,.png,.webp">
                        <?php if ($edit_item && !empty($edit_item['thumbnail'])): ?>
                            <div class="current-thumb">Current: <img src="../<?= htmlspecialchars($edit_item['thumbnail']) ?>" alt="Preview"></div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="form-grid-2">
                    <div class="form-group">
                        <label for="source_url">Source URL</label>
                        <input type="url" id="source_url" name="source_url" value="<?= $edit_item ? htmlspecialchars($edit_item['source_url'] ?? '') : '' ?>" placeholder="https://github.com/user/repo">
                    </div>
                    <div class="form-group">
                        <label for="demo_url">Demo URL</label>
                        <input type="url" id="demo_url" name="demo_url" value="<?= $edit_item ? htmlspecialchars($edit_item['demo_url'] ?? '') : '' ?>" placeholder="https://demo.example.com">
                    </div>
                </div>

                <div class="form-group">
                    <label for="youtube_url">YouTube URL <span style="color:#555577;font-weight:400;">(opsional — untuk tombol Docs di card project)</span></label>
                    <input type="url" id="youtube_url" name="youtube_url" value="<?= $edit_item ? htmlspecialchars($edit_item['youtube_url'] ?? '') : '' ?>" placeholder="https://youtube.com/watch?v=xxx">
                </div>

                <div class="form-row">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button>
                    <a href="dashboard.php" class="btn btn-delete" style="flex:1;justify-content:center;display:flex;align-items:center;gap:8px;border-radius:8px;text-decoration:none;padding:10px 14px;font-weight:600;font-size:14px;font-family:inherit;"><i class="fas fa-times"></i> Batal</a>
                </div>
            </form>
        </div>
        <?php endif; ?>

        <div class="table-wrapper">
            <table>
                <thead>
                    <tr><th>Thumbnail</th><th>Judul</th><th>Tags</th><th>YouTube</th><th>Aksi</th></tr>
                </thead>
                <tbody>
                    <?php if (empty($data)): ?>
                    <tr><td colspan="5"><div class="empty-state"><i class="fas fa-folder-open"></i><p>Belum ada project. <a href="dashboard.php?add=1" style="color:#FFC107;">Tambah sekarang</a>.</p></div></td></tr>
                    <?php else: ?>
                        <?php foreach ($data as $item): ?>
                        <tr>
                            <td class="td-thumb">
                                <?php if (!empty($item['thumbnail'])): ?>
                                    <img src="../<?= htmlspecialchars($item['thumbnail']) ?>" alt="<?= htmlspecialchars($item['title']) ?>">
                                <?php else: ?>
                                    <div class="no-thumb"><i class="fas fa-code"></i></div>
                                <?php endif; ?>
                            </td>
                            <td class="td-title"><?= htmlspecialchars($item['title']) ?></td>
                            <td class="td-meta"><?= htmlspecialchars($item['tags'] ?? '') ?></td>
                            <td class="td-meta">
                                <?php if (!empty($item['youtube_url'])): ?>
                                    <a href="<?= htmlspecialchars($item['youtube_url']) ?>" target="_blank" style="color:#ff0000;text-decoration:none;" title="Docs available"><i class="fab fa-youtube"></i></a>
                                <?php else: ?>
                                    <span style="color:#555577;">—</span>
                                <?php endif; ?>
                            </td>
                            <td class="td-actions">
                                <a href="dashboard.php?edit=<?= (int)$item['id'] ?>" class="btn btn-edit btn-sm"><i class="fas fa-pen"></i></a>
                                <form method="POST" style="display:inline-flex;gap:4px;align-items:center;" onsubmit="event.preventDefault(); showConfirm('Hapus project ini?', function(){ this.submit(); }.bind(this));">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id" value="<?= (int)$item['id'] ?>">
                                    <button type="submit" class="btn btn-delete btn-sm"><i class="fas fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

<style>
.modal-overlay{position:fixed;inset:0;z-index:9999;background:rgba(0,0,0,0.7);backdrop-filter:blur(4px);display:flex;align-items:center;justify-content:center;opacity:0;pointer-events:none;transition:opacity 0.3s ease;}
.modal-overlay.open{opacity:1;pointer-events:all;}
.modal-box{background:#1a1a3e;border:1px solid #2a2a4e;border-radius:16px;padding:36px;max-width:420px;width:90%;text-align:center;transform:scale(0.9);transition:transform 0.3s ease;}
.modal-overlay.open .modal-box{transform:scale(1);}
.modal-icon{width:64px;height:64px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:26px;margin:0 auto 16px;}
.modal-icon.confirm{background:rgba(17,17,132,0.15);color:#1a1a9e;}
.modal-box h3{font-size:20px;font-weight:600;margin-bottom:8px;}
.modal-box p{color:#8888aa;font-size:14px;margin-bottom:24px;line-height:1.6;}
.modal-actions{display:flex;gap:10px;justify-content:center;}
</style>
<script>
function showConfirm(msg,cb){var o=document.createElement('div');o.className='modal-overlay';o.innerHTML='<div class="modal-box"><div class="modal-icon confirm"><i class="fas fa-question-circle"></i></div><h3>Confirm</h3><p>'+msg+'</p><div class="modal-actions"><button class="btn btn-outline btn-sm" onclick="this.closest(\'.modal-overlay\').classList.remove(\'open\');setTimeout(function(){this.closest(\'.modal-overlay\').remove()},300)"><i class="fas fa-times"></i> Cancel</button><button class="btn btn-primary btn-sm" id="confirmYes"><i class="fas fa-check"></i> Yes</button></div></div>';document.body.appendChild(o);requestAnimationFrame(function(){o.classList.add('open')});document.getElementById('confirmYes').addEventListener('click',function(){o.classList.remove('open');setTimeout(function(){o.remove();if(typeof cb==='function')cb()},300)});}

(function(){
  document.querySelectorAll('form').forEach(function(form){
    var btn = form.querySelector('button[type="submit"]');
    if (!btn) return;
    var inputs = form.querySelectorAll('input:not([type="hidden"]):not([type="file"]), textarea, select');
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
