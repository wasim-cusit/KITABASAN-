# PHP Upload Configuration Guide

This document explains how to configure PHP to allow large video file uploads (5GB+).

## Current Upload Limits

The application is configured to accept video files up to **5GB** (5,242,880 KB).

## Required PHP Configuration

To support large video uploads, you need to increase the following PHP settings:

### 1. For XAMPP (Windows)

Edit `php.ini` file (located at `C:\xampp\php\php.ini`):

```ini
upload_max_filesize = 5120M
post_max_size = 5120M
max_execution_time = 3600
max_input_time = 3600
memory_limit = 1024M
```

**Important:** After making changes, restart Apache from XAMPP Control Panel.

### 2. For Apache (.htaccess)

The `public/.htaccess` file has been updated with these settings:
- `upload_max_filesize = 5120M`
- `post_max_size = 5120M`
- `max_execution_time = 3600`
- `max_input_time = 3600`
- `memory_limit = 1024M`

**Note:** `.htaccess` PHP directives only work if `AllowOverride` is enabled in your Apache configuration.

### 3. For Nginx

Add to your Nginx configuration:

```nginx
client_max_body_size 5120M;
```

And ensure PHP-FPM has these settings in `php.ini` or `php-fpm.conf`:
```ini
upload_max_filesize = 5120M
post_max_size = 5120M
max_execution_time = 3600
memory_limit = 1024M
```

### 4. Verify Configuration

Run this command to check current PHP settings:
```bash
php -i | findstr /i "upload_max_filesize post_max_size max_execution_time memory_limit"
```

Expected output:
```
upload_max_filesize => 5120M => 5120M
post_max_size => 5120M => 5120M
max_execution_time => 3600 => 3600
memory_limit => 1024M => 1024M
```

## Laravel Application Limits

The following validation rules have been updated:
- **TopicController**: Max file size set to 5GB (5242880 KB)
- **VideoUploadController**: Already configured for 100GB

## Important Notes

1. **Server Resources**: Large uploads require:
   - Sufficient disk space
   - Adequate memory (at least 1GB PHP memory)
   - Extended execution time

2. **Network Timeouts**: For very large files (>1GB), consider:
   - Implementing chunked/resumable uploads
   - Using a dedicated upload service
   - Increasing nginx/Apache timeouts

3. **Storage**: Ensure your storage directory has enough space:
   ```bash
   # Check available space
   df -h storage/app/public/videos
   ```

4. **Database**: Large files may require adjusting:
   - `max_allowed_packet` in MySQL (if storing file paths)

## Troubleshooting

### Upload Still Fails After Configuration

1. **Check PHP settings are loaded:**
   ```php
   <?php phpinfo(); ?>
   ```
   Look for `upload_max_filesize` and `post_max_size` values.

2. **Check web server logs:**
   - Apache: `logs/error.log`
   - Nginx: `/var/log/nginx/error.log`

3. **Check Laravel logs:**
   ```bash
   tail -f storage/logs/laravel.log
   ```

4. **Verify file permissions:**
   ```bash
   chmod -R 775 storage/app/public/videos
   ```

### Alternative: Chunked Uploads

For very large files, consider implementing chunked uploads using a library like:
- [Resumable.js](https://github.com/23/resumable.js)
- [Uppy](https://uppy.io/)

This allows breaking large files into smaller chunks, avoiding PHP upload limits.

## File Size Limits by Format

- **MP4**: Up to 5GB
- **WebM**: Up to 5GB
- **MOV**: Up to 5GB
- **AVI**: Up to 5GB
- **FLV**: Up to 5GB
- **WMV**: Up to 5GB
- **MKV**: Up to 5GB

## Support

If you continue to experience issues, check:
1. Server error logs
2. Laravel application logs
3. PHP error logs
4. Web server configuration
