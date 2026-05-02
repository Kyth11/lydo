# Hostinger Deployment Guide

This guide will help you deploy your Laravel application to Hostinger shared hosting.

## Prerequisites

- Hostinger hosting account with SSH access
- PHP 8.2 or higher
- MySQL database
- Composer installed locally or on Hostinger
- Node.js and npm installed locally

## Step 1: Prepare Local Environment

### 1.1 Install Dependencies

```bash
composer install --no-dev --optimize-autoloader
npm install
npm run build
```

### 1.2 Set Production Environment

Copy the production environment template:

```bash
cp .env.production.example .env
```

Update the following values in `.env`:

- `APP_URL`: Your domain name (e.g., `https://lydo.mswdopol.site`)
- `APP_KEY`: Generate with `php artisan key:generate`
- `APP_DEBUG`: Set to `false`
- Database credentials (get from Hostinger panel)
- Mail configuration (if needed)

### 1.3 Clear and Cache

```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 1.4 Optimize Application

```bash
php artisan optimize
```

## Step 2: Upload Files to Hostinger

### Option A: Using File Manager (Hostinger Panel)

1. Log in to Hostinger hPanel
2. Go to **Files** > **File Manager**
3. Navigate to `public_html`
4. Upload all files EXCEPT:
   - `node_modules/`
   - `vendor/`
   - `.git/`
   - `.env` (use the production one)
   - `storage/` (keep structure, but upload contents)
   - `tests/`
   - `.phpunit.result.cache`

### Option B: Using SFTP/SSH

```bash
# Upload files using SFTP
sftp user@your-hostinger-server
cd public_html
put -r .
```

Or use rsync:

```bash
rsync -avz --exclude 'node_modules' --exclude 'vendor' --exclude '.git' \
  --exclude 'tests' --exclude '.phpunit.result.cache' \
  ./ user@your-hostinger-server:public_html/
```

## Step 3: Install Dependencies on Hostinger

### 3.1 SSH into Hostinger

```bash
ssh user@your-hostinger-server
cd public_html
```

### 3.2 Install Composer Dependencies

```bash
composer install --no-dev --optimize-autoloader
```

### 3.3 Generate Application Key

```bash
php artisan key:generate
```

## Step 4: Set File Permissions

Run these commands on Hostinger:

```bash
# Set storage permissions
chmod -R 775 storage
chmod -R 775 bootstrap/cache

# Set ownership (if needed)
chown -R user:group storage
chown -R user:group bootstrap/cache
```

## Step 5: Database Setup

### 5.1 Create Database in Hostinger

1. Go to **Databases** > **MySQL Databases**
2. Create a new database
3. Create a database user
4. Link user to database with all privileges

### 5.2 Update .env with Database Credentials

**IMPORTANT:** Do NOT use the default 'root' credentials. You must use your actual Hostinger database credentials.

1. In Hostinger hPanel, go to **Databases** > **MySQL Databases**
2. Copy the actual database name, username, and password
3. Update `.env` with these credentials:

```env
DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=u123456789_lydodb
DB_USERNAME=u123456789_user
DB_PASSWORD=your_actual_password
```

**Note:** Hostinger database names and usernames typically start with your hosting account ID (e.g., `u123456789_`).

### 5.3 Run Migrations

```bash
php artisan migrate --force
```

### 5.4 Seed Database (Optional)

```bash
php artisan db:seed --force
```

### 5.5 Create Storage Link

The storage link allows public access to uploaded files (images, attachments, etc.). Run this command on Hostinger:

```bash
php artisan storage:link
```

**Note:** If the link already exists, you'll see a message saying it already exists - this is normal.

**Verify the link exists:**
```bash
ls -la public/storage
```

You should see it pointing to `../storage/app/public`.

## Step 6: Configure Document Root

### 6.1 Point Domain to public Directory

In Hostinger hPanel:

1. Go to **Domains** > **Manage**
2. Set **Document Root** to `public_html/public`
3. Or create a `.htaccess` file in `public_html` to redirect to `public`

### Alternative: Using .htaccess in public_html

Create `.htaccess` in `public_html`:

```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteCond %{REQUEST_URI} !^public/
    RewriteRule ^(.*)$ public/$1 [L]
</IfModule>
```

## Step 7: Final Checks

### 7.1 Verify Configuration

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 7.2 Test Application

- Visit your domain in browser
- Check if all routes work
- Test file uploads (if applicable)
- Check email functionality (if configured)

## Step 8: Configure Email (Optional)

### 8.1 Get Email Credentials from Hostinger

1. Log in to Hostinger hPanel
2. Go to **Emails** > **Email Accounts**
3. Create a new email account or use existing one
4. Note down:
   - Email address (e.g., `noreply@lydo.mswdopol.site`)
   - Email password
   - SMTP server (usually `smtp.hostinger.com`)
   - SMTP port (usually 587 for TLS)

### 8.2 Update .env with Email Configuration

Edit `.env` on Hostinger:

```env
MAIL_MAILER=smtp
MAIL_SCHEME=tls
MAIL_HOST=smtp.hostinger.com
MAIL_PORT=587
MAIL_USERNAME=noreply@lydo.mswdopol.site
MAIL_PASSWORD=your_email_password
MAIL_ENCRYPTION=tls
MAIL_AUTH_MODE=null

MAIL_FROM_ADDRESS="noreply@lydo.mswdopol.site"
MAIL_FROM_NAME="${APP_NAME}"
```

### 8.3 Test Email Configuration

A test email route has been added to your application. To test:

1. Log in as admin on your Hostinger site
2. Use curl or a form to send a POST request:

```bash
curl -X POST https://lydo.mswdopol.site/mail/test \
  -d "email=your-personal-email@gmail.com" \
  -d "_token=your_csrf_token"
```

Or create a simple HTML form to test:

```html
<form method="POST" action="https://lydo.mswdopol.site/mail/test">
    @csrf
    <input type="email" name="email" placeholder="Enter your email" required>
    <button type="submit">Send Test Email</button>
</form>
```

3. Check your email inbox for the test message

### 8.4 Troubleshooting Email Issues

**Email not sending:**
- Verify SMTP credentials in `.env`
- Check if port 587 is not blocked by Hostinger firewall
- Try port 465 with SSL encryption instead
- Check Hostinger email logs in hPanel

**Authentication failed:**
- Ensure email password is correct
- Some hosts require "Use SSL/TLS" setting
- Try using full email address as username

**Connection timeout:**
- Check if SMTP host is correct
- Verify port is open (587 for TLS, 465 for SSL)
- Contact Hostinger support if ports are blocked

## Troubleshooting

### 403 Forbidden Error

If you see a 403 Forbidden error, it's usually because the document root is not pointing to the `public` directory.

**Solution 1: Update Document Root in Hostinger Panel**
1. Go to **Domains** > **Manage**
2. Set **Document Root** to `public_html/public`
3. Save changes

**Solution 2: Use .htaccess Redirect (if Solution 1 doesn't work)**
The root `.htaccess` file has been configured to redirect requests to the `public` folder. Ensure:
- The `.htaccess` file exists in the root directory (public_html)
- Mod_rewrite is enabled on Hostinger (usually enabled by default)

**Solution 3: Check File Permissions**
```bash
# On Hostinger SSH
cd public_html
chmod -R 755 .
chmod -R 755 public
```

**Solution 4: Verify .htaccess is Uploaded**
Ensure the `.htaccess` file was uploaded (it's a hidden file, make sure your FTP client shows hidden files).

### 500 Internal Server Error

1. Check `storage/logs/laravel.log` for errors
2. Verify file permissions on `storage/` and `bootstrap/cache/`
3. Ensure `.env` file exists and has correct permissions
4. Check PHP version in Hostinger (requires 8.2+)

### Database Connection Failed

1. Verify database credentials in `.env`
2. Ensure database exists in Hostinger
3. Check database user has proper privileges
4. Verify database host is `localhost`

### Assets Not Loading

1. Run `php artisan storage:link`
2. Check `public/build` directory exists
3. Verify asset permissions

### Images/Files Not Loading (404)

If uploaded images or files are not showing:

1. **Create Storage Link:**
   ```bash
   php artisan storage:link
   ```

2. **Verify Link Exists:**
   ```bash
   ls -la public/storage
   ```
   Should show: `storage -> ../storage/app/public`

3. **Check File Permissions:**
   ```bash
   chmod -R 755 storage/app/public
   chmod -R 755 public/storage
   ```

4. **Clear Cache:**
   ```bash
   php artisan cache:clear
   php artisan view:clear
   ```

5. **Verify Files Exist:**
   Check that files are actually in `storage/app/public/` directory

### Permission Issues

If you encounter permission errors, try:

```bash
chmod -R 777 storage
chmod -R 777 bootstrap/cache
```

**Note:** 777 permissions are not recommended for production. Use 775 if possible.

## Post-Deployment Maintenance

### Regular Updates

```bash
# On Hostinger SSH
composer update
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Backup Strategy

1. Use Hostinger's automated backups
2. Export database regularly: `php artisan db:dump`
3. Backup `storage/app` directory

### Monitoring

- Check logs regularly: `tail -f storage/logs/laravel.log`
- Monitor disk space usage
- Keep PHP and dependencies updated

## Security Recommendations

1. **Never commit `.env` to version control**
2. Keep `APP_DEBUG=false` in production
3. Use strong database passwords
4. Enable HTTPS (SSL certificate)
5. Regularly update Laravel and dependencies
6. Implement rate limiting on API routes
7. Use CORS configuration if needed

## Additional Resources

- [Laravel Deployment Documentation](https://laravel.com/docs/deployment)
- [Hostinger PHP Configuration Guide](https://support.hostinger.com/en/articles/3028254-how-to-change-php-configuration-in-hostinger)
- [Hostinger SSH Access Guide](https://support.hostinger.com/en/articles/3027935-how-to-access-my-account-using-ssh)
