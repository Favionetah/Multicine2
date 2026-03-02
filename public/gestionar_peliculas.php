<?php
session_start();
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'administrador') {
    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="referrer" content="no-referrer">
    <title>Gestión - Multicine</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/estilos.css">
    <style>
        .layout { display: flex; min-height: 100vh; background: #0f0f0f; color: white; font-family: 'Montserrat', sans-serif;}
        .sidebar { width: 260px; background: #1a1a1a; padding: 20px; border-right: 1px solid #333; }
        .nav-btn { width: 100%; padding: 15px; margin-bottom: 10px; border: none; border-radius: 8px; background: transparent; color: white; text-align: left; font-weight: bold; cursor: pointer; transition: 0.3s; }
        .nav-btn:hover { background: #333; }
        .nav-btn.active { background: #00d2d3; color: black; }
        .main-content { flex: 1; padding: 40px; overflow-y: auto; }
        .title-cyan { color: #00d2d3; text-transform: uppercase; letter-spacing: 2px; }
    </style>
</head>
<body>
    <div class="layout">
        <nav class="sidebar">
            <div style="display:flex; align-items:center; gap:10px; margin-bottom:40px;">
                <div style="width:20px; height:20px; background:#00d2d3; border-radius:4px;"></div>
                <h2 style="margin:0; font-size:1.2rem;">MULTICINE<br>LP</h2>
            </div>
            
            <button class="nav-btn" onclick="location.href='admin.php'"><i class="fas fa-home"></i> Menú Principal</button>
            <button class="nav-btn active"><i class="fas fa-film"></i> Gestión Películas</button>
            <button class="nav-btn"><i class="fas fa-calendar-alt"></i> Armar Cartelera</button>
        </nav>

        <main class="main-content">
            <?php include __DIR__ . '/sections/crud_peliculas.php'; ?>
        </main>
    </div>

    <script>
        let peliculasGlobal = [];

        function cargarPeliculas() {
            fetch('api.php?action=listar_peliculas')
                .then(res => res.json())
                .then(data => {
                    const contenedor = document.getElementById('listaPeliculas');
                    if (!Array.isArray(data)) return;
                    peliculasGlobal = data;
                    contenedor.innerHTML = data.map(p => {
                        const rutaImagen = p.imagenPoster.startsWith('http') ? p.imagenPoster : `img/${p.imagenPoster}`;
                        return `
                            <div class="movie-card">
                                <img src="${rutaImagen}" onerror="this.src='https://ih1.redbubble.net/image.1893341687.8294/fposter,small,wall_texture,product,750x1000.jpg'">
                                <div class="movie-info">
                                    <h3>${p.titulo}</h3>
                                    <p>${p.genero}</p>
                                    <div class="btn-group">
                                        <button class="btn-edit" onclick="editar(${p.idPelicula})">EDITAR</button>
                                        <button class="btn-delete" onclick="eliminar(${p.idPelicula})">ELIMINAR</button>
                                    </div>
                                </div>
                            </div>
                        `;
                    }).join('');
                });
        }

        function editar(id) {
            const peli = peliculasGlobal.find(p => p.idPelicula === id);
            if (!peli) return;
            document.getElementById('idPelicula').value = peli.idPelicula;
            document.getElementById('actionPelicula').value = 'editar_pelicula';
            document.getElementById('titulo').value = peli.titulo;
            document.getElementById('duracion').value = peli.duracion;
            document.getElementById('genero').value = peli.genero;
            document.getElementById('clasificacion').value = peli.clasificacion;
            document.getElementById('idioma').value = peli.idioma;
            document.getElementById('sinopsis').value = peli.sinopsis;
            document.getElementById('btnGuardar').textContent = 'Actualizar Película';
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        document.getElementById('formPelicula').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            fetch('api.php', { method: 'POST', body: formData })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    alert('✅ ' + data.message);
                    this.reset();
                    document.getElementById('actionPelicula').value = 'crear_pelicula';
                    document.getElementById('idPelicula').value = '';
                    document.getElementById('btnGuardar').textContent = 'Guardar Película';
                    cargarPeliculas();
                } else { alert('❌ Error: ' + data.message); }
            });
        });

        function eliminar(id) {
            if (confirm('¿Eliminar esta película?')) {
                const fd = new FormData(); fd.append('action', 'eliminar_pelicula'); fd.append('id', id);
                fetch('api.php', { method: 'POST', body: fd }).then(() => cargarPeliculas());
            }
        }

        window.onload = cargarPeliculas;
    </script>
</body>
</html>