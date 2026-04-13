# RE Nurture CRM — Setup Guide

Complete step-by-step setup instructions for local development and production deployment.

## Local Development Setup

### 1. Prerequisites
Ensure you have:
- PHP 8.2 or higher
- MySQL 5.7 or PostgreSQL 10+
- Composer (latest)
- Node.js 18+ and npm/yarn (for frontend assets)

### 2. Install Dependencies
```bash
cd RE_nurture_CRM
composer install
npm install  # For Tailwind CSS and Livewire assets
```

### 3. Environment Configuration
```bash
cp .env.example .env
php artisan key:generate
```

Edit `.env` with your settings:
```env
APP_NAME="RE Nurture CRM"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=re_nurture_crm
DB_USERNAME=root
DB_PASSWORD=your_password

MAIL_MAILER=sendgrid
SENDGRID_API_KEY=your_sendgrid_api_key

TWILIO_ACCOUNT_SID=your_account_sid
TWILIO_AUTH_TOKEN=your_auth_token
TWILIO_PHONE_NUMBER=+1234567890

GMAIL_SERVICE_ACCOUNT_JSON=/path/to/gmail-service-account.json
```

### 4. Database Setup
```bash
# Create database
mysql -u root -p -e "CREATE DATABASE re_nurture_crm;"

# Run migrations
php artisan migrate

# Seed sample data
php artisan db:seed
```

### 5. Build Frontend Assets
```bash
npm run dev    # Development
npm run build  # Production
```

### 6. Start Development Server
```bash
php artisan serve
```

Access at `http://localhost:8000`

### 7. Create Admin User (Future)
```bash
php artisan tinker
>>> User::create(['name' => 'Admin', 'email' => 'admin@example.com', 'password' => bcrypt('password')])
```

---

## Production Deployment on cPanel

### Prerequisites
- cPanel/WHM account with SSH access
- MySQL database provisioned
- SSL certificate (automatic with Cloudflare)

### Deployment Steps

#### 1. Connect via SSH
```bash
ssh root@167.172.229.119  # server1.brittonbox.com
cd /home/nurture/public_html/
```

#### 2. Clone Repository
```bash
# If first time
git clone https://github.com/HustleSmilez/re-nurture-crm.git .

# Or if already exists
git pull origin main
```

#### 3. Install PHP Dependencies
```bash
composer install --no-dev --optimize-autoloader
```

#### 4. Configure Environment
```bash
cp .env.example .env
```

Edit `.env` with production credentials:
```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://nurture.realtorcedric.com

DB_HOST=localhost
DB_DATABASE=nurture_crm
DB_USERNAME=nurture_user
DB_PASSWORD=secure_password_here

SENDGRID_API_KEY=...
TWILIO_ACCOUNT_SID=...
```

#### 5. Generate Application Key
```bash
php artisan key:generate
```

#### 6. Migrate Database
```bash
php artisan migrate --force
php artisan db:seed --force  # First time only
```

#### 7. Cache Configuration
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

#### 8. Set Permissions
```bash
chmod -R 775 storage/
chmod -R 775 bootstrap/cache/
chown -R nobody:nobody storage/ bootstrap/
```

#### 9. Configure Web Server

**Using Apache (cPanel default):**

Create/update `.htaccess` in `/home/nurture/public_html/`:

```apache
<IfModule mod_rewrite.c>
    <IfModule mod_negotiation.c>
        Options -MultiViews -Indexes
    </IfModule>

    RewriteEngine On
    
    # Handle Authorization Header
    RewriteCond %{HTTP:Authorization} .
    RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]

    # Redirect Trailing Slashes If Not A Folder...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_URI} (.+)/$
    RewriteRule ^ %1 [L,R=301]

    # Send Requests To Front Controller...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule>
```

**Public Directory:**

Ensure web root points to `/home/nurture/public_html/public/` (cPanel Document Root)

#### 10. Configure DNS & SSL

**In Cloudflare:**
- Type: CNAME
- Name: nurture
- Target: server1.brittonbox.com
- Proxy status: Proxied (orange cloud)
- SSL/TLS: Full (strict)

SSL is automatic via Cloudflare Universal SSL.

#### 11. Setup Webhooks

**SendGrid Webhook:**
1. Go to SendGrid dashboard → Settings → Mail Send
2. Event Webhook URL: `https://nurture.realtorcedric.com/api/webhooks/sendgrid`
3. Select: Delivered, Opened, Clicked, Bounced, Unsubscribed

**Twilio Webhook:**
1. Go to Twilio console → Phone Numbers → Manage Numbers
2. Messaging: Status Callback URL: `https://nurture.realtorcedric.com/api/webhooks/twilio`

#### 12. Setup Cron Jobs

In cPanel → Cron Jobs, add:

```bash
* * * * * /usr/bin/php /home/nurture/public_html/artisan schedule:run >> /dev/null 2>&1
```

This runs Laravel's task scheduler every minute.

#### 13. Monitor & Logs

**View error logs:**
```bash
tail -f storage/logs/laravel.log
```

**Access phpMyAdmin** (via cPanel)

**Monitor with tools:**
- Google Search Console for indexing
- Cloudflare Analytics for traffic
- Application Monitoring (optional)

---

## Database Backup & Restore

### Automatic Backups (via cPanel)
- Configure in cPanel → Backup
- Frequency: Daily
- Retain: 30 days

### Manual Backup
```bash
mysqldump -u nurture_user -p nurture_crm > backup-$(date +%Y%m%d).sql
```

### Restore
```bash
mysql -u nurture_user -p nurture_crm < backup-20240413.sql
```

---

## Troubleshooting

### 500 Error
1. Check error log: `tail storage/logs/laravel.log`
2. Clear cache: `php artisan cache:clear`
3. Check file permissions: `chmod -R 755 storage/`

### Database Connection Error
```bash
# Test connection
php artisan tinker
>>> DB::connection()->getPdo()
```

### SendGrid Not Sending
1. Verify API key in `.env`
2. Check email address is verified in SendGrid
3. Review SendGrid activity log for bounces

### Twilio SMS Issues
1. Verify Account SID and Auth Token
2. Check phone number format: +1 (country code + number)
3. Test via Twilio console

### Cloudflare SSL Issues
1. Set SSL/TLS to "Full" mode
2. Ensure server certificate is valid
3. Check origin certificate compatibility

---

## Monitoring & Maintenance

### Daily
- Check error logs
- Monitor payment/billing alerts
- Verify SendGrid quota

### Weekly
- Review contact/lead creation trends
- Check task completion rates
- Audit failed communications

### Monthly
- Database backup verification
- SSL certificate renewal check
- Security updates for PHP/Laravel

### Quarterly
- Performance optimization review
- Database maintenance (optimize tables)
- Security audit (OWASP top 10)

---

## Scaling Considerations

As the CRM grows:

1. **Database Optimization**
   - Add indexes on frequently queried fields
   - Archive old communications
   - Implement data partitioning

2. **Caching**
   - Implement Redis for session/cache
   - Cache frequent queries
   - Use queue for bulk operations

3. **Load Balancing**
   - Multiple app servers
   - Separate database server
   - CDN for static assets

4. **API Rate Limiting**
   - Throttle bulk imports
   - Implement request queuing
   - Set API usage quotas

---

## Support

For issues or questions:
1. Check error logs: `storage/logs/laravel.log`
2. Review API documentation in README.md
3. Inspect webhook payloads for integration issues
4. Contact Cedric Britton or Clarence (AI Assistant)

---

_Last updated: April 13, 2026_
_Deployed by: Clarence_
