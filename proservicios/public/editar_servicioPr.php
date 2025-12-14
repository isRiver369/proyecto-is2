<?php

require_once '../src/Modelos/Servicio.php';

class EditarServicioAc
{
    private Servicio $servicio;

    public function __construct(PDO $db, private int $usuario_id)
    {
        $this->servicio = new Servicio($db);
    }

    public function execute(array $data): void
    {
        $actualizado = $this->servicio->editar(
            $data['servicio_id'],
            $this->usuario_id,
            $data['nombre'],
            $data['precio'],
            $data['descripcion'],
            $data['horario'],
            $data['politicas'],
            $data['cupos'],
            $data['fecha_inicio'],
            $data['fecha_fin'],
            $data['descripcion_breve'],
            $data['categoria_id'],
            $data['modalidad'],
            $data['ubicacion']
        );

        $_SESSION['mensaje'] = $actualizado
            ? "Servicio actualizado con éxito."
            : "Error al actualizar el servicio.";
    }
}