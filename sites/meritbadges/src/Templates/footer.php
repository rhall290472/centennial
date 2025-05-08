<?php

/**
 * File: Footer.php
 * Description: Footer and scroll-to-top button for Centennial District Merit Badges
 * Author: Richard Hall
 * License: Proprietary Software, Copyright 2024 Richard Hall
 */

defined('IN_APP') or die('Direct access not allowed.');

// Load configuration
//require_once 'config.php';
?>

<!-- Scroll-to-top button -->
<a href="#" id="scroll-top" class="scroll-top d-none d-lg-flex align-items-center justify-content-center"
  aria-label="Scroll to top" title="Scroll to top">
  <i class="bi bi-arrow-up-short"></i>
  <span class="visually-hidden">Scroll to top</span>
</a>

<!-- Footer -->
<foot class="fixed-bottom">
  <footer class="py-3 bg-dark d-print-none" role="contentinfo">
    <div class="container">
      <p class="m-0 text-center text-white">
        <?php echo htmlspecialchars(SITE_URL); ?> - Copyright &copy; <?php echo date('Y'); ?> <?php echo htmlspecialchars(PAGE_TITLE); ?>
      </p>
    </div>
  </footer>
</foot>