<?php
// Spotify API helper functions

require_once __DIR__ . '/../config/config.php';

class Spotify {
    private static function getAccessToken($userId) {
        require_once __DIR__ . '/db.php';
        require_once __DIR__ . '/user.php';
        
        $user = User::getCurrentUser();
        if (!$user || !$user['spotify_access_token']) {
            return null;
        }
        
        // Check if token is expired
        if (strtotime($user['token_expires_at']) <= time()) {
            // Refresh token
            return self::refreshAccessToken($userId, $user['spotify_refresh_token']);
        }
        
        return $user['spotify_access_token'];
    }
    
    private static function refreshAccessToken($userId, $refreshToken) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://accounts.spotify.com/api/token');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
            'grant_type' => 'refresh_token',
            'refresh_token' => $refreshToken
        ]));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Basic ' . base64_encode(SPOTIFY_CLIENT_ID . ':' . SPOTIFY_CLIENT_SECRET),
            'Content-Type: application/x-www-form-urlencoded'
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
        $response = curl_exec($ch);
        curl_close($ch);
        
        $data = json_decode($response, true);
        
        if (isset($data['access_token'])) {
            User::updateSpotifyTokens($userId, $data['access_token'], $refreshToken, $data['expires_in']);
            return $data['access_token'];
        }
        
        return null;
    }
    
    public static function searchTracks($query, $userId) {
        $accessToken = self::getAccessToken($userId);
        if (!$accessToken) {
            return ['error' => 'Not authenticated with Spotify'];
        }
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://api.spotify.com/v1/search?' . http_build_query([
            'q' => $query,
            'type' => 'track',
            'limit' => 10
        ]));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $accessToken
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
        $response = curl_exec($ch);
        curl_close($ch);
        
        return json_decode($response, true);
    }
    
    public static function getAuthUrl() {
        $params = [
            'client_id' => SPOTIFY_CLIENT_ID,
            'response_type' => 'code',
            'redirect_uri' => SPOTIFY_REDIRECT_URI,
            'scope' => 'playlist-modify-public playlist-modify-private user-read-email'
        ];
        
        return 'https://accounts.spotify.com/authorize?' . http_build_query($params);
    }
    
    public static function handleCallback($code) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://accounts.spotify.com/api/token');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
            'grant_type' => 'authorization_code',
            'code' => $code,
            'redirect_uri' => SPOTIFY_REDIRECT_URI
        ]));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Basic ' . base64_encode(SPOTIFY_CLIENT_ID . ':' . SPOTIFY_CLIENT_SECRET),
            'Content-Type: application/x-www-form-urlencoded'
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
        $response = curl_exec($ch);
        curl_close($ch);
        
        return json_decode($response, true);
    }
    
    public static function getUserProfile($accessToken) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://api.spotify.com/v1/me');
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $accessToken
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
        $response = curl_exec($ch);
        curl_close($ch);
        
        return json_decode($response, true);
    }
}
?>
