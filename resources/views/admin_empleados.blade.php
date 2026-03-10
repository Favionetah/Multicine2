<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Empleados - Admin</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;800;900&display=swap');
        :root { --primary: #00d2d3; --dark-bg: #050505; --card-bg: #111; --danger: #ff4757; --success: #2ed573; }
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Montserrat', sans-serif; }
        body { background: var(--dark-bg); color: white; padding: 40px; }
        
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; border-bottom: 1px solid #333; padding-bottom: 20px;}
        .btn-back { color: var(--primary); text-decoration: none; font-weight: bold; }
        
        .top-actions { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        .btn-action { background: var(--primary); color: black; border: none; padding: 10px 20px; border-radius: 8px; font-weight: bold; cursor: pointer; transition: 0.3s; }
        .btn-action:hover { background: white; }

        .panels-container { display: flex; gap: 30px; flex-wrap: wrap; }
        .panel { background: var(--card-bg); border-radius: 15px; border: 1px solid #333; padding: 20px; flex: 1; min-width: 400px; height: 600px; display: flex; flex-direction: column; }
        
        .panel-title { color: var(--primary); margin-bottom: 15px; font-weight: 900; text-transform: uppercase; font-size: 1.1rem; border-bottom: 1px solid #333; padding-bottom: 10px; }
        .table-wrapper { flex: 1; overflow-y: auto; }

        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #222; font-size: 0.85rem; }
        th { background: #1a1a1a; color: #888; position: sticky; top: 0; }
        
        .badge { padding: 5px 10px; border-radius: 20px; font-size: 0.7rem; font-weight: bold; text-transform: uppercase; }
        .badge-admin { background: rgba(0,210,211,0.2); color: var(--primary); border: 1px solid var(--primary); }
        .badge-cajero { background: rgba(255,165,2,0.2); color: #ffa502; border: 1px solid #ffa502; }
        .badge-activo { background: rgba(46,213,115,0.2); color: var(--success); }
        .badge-inactivo { background: rgba(255,71,87,0.2); color: var(--danger); }

        .btn-toggle { background: transparent; border: 1px solid #555; color: white; padding: 5px 10px; border-radius: 5px; cursor: pointer; font-size: 0.7rem; transition: 0.3s; }
        .btn-toggle:hover { background: #333; }

        /* Modal Crear Empleado */
        .modal-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.9); z-index: 500; display: none; align-items: center; justify-content: center; }
        .modal-overlay.active { display: flex; }
        .modal-content { background: #121212; width: 400px; padding: 30px; border-radius: 15px; border: 1px solid #333; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; color: #888; font-size: 0.8rem; margin-bottom: 5px; }
        .form-input { width: 100%; padding: 10px; background: #1a1a1a; border: 1px solid #333; color: white; border-radius: 8px; }
        .form-input:focus { outline: none; border-color: var(--primary); }
    </style>
</head>
<body>

    <div class="header">
        <a href="/admin" class="btn-back"><i class="fas fa-arrow-left"></i> VOLVER AL PANEL</a>
        <h2><i class="fas fa-users-cog"></i> GESTIÓN DE EMPLEADOS Y SEGURIDAD</h2>
        <span style="color:#666;">Admin: <b>{{ session('nombre', 'Administrador') }}</b></span>
    </div>

    <div class="top-actions">
        <p style="color:#888;">Administra los accesos al sistema y revisa el registro de actividades (Logs).</p>
        <button class="btn-action" onclick="document.getElementById('modalCrear').classList.add('active')">
            <i class="fas fa-user-plus"></i> NUEVO EMPLEADO
        </button>
    </div>

    <div class="panels-container">
        <div class="panel">
            <div class="panel-title"><i class="fas fa-id-badge"></i> Personal Autorizado</div>
            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>CI</th>
                            <th>Nombre</th>
                            <th>Rol</th>
                            <th>Estado</th>
                            <th>Acción</th>
                        </tr>
                    </thead>
                    <tbody id="tablaEmpleados">
                        <tr><td colspan="5" style="text-align:center;">Cargando...</td></tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="panel">
            <div class="panel-title"><i class="fas fa-clipboard-list"></i> Registro de Actividad (Últimos 50)</div>
            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>Fecha y Hora</th>
                            <th>Usuario</th>
                            <th>Acción Registrada</th>
                        </tr>
                    </thead>
                    <tbody id="tablaLogs">
                        <tr><td colspan="3" style="text-align:center;">Cargando...</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="modal-overlay" id="modalCrear">
        <div class="modal-content">
            <div style="display:flex; justify-content:space-between; margin-bottom:20px;">
                <h3 style="color:var(--primary);"><i class="fas fa-user-shield"></i> Alta de Empleado</h3>
                <button onclick="document.getElementById('modalCrear').classList.remove('active')" style="background:none;border:none;color:white;cursor:pointer;font-size:1.2rem;">&times;</button>
            </div>
            <form id="formCrearEmpleado" onsubmit="crearEmpleado(event)">
                <div class="form-group">
                    <label>Carnet de Identidad (CI)</label>
                    <input type="text" id="empCI" class="form-input" required>
                </div>
                <div class="form-group">
                    <label>Nombre Completo</label>
                    <input type="text" id="empNombre" class="form-input" required>
                </div>
                <div class="form-group">
                    <label>Correo Electrónico</label>
                    <input type="email" id="empCorreo" class="form-input" required>
                </div>
                <div class="form-group">
                    <label>Contraseña Temporal</label>
                    <input type="text" id="empPass" class="form-input" required>
                </div>
                <div class="form-group">
                    <label>Rol en el Sistema</label>
                    <select id="empRol" class="form-input" required>
                        <option value="cajero">Cajero (Ventas)</option>
                        <option value="administrador">Administrador (Total)</option>
                    </select>
                </div>
                <button type="submit" class="btn-action" style="width:100%; margin-top:10px;">REGISTRAR EMPLEADO</button>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', cargarDatos);

        function cargarDatos() {
            // CAMBIO: De 'api.php?action=admin_datos_empleados' a la ruta de Laravel
            fetch('/api/empleados/datos')
            .then(r => r.json())
            .then(data => {
                dibujarEmpleados(data.empleados);
                dibujarLogs(data.logs);
            });
        }

        function dibujarEmpleados(empleados) {
            const tbody = document.getElementById('tablaEmpleados');
            tbody.innerHTML = '';
            empleados.forEach(e => {
                const badgeRol = e.rol === 'administrador' ? 'badge-admin' : 'badge-cajero';
                const badgeEstado = e.estado === 'activo' ? 'badge-activo' : 'badge-inactivo';
                const btnText = e.estado === 'activo' ? 'Desactivar' : 'Activar';
                const nuevoEstado = e.estado === 'activo' ? 'inactivo' : 'activo';

                tbody.innerHTML += `
                    <tr>
                        <td><b>${e.CI}</b></td>
                        <td>${e.nombre}</td>
                        <td><span class="badge ${badgeRol}">${e.rol}</span></td>
                        <td><span class="badge ${badgeEstado}">${e.estado}</span></td>
                        <td>
                            <button class="btn-toggle" onclick="cambiarEstado('${e.CI}', '${nuevoEstado}')">
                                <i class="fas fa-power-off"></i> ${btnText}
                            </button>
                        </td>
                    </tr>
                `;
            });
        }

        function dibujarLogs(logs) {
            const tbody = document.getElementById('tablaLogs');
            tbody.innerHTML = '';
            if(logs.length === 0) {
                tbody.innerHTML = '<tr><td colspan="3" style="text-align:center;color:#666;">No hay actividad reciente.</td></tr>';
                return;
            }
            logs.forEach(l => {
                tbody.innerHTML += `
                    <tr>
                        <td style="color:#888; font-size:0.75rem;">${l.fecha}</td>
                        <td><b>${l.nombre}</b> <span style="color:var(--primary); font-size:0.7rem;">(${l.rol})</span></td>
                        <td style="color:#ccc;">${l.accion}</td>
                    </tr>
                `;
            });
        }

        function cambiarEstado(ci, nuevoEstado) {
            if(!confirm(`¿Seguro que deseas cambiar el estado a ${nuevoEstado}?`)) return;

            const fd = new FormData();
            fd.append('accion_tipo', 'cambiar_estado');
            fd.append('CI', ci);
            fd.append('estado', nuevoEstado);

            // CAMBIO: De 'api.php' a la ruta de gestión
            fetch('/api/empleados/gestionar', { method: 'POST', body: fd })
            .then(r => r.json())
            .then(data => {
                if(data.status === 'success') cargarDatos();
                else alert('Error: ' + data.message);
            });
        }

        function crearEmpleado(e) {
            e.preventDefault();
            const fd = new FormData();
            fd.append('accion_tipo', 'crear');
            fd.append('CI', document.getElementById('empCI').value);
            fd.append('nombre', document.getElementById('empNombre').value);
            fd.append('correo', document.getElementById('empCorreo').value);
            fd.append('contrasena', document.getElementById('empPass').value);
            fd.append('rol', document.getElementById('empRol').value);

            // CAMBIO: De 'api.php' a la ruta de gestión
            fetch('/api/empleados/gestionar', { method: 'POST', body: fd })
            .then(r => r.json())
            .then(data => {
                if(data.status === 'success') {
                    alert('✅ Empleado creado exitosamente.');
                    document.getElementById('modalCrear').classList.remove('active');
                    document.getElementById('formCrearEmpleado').reset();
                    cargarDatos();
                } else {
                    alert('❌ Error: ' + data.message);
                }
            });
        }
    </script>
</body>
</html>