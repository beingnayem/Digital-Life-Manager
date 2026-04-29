<?php

use App\Models\Budget;
use App\Models\Expense;
use App\Models\Note;
use App\Models\Task;

return [

    'driver' => env('SCOUT_DRIVER', 'meilisearch'),

    'prefix' => env('SCOUT_PREFIX', ''),

    'queue' => env('SCOUT_QUEUE', false),

    'after_commit' => false,

    'chunk' => [
        'searchable' => 500,
        'unsearchable' => 500,
    ],

    'soft_delete' => false,

    'identify' => env('SCOUT_IDENTIFY', false),

    'meilisearch' => [
        'host' => env('MEILISEARCH_HOST', 'http://127.0.0.1:7700'),
        'key' => env('MEILISEARCH_KEY', null),
        'index-settings' => [
            Task::class => [
                'filterableAttributes' => ['user_id', 'status', 'priority', 'category', 'due_date'],
                'sortableAttributes' => ['due_date', 'updated_at', 'created_at'],
            ],
            Note::class => [
                'filterableAttributes' => ['user_id', 'category', 'is_pinned', 'is_archived'],
                'sortableAttributes' => ['updated_at', 'created_at'],
            ],
            Expense::class => [
                'filterableAttributes' => ['user_id', 'category', 'status', 'payment_method', 'date'],
                'sortableAttributes' => ['date', 'amount', 'created_at'],
            ],
            Budget::class => [
                'filterableAttributes' => ['user_id', 'category', 'is_active', 'month_year'],
                'sortableAttributes' => ['month_year', 'limit_amount', 'spent_amount'],
            ],
        ],
    ],

];
