<?php
header('Content-Type: application/json');

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/user.php';
require_once __DIR__ . '/../includes/spotify.php';

if (!User::isLoggedIn()) {
    echo json_encode(['error' => 'Not logged in']);
    exit;
}

$query = isset($_GET['q']) ? trim($_GET['q']) : '';

if (empty($query)) {
    echo json_encode(['error' => 'Query required']);
    exit;
}

$userId = User::getCurrentUserId();
$results = Spotify::searchTracks($query, $userId);

echo json_encode($results);
?>
