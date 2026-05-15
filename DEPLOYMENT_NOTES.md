# Deployment Notes - Database Configuration

## Database Credentials (Updated)

The application has been configured with the following production database credentials:

- **Host:** localhost
- **Database Name:** voxelnodes_music_2004
- **Username:** voxelnodes_music_2004
- **Password:** o9pqI9Mz,0Hd

## Files Updated

1. **config/config.php** - Updated default database credentials
2. **.env.example** - Updated example configuration
3. **database.sql** - Updated database name in schema

## Database Setup

The database has been set up with the following structure:

### Tables Created

1. **users** - User accounts and Spotify OAuth tokens
2. **songs** - Playlist tracks with metadata
3. **votes** - Democratic voting records (keep/remove)
4. **settings** - System configuration (3 default settings)

### Database Schema

```sql
CREATE DATABASE voxelnodes_music_2004 CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

All tables use utf8mb4 character set for full Unicode support.

## Verification

✅ Database connection tested and working
✅ All 4 tables created successfully
✅ Default settings populated
✅ Web application loads correctly
✅ Ready for production use

## Environment Variables

The application still supports environment variables for configuration flexibility:

- `DB_HOST` - Database host (default: localhost)
- `DB_USER` - Database username (default: voxelnodes_music_2004)
- `DB_PASS` - Database password (default: o9pqI9Mz,0Hd)
- `DB_NAME` - Database name (default: voxelnodes_music_2004)

To override defaults, set these environment variables in your hosting environment.

## Security Notes

⚠️ The database password is stored in plaintext in `config/config.php` as a default fallback. For production deployments, consider:

1. Setting `DB_PASS` as an environment variable instead
2. Using restricted file permissions on config.php (chmod 600)
3. Ensuring the database user has only necessary privileges
4. Regular password rotation

## Next Steps

The application is ready to use:

1. Users can login with any username
2. Songs can be added to the playlist
3. Democratic voting system is active
4. Optional Spotify integration available

Date: February 3, 2026
Status: Production Ready ✅
