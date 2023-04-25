<?php
    $page_title = isset($page_title) ? $page_title : "";

    if (session_status() == PHP_SESSION_NONE)
    {
        session_start();
    }

?>
<nav class="navbar sticky-top navbar-expand-md navbar-dark"
     style="background-color: #0047ab;">
    <a class="navbar-brand" href=<?= dirname($_SERVER['PHP_SELF']) ?>>
        <img src="resources/happy_face_icon.png" width="30" height="30"
             class="d-inline-block align-top" alt="">
        <?= ET_HOME_PAGE ?>
    </a>
    <button class="navbar-toggler" type="button" data-toggle="collapse"
            data-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup"
            aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
        <div class="navbar-nav">
        <a class="nav-item nav-link<?= $page_title == ET_HOME_PAGE ? ' active' : '' ?>"
               href=<?= dirname($_SERVER['PHP_SELF']) ?>>Home </a>
            <?php if (isset($_SESSION['user_id'])): ?>
                <a class="nav-item nav-link<?= $page_title == ET_ADD_PAGE ? ' active' : '' ?>" href="logexercise.php">Log Exercise</a>
                <a class="nav-item nav-link<?= $page_title == ET_DETAILS_PAGE ? ' active' : '' ?>" href="viewprofile.php?id=<?=$_SESSION['user_id']?>">View/Edit Profile</a>
                
            <?php endif; ?>
            <?php if (!isset($_SESSION['user_name'])): ?>
                <a class="nav-item nav-link<?= $page_title == ET_LOGIN_PAGE ? ' active' : '' ?>" href="login.php">Login</a>
                <a class="nav-item nav-link<?= $page_title == ET_SIGNUP_PAGE ? ' active' : '' ?>" href="signup.php">Sign Up</a>
            <?php else: ?>
                <a class='nav-item nav-link' href='logout.php'>Logout (<?=$_SESSION['user_name'] ?>)</a>
            <?php endif; ?>
        </div>
    </div>
</nav>
