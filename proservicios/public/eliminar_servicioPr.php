<?php

require_once '../src/Modelos/Servicio.php';
require_once '../src/Servicios/ReservaService.php';

class EliminarServicioAc
{
    private Servicio $servicio;
    private ReservaService $reservas;

    public function __construct(PDO $db, private int $usuario_id)
    {
        $this->servicio = new Servicio($db);
        $this->reservas = new ReservaService($db);
    }

    public function execute(array $data): void
    {
        $servicio_id = $data['servicio_id'];

        if ($this->reservas->contarReservasPorServicio($servicio_id) > 0) {
            $_SESSION['error_eliminar'] =
                "No puedes eliminar un servicio que ya tiene reservas activas.";
            return;
        }

        $eliminado = $this->servicio->eliminar($servicio_id, $this->usuario_id);

        $_SESSION['mensaje'] = $eliminado
            ? "Servicio eliminado."
            : "Error al eliminar.";
    }
}