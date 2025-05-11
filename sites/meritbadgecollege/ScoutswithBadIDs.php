<?php
if(!session_id()){
    session_start();
}
/*
!==============================================================================!
!\                                                                            /!
!\\                                                                          //!
! \##########################################################################/ !
!  #         This is Proprietary Software of Richard Hall                   #  !
!  ##########################################################################  !
!  #                                                                        #  !
!  #  FILE NAME   :  ScoutswithBadIDs.php                                   #  !
!  #                                                                        #  !
!  #  DESCRIPTION :  Website to Support Merit Badge College.                #  !
!  #                                                                        #  !
!  #                                                                        #  !
!  #  REFERENCES  :                                                         #  !
!  #                                                                        #  !
!  #                                                                        #  !
!  #  CHANGE HISTORY ;                                                      #  !
!  #                                                                        #  !
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

include_once "CScout.php";

$CScout = CScout::getInstance();

	// This code stops anyone for seeing this page unless they have logged in and
	// their account is enabled.
	if(   !(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true)){
        $CScout->GotoURL("index.php");
		header("location: index.php");
    	exit;
	}

?>

<!-- CreateScoutbookCSV.php
     This file will create a csv file properly formatted to import in Scoutbook,
     There are four columns as show below, column names must match

     ScoutLastName	ScoutBSAMemberID	MeritBadgeName	MBCBSAMemberID
    Schmoe	           123456789	         Music	       987654321


-->
<html>

<head>
    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-CPC23NSK6F"></script>
    <script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());

    gtag('config', 'G-CPC23NSK6F');
    </script>

    <title>Merit Badge College - Scout(s) With Bad BSA ID</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="css/MBCollege.css">
    <link rel="stylesheet" href="https://www.centennialdistrict.co/bootstrap-5.3.2/css/bootstrap.css">
</head>

<body style="padding:10px">
    <div class="header">
    <div class="header-right">
            <a class="active" href="index.php">Home</a>
            <a href="mailto:richard.hall@centennialdistrict.co?subject=Merit Badge College Help website">Contact</a>
            <!--<a href="#contact">Contact</a>-->
            <!-- <a href="advancement/about.html">About</a> -->
        </div>

        <?php
        $CollegeYear = $CScout->getYear();
        $queryCollegeYear = "SELECT DISTINCTROW College FROM college_details ORDER BY College DESC";
        $result_CollegeYear = $CScout->doQuery($queryCollegeYear);

        echo "<div style='font:14px sans-serif'>";
        echo "<form method=post>";
        echo "<a href='https://www.denverboyscouts.org/districts/centennial/' class='logo'>Merit Badge College</a>";
        echo "<label for='UnitName'>&nbsp;</label>";
        echo "<select class='selectWrapper' id= 'CollegeYear' name='CollegeYear' >";
        if (isset($_POST['CollegeYear']) && $_POST['CollegeYear'] !== '') {
            $CollegeYear = $_POST['CollegeYear'];
            setYear($CollegeYear);
        }
        
        echo "<option value=\"\" </option>";    //First line is blank
        while ($rowCollege = $result_CollegeYear->fetch_assoc()) {
            if( !strcmp($rowCollege['College'], $CollegeYear))
            {
                echo "<option selected value=" . $rowCollege['College'] . ">" . $rowCollege['College'] . "</option>";
            }else
                echo "<option value=" . $rowCollege['College'] . ">" . $rowCollege['College'] . "</option>";
        }
        echo '</select>';
        echo "<input class='rounded' type='submit' name='SubmitCollege' value='Select College'/>";
        echo "</form>";
        echo "</div>";
        ?>
    </div>

    <?php
        $queryByMBCollege = "SELECT DISTINCTROW  FirstNameScout, LastNameScout, District, UnitType, UnitNumber, Telephone, email, BSAIdScout  
            FROM college_registration
            WHERE college_registration.College='$CollegeYear' AND (college_registration.BSAIdScout<='0')
            ORDER BY District, UnitNumber, LastNameScout";

        $report_results = $CScout->doQuery($queryByMBCollege, $CollegeYear);
        if ($CScout->ReportBadBSAId($report_results)) {
            $report_results->free_result();
        }
    ?>
</body>

</html>