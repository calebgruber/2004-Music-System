# Quick Setup Guide

## 1. Database Setup

Run the following command to set up the database:

```bash
mysql -u root -p < database.sql
```

Or use phpMyAdmin or another MySQL client to import `database.sql`.

## 2. Configuration

The application uses environment variables for configuration. You can either:

### Option A: Using .env file (Development)

1. Copy `.env.example` to `.env`:
   ```bash
   cp .env.example .env
   ```

2. Edit `.env` with your settings:
   ```ini
   DB_HOST=localhost
   DB_USER=root
   DB_PASS=your_password
   DB_NAME=music_system
   ```

3. Load environment variables (Apache with mod_env or using PHP dotenv library)

### Option B: Direct environment variables (Production)

Set environment variables in your hosting control panel or server configuration:

```bash
export DB_HOST=localhost
export DB_USER=root
export DB_PASS=your_password
export DB_NAME=music_system
```

### Option C: Direct edit (Not recommended for production)

Edit `config/config.php` and replace `getenv()` calls with actual values.

## 3. Spotify Integration (Optional)

1. Create a Spotify App at https://developer.spotify.com/dashboard
2. Add your redirect URI (e.g., `http://localhost/callback.php`)
3. Set environment variables:
   ```bash
   export SPOTIFY_CLIENT_ID=your_client_id
   export SPOTIFY_CLIENT_SECRET=your_client_secret
   export SPOTIFY_REDIRECT_URI=http://yourdomain.com/callback.php
   ```

## 4. Web Server

### PHP Built-in Server (Development only)

```bash
php -S localhost:8000
```

Then visit: http://localhost:8000

### Apache

Ensure mod_rewrite is enabled and `.htaccess` is working.

### Nginx

Configure as per the README.md instructions.

## 5. First Use

1. Visit your site
2. Click "Login" and enter a username
3. Start adding songs!

## Troubleshooting

- **Can't connect to database**: Check credentials in config
- **Spotify not working**: Make sure CLIENT_ID and CLIENT_SECRET are set correctly
- **Blank page**: Check PHP error logs

## Default Settings

- **Votes required to remove a song**: 3
- **Anonymous song adding**: Allowed (users just need to login with a username)

These can be changed in `config/config.php` or the `settings` table in the database.
