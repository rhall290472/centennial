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

include_once('CMBCollege.php');

$CMBCollege = CMBCollege::getInstance();

// Reset year 
unset($_SESSION['year']);
?>
<!DOCTYPE html>
<html lang="en">

<head>
	<!-- Google tag (gtag.js) -->
	<script async src="https://www.googletagmanager.com/gtag/js?id=G-CPC23NSK6F"></script>
	<script>
		window.dataLayer = window.dataLayer || [];

		function gtag() {
			dataLayer.push(arguments);
		}
		gtag('js', new Date());

		gtag('config', 'G-CPC23NSK6F');
	</script>

	<title>Centennial District Merit Badge College <?php echo $CMBCollege->GetYear() ?></title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="css/MBCollege.css">
	<link rel="stylesheet" href="https://www.centennialdistrict.co/bootstrap-5.3.2/css/bootstrap.css">
	    <link rel="icon" type="image/x-icon" href="https://shared.centennialdistrict.co/assets/centennial.ico" />
</head>

<body style="padding:10px">
	<div class="header">
		<img border="0" src="./images/MBsash.png" alt="MB sash" width="150" height="120" />
		<a href="#default" class="logo">Centennial - Black Feather District Merit Badge College <?php echo $CMBCollege->GetYear() ?></a>
		<div class="header-right">
			<?php
			if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
				echo "<a href='logoff.php' target='_self'>Logoff</a>";
			} else {
				echo "<a href='logon.php' target='_self'>Login</a>";
			}
			?>
			<!--<a class="active" href="#home">Home</a>-->
			<a href="mailto:richard.hall@centennialdistrict.co?subject=Merit Badge College Help website">Contact</a>
			<!--<a href="#contact">Contact</a>-->
			<!-- <a href="https://www.centennialdistrict.co/advancement/about.html">About</a> -->
		</div>
	</div>

	<section>
		<nav>
			<div>
				<ul>
					<li><a href="CounselorSelect.php">Counselors sign up</a></li>
					<li><a href="ViewSchedule.php">View College Schedule</a></li>
					<li><a href="ViewByBadges.php">View By Merit Badges</a></li>
					<li><a href="ViewByCounselor.php">View By Merit Counselors</a><br><br /></li>
					<!--
					<li><a href="#Default">View By Merit Scout</a></li><br><br/>
					-->
					<!-- Start Admin only options -->
					<?php
					// Check if the user is already logged in, if yes then redirect him to welcome page
					if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
						//echo "<button class='RoundButton' onclick='window.location.href =`logoff.php`;'>Log off</button><br><br/>";

						echo "<button class='RoundButton' style='width:250px;color:RED' onclick='window.location.href =`EnterScout.php`;'>Enter Scout Data</button><br><br/>";
						echo "<button class='RoundButton' style='width:250px;color:RED' onclick='window.location.href =`ImportScout.php`;'>Import Scout Data</button><br><br/>";
						echo "<button class='RoundButton' style='width:250px;color:RED' onclick='window.location.href =`ImportCounselor.php`;'>Import Counselor Data</button><br><br/>";
						echo "<button class='RoundButton' style='width:250px;color:RED' onclick='window.location.href =`ScoutswithBadIDs.php`;'>Bad BSA IDs</button><br><br/>";
						echo "<button class='RoundButton' style='width:250px;color:RED' onclick='window.location.href =`ViewByScoutSchedule.php`;'>Scout(s) schedule</button><br><br/>";
						echo "<button class='RoundButton' style='width:250px;color:RED' onclick='window.location.href =`ViewByCounselorSchedule.php`;'>Counselor(s) schedule</button><br><br/>";
						echo "<button class='RoundButton' style='width:250px;color:RED' onclick='window.location.href =`ViewByRoom.php`;'>Room(s) schedule</button><br><br/>";
						echo "<button class='RoundButton' style='width:250px;color:RED' onclick='window.location.href =`CreateScoutbookCSV.php`;'>Create a Scoutbook CSV file</button><br><br/>";
						echo "<button class='RoundButton' style='width:250px;color:RED' onclick='window.location.href =`ViewCollegeStats.php`;'>Report College stat's</button><br><br/>";
						echo "<button class='RoundButton' style='width:250px;color:RED' onclick='window.location.href =`DoubleKnot.php`;'>Double Knot Signup</button><br><br/>";
						echo "<button class='RoundButton' style='width:250px;color:RED' onclick='window.location.href =`CollegeDetails.php`;'>College Details</button><br><br/>";


						//echo "<br/><br/><li><a href='EnterScout.php'>Enter Scout Data</a></li><br><br/>";
						//echo "<li><a href='ScoutswithBadIDs.php'>Bad BSA IDs</a></li><br><br/>";
						//echo "<li><a href='ViewByScoutSchedule.php'>Scout(s) schedule</a></li><br><br/>";
						//echo "<li><a href='ViewByCounselorSchedule.php'>Counselor(s) schedule</a></li><br><br/>";
						//echo "<li><a href='CreateScoutbookCSV.php'>Create a Scoutbook CSV file</a></li><br><br/>";
						//echo "<li><a href='ViewCollegeStats.php'>Report College stat's</a></li><br><br/>";
						echo "<br><br/><button class='RoundButton' style='width:250px;color:RED' onclick='window.location.href =`EmailScouts.php`;'>Email Scouts Schedule</button><br><br/>";
						echo "<button class='RoundButton' style='width:250px;color:RED' onclick='window.location.href =`EmailCounselors.php`;'>Email Counselors Schedule</button><br><br/>";

						echo "<br><br/><button class='RoundButton' style='width:250px;color:RED' onclick='window.location.href =`ErrorLog.php`;'>Show Error Log</button><br><br/>";
					} else {
						//	echo "<button class='RoundButton' onclick='window.location.href =`logon.php`;'>Log on</button>";
					}
					?>
					<!-- </div> -->
				</ul>
		</nav>
		</div>
		<article>

			<br><b><?php echo $CMBCollege->GetDate($CMBCollege->getyear()); ?></b></br></br>
			<!--
			Registration link <a href="https://centennialdistrict.doubleknot.com/event/space-available/2023-centennial-and-black-feather-district-merit-badge-college/77503">Click Here</a>
			-->
			Centennial and Black Feather Districts will be holding a Merit Badge College on <?php echo $CMBCollege->GetDate($CMBCollege->getyear()); ?> at
			<?php echo $CMBCollege->GetLocation($CMBCollege->getyear()) . " " . $CMBCollege->GetAddress($CMBCollege->getyear()); ?>,
			from <?php echo $CMBCollege->GetStartTime($CMBCollege->getyear()) . " to " . $CMBCollege->GetEndTime($CMBCollege->getyear()) ?>.<br></br>
			The Districts would like to welcome all Merit Badge counselors to please consider helping with this advancement
			opportunity for the Scouts.
			The purpose of the Merit Badge College (MBC) is to offer Scouts an opportunity to meet with highly
			qualified professionals to learn and foster development of lifelong interests. Particular emphasis is given
			to Eagle Required, career and hobby oriented Merit Badges (MB) especially those MB&apos;s with a limited
			availability of counselors.

			<br></br>
			<b>Counselors:</b>
			<br></br>
			Please view the <a href='./ViewSchedule.php'>College schedule </a> to see what Merit Badges and period(s) have already been selected. You may offer a duplicate merit
			badge but just at a different time.

			To sign up to support this district event please select the counselors sign up link to the left and complete
			the sign up form.

			Once you select your name from the counselors drop down, only the Merit badges that you are approved for will be shown

			<br></br>
			<a href="https://filestore.scouting.org/filestore/pdf/512-065.pdf">A Guide for Merit Badge Counseling</a></br></br>
			<a href="https://www.scouting.org/skills/merit-badges/">BSA Merit Badge Hub</a></br></br>
			<b>Some thoughts from the <a href="https://filestore.scouting.org/filestore/pdf/33088.pdf?_gl=1*1qopu25*_ga*MjAxOTIzMzAzNS4xNjIxOTg3NTcy*_ga_20G0JHESG4*MTYyMTk4NzU3Mi4xLjEuMTYyMTk4NzcwNC4yMw..">Guide to Advancment</a></b><br></br>
			7.0.3.2 Group Instruction
			<br></br>
			It is acceptable—and sometimes desirable—for merit badges to be taught in group settings. This often occurs
			at camp and merit badge midways, fairs,
			clinics, or similar events, and even online through webinars. These can be efficient methods, and
			interactive group discussions can support learning.
			Group instruction can also be attractive to “guest experts” assisting registered and approved counselors.
			Slide shows, skits, demonstrations, panels,
			and various other techniques can also be employed, but as any teacher can attest, not everyone will learn
			all the material. Because of the importance of
			individual attention and personal learning in the merit badge program, group instruction should be focused
			on those scenarios where the benefits are
			compelling. There must be attention to each individual’s projects and fulfillment of all requirements. We
			must know that every Scout—actually and
			personally—completed them. If, for example, a requirement uses words like “show,” “demonstrate,” or
			“discuss,” then every Scout must do that. It is
			unacceptable to award badges on the basis of sitting in classrooms watching demonstrations, or remaining
			silent during discussions. It is sometimes reported
			that Scouts who have received merit badges through group instructional settings have not fulfilled all the
			requirements. To offer a quality merit badge program,
			council and district advancement committees should ensure the following are in place for all group
			instructional events. A culture is established for merit
			badge group instructional events that partial completions are acceptable expected results. A guide or
			information sheet is distributed in advance of events
			that promotes the acceptability of partials, explains how merit badges can be finished after events, lists
			merit badge prerequisites, and provides other helpful
			information that will establish realistic expectations for the number of merit badges that can be earned at
			an event.
			<br></br>
			Merit badge counselors are known to be registered and approved.
			<br></br>
			Any guest experts or guest speakers, or others assisting who are not registered and approved as merit badge
			counselors, do not accept the responsibilities of,
			or behave as, merit badge counselors, either at a group instructional event or at any other time. Their
			service is temporary, not ongoing.
			<br></br>
			Counselors agree to sign off only requirements that Scouts have actually and personally completed.
			<br></br>
			Counselors agree not to assume that stated prerequisites for an event have been completed without some level
			of evidence that the work has been done. Pictures
			and letters from other merit badge counselors or unit leaders are the best form of prerequisite
			documentation when the actual work done cannot be brought to the
			camp or site of the merit badge event.
			<br></br>
			There is a mechanism for unit leaders or others to report concerns to a council advancement committee on
			summer camp merit badge programs, group instructional
			events, and any other merit badge counseling issues— especially in instances where it is believed BSA
			procedures are not followed. See “Reporting Merit Badge
			Counseling Concerns,” 11.1.0.0.
			<br></br>
			Additional guidelines and best practices can be found in the Merit Badge Group Instruction Guide, developed
			by volunteers in conjunction with the National
			Advancement Program Team. This guide for units, districts, and councils includes several important event
			planning considerations as well as suggestions for
			evaluating the event after it is over to identify opportunities for improvement. The guide can be downloaded
			from
			<a href="https://filestore.scouting.org/filestore/pdf/512-066_web.pdf">Merit Badge Group Instruction Guide</a>.


			<article>
	</section>


</body>

</html>