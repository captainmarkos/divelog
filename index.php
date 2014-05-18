<?php

session_start();
$email = isset($_SESSION['email']) ? $_SESSION['email'] : '';


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta name="description" content="Free Online Scuba Diver Logbook" />
<meta name="keywords" content="dive log, free dive log, free online diver logbook, free diver logbook" />
<title>Blue Wild - Dive Log</title>
<link type="text/css" rel="stylesheet" href="divelog.css" />
<link type="text/css" rel="stylesheet" href="javascript/jquery-ui-1.8.21.custom/css/custom-theme/jquery-ui-1.8.21.custom.css" />
<script type="text/javascript" src="javascript/jquery-ui-1.8.21.custom/js/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="javascript/jquery-ui-1.8.21.custom/js/jquery-ui-1.8.21.custom.min.js"></script>
<script type="text/javascript" src="javascript/divelog.js"></script>
<script type="text/javascript" src="javascript/base64.js"></script>

<link rel="stylesheet" href="../vendor/font-awesome-4.1.0/css/font-awesome.min.css">
<link href='http://fonts.googleapis.com/css?family=Raleway:700,400,300,200' rel='stylesheet' type='text/css'>
<link rel="stylesheet" href="../styles/bluewild.css">
<link rel="stylesheet" href="../styles/normalize.css">

<script type="text/javascript">

    var email = '<?php echo $email; ?>';

</script>
</head>
<body>

<header>
    <div class="header-wrapper">
      <h1 id="logo">blue wild us</h1>
      <div class="contact-info">
         <a href="tel:19542135067">
            <i class="fa fa-phone"></i> : (954) 213-5067&nbsp;&nbsp;
         </a>
         <br class="rw-break" /> <a href="mailto:bluewildscuba@gmail.com" target="_blank">
            <i class="fa fa-envelope"></i> : bluewildscuba@gmail.com
         </a>
      </div>
    </div>
</header>

<div class="main-wrapper">
    <nav>
      <ul>
        <li><a href="../"><i class="fa fa-home icon-font-size"></i></a></li>
        <li><a href="../#/courses">Scuba Courses</a></li>
        <li><a href="../#/aboutus">About Us</a></li>
        <li><a class="selected" href="divelog/index.php">Dive Log</a></li>
      </ul>
    </nav>

    <section>
        <div class="main-content">
        <?php include('divelog.html'); ?>
        <?php include('divelog_login.html'); ?>
        <?php include('divelog_listing.html'); ?>
        <?php include('divelog_settings.html'); ?>
        <?php include('divelog_help.html'); ?>
        </div>
    </section>
</div>
</body>
</html>
