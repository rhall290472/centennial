<?php

/**
 * File: config.php
 * Description: Centralized configuration settings for Centennial District Advancement
 * Author: Richard Hall
 * License: Proprietary Software, Copyright 2024 Richard Hall
 */

defined('IN_APP') or define('IN_APP', true);
// Base path, only set once
defined('BASE_PATH') or define('BASE_PATH', dirname(__DIR__));

// Ensure upload directory exists
$uploadDir = BASE_PATH . '/Uploads/';
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
define('SHARED_URL', 'https://share.centennialdistrict.co');
define('SHARED_ASSETS_URL', SHARED_URL . '/assets');
define('SHARED_CLASS_URL', SHARED_URL . '/src/Classes');
define('SHARED_PATH', __DIR__ . '/../../../shared/'); 

// Site metadata
define('PAGE_TITLE', 'Centennial District Advancements');
define('PAGE_DESCRIPTION', 'Review Advancements for the Centennial District');

// Contact email
define('CONTACT_EMAIL', 'richard.hall@centennialdistrict.co');

// SMTP settings
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_USERNAME', 'rhall290472@gmail.com');
define('SMTP_PASSWORD', 'vicx cxho rywh ylok'); // Use .env in production

define('ALLOWED_FILE_EXTENSIONS', ['csv']);
define('MAX_FILE_SIZE', 4000000); // 4MB
define('UPLOAD_DIRECTORY', __DIR__ . '/Data/');

$pageHome = SITE_URL.'/centennial/sites/advancement/public/index.php';
$pageContact = SITE_URL . '/centennial/sites/advancement/src/Pages/contact.php';
// Navigation links
define('NAV_LINKS', [
    [
        'href' => $pageHome,
        'text' => 'Home',
        'active' => false,
        'rel' => 'nofollow',
        'aria-label' => 'Home'
    ],
    [
        'href' => $pageContact,
        'text' => 'Contact',
        'active' => false,
        'rel' => 'nofollow',
        'aria-label' => 'Contact'
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


// Template loader function
if (!function_exists('load_template')) {
    function load_template($file)
    {
        $path = BASE_PATH . $file;
        if (file_exists($path)) {
            require_once $path;
        } else {
            error_log("Template $file is missing.");
            if (defined('ENV') && ENV === 'development') {
                echo 'Template ' . $path . ' is missing.</br>';
                die('Template $file is missing.');
            } else
                die('An error occurred. Please try again later.');
        }
    }
}
// Class loader function
if (!function_exists('load_class')) {
    function load_class($file)
    {
        $path = $file;
        if (file_exists($path)) {
            require_once $path;
        } else {
            error_log("Class $file is missing.");
            if (defined('ENV') && ENV === 'development') {
                echo 'Template ' . $path . ' is missing.</br>';
                die('Class $file is missing.');
            } else
                die('An error occurred. Please try again later.');
        }
    }
}
