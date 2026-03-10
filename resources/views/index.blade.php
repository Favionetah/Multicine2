<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Multicine La Paz - Ingreso</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;800;900&display=swap');
        :root { --primary: #00d2d3; --danger: #ff4757; }
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Montserrat', sans-serif; }
        body { background: linear-gradient(rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.9)), url('img/Avatar.jpg') center/cover no-repeat; background-color: #050505; height: 100vh; display: flex; align-items: center; justify-content: center; color: white; }
        .login-container { background: rgba(15, 15, 15, 0.9); padding: 40px; border-radius: 20px; width: 100%; max-width: 400px; border: 1px solid #333; box-shadow: 0 15px 50px rgba(0, 0, 0, 0.8); backdrop-filter: blur(10px); }
        .brand { display: flex; align-items: center; gap: 10px; margin-bottom: 30px; font-weight: 900; font-size: 1.2rem; }
        .brand-icon { background: var(--primary); color: black; padding: 5px 10px; border-radius: 8px; font-size: 0.9rem; }
        .tabs { display: flex; border-bottom: 1px solid #333; margin-bottom: 25px; }
        .tab-btn { flex: 1; background: none; border: none; color: #666; padding: 10px; font-weight: bold; cursor: pointer; transition: 0.3s; border-bottom: 2px solid transparent; font-size: 0.9rem; }
        .tab-btn.active { color: white; border-bottom-color: var(--primary); }
        .form-section { display: none; animation: fadeIn 0.3s; }
        .form-section.active { display: block; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
        .input-group { margin-bottom: 15px; }
        .input-group label { display: block; color: #888; font-size: 0.7rem; font-weight: bold; margin-bottom: 5px; text-transform: uppercase; }
        .input-group input { width: 100%; padding: 12px 15px; background: #0a0a0a; border: 1px solid #333; color: white; border-radius: 8px; font-size: 0.9rem; transition: 0.3s; }
        .input-group input:focus { border-color: var(--primary); outline: none; }
        .btn-submit { width: 100%; padding: 15px; background: var(--danger); color: white; border: none; border-radius: 8px; font-weight: 900; cursor: pointer; transition: 0.3s; margin-top: 10px; font-size: 1rem; }
        .btn-submit:hover { background: #ff6b81; }
        .btn-submit.btn-registro { background: var(--primary); color: black; }
        .btn-submit.btn-registro:hover { background: #00e5e6; }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="brand"><span class="brand-icon"><i class="fas fa-ticket-alt"></i></span> MULTICINE LA PAZ</div>

        <div class="tabs">
            <button class="tab-btn active" id="tabLogin" onclick="switchTab('login')">INICIAR SESIÓN</button>
            <button class="tab-btn" id="tabRegistro" onclick="switchTab('registro')">REGISTRARSE</button>
        </div>

        <form id="formLogin" class="form-section active">
            <div class="input-group">
                <label>Correo Electrónico</label>
                <input type="email" name="correo" required placeholder="ejemplo@correo.com">
            </div>
            <div class="input-group">
                <label>Contraseña</label>
                <input type="password" name="contrasena" required placeholder="••••••••">
            </div>
            <button type="submit" class="btn-submit">ENTRAR AL SISTEMA</button>
        </form>

        <form id="formRegistro" class="form-section">
            <div class="input-group">
                <label>Carnet de Identidad (CI)*</label>
                <input type="text" name="CI" required placeholder="Ej. 1234567">
            </div>
            <div class="input-group">
                <label>Nombre Completo*</label>
                <input type="text" name="nombre" required placeholder="Ej. Juan Pérez">
            </div>
            <div class="input-group" style="display:flex; gap:10px;">
                <div style="flex:1;"><label>Teléfono*</label><input type="number" name="telefono" required placeholder="8 dígitos"></div>
                <div style="flex:1;"><label>Correo*</label><input type="email" name="correo" required placeholder="tu@email.com"></div>
            </div>
            <div class="input-group">
                <label>Crear Contraseña*</label>
                <input type="password" name="contrasena" required placeholder="Mínimo 6 caracteres">
            </div>
            <button type="submit" class="btn-submit btn-registro">CREAR CUENTA</button>
        </form>
    </div>

    <script>
        function switchTab(tab) {
            document.querySelectorAll('.form-section').forEach(f => f.classList.remove('active'));
            document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
            if(tab === 'login') {
                document.getElementById('formLogin').classList.add('active');
                document.getElementById('tabLogin').classList.add('active');
            } else {
                document.getElementById('formRegistro').classList.add('active');
                document.getElementById('tabRegistro').classList.add('active');
            }
        }

        // --- LÓGICA DE LOGIN ---
        document.getElementById('formLogin').addEventListener('submit', function(e) {
            e.preventDefault(); 
            const formData = new FormData(this);
            
            // Obtenemos el token actualizado del meta tag
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            fetch('/login', {
                method: 'POST',
                headers: { 
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken // Enviamos el escudo de seguridad
                },
                body: formData
            })
            .then(res => {
                if (res.status === 419) {
                    alert('⚠️ La sesión expiró. Por favor recarga la página (F5).');
                    location.reload();
                    return;
                }
                return res.json();
            })
            .then(data => {
                if (data.status === 'success') {
                    // Redirección exacta según tu base de datos
                    if (data.rol === 'administrador') window.location.href = '/admin';
                    else if (data.rol === 'cajero') window.location.href = '/cajero';
                    else window.location.href = '/cartelera'; // Para clientes como Fernando
                } else {
                    alert('❌ ' + data.message);
                }
            });
        });

        // --- LÓGICA DE REGISTRO ---
        document.getElementById('formRegistro').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            const btn = this.querySelector('button');
            btn.textContent = 'CREANDO CUENTA...';

            fetch('/registro', {
                method: 'POST',
                headers: { 
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    alert('✅ ' + data.message);
                    switchTab('login');
                } else {
                    alert('❌ ' + data.message);
                }
                btn.textContent = 'CREAR CUENTA';
            });
        });
    </script>
</body>
</html>