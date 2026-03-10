<!DOCTYPE html>
<html lang="es">
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Gestión de Funciones - Multicine</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;800;900&display=swap');
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { background-color: #050505; color: white; font-family: 'Montserrat', sans-serif; padding-bottom: 50px; }

        header { padding: 30px 50px; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #222; background: rgba(5,5,5,0.95); position: sticky; top: 0; z-index: 100; backdrop-filter: blur(10px); }
        .logo { font-weight: 900; font-size: 1.5rem; color: #54a0ff; letter-spacing: 1px; text-decoration: none;}
        .back-btn { color: #aaa; text-decoration: none; font-weight: bold; font-size: 0.9rem; border: 1px solid #444; padding: 10px 25px; border-radius: 30px; transition: 0.3s; }
        .back-btn:hover { background: white; color: black; }

        .catalog-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 30px; padding: 40px 50px; max-width: 1400px; margin: 0 auto; }
        .function-card { background: #111; border-radius: 15px; overflow: hidden; transition: 0.4s; border: 1px solid #222; display: flex; flex-direction: column;}
        .function-card:hover { transform: translateY(-5px); border-color: #54a0ff; box-shadow: 0 10px 30px rgba(84, 160, 255, 0.2); }
        
        .card-header { position: relative; height: 150px; }
        .card-header img { width: 100%; height: 100%; object-fit: cover; opacity: 0.6; }
        .card-header .title { position: absolute; bottom: 10px; left: 15px; font-weight: 900; font-size: 1.2rem; text-shadow: 0 2px 10px black; text-transform: uppercase;}
        
        .info { padding: 20px; flex: 1; }
        .info-row { display: flex; justify-content: space-between; margin-bottom: 10px; font-size: 0.85rem; border-bottom: 1px solid #222; padding-bottom: 5px;}
        .info-row span { color: #888; font-weight: bold; }
        .info-row strong { color: white; }
        .price-badge { background: #54a0ff; color: black; padding: 5px 10px; border-radius: 5px; font-weight: 900; display: inline-block; margin-top: 10px;}

        /* Botones Admin */
        .admin-actions { display: flex; justify-content: space-between; padding: 15px; background: #0a0a0a; border-top: 1px solid #222; gap: 10px; }
        .btn-mini { flex: 1; background: transparent; border: 1px solid; font-weight: bold; cursor: pointer; font-size: 0.75rem; transition: 0.3s; padding: 8px; border-radius: 4px; text-transform: uppercase; }
        .btn-edit { color: #f1c40f; border-color: #f1c40f; }
        .btn-edit:hover { background: #f1c40f; color: black; }
        .btn-del { color: #ff4757; border-color: #ff4757; }
        .btn-del:hover { background: #ff4757; color: white; }

        /* Modal Edición */
        .modal-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.9); z-index: 500; display: none; align-items: center; justify-content: center; backdrop-filter: blur(5px); }
        .modal-overlay.active { display: flex; }
        .modal-content { background: #151515; width: 90%; max-width: 500px; padding: 40px; border-radius: 20px; border: 1px solid #333; position: relative; }
        .form-input { width: 100%; padding: 12px; margin-bottom: 15px; background: #0a0a0a; border: 1px solid #333; color: white; border-radius: 8px; font-family: inherit; }
        .btn-submit { width: 100%; padding: 15px; background: #f1c40f; color: black; border: none; border-radius: 8px; font-weight: 900; cursor: pointer; }
    </style>
</head>
<body>

    <header>
        <div class="logo">CARTELERA PROGRAMADA</div>
        <div style="display:flex; gap:10px;">
            <button class="back-btn" onclick="abrirCrear()" style="background:#54a0ff; color:black; border:none;">+ NUEVA FUNCIÓN</button>
            <a href="/admin" class="back-btn">VOLVER AL DASHBOARD</a>
        </div>
    </header>

    <div class="catalog-grid" id="funcionesContainer"></div>

    <div class="modal-overlay" id="editModal">
        <div class="modal-content">
            <button onclick="document.getElementById('editModal').classList.remove('active')" style="position:absolute; top:20px; right:20px; background:none; border:none; color:white; font-size:1.5rem; cursor:pointer;">&times;</button>
            <h2 style="color:#f1c40f; margin-bottom:20px;">EDITAR FUNCIÓN</h2>
            <form id="editForm">
                <input type="hidden" name="action" value="editar_funcion">
                <input type="hidden" name="idFuncion" id="edit_idFuncion">
                
                <label style="color:#888; font-size:0.8rem;">Película:</label>
                <select name="idPelicula" id="edit_idPelicula" class="form-input" required></select>

                <label style="color:#888; font-size:0.8rem;">Sala:</label>
                <select name="idSala" id="edit_idSala" class="form-input" required>
                    <option value="">Cargando salas...</option>
                </select>

                <div style="display:flex; gap:10px;">
                    <div style="flex:1;">
                        <label style="color:#888; font-size:0.8rem;">Fecha:</label>
                        <input type="date" name="fechaFuncion" id="edit_fecha" class="form-input" required>
                    </div>
                    <div class="form-group">
                        <label style="color:#888; font-size:0.8rem;">Fecha Fin (Opcional - Para programar varios días):</label>
                        <input type="date" name="fechaFin" class="form-input">
                        <small style="color:var(--primary); font-size:0.7rem;">Si dejas esto vacío, se programará solo para el primer día.</small>
                    </div>
                    <div style="flex:1;">
                        <label style="color:#888; font-size:0.8rem;">Hora:</label>
                        <input type="time" name="horaInicio" id="edit_hora" class="form-input" required>
                    </div>
                </div>

                <label style="color:#888; font-size:0.8rem;">Precio Base (Bs):</label>
                <input type="number" name="precioBase" id="edit_precio" class="form-input" required>

                <button type="submit" class="btn-submit">GUARDAR CAMBIOS</button>
            </form>
        </div>
    </div>

    <script>
        let funcionesGlobal = [];

        document.addEventListener('DOMContentLoaded', () => {
            cargarFunciones();
            cargarPeliculasParaSelect();
            cargarSalasParaSelect(); 
        });

        // 1. CARGAR PELÍCULAS EN EL SELECT (¡Esta función faltaba!)
        function cargarPeliculasParaSelect() {
            fetch('/api/peliculas')
            .then(res => res.json())
            .then(data => {
                const select = document.getElementById('edit_idPelicula');
                select.innerHTML = '<option value="">-- Selecciona una Película --</option>';
                data.forEach(p => {
                    select.innerHTML += `<option value="${p.idPelicula}">${p.titulo}</option>`;
                });
            });
        }

        // 2. CARGAR SALAS EN EL SELECT
        function cargarSalasParaSelect() {
            fetch('/api/salas') 
            .then(res => res.json())
            .then(data => {
                const select = document.getElementById('edit_idSala');
                select.innerHTML = '<option value="">-- Selecciona una Sala --</option>';
                data.forEach(s => {
                    select.innerHTML += `<option value="${s.idSala}">${s.nombre} (${s.capacidadTotal} asnts.)</option>`;
                });
            });
        }

        // 3. CARGAR Y DIBUJAR FUNCIONES (Con protección de imágenes nulas)
        function cargarFunciones() {
            fetch('/api/funciones')
            .then(res => res.json())
            .then(data => {
                funcionesGlobal = data;
                const container = document.getElementById('funcionesContainer');
                container.innerHTML = '';

                if(data.length === 0) {
                    container.innerHTML = '<p style="color:#666; text-align:center; width:100%;">No hay funciones programadas.</p>';
                    return;
                }

                data.forEach(f => {
                    const poster = f.imagenPoster || ''; 
                    const ruta = poster.startsWith('http') ? poster : `/img/${poster}`;
                    
                    container.innerHTML += `
                        <div class="function-card">
                            <div class="card-header">
                                <img src="${ruta}" onerror="this.src='/img/fondo.jpg'">
                                <div class="title">${f.titulo}</div>
                            </div>
                            <div class="info">
                                <div class="info-row"><span>SALA:</span> <strong>${f.sala || f.idSala}</strong></div>
                                <div class="info-row"><span>FECHA:</span> <strong>${f.fechaFuncion}</strong></div>
                                <div class="info-row"><span>HORARIO:</span> <strong>${f.horaInicio}</strong></div>
                                <div class="price-badge">Bs ${f.precioBase}</div>
                            </div>
                            <div class="admin-actions">
                                <button class="btn-mini btn-edit" onclick="abrirEditar(${f.idFuncion})">EDITAR</button>
                                <button class="btn-mini btn-del" onclick="eliminar(${f.idFuncion})">ELIMINAR</button>
                            </div>
                        </div>
                    `;
                });
            })
            .catch(error => console.error("Error cargando funciones:", error));
        }

        // 4. EDITAR FUNCIÓN
        function abrirEditar(id) {
            const func = funcionesGlobal.find(f => f.idFuncion == id);
            document.getElementById('edit_idFuncion').value = func.idFuncion;
            document.getElementById('edit_idPelicula').value = func.idPelicula;
            document.getElementById('edit_idSala').value = func.idSala;
            document.getElementById('edit_fecha').value = func.fechaFuncion;
            document.getElementById('edit_hora').value = func.horaInicio;
            document.getElementById('edit_precio').value = func.precioBase;
            
            document.getElementById('editModal').classList.add('active');
        }

        // Guardar Función (crear o editar) — listener único
        document.getElementById('editForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const id = document.getElementById('edit_idFuncion').value;
            // Seleccionamos la URL según si es nueva función o edición
            const url = id ? '/api/funciones/editar' : '/api/funciones';
            const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            fetch(url, { 
                method: 'POST', 
                body: new FormData(this),
                headers: { 
                    'X-CSRF-TOKEN': token,
                    'Accept': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    alert('✅ ' + data.message);
                    document.getElementById('editModal').classList.remove('active');
                    cargarFunciones();
                } else alert('❌ ' + data.message);
            })
            .catch(err => {
                console.error('Error al guardar función:', err);
                alert('Error de conexión. Revisa la consola.');
            });
        });

        // Eliminar Función
        function eliminar(id) {
            if (confirm('¿Eliminar esta función?')) {
                const fd = new FormData(); 
                fd.append('id', id);
                fetch('/api/funciones/eliminar', { 
                    method: 'POST', 
                    body: fd,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    }
                })
                .then(res => res.json())
                .then(data => {
                    if (data.status === 'success') {
                        alert('✅ Función eliminada');
                        cargarFunciones();
                    } else alert('❌ Error: ' + data.message);
                });
            }
        }

        function abrirCrear() {
            document.getElementById('editForm').reset();
            document.getElementById('edit_idFuncion').value = ''; // ID vacío para nueva función
            document.querySelector('#editModal h2').textContent = 'NUEVA FUNCIÓN';
            document.getElementById('editModal').classList.add('active');
        }
    </script>
</body>
</html>
