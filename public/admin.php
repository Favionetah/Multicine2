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
    <title>Multicine - Dashboard Admin</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;600;800;900&display=swap');
        :root { --primary: #00d2d3; --dark-bg: #050505; --danger: #ff4757; }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Montserrat', sans-serif; background-color: var(--dark-bg); color: white; overflow-x: hidden; }
        
        /* Fondo */
        .background-container { position: fixed; top: 0; left: 0; width: 100%; height: 100%; z-index: -2; }
        .background-image { width: 100%; height: 100%; background-image: url('img/fondo.jpg'); background-size: cover; background-position: center; filter: brightness(0.2) blur(8px); transform: scale(1.05); }

        /* Header */
        header { display: flex; align-items: center; justify-content: space-between; padding: 25px 50px; position: sticky; top: 0; z-index: 100; background: rgba(5, 5, 5, 0.7); backdrop-filter: blur(15px); border-bottom: 1px solid rgba(255,255,255,0.05); }
        .menu-icon { font-size: 1.8rem; color: var(--primary); cursor: pointer; transition: 0.3s; z-index: 500; }
        .menu-icon:hover { text-shadow: 0 0 15px var(--primary); }
        
        nav ul { display: flex; list-style: none; gap: 40px; margin: 0 auto; }
        nav ul li a { text-decoration: none; color: #aaa; font-weight: 700; text-transform: uppercase; font-size: 0.85rem; letter-spacing: 1px; transition: 0.3s; }
        nav ul li a:hover { color: var(--primary); text-shadow: 0 0 10px var(--primary); }

        /* Panel Lateral (Sidebar) */
        .sidebar { position: fixed; top: 0; left: -350px; width: 320px; height: 100%; background: #111; border-right: 1px solid #222; z-index: 400; transition: 0.4s cubic-bezier(0.77, 0, 0.175, 1); padding: 100px 30px 40px 30px; display: flex; flex-direction: column; box-shadow: 20px 0 50px rgba(0,0,0,0.8); }
        .sidebar.active { left: 0; }
        
        .side-btn { background: #1a1a1a; color: white; border: 1px solid #333; padding: 15px 20px; border-radius: 12px; margin-bottom: 15px; cursor: pointer; text-align: left; font-weight: bold; font-size: 0.9rem; transition: 0.3s; display: flex; align-items: center; gap: 15px; font-family: 'Montserrat'; }
        .side-btn i { color: var(--primary); font-size: 1.2rem; width: 20px; text-align: center; }
        .side-btn:hover { border-color: var(--primary); background: #222; transform: translateX(5px); }
        .btn-logout { margin-top: auto; background: rgba(255, 71, 87, 0.1); color: var(--danger); border-color: var(--danger); text-align: center; justify-content: center; text-decoration: none; }
        .btn-logout i { color: var(--danger); }
        .btn-logout:hover { background: var(--danger); color: white; transform: none; }

        /* Main Content */
        main { padding: 40px 50px; max-width: 1400px; margin: 0 auto; transition: 0.4s; }
        main.shifted { margin-left: 320px; } /* Empuja el contenido si abres el menú en PC */
        
        h1.hero-title { font-size: 4.5rem; line-height: 0.9; font-weight: 900; text-transform: uppercase; color: white; margin: 20px 0 60px 0; letter-spacing: -2px; }
        h1.hero-title span { color: var(--primary); display: block; }

        .movies-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 30px; }
        .movie-card { text-align: center; cursor: pointer; transition: 0.4s; }
        .movie-card:hover { transform: translateY(-10px); }
        .poster-frame { width: 100%; height: 340px; border-radius: 15px; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.6); border: 1px solid #222; margin-bottom: 15px; }
        .poster-frame img { width: 100%; height: 100%; object-fit: cover; transition: 0.5s; }
        .movie-card:hover .poster-frame img { transform: scale(1.05); filter: brightness(1.1); }
        .movie-title { font-weight: 800; font-size: 1rem; color: white; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; text-transform: uppercase; }

        /* Modal Crear */
        .modal-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.9); z-index: 1000; display: none; align-items: center; justify-content: center; backdrop-filter: blur(5px); }
        .modal-overlay.active { display: flex; }
        .modal-content { background: #151515; width: 90%; max-width: 500px; padding: 40px; border-radius: 20px; border: 1px solid #333; position: relative; }
        .close-modal { position: absolute; top: 20px; right: 20px; background: none; border: none; color: #666; font-size: 1.5rem; cursor: pointer; }
        .form-input { width: 100%; padding: 12px; margin-bottom: 15px; background: #0a0a0a; border: 1px solid #333; color: white; border-radius: 8px; font-family: inherit; }
        .btn-submit { width: 100%; padding: 15px; background: var(--primary); color: black; border: none; border-radius: 8px; font-weight: 900; cursor: pointer; text-transform: uppercase; }
    </style>
</head>
<body>

    <div class="background-container"><div class="background-image"></div></div>

    <div class="sidebar" id="sidebar">
        <h3 style="color:#666; font-size:0.8rem; margin-bottom:15px; letter-spacing:1px;">PANEL DE CONTROL</h3>
        <button class="side-btn" onclick="abrirModalPelicula()"><i class="fas fa-film"></i> Añadir Película</button>
        <button class="side-btn" onclick="abrirModalFuncion()"><i class="fas fa-calendar-plus"></i> Crear Función</button>
        <button class="side-btn" onclick="abrirModalSala()"><i class="fas fa-couch"></i> Crear Sala</button>

        <a href="api.php?action=logout" class="side-btn btn-logout"><i class="fas fa-power-off"></i> Cerrar Sesión</a>
    </div>

    <header>
        <div class="menu-icon" onclick="toggleSidebar()"><i class="fas fa-bars"></i></div>
        <nav>
            <ul>
                <li><a href="admin_salas.php">SALAS</a></li>
                <li><a href="admin_funciones.php">FUNCIONES</a></li>
                <li><a href="peliculas.php">PELÍCULAS</a></li>
            </ul>
        </nav>
        <div style="width: 30px;"></div> </header>

    <main id="mainContent">
        <h1 class="hero-title">ESTRENOS<br><span>TAQUILLEROS</span></h1>
        <div class="movies-grid" id="gridPeliculas"></div>
    </main>

    <div class="modal-overlay" id="modalPelicula">
        <div class="modal-content">
            <button class="close-modal" onclick="cerrarModal()">&times;</button>
            <h2 style="color:var(--primary); margin-bottom:20px;">NUEVA PELÍCULA</h2>
            <form id="formPelicula" enctype="multipart/form-data">> 
                <input type="hidden" name="action" value="crear_pelicula">
                <input type="text" name="titulo" class="form-input" placeholder="Título" required>
                <input type="text" name="genero" class="form-input" placeholder="Género (Ej. Acción)" required>
                <input type="number" name="duracion" class="form-input" placeholder="Duración (minutos)" required>
                <select name="clasificacion" class="form-input">
                    <option value="A">Todo Público (A)</option>
                    <option value="B">Mayores de 12 (B)</option>
                    <option value="C">Mayores de 18 (C)</option>
                </select>
                <input type="text" name="idioma" class="form-input" placeholder="Idioma" required>
                <textarea name="sinopsis" class="form-input" placeholder="Sinopsis..." style="height:80px;" required></textarea>
                <input type="file" name="imagen" class="form-input" accept="image/*" required>
                <button type="submit" class="btn-submit">Guardar Película</button>
            </form>
        </div>
    </div>
    <div class="modal-overlay" id="modalFuncion">
        <div class="modal-content">
            <button class="close-modal" onclick="cerrarModalFuncion()">&times;</button>
            <h2 style="color:var(--accent); margin-bottom:20px;">PROGRAMAR FUNCIÓN</h2>
            
            <form id="formFuncion">
                <input type="hidden" name="action" id="actionFuncion" value="crear_funcion">
                
                <label style="color:#888; font-size:0.8rem; font-weight:bold;">PELÍCULA</label>
                <select name="idPelicula" id="selectPelicula" class="form-input" required>
                    <option value="">Cargando películas...</option>
                </select>

                <label style="color:#888; font-size:0.8rem; font-weight:bold;">SALA</label>
                <select name="idSala" id="selectSala" class="form-input" required>
                    <option value="">Cargando salas reales...</option>
                </select>

                <div style="display:flex; gap:10px;">
                    <div style="flex:1;">
                        <label style="color:#888; font-size:0.8rem; font-weight:bold;">FECHA</label>
                        <input type="date" name="fechaFuncion" id="fechaFuncion" class="form-input" required>
                    </div>
                    <div style="flex:1;">
                        <label style="color:#888; font-size:0.8rem; font-weight:bold;">HORA INICIO</label>
                        <input type="time" name="horaInicio" class="form-input" required>
                    </div>
                </div>

                <label style="color:#888; font-size:0.8rem; font-weight:bold;">PRECIO BASE (Bs)</label>
                <input type="number" name="precioBase" class="form-input" value="45" required>

                <button type="submit" class="btn-submit" style="background:var(--accent);">Guardar Función</button>
            </form>
        </div>
    </div>

    <div class="modal-overlay" id="modalSala">
        <div class="modal-content">
            <button class="close-modal" onclick="cerrarModalSala()">&times;</button>
            <h2 id="tituloModalSala" style="color:#feca57; margin-bottom:20px; transition:0.3s;">NUEVA SALA</h2>
            
            <div style="text-align:center; margin-bottom:15px;">
                <div id="asientoPreview" style="width:40px; height:40px; background:#feca57; border-radius:10px 10px 15px 15px; margin:0 auto; transition:0.3s; box-shadow: 0 5px 15px rgba(0,0,0,0.5);"></div>
            </div>

            <form id="formSala">
                <input type="hidden" name="action" value="crear_sala">
                
                <label style="color:#888; font-size:0.8rem;">Formato de la Sala</label>
                <select name="tipo" id="crear_tipo" class="form-input" required onchange="cambiarColorSala(this.value, 'modalSala')">
                    <option value="classic">Classic (Estandar)</option>
                    <option value="xl">Macro XL (Premium)</option>
                    <option value="plus">Prime Plus (VIP)</option>
                    <option value="4d">4D E-Motion (Especial)</option>
                </select>

                <label style="color:#888; font-size:0.8rem;">Nombre de Sala (Ej. Sala 5)</label>
                <input type="text" name="nombre" class="form-input" required>
                
                <div style="display:flex; gap:10px;">
                    <div style="flex:1;">
                        <label style="color:#888; font-size:0.8rem;">Filas</label>
                        <input type="number" name="filas" class="form-input" value="10" required>
                    </div>
                    <div style="flex:1;">
                        <label style="color:#888; font-size:0.8rem;">Columnas</label>
                        <input type="number" name="columnas" class="form-input" value="14" required>
                    </div>
                </div>

                <label style="color:#888; font-size:0.8rem;">Precio por Asiento (Bs)</label>
                <input type="number" name="precio" class="form-input" value="45" step="0.5" required>

                <button type="submit" id="btnGuardarSala" class="btn-submit" style="background:#feca57; color:black; transition:0.3s;">Guardar Sala</button>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', cargarPeliculas);

        // Control del Panel Lateral
        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('active');
            document.getElementById('mainContent').classList.toggle('shifted');
        }

        // Control del Modal
        function abrirModalPelicula() {
            toggleSidebar(); // Cierra el menú lateral
            document.getElementById('modalPelicula').classList.add('active');
        }
        function cerrarModal() { document.getElementById('modalPelicula').classList.remove('active'); }

        // Guardar Nueva Película
        document.getElementById('formPelicula').addEventListener('submit', function(e) {
            e.preventDefault();
            fetch('api.php', { method: 'POST', body: new FormData(this) })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    alert('✅ Película guardada');
                    this.reset();
                    cerrarModal();
                    cargarPeliculas();
                } else alert('❌ Error: ' + data.message);
            });
        });

        // Cargar Catálogo (Solo lectura en el Dashboard)
        function cargarPeliculas() {
            fetch('api.php?action=listar_peliculas')
            .then(res => res.json())
            .then(data => {
                const grid = document.getElementById('gridPeliculas');
                grid.innerHTML = data.map(p => {
                    const ruta = p.imagenPoster.startsWith('http') ? p.imagenPoster : `img/${p.imagenPoster}`;
                    return `
                        <div class="movie-card">
                            <div class="poster-frame"><img src="${ruta}" onerror="this.src='img/fondo.jpg'"></div>
                            <div class="movie-title">${p.titulo}</div>
                        </div>`;
                }).join('');
            });
        }

        // --- CONTROL DEL MODAL DE FUNCIONES ---
        function abrirModalFuncion() {
            toggleSidebar(); 
            
            // 1. Traer películas de la Base de Datos
            fetch('api.php?action=listar_peliculas')
            .then(res => res.json())
            .then(data => {
                const selectPeli = document.getElementById('selectPelicula');
                selectPeli.innerHTML = '<option value="">-- Selecciona una Película --</option>';
                data.forEach(p => {
                    selectPeli.innerHTML += `<option value="${p.idPelicula}">${p.titulo}</option>`;
                });
            });

            // 2. Traer SALAS REALES de la Base de Datos
            fetch('api.php?action=listar_salas')
            .then(res => res.json())
            .then(data => {
                const selectSala = document.getElementById('selectSala');
                selectSala.innerHTML = '<option value="">-- Selecciona una Sala --</option>';
                data.forEach(s => {
                    selectSala.innerHTML += `<option value="${s.idSala}">${s.nombre} (${s.capacidad} asientos)</option>`;
                });
            });

            const hoy = new Date().toISOString().split('T')[0];
            const inputFecha = document.getElementById('fechaFuncion');
            
            inputFecha.value = hoy;
            inputFecha.min = hoy;   

            document.getElementById('modalFuncion').classList.add('active');
        }

        function cerrarModalFuncion() { 
            document.getElementById('modalFuncion').classList.remove('active'); 
        }

        document.getElementById('formFuncion').addEventListener('submit', function(e) {
            e.preventDefault();
            
            fetch('api.php', { method: 'POST', body: new FormData(this) })
            .then(res => res.json())
            .then(data => {
                if(data.status === 'success') {
                    alert('📅 Función programada con éxito en la cartelera.');
                    cerrarModalFuncion();
                    this.reset();
                } else {
                    alert('❌ Error: ' + data.message);
                }
            })
            .catch(err => console.error("Error al programar:", err));
        });

        function cambiarColorSala(tipo, modalId) {
            const colores = { 'classic': '#feca57', 'xl': '#00d2d3', 'plus': '#54a0ff', '4d': '#ff4757' };
            const color = colores[tipo];
            const modal = document.getElementById(modalId);
            
            modal.querySelector('h2').style.color = color;
            modal.querySelector('.btn-submit').style.background = color;
            modal.querySelector('.btn-submit').style.color = (tipo === 'plus' || tipo === '4d') ? 'white' : 'black';
            
            const preview = modal.querySelector('#asientoPreview');
            if(preview) preview.style.background = color;
        }

        function abrirModalSala() { toggleSidebar(); document.getElementById('modalSala').classList.add('active'); }
        function cerrarModalSala() { document.getElementById('modalSala').classList.remove('active'); }

        document.getElementById('formSala').addEventListener('submit', function(e) {
            e.preventDefault();
            fetch('api.php', { method: 'POST', body: new FormData(this) })
            .then(res => res.json())
            .then(data => {
                if(data.status === 'success') { alert('🛋️ ' + data.message); cerrarModalSala(); this.reset(); } 
                else { alert('❌ Error: ' + data.message); }
            });
        });
    </script>
</body>
</html>