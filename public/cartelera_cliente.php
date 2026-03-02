<?php
session_start();
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'cliente') {
    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cartelera - Multicine La Paz</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;800;900&display=swap');
        :root { --primary: #00d2d3; --dark-bg: #050505; --card-bg: #111; --danger: #ff4757; }
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Montserrat', sans-serif; }
        
        body { background: var(--dark-bg); color: white; padding-bottom: 50px; }

        /* HEADER Y HERO */
        header { padding: 20px 50px; border-bottom: 1px solid #222; display: flex; justify-content: space-between; align-items: center; background: rgba(10,10,10,0.95); position: sticky; top: 0; z-index: 100; backdrop-filter: blur(10px);}
        .logo { color: var(--primary); font-weight: 900; font-size: 1.5rem; text-decoration: none; display: flex; align-items: center; gap: 10px;}
        .user-menu { display: flex; align-items: center; gap: 20px; }
        .user-name { font-weight: 600; color: #aaa; }
        .btn-logout { background: transparent; color: var(--danger); border: 1px solid var(--danger); padding: 8px 20px; border-radius: 20px; text-decoration: none; font-weight: bold; transition: 0.3s; font-size: 0.8rem;}
        .btn-logout:hover { background: var(--danger); color: white; }

        .hero { padding: 40px 50px 20px; text-align: center; }
        .hero h1 { font-size: 2.5rem; font-weight: 900; text-transform: uppercase; margin-bottom: 10px; }
        .hero p { color: #888; margin-bottom: 30px; }

        /* CALENDARIO */
        .date-scroller { display: flex; gap: 15px; overflow-x: auto; padding: 10px 50px 20px; justify-content: center; }
        .date-btn { background: #111; border: 1px solid #333; padding: 15px 25px; border-radius: 12px; cursor: pointer; transition: 0.3s; text-align: center; min-width: 100px;}
        .date-btn span { display: block; }
        .date-btn .d-day { font-size: 0.7rem; color: #888; font-weight: bold; margin-bottom: 5px; text-transform: uppercase;}
        .date-btn .d-date { font-size: 1.2rem; color: white; font-weight: 900; }
        .date-btn:hover { background: #222; border-color: #555; }
        .date-btn.active { background: var(--primary); border-color: var(--primary); box-shadow: 0 5px 20px rgba(0, 210, 211, 0.3);}
        .date-btn.active .d-day, .date-btn.active .d-date { color: black; }

        /* GRID DE PELÍCULAS */
        .movies-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 40px; padding: 20px 50px; max-width: 1400px; margin: 0 auto; }
        .movie-card { background: var(--card-bg); border-radius: 20px; overflow: hidden; border: 1px solid #222; transition: 0.3s; display: flex; flex-direction: column;}
        .movie-card:hover { transform: translateY(-10px); box-shadow: 0 15px 40px rgba(0,0,0,0.5); border-color: #444; }
        .m-poster { width: 100%; height: 400px; object-fit: cover; border-bottom: 3px solid var(--primary); }
        .m-info { padding: 20px; flex: 1; display: flex; flex-direction: column;}
        .m-title { font-size: 1.4rem; font-weight: 900; text-transform: uppercase; margin-bottom: 15px; line-height: 1.2;}
        
        .functions-area { margin-top: auto; }
        .f-label { font-size: 0.7rem; color: #888; font-weight: bold; margin-bottom: 10px; display: block; text-transform: uppercase;}
        .f-buttons { display: flex; flex-wrap: wrap; gap: 10px; }
        
        .btn-time { background: #1a1a1a; border: 1px solid #333; color: white; padding: 10px 15px; border-radius: 8px; cursor: pointer; font-weight: bold; transition: 0.3s; display: flex; flex-direction: column; align-items: center; justify-content: center; min-width: 80px;}
        .btn-time .t-hora { font-size: 1.1rem; color: var(--primary); }
        .btn-time .t-sala { font-size: 0.65rem; color: #aaa; margin-top: 3px;}
        .btn-time:hover { background: var(--primary); border-color: var(--primary); }
        .btn-time:hover .t-hora, .btn-time:hover .t-sala { color: black; }
        .btn-time.agotado { background: #111; border-color: #222; cursor: not-allowed; opacity: 0.5; }

        /* MODAL Y MAPA DE ASIENTOS (US-03) */
        .modal-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.9); z-index: 500; display: none; align-items: center; justify-content: center; backdrop-filter: blur(5px);}
        .modal-overlay.active { display: flex; }
        
        .modal-content { width: 95%; max-width: 1000px; background: #121212; border-radius: 20px; border: 1px solid #333; display: flex; flex-direction: column; overflow: hidden; box-shadow: 0 20px 60px rgba(0,0,0,0.8);}
        .modal-header { padding: 20px 30px; background: #1a1a1a; border-bottom: 1px solid #333; display: flex; justify-content: space-between; align-items: center; }
        
        .modal-body-split { display: flex; flex-wrap: wrap; }
        .map-section { flex: 2; padding: 30px; border-right: 1px solid #222; min-width: 300px; overflow-x: auto; display: flex; flex-direction: column; align-items: center;}
        .pay-section { flex: 1; padding: 30px; background: #0a0a0a; min-width: 300px; }
        
        /* DIBUJO DE ASIENTOS */
        .screen-display { width: 80%; height: 40px; background: linear-gradient(180deg, #fff, transparent); margin: 0 auto 30px auto; border-radius: 50% 50% 0 0 / 20px 20px 0 0; text-align: center; color: black; font-weight: bold; line-height: 25px; letter-spacing: 5px; transform: perspective(600px) rotateX(-30deg); opacity: 0.8; }
        .seat-row { display: flex; gap: 6px; align-items: center; justify-content: center; margin-bottom: 8px;}
        .row-letter { color: var(--primary); font-weight: bold; width: 20px; text-align: right; margin-right: 10px; font-size: 0.8rem;}
        
        .seat-icon { width: 25px; height: 25px; border-radius: 5px 5px 8px 8px; background-color: #333; cursor: pointer; transition: 0.2s; position: relative; color: white; font-size: 0.6rem; display: flex; align-items: center; justify-content: center; font-weight: bold;}
        .seat-icon:hover { background-color: #555; transform: translateY(-3px); }
        .seat-icon::after { content:''; position: absolute; bottom: -2px; left: 2px; right: 2px; height: 3px; background: #222; border-radius: 2px; }
        
        .seat-icon.selected { background-color: var(--primary) !important; color: black; transform: translateY(-5px); box-shadow: 0 0 10px var(--primary); }
        .seat-icon.occupied { background-color: var(--danger) !important; color: transparent; cursor: not-allowed; opacity: 0.3;}
        
        /* FORMULARIO DE PAGO (US-04) */
        .form-input { width: 100%; padding: 12px; background: #111; border: 1px solid #333; color: white; border-radius: 8px; font-family: inherit; margin-bottom: 15px;}
        .form-input:focus { border-color: var(--primary); outline: none; }
        .btn-pay { width: 100%; padding: 15px; background: var(--primary); color: black; border: none; border-radius: 8px; font-weight: 900; cursor: pointer; transition:0.3s; font-size: 1rem;}
        .btn-pay:hover { background: white; }
        .btn-pay:disabled { background: #333; color: #666; cursor: not-allowed; }
        .btn-historial { background: var(--primary); color: black; padding: 8px 20px; border-radius: 20px; text-decoration: none; font-weight: bold; transition: 0.3s; font-size: 0.8rem; border: 1px solid var(--primary);}
        .btn-historial:hover { background: transparent; color: var(--primary); }
    </style>
</head>
<body>

    <header>
        <header>
        <div class="logo"><i class="fas fa-ticket-alt"></i> MULTICINE</div>
        <div class="user-menu">
            <span class="user-name">Hola, <?php echo $_SESSION['nombre']; ?></span>
            
            <a href="historial_cliente.php" class="btn-historial"><i class="fas fa-qrcode"></i> MIS ENTRADAS</a>
            
            <a href="api.php?action=logout" class="btn-logout">SALIR</a>
        </div>
    </header>
    </header>

    <div class="hero">
        <h1>EN CARTELERA</h1>
        <p>Selecciona una fecha para ver los horarios disponibles</p>
    </div>

    <div class="date-scroller" id="dateScroller"></div>

    <div class="movies-grid" id="moviesContainer"></div>

    <div class="modal-overlay" id="compraModal">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <h3 id="modalMovieTitle" style="color:white; text-transform:uppercase; margin:0;">Película</h3>
                    <small style="color:var(--primary);" id="modalRoomInfo">Sala - Bs 0.00</small>
                </div>
                <button onclick="document.getElementById('compraModal').classList.remove('active')" style="background:none; border:none; color:#666; font-size:2rem; cursor:pointer;">&times;</button>
            </div>
            
            <div class="modal-body-split">
                <div class="map-section">
                    <div class="screen-display">PANTALLA</div>
                    <div id="seatContainer" style="width: 100%;"></div>
                    
                    <div style="display:flex; gap:15px; margin-top:30px; font-size:0.8rem; color:#888; font-weight:bold;">
                        <div style="display:flex; align-items:center; gap:5px;"><div style="width:15px;height:15px;background:#333;border-radius:3px;"></div> Libre</div>
                        <div style="display:flex; align-items:center; gap:5px;"><div style="width:15px;height:15px;background:var(--primary);border-radius:3px;"></div> Tu Selección</div>
                        <div style="display:flex; align-items:center; gap:5px;"><div style="width:15px;height:15px;background:var(--danger);opacity:0.3;border-radius:3px;"></div> Ocupado</div>
                    </div>
                </div>

                <div class="pay-section">
                    <h4 style="color:white; margin-bottom: 20px; border-bottom: 1px solid #333; padding-bottom: 10px;">RESUMEN DE COMPRA</h4>
                    <div style="margin-bottom: 10px;">
                        <span style="color:#888; font-size:0.8rem; display:block;">Asientos:</span>
                        <strong id="asientosElegidos" style="color:white; font-size:1rem;">Ninguno</strong>
                    </div>
                    <div style="margin-bottom: 25px;">
                        <span style="color:#888; font-size:0.8rem; display:block;">Total a Pagar:</span>
                        <strong style="color:var(--primary); font-size:2rem;">Bs <span id="precioTotal">0.00</span></strong>
                    </div>

                    <h4 style="color:#888; font-size:0.8rem; margin-bottom:10px;">MÉTODO DE PAGO</h4>
                    <select id="metodoPago" class="form-input" onchange="cambiarMetodoPago()">
                        <option value="tarjeta">💳 Tarjeta de Crédito / Débito</option>
                        <option value="qr">📱 Pago con QR Simple</option>
                    </select>

                    <div id="formTarjeta">
                        <input type="text" placeholder="Número de Tarjeta (Ej. 4500 1234 ...)" class="form-input" maxlength="16" id="cc_num">
                        <div style="display:flex; gap:10px;">
                            <input type="text" placeholder="MM/AA" class="form-input" maxlength="5" id="cc_exp">
                            <input type="password" placeholder="CVV" class="form-input" maxlength="3" id="cc_cvv">
                        </div>
                    </div>

                    <div id="formQR" style="display:none; text-align:center; padding: 15px 0;">
                        <div style="background: white; padding: 10px; border-radius: 10px; display: inline-block; margin-bottom: 15px; box-shadow: 0 10px 20px rgba(0,0,0,0.5);">
                            <img src="img/mi_qr.jpeg" alt="QR de Pago" style="width: 180px; height: 180px; object-fit: contain;">
                        </div>
                        <p style="font-size:0.8rem; color:#888; font-weight:bold;">1. Escanea el código desde tu App Bancaria.<br>2. Realiza el pago exacto.<br>3. Haz clic en "Confirmar".</p>
                    </div>

                    <button class="btn-pay" id="btnConfirmarCompra" onclick="procesarPago()" disabled>SELECCIONA ASIENTOS</button>
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

        document.addEventListener('DOMContentLoaded', () => {
            generarCalendario();
            cargarCartelera();
        });

        // --- 1. CARGA DE CARTELERA ---
        function generarCalendario() {
            const container = document.getElementById('dateScroller');
            const diasSemana = ['Dom', 'Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb'];
            const hoy = new Date();
            for(let i = 0; i < 7; i++) {
                const d = new Date(hoy); d.setDate(hoy.getDate() + i);
                const fechaStr = d.toISOString().split('T')[0];
                
                const btn = document.createElement('button');
                btn.className = `date-btn ${i === 0 ? 'active' : ''}`;
                btn.innerHTML = `<span class="d-day">${i === 0 ? 'HOY' : diasSemana[d.getDay()]}</span><span class="d-date">${d.getDate()}</span>`;
                btn.onclick = () => {
                    document.querySelectorAll('.date-btn').forEach(b => b.classList.remove('active'));
                    btn.classList.add('active');
                    fechaSeleccionada = fechaStr;
                    renderizarPeliculas();
                };
                container.appendChild(btn);
            }
        }

        function cargarCartelera() {
            fetch('api.php?action=cartelera_cajero')
            .then(res => res.json())
            .then(data => { funcionesGlobales = data; renderizarPeliculas(); });
        }

        function renderizarPeliculas() {
            const container = document.getElementById('moviesContainer');
            container.innerHTML = '';
            const funcionesDelDia = funcionesGlobales.filter(f => f.fecha === fechaSeleccionada);
            
            if(funcionesDelDia.length === 0) {
                container.innerHTML = '<h3 style="text-align:center; grid-column:1/-1; color:#666; margin-top:50px;">No hay funciones programadas para hoy.</h3>'; return;
            }

            const peliculasAgrupadas = {};
            funcionesDelDia.forEach(f => {
                if (!peliculasAgrupadas[f.titulo]) peliculasAgrupadas[f.titulo] = { titulo: f.titulo, imagen: f.imagen, funciones: [] };
                peliculasAgrupadas[f.titulo].funciones.push(f);
            });

            for (const titulo in peliculasAgrupadas) {
                const peli = peliculasAgrupadas[titulo];
                let botonesHtml = '';
                peli.funciones.forEach(func => {
                    if(func.llena) botonesHtml += `<button class="btn-time agotado"><span class="t-hora">${func.hora.substring(0,5)}</span><span class="t-sala">${func.sala}</span></button>`;
                    else botonesHtml += `<button class="btn-time" onclick="abrirMapaAsientos(${func.id})"><span class="t-hora">${func.hora.substring(0,5)}</span><span class="t-sala">${func.sala}</span></button>`;
                });

                container.innerHTML += `
                    <div class="movie-card">
                        <img src="${peli.imagen}" class="m-poster" onerror="this.src='img/fondo.jpg'">
                        <div class="m-info">
                            <div class="m-title">${peli.titulo}</div>
                            <div class="functions-area"><span class="f-label">Horarios Disponibles:</span><div class="f-buttons">${botonesHtml}</div></div>
                        </div>
                    </div>`;
            }
        }

        // --- 2. MAPA DE ASIENTOS (US-03) ---
        function abrirMapaAsientos(idFuncion) {
            const func = funcionesGlobales.find(f => f.id === idFuncion);
            funcionSeleccionadaId = func.id;
            precioActual = parseFloat(func.precio);
            asientosSeleccionados = [];
            
            document.getElementById('modalMovieTitle').textContent = func.titulo;
            document.getElementById('modalRoomInfo').textContent = `${func.sala} - Bs ${precioActual.toFixed(2)}`;
            
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
                    if (j === Math.floor(columnas / 2)) seat.style.marginRight = '20px'; // Pasillo
                    
                    rowDiv.appendChild(seat);
                }
                container.appendChild(rowDiv);
            }
            
            actualizarResumen();
            document.getElementById('compraModal').classList.add('active');
        }

        function toggleSeat(element, seatId) {
            if (asientosSeleccionados.includes(seatId)) {
                asientosSeleccionados = asientosSeleccionados.filter(id => id !== seatId);
                element.classList.remove('selected');
            } else {
                asientosSeleccionados.push(seatId);
                element.classList.add('selected');
            }
            actualizarResumen();
        }

        // --- 3. PAGO Y CHECKOUT (US-04) ---
        function actualizarResumen() {
            document.getElementById('asientosElegidos').textContent = asientosSeleccionados.length > 0 ? asientosSeleccionados.join(', ') : 'Ninguno';
            document.getElementById('precioTotal').textContent = (asientosSeleccionados.length * precioActual).toFixed(2);
            
            const btn = document.getElementById('btnConfirmarCompra');
            if (asientosSeleccionados.length === 0) {
                btn.disabled = true;
                btn.textContent = "SELECCIONA ASIENTOS";
            } else {
                btn.disabled = false;
                btn.textContent = `PAGAR Bs ${(asientosSeleccionados.length * precioActual).toFixed(2)}`;
            }
        }

        function cambiarMetodoPago() {
            const metodo = document.getElementById('metodoPago').value;
            document.getElementById('formTarjeta').style.display = metodo === 'tarjeta' ? 'block' : 'none';
            document.getElementById('formQR').style.display = metodo === 'qr' ? 'block' : 'none';
        }

        function procesarPago() {
            if (document.getElementById('metodoPago').value === 'tarjeta') {
                const num = document.getElementById('cc_num').value;
                const cvv = document.getElementById('cc_cvv').value;
                if(num.length < 15 || cvv.length < 3) {
                    alert("⚠️ Datos de tarjeta inválidos."); return;
                }
            }

            const formData = new FormData();
            formData.append('action', 'comprar_cliente'); 
            formData.append('idFuncion', funcionSeleccionadaId);
            formData.append('asientos', JSON.stringify(asientosSeleccionados));
            formData.append('total', (asientosSeleccionados.length * precioActual).toFixed(2));

            document.getElementById('btnConfirmarCompra').textContent = "PROCESANDO...";
            
            fetch('api.php', { method: 'POST', body: formData })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    document.getElementById('compraModal').classList.remove('active');
                    alert(`✅ ¡PAGO EXITOSO!\nTu código de ticket es: ${data.codigo}`);
                    window.location.href = 'historial_cliente.php'; // Redirigir al historial
                } else {
                    alert('❌ Error: ' + data.message);
                    document.getElementById('btnConfirmarCompra').textContent = "REINTENTAR PAGO";
                }
            });
        }
    </script>
</body>
</html>