<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/user.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['username'])) {
    $username = trim($_POST['username']);
    
    if (!empty($username)) {
        User::login($username);
        header('Location: index.php');
        exit;
    }
}

header('Location: index.php');
exit;
?>
