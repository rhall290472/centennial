<?php
// Load configuration
if (file_exists(__DIR__ . '/../../config/config.php')) {
  require_once __DIR__ . '/../../config/config.php';
} else {
  die('An error occurred. Please try again later.');
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
  <?php load_template('/src/Templates/header.php'); ?>
</head>

<body>
  <header id="header" class="header sticky-top">
    <?php load_template('/src/Templates/navbar.php'); ?>

    <div class="container-fluid">
      <div class="row flex-nowrap">
        <?php load_template('/src/Templates/sidebar.php'); ?>
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