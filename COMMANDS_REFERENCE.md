# Available Scripts & Commands Reference

## 🚀 Quick Commands Summary

### Start Development
```bash
php artisan serve          # Start Laravel development server
npm run dev                # Start Vite development server with hot reload
npm run watch              # Watch files and rebuild on changes
```

### Build for Production
```bash
npm run build              # Build assets for production
```

### Database
```bash
php artisan migrate        # Run pending migrations
php artisan migrate:reset  # Reset all migrations
php artisan migrate:refresh # Reset and re-run all migrations
php artisan db:seed        # Run database seeders
php artisan tinker         # Interactive shell to query database
```

### Generate Files
```bash
php artisan make:model Model -m               # Create model with migration
php artisan make:controller ControllerName    # Create controller
php artisan make:request StoreRequest         # Create form request
php artisan make:migration create_table       # Create migration
php artisan make:seeder SeederName            # Create seeder
php artisan make:job JobName                  # Create job
php artisan make:event EventName              # Create event
php artisan make:listener ListenerName        # Create listener
php artisan make:mail MailClass               # Create mailable
php artisan make:notification NotificationName # Create notification
php artisan make:policy PolicyName            # Create policy
```

### Optimization & Cache
```bash
php artisan config:cache   # Cache configuration
php artisan route:cache    # Cache routes
php artisan view:cache     # Cache views
php artisan cache:clear    # Clear cache
php artisan view:clear     # Clear view cache
composer dump-autoload -o  # Optimize autoloader
```

### Testing
```bash
php artisan test           # Run test suite
php artisan test Feature   # Run feature tests
php artisan test Unit      # Run unit tests
```

---

## 📦 NPM Commands (from package.json)

```json
{
  "scripts": {
    "dev": "vite",                    // Development server
    "build": "vite build",            // Production build
    "watch": "vite build --watch"     // Watch and rebuild
  }
}
```

---

## 🛠️ Detailed Artisan Commands

### Model & Migration
```bash
# Create a new model
php artisan make:model Post

# Create model with migration
php artisan make:model Post -m

# Create model with controller and migration
php artisan make:model Post -mcr

# Create model with factory
php artisan make:model Post -f
```

### Controllers
```bash
# Create a basic controller
php artisan make:controller UserController

# Create a resource controller (with all CRUD methods)
php artisan make:controller PostController --model=Post -r

# Create an invokable controller (single __invoke method)
php artisan make:controller SingleActionController --invokable
```

### Requests & Validation
```bash
# Create a form request
php artisan make:request StorePostRequest

# List all requests
php artisan command --help | grep request
```

### Migrations
```bash
# Create a new migration
php artisan make:migration create_users_table

# Create migration for adding columns
php artisan make:migration add_avatar_to_users

# Run migrations
php artisan migrate

# Rollback last batch
php artisan migrate:rollback

# Rollback last N batches
php artisan migrate:rollback --step=5

# Rollback all migrations
php artisan migrate:reset

# Rollback and re-run
php artisan migrate:refresh

# Migrate fresh
php artisan migrate:fresh
```

### Database Operations
```bash
# Enter Tinker (interactive shell)
php artisan tinker

# Examples in Tinker:
# > $user = App\Models\User::first();
# > $user->name;
# > create new data manually

# Run seeders
php artisan db:seed

# Run specific seeder
php artisan db:seed --class=UserSeeder
```

### Authentication & Authorization
```bash
# Create auth scaffolding (Breeze is already installed)
php artisan breeze:install blade

# Create policy
php artisan make:policy PostPolicy --model=Post
```

### Jobs & Queues
```bash
# Create a job
php artisan make:job ProcessPodcast

# Work jobs
php artisan queue:work

# Work with timeout
php artisan queue:work --timeout=3600
```

### Events & Listeners
```bash
# Create event
php artisan make:event UserCreated

# Create listener
php artisan make:listener SendWelcomeEmail --event=UserCreated
```

### Mail & Notifications
```bash
# Create mailable
php artisan make:mail OrderShipped

# Create notification
php artisan make:notification OrderShipped

# Mail preview (visit in browser)
php artisan tinker
# > new App\Mail\OrderShipped()
```

### Utility Commands
```bash
# List all available routes
php artisan route:list

# Show specific route
php artisan route:list | grep posts

# Check Laravel version
php artisan --version

# Display help for a command
php artisan migrate --help

# Generate application documentation
php artisan docs

# Generate API documentation (if installed)
php artisan scribe:generate
```

### Cache Commands
```bash
# Clear all caches
php artisan cache:clear

# Clear specific cache
php artisan cache:forget key_name

# Clear view cache
php artisan view:clear

# Clear route cache
php artisan route:clear

# Clear config cache
php artisan config:clear
```

### Storage & Files
```bash
# Create storage link (for public files)
php artisan storage:link

# Create directory permissions
chmod -R 775 storage bootstrap/cache
```

### Development Tools
```bash
# Interactive shell
php artisan tinker

# Show environment variables
php artisan env

# Check application status
php artisan version

# Run scheduled tasks (for local testing)
php artisan schedule:run
```

---

## 🧪 Testing Commands

```bash
# Run all tests
php artisan test

# Run specific test file
php artisan test tests/Feature/UserTest.php

# Run specific test method
php artisan test tests/Feature/UserTest.php --filter=test_user_can_login

# Run with coverage report
php artisan test --coverage

# Run with parallel execution
php artisan test --parallel

# Run only feature tests
php artisan test --only feature

# Run only unit tests
php artisan test --only unit
```

---

## 📋 Package.json Scripts

### Current Scripts
```json
{
  "scripts": {
    "dev": "vite",
    "build": "vite build"
  }
}
```

### What These Do
- `npm run dev` - Start development server with hot reload at localhost:5173
- `npm run build` - Build minified assets for production

---

## 🔧 Common Workflows

### Creating a New Feature (CRUD)

```bash
# Step 1: Create model with migration
php artisan make:model Post -m

# Step 2: Create controller with resource methods
php artisan make:controller PostController --model=Post -r

# Step 3: Create form request for validation
php artisan make:request StorePostRequest
php artisan make:request UpdatePostRequest

# Step 4: Edit migration and add columns
# (Edit database/migrations/xxxx_create_posts_table.php)

# Step 5: Run migration
php artisan migrate

# Step 6: Create views
# (Create resources/views/posts/*.blade.php)

# Step 7: Add routes to routes/web.php
Route::resource('posts', PostController::class);
```

### Setting Up Database

```bash
# 1. Create database
mysql -u root -p
> CREATE DATABASE digital_life_manager;

# 2. Run migrations
php artisan migrate

# 3. Create test data (if seeders exist)
php artisan db:seed

# 4. Verify tables with Tinker
php artisan tinker
# > DB::table('posts')->count();
```

### Deploying to Production

```bash
# 1. Optimize code
php artisan optimize

# 2. Cache configuration
php artisan config:cache

# 3. Cache routes
php artisan route:cache

# 4. Cache views
php artisan view:cache

# 5. Build frontend
npm run build

# 6. Create storage link (if using local storage)
php artisan storage:link
```

### Debugging

```bash
# View logs
tail -f storage/logs/laravel.log

# Test database connection
php artisan tinker
# > DB::connection()->getPdo();

# Check config
php artisan config:show database

# List all services
php artisan config:show app
```

---

## 💡 Pro Tips

### Understanding Artisan Output
- `✓` or `✔` = Success
- `✗` or `✘` = Error
- `⚠` = Warning

### Useful Aliases
```bash
# Add to ~/.bashrc or ~/.zshrc
alias artisan='php artisan'
alias serve='php artisan serve'

# Then use: artisan make:model Post -m
```

### Time-Saving Combinations
```bash
# Create model, migration, controller, and factory all at once
php artisan make:model Post -mcfr

# Flags explained:
# -m = migration
# -c = controller
# -f = factory
# -r = resource controller methods
```

---

## 📚 Further Reading

- [Laravel Artisan Documentation](https://laravel.com/docs/13/artisan)
- [Composer Scripts Documentation](https://getcomposer.org/doc/articles/scripts.md)
- [NPM Scripts Documentation](https://docs.npmjs.com/cli/run-script)

---

## Notes

- All paths are relative to project root
- Commands work on Linux/Mac/Windows (with bash or PowerShell)
- Some commands require dependencies to be installed first
- Database commands assume MySQL is running
- Always commit migrations before deploying to production

---

Last Updated: April 28, 2026
Laravel Version: 13
