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
    <title>Mi Historial - Multicine</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;800;900&display=swap');

        :root {
            --primary: #00d2d3;
            --dark-bg: #050505;
            --card-bg: #111;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Montserrat', sans-serif;
        }

        body {
            background: var(--dark-bg);
            color: white;
            padding-bottom: 50px;
        }

        header {
            padding: 20px 50px;
            border-bottom: 1px solid #222;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: rgba(10, 10, 10, 0.95);
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .logo {
            color: var(--primary);
            font-weight: 900;
            font-size: 1.5rem;
            text-decoration: none;
        }

        .back-btn {
            background: transparent;
            color: white;
            border: 1px solid #555;
            padding: 8px 20px;
            border-radius: 20px;
            text-decoration: none;
            font-weight: bold;
            transition: 0.3s;
            font-size: 0.8rem;
        }

        .back-btn:hover {
            background: white;
            color: black;
        }

        .container {
            max-width: 1000px;
            margin: 40px auto;
            padding: 0 20px;
        }

        h1 {
            font-weight: 900;
            font-size: 2.5rem;
            text-transform: uppercase;
            margin-bottom: 30px;
            text-align: center;
        }

        .ticket-list {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .ticket-card {
            background: var(--card-bg);
            border-radius: 15px;
            border: 1px solid #222;
            display: flex;
            overflow: hidden;
            position: relative;
            transition: border-color 0.3s;
        }

        .ticket-card:hover {
            border-color: #444;
        }

        .ticket-poster {
            width: 120px;
            object-fit: cover;
        }

        .ticket-info {
            padding: 20px;
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .t-title {
            font-size: 1.2rem;
            font-weight: 900;
            text-transform: uppercase;
            color: var(--primary);
            margin-bottom: 5px;
        }

        .t-details {
            font-size: 0.8rem;
            color: #aaa;
            margin-bottom: 15px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
        }

        .code-box {
            background: #1a1a1a;
            padding: 15px;
            border-radius: 10px;
            border-left: 3px solid var(--primary);
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 15px;
        }

        .code-box span {
            font-size: 0.7rem;
            color: #888;
            text-transform: uppercase;
            font-weight: bold;
            display: block;
        }

        .code-box strong {
            font-size: 1.5rem;
            color: white;
            letter-spacing: 2px;
        }

        /* Botón QR */
        .btn-ver-qr {
            background: var(--primary);
            color: black;
            border: none;
            padding: 10px 18px;
            border-radius: 8px;
            font-weight: 900;
            font-size: 0.8rem;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: 0.2s;
            white-space: nowrap;
            font-family: 'Montserrat', sans-serif;
        }

        .btn-ver-qr:hover {
            background: white;
        }

        /* Modal QR */
        .qr-modal-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.92);
            z-index: 999;
            display: none;
            align-items: center;
            justify-content: center;
            backdrop-filter: blur(8px);
        }

        .qr-modal-overlay.active {
            display: flex;
        }

        .qr-modal-box {
            background: #121212;
            border: 1px solid var(--primary);
            border-radius: 20px;
            padding: 40px 35px;
            text-align: center;
            max-width: 380px;
            width: 90%;
            position: relative;
            box-shadow: 0 0 60px rgba(0, 210, 211, 0.15);
        }

        .qr-modal-box h3 {
            font-size: 1.1rem;
            text-transform: uppercase;
            color: var(--primary);
            margin-bottom: 5px;
            font-weight: 900;
        }

        .qr-modal-box .qr-movie {
            font-size: 0.8rem;
            color: #888;
            margin-bottom: 25px;
        }

        .qr-img-wrapper {
            background: white;
            padding: 15px;
            border-radius: 12px;
            display: inline-block;
            margin-bottom: 20px;
        }

        .qr-img-wrapper img {
            display: block;
            width: 200px;
            height: 200px;
        }

        .qr-code-text {
            font-size: 0.75rem;
            color: #666;
            letter-spacing: 1px;
            word-break: break-all;
            margin-bottom: 5px;
        }

        .qr-ticket-code {
            font-size: 1.4rem;
            font-weight: 900;
            color: white;
            letter-spacing: 3px;
            margin-bottom: 25px;
        }

        .btn-cerrar-qr {
            background: transparent;
            border: 1px solid #444;
            color: #aaa;
            padding: 10px 30px;
            border-radius: 20px;
            cursor: pointer;
            font-family: 'Montserrat', sans-serif;
            font-weight: 700;
            font-size: 0.8rem;
            transition: 0.2s;
        }

        .btn-cerrar-qr:hover {
            background: #222;
            color: white;
        }

        .qr-close-x {
            position: absolute;
            top: 15px;
            right: 20px;
            background: none;
            border: none;
            color: #555;
            font-size: 1.5rem;
            cursor: pointer;
            line-height: 1;
            transition: color 0.2s;
        }

        .qr-close-x:hover {
            color: white;
        }
    </style>
</head>

<body>

    <header>
        <div class="logo"><i class="fas fa-ticket-alt"></i> MULTICINE</div>
        <a href="cartelera_cliente.php" class="back-btn"><i class="fas fa-arrow-left"></i> VOLVER A CARTELERA</a>
    </header>

    <div class="container">
        <h1>MIS ENTRADAS</h1>
        <div class="ticket-list" id="historialContainer"></div>
    </div>

    <!-- Modal QR -->
    <div class="qr-modal-overlay" id="qrModal">
        <div class="qr-modal-box">
            <button class="qr-close-x" onclick="cerrarQR()">&times;</button>
            <h3 id="qrModalTitulo">Tu Ticket</h3>
            <p class="qr-movie" id="qrModalPelicula"></p>

            <div class="qr-img-wrapper">
                <img id="qrImagen" src="" alt="Código QR">
            </div>

            <p class="qr-code-text">CÓDIGO DE RETIRO</p>
            <div class="qr-ticket-code" id="qrModalCodigo"></div>

            <button class="btn-cerrar-qr" onclick="cerrarQR()">CERRAR</button>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            fetch('api.php?action=historial_cliente')
                .then(res => res.json())
                .then(data => {
                    const container = document.getElementById('historialContainer');

                    if (data.length === 0) {
                        container.innerHTML = '<p style="text-align:center; color:#666; font-size:1.2rem; margin-top:50px;">Aún no tienes entradas compradas.</p>';
                        return;
                    }

                    data.forEach(ticket => {
                        const horaCorta = ticket.horaInicio.substring(0, 5);

                        // El codigoQR viene desde la tabla tickets via historial
                        // Si no viene (compras antiguas del cajero), usamos el codigo_ticket
                        const datosQR = ticket.codigoQR || ticket.codigo_ticket;
                        const urlQR = `https://api.qrserver.com/v1/create-qr-code/?data=${encodeURIComponent(datosQR)}&size=200x200&bgcolor=ffffff&color=000000&margin=10`;

                        const tituloEscape = ticket.titulo.replace(/'/g, "\\'");
                        const codigoEscape = ticket.codigo_ticket.replace(/'/g, "\\'");

                        container.innerHTML += `
                        <div class="ticket-card">
                            <img src="img/${ticket.imagenPoster}" class="ticket-poster" onerror="this.src='img/poster.jpeg'">
                            <div class="ticket-info">
                                <div class="t-title">${ticket.titulo}</div>
                                <div class="t-details">
                                    <div><b>SALA:</b> ${ticket.sala}</div>
                                    <div><b>ASIENTOS:</b> <span style="color:white;">${ticket.asientos}</span></div>
                                    <div><b>FECHA:</b> ${ticket.fechaFuncion} (${horaCorta})</div>
                                    <div><b>TOTAL:</b> Bs ${ticket.total}</div>
                                </div>
                                <div class="code-box">
                                    <div>
                                        <span>CÓDIGO DE RETIRO</span>
                                        <strong>${ticket.codigo_ticket}</strong>
                                    </div>
                                    <button class="btn-ver-qr" onclick="mostrarQR('${urlQR}', '${tituloEscape}', '${codigoEscape}')">
                                        <i class="fas fa-qrcode"></i> VER QR
                                    </button>
                                </div>
                                <div style="font-size:0.6rem; color:#666; margin-top:10px;">Comprado el: ${ticket.fecha_compra}</div>
                            </div>
                        </div>`;
                    });
                });
        });

        function mostrarQR(urlQR, titulo, codigo) {
            document.getElementById('qrImagen').src = urlQR;
            document.getElementById('qrModalTitulo').textContent = 'TU TICKET';
            document.getElementById('qrModalPelicula').textContent = titulo;
            document.getElementById('qrModalCodigo').textContent = codigo;
            document.getElementById('qrModal').classList.add('active');
        }

        function cerrarQR() {
            document.getElementById('qrModal').classList.remove('active');
            document.getElementById('qrImagen').src = ''; // limpia para no parpadear al reabrir
        }

        // Cerrar al hacer click fuera del box
        document.getElementById('qrModal').addEventListener('click', function(e) {
            if (e.target === this) cerrarQR();
        });
    </script>
</body>

</html>