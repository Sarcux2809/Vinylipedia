<?php
session_start();
if (!isset($_SESSION['user']) || empty($_SESSION['user']['username'])) {
    header('Location: index.html');
    exit();
}
$nombreUsuario = htmlspecialchars($_SESSION['user']['username']);
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8" />
  <meta content="width=device-width, initial-scale=1" name="viewport" />
  <title>Vinylpedia</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet" />
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet" />
  <style>
    body { font-family: "Inter", sans-serif; }
    .scrollbar-hide::-webkit-scrollbar { display: none; }
    .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
  </style>
</head>

<body class="bg-gradient-to-b from-[#2a2520] to-[#1a1a1a] min-h-screen flex flex-col items-stretch p-0">
  <div class="w-full h-full bg-gradient-to-b from-[#2a2520] to-[#1a1a1a] p-6 text-white">

    <!-- Header -->
    <header class="flex flex-wrap items-center justify-between mb-6 gap-3">
      <div class="flex items-center gap-3">
        <div class="w-8 h-8 bg-gray-600 rounded-full"></div>
        <span class="text-lg font-normal select-none">Vinylpedia</span>
        <nav class="flex gap-3 text-sm font-normal select-none">
          <button aria-current="page" class="bg-white text-black rounded px-3 py-1 leading-5 cursor-default">Inicio</button>
          <button class="hover:underline">Biblioteca</button>
        </nav>
      </div>
      <div class="flex items-center gap-3 flex-wrap">
        <div class="relative">
          <input id="searchInput" class="bg-[#4a4540] rounded-full pl-4 pr-10 py-1 text-sm placeholder:text-[#7a7a7a] focus:outline-none" placeholder="Buscar" type="search" />
          <i class="fas fa-search absolute right-3 top-1/2 -translate-y-1/2 text-[#7a7a7a] text-xs"></i>
        </div>
        <button aria-label="Carrito de compras" class="text-white text-lg"><i class="fas fa-shopping-cart"></i></button>
        <div class="relative group">
          <button class="bg-white text-black rounded px-3 py-1 font-semibold"><?= $nombreUsuario ?></button>
          <div class="absolute right-0 mt-1 w-36 bg-white text-black rounded shadow-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-opacity text-xs">
            <button class="block w-full text-left px-3 py-1 font-bold hover:bg-gray-200">Mi cuenta</button>
            <button class="block w-full text-left px-3 py-1 hover:bg-gray-200">Para Artistas</button>
            <a href="logout.php" class="block w-full text-left px-3 py-1 hover:bg-gray-200">Cerrar sesión</a>
          </div>
        </div>
      </div>
    </header>

    <!-- Secciones -->
    <section class="mb-6">
      <h2 class="font-semibold text-base mb-3 select-none">Explora vinilos</h2>
      <div id="vinylList" class="flex gap-6 overflow-x-auto scrollbar-hide py-1"></div>
    </section>

    <section class="mb-6">
      <h2 class="font-semibold text-base mb-3 select-none">Las leyendas detrás del sonido</h2>
      <ul class="flex gap-6 overflow-x-auto scrollbar-hide py-1" id="legendList"></ul>
    </section>

    <section class="mb-6">
      <h2 class="font-semibold text-base mb-3 select-none">Sonidos que marcaron historia</h2>
      <div class="flex gap-3" id="genresList"></div>
    </section>

    <section class="mb-6">
      <h2 class="font-semibold text-base mb-3 select-none">Lo más escuchado, lo más amado</h2>
      <div id="topList" class="flex gap-6 overflow-x-auto scrollbar-hide py-1"></div>
    </section>

    <section class="mb-6">
      <h2 class="font-semibold text-base mb-3 select-none">Del baúl sonoro a tus oídos: vinilos que debes escuchar</h2>
      <div id="baulList" class="flex gap-6 overflow-x-auto scrollbar-hide py-1"></div>
    </section>

    <section class="mb-6">
      <h2 class="font-semibold text-base mb-3 select-none">Nuevas joyas con alma de clásicos</h2>
      <div id="joyasList" class="flex gap-6 overflow-x-auto scrollbar-hide py-1"></div>
    </section>

    <section class="mb-6">
      <h2 class="font-semibold text-base mb-3 select-none">Sonidos que marcaron historia</h2>
      <div class="flex gap-3" id="genresList2"></div>
    </section>

  </div>

  <script>
    let allVinyls = [];

    fetch('vinyls.json')
      .then(res => res.json())
      .then(data => {
        allVinyls = data.todos;
        renderVinyls(allVinyls);

        renderList(data.leyendas, 'legendList', true);
        renderList(data.mas_escuchados, 'topList');
        renderList(data.baul, 'baulList');
        renderList(data.joyas, 'joyasList');
        renderGenres(data.generos, 'genresList');
        renderGenres(data.generos, 'genresList2');
      });

    function renderVinyls(list) {
      const container = document.getElementById('vinylList');
      container.innerHTML = '';
      if (list.length === 0) {
        container.innerHTML = '<p class="text-white">No se encontraron resultados.</p>';
        return;
      }
      list.forEach(v => {
        const item = document.createElement('div');
        item.className = "flex-shrink-0 w-40 text-center";
        item.innerHTML = `
          <img src="${v.imagen}" alt="${v.titulo}" class="rounded mb-2 w-full">
          <p class="text-sm font-semibold">${v.titulo}</p>
          <p class="text-xs text-gray-400">${v.artista}</p>
        `;
        container.appendChild(item);
      });
    }

    function renderList(list, id, isCircle = false) {
      const container = document.getElementById(id);
      container.innerHTML = '';
      list.forEach(v => {
        const item = document.createElement(isCircle ? 'li' : 'div');
        item.className = isCircle ? "text-center" : "flex-shrink-0 w-40 text-center";
        item.innerHTML = `
          <img src="${v.imagen}" alt="${v.titulo || v.nombre}" class="${isCircle ? 'rounded-full w-14 h-14 mb-1 object-cover' : 'rounded mb-2 w-full'}">
          <p class="text-sm font-semibold">${v.titulo || ''}</p>
          <p class="text-xs text-gray-400">${v.artista || v.nombre}</p>
        `;
        container.appendChild(item);
      });
    }

    function renderGenres(genres, id) {
      const container = document.getElementById(id);
      container.innerHTML = '';
      genres.forEach(genre => {
        const item = document.createElement('div');
        item.className = 'bg-[#3a3631] text-white rounded px-4 py-2 text-sm';
        item.textContent = genre;
        container.appendChild(item);
      });
    }

    document.getElementById('searchInput').addEventListener('input', function () {
      const query = this.value.toLowerCase();
      const filtered = allVinyls.filter(v =>
        v.titulo.toLowerCase().includes(query) ||
        v.artista.toLowerCase().includes(query)
      );
      renderVinyls(filtered);
    });
  </script>
</body>
</html>
