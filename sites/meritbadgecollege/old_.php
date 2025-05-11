<?php
/*
!==============================================================================!
!\                                                                            /!
!\\                                                                          //!
! \##########################################################################/ !
!  #         This is Proprietary Software of Richard Hall                   #  !
!  ##########################################################################  !
!  #                                                                        #  !
!  #  FILE NAME   :  header.php                                             #  !
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

class CHeader
{

    public static function DisplayHomeHeader()
    {
?>
            <div class="header">
                <center>
                    <p style="font-size: 25px;font-weight: bold;">Centennial District Merit Badge College Data</p>
                </center>
                <div class="header-right">
                    <?php
                    if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
                        echo "<a href='logoff.php' target='_self'>Logoff</a>";
                    } else {
                        echo "<a href='logon.php' target='_self'>Login</a>";
                    }
                    ?>
                    <a href="mailto:richard.hall@centennial.co?subject=District Advancement Data">Contact</a>
                    <a href="advancement/about.html">About</a>
                </div>

                <div class="header-left">
                    <!-- </br> -->
                    <div class="Mytooltip">
                        <span class="Mytooltiptext">Advancement Help page</span>
                        <a href='advancement/main.html' target='_self'>Advancement</a>
                    </div>

                    <?php
                    if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
                    ?>
                        <div class="Mytooltip">
                            <span class="Mytooltiptext">Updated the data tables used by this site</span>
                            <a href='./UpdateTables.php'>Update Tables</a>
                        </div>
                    <?php
                    }
                    ?>
                </div>

            </div> <!-- <div class="header"> -->
    <?php
    }


    public static function DisplayPageHeader($title, $UnitCO, $CommissionerData)
    {
    ?>
            <div class="header">
                <center>
                    <p style="font-size: 25px;font-weight: bold;"><?php echo $title ?></p>
                </center>
                <?php 
                    if(strlen($UnitCO) > 0)
                        echo "<p>".$UnitCO."</p>";
                    if(strlen($CommissionerData) > 0)
                        echo "<p>".$CommissionerData."</p>"; 
                ?>
                <div class="header-right">
                    <a class="active" href="index.php">Home</a>
                    <a href="mailto:richard.hall@centennial.co?subject=District Advancement Data">Contact</a>
                    <a href="advancement/about.html">About</a>
                </div>

                <div class="header-left">
                    <!-- </br> -->
                    <div class="Mytooltip">
                        <span class="Mytooltiptext">Advancement Help page</span>
                        <a href='advancement/main.html' target='_self'>Advancement</a>
                    </div>
                    <?php
                    if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
                    ?>
                        <div class="Mytooltip">
                            <span class="Mytooltiptext">Updated the data tables used by this site</span>
                            <a href='./UpdateTables.php'>Update Tables</a>
                        </div>
                    <?php
                    }
                    ?>

                </div>

            </div> <!-- <div class="header"> -->
    <?php
    }
}
?>