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

/* Check if the user is already logged in, if yes then redirect him to welcome page */
if (!(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true)) {
  header("HTTP/1.0 403 Forbidden");
  exit;
}
require_once 'CDistrictAwards.php';
require_once 'CAwards.php';
$cDistrictAwards = cDistrictAwards::getInstance();
$cAwards = CAwards::getInstance();


?>
<!DOCTYPE html>
<html lang="en">

<head>
  <?php include("header.php"); ?>
  <meta name="description" content="ViewUsers.php">
  </head>

<body class="body" style="padding:20px">
  <!-- Responsive navbar-->
  <nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top">
    <div class="container px-lg-4">
      <a class="navbar-brand" href="#!">Centennial District Awards</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation"><span class="navbar-toggler-icon"></span></button>
      <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
          <li class="nav-item"><a class="nav-link active" aria-current="page" href="./index.php">Home</a></li>
          <li class="nav-item"><a class="nav-link" href="#!">About</a></li>
          <li class="nav-item"><a class="nav-link" href="#!">Contact</a></li>
          <?php
          if (isset($_SESSION["loggedin"]) && $_SESSION["role"] == "Admin")
            echo '<li class="nav-item"><a class="nav-link" href="./ViewUsers.php">Users</a></li>';
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


  <!-- Header-->
  <header class="py-5">
    <div class="container px-lg-5">
      <div class="p-4 p-lg-5 bg-light rounded-3 text-center">
        <div class="m-4 m-lg-5">
          <h1 class="display-5 fw-bold">Users for the District Awards</h1>
          <p class="fs-4">Below is a list of Users</p>
          <!-- <a class=" btn btn-primary btn-lg" href="./AddUsers.php">Add User</a> -->
        </div>
      </div>
    </div>
  </header>

  <section class="py-5">
    <?php
    // Get current events in database
    $sql = "SELECT * FROM users";
    $result = $cDistrictAwards->doQuery($sql);
    if ($result) {
      // Display the events 
    ?>
      <table class="table table-striped">
        <tr>
          <th>Userid </th>
          <th>Username</th>
          <th>Enabled</th>
          <th>Last login</th>
          <th>Role</th>
          <th>is_deleted</th>
          <th>created</th>
          <!-- <th>Updated</th> -->
        </tr>
        <?php
        while ($row = $result->fetch_assoc()) {
          echo "<tr><td>" .
            "<a href=./EditUser.php?Userid=" . $row["Userid"] . ">" . $row["Userid"] . "</a>" . "</td><td>" .
            $row["username"] . "</td><td>" .
            $row["enabled"] . "</td><td>" .
            $row["LastLogin"] . "</td><td>" .
            $row["Role"] . "</td><td>" .
            $row["is_deleted"] . "</td><td>" .
            $row["created"] . "</td><tr>";
          // $row["updated_by"] . "</td></tr>";
        }
        ?>
      </table>
    <?php

    }
    ?>
  </section>
  <?php include("./Footer.php"); ?>
</body>

</html>