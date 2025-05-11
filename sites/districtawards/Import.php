<?php
if (!session_id()) {
    session_start();
}

require_once 'CDistrictAwards.php';
$cDistrictAwards = cDistrictAwards::getInstance();

// This code stops anyone for seeing this page unless they have logged in and
// their account is enabled.
if (!(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true)) {
    $cDistrictAwards->GotoURL("index.php");
    exit;
}

?>

<html>

<head>
    <?php include("header.php"); ?>
    <meta name="description" content="Import.php">

</head>

<body style="padding:10px; background-color:#d8eec3">
    <div class="my_div">

        <form action="FileUpload.php" method="post" enctype="multipart/form-data">
            <!-- Upload a file -->
            </br>

            <input class='RoundButton' style="width:500px; height:35px ! important; " type="file" accept=".csv" size="255" name="the_file" id=fileToUpload"></br></br>

            <!--
		<input type="file" name="the_file" id="fileToUpload" style="display: outside;" />
		
		<input class='RoundButton' type="button" value="Browse..." onclick="document.getElementById('fileToUpload').click();" />
		-->

            <p>This function will allow you to insert or update the Member ID from the YPT report (YPT_Centennial_02.csv).</p>
            <input class='RoundButton' style="width:200;  height:30px; margin-bottom:15px ! important" type="submit" name="submit" value="ImportIDs"></br>
        </form>
    </div>
</body>

</html>