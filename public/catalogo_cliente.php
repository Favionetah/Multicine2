<?php
session_start();
// Solo verificamos que esté logueado, no importa el rol, 
// o incluso puedes quitar esto si el catálogo es público.
if (!isset($_SESSION['rol'])) {
    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="referrer" content="no-referrer">
    <title>Multicine - Cartelera</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;600;800;900&display=swap');

        :root {
            --primary: #00d2d3;
            --dark-bg: #050505;
            --danger: #ff4757;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Montserrat', sans-serif;
            background-color: var(--dark-bg);
            color: white;
            overflow-x: hidden;
        }

        /* Fondo */
        .background-container {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -2;
        }

        .background-image {
            width: 100%;
            height: 100%;
            background-image: url('img/fondo.jpg');
            background-size: cover;
            background-position: center;
            filter: brightness(0.2) blur(8px);
            transform: scale(1.05);
        }

        /* Header Simplificado */
        header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 25px 50px;
            position: sticky;
            top: 0;
            z-index: 100;
            background: rgba(5, 5, 5, 0.7);
            backdrop-filter: blur(15px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }

        .logo {
            font-weight: 900;
            color: var(--primary);
            font-size: 1.5rem;
            text-decoration: none;
        }

        nav ul {
            display: flex;
            list-style: none;
            gap: 40px;
        }

        nav ul li a {
            text-decoration: none;
            color: #aaa;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 0.85rem;
            letter-spacing: 1px;
            transition: 0.3s;
        }

        nav ul li a:hover {
            color: var(--primary);
        }

        /* Main Content */
        main {
            padding: 40px 50px;
            max-width: 1400px;
            margin: 0 auto;
        }

        h1.hero-title {
            font-size: 4.5rem;
            line-height: 0.9;
            font-weight: 900;
            text-transform: uppercase;
            color: white;
            margin: 20px 0 60px 0;
            letter-spacing: -2px;
        }

        h1.hero-title span {
            color: var(--primary);
            display: block;
        }

        /* Grid de Películas */
        .movies-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
            gap: 30px;
        }

        .movie-card {
            text-align: center;
            cursor: pointer;
            transition: 0.4s;
            position: relative;
        }

        .movie-card:hover {
            transform: translateY(-10px);
        }

        .poster-frame {
            width: 100%;
            height: 340px;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.6);
            border: 1px solid #222;
            margin-bottom: 15px;
            position: relative;
        }

        .poster-frame img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: 0.5s;
        }

        /* Overlay de "Comprar" al pasar el mouse */
        .movie-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 210, 211, 0.8);
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: 0.3s;
        }

        .movie-card:hover .movie-overlay {
            opacity: 1;
        }

        .btn-buy {
            background: white;
            color: black;
            padding: 10px 20px;
            border-radius: 20px;
            font-weight: 800;
            text-decoration: none;
            font-size: 0.8rem;
        }

        .movie-title {
            font-weight: 800;
            font-size: 1rem;
            color: white;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            text-transform: uppercase;
        }

        .movie-info {
            font-size: 0.75rem;
            color: #888;
            margin-top: 5px;
        }
    </style>
</head>

<body>

    <div class="background-container">
        <div class="background-image"></div>
    </div>

    <header>
        <a href="#" class="logo">MULTICINE</a>
        <nav>
            <ul>
                <li><a href="catalogo.php">CARTELERA</a></li>
                <li><a href="mis_compras.php">MIS TICKETS</a></li>
                <li><a href="api.php?action=logout" style="color:var(--danger)">SALIR</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <h1 class="hero-title">PELÍCULAS EN<br><span>CARTELERA</span></h1>
        <div class="movies-grid" id="gridPeliculas">
        </div>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', cargarPeliculas);

        function cargarPeliculas() {
            fetch('api.php?action=listar_peliculas')
                .then(res => res.json())
                .then(data => {
                    const grid = document.getElementById('gridPeliculas');
                    grid.innerHTML = data.map(p => {
                        const ruta = p.imagenPoster.startsWith('http') ? p.imagenPoster : `img/${p.imagenPoster}`;
                        return `
                        <div class="movie-card" onclick="verDetalles(${p.idPelicula})">
                            <div class="poster-frame">
                                <img src="${ruta}" onerror="this.src='img/fondo.jpg'">
                                <div class="movie-overlay">
                                    <span class="btn-buy">VER FUNCIONES</span>
                                </div>
                            </div>
                            <div class="movie-title">${p.titulo}</div>
                            <div class="movie-info">${p.genero} | ${p.clasificacion}</div>
                        </div>`;
                    }).join('');
                });
        }

        function verDetalles(id) {
            // Redirigir a la página de selección de horario/asientos
            window.location.href = `detalles_pelicula.php?id=${id}`;
        }
    </script>
</body>

</html>