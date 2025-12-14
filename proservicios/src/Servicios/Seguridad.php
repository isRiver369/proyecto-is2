<?php
// src/Servicios/Seguridad.php

class Seguridad {
    
    public static function iniciarSesionSiNoExiste() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public static function requerirRol($rolRequerido) {
        self::iniciarSesionSiNoExiste();

        // 1. Si no hay sesión, intentamos revivirla con la Cookie
        if (!isset($_SESSION['usuario_id'])) {
            self::intentarLoginConCookie();
        }

        // 2. Si AÚN no hay sesión después de intentar con la cookie, adiós.
        if (!isset($_SESSION['usuario_id'])) {
            header("Location: login.php");
            exit();
        }

        // 3. Verificar Rol (Lógica normal)
        // Permitimos acceso si es el rol correcto O si es administrador (el admin suele poder ver todo)
        if ($_SESSION['rol'] !== $rolRequerido && $_SESSION['rol'] !== 'administrador') {
            // Si el cliente intenta entrar a panel admin, lo mandamos a su home
            if ($_SESSION['rol'] === 'cliente') {
                header("Location: menucliente.php");
            } else {
                header("Location: login.php");
            }
            exit();
        }
    }

    private static function intentarLoginConCookie() {
        if (isset($_COOKIE['proservicios_remember'])) {
            // Decodificamos el token (ID:Rol)
            $datos = explode(':', base64_decode($_COOKIE['proservicios_remember']));
            
            if (count($datos) === 2) {
                $_SESSION['usuario_id'] = $datos[0];
                $_SESSION['rol'] = $datos[1];
            
            }
        }
    }
}
?>