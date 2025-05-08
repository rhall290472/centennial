<?php

/**
 * File: navbar.php
 * Description: Responsive navigation bar for Centennial District Merit Badges
 * Author: Richard Hall
 * License: Proprietary Software, Copyright 2024 Richard Hall
 */

defined('IN_APP') or die('Direct access not allowed.');

// Load configuration
//require_once 'config.php';

// Start session
if (session_status() === PHP_SESSION_NONE) {
  session_start([
    'cookie_httponly' => true,
    'use_strict_mode' => true,
    'cookie_secure' => isset($_SERVER['HTTPS'])
  ]);
}

// Generate CSRF token for Log off
if (empty($_SESSION['csrf_token'])) {
  $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Navigation links
$nav_links = NAV_LINKS; // Defined in config.php
$current_page = basename($_SERVER['PHP_SELF']);

// Set active state
foreach ($nav_links as &$link) {
  $link['active'] = (basename($link['href']) === $current_page);
}

// Add Log on/Log off link
if (!empty($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
  $nav_links[] = [
    'href' => 'logoff.php?csrf=' . urlencode($_SESSION['csrf_token']),
    'text' => '<i class="bi bi-box-arrow-right"></i> Log off',
    'active' => false,
    'rel' => 'nofollow',
    'aria-label' => 'Log off from your account'
  ];
} else {
  $nav_links[] = [
    'href' => 'logon.php',
    'text' => '<i class="bi bi-person"></i> Log on',
    'active' => ($current_page === 'logon.php'),
    'rel' => 'nofollow',
    'aria-label' => 'Log in to your account'
  ];
}

// Create a new array with updated active states
$updated_nav_links = array_map(function ($link) use ($current_page) {
  $link['active'] = (basename($link['href']) === $current_page);
  return $link;
}, $nav_links);



// Helper function to render nav item
function render_nav_item($link)
{
  $active_class = $link['active'] ? ' active' : '';
  $aria_current = $link['active'] ? ' aria-current="page"' : '';
  $rel_attr = !empty($link['rel']) ? ' rel="' . $link['rel'] . '"' : '';
  $aria_label = !empty($link['aria-label']) ? ' aria-label="' . $link['aria-label'] . '"' : '';
  return '<li class="nav-item"><a class="nav-link' . $active_class . '" href="' . $link['href'] . '"' .
    $aria_current . $rel_attr . $aria_label . '>' . $link['text'] . '</a></li>';
}
?>







<!-- Responsive navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark" role="navigation" aria-label="Main navigation">
  <div class="container px-4 px-lg-5">
    <a class="navbar-brand" href="<?php echo SITE_URL; ?>/"><?php echo PAGE_TITLE; ?></a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent"
      aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarSupportedContent">
      <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
        <?php foreach ($updated_nav_links as $link): ?>
          <?php echo render_nav_item($link); ?>
        <?php endforeach; ?>
      </ul>
    </div>
  </div>
</nav>