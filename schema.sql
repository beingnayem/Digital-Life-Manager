-- ============================================================================
-- Digital Life Manager - Complete Database Schema
-- Database: digital_life_manager
-- Created: 2026-04-28
-- ============================================================================

-- Create database if it doesn't exist
CREATE DATABASE IF NOT EXISTS digital_life_manager;
USE digital_life_manager;

-- Set proper character set
ALTER DATABASE digital_life_manager CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- ============================================================================
-- 1. CORE TABLES
-- ============================================================================

-- Users table (Base table for authentication)
CREATE TABLE IF NOT EXISTS users (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    email_verified_at TIMESTAMP NULL DEFAULT NULL,
    password VARCHAR(255) NOT NULL,
    remember_token VARCHAR(100) NULL DEFAULT NULL,
    phone VARCHAR(20) NULL DEFAULT NULL,
    avatar_url VARCHAR(500) NULL DEFAULT NULL,
    bio TEXT NULL DEFAULT NULL,
    timezone VARCHAR(50) NULL DEFAULT 'UTC',
    notification_preferences JSON NULL DEFAULT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    last_login_at TIMESTAMP NULL DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_email (email),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Password reset tokens table
CREATE TABLE IF NOT EXISTS password_reset_tokens (
    email VARCHAR(255) PRIMARY KEY,
    token VARCHAR(255) NOT NULL,
    created_at TIMESTAMP NULL DEFAULT NULL,
    
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Sessions table
CREATE TABLE IF NOT EXISTS sessions (
    id VARCHAR(255) PRIMARY KEY,
    user_id BIGINT UNSIGNED NULL,
    ip_address VARCHAR(45) NULL,
    user_agent TEXT NULL,
    payload LONGTEXT NOT NULL,
    last_activity INT NOT NULL,
    
    INDEX idx_user_id (user_id),
    INDEX idx_last_activity (last_activity),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- 2. FEATURE TABLES
-- ============================================================================

-- Tasks table
CREATE TABLE IF NOT EXISTS tasks (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    title VARCHAR(255) NOT NULL,
    description LONGTEXT NULL,
    category VARCHAR(100) NULL,
    priority ENUM('low', 'medium', 'high', 'urgent') DEFAULT 'medium',
    status ENUM('not_started', 'in_progress', 'completed', 'archived', 'cancelled') DEFAULT 'not_started',
    due_date DATETIME NULL,
    completed_at TIMESTAMP NULL,
    estimated_hours INT UNSIGNED NULL,
    actual_hours INT UNSIGNED NULL,
    color_tag VARCHAR(7) DEFAULT '#3b82f6',
    is_recurring BOOLEAN DEFAULT FALSE,
    recurrence_pattern VARCHAR(50) NULL,
    tags JSON NULL,
    deleted_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    CONSTRAINT fk_tasks_user_id FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE ON UPDATE CASCADE,
    INDEX idx_user_id (user_id),
    INDEX idx_status (status),
    INDEX idx_due_date (due_date),
    INDEX idx_priority (priority),
    INDEX idx_created_at (created_at),
    INDEX idx_user_status (user_id, status),
    FULLTEXT idx_fulltext_tasks (title, description)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Expenses table
CREATE TABLE IF NOT EXISTS expenses (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    amount DECIMAL(10, 2) NOT NULL,
    category VARCHAR(100) NOT NULL,
    description VARCHAR(500) NULL,
    payment_method ENUM('cash', 'card', 'check', 'bank_transfer', 'mobile_payment', 'other') DEFAULT 'card',
    date DATE NOT NULL,
    receipt_url VARCHAR(500) NULL,
    status ENUM('pending', 'confirmed', 'disputed', 'refunded') DEFAULT 'confirmed',
    tags JSON NULL,
    budget_alert_sent BOOLEAN DEFAULT FALSE,
    deleted_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    CONSTRAINT fk_expenses_user_id FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT chk_amount_positive CHECK (amount > 0),
    INDEX idx_user_id (user_id),
    INDEX idx_category (category),
    INDEX idx_date (date),
    INDEX idx_amount (amount),
    INDEX idx_created_at (created_at),
    INDEX idx_user_date (user_id, date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Notes table
CREATE TABLE IF NOT EXISTS notes (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    title VARCHAR(255) NOT NULL,
    content LONGTEXT NOT NULL,
    category VARCHAR(100) NULL,
    color_tag VARCHAR(7) DEFAULT '#fbbf24',
    is_pinned BOOLEAN DEFAULT FALSE,
    is_archived BOOLEAN DEFAULT FALSE,
    tags JSON NULL,
    attachments JSON NULL,
    collaborator_ids JSON NULL,
    permission_level ENUM('private', 'shared', 'public') DEFAULT 'private',
    word_count INT UNSIGNED DEFAULT 0,
    reading_time INT UNSIGNED DEFAULT 0,
    deleted_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    CONSTRAINT fk_notes_user_id FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE ON UPDATE CASCADE,
    INDEX idx_user_id (user_id),
    INDEX idx_is_pinned (is_pinned),
    INDEX idx_is_archived (is_archived),
    INDEX idx_category (category),
    INDEX idx_created_at (created_at),
    INDEX idx_updated_at (updated_at),
    FULLTEXT idx_fulltext_notes (title, content)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Moods table
CREATE TABLE IF NOT EXISTS moods (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    mood_level TINYINT UNSIGNED NOT NULL,
    mood_label VARCHAR(50) NULL,
    energy_level TINYINT UNSIGNED NULL,
    stress_level TINYINT UNSIGNED NULL,
    focus_level TINYINT UNSIGNED NULL,
    emotion_tags JSON NULL,
    notes TEXT NULL,
    activities JSON NULL,
    sleep_hours DECIMAL(3, 1) NULL,
    weather VARCHAR(50) NULL,
    location VARCHAR(100) NULL,
    recorded_date DATE NOT NULL,
    recorded_at TIMESTAMP NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    CONSTRAINT fk_moods_user_id FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT chk_mood_level CHECK (mood_level BETWEEN 1 AND 10),
    CONSTRAINT chk_energy_level CHECK (energy_level IS NULL OR energy_level BETWEEN 1 AND 10),
    CONSTRAINT chk_stress_level CHECK (stress_level IS NULL OR stress_level BETWEEN 1 AND 10),
    CONSTRAINT chk_focus_level CHECK (focus_level IS NULL OR focus_level BETWEEN 1 AND 10),
    UNIQUE KEY unique_user_date (user_id, recorded_date),
    INDEX idx_user_id (user_id),
    INDEX idx_recorded_date (recorded_date),
    INDEX idx_mood_level (mood_level),
    INDEX idx_created_at (created_at),
    INDEX idx_user_recorded_date (user_id, recorded_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Budgets table
CREATE TABLE IF NOT EXISTS budgets (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    category VARCHAR(100) NOT NULL,
    limit_amount DECIMAL(10, 2) NOT NULL,
    month_year VARCHAR(7) NOT NULL,
    spent_amount DECIMAL(10, 2) DEFAULT 0,
    alert_threshold TINYINT UNSIGNED DEFAULT 80,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    CONSTRAINT fk_budgets_user_id FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT chk_limit_amount CHECK (limit_amount > 0),
    CONSTRAINT chk_spent_amount CHECK (spent_amount >= 0),
    CONSTRAINT chk_alert_threshold CHECK (alert_threshold BETWEEN 0 AND 100),
    UNIQUE KEY unique_user_category_month (user_id, category, month_year),
    INDEX idx_user_id (user_id),
    INDEX idx_month_year (month_year),
    INDEX idx_user_month (user_id, month_year)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- 3. AUDIT & LOGGING TABLES
-- ============================================================================

-- Audit logs table (Immutable history)
CREATE TABLE IF NOT EXISTS audit_logs (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    action VARCHAR(255) NOT NULL,
    entity_type VARCHAR(100) NOT NULL,
    entity_id BIGINT UNSIGNED NULL,
    old_values JSON NULL,
    new_values JSON NULL,
    ip_address VARCHAR(45) NULL,
    user_agent TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    CONSTRAINT fk_audit_logs_user_id FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE ON UPDATE CASCADE,
    INDEX idx_user_id (user_id),
    INDEX idx_action (action),
    INDEX idx_created_at (created_at),
    INDEX idx_entity (entity_type, entity_id),
    INDEX idx_user_created (user_id, created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- 4. CACHE & JOBS TABLES (Laravel System)
-- ============================================================================

-- Cache table
CREATE TABLE IF NOT EXISTS cache (
    `key` VARCHAR(255) PRIMARY KEY,
    value LONGTEXT NOT NULL,
    expiration INT NOT NULL,
    
    INDEX idx_expiration (expiration)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Cache locks table
CREATE TABLE IF NOT EXISTS cache_locks (
    `key` VARCHAR(255) PRIMARY KEY,
    owner VARCHAR(100) NOT NULL,
    expiration INT NOT NULL,
    
    INDEX idx_expiration (expiration)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Jobs table
CREATE TABLE IF NOT EXISTS jobs (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    queue VARCHAR(255) NOT NULL,
    payload LONGTEXT NOT NULL,
    attempts TINYINT UNSIGNED NOT NULL DEFAULT 0,
    reserved_at INT UNSIGNED NULL,
    available_at INT UNSIGNED NOT NULL,
    created_at INT UNSIGNED NOT NULL,
    
    INDEX idx_queue (queue),
    INDEX idx_reserved_at (reserved_at),
    INDEX idx_available_at (available_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Job batches table
CREATE TABLE IF NOT EXISTS job_batches (
    id VARCHAR(255) PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    total_jobs INT NOT NULL,
    pending_jobs INT NOT NULL,
    failed_jobs INT NOT NULL,
    failed_job_ids LONGTEXT NOT NULL,
    options MEDIUMTEXT NULL,
    cancelled_at INT NULL,
    created_at INT NOT NULL,
    finished_at INT NULL,
    
    INDEX idx_name (name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Failed jobs table
CREATE TABLE IF NOT EXISTS failed_jobs (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    uuid VARCHAR(255) NOT NULL UNIQUE,
    connection TEXT NOT NULL,
    queue TEXT NOT NULL,
    payload LONGTEXT NOT NULL,
    exception LONGTEXT NOT NULL,
    failed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    INDEX idx_uuid (uuid)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Migrations table (Already created by Laravel, but including for reference)
CREATE TABLE IF NOT EXISTS migrations (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    migration VARCHAR(255) NOT NULL,
    batch INT NOT NULL,
    
    UNIQUE KEY unique_migration (migration)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- 5. VIEWS (Optional - for common queries)
-- ============================================================================

-- User dashboard summary view
CREATE OR REPLACE VIEW v_user_dashboard_summary AS
SELECT
    u.id as user_id,
    u.name,
    u.email,
    COUNT(DISTINCT t.id) as total_tasks,
    COUNT(DISTINCT CASE WHEN t.status = 'completed' THEN t.id END) as completed_tasks,
    COUNT(DISTINCT CASE WHEN t.status = 'in_progress' THEN t.id END) as in_progress_tasks,
    COUNT(DISTINCT CASE WHEN t.due_date < NOW() AND t.status != 'completed' THEN t.id END) as overdue_tasks,
    COUNT(DISTINCT e.id) as total_expenses,
    SUM(CASE WHEN e.deleted_at IS NULL THEN e.amount ELSE 0 END) as total_expense_amount,
    COUNT(DISTINCT n.id) as total_notes,
    COUNT(DISTINCT m.id) as mood_entries,
    u.last_login_at,
    u.created_at
FROM users u
LEFT JOIN tasks t ON u.id = t.user_id
LEFT JOIN expenses e ON u.id = e.user_id
LEFT JOIN notes n ON u.id = n.user_id
LEFT JOIN moods m ON u.id = m.user_id
GROUP BY u.id, u.name, u.email, u.last_login_at, u.created_at;

-- Monthly expense summary by category
CREATE OR REPLACE VIEW v_monthly_expenses_by_category AS
SELECT
    e.user_id,
    DATE_FORMAT(e.date, '%Y-%m') as month,
    e.category,
    COUNT(e.id) as transaction_count,
    SUM(e.amount) as total_amount,
    AVG(e.amount) as avg_amount,
    MIN(e.amount) as min_amount,
    MAX(e.amount) as max_amount
FROM expenses e
WHERE e.deleted_at IS NULL
GROUP BY e.user_id, DATE_FORMAT(e.date, '%Y-%m'), e.category;

-- ============================================================================
-- 6. SUMMARY STATISTICS
-- ============================================================================

-- After executing this script, you can verify with these queries:
--
-- SELECT 'Users' as table_name, COUNT(*) as row_count FROM users
-- UNION ALL
-- SELECT 'Tasks', COUNT(*) FROM tasks
-- UNION ALL
-- SELECT 'Expenses', COUNT(*) FROM expenses
-- UNION ALL
-- SELECT 'Notes', COUNT(*) FROM notes
-- UNION ALL
-- SELECT 'Moods', COUNT(*) FROM moods
-- UNION ALL
-- SELECT 'Budgets', COUNT(*) FROM budgets
-- UNION ALL
-- SELECT 'Audit Logs', COUNT(*) FROM audit_logs;
--
-- To check table structures:
-- DESCRIBE tasks;
-- DESCRIBE expenses;
-- DESCRIBE notes;
-- DESCRIBE moods;
-- DESCRIBE budgets;
-- DESCRIBE audit_logs;
--
-- To check indexes:
-- SHOW INDEX FROM tasks;
-- SHOW INDEX FROM expenses;
--
-- To verify foreign keys:
-- SELECT CONSTRAINT_NAME, TABLE_NAME, REFERENCED_TABLE_NAME
-- FROM INFORMATION_SCHEMA.REFERENTIAL_CONSTRAINTS
-- WHERE CONSTRAINT_SCHEMA = 'digital_life_manager';
--
-- ============================================================================
-- Database schema creation complete!
-- All tables, constraints, indexes, and views created successfully.
-- ============================================================================
