<?php
// 1. SEGURIDAD: VERIFICAR QUE SEA CLIENTE
require_once '../src/Servicios/Seguridad.php';
Seguridad::requerirRol('cliente');
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio - ProServicios</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        :root {
            --primary: #1A4B8C;
            --primary-dark: #0D2F5A;
            --secondary: #6C757D;
            --text-dark: #212529;
            --text-light: #FFFFFF;
            --bg-light: #F8F9FA;
            --bg-white: #FFFFFF;
            --success: #28A745;
            --danger: #DC3545;
            --info: #17A2B8;
            --warning: #FFC107;
            --gray-light: #8F9FA;
            --border: #DEE2E6;
        }

        body {
            font-family: Arial, sans-serif;
            background-color: var(--bg-light);
            line-height: 1.6;
            color: var(--text-dark);
        }

        /* Hero Section */
        .hero {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: var(--text-light);
            padding: 4rem 0;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: url('https://images.unsplash.com/photo-1504674900247-0877df9cc836?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80') no-repeat center center;
            background-size: cover;
            opacity: 0.1;
            z-index: 0;
        }

        .hero-content { position: relative; z-index: 1; }

        .hero h1 { font-size: 2.5rem; margin-bottom: 1rem; font-weight: 800; }
        .hero p { font-size: 1.2rem; margin-bottom: 2rem; max-width: 600px; margin-left: auto; margin-right: auto; }

        .hero-button {
            background-color: var(--text-light);
            color: var(--primary);
            padding: 0.75rem 1.5rem;
            border-radius: 20px;
            font-weight: bold;
            text-decoration: none;
            display: inline-block;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .hero-button:hover {
            background-color: #e9ecef;
            transform: translateY(-2px);
            box-shadow: 0 6px 8px rgba(0,0,0,0.15);
        }

        /* Trending Section */
        .trending-section { padding: 3rem 0; text-align: center; }
        .trending-section h2 { font-size: 2rem; margin-bottom: 2rem; color: var(--text-dark); }
        
        .trending-cards {
            display: flex; gap: 1.5rem; overflow-x: auto; padding: 1rem 2rem;
            scroll-behavior: smooth; -ms-overflow-style: none; scrollbar-width: none;
        }
        .trending-cards::-webkit-scrollbar { display: none; }

        .trending-card {
            background-color: var(--bg-white); border-radius: 10px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            width: 250px; flex-shrink: 0; padding: 1rem; text-align: left;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            display: flex; flex-direction: column; justify-content: space-between; min-height: 320px;
        }
        .trending-card:hover { transform: translateY(-5px); box-shadow: 0 4px 8px rgba(0,0,0,0.15); }

        .trending-card-image {
            width: 100%; height: 150px; background-color: var(--gray-light); border-radius: 8px;
            margin-bottom: 1rem; background-size: cover; background-position: center;
        }

        .trending-card h3 { font-size: 1.2rem; color: var(--primary); margin-bottom: 0.5rem; font-weight: 700; }
        .trending-card p { font-size: 0.9rem; color: var(--secondary); margin-bottom: 1rem; }
        .trending-card-price { font-size: 1.5rem; font-weight: bold; color: var(--text-dark); margin-bottom: 0.5rem; }
        
        .trending-card-status { display: inline-block; padding: 0.25rem 0.5rem; border-radius: 20px; font-size: 0.8rem; font-weight: bold; }
        .status-available { background-color: #d4edda; color: var(--success); }
        .status-unavailable { background-color: #f8d7da; color: var(--danger); }

        .trending-card-button {
            width: 100%; background-color: var(--primary); color: var(--text-light); padding: 0.75rem;
            border-radius: 5px; text-align: center; text-decoration: none; font-weight: bold;
            display: block; margin-top: 1rem; transition: background-color 0.2s ease;
        }
        .trending-card-button:hover { background-color: #163c6b; }

        .footer { background-color: var(--secondary); color: var(--text-light); text-align: center; padding: 1rem 0; font-size: 0.9rem; margin-top: 2rem; }
    </style>
</head>
<body>

    <?php include 'menu_header.php'; ?>

    <section id="inicio" class="hero">
        <div class="hero-content">
            <h1>Descubre los mejores servicios cerca de ti</h1>
            <p>Clases, consultorías, masajes y mucho más. Todo en un solo lugar.</p>
            <a href="catalogo.php" class="hero-button">Explorar Catálogo</a>
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
             <!-- Para Clientes (usuario ya autenticado como cliente) -->
                <div class="flex-1 bg-white p-8 rounded-xl shadow-md">
                    <div class="bg-[#1A4B8C] w-12 h-12 rounded-full flex items-center justify-center mb-6">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-3">Soporte para clientes</h3>
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
                
            </div>
        </div>
    </section>



























 

















    <footer class="bg-gray-800 text-white  py-6 mt-10">
             © Copyright  2025 ProServicios. Todos los derechos reservados.
    </footer>

</body>
</html>