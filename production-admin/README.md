# Admin Panel - Production Deployment Guide

## 📦 Package Contents
This folder contains the production-ready Laravel admin panel for GariKinun marketplace.

**Included:**
- All application code (app/, routes/, resources/, config/)
- Database migrations
- Public assets
- Bootstrap files
- Composer dependency files

**Excluded:**
- vendor/ folder (install on server)
- .env file (create from .env.example)
- Test files and documentation
- Development scripts

## 🚀 Deployment Steps

### 1. Upload to Server
```bash
# Upload all files to server path
scp -P 65002 -r * u960929282@195.35.62.48:domains/garikinun.com/public_html/admin/
```

### 2. Server Setup (SSH into server)
```bash
# Navigate to admin directory
cd domains/garikinun.com/public_html/admin/

# Install Composer dependencies
composer install --optimize-autoloader --no-dev

# Set up environment file
cp .env.example .env
nano .env  # Configure database and app settings

# Generate application key
php artisan key:generate

# Set permissions
chmod -R 755 storage bootstrap/cache
chmod -R 775 storage

# Run migrations
php artisan migrate --force

# Link storage
php artisan storage:link

# Clear and cache  config
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 3. Environment Configuration
Edit `.env` file with these production values:
```env
APP_NAME="GariKinun Admin"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://admin.garikinun.com

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=u960929282_GK
DB_USERNAME=u960929282
DB_PASSWORD=your_database_password

SESSION_DRIVER=file
QUEUE_CONNECTION=sync
```

### 4. Apache Configuration
Ensure `.htaccess` is present in `public/` folder with:
```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteRule ^(.*)$ public/$1 [L]
</IfModule>
```

### 5. Verify Deployment
- Visit: https://admin.garikinun.com/admin/login
- Login with admin credentials
- Test CMS features (Hero Section, Media Library, etc.)
- Check API endpoints: https://admin.garikinun.com/api/cms/hero-section

## 📝 Post-Deployment Checklist
- [ ] All files uploaded successfully
- [ ] Composer dependencies installed
- [ ] .env file configured correctly
- [ ] Database migrations run
- [ ] Storage linked and writable
- [ ] Admin panel accessible
- [ ] Login working
- [ ] APIs responding correctly

## 🔧 Troubleshooting

**500 Internal Server Error:**
- Check `.env` database credentials
- Verify storage/ permissions (755)
- Clear cache: `php artisan cache:clear`

**403 Forbidden on PUT/POST:**
- Already fixed with ModSecurity bypass in `.htaccess`

**Missing images/assets:**
- Run `php artisan storage:link`
- Check public/storage symlink exists

## 📞 Server Details
- **Host:** sg-nme-web545 (195.35.62.48:65002)
- **User:** u960929282
- **Path:** domains/garikinun.com/public_html/admin/
- **Database:** u960929282_GK
- **URL:** https://admin.garikinun.com

## 🔄 Update Workflow
1. Make changes locally
2. Test thoroughly
3. Upload modified files via SCP
4. Run `php artisan config:cache` on server
5. Clear browser cache
6. Verify changes on production
