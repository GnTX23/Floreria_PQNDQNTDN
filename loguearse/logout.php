<?php
session_start();
session_destroy();
header("Location: /codigo/loguearse/login.php");
exit();
?>