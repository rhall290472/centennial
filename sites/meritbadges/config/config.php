<?php
/**
 * File: config.php
 * Description: Centralized configuration settings for Centennial District Merit Badges
 * Author: Richard Hall
 * License: Proprietary Software, Copyright 2024 Richard Hall
 */

defined('IN_APP') or define('IN_APP', true);
// Base path
define('BASE_PATH', dirname(__DIR__));

// Ensure upload directory exists
$uploadDir = BASE_PATH . '/Data/';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

// Create log directory if it doesn't exist
$logDir = BASE_PATH . '/../../shared/logs';
if (!is_dir($logDir)) {
    mkdir($logDir, 0755, true);
}

// Environment configuration  // development
define('ENV', 'development'); // Set to 'production' on live server
// Enable error reporting in development only

if (defined('ENV') && ENV === 'development') {
  ini_set('display_errors', 1);
  ini_set('log_errors', 1);
  ini_set('error_log', BASE_PATH . '/../../shared/logs');
  error_reporting(E_ALL);
} else {
  ini_set('display_errors', 0);
  ini_set('log_errors', 1);
  ini_set('error_log', 'https://shared.centennialdistrict.co/logs/error.log');
}


// Dynamically set SITE_URL based on environment
$is_localhost = isset($_SERVER['SERVER_NAME']) && in_array($_SERVER['SERVER_NAME'], ['localhost', '127.0.0.1']);
$protocol = 'https'; // Simplified since it's always HTTPS in the original code
$host = $is_localhost ? ($_SERVER['SERVER_NAME'] ?? 'localhost') : 'centennialdistrict.co';
$port = ($is_localhost && isset($_SERVER['SERVER_PORT']) && !in_array($_SERVER['SERVER_PORT'], ['80', '443'])) ? ':' . $_SERVER['SERVER_PORT'] : '';
define('SITE_URL', $protocol . '://' . $host . $port);

// Assets URL
define('SHARED_ASSETS_URL', SITE_URL . '/centennial/shared/assets');

// Site metadata
define('PAGE_TITLE', 'Centennial District Merit Badges');
define('PAGE_DESCRIPTION', 'Review Merit Badge Counselors for the Centennial District');

// Contact email
define('CONTACT_EMAIL', 'richard.hall@centennialdistrict.co');

// SMTP settings
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_USERNAME', 'rhall290472@gmail.com');
define('SMTP_PASSWORD', 'vicx cxho rywh ylok'); // Use .env in production

// Navigation links
define('NAV_LINKS', [
    [
        'href' => 'index.php',
        'text' => 'Home',
        'active' => false,
        'rel' => 'nofollow',
        'aria-label' => 'Home'
    ],
    [
        'href' => 'contact.php',
        'text' => 'Contact',
        'active' => false,
        'rel' => 'nofollow',
        'aria-label' => 'Home'
    ],
]);

if ($is_localhost) {
    define('DB_HOST', 'localhost');
    define('DB_USER', 'mbcuser');
    define('DB_PASS', 'ZCSCA?yrW7}L');
    define('DB_NAME', 'meritbadges');
} else {
    define('DB_HOST', 'rhall29047217205.ipagemysql.com');
    define('DB_USER', 'mbcuser');
    define('DB_PASS', 'ZCSCA?yrW7}L');
    define('DB_NAME', 'meritbadges');
}

// Security headers
//header('X-Content-Type-Options: nosniff');
//header('X-Frame-Options: DENY');
//header('X-XSS-Protection: 1; mode=block');

// File upload limits
ini_set('upload_max_filesize', '4M');
ini_set('post_max_size', '4M');

