<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title><?php if(isset($title)){ echo $title; }?></title>
    <meta name="description" content="Bootstrap Tab + Fixed Sidebar Tutorial with HTML5 / CSS3 / JavaScript">
    <meta name="author" content="Untame.net">

    <script src="http://ajax.googleapis.com/ajax/libs/jquery/2.0.0/jquery.min.js"></script>
    <script src="../../Libs/bootstrap.min.js"></script>
    <link href="../../Libs/bootstrap.min.css" rel="stylesheet" media="screen">
    <style type="text/css">
        body { background: url(../../Libs/bglight.png); }
        .hero-unit { background-color: #fff; }
        .center { display: block; margin: 0 auto; }
    </style>
</head>

<body>
<div class="navbar navbar-fixed-top navbar-inverse">
    <div class="navbar-inner">
        <div class="container">
            <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </a>
            <a class="brand">Conference Scheduler</a>
            <div class="nav-collapse collapse">
                <ul class="nav pull-right">
                    <li><a href="/user/register">Register</a></li>
                    <li class="divider-vertical"></li>
                    <li><a href="/user/login">Login</a></li>
                    <li class="divider-vertical"></li>
                    <li><a href="/user/logout"><?php if (isset($GLOBALS['httpContext'])) { $httpContext = $GLOBALS['httpContext']; if ($httpContext->getLoggedUser() != null) { echo "Logout"; }; } ?></a></li>
                </ul>
            </div>
        </div>
    </div>
</div>
<?php
/*
* Error modal
*/
if (isset( $_SESSION['error'] )) {
    include_once( 'error.php' );
    unset( $_SESSION[ 'error' ] );
}
?>