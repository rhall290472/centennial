<?php
if (!session_id()) {
  session_start();
}

require_once 'CEagle.php';
$cEagle = CEagle::getInstance();

// This code stops anyone for seeing this page unless they have logged in and
// they account is enabled.
if (!(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true)) {
  header("HTTP/1.0 403 Forbidden");
  exit;
}


?>
<!DOCTYPE html>
<html lang="en">

<head>
  <?php include('head.php'); ?>
  <style>
    .wrapper {
      width: 360px;
      padding: 20px;
    }
  </style>
</head>

<body>
  <?php include('header.php'); ?>
  <?php include('navmenu.php'); ?>


  </br></br>
  Coaches
  <ul>
    <li>
      <div class="Mytooltip">
        <span class="Mytooltiptext">Report Active Coaches</span>
        <a href='./ReportCoachesActive.php'>Active Coaches</a>
    </li>

    <li>
      <div class="Mytooltip">
        <span class="Mytooltiptext">Report Inactive Coaches</span>
        <a href='./ReportCoachesInactive.php'>Inactive Coaches</a>
    </li>

    <li>
      <div class="Mytooltip">
        <span class="Mytooltiptext">Report of Coaches YPT dates</span>
        <a href='./ReportCoachesActiveYPT.php'>YPT Report</a>
    </li>

    <li>
      <div class="Mytooltip">
        <span class="Mytooltiptext">Report of Coaches workload</span>
        <a href='./ReportCoachesLoad.php'>Workload Report</a>
    </li>

    <li>
      <div class="Mytooltip">
        <span class="Mytooltiptext">Report of Coaches workload History</span>
        <a href='./ReportCoachesHistory.php'>Workload History Report</a>
    </li>
  </ul>
  Scouts:
  <ul>
    <li>
      <div class="Mytooltip">
        <span class="Mytooltiptext">Report all Eagles since 2017, by year then unit</span>
        <a href='./ReportEagles.php'>Eagles by Unit</a>
    </li>

    <li>
      <div class="Mytooltip">
        <span class="Mytooltiptext">Report all Eagles current year</span>
        <a href='./ReportEagleYear.php'>Eagles by Year</a>
    </li>
    <!--
        <li>
            <div class="Mytooltip">
                <span class="Mytooltiptext">Report all Eagles previous year</span>
                <a href='./ReportEaglePreviousYear.php'>Eagles Previous Year</a>
        </li>
-->
    <li>
      <div class="Mytooltip">
        <span class="Mytooltiptext">Report of Aged out Scouts</span>
        <a href='./ReportAgedOut.php'>Aged Out</a>
    </li>

    <li>
      <div class="Mytooltip">
        <span class="Mytooltiptext">Report Scouts that have not attend preview</span>
        <a href='./ReportPreview.php'>Did Not Attended Preview</a>
    </li>

    <li>
      <div class="Mytooltip">
        <span class="Mytooltiptext">Report Scouts do not have proposal approval</span>
        <a href='./ReportProject.php'>Lacking Proposal Approval</a>
    </li>

    <li>
      <div class="Mytooltip">
        <span class="Mytooltiptext">Report Scouts with approved proposal</span>
        <a href='./ReportApprovedProject.php'>Approved Proposal</a>
    </li>

    <li>
      <div class="Mytooltip">
        <span class="Mytooltiptext">Report pending EBOR's</span>
        <a href='./ReportPendingEBOR.php'>Pending EBOR's</a>
    </li>

    <li>
      <div class="Mytooltip">
        <span class="Mytooltiptext">Report Age Out dates</span>
        <a href='./ReportAgeOut.php'>Age Out</a>
    </li>

    <li>
      <div class="Mytooltip">
        <span class="Mytooltiptext">Report of all scouts in database</span>
        <a href='./ReportAllScouts.php'>All Scouts</a>
    </li>

    <li>
      <div class="Mytooltip">
        <span class="Mytooltiptext">Report of all Active Life scouts</span>
        <a href='./ReportAllLifeScouts.php'>All Active Life Scouts</a>
    </li>

  </ul>
  <div>
</body>

</html>