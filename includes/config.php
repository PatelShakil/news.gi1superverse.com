<?php
/**
 * Configuration file for News Site
 */

// Environment Detection
// Automatically detect if running on localhost or production
$isLocal = (
    $_SERVER['SERVER_NAME'] === 'localhost' ||
    $_SERVER['SERVER_NAME'] === '127.0.0.1' ||
    strpos($_SERVER['SERVER_NAME'], 'localhost') !== false
);

// Define environment constant
define('IS_LOCAL_ENV', $isLocal);

// API Configuration - Auto-switch based on environment
if (IS_LOCAL_ENV) {
    // Local Development Configuration
    define('API_BASE_URL', 'https://api.gi1superverse.com/api');
    define('STORAGE_URL', 'https://api.gi1superverse.com/storage/');
    define('SITE_URL', 'http://localhost/news-site');
    define('BASE_PATH', '/news-site'); // Base path for local XAMPP
} else {
    // Production Configuration
    define('API_BASE_URL', 'https://api.gi1superverse.com/api');
    define('STORAGE_URL', 'https://api.gi1superverse.com/storage/');
    define('SITE_URL', 'https://news.gi1superverse.com');
    define('BASE_PATH', ''); // No base path in production
}

define('API_TIMEOUT', 30);

// Site Configuration
define('SITE_NAME', 'Gi1 News');
define('SITE_DESCRIPTION', 'Latest news and updates from Gi1SuperVerse');
define('SITE_KEYWORDS', 'news, gi1superverse, updates, technology, business');

// Social Media
define('FACEBOOK_URL', 'https://facebook.com/gi1superverse');
define('TWITTER_URL', 'https://twitter.com/gi1superverse');
define('LINKEDIN_URL', 'https://linkedin.com/company/gi1superverse');

// Pagination
define('NEWS_PER_PAGE', 12);
define('RELATED_NEWS_COUNT', 5);

// Cache Configuration
define('CACHE_ENABLED', true);
define('CACHE_DURATION', 300); // 5 minutes in seconds

// Error Reporting - Auto-adjust based on environment
if (IS_LOCAL_ENV) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// Timezone
date_default_timezone_set('Asia/Kolkata');

// Session Configuration
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Helper function to get full URL with base path
 */
function getUrl($path = '')
{
    $path = ltrim($path, '/');
    return SITE_URL . ($path ? '/' . $path : '');
}

/**
 * Helper function to get asset URL
 */
function asset($path)
{
    $path = ltrim($path, '/');
    return SITE_URL . '/' . $path;
}
