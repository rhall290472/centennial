<?php
if (!session_id()) {
  session_start();
}

require_once 'CEagle.php';
$cEagle = CEagle::getInstance();

// This code stops anyone for seeing this page unless they have logged in and
// their account is enabled.
if (!(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true)) {
  header("HTTP/1.0 403 Forbidden");
  exit;
}

?>

<html>

<head>
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

  <title>Centennial District Import Eagle Scouts</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="../bootstrap-5.3.2/css/bootstrap.css">
  <link rel="stylesheet" href="css/eagle.css">
</head>

<body style="padding:10px; background-color:#d8eec3">
  <div class="my_div">
    <?php include('header.php'); ?>

    <form action="FileUpload.php" method="post" enctype="multipart/form-data">
      <!-- Upload a file -->
      </br>

      <input class='btn btn-primary btn-sm' style="width:500px; height:35px ! important; " type="file" accept=".csv" size="255" name="the_file" id=fileToUpload"></br></br>

      <!--
		<input type="file" name="the_file" id="fileToUpload" style="display: outside;" />
		
		<input class='btn btn-primary btn-sm' type="button" value="Browse..." onclick="document.getElementById('fileToUpload').click();" />
		-->

      <p>This function will allow you to insert or update the database from the year end Council report.</p>
      <input class='btn btn-primary btn-sm' style="width:200;  height:30px; margin-bottom:15px ! important" type="submit" name="submit" value="ImportEagles"></br>
      <p>This function will allow you import Life scouts. Data will come from the Youth_Member_Age_Report.csv report</p>
      <input class='btn btn-primary btn-sm' style="width:200;  height:30px; margin-bottom:15px ! important" type="submit" name="submit" value="LifeScouts"></br>
      <p>This function will allow you update Coaches YPT.</p>
      <input class='btn btn-primary btn-sm' style="width:200;  height:30px; margin-bottom:15px ! important" type="submit" name="submit" value="YPT"></br>
      <p>This function will allow you update the Unit Leader information for active "in-work" scouts.. This data will be pulled from the YPT report</p>
      <input class='btn btn-primary btn-sm' style="width:200;  height:30px; margin-bottom:15px ! important" type="submit" name="submit" value="UnitLeaders"></br>
    </form>

    <?php
    //    //Allow selection by Unit
    //    $qryScoutss = "SELECT * FROM scouts WHERE `MemberId` ='0' ORDER BY `Scoutid` ASC";
    //
    //
    //    if (!$Scouts= $cEagle->doQuery($qryScoutss)) {
    //        $msg = "Error: doQuery()";
    //        $cEagle->function_alert($msg);
    //    }
    //
    //    While ($rowScout = $Scouts->fetch_assoc()){
    //        $MemberID = $rowScout['Scoutid'];
    //        $qtyUpdate = "UPDATE `scouts` SET  `MemberId`='$MemberID' WHERE `Scoutid`= '$MemberID'";
    //        if (!$cEagle->doQuery($qtyUpdate)) {
    //            $msg = "Error: doQuery()";
    //            $cEagle->function_alert($msg);
    //            exit;
    //        }
    //    }
    //
    ?>
  </div>
</body>

</html>