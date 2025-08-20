	    <!-- Google tag (gtag.js) -->
	    <script async src="https://www.googletagmanager.com/gtag/js?id=G-6PCWFTPZDZ"></script>
	    <script>
	    	window.dataLayer = window.dataLayer || [];

	    	function gtag() {
	    		dataLayer.push(arguments);
	    	}
	    	gtag('js', new Date());
	    	gtag('config', 'G-6PCWFTPZDZ');
	    </script>

	    <meta charset="utf-8" />
	    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
	    <meta name="description" content="Centennial District Merit Badge data." />
	    <meta name="author" content="Richard Hall" />
	    <title>Centennial District Merit Badges</title>
	    <!-- Favicon-->
	    <link rel="icon" type="image/x-icon" href="https://shared.centennialdistrict.co/assets/centennial.ico" />
	    <!-- Bootstrap icons-->
	    <!-- Vendor CSS Files -->
	    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/css/bootstrap.min.css">
	    <!-- <link href="assets/vendor/bootstrap/css/bootstrap.css" rel="stylesheet" /> -->
	    <!-- <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet" /> -->
	    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
	    <!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet" /> -->
	    <!-- Core theme CSS (includes Bootstrap)-->
	    <?php
			$is_localhost = isset($_SERVER['SERVER_NAME']) && in_array($_SERVER['SERVER_NAME'], ['localhost', '127.0.0.1']);
			if ($is_localhost) {
			?>
	    	<link href="https://localhost/centennial/shared/assets/styles.css" rel="stylesheet" />
	    <?php
			} else { ?>
	    	<link href="https://shared.centennialdistrict.co/assets/styles.css" rel="stylesheet" />
	    <?php } ?>