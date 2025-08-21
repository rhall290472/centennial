<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
    <div class="container-fluid">
        <a class="navbar-brand" href="?page=home"><?php echo PAGE_TITLE; ?></a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link <?php echo $page === 'home' ? 'active' : ''; ?>" href="?page=home">Home</a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle <?php echo in_array($page, ['pack-summary', 'pack-below-goal', 'pack-meeting-goal']) ? 'active' : ''; ?>"
                        href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Scout Awards
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="?page=jl-year&SubmitAward=29">Junior Leader of the
                                Year</a></li>
                    </ul>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle <?php echo in_array($page, ['troop-summary', 'troop-below-goal', 'troop-meeting-goal']) ? 'active' : ''; ?>"
                        href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Pack Awards
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="?page=cm-year&SubmitAward=4">Cubmaster of the Year</a></li>
                        <li><a class="dropdown-item" href="?page=rcm-year&SubmitAward=5">Rookie Cubmaster of the
                                Year</a></li>
                        <li><a class="dropdown-item" href="?page=dl-year&SubmitAward=12">Den Leader of the Year</a></li>
                        <li><a class="dropdown-item" href="?page=rdl-year&SubmitAward=13">Rookie Den Leader of the
                                Year</a></li>
                        <li><a class="dropdown-item" href="?page=pcm-year&SubmitAward=20">Pack Committee Member of the
                                Year</a></li>
                        <li><a class="dropdown-item" href="?page=rpcm-year&SubmitAward=22">Rookie Pack Committee Member
                                of the Year</a></li>
                        <li><a class="dropdown-item" href="?page=outleader&SubmitAward=14">Outstanding Leaders</a></li>
                        <li><a class="dropdown-item" href="?page=keyscout&SubmitAward=15">Key Scouters</a></li>
                    </ul>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle <?php echo in_array($page, ['adv-report', 'membership-report']) ? 'active' : ''; ?>"
                        href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Troop Awards
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="?page=sm-year&SubmitAward=2">Scoutmaster of the Year</a></li>
                        <li><a class="dropdown-item" href="?page=rsm-year&SubmitAward=3">Rookie Scoutmaster of the
                                Year</a></li>
                        <li><a class="dropdown-item" href="?page=tcm-year&SubmitAward=8">Troop Committee Member of teh
                                Year</a></li>
                        <li><a class="dropdown-item" href="?page=rtcm-year&SubmitAward=9">Rookie Troop Committee Member
                                of teh Year</a></li>
                        <li><a class="dropdown-item" href="?page=outleader&SubmitAward=14">Outstanding Leaders</a></li>
                        <li><a class="dropdown-item" href="?page=keyscout&SubmitAward=15">Key Scouters</a></li>
                    </ul>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle <?php echo in_array($page, ['adv-report', 'membership-report']) ? 'active' : ''; ?>"
                        href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Crew Awards
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="?page=ca-year&SubmitAward=6">Crew Advisor</a></li>
                        <li><a class="dropdown-item" href="?page=rca-year&SubmitAward=7">Rookie Crew Advisor</a></li>
                        <li><a class="dropdown-item" href="?page=skip-year&SubmitAward=48">Skipper</a></li>
                        <li><a class="dropdown-item" href="?page=rskip-year&SubmitAward=49">Rookie Skipper</a></li>
                        <li><a class="dropdown-item" href="?page=cscm-year&SubmitAward=50">Crew/Ship Committee
                                Member</a></li>
                        <li><a class="dropdown-item" href="?page=rcssm-year&SubmitAward=30">Rookie Crew/Ship Committee
                                Member</a></li>
                        <li><a class="dropdown-item" href="?page=outleader&SubmitAward=14">Outstanding Leaders</a></li>
                        <li><a class="dropdown-item" href="?page=keyscout&SubmitAward=15">Key Scouters</a></li>
                    </ul>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle <?php echo in_array($page, ['adv-report', 'membership-report']) ? 'active' : ''; ?>"
                        href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        District Awards
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="?page=beaward&SubmitAward=16">Bald Eagle Award</a></li>
                        <li><a class="dropdown-item" href="?page=dam&SubmitAward=1">District Award of Merit</a></li>
                        <li><a class="dropdown-item" href="?page=dco-year&SubmitAward=18">District Commissioner of the
                                Year</a></li>
                        <li><a class="dropdown-item" href="?page=rdcm-year&SubmitAward=19">Rookie District Commissioner
                                of the Year</a></li>
                        <li><a class="dropdown-item" href="?page=dcm-year&SubmitAward=25">District Committee Member of
                                the Year</a></li>
                    </ul>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle <?php echo in_array($page, ['adv-report', 'membership-report']) ? 'active' : ''; ?>"
                        href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Other Awards
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="?page=fofs&SubmitAward=17">Friends of Scouting</a></li>
                    </ul>
                </li>
                <?php if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true): ?>
                <li class="nav-item">
                    <a class="nav-link" href="?page=logout">Logout</a>
                </li>
                <?php else: ?>
                <li class="nav-item">
                    <a class="nav-link <?php echo $page === 'login' ? 'active' : ''; ?>" href="?page=login">Login</a>
                </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>