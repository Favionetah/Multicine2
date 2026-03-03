<?php
session_start();
// Validación estricta: Solo cajeros (o admins) pueden entrar aquí
if (!isset($_SESSION['rol'])) {
    header("Location: index.php");
    exit();
}

// Extraemos el nombre real del empleado desde la base de datos/sesión
$nombreEmpleadoReal = $_SESSION['nombre'] ?? 'Empleado Desconocido';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Punto de Venta - Multicine</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;800;900&display=swap');
        :root { --primary: #00d2d3; --danger: #ff4757; --success: #2ed573; --dark-bg: #050505; --card-bg: #111; }
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Montserrat', sans-serif; }
        
        body { background: var(--dark-bg); color: white; display: flex; height: 100vh; overflow: hidden; }

        /* SIDEBAR */
        .sidebar { width: 280px; background: #0a0a0a; border-right: 1px solid #222; display: flex; flex-direction: column; padding: 25px; z-index: 10; }
        .brand { color: var(--primary); font-weight: 900; font-size: 1.4rem; margin-bottom: 30px; text-align: center; }
        .cajero-btn { background: transparent; border: 1px solid #333; color: #888; padding: 15px; border-radius: 12px; margin-bottom: 10px; cursor: pointer; font-weight: bold; transition: 0.3s; display: flex; align-items: center; gap: 10px; }
        .cajero-btn:hover { background: #222; color: white; }
        .cajero-btn.active { background: var(--primary); color: black; border-color: var(--primary); }
        .btn-logout { margin-top: auto; background: rgba(255,71,87,0.1); color: var(--danger); border: 1px solid var(--danger); text-align: center; text-decoration: none; padding: 15px; border-radius: 12px; font-weight: bold; transition: 0.3s; display: block;}
        .btn-logout:hover { background: var(--danger); color: white; }

        /* PANTALLA BLOQUEADA */
        .welcome-screen { flex: 1; display: flex; flex-direction: column; justify-content: center; align-items: center; background: #050505; z-index: 5; }
        
        /* CONTENIDO PRINCIPAL */
        .main-content { flex: 1; display: none; flex-direction: column; background: #050505; }
        header { padding: 20px 40px; border-bottom: 1px solid #222; display: flex; justify-content: space-between; align-items: center; background: #0a0a0a; }
        .scrollable-area { flex: 1; overflow-y: auto; padding: 30px 40px; }
        
        /* FECHAS */
        .date-scroller { display: flex; gap: 10px; margin-bottom: 20px; overflow-x: auto; }
        .date-btn { background: #111; border: 1px solid #333; padding: 10px 20px; border-radius: 10px; cursor: pointer; font-weight: bold; color: #888; }
        .date-btn.active { background: var(--primary); color: black; border-color: var(--primary); }

        /* GRILLA FUNCIONES */
        .screening-list { display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 20px; }
        .screening-card { display: flex; background: var(--card-bg); border-radius: 15px; border: 1px solid #222; overflow: hidden; height: 140px; }
        .s-poster { width: 95px; object-fit: cover; }
        .s-info { flex: 1; padding: 15px; display: flex; flex-direction: column; justify-content: center; }
        .s-title { font-weight: 800; font-size: 0.9rem; text-transform: uppercase; margin-bottom: 5px; }
        .s-sala { font-size: 0.7rem; color: #888; background: #222; padding: 3px 8px; border-radius: 4px; display: inline-block; align-self: flex-start;}
        .s-time-box { background: #1a1a1a; padding: 0 20px; display: flex; flex-direction: column; justify-content: center; align-items: center; border-left: 1px solid #222; min-width: 110px;}
        .time-val { font-size: 1.4rem; font-weight: 900; color: var(--primary); }
        .btn-vender { margin-top: 10px; background: white; color: black; border: none; padding: 5px 15px; border-radius: 15px; font-weight: bold; cursor: pointer; font-size: 0.7rem; transition: 0.3s; width: 100%;}
        .btn-vender:hover { background: var(--primary); }

        /* MODALES Y MAPA ASIENTOS */
        .modal-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.9); z-index: 500; display: none; align-items: center; justify-content: center; }
        .modal-overlay.active { display: flex; }
        
        .modal-seat-content { background: #121212; width: 95%; max-width: 900px; border-radius: 20px; border: 1px solid #333; display: flex; flex-direction: column; }
        .screen-display { width: 80%; height: 50px; background: linear-gradient(180deg, #fff, transparent); margin: 0 auto 40px auto; border-radius: 50% 50% 0 0 / 20px 20px 0 0; text-align: center; color: black; font-weight: bold; line-height: 35px; letter-spacing: 5px; transform: perspective(600px) rotateX(-30deg); opacity: 0.8; }
        .seat-map-wrapper { width: 100%; display: flex; flex-direction: column; align-items: center; gap: 8px; overflow-x: auto; padding-bottom: 20px;}
        .seat-row { display: flex; gap: 6px; align-items: center; justify-content: center; }
        .row-letter { color: var(--primary); font-weight: bold; width: 20px; text-align: right; margin-right: 10px; }
        
        .seat-icon { width: 30px; height: 30px; border-radius: 5px 5px 10px 10px; background-color: #333; cursor: pointer; transition: 0.2s; position: relative; color: white; font-size: 0.6rem; display: flex; align-items: center; justify-content: center; font-weight: bold;}
        .seat-icon:hover { background-color: #555; transform: translateY(-3px); }
        .seat-icon.selected { background-color: var(--success) !important; color: black; transform: translateY(-5px); box-shadow: 0 0 10px var(--success); }
        .seat-icon.occupied { background-color: var(--danger) !important; color: transparent; cursor: not-allowed; opacity: 0.5;}
        
        .checkout-panel { background: #0a0a0a; padding: 20px 30px; border-top: 1px solid #333; display: flex; justify-content: space-between; align-items: center; border-radius: 0 0 20px 20px;}
        .btn-confirm { background: var(--primary); color: black; border: none; padding: 15px; border-radius: 8px; font-weight: 900; cursor: pointer; transition: 0.3s;}
    </style>
</head>
<body>

    <div class="sidebar">
        <div class="brand"><i class="fas fa-ticket-alt"></i> TAQUILLA LP</div>
        
        <div style="background: #111; padding: 15px; border-radius: 10px; margin-bottom: 20px; border: 1px solid #333; text-align:center;">
            <i class="fas fa-user-circle" style="font-size: 2rem; color: var(--primary); margin-bottom:10px;"></i>
            <h4 style="color: white; font-size: 0.9rem; margin:0;"><?php echo $nombreEmpleadoReal; ?></h4>
            <p style="color: #666; font-size: 0.7rem; margin-top:3px;">Operador en Turno</p>
        </div>

        <div id="terminales">
            <button class="cajero-btn" onclick="iniciarTurno('Terminal 1', this)"><i class="fas fa-desktop"></i> Terminal 1</button>
            <button class="cajero-btn" onclick="iniciarTurno('Terminal 2', this)"><i class="fas fa-desktop"></i> Terminal 2</button>
            <button class="cajero-btn" onclick="iniciarTurno('Terminal 3', this)"><i class="fas fa-desktop"></i> Terminal 3</button>
        </div>
        <a href="api.php?action=logout" class="btn-logout"><i class="fas fa-power-off"></i> CERRAR SESIÓN</a>
    </div>

    <div class="welcome-screen" id="welcomeScreen">
        <i class="fas fa-lock" style="font-size: 4rem; color: #333; margin-bottom: 20px;"></i>
        <h2 style="color: #666;">CAJA CERRADA</h2>
        <p style="color: #444;">Selecciona en qué terminal estás trabajando para abrir el sistema.</p>
    </div>

    <div class="main-content" id="mainContent">
        <header>
            <div><span style="color:#888; font-size:0.8rem;">OPERADOR:</span> <strong style="color:var(--primary); margin-left:10px;"><?php echo $nombreEmpleadoReal; ?></strong></div>
            <div><span style="color:#888; font-size:0.8rem;">ESTACIÓN:</span> <strong id="headerTerminal" style="color:white; margin-left:10px;">...</strong></div>
        </header>
        <div class="scrollable-area">
            <div class="date-scroller" id="dateScroller"></div>
            <div class="screening-list" id="carteleraContainer"></div>
        </div>
    </div>

    <div class="modal-overlay" id="ventaModal">
        <div class="modal-seat-content">
            <div style="padding: 20px; background: #1a1a1a; border-bottom: 1px solid #333; display: flex; justify-content: space-between; align-items: center; border-radius: 20px 20px 0 0;">
                <div>
                    <h3 id="modalMovieTitle" style="color:white; text-transform:uppercase; margin:0;">Pelicula</h3>
                    <small style="color:var(--primary);" id="modalRoomInfo">Sala Info</small>
                </div>
                <button onclick="document.getElementById('ventaModal').classList.remove('active')" style="background:none; border:none; color:#666; font-size:2rem; cursor:pointer;">&times;</button>
            </div>
            
            <div style="padding: 30px; overflow-y: auto; max-height: 60vh;">
                <div class="screen-display">PANTALLA</div>
                <div id="seatContainer" class="seat-map-wrapper"></div>
            </div>

            <div class="checkout-panel" style="flex-direction: column; align-items: stretch; gap: 15px;">
                <div style="display: flex; gap: 15px;">
                    <div style="flex: 1;">
                        <label style="color:#888; font-size:0.8rem;">CI / NIT Cliente:</label>
                        <input type="text" id="clienteCI" class="form-input" placeholder="Ej. 1234567" style="margin: 5px 0 0 0; padding: 10px;">
                    </div>
                    <div style="flex: 2;">
                        <label style="color:#888; font-size:0.8rem;">Nombre / Razón Social:</label>
                        <input type="text" id="clienteNombre" class="form-input" placeholder="Ej. Juan Pérez" style="margin: 5px 0 0 0; padding: 10px;">
                    </div>
                </div>
                
                <div style="display: flex; justify-content: space-between; align-items: center; border-top: 1px solid #333; padding-top: 15px;">
                    <div>
                        <span style="color:#888; font-size:0.8rem; display:block;">Asientos Seleccionados:</span>
                        <strong id="asientosElegidos" style="color:white; font-size:1.1rem;">Ninguno</strong>
                    </div>
                    <div style="text-align:right;">
                        <span style="color:#888; font-size:0.8rem; display:block;">Total a Pagar:</span>
                        <strong style="color:var(--success); font-size:1.8rem;">Bs <span id="precioTotal">0.00</span></strong>
                    </div>
                    <button class="btn-confirm" id="btnConfirmarVenta" style="width:200px; margin:0;" onclick="procesarVenta()" disabled>COBRAR Y FACTURAR</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // PASO VARIABLES DE PHP A JAVASCRIPT
        const operadorActual = "<?php echo $nombreEmpleadoReal; ?>";
        
        let terminalActual = '';
        let funcionesGlobales = [];
        let fechaSeleccionada = new Date().toISOString().split('T')[0];
        
        let funcionSeleccionadaId = null;
        let asientosSeleccionados = [];
        let precioActual = 0;

        document.addEventListener('DOMContentLoaded', () => {
            generarFechas();
            cargarCartelera();
        });

        // --- 1. LÓGICA DE INICIO DE TURNO (Directo, sin modal) ---
        function iniciarTurno(terminal, btn) {
            terminalActual = terminal;
            
            // Efecto visual en los botones
            document.querySelectorAll('.cajero-btn').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');

            // Actualizar Cabecera
            document.getElementById('headerTerminal').textContent = terminalActual;
            
            // Ocultar bloqueo y mostrar sistema
            document.getElementById('welcomeScreen').style.display = 'none';
            document.getElementById('mainContent').style.display = 'flex';
        }

        // --- 2. LÓGICA DE CARTELERA ---
        function generarFechas() {
            const container = document.getElementById('dateScroller');
            const hoy = new Date();
            for(let i=0; i<7; i++) {
                const d = new Date(hoy); d.setDate(hoy.getDate() + i);
                const fechaStr = d.toISOString().split('T')[0];
                
                const btn = document.createElement('button');
                btn.className = `date-btn ${i === 0 ? 'active' : ''}`;
                btn.textContent = i === 0 ? 'HOY' : fechaStr;
                btn.onclick = () => {
                    document.querySelectorAll('.date-btn').forEach(b => b.classList.remove('active'));
                    btn.classList.add('active');
                    fechaSeleccionada = fechaStr;
                    dibujarCartelera();
                };
                container.appendChild(btn);
            }
        }

        function cargarCartelera() {
            fetch('api.php?action=cartelera_cajero')
            .then(res => res.json())
            .then(data => {
                funcionesGlobales = data;
                dibujarCartelera();
            });
        }

        function dibujarCartelera() {
            const container = document.getElementById('carteleraContainer');
            container.innerHTML = '';
            
            const filtradas = funcionesGlobales.filter(f => f.fecha === fechaSeleccionada);
            
            if(filtradas.length === 0) {
                container.innerHTML = '<p style="grid-column:1/-1; text-align:center; color:#666; padding:50px;">No hay funciones para esta fecha.</p>';
                return;
            }

            filtradas.forEach(f => {
                let btnHtml = f.llena 
                    ? `<button class="btn-vender" style="background:#333; color:#666; cursor:not-allowed;" disabled>AGOTADO</button>` 
                    : `<button class="btn-vender" onclick="abrirMapaAsientos(${f.id})">VENDER BOLETOS</button>`;

                container.innerHTML += `
                    <div class="screening-card">
                        <img src="${f.imagen}" class="s-poster" onerror="this.src='img/fondo.jpg'">
                        <div class="s-info">
                            <div class="s-title">${f.titulo}</div>
                            <div class="s-sala">${f.sala}</div>
                            <div style="font-size:0.7rem; color:#888; margin-top:5px;">Disponibles: <b style="color:white;">${f.disponibles}</b></div>
                        </div>
                        <div class="s-time-box">
                            <span class="time-val">${f.hora.substring(0,5)}</span>
                            ${btnHtml}
                        </div>
                    </div>
                `;
            });
        }

        // --- 3. LÓGICA DEL MAPA DE ASIENTOS ---
        function abrirMapaAsientos(idFuncion) {
            const func = funcionesGlobales.find(f => f.id === idFuncion);
            funcionSeleccionadaId = func.id;
            precioActual = parseFloat(func.precio);
            asientosSeleccionados = [];
            
            // Limpiar datos del cliente al abrir nueva venta
            document.getElementById('clienteCI').value = '';
            document.getElementById('clienteNombre').value = '';
            
            document.getElementById('modalMovieTitle').textContent = func.titulo;
            document.getElementById('modalRoomInfo').textContent = `${func.sala} - Bs ${precioActual}`;
            
            const container = document.getElementById('seatContainer');
            container.innerHTML = '';
            
            const filas = parseInt(func.filas);
            const columnas = parseInt(func.columnas);
            const ocupados = func.asientos_vendidos ? func.asientos_vendidos.split(',') : [];

            for (let i = 0; i < filas; i++) {
                const letra = String.fromCharCode(65 + i); 
                const rowDiv = document.createElement('div');
                rowDiv.className = 'seat-row';
                
                rowDiv.innerHTML = `<div class="row-letter">${letra}</div>`;

                for (let j = 1; j <= columnas; j++) {
                    const seatId = `${letra}${j}`;
                    const isOccupied = ocupados.includes(seatId);
                    
                    const seat = document.createElement('div');
                    seat.className = `seat-icon ${isOccupied ? 'occupied' : ''}`;
                    seat.textContent = j;
                    
                    if (!isOccupied) seat.onclick = () => toggleSeat(seat, seatId);
                    if (j === Math.floor(columnas / 2)) seat.style.marginRight = '30px';
                    
                    rowDiv.appendChild(seat);
                }
                container.appendChild(rowDiv);
            }
            
            actualizarResumenVenta();
            document.getElementById('ventaModal').classList.add('active');
        }

        function toggleSeat(element, seatId) {
            if (asientosSeleccionados.includes(seatId)) {
                asientosSeleccionados = asientosSeleccionados.filter(id => id !== seatId);
                element.classList.remove('selected');
            } else {
                asientosSeleccionados.push(seatId);
                element.classList.add('selected');
            }
            actualizarResumenVenta();
        }

        function actualizarResumenVenta() {
            document.getElementById('asientosElegidos').textContent = asientosSeleccionados.length > 0 ? asientosSeleccionados.join(', ') : 'Ninguno';
            document.getElementById('precioTotal').textContent = (asientosSeleccionados.length * precioActual).toFixed(2);
            
            const btn = document.getElementById('btnConfirmarVenta');
            btn.disabled = asientosSeleccionados.length === 0;
            btn.style.opacity = asientosSeleccionados.length === 0 ? '0.5' : '1';
        }

        // --- 4. COBRAR E IMPRIMIR (US-10) ---
        function procesarVenta() {
            if(asientosSeleccionados.length === 0) return;

            // Recoger los datos del cliente (Si están vacíos, ponemos S/N)
            const nombreCliente = document.getElementById('clienteNombre').value.trim() || 'S/N';
            const ciCliente = document.getElementById('clienteCI').value.trim() || '0';

            const formData = new FormData();
            formData.append('action', 'vender_boletos');
            formData.append('idFuncion', funcionSeleccionadaId);
            formData.append('asientos', JSON.stringify(asientosSeleccionados));
            
            formData.append('ciCliente', ciCliente);

            fetch('api.php', { method: 'POST', body: formData })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    document.getElementById('ventaModal').classList.remove('active');
                    const totalPagado = asientosSeleccionados.length * precioActual;
                    
                    cargarCartelera(); // Actualizar asientos en tiempo real
                    
                    // Mostramos la factura al instante
                    imprimirFactura(
                        document.getElementById('modalMovieTitle').textContent, 
                        asientosSeleccionados, 
                        totalPagado,
                        nombreCliente,
                        ciCliente
                    );
                } else alert('❌ Error: ' + data.message);
            });
        }

        function imprimirFactura(pelicula, asientosArray, total, nombre, ci) {
            // Generamos una fecha y un número de factura aleatorio simulado
            const nroFactura = Math.floor(Math.random() * 90000) + 10000;
            const fechaHora = new Date().toLocaleString();
            
            // Creamos una lista detallada de los asientos comprados
            let detalleHTML = '';
            asientosArray.forEach(asiento => {
                detalleHTML += `
                <div style="display:flex; justify-content:space-between; font-size:12px; margin-bottom:5px;">
                    <span>1x Boleto (Asiento ${asiento})</span>
                    <span>Bs ${precioActual.toFixed(2)}</span>
                </div>`;
            });

            // Diseño de la factura estilo ticket de cine profesional
            const ticket = `
                <html><head><title>Factura #${nroFactura}</title>
                <style>
                    body { font-family: 'Courier New', Courier, monospace; width: 320px; padding: 20px; margin: 0 auto; color: #000; background: #fff;}
                    h2, h3, h4 { text-align: center; margin: 5px 0; }
                    .divisor { border-top: 1px dashed #000; margin: 15px 0; }
                    .row { display: flex; justify-content: space-between; font-size: 13px; margin-bottom: 5px;}
                    .btn-print { display:block; width:100%; padding:15px; background:#00d2d3; color:black; font-weight:bold; border:none; cursor:pointer; margin-bottom:20px; font-size:16px;}
                    @media print { .no-print { display: none; } body { width: 100%; padding:0; margin:0;} }
                </style>
                </head><body>
                    <button class="btn-print no-print" onclick="window.print()">🖨️ IMPRIMIR / GUARDAR PDF</button>
                    
                    <h2>MULTICINE LA PAZ</h2>
                    <h4>NIT: 1029384756</h4>
                    <p style="text-align:center; font-size:12px; margin-top:0;">Av. Arce, La Paz - Bolivia</p>
                    
                    <div class="divisor"></div>
                    <h3 style="margin-bottom:15px;">FACTURA NRO: ${nroFactura}</h3>
                    
                    <div class="row"><span><b>FECHA:</b></span> <span>${fechaHora}</span></div>
                    <div class="row"><span><b>SEÑOR(A):</b></span> <span>${nombre.toUpperCase()}</span></div>
                    <div class="row"><span><b>CI/NIT:</b></span> <span>${ci}</span></div>
                    
                    <div class="divisor"></div>
                    <div class="row" style="font-weight:bold; margin-bottom:10px;"><span><b>PELÍCULA:</b></span> <span>${pelicula.toUpperCase()}</span></div>
                    <div class="row"><span><b>SALA:</b></span> <span>${document.getElementById('modalRoomInfo').textContent.split(' - ')[0]}</span></div>
                    
                    <div class="divisor"></div>
                    <h4 style="text-align:left; margin-bottom:10px;">DETALLE DE COMPRA</h4>
                    ${detalleHTML}
                    
                    <div class="divisor"></div>
                    <div class="row" style="font-size:16px; font-weight:900;">
                        <span>TOTAL A PAGAR:</span>
                        <span>Bs ${total.toFixed(2)}</span>
                    </div>
                    
                    <div class="divisor"></div>
                    <p style="text-align:center; font-size:11px;"><b>CAJERO:</b> ${operadorActual.toUpperCase()}</p>
                    <p style="text-align:center; font-size:10px; margin-top:15px;">¡ESTA FACTURA CONTRIBUYE AL DESARROLLO DEL PAÍS, EL USO ILÍCITO SERÁ SANCIONADO PENALMENTE!</p>
                    <p style="text-align:center; font-size:11px; font-weight:bold; margin-top:15px;">¡Gracias por preferir Multicine!</p>
                </body></html>
            `;
            const win = window.open('', '_blank', 'width=450,height=800');
            win.document.write(ticket); 
            win.document.close();
        }   
    </script>
</body>
</html>