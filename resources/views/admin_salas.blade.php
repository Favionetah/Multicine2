<!DOCTYPE html>
<html lang="es">
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Gestión de Salas - Multicine</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;800;900&display=swap');
        
        :root {
            --xl-color: #00d2d3;
            --plus-color: #54a0ff;
            --4d-color: #ff4757;
            --classic-color: #feca57;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { background-color: #050505; color: white; font-family: 'Montserrat', sans-serif; padding-bottom: 50px; }

        header { padding: 30px 50px; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #222; background: rgba(5,5,5,0.95); position: sticky; top: 0; z-index: 100; }
        .logo { font-weight: 900; font-size: 1.5rem; color: white; letter-spacing: 1px; text-decoration: none;}
        .back-btn { color: #aaa; text-decoration: none; font-weight: bold; border: 1px solid #444; padding: 10px 25px; border-radius: 30px; transition: 0.3s; }
        .back-btn:hover { background: white; color: black; }

        .catalog-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 30px; padding: 40px 50px; max-width: 1400px; margin: 0 auto; }
        
        /* TARJETA BASE */
        .sala-card { background: #111; border-radius: 15px; overflow: hidden; border: 1px solid #222; display: flex; flex-direction: column; transition: 0.3s; position: relative;}
        .sala-card:hover { transform: translateY(-5px); box-shadow: 0 10px 40px rgba(0,0,0,0.5); }
        
        .card-header { height: 120px; position: relative; border-bottom: 3px solid #333; background: #0a0a0a; }
        
        .badge-tipo { position: absolute; top: 15px; left: 15px; padding: 5px 15px; border-radius: 20px; font-size: 0.7rem; font-weight: 900; text-transform: uppercase; letter-spacing: 1px; background: rgba(0,0,0,0.8); border: 1px solid; }
        .title { position: absolute; bottom: 15px; left: 20px; font-weight: 900; font-size: 1.8rem; text-transform: uppercase; font-style: italic; z-index: 2;}
        
        .info { padding: 25px 20px 20px 20px; flex: 1; display: grid; grid-template-columns: 1fr 1fr; gap: 10px; background: linear-gradient(180deg, #151515, #0a0a0a);}
        .info-box { background: rgba(0,0,0,0.5); padding: 10px; border-radius: 8px; text-align: center; border: 1px solid #222;}
        .info-box span { display: block; font-size: 0.65rem; color: #888; font-weight: bold; margin-bottom: 5px;}
        .info-box strong { font-size: 1.1rem; color: white; }

        /* COLORES DINÁMICOS */
        .tipo-xl .card-header { border-bottom-color: var(--xl-color); }
        .tipo-xl .badge-tipo { color: var(--xl-color); border-color: var(--xl-color); }
        .tipo-xl .title { color: var(--xl-color); text-shadow: 0 0 15px rgba(0, 210, 211, 0.4);}

        .tipo-plus .card-header { border-bottom-color: var(--plus-color); }
        .tipo-plus .badge-tipo { color: var(--plus-color); border-color: var(--plus-color); }
        .tipo-plus .title { color: var(--plus-color); text-shadow: 0 0 15px rgba(84, 160, 255, 0.4);}

        .tipo-4d .card-header { border-bottom-color: var(--4d-color); }
        .tipo-4d .badge-tipo { color: var(--4d-color); border-color: var(--4d-color); }
        .tipo-4d .title { color: var(--4d-color); text-shadow: 0 0 15px rgba(255, 71, 87, 0.4);}

        .tipo-classic .card-header { border-bottom-color: var(--classic-color); }
        .tipo-classic .badge-tipo { color: var(--classic-color); border-color: var(--classic-color); }
        .tipo-classic .title { color: var(--classic-color); text-shadow: 0 0 15px rgba(254, 202, 87, 0.4);}

        /* BOTONES ADMIN Y VER BUTACAS */
        .admin-actions { display: flex; flex-direction: column; padding: 15px; background: #050505; gap: 10px; }
        
        .btn-butacas { background: transparent; border: 1px solid #555; color: white; padding: 10px; border-radius: 30px; cursor: pointer; font-weight: 800; font-size: 0.8rem; transition: 0.3s; display: flex; justify-content: center; gap: 8px; align-items: center; letter-spacing: 1px;}
        .btn-butacas:hover { background: white; color: black; border-color: white; }

        .btn-mini { flex: 1; background: transparent; border: 1px solid; font-weight: bold; cursor: pointer; padding: 10px; border-radius: 5px; transition: 0.3s; font-size: 0.8rem;}
        .btn-edit { color: #aaa; border-color: #aaa; } .btn-edit:hover { background: #fff; color: black; border-color: #fff;}
        .btn-del { color: #ff4757; border-color: #ff4757; } .btn-del:hover { background: #ff4757; color: white; }

        /* MODALES BÁSICOS */
        .modal-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.9); z-index: 500; display: none; align-items: center; justify-content: center; backdrop-filter: blur(5px); }
        .modal-overlay.active { display: flex; }
        .modal-content { background: #151515; width: 90%; max-width: 500px; padding: 40px; border-radius: 20px; border: 1px solid #333; position: relative; }
        .form-input { width: 100%; padding: 12px; margin-bottom: 15px; background: #0a0a0a; border: 1px solid #333; color: white; border-radius: 8px; font-family: inherit; }
        .btn-submit { width: 100%; padding: 15px; border: none; border-radius: 8px; font-weight: 900; cursor: pointer; transition:0.3s;}

        /* ESTILOS DEL MAPA DE BUTACAS (Heredado de salas.html) */
        .modal-seat-content { background: #121212; width: 95%; max-width: 900px; border-radius: 25px; border: 1px solid #333; display: flex; flex-direction: column; box-shadow: 0 0 100px rgba(0, 0, 0, 0.5); position: relative;}
        .modal-header { padding: 20px 30px; background: #1a1a1a; border-bottom: 1px solid #333; border-radius: 25px 25px 0 0; display: flex; justify-content: space-between; align-items: center; }
        .modal-title-text { font-size: 1.5rem; color: white; text-transform: uppercase; font-weight:900; margin: 0; }
        .modal-body { padding: 40px; overflow-y: auto; max-height: 70vh; display: flex; flex-direction: column; align-items: center; }
        
        .screen-display { width: 80%; height: 60px; background: linear-gradient(180deg, #fff, transparent); margin-bottom: 60px; border-radius: 50% 50% 0 0 / 20px 20px 0 0; box-shadow: 0 20px 50px rgba(255,255,255,0.2); text-align: center; color: #000; font-weight: bold; font-size: 0.9rem; line-height: 40px; letter-spacing: 8px; transform: perspective(600px) rotateX(-30deg); opacity: 0.8; }
        
        .seat-map-wrapper { width: 100%; display: flex; flex-direction: column; align-items: center; gap: 8px; overflow-x: auto; padding-bottom: 20px; }
        .seat-row { display: flex; gap: 6px; justify-content: center; width: 100%; min-width: max-content; }
        
        .seat-icon { width: 24px; height: 24px; border-radius: 6px 6px 10px 10px; background-color: #333; transition: 0.3s; position: relative; box-shadow: 0 2px 5px rgba(0,0,0,0.5); }
        .seat-icon:hover { background-color: #fff; transform: translateY(-5px); box-shadow: 0 0 15px white; }
        .seat-icon::after { content:''; position: absolute; bottom: -2px; left: 2px; right: 2px; height: 4px; background: #222; border-radius: 2px; }

        .seat-plus { width: 40px; background-color: var(--plus-color); border-radius: 8px 8px 12px 12px; }
        .seat-4d { background-color: var(--4d-color); }
        .seat-xl { background-color: var(--xl-color); }
        .seat-classic { background-color: var(--classic-color); }
        
    </style>
</head>
<body>

    <header>
        <div class="logo">INFRAESTRUCTURA (SALAS)</div>
        <div style="display:flex; gap:10px;">
            <button class="back-btn" onclick="abrirCrearSala()" style="background:#00d2d3; color:black; border:none;">+ NUEVA SALA</button>
            <a href="/admin" class="back-btn">VOLVER AL DASHBOARD</a>
        </div>
    </header>

    <div class="catalog-grid" id="salasContainer"></div>

    <div class="modal-overlay" id="seatModal">
        <div class="modal-seat-content">
            <div class="modal-header">
                <div>
                    <h3 id="seatTitle" class="modal-title-text">SALA</h3>
                    <small style="color: #666; font-weight:600;">ESQUEMA REFERENCIAL DE ASIENTOS</small>
                </div>
                <button onclick="document.getElementById('seatModal').classList.remove('active')" style="background:none; border:none; color:#666; font-size:2rem; cursor:pointer;">&times;</button>
            </div>
            <div class="modal-body">
                <div class="screen-display">PANTALLA</div>
                <div id="seatContainer" class="seat-map-wrapper"></div>
            </div>
        </div>
    </div>

    <div class="modal-overlay" id="editModal">
        <div class="modal-content">
            <button onclick="document.getElementById('editModal').classList.remove('active')" style="position:absolute; top:20px; right:20px; background:none; border:none; color:white; font-size:1.5rem; cursor:pointer;">&times;</button>
            <h2 id="tituloEditSala" style="margin-bottom:20px; transition:0.3s;">EDITAR SALA</h2>
            
            <div style="text-align:center; margin-bottom:15px;">
                <div id="editAsientoPreview" style="width:40px; height:40px; border-radius:10px 10px 15px 15px; margin:0 auto; transition:0.3s; box-shadow: 0 5px 15px rgba(0,0,0,0.5);"></div>
            </div>

            <form id="editForm">
                <input type="hidden" name="action" value="editar_sala">
                <input type="hidden" name="id" id="edit_id">
                
                <label style="color:#888; font-size:0.8rem;">Formato de la Sala</label>
                <select name="tipo" id="edit_tipo" class="form-input" required onchange="cambiarColorSala(this.value, 'editModal')">
                    <option value="classic">Classic (Estandar)</option>
                    <option value="xl">Macro XL (Premium)</option>
                    <option value="plus">Prime Plus (VIP)</option>
                    <option value="4d">4D E-Motion (Especial)</option>
                </select>

                <label style="color:#888; font-size:0.8rem;">Nombre de Sala</label>
                <input type="text" name="nombre" id="edit_nombre" class="form-input" required>
                
                <div style="display:flex; gap:10px;">
                    <div style="flex:1;">
                        <label style="color:#888; font-size:0.8rem;">Filas</label>
                        <input type="number" name="filas" id="edit_filas" class="form-input" required>
                    </div>
                    <div style="flex:1;">
                        <label style="color:#888; font-size:0.8rem;">Columnas</label>
                        <input type="number" name="columnas" id="edit_columnas" class="form-input" required>
                    </div>
                </div>

                <label style="color:#888; font-size:0.8rem;">Precio por Asiento (Bs)</label>
                <input type="number" name="precio" id="edit_precio" class="form-input" step="0.5" required>

                <button type="submit" id="btnActualizarSala" class="btn-submit">GUARDAR CAMBIOS</button>
            </form>
        </div>
    </div>

    <script>
        let salasGlobal = [];

        document.addEventListener('DOMContentLoaded', cargarSalas);

        // --- CARGAR SALAS Y RENDERIZAR TARJETAS ---
        function cargarSalas() {
            fetch('/api/salas')
            .then(res => res.json())
            .then(data => {
                salasGlobal = data;
                const container = document.getElementById('salasContainer');
                container.innerHTML = '';

                data.forEach(s => {
                    const tipoClase = s.tipo ? `tipo-${s.tipo}` : 'tipo-classic';
                    const nombreTipo = s.tipo ? s.tipo.toUpperCase() : 'CLASSIC';
                    
                    container.innerHTML += `
                        <div class="sala-card ${tipoClase}">
                            <div class="card-header">
                                <span class="badge-tipo">${nombreTipo}</span>
                                <div class="title">${s.nombre}</div>
                            </div>
                            <div class="info">
                                <div class="info-box"><span>ASIENTOS</span><strong>${s.capacidad}</strong></div>
                                <div class="info-box"><span>PRECIO</span><strong>Bs ${s.precio}</strong></div>
                                <div class="info-box"><span>FILAS</span><strong>${s.filas}</strong></div>
                                <div class="info-box"><span>COLUMNAS</span><strong>${s.columnas}</strong></div>
                            </div>
                            <div class="admin-actions">
                                <button class="btn-butacas" onclick="abrirMapaAsientos(${s.idSala})"><i class="fas fa-couch"></i> VER BUTACAS</button>
                                <div style="display:flex; gap:10px;">
                                    <button class="btn-mini btn-edit" onclick="abrirEditar(${s.idSala})">EDITAR</button>
                                    <button class="btn-mini btn-del" onclick="eliminar(${s.idSala})">ELIMINAR</button>
                                </div>
                            </div>
                        </div>
                    `;
                });
            });
        }

        // --- LÓGICA DEL MAPA DE ASIENTOS (Heredado de salas.html) ---
        function abrirMapaAsientos(id) {
            const sala = salasGlobal.find(s => s.idSala == id);
            const tipo = sala.tipo || 'classic';
            const rows = parseInt(sala.filas);
            const cols = parseInt(sala.columnas);
            
            const modal = document.getElementById('seatModal');
            const titleEl = document.getElementById('seatTitle');
            const container = document.getElementById('seatContainer');
            
            titleEl.textContent = sala.nombre;
            
            // Colores según tipo
            const colores = { 'classic': 'var(--classic-color)', 'xl': 'var(--xl-color)', 'plus': 'var(--plus-color)', '4d': 'var(--4d-color)' };
            titleEl.style.color = colores[tipo];
            
            container.innerHTML = ''; 
            
            // Asignar clase de estilo al asiento
            let styleClass = tipo === 'xl' ? 'seat-xl' : (tipo === 'plus' ? 'seat-plus' : (tipo === '4d' ? 'seat-4d' : 'seat-classic'));

            // Dibujar asientos
            for (let i = 0; i < rows; i++) {
                const rowDiv = document.createElement('div');
                rowDiv.className = 'seat-row';
                
                // Efecto curvo para XL y Classic
                if(tipo === 'xl' || tipo === 'classic') {
                    let curve = Math.abs(i - rows/2) * 1.5;
                    rowDiv.style.transform = `translateY(${curve}px)`; 
                }

                for (let j = 0; j < cols; j++) {
                    const seat = document.createElement('div');
                    seat.className = `seat-icon ${styleClass}`;
                    
                    // Crear un pasillo central si hay más de 6 columnas
                    if (cols >= 6 && j === Math.floor(cols/2)) {
                        seat.style.marginLeft = '30px';
                    }
                    
                    rowDiv.appendChild(seat);
                }
                container.appendChild(rowDiv);
            }

            modal.classList.add('active');
        }

        // --- LÓGICA DE EDICIÓN ---
        function cambiarColorSala(tipo, modalId) {
            const colores = { 'classic': '#feca57', 'xl': '#00d2d3', 'plus': '#54a0ff', '4d': '#ff4757' };
            const color = colores[tipo];
            const modal = document.getElementById(modalId);
            
            modal.querySelector('h2').style.color = color;
            modal.querySelector('.btn-submit').style.background = color;
            modal.querySelector('.btn-submit').style.color = (tipo === 'plus' || tipo === '4d') ? 'white' : 'black';
            
            const preview = modal.querySelector('div[id$="Preview"]');
            if(preview) preview.style.background = color;
        }

        function abrirEditar(id) {
            const sala = salasGlobal.find(s => s.idSala == id);
            document.getElementById('edit_id').value = sala.idSala;
            document.getElementById('edit_tipo').value = sala.tipo || 'classic';
            document.getElementById('edit_nombre').value = sala.nombre;
            document.getElementById('edit_filas').value = sala.filas;
            document.getElementById('edit_columnas').value = sala.columnas;
            document.getElementById('edit_precio').value = sala.precio;
            
            cambiarColorSala(sala.tipo || 'classic', 'editModal');
            document.getElementById('editModal').classList.add('active');
        }

        // Lógica de Guardar/Editar Sala (listener único)
        document.getElementById('editForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            fetch('/api/salas', { 
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
                    document.getElementById('editModal').classList.remove('active');
                    cargarSalas();
                } else alert('❌ Error: ' + data.message);
            })
            .catch(err => {
                console.error('Error al guardar sala:', err);
                alert('Error de conexión. Revisa la consola.');
            });
        });

        // Lógica de Eliminación
        function eliminar(id) {
            if (confirm('¿Estás seguro de que deseas eliminar esta sala?')) {
                const fd = new FormData(); 
                fd.append('id', id); 
                
                fetch('/api/salas/eliminar', { 
                    method: 'POST', 
                    body: fd,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    }
                })
                .then(res => res.json())
                .then(data => {
                    if(data.status === 'success') {
                        alert('🗑️ ' + data.message);
                        cargarSalas();
                    } else alert('❌ Error: ' + data.message);
                });
            }
        }
        function abrirCrearSala() {
            document.getElementById('editForm').reset();
            document.getElementById('edit_id').value = ''; // Muy importante: ID vacío para crear
            document.getElementById('tituloEditSala').textContent = 'NUEVA SALA';
            cambiarColorSala('classic', 'editModal');
            document.getElementById('editModal').classList.add('active');
        }
    </script>
</body>
</html>
