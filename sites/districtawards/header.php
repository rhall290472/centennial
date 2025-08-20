    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-QSBVK4B976"></script>
    <script>
        window.dataLayer = window.dataLayer || [];

        function gtag() {
            dataLayer.push(arguments);
        }
        gtag('js', new Date());
        gtag('config', 'G-QSBVK4B976');
        s
    </script>

    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="District Awards" />
    <meta name="author" content="Richard Hall" />
    <title>Centennial District Awards</title>
    <!-- Favicon-->
	    <link rel="icon" type="image/x-icon" href="https://shared.centennialdistrict.co/assets/centennial.ico" />
    <!-- Bootstrap icons-->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet" />
    <!-- Core theme CSS (includes Bootstrap)-->
    <!--<link href="https://centennialdistrict.co/css/styles.css" rel="stylesheet" /> -->
    <?php
    $is_localhost = isset($_SERVER['SERVER_NAME']) && in_array($_SERVER['SERVER_NAME'], ['localhost', '127.0.0.1']);
    if ($is_localhost) {
    ?>
        <link href="https://localhost/centennial/shared/assets/styles.css" rel="stylesheet" />
    <?php
    } else { ?>
        <link href="https://shared.centennialdistrict.co/assets/styles.css" rel="stylesheet" />
    <?php } ?>
