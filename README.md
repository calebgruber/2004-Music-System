# 2004 Design Tech Music System

A collaborative music playlist system for the 2004 Design Tech Freshman Studio, built with PHP, MySQL, Bootstrap, and Tabler UI. Features Spotify integration and democratic song removal through voting.

## Features

- 🎵 **Add Songs**: Users can add songs manually or search via Spotify API
- 🔐 **User Authentication**: Simple username-based login system
- 🎤 **Spotify Integration**: Optional Spotify account connection for enhanced features
- 🗳️ **Democratic Voting**: Multiple users must vote to remove a song (3 votes by default)
- 🎨 **Modern UI**: Clean interface using Tabler UI and Bootstrap
- 📱 **Responsive Design**: Works on desktop and mobile devices

## Requirements

- PHP 7.4 or higher
- MySQL 5.7 or higher
- Apache/Nginx web server
- Spotify Developer Account (optional, for Spotify integration)

## Installation

### 1. Clone the Repository

```bash
git clone https://github.com/calebgruber/2004-Music-System.git
cd 2004-Music-System
```

### 2. Set Up the Database

Create a MySQL database and import the schema:

```bash
mysql -u root -p < database.sql
```

Or manually create the database:

```sql
CREATE DATABASE music_system;
```

Then import the `database.sql` file through your MySQL client.

### 3. Configure Environment

Copy the example environment file:

```bash
cp .env.example .env
```

Edit `.env` with your configuration:

```ini
DB_HOST=localhost
DB_USER=your_db_user
DB_PASS=your_db_password
DB_NAME=music_system

# Optional: For Spotify Integration
SPOTIFY_CLIENT_ID=your_client_id
SPOTIFY_CLIENT_SECRET=your_client_secret
SPOTIFY_REDIRECT_URI=http://yourdomain.com/callback.php
```

### 4. Set Up Spotify Integration (Optional)

1. Go to [Spotify Developer Dashboard](https://developer.spotify.com/dashboard)
2. Create a new app
3. Add your redirect URI (e.g., `http://localhost/callback.php`)
4. Copy your Client ID and Client Secret to `.env`

### 5. Configure Web Server

#### Apache

Make sure your `.htaccess` is configured properly, or add this to your Apache config:

```apache
<Directory /path/to/2004-Music-System>
    AllowOverride All
    Require all granted
</Directory>
```

#### Nginx

Add this to your nginx config:

```nginx
location / {
    try_files $uri $uri/ /index.php?$query_string;
}

location ~ \.php$ {
    fastcgi_pass unix:/var/run/php/php7.4-fpm.sock;
    fastcgi_index index.php;
    include fastcgi_params;
}
```

### 6. Set Permissions

```bash
chmod 755 /path/to/2004-Music-System
```

## Usage

### For Users

1. **Login**: Click "Login" and enter a username
2. **Add Songs**: 
   - Click "Add Song" button
   - Either search Spotify (if connected) or manually enter song details
3. **Vote on Songs**:
   - Click "Keep" to vote to keep a song
   - Click "Remove" to vote to remove a song
   - Songs need 3 removal votes (more than keep votes) to be removed
4. **Connect Spotify** (optional):
   - Click your profile icon
   - Select "Connect Spotify"
   - Authorize the application

### For Administrators

Database configuration can be modified in `config/config.php`:

- `VOTES_TO_REMOVE`: Number of votes needed to remove a song (default: 3)
- Other settings in the `settings` table

## Database Schema

### Tables

- **users**: User accounts and Spotify tokens
- **songs**: Song information and metadata
- **votes**: User votes for keeping or removing songs
- **settings**: System configuration

## Security Notes

- Never commit `.env` or `config/config.php` with sensitive data
- Use HTTPS in production
- Set secure session cookies in production
- Regularly update dependencies
- Consider implementing CSRF protection for production use

## Technology Stack

- **Backend**: PHP 7.4+
- **Database**: MySQL 5.7+
- **Frontend**: 
  - Tabler UI 1.0 (UI framework)
  - Bootstrap 5 (included with Tabler)
  - Tabler Icons
- **APIs**: Spotify Web API

## Troubleshooting

### Database Connection Issues

- Verify MySQL is running
- Check credentials in `.env`
- Ensure the database exists

### Spotify Integration Not Working

- Verify Client ID and Secret are correct
- Check redirect URI matches exactly in Spotify Dashboard
- Ensure your domain is added to allowed redirect URIs

### Songs Not Appearing

- Check database connection
- Verify songs are marked as `is_active = 1`
- Check browser console for JavaScript errors

## Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Submit a pull request

## License

This project is created for the 2004 Design Tech Freshman Studio.

## Support

For issues or questions, please open an issue on GitHub.