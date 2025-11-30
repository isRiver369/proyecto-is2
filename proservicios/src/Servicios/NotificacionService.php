<?php
// Archivo: src/Servicios/NotificacionService.php

class NotificacionService {
    private $logFile;

    public function __construct() {
        // Guardaremos los emails simulados en una carpeta 'logs'
        $this->logFile = __DIR__ . '/../../logs/emails.txt';
        
        // Crear carpeta logs si no existe
        if (!file_exists(dirname($this->logFile))) {
            mkdir(dirname($this->logFile), 0777, true);
        }
    }

    public function enviarEmail($destinatario, $asunto, $mensaje) {
        $fecha = date('Y-m-d H:i:s');
        $contenido = "--------------------------------------------------\n";
        $contenido .= "FECHA: $fecha\n";
        $contenido .= "PARA: $destinatario\n";
        $contenido .= "ASUNTO: $asunto\n";
        $contenido .= "MENSAJE:\n$mensaje\n";
        $contenido .= "--------------------------------------------------\n\n";

        // Escribir en el archivo (simulación de envío)
        file_put_contents($this->logFile, $contenido, FILE_APPEND);
    }
}
?>