<?php
require_once __DIR__ . '/db.php';

$GLOBALS['db'] = Database::getInstance();

$GLOBALS['settings'] = $GLOBALS['db']->query('SELECT setting_key, setting_value FROM settings')->fetchAll(PDO::FETCH_KEY_PAIR);

$GLOBALS['skills'] = $GLOBALS['db']->query('SELECT * FROM skills ORDER BY sort_order ASC')->fetchAll();

$GLOBALS['experiences'] = $GLOBALS['db']->query('SELECT * FROM experiences ORDER BY sort_order ASC')->fetchAll();

function setting($key, $default = '') {
    return $GLOBALS['settings'][$key] ?? $default;
}
