<div class="container">
    <h1 class="titulo">CARTELERA DISPONIBLE</h1>
    <div class="grid-peliculas">
        @foreach($peliculas as $p)
        <div class="card-pelicula">
            <img src="/img/posters/{{ $p->imagenPoster }}" alt="{{ $p->titulo }}">
            <h3>{{ $p->titulo }}</h3>
            <p>{{ $p->genero }} | {{ $p->duracion }} min</p>
            <a href="/ver-funciones/{{ $p->idPelicula }}" class="btn-comprar">VER FUNCIONES</a>
        </div>
        @endforeach
    </div>
</div>