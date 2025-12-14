<?php

require_once '../src/Modelos/Servicio.php';

class CrearServicioAc
{
    private Servicio $servicio;

    public function __construct(PDO $db, private int $usuario_id)
    {
        $this->servicio = new Servicio($db);
    }

    public function execute(array $data): void
    {
        $creado = $this->servicio->crear(
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

        $_SESSION['mensaje'] = $creado
            ? "¡Servicio creado con éxito!"
            : "Error al crear servicio.";
    }
}