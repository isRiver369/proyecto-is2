<?php
session_start();
session_destroy();

// Ahora redirige a la página de inicio pública
header("Location: menuvisitante.php"); 
exit();
?>