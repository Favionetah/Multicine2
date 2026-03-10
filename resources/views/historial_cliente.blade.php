<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mi Historial - Multicine</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;800;900&display=swap');
        :root { --primary: #00d2d3; --dark-bg: #050505; --card-bg: #111; }
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Montserrat', sans-serif; }
        body { background: var(--dark-bg); color: white; padding-bottom: 50px; }
        header { padding: 20px 50px; border-bottom: 1px solid #222; display: flex; justify-content: space-between; align-items: center; background: rgba(10, 10, 10, 0.95); position: sticky; top: 0; z-index: 100; }
        .logo { color: var(--primary); font-weight: 900; font-size: 1.5rem; text-decoration: none; }
        .back-btn { background: transparent; color: white; border: 1px solid #555; padding: 8px 20px; border-radius: 20px; text-decoration: none; font-weight: bold; font-size: 0.8rem; transition: 0.3s; }
        .back-btn:hover { background: white; color: black; }
        .container { max-width: 1000px; margin: 40px auto; padding: 0 20px; }
        h1 { font-weight: 900; font-size: 2.5rem; text-transform: uppercase; margin-bottom: 30px; text-align: center; }
        .ticket-card { background: var(--card-bg); border-radius: 15px; border: 1px solid #222; display: flex; overflow: hidden; margin-bottom: 20px; transition: 0.3s; }
        .ticket-poster { width: 120px; object-fit: cover; }
        .ticket-info { padding: 20px; flex: 1; display: flex; flex-direction: column; justify-content: center; }
        .t-title { font-size: 1.2rem; font-weight: 900; text-transform: uppercase; color: var(--primary); margin-bottom: 5px; }
        .code-box { background: #1a1a1a; padding: 15px; border-radius: 10px; border-left: 3px solid var(--primary); display: flex; justify-content: space-between; align-items: center; }
        .qr-modal-overlay { position: fixed; inset: 0; background: rgba(0, 0, 0, 0.92); z-index: 999; display: none; align-items: center; justify-content: center; backdrop-filter: blur(8px); }
        .qr-modal-overlay.active { display: flex; }
        .qr-modal-box { background: #121212; border: 1px solid var(--primary); border-radius: 20px; padding: 40px; text-align: center; max-width: 380px; width: 90%; }
    </style>
</head>
<body>
    <header>
        <div class="logo">MULTICINE</div>
        <a href="/cartelera" class="back-btn"><i class="fas fa-arrow-left"></i> VOLVER A CARTELERA</a>
    </header>

    <div class="container">
        <h1>MIS ENTRADAS</h1>
        <div id="historialContainer"></div>
    </div>

    <div class="qr-modal-overlay" id="qrModal">
        <div class="qr-modal-box">
            <h3 style="color:var(--primary); font-weight:900; margin-bottom:5px;">TU TICKET</h3>
            <p id="qrModalPelicula" style="color:#888; font-size:0.8rem; margin-bottom:20px;"></p>
            <div style="background: white; padding: 15px; border-radius: 12px; display: inline-block; margin-bottom: 20px;">
                <img id="qrImagen" src="" style="width: 200px; height: 200px; display: block;">
            </div>
            <div id="qrModalCodigo" style="font-size:1.4rem; font-weight:900; color:white; letter-spacing:3px; margin-bottom:25px;"></div>
            <button onclick="document.getElementById('qrModal').classList.remove('active')" style="background:transparent; border:1px solid #444; color:#aaa; padding:10px 30px; border-radius:20px; cursor:pointer;">CERRAR</button>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Usamos la sesión de Laravel que configuramos en el paso anterior
            const ci = "{{ session('CI') }}"; 
            
            fetch(`/api/historial/${ci}`)
                .then(res => res.json())
                .then(data => {
                    const container = document.getElementById('historialContainer');
                    
                    // 1. SI HAY UN ERROR EN LA BASE DE DATOS, LO MOSTRAMOS EN PANTALLA
                    if (data.error) {
                        container.innerHTML = `
                        <div style="background: rgba(255, 71, 87, 0.1); border: 2px solid var(--danger); color: white; padding: 20px; border-radius: 10px;">
                            <h3 style="color: var(--danger); margin-bottom: 10px;"><i class="fas fa-exclamation-triangle"></i> Error en la Base de Datos</h3>
                            <p style="font-family: monospace; color: #ffcccc;">${data.error}</p>
                            <hr style="border-color: #555; margin: 15px 0;">
                            <p style="font-size: 0.8rem; color: #aaa;">Revisa tu <b>HistorialController.php</b> y asegúrate de que el nombre de la tabla (ej. 'ventas' o 'tickets') y sus columnas coincidan exactamente con tu phpMyAdmin.</p>
                        </div>`;
                        return;
                    }

                    // 2. SI NO TIENE ENTRADAS
                    if (!Array.isArray(data) || data.length === 0) {
                        container.innerHTML = '<p style="text-align:center; color:#666; font-size:1.2rem; margin-top:50px;">Aún no tienes entradas compradas.</p>';
                        return;
                    }

                    // 3. SI TODO ESTÁ BIEN, DIBUJAMOS LOS TICKETS
                    data.forEach(ticket => {
                        const urlQR = `https://api.qrserver.com/v1/create-qr-code/?data=${ticket.codigo_ticket}&size=200x200`;
                        container.innerHTML += `
                        <div class="ticket-card" style="background: var(--card-bg); border-radius: 15px; border: 1px solid #222; display: flex; overflow: hidden; margin-bottom: 20px;">
                            <img src="${ticket.imagenPoster}" style="width: 120px; object-fit: cover;" onerror="this.src='img/fondo.jpg'">
                            <div style="padding: 20px; flex: 1; display: flex; flex-direction: column; justify-content: center;">
                                <div style="font-size: 1.2rem; font-weight: 900; text-transform: uppercase; color: var(--primary); margin-bottom: 5px;">${ticket.titulo}</div>
                                <div style="font-size:0.8rem; color:#aaa; margin-bottom:10px;">
                                    SALA: ${ticket.sala} | ASIENTOS: ${ticket.asientos}<br>
                                    FECHA: ${ticket.fechaFuncion} (${ticket.horaInicio.substring(0,5)})
                                </div>
                                <div style="background: #1a1a1a; padding: 15px; border-radius: 10px; border-left: 3px solid var(--primary); display: flex; justify-content: space-between; align-items: center;">
                                    <strong style="font-size:1.2rem; letter-spacing:2px; color:white;">${ticket.codigo_ticket}</strong>
                                    <button onclick="mostrarQR('${urlQR}', '${ticket.titulo.replace(/'/g, "\\'")}', '${ticket.codigo_ticket}')" style="background:var(--primary); color:black; border:none; padding:8px 15px; border-radius:5px; font-weight:bold; cursor:pointer;">VER QR</button>
                                </div>
                            </div>
                        </div>`;
                    });
                })
                .catch(err => {
                    console.error(err);
                    document.getElementById('historialContainer').innerHTML = '<p style="color:red; text-align:center;">Error de conexión con el servidor.</p>';
                });
        });

        function mostrarQR(url, titulo, codigo) {
            document.getElementById('qrImagen').src = url;
            document.getElementById('qrModalPelicula').textContent = titulo;
            document.getElementById('qrModalCodigo').textContent = codigo;
            document.getElementById('qrModal').classList.add('active');
        }
    </script>
</body>
</html>