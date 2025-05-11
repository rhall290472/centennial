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
!  #                                                                        #  !
!  #  FILE NAME   :  index.php                                              #  !
!  #                                                                        #  !
!  #  DESCRIPTION :  Website to Support Centennial District Advacncement    #  !
!  #                 Data                                                   #  !
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
include('../ESC.php');
$classESC = ESC::getInstance();

/* Check if the user is already logged in, if yes then redirect him to welcome page */
if (!(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true)) {
  header("HTTP/1.0 403 Forbidden");
  //$classESC::GotoURL("./index.php");
  exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>

  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
  <meta name="description" content="" />
  <meta name="author" content="" />
  <title>Emergency Service Corp</title>
  <!-- Favicon-->
  <!-- <link rel="icon" type="image/x-icon" href="assets/centennial.ico" /> -->
  <!-- Bootstrap icons-->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
  <!-- Core theme CSS (includes Bootstrap)-->
  <link href="../assets/css/main.css" rel="stylesheet" />


</head>

<body>
  <!-- Responsive navbar-->
  <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container px-lg-5">
      <a class="navbar-brand" href="../index.php">Emergency Service Corp</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation"><span class="navbar-toggler-icon"></span></button>
      <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
          <li class="nav-item"><a class="nav-link active" aria-current="page" href="../admin/admin_index.php">Home</a></li>
          <li class="nav-item"><a class="nav-link" href="#!">About</a></li>
          <li class="nav-item"><a class="nav-link" href="#!">Contact</a></li>
          <li class="nav-item"><a class="nav-link" href="./logon.php">Log on</a></li>
        </ul>
      </div>
    </div>
  </nav>

  <?php
  if (isset($_GET['IDX'])) {
    $IDX = $_GET['IDX'];
    // User IDX was passed to use so, go get their data
    $sql = "SELECT * from users WHERE IDX='$IDX'";

    $Result = $classESC->doQuery($sql);
    $user = $Result->fetch_assoc();
  }

  // CHeck is user submitted form
  if (isset($_POST['SubmitForm'])) {
    if ($_POST['SubmitForm'] == "Cancel") {
      $classESC->GotoURL('../admin/admin_index.php');
      exit;
    }

    // Save New data..From the user form
    $FormData = array();
    //Line 1
    $FormData['FirstName'] = $classESC->GetFormData('element_1_1');
    $FormData['LastName'] = $classESC->GetFormData('element_1_2');
    $FormData['is_deleted'] = $classESC->GetFormData('element_1_3');

    $FormData['Email'] = $classESC->GetFormData('element_2_1');
    $FormData['username'] = $classESC->GetFormData('element_2_2');
    $FormData['enabled'] = $classESC->GetFormData('element_2_3');

    $FormData['role'] = $classESC->GetFormData('element_3_1');

    $sql = "UPDATE `users` SET `FirstName`='$FormData[FirstName]',
    `LastName`='$FormData[LastName]',`Email`='$FormData[Email]',`username`='$FormData[username]',
    `enabled`='$FormData[enabled]',`role`='$FormData[role]',`is_deleted`='$FormData[is_deleted]',
    `updated_by`='$_SESSION[username]' WHERE `IDX`='$_POST[ID]'";

    $classESC->doQuery($sql);

    $classESC->GotoURL('../admin/admin_index.php');
    exit;
  }
  ?>
  <div class="px-5 py-5">
    <div class="container" style="background-color: var(--scouting-lighttan);">
      <p style="text-align:Left"><b>User Information</b></p>

      <form action="<?php echo $_SERVER['PHP_SELF']; ?>" s id="form-user" method="post">

        <div class="form-row">
          <div class="col-2">
            <label>First</label>
            <input type="text" name="element_1_1" class="form-control" <?php if (strlen($user['FirstName']) > 0) echo "value=" . $user['FirstName']; ?> />
          </div>
          <div class="col-3">
            <label>Last</label>
            <input type="text" name="element_1_2" class="form-control" <?php if (strlen($user['LastName']) > 0) echo "value=" . $user['LastName']; ?> />
          </div>
          <div class="col-1 py-4">
            <div class="form-check">
              <input class="form-check-input" type="hidden" name="element_1_3" value='0' />
              <input class="form-check-input" type="checkbox" name="element_1_3" value='1' <?php if ($user['is_deleted'] == 1) echo "checked=checked"; ?>>
              <label class="form-check-label" for="element_1_3">Deleted</label>
            </div>
          </div>
        </div>

        <div class="form-row">
          <div class="col-3">
            <label>Email</label>
            <input type="text" name="element_2_1" class="form-control" <?php if (strlen($user['Email']) > 0) echo "value=" . $user['Email']; ?> />
          </div>
          <div class="col-2">
            <label>username</label>
            <input type="text" name="element_2_2" class="form-control" <?php if (strlen($user['username']) > 0) echo "value=" . $user['username']; ?> />
          </div>
          <div class="col-1 py-4">
            <div class="form-check">
              <input class="form-check-input" type="hidden" name="element_2_3" value='0' />
              <input class="form-check-input" type="checkbox" name="element_2_3" value='1' <?php if ($user['enabled'] == 1) echo "checked=checked"; ?>>
              <label class="form-check-label" for="element_2_3">Enabled</label>
            </div>
          </div>
        </div>

        <div class="form-row">
          <div class="col-2">
            <label>Role</label>
            <select class='form-control' name='element_3_1'>
              <option value=\"\" </option>
                <?php
                $Role = $user['role'];
                $selected = !strcmp($Role, "webmaster") ? $Selected = "selected" : $Selected = "";
                echo "<option value=webmaster " . $Selected . ">Webmaster</option>";
                $selected = !strcmp($Role, "Admin") ? $Selected = "selected" : $Selected = "";
                echo "<option value=Admin " . $Selected . ">Admin</option>";
                ?>
            </select>
          </div>
        </div>


        <div class="form-row">
          <div class="col-10 py-5">
            <?php $ID = $user['IDX']; ?>
            <?php echo '<input type="hidden" name="ID" value="' . $user['IDX'] . '"/>'; ?>
            <input id="saveForm2" class="btn btn-primary btn-sm" type="submit" name="SubmitForm" value="Save" />
            <input id="saveForm2" class="btn btn-primary btn-sm" type="submit" name="SubmitForm" value="Cancel" />
          </div>
        </div>

      </form>
    </div>
  </div>
  <!-- User Edit Form -->



  <Footer class="fixed-bottom">

    <!-- Footer-->
    <footer class="py-3 bg-dark">
      <div class="container">
        <p class="m-0 text-center text-white">Copyright &copy; centennialdistirct.co 2024</p>
      </div>
    </footer>
    <!-- Bootstrap core JS-->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Core theme JS-->
    <script src="https://centennialdistrict.co/assets/js/scripts.js"></script>
  </Footer>
</body>

</html>