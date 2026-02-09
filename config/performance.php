<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Production Performance Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains production-specific performance optimizations
    | for the Tablelists page and related components.
    |
    */

    'cache' => [
        /*
        |--------------------------------------------------------------------------
        | Cache Configuration
        |--------------------------------------------------------------------------
        */
        'driver' => env('CACHE_DRIVER', 'redis'),
        
        /*
        |--------------------------------------------------------------------------
        | Cache TTL Settings (in minutes)
        |--------------------------------------------------------------------------
        */
        'ttl' => [
            'units_data' => env('UNITS_CACHE_TTL', 60),        // 60 minutes
            'resident_data' => env('RESIDENT_CACHE_TTL', 30),  // 30 minutes
            'notes_data' => env('NOTES_CACHE_TTL', 15),         // 15 minutes
            'user_specific' => env('USER_CACHE_TTL', 45),      // 45 minutes
        ],
        
        /*
        |--------------------------------------------------------------------------
        | Cache Keys
        |--------------------------------------------------------------------------
        */
        'keys' => [
            'units' => 'units_with_dependence_v4',
            'user_prefix' => 'user_',
        ],
    ],

    'database' => [
        /*
        |--------------------------------------------------------------------------
        | Database Connection Pool
        |--------------------------------------------------------------------------
        */
        'connection_pool' => [
            'max_connections' => env('DB_MAX_CONNECTIONS', 100),
            'min_connections' => env('DB_MIN_CONNECTIONS', 10),
            'connection_timeout' => env('DB_CONNECTION_TIMEOUT', 5),
            'idle_timeout' => env('DB_IDLE_TIMEOUT', 60),
        ],
        
        /*
        |--------------------------------------------------------------------------
        | Query Optimization
        |--------------------------------------------------------------------------
        */
        'query_optimization' => [
            'limit_notes' => env('LIMIT_NOTES', 3),
            'use_transactions' => env('USE_TRANSACTIONS', true),
            'batch_size' => env('BATCH_SIZE', 100),
        ],
    ],

    'livewire' => [
        /*
        |--------------------------------------------------------------------------
        | Livewire Performance Settings
        |--------------------------------------------------------------------------
        */
        'lazy_loading' => env('LIVEWIRE_LAZY_LOADING', true),
        'debounce_delay' => env('LIVEWIRE_DEBOUNCE_DELAY', 300),
        'render_on_request' => env('LIVEWIRE_RENDER_ON_REQUEST', true),
        
        /*
        |--------------------------------------------------------------------------
        | Modal Optimization
        |--------------------------------------------------------------------------
        */
        'modal' => [
            'lazy_load' => env('MODAL_LAZY_LOAD', true),
            'preload_timeout' => env('MODAL_PRELOAD_TIMEOUT', 100),
            'cache_modal_data' => env('CACHE_MODAL_DATA', true),
        ],
    ],

    'frontend' => [
        /*
        |--------------------------------------------------------------------------
        | Frontend Optimization
        |--------------------------------------------------------------------------
        */
        'client_side_cache' => env('CLIENT_SIDE_CACHE', true),
        'client_cache_ttl' => env('CLIENT_CACHE_TTL', 300), // 5 minutes
        
        /*
        |--------------------------------------------------------------------------
        | JavaScript Optimization
        |--------------------------------------------------------------------------
        */
        'javascript' => [
            'debounce_inline_edit' => env('DEBOUNCE_INLINE_EDIT', 500),
            'debounce_phone_format' => env('DEBOUNCE_PHONE_FORMAT', 150),
            'lazy_load_modals' => env('LAZY_LOAD_MODALS', true),
        ],
        
        /*
        |--------------------------------------------------------------------------
        | Asset Optimization
        |--------------------------------------------------------------------------
        */
        'assets' => [
            'minify_js' => env('MINIFY_JS', true),
            'minify_css' => env('MINIFY_CSS', true),
            'combine_files' => env('COMBINE_FILES', true),
        ],
    ],

    'monitoring' => [
        /*
        |--------------------------------------------------------------------------
        | Performance Monitoring
        |--------------------------------------------------------------------------
        */
        'enabled' => env('PERFORMANCE_MONITORING', true),
        'log_slow_queries' => env('LOG_SLOW_QUERIES', true),
        'slow_query_threshold' => env('SLOW_QUERY_THRESHOLD', 100), // ms
        
        /*
        |--------------------------------------------------------------------------
        | Alert Thresholds
        |--------------------------------------------------------------------------
        */
        'thresholds' => [
            'response_time' => env('RESPONSE_TIME_THRESHOLD', 1000),    // ms
            'memory_usage' => env('MEMORY_USAGE_THRESHOLD', 128),        // MB
            'query_count' => env('QUERY_COUNT_THRESHOLD', 50),           // count
        ],
    ],

    'security' => [
        /*
        |--------------------------------------------------------------------------
        | Rate Limiting
        |--------------------------------------------------------------------------
        */
        'rate_limiting' => [
            'enabled' => env('RATE_LIMITING_ENABLED', true),
            'requests_per_minute' => env('RATE_LIMIT_REQUESTS', 60),
            'burst_limit' => env('RATE_LIMIT_BURST', 10),
        ],
        
        /*
        |--------------------------------------------------------------------------
        | Input Validation
        |--------------------------------------------------------------------------
        */
        'validation' => [
            'sanitize_input' => env('SANITIZE_INPUT', true),
            'validate_phone' => env('VALIDATE_PHONE', true),
            'max_input_length' => env('MAX_INPUT_LENGTH', 255),
        ],
    ],

    'optimization' => [
        /*
        |--------------------------------------------------------------------------
        | Auto-optimization Settings
        |--------------------------------------------------------------------------
        */
        'auto_optimize' => env('AUTO_OPTIMIZE', true),
        
        /*
        |--------------------------------------------------------------------------
        | Compression
        |--------------------------------------------------------------------------
        */
        'compression' => [
            'enabled' => env('ENABLE_COMPRESSION', true),
            'level' => env('COMPRESSION_LEVEL', 6),
        ],
        
        /*
        |--------------------------------------------------------------------------
        | HTTP Caching
        |--------------------------------------------------------------------------
        */
        'http_cache' => [
            'enabled' => env('HTTP_CACHE_ENABLED', true),
            'max_age' => env('HTTP_CACHE_MAX_AGE', 3600), // 1 hour
        ],
    ],
];
