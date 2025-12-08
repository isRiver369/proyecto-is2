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

<section id="precios" class="trending-section py-12 bg-gray-50">
    <div class="container mx-auto px-4">
        <h2 class="text-3xl font-bold text-gray-800 text-center mb-10">Cursos en Tendencia</h2>
        <div class="trending-cards grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-6">
            <div class="trending-card bg-white rounded-xl border border-gray-200 overflow-hidden flex flex-col h-full">
                <div class="trending-card-image h-32 bg-cover bg-center" style="background-image: url('https://via.placeholder.com/250x150/1A4B8C/FFFFFF?text=Piano');"></div>
                
                <div class="p-5 flex flex-col flex-grow">
                    <h3 class="font-bold text-gray-800 text-base mb-2 line-clamp-1">Clase de Piano Avanzado</h3>
                    <p class="text-gray-600 text-sm mb-3 line-clamp-2 flex-grow">Taller intensivo de armonía y técnica avanzada.</p>
                    
                    <div class="flex justify-between items-center mt-auto pt-2">
                        <div class="trending-card-price font-bold text-lg text-[#1A4B8C]">$65.00</div>
                        
                        <span class="trending-card-status status-available text-xs font-medium bg-green-100 text-green-800 px-2 py-1 rounded-full">
                            Disponible
                        </span>
                    </div>
                    
                    <a href="catalogo.php?q=Piano" class="mt-3 trending-card-button block text-center w-full py-2 bg-[#1A4B8C] text-white font-medium rounded-lg hover:bg-[#153a6d] transition-colors focus:ring-2 focus:ring-[#1A4B8C] focus:outline-none">
                        Ver detalles
                    </a>
                </div>
            </div>

            <div class="trending-card bg-white rounded-xl border border-gray-200 overflow-hidden flex flex-col h-full">
                <div class="trending-card-image h-32 bg-cover bg-center" style="background-image: url('https://via.placeholder.com/250x150/1A4B8C/FFFFFF?text=SEO');"></div>
                <div class="p-5 flex flex-col flex-grow">
                    <h3 class="font-bold text-gray-800 text-base mb-2 line-clamp-1">Consultoría SEO Principiantes</h3>
                    <p class="text-gray-600 text-sm mb-3 line-clamp-2 flex-grow">Aprende a optimizar tu sitio web para Google.</p>
                    <div class="flex justify-between items-center mt-auto pt-2">
                        <div class="trending-card-price font-bold text-lg text-[#1A4B8C]">$40.00</div>
                        <span class="trending-card-status status-available text-xs font-medium bg-green-100 text-green-800 px-2 py-1 rounded-full">Disponible</span>
                    </div>
                    <a href="catalogo.php?q=SEO" class="mt-3 trending-card-button block text-center w-full py-2 bg-[#1A4B8C] text-white font-medium rounded-lg hover:bg-[#153a6d] transition-colors focus:ring-2 focus:ring-[#1A4B8C] focus:outline-none">Ver Detalles</a>
                </div>
            </div>

            <div class="trending-card bg-white rounded-xl border border-gray-200 overflow-hidden flex flex-col h-full">
                <div class="trending-card-image h-32 bg-cover bg-center" style="background-image: url('https://via.placeholder.com/250x150/1A4B8C/FFFFFF?text=Masaje');"></div>
                <div class="p-5 flex flex-col flex-grow">
                    <h3 class="font-bold text-gray-800 text-base mb-2 line-clamp-1">Masaje Relajante Express</h3>
                    <p class="text-gray-600 text-sm mb-3 line-clamp-2 flex-grow">Sesión anti-estrés de 30 minutos.</p>
                    <div class="flex justify-between items-center mt-auto pt-2">
                        <div class="trending-card-price font-bold text-lg text-[#1A4B8C]">$25.00</div>
                        <span class="trending-card-status status-available text-xs font-medium bg-green-100 text-green-800 px-2 py-1 rounded-full">Disponible</span>
                    </div>
                    <a href="catalogo.php?q=Masaje" class="mt-3 trending-card-button block text-center w-full py-2 bg-[#1A4B8C] text-white font-medium rounded-lg hover:bg-[#153a6d] transition-colors focus:ring-2 focus:ring-[#1A4B8C] focus:outline-none">Ver Detalles</a>
                </div>
            </div>

            <div class="trending-card bg-white rounded-xl border border-gray-200 overflow-hidden flex flex-col h-full opacity-90">
                <div class="trending-card-image h-32 bg-cover bg-center" style="background-image: url('https://via.placeholder.com/250x150/1A4B8C/FFFFFF?text=Pintura');"></div>
                <div class="p-5 flex flex-col flex-grow">
                    <h3 class="font-bold text-gray-800 text-base mb-2 line-clamp-1">Curso Completo de Pintura</h3>
                    <p class="text-gray-600 text-sm mb-3 line-clamp-2 flex-grow">Técnicas al óleo, acrílico y acuarela.</p>
                    <div class="flex justify-between items-center mt-auto pt-2">
                        <div class="trending-card-price font-bold text-lg text-gray-400 line-through">$90.00</div>
                        <span class="trending-card-status status-unavailable text-xs font-medium bg-gray-100 text-gray-600 px-2 py-1 rounded-full">
                            Sin cupo
                        </span>
                    </div>
                    <button disabled class="mt-3 trending-card-button block text-center w-full py-2 bg-gray-300 text-gray-500 font-medium rounded-lg cursor-not-allowed">
                        Sin cupo
                    </button>
                </div>
            </div>

            <div class="trending-card bg-white rounded-xl border border-gray-200 overflow-hidden flex flex-col h-full">
                <div class="trending-card-image h-32 bg-cover bg-center" style="background-image: url('https://via.placeholder.com/250x150/1A4B8C/FFFFFF?text=Cocina');"></div>
                <div class="p-5 flex flex-col flex-grow">
                    <h3 class="font-bold text-gray-800 text-base mb-2 line-clamp-1">Curso de Cocina Mediterránea</h3>
                    <p class="text-gray-600 text-sm mb-3 line-clamp-2 flex-grow">Aprende a preparar platos saludables y deliciosos.</p>
                    <div class="flex justify-between items-center mt-auto pt-2">
                        <div class="trending-card-price font-bold text-lg text-[#1A4B8C]">$55.00</div>
                        <span class="trending-card-status status-available text-xs font-medium bg-green-100 text-green-800 px-2 py-1 rounded-full">Disponible</span>
                    </div>
                    <a href="catalogo.php?q=Cocina" class="mt-3 trending-card-button block text-center w-full py-2 bg-[#1A4B8C] text-white font-medium rounded-lg hover:bg-[#153a6d] transition-colors focus:ring-2 focus:ring-[#1A4B8C] focus:outline-none">Ver Detalles</a>
                </div>
            </div>

        </div>
    </div>
</section>
     <section class="py-16 bg-gray-100">
        <div class="container mx-auto px-4 max-w-6xl">
            <h2 class="text-3xl font-bold text-center text-gray-800 mb-12">Contáctanos</h2>
            <div class="flex flex-col md:flex-row gap-10">
                <!-- Para Clientes -->
                <div class="flex-1 bg-white p-8 rounded-xl shadow-md">
                    <div class="bg-[#1A4B8C] w-12 h-12 rounded-full flex items-center justify-center mb-6">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-3">¿Eres cliente?</h3>
                    <p class="text-gray-600 mb-4">¿Tienes dudas sobre un servicio o necesitas ayuda con tu reserva?</p>
                    <ul class="space-y-2 text-gray-700">
                        <li class="flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-[#1A4B8C] mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                            soporte@proservicios.com
                        </li>
                        <li class="flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-[#1A4B8C] mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                            </svg>
                            +593 95 452 3141
                        </li>
                    </ul>
                </div>

                <!-- Para Proveedores -->
                <div class="flex-1 bg-white p-8 rounded-xl shadow-md">
                    <div class="bg-[#6C757D] w-12 h-12 rounded-full flex items-center justify-center mb-6">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m8 0H8m8 0v6a2 2 0 01-2 2H10a2 2 0 01-2-2V6" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-3">¿Quieres ser proveedor?</h3>
                    <p class="text-gray-600 mb-4">Únete a nuestra red de profesionales y ofrece tus servicios.</p>
                    <ul class="space-y-2 text-gray-700">
                        <li class="flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-[#6C757D] mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                            proveedores@gmail.com
                        </li>
                        <li class="flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-[#6C757D] mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                            </svg>
                            +593 95 452 3142
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </section>
     <footer class="bg-gray-800 text-white  py-6 mt-10">
             © Copyright  2025 ProServicios. Todos los derechos reservados.
    </footer>

</body>
</html>