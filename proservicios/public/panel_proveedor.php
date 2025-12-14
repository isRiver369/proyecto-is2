<?php
session_start();
require_once '../src/Servicios/Seguridad.php';
require_once '../config/Database.php';
require_once '../src/Modelos/Servicio.php';
include_once 'obtener_categorias.php';
require_once '../src/Servicios/AuthService.php'; // Para perfil
require_once '../src/Servicios/ReservaService.php'; // Para ver reservas

// 1. Seguridad
Seguridad::requerirRol('proveedor');
$usuario_id = $_SESSION['usuario_id'];

// 2. Inicializar
$db = (new Database())->getConnection();
$servicioModel = new Servicio($db);
$authService = new AuthService();
$reservaService = new ReservaService($db); 
$categoriaObj = new Categoria();
$categorias = $categoriaObj->obtenerCategorias();

// 4. Obtener Datos para la Vista
$misServicios = $servicioModel->obtenerPorProveedor($usuario_id);
$miPerfil = $authService->obtenerUsuarioPorId($usuario_id); // Trae nombre, email. bio, portafolio
$miProveedorInfo = $authService->obtenerProveedorInfo($usuario_id);
$misReservas = $reservaService->obtenerReservasPorProveedor($usuario_id);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel Proveedor - ProServicios</title>
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        .active-tab { border-bottom: 2px solid #1A4B8C; color: #1A4B8C; font-weight: bold; }
        input::placeholder,
        textarea::placeholder {
            font-style: italic;
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">

    <header class="bg-[#1A4B8C] text-white shadow-md">
        <div class="max-w-7xl mx-auto px-4 py-4 flex justify-between items-center">
            <div class="flex items-center gap-2 font-bold text-xl">
                <span>ProServicios | Proveedor</span>
            </div>
            <div class="flex gap-4 items-center">
                <span>Hola, <?php echo $_SESSION['nombre_completo']; ?></span>
                <a href="logout.php" class="bg-red-500 hover:bg-red-600 px-3 py-1 rounded text-sm transition">Cerrar Sesión</a>
            </div>
        </div>
    </header>

    <main class="max-w-7xl mx-auto px-4 py-8">
        
        <?php if(isset($_SESSION['mensaje'])): ?>
            <div class="bg-green-100 text-green-700 p-3 rounded mb-4 text-center font-bold">
                <?php 
                    echo $_SESSION['mensaje'];
                    unset($_SESSION['mensaje']); 
                ?>
            </div>
        <?php endif; ?>

        <div class="flex border-b border-gray-200 mb-6">
            <button onclick="switchTab('servicios')" id="tab-servicios" class="active-tab px-6 py-3 text-gray-500 hover:text-[#1A4B8C] transition">Mis Servicios</button>
            <button onclick="switchTab('perfil')" id="tab-perfil" class="px-6 py-3 text-gray-500 hover:text-[#1A4B8C] transition">Mi Perfil (Bio)</button>
            <button onclick="switchTab('reservas')" id="tab-reservas" class="px-6 py-3 text-gray-500 hover:text-[#1A4B8C] transition">Reservas</button>
            <button onclick="switchTab('agenda')" id="tab-agenda" class="px-6 py-3 text-gray-500 hover:text-[#1A4B8C] transition">Agenda</button>
        </div>

        <div id="view-servicios" class="block">
            <div class="flex flex-col mb-6">
                <h2 class="text-2xl font-bold text-gray-800">Crear nuevo servicio</h2>

                <div class="flex justify-center">
                    <!-- Botón debajo del título -->
                    <button id="btn-abrir-form"
                            class="mt-4 inline-block bg-[#1A4B8C] text-white font-bold px-5 py-2 rounded-lg shadow hover:bg-blue-900 transition w-max">
                        + Agregar servicio
                    </button>
                </div>
            </div>

            <!-- Modal: Crear servicio -->
            <div id="contenedor-form-servicio" class="hidden mt-4 p-4 bg-white shadow rounded-lg border">
                <h2 class="text-xl font-bold mb-4 text-gray-800">Crear nuevo servicio</h2>

                <form id="form-crear-servicio" action="gestionar_servicios.php" method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <input type="hidden" name="accion" value="crear_servicio">

                    <!-- Nombre -->
                    <div class="flex flex-col">
                        <label for="nombre" class="font-semibold text-sm mb-1">Nombre:</label>
                        <input type="text" name="nombre" id="nombre"
                            placeholder="Nombre del Servicio"
                            class="border p-2 rounded w-full" required>
                    </div>

                    <!-- Precio -->
                    <div class="flex flex-col">
                        <label for="precio" class="font-semibold text-sm mb-1">Precio ($):</label>
                        <input type="number" name="precio" id="precio"
                            placeholder="Precio ($)"
                            class="border p-2 rounded w-full" step="0.01" required>
                    </div>

                    <div class="flex flex-col">
                        <label for="categoria_id" class="font-semibold text-sm mb-1">Categoría:</label>
                        <select name="categoria_id" id="categoria_id" class="border p-2 rounded w-full" required>
                            <option value="" disabled selected>-- Selecciona categoría --</option>

                            <?php foreach ($categorias as $cat): ?>
                                <option value="<?= $cat['categoria_id'] ?>">
                                    <?= htmlspecialchars($cat['nombre_categoria']) ?>
                                </option>
                            <?php endforeach; ?>

                        </select>
                    </div>

                    <!-- Cupos -->
                    <div class="flex flex-col">
                        <label for="cupos" class="font-semibold text-sm mb-1">Cupos Máximos:</label>
                        <input type="number" name="cupos" id="cupos"
                            placeholder="Cupos Máximos"
                            class="border p-2 rounded w-full" required>
                    </div>

                    <!-- Descripción breve (usa 2 columnas) -->
                    <div class="flex flex-col md:col-span-2">
                        <label for="descripcion_breve" class="font-semibold text-sm mb-1">Descripción breve:</label>
                        <input type="text" name="descripcion_breve" id="descripcion_breve"
                            placeholder="Descripción resumida y puntual"
                            class="border p-2 rounded w-full" required>
                    </div>

                    <!-- Descripción larga (2 columnas) -->
                    <div class="flex flex-col md:col-span-2">
                        <label for="descripcion" class="font-semibold text-sm mb-1">Descripción detallada:</label>
                        <textarea name="descripcion" id="descripcion"
                                placeholder="Descripción detallada y completa"
                                class="border p-2 rounded w-full"
                                rows="3"></textarea>
                    </div>

                    <?php include 'seleccion_horario.php'; ?>
                    <div id="bloque-ubicacion" class="flex flex-col md:col-span-2 hidden">
                        <label for="ubicacion" class="font-semibold text-sm mb-1">Ubicación:</label>
                        <select name="ubicacion" id="ubicacion"
                                class="border p-2 rounded w-full">
                            <option value="" disabled selected>-- Seleccione ubicación --</option>
                            <option value="Guayaquil">Guayaquil</option>
                            <option value="Quito">Quito</option>
                            <option value="Cuenca">Cuenca</option>
                        </select>
                    </div>

                    <!-- Políticas -->
                    <div class="flex flex-col md:col-span-2">
                        <label for="politicas" class="font-semibold text-sm mb-1">Políticas del servicio:</label>
                        <textarea name="politicas" id="politicas"
                                placeholder="Políticas del servicio"
                                class="border p-2 rounded w-full"
                                rows="2"></textarea>
                    </div>

                    <!-- Fechas -->
                    <div class="flex flex-col">
                        <label for="fecha_inicio" class="font-semibold text-sm mb-1">Inicio:</label>
                        <input type="date" name="fecha_inicio" id="fecha_inicio"
                            class="border p-2 rounded w-full" required>
                    </div>

                    <div class="flex flex-col">
                        <label for="fecha_fin" class="font-semibold text-sm mb-1">Fin:</label>
                        <input type="date" name="fecha_fin" id="fecha_fin"
                            class="border p-2 rounded w-full" required>
                    </div>

                    <!-- Botones -->
                    <button type="submit"
                            class="bg-green-600 text-white font-bold py-2 rounded md:col-span-2 hover:bg-green-700 transition">
                        Publicar Servicio
                    </button>

                    <button type="button" id="btn-cerrar-form"
                            class="text-red-600 font-semibold md:col-span-2 py-2">
                        Cancelar
                    </button>

                </form>

            </div>

            <hr class="my-6 border-gray-200">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold text-gray-800">Servicios</h2> 
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php if ($misServicios->rowCount() === 0): ?>
                    <p class="col-span-full text-center text-gray-500 italic">
                        Aún no tienes servicios creados
                    </p>
                <?php else: ?>
                    <?php while($row = $misServicios->fetch(PDO::FETCH_ASSOC)): ?>
                        <div class="bg-white rounded-lg shadow hover:shadow-md transition p-5 border-l-4 border-[#1A4B8C]">
                            <h4 class="font-bold text-lg text-gray-800"><?php echo htmlspecialchars($row['nombre_servicio']); ?></h4>
                            <p class="text-gray-500 text-sm mb-2 h-10 overflow-hidden"><?php echo htmlspecialchars($row['descripcion_breve']); ?></p>
                            
                            <div class="text-sm bg-gray-50 p-2 rounded mb-3">
                                <p><strong>Precio:</strong> $<?php echo number_format($row['precio'], 2); ?></p>
                                <p><strong>Cupos:</strong> <?php echo $row['cupo_maximo']; ?></p>
                                <p><strong>Horario:</strong> <?php echo $row['horario'] ?: 'No definido'; ?></p>
                            </div>

                            <div class="flex justify-between mt-3">
                                <button type="button"
                                    class="text-blue-600 text-sm font-bold hover:underline" onclick='editarServicio(<?php echo json_encode($row); ?>)'>
                                        Editar Servicio
                                </button>

                                <form action="gestionar_servicios.php" method="POST" onsubmit="return confirm('¿Estás seguro de que quieres eliminar este servicio?');">
                                    <input type="hidden" name="accion" value="eliminar_servicio">
                                    <input type="hidden" name="servicio_id" value="<?php echo $row['servicio_id']; ?>">

                                    <button type="submit" class="text-red-500 text-sm font-bold hover:underline">
                                        Eliminar Servicio
                                    </button>
                                </form>
                            </div>
                            
                        </div>
                    <?php endwhile; ?>
                <?php endif; ?>
            </div>
        </div>

        <div id="view-perfil" class="hidden">
            <h2 class="text-2xl font-bold text-gray-800 mb-6">Mi Perfil Profesional</h2>

            <form action="gestionar_servicios.php" method="POST" id="formPerfil">
                <input type="hidden" name="accion" value="actualizar_perfil">

                <div class="bg-white rounded-xl shadow-lg p-8 grid grid-cols-1 lg:grid-cols-3 gap-10">
                    <div class="lg:col-span-2">

                        <!-- CAMPOS NO EDITABLES -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            
                            <div>
                                <label class="block text-gray-700 font-bold mb-2">Nombre</label>
                                <input type="text" disabled
                                    value="<?= htmlspecialchars($miPerfil['nombre']) ?>"
                                    class="w-full bg-gray-100 border border-gray-200 text-gray-600 rounded-lg px-4 py-3 cursor-not-allowed">
                            </div>

                            <div>
                                <label class="block text-gray-700 font-bold mb-2">Apellido</label>
                                <input type="text" disabled
                                    value="<?= htmlspecialchars($miPerfil['apellido']) ?>"
                                    class="w-full bg-gray-100 border border-gray-200 text-gray-600 rounded-lg px-4 py-3 cursor-not-allowed">
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-gray-700 font-bold mb-2">Correo Electrónico</label>
                                <input type="email" disabled
                                    value="<?= htmlspecialchars($miPerfil['email']) ?>"
                                    class="w-full bg-gray-100 border border-gray-200 text-gray-500 rounded-lg px-4 py-3 cursor-not-allowed">
                            </div>
                        </div>

                        <!-- BIOGRAFIA -->
                        <div class="mb-6">
                            <label class="block text-gray-700 font-bold mb-2">Biografía / Acerca de mí</label>
                            <textarea name="bio" rows="4"
                                class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:border-[#1A4B8C]"
                                placeholder="Cuenta sobre ti..."><?= htmlspecialchars($miProveedorInfo['biografia'] ?? '') ?></textarea>
                        </div>
                    </div>

                    <div class="flex flex-col items-center">

                        <!-- PORTAFOLIO -->
                        <div class="w-full mb-6">
                            <label class="block text-gray-700 font-bold mb-2">Enlace a Portafolio (URL)</label>
                            <input type="text" name="portafolio_url"
                                value="<?= htmlspecialchars($miProveedorInfo['enlace_portafolio'] ?? '') ?>"
                                class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:border-[#1A4B8C]"
                                placeholder="https://mipagina.com">
                        </div>

                        <!-- CONTACTO -->
                        <div class="w-full mt-[-10px] mb-6">
                            <label class="block text-gray-700 font-bold mb-2">Contacto adicional</label>
                            <input type="text" name="contacto"
                                value="<?= htmlspecialchars($miProveedorInfo['contacto'] ?? '') ?>"
                                class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:border-[#1A4B8C]"
                                placeholder="Número de teléfono, correo o redes adicionales">
                        </div>
                    </div>
                </div>
                <div class="flex justify-end gap-4 mt-6">
                    <button type="button" id="btnCancelar"
                        class="px-5 py-2 bg-gray-300 hover:bg-gray-400 text-gray-800 rounded-lg transition">
                        Cancelar
                    </button>

                    <button type="submit" id="btnGuardar"
                        class="px-5 py-2 bg-[#1A4B8C] hover:bg-[#163d73] text-white rounded-lg transition">
                        Guardar cambios
                    </button>

                </div>

            </form>
        </div>


        <div id="view-reservas" class="hidden">
            <h2 class="text-2xl font-bold text-gray-800 mb-6">Reservas</h2>
            
            <div class="bg-white rounded-xl shadow overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Cliente</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Servicio</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Fecha</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Estado</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php if ($misReservas->rowCount() === 0): ?>
                            <tr>
                                <td colspan="4" class="px-6 py-6 text-center text-gray-500 italic">
                                    Aún no tienes reservas de tus servicios
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php while ($r = $misReservas->fetch(PDO::FETCH_ASSOC)): ?>

                            <tr>
                                <td class="px-6 py-4 font-semibold text-gray-800">
                                    <?php echo $r['cliente']; ?>
                                </td>

                                <td class="px-6 py-4 text-gray-700">
                                    <?php echo $r['servicio_nombre']; ?>
                                </td>

                                <td class="px-6 py-4 text-gray-700">
                                    <?php echo $r['fecha_reserva']; ?>
                                </td>

                                <td class="px-6 py-4">
                                    <?php if ($r['estado'] === 'pagada'): ?>
                                        <span class="px-3 py-1 bg-green-100 text-green-700 text-xs font-semibold rounded-full">Pagada</span>
                                    <?php elseif ($r['estado'] === 'cancelada'): ?>
                                        <span class="px-3 py-1 bg-red-100 text-red-700 text-xs font-semibold rounded-full">Cancelada</span>
                                    <?php else: ?>
                                        <span class="px-3 py-1 bg-gray-200 text-gray-700 text-xs font-semibold rounded-full">
                                            <?php echo ucfirst($r['estado']); ?>
                                        </span>
                                    <?php endif; ?>
                                </td>
                            </tr>

                            <?php endwhile; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        
        <div id="view-agenda" class="hidden">
            <?php include 'llenar_agenda.php'; ?>
        </div>
    </main>

    <script>
        function rellenarFormulario(data) {
            for (let key in data) {
                let input = document.getElementById(key);
                if (input) {
                    input.value = data[key];
                }
            }
        }
    </script>

    <script>
    function limpiarFormulario() {
        const form = document.getElementById("form-crear-servicio");

        // 1. Reiniciar el formulario
        form.reset();

        // 2. Eliminar servicio_id si existe (solo se usa al editar)
        let hiddenId = document.getElementById("servicio_id_edit");
        if (hiddenId) hiddenId.remove();

        // 3. Volver la acción a CREAR
        form.querySelector("input[name='accion']").value = "crear_servicio";

        // 4. Restaurar texto del título
        document.querySelector("#contenedor-form-servicio h2").textContent = "Crear nuevo servicio";
    }
    </script>

    <script>
    function editarServicio(data) {
        // 1. Mostrar el formulario
        document.getElementById("contenedor-form-servicio").classList.remove("hidden");

        // 2. Cambiar la acción del formulario a EDITAR
        document.querySelector("#form-crear-servicio input[name='accion']").value = "editar_servicio";
        document.querySelector("#contenedor-form-servicio h2").textContent = "Editar servicio";
        document.getElementById("btn-abrir-form").classList.add("hidden");

        // 3. Insertar el ID del servicio
        let hiddenId = document.getElementById("servicio_id_edit");
        if (!hiddenId) {
            hiddenId = document.createElement("input");
            hiddenId.type = "hidden";
            hiddenId.name = "servicio_id";
            hiddenId.id = "servicio_id_edit";
            document.getElementById("form-crear-servicio").appendChild(hiddenId);
        }
        hiddenId.value = data.servicio_id;

        // 4. Rellenar automáticamente todos los campos
        rellenarFormulario({
            nombre: data.nombre_servicio,
            precio: data.precio,
            descripcion_breve: data.descripcion_breve,
            descripcion: data.descripcion,
            cupos: data.cupo_maximo,
            fecha_inicio: data.fecha_inicio,
            fecha_fin: data.fecha_fin,
            categoria_id: data.categoria_id,
            modalidad: data.modalidad,
            ubicacion: data.ubicacion,
            politicas: data.politicas
        });
        const horarioFinal = document.getElementById("horario-final");
        if (horarioFinal) {
            horarioFinal.value = data.horario ?? "";
        }

        // ===== RESTAURAR UI DE HORARIOS EN EDICIÓN =====
        if (data.horario) {

            // Limpiar estado previo
            document.getElementById("chk-mañana").checked = false;
            document.getElementById("chk-tarde").checked = false;

            document.getElementById("mat-inicio").value = "";
            document.getElementById("mat-fin").value = "";
            document.getElementById("ves-inicio").value = "";
            document.getElementById("ves-fin").value = "";

            const horario = data.horario;

            // Mañana
            const matMatch = horario.match(/Mañana:\s*(\d{2}:\d{2})\s*-\s*(\d{2}:\d{2})/);
            if (matMatch) {
                document.getElementById("chk-mañana").checked = true;
                document.getElementById("mat-inicio").value = matMatch[1];
                document.getElementById("mat-fin").value = matMatch[2];
            }

            // Tarde
            const vesMatch = horario.match(/Tarde:\s*(\d{2}:\d{2})\s*-\s*(\d{2}:\d{2})/);
            if (vesMatch) {
                document.getElementById("chk-tarde").checked = true;
                document.getElementById("ves-inicio").value = vesMatch[1];
                document.getElementById("ves-fin").value = vesMatch[2];
            }

            // Forzar actualización visual
            document.getElementById("chk-mañana").dispatchEvent(new Event("input"));
            document.getElementById("chk-tarde").dispatchEvent(new Event("input"));
        }

        // Mostrar ubicación si es presencial (modo edición)
        const modalidadSelect = document.getElementById("modalidad");
        const bloqueUbicacion = document.getElementById("bloque-ubicacion");
        const ubicacion = document.getElementById("ubicacion");

        if (data.modalidad === "Presencial") {
            bloqueUbicacion.classList.remove("hidden");
            ubicacion.required = true;
            ubicacion.value = data.ubicacion ?? "";
        } else {
            bloqueUbicacion.classList.add("hidden");
            ubicacion.required = false;
        }

        // 5. Mover la vista hacia el formulario
        document.getElementById("contenedor-form-servicio").scrollIntoView({
            behavior: "smooth"
        });
    }
    </script>

    <script>
    document.addEventListener("DOMContentLoaded", () => {

        const abrir = document.getElementById("btn-abrir-form");
        const cerrar = document.getElementById("btn-cerrar-form");
        const contenedor = document.getElementById("contenedor-form-servicio");
        const form = document.getElementById("form-crear-servicio");

        abrir.addEventListener("click", () => {
            limpiarFormulario();
            contenedor.classList.remove("hidden");
            abrir.classList.add("hidden");
        });

        cerrar.addEventListener("click", () => {
            limpiarFormulario();
            contenedor.classList.add("hidden");
            abrir.classList.remove("hidden");
        });

        form.addEventListener("submit", () => {
            contenedor.classList.add("hidden");
            abrir.classList.remove("hidden");
        });

    });
    </script>

    <script>
        function switchTab(tabId) {
            // Ocultar contenidos
            ['servicios', 'perfil', 'reservas', 'agenda'].forEach(t => {
                document.getElementById('view-' + t).classList.add('hidden');
                document.getElementById('tab-' + t).classList.remove('active-tab');
            });
            // Mostrar seleccionado
            document.getElementById('view-' + tabId).classList.remove('hidden');
            document.getElementById('tab-' + tabId).classList.add('active-tab');
        }
    </script>

    <!-- Manejar 25 palabras máximo para la descripción breve -->
    <script>
    document.addEventListener("DOMContentLoaded", () => {

        const input = document.getElementById("descripcion_breve");
        const form = document.querySelector("form");

        function contarPalabras(texto) {
            return texto.trim().split(/\s+/).filter(w => w.length > 0).length;
        }

        input.addEventListener("input", () => {
            let palabras = input.value.trim().split(/\s+/);

            if (palabras.length > 25) {
                palabras = palabras.slice(0, 25);
                input.value = palabras.join(" ") + " ";
                mostrarTooltip(input, "Máximo 25 palabras permitidas");
            }
        });

        // Verificación antes de enivar
        form.addEventListener("submit", (e) => {
            const total = contarPalabras(input.value);

            if (total === 0) {
                e.preventDefault();
                mostrarTooltip(input, "Debe ingresar un resumen del servicio");
                input.focus();
                return;
            }

            if (total > 25) {
                e.preventDefault();
                mostrarTooltip(input, "Máximo 25 palabras permitidas");
                input.focus();
                return;
            }

        });

        function mostrarTooltip(el, msg) {
            el.setCustomValidity(msg);
            el.reportValidity();
            setTimeout(() => el.setCustomValidity(""), 2000);
        }
    });
    </script>

    <script>
    document.addEventListener("DOMContentLoaded", () => {
        // Guardar los valores originales al cargar la vista (inicialmente)
        let perfilOriginal = {
            bio: "",
            portafolio: "",
            contacto: ""
        };

        // Helper: lee de forma segura el value de un selector, devuelve "" si no existe
        function safeValue(selector) {
            const el = document.querySelector(selector);
            return el ? el.value : "";
        }

        // Esta función se ejecutará cuando abras el tab "view-perfil"
        function obtenerValoresOriginales() {
            perfilOriginal = {
                bio: safeValue("textarea[name='bio']"),
                portafolio: safeValue("input[name='portafolio_url']"),
                contacto: safeValue("input[name='contacto']")
            };
            // útil para debug: // console.log("perfilOriginal guardado:", perfilOriginal);
        }

        // Restaurar los valores cuando el usuario presiona "Cancelar"
        const btnCancelar = document.getElementById("btnCancelar");
        if (btnCancelar) {
            btnCancelar.addEventListener("click", (e) => {
                // prevenir acciones no deseadas (por si en algún navegador el botón actúa distinto)
                e.preventDefault();

                // Restaurar sólo si existen los elementos
                const bioEl = document.querySelector("textarea[name='bio']");
                const portEl = document.querySelector("input[name='portafolio_url']");
                const contEl = document.querySelector("input[name='contacto']");

                if (bioEl) bioEl.value = perfilOriginal.bio ?? "";
                if (portEl) portEl.value = perfilOriginal.portafolio ?? "";
                if (contEl) contEl.value = perfilOriginal.contacto ?? "";
            });
        }

        // Ejecutar la captura inicial de los valores originales
        obtenerValoresOriginales();

        // Exponer la función para que switchTab pueda llamarla (si switchTab está en otro scope)
        window.obtenerValoresOriginales = obtenerValoresOriginales;
    });
    </script>

    <script>
    document.getElementById("form-crear-servicio")
        .addEventListener("submit", function(e) {

        const horario = document.getElementById("horario-final").value.trim();

        if (!horario) {
            e.preventDefault();
            alert("Debe seleccionar al menos una jornada y definir su horario.");
        }
    });
    </script>

    <script>
    document.addEventListener("DOMContentLoaded", () => {
        const modalidad = document.getElementById("modalidad");
        const bloqueUbicacion = document.getElementById("bloque-ubicacion");
        const ubicacion = document.getElementById("ubicacion");

        modalidad.addEventListener("change", () => {
            if (modalidad.value === "Presencial") {
                bloqueUbicacion.classList.remove("hidden");
                ubicacion.required = true;
            } else {
                bloqueUbicacion.classList.add("hidden");
                ubicacion.required = false;
                ubicacion.value = "";
            }
        });
    });
    </script>

    <!-- Error al intentar eliminar un servicio ya reservado -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <?php if (isset($_SESSION['error_eliminar'])): ?>
        <script>
        Swal.fire({
            icon: 'error',
            title: 'Política infringida',
            text: '<?php echo $_SESSION['error_eliminar']; ?>',
            confirmButtonColor: '#1A4B8C'
        });
        </script>
    <?php unset($_SESSION['error_eliminar']); endif; ?>

</body>
</html>