<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Cartelera - Multicine La Paz</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;800;900&display=swap');
        :root { --primary: #00d2d3; --dark-bg: #050505; --card-bg: #111; --danger: #ff4757; --success: #2ed573; }
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Montserrat', sans-serif; }
        body { background: var(--dark-bg); color: white; padding-bottom: 50px; }

        header { padding: 20px 50px; border-bottom: 1px solid #222; display: flex; justify-content: space-between; align-items: center; background: rgba(10, 10, 10, 0.95); position: sticky; top: 0; z-index: 100; backdrop-filter: blur(10px); }
        .logo { color: var(--primary); font-weight: 900; font-size: 1.5rem; text-decoration: none; }
        .user-menu { display: flex; align-items: center; gap: 20px; }
        .btn-logout { background: transparent; color: var(--danger); border: 1px solid var(--danger); padding: 8px 20px; border-radius: 20px; text-decoration: none; font-weight: bold; font-size: 0.8rem; }
        .btn-historial { background: var(--primary); color: black; padding: 8px 20px; border-radius: 20px; text-decoration: none; font-weight: bold; font-size: 0.8rem; }

        .date-scroller { display: flex; gap: 15px; overflow-x: auto; padding: 20px 50px; justify-content: center; }
        .date-btn { background: #111; border: 1px solid #333; padding: 12px 20px; border-radius: 12px; cursor: pointer; text-align: center; min-width: 85px; }
        .date-btn .d-day { display: block; font-size: 0.7rem; color: #888; text-transform: uppercase; font-weight: bold; }
        .date-btn .d-date { display: block; font-size: 1.2rem; font-weight: 900; }
        .date-btn.active { background: var(--primary); color: black; border-color: var(--primary); box-shadow: 0 5px 15px rgba(0, 210, 211, 0.4); }

        .movies-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 40px; padding: 20px 50px; max-width: 1400px; margin: 0 auto; }
        .movie-card { background: var(--card-bg); border-radius: 20px; overflow: hidden; border: 1px solid #222; transition: 0.3s; display: flex; flex-direction: column; }
        .m-poster { width: 100%; height: 400px; object-fit: cover; border-bottom: 3px solid var(--primary); }
        .m-info { padding: 20px; flex: 1; }
        .m-title { font-size: 1.3rem; font-weight: 900; text-transform: uppercase; margin-bottom: 15px; }
        .btn-info-action { background: transparent; border: 1px solid var(--primary); color: var(--primary); padding: 10px; border-radius: 8px; width: 100%; font-weight: 900; font-size: 0.8rem; cursor: pointer; margin-bottom: 10px; }
        .btn-time { background: #1a1a1a; border: 1px solid #333; color: white; padding: 10px; border-radius: 8px; cursor: pointer; font-weight: bold; min-width: 80px; }

        .modal-overlay { position: fixed; inset: 0; background: rgba(0,0,0,0.95); z-index: 500; display: none; align-items: center; justify-content: center; backdrop-filter: blur(8px); }
        .modal-overlay.active { display: flex; }
        .modal-content { width: 95%; max-width: 1000px; background: #121212; border-radius: 20px; border: 1px solid #333; overflow: hidden; }
        .modal-body-split { display: flex; flex-wrap: wrap; height: 75vh; }
        .map-section { flex: 2; padding: 30px; border-right: 1px solid #222; overflow-y: auto; text-align: center; }
        .pay-section { flex: 1; padding: 30px; background: #0a0a0a; overflow-y: auto; }

        .screen-display { width: 80%; height: 30px; background: white; margin: 0 auto 40px; border-radius: 0 0 50% 50%; color: black; font-weight: 900; letter-spacing: 10px; font-size: 0.7rem; line-height: 25px; text-align: center;}
        .seat-row { display: flex; gap: 8px; align-items: center; justify-content: center; margin-bottom: 10px; }
        .row-letter { color: var(--primary); font-weight: bold; width: 25px; font-size: 0.8rem; }
        .seat-icon { width: 30px; height: 30px; border-radius: 5px; background: #333; cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: 0.7rem; font-weight: bold; }
        .seat-icon.selected { background: var(--primary); color: black; box-shadow: 0 0 15px var(--primary); }
        .seat-icon.occupied { background: var(--danger); opacity: 0.3; cursor: not-allowed; }
        .btn-pay { width: 100%; padding: 15px; background: var(--primary); border: none; font-weight: 900; cursor: pointer; border-radius: 8px; font-size: 1.1rem; color: black; }
    </style>
</head>
<body>

    <header>
        <div class="logo">MULTICINE</div>
        <div class="user-menu">
            <span class="user-name">Hola, {{ auth()->user()->nombre ?? 'Invitado' }} 
                <span id="headerPuntos" style="color:var(--primary); margin-left:10px;"><i class="fas fa-star"></i> {{ auth()->user()->puntos ?? 0 }} pts</span>
            </span>
            <a href="/historial" class="btn-historial">MIS ENTRADAS</a>
            <a href="{{ route('logout') }}" class="btn-logout">SALIR</a>
        </div>
    </header>

    <div class="hero" style="text-align:center; padding:40px;"><h1>EN CARTELERA</h1><p style="color:#666;">Selecciona una fecha para ver los horarios disponibles</p></div>
    <div class="date-scroller" id="dateScroller"></div>
    <div class="movies-grid" id="moviesContainer"></div>

    <div class="modal-overlay" id="infoModal">
        <div class="modal-content" style="max-width: 600px; padding: 40px; text-align: center;">
            <h2 id="infoTitulo" style="color:var(--primary); text-transform:uppercase; margin-bottom:15px; font-weight:900;"></h2>
            <p id="infoSinopsis" style="color:#ccc; line-height:1.8; margin-bottom:25px;"></p>
            <div style="display:flex; justify-content:center; gap:20px; font-size:0.8rem; border-top:1px solid #333; padding-top:20px; color:#666;">
                <span>DURACIÓN: <b id="infoDuracion" style="color:white;"></b> min</span>
                <span>GENERO: <b id="infoGenero" style="color:white;"></b></span>
                <span>CLASIF: <b id="infoClasif" style="color:white;"></b></span>
            </div>
            <button onclick="document.getElementById('infoModal').classList.remove('active')" style="margin-top:30px; width:100%; padding:15px; background:var(--primary); border:none; border-radius:8px; font-weight:900; cursor:pointer;">CERRAR</button>
        </div>
    </div>

    <div class="modal-overlay" id="compraModal">
        <div class="modal-content">
            <div style="padding:20px; background:#1a1a1a; display:flex; justify-content:space-between; align-items:center;">
                <div>
                    <h3 id="modalMovieTitle" style="text-transform:uppercase; font-weight:900;"></h3>
                    <small id="modalRoomInfo" style="color:var(--primary);"></small>
                </div>
                <button onclick="document.getElementById('compraModal').classList.remove('active')" style="background:none; border:none; color:white; font-size:2rem; cursor:pointer;">&times;</button>
            </div>
            <div class="modal-body-split">
                <div class="map-section">
                    <div class="screen-display">PANTALLA</div>
                    <div id="seatContainer"></div>
                </div>
                <div class="pay-section">
                    <h4 style="color:#888; font-size:0.8rem; text-transform:uppercase; margin-bottom:15px;">Resumen de Compra</h4>
                    
                    <div id="panelPuntos" style="background:rgba(0,210,211,0.1); border:1px solid var(--primary); padding:12px; border-radius:8px; margin-bottom:15px; display:flex; justify-content:space-between; align-items:center;">
                        <span style="font-size:0.85rem;"><i class="fas fa-star"></i> Tienes <b id="misPuntosActuales">{{ auth()->user()->puntos ?? 0 }}</b> pts</span>
                        <div style="display:flex; align-items:center; gap:8px;">
                            <label style="font-size:0.75rem; color:var(--primary); font-weight:bold;">Canjear:</label>
                            <input type="number" id="inputPuntosCliente" value="0" min="0" oninput="actualizarResumen()" style="width:60px; background:#111; color:white; border:1px solid #333; text-align:center; padding:4px;">
                        </div>
                    </div>

                    <div style="background:#111; padding:15px; border-radius:10px; border:1px solid #222; margin-bottom:20px;">
                        <div style="display:flex; justify-content:space-between; margin-bottom:10px;">
                            <label style="color:#888; font-size:0.8rem;">Adultos (Bs <span id="lblPrecioAdulto"></span>):</label>
                            <input type="number" id="cantAdulto" value="0" min="0" onchange="validarCantidades()" style="width:55px; text-align:center; background:#000; color:white; border:1px solid #333;">
                        </div>
                        <div style="display:flex; justify-content:space-between;">
                            <label style="color:#888; font-size:0.8rem;">Niños -20% (Bs <span id="lblPrecioNino"></span>):</label>
                            <input type="number" id="cantNino" value="0" min="0" onchange="validarCantidades()" style="width:55px; text-align:center; background:#000; color:white; border:1px solid #333;">
                        </div>
                    </div>

                    <div style="text-align:right; margin-bottom:25px;">
                        <span style="color:#888; font-size:0.8rem;">Total a Pagar:</span>
                        <strong style="color:var(--primary); font-size:2.5rem; display:block;">Bs <span id="precioTotal">0.00</span></strong>
                    </div>

                    <h4 style="color:#888; font-size:0.75rem; text-transform:uppercase; margin-bottom:10px;">Método de Pago</h4>
                    <select id="metodoPago" style="width:100%; padding:12px; background:#111; color:white; border:1px solid #333; border-radius:8px; margin-bottom:15px;" onchange="cambiarMetodoPago()">
                        <option value="tarjeta">💳 Tarjeta de Crédito / Débito</option>
                        <option value="qr">📱 Pago con QR Simple</option>
                    </select>

                    <div id="formTarjeta">
                        <input type="text" placeholder="Número de Tarjeta" style="width:100%; padding:12px; background:#111; border:1px solid #333; color:white; border-radius:8px; margin-bottom:10px;">
                        <div style="display:flex; gap:10px;"><input type="text" placeholder="MM/AA" style="flex:1; padding:12px; background:#111; border:1px solid #333; color:white; border-radius:8px;"><input type="password" placeholder="CVV" style="flex:1; padding:12px; background:#111; border:1px solid #333; color:white; border-radius:8px;"></div>
                    </div>
                    <div id="formQR" style="display:none; text-align:center;"><img src="img/mi_qr.jpeg" style="width:150px; border:4px solid white; border-radius:10px;"></div>

                    <button class="btn-pay" id="btnConfirmarCompra" onclick="procesarPago()" style="margin-top:20px;" disabled>SELECCIONA ASIENTOS</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        let funcionesGlobales = [];
        let fechaSeleccionada = new Date().toISOString().split('T')[0];
        let funcionSeleccionadaId = null;
        let asientosSeleccionados = [];
        let precioActual = 0;
        let puntosDisp = {{ auth()->user()->puntos ?? 0 }};
        
        // --- NUEVO: Tarifas Dinámicas ---
        let tarifas = { nino: 0, miercoles: 0, socio: 0 };

        document.addEventListener('DOMContentLoaded', () => {
            generarCalendario();
            cargarCartelera();
            cargarTarifas(); // Cargar los descuentos del JSON
        });

        function cargarTarifas() {
            fetch('/api/config/tarifas')
            .then(res => res.json())
            .then(data => {
                tarifas = data;
                console.log("Tarifas cargadas:", tarifas);
            })
            .catch(err => console.error("Error cargando tarifas:", err));
        }

        function generarCalendario() {
            const container = document.getElementById('dateScroller');
            const dias = ['DOM','LUN','MAR','MIE','JUE','VIE','SAB'];
            const hoy = new Date();
            for(let i=0; i<7; i++){
                const d = new Date(hoy); d.setDate(hoy.getDate() + i);
                const fStr = d.toISOString().split('T')[0];
                const btn = document.createElement('button');
                btn.className = `date-btn ${i===0 ? 'active' : ''}`;
                btn.innerHTML = `<span class="d-day">${i===0 ? 'HOY' : dias[d.getDay()]}</span><span class="d-date">${d.getDate()}</span>`;
                btn.onclick = () => {
                    document.querySelectorAll('.date-btn').forEach(b => b.classList.remove('active'));
                    btn.classList.add('active'); fechaSeleccionada = fStr; renderizarPeliculas();
                };
                container.appendChild(btn);
            }
        }

        function cargarCartelera() {
            fetch('/api/cartelera').then(r => r.json()).then(data => {
                funcionesGlobales = data; renderizarPeliculas();
            });
        }

        function renderizarPeliculas() {
            const container = document.getElementById('moviesContainer');
            container.innerHTML = '';
            const filtradas = funcionesGlobales.filter(f => f.fecha === fechaSeleccionada);
            const agrupadas = {};
            filtradas.forEach(f => {
                if(!agrupadas[f.titulo]) agrupadas[f.titulo] = { info: f, horarios: [] };
                agrupadas[f.titulo].horarios.push(f);
            });

            for(const t in agrupadas) {
                const p = agrupadas[t];
                let hrsHtml = p.horarios.map(h => `<button class="btn-time" onclick="abrirMapaAsientos(${h.id})">${h.hora.substring(0,5)}</button>`).join(' ');
                container.innerHTML += `
                    <div class="movie-card">
                        <img src="${p.info.imagen}" class="m-poster" onerror="this.src='img/fondo.jpg'">
                        <div class="m-info">
                            <button class="btn-info-action" onclick="abrirInfo('${t.replace(/'/g, "\\'")}')">VER INFORMACIÓN</button>
                            <div class="m-title">${t}</div>
                            <div style="display:flex; gap:10px; flex-wrap:wrap;">${hrsHtml}</div>
                        </div>
                    </div>`;
            }
        }

        function abrirInfo(titulo) {
            const f = funcionesGlobales.find(x => x.titulo === titulo);
            document.getElementById('infoTitulo').textContent = f.titulo;
            document.getElementById('infoSinopsis').textContent = f.sinopsis || "Sin sinopsis disponible.";
            document.getElementById('infoDuracion').textContent = f.duracion || "0";
            document.getElementById('infoGenero').textContent = f.genero || "Acción";
            document.getElementById('infoClasif').textContent = f.clasificacion || "B";
            document.getElementById('infoModal').classList.add('active');
        }

        function abrirMapaAsientos(id) {
            const f = funcionesGlobales.find(x => x.id === id);
            funcionSeleccionadaId = id; 
            
            // --- CALCULO DE DESCUENTO POR DÍA (MIÉRCOLES) ---
            // Usamos la fecha de la función para mayor precisión
            const fechaObj = new Date(f.fecha + 'T00:00:00'); 
            const esMiercoles = fechaObj.getDay() === 3; 
            
            let basePrice = parseFloat(f.precio);
            if (esMiercoles && tarifas.miercoles > 0) {
                basePrice = basePrice * (1 - (tarifas.miercoles / 100));
            }
            
            precioActual = basePrice;
            asientosSeleccionados = [];
            
            document.getElementById('modalMovieTitle').textContent = f.titulo;
            const extraInfo = esMiercoles ? ' <span style="color:#2ed573;">(PROMO MIÉRCOLES APLICADA)</span>' : '';
            document.getElementById('modalRoomInfo').innerHTML = `${f.sala} - Tarifa: Bs ${precioActual.toFixed(2)} ${extraInfo}`;
            
            document.getElementById('lblPrecioAdulto').textContent = precioActual.toFixed(2);
            
            // Aplicamos descuento de niño desde las tarifas
            const factorNino = 1 - (tarifas.nino / 100);
            document.getElementById('lblPrecioNino').textContent = (precioActual * factorNino).toFixed(2);

            const container = document.getElementById('seatContainer');
            container.innerHTML = '';
            const ocupados = f.asientos_vendidos ? f.asientos_vendidos.split(',') : [];

            for(let i=0; i<f.filas; i++){
                const letra = String.fromCharCode(65 + i);
                const row = document.createElement('div'); row.className = 'seat-row';
                row.innerHTML = `<div class="row-letter">${letra}</div>`;
                for(let j=1; j<=f.columnas; j++){
                    const sId = letra + j; const isOcu = ocupados.includes(sId);
                    const seat = document.createElement('div');
                    seat.className = `seat-icon ${isOcu ? 'occupied' : ''}`;
                    seat.textContent = j;
                    if(!isOcu) seat.onclick = () => {
                        if(asientosSeleccionados.includes(sId)) {
                            asientosSeleccionados = asientosSeleccionados.filter(x => x !== sId);
                            seat.classList.remove('selected');
                        } else {
                            asientosSeleccionados.push(sId); seat.classList.add('selected');
                        }
                        document.getElementById('cantAdulto').value = asientosSeleccionados.length;
                        document.getElementById('cantNino').value = 0;
                        actualizarResumen();
                    };
                    row.appendChild(seat);
                }
                container.appendChild(row);
            }
            actualizarResumen();
            document.getElementById('compraModal').classList.add('active');
        }

        function validarCantidades() {
            let a = parseInt(document.getElementById('cantAdulto').value) || 0;
            let n = parseInt(document.getElementById('cantNino').value) || 0;
            if(a + n !== asientosSeleccionados.length) {
                a = asientosSeleccionados.length - n; if(a < 0){ a=0; n=asientosSeleccionados.length; }
            }
            document.getElementById('cantAdulto').value = a;
            document.getElementById('cantNino').value = n;
            actualizarResumen();
        }

        function actualizarResumen() {
            let a = parseInt(document.getElementById('cantAdulto').value) || 0;
            let n = parseInt(document.getElementById('cantNino').value) || 0;
            
            const factorNino = 1 - (tarifas.nino / 100);
            let subtotal = (a * precioActual) + (n * precioActual * factorNino);
            
            let canje = parseInt(document.getElementById('inputPuntosCliente').value) || 0;
            
            if(canje > puntosDisp) canje = puntosDisp;
            if(canje > subtotal) canje = Math.floor(subtotal);
            document.getElementById('inputPuntosCliente').value = canje;

            const total = subtotal - canje;
            document.getElementById('precioTotal').textContent = total.toFixed(2);
            document.getElementById('btnConfirmarCompra').disabled = asientosSeleccionados.length === 0;
            document.getElementById('btnConfirmarCompra').textContent = `PAGAR Bs ${total.toFixed(2)}`;
        }

        function cambiarMetodoPago() {
            const m = document.getElementById('metodoPago').value;
            document.getElementById('formTarjeta').style.display = m === 'tarjeta' ? 'block' : 'none';
            document.getElementById('formQR').style.display = m === 'qr' ? 'block' : 'none';
        }


        function procesarPago() {
            const fd = new FormData();
            fd.append('idFuncion', funcionSeleccionadaId);
            fd.append('asientos', JSON.stringify(asientosSeleccionados));
            fd.append('ciCliente', "{{ auth()->user()->CI ?? '0' }}");
            fd.append('total', document.getElementById('precioTotal').textContent);
            fd.append('puntosUsados', document.getElementById('inputPuntosCliente').value);

            document.getElementById('btnConfirmarCompra').textContent = "PROCESANDO...";
            document.getElementById('btnConfirmarCompra').disabled = true;

            fetch('/api/venta/procesar', { method: 'POST', body: fd })
            .then(res => res.json()).then(data => {
                if(data.status === 'success') {
                    alert("✅ ¡Pago exitoso! Código: " + data.codigo);
                    window.location.href = '/historial';
                } else {
                    alert("Error: " + data.message);
                    document.getElementById('btnConfirmarCompra').disabled = false;
                    document.getElementById('btnConfirmarCompra').textContent = "PAGAR Bs " + document.getElementById('precioTotal').textContent;
                }
            })
            .catch(error => {
                console.error("Error en el pago:", error);
                alert("Ocurrió un error al procesar el pago. Intente de nuevo.");
                document.getElementById('btnConfirmarCompra').disabled = false;
                document.getElementById('btnConfirmarCompra').textContent = "PAGAR Bs " + document.getElementById('precioTotal').textContent;
            });
        }
    </script>
</body>
</html>