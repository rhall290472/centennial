<?php
if (!session_id()) {
  session_start();
}
/*
!==============================================================================!
!\                                                                            /!
!\\                                                                          //!
! \##########################################################################/ !
!  #         This is Proprietary Software of Richard Hall                   #  !
!  ##########################################################################  !
!  ##########################################################################  !
!  #                                                                        #  !
!  #                                                                        #  !
!  #   Copyright 2017-2024 - Richard Hall                                   #  !
!  #                                                                        #  !
!  #   The information contained herein is the property of Richard          #  !
!  #   Hall, and shall not be copied, in whole or in part, or               #  !
!  #   disclosed to others in any manner without the express written        #  !
!  #   authorization of Richard Hall.                                       #  !
!  #                                                                        #  !
!  #                                                                        #  !
! /##########################################################################\ !
!//                                                                          \\!
!/                                                                            \!
!==============================================================================!
*/

include_once 'CMBCollege.php';
$CMBCollege = CMBCollege::getInstance();
// This code stops anyone for seeing this page unless they have logged in and
// they account is enabled.
if (!(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true)) {
  $CMBCollege->GotoURL("index.php");
  exit;
}
?>
<html>

<head>
  <?php include('header.php'); ?>
</head>

<body>
  <!-- Responsive navbar-->
  <nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top">
    <div class="container px-lg-4">
      <a class="navbar-brand" href="#!">Centennial District Merit Badge College</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation"><span class="navbar-toggler-icon"></span></button>
      <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
        <li class="nav-item"><a class="nav-link active" aria-current="page" href="https://mbcollege.centennialdistrict.co/index.php">Home</a></li>
          <!-- <li class="nav-item"><a class="nav-link" href="#!">About</a></li> -->
          <li class="nav-item"><a class="nav-link" href="mailto:richard.hall@centennialdistrict.co?subject=Merit Badge College">Contact</a></li>
          <?php
          if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
            echo '<li class="nav-item"><a class="nav-link" href="./logoff.php">Log off</a></li>';
          } else {
            echo '<li class="nav-item"><a class="nav-link" href="./logon.php">Log on</a></li>';
          }
          ?>
        </ul>
      </div>
    </div>
  </nav>


  <div class="container-fluid">
    <div class="row flex-nowrap">
      <!-- Include the common side nav bar -->
      <?php include 'navbar.php'; ?>
      <div class="col">
        <?php
        if (isset($_POST['CollegeYear']) && $_POST['CollegeYear'] !== '') {
          $CollegeYear = $_POST['CollegeYear'];
          setYear($CollegeYear);
        }

        // Display college year and allow user to select.
        $CollegeYear = $CMBCollege->getYear();
        $CMBCollege->SelectCollegeYear($CollegeYear, " Details", false); ?>
        <hr />
        <div class="form-row">
          <div class="col-1">

            <?php

            $queryByMBCollege = sprintf("SELECT * FROM college_details WHERE College='%s' AND College > 0", $CollegeYear);

            $report_results = $CMBCollege->doQuery($queryByMBCollege, $CollegeYear);

            if ($report_results) {
              $rowCollegeDetails = $report_results->fetch_assoc();

              $ContactPerson = $rowCollegeDetails['Contact'];
              $CollegeName = $rowCollegeDetails['College'];
              $CollegeLocation = $rowCollegeDetails['Location'];
              $CollegeAddress = $rowCollegeDetails['Address'];
              $PeriodA = $rowCollegeDetails['PeriodA'];
              $PeriodB = $rowCollegeDetails['PeriodB'];
              $PeriodC = $rowCollegeDetails['PeriodC'];
              $PeriodD = $rowCollegeDetails['PeriodD'];
              $PeriodE = $rowCollegeDetails['PeriodE'];
              $PeriodF = $rowCollegeDetails['PeriodF'];
              $PeriodAB = $rowCollegeDetails['PeriodAB'];
              $PeriodCD = $rowCollegeDetails['PeriodCD'];
              $LunchTIme = $rowCollegeDetails['Lunch'];
              $StartTime = $rowCollegeDetails['StartTime'];
              $EndTime = $rowCollegeDetails['EndTime'];
              $date = strtotime($rowCollegeDetails['Date']);
              $CollegeDate = date('m/d/Y', $date);
              $CollegeNotes = $rowCollegeDetails['Notes'];
            ?>
              <form action="<?php echo $_SERVER['PHP_SELF']; ?>" id="College_Details" method="post">
                <label class="form col-form-label" for="element_1_1">Open for Registration </label>
                <input class="form form-check" id="element_1_1" name="element_1_1" type="hidden" value='0' />
                <input class="form form-check" id="element_1_1" name="element_1_1" type="checkbox" value='1'
                  <?php if ($rowCollegeDetails['Open'] == 1) echo "checked=checked"; ?> />

          </div>
          <div class="col-1">
            <label class="form col-form-label" for="element_1_2">College Name </label>
            <input class="form-control" id="element_1_2" name="element_1_2" class="element text" maxlength="6" size="6"
              <?php if (strlen($rowCollegeDetails['College']) > 0) echo "value='$CollegeName'"; ?> />
            <label>Year</label>

          </div>
          <div class="col">
            <label class="form col-form-label" for="element_1_3">Location </label>
            <input class="form-control" id="element_1_3" name="element_1_3" class="element text large" type="element text" maxlength="255" size="50"
              <?php if (strlen($rowCollegeDetails['Location']) > 0) echo "value='$CollegeLocation'"; ?> />
            <label>Location</label>
          </div>
          <div class="col">
            <label class="form col-form-label" for="element_1_4">Address </label>
            <input class="form-control" id="element_1_4" name="element_1_4" class="element text large" type="element text" maxlength="255" size="50"
              <?php if (strlen($rowCollegeDetails['Address']) > 0) echo "value='$CollegeAddress'"; ?> />
            <label>Address</label>
          </div>
        </div>
        <div class="row">
          <div class="col">
            <label class="form col-form-label" for="element_2_1">Contact Person </label>
            <input class="form-control" id="element_2_1" name="element_2_1" class="element text" maxlength="255" size="50"
              <?php if (strlen($ContactPerson) > 0) echo "value='$ContactPerson'"; ?> />
          </div>
          <div class="col">
            <label class="form col-form-label" for="element_2_2">Phone </label>
            <input class="form-control" id="element_2_2" name="element_2_2" class="element number" type="number" maxlength="10" size="10"
              <?php if (strlen($rowCollegeDetails['Phone']) > 0) echo "value=" . $rowCollegeDetails['Phone']; ?> />
          </div>
          <div class="col">
            <label class="form col-form-label" for="element_2_3">Email </label>
            <input class="form-control" id="element_2_3" name="element_2_3" class="element text" type="email" maxlength="255" size="50"
              <?php if (strlen($rowCollegeDetails['Email']) > 0) echo "value=" . $rowCollegeDetails['Email']; ?> />
          </div>
        </div>
        <div class="row">
          <div class="col">
            <label class="form col-form-label" for="element_3_1">Fee per Scout </label>
            <input class="form-control" id="element_3_1" name="element_3_1" class="element number" type="number" maxlength="5" size="5"
              <?php if (strlen($rowCollegeDetails['Fee/Scout']) > 0) echo "value=" . $rowCollegeDetails['Fee/Scout']; ?> />
          </div>
          <div class="col">
            <label class="form col-form-label" for="element_3_2">Facility Cost </label>
            <input class="form-control" id="element_3_2" name="element_3_2" class="element number" type="number" maxlength="5" size="5"
              <?php if (strlen($rowCollegeDetails['FacilityCost']) > 0) echo "value=" . $rowCollegeDetails['FacilityCost']; ?> />
          </div>
          <div class="col">
            <label class="form col-form-label" for="element_3_3">Lunch Cost </label>
            <input class="form-control" id="element_3_3" name="element_3_3" class="element number" type="number" maxlength="5" size="5"
              <?php if (strlen($rowCollegeDetails['LunchCost']) > 0) echo "value=" . $rowCollegeDetails['LunchCost']; ?> />
          </div>
          <div class="col">
            <label class="form col-form-label" for="element_3_4">% to Council </label>
            <input class="form-control" id="element_3_4" name="element_3_4" class="element number" type="number" step="0.01" maxlength="5" size="5"
              <?php if (strlen($rowCollegeDetails['%ToCouncil']) > 0) echo "value=" . $rowCollegeDetails['%ToCouncil']; ?> />
          </div>
          <div class="col">
            <label class="form col-form-label" for="element_3_5">Profit Loss </label>
            <input class="form-control" id="element_3_5" name="element_3_5" class="element number" type="number" maxlength="5" size="5"
              <?php if (strlen($rowCollegeDetails['Profit/Loss']) > 0) echo "value=" . $rowCollegeDetails['Profit/Loss']; ?> />
          </div>
        </div>
        <div class="row">
          <div class="col">
            <label class="form col-form-label" for="element_4_1">Period A Times </label>
            <input class="form-control" id="element_4_1" name="element_4_1" class="element text" maxlength="255" size="25"
              <?php if (strlen($rowCollegeDetails['PeriodA']) > 0) echo "value='$PeriodA'"; ?> />
          </div>
          <div class="col">
            <label class="form col-form-label" for="element_4_2">Period B Times </label>
            <input class="form-control" id="element_4_2" name="element_4_2" class="element text" maxlength="255" size="25"
              <?php if (strlen($rowCollegeDetails['PeriodB']) > 0) echo "value='$PeriodB'"; ?> />
          </div>
          <div class="col">
            <label class="form col-form-label" for="element_4_3">Period C Times </label>
            <input class="form-control" id="element_4_3" name="element_4_3" class="element text" maxlength="255" size="25"
              <?php if (strlen($rowCollegeDetails['PeriodC']) > 0) echo "value='$PeriodC'"; ?> />
          </div>
          <div class="col">
            <label class="form col-form-label" for="element_4_4">Period D Times </label>
            <input class="form-control" id="element_4_4" name="element_4_4" class="element text" maxlength="255" size="25"
              <?php if (strlen($rowCollegeDetails['PeriodD']) > 0) echo "value='$PeriodD'"; ?> />
          </div>
        </div>
        <div class="row">
          <div class="col">
            <label class="form col-form-label" for="element_5_1">Period AB Times </label>
            <input class="form-control" id="element_5_1" name="element_5_1" class="element text" maxlength="255" size="25"
              <?php if (strlen($rowCollegeDetails['PeriodAB']) > 0) echo "value='$PeriodAB'"; ?> />
          </div>
          <div class="col">
            <label class="form col-form-label" for="element_5_2">Period CD Times </label>
            <input class="form-control" id="element_5_2" name="element_5_2" class="element text" maxlength="255" size="25"
              <?php if (strlen($rowCollegeDetails['PeriodCD']) > 0) echo "value='$PeriodCD'"; ?> />
          </div>
          <div class="col">
            <label class="form col-form-label" for="element_5_3">Period E Times </label>
            <input class="form-control" id="element_5_3" name="element_5_3" class="element text" maxlength="255" size="25"
              <?php if (strlen($rowCollegeDetails['PeriodE']) > 0) echo "value='$PeriodE'"; ?> />
          </div>
          <div class="col">
            <label class="form col-form-label" for="element_5_4">Period F Times </label>
            <input class="form-control" id="element_5_4" name="element_5_4" class="element text" maxlength="255" size="25"
              <?php if (strlen($rowCollegeDetails['PeriodF']) > 0) echo "value='$PeriodF'"; ?> />
          </div>
        </div>
        <div class="row">
          <div class="col">
            <label class="form col-form-label" for="element_6_1">Lunch </label>
            <input class="form-control" id="element_6_1" name="element_6_1" class="element text" maxlength="255" size="25"
              <?php if (strlen($rowCollegeDetails['Lunch']) > 0) echo "value='$LunchTIme'"; ?> />
          </div>
          <div class="col">
            <label class="form col-form-label" for="element_6_2">Date</label>
            <input class="form-control" id="element_6_2" name="element_6_2" type="element text" maxlength="255" size="25"
              <?php if (strlen($CollegeDate) > 0) echo "value=" . $CollegeDate; ?> />
          </div>
          <div class="col">
            <label class="form col-form-label" for="element_6_3">Start Time </label>
            <input class="form-control" id="element_6_3" name="element_6_3" class="element text" maxlength="255" size="25"
              <?php if (strlen($rowCollegeDetails['StartTime']) > 0) echo "value='$StartTime'"; ?> />
          </div>
          <div class="col">
            <label class="form col-form-label" for="element_6_14">End Time </label>
            <input class="form-control" id="element_6_4" name="element_6_4" class="element text" maxlength="255" size="25"
              <?php if (strlen($rowCollegeDetails['EndTime']) > 0) echo "value='$EndTime'"; ?> />
          </div>
        </div>
        <div class="row">
          <div class="col-12 py-3">
            <label class="form col-form-label" for="element_7_1">Notes </label>
            <textarea class="textarea form-control-sm" rows="10" cols="100" id="element_7_1" name="element_7_1"><?php echo $CollegeNotes; ?></textarea>
          </div>
        </div>
        <div class="row">
          <div class="col" style="text-align: center;">
            <input type="hidden" name="form_id" value="22772" />
            <input id="saveForm2" class="btn btn-primary btn-sm" type="submit" name="SubmitForm" value="SubmitForm" />

          </div>
        </div>
        </form>
      <?php
            }
      ?>
      </div>
    </div>
    <?php
    //#####################################################
    //
    // Check to see if user as Submitted the form.
    // If so add or update the college details
    //
    //#####################################################
    if (isset($_POST['SubmitForm'])) {

      $CollegeDetails = array();
      // First row of data
      $CollegeDetails['Open']         = $CMBCollege->GetFormData('element_1_1');
      $CollegeDetails['College']      = $CMBCollege->GetFormData('element_1_2');
      $CollegeDetails['Location']     = $CMBCollege->GetFormData('element_1_3');
      $CollegeDetails['Address']      = $CMBCollege->GetFormData('element_1_4');
      // Second row of data
      $CollegeDetails['Contact']      = $CMBCollege->GetFormData('element_2_1');
      $CollegeDetails['Phone']        = $CMBCollege->GetFormData('element_2_2');
      $CollegeDetails['Email']        = $CMBCollege->GetFormData('element_2_3');
      // Third row of data
      $CollegeDetails['Fee/Scout']    = $CMBCollege->GetFormData('element_3_1');
      $CollegeDetails['FacilityCost'] = $CMBCollege->GetFormData('element_3_2');
      $CollegeDetails['LunchCost']    = $CMBCollege->GetFormData('element_3_3');
      $CollegeDetails['ToCouncil']    = $CMBCollege->GetFormData('element_3_4');
      $CollegeDetails['Profit/Loss']  = $CMBCollege->GetFormData('element_3_5');
      // Fourth row
      $CollegeDetails['PeriodA']      = $CMBCollege->GetFormData('element_4_1');
      $CollegeDetails['PeriodB']      = $CMBCollege->GetFormData('element_4_2');
      $CollegeDetails['PeriodC']      = $CMBCollege->GetFormData('element_4_3');
      $CollegeDetails['PeriodD']      = $CMBCollege->GetFormData('element_4_4');
      // FIfth row
      $CollegeDetails['PeriodAB']     = $CMBCollege->GetFormData('element_5_1');
      $CollegeDetails['PeriodCD']     = $CMBCollege->GetFormData('element_5_2');
      $CollegeDetails['PeriodE']      = $CMBCollege->GetFormData('element_5_3');
      $CollegeDetails['PeriodF']      = $CMBCollege->GetFormData('element_5_4');
      // Sixth row
      $CollegeDetails['Lunch']        = $CMBCollege->GetFormData('element_6_1');
      $CollegeDetails['Date']         = $CMBCollege->GetFormData('element_6_2');
      $CollegeDetails['StartTime']    = $CMBCollege->GetFormData('element_6_3');
      $CollegeDetails['EndTime']      = $CMBCollege->GetFormData('element_6_4');
      // Seventh row
      $CollegeDetails['Notes']        = $CMBCollege->GetFormData('element_7_1');

      $CMBCollege->AddUpdateCollege($CollegeDetails);
      $CMBCollege->GotoURL('index.php');
      exit;
    }


    ?>



    <?php include("Footer.php"); ?>

</body>

</html>