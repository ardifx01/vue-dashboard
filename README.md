# Vue Dashboard - Laravel Starter Kit

A modern, secure, and feature-rich dashboard starter kit built with Vue 3, Vuetify 3, and Laravel 12.

## 🚀 Features

### 🎯 Core Features
- **Vue 3** + **Vuetify 3** + **Laravel 12** 
- **Role-based Access Control** with Spatie Permission package
- **Social Authentication** (Google & GitHub OAuth)
- **Responsive Dashboard** with Material Design components
- **User Management** with DataTable interface
- **Role & Permission Management**
- **Modern Build System** with Vite

### 🔒 Security Features
- Laravel Sanctum authentication
- CSRF protection
- Password hashing
- Social login validation
- Role-based authorization
- Input validation and sanitization

### 🎨 UI/UX Features
- Material Design 3 components
- Dark/Light theme support
- Responsive layout
- Clean, modern interface
- DataTable with search and pagination
- Toast notifications

## 📋 Requirements

- PHP 8.2+
- Node.js 18+
- MySQL 8.0+
- Composer
- NPM/PNPM

## 🛠️ Installation

1. **Clone the repository**
```bash
git clone https://github.com/ardifx01/vue-dashboard.git
cd vue-dashboard
```

2. **Install PHP dependencies**
```bash
composer install
```

3. **Install Node.js dependencies**
```bash
npm install
# or
pnpm install
```

4. **Environment setup**
```bash
cp .env.example .env
php artisan key:generate
```

5. **Configure your database**
Edit `.env` file with your database credentials:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=vue_dashboard
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

6. **Configure social authentication (optional)**
Add your OAuth credentials to `.env`:
```env
GOOGLE_CLIENT_ID=your_google_client_id
GOOGLE_CLIENT_SECRET=your_google_client_secret
GOOGLE_REDIRECT_URL=http://localhost:8000/auth/google/callback

GITHUB_CLIENT_ID=your_github_client_id
GITHUB_CLIENT_SECRET=your_github_client_secret
GITHUB_REDIRECT_URL=http://localhost:8000/auth/github/callback
```

7. **Run migrations and seeders**
```bash
php artisan migrate:fresh --seed
```

8. **Build assets**
```bash
npm run build
```

9. **Start the development server**
```bash
php artisan serve
```

The application will be available at `http://localhost:8000`.

## 👤 Default Users

After seeding, you can login with:

- **Admin User**
  - Email: `admin@demo.com`
  - Password: `admin`
  - Role: Super Admin

- **Regular User**
  - Email: `user@demo.com`
  - Password: `password`
  - Role: User

## 🎛️ Available Scripts

### PHP/Laravel
```bash
php artisan serve          # Start development server
php artisan migrate:fresh --seed  # Reset database with fresh data
php artisan route:list     # List all routes
php artisan tinker         # Interactive shell
```

### Node.js/Vue
```bash
npm run dev               # Start Vite dev server
npm run build             # Build for production
npm run preview           # Preview production build
```

## 📁 Project Structure

```
├── app/
│   ├── Http/Controllers/     # Laravel controllers
│   └── Models/              # Eloquent models
├── config/                  # Laravel configuration
├── database/
│   ├── migrations/          # Database migrations
│   └── seeders/            # Database seeders
├── resources/
│   ├── js/                 # Vue.js application
│   │   ├── components/     # Vue components
│   │   ├── pages/          # Page components
│   │   ├── stores/         # Pinia stores
│   │   └── main.js         # Vue app entry point
│   └── styles/             # SCSS stylesheets
├── routes/
│   ├── web.php             # Web routes
│   └── api.php             # API routes
└── example/                # Reference examples (excluded from production)
```

## 🔧 Configuration

### Roles & Permissions

The application comes with a predefined role system:

- **Super Admin**: Full access to all features
- **Admin**: Administrative access (user management, roles)
- **User**: Basic access (dashboard view only)

You can modify roles and permissions in `database/seeders/RolePermissionSeeder.php`.

### Adding New Pages

1. Create a new Vue component in `resources/js/pages/`
2. The router will automatically generate routes based on file structure
3. Add navigation items in `resources/js/navigation/vertical/index.js`

### Customizing Theme

Edit `themeConfig.js` to customize:
- App name and logo
- Color scheme
- Layout settings
- Navigation structure

## 🚀 Deployment

### Production Build

1. **Build assets**
```bash
npm run build
```

2. **Configure production environment**
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

3. **Set proper permissions**
```bash
chmod -R 755 storage bootstrap/cache
```

### Environment Variables

Ensure these are set in production:
```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com
```

## 🤝 Contributing

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

## 📝 License

This project is open-sourced software licensed under the [MIT license](LICENSE).

## 🙏 Credits

- [Laravel](https://laravel.com/) - The PHP framework
- [Vue.js](https://vuejs.org/) - The progressive JavaScript framework
- [Vuetify](https://vuetifyjs.com/) - Vue component framework
- [Spatie Laravel Permission](https://spatie.be/docs/laravel-permission) - Role and permission management
- [Laravel Socialite](https://laravel.com/docs/socialite) - Social authentication

## 💬 Support

If you encounter any issues or have questions, please [open an issue](https://github.com/ardifx01/vue-dashboard/issues) on GitHub.

---

Made with ❤️ by [IlhamriSKY](https://github.com/IlhamriSKY)
