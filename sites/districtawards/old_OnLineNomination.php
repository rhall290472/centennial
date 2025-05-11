<?php
if (!session_id()) {
    session_start();

    require_once 'CDistrictAwards.php';
    require_once 'CAwards.php';
    $cDistrictAwards = cDistrictAwards::getInstance();
    $cAwards = cAwards::getInstance();

    // This code stops anyone for seeing this page unless they have logged in and
    // they account is enabled.
//    if (!(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true)) {
//        $cDistrictAwards->GotoURL("index.php");
//        exit;
//    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-6PCWFTPZDZ"></script>
    <script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());

    gtag('config', 'G-6PCWFTPZDZ');
    </script>

    <meta charset="UTF-8">
    <title>OnLine Nomination</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="css/districtawards.css">
    <link rel="stylesheet" href="../bootstrap-5.3.2/css/bootstrap.css">
</head>

<body>
    <div class="my_div">
        <?php include('header.php');

        //#####################################################
        //
        // Check to see if user has selected an award
        //
        //#####################################################
        if ( isset($_POST['SubmitAward']) && $_POST['SubmitAward'] !== '' ) 
        {
            // User has selected an award. Go display award requirements and then get Nominee data
            switch( $_POST['AwardIDX']) {
                case 1:         // District Award of Merit
                    $cAwards->DistrictAwardofMerit();
                    break;
                case 14:        // Outstanding Leader
                    $cAwards->OustandingLeader();
                    break;
                case 15:        // Key Scouter
                    $cAwards->KeyScouter();
                    break;
                case 2:        // Scoutmaster of the Year
                    $cAwards->AwardNomination("./Awards/Scoutmaster.html");
                    break;
                case 3:        // Rookie Scoutmaster of the Year
                    $cAwards->AwardNomination("./Awards/RookieScoutmaster.html");
                    break;
                case 4:        // Cubtmaster of the Year
                    $cAwards->AwardNomination("./Awards/Cubmaster.html");
                    break;
                case 5:        // Rookie Cubmaster of the Year
                    $cAwards->AwardNomination("./Awards/RookieCubmaster.html");
                     break;
                case 6:        // Crew Advisor of the Year
                    $cAwards->AwardNomination("./Awards/CrewAdvisor.html");
                    break;
                case 7:        // Rookie Crew Advisor of the Year
                    $cAwards->AwardNomination("./Awards/RookieCewAdvisor.html");
                    break;
                case 48:
                    $cAwards->AwardNomination("./Awards/Skipper.html");
                    break;
                case 49:
                    $cAwards->AwardNomination("./Awards/RookieSkipper.html");
                    break;
                case 20:
                    $cAwards->AwardNomination("./Awards/PackCM.html");
                    break;
                case 22:
                    $cAwards->AwardNomination("./Awards/RookiePackCM.html");
                    break;
                case 21:
                    $cAwards->AwardNomination("./Awards/TroopCM.html");
                    break;
                case 23:
                    $cAwards->AwardNomination("./Awards/RookieTroopCM.html");
                    break;
                case 36:
                    $cAwards->AwardNomination("./Awards/CrewSkipperCM.html.html");
                    break;
                case 49:
                    $cAwards->AwardNomination("./Awards/CrewSkipperCM.html");
                    break;
                case 50:
                    $cAwards->AwardNomination("./Awards/RookieCrewSkipperCM.html");
                    break;
                case 18:
                    $cAwards->AwardNomination("./Awards/Commissioner.html");
                    break;
                case 38:
                    $cAwards->AwardNomination("./Awards/RookieCommissioner.html");
                    break;
                case 25:
                    $cAwards->AwardNomination("./Awards/DistrictCM.html");
                    break;
                case 16:        
                    $cAwards->AwardNomination("./Awards/BaldEagle.html");
                    break;
                case 29:
                    $cAwards->AwardNomination("./Awards/JuniorLeader.html");
                    break;
                default:
            }
            exit();
        }
        ?>


<!------------------------------------------------------------------------------


------------------------------------------------------------------------------>
        <center>
            <?php

            $queryAward = "SELECT DISTINCTROW Award, AwardIDX, UnitLevel FROM awards WHERE `OnLine`='1' ORDER BY UnitLevel, Award";

            $result_ByAward = $cDistrictAwards->doQuery($queryAward);
            if (!$result_ByAward) {
                $cDistrictAwards->function_alert("ERROR: $cDistrictAwards->doQuery($result_ByAward)");
            }
            echo "</br></br>";
            echo "<form method=post>";
            echo "<label for='AwardIDX'>Choose an Award: </label>";
            echo "<select class='selectWrapper' id= 'AwardIDX' name='AwardIDX' >";
            echo "<option value=\"\" </option>";
            while ($rowAward = $result_ByAward->fetch_assoc()) {
                echo "<option value=" . $rowAward['AwardIDX'] . ">" . $rowAward['Award'] ."</option>";
            }
            echo '</select>';
            echo "<input class='rounded' type='submit' name='SubmitAward' value='Select Award'/>";
            echo "</form>";

            //#####################################################
            //
            // Check to see if user selected a Award.
            //
            //#####################################################
            if (
                isset($_POST['SubmitNominee']) && isset($_POST['NomineeName']) && $_POST['NomineeName'] !== '' ||
                (isset($_GET['NomineeIDX']))
            ) {

                if (isset($_POST['NomineeName']))
                    $SelectedNominee = $_POST['NomineeName']; // Get name of Counselor selected
                else if (isset($_GET['NomineeIDX']))
                    $SelectedNominee = $_GET['NomineeIDX'];
                // If new coach is selected must create a record in the database for them.
                // There is a blank record in the database with Coachid set to -1 for this.
                // Go get the Scout data

                // Go get the Nominees data
                $queryNominee = "SELECT * FROM `district_awards` WHERE NomineeIDX='$SelectedNominee'";

                if (!$Nominee = $cDistrictAwards->doQuery($queryNominee)) {
                    $msg = "Error: doQuery()";
                    $cDistrictAwards->function_alert($msg);
                }
                $rowNominee = $Nominee->fetch_assoc();
            ?>
                </br>
                <div class="form-nominee">
                    <p style="text-align:Left"><b>Nominee Information</b></p>
                    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" s id="add_nominee" method="post">
                        <ul>

                            <li id=Line1>
                                <span>
                                    <label>First</label>
                                    <input id="element_1_1" name="element_1_1" <?php if (strlen($rowNominee['FirstName']) > 0) echo "value=" . $rowNominee['FirstName']; ?> />
                                </span>
                                <span>
                                    <label>Preferred Name</label>
                                    <input id="element_1_2" name="element_1_2" <?php if (strlen($rowNominee['PName']) > 0) echo "value=" . $rowNominee['PName']; ?> />
                                </span>
                                <span>
                                    <label>Middle</label>
                                    <input id="element_1_3" name="element_1_3" <?php if (strlen($rowNominee['MName']) > 0) echo "value=" . $rowNominee['MName']; ?> />
                                </span>
                                <span>
                                    <label>Last</label>
                                    <input id="element_1_4" name="element_1_4" <?php if (strlen($rowNominee['LastName']) > 0) echo "value=" . $rowNominee['LastName']; ?> />
                                </span>
                            </li>

                            <li id=Line2>
                                <span>
                                    <label>Award Year</label>
                                    <input id="element_2_1" name="element_2_1" style="width:75px;" type="number" <?php if (strlen($rowNominee['Year']) > 0) echo "value=" . $rowNominee['Year']; ?> />
                                </span>

                                <span>
                                    <label>Award</label>
                                    <select class='selectWrapper' id='element_2_2' name='element_2_2'>
                                        <option value=""> </option>
                                        <?php $cDistrictAwards->DisplayAwardsList($rowNominee['Award']); ?>
                                    </select>
                                </span>

                                <span>
                                    <label>Status</label>
                                    <select class='selectWrapper' id='element_2_3' name='element_2_3'>
                                        <option value=""> </option>
                                        <?php $cDistrictAwards->DisplayAwardsStatus($rowNominee['Status']); ?>
                                    </select>
                                </span>

                                <span>
                                    <label>BSA ID</label>
                                    <input id="element_2_4" name="element_2_4" style="width:130px;" type="number" <?php if (strlen($rowNominee['MemberID']) > 0) echo "value=" . $rowNominee['MemberID']; ?> />
                                </span>

                                <span>
                                    <label>Deleted</label>
                                    <input id="element_2_5" name="element_2_5" type="hidden" value='0' />
                                    <input id="element_2_5" name="element_2_5" type="checkbox" value='1' <?php if ($rowNominee['IsDeleted'] == 1) echo "checked=checked"; ?> />
                                </span>
                            </li>

                            <li id=Line3>
                                <span>
                                    <label>Unit</label>
                                    <input id="element_3_1" name="element_3_1" style="width:150px;"<?php if (strlen($rowNominee['Unit']) > 0) echo "value='" . $rowNominee['Unit'] . "'"; ?> />
                                </span>
                            <li id=Line5>
                                <span>
                                    <label class="description" for="Notes">Notes </label>
                                    <!-- <textarea cols="100" rows="10" id="Notes" name="Notes"><?php echo $rowNominee['Notes']; ?></textarea> -->
                                    <textarea rows="10" id="Notes" name="Notes"><?php echo $rowNominee['Notes']; ?></textarea>
                                    <span>
                            </li>

                                <li id="li_6" class="buttons">
                                    <?php $ID = $rowNominee['NomineeIDX']; ?>
                                    <?php echo '<input type="hidden" name="NomineeIDX" value="' . $rowNominee['NomineeIDX'] . '"/>'; ?>
                                    <input id="saveForm2" class="rounded" type="submit" name="SubmitForm" value="Save" />
                                    <input id="saveForm2" class="rounded" type="submit" name="SubmitForm" value="Cancel" />
                                </li>
                        </ul>
                    </form>
                </div>
            <?php } ?>
        </center>
    </div>
</body>
</header>