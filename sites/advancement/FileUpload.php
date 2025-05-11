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

include_once('CPack.php');
include_once('CTroop.php');
include_once('CCrew.php');
//include_once('CPost.php');
include_once('CUnit.php');
//include_once('CTrainedLeaders.php')	;
//include_once("CYpt.php");
include_once('CAdvancement.php');
include_once('cAdultLeaders.php');

$CAdvancement = CAdvancement::getInstance();
$CUnit = UNIT::getInstance();
$CPack = CPack::getInstance();
$CTroop = CTroop::getInstance();
$CCrew = CCrew::getInstance();
$CPost = CPost::getInstance();
//$CTrainedLeaders = TrainedLeaders::getInstance();
//$CYPT = YPT::getInstance();
$cAdultLeaders = AdultLeaders::getInstance();
?>
<!DOCTYPE html>
<html lang="en">


<head>
	<?php include 'head.php'; ?>
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
$strTemp = explode($filedot, $fileName);
$strTemp = end($strTemp);
$fileExtension = strtolower($strTemp);

$uploadPath = $currentDirectory . $uploadDirectory . basename($fileName);

if (isset($_POST['submit'])) {

	if (!in_array($fileExtension, $fileExtensionsAllowed)) {
		$errors[] = "This file extension is not allowed. Please upload a CSV file";
	}

	if ($fileSize > 4000000) {
		$errors[] = "File exceeds maximum size (4MB)";
	}

	if (empty($errors)) {
		$didUpload = move_uploaded_file($fileTmpName, $uploadPath);

		if ($didUpload) {
			/* echo "The file " . basename($fileName) . " has been uploaded<br/>"; */
			$Update = $_POST['submit'];
			switch ($Update) {
				case "UpdateTotals":
					//$RecordsInError = $CUnit->UpdateTotals($fileName);
					$RecordsInError = $CUnit->ImportCORData($fileName);
					break;
				case "UpdatePack":
					$RecordsInError = $CPack->UpdatePack($fileName);
					break;
				case "UpdateTroop":
					$RecordsInError = $CTroop->UpdateTroop($fileName);
					break;
				case "UpdateCrew":
					$RecordsInError = $CCrew->UpdateCrew($fileName);
					break;
				case "TrainedLeader":
					$RecordsInError = $cAdultLeaders->TrainedLeader($fileName);
					$CAdvancement->UpdateLastUpdated('trainedleaders', "");
					break;
				case "Updateypt":
					$RecordsInError = $cAdultLeaders->Updateypt($fileName);
					$CAdvancement->UpdateLastUpdated('ypt', "");

					break;
				case "UpdateVenturing":
					$RecordsInError = $CCrew->UpdateVenturing($fileName);
					break;
				case "UpdateAdventure":
					$RecordsInError = $CPack->UpdateAdventure($fileName);
					break;
				case "UpdateCommissioners":
					$RecordsInError = $CUnit->UpdateCommissioner($fileName);
					break;
				case "UpdateFunctionalRole":
					$RecordsInError = $cAdultLeaders->UpdateFunctionalRole($fileName);
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

	//if($RecordsInError == 0){
	echo "<script>window.location.href = 'index.php';</script>";
	//}

}
