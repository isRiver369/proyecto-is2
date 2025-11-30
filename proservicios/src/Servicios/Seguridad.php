<?php
// Archivo: src/Servicios/Seguridad.php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

class Seguridad {
    
    // Método 1: Solo verifica que haya iniciado sesión (cualquier rol)
    public static function verificarSesion() {
        if (!isset($_SESSION['usuario_id'])) {
            header("Location: login.php");
            exit();
        }
    }

    // Método 2: Verifica sesión Y un rol específico
    public static function requerirRol($rolRequerido) {
        // 1. Primero verificamos que esté logueado
        self::verificarSesion();

        // 2. Verificamos si el rol coincide
        // (Asumimos que $_SESSION['rol'] se guardó en el login)
        if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== $rolRequerido) {
            
            // Si no tiene permiso, lo "regresamos" a su lugar correspondiente o al login
            // Esto evita bucles de redirección
            if (isset($_SESSION['rol']) && $_SESSION['rol'] === 'cliente') {
                header("Location: menucliente.php");
            } elseif (isset($_SESSION['rol']) && $_SESSION['rol'] === 'administrador') {
                header("Location: admin.php"); 
            } else {
                header("Location: login.php");
            }
            exit();
        }
    }
}
?>