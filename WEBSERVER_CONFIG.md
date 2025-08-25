# Web Server Configuration Guide

## Issue: URLs showing `/build/` prefix

If you're seeing URLs like `https://yourdomain.com/build/login` instead of `https://yourdomain.com/login`, this means your web server is not properly configured.

## Solution: Proper Web Server Configuration

### For Apache (Recommended)
Ensure your virtual host points to the `public` folder, not the root project folder:

```apache
<VirtualHost *:80>
    ServerName vue-dashboard.dev
    DocumentRoot "/path/to/vue-dashboard/public"
    
    <Directory "/path/to/vue-dashboard/public">
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

### For Nginx
```nginx
server {
    listen 80;
    server_name vue-dashboard.dev;
    root /path/to/vue-dashboard/public;
    
    index index.php index.html;
    
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }
    
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
```

### For Local Development
If using Laravel's built-in server:
```bash
php artisan serve
```
This automatically serves from the correct directory.

## What We Fixed

1. **Added `.htaccess` redirect rules** to prevent direct `/build/` access
2. **Updated Laravel routes** for proper SPA handling
3. **Enhanced Vite configuration** with explicit base URL
4. **Added build directory protection**

## Result
- ✅ Clean URLs: `/login`, `/dashboard`, `/users`
- ✅ Proper Laravel routing through `index.php`
- ✅ SPA navigation working correctly
- ✅ Assets still served efficiently

## Note for Hosting Providers
If using shared hosting, ensure your domain points to the `public` folder as the document root, not the project root folder.
