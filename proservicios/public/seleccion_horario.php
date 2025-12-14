<?php
// seleccion_horario.php
?>

<!-- FILA SUPERIOR: HORARIOS | MODALIDAD -->
<div class="grid grid-cols-1 md:grid-cols-2 gap-6 md:col-span-2">

    <!-- HORARIOS (CHECKBOXES) -->
    <div class="flex flex-col">
        <label class="font-semibold text-sm mb-2">Horarios:</label>

        <div class="flex gap-6">
            <label class="flex items-center gap-2">
                <input type="checkbox" id="chk-mañana">
                <span>Mañana</span>
            </label>

            <label class="flex items-center gap-2">
                <input type="checkbox" id="chk-tarde">
                <span>Tarde</span>
            </label>
        </div>
    </div>

    <!-- Modalidad -->
    <div class="flex flex-col">
        <label for="modalidad" class="font-semibold text-sm mb-1">Modalidad:</label>
        <select name="modalidad" id="modalidad"
                class="border p-2 rounded w-full" required>
            <option value="" disabled selected>-- Seleccione modalidad --</option>
            <option value="Presencial">Presencial</option>
            <option value="Online">Online</option>
        </select>
    </div>

</div>

<!-- FILA INFERIOR: BLOQUES DE HORARIO (EN PARALELO) -->
<div id="contenedor-horarios"
     class="hidden mt-4 grid grid-cols-1 md:grid-cols-2 gap-6 md:col-span-2">

    <!-- Mañana -->
    <div id="bloque-mañana" class="hidden flex flex-col gap-2">
        <span class="font-semibold text-sm">Mañana</span>
        <div class="flex gap-2 items-center">
            <input type="time" id="mat-inicio" class="border p-2 rounded">
            <span>-</span>
            <input type="time" id="mat-fin" class="border p-2 rounded">
        </div>
    </div>

    <!-- Tarde -->
    <div id="bloque-tarde" class="hidden flex flex-col gap-2">
        <span class="font-semibold text-sm">Tarde</span>
        <div class="flex gap-2 items-center">
            <input type="time" id="ves-inicio" class="border p-2 rounded">
            <span>-</span>
            <input type="time" id="ves-fin" class="border p-2 rounded">
        </div>
    </div>

</div>

<!-- CAMPO FINAL -->
<input type="hidden" name="horario" id="horario-final">

<script>
document.addEventListener("DOMContentLoaded", () => {

    const chkMat = document.getElementById("chk-mañana");
    const chkVes = document.getElementById("chk-tarde");

    const contenedor = document.getElementById("contenedor-horarios");
    const bloqueMat = document.getElementById("bloque-mañana");
    const bloqueVes = document.getElementById("bloque-tarde");

    const matIni = document.getElementById("mat-inicio");
    const matFin = document.getElementById("mat-fin");
    const vesIni = document.getElementById("ves-inicio");
    const vesFin = document.getElementById("ves-fin");

    const salida = document.getElementById("horario-final");

    function actualizarVista() {
        const hayAlgo = chkMat.checked || chkVes.checked;
        contenedor.classList.toggle("hidden", !hayAlgo);

        bloqueMat.classList.toggle("hidden", !chkMat.checked);
        bloqueVes.classList.toggle("hidden", !chkVes.checked);

        construirHorario();
    }

    function construirHorario() {
        let partes = [];

        if (chkMat.checked && matIni.value && matFin.value) {
            partes.push(`Mañana: ${matIni.value} - ${matFin.value}`);
        }

        if (chkVes.checked && vesIni.value && vesFin.value) {
            partes.push(`Tarde: ${vesIni.value} - ${vesFin.value}`);
        }

        salida.value = partes.join(" | ");
    }

    [chkMat, chkVes, matIni, matFin, vesIni, vesFin]
        .forEach(el => el.addEventListener("input", () => {
            actualizarVista();
            construirHorario();
        }));
});
</script>