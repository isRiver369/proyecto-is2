<?php
session_start();
session_destroy();

// Matar la cookie (poniendo la fecha en el pasado)
setcookie('proservicios_remember', '', time() - 3600, "/");

header("Location: login.php");
exit();
?>