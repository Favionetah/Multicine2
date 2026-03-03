<?php
// Detectar si estamos en HTTPS (en XAMPP normalmente será false)
$isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');

// Configuración segura de la cookie de sesión
session_set_cookie_params([
    'lifetime' => 0,              // Sesión hasta cerrar navegador
    'path' => '/',
    'domain' => '',
    'secure' => $isHttps,         // Solo true si hay HTTPS
    'httponly' => true,           // Impide acceso desde JS (XSS protection)
    'samesite' => 'Lax'           // Recomendado para login normal
]);

session_start();
// Si el usuario ya inició sesión, lo mandamos a su panel correspondiente
if (isset($_SESSION['rol'])) {
    if ($_SESSION['rol'] === 'administrador') header("Location: admin.php");
    elseif ($_SESSION['rol'] === 'cajero') header("Location: cajero.php");
    else header("Location: cartelera_cliente.php"); // O como se llame la vista del cliente
    exit();
}

?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Multicine La Paz - Ingreso</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;800;900&display=swap');

        :root {
            --primary: #00d2d3;
            --danger: #ff4757;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Montserrat', sans-serif;
        }

        body {
            /* Fondo inspirado en tu diseño original de Avatar */
            background: linear-gradient(rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.9)), url('img/Avatar.jpg') center/cover no-repeat;
            background-color: #050505;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
        }

        .login-container {
            background: rgba(15, 15, 15, 0.9);
            padding: 40px;
            border-radius: 20px;
            width: 100%;
            max-width: 400px;
            border: 1px solid #333;
            box-shadow: 0 15px 50px rgba(0, 0, 0, 0.8);
            backdrop-filter: blur(10px);
            position: relative;
            overflow: hidden;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 30px;
            font-weight: 900;
            font-size: 1.2rem;
        }

        .brand-icon {
            background: var(--primary);
            color: black;
            padding: 5px 10px;
            border-radius: 8px;
            font-size: 0.9rem;
        }

        /* Pestañas (Tabs) */
        .tabs {
            display: flex;
            border-bottom: 1px solid #333;
            margin-bottom: 25px;
        }

        .tab-btn {
            flex: 1;
            background: none;
            border: none;
            color: #666;
            padding: 10px;
            font-weight: bold;
            cursor: pointer;
            transition: 0.3s;
            border-bottom: 2px solid transparent;
            font-size: 0.9rem;
        }

        .tab-btn.active {
            color: white;
            border-bottom-color: var(--primary);
        }

        /* Formularios */
        .form-section {
            display: none;
            animation: fadeIn 0.3s;
        }

        .form-section.active {
            display: block;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .input-group {
            margin-bottom: 15px;
        }

        .input-group label {
            display: block;
            color: #888;
            font-size: 0.7rem;
            font-weight: bold;
            margin-bottom: 5px;
            text-transform: uppercase;
        }

        .input-group input {
            width: 100%;
            padding: 12px 15px;
            background: #0a0a0a;
            border: 1px solid #333;
            color: white;
            border-radius: 8px;
            font-size: 0.9rem;
            transition: 0.3s;
        }

        .input-group input:focus {
            border-color: var(--primary);
            outline: none;
        }

        .btn-submit {
            width: 100%;
            padding: 15px;
            background: var(--danger);
            color: white;
            border: none;
            border-radius: 8px;
            font-weight: 900;
            cursor: pointer;
            transition: 0.3s;
            margin-top: 10px;
            font-size: 1rem;
        }

        .btn-submit:hover {
            background: #ff6b81;
        }

        .btn-submit.btn-registro {
            background: var(--primary);
            color: black;
        }

        .btn-submit.btn-registro:hover {
            background: #00e5e6;
        }
    </style>
</head>

<body>

    <div class="login-container">
        <div class="brand">
            <span class="brand-icon"><i class="fas fa-ticket-alt"></i></span> MULTICINE LA PAZ
        </div>

        <div class="tabs">
            <button class="tab-btn active" id="tabLogin" onclick="switchTab('login')">INICIAR SESIÓN</button>
            <button class="tab-btn" id="tabRegistro" onclick="switchTab('registro')">REGISTRARSE</button>
        </div>

        <form id="formLogin" class="form-section active">
            <input type="hidden" name="action" value="login">

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
            <input type="hidden" name="action" value="registro_cliente">

            <div class="input-group">
                <label>Carnet de Identidad (CI)</label>
                <input type="text" name="CI" required placeholder="Ej. 1234567">
            </div>

            <div class="input-group">
                <label>Nombre Completo</label>
                <input type="text" id="reg_nombre" name="nombre" required placeholder="Ej. Juan Pérez">
            </div>

            <div class="input-group" style="display:flex; gap:10px;">
                <div style="flex:1;">
                    <label>Teléfono</label>
                    <input type="number" id="reg_telefono" name="telefono" required placeholder="8 dígitos">
                </div>
                <div style="flex:1;">
                    <label>Correo</label>
                    <input type="email" name="correo" required placeholder="tu@email.com">
                </div>
            </div>

            <div class="input-group">
                <label>Crear Contraseña</label>
                <input type="password" name="contrasena" required placeholder="Mínimo 6 caracteres">
            </div>

            <button type="submit" class="btn-submit btn-registro">CREAR CUENTA</button>
        </form>
    </div>

    <script>
        // --- ALTERNAR ENTRE LOGIN Y REGISTRO ---
        function switchTab(tab) {
            document.getElementById('tabLogin').classList.remove('active');
            document.getElementById('tabRegistro').classList.remove('active');
            document.getElementById('formLogin').classList.remove('active');
            document.getElementById('formRegistro').classList.remove('active');

            if (tab === 'login') {
                document.getElementById('tabLogin').classList.add('active');
                document.getElementById('formLogin').classList.add('active');
            } else {
                document.getElementById('tabRegistro').classList.add('active');
                document.getElementById('formRegistro').classList.add('active');
            }
        }

        // --- ENVIAR LOGIN (US-07) ---
        document.getElementById('formLogin').addEventListener('submit', function(e) {
            e.preventDefault();

            fetch('api.php', {
                    method: 'POST',
                    body: new FormData(this)
                })
                .then(res => res.json())
                .then(data => {
                    if (data.status === 'success') {
                        // Redirigir según el rol del usuario que inició sesión
                        if (data.rol === 'administrador') window.location.href = 'admin.php';
                        else if (data.rol === 'cajero') window.location.href = 'cajero.php';
                        else window.location.href = 'cartelera_cliente.php'; // Cambia esto al nombre de tu vista de cliente
                    } else {
                        alert('❌ Error: ' + data.message);
                    }
                });
        });

        // --- ENVIAR REGISTRO (US-01) ---
        document.getElementById('formRegistro').addEventListener('submit', function(e) {
            e.preventDefault();

            // 1. Validación Visual Frontend: Teléfono de 8 dígitos
            const telefono = document.getElementById('reg_telefono').value;
            if (telefono.length !== 8) {
                alert("⚠️ El número de teléfono debe tener exactamente 8 dígitos.");
                return;
            }

            // 2. Validación Visual Frontend: Nombre sin números
            const nombre = document.getElementById('reg_nombre').value;
            const regexLetras = /^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/;
            if (!regexLetras.test(nombre)) {
                alert("⚠️ El nombre no es válido. No uses números ni símbolos (Ej. Di3go).");
                return;
            }

            // 3. Enviar a la API
            fetch('api.php', {
                    method: 'POST',
                    body: new FormData(this)
                })
                .then(res => res.json())
                .then(data => {
                    if (data.status === 'success') {
                        alert('✅ ' + data.message);
                        this.reset(); // Limpiar el formulario
                        switchTab('login'); // Devolver al usuario a la pantalla de login
                    } else {
                        alert('❌ Error: ' + data.message);
                    }
                });
        });
    </script>
</body>

</html>