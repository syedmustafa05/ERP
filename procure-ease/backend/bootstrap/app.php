<?php

/**
 * ProcureEase Application Bootstrap
 * Laravel-style application initialization
 */

// Database configuration
$config = [
    'database' => [
        'default' => 'sqlite',
        'connections' => [
            'sqlite' => [
                'driver' => 'sqlite',
                'database' => __DIR__ . '/../storage/database.sqlite',
                'prefix' => '',
                'foreign_key_constraints' => true,
            ]
        ]
    ],
    'app' => [
        'name' => 'ProcureEase',
        'env' => 'production',
        'debug' => false,
        'key' => 'base64:' . base64_encode('procure-ease-secret-key'),
    ]
];

// Database connection
try {
    $dbPath = $config['database']['connections']['sqlite']['database'];
    
    // Create database file if it doesn't exist
    if (!file_exists($dbPath)) {
        $dir = dirname($dbPath);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        touch($dbPath);
    }
    
    $pdo = new PDO('sqlite:' . $dbPath);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    
} catch (Exception $e) {
    throw new Exception('Database connection failed: ' . $e->getMessage());
}

// Simple autoloader for our classes
spl_autoload_register(function ($class) {
    $prefixes = [
        'App\\Models\\' => __DIR__ . '/../app/Models/',
        'App\\Http\\Controllers\\' => __DIR__ . '/../app/Http/Controllers/',
        'App\\Database\\Migrations\\' => __DIR__ . '/../app/Database/Migrations/',
        'App\\Database\\Seeders\\' => __DIR__ . '/../app/Database/Seeders/',
    ];
    
    foreach ($prefixes as $prefix => $baseDir) {
        $len = strlen($prefix);
        if (strncmp($prefix, $class, $len) !== 0) {
            continue;
        }
        
        $relativeClass = substr($class, $len);
        $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';
        
        if (file_exists($file)) {
            require $file;
            return;
        }
    }
});

// Make PDO globally available
$GLOBALS['pdo'] = $pdo;
$GLOBALS['config'] = $config;

return $pdo;