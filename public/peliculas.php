<?php
session_start();
if (!isset($_SESSION['rol'])) { header("Location: index.php"); exit(); }
$esAdmin = ($_SESSION['rol'] === 'administrador');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="referrer" content="no-referrer">
    <title>Catálogo Completo - Multicine</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;800;900&display=swap');
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { background-color: #050505; color: white; font-family: 'Montserrat', sans-serif; padding-bottom: 50px; }

        header { padding: 30px 50px; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #222; background: rgba(5,5,5,0.95); position: sticky; top: 0; z-index: 100; backdrop-filter: blur(10px); }
        .logo { font-weight: 900; font-size: 1.5rem; color: #00d2d3; letter-spacing: 1px; text-decoration: none;}
        .back-btn { color: #aaa; text-decoration: none; font-weight: bold; font-size: 0.9rem; border: 1px solid #444; padding: 10px 25px; border-radius: 30px; transition: 0.3s; }
        .back-btn:hover { background: white; color: black; }

        /* Filtros calcados de tu imagen */
        .filters { padding: 30px 50px; display: flex; gap: 15px; justify-content: center; flex-wrap: wrap; }
        .filter-btn { background: #1a1a1a; border: 1px solid #333; color: #888; padding: 10px 25px; border-radius: 20px; cursor: pointer; font-weight: bold; transition: 0.3s; text-transform: uppercase; }
        .filter-btn:hover, .filter-btn.active { background: #00d2d3; color: black; border-color: #00d2d3; }

        .catalog-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 30px; padding: 0 50px; max-width: 1400px; margin: 0 auto; }
        .movie-item { background: #111; border-radius: 15px; overflow: hidden; transition: 0.4s; border: 1px solid #222; }
        .movie-item:hover { transform: translateY(-5px); border-color: #00d2d3; box-shadow: 0 10px 30px rgba(0, 210, 211, 0.2); }
        
        .poster { width: 100%; height: 340px; object-fit: cover; }
        .info { padding: 15px; text-align: center; }
        .m-title { font-weight: 800; font-size: 0.9rem; margin-bottom: 5px; text-transform: uppercase; }
        .m-genre { color: #00d2d3; font-size: 0.75rem; font-weight: bold; text-transform: uppercase; }

        /* Botones de Admin (Igual a tu imagen) */
        .admin-actions { display: flex; justify-content: space-between; padding: 15px; background: #0a0a0a; border-top: 1px solid #222; gap: 10px; }
        .btn-mini { flex: 1; background: transparent; border: 1px solid; font-weight: bold; cursor: pointer; font-size: 0.75rem; transition: 0.3s; padding: 8px; border-radius: 4px; text-transform: uppercase; }
        .btn-edit { color: #f1c40f; border-color: #f1c40f; }
        .btn-edit:hover { background: #f1c40f; color: black; }
        .btn-del { color: #ff4757; border-color: #ff4757; }
        .btn-del:hover { background: #ff4757; color: white; }

        /* Modal Edición (Oculto por defecto) */
        .modal-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.9); z-index: 500; display: none; align-items: center; justify-content: center; backdrop-filter: blur(5px); }
        .modal-overlay.active { display: flex; }
        .modal-content { background: #151515; width: 90%; max-width: 500px; padding: 40px; border-radius: 20px; border: 1px solid #333; position: relative; }
        .form-input { width: 100%; padding: 12px; margin-bottom: 15px; background: #0a0a0a; border: 1px solid #333; color: white; border-radius: 8px; font-family: inherit; }
        .btn-submit { width: 100%; padding: 15px; background: #f1c40f; color: black; border: none; border-radius: 8px; font-weight: 900; cursor: pointer; }
    </style>
</head>
<body>

    <header>
        <div class="logo">CATÁLOGO</div>
        <a href="<?php echo $esAdmin ? 'admin.php' : 'cajero.php'; ?>" class="back-btn">VOLVER</a>
    </header>

    <div class="filters">
        <button class="filter-btn active" onclick="filtrar('all', this)">TODAS</button>
        <button class="filter-btn" onclick="filtrar('Acción', this)">ACCIÓN</button>
        <button class="filter-btn" onclick="filtrar('Terror', this)">TERROR</button>
        <button class="filter-btn" onclick="filtrar('Infantil', this)">INFANTIL</button>
        <button class="filter-btn" onclick="filtrar('Sci-Fi', this)">SCI-FI</button>
        <button class="filter-btn" onclick="filtrar('Drama', this)">DRAMA</button>
    </div>

    <div class="catalog-grid" id="moviesContainer"></div>

    <div class="modal-overlay" id="editModal">
        <div class="modal-content">
            <button onclick="document.getElementById('editModal').classList.remove('active')" style="position:absolute; top:20px; right:20px; background:none; border:none; color:white; font-size:1.5rem; cursor:pointer;">&times;</button>
            <h2 style="color:#f1c40f; margin-bottom:20px;">EDITAR PELÍCULA</h2>
            <form id="editForm" enctype="multipart/form-data">>
                <input type="hidden" name="action" value="editar_pelicula">
                <input type="hidden" name="id" id="edit_id">
                <input type="text" name="titulo" id="edit_titulo" class="form-input" required>
                <input type="text" name="genero" id="edit_genero" class="form-input" required>
                <input type="number" name="duracion" id="edit_duracion" class="form-input" required>
                <select name="clasificacion" id="edit_clasificacion" class="form-input">
                    <option value="A">Todo Público (A)</option>
                    <option value="B">Mayores de 12 (B)</option>
                    <option value="C">Mayores de 18 (C)</option>
                </select>
                <input type="text" name="idioma" id="edit_idioma" class="form-input" required>
                <textarea name="sinopsis" id="edit_sinopsis" class="form-input" style="height:80px;" required></textarea>
                <button type="submit" class="btn-submit">GUARDAR CAMBIOS</button>
            </form>
        </div>
    </div>

    <script>
        const ES_ADMIN = <?php echo $esAdmin ? 'true' : 'false'; ?>;
        let peliculasGlobal = [];

        document.addEventListener('DOMContentLoaded', cargarPeliculas);

        function cargarPeliculas() {
            fetch('api.php?action=listar_peliculas')
            .then(res => res.json())
            .then(data => {
                peliculasGlobal = data;
                dibujarGrilla(data);
            });
        }

        function dibujarGrilla(peliculas) {
            const container = document.getElementById('moviesContainer');
            container.innerHTML = '';

            peliculas.forEach(p => {
                const ruta = p.imagenPoster.startsWith('http') ? p.imagenPoster : `img/${p.imagenPoster}`;
                
                // Botones solo si es admin
                let botonesAdmin = '';
                if (ES_ADMIN) {
                    botonesAdmin = `
                        <div class="admin-actions">
                            <button class="btn-mini btn-edit" onclick="abrirEditar(${p.idPelicula})">EDITAR</button>
                            <button class="btn-mini btn-del" onclick="eliminar(${p.idPelicula})">ELIMINAR</button>
                        </div>
                    `;
                }

                container.innerHTML += `
                    <div class="movie-item">
                        <img src="${ruta}" class="poster" onerror="this.src='img/fondo.jpg'">
                        <div class="info">
                            <div class="m-title">${p.titulo}</div>
                            <div class="m-genre">${p.genero}</div>
                        </div>
                        ${botonesAdmin}
                    </div>
                `;
            });
        }

        // --- SISTEMA DE FILTROS ---
        function filtrar(categoria, btnElement) {
            document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
            btnElement.classList.add('active');

            if (categoria === 'all') dibujarGrilla(peliculasGlobal);
            else {
                const filtradas = peliculasGlobal.filter(p => p.genero.toLowerCase().includes(categoria.toLowerCase()));
                dibujarGrilla(filtradas);
            }
        }

        // --- FUNCIONES CRUD EN LA MISMA TARJETA ---
        function abrirEditar(id) {
            const peli = peliculasGlobal.find(p => p.idPelicula === id);
            document.getElementById('edit_id').value = peli.idPelicula;
            document.getElementById('edit_titulo').value = peli.titulo;
            document.getElementById('edit_genero').value = peli.genero;
            document.getElementById('edit_duracion').value = peli.duracion;
            document.getElementById('edit_clasificacion').value = peli.clasificacion;
            document.getElementById('edit_idioma').value = peli.idioma;
            document.getElementById('edit_sinopsis').value = peli.sinopsis;
            document.getElementById('editModal').classList.add('active');
        }

        document.getElementById('editForm').addEventListener('submit', function(e) {
            e.preventDefault();
            fetch('api.php', { method: 'POST', body: new FormData(this) })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    document.getElementById('editModal').classList.remove('active');
                    cargarPeliculas(); // Recarga la grilla
                } else alert('Error: ' + data.message);
            });
        });

        function eliminar(id) {
            if (confirm('¿Eliminar esta película del catálogo?')) {
                const fd = new FormData(); fd.append('action', 'eliminar_pelicula'); fd.append('id', id);
                fetch('api.php', { method: 'POST', body: fd }).then(() => cargarPeliculas());
            }
        }
    </script>
</body>
</html>