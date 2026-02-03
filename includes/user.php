<?php
// User management functions

require_once __DIR__ . '/db.php';

class User {
    public static function getCurrentUserId() {
        return isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
    }
    
    public static function getCurrentUser() {
        $userId = self::getCurrentUserId();
        if (!$userId) {
            return null;
        }
        
        $db = getDB();
        $stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }
    
    public static function isLoggedIn() {
        return self::getCurrentUserId() !== null;
    }
    
    public static function createOrUpdateUser($username, $email = null) {
        $db = getDB();
        
        // Check if user exists
        $stmt = $db->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($row = $result->fetch_assoc()) {
            return $row['id'];
        }
        
        // Create new user
        $stmt = $db->prepare("INSERT INTO users (username, email) VALUES (?, ?)");
        $stmt->bind_param("ss", $username, $email);
        $stmt->execute();
        return $db->lastInsertId();
    }
    
    public static function updateSpotifyTokens($userId, $accessToken, $refreshToken, $expiresIn) {
        $db = getDB();
        $expiresAt = date('Y-m-d H:i:s', time() + $expiresIn);
        
        $stmt = $db->prepare("UPDATE users SET spotify_access_token = ?, spotify_refresh_token = ?, token_expires_at = ? WHERE id = ?");
        $stmt->bind_param("sssi", $accessToken, $refreshToken, $expiresAt, $userId);
        return $stmt->execute();
    }
    
    public static function setSpotifyUserId($userId, $spotifyUserId) {
        $db = getDB();
        $stmt = $db->prepare("UPDATE users SET spotify_user_id = ? WHERE id = ?");
        $stmt->bind_param("si", $spotifyUserId, $userId);
        return $stmt->execute();
    }
    
    public static function login($username) {
        $userId = self::createOrUpdateUser($username);
        $_SESSION['user_id'] = $userId;
        $_SESSION['username'] = $username;
        return $userId;
    }
    
    public static function logout() {
        session_destroy();
    }
}
?>
