<?php

/**
 * File: logoff.php
 * Description: Log out user from Centennial District Merit Badges
 * Author: Richard Hall
 * License: Proprietary Software, Copyright 2024 Richard Hall
 */

//defined('IN_APP') or die('Direct access not allowed.');

require_once 'config.php';

if (session_status() === PHP_SESSION_NONE) {
  session_start([
    'cookie_httponly' => true,
    'use_strict_mode' => true,
    'cookie_secure' => isset($_SERVER['HTTPS'])
  ]);
}

// Validate CSRF token
if (!isset($_GET['csrf']) || $_GET['csrf'] !== $_SESSION['csrf_token']) {
  error_log('Invalid CSRF token in logoff.php');
  header('Location: ' . SITE_URL . '/MeritBadges/index.php?error=' . urlencode('Invalid request.'));
  exit;
}

// Log out
error_log('User ' . $_SESSION['username'] . ' logged out');
session_unset();
session_destroy();

header('Location: ' . SITE_URL . '/MeritBadges/index.php');
exit;
