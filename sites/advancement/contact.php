<!DOCTYPE html>
<html lang="en">

<head>
  <?php include("header.php"); ?>
</head>

<body>
  <header id="header" class="header sticky-top">

    <!-- Responsive navbar-->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
      <div class="container px-lg-5">
        <a class="navbar-brand" href="#!">Centennial District Advancement</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation"><span class="navbar-toggler-icon"></span></button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
          <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
            <li class="nav-item"><a class="nav-link active" aria-current="page" href="./index.php">Home</a></li>
            <!-- <li class="nav-item"><a class="nav-link" href="#!">About</a></li> -->
            <li class="nav-item"><a class="nav-link" href="contact.php">Contact</a></li>
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
        <?php include 'sidebar.php'; ?>
        <div class="col py-3">
          <div class="container px-md-3">
            <div class="p-3 p-md-3 bg-light rounded-2 text-center">
              <div class="m-3 m-lg-3">
                <!-- <a class="btn btn-primary btn-lg" href="./advancement_index.php">Advancement Data</a> -->
                </hr>
                <div class="col-lg-9">
                  <h2>Contact</h2>
                  <p>If you have any questions or commenst please complete the form below and we will get back to you.</p>
                  <form action="./send_email.php" method="post" class="php-email-form" data-aos="fade-up" data-aos-delay="200">
                    <div class="row gy-4">

                      <div class="col-md-6">
                        <input type="text" name="name" class="form-control" placeholder="Your Name" required="">
                      </div>

                      <div class="col-md-6 ">
                        <input type="email" class="form-control" name="email" placeholder="Your Email" required="">
                      </div>

                      <div class="col-md-12">
                        <input type="text" class="form-control" name="subject" placeholder="Subject" required="">
                      </div>

                      <div class="col-md-12">
                        <textarea class="form-control" name="message" rows="10" style="height:100%;" placeholder="Message" required=""></textarea>
                      </div>

                      <div class="col-md-12 text-center">
                        <div class="loading"></div>
                        <div class="error-message"></div>
                        <div class="sent-message"></div>
                        <button type="submit" class="btn btn-primary btn-sm">Send Message</button>
                      </div>
                    </div>
                  </form>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </header>


  <!-- Vendor JS Files -->
  <script src="assets/vendor/bootstrap/js/bootstrap.bundle.js"></script>
  <script src="assets/vendor/php-email-form/validate.js"></script>
  <script src="assets/lib/bootstrap/bootstrap.js"></script>



</body>

</html>