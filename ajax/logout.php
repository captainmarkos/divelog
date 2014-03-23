<?php

session_start();
$_SESSION['email'] = '';
unset($_SESSION['email']);
session_destroy();
exit();

?>
