<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/user.php';
require_once __DIR__ . '/includes/song.php';
require_once __DIR__ . '/includes/spotify.php';

// Get all songs
$songs = Song::getAll();
$currentUser = User::getCurrentUser();
$isLoggedIn = User::isLoggedIn();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo SITE_NAME; ?></title>
    
    <!-- Tabler CSS -->
    <link href="https://cdn.jsdelivr.net/npm/@tabler/core@1.0.0-beta19/dist/css/tabler.min.css" rel="stylesheet"/>
    <link href="https://cdn.jsdelivr.net/npm/@tabler/icons@2.40.0/iconfont/tabler-icons.min.css" rel="stylesheet"/>
    
    <style>
        .song-card {
            transition: all 0.3s ease;
        }
        .song-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        .vote-btn {
            cursor: pointer;
        }
        .vote-btn.active {
            background-color: #206bc4;
            color: white;
        }
    </style>
</head>
<body>
    <div class="page">
        <!-- Header -->
        <header class="navbar navbar-expand-md navbar-light d-print-none">
            <div class="container-xl">
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbar-menu">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <h1 class="navbar-brand navbar-brand-autodark d-none-navbar-horizontal pe-0 pe-md-3">
                    <a href="index.php">
                        <i class="ti ti-music"></i>
                        <?php echo SITE_NAME; ?>
                    </a>
                </h1>
                <div class="navbar-nav flex-row order-md-last">
                    <?php if ($isLoggedIn): ?>
                        <div class="nav-item dropdown">
                            <a href="#" class="nav-link d-flex lh-1 text-reset p-0" data-bs-toggle="dropdown">
                                <span class="avatar avatar-sm"><?php echo substr($currentUser['username'], 0, 2); ?></span>
                                <div class="d-none d-xl-block ps-2">
                                    <div><?php echo htmlspecialchars($currentUser['username']); ?></div>
                                </div>
                            </a>
                            <div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                                <?php if (!$currentUser['spotify_user_id']): ?>
                                    <a href="<?php echo Spotify::getAuthUrl(); ?>" class="dropdown-item">
                                        <i class="ti ti-brand-spotify me-2"></i>Connect Spotify
                                    </a>
                                <?php else: ?>
                                    <span class="dropdown-item text-success">
                                        <i class="ti ti-check me-2"></i>Spotify Connected
                                    </span>
                                <?php endif; ?>
                                <div class="dropdown-divider"></div>
                                <a href="logout.php" class="dropdown-item">
                                    <i class="ti ti-logout me-2"></i>Logout
                                </a>
                            </div>
                        </div>
                    <?php else: ?>
                        <a href="#" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#loginModal">
                            <i class="ti ti-login me-2"></i>Login
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </header>

        <div class="page-wrapper">
            <!-- Page header -->
            <div class="page-header d-print-none">
                <div class="container-xl">
                    <div class="row g-2 align-items-center">
                        <div class="col">
                            <h2 class="page-title">
                                Studio Playlist
                            </h2>
                            <div class="text-muted mt-1">2004 Design Tech Freshman Studio</div>
                        </div>
                        <div class="col-auto ms-auto">
                            <?php if ($isLoggedIn): ?>
                                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addSongModal">
                                    <i class="ti ti-plus me-2"></i>Add Song
                                </button>
                            <?php else: ?>
                                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#loginModal">
                                    <i class="ti ti-login me-2"></i>Login to Add Songs
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Page body -->
            <div class="page-body">
                <div class="container-xl">
                    <div class="row row-cards">
                        <?php if (empty($songs)): ?>
                            <div class="col-12">
                                <div class="empty">
                                    <div class="empty-icon">
                                        <i class="ti ti-music-off"></i>
                                    </div>
                                    <p class="empty-title">No songs in the playlist yet</p>
                                    <p class="empty-subtitle text-muted">
                                        Be the first to add a song!
                                    </p>
                                </div>
                            </div>
                        <?php else: ?>
                            <?php foreach ($songs as $song): ?>
                                <div class="col-md-6 col-lg-4">
                                    <div class="card song-card">
                                        <div class="card-body">
                                            <h3 class="card-title"><?php echo htmlspecialchars($song['title']); ?></h3>
                                            <p class="text-muted mb-2">
                                                <i class="ti ti-user me-1"></i><?php echo htmlspecialchars($song['artist']); ?>
                                            </p>
                                            <?php if ($song['album']): ?>
                                                <p class="text-muted small mb-2">
                                                    <i class="ti ti-album me-1"></i><?php echo htmlspecialchars($song['album']); ?>
                                                </p>
                                            <?php endif; ?>
                                            <div class="mt-3">
                                                <small class="text-muted">
                                                    Added by <?php echo htmlspecialchars($song['added_by_username'] ?: 'Unknown'); ?>
                                                </small>
                                            </div>
                                            
                                            <?php if ($isLoggedIn): ?>
                                                <div class="mt-3 d-flex gap-2">
                                                    <button class="btn btn-sm btn-outline-success vote-btn <?php echo Vote::getUserVote($song['id'], $currentUser['id']) === 'keep' ? 'active' : ''; ?>" 
                                                            onclick="vote(<?php echo $song['id']; ?>, 'keep')">
                                                        <i class="ti ti-heart me-1"></i>Keep (<?php echo $song['keep_votes']; ?>)
                                                    </button>
                                                    <button class="btn btn-sm btn-outline-danger vote-btn <?php echo Vote::getUserVote($song['id'], $currentUser['id']) === 'remove' ? 'active' : ''; ?>" 
                                                            onclick="vote(<?php echo $song['id']; ?>, 'remove')">
                                                        <i class="ti ti-trash me-1"></i>Remove (<?php echo $song['remove_votes']; ?>)
                                                    </button>
                                                </div>
                                                <small class="text-muted mt-2 d-block">
                                                    <?php echo VOTES_TO_REMOVE; ?> removal votes needed
                                                </small>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <footer class="footer footer-transparent d-print-none">
                <div class="container-xl">
                    <div class="row text-center align-items-center">
                        <div class="col-12 col-lg-auto mt-3 mt-lg-0">
                            <ul class="list-inline list-inline-dots mb-0">
                                <li class="list-inline-item">
                                    2004 Design Tech Freshman Studio Music System
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    </div>

    <!-- Login Modal -->
    <div class="modal fade" id="loginModal" tabindex="-1">
        <div class="modal-dialog modal-sm modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Login</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="login.php" method="POST">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Username</label>
                            <input type="text" class="form-control" name="username" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Login</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Add Song Modal -->
    <div class="modal fade" id="addSongModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Song to Playlist</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <?php if ($currentUser && $currentUser['spotify_user_id']): ?>
                        <ul class="nav nav-tabs mb-3" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#spotify-search" type="button">
                                    <i class="ti ti-brand-spotify me-2"></i>Search Spotify
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#manual-add" type="button">
                                    <i class="ti ti-edit me-2"></i>Manual Entry
                                </button>
                            </li>
                        </ul>
                        <div class="tab-content">
                            <div class="tab-pane fade show active" id="spotify-search">
                                <div class="mb-3">
                                    <label class="form-label">Search for a song</label>
                                    <input type="text" class="form-control" id="spotifySearch" placeholder="Song name or artist...">
                                </div>
                                <div id="searchResults"></div>
                            </div>
                            <div class="tab-pane fade" id="manual-add">
                                <form action="api/add_song.php" method="POST">
                                    <div class="mb-3">
                                        <label class="form-label">Song Title</label>
                                        <input type="text" class="form-control" name="title" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Artist</label>
                                        <input type="text" class="form-control" name="artist" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Album (optional)</label>
                                        <input type="text" class="form-control" name="album">
                                    </div>
                                    <button type="submit" class="btn btn-primary">Add Song</button>
                                </form>
                            </div>
                        </div>
                    <?php else: ?>
                        <form action="api/add_song.php" method="POST">
                            <div class="mb-3">
                                <label class="form-label">Song Title</label>
                                <input type="text" class="form-control" name="title" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Artist</label>
                                <input type="text" class="form-control" name="artist" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Album (optional)</label>
                                <input type="text" class="form-control" name="album">
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-primary">Add Song</button>
                            </div>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabler JS -->
    <script src="https://cdn.jsdelivr.net/npm/@tabler/core@1.0.0-beta19/dist/js/tabler.min.js"></script>
    
    <script>
        // Voting function
        function vote(songId, voteType) {
            fetch('api/vote.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `song_id=${songId}&vote_type=${voteType}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert(data.error || 'Failed to cast vote');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Failed to cast vote');
            });
        }

        // Spotify search functionality
        <?php if ($currentUser && $currentUser['spotify_user_id']): ?>
        let searchTimeout;
        document.getElementById('spotifySearch')?.addEventListener('input', function(e) {
            clearTimeout(searchTimeout);
            const query = e.target.value;
            
            if (query.length < 2) {
                document.getElementById('searchResults').innerHTML = '';
                return;
            }
            
            searchTimeout = setTimeout(() => {
                fetch('api/spotify_search.php?q=' + encodeURIComponent(query))
                    .then(response => response.json())
                    .then(data => {
                        if (data.tracks && data.tracks.items) {
                            displaySearchResults(data.tracks.items);
                        }
                    })
                    .catch(error => console.error('Error:', error));
            }, 500);
        });

        function displaySearchResults(tracks) {
            const resultsDiv = document.getElementById('searchResults');
            if (tracks.length === 0) {
                resultsDiv.innerHTML = '<p class="text-muted">No results found</p>';
                return;
            }
            
            let html = '<div class="list-group">';
            tracks.forEach(track => {
                const artists = track.artists.map(a => a.name).join(', ');
                html += `
                    <a href="#" class="list-group-item list-group-item-action spotify-track-item" 
                       data-uri="${escapeHtml(track.uri)}" 
                       data-id="${escapeHtml(track.id)}" 
                       data-name="${escapeHtml(track.name)}" 
                       data-artists="${escapeHtml(artists)}" 
                       data-album="${escapeHtml(track.album.name)}" 
                       data-duration="${track.duration_ms}">
                        <div class="d-flex align-items-center">
                            ${track.album.images[2] ? `<img src="${track.album.images[2].url}" class="me-3" width="40" height="40">` : ''}
                            <div>
                                <strong>${escapeHtml(track.name)}</strong><br>
                                <small class="text-muted">${escapeHtml(artists)} • ${escapeHtml(track.album.name)}</small>
                            </div>
                        </div>
                    </a>
                `;
            });
            html += '</div>';
            resultsDiv.innerHTML = html;
            
            // Add event listeners to track items
            document.querySelectorAll('.spotify-track-item').forEach(item => {
                item.addEventListener('click', function(e) {
                    e.preventDefault();
                    addSpotifyTrack(
                        this.dataset.uri,
                        this.dataset.id,
                        this.dataset.name,
                        this.dataset.artists,
                        this.dataset.album,
                        this.dataset.duration
                    );
                });
            });
        }

        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        function addSpotifyTrack(uri, id, title, artist, album, duration) {
            
            fetch('api/add_song.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `spotify_uri=${encodeURIComponent(uri)}&spotify_id=${encodeURIComponent(id)}&title=${encodeURIComponent(title)}&artist=${encodeURIComponent(artist)}&album=${encodeURIComponent(album)}&duration_ms=${duration}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert(data.error || 'Failed to add song');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Failed to add song');
            });
        }
        <?php endif; ?>
    </script>
</body>
</html>
