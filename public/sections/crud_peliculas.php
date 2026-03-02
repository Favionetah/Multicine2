<h2 class="title-cyan">Gestión de Películas</h2>

<form id="formPelicula" style="background:#222; padding:20px; border-radius:10px; margin-bottom:40px;">
    <input type="hidden" name="action" id="actionPelicula" value="crear_pelicula">
    <input type="hidden" name="id" id="idPelicula" value="">

    <div style="display:flex; gap:15px; margin-bottom:15px;">
        <input type="text" name="titulo" id="titulo" placeholder="Título de la Película" required 
               style="flex:1; padding:12px; background:#333; color:white; border:1px solid #444; border-radius:4px;">
        <input type="number" name="duracion" id="duracion" placeholder="Duración (minutos)" required 
               style="flex:1; padding:12px; background:#333; color:white; border:1px solid #444; border-radius:4px;">
    </div>

    <div style="display:flex; gap:15px; margin-bottom:15px;">
        <input type="text" name="genero" id="genero" placeholder="Género (Ej. Acción, Terror)" required 
               style="flex:1; padding:12px; background:#333; color:white; border:1px solid #444; border-radius:4px;">
        <select name="clasificacion" id="clasificacion" 
                style="flex:1; padding:12px; background:#333; color:white; border:1px solid #444; border-radius:4px;">
            <option value="A">Todo Público (A)</option>
            <option value="B">Mayores de 12 (B)</option>
            <option value="C">Mayores de 18 (C)</option>
        </select>
    </div>

    <div style="display:flex; gap:15px; margin-bottom:15px;">
        <input type="text" name="idioma" id="idioma" placeholder="Idioma (Ej. Español, Subtitulada)" required 
               style="flex:1; padding:12px; background:#333; color:white; border:1px solid #444; border-radius:4px;">
        <div style="flex:1; background:#333; padding:12px; border:1px solid #444; border-radius:4px; display:flex; align-items:center;">
            <input type="file" name="imagen" id="imagen" accept="image/*" style="color:white; width:100%;">
        </div>
    </div>

    <textarea name="sinopsis" id="sinopsis" placeholder="Sinopsis de la película..." required 
              style="width:100%; padding:12px; background:#333; color:white; border:1px solid #444; border-radius:4px; margin-bottom:20px; height:100px; box-sizing:border-box; font-family:inherit; resize:vertical;"></textarea>

    <button type="submit" id="btnGuardar" 
            style="background:var(--cyan); color:black; padding:12px 25px; border:none; font-weight:bold; cursor:pointer; border-radius:5px; font-size:1rem;">
        Guardar Película
    </button>
</form>

<hr style="border:0; border-top:1px solid #333; margin-bottom:30px;">

<h2 class="title-cyan">Catálogo</h2>
<div id="listaPeliculas" class="movie-grid"></div>