<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/user.php';
require_once __DIR__ . '/includes/spotify.php';

if (isset($_GET['code'])) {
    $code = $_GET['code'];
    
    // Exchange code for access token
    $tokenData = Spotify::handleCallback($code);
    
    if (isset($tokenData['access_token'])) {
        // Get user profile
        $profile = Spotify::getUserProfile($tokenData['access_token']);
        
        if (isset($profile['id'])) {
            // Create or login user
            $userId = User::getCurrentUserId();
            
            if (!$userId) {
                // Create new user based on Spotify profile
                $username = $profile['display_name'] ?: $profile['id'];
                $email = $profile['email'] ?? null;
                $userId = User::createOrUpdateUser($username, $email);
                $_SESSION['user_id'] = $userId;
                $_SESSION['username'] = $username;
            }
            
            // Update Spotify tokens
            User::updateSpotifyTokens($userId, $tokenData['access_token'], $tokenData['refresh_token'], $tokenData['expires_in']);
            User::setSpotifyUserId($userId, $profile['id']);
            
            header('Location: index.php?spotify=connected');
            exit;
        }
    }
}

header('Location: index.php?error=spotify_auth_failed');
exit;
?>
