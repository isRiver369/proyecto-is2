<?php

require_once '../src/Servicios/AuthService.php';

class ActualizarPerfilAc
{
    private AuthService $authService;

    public function __construct(PDO $db, private int $usuario_id)
    {
        $this->authService = new AuthService($db);
    }

    public function execute(array $data): void
    {
        $bio        = $data['bio'] ?? '';
        $portafolio = $data['portafolio_url'] ?? '';
        $contacto   = $data['contacto'] ?? '';

        $ok = $this->authService->actualizarPerfilProveedor(
            $this->usuario_id,
            $bio,
            $portafolio,
            $contacto
        );

        $_SESSION['mensaje'] = $ok
            ? "Perfil actualizado correctamente."
            : "Error al actualizar el perfil.";
    }
}