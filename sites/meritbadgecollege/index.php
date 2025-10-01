<?php
  // Secure session start
  if (session_status() === PHP_SESSION_NONE) {
    session_start([
      'cookie_httponly' => true,
      'use_strict_mode' => true,
      'cookie_secure' => isset($_SERVER['HTTPS'])
    ]);
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

  include_once('CMBCollege.php');

$CMBCollege = CMBCollege::getInstance();


?>
<!DOCTYPE html>
<html lang="en">

<head>
  <?php include("header.php"); ?>
  <meta name="description" content="index.php">

</head>

<body>
  <!-- Responsive navbar-->
  <nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top">
    <div class="container px-lg-4">
      <a class="navbar-brand" href="#!">Centennial District Merit Badge College</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation"><span class="navbar-toggler-icon"></span></button>
      <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
          <li class="nav-item"><a class="nav-link active" aria-current="page" href="https://mbcollege.centennialdistrict.co/index.php">Home</a></li>
          <!-- <li class="nav-item"><a class="nav-link" href="#!">About</a></li> -->
          <li class="nav-item"><a class="nav-link" href="mailto:richard.hall@centennialdistrict.co?subject=Merit Badge College">Contact</a></li>
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



  <div class="container-fluid">
    <div class="row flex-nowrap">
      <!-- Include the common side nav bar -->
      <?php include 'navbar.php'; ?>
      <div class="col py-3">
        <h3 style="text-align: center" ;>Centennial Districts will be holding a Merit Badge College on <?php echo $CMBCollege->GetDate($CMBCollege->getyear()); ?> </br>at
          <?php echo $CMBCollege->GetLocation($CMBCollege->getyear()) . " " . $CMBCollege->GetAddress($CMBCollege->getyear()); ?>,
          from <?php echo $CMBCollege->GetStartTime($CMBCollege->getyear()) . " to " . $CMBCollege->GetEndTime($CMBCollege->getyear()) ?>.</h3>
          <hr/>
        <p>The Districts would like to welcome all Merit Badge counselors to please consider helping with this advancement
          opportunity for the Scouts.</p>
        <p>The purpose of the Merit Badge College (MBC) is to offer Scouts an opportunity to meet with highly
          qualified professionals to learn and foster development of lifelong interests. Particular emphasis is given
          to Eagle Required, career and hobby oriented Merit Badges (MB) especially those MB&apos;s with a limited
          availability of counselors.</p>

        <h4>Counselors:</h4>
        <p>Please view the <a href='./ViewSchedule.php'>College schedule </a> to see what Merit Badges and period(s) have already been selected. You may offer a duplicate merit
          badge but just at a different time.</p>

        <p>To sign up to support this district event please select the counselors sign up link to the left and complete
          the sign up form.</p>

        <p>Once you select your name from the counselors drop down, only the Merit badges that you are approved for will be shown</p>

        <ul>
          <li>
          <a href="https://filestore.scouting.org/filestore/pdf/512-065.pdf">A Guide for Merit Badge Counseling</a>
          </li>
          <li>
          <a href="https://www.scouting.org/skills/merit-badges/">BSA Merit Badge Hub</a>
          </li>
          <li>
          <b>Some thoughts from the <a href="https://filestore.scouting.org/filestore/pdf/33088.pdf?_gl=1*1qopu25*_ga*MjAxOTIzMzAzNS4xNjIxOTg3NTcy*_ga_20G0JHESG4*MTYyMTk4NzU3Mi4xLjEuMTYyMTk4NzcwNC4yMw..">Guide to Advancment</a></b>
          </li>
        </ul>
        
        
       
        <p>&nbsp;</p>
        <iframe src="https://www.google.com/maps/embed?pb=!1m14!1m12!1m3!1d3856.1779803017257!2d-104.80239407026473!3d39.71812148920198!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!5e1!3m2!1sen!2sus!4v1731870730623!5m2!1sen!2sus" width="800" height="650" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
        
        
        <h4>7.0.3.2 Group Instruction</h4>
        <p>It is acceptable—and sometimes desirable—for merit badges to be taught in group settings. This often occurs
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
          an event.</p>
        <p>Merit badge counselors are known to be registered and approved.</p>
        <p>Any guest experts or guest speakers, or others assisting who are not registered and approved as merit badge
          counselors, do not accept the responsibilities of,
          or behave as, merit badge counselors, either at a group instructional event or at any other time. Their
          service is temporary, not ongoing.</p>
        <p>Counselors agree to sign off only requirements that Scouts have actually and personally completed.</p>
        <p>Counselors agree not to assume that stated prerequisites for an event have been completed without some level
          of evidence that the work has been done. Pictures
          and letters from other merit badge counselors or unit leaders are the best form of prerequisite
          documentation when the actual work done cannot be brought to the
          camp or site of the merit badge event.</p>
        <p>There is a mechanism for unit leaders or others to report concerns to a council advancement committee on
          summer camp merit badge programs, group instructional
          events, and any other merit badge counseling issues— especially in instances where it is believed BSA
          procedures are not followed. See “Reporting Merit Badge
          Counseling Concerns,” 11.1.0.0.</p>
        <p>Additional guidelines and best practices can be found in the Merit Badge Group Instruction Guide, developed
          by volunteers in conjunction with the National
          Advancement Program Team. This guide for units, districts, and councils includes several important event
          planning considerations as well as suggestions for
          evaluating the event after it is over to identify opportunities for improvement. The guide can be downloaded
          from
          <a href="https://filestore.scouting.org/filestore/pdf/512-066_web.pdf">Merit Badge Group Instruction Guide</a>.
        </p>

      </div>
    </div>
  </div>





  <?php include("Footer.php"); ?>
</body>

</html>