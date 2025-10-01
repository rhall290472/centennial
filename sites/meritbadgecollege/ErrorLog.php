<?php
	include_once('CMBColle
	$CMBCollege = CMBCollege::getInstance();
  
    !session_id()){
    ssion_start();
    
  req

		if(   !(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true)){
			$CMBCollege->GotoURL("index.php");
			exit;
		}
	}
?>

<!DOCTYPE html>
<html lang="en">


<head>
    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-CPC23NSK6F"></script>
    <script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());

    gtag('config', 'G-CPC23NSK6F');
    </script>

	<

	<link rel="stylesheet" href="./bootstrap-5.3.2/css/bootstrap.css">
	<link rel="stylesheet" href="css/centennial.css">
</head>

<body>
	<div class="header">
	</div>

	<?php //CHeader::DisplayPageHeader("Centennial District Update Unit Advancement Data", "", ""); ?>


	<body style="padding:10px">
	<div class="my_div">
		<div>
			<p>Below is a list of recorded errors found.
			</p>
		</div>
		<?php
            $errorlog = file_get_contents('https://centennialdistrict.co/MBCollege/php_errors.log');

            if($errorlog){
                echo $errorlog;
            }
        ?>
    </div>

    
	</body>

</html>