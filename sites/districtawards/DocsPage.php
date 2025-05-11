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

require_once 'CDistrictAwards.php';
$cDistrictAwards = cDistrictAwards::getInstance();

// This code stops anyone for seeing this page unless they have logged in and
// they account is enabled.
//if (!(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true)) {
//    $cDistrictAwards->GotoURL("index.php");
//    exit;
//}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php include("header.php"); ?>
    <meta name="description" content="DocsPage.php">
</head>

<body>
    <!-- Responsive navbar-->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top">
        <div class="container px-lg-5">
            <a class="navbar-brand" href="#!">Centennial District Awards - Forms Download</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation"><span class="navbar-toggler-icon"></span></button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                    <li class="nav-item"><a class="nav-link active" aria-current="page" href="./index.php">Home</a></li>
                    <!-- <li class="nav-item"><a class="nav-link" href="#!">About</a></li> -->
                    <li class="nav-item"><a class="nav-link" href="./contact.php">Contact</a></li>
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

    <div class="my_div">

        <?php
        $arrFiles = array();
        $handle = opendir('./Policy');
        $currentDirectory = getcwd();
        $uploadDirectory = "./Policy/";
        if ($handle) {
            while (($entry = readdir($handle)) !== FALSE) {
                $arrFiles[] = $entry;
            }
            closedir($handle);

            // Now display them..
            echo "</br></br>";
            echo '<ul>';
            for ($i = 2; $i < count($arrFiles); $i++) {
                $uploadPath = $uploadDirectory . basename($arrFiles[$i]);

                echo "<li><a href='$uploadPath'>$arrFiles[$i]</a></li>";
            }
            echo '</ul>';
        }

        ?>
    </div>

    <?php include("Footer.php"); ?>
</body>

</html>