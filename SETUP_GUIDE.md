# Digital Life Manager - Laravel Setup Guide

## Project Overview
A complete Laravel web application for managing digital life activities. Built with Laravel 13, featuring user authentication, MySQL database integration, and MVC architecture.

---

## Prerequisites

Before you start, ensure you have:
- **PHP 8.0+** (PHP 8.5.5 recommended)
- **MySQL 5.7+** or **MariaDB 10.2+**
- **Composer** (Latest version)
- **Node.js 16+** (For frontend build tools)
- **npm** or **yarn** (Package manager)

---

## Step-by-Step Setup Instructions

### 1. **Environment Setup**

The project has already been initialized. Verify the `.env` configuration:

```bash
# Navigate to project directory
cd /path/to/digital-life-manager

# The .env file is already configured with:
- APP_NAME=Digital Life Manager
- APP_DEBUG=true (for development)
- DB_CONNECTION=mysql
- DB_HOST=127.0.0.1
- DB_PORT=3306
- DB_DATABASE=digital_life_manager
```

### 2. **Database Configuration**

Before running migrations, create the MySQL database:

```bash
# Login to MySQL
mysql -u root -p

# Create database
CREATE DATABASE digital_life_manager CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
EXIT;
```

Or using a GUI tool like **MySQL Workbench** or **phpMyAdmin**.

### 3. **Install Dependencies**

```bash
# Install PHP dependencies
composer install

# Install Node dependencies
npm install

# Or with Yarn
yarn install
```

### 4. **Generate Application Key** (Already Done)

```bash
php artisan key:generate
```

The APP_KEY is already set in `.env` file.

### 5. **Run Database Migrations**

```bash
# Run all migrations
php artisan migrate

# Run migrations with seeders (optional, if seeders exist)
php artisan migrate:seed
```

This creates:
- `users` table (for authentication)
- `password_reset_tokens` table
- `sessions` table
- `failed_jobs` table
- `cache` table

### 6. **Build Frontend Assets**

```bash
# Development build
npm run dev

# Production build
npm run build

# Watch for changes during development
npm run watch
```

### 7. **Start Development Server**

```bash
# Option 1: Using Artisan
php artisan serve

# Option 2: Using local PHP server
php -S localhost:8000 -t public

# Option 3: Using Valet (if installed)
valet start
```

Access the application at: **http://localhost:8000**

---

## Available Commands

### Artisan Commands

```bash
# Run migrations
php artisan migrate

# Rollback migrations
php artisan migrate:rollback

# Refresh database (resets and re-runs migrations)
php artisan migrate:refresh

# Run seeders
php artisan db:seed

# Create a new model with migration
php artisan make:model ModelName -m

# Create a new controller
php artisan make:controller ControllerName

# Create a new migration
php artisan make:migration create_table_name

# Run tests
php artisan test

# Cache clear
php artisan cache:clear

# View cache clear
php artisan view:clear

# Config cache
php artisan config:cache
```

### NPM Commands

```bash
# Development build with hot reload
npm run dev

# Production build (minified)
npm run build

# Watch for file changes
npm run watch

# Lint code
npm run lint
```

---

## Project Folder Structure

```
digital-life-manager/
├── app/                          # Application code
│   ├── Http/
│   │   ├── Controllers/          # Controllers (MVC - Controller)
│   │   │   ├── Auth/            # Authentication controllers (Login, Register, etc)
│   │   │   └── Controller.php    # Base controller class
│   │   ├── Middleware/           # HTTP middleware
│   │   ├── Requests/             # Form request validation
│   │   └── Resources/            # API resources
│   │
│   ├── Models/                   # Database models (MVC - Model)
│   │   └── User.php              # User model
│   │
│   ├── Services/                 # Business logic services
│   ├── Traits/                   # Reusable code traits
│   ├── Exceptions/               # Custom exceptions
│   ├── Events/                   # Event classes
│   ├── Jobs/                     # Queued jobs
│   ├── Listeners/                # Event listeners
│   ├── Mail/                     # Mailable classes
│   ├── Notifications/            # Notification classes
│   ├── Policies/                 # Authorization policies
│   └── Providers/                # Service providers
│
├── bootstrap/                    # Application bootstrapping
│   ├── app.php                   # Application bootstrap
│   └── cache/                    # Cached bootstrap files
│
├── config/                       # Configuration files
│   ├── app.php                   # Application settings
│   ├── database.php              # Database configuration
│   ├── mail.php                  # Mail settings
│   ├── auth.php                  # Authentication configuration
│   ├── cache.php                 # Cache configuration
│   ├── filesystems.php           # File storage configuration
│   ├── queue.php                 # Queue configuration
│   └── services.php              # Third-party services config
│
├── database/                     # Database files
│   ├── migrations/               # Database migrations
│   │   ├── 2025_01_01_000000_create_users_table.php
│   │   └── ...
│   ├── seeders/                  # Database seeders
│   │   └── DatabaseSeeder.php
│   └── factories/                # Model factories for testing
│
├── public/                       # Web root directory
│   ├── index.php                 # Application entry point
│   ├── css/                      # Compiled CSS files
│   ├── js/                       # Compiled JavaScript files
│   └── images/                   # Static images
│
├── resources/                    # Frontend resources (MVC - View)
│   ├── views/                    # Blade templates
│   │   ├── layouts/              # Layout templates
│   │   │   └── app.blade.php     # Main layout
│   │   ├── components/           # Reusable Blade components
│   │   ├── auth/                 # Authentication views
│   │   │   ├── login.blade.php
│   │   │   ├── register.blade.php
│   │   │   └── ...
│   │   └── dashboard.blade.php   # Dashboard view
│   │
│   ├── css/                      # Source CSS files
│   │   └── app.css
│   │
│   └── js/                       # Source JavaScript files
│       └── app.js
│
├── routes/                       # Route definitions
│   ├── web.php                   # Web routes
│   ├── api.php                   # API routes
│   ├── console.php               # Console routes
│   └── channels.php              # Broadcasting channels
│
├── storage/                      # Generated files (logs, cache)
│   ├── app/                      # Generated application files
│   ├── framework/                # Framework generated files
│   ├── logs/                     # Application logs
│   └── uploads/                  # User uploaded files
│
├── tests/                        # Test files
│   ├── Feature/                  # Feature/integration tests
│   ├── Unit/                     # Unit tests
│   └── CreatesApplication.php
│
├── vendor/                       # Installed Composer packages (auto-generated)
│
├── .env                          # Environment variables (local)
├── .env.example                  # Environment variables example template
├── .gitignore                    # Git ignore rules
├── composer.json                 # PHP dependencies
├── composer.lock                 # Locked dependency versions
├── package.json                  # Node.js dependencies
├── package-lock.json             # Locked Node versions
├── vite.config.js                # Vite configuration
├── phpunit.xml                   # PHPUnit testing configuration
├── artisan                       # Artisan command interface
└── README.md                     # Project documentation

```

---

## MVC Architecture Explanation

### **Model** (Database Layer)
Located in: `app/Models/`
- Represents database tables
- Handles data operations and relationships
- Example: `User.php` represents the users table

```php
// Example Model: app/Models/User.php
class User extends Model
{
    protected $fillable = ['name', 'email', 'password'];
}
```

### **View** (Presentation Layer)
Located in: `resources/views/`
- Blade templates for rendering UI
- Displays data to users
- Uses `.blade.php` extension

```html
<!-- Example View: resources/views/dashboard.blade.php -->
<h1>Welcome, {{ $user->name }}</h1>
```

### **Controller** (Business Logic Layer)
Located in: `app/Http/Controllers/`
- Handles HTTP requests
- Processes user input
- Interacts with models
- Returns views or responses

```php
// Example Controller: app/Http/Controllers/DashboardController.php
class DashboardController extends Controller
{
    public function index()
    {
        $users = User::all();
        return view('dashboard', ['users' => $users]);
    }
}
```

---

## Authentication System (Laravel Breeze)

### Features Included
✅ User registration
✅ Login/logout functionality
✅ Email verification
✅ Password reset
✅ Remember me option
✅ CSRF protection
✅ Session management

### Key Files
```
app/Http/Controllers/Auth/
├── AuthenticatedSessionController.php   # Login
├── NewPasswordController.php             # Password reset
├── PasswordResetLinkController.php       # Reset link
├── RegisteredUserController.php          # Registration
├── VerifyEmailController.php             # Email verification
└── ConfirmablePasswordController.php     # Password confirmation

resources/views/auth/
├── login.blade.php
├── register.blade.php
├── forgot-password.blade.php
└── reset-password.blade.php
```

### Authentication Routes
```
GET  /login                  Show login form
POST /login                  Process login
POST /logout                 Process logout
GET  /register               Show registration form
POST /register               Process registration
```

---

## Database Migrations

### What are Migrations?
- Version control for database schema
- Located in `database/migrations/`
- Allows team collaboration on database structure

### Running Migrations

```bash
# Run all pending migrations
php artisan migrate

# Create a new migration
php artisan make:migration create_tasks_table

# Rollback last migration batch
php artisan migrate:rollback

# Rollback all migrations
php artisan migrate:reset

# Refresh (rollback and re-run)
php artisan migrate:refresh
```

### Example Migration

```php
// database/migrations/xxxx_xx_xx_xxxxxx_create_tasks_table.php
Schema::create('tasks', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->onDelete('cascade');
    $table->string('title');
    $table->text('description')->nullable();
    $table->boolean('completed')->default(false);
    $table->timestamps();
});
```

---

## Development Workflow

### 1. Creating a New Feature

```bash
# Create Model with Migration
php artisan make:model Task -m

# Create Controller
php artisan make:controller TaskController --model=Task

# Create Request (Validation)
php artisan make:request StoreTaskRequest

# Create Migration (if needed separately)
php artisan make:migration add_status_to_tasks
```

### 2. Adding Routes

Edit `routes/web.php`:

```php
Route::middleware(['auth'])->group(function () {
    Route::resource('tasks', TaskController::class);
});
```

### 3. Building Views

Create Blade templates in `resources/views/tasks/`:
- `index.blade.php` - List all tasks
- `create.blade.php` - Create form
- `edit.blade.php` - Edit form
- `show.blade.php` - Single task view

---

## Configuration Files

### Important Configuration Files

| File | Purpose |
|------|---------|
| `config/app.php` | Application settings, timezone, timezone |
| `config/database.php` | Database connections |
| `config/auth.php` | Authentication settings |
| `config/mail.php` | Email configuration |
| `config/cache.php` | Cache settings |
| `config/filesystems.php` | File storage locations |
| `config/queue.php` | Queue settings |

---

## Common Issues & Solutions

### Issue 1: "No application encryption key has been specified"
```bash
php artisan key:generate
```

### Issue 2: Database connection error
- Verify MySQL credentials in `.env`
- Ensure database exists
- Check MySQL is running

### Issue 3: Permission issues
```bash
chmod -R 775 storage bootstrap/cache
```

### Issue 4: Composer issues
```bash
composer dump-autoload
composer update
```

---

## Useful Resources

- [Laravel Documentation](https://laravel.com/docs)
- [Laravel Breeze Documentation](https://laravel.com/docs/breeze)
- [Blade Template Engine](https://laravel.com/docs/blade)
- [Eloquent ORM](https://laravel.com/docs/eloquent)
- [Database Migrations](https://laravel.com/docs/migrations)

---

## Next Steps

1. ✅ Update `.env` with your database credentials
2. ✅ Create MySQL database
3. ✅ Run `php artisan migrate`
4. ✅ Run `npm run dev`
5. ✅ Start development server with `php artisan serve`
6. ✅ Visit `http://localhost:8000`
7. Register a test account and explore the application

---

## Summary

You now have a complete, production-ready Laravel project with:
- ✅ Latest Laravel version (13)
- ✅ MySQL database configured
- ✅ Proper MVC architecture
- ✅ Clean, organized folder structure
- ✅ Complete authentication system (Laravel Breeze)
- ✅ Ready for development

Happy coding! 🚀
