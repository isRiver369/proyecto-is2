<?php
// 1. INICIAR SESIÓN Y CONEXIÓN
session_start(); 
include_once '../config/Database.php';
include_once '../src/Modelos/Servicio.php';

$database = new Database();
$db = $database->getConnection();
$servicio = new Servicio($db);

// 2. CAPTURAR FILTROS (Sin ubicación)
$busqueda = isset($_GET['q']) ? $_GET['q'] : "";
$horario = isset($_GET["horario"]) ? $_GET["horario"] : "";
$precio = isset($_GET["precio"]) ? $_GET["precio"] : "";
$disponibilidad = isset($_GET["disponibilidad"]) ? $_GET["disponibilidad"] : "";
$modalidad = isset($_GET["modalidad"]) ? $_GET["modalidad"] : ""; 

// 3. PASAMOS TODO AL MODELO
$stmt = $servicio->obtenerTodos($busqueda, $horario, $precio, $disponibilidad, $modalidad);
$num = $stmt->rowCount();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catálogo - ProServicios</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .proservicios-primary { background-color: #1A4B8C; }
        .arrow { transition: transform 0.3s ease; }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">

    <?php include 'menu_header.php'; ?>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        
        <h1 class="text-3xl font-bold text-[#212529] mb-8 text-center">Inscripción a Cursos y Talleres</h1>
        
        <div class="max-w-xl mx-auto mb-10">
            <form action="catalogo.php" method="GET" class="flex flex-col gap-3 relative">
                <div class="flex gap-2">
                    <input type="text" name="q" 
                        value="<?php echo htmlspecialchars($busqueda); ?>" 
                        placeholder="Buscar curso..."
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:border-[#1A4B8C] shadow-sm">

                    <button type="submit" 
                            class="bg-[#1A4B8C] text-white px-6 py-3 rounded-xl font-bold hover:bg-opacity-90 transition">
                        Buscar
                    </button>

                    <button type="button" id="btnFiltros"
                        class="bg-gray-200 text-gray-700 px-4 py-3 rounded-xl font-bold hover:bg-gray-300 transition flex items-center justify-between gap-2 w-28">
                        <span>Filtros</span>
                            <svg id="arrowFiltros" class="arrow w-4 h-4 text-gray-700" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                            </svg>
                    </button>
                </div>

                <div id="menuFiltros" class="absolute mt-2 right-0 w-[500px] bg-white border rounded-xl shadow-xl hidden z-50 p-4">

                    <h3 class="font-bold text-gray-700 mb-3">Filtrar por:</h3>

                    <div class="grid grid-cols-2 gap-4">

                        <div>
                            <button type="button" class="w-full flex justify-between items-center font-semibold text-left text-gray-700 py-2 border-b" onclick="toggleSubmenu(this)">
                                Disponibilidad <svg class="arrow w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 9l-7 7-7-7" /></svg>
                            </button>
                            <div class="mt-2 hidden submenu text-sm text-gray-600">
                                <label class="block"><input type="radio" name="disponibilidad" value="abierto" <?= $disponibilidad=="abierto" ? "checked" : "" ?>> Abiertas</label>
                                <label class="block mt-1"><input type="radio" name="disponibilidad" value="cerrado" <?= $disponibilidad=="cerrado" ? "checked" : "" ?>> Cerradas</label>
                                <label class="block mt-1"><input type="radio" name="disponibilidad" value="" <?= $disponibilidad=="" ? "checked" : "" ?>> Todos</label>
                            </div>
                        </div>

                        <div>
                            <button type="button" class="w-full flex justify-between items-center font-semibold text-left text-gray-700 py-2 border-b" onclick="toggleSubmenu(this)">
                                Horario <svg class="arrow w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 9l-7 7-7-7" /></svg>
                            </button>
                            <div class="mt-2 hidden submenu text-sm text-gray-600">
                                <label class="block"><input type="radio" name="horario" value="Mañana" <?= $horario=="Mañana" ? "checked" : "" ?>> Mañana</label>
                                <label class="block mt-1"><input type="radio" name="horario" value="Tarde" <?= $horario=="Tarde" ? "checked" : "" ?>> Tarde</label>
                                <label class="block mt-1"><input type="radio" name="horario" value="" <?= $horario=="" ? "checked" : "" ?>> Todos</label>
                            </div>
                        </div>

                        <div>
                            <button type="button" class="w-full flex justify-between items-center font-semibold text-left text-gray-700 py-2 border-b" onclick="toggleSubmenu(this)">
                                Modalidad <svg class="arrow w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 9l-7 7-7-7" /></svg>
                            </button>
                            <div class="mt-2 hidden submenu text-sm text-gray-600">
                                <label class="block"><input type="radio" name="modalidad" value="Presencial" <?= ($modalidad ?? "") == "Presencial" ? "checked" : "" ?>> Presencial</label>
                                <label class="block mt-1"><input type="radio" name="modalidad" value="Online" <?= ($modalidad ?? "") == "Online" ? "checked" : "" ?>> Online</label>
                                <label class="block mt-1"><input type="radio" name="modalidad" value="" <?= ($modalidad ?? "") == "" ? "checked" : "" ?>> Ambas</label>
                            </div>
                        </div>

                        <div>
                            <button type="button" class="w-full flex justify-between items-center font-semibold text-left text-gray-700 py-2 border-b" onclick="toggleSubmenu(this)">
                                Precio <svg class="arrow w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 9l-7 7-7-7" /></svg>
                            </button>
                            <div class="mt-2 hidden submenu text-sm text-gray-600">
                                <label class="block"><input type="radio" name="precio" value="10-40" <?= $precio=="10-40" ? "checked" : "" ?>> $10 - $40</label>
                                <label class="block mt-1"><input type="radio" name="precio" value="50-70" <?= $precio=="50-70" ? "checked" : "" ?>> $50 - $70</label>
                                <label class="block mt-1"><input type="radio" name="precio" value="70-100" <?= $precio=="70-100" ? "checked" : "" ?>> $70 - $100</label>
                                <label class="block mt-1"><input type="radio" name="precio" value="" <?= $precio=="" ? "checked" : "" ?>> Todos</label>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 flex gap-2">
                        <button type="submit" class="flex-1 bg-[#1A4B8C] text-white py-2 rounded-lg font-bold hover:opacity-90">Aplicar</button>
                        <a href="catalogo.php" class="flex-1 text-center py-2 text-sm text-gray-600 bg-gray-100 rounded-lg font-medium hover:bg-gray-200">Limpiar</a>
                    </div>
                </div>
            </form>
        </div>

        <?php if(isset($_GET['error']) && $_GET['error'] == 'sin_cupo'): ?>
            <div class="bg-yellow-100 text-yellow-800 p-4 rounded mb-6 text-center font-bold border border-yellow-300">
                 Lo sentimos: El horario seleccionado ya no tiene cupos disponibles.
            </div>
        <?php endif; ?>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
            
            <?php
            if($num > 0) {
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    extract($row);
                    
                    $tieneCupo = ($cupos_restantes > 0);
                    $estaActivo = ($disponible == 1);
                    $isAvailable = ($tieneCupo && $estaActivo);

                    if ($isAvailable) {
                        if ($cupos_restantes <= 3) {
                            $badgeClass = 'bg-orange-100 text-orange-800 border border-orange-200';
                            $badgeText  = "¡Solo $cupos_restantes quedan!";
                        } else {
                            $badgeClass = 'bg-green-100 text-green-800 border border-green-200';
                            $badgeText  = "Disponible";
                        }
                        $cardOpacity = ''; 
                    } else {
                        $badgeClass = 'bg-red-100 text-red-800 border border-red-200';
                        $badgeText  = 'Agotado';
                        $cardOpacity = 'opacity-75 grayscale';
                    }

                    // --- DETECTAR MODALIDAD VISUALMENTE ---
                    $modalidadTag = "PRESENCIAL";
                    $modalidadColor = "bg-purple-100 text-purple-800";
                    if (stripos($descripcion, 'Online') !== false || stripos($nombre_servicio, 'Online') !== false) {
                        $modalidadTag = "ONLINE";
                        $modalidadColor = "bg-blue-100 text-blue-800";
                    }

                    $horariosArray = explode(',', $opciones_horario ?? 'Mañana,Tarde');
            ?>
            
            <div class="bg-white rounded-xl shadow-lg hover:shadow-xl transition duration-300 overflow-hidden flex flex-col justify-between <?php echo $cardOpacity; ?>">
                <div class="p-5 flex-grow">
                    <div class="flex justify-between items-start mb-3">
                        <div class="flex gap-1">
                            <span class="text-[10px] font-bold <?php echo $modalidadColor; ?> px-2 py-1 rounded uppercase">
                                <?php echo $modalidadTag; ?>
                            </span>
                        </div>
                        <span class="text-[10px] font-bold <?php echo $badgeClass; ?> px-2 py-1 rounded uppercase">
                            <?php echo $badgeText; ?>
                        </span>
                    </div>

                    <h3 class="text-xl font-bold text-[#1A4B8C] mb-2 leading-tight"><?php echo htmlspecialchars($nombre_servicio); ?></h3>
                    
                    <div class="flex flex-col gap-2 text-sm text-gray-600 mb-3 bg-gray-50 p-3 rounded border">
                        <div class="flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-[#1A4B8C]" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                            <span class="font-medium">
                            <?php
                            $fi = !empty($fecha_inicio) ? $fecha_inicio : date('Y-m-d');
                            $ff = !empty($fecha_fin) ? $fecha_fin : date('Y-m-d');
                            echo date('d M', strtotime($fi)) . " - " . date('d M', strtotime($ff));
                            ?>
                            </span>
                        </div>
                        <div class="flex items-center gap-2">
                             <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-[#1A4B8C]" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                             <span>Cupos: <strong><?php echo $cupos_restantes; ?></strong> / <?php echo $cupo_maximo; ?></span>
                        </div>
                    </div>

                    <p class="text-gray-500 mb-4 text-sm line-clamp-3">
                        <?php echo htmlspecialchars($descripcion); ?>
                    </p>
                    
                    <div class="flex items-center justify-between mt-auto pt-3 border-t">
                        <span class="text-xs text-gray-400 font-bold uppercase">Inversión</span>
                        <span class="text-2xl font-extrabold text-gray-800">$<?php echo number_format($precio, 2); ?></span>
                    </div>
                </div>

                <?php if($isAvailable): ?>
                    <form action="procesar_reserva.php" method="POST" class="w-full bg-gray-50 border-t p-4">
                        <input type="hidden" name="servicio_id" value="<?php echo $servicio_id; ?>">
                        <input type="hidden" name="precio" value="<?php echo $precio; ?>">
                        <input type="hidden" name="fecha" value="<?php echo $fecha_inicio; ?>">

                        <div class="mb-3">
                        <div class="relative">
                            <select name="horario_elegido" required class="block w-full bg-white border border-gray-300 text-gray-700 py-2 px-3 rounded leading-tight focus:outline-none focus:border-[#1A4B8C] text-sm">
                                <option value="" disabled selected>Selecciona tu Turno</option>
                                
                                <?php 
                                // 1. Convertimos el string de la BD en array
                                // BD: "Mañana (08-12), Tarde (14-18)"
                                $horariosRaw = explode(',', $horario ?? ''); 
                                
                                $manana = [];
                                $tarde = [];
                                $noche = [];

                                // 2. Clasificamos cada horario
                                foreach($horariosRaw as $h) {
                                    $h = trim($h);
                                    if(stripos($h, 'Mañana') !== false) {
                                        $manana[] = $h;
                                    } elseif(stripos($h, 'Tarde') !== false) {
                                        $tarde[] = $h;
                                    } else {
                                        $noche[] = $h; // Por si acaso o Noche
                                    }
                                }
                                ?>

                                <?php if(!empty($manana)): ?>
                                    <optgroup label="HORARIOS MAÑANA">
                                        <?php foreach($manana as $h) echo "<option value='$h'>$h</option>"; ?>
                                    </optgroup>
                                <?php endif; ?>

                                <?php if(!empty($tarde)): ?>
                                    <optgroup label="HORARIOS TARDE">
                                        <?php foreach($tarde as $h) echo "<option value='$h'>$h</option>"; ?>
                                    </optgroup>
                                <?php endif; ?>

                                <?php if(!empty($noche)): ?>
                                    <optgroup label="OTROS HORARIOS">
                                        <?php foreach($noche as $h) echo "<option value='$h'>$h</option>"; ?>
                                    </optgroup>
                                <?php endif; ?>

                            </select>
                        </div>
                    </div>

                        <button type="submit" class="w-full py-2.5 font-bold text-white bg-[#1A4B8C] hover:bg-opacity-90 transition rounded shadow-sm text-sm uppercase tracking-wide">
                            Inscribirme Ahora
                        </button>
                    </form>
                <?php else: ?>
                    <div class="p-4 bg-gray-100 text-center">
                        <span class="text-gray-500 font-bold text-sm">Inscripciones Cerradas</span>
                    </div>
                <?php endif; ?>
            </div>
            <?php 
                } 
            } else {
                echo "<div class='col-span-3 text-center py-20 bg-white rounded-xl shadow-sm border border-dashed border-gray-300'>
                        <p class='text-xl text-gray-500 font-bold'>No encontramos cursos con esos filtros.</p>
                        <a href='catalogo.php' class='text-[#1A4B8C] underline mt-2 inline-block'>Ver todos los cursos</a>
                      </div>";
            }
            ?>
        </div>
    </main>

    <script>
        const btnFiltros = document.getElementById("btnFiltros");
        const menuFiltros = document.getElementById("menuFiltros");
        
        btnFiltros.addEventListener("click", (e) => {
            e.stopPropagation(); 
            menuFiltros.classList.toggle("hidden");
        });

        menuFiltros.addEventListener("click", (e) => { e.stopPropagation(); });

        document.addEventListener("click", () => {
            if (!menuFiltros.classList.contains("hidden")) {
                menuFiltros.classList.add("hidden");
            }
        });

        function toggleSubmenu(btn) {
            const submenu = btn.nextElementSibling;
            if (submenu) submenu.classList.toggle("hidden");
            const arrow = btn.querySelector('.arrow');
            if (arrow) arrow.classList.toggle('rotate-180');
        }
    </script>
</body>
</html>