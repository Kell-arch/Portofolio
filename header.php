<?php
$pageTitle = $pageTitle ?? 'Home';
$activePage = $activePage ?? 'home';
require_once __DIR__ . '/admin/config/load_settings.php';
$db = $GLOBALS['db'];
$settings = $GLOBALS['settings'];
$skills = $GLOBALS['skills'];
$experiences = $GLOBALS['experiences'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?> | <?= htmlspecialchars(setting('site_name')) ?></title>
    <meta name="description" content="<?= htmlspecialchars(setting('site_description')) ?>">
    <meta name="keywords" content="<?= htmlspecialchars(setting('site_keywords')) ?>">
    <meta name="author" content="<?= htmlspecialchars(setting('site_author')) ?>">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">

    <link rel="icon" type="image/svg+xml" href="assets/img/favicon.svg">
</head>
