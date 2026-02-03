<?php
// Configuration file for Music System

// Database Configuration
define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
define('DB_USER', getenv('DB_USER') ?: 'root');
define('DB_PASS', getenv('DB_PASS') ?: '');
define('DB_NAME', getenv('DB_NAME') ?: 'music_system');

// Spotify API Configuration
define('SPOTIFY_CLIENT_ID', getenv('SPOTIFY_CLIENT_ID') ?: '');
define('SPOTIFY_CLIENT_SECRET', getenv('SPOTIFY_CLIENT_SECRET') ?: '');
define('SPOTIFY_REDIRECT_URI', getenv('SPOTIFY_REDIRECT_URI') ?: 'http://localhost/callback.php');

// Application Settings
define('SITE_NAME', '2004 Design Tech Music System');
define('VOTES_TO_REMOVE', 3); // Number of votes required to remove a song

// Session Configuration
ini_set('session.cookie_httponly', 1);
ini_set('session.use_strict_mode', 1);
if (!session_id()) {
    session_start();
}

// Timezone
date_default_timezone_set('America/Los_Angeles');
?>
