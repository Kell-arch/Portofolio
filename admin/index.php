<?php
require_once __DIR__ . '/config.php';

if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header('Location: dashboard.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    if ($username === ADMIN_USERNAME && password_verify($password, ADMIN_PASSWORD_HASH)) {
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_user'] = $username;
        header('Location: dashboard.php');
        exit;
    } else {
        $error = 'Username atau password salah.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login | Yosef Kelfian Pambut</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Poppins', sans-serif;
            background: #0a0a1a;
            color: #e8e8f0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-container {
            background: #12122a;
            border: 1px solid #2a2a4e;
            border-radius: 16px;
            padding: 40px;
            width: 100%;
            max-width: 420px;
        }
        .login-container h1 {
            text-align: center;
            font-size: 24px;
            margin-bottom: 4px;
        }
        .login-container .subtitle {
            text-align: center;
            color: #8888aa;
            font-size: 14px;
            margin-bottom: 28px;
        }
        .form-group { margin-bottom: 20px; }
        .form-group label {
            display: block;
            font-size: 14px;
            font-weight: 500;
            color: #8888aa;
            margin-bottom: 6px;
        }
        .form-group input {
            width: 100%;
            padding: 14px 16px;
            background: #0a0a1a;
            border: 1px solid #2a2a4e;
            border-radius: 8px;
            color: #e8e8f0;
            font-size: 15px;
            font-family: inherit;
            outline: none;
            transition: border-color 0.3s ease;
        }
        .form-group input:focus {
            border-color: #FFC107;
        }
        .btn {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #FFC107, #c99406);
            color: #0a0a1a;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            font-family: inherit;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(255, 193, 7, 0.3);
        }
        .error {
            background: rgba(255, 68, 68, 0.1);
            border: 1px solid #ff4444;
            color: #ff4444;
            padding: 12px 16px;
            border-radius: 8px;
            font-size: 14px;
            margin-bottom: 20px;
            text-align: center;
        }
        .brand {
            font-family: 'Bebas Neue', sans-serif;
            font-size: 48px;
            color: #FFC107;
            text-align: center;
            margin-bottom: 8px;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="brand">YP.</div>
        <h1>Admin Login</h1>
        <p class="subtitle">Masuk ke panel administrasi</p>

        <?php if ($error): ?>
            <div class="error"><i class="fas fa-exclamation-triangle"></i> <?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" autocomplete="off">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" placeholder="Masukkan username" required autocomplete="off">
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="Masukkan password" required>
            </div>
            <button type="submit" class="btn"><i class="fas fa-lock"></i> Masuk</button>
        </form>
    </div>
</body>
</html>
