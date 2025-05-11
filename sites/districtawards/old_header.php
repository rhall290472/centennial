<body style="padding:10px;background-color:#10c2f8"">
	<div class="header">
    <center>
        <a href="#default" class="logo">Centennial District Award Data</a>
    </center>
    <div class="header-left">
    </div>
    <div class="header-right">
        <?php
        if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
            echo "<a href='logoff.php' target='_self'>Logoff</a>";
        } else {
            echo "<a href='logon.php' target='_self'>Login</a>";
        }
        ?>

        <!--<a class="active" href="#home">Home</a>-->
        <a href="mailto:richard.hall@centennial.co?subject=District Awards website">Contact</a>
        <a href="https://centennialdistrict.co/advancement/about.html">About</a>
    </div>
    </br></br>
        <div class="Mytooltip">
            <span class="Mytooltiptext">District forms for Awards</span>
            <a href='./DocsPage.php'>Forms</a>
        </div>
        <div class="Mytooltip">
            <span class="Mytooltiptext">Submit a nomination</span>
            <a href='./OnLineNomination.php'>Online Nomination</a>
        </div>

    <?php
    if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
    ?>
        <?php
        // If Current user is Admin, show extra selections.
        if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true && $_SESSION['role'] == "Admin") {
        ?>
            <div class="Mytooltip">
                <span class="Mytooltiptext">Import Member ID from YPT report</span>
                <a href='./Import.php'>Member ID</a>
            </div>
            <div class="Mytooltip">
                <span class="Mytooltiptext">Add or Edit nominee information</span>
                <a href='./NomineePage.php'>Nominee</a>
            </div>
            <div class="Mytooltip">
                <span class="Mytooltiptext">List of avaiable Reports</span>
                <a href='./Reports.php' target='_self'>Reports</a>
            </div>
    <?php
        }
    }
    ?>
    </div>
</body>