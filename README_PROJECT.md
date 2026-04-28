# Digital Life Manager - Laravel Project

A complete, production-ready Laravel web application with full authentication system, MVC architecture, and clean folder structure.

---

## 📋 Contents at a Glance

📁 **Project Location**: `/Code/MU/Vai_er_Vatar_WP/WP_Final_Project/digital-life-manager/`

**Documentation Files** (Read these to get started):
- **[SETUP_SUMMARY.md](SETUP_SUMMARY.md)** ⭐ **START HERE** - Complete overview and next steps
- **[SETUP_GUIDE.md](SETUP_GUIDE.md)** - Detailed step-by-step installation
- **[QUICK_START.md](QUICK_START.md)** - Quick reference for common tasks
- **[FEATURE_GUIDE.md](FEATURE_GUIDE.md)** - Tutorial: Creating your first feature
- **[COMMANDS_REFERENCE.md](COMMANDS_REFERENCE.md)** - All available commands

---

## 🚀 Quick Start (2 Minutes)

### Setup MySQL Database
```bash
mysql -u root -p
CREATE DATABASE digital_life_manager CHARACTER SET utf8mb4;
EXIT;
```

### Install & Run
```bash
cd digital-life-manager

# Install dependencies
composer install
npm install

# Run migrations
php artisan migrate

# Start servers (in separate terminals)
php artisan serve          # Terminal 1
npm run dev                # Terminal 2

# Visit: http://localhost:8000
```

---

## ✨ What's Included

### ✅ Core Framework
- Latest Laravel 13
- PHP 8.5.5
- Composer 2.9.7
- Vite (frontend build tool)

### ✅ Authentication
- User Registration
- Login/Logout
- Email Verification
- Password Reset
- Session Management
- CSRF Protection
- Laravel Breeze scaffolding

### ✅ Frontend
- Blade Templating Engine
- Tailwind CSS
- Responsive Components
- Pre-built Forms

### ✅ Database
- MySQL Ready (configured in `.env`)
- Eloquent ORM
- Migrations System
- Seeders Support

### ✅ Testing
- PHPUnit Setup
- Feature Tests for auth
- Test Database Support

---

## 📁 Complete Directory Structure

```
digital-life-manager/
│
├── 📄 Documentation Files (START HERE)
│   ├── SETUP_SUMMARY.md          ⭐ Quick overview
│   ├── SETUP_GUIDE.md            📖 Complete guide
│   ├── QUICK_START.md            ⚡ Quick reference
│   ├── FEATURE_GUIDE.md          🎯 Create features
│   └── COMMANDS_REFERENCE.md     🔧 All commands
│
├── app/                          Main Application Code (MVC)
│   ├── Http/
│   │   ├── Controllers/          Controllers (C in MVC)
│   │   │   ├── Auth/             Authentication controllers
│   │   │   ├── ProfileController.php
│   │   │   └── Controller.php    Base controller
│   │   ├── Requests/             Form validation
│   │   │   ├── Auth/LoginRequest.php
│   │   │   └── ProfileUpdateRequest.php
│   │   └── Middleware/
│   │
│   ├── Models/                   Database Models (M in MVC)
│   │   └── User.php              User model (with auth)
│   │
│   ├── Providers/                Service Providers
│   │   └── AppServiceProvider.php
│   │
│   └── View/
│       └── Components/           Reusable Blade components
│
├── bootstrap/                    Application Bootstrap
│   ├── app.php
│   ├── providers.php
│   └── cache/
│
├── config/                       Configuration Files
│   ├── app.php                   Application settings
│   ├── auth.php                  Authentication config
│   ├── database.php              Database connections
│   ├── cache.php
│   ├── filesystems.php
│   ├── mail.php
│   ├── queue.php
│   └── session.php
│
├── database/                     Database Management
│   ├── migrations/               Database schemas
│   │   ├── 0001_01_01_000000_create_users_table.php
│   │   ├── 0001_01_01_000001_create_cache_table.php
│   │   └── 0001_01_01_000002_create_jobs_table.php
│   ├── seeders/                  Database seeders
│   │   └── DatabaseSeeder.php
│   ├── factories/                Model factories
│   │   └── UserFactory.php
│   └── database.sqlite           SQLite database (if needed)
│
├── public/                       Web Root (Accessible by Browser)
│   ├── index.php                 Entry point
│   ├── .htaccess
│   ├── favicon.ico
│   ├── robots.txt
│   └── css/                      Compiled CSS (generated)
│   └── js/                       Compiled JS (generated)
│
├── resources/                    Frontend Resources (V in MVC)
│   ├── views/                    Blade Templates
│   │   ├── layouts/
│   │   │   ├── app.blade.php     Main layout
│   │   │   ├── guest.blade.php   Guest layout
│   │   │   └── navigation.blade.php
│   │   │
│   │   ├── auth/                 Authentication pages
│   │   │   ├── login.blade.php
│   │   │   ├── register.blade.php
│   │   │   ├── forgot-password.blade.php
│   │   │   ├── reset-password.blade.php
│   │   │   └── verify-email.blade.php
│   │   │
│   │   ├── components/           Reusable components
│   │   │   ├── application-logo.blade.php
│   │   │   ├── auth-session-status.blade.php
│   │   │   ├── nav-link.blade.php
│   │   │   ├── primary-button.blade.php
│   │   │   ├── text-input.blade.php
│   │   │   └── ... (more components)
│   │   │
│   │   ├── profile/              User profile pages
│   │   │   ├── edit.blade.php
│   │   │   └── partials/         Form partials
│   │   │
│   │   ├── dashboard.blade.php   Main dashboard
│   │   └── welcome.blade.php     Welcome page
│   │
│   ├── css/                      CSS Source Files
│   │   └── app.css
│   │
│   └── js/                       JavaScript Source
│       └── app.js
│
├── routes/                       Route Definitions
│   ├── web.php                   Web routes
│   ├── api.php                   API routes
│   ├── auth.php                  Auth routes
│   ├── console.php               Console commands
│   └── channels.php              Broadcasting channels
│
├── storage/                      Storage & Logs
│   ├── app/                      Generated files
│   ├── logs/                     Application logs
│   │   └── laravel.log
│   └── framework/                Cache & temp files
│
├── tests/                        Test Suite
│   ├── Feature/                  Feature tests
│   │   ├── Auth/
│   │   │   ├── AuthenticationTest.php
│   │   │   ├── EmailVerificationTest.php
│   │   │   ├── PasswordConfirmationTest.php
│   │   │   ├── PasswordResetTest.php
│   │   │   ├── PasswordUpdateTest.php
│   │   │   ├── RegistrationTest.php
│   │   │   └── ...
│   │   ├── ExampleTest.php
│   │   └── ProfileTest.php
│   │
│   ├── Unit/                     Unit tests
│   │   └── ExampleTest.php
│   │
│   └── TestCase.php              Base test class
│
├── vendor/                       Composer Packages (auto-generated)
├── node_modules/                 NPM Packages (auto-generated)
│
├── .env                          ✅ Environment Variables (Configured)
├── .env.example                  Environment Template
├── .gitignore                    Git ignore rules
├── .gitattributes
├── .editorconfig
├── .npmrc
│
├── composer.json                 PHP Dependencies
├── composer.lock                 Locked versions
├── package.json                  Node Dependencies
├── package-lock.json             Locked NPM versions
│
├── artisan                       Laravel CLI Tool
├── phpunit.xml                   Testing config
├── vite.config.js                Vite config
├── tailwind.config.js            Tailwind config
│
└── README.md                     This file

```

---

## 🔑 Key Files Explained

| File| Purpose |
|-----|---------|
| `.env` | Database & app configuration |
| `routes/web.php` | Define URL routes |
| `app/Http/Controllers/` | Request handlers |
| `app/Models/User.php` | User database model |
| `resources/views/` | HTML templates |
| `database/migrations/` | Database schemas |
| `composer.json` | PHP dependencies |
| `package.json` | Node.js dependencies |

---

## 🔐 Authentication Routes Ready

```
GET    /                  Welcome page
GET    /login             Show login form
POST   /login             Process login
POST   /logout            Process logout
GET    /register          Show registration form
POST   /register          Process registration
GET    /forgot-password   Password reset request
POST   /forgot-password   Send reset link
GET    /reset-password    Reset password form
POST   /reset-password    Process password reset
GET    /verify-email      Email verification

Protected Routes (Require Login):
GET    /dashboard         User dashboard
GET    /profile           Edit profile
PATCH  /profile           Update profile
DELETE /profile           Delete account
```

---

## 💻 Core Commands

```bash
# Serve & Build
php artisan serve              # Start Laravel server
npm run dev                    # Start Vite with hot reload
npm run build                  # Build for production

# Database
php artisan migrate            # Run migrations
php artisan db:seed            # Seed database
php artisan tinker             # Interactive shell

# Create Files
php artisan make:model Name -m # Model + migration
php artisan make:controller Name      # Controller
php artisan make:request Name         # Validation

# Testing
php artisan test               # Run tests

# Optimize
php artisan cache:clear
php artisan config:cache
composer dump-autoload -o
```

---

## 🎯 MVC Architecture

### **Model** (`app/Models/`)
Represents your database tables with relationships and business logic.

```php
// Example: User model (already set up)
class User extends Model {
    public function posts() { 
        return $this->hasMany(Post::class);
    }
}
```

### **View** (`resources/views/`)
Blade templates that display data to users.

```blade
<!-- Display username -->
<h1>{{ $user->name }}</h1>

<!-- Loop through users -->
@foreach ($users as $user)
    <p>{{ $user->email }}</p>
@endforeach
```

### **Controller** (`app/Http/Controllers/`)
Handles requests, processes logic, returns views.

```php
// Example: PostController
public function index() {
    $posts = Post::all();
    return view('posts.index', ['posts' => $posts]);
}
```

---

## 🗂️ Where to Add Code

### Adding New Features
```
1. Create Model:         app/Models/Post.php
2. Create Migration:     database/migrations/xxxx_create_posts_table.php
3. Create Controller:    app/Http/Controllers/PostController.php
4. Create Views:         resources/views/posts/
5. Define Routes:        routes/web.php
6. Run Migration:        php artisan migrate
```

### Database Relationships
```php
// One-to-Many (User has many Posts)
class User extends Model {
    public function posts() {
        return $this->hasMany(Post::class);
    }
}

// Belongs to (Post belongs to User)
class Post extends Model {
    public function user() {
        return $this->belongsTo(User::class);
    }
}
```

---

## ⚙️ Configuration

### Database (.env)
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=digital_life_manager
DB_USERNAME=root
DB_PASSWORD=
```

### Application (.env)
```
APP_NAME="Digital Life Manager"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost
```

---

## 🧪 Testing

```bash
# Run all tests
php artisan test

# Run specific test
php artisan test tests/Feature/Auth/RegistrationTest.php

# Run with coverage
php artisan test --coverage
```

Tests included:
- ✅ Authentication tests
- ✅ Email verification tests
- ✅ Password reset tests
- ✅ Profile update tests

---

## 📚 Full Documentation

See individual documentation files for detailed information:

1. **[SETUP_SUMMARY.md](SETUP_SUMMARY.md)** - Overview & next steps
2. **[SETUP_GUIDE.md](SETUP_GUIDE.md)** - Complete installation guide
3. **[QUICK_START.md](QUICK_START.md)** - Quick reference
4. **[FEATURE_GUIDE.md](FEATURE_GUIDE.md)** - Create your first feature
5. **[COMMANDS_REFERENCE.md](COMMANDS_REFERENCE.md)** - All commands

---

## 🐛 Common Issues

### Database connection error?
```bash
# Verify MySQL is running
# Check .env credentials
# Ensure database exists: CREATE DATABASE digital_life_manager;
```

### "No application key" error?
```bash
php artisan key:generate
```

### Permission errors?
```bash
chmod -R 775 storage bootstrap/cache
```

### Node modules issues?
```bash
rm -rf node_modules package-lock.json
npm install
```

---

## 📖 Learning Resources

- [Laravel 13 Documentation](https://laravel.com/docs/13)
- [Blade Template Engine](https://laravel.com/docs/13/blade)
- [Eloquent ORM](https://laravel.com/docs/13/eloquent)
- [Database Migrations](https://laravel.com/docs/13/migrations)
- [Authentication](https://laravel.com/docs/13/authentication)

---

## ✅ Setup Checklist

- [x] Laravel installed
- [x] Laravel Breeze authentication installed
- [x] MySQL configuration in .env
- [x] Migrations created
- [x] Application key generated
- [ ] Create MySQL database
- [ ] Run migrations (`php artisan migrate`)
- [ ] Start development servers
- [ ] Register test account
- [ ] Start building your features!

---

## 🚀 Next Steps

1. **Read** [SETUP_SUMMARY.md](SETUP_SUMMARY.md)
2. **Create** MySQL database
3. **Run** `php artisan migrate`
4. **Execute** `php artisan serve` and `npm run dev`
5. **Visit** http://localhost:8000
6. **Register** a test account
7. **Explore** the dashboard
8. **Create** your first feature using [FEATURE_GUIDE.md](FEATURE_GUIDE.md)

---

## 📞 Support Resources

For issues or questions:
- Check [SETUP_GUIDE.md](SETUP_GUIDE.md) troubleshooting section
- Review [COMMANDS_REFERENCE.md](COMMANDS_REFERENCE.md)
- Consult Laravel documentation links above
- Use `php artisan tinker` to debug

---

## 📝 License

This is a Laravel application built with Laravel Breeze. Follow Laravel's licensing terms.

---

**Happy Coding!** 🎉

Your Digital Life Manager application is ready for development. Start with the documentation files linked above and enjoy building amazing features!

---

*Project Setup: April 28, 2026*
*Laravel Version: 13*
*PHP Version: 8.5.5*
*MySQL Compatible: Yes*
