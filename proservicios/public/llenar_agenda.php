<?php
require_once '../config/Database.php';

// Función para obtener los eventos guardados
function obtenerEventosGuardados($usuario_id) {
    // Conexión a la base de datos
    $database = new Database();
    $conn = $database->getConnection();

    // Consultar los eventos para este proveedor
    $query = "SELECT evento_id, nombre_evento, descripcion, dia, hora 
              FROM eventos 
              WHERE proveedor_id = :proveedor_id";
    
    $stmt = $conn->prepare($query);
    $stmt->bindParam(":proveedor_id", $usuario_id);
    $stmt->execute();
    
    // Devolver los resultados como un arreglo
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Obtener los eventos guardados para el usuario
$usuario_id = $_SESSION['usuario_id'];  // Asumimos que el ID del usuario está en la sesión
$eventosGuardados = obtenerEventosGuardados($usuario_id);
?>

<h2 class="text-2xl font-bold text-gray-800 mb-6">Agenda de mis servicios</h2>

<div class="bg-white p-6 rounded-xl shadow border border-gray-200 overflow-x-auto">
    <table class="min-w-max border-collapse">
        <thead>
            <tr>
                <th class="border p-3 bg-gray-100 font-bold text-gray-700 w-32"></th>

                <?php for ($h = 0; $h <= 23; $h++): 
                    $hora = str_pad($h, 2, "0", STR_PAD_LEFT) . ":00";
                ?>
                    <th class="border p-3 bg-gray-100 text-xs font-semibold text-gray-600">
                        <?= $hora ?>
                    </th>
                <?php endfor; ?>
            </tr>
        </thead>

        <tbody>
            <?php 
            $dias = ["Lunes", "Martes", "Miércoles", "Jueves", "Viernes", "Sábado", "Domingo"];
            foreach ($dias as $dia):
            ?>
                <tr class="text-center">
                    <td class="border p-3 bg-gray-50 font-semibold text-gray-700">
                        <?= $dia ?>
                    </td>

                    <?php for ($h = 0; $h <= 23; $h++): 
                        // Verificar si hay un evento para este día y hora
                        $eventoExistente = false;
                        foreach ($eventosGuardados as $evento) {
                            if ($evento['dia'] === $dia && (int)$evento['hora'] === $h) {
                                $eventoExistente = $evento;
                                break;
                            }
                        }
                    ?>
                        <td class="border p-3 text-sm text-gray-600 hover:bg-blue-50 cursor-pointer celda-agenda 
                            <?= $eventoExistente ? 'bg-green-100' : '' ?>" 
                            data-dia="<?= $dia ?>" 
                            data-hora="<?= $h ?>" 
                            data-evento-id="<?= $eventoExistente ? $eventoExistente['evento_id'] : '' ?>">
                            <!-- La celda está marcada si tiene un evento -->
                        </td>
                    <?php endfor; ?>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Formulario para agregar un evento -->
<div id="form-evento" class="hidden mt-6 bg-gray-50 p-6 rounded-xl border">
    <h3 class="text-xl font-bold text-gray-800 mb-4">Nuevo evento</h3>

    <form action="guardar_evento.php" method="POST" class="grid grid-cols-1 gap-4">
        <input type="hidden" name="dia" id="dia">
        <input type="hidden" name="hora" id="hora">
        <input type="hidden" name="evento_id" id="evento_id"> <!-- Para identificar el evento editado -->

        <div>
            <label class="block font-semibold text-sm mb-1">Nombre del evento</label>
            <input type="text" name="nombre_evento"
                   placeholder="Ej: Reunión, Clase, Proyecto"
                   class="border p-2 rounded w-full" required>
        </div>

        <div>
            <label class="block font-semibold text-sm mb-1">Detalles del evento</label>
            <textarea name="descripcion"
                      placeholder="Detalles del evento"
                      class="border p-2 rounded w-full"
                      rows="3"></textarea>
        </div>

        <div class="flex justify-end gap-4 mt-4">
            <button type="button" id="btn-cancelar-evento"
                    class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">
                Cancelar
            </button>

            <button type="submit"
                    class="px-4 py-2 bg-[#1A4B8C] text-white rounded hover:bg-blue-900">
                Guardar
            </button>
        </div>
    </form>
</div>

<script>
document.addEventListener("DOMContentLoaded", () => {

    const celdas = document.querySelectorAll(".celda-agenda");
    const formEvento = document.getElementById("form-evento");
    const btnCancelar = document.getElementById("btn-cancelar-evento");
    
    // Al hacer clic en una celda
    celdas.forEach(celda => {
        celda.addEventListener("click", function () {
        const dia = this.dataset.dia;
        const hora = this.dataset.hora;
        const eventoId = this.dataset.eventoId;

        // Asignar siempre día y hora
        document.getElementById("dia").value = dia;
        document.getElementById("hora").value = hora;

        // Mostrar el formulario
        formEvento.classList.remove("hidden");
        formEvento.scrollIntoView({ behavior: "smooth" });

        // LIMPIAR siempre primero
        document.querySelector("input[name='nombre_evento']").value = '';
        document.querySelector("textarea[name='descripcion']").value = '';
        document.getElementById("evento_id").value = '';

        // Solo si existe evento, cargamos los datos
        if (eventoId && eventoId !== '') {
            fetch(`obtener_evento.php?evento_id=${eventoId}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error("Error al obtener evento");
                    }
                    return response.json();
                })
                .then(data => {
                    console.log("DATA RECIBIDA:", data); // 👈 para verificar

                    document.querySelector("input[name='nombre_evento']").value =
                        data.nombre_evento ?? '';

                    document.querySelector("textarea[name='descripcion']").value =
                        data.descripcion ?? '';

                    document.getElementById("evento_id").value =
                        data.evento_id ?? '';
                })
                .catch(err => {
                    console.error("Fetch error:", err);
                });
        }

    });

    });

    // Al hacer clic en "Cancelar"
    btnCancelar.addEventListener("click", () => {
        // Ocultar el formulario
        formEvento.classList.add("hidden");

        // Limpiar los campos del formulario solo si no estaba editando
        const eventoId = document.getElementById("evento_id").value;
        if (!eventoId) {  // Si es un nuevo evento
            document.querySelector("input[name='nombre_evento']").value = '';
            document.querySelector("textarea[name='descripcion']").value = '';
        }
        
        // Limpiar los valores ocultos de día y hora
        document.getElementById("dia").value = '';
        document.getElementById("hora").value = '';
        document.getElementById("evento_id").value = '';
    });
});
</script>