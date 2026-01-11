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
$uploadDir = BASE_PATH . '/Data/';
if (!is_dir($uploadDir)) {
  mkdir($uploadDir, 0755, true);
}
define('UPLOAD_DIRECTORY', $uploadDir);

// Create log directory if it doesn't exist
$logDir = BASE_PATH . '/../../shared/logs';
if (!is_dir($logDir)) {
  mkdir($logDir, 0755, true);
}



// Dynamically set SITE_URL based on environment
$is_localhost = isset($_SERVER['SERVER_NAME']) && in_array($_SERVER['SERVER_NAME'], ['localhost', '127.0.0.1']);
$protocol = 'https'; // Simplified since it's always HTTPS in the original code
$host = $is_localhost ? ($_SERVER['SERVER_NAME'] ?? 'localhost') : 'mbcollege.centennialdistrict.co';
$port = ($is_localhost && isset($_SERVER['SERVER_PORT']) && !in_array($_SERVER['SERVER_PORT'], ['80', '443'])) ? ':' . $_SERVER['SERVER_PORT'] : '';
define('SITE_URL', $protocol . '://' . $host . $port);

// Assets URL
// https: //shared.centennialdistrict.co/assets/styles.css
define('SHARED_ASSETS_URL', SITE_URL . '/centennial/shared/assets');
define('SHARED_CLASS_URL', SITE_URL . '/centennial/shared/src/Classes');

define('SHARED_PATH', dirname(__DIR__, 3)); // backup to the shared directory
define('SRC_PATH', SHARED_PATH . '/shared/src');
define('SHARED_CLASS_PATH', SRC_PATH . '/Classes');

// Site metadata
define('PAGE_TITLE', 'Centennial District Merit Badge College');
define('PAGE_DESCRIPTION', 'Review Merit Badge College for the Centennial District');

// Contact email
define('CONTACT_EMAIL', 'richard.hall@centennialdistrict.co');

// SMTP settings
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_USERNAME', 'rhall290472@gmail.com');
define('SMTP_PASSWORD', 'vicx cxho rywh ylok'); // Use .env in production

define('ALLOWED_FILE_EXTENSIONS', ['csv']);
define('MAX_FILE_SIZE', 4000000); // 4MB

$pageHome = SITE_URL . '/centennial/sites/meritbadgecollege/public/index.php';
$pageContact = SITE_URL . '/centennial/sites/meritbadgecollege/src/Pages/contact.php';
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

// Environment configuration  // development
define('ENV', 'development'); // Set to 'production' on live server
// Enable error reporting in development only

if (defined('ENV') && ENV === 'development') {
  ini_set('display_errors', 1);
  ini_set('log_errors', 1);
  ini_set('error_log', SHARED_PATH . '/logs/php_errors.log');
  error_reporting(E_ALL);
} else {
  ini_set('display_errors', 0);
  ini_set('log_errors', 1);
  ini_set('error_log', SHARED_PATH . '/logs/php_errors.log');
}


if ($is_localhost) {
  define('DB_HOST', 'localhost');
  define('DB_USER', 'root');
  define('DB_PASS', '');
  define('DB_NAME', 'meritbadges');
} else {
  define('DB_HOST', 'rhall29047217205.ipagemysql.com');
  define('DB_USER', 'mbcuser');
  define('DB_PASS', 'ZCSCA?yrW7}L');
  define('DB_NAME', 'meritbadges');
}

// File upload limits
ini_set('upload_max_filesize', '4M');
ini_set('post_max_size', '4M');


// Template loader function
if (!function_exists('load_template')) {
  function load_template($file, $vars = [])
  {
    extract($vars);
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
        die('Template $file is missing.');
      } else
        die('An error occurred. Please try again later.');
    }
  }
}
