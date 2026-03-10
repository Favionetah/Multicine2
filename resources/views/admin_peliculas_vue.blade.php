<!DOCTYPE html>
<html lang="es">
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>CRUD Películas con Vue.js</title>
    <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;800&display=swap');
        :root { --primary: #42b883; --dark: #0f172a; --card: #1e293b; }
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Montserrat', sans-serif; }
        body { background: var(--dark); color: white; padding: 40px; }
        
        .header-vue { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; border-bottom: 1px solid #334155; padding-bottom: 20px;}
        .btn-vue { background: var(--primary); color: black; border: none; padding: 10px 20px; border-radius: 8px; font-weight: bold; cursor: pointer; transition: 0.3s; }
        .btn-vue:hover { background: white; }
        
        .grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 20px; }
        .card { background: var(--card); border-radius: 12px; overflow: hidden; border: 1px solid #334155; }
        .card img { width: 100%; height: 350px; object-fit: cover; }
        .card-body { padding: 15px; }
        .actions { display: flex; gap: 10px; margin-top: 15px; }
        .btn-edit { flex: 1; background: #3b82f6; color: white; border: none; padding: 8px; border-radius: 5px; cursor: pointer; }
        .btn-del { flex: 1; background: #ef4444; color: white; border: none; padding: 8px; border-radius: 5px; cursor: pointer; }
        
        .modal { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.8); display: flex; align-items: center; justify-content: center; z-index: 100;}
        .modal-content { background: var(--card); width: 500px; padding: 30px; border-radius: 15px; max-height: 90vh; overflow-y: auto;}
        .form-group { margin-bottom: 15px; }
        .form-control { width: 100%; padding: 10px; background: var(--dark); border: 1px solid #334155; color: white; border-radius: 5px; }
    </style>
</head>
<body>

<div id="app">
    <div class="header-vue">
        <div style="display:flex; gap:15px; align-items:center;">
            <h2><i class="fab fa-vuejs" style="color:var(--primary);"></i> GESTIÓN DE PELÍCULAS</h2>
            <a href="/admin" style="color:#888; text-decoration:none; font-size:0.9rem; border:1px solid #888; padding:5px 10px; border-radius:5px;">Volver</a>
        </div>
        <button class="btn-vue" @click="abrirModal(null)"><i class="fas fa-plus"></i> NUEVA PELÍCULA</button>
    </div>

    <div v-if="cargando" style="text-align: center; color: #888;">Cargando catálogo...</div>

    <div class="grid">
        <div class="card" v-for="peli in peliculas" :key="peli.idPelicula">
            <img :src="peli.imagenPoster ? (peli.imagenPoster.startsWith('http') ? peli.imagenPoster : '/img/' + peli.imagenPoster) : '/img/fondo.jpg'" onerror="this.src='/img/fondo.jpg'" alt="Poster">
            
            <div class="card-body">
                <h3 style="font-size:1.1rem; margin-bottom:5px;">@{{ peli.titulo }}</h3>
                <span style="color:var(--primary); font-size:0.8rem; font-weight:bold;">@{{ peli.genero }} | @{{ peli.duracion }} min</span>
                <div class="actions">
                    <button class="btn-edit" @click="abrirModal(peli)">Editar</button>
                    <button class="btn-del" @click="eliminar(peli.idPelicula)">Eliminar</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal" v-if="modalVisible">
        <div class="modal-content">
            <div style="display:flex; justify-content:space-between; margin-bottom: 20px;">
                <h3 style="color:var(--primary);">@{{ form.id ? 'Editar Película' : 'Crear Nueva Película' }}</h3>
                <button @click="modalVisible = false" style="background:none; border:none; color:white; font-size:1.5rem; cursor:pointer;">&times;</button>
            </div>
            
            <form @submit.prevent="guardar">
                <div class="form-group"><input type="text" v-model="form.titulo" class="form-control" placeholder="Título" required></div>
                <div class="form-group"><input type="text" v-model="form.genero" class="form-control" placeholder="Género" required></div>
                <div style="display:flex; gap:10px;">
                    <div class="form-group" style="flex:1;"><input type="number" v-model="form.duracion" class="form-control" placeholder="Duración (min)" required></div>
                    <div class="form-group" style="flex:1;"><input type="text" v-model="form.clasificacion" class="form-control" placeholder="Clasificación" required></div>
                </div>
                <div class="form-group"><input type="text" v-model="form.idioma" class="form-control" placeholder="Idioma" required></div>
                <div class="form-group"><textarea v-model="form.sinopsis" class="form-control" placeholder="Sinopsis..." rows="4" required></textarea></div>
                
                <div class="form-group">
                    <label style="color:#888; font-size:0.8rem; display:block; margin-bottom:5px;">Póster de la película (Opcional):</label>
                    <input type="file" id="inputFile" class="form-control" accept="image/*">
                </div>

                <button type="submit" class="btn-vue" style="width:100%;" :disabled="guardando">
                    @{{ guardando ? 'Guardando en Base de Datos...' : 'Guardar Película' }}
                </button>
            </form>
        </div>
    </div>
</div>

<script>
    const { createApp, ref, onMounted } = Vue;

    createApp({
        setup() {
            const peliculas = ref([]);
            const cargando = ref(true);
            const guardando = ref(false);
            const modalVisible = ref(false);
            
            // Estado del formulario
            const form = ref({ id: '', titulo: '', genero: '', duracion: '', clasificacion: '', idioma: '', sinopsis: '' });

            const fetchPeliculas = async () => {
                cargando.value = true;
                try {
                    const res = await fetch('/api/peliculas');
                    peliculas.value = await res.json();
                } catch (e) { console.error("Error al cargar cartelera", e); }
                cargando.value = false;
            };

            const abrirModal = (peli) => {
                if(peli) {
                    // MODO EDITAR: Llenamos el formulario con los datos de la película
                    form.value = { 
                        id: peli.idPelicula, 
                        titulo: peli.titulo, 
                        genero: peli.genero, 
                        duracion: peli.duracion, 
                        clasificacion: peli.clasificacion, 
                        idioma: peli.idioma, 
                        sinopsis: peli.sinopsis 
                    };
                } else {
                    // MODO CREAR: Limpiamos el formulario (El ID queda vacío)
                    form.value = { id: '', titulo: '', genero: '', duracion: '', clasificacion: '', idioma: '', sinopsis: '' };
                }
                
                modalVisible.value = true;
                
                // Limpiar el campo de archivo visualmente
                setTimeout(() => {
                    const fileInput = document.getElementById('inputFile');
                    if (fileInput) fileInput.value = '';
                }, 100);
            };

            const guardar = async () => {
                guardando.value = true;
                
                // 1. CAPTURAMOS EL TOKEN (Asegúrate de que esto esté dentro de la función)
                const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                const formData = new FormData();
                
                // IMPORTANTE: El controlador espera 'idPelicula' para verificar si es edición
                if(form.value.id) formData.append('idPelicula', form.value.id);
                
                formData.append('titulo', form.value.titulo);
                formData.append('genero', form.value.genero);
                formData.append('duracion', form.value.duracion);
                formData.append('clasificacion', form.value.clasificacion);
                formData.append('idioma', form.value.idioma);
                formData.append('sinopsis', form.value.sinopsis);

                // Capturamos el archivo del póster
                const fileInput = document.getElementById('inputFile');
                if (fileInput && fileInput.files[0]) {
                    formData.append('imagen', fileInput.files[0]);
                }

                try {
                    // 2. ENVIAMOS LA PETICIÓN CON LOS HEADERS DE SEGURIDAD
                    const res = await fetch('/api/peliculas', { 
                        method: 'POST', 
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': token, // ESTA LÍNEA QUITA EL ERROR 419
                            'Accept': 'application/json' // ESTA LÍNEA EVITA EL ERROR DE SYNTAXERROR
                        }
                    });

                    const data = await res.json();
                    
                    if(data.status === 'success') {
                        alert('✅ Película guardada correctamente');
                        modalVisible.value = false;
                        fetchPeliculas(); 
                    } else {
                        alert('❌ Error: ' + data.message);
                    }
                } catch (e) { 
                    console.error("Error crítico:", e);
                    alert("Error de conexión. Revisa la consola.");
                }
                guardando.value = false;
            };

            const eliminar = async (id) => {
                if(!confirm('¿Seguro que deseas eliminar esta película permanentemente?')) return;
                
                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                const formData = new FormData();
                formData.append('id', id);

                try {
                    const res = await fetch('/api/peliculas/eliminar', { 
                        method: 'POST', 
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json'
                        }
                    });
                    
                    const data = await res.json();
                    if(data.status === 'success') {
                        fetchPeliculas();
                    } else {
                        alert('❌ Error: ' + data.message);
                    }
                } catch (e) { 
                    console.error(e); 
                    alert("Error al intentar eliminar.");
                }
            };

            onMounted(() => { fetchPeliculas(); });

            return { peliculas, cargando, guardando, modalVisible, form, abrirModal, guardar, eliminar };
        }
    }).mount('#app');
</script>
</body>
</html>