<?php
header('Content-Type: application/json');

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/user.php';
require_once __DIR__ . '/../includes/song.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

if (!User::isLoggedIn()) {
    echo json_encode(['error' => 'Not logged in']);
    exit;
}

$songId = isset($_POST['song_id']) ? intval($_POST['song_id']) : 0;
$voteType = isset($_POST['vote_type']) ? $_POST['vote_type'] : '';

if ($songId <= 0 || !in_array($voteType, ['keep', 'remove'])) {
    echo json_encode(['error' => 'Invalid parameters']);
    exit;
}

$userId = User::getCurrentUserId();

if (Vote::cast($songId, $userId, $voteType)) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['error' => 'Failed to cast vote']);
}
?>
