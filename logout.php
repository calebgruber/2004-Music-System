<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/user.php';

User::logout();
header('Location: index.php');
exit;
?>
