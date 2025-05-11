<?php
if (!session_id()) {
    session_start();
}
require_once 'CDistrictAwards.php';
$CDistrictAwards = CDistrictAwards::getInstance();

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html lang="en">

<head style="min-width: 1092px;">
    <title>Centennial District Award Data</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="css/districtawards.css">
    <link rel="stylesheet" href="../bootstrap-5.3.2/css/bootstrap.css">
    <link rel="icon" type="image/x-icon" href="./img/centennial.ico">
</head>

<?php
include_once('header.php');
$CDistrictAwards->GetYear(); //Set a year value to current year
?>

<img src="./img/DistrictAwards.png" width="50vw" alt="Eagle Rank" class="center"">

</html>