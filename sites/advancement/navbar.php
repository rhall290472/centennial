<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container px-lg-5">
    <?php
    if(isset($navbarTitle)){ ?>
      <a class="navbar-brand" href="#!"><?php echo $navbarTitle ?></a> <?php
    }else{ ?>
      <a class="navbar-brand" href="#!">Centennial District Advancment</a> <?php
    } ?>
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