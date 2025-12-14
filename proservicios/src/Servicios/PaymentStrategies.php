<?php
// Interfaz común para todos los validadores
interface IPagoValidador {
    public function validar($datos, $archivos);
}

// Estrategia para Tarjeta
class ValidadorTarjeta implements IPagoValidador {
    public function validar($datos, $archivos) {
        $errores = [];
        $numTarjeta = str_replace(' ', '', $datos['cardNumber'] ?? '');
        
        if (!preg_match('/^[0-9]{16}$/', $numTarjeta)) {
            $errores[] = "La tarjeta debe tener 16 dígitos.";
        }
        if (empty($datos['cardName'])) {
            $errores[] = "El nombre en la tarjeta es obligatorio.";
        }
        if (!preg_match('/^[0-9]{3,4}$/', $datos['cvv'] ?? '')) {
            $errores[] = "CVV inválido.";
        }
        return $errores;
    }
}

// Estrategia para Transferencia
class ValidadorTransferencia implements IPagoValidador {
    public function validar($datos, $archivos) {
        $errores = [];
        if (empty($archivos['voucher']['name'])) {
            $errores[] = "Debes subir la foto del comprobante.";
        }
        return $errores;
    }
}

// Fábrica para elegir la estrategia correcta (Factory Pattern)
class ValidadorFactory {
    public static function obtenerValidador($metodo) {
        switch ($metodo) {
            case 'tarjeta':
                return new ValidadorTarjeta();
            case 'transferencia':
                return new ValidadorTransferencia();
            default:
                throw new Exception("Método de pago no válido.");
        }
    }
}
?>