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

        .sidebar { width: 280px; background: #0a0a0a; border-right: 1px solid #222; display: flex; flex-direction: column; padding: 25px; z-index: 10; flex-shrink: 0;}
        .brand { color: var(--primary); font-weight: 900; font-size: 1.4rem; margin-bottom: 30px; text-align: center; }
        .cajero-btn { background: transparent; border: 1px solid #333; color: #888; padding: 15px; border-radius: 12px; margin-bottom: 10px; cursor: pointer; font-weight: bold; transition: 0.3s; display: flex; align-items: center; gap: 10px; }
        .cajero-btn:hover { background: #222; color: white; }
        .cajero-btn.active { background: var(--primary); color: black; border-color: var(--primary); }
        .btn-logout { margin-top: auto; background: rgba(255,71,87,0.1); color: var(--danger); border: 1px solid var(--danger); text-align: center; text-decoration: none; padding: 15px; border-radius: 12px; font-weight: bold; transition: 0.3s; display: block;}
        .btn-logout:hover { background: var(--danger); color: white; }

        .welcome-screen { flex: 1; display: flex; flex-direction: column; justify-content: center; align-items: center; background: #050505; z-index: 5; }
        
        .main-content { flex: 1; display: none; flex-direction: column; background: #050505; min-width: 0; }
        header { padding: 20px 40px; border-bottom: 1px solid #222; display: flex; justify-content: space-between; align-items: center; background: #0a0a0a; }
        .scrollable-area { flex: 1; overflow-y: auto; padding: 30px 40px; }
        
        .date-scroller { display: flex; gap: 10px; margin-bottom: 20px; overflow-x: auto; padding-bottom: 10px; }
        .date-btn { background: #111; border: 1px solid #333; padding: 10px 20px; border-radius: 10px; cursor: pointer; font-weight: bold; color: #888; white-space: nowrap;}
        .date-btn.active { background: var(--primary); color: black; border-color: var(--primary); }

        .screening-list { display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 20px; }
        .screening-card { display: flex; background: var(--card-bg); border-radius: 15px; border: 1px solid #222; overflow: hidden; height: 140px; }
        .s-poster { width: 95px; object-fit: cover; }
        .s-info { flex: 1; padding: 15px; display: flex; flex-direction: column; justify-content: center; min-width: 0;}
        .s-title { font-weight: 800; font-size: 0.9rem; text-transform: uppercase; margin-bottom: 5px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;}
        .s-sala { font-size: 0.7rem; color: #888; background: #222; padding: 3px 8px; border-radius: 4px; display: inline-block; align-self: flex-start;}
        .s-time-box { background: #1a1a1a; padding: 0 20px; display: flex; flex-direction: column; justify-content: center; align-items: center; border-left: 1px solid #222; min-width: 110px;}
        .time-val { font-size: 1.4rem; font-weight: 900; color: var(--primary); }
        .btn-vender { margin-top: 10px; background: white; color: black; border: none; padding: 5px 15px; border-radius: 15px; font-weight: bold; cursor: pointer; font-size: 0.7rem; transition: 0.3s; width: 100%;}
        .btn-vender:hover { background: var(--primary); }

        .modal-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.9); z-index: 500; display: none; align-items: center; justify-content: center; padding: 10px;}
        .modal-overlay.active { display: flex; }
        
        .modal-seat-content { background: #121212; width: 100%; max-width: 900px; border-radius: 20px; border: 1px solid #333; display: flex; flex-direction: column; max-height: 95vh; overflow: hidden; }
        .seat-map-container { padding: 20px; overflow-y: auto; flex: 1; min-height: 0; }
        
        .screen-display { width: 80%; height: 40px; background: linear-gradient(180deg, #fff, transparent); margin: 0 auto 30px auto; border-radius: 50% 50% 0 0 / 20px 20px 0 0; text-align: center; color: black; font-weight: bold; line-height: 30px; letter-spacing: 5px; transform: perspective(600px) rotateX(-30deg); opacity: 0.8; font-size: 0.8rem;}
        .seat-map-wrapper { width: 100%; display: flex; flex-direction: column; align-items: center; gap: 8px; margin-bottom: 20px;}
        .seat-row { display: flex; gap: 6px; align-items: center; justify-content: center; flex-wrap: wrap;}
        .row-letter { color: var(--primary); font-weight: bold; width: 20px; text-align: right; margin-right: 5px; }
        
        .seat-icon { width: 30px; height: 30px; border-radius: 5px 5px 10px 10px; background-color: #333; cursor: pointer; transition: 0.2s; position: relative; color: white; font-size: 0.6rem; display: flex; align-items: center; justify-content: center; font-weight: bold;}
        .seat-icon:hover { background-color: #555; transform: translateY(-3px); }
        .seat-icon.selected { background-color: var(--success) !important; color: black; transform: translateY(-5px); box-shadow: 0 0 10px var(--success); }
        .seat-icon.occupied { background-color: var(--danger) !important; color: transparent; cursor: not-allowed; opacity: 0.5;}
        
        .checkout-panel { background: #0a0a0a; padding: 15px 25px; border-top: 1px solid #333; display: flex; flex-direction: column; gap: 10px; flex-shrink: 0; overflow-y: auto;}
        .btn-confirm { background: var(--primary); color: black; border: none; padding: 12px 20px; border-radius: 8px; font-weight: 900; cursor: pointer; transition: 0.3s; white-space: nowrap;}
        
        .form-input { background: #1a1a1a; color: white; border: 1px solid #333; border-radius: 8px; }
        .form-input:focus { outline: none; border-color: var(--primary); }
        
        @media (max-width: 768px) {
            .sidebar { display: none; } 
            .checkout-panel-row { flex-direction: column; align-items: stretch !important; gap: 10px !important;}
            .checkout-panel-row > div { width: 100%; }
        }
    </style>
</head>
<body>

    <div class="sidebar">
        <div class="brand"><i class="fas fa-ticket-alt"></i> TAQUILLA LP</div>
        
        <div style="background: #111; padding: 15px; border-radius: 10px; margin-bottom: 20px; border: 1px solid #333; text-align:center;">
            <i class="fas fa-user-circle" style="font-size: 2rem; color: var(--primary); margin-bottom:10px;"></i>
            <h4 style="color: white; font-size: 0.9rem; margin:0;">{{ auth()->user()->nombre ?? 'Cajero' }}</h4>            
            <p style="color: #666; font-size: 0.7rem; margin-top:3px;">Operador en Turno</p>
        </div>

        <div id="terminales">
            <button class="cajero-btn" onclick="iniciarTurno('Terminal 1', this)"><i class="fas fa-desktop"></i> Terminal 1</button>
            <button class="cajero-btn" onclick="iniciarTurno('Terminal 2', this)"><i class="fas fa-desktop"></i> Terminal 2</button>
            <button class="cajero-btn" onclick="iniciarTurno('Terminal 3', this)"><i class="fas fa-desktop"></i> Terminal 3</button>
            <button class="cajero-btn" onclick="abrirBuscadorTicket()" style="background: rgba(0, 210, 211, 0.1); color: var(--primary); border-color: var(--primary); margin-top: 20px;"><i class="fas fa-qrcode"></i> VERIFICAR TICKET</button>
        </div>
        
        <button class="cajero-btn" id="btnReimprimir" onclick="reimprimirUltimo()" style="display:none; background:#1a1a1a; color:var(--primary); border-color:var(--primary); margin-top:20px;">
            <i class="fas fa-print"></i> REIMPRIMIR ÚLTIMO
        </button>

        <a href="{{ url('/') }}" class="btn-logout"><i class="fas fa-power-off"></i> CERRAR SESIÓN</a>
    </div>

    <div class="welcome-screen" id="welcomeScreen">
        <i class="fas fa-lock" style="font-size: 4rem; color: #333; margin-bottom: 20px;"></i>
        <h2 style="color: #666;">CAJA CERRADA</h2>
        <p style="color: #444;">Selecciona en qué terminal estás trabajando para abrir el sistema.</p>
    </div>

    <div class="main-content" id="mainContent">
        <header>
            <div><span style="color:#888; font-size:0.8rem;">OPERADOR:</span> <strong style="color:var(--primary); margin-left:10px;">{{ auth()->user()->nombre ?? 'Cajero ' }}</strong></div>
            <div><span style="color:#888; font-size:0.8rem;">ESTACIÓN:</span> <strong id="headerTerminal" style="color:white; margin-left:10px;">...</strong></div>
        </header>
        <div class="scrollable-area">
            <div class="date-scroller" id="dateScroller"></div>
            <div class="screening-list" id="carteleraContainer"></div>
        </div>
    </div>

    <div class="modal-overlay" id="ventaModal">
        <div class="modal-seat-content">
            <div style="padding: 15px 25px; background: #1a1a1a; border-bottom: 1px solid #333; display: flex; justify-content: space-between; align-items: center; flex-shrink: 0;">
                <div>
                    <h3 id="modalMovieTitle" style="color:white; text-transform:uppercase; margin:0; font-size:1.1rem;">Pelicula</h3>
                    <small style="color:var(--primary);" id="modalRoomInfo">Sala Info</small>
                </div>
                <button onclick="document.getElementById('ventaModal').classList.remove('active')" style="background:none; border:none; color:#666; font-size:2rem; cursor:pointer;">&times;</button>
            </div>
            
            <div class="seat-map-container">
                <div class="screen-display">PANTALLA</div>
                <div id="seatContainer" class="seat-map-wrapper"></div>
            </div>

            <div class="checkout-panel">
                <div class="checkout-panel-row" style="display: flex; gap: 15px; align-items: flex-end;">
                    <div style="flex: 1;">
                        <label style="color:#888; font-size:0.8rem;">CI / NIT Cliente:</label>
                        <div style="display: flex; gap: 5px; margin-top: 5px;">
                            <input type="text" id="clienteCI" class="form-input" placeholder="Ej. 1234567" style="margin: 0; padding: 10px; flex: 1;">
                            <button onclick="verificarSocio()" style="background:var(--primary); color:black; border:none; padding:10px 15px; border-radius:8px; font-weight:bold; cursor:pointer;" title="Validar Socio"><i class="fas fa-search"></i></button>
                        </div>
                    </div>
                    <div style="flex: 2;">
                        <label style="color:#888; font-size:0.8rem;">Nombre / Razón Social:</label>
                        <input type="text" id="clienteNombre" class="form-input" placeholder="S/N" style="margin: 5px 0 0 0; padding: 10px; width: 100%;">
                    </div>
                </div>

                <div id="panelPuntos" style="display: none; background: rgba(0, 210, 211, 0.1); border: 1px solid var(--primary); padding: 8px 15px; border-radius: 8px; align-items: center; justify-content: space-between;">
                    <div>
                        <i class="fas fa-star" style="color: var(--primary);"></i> 
                        <span style="color: white; font-size: 0.8rem; margin-left: 5px;">Cliente tiene <b id="lblPuntosDisp">0</b> puntos disponibles.</span>
                    </div>
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <label style="color: var(--primary); font-weight: bold; font-size: 0.8rem;">Canjear:</label>
                        <input type="number" id="inputPuntosCanjear" class="form-input" value="0" min="0" oninput="actualizarResumenVenta()" style="width: 70px; padding: 5px; text-align: center; height: 30px; margin:0;">
                    </div>
                </div>
                
                <div class="checkout-panel-row" style="display: flex; gap: 15px; background: #111; padding: 10px 15px; border-radius: 10px; border: 1px solid #222; align-items: center; flex-wrap: wrap;">
                    <span style="color:var(--primary); font-weight:bold; font-size:0.8rem;"><i class="fas fa-users"></i> TARIFAS:</span>
                    <div>
                        <label style="color:#888; font-size:0.75rem;">Adultos (Bs <span id="lblPrecioAdulto">0.00</span>):</label>
                        <input type="number" id="cantAdulto" value="0" min="0" onchange="validarCantidades()" class="form-input" style="width:60px; padding:5px; margin:0; height:30px; text-align: center;">
                    </div>
                    <div>
                        <label style="color:#888; font-size:0.75rem;" id="lblNinoText">Niños -20% (Bs <span id="lblPrecioNino">0.00</span>):</label>
                        <input type="number" id="cantNino" value="0" min="0" onchange="validarCantidades()" class="form-input" style="width:60px; padding:5px; margin:0; height:30px; text-align: center;">
                    </div>
                    <div id="msgPromo" style="margin-left:auto; color:var(--success); font-size:0.8rem; font-weight:bold; display:none; border: 1px solid var(--success); padding: 3px 10px; border-radius: 20px;">
                        <i class="fas fa-tag"></i> ¡MIÉRCOLES EXTRA!
                    </div>
                </div>
                
                <div class="checkout-panel-row" style="display: flex; justify-content: space-between; align-items: center; border-top: 1px solid #333; padding-top: 10px;">
                    <div>
                        <span style="color:#888; font-size:0.75rem; display:block;">Asientos Seleccionados:</span>
                        <strong id="asientosElegidos" style="color:white; font-size:1rem; word-break: break-all;">Ninguno</strong>
                    </div>
                    <div style="text-align:right;">
                        <span style="color:#888; font-size:0.7rem; display:block;" id="lblSubtotal">Subtotal: Bs 0.00</span>
                        <span style="color:var(--primary); font-size:0.7rem; display:none; font-weight: bold;" id="lblAhorro">Pago con Puntos: -Bs 0.00</span>
                        <span style="color:#888; font-size:0.75rem; display:block;">Total a Pagar:</span>
                        <strong style="color:var(--success); font-size:1.6rem;">Bs <span id="precioTotal">0.00</span></strong>
                    </div>
                    <button class="btn-confirm" id="btnConfirmarVenta" onclick="procesarVenta()" disabled>COBRAR Y FACTURAR</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-overlay" id="ticketModal">
        <div class="modal-content" style="max-width: 400px; padding: 30px; background: #121212; border: 1px solid var(--primary); border-radius: 15px;">
            <h3 style="color:var(--primary); margin-bottom: 15px; text-align: center;"><i class="fas fa-search"></i> VERIFICAR TICKET</h3>
            
            <input type="text" id="inputCodigoTicket" class="form-input" placeholder="Ej. TK-A1B2C o CJ-9F8D7" style="text-align: center; font-size: 1.2rem; text-transform: uppercase;">
            <button class="btn-confirm" onclick="buscarTicket()" style="width: 100%; margin-bottom: 20px;">BUSCAR EN SISTEMA</button>
            
            <div id="resultadoTicket" style="display:none; background: #1a1a1a; padding: 15px; border-radius: 10px; border: 1px solid #333;"></div>
            
            <button onclick="document.getElementById('ticketModal').classList.remove('active')" style="background:transparent; border:none; color:#888; width:100%; cursor:pointer; margin-top:10px; font-weight: bold;">CERRAR</button>
        </div>
    </div>
    <script>
        const operadorActual = "{{ auth()->user()->nombre ?? 'Cajero' }}";
        
        let terminalActual = '';
        let funcionesGlobales = [];
        let fechaSeleccionada = new Date().toISOString().split('T')[0];
        
        let funcionSeleccionadaId = null;
        let asientosSeleccionados = [];
        let precioActual = 0;
        
        let esSocio = false;
        let isMiercoles = false;
        let totalFinalVenta = 0;
        let ultimoTicketGlobal = null;

        // Variables de Puntos
        let puntosDisponibles = 0;
        let puntosAUsar = 0;
        let puntosGanados = 0;

        let configTarifas = { nino: 0, socio: 0, miercoles: 0 };

        /**
         * NUEVO: Cargar tarifas desde el sistema de archivos (Laravel API)
         */
        function cargarTarifas() {
            fetch('/api/config/tarifas')
                .then(res => res.json())
                .then(data => { 
                    configTarifas = data; 
                    console.log("Tarifas cajero cargadas:", configTarifas);
                    if(typeof actualizarResumenVenta === 'function') actualizarResumenVenta();
                })
                .catch(e => console.error('Error cargando tarifas en cajero:', e));
        }

        document.addEventListener('DOMContentLoaded', () => {
            generarFechas();
            cargarCartelera();
            cargarTarifas();
        });

        function iniciarTurno(terminal, btn) {
            terminalActual = terminal;
            document.querySelectorAll('.cajero-btn').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            document.getElementById('headerTerminal').textContent = terminalActual;
            document.getElementById('welcomeScreen').style.display = 'none';
            document.getElementById('mainContent').style.display = 'flex';
        }

        function generarFechas() {
            const container = document.getElementById('dateScroller');
            const hoy = new Date();
            for(let i=0; i<7; i++) {
                const d = new Date(hoy); d.setDate(hoy.getDate() + i);
                const fechaStr = d.toISOString().split('T')[0];
                const btn = document.createElement('button');
                btn.className = `date-btn ${i === 0 ? 'active' : ''}`;
                btn.textContent = i === 0 ? 'HOY' : fechaStr;
                btn.onclick = () => { document.querySelectorAll('.date-btn').forEach(b => b.classList.remove('active')); btn.classList.add('active'); fechaSeleccionada = fechaStr; dibujarCartelera(); };
                container.appendChild(btn);
            }
        }

        function cargarCartelera() { 
            fetch('/api/cartelera')
            .then(res => res.json())
            .then(data => { 
                funcionesGlobales = data; 
                dibujarCartelera(); 
            })
            .catch(error => console.error("Error cargando cartelera:", error));
        }
        function dibujarCartelera() {
            const container = document.getElementById('carteleraContainer');
            container.innerHTML = '';
            const filtradas = funcionesGlobales.filter(f => f.fecha === fechaSeleccionada);
            if(filtradas.length === 0) { container.innerHTML = '<p style="grid-column:1/-1; text-align:center; color:#666; padding:50px;">No hay funciones registradas o el controlador de cartelera aún no está listo.</p>'; return; }
            filtradas.forEach(f => {
                let btnHtml = f.llena ? `<button class="btn-vender" style="background:#333; color:#666; cursor:not-allowed;" disabled>AGOTADO</button>` : `<button class="btn-vender" onclick="abrirMapaAsientos(${f.id})">VENDER BOLETOS</button>`;
                container.innerHTML += `<div class="screening-card"><img src="${f.imagen}" class="s-poster" onerror="this.src='img/fondo.jpg'"><div class="s-info"><div class="s-title" title="${f.titulo}">${f.titulo}</div><div class="s-sala">${f.sala}</div><div style="font-size:0.7rem; color:#888; margin-top:5px;">Disponibles: <b style="color:white;">${f.disponibles}</b></div></div><div class="s-time-box"><span class="time-val">${f.hora.substring(0,5)}</span>${btnHtml}</div></div>`;
            });
        }

        function abrirMapaAsientos(idFuncion) {
            const func = funcionesGlobales.find(f => f.id === idFuncion);
            funcionSeleccionadaId = func.id;
            precioActual = parseFloat(func.precio);
            asientosSeleccionados = [];
            
            esSocio = false;
            puntosDisponibles = 0;
            puntosAUsar = 0;
            document.getElementById('clienteCI').value = '';
            document.getElementById('clienteNombre').value = '';
            if(document.getElementById('inputPuntosCanjear')) document.getElementById('inputPuntosCanjear').value = 0;
            document.getElementById('panelPuntos').style.display = 'none';
            document.getElementById('lblAhorro').style.display = 'none';
            
            const diaSemana = new Date(fechaSeleccionada + 'T12:00:00').getDay();
            isMiercoles = (diaSemana === 3);
            document.getElementById('msgPromo').style.display = isMiercoles ? 'block' : 'none';

            document.getElementById('lblPrecioAdulto').textContent = precioActual.toFixed(2);
            document.getElementById('modalMovieTitle').textContent = func.titulo;
            document.getElementById('modalRoomInfo').textContent = `${func.sala} - Tarifa Base: Bs ${precioActual.toFixed(2)}`;
            
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
                    if (j === Math.floor(columnas / 2)) seat.style.marginRight = '20px';
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
            document.getElementById('cantAdulto').value = asientosSeleccionados.length;
            document.getElementById('cantNino').value = 0;
            actualizarResumenVenta();
        }

        function validarCantidades() {
            let a = parseInt(document.getElementById('cantAdulto').value) || 0;
            let n = parseInt(document.getElementById('cantNino').value) || 0;
            const totalAsientos = asientosSeleccionados.length;
            if(a + n !== totalAsientos) { a = totalAsientos - n; if (a < 0) { a = 0; n = totalAsientos; } }
            document.getElementById('cantAdulto').value = a;
            document.getElementById('cantNino').value = n;
            actualizarResumenVenta();
        }

        function verificarSocio() {
            const ci = document.getElementById('clienteCI').value.trim();
            if(!ci) { alert("⚠️ Ingresa el CI o NIT."); return; }

            const fd = new FormData();
            fd.append('ci', ci);

            fetch('/api/socio/verificar', { 
                method: 'POST', 
                body: fd 
            })
            .then(r => r.json())
            .then(data => {
                if(data.status === 'success') {
                    document.getElementById('clienteNombre').value = data.nombre;
                    esSocio = true;
                    puntosDisponibles = parseInt(data.puntos) || 0;
                    
                    // Mostramos el panel de estrellita con los puntos
                    document.getElementById('panelPuntos').style.display = 'flex';
                    document.getElementById('lblPuntosDisp').textContent = puntosDisponibles;
                    document.getElementById('inputPuntosCanjear').value = 0;
                    
                    alert(`✅ Cliente encontrado. Tiene ${puntosDisponibles} puntos acumulados.`);
                    actualizarResumenVenta(); // Recalcula los totales
                } else {
                    esSocio = false;
                    puntosDisponibles = 0;
                    document.getElementById('panelPuntos').style.display = 'none';
                    document.getElementById('inputPuntosCanjear').value = 0;
                    document.getElementById('clienteNombre').value = ''; // Limpiamos la casilla
                    alert("❌ " + data.message);
                    actualizarResumenVenta();
                }
            })
            .catch(e => {
                console.error("Error conectando con Laravel:", e);
                alert("Ocurrió un error al buscar al cliente.");
            });
        }

        function actualizarResumenVenta() {
            document.getElementById('asientosElegidos').textContent = asientosSeleccionados.length > 0 ? asientosSeleccionados.join(', ') : 'Ninguno';
            
            let a = parseInt(document.getElementById('cantAdulto').value) || 0;
            let n = parseInt(document.getElementById('cantNino').value) || 0;
            
            let descNinoMath = configTarifas.nino / 100;
            let descMiercolesMath = configTarifas.miercoles / 100;

            let precioNinoCalculado = (precioActual * (1 - descNinoMath)).toFixed(2);
            document.getElementById('lblNinoText').innerHTML = `Niños -${configTarifas.nino}% (Bs <span id="lblPrecioNino">${precioNinoCalculado}</span>):`;
            document.getElementById('msgPromo').innerHTML = `<i class="fas fa-tag"></i> ¡MIÉRCOLES ${configTarifas.miercoles}% EXTRA!`;

            let precioNinoAplicado = precioActual * (1 - descNinoMath);
            let subtotal = (a * precioActual) + (n * precioNinoAplicado);
            
            // Si es miércoles, aplicamos el descuento configurado al total
            if(isMiercoles && configTarifas.miercoles > 0) {
                subtotal = subtotal * (1 - descMiercolesMath);
                document.getElementById('msgPromo').style.display = 'block';
            } else {
                document.getElementById('msgPromo').style.display = 'none';
            }
            
            document.getElementById('lblSubtotal').textContent = `Subtotal: Bs ${subtotal.toFixed(2)}`;

            let inputPuntos = document.getElementById('inputPuntosCanjear');
            let ptsDeseados = parseInt(inputPuntos ? inputPuntos.value : 0) || 0;
            
            let maxCanjeable = Math.floor(Math.min(puntosDisponibles, subtotal));
            
            if (ptsDeseados > maxCanjeable) ptsDeseados = maxCanjeable;
            if (ptsDeseados < 0) ptsDeseados = 0;
            
            if (inputPuntos) inputPuntos.value = ptsDeseados;
            puntosAUsar = ptsDeseados;

            if (esSocio && puntosAUsar > 0 && asientosSeleccionados.length > 0) {
                document.getElementById('lblAhorro').style.display = 'block';
                document.getElementById('lblAhorro').textContent = `Pago con Puntos (${puntosAUsar}): -Bs ${puntosAUsar.toFixed(2)}`;
            } else {
                document.getElementById('lblAhorro').style.display = 'none';
            }

            totalFinalVenta = subtotal - puntosAUsar;
            document.getElementById('precioTotal').textContent = totalFinalVenta.toFixed(2);
            
            puntosGanados = Math.floor(totalFinalVenta / 30);
            
            const btn = document.getElementById('btnConfirmarVenta');
            btn.disabled = asientosSeleccionados.length === 0;
            btn.style.opacity = asientosSeleccionados.length === 0 ? '0.5' : '1';
        }

        function procesarVenta() {
            if(asientosSeleccionados.length === 0) return;

            // Cambiamos el texto del botón para que el cajero sepa que está cargando
            const btn = document.getElementById('btnConfirmarVenta');
            btn.textContent = "PROCESANDO...";
            btn.disabled = true;

            const nombreCliente = document.getElementById('clienteNombre').value.trim() || 'S/N';
            const ciCliente = document.getElementById('clienteCI').value.trim() || '0';

            const formData = new FormData();
            formData.append('idFuncion', funcionSeleccionadaId);
            formData.append('asientos', JSON.stringify(asientosSeleccionados));
            formData.append('ciCliente', ciCliente);
            formData.append('total', totalFinalVenta.toFixed(2));
            formData.append('puntosUsados', puntosAUsar);

            // Hacemos la petición POST a nuestra nueva ruta de Laravel
            fetch('/api/venta/procesar', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    document.getElementById('ventaModal').classList.remove('active');
                    cargarCartelera(); // Recargamos para que los asientos aparezcan ocupados
                    
                    // Guardamos los datos para imprimir el ticket
                    ultimoTicketGlobal = {
                        pelicula: document.getElementById('modalMovieTitle').textContent,
                        asientosArray: asientosSeleccionados,
                        total: totalFinalVenta,
                        nombre: nombreCliente,
                        ci: ciCliente,
                        codigoAcceso: data.codigo, // El código generado por Laravel
                        cantAdulto: parseInt(document.getElementById('cantAdulto').value) || 0,
                        cantNino: parseInt(document.getElementById('cantNino').value) || 0,
                        esMiercoles: isMiercoles,
                        puntosUsadosTicket: puntosAUsar,
                        puntosGanadosTicket: puntosGanados
                    };

                    document.getElementById('btnReimprimir').style.display = 'flex';
                    imprimirFactura(ultimoTicketGlobal); // ¡Abre el ticket!
                } else {
                    alert('❌ Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error("Error en la venta:", error);
                alert("Ocurrió un error de conexión.");
            })
            .finally(() => {
                btn.textContent = "COBRAR Y FACTURAR";
                btn.disabled = false;
            });
        }

        function reimprimirUltimo() { if (ultimoTicketGlobal) imprimirFactura(ultimoTicketGlobal); else alert("No hay ventas recientes para re-imprimir."); }

        function imprimirFactura(ticketData) {
            const nroFactura = Math.floor(Math.random() * 90000) + 10000;
            const fechaHora = new Date().toLocaleString();
            
            let detalleHTML = '';
            detalleHTML += `<div style="font-size:12px; margin-bottom:10px;"><b>Asientos:</b> ${ticketData.asientosArray.join(', ')}</div>`;
            detalleHTML += `<div style="font-size:12px; margin-bottom:5px;">${ticketData.cantAdulto}x Boleto(s) Adulto</div>`;
            if(ticketData.cantNino > 0) detalleHTML += `<div style="font-size:12px; margin-bottom:5px;">${ticketData.cantNino}x Boleto(s) Niño</div>`;
            if(ticketData.esMiercoles) detalleHTML += `<div style="font-size:12px; margin-bottom:5px; color:#555;">* Promoción Miércoles Aplicada</div>`;
            
            if(ticketData.puntosUsadosTicket > 0) detalleHTML += `<div style="font-size:12px; margin-bottom:5px; color:black; font-weight:bold;">* PAGO CON PUNTOS: -Bs ${ticketData.puntosUsadosTicket}</div>`;

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
                    <div class="row"><span><b>SEÑOR(A):</b></span> <span>${ticketData.nombre.toUpperCase()}</span></div>
                    <div class="row"><span><b>CI/NIT:</b></span> <span>${ticketData.ci}</span></div>
                    
                    <div class="divisor"></div>
                    <div class="row" style="font-weight:bold; margin-bottom:10px;"><span><b>PELÍCULA:</b></span> <span>${ticketData.pelicula.toUpperCase()}</span></div>
                    <div class="row"><span><b>SALA:</b></span> <span>${document.getElementById('modalRoomInfo').textContent.split(' - ')[0]}</span></div>
                    
                    <div class="divisor"></div>
                    <div class="row" style="font-weight:900; font-size:15px; justify-content: center; text-align: center; display: block;">
                        <span>CÓDIGO DE ACCESO (QR/CINE):</span><br>
                        <span style="font-size:22px;">${ticketData.codigoAcceso}</span>
                    </div>
                    
                    <div class="divisor"></div>
                    <h4 style="text-align:left; margin-bottom:10px;">DETALLE DE COMPRA</h4>
                    ${detalleHTML}
                    
                    <div class="divisor"></div>
                    <div class="row" style="font-size:16px; font-weight:900;">
                        <span>TOTAL PAGADO:</span>
                        <span>Bs ${ticketData.total.toFixed(2)}</span>
                    </div>
                    
                    <div class="divisor"></div>
                    <p style="text-align:center; font-size:12px; font-weight:bold;">¡Felicidades! Ganaste ${ticketData.puntosGanadosTicket} pts en esta compra.</p>
                    <p style="text-align:center; font-size:11px; margin-top:10px;"><b>CAJERO:</b> ${operadorActual.toUpperCase()}</p>
                </body></html>
            `;
            const win = window.open('', '_blank', 'width=450,height=800');
            win.document.write(ticket); 
            win.document.close();
        }

        function abrirBuscadorTicket() {
            document.getElementById('inputCodigoTicket').value = '';
            document.getElementById('resultadoTicket').style.display = 'none';
            document.getElementById('ticketModal').classList.add('active');
        }

        function buscarTicket() {
            const codigo = document.getElementById('inputCodigoTicket').value.trim();
            if(!codigo) { alert("⚠️ Ingrese un código."); return; }
            
            const resDiv = document.getElementById('resultadoTicket');
            resDiv.style.display = 'block';
            resDiv.innerHTML = '<p style="text-align:center; color:var(--primary);">Buscando...</p>';

            const fd = new FormData();
            fd.append('codigo', codigo);

            fetch('/api/ticket/verificar', { method: 'POST', body: fd })
            .then(res => res.json())
            .then(data => {
                if(data.status === 'success') {
                    const t = data.data;
                    resDiv.innerHTML = `
                        <div style="border-bottom: 1px solid #333; padding-bottom:10px; margin-bottom:10px;">
                            <span style="color:#888; font-size:0.7rem;">PELÍCULA:</span><br>
                            <strong style="color:var(--primary); font-size:1rem;">${t.titulo.toUpperCase()}</strong>
                        </div>
                        <div style="display:grid; grid-template-columns: 1fr 1fr; gap:10px; margin-bottom:10px;">
                            <div>
                                <span style="color:#888; font-size:0.7rem;">SALA:</span><br>
                                <strong style="color:white;">${t.sala}</strong>
                            </div>
                            <div>
                                <span style="color:#888; font-size:0.7rem;">TOTAL:</span><br>
                                <strong style="color:var(--success);">Bs ${parseFloat(t.total).toFixed(2)}</strong>
                            </div>
                        </div>
                        <div style="margin-bottom:10px;">
                            <span style="color:#888; font-size:0.7rem;">FECHA Y HORA:</span><br>
                            <strong style="color:white;">${t.fechaFuncion} - ${t.horaInicio.substring(0,5)}</strong>
                        </div>
                        <div>
                            <span style="color:#888; font-size:0.7rem;">ASIENTOS:</span><br>
                            <strong style="color:var(--primary); font-size:1.1rem;">${t.asientos}</strong>
                        </div>
                        <div style="margin-top:15px; padding-top:10px; border-top:1px dashed #444; text-align:center;">
                            <span style="background:var(--success); color:black; padding:4px 10px; border-radius:5px; font-weight:900; font-size:0.8rem;">TICKET VÁLIDO</span>
                        </div>
                    `;
                } else {
                    resDiv.innerHTML = `<p style="text-align:center; color:var(--danger); font-weight:bold;">❌ ${data.message}</p>`;
                }
            })
            .catch(err => {
                resDiv.innerHTML = `<p style="text-align:center; color:white;">Error de conexión.</p>`;
                console.error(err);
            });
        }
    </script>
</body>
</html>