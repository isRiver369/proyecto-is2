<?php
// 1. INICIAR SESIÓN Y CONEXIÓN
session_start(); 
include_once '../config/Database.php';
include_once '../src/Modelos/Servicio.php';

$database = new Database();
$db = $database->getConnection();
$servicio = new Servicio($db);
//sdsds
// Capturar búsqueda del filtro
$busqueda = isset($_GET['q']) ? $_GET['q'] : "";
$horario = isset($_GET["horario"]) ? $_GET["horario"] : "";
$modalidad = isset($_GET["modalidad"]) ? $_GET["modalidad"] : "";
$precio = isset($_GET["precio"]) ? $_GET["precio"] : "";
$disponibilidad = isset($_GET["disponibilidad"]) ? $_GET["disponibilidad"] : "";
$ubicacion = isset($_GET["ubicacion"]) ? $_GET["ubicacion"] : "";
$stmt = $servicio->obtenerTodos($busqueda, $horario, $modalidad, $precio, $disponibilidad, $ubicacion);
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
        .proservicios-secondary-text { color: #6C757D; }
        .arrow {
            transition: transform 0.3s ease;
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">

    <?php include 'menu_header.php'; ?>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        
        <h1 class="text-3xl font-bold text-[#212529] mb-8 text-center">Inscripción a Cursos y Talleres</h1>
        
        <div class="max-w-xl mx-auto mb-10">
            <form action="catalogo.php" method="GET" class="flex flex-col gap-3 relative">
                <!-- Buscador -->
                <div class="flex gap-2">
                    <input type="text" name="q" 
                        value="<?php echo htmlspecialchars($busqueda); ?>" 
                        placeholder="Buscar curso..."
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:border-[#1A4B8C] shadow-sm">

                    <button type="submit" 
                            class="bg-[#1A4B8C] text-white px-6 py-3 rounded-xl font-bold hover:bg-opacity-90 transition">
                        Buscar
                    </button>

                    <!-- Botón filtros -->
                    <button type="button" id="btnFiltros"
                        class="bg-gray-200 text-gray-700 px-4 py-3 rounded-xl font-bold hover:bg-gray-300 transition flex items-center justify-between gap-2 w-28">
                        <span>Filtros</span>
                            <svg id="arrowFiltros" class="arrow w-4 h-4 text-gray-700" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                            </svg>
                    </button>
                </div>

                <!-- Menú desplegable principal -->
                <div id="menuFiltros" 
    class="absolute mt-2 right-0 w-[600px] bg-white border rounded-xl shadow-xl hidden z-50 p-4">

    <h3 class="font-bold text-gray-700 mb-3">Filtrar por:</h3>

    <!-- Contenedor horizontal de los filtros -->
    <div class="flex flex-wrap gap-4">

        <!-- Disponibilidad -->
        <div class="flex-1 min-w-[160px]">
            <button type="button" 
                    class="w-full flex justify-between items-center font-semibold text-left text-gray-700 py-2"
                    onclick="toggleSubmenu(this)">
                Disponibilidad
                <svg class="arrow w-4 h-4 text-gray-700 transition-transform"
                    fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                </svg>
            </button>
            <div class="mt-2 hidden submenu">
                <label class="block"><input type="radio" name="disponibilidad" value="abierto" <?= $disponibilidad=="abierto" ? "checked" : "" ?>> Inscripciones abiertas</label>
                <label class="block mt-1"><input type="radio" name="disponibilidad" value="cerrado" <?= $disponibilidad=="cerrado" ? "checked" : "" ?>> Inscripciones cerradas</label>
                <label class="block mt-1"><input type="radio" name="disponibilidad" value="" <?= $disponibilidad=="" ? "checked" : "" ?>> Todos</label>
            </div>
        </div>

        <!-- Horario -->
        <div class="flex-1 min-w-[160px]">
                      <button type="button" 
                              class="w-full flex justify-between items-center font-semibold text-left text-gray-700 py-2"
                              onclick="toggleSubmenu(this)">
                          Horario
                          <svg class="arrow w-4 h-4 text-gray-700 transition-transform"
                              fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                          </svg>
                      </button>
                      <div class="mt-2 hidden submenu">
                          <label class="block"><input type="radio" name="horario" value="Mañana" <?= $horario=="Mañana" ? "checked" : "" ?>> Mañana</label>
                          <label class="block mt-1"><input type="radio" name="horario" value="Tarde" <?= $horario=="Tarde" ? "checked" : "" ?>> Tarde</label>
                          <label class="block mt-1"><input type="radio" name="horario" value="" <?= $horario=="" ? "checked" : "" ?>> Todos</label>
                      </div>
                  </div>

                  <!-- Modalidad (NUEVO) -->
                  <div class="flex-1 min-w-[160px]">
                      <button type="button" 
                              class="w-full flex justify-between items-center font-semibold text-left text-gray-700 py-2"
                              onclick="toggleSubmenu(this)">
                          Modalidad
                          <svg class="arrow w-4 h-4 text-gray-700 transition-transform"
                              fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                          </svg>
                      </button>
                      <div class="mt-2 hidden submenu">
                          <label class="block"><input type="radio" name="modalidad" value="Presencial" <?= ($modalidad ?? "") == "Presencial" ? "checked" : "" ?>> Presencial</label>
                          <label class="block mt-1"><input type="radio" name="modalidad" value="Online" <?= ($modalidad ?? "") == "Online" ? "checked" : "" ?>> Online</label>
                          <label class="block mt-1"><input type="radio" name="modalidad" value="" <?= ($modalidad ?? "") == "" ? "checked" : "" ?>> Ambas</label>
                      </div>
                  </div>

                  <!-- Precio -->
                  <div class="flex-1 min-w-[160px]">
                      <button type="button" 
                              class="w-full flex justify-between items-center font-semibold text-left text-gray-700 py-2"
                              onclick="toggleSubmenu(this)">
                          Precio
                          <svg class="arrow w-4 h-4 text-gray-700 transition-transform"
                              fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                          </svg>
                      </button>
                      <div class="mt-2 hidden submenu">
                          <label class="block"><input type="radio" name="precio" value="10-40" <?= $precio=="10-40" ? "checked" : "" ?>> $10 - $40</label>
                          <label class="block mt-1"><input type="radio" name="precio" value="50-70" <?= $precio=="50-70" ? "checked" : "" ?>> $50 - $70</label>
                          <label class="block mt-1"><input type="radio" name="precio" value="70-100" <?= $precio=="70-100" ? "checked" : "" ?>> $70 - $100</label>
                          <label class="block mt-1"><input type="radio" name="precio" value="" <?= $precio=="" ? "checked" : "" ?>> Todos</label>
                      </div>
                  </div>
              </div>

                <!-- Ubicación -->
                <div class="flex-1 min-w-[160px]">
                    <button type="button" 
                            class="w-full flex justify-between items-center font-semibold text-left text-gray-700 py-2"
                            onclick="toggleSubmenu(this)">
                        Ubicación
                        <svg class="arrow w-4 h-4 text-gray-700 transition-transform"
                            fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div class="mt-2 hidden submenu">
                        <label class="block"><input type="radio" name="ubicacion" value="Guayaquil" <?= ($ubicacion ?? "") == "Guayaquil" ? "checked" : "" ?>> Guayaquil</label>
                        <label class="block mt-1"><input type="radio" name="ubicacion" value="Quito" <?= ($ubicacion ?? "") == "Quito" ? "checked" : "" ?>> Quito</label>
                        <label class="block mt-1"><input type="radio" name="ubicacion" value="Cuenca" <?= ($ubicacion ?? "") == "Cuenca" ? "checked" : "" ?>> Cuenca</label>
                        <label class="block mt-1"><input type="radio" name="ubicacion" value="" <?= ($ubicacion ?? "") == "" ? "checked" : "" ?>> Todas</label>
                    </div>
                </div>



              <!-- Botones de acción -->
              <div class="mt-4 flex gap-2">
                  <button type="submit" class="flex-1 bg-[#1A4B8C] text-white py-2 rounded-lg font-bold">
                      Aplicar filtros
                  </button>
                  <a href="catalogo.php" class="flex-1 text-center py-2 text-sm text-gray-600 bg-gray-100 rounded-lg font-medium hover:bg-gray-200">
                      Restablecer
                  </a>
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
                    
                    // Extraer variables, incluyendo 'cupos_restantes' que viene del nuevo query
                    extract($row);
    
                    // Lógica visual inteligente
                    $tieneCupo = ($cupos_restantes > 0);
                    $estaActivo = ($disponible == 1);
                    $isAvailable = ($tieneCupo && $estaActivo);

                    if ($isAvailable) {
                        if ($cupos_restantes <= 3) {
                            // ¡Urgencia! Quedan pocos
                            $badgeClass = 'bg-orange-100 text-orange-800 border border-orange-200';
                            $badgeText  = "¡Últimos $cupos_restantes cupos!";
                        } else {
                            // Normal
                            $badgeClass = 'bg-green-100 text-green-800 border border-green-200';
                            $badgeText  = "$cupos_restantes cupos disponibles";
                        }
                        $cardOpacity = ''; // Opacidad normal
                    } else {
                        // Cerrado
                        $badgeClass = 'bg-red-100 text-red-800 border border-red-200';
                        $badgeText  = 'Agotado / Cerrado';
                        $cardOpacity = 'opacity-75 grayscale'; // Efecto visual de deshabilitado
                    }
                    $badgeText  = $isAvailable ? 'Inscripciones Abiertas' : 'Cerrado';
                    $cardOpacity = $isAvailable ? '' : 'opacity-75';

                    // Convertir las opciones de horario (string) en un array
                    // Ejemplo BD: "Mañana (09-12),Tarde (14-18)" -> Array
                    $horariosArray = explode(',', $opciones_horario ?? 'Mañana,Tarde');
            ?>
            
            <div class="bg-white rounded-xl shadow-lg hover:shadow-xl transition duration-300 overflow-hidden flex flex-col justify-between <?php echo $cardOpacity; ?>">
                <div class="p-5 flex-grow">
                    <div class="flex justify-between items-start mb-2">
                        <span class="text-xs font-bold bg-blue-100 text-blue-800 px-2 py-1 rounded">CURSO</span>
                        <span class="text-xs font-semibold <?php echo $badgeClass; ?> px-2 py-1 rounded">
                            <?php echo $badgeText; ?>
                        </span>
                    </div>

                    <p class="text-xs font-semibold text-gray-600 mb-2">
                        <span class="font-bold">Categoría:</span>
                        <?php echo htmlspecialchars($row['nombre_categoria']); ?>
                    </p>

                    <h3 class="text-xl font-bold text-[#1A4B8C] mb-2"><?php echo htmlspecialchars($nombre_servicio); ?></h3>
                    <p class="text-sm text-gray-600 mb-4"><?php echo htmlspecialchars($row['descripcion_breve']); ?></p>
                    
                    <div class="flex items-center justify-between mt-2 border-t pt-3">
                        <span class="text-xs text-gray-500 font-bold">Precio del curso</span>
                        <span class="text-2xl font-extrabold text-gray-800">$<?php echo number_format($precio, 2); ?></span>
                    </div>
                </div>

                <?php if($isAvailable): ?>
                    <div class="w-full bg-gray-50 border-t p-4">
                        <a href="detalles_servicio.php?servicio_id=<?= $servicio_id ?>" 
                            class="w-full block text-center py-3 font-bold text-white bg-[#1A4B8C] hover:bg-opacity-90 transition rounded-lg shadow-md">
                            Ver Detalles
                        </a>
                    </div>
                <?php else: ?>
                    <div class="p-4 bg-gray-100">
                        <button disabled class="w-full py-3 font-bold text-white bg-gray-400 cursor-not-allowed rounded-lg">
                            Cupos Agotados
                        </button>
                    </div>
                <?php endif; ?>
            </div>
            <?php 
                } 
            } else {
                echo "<div class='col-span-3 text-center py-10 text-gray-500'>No hay cursos disponibles.</div>";
            }
            ?>
        </div>
    </main>
    
    <script>
        const btnFiltros = document.getElementById("btnFiltros");
        const menuFiltros = document.getElementById("menuFiltros");
        const arrowFiltros = document.getElementById("arrowFiltros");

        // Abrir menú desplegable de filtros
        btnFiltros.addEventListener("click", (e) => {
            e.stopPropagation(); 
            menuFiltros.classList.toggle("hidden");
        });

        menuFiltros.addEventListener("click", (e) => {
            e.stopPropagation();
        });

        document.addEventListener("click", () => {
            if (!menuFiltros.classList.contains("hidden")) {
                menuFiltros.classList.add("hidden");
                arrowFiltros.classList.remove("open");
            }
        });

        document.querySelectorAll("#menuFiltros button").forEach((btn) => {
            btn.addEventListener("click", () => {
                const submenu = btn.nextElementSibling;

                if (submenu && submenu.classList.contains("submenu")) {
                    submenu.classList.toggle("hidden");
                }
            });
        });
    </script>
</body>
</html>