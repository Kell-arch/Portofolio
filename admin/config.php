<?php
session_start();

require_once __DIR__ . '/config/db.php';

define('ADMIN_USERNAME', 'kelfin');
define('ADMIN_PASSWORD_HASH', '$2y$12$ADOSKzinp406N26Qy7qxnuiOE0oan6RsARY7ASn.VBr4y6GmLJ0DK');
define('UPLOAD_DIR', __DIR__ . '/../assets/img/dokumentasi/');
define('UPLOAD_URL', 'assets/img/dokumentasi/');
define('MAX_FILE_SIZE', 2 * 1024 * 1024);
define('ALLOWED_EXT', ['jpg', 'jpeg', 'png', 'webp']);
