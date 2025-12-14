<?php
// Asegurarnos de que no haya sesión iniciada antes de iniciarla (evitar errores)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Obtener nombre para mostrar
$nombreDisplay = isset($_SESSION['nombre_completo']) ? explode(" ", $_SESSION['nombre_completo'])[0] : 'Usuario';
?>

<header class="nav-header" style="background-color: #1A4B8C; color: #FFFFFF; padding: 0.75rem 2rem; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 2px 4px rgba(0,0,0,0.1); position: sticky; top: 0; z-index: 1000;">
    
    <div class="flex items-center gap-2 font-bold text-xl">
        <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" viewBox="0 0 200 200">
            <path d="M100 10 C150 10, 190 50, 190 100 C190 150, 150 190, 100 190 C50 190, 10 150, 10 100 C10 50, 50 10, 100 10 Z" fill="#FFFFFF"/>
            <path d="M60 50 Q80 45, 100 50 L100 150 Q80 155, 60 150 L60 50 M100 50 Q120 45, 140 50 L140 150 Q120 155, 100 150" fill="#1A4B8C" />
            <path d="M80 100 L110 130 L160 70 L145 55 L110 100 L95 85 Z" fill="#17A2B8" stroke="#FFFFFF" stroke-width="8" stroke-linejoin="round" stroke-linecap="round"/>
        </svg>
        <span>ProServicios</span>
    </div>
    
    <nav class="flex gap-5 items-center font-semibold">
        <a href="<?php echo isset($_SESSION['usuario_id']) ? 'menucliente.php' : 'menuvisitante.php'; ?>" 
            class="hover:bg-white/20 px-3 py-2 rounded transition">
            Inicio
        </a>
        <a href="catalogo.php" class="hover:bg-white/20 px-3 py-2 rounded transition">Catálogo</a>
        <a href="reserva.php" class="hover:bg-white/20 px-3 py-2 rounded transition">Mis Reservas</a>
        
        <?php if(isset($_SESSION['usuario_id'])): ?>
            <a href="mis_pagos.php" class="hover:bg-white/20 px-3 py-2 rounded transition flex items-center gap-1">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Historial Pagos
            </a>
        <?php endif; ?>
        
        
        <?php if(isset($_SESSION['usuario_id'])): ?>
            
            <div class="relative group ml-4 border-l border-white/30 pl-4">
                
                <button class="flex items-center gap-2 bg-transparent border-2 border-white text-white px-3 py-1 rounded hover:bg-white hover:text-[#1A4B8C] transition duration-200">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
                        <path d="M8 8a3 3 0 1 1 0-6 3 3 0 0 1 0 6zm2-3a2 2 0 1 1-4 0 2 2 0 0 1 4 0zm4 8c0 1-1 1-1 1H3s-1 0-1-1 1-4 6-4 6 3 6 4zm-1-.1c1.11.35 2 1.22 2 2.5v1c0 .55-.45 1-1 1H1s-.45-.45-1-1v-1c0-1.28.88-2.25 2-2.5V7.5C2 6.56 2.66 6 3.5 6S5 6.56 5 7.5v1.5z"/>
                    </svg>
                    <span>Hola, <?php echo $nombreDisplay; ?></span>
                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="currentColor" viewBox="0 0 16 16">
                        <path d="M7.247 11.14 2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z"/>
                    </svg>
                </button>

                <div class="absolute right-0 mt-0 w-48 bg-white rounded-md shadow-xl overflow-hidden hidden group-hover:block z-50">
                    
                    <a href="perfil.php" class="flex items-center gap-2 px-4 py-3 text-sm text-gray-700 hover:bg-gray-100 transition border-b">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#17A2B8" viewBox="0 0 16 16">
                            <path d="M8 8a3 3 0 1 1 0-6 3 3 0 0 1 0 6zm2-3a2 2 0 1 1-4 0 2 2 0 0 1 4 0zm4 8c0 1-1 1-1 1H3s-1 0-1-1 1-4 6-4 6 3 6 4zm-1-.1c1.11.35 2 1.22 2 2.5v1c0 .55-.45 1-1 1H1s-.45-.45-1-1v-1c0-1.28.88-2.25 2-2.5V7.5C2 6.56 2.66 6 3.5 6S5 6.56 5 7.5v1.5z"/>
                        </svg>
                        Mis Datos
                    </a>

                    <a href="logout.php" class="flex items-center gap-2 px-4 py-3 text-sm text-red-600 hover:bg-red-50 transition font-semibold">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                            <path fill-rule="evenodd" d="M10 12.5a.5.5 0 0 1-.5.5h-8a.5.5 0 0 1-.5-.5v-9a.5.5 0 0 1 .5-.5h8a.5.5 0 0 1 .5.5v2a.5.5 0 0 0 1 0v-2A1.5 1.5 0 0 0 9.5 2h-8A1.5 1.5 0 0 0 0 3.5v9A1.5 1.5 0 0 0 1.5 14h8a1.5 1.5 0 0 0 1.5-1.5v-2a.5.5 0 0 0-1 0v2z"/>
                            <path fill-rule="evenodd" d="M15.854 8.354a.5.5 0 0 0 0-.708l-3-3a.5.5 0 0 0-.708.708L14.293 7.5H5.5a.5.5 0 0 0 0 1h8.793l-2.147 2.146a.5.5 0 0 0 .708.708l3-3z"/>
                        </svg>
                        Salir
                    </a>
                </div>
            </div>

        <?php else: ?>
            <a href="login.php" class="bg-white text-[#1A4B8C] px-4 py-1 rounded hover:bg-gray-100 transition">Iniciar Sesión</a>
        <?php endif; ?>
    </nav>
</header>