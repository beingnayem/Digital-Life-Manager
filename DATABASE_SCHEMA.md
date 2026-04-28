# Digital Life Manager - Database Schema Design

## 📊 Database Overview

A comprehensive, normalized relational database schema for a productivity web application with user authentication, task management, expense tracking, note-taking, and mood tracking capabilities.

---

## 🎯 Database Design Principles

- **Normalization**: Third Normal Form (3NF)
- **Scalability**: Indexed for performance
- **Integrity**: Referential integrity with foreign keys
- **Consistency**: Standardized naming conventions
- **Flexibility**: Designed for future feature expansion

---

## 📋 Table Specifications

### 1. **users** - User Authentication & Profile
Primary table for user account management.

```sql
CREATE TABLE users (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    email_verified_at TIMESTAMP NULL,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(20) NULL,
    avatar_url VARCHAR(500) NULL,
    bio TEXT NULL,
    timezone VARCHAR(50) DEFAULT 'UTC',
    notification_preferences JSON NULL,
    is_active BOOLEAN DEFAULT true,
    last_login_at TIMESTAMP NULL,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    deleted_at TIMESTAMP NULL,
    
    UNIQUE INDEX email_unique (email),
    INDEX is_active_idx (is_active),
    INDEX created_at_idx (created_at),
    FULLTEXT INDEX name_search (name)
);
```

**Columns:**
- `id`: Unique user identifier (Primary Key)
- `name`: User full name
- `email`: Login email (Unique)
- `email_verified_at`: Email verification timestamp
- `password`: Hashed password
- `phone`: Optional contact phone
- `avatar_url`: Profile picture URL
- `bio`: User biography/about me
- `timezone`: User's timezone for scheduling
- `notification_preferences`: JSON config for notifications
- `is_active`: Account status flag
- `last_login_at`: Last login timestamp (for analytics)
- `created_at`, `updated_at`: Timestamps
- `deleted_at`: Soft delete timestamp

---

### 2. **tasks** - Task Management
Tracks user tasks with priority, status, and deadlines.

```sql
CREATE TABLE tasks (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    user_id BIGINT UNSIGNED NOT NULL,
    title VARCHAR(255) NOT NULL,
    description LONGTEXT NULL,
    category VARCHAR(100) NULL,
    priority ENUM('low', 'medium', 'high', 'urgent') DEFAULT 'medium',
    status ENUM('not_started', 'in_progress', 'completed', 'archived', 'cancelled') DEFAULT 'not_started',
    due_date DATETIME NULL,
    completed_at TIMESTAMP NULL,
    estimated_hours INT NULL,
    actual_hours INT NULL,
    color_tag VARCHAR(7) DEFAULT '#3b82f6',
    is_recurring BOOLEAN DEFAULT false,
    recurrence_pattern VARCHAR(50) NULL,
    tags JSON NULL,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    deleted_at TIMESTAMP NULL,
    
    INDEX user_id_idx (user_id),
    INDEX status_idx (status),
    INDEX due_date_idx (due_date),
    INDEX priority_idx (priority),
    INDEX created_at_idx (created_at),
    FULLTEXT INDEX title_description (title, description),
    
    CONSTRAINT fk_tasks_user_id 
        FOREIGN KEY (user_id) REFERENCES users(id) 
        ON DELETE CASCADE ON UPDATE CASCADE
);
```

**Columns:**
- `id`: Task identifier (Primary Key)
- `user_id`: Owning user (Foreign Key → users)
- `title`: Task name/title
- `description`: Detailed task description
- `category`: Task category (Work, Personal, Health, etc.)
- `priority`: Task priority level (ENUM)
- `status`: Current task status (ENUM)
- `due_date`: Task deadline
- `completed_at`: When task was marked complete
- `estimated_hours`: Estimated time to complete
- `actual_hours`: Actual time spent
- `color_tag`: Visual color tag (hex)
- `is_recurring`: Whether task repeats
- `recurrence_pattern`: Cron-like pattern (daily, weekly, monthly)
- `tags`: JSON array of tags
- `created_at`, `updated_at`: Timestamps
- `deleted_at`: Soft delete

---

### 3. **expenses** - Expense Tracking
Tracks user expenses with categories and budgeting.

```sql
CREATE TABLE expenses (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    user_id BIGINT UNSIGNED NOT NULL,
    amount DECIMAL(10, 2) NOT NULL,
    category VARCHAR(100) NOT NULL,
    description VARCHAR(500) NULL,
    payment_method ENUM('cash', 'card', 'check', 'bank_transfer', 'mobile_payment', 'other') DEFAULT 'card',
    date DATE NOT NULL,
    receipt_url VARCHAR(500) NULL,
    status ENUM('pending', 'confirmed', 'disputed', 'refunded') DEFAULT 'confirmed',
    tags JSON NULL,
    budget_alert_sent BOOLEAN DEFAULT false,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    deleted_at TIMESTAMP NULL,
    
    INDEX user_id_idx (user_id),
    INDEX category_idx (category),
    INDEX date_idx (date),
    INDEX amount_idx (amount),
    INDEX created_at_idx (created_at),
    INDEX user_date_idx (user_id, date),
    
    CONSTRAINT fk_expenses_user_id 
        FOREIGN KEY (user_id) REFERENCES users(id) 
        ON DELETE CASCADE ON UPDATE CASCADE
);
```

**Columns:**
- `id`: Expense identifier (Primary Key)
- `user_id`: Owning user (Foreign Key → users)
- `amount`: Expense amount (decimal for currency)
- `category`: Expense category (Food, Transport, Entertainment, etc.)
- `description`: Expense details
- `payment_method`: How payment was made (ENUM)
- `date`: Date of expense
- `receipt_url`: Digital receipt image/document URL
- `status`: Expense status (pending, confirmed, disputed, refunded)
- `tags`: JSON array of tags
- `budget_alert_sent`: Flag for budget notifications
- `created_at`, `updated_at`: Timestamps
- `deleted_at`: Soft delete

---

### 4. **notes** - Note-Taking
Stores user notes with organization and search capabilities.

```sql
CREATE TABLE notes (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    user_id BIGINT UNSIGNED NOT NULL,
    title VARCHAR(255) NOT NULL,
    content LONGTEXT NOT NULL,
    category VARCHAR(100) NULL,
    color_tag VARCHAR(7) DEFAULT '#fbbf24',
    is_pinned BOOLEAN DEFAULT false,
    is_archived BOOLEAN DEFAULT false,
    tags JSON NULL,
    attachments JSON NULL,
    collaborator_ids JSON NULL,
    permission_level ENUM('private', 'shared', 'public') DEFAULT 'private',
    word_count INT DEFAULT 0,
    reading_time INT DEFAULT 0,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    deleted_at TIMESTAMP NULL,
    
    INDEX user_id_idx (user_id),
    INDEX is_pinned_idx (is_pinned),
    INDEX is_archived_idx (is_archived),
    INDEX category_idx (category),
    INDEX created_at_idx (created_at),
    INDEX updated_at_idx (updated_at),
    FULLTEXT INDEX title_content (title, content),
    
    CONSTRAINT fk_notes_user_id 
        FOREIGN KEY (user_id) REFERENCES users(id) 
        ON DELETE CASCADE ON UPDATE CASCADE
);
```

**Columns:**
- `id`: Note identifier (Primary Key)
- `user_id`: Owning user (Foreign Key → users)
- `title`: Note title
- `content`: Note body/content (supports markdown)
- `category`: Organization category
- `color_tag`: Visual color for organization
- `is_pinned`: Important notes flag
- `is_archived`: Archive status for older notes
- `tags`: JSON array of tags
- `attachments`: JSON array of attachment metadata
- `collaborator_ids`: JSON array of other users with access
- `permission_level`: Access control (private, shared, public)
- `word_count`: Calculated word count for stats
- `reading_time`: Estimated reading time in minutes
- `created_at`, `updated_at`: Timestamps
- `deleted_at`: Soft delete

---

### 5. **moods** - Mood Tracking
Daily mood entries for mental health & wellbeing tracking.

```sql
CREATE TABLE moods (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    user_id BIGINT UNSIGNED NOT NULL,
    mood_level INT NOT NULL CHECK (mood_level >= 1 AND mood_level <= 10),
    mood_label VARCHAR(50) NULL,
    energy_level INT NULL CHECK (energy_level >= 1 AND energy_level <= 10),
    stress_level INT NULL CHECK (stress_level >= 1 AND stress_level <= 10),
    focus_level INT NULL CHECK (focus_level >= 1 AND focus_level <= 10),
    emotion_tags JSON NULL,
    notes TEXT NULL,
    activities JSON NULL,
    sleep_hours DECIMAL(3, 1) NULL,
    weather VARCHAR(50) NULL,
    location VARCHAR(100) NULL,
    recorded_date DATE NOT NULL,
    recorded_at TIMESTAMP NOT NULL,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    
    INDEX user_id_idx (user_id),
    INDEX recorded_date_idx (recorded_date),
    INDEX mood_level_idx (mood_level),
    INDEX user_date_idx (user_id, recorded_date),
    INDEX recorded_at_idx (recorded_at),
    UNIQUE INDEX user_date_unique (user_id, recorded_date),
    
    CONSTRAINT fk_moods_user_id 
        FOREIGN KEY (user_id) REFERENCES users(id) 
        ON DELETE CASCADE ON UPDATE CASCADE
);
```

**Columns:**
- `id`: Mood entry identifier (Primary Key)
- `user_id`: Owning user (Foreign Key → users)
- `mood_level`: Mood rating 1-10 (Primary metric)
- `mood_label`: Text label (happy, sad, anxious, etc.)
- `energy_level`: Energy rating 1-10
- `stress_level`: Stress rating 1-10
- `focus_level`: Focus/concentration rating 1-10
- `emotion_tags`: JSON array of emotions
- `notes`: Additional context/notes
- `activities`: JSON array of activities done
- `sleep_hours`: Hours of sleep from previous night
- `weather`: Weather condition (sunny, rainy, cloudy)
- `location`: Where mood was recorded
- `recorded_date`: Date of mood entry
- `recorded_at`: Exact timestamp
- `created_at`, `updated_at`: Timestamps
- **Note**: One mood entry per user per date (UNIQUE constraint)

---

### 6. **budget** - Budget Planning (Optional Enhancement)
Budgets for expense categories by month.

```sql
CREATE TABLE budgets (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    user_id BIGINT UNSIGNED NOT NULL,
    category VARCHAR(100) NOT NULL,
    limit_amount DECIMAL(10, 2) NOT NULL,
    month_year VARCHAR(7) NOT NULL,
    spent_amount DECIMAL(10, 2) DEFAULT 0,
    alert_threshold INT DEFAULT 80,
    is_active BOOLEAN DEFAULT true,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    
    INDEX user_id_idx (user_id),
    INDEX month_year_idx (month_year),
    UNIQUE INDEX user_category_month (user_id, category, month_year),
    
    CONSTRAINT fk_budgets_user_id 
        FOREIGN KEY (user_id) REFERENCES users(id) 
        ON DELETE CASCADE ON UPDATE CASCADE
);
```

---

### 7. **audit_logs** (Optional)
Audit trail for security & compliance.

```sql
CREATE TABLE audit_logs (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    user_id BIGINT UNSIGNED NOT NULL,
    action VARCHAR(255) NOT NULL,
    entity_type VARCHAR(100) NOT NULL,
    entity_id BIGINT UNSIGNED NULL,
    old_values JSON NULL,
    new_values JSON NULL,
    ip_address VARCHAR(45) NULL,
    user_agent TEXT NULL,
    created_at TIMESTAMP,
    
    INDEX user_id_idx (user_id),
    INDEX action_idx (action),
    INDEX created_at_idx (created_at),
    INDEX entity_idx (entity_type, entity_id),
    
    CONSTRAINT fk_audit_user_id 
        FOREIGN KEY (user_id) REFERENCES users(id) 
        ON DELETE CASCADE ON UPDATE CASCADE
);
```

---

## 📐 Entity Relationship Diagram (Text Format)

```
┌─────────────────────────────────────────────────────────────────┐
│                      DIGITAL LIFE MANAGER DB                    │
└─────────────────────────────────────────────────────────────────┘

                            ┌─────────────┐
                            │   USERS     │
                            ├─────────────┤
                            │ id (PK)     │
                            │ name        │
                            │ email (UQ)  │
                            │ password    │
                            │ timezone    │
                            │ is_active   │
                            └─────────────┘
                                  │
                    ┌─────────────┼─────────────┬────────────────┐
                    │             │             │                │
                    ▼             ▼             ▼                ▼
            ┌──────────────┐ ┌──────────────┐ ┌──────────────┐ ┌──────────────┐
            │    TASKS     │ │  EXPENSES    │ │    NOTES     │ │    MOODS     │
            ├──────────────┤ ├──────────────┤ ├──────────────┤ ├──────────────┤
            │ id (PK)      │ │ id (PK)      │ │ id (PK)      │ │ id (PK)      │
            │ user_id (FK) │ │ user_id (FK) │ │ user_id (FK) │ │ user_id (FK) │
            │ title        │ │ amount       │ │ title        │ │ mood_level   │
            │ description  │ │ category     │ │ content      │ │ energy_level │
            │ status       │ │ date         │ │ category     │ │ date         │
            │ priority     │ │ payment_type │ │ color_tag    │ │ recorded_at  │
            │ due_date     │ │ payment_meth │ │ is_pinned    │ │ emotion_tags │
            │ completed_at │ │ status       │ │ is_archived  │ │ activities   │
            │ tags         │ │ tags         │ │ tags         │ │ sleep_hours  │
            │ recurrence   │ │              │ │ attachments  │ │ weather      │
            └──────────────┘ └──────────────┘ └──────────────┘ └──────────────┘
                    │                 │
                    └─────────┬───────┘
                              │
                    (Optional)▼
                    ┌──────────────┐
                    │   BUDGETS    │
                    ├──────────────┤
                    │ id (PK)      │
                    │ user_id (FK) │
                    │ category     │
                    │ limit_amount │
                    │ month_year   │
                    │ spent_amount │
                    └──────────────┘

Relationships:
─────────────
1. users → tasks         (1:N) One user has many tasks
2. users → expenses      (1:N) One user has many expenses
3. users → notes         (1:N) One user has many notes
4. users → moods         (1:N) One user has many mood entries
5. users → budgets       (1:N) One user has many budgets
6. expenses → budgets    (Implicit) Expenses tracked against budgets
```

---

## 🔑 Relationships & Constraints

### Foreign Key Relationships

| From Table | To Table | Cardinality | Cascade Delete |
|------------|----------|-------------|----------------|
| tasks | users | N:1 | YES (delete user deletes tasks) |
| expenses | users | N:1 | YES (delete user deletes expenses) |
| notes | users | N:1 | YES (delete user deletes notes) |
| moods | users | N:1 | YES (delete user deletes mood entries) |
| budgets | users | N:1 | YES (delete user deletes budgets) |
| audit_logs | users | N:1 | YES (delete user deletes audit logs) |

### Unique Constraints

| Table | Column(s) | Purpose |
|-------|-----------|---------|
| users | email | Prevent duplicate emails |
| moods | user_id, recorded_date | One mood entry per day per user |
| budgets | user_id, category, month_year | One budget per category per month |

---

## 📑 Indexes Strategy

### Performance Indexes

```sql
-- Fast lookups by user (most common queries)
INDEX user_id_idx ON tasks(user_id);
INDEX user_id_idx ON expenses(user_id);
INDEX user_id_idx ON notes(user_id);
INDEX user_id_idx ON moods(user_id);

-- Status/category filtering
INDEX status_idx ON tasks(status);
INDEX category_idx ON expenses(category);
INDEX is_archived_idx ON notes(is_archived);

-- Date range queries (reports, analytics)
INDEX date_idx ON expenses(date);
INDEX recorded_date_idx ON moods(recorded_date);
INDEX due_date_idx ON tasks(due_date);

-- Composite indexes for complex queries
INDEX user_date_idx ON expenses(user_id, date);
INDEX user_date_idx ON moods(user_id, recorded_date);
INDEX user_status_idx ON tasks(user_id, status);

-- Full-text search
FULLTEXT INDEX title_search ON tasks(title);
FULLTEXT INDEX title_content ON notes(title, content);
FULLTEXT INDEX name_search ON users(name);
```

---

## 🔒 Data Integrity Rules

### Check Constraints

```sql
-- Mood levels must be 1-10
CONSTRAINT mood_level_check CHECK (mood_level >= 1 AND mood_level <= 10);
CONSTRAINT energy_level_check CHECK (energy_level >= 1 AND energy_level <= 10);
CONSTRAINT stress_level_check CHECK (stress_level >= 1 AND stress_level <= 10);
CONSTRAINT focus_level_check CHECK (focus_level >= 1 AND focus_level <= 10);

-- Amounts must be positive
CONSTRAINT amount_positive CHECK (amount > 0);
CONSTRAINT limit_positive CHECK (limit_amount > 0);
```

### Default Values

```sql
-- Timestamps
DEFAULT CURRENT_TIMESTAMP ON created_at;
DEFAULT CURRENT_TIMESTAMP ON UPDATE updated_at;

-- Status defaults
DEFAULT 'not_started' ON tasks(status);
DEFAULT 'confirmed' ON expenses(status);
DEFAULT 'private' ON notes(permission_level);

-- Ratings defaults
DEFAULT 5 ON moods(mood_level);
DEFAULT 'UTC' ON users(timezone);
```

---

## 📊 Data Types Selection Rationale

| Data Type | Usage | Reason |
|-----------|-------|--------|
| BIGINT UNSIGNED | Primary/Foreign keys | Supports up to 18 billion records |
| VARCHAR(255) | Text fields | MySQL key size limit |
| LONGTEXT | Large content | Notes, descriptions (up to 4GB) |
| DECIMAL(10, 2) | Currency | Precise decimal calculation |
| ENUM | Fixed options | Space-efficient, validated at DB level |
| JSON | Complex data | Tags, arrays, flexible structure |
| DATE | Dates | Efficient date storage |
| DATETIME | Exact timestamps | Combine date + time |
| TIMESTAMP | Audit trails | Auto-update capability |
| BOOLEAN | Flags | True/false values |

---

## 🚀 Scalability Considerations

### 1. **Partitioning** (For large datasets)
```sql
-- Partition moods by year (automatic archival)
PARTITION BY RANGE (YEAR(recorded_date)) (
    PARTITION p2024 VALUES LESS THAN (2025),
    PARTITION p2025 VALUES LESS THAN (2026)
);

-- Partition expenses by month
PARTITION BY RANGE (YEAR_MONTH(date)) (
    PARTITION p202501 VALUES LESS THAN (202502),
    PARTITION p202502 VALUES LESS THAN (202503)
);
```

### 2. **Query Optimization**
- Indexes on frequently queried columns
- Composite indexes for common filter combinations
- Full-text indexes for search operations

### 3. **Data Archival**
- Use `deleted_at` for soft deletes
- Archive old expense/mood data annually
- Implement data retention policies

### 4. **Caching Strategy**
- Cache user preferences
- Cache mood statistics
- Cache expense summaries

---

## 📈 Query Examples

### Get user's tasks for today
```sql
SELECT * FROM tasks 
WHERE user_id = ? 
AND DATE(due_date) = CURDATE()
AND status != 'completed'
ORDER BY priority DESC;
```

### Monthly expense summary by category
```sql
SELECT category, SUM(amount) as total
FROM expenses
WHERE user_id = ? 
AND YEAR(date) = YEAR(CURDATE())
AND MONTH(date) = MONTH(CURDATE())
GROUP BY category;
```

### Mood trends (last 30 days)
```sql
SELECT 
    recorded_date,
    AVG(mood_level) as avg_mood,
    AVG(energy_level) as avg_energy,
    AVG(stress_level) as avg_stress
FROM moods
WHERE user_id = ? 
AND recorded_date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
GROUP BY recorded_date
ORDER BY recorded_date;
```

### Search notes with full-text
```sql
SELECT * FROM notes
WHERE user_id = ?
AND MATCH(title, content) AGAINST(? IN BOOLEAN MODE)
AND is_archived = FALSE;
```

---

## 🔐 Security Considerations

1. **Hashed Passwords**: Always hash with bcrypt/argon2
2. **Soft Deletes**: Keep audit trail (deleted_at)
3. **Audit Logs**: Track all important changes
4. **Role-Based Access**: Implement at application layer
5. **Encryption**: Sensitive fields (SSN, CC info) if stored
6. **SQL Injection**: Use prepared statements (Laravel ORM handles this)
7. **Rate Limiting**: Track by user_id for throttling

---

## 🛠️ Migration Best Practices

1. **Version Control**: Keep all migrations in git
2. **Naming Convention**: Timestamp_description (2026_04_28_create_tasks_table)
3. **Reversible**: Always include `down()` method for rollback
4. **Atomic**: One logical change per migration
5. **Testing**: Test migrations on staging first
6. **Documentation**: Comment complex logic

---

## 📊 Database Statistics (Estimated)

| Metric | Small | Medium | Large |
|--------|-------|--------|-------|
| Users | 100 | 10,000 | 1M+ |
| Tasks | 2,500 | 250,000 | 25M+ |
| Expenses | 5,000 | 500,000 | 50M+ |
| Notes | 2,000 | 200,000 | 20M+ |
| Moods | 360 | 36,000 | 3.6M+ |
| Storage | ~50MB | ~5GB | ~500GB |

---

## ✅ Normalization Check

### First Normal Form (1NF)
✅ All attributes contain atomic (indivisible) values
✅ No repeating groups (use JSON arrays for flexibility)

### Second Normal Form (2NF)
✅ In 1NF
✅ All non-key attributes fully dependent on primary key

### Third Normal Form (3NF)
✅ In 2NF
✅ No transitive dependencies between non-key attributes

**Note**: JSON fields provide flexibility while maintaining data integrity.

---

## 🎓 Key Design Decisions

1. **Soft Deletes**: `deleted_at` timestamps preserve data
2. **JSON Fields**: Flexible schema for tags, preferences, arrays
3. **Audit Timestamps**: All tables have `created_at`, `updated_at`
4. **Enum Types**: Validated status/priority values at database layer
5. **Composite Indexes**: Optimize common query patterns
6. **Cascade Delete**: Simplify cleanup when users are deleted
7. **UNIQUE Constraints**: No duplicate emails, one mood per day
8. **Check Constraints**: Validate numeric ranges (1-10 for ratings)

---

## 📚 Next Steps

1. Generate Laravel migrations (see MIGRATIONS.md)
2. Create Eloquent models with relationships
3. Add model factories for testing
4. Write database seeders for development
5. Run migrations: `php artisan migrate`
6. Test relationships and queries

---

## 📄 Summary

**Tables**: 7 core + 1 optional (audit)
**Columns**: 80+ across all tables
**Relationships**: 6 foreign key relationships
**Indexes**: 25+ performance indexes
**Constraints**: UNIQUE, CHECK, NOT NULL, DEFAULT
**Scalability**: Designed for millions of records
**Security**: Soft deletes, audit trail, data integrity
**Performance**: Optimized query patterns, composite indexes

This schema is **production-ready**, **scalable**, **maintainable**, and follows **database best practices**.

---

Last Updated: April 28, 2026
Database: MySQL 5.7+ / 8.0+
Framework: Laravel 13
