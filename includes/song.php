<?php
// Song management functions

require_once __DIR__ . '/db.php';

class Song {
    public static function getAll($activeOnly = true) {
        $db = getDB();
        $sql = "SELECT s.*, u.username as added_by_username, 
                COUNT(DISTINCT CASE WHEN v.vote_type = 'remove' THEN v.id END) as remove_votes,
                COUNT(DISTINCT CASE WHEN v.vote_type = 'keep' THEN v.id END) as keep_votes
                FROM songs s 
                LEFT JOIN users u ON s.added_by = u.id 
                LEFT JOIN votes v ON s.id = v.song_id";
        
        if ($activeOnly) {
            $sql .= " WHERE s.is_active = 1";
        }
        
        $sql .= " GROUP BY s.id ORDER BY s.added_at DESC";
        
        $result = $db->query($sql);
        $songs = [];
        while ($row = $result->fetch_assoc()) {
            $songs[] = $row;
        }
        return $songs;
    }
    
    public static function add($title, $artist, $addedBy, $spotifyUri = null, $spotifyId = null, $album = null, $durationMs = null) {
        $db = getDB();
        $stmt = $db->prepare("INSERT INTO songs (title, artist, spotify_uri, spotify_id, album, duration_ms, added_by) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssiii", $title, $artist, $spotifyUri, $spotifyId, $album, $durationMs, $addedBy);
        
        if ($stmt->execute()) {
            return $db->lastInsertId();
        }
        return false;
    }
    
    public static function delete($songId) {
        $db = getDB();
        $stmt = $db->prepare("UPDATE songs SET is_active = 0 WHERE id = ?");
        $stmt->bind_param("i", $songId);
        return $stmt->execute();
    }
    
    public static function getById($songId) {
        $db = getDB();
        $stmt = $db->prepare("SELECT s.*, u.username as added_by_username FROM songs s LEFT JOIN users u ON s.added_by = u.id WHERE s.id = ?");
        $stmt->bind_param("i", $songId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }
}

class Vote {
    public static function cast($songId, $userId, $voteType) {
        $db = getDB();
        
        // Check if user already voted
        $stmt = $db->prepare("SELECT id, vote_type FROM votes WHERE song_id = ? AND user_id = ?");
        $stmt->bind_param("ii", $songId, $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($row = $result->fetch_assoc()) {
            // Update existing vote
            $stmt = $db->prepare("UPDATE votes SET vote_type = ? WHERE id = ?");
            $stmt->bind_param("si", $voteType, $row['id']);
            $stmt->execute();
        } else {
            // Insert new vote
            $stmt = $db->prepare("INSERT INTO votes (song_id, user_id, vote_type) VALUES (?, ?, ?)");
            $stmt->bind_param("iis", $songId, $userId, $voteType);
            $stmt->execute();
        }
        
        // Check if song should be removed
        self::checkRemovalThreshold($songId);
        
        return true;
    }
    
    public static function checkRemovalThreshold($songId) {
        $db = getDB();
        
        // Count votes
        $stmt = $db->prepare("SELECT 
            COUNT(CASE WHEN vote_type = 'remove' THEN 1 END) as remove_votes,
            COUNT(CASE WHEN vote_type = 'keep' THEN 1 END) as keep_votes
            FROM votes WHERE song_id = ?");
        $stmt->bind_param("i", $songId);
        $stmt->execute();
        $result = $stmt->get_result();
        $votes = $result->fetch_assoc();
        
        // If remove votes exceed threshold and more than keep votes, remove song
        if ($votes['remove_votes'] >= VOTES_TO_REMOVE && $votes['remove_votes'] > $votes['keep_votes']) {
            Song::delete($songId);
            return true;
        }
        
        return false;
    }
    
    public static function getUserVote($songId, $userId) {
        $db = getDB();
        $stmt = $db->prepare("SELECT vote_type FROM votes WHERE song_id = ? AND user_id = ?");
        $stmt->bind_param("ii", $songId, $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        return $row ? $row['vote_type'] : null;
    }
}
?>
