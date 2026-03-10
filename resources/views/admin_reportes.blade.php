<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reportes - Multicine Admin</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;800;900&display=swap');
        :root { --primary: #00d2d3; --dark-bg: #050505; --card-bg: #111; --success: #2ed573; }
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Montserrat', sans-serif; }
        body { background: var(--dark-bg); color: white; padding: 40px; }
        
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; border-bottom: 1px solid #333; padding-bottom: 20px;}
        .btn-back { color: var(--primary); text-decoration: none; font-weight: bold; }
        
        .filters-card { background: var(--card-bg); padding: 20px; border-radius: 15px; border: 1px solid #333; margin-bottom: 20px; display: flex; gap: 20px; align-items: flex-end; flex-wrap: wrap;}
        .form-group { display: flex; flex-direction: column; }
        label { color: #888; font-size: 0.8rem; margin-bottom: 5px; font-weight: bold; }
        input[type="date"] { background: #1a1a1a; color: white; border: 1px solid #333; padding: 10px; border-radius: 8px; outline: none; }
        input[type="date"]:focus { border-color: var(--primary); }
        
        .btn-action { background: var(--primary); color: black; border: none; padding: 10px 20px; border-radius: 8px; font-weight: bold; cursor: pointer; transition: 0.3s; }
        .btn-action:hover { background: white; }
        
        .btn-export { background: transparent; color: white; border: 1px solid #555; padding: 10px 15px; border-radius: 8px; cursor: pointer; transition: 0.3s; display: flex; align-items: center; gap: 8px; }
        .btn-export:hover { background: #333; }
        .btn-excel { color: #2ed573; border-color: #2ed573; }
        .btn-excel:hover { background: #2ed573; color: black; }
        .btn-pdf { color: #ff4757; border-color: #ff4757; }
        .btn-pdf:hover { background: #ff4757; color: white; }

        table { width: 100%; border-collapse: collapse; margin-top: 20px; background: var(--card-bg); border-radius: 10px; overflow: hidden; }
        th, td { padding: 15px; text-align: left; border-bottom: 1px solid #222; }
        th { background: #1a1a1a; color: var(--primary); font-size: 0.9rem; text-transform: uppercase; }
        td { font-size: 0.9rem; }
        
        .progress-bar { width: 100px; height: 8px; background: #333; border-radius: 4px; overflow: hidden; display: inline-block; vertical-align: middle; margin-right: 10px; }
        .progress-fill { height: 100%; background: var(--primary); }

        .summary-cards { display: flex; gap: 20px; margin-top: 20px; }
        .stat-card { flex: 1; background: #1a1a1a; padding: 20px; border-radius: 10px; border: 1px solid #333; text-align: center; }
        .stat-val { font-size: 2rem; font-weight: 900; color: var(--success); margin-top: 10px; }

        /* Estilos para Imprimir (PDF) */
        @media print {
            body { background: white; color: black; padding: 0; }
            .header, .filters-card, .btn-export, .btn-back { display: none !important; }
            table { border: 1px solid #ccc; }
            th { background: #eee !important; color: black !important; }
            th, td { border: 1px solid #ddd; padding: 8px; }
            .progress-bar { border: 1px solid #999; }
            .progress-fill { background: black !important; -webkit-print-color-adjust: exact; }
            .stat-card { border: 1px solid #ccc; background: transparent; }
        }
    </style>
</head>
<body>

    <div class="header">
        <a href="/admin" class="btn-back"><i class="fas fa-arrow-left"></i> VOLVER AL PANEL</a>
        <h2><i class="fas fa-chart-line"></i> REPORTES FINANCIEROS</h2>
        <span style="color:#666;">Admin: <b>{{ session('nombre', 'Administrador') }}</b></span>
    </div>

    <div class="filters-card">
        <div class="form-group">
            <label>Fecha Inicio</label>
            <input type="date" id="fechaInicio">
        </div>
        <div class="form-group">
            <label>Fecha Fin</label>
            <input type="date" id="fechaFin">
        </div>
        <button class="btn-action" onclick="cargarReporte()"><i class="fas fa-search"></i> GENERAR</button>
        
        <div style="margin-left: auto; display: flex; gap: 10px;">
            <button class="btn-export btn-excel" onclick="exportarExcel()"><i class="fas fa-file-excel"></i> EXCEL</button>
            <button class="btn-export btn-pdf" onclick="window.print()"><i class="fas fa-file-pdf"></i> PDF</button>
        </div>
    </div>

    <div class="summary-cards">
        <div class="stat-card">
            <div style="color:#888; font-size:0.8rem; font-weight:bold; text-transform:uppercase;">Recaudación Total</div>
            <div class="stat-val" id="resTotal">Bs 0.00</div>
        </div>
        <div class="stat-card">
            <div style="color:#888; font-size:0.8rem; font-weight:bold; text-transform:uppercase;">Boletos Vendidos</div>
            <div class="stat-val" id="resBoletos" style="color:var(--primary);">0</div>
        </div>
    </div>

    <table id="tablaReporte">
        <thead>
            <tr>
                <th>Fecha / Hora</th>
                <th>Película</th>
                <th>Sala</th>
                <th>Ocupación</th>
                <th>Boletos</th>
                <th>Recaudación (Bs)</th>
            </tr>
        </thead>
        <tbody id="tablaBody">
            <tr><td colspan="6" style="text-align:center; color:#666;">Selecciona un rango de fechas y presiona Generar.</td></tr>
        </tbody>
    </table>

    <script>
        // Establecer fechas por defecto (hoy)
        document.getElementById('fechaInicio').valueAsDate = new Date();
        document.getElementById('fechaFin').valueAsDate = new Date();

        function cargarReporte() {
            const fd = new FormData();
            // Ya no enviamos 'action', Laravel usa la URL
            fd.append('fechaInicio', document.getElementById('fechaInicio').value);
            fd.append('fechaFin', document.getElementById('fechaFin').value);

            document.getElementById('tablaBody').innerHTML = '<tr><td colspan="6" style="text-align:center;">Cargando reporte...</td></tr>';

            // CAMBIO: De 'api.php' a '/api/reportes/generar'
            fetch('/api/reportes/generar', { method: 'POST', body: fd })
            .then(r => r.json())
            .then(data => {
                if(data.status === 'success') dibujarTabla(data.data);
                else alert('Error: ' + data.message);
            })
            .catch(err => {
                console.error(err);
                document.getElementById('tablaBody').innerHTML = '<tr><td colspan="6" style="text-align:center; color:red;">Error de conexión con el servidor.</td></tr>';
            });
        }

        function dibujarTabla(datos) {
            const tbody = document.getElementById('tablaBody');
            tbody.innerHTML = '';
            
            let sumaTotal = 0;
            let sumaBoletos = 0;

            if (datos.length === 0) {
                tbody.innerHTML = '<tr><td colspan="6" style="text-align:center; color:#666;">No hay ventas registradas en esta fecha.</td></tr>';
                document.getElementById('resTotal').textContent = 'Bs 0.00';
                document.getElementById('resBoletos').textContent = '0';
                return;
            }

            datos.forEach(f => {
                sumaTotal += parseFloat(f.recaudacion);
                sumaBoletos += parseInt(f.boletos_vendidos);

                let colorBar = f.porcentaje_ocupacion > 80 ? '#ff4757' : (f.porcentaje_ocupacion > 50 ? '#ffa502' : 'var(--primary)');

                tbody.innerHTML += `
                    <tr>
                        <td style="color:#aaa;">${f.fechaFuncion} <br><b style="color:white;">${f.horaInicio.substring(0,5)}</b></td>
                        <td><b>${f.pelicula}</b></td>
                        <td>${f.sala}</td>
                        <td>
                            <div class="progress-bar"><div class="progress-fill" style="width:${f.porcentaje_ocupacion}%; background:${colorBar};"></div></div>
                            ${f.porcentaje_ocupacion}%
                        </td>
                        <td>${f.boletos_vendidos} / ${f.capacidad}</td>
                        <td style="color:var(--success); font-weight:bold;">${f.recaudacion}</td>
                    </tr>
                `;
            });

            document.getElementById('resTotal').textContent = `Bs ${sumaTotal.toFixed(2)}`;
            document.getElementById('resBoletos').textContent = sumaBoletos;
        }

        function exportarExcel() {
            let tabla = document.getElementById("tablaReporte");
            let filas = tabla.querySelectorAll("tr");
            let csv = [];
            
            // Título para el Excel
            csv.push("REPORTE MULTICINE: " + document.getElementById('fechaInicio').value + " AL " + document.getElementById('fechaFin').value);
            csv.push(""); // Fila vacía

            for (let i = 0; i < filas.length; i++) {
                let fila = [], cols = filas[i].querySelectorAll("td, th");
                
                for (let j = 0; j < cols.length; j++) {
                    // Limpiamos el HTML interior y saltos de línea para que quede texto limpio
                    let texto = cols[j].innerText.replace(/(\r\n|\n|\r)/gm, " ").trim();
                    fila.push('"' + texto + '"'); // Comillas por si hay comas en los títulos
                }
                csv.push(fila.join(","));
            }

            // Descargar archivo
            let csvArchivo = new Blob([csv.join("\n")], { type: "text/csv;charset=utf-8;" });
            let link = document.createElement("a");
            link.href = URL.createObjectURL(csvArchivo);
            link.download = "Reporte_Multicine.csv";
            link.style.display = "none";
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }
    </script>
</body>
</html>