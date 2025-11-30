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

    <section id="precios" class="trending-section">
        <h2>Cursos en Tendencia</h2>
        <div class="trending-cards">
            <div class="trending-card">
                <div class="trending-card-image" style="background-image: url('https://via.placeholder.com/250x150/1A4B8C/FFFFFF?text=Piano');"></div>
                <h3>Clase de Piano Avanzado</h3>
                <p>Taller intensivo de armonía y técnica avanzada.</p>
                <div class="trending-card-price">$65.00</div>
                <span class="trending-card-status status-available">Disponible</span>
                <a href="catalogo.php?q=Piano" class="trending-card-button">Ver más</a>
            </div>
            <div class="trending-card">
                <div class="trending-card-image" style="background-image: url('https://via.placeholder.com/250x150/1A4B8C/FFFFFF?text=SEO');"></div>
                <h3>Consultoría SEO Principiantes</h3>
                <p>Aprende a optimizar tu sitio web para Google.</p>
                <div class="trending-card-price">$40.00</div>
                <span class="trending-card-status status-available">Disponible</span>
                <a href="catalogo.php?q=SEO" class="trending-card-button">Ver más</a>
            </div>
            <div class="trending-card">
                <div class="trending-card-image" style="background-image: url('https://via.placeholder.com/250x150/1A4B8C/FFFFFF?text=Masaje');"></div>
                <h3>Masaje Relajante Express</h3>
                <p>Sesión anti-estrés de 30 minutos.</p>
                <div class="trending-card-price">$25.00</div>
                <span class="trending-card-status status-available">Disponible</span>
                <a href="catalogo.php?q=Masaje" class="trending-card-button">Ver más</a>
            </div>
            <div class="trending-card">
                <div class="trending-card-image" style="background-image: url('https://via.placeholder.com/250x150/1A4B8C/FFFFFF?text=Pintura');"></div>
                <h3>Curso Completo de Pintura</h3>
                <p>Técnicas al óleo, acrílico y acuarela.</p>
                <div class="trending-card-price">$90.00</div>
                <span class="trending-card-status status-unavailable">Sin cupo</span>
                <button disabled class="trending-card-button" style="background-color: var(--secondary); cursor: not-allowed;">Sin cupo</button>
            </div>
            <div class="trending-card">
                <div class="trending-card-image" style="background-image: url('https://via.placeholder.com/250x150/1A4B8C/FFFFFF?text=Cocina');"></div>
                <h3>Curso de Cocina Mediterránea</h3>
                <p>Aprende a preparar platos saludables y deliciosos.</p>
                <div class="trending-card-price">$55.00</div>
                <span class="trending-card-status status-available">Disponible</span>
                <a href="catalogo.php?q=Cocina" class="trending-card-button">Ver más</a>
            </div>
        </div>
    </section>

    <footer class="footer">
        Contactate con  el Administrador +593 09545231
    </footer>

</body>
</html>