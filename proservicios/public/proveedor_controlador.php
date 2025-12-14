<?php

require_once 'crear_servicioPr.php';
require_once 'editar_servicioPr.php';
require_once 'eliminar_servicioPr.php';
require_once 'actualizar_perfil.php';

class ProveedorControlador
{
    private array $acciones;

    public function __construct(private PDO $db, private int $usuario_id)
    {
        $this->acciones = [
            'crear_servicio'     => new CrearServicioAc($db, $usuario_id),
            'editar_servicio'    => new EditarServicioAc($db, $usuario_id),
            'eliminar_servicio'  => new EliminarServicioAc($db, $usuario_id),
            'actualizar_perfil'  => new ActualizarPerfilAc($db, $usuario_id),
        ];
    }

    public function handle(array $request): void
    {
        $accion = $request['accion'] ?? null;

        if (!isset($this->acciones[$accion])) {
            $_SESSION['mensaje'] = "Acción no válida.";
            header("Location: panel_proveedor.php");
            exit;
        }

        $this->acciones[$accion]->execute($request);

        header("Location: panel_proveedor.php");
        exit;
    }
}