<?php

session_start();
$email = isset($_SESSION['email']) ? $_SESSION['email'] : '';

?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<meta name="description" content="Enjoy the free dive log application. Scuba dive the blue wild in Fort Lauderdale and the Florida Keys." />
<meta name="keywords" content="scuba diving, dive log, free online dive log" />
<meta name="author" content="Captain Markos" />
<link type="text/css" rel="stylesheet" href="javascript/jquery-ui-1.8.21.custom/css/custom-theme/jquery-ui-1.8.21.custom.css" />
<script type="text/javascript" src="javascript/jquery-ui-1.8.21.custom/js/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="javascript/jquery-ui-1.8.21.custom/js/jquery-ui-1.8.21.custom.min.js"></script>
<script type="text/javascript" src="javascript/divelog.js"></script>
<script type="text/javascript" src="javascript/base64.js"></script>

<link type="text/css" rel="stylesheet" href="../vendor/font-awesome-4.1.0/css/font-awesome.min.css" />
<link type="text/css" rel="stylesheet" href="http://fonts.googleapis.com/css?family=Raleway:400" />
<link type="text/css" rel="stylesheet" href="../styles/normalize.css" />
<link type="text/css" rel="stylesheet" href="../styles/foundation.css" />
<link type="text/css" rel="stylesheet" href="../styles/bluewild.css" />
<link type="text/css" rel="stylesheet" href="divelog.css" />

<script type="text/javascript">

    var email = '<?php echo $email; ?>';

</script>

<title>blue wild scuba: dive log</title>
</head>
<body>

<header>
   <div class="row offset-top">
      <div class="small-12 medium-6 large-6 columns no-padding small-only-text-center">
         <h3>dive the blue wild</h3>
      </div>
      <div class="small-12 medium-6 large-6 columns contact-info small-only-text-center">
         <a href="tel:19542135067"><i class="fa fa-phone"></i> : (954) 213-5067</a>&nbsp;&nbsp;
         <br class="show-for-small-only" />
         <br class="show-for-small-only" />
         <a href="mailto:bluewildscuba@gmail.com" target="_blank">
         <i class="fa fa-envelope"></i> : bluewildscuba@gmail.com</a>
      </div>
   </div>
</header>

<!-- navbar -->
<div class="row">
    <div class="large-12 column nav">
        <ul class="inline-list">
          <li class="no-margin-left"><a href="../"><i class="fa fa-home icon-font-size"></i></a></li>
          <li><a href="/courses.html">Scuba Courses</a></li>
          <li><a href="/aboutus.html">About Us</a></li>
          <li><a href="/divelog/index.php" class="hide-for-small-only selected">Dive Log</a></li>
          <li><a href="/reefcreatures/index.php">Reef Creature Quiz</a></li>
        </ul>
    </div>
</div>

<div class="row panel-margin">
  <div class="large-12 columns no-padding">
    <div class="panel">
      <!--single row for info-->
        <div class="row">
          <div class="large-12 columns">
            <?php include('divelog.html'); ?>
            <?php include('divelog_login.html'); ?>
            <?php include('divelog_listing.html'); ?>
            <?php include('divelog_settings.html'); ?>
            <?php include('divelog_help.html'); ?>
          </div>
        </div>
      <!--end content-->
    </div>
  </div>
</div>
</body>
</html>
