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

// Define variables and initialize with empty values
$FirstName = $LastName = $Email = "";
$username = $password = $confirm_password = "";
$username_err = $password_err = $confirm_password_err = "";
$FirstName_err = $LastName_err = $Email_err = "";

// Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Validate First Name
    if (empty(trim($_POST["FirstName"]))) {
        $FirstName_err = "Please enter a First Name.";
    } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', trim($_POST["FirstName"]))) {
        $FirstName_err = "First can only contain letters, numbers, and underscores.";
    }
            
    // Validate Last Name
    if (empty(trim($_POST["LastName"]))) {
        $LastName_err = "Please enter a Last Name.";
    } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', trim($_POST["LastName"]))) {
        $LastName_err = "Username can only contain letters, numbers, and underscores.";
    }

    // Validate eamil
    if (empty(trim($_POST["Email"]))) {
        $Email_err = "Please enter a Email.";
    } elseif (!filter_var(trim($_POST["Email"]),FILTER_VALIDATE_EMAIL)) {
        $Email_err = "Emails can only conain letters and white space allowed.";
    }


    // Validate username
    if (empty(trim($_POST["username"]))) {
        $username_err = "Please enter a username.";
    } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', trim($_POST["username"]))) {
        $username_err = "Username can only contain letters, numbers, and underscores.";
    } else {
        // Prepare a select statement
        $sql = "SELECT id FROM users WHERE username = ?";

        if ($stmt = mysqli_prepare($classESC->getDbConn(), $sql)) {
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "s", $param_username);

            // Set parameters
            $param_username = trim($_POST["username"]);

            // Attempt to execute the prepared statement
            if (mysqli_stmt_execute($stmt)) {
                /* store result */
                mysqli_stmt_store_result($stmt);

                if (mysqli_stmt_num_rows($stmt) == 1) {
                    $username_err = "This username is already taken.";
                } else {
                    $username = trim($_POST["username"]);
                }
            } else {
                echo "Oops! Something went wrong. Please try again later.";
            }

            // Close statement
            mysqli_stmt_close($stmt);
        }
    }

    // Validate password
    if (empty(trim($_POST["password"]))) {
        $password_err = "Please enter a password.";
    } elseif (strlen(trim($_POST["password"])) < 6) {
        $password_err = "Password must have atleast 6 characters.";
    } else {
        $password = trim($_POST["password"]);
    }

    // Validate confirm password
    if (empty(trim($_POST["confirm_password"]))) {
        $confirm_password_err = "Please confirm password.";
    } else {
        $confirm_password = trim($_POST["confirm_password"]);
        if (empty($password_err) && ($password != $confirm_password)) {
            $confirm_password_err = "Password did not match.";
        }
    }

    // Check input errors before inserting in database
    if (empty($username_err) && empty($password_err) && empty($confirm_password_err) &&
        empty($FirstName_err) && empty($LastName_err) && empty($Email_err)) {

        // Prepare an insert statement
        $sql = "INSERT INTO users (FirstName, LastName, Email, username, password) VALUES (?, ?, ?, ?, ?)";

        if ($stmt = mysqli_prepare($classESC->getDbConn(), $sql)) {
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "sssss", $param_FirstName, $param_LastName, $param_Email, $param_username, $param_password);

            // Set parameters
            $param_FirstName = trim($_POST["FirstName"]);
            $param_LastName = trim($_POST["LastName"]);
            $param_Email = trim($_POST["Email"]);
            $param_username = trim($_POST["username"]);
            $param_password = password_hash($password, PASSWORD_DEFAULT); // Creates a password hash

            // Attempt to execute the prepared statement
            if (mysqli_stmt_execute($stmt)) {
                // 
                $msg = "Once your account has been enabled you will be able to log in.";
                $classESC->function_alert($msg);
                $_POST['name'] = 'Admin';
                $_POST['email'] = 'admin@gccesc.org';
                $_POST['subject'] = 'New User';
                $_POST['message'] =  sprintf("New GCCESC.org registration, for %s %s (%s) at %s\n", $param_FirstName, $param_LastName, $param_username, Date('Y-m-d H:i:s'));
                $classESC->GotoURL('./NewUser.php');
                $str = sprintf("New GCCESC.org registration, for %s %s (%s) at %s\n", $param_FirstName, $param_LastName, $param_username, Date('Y-m-d H:i:s'));
                error_log($str, 1, "admin@gccesc.org");
                $classESC->GotoURL("../index.php");
            } else {
                echo "Oops! Something went wrong. Please try again later.";
            }

            // Close statement
            mysqli_stmt_close($stmt);
        }
    }

    // Close connection
    mysqli_close($classESC->getDbConn());
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
    <link rel="icon" type="image/x-icon" href="assets/centennial.ico" />
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
                    <li class="nav-item"><a class="nav-link active" aria-current="page" href="./index.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="#!">About</a></li>
                    <li class="nav-item"><a class="nav-link" href="#!">Contact</a></li>
                    <li class="nav-item"><a class="nav-link" href="./logon.php">Log on</a></li>
                </ul>
            </div>
        </div>
    </nav>

        <center>
        <header class="wrapper-logon">
        <div class="my_div">
                <div class="wrapper">
                    <h2>Sign Up</h2>
                    <p>Please fill this form to create an account.</p>
                    </br>
                    <p>Acounts are reserved for those involved Emergency Service Corp.</p>
                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                    <div class="form-group">
                            <label>First Name</label>
                            <input type="text" name="FirstName" class="form-control <?php echo (!empty($FirstName_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $FirstName; ?>">
                            <span class="invalid-feedback"><?php echo $FirstName_err; ?></span>
                        </div>
                        <div class="form-group">
                            <label>Last Name</label>
                            <input type="text" name="LastName" class="form-control <?php echo (!empty($LastName_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $LastName; ?>">
                            <span class="invalid-feedback"><?php echo $LastName_err; ?></span>
                        </div>
                        <div class="form-group">
                            <label>Email</label>
                            <input type="text" name="Email" class="form-control <?php echo (!empty($Email_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $Email; ?>">
                            <span class="invalid-feedback"><?php echo $Email_err; ?></span>
                        </div>
                        <div class="form-group">
                            <label>Username</label>
                            <input type="text" name="username" class="form-control <?php echo (!empty($username_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $username; ?>">
                            <span class="invalid-feedback"><?php echo $username_err; ?></span>
                        </div>
                        <div class="form-group">
                            <label>Password</label>
                            <input type="password" name="password" class="form-control <?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $password; ?>">
                            <span class="invalid-feedback"><?php echo $password_err; ?></span>
                        </div>
                        <div class="form-group">
                            <label>Confirm Password</label>
                            <input type="password" name="confirm_password" class="form-control <?php echo (!empty($confirm_password_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $confirm_password; ?>">
                            <span class="invalid-feedback"><?php echo $confirm_password_err; ?></span>
                        </div>
                        <div class="form-group">
                            <input type="submit" class="btn btn-primary" value="Submit">
                            <input type="reset" class="btn btn-secondary ml-2" value="Reset">
                        </div>
                        <p>Already have an account? <a href="logon.php">Login here</a>.</p>
                    </form>
                </div>
            </div>
        </center>
    </header>
    <!-- Footer-->
    <footer class="py-5 bg-dark">
        <div class="container">
            <p class="m-0 text-center text-white">Copyright &copy; gccesc.org 2024</p>
        </div>
    </footer>
    <!-- Bootstrap core JS-->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Core theme JS-->
    <script src="js/scripts.js"></script>

</body>

</html>