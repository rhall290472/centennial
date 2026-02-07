<?php
// Secure session start
if (session_status() === PHP_SESSION_NONE) {
  session_start([
    'cookie_httponly' => true,
    'use_strict_mode' => true,
    'cookie_secure' => isset($_SERVER['HTTPS'])
  ]);
}

//include('cEagle.php');
$cEagle = cEagle::getInstance();

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


?>
<!DOCTYPE html>
<html lang="en">

<head>
  <?php //include('./head.php'); ?>
</head>
<?php //load_template('/navbar.php'); ?>

<body class="body" style="padding:20px">
  <div class="container-fluid">
    <div class="row flex-nowrap">
      <!-- Include the common side nav bar -->
      <?php //include 'sidebar.php'; ?>
      <div class="col py-3">
        <!-- Header-->
        <header class="py-5">
          <div class="container px-lg-5">
            <div class="p-4 p-lg-5 bg-light rounded-3 text-center">
              <div class="m-4 m-lg-5">
                <h1 class="display-5 fw-bold">Users for the Eagles Database</h1>
                <p class="fs-4">Below is a list of Users</p>
                <!-- <a class=" btn btn-primary btn-lg" href="./AddUsers.php">Add User</a> -->
              </div>
            </div>
          </div>
        </header>

        <section class="py-5">
          <?php
          // Get current events in database
          $sql = "SELECT * FROM users WHERE is_deleted <> 1";
          $result = $cEagle->doQuery($sql);
          if ($result) {
            // Display the events 
          ?>
            <div class="table-responsive">
              <table id="usersTable" class="table table-striped">
                <thead>
                  <tr>
                    <th>Userid</th>
                    <th>Username</th>
                    <th>Enabled</th>
                    <th>Last login</th>
                    <th>Role</th>
                    <th>is_deleted</th>
                    <th>Created</th>
                    <!-- <th>Updated</th> -->
                  </tr>
                </thead>
                <tbody>
                  <?php
                  while ($row = $result->fetch_assoc()) {
                    echo "<tr><td>" .
                      "<a href=?page=edituser&Userid=" . $row["Userid"] . ">" . $row["Userid"] . "</a>" . "</td><td>" .
                      $row["username"] . "</td><td>" .
                      $row["enabled"] . "</td><td>" .
                      $row["LastLogin"] . "</td><td>" .
                      $row["Role"] . "</td><td>" .
                      $row["is_deleted"] . "</td><td>" .
                      $row["created_by"] . "</td></tr>";
                    // $row["updated_by"] . "</td></tr>";
                  }
                  ?>
                </tbody>
              </table>
            </div>
          <?php

          }
          ?>
        </section>
      </div>
    </div>
  </div>

  <!-- DataTables Initialization Script -->
  <script>
    $(document).ready(function() {
      $('#usersTable').DataTable({
        "order": [
          [0, "asc"]
        ], // Sort by first column (ID) ascending by default
        "pageLength": 25, // Show 25 rows per page (adjust as needed)
        "lengthMenu": [
          [10, 25, 50, -1],
          [10, 25, 50, "All"]
        ], // Pagination options
        "responsive": true // Enable responsive behavior for mobile
      });
    });
  </script>

</body>

</html>