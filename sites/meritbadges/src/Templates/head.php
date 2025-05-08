<?php
defined('IN_APP') or die('Direct access not allowed.');
?>
<!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-ESR495W3GB"></script>
<script>
  window.dataLayer = window.dataLayer || [];

  function gtag() {
    dataLayer.push(arguments);
  }
  gtag('js', new Date());
  gtag('config', 'G-ESR495W3GB');
</script>

<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
<meta name="author" content="Richard Hall" />
<title><?php echo PAGE_TITLE; ?></title>
<meta name="description" content="<?php echo PAGE_DESCRIPTION; ?>">
<!-- Favicon-->
<link rel="icon" type="image/x-icon" href="https://centennialdistrict.co/assets/centennial.ico" />
<!-- Bootstrap icons-->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<!-- Core theme CSS (includes Bootstrap)-->
<link href=<?php echo SHARED_ASSETS_URL ."/styles.css"?> rel="stylesheet" />

<link rel="canonical" href="https://meritbadges.centennialdistrict.co/index.php">
<meta property="og:title" content="Open Graph Meta Tags" />
<meta property="og:description" content="Learn how to customize the preview that is displayed when people share your website on social media." />
<meta property="og:image" content="https://www.validbot.com/img/info/open-graph.jpg" />
<meta property="og:image:width" content="1200" />
<meta property="og:image:height" content="630" />
<meta property="og:image:type" content="image/jpeg" />
<meta property="og:image" content="https://www.validbot.com/img/info/open-graph-alt.jpg" />
<meta property="og:image:width" content="1024" />
<meta property="og:image:height" content="1024" />
<meta property="og:image:type" content="image/jpeg" />
<meta property="og:url" content="https://meritbadges.centennialdistrict.co/index.php" />
<meta property="og:type" content="website" />

<script defer>
  document.addEventListener('DOMContentLoaded', () => {
    const scrollTop = document.getElementById('scroll-top');
    window.addEventListener('scroll', () => {
      scrollTop.classList.toggle('d-flex', window.scrollY > 200);
      scrollTop.classList.toggle('d-none', window.scrollY <= 200);
    });
    scrollTop.addEventListener('click', (e) => {
      e.preventDefault();
      window.scrollTo({
        top: 0,
        behavior: 'smooth'
      });
    });
  });
</script>