<?php
if (!session_id()) {
  session_start();
}
include_once('../CMeritBadges.php');

// This code stops anyone for seeing this page unless they have logged in and
// their account is enabled.
if (!(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true)) {
  header("HTTP/1.0 403 Forbidden");

  exit;
}
?>

<!DOCTYPE html>
<html lang='en'>

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

  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
  <title>Edit Counselor</title>
  <link rel="stylesheet" type="text/css" href="../../css/centennial.css" media="all">
  <div class="header">
    <a href="$default" class="logo">Merit Badge Counselors Edit</a>
    <div class="header-right">
      <a class="active" href="../index.php">Home</a>
      <a href="mailto:richard.hall@centennial.co?subject=Merit Badge website">Contact</a>
      <a href="https://www.centennialdistrict.co/advancement/about.html">About</a>
    </div>
  </div>

  <script>
    function CounselorForm() {
      location.replace("Counselorform.php");
      exit();
    }
  </script>
</head>

<body>


  <h1>Thank You</h1>
  <p>Here is the information you have submitted:</p>
  <ol>
    <li><em>Frist Name:</em> <?php echo $_POST["FristName"] ?></li>
    <li><em>Last Name:</em> <?php echo $_POST["LastName"] ?></li>
    <li><em>Email:</em> <?php echo $_POST["Email"] ?></li>

    <li><em>YPT:</em> <?php echo $_POST["YPT"] ?></li>
    <li><em>Zip Code:</em> <?php echo $_POST["Zip"] ?></li>

    <li><em>Home Phone:</em> <?php echo $_POST["element_5_1"] . $_POST["element_5_2"] . $_POST["element_5_3"] ?></li>
    <li><em>Mobile Phone:</em> <?php echo $_POST["element_6_1"] . $_POST["element_6_2"] . $_POST["element_6_3"] ?></li>

    <li><em>Last Date Verified:</em> <?php echo $_POST["ValidationDate"] ?></li>
    <li><em>District:</em> <?php echo $_POST["HomeDistrict"] ?></li>
    <li><em>Registration:</em> <?php echo $_POST["Registration"] ?></li>
    <li><em>Home Troop:</em> <?php echo $_POST["Unit1"] ?></li>
    <li><em>Sec Troop:</em> <?php echo $_POST["Unit2"] ?></li>

    <li><em>Active:</em> <?php echo isset($_POST["Active"]) ?></li>
    <li><em>Trained:</em> <?php echo isset($_POST["Trained"]) ?></li>
    <li><em>Do Not Publish:</em> <?php echo isset($_POST["DoNotPublish"]) ?></li>

    <li><em>Note:</em> <?php echo $_POST["Note"] ?></li>

  </ol>
  <?php
  $CMeritBadge = CMeritBadges::getInstance();

  if ($_POST['submit'] == 'Submit') {

    if ($_POST["Email"] == "/" || is_null($_POST["Email"]))
      $email = null;
    else
      $email = $_POST["Email"];

    $HomeDistrict = str_replace(".", "", $_POST["HomeDistrict"]);

    /*=============================================================================
        *
        * This function will 
        * 
        =========================================================================*/
    //public static function RemoveNewLine($str){
    $count = null;
    $str = $_POST["Note"];
    $str = str_replace("\n", "", $str, $Count);
    $str = str_replace("\r", "", $str, $Count);

    $HomeTroop = ($_POST["Unit1"] == "0000") ? "" : $_POST["Unit1"];
    if (strlen($HomeTroop) > 0 && strlen($HomeTroop) < 4) {
      while (strlen($HomeTroop) < 4)
        $HomeTroop = "0" . $HomeTroop;
    }
    $SecTroop = ($_POST["Unit2"] == "0000") ? "" : $_POST["Unit2"];
    if (strlen($SecTroop) > 0 && strlen($SecTroop) < 4) {
      while (strlen($SecTroop) < 4)
        $SecTroop = "0" . $SecTroop;
    }

    $sqlUpdate = sprintf(
      "UPDATE `counselors` SET 
        `LastName`='%s',`FirstName`='%s',`Email`='%s',
        `YPT`='%s',`Zip`='%s',
        `HomePhone`='%s', `MobilePhone`='%s',
        `ValidationDate`='%s', `HomeDistrict`='%s', `MemberID`='%s', `Unit1`='%s',`Unit2`='%s',
        `Active`='%s', `DoNotPublish`='%s',
        `Notes`='%s'
        WHERE `LastName`= '%s' AND `FirstName`= '%s'",
      $_POST["LastName"],
      $_POST["FristName"],
      $email,
      $_POST["YPT"],
      $_POST["Zip"],
      $_POST["element_5_1"] . $_POST["element_5_2"] . $_POST["element_5_3"],
      $_POST["element_6_1"] . $_POST["element_6_2"] . $_POST["element_6_3"],
      $_POST["ValidationDate"],
      $HomeDistrict,
      $_POST["Registration"],
      $HomeTroop,
      $SecTroop,
      isset($_POST["Active"]) ? "Yes" : "No",
      isset($_POST["DoNotPublish"]) ? "Yes" : "No",
      addslashes($str),
      $_POST["LastName"],
      $_POST["FristName"]
    );

    if ($result = $CMeritBadge->doQuery($sqlUpdate)) {
      $CMeritBadge->function_alert("Counselor Updated");
      $CMeritBadge->GotoURL("Counselorform.php");
      exit();
    } else {
      $msg = "FAILED to Update " . "</br>" . $sqlUpdate;
      echo $msg;
      $CMeritBadge->function_alert($msg);
    }
  } else if ($_POST['delete'] == 'Delete') {
    $sqlDelete = sprintf(
      "DELETE FROM `counselors` WHERE `LastName`='%s' AND `FirstName`='%s'",
      $_POST["LastName"],
      $_POST["FristName"]
    );

    if ($result = $CMeritBadge->doQuery($sqlDelete)) {
      $msg = "Counselor Deleted " . $sqlDelete;
      echo $msg;
      $CMeritBadge->function_alert("Counselor Deleted " . $sqlDelete);
      CMeritBadges::GotoURL("Counselorform.php");
      exit();
    } else {
      $msg = "FAILED to Delete " . "</br>" . $sqlDelete;
      echo $msg;
      $CMeritBadge->function_alert("Counselor Deleted " . $sqlDelete);
    }
  }
  ?>
</body>