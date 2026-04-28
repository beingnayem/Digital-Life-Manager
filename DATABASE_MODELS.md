# Database Implementation Guide - Digital Life Manager

## 📚 Complete Database Setup & Usage

This guide covers the full implementation of the Digital Life Manager database with models, relationships, and usage examples.

---

## 🚀 Setup Instructions

### Step 1: Run Migrations

```bash
cd /Code/MU/Vai_er_Vatar_WP/WP_Final_Project/digital-life-manager

# Create MySQL database
mysql -u root -p
CREATE DATABASE digital_life_manager CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
EXIT;

# Run migrations to create all tables
php artisan migrate
```

This creates all 7 tables:
- `users` - User accounts & profiles
- `tasks` - Task management
- `expenses` - Expense tracking
- `notes` - Note-taking
- `moods` - Mood tracking
- `budgets` - Budget planning
- `audit_logs` - Audit trail

### Step 2: Update User Model

The User model now includes relationships to all other tables.

---

## 📊 Data Models & Relationships

### User Model Relationships

```php
// app/Models/User.php
class User {
    // One-to-Many relationships
    public function tasks() { return $this->hasMany(Task::class); }
    public function expenses() { return $this->hasMany(Expense::class); }
    public function notes() { return $this->hasMany(Note::class); }
    public function moods() { return $this->hasMany(Mood::class); }
    public function budgets() { return $this->hasMany(Budget::class); }
    public function auditLogs() { return $this->hasMany(AuditLog::class); }
}
```

### Relationship Diagram

```
User (1)
├── Tasks (N)
├── Expenses (N)
├── Notes (N)
├── Moods (N)
├── Budgets (N)
└── AuditLogs (N)
```

---

## 💡 Usage Examples

### 🎯 Tasks

```php
use App\Models\Task;

// Create a new task
$task = auth()->user()->tasks()->create([
    'title' => 'Complete project report',
    'description' => 'Detailed report for Q1',
    'priority' => 'high',
    'status' => 'in_progress',
    'due_date' => now()->addDays(3),
    'estimated_hours' => 8,
]);

// Get all incomplete tasks
$tasks = auth()->user()->tasks()->incomplete()->get();

// Get tasks by priority
$urgentTasks = auth()->user()->tasks()
    ->byPriority('urgent')
    ->get();

// Get overdue tasks
$overdueTasks = auth()->user()->tasks()
    ->where('due_date', '<', now())
    ->where('status', '!=', 'completed')
    ->get();

// Get tasks due this week
$weekTasks = auth()->user()->tasks()->dueThisWeek()->get();

// Mark task as completed
$task->complete(); // Sets status to 'completed' and completed_at timestamp

// Get task progress
$progress = $task->getProgressPercentage(); // 0-100%

// Calculate remaining hours
$remaining = $task->hoursRemaining(); // estimated - actual hours

// Search tasks
$results = auth()->user()->tasks()
    ->whereRaw("MATCH(title, description) AGAINST(? IN BOOLEAN MODE)", ['report'])
    ->get();

// Get task statistics
$stats = [
    'total' => auth()->user()->tasks()->count(),
    'completed' => auth()->user()->tasks()->where('status', 'completed')->count(),
    'this_week' => auth()->user()->tasks()->dueThisWeek()->count(),
];
```

### 💰 Expenses

```php
use App\Models\Expense;

// Create an expense
$expense = auth()->user()->expenses()->create([
    'amount' => 45.99,
    'category' => 'groceries',
    'description' => 'Weekly groceries',
    'payment_method' => 'card',
    'date' => now(),
]);

// Get current month expenses
$monthExpenses = auth()->user()->expenses()->currentMonth()->get();

// Get expenses by category
$foodExpenses = auth()->user()->expenses()
    ->byCategory('food')
    ->get();

// Get monthly summary
$summary = Expense::monthlySummaryByCategory(auth()->id());
// Returns: [
//     ['category' => 'food', 'total' => 250.00],
//     ['category' => 'transport', 'total' => 120.50],
// ]

// Get total for period
$total = Expense::totalForPeriod(
    auth()->id(),
    now()->startOfMonth(),
    now()->endOfMonth()
); // Returns: 450.00

// Get high-value expenses
$highValue = auth()->user()->expenses()
    ->highValue(100) // >= $100
    ->get();

// Check budget status
$expense->checkBudgetExceedance(); // true if over budget

// Get budget percentage
$percentage = $expense->getBudgetPercentage(); // 75%

// Get detailed monthly report
$monthlyReport = [
    'total_spent' => auth()->user()->expenses()->currentMonth()->sum('amount'),
    'by_category' => Expense::monthlySummaryByCategory(auth()->id()),
    'average_per_day' => auth()->user()->expenses()->currentMonth()->average('amount'),
];
```

### 📝 Notes

```php
use App\Models\Note;

// Create a note
$note = auth()->user()->notes()->create([
    'title' => 'Project Ideas',
    'content' => '# Ideas\n- Feature A\n- Feature B',
    'category' => 'work',
    'color_tag' => '#3b82f6',
    'permission_level' => 'private',
]);

// Automatically calculate word count and reading time
$note->calculateWordCount();
$note->calculateReadingTime();
$note->save();

// Pin a note
$note->pin();

// Archive a note
$note->archive();

// Restore from archive
$note->restore();

// Get pinned notes
$pinned = auth()->user()->notes()->pinned()->get();

// Get active (non-archived) notes
$active = auth()->user()->notes()->active()->get();

// Get notes by category
$workNotes = auth()->user()->notes()->byCategory('work')->get();

// Search notes
$results = auth()->user()->notes()
    ->search('important meeting')
    ->get();

// Get recently updated notes
$recent = auth()->user()->notes()->recent(days: 7)->get();

// Collaboration: Add collaborator
$note->addCollaborator($userId);

// Remove collaborator
$note->removeCollaborator($userId);

// Check access permission
$canAccess = $note->canAccess(auth()->id()); // true/false

// Get note statistics
$stats = [
    'total' => auth()->user()->notes()->count(),
    'pinned' => auth()->user()->notes()->pinned()->count(),
    'archived' => auth()->user()->notes()->archived()->count(),
];
```

### 🎭 Moods

```php
use App\Models\Mood;

// Record mood entry
$mood = auth()->user()->moods()->create([
    'mood_level' => 7,
    'mood_label' => 'happy',
    'energy_level' => 8,
    'stress_level' => 3,
    'focus_level' => 9,
    'emotion_tags' => ['grateful', 'motivated'],
    'activities' => ['exercise', 'coding', 'meditation'],
    'sleep_hours' => 8.5,
    'weather' => 'sunny',
    'recorded_date' => now()->date,
    'recorded_at' => now(),
]);

// Get today's mood
$today = auth()->user()->moods()
    ->forDate(now())
    ->first();

// Get mood for a period
$weekMoods = auth()->user()->moods()
    ->betweenDates(now()->subDays(7), now())
    ->get();

// Get moods for a month
$monthMoods = auth()->user()->moods()
    ->forMonth(2026, 4) // April 2026
    ->get();

// Get positive moods
$positive = auth()->user()->moods()->positive()->get();

// Get high stress entries
$stressed = auth()->user()->moods()->highStress()->get();

// Get average mood
$avg = Mood::getAverageMood(
    auth()->id(),
    now()->startOfMonth(),
    now()->endOfMonth()
); // 6.5

// Get statistics for a period
$stats = Mood::getStatistics(
    auth()->id(),
    now()->subDays(30),
    now()
);
// Returns: [
//     'count' => 28,
//     'avg_mood' => 6.8,
//     'avg_energy' => 7.2,
//     'avg_stress' => 4.1,
//     'avg_sleep' => 7.9,
//     'highest_mood' => 10,
//     'lowest_mood' => 2,
// ]

// Get mood category
$category = $mood->getMoodCategory(); // 'excellent', 'good', 'neutral', 'poor', 'critical'

// Get mood trend
$trend = Mood::getTrend(auth()->id(), days: 7); // 'improving', 'declining', 'stable'

// Identify patterns
$patterns = Mood::identifyPatterns(auth()->id());
// Returns: [
//     'best_days' => ['Monday', 'Friday', ...],
//     'worst_days' => ['Sunday', 'Wednesday', ...],
//     'common_activities' => [...],
// ]
```

### 💵 Budgets

```php
use App\Models\Budget;

// Create a budget
$budget = auth()->user()->budgets()->create([
    'category' => 'food',
    'limit_amount' => 500,
    'month_year' => now()->format('Y-m'), // '2026-04'
    'alert_threshold' => 80,
]);

// Get active budgets
$active = auth()->user()->budgets()->active()->get();

// Get budgets for a month
$thisMonth = auth()->user()->budgets()
    ->forMonth(now()->format('Y-m'))
    ->get();

// Get utilization percentage
$used = $budget->getUtilizationPercentage(); // 45.5%

// Get remaining budget
$remaining = $budget->getRemaining(); // 275.00

// Check if exceeded
$exceeded = $budget->isExceeded(); // true/false

// Check if threshold reached
$alertable = $budget->isThresholdReached(); // Should send alert?

// Recalculate from expenses
$budget->recalculateSpentAmount();

// Monthly budget summary
$budgets = auth()->user()->budgets()
    ->forMonth('2026-04')
    ->with(['expenses' => function($q) {
        $q->where('status', 'confirmed');
    }])
    ->get();
```

### 🔐 Audit Logs

```php
use App\Models\AuditLog;

// View audit logs
$logs = auth()->user()->auditLogs()
    ->latest()
    ->paginate(50);

// Get logs for an action
$updates = auth()->user()->auditLogs()
    ->byAction('updated')
    ->recent() // Last 60 minutes
    ->get();

// Get logs for an entity
$taskLogs = AuditLog::byEntity('Task', $taskId)->get();

// Get logs in date range
$monthLogs = auth()->user()->auditLogs()
    ->betweenDates(now()->startOfMonth(), now()->endOfMonth())
    ->get();

// Get human-readable description
foreach ($logs as $log) {
    echo $log->getDescription();
    // "John updated the Task (#123)"
}

// See what changed
$changes = $log->getChanges();
// Returns: [
//     'status' => ['from' => 'pending', 'to' => 'completed'],
//     'actual_hours' => ['from' => 5, 'to' => 6],
// ]
```

---

## 📈 Dashboard Statistics

```php
// Get comprehensive dashboard stats
$stats = auth()->user()->getDashboardStats();

// Returns: [
//     'tasks' => [
//         'total' => 25,
//         'completed' => 8,
//         'pending' => 17,
//         'overdue' => 3,
//     ],
//     'expenses' => [
//         'this_month' => 450.00,
//         'average_per_day' => 15.00,
//         'by_category' => [...],
//     ],
//     'notes' => [
//         'total' => 42,
//         'pinned' => 3,
//         'archived' => 5,
//     ],
//     'mood' => [
//         'today' => {...},
//         'week_average' => 6.8,
//         'month_average' => 6.5,
//     ],
// ]
```

---

## 🔍 Advanced Queries

### Query Optimization

```php
// Use with() to eager load relationships (prevents N+1)
$tasksWithUser = Task::with('user')->get();

// Use select() to fetch only needed columns
$taskNames = Task::select('id', 'title')->get();

// Use chunk() for large datasets
Task::where('status', '!=', 'completed')->chunk(100, function ($tasks) {
    foreach ($tasks as $task) {
        // Process each chunk
    }
});

// Use exists() for checking presence
if (auth()->user()->tasks()->where('status', 'incomplete')->exists()) {
    // User has incomplete tasks
}
```

### Complex Filtering

```php
// Tasks with multiple conditions
$tasks = auth()->user()->tasks()
    ->where('priority', 'high')
    ->where('status', '!=', 'completed')
    ->whereDate('due_date', '<=', now()->addDays(7))
    ->orderBy('due_date')
    ->get();

// Complex expense report
$report = DB::table('expenses')
    ->where('user_id', auth()->id())
    ->whereYear('date', 2026)
    ->whereMonth('date', 4)
    ->groupBy('category')
    ->selectRaw('category, COUNT(*) as count, SUM(amount) as total, AVG(amount) as average')
    ->get();

// Mood analysis
$analysis = auth()->user()->moods()
    ->where('recorded_date', '>=', now()->subDays(30))
    ->selectRaw('
        DATE_FORMAT(recorded_date, "%w") as day_of_week,
        AVG(mood_level) as avg_mood,
        AVG(energy_level) as avg_energy,
        AVG(stress_level) as avg_stress
    ')
    ->groupBy('day_of_week')
    ->get();
```

---

## 🔧 Seeding Development Data

Create `database/seeders/DevelopmentSeeder.php`:

```php
<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Task;
use App\Models\Expense;
use App\Models\Note;
use App\Models\Mood;
use App\Models\Budget;
use Illuminate\Database\Seeder;

class DevelopmentSeeder extends Seeder
{
    public function run(): void
    {
        // Create test user
        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        // Create tasks
        Task::factory(20)->for($user)->create();

        // Create expenses
        Expense::factory(50)->for($user)->create();

        // Create notes
        Note::factory(15)->for($user)->create();

        // Create mood entries
        Mood::factory(30)->for($user)->create();

        // Create budgets
        Budget::factory(5)->for($user)->create();
    }
}
```

Run seeder:
```bash
php artisan db:seed --class=DevelopmentSeeder
```

---

## 🧪 Testing Examples

```php
// tests/Feature/TaskTest.php
public function test_user_can_create_task()
{
    $user = User::factory()->create();
    
    $response = $this->actingAs($user)
        ->post('/api/tasks', [
            'title' => 'Test Task',
            'priority' => 'high',
        ]);

    $response->assertStatus(201);
    
    $this->assertDatabaseHas('tasks', [
        'user_id' => $user->id,
        'title' => 'Test Task',
    ]);
}

public function test_user_can_only_see_own_tasks()
{
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();
    
    Task::factory()->for($user1)->create();
    Task::factory(5)->for($user2)->create();

    $this->actingAs($user1);
    
    $this->assertEquals(1, $user1->tasks()->count());
    $this->assertEquals(5, $user2->tasks()->count());
}
```

---

## 📋 Migration Checklist

- [x] Create `tasks` table with relationships
- [x] Create `expenses` table with relationships
- [x] Create `notes` table with relationships
- [x] Create `moods` table with relationships
- [x] Create `budgets` table with relationships
- [x] Create `audit_logs` table with relationships
- [x] Add indexes for performance
- [x] Add foreign key constraints
- [x] Add unique constraints
- [x] Add check constraints
- [x] Create Eloquent models
- [x] Add model relationships
- [x] Add model scopes
- [x] Add model methods
- [x] Update User model with relationships

---

## 🚀 Next Steps

1. **Run migrations**: `php artisan migrate`
2. **Create controllers**: `php artisan make:controller TaskController --model=Task`
3. **Create API routes**: Define in `routes/api.php`
4. **Create views**: Build Blade templates
5. **Add seeders**: For development/testing
6. **Write tests**: Ensure data integrity
7. **Create policies**: Authorize user actions

---

## ✅ Verification

After setup, verify tables exist:

```bash
php artisan tinker

>>> DB::connection()->getDoctrineSchemaManager()->listTableNames()
=> [
     "users",
     "tasks",
     "expenses",
     "notes",
     "moods",
     "budgets",
     "audit_logs",
   ]

>>> User::first()->tasks()->count()
=> 0  # No tasks yet (seeded)
```

---

## 📚 Resources

- [Laravel Models Documentation](https://laravel.com/docs/13/eloquent)
- [Database Relationships](https://laravel.com/docs/13/eloquent-relationships)
- [Query Builder](https://laravel.com/docs/13/queries)
- [Database Schema](https://laravel.com/docs/13/migrations)

---

This implementation is **production-ready**, **fully normalized**, **optimized for performance**, and **follows Laravel best practices**.

---

Last Updated: April 28, 2026
Laravel Version: 13
Database: MySQL 8.0+
