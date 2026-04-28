# Quick Start Guide - Digital Life Manager

## 🚀 Getting Started in 5 Minutes

### Initial Setup (One-time)

```bash
# 1. Navigate to project
cd digital-life-manager

# 2. Create MySQL database
mysql -u root -p
> CREATE DATABASE digital_life_manager CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
> EXIT;

# 3. Install PHP dependencies
composer install

# 4. Install NPM dependencies
npm install

# 5. Run database migrations
php artisan migrate

# 6. Build frontend assets
npm run dev
```

### Development Server

```bash
# In one terminal
php artisan serve

# In another terminal
npm run watch

# Visit http://localhost:8000
```

---

## 📁 Key Directories for Development

| Directory | Purpose | What to do here |
|-----------|---------|-----------------|
| `app/Http/Controllers/` | Business logic | Create controllers for features |
| `app/Models/` | Database models | Define data models & relationships |
| `resources/views/` | HTML templates | Create Blade views |
| `routes/web.php` | URL routes | Define application routes |
| `database/migrations/` | Schema changes | Create/modify database tables |
| `database/seeders/` | Dummy data | Seed database with test data |
| `tests/` | Unit/feature tests | Write automated tests |

---

## 🔧 Common Development Commands

```bash
# Create new Model with Migration
php artisan make:model Post -m

# Create Controller
php artisan make:controller PostController --model=Post

# Create Migration
php artisan make:migration create_posts_table

# Create Request (for validation)
php artisan make:request StorePostRequest

# Run Migrations
php artisan migrate

# Rollback & Re-run Migrations
php artisan migrate:refresh

# Seed Database
php artisan db:seed

# Run Tests
php artisan test

# Clear Caches
php artisan cache:clear
php artisan view:clear
```

---

## 🔐 Authentication Routes

Available out of the box with Breeze:

```
/login           - User login
/register        - User registration
/forgot-password - Password reset
/verify-email    - Email verification
/dashboard       - Protected dashboard
```

---

## 📝 Useful Blade Syntax

```blade
<!-- Display variable -->
{{ $variable }}

<!-- If statement -->
@if ($user->isAdmin())
    <p>Welcome Admin</p>
@endif

<!-- Loop -->
@foreach ($users as $user)
    <p>{{ $user->name }}</p>
@endforeach

<!-- Include component -->
@component('components.alert', ['type' => 'success'])
    Operation successful!
@endcomponent

<!-- Protected routes -->
@auth
    <p>You are logged in</p>
@endauth

@guest
    <p>Please log in</p>
@endguest
```

---

## 🗂️ Project Structure at a Glance

```
digital-life-manager/
├── app/                    ← Your application code
│   ├── Http/Controllers/   ← Request handlers
│   └── Models/             ← Database models
├── resources/views/        ← HTML templates
├── routes/web.php          ← URL routes
├── database/
│   ├── migrations/         ← Database schemas
│   └── seeders/            ← Seed data
├── storage/logs/           ← Application logs
├── public/                 ← Publicly accessible files
└── .env                    ← Configuration
```

---

## 🚦 Database Workflow

### 1. Create Model & Migration
```bash
php artisan make:model Post -m
```

### 2. Edit Migration File
```php
// database/migrations/xxxx_xx_xx_create_posts_table.php
Schema::create('posts', function (Blueprint $table) {
    $table->id();
    $table->string('title');
    $table->text('content');
    $table->timestamps();
});
```

### 3. Run Migration
```bash
php artisan migrate
```

### 4. Use Model in Controller
```php
// app/Http/Controllers/PostController.php
$posts = Post::all();
$post = Post::find($id);
```

---

## 🔍 Debugging Tips

### Enable Debug Mode
Edit `.env`:
```
APP_DEBUG=true
```

### View Logs
```bash
# Watch logs in real-time
tail -f storage/logs/laravel.log
```

### Database Debugging
```php
// In controller
$posts = Post::query()->toSql();  // See SQL query
report(new Exception('Debug'));    // Throw error with stack trace
```

---

## 🌐 Environment Variables (.env)

Key variables:
```
APP_NAME=Digital Life Manager
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=digital_life_manager
DB_USERNAME=root
DB_PASSWORD=
```

---

## 📚 Learning Resources

- 🎥 [Laravel Documentation](https://laravel.com/docs)
- 🎥 [Blade Templates](https://laravel.com/docs/blade)
- 🎥 [Eloquent ORM](https://laravel.com/docs/eloquent)
- 🎥 [Routing](https://laravel.com/docs/routing)

---

## ⚡ Performance Tips

```bash
# Optimize autoloader
composer dump-autoload -o

# Cache configuration
php artisan config:cache

# Cache routes
php artisan route:cache

# Cache views
php artisan view:cache
```

---

Happy Coding! 🎉
