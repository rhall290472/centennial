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

	include_once('CScout.php');
	$CScout = CScout::getInstance();
	include_once('CCounselor.php');
	$CCounselor = CCounselor::getInstance();
?>


	<head>
    <?php include('header.php'); ?>
	</head>
<?php
	$currentDirectory = getcwd();
    $uploadDirectory = "/Data/";

    $errors = []; // Store errors here

    $fileExtensionsAllowed = ['csv']; // These will be the only file extensions allowed 

    $fileName = $_FILES['the_file']['name'];
    $fileSize = $_FILES['the_file']['size'];
    $fileTmpName  = $_FILES['the_file']['tmp_name'];
    $fileType = $_FILES['the_file']['type'];
   	$filedot = '.';
    $Explode = explode($filedot,$fileName);
    $End = end($Explode);
    $fileExtension = strtolower($End);

    $uploadPath = $currentDirectory . $uploadDirectory . basename($fileName); 

    if (isset($_POST['submit'])) {

      if (! in_array($fileExtension,$fileExtensionsAllowed)) {
        $errors[] = "This file extension is not allowed. Please upload a CSV file";
        exit();
      }

      if ($fileSize > 4000000) {
        $errors[] = "File exceeds maximum size (4MB)";
        exit();
      }

      if (empty($errors)) {
        $didUpload = move_uploaded_file($fileTmpName, $uploadPath);

        if ($didUpload) {
          /* echo "The file " . basename($fileName) . " has been uploaded<br/>"; */
          $Update = $_POST['submit'];
          switch ($Update){
            case "ImportScouts":
				      $RecordsInError = $CScout->ImportScouts($fileName);
              break;
            case "ImportCounselor":
              $RecordsInError = $CCounselor->UpdateCouncilListShort($fileName);
              break;
			default:
              echo "Default case reached";
            break;
          }
        } else {
          echo "An error occurred. Please contact the administrator.";
        }
      } else {
        foreach ($errors as $error) {
          echo $error . "These are the errors" . "\n";
        }
      }

	    if($RecordsInError == 0){
		    echo "<script>window.location.href = 'index.php';</script>";
	    }
      else{
        ?>
        <a class="active" href="index.php">Home</a>
        <?php
      }
	  
    }


