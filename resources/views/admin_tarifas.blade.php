<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Configuración de Tarifas - Admin</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;800;900&display=swap');
        :root { --primary: #00d2d3; --dark-bg: #050505; --card-bg: #111; }
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Montserrat', sans-serif; }
        body { background: var(--dark-bg); color: white; padding: 50px; display: flex; flex-direction: column; align-items: center; }
        
        .header { width: 100%; max-width: 600px; display: flex; justify-content: space-between; margin-bottom: 30px; }
        .btn-back { color: var(--primary); text-decoration: none; font-weight: bold; }
        
        .config-card { background: var(--card-bg); padding: 40px; border-radius: 20px; border: 1px solid #333; width: 100%; max-width: 600px; box-shadow: 0 10px 30px rgba(0,0,0,0.5); }
        .form-group { margin-bottom: 25px; }
        label { display: block; color: #aaa; font-size: 0.9rem; margin-bottom: 10px; font-weight: bold; }
        .input-group { display: flex; align-items: center; background: #1a1a1a; border: 1px solid #333; border-radius: 10px; overflow: hidden; }
        .input-group span { background: #333; padding: 15px; font-weight: bold; color: var(--primary); }
        input { width: 100%; padding: 15px; background: transparent; border: none; color: white; font-size: 1.2rem; outline: none; }
        
        .btn-save { background: var(--primary); color: black; border: none; padding: 15px; width: 100%; border-radius: 10px; font-weight: 900; font-size: 1.1rem; cursor: pointer; transition: 0.3s; margin-top: 20px; }
        .btn-save:hover { background: white; }
    </style>
</head>
<body>

    <div class="header">
        <a href="/admin" class="btn-back"><i class="fas fa-arrow-left"></i> VOLVER AL PANEL</a>
        <span style="color:#666;">Admin: <b>{{ session('nombre', 'Administrador') }}</b></span>
    </div>

    <div class="config-card">
        <h2 style="margin-bottom: 5px; color: var(--primary);"><i class="fas fa-percent"></i> POLÍTICA DE DESCUENTOS</h2>
        <p style="color: #666; font-size: 0.85rem; margin-bottom: 30px;">Define los porcentajes que se aplicarán en la taquilla y la web.</p>

        <div class="form-group">
            <label>Descuento para Niños (Categoría de Edad)</label>
            <div class="input-group">
                <input type="number" id="descNino" min="0" max="100">
                <span>%</span>
            </div>
        </div>

        <div class="form-group">
            <label>Promoción de Miércoles (Días Específicos)</label>
            <div class="input-group">
                <input type="number" id="descMiercoles" min="0" max="100">
                <span>%</span>
            </div>
        </div>

        <div class="form-group">
            <label>Descuento Socio Multicine (Lealtad)</label>
            <div class="input-group">
                <input type="number" id="descSocio" min="0" max="100">
                <span>%</span>
            </div>
        </div>

        <button class="btn-save" onclick="guardarTarifas()"><i class="fas fa-save"></i> GUARDAR CAMBIOS</button>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // CAMBIO: De 'api.php?action=obtener_tarifas' a la ruta de Laravel
            fetch('/api/config/tarifas') 
            .then(res => res.json())
            .then(data => {
                document.getElementById('descNino').value = data.nino;
                document.getElementById('descMiercoles').value = data.miercoles;
                document.getElementById('descSocio').value = data.socio;
            });
        });

        function guardarTarifas() {
            const fd = new FormData();
            // Ya no necesitas enviar 'action', Laravel usa la URL para saber qué hacer
            fd.append('nino', document.getElementById('descNino').value);
            fd.append('miercoles', document.getElementById('descMiercoles').value);
            fd.append('socio', document.getElementById('descSocio').value);

            // CAMBIO: De 'api.php' a '/api/config/tarifas'
            fetch('/api/config/tarifas', { method: 'POST', body: fd })
            .then(res => res.json())
            .then(data => {
                if(data.status === 'success') alert("✅ ¡" + data.message + "!");
                else alert("❌ " + data.message);
            });
        }
    </script>
</body>
</html>