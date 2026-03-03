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

        .qr-placeholder {
            background: white;
            width: 60px;
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 5px;
            color: black;
            font-size: 2rem;
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
                        container.innerHTML += `
                        <div class="ticket-card">
                            <img src="${ticket.imagenPoster}" class="ticket-poster" onerror="this.src='img/mi_qr.jpeg'">
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
                                    <div class="qr-placeholder"><i class="fas fa-qrcode"></i></div>
                                </div>
                                <div style="font-size:0.6rem; color:#666; margin-top:10px;">Comprado el: ${ticket.fecha_compra}</div>
                            </div>
                        </div>
                    `;
                    });
                });
        });
    </script>
</body>

</html>