-- Database schema for 2004 Design Tech Music System
CREATE DATABASE IF NOT EXISTS voxelnodes_music_2004 CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE voxelnodes_music_2004;

-- Users table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL UNIQUE,
    email VARCHAR(255),
    spotify_user_id VARCHAR(255),
    spotify_access_token TEXT,
    spotify_refresh_token TEXT,
    token_expires_at DATETIME,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_username (username),
    INDEX idx_spotify_user_id (spotify_user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Songs table
CREATE TABLE IF NOT EXISTS songs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    artist VARCHAR(255) NOT NULL,
    spotify_uri VARCHAR(255),
    spotify_id VARCHAR(255),
    album VARCHAR(255),
    duration_ms INT,
    added_by INT,
    added_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    is_active TINYINT(1) DEFAULT 1,
    vote_count INT DEFAULT 0,
    INDEX idx_spotify_id (spotify_id),
    INDEX idx_added_by (added_by),
    INDEX idx_is_active (is_active),
    FOREIGN KEY (added_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Votes table for song deletion
CREATE TABLE IF NOT EXISTS votes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    song_id INT NOT NULL,
    user_id INT NOT NULL,
    vote_type ENUM('keep', 'remove') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_vote (song_id, user_id),
    FOREIGN KEY (song_id) REFERENCES songs(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_song_id (song_id),
    INDEX idx_user_id (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Settings table for configuration
CREATE TABLE IF NOT EXISTS settings (
    setting_key VARCHAR(100) PRIMARY KEY,
    setting_value TEXT,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default settings
INSERT INTO settings (setting_key, setting_value) VALUES
('votes_required_to_remove', '3'),
('spotify_playlist_id', ''),
('allow_anonymous_add', '1')
ON DUPLICATE KEY UPDATE setting_key=setting_key;
