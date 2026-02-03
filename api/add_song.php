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

$title = isset($_POST['title']) ? trim($_POST['title']) : '';
$artist = isset($_POST['artist']) ? trim($_POST['artist']) : '';
$album = isset($_POST['album']) ? trim($_POST['album']) : null;
$spotifyUri = isset($_POST['spotify_uri']) ? trim($_POST['spotify_uri']) : null;
$spotifyId = isset($_POST['spotify_id']) ? trim($_POST['spotify_id']) : null;
$durationMs = isset($_POST['duration_ms']) ? intval($_POST['duration_ms']) : null;

if (empty($title) || empty($artist)) {
    echo json_encode(['error' => 'Title and artist are required']);
    exit;
}

$userId = User::getCurrentUserId();
$songId = Song::add($title, $artist, $userId, $spotifyUri, $spotifyId, $album, $durationMs);

if ($songId) {
    echo json_encode(['success' => true, 'song_id' => $songId]);
} else {
    echo json_encode(['error' => 'Failed to add song']);
}
?>
