<?php
// Secure session start
if (session_status() === PHP_SESSION_NONE) {
  session_start([
    'cookie_httponly' => true,
    'use_strict_mode' => true,
    'cookie_secure' => isset($_SERVER['HTTPS'])
  ]);
}

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
!  #   Copyright 2024 - Richard Hall                                        #  !
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

<body class="body">


  <?php
  //load_template('/navbar.php');

  // Check which type of camp view they wish to view
  if (isset($_GET['Userid'])) {
    $user = $_GET['Userid'];
    $sql = "SELECT * FROM `users` WHERE Userid = '" . $user . "'";

    $ResultUser = $cEagle->doQuery($sql);
    if (!$ResultUser) {
      $strErr = "Internal Error";
      $cEagle->function_alert($strErr);
      exit();
    }
    $user = $ResultUser->fetch_assoc();
  }



  // CHeck is user submitted form
  if (isset($_POST['SubmitForm'])) {
    if ($_POST['SubmitForm'] == "Cancel") {
      $classESC->GotoURL('./index.php');
      exit;
    }

    // Save New data..From the user form
    $FormData = array();
    //Line 1
    $FormData['username'] = $cEagle->GetFormData('element_1_1');
    $FormData['password'] = $cEagle->GetFormData('element_1_2');
    $FormData['is_deleted'] = $cEagle->GetFormData('element_1_3');

    //$FormData['email'] = $cEagle->GetFormData('element_2_1');
    $FormData['Role'] = $cEagle->GetFormData('element_2_2');
    $FormData['enabled'] = $cEagle->GetFormData('element_2_3');

    //$FormData['Notes'] = $cEagle->GetFormData('element_4_1');



    $sql = "UPDATE `users` SET `username`='$FormData[username]',
    `password`='$FormData[password]',`is_deleted`='$FormData[is_deleted]',
    `enabled`='$FormData[enabled]'
    WHERE `Userid`='$_POST[ID]'";

    $cEagle->doQuery($sql);

    $cEagle->GotoURL('index.php?page=viewuser');
    exit;
  }




  ?>
  <div style="padding:20px">
    <form action="index.php?page=edituser" id="form-user" method="post">
    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'] ?? bin2hex(random_bytes(32))); ?>">
      <div class="form-row">
        <div class="col-2">
          <label>Username</label>
          <input type="text" name="element_1_1" class="form-control" <?php if (strlen($user['username']) > 0) echo "value=" . $user['username']; ?> />
        </div>
        <div class="col-3">
          <label>Password</label>
          <input type="text" name="element_1_2" class="form-control" <?php if (strlen($user['password']) > 0) echo "value=" . $user['password']; ?> />
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
        <!-- <div class="col-3">
          <label>Email</label>
          <input type="text" name="element_2_1" class="form-control" <?php if (strlen($user['email']) > 0) echo "value=" . $user['email']; ?> />
        </div> -->
        <div class="col-2">
          <label>Type</label>
          <select class='form-control' name='element_2_2'>
            <option value=""> </option>
              <?php
              $Role = $user['Role'];
              $selected = !strcmp($Role, "Admin") ? $Selected = "selected" : $Selected = "";
              echo "<option value=Admin " . $Selected . ">Admin</option>";
              $selected = !strcmp($Role, "User") ? $Selected = "selected" : $Selected = "";
              echo "<option value=User " . $Selected . ">User</option>";
              $selected = !strcmp($Role, "Spammer") ? $Selected = "selected" : $Selected = "";
              echo "<option value=Spammer " . $Selected . ">Spammer</option>";
              ?>
          </select>
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
        <div class="col-3">
          <label>Created</label>
          <input type="text" name="element_3_1" class="form-control" <?php if (strlen($user['created_by']) > 0) echo "value=" . $user['created_by']; ?> readonly />
        </div>
        <div class="col-2">
          <label>Last Login</label>
          <input type="text" name="element_3_2" class="form-control" <?php if (strlen($user['LastLogin']) > 0) echo "value=" . $user['LastLogin']; ?> readonly />
        </div>
      </div>

      <!-- <div class="form-row">
        <div class="col-5">
          <label>Notes</label>
          <textarea name="element_4_1" class="form-control" id="Notes" rows="10"><?php if (strlen($user['Notes']) > 0) echo $user['Notes']; ?></textarea>
        </div>
      </div> -->

      <div class="form-row">
        <div class="col-10 py-5">
          <?php $ID = $user['Userid']; ?>
          <?php echo '<input type="hidden" name="ID" value="' . $user['Userid'] . '"/>'; ?>
          <input id="saveForm2" class="btn btn-primary btn-sm" type="submit" name="SubmitForm" value="Save" />
          <input id="saveForm2" class="btn btn-primary btn-sm" type="submit" name="SubmitForm" value="Cancel" />
        </div>
      </div>

    </form>
  </div>


  <?php //include "./Footer.php"; ?>
</body>

</html>