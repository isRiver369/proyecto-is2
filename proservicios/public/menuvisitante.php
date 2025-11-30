<?php
session_start();
// Si el usuario ya inició sesión, no tiene nada que hacer en la página de "Visitante".
// Lo redirigimos automáticamente a SU menú de cliente.
if (isset($_SESSION['usuario_id'])) {
    header("Location: menucliente.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio - ProServicios</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        :root { --primary: #1A4B8C; --primary-dark: #0D2F5A; --secondary: #6C757D; --text-light: #FFFFFF; --bg-light: #F8F9FA; }
        body { font-family: Arial, sans-serif; background-color: var(--bg-light); }
        /* Hero Section */
        .hero {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: var(--text-light);
            padding: 4rem 0;
            text-align: center;
            position: relative;
        }
        .hero h1 { font-size: 2.5rem; margin-bottom: 1rem; font-weight: 800; }
        .hero-button {
            background-color: var(--text-light); color: var(--primary);
            padding: 0.75rem 1.5rem; border-radius: 20px; font-weight: bold;
            display: inline-block; margin-top: 20px;
        }
        
    </style>
</head>
<body>

    <?php include 'menu_header.php'; ?>

    <section id="inicio" class="hero">
        <h1>Descubre los mejores servicios cerca de ti</h1>
        <p>Clases, consultorías, masajes y mucho más. Todo en un solo lugar.</p>
        <a href="catalogo.php" class="hero-button">Explorar Catálogo</a>
    </section>

    <section class="py-12 bg-white text-center">
        <h2 class="text-3xl font-bold text-gray-800 mb-8">¿Cómo funciona ProServicios?</h2>
        <div class="flex flex-wrap justify-center gap-8">
            <div class="w-64 p-6 shadow-lg rounded-xl bg-gray-50">
                <div class="w-10 h-10 bg-[#1A4B8C] text-white rounded-full flex items-center justify-center mx-auto mb-4 font-bold">1</div>
                <h3 class="font-bold text-xl mb-2">Explora</h3>
                <p class="text-gray-600">Navega por nuestro catálogo de servicios.</p>
            </div>
            <div class="w-64 p-6 shadow-lg rounded-xl bg-gray-50">
                <div class="w-10 h-10 bg-[#1A4B8C] text-white rounded-full flex items-center justify-center mx-auto mb-4 font-bold">2</div>
                <h3 class="font-bold text-xl mb-2">Reserva</h3>
                <p class="text-gray-600">Elige fecha y hora. ¡Es rápido y fácil!</p>
            </div>
            <div class="w-64 p-6 shadow-lg rounded-xl bg-gray-50">
                <div class="w-10 h-10 bg-[#1A4B8C] text-white rounded-full flex items-center justify-center mx-auto mb-4 font-bold">3</div>
                <h3 class="font-bold text-xl mb-2">Disfruta</h3>
                <p class="text-gray-600">Asiste a tu servicio y vive la experiencia.</p>
            </div>
        </div>
    </section>

    <footer class="bg-gray-800 text-white text-center py-6 mt-10">
        Contactate con el Administrador +593 09545231
    </footer>

</body>
</html>