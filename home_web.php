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
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&amp;display=swap" rel="stylesheet" />
  <style>
    body {
      font-family: "Inter", sans-serif;
    }

    .scrollbar-hide::-webkit-scrollbar {
      display: none;
    }

    .scrollbar-hide {
      -ms-overflow-style: none;
      scrollbar-width: none;
    }
    
    .vinyl-placeholder {
      background: linear-gradient(45deg, #3a2a22 25%, #4a3a32 50%, #3a2a22 75%);
      background-size: 200% 200%;
      animation: gradient 2s ease infinite;
    }
    
    @keyframes gradient {
      0% { background-position: 0% 50%; }
      50% { background-position: 100% 50%; }
      100% { background-position: 0% 50%; }
    }
  </style>
</head>

<body class="bg-gradient-to-b from-[#3a2a22] via-[#1f2e2a] to-[#1a1f22] text-white min-h-screen p-4 sm:p-6 md:p-8">
  <main class="max-w-[1200px] mx-auto space-y-8">

    <!-- Header -->
    <header class="flex flex-wrap items-center justify-between gap-3 mb-8">
      <div class="flex items-center gap-3">
        <span class="text-lg font-normal">Vinylpedia</span>
        <nav class="flex gap-3 text-sm font-normal">
          <button class="bg-white text-black rounded px-3 py-1">Inicio</button>
          <button class="hover:underline">Biblioteca</button>
        </nav>
      </div>
      <div class="flex items-center gap-3 flex-wrap">
        <div class="relative">
          <input id="searchInput" class="bg-[#4a4540] rounded-full pl-4 pr-10 py-1 text-sm placeholder:text-[#7a7a7a] focus:outline-none" placeholder="Buscar" type="search" />
          <i class="fas fa-search absolute right-3 top-1/2 -translate-y-1/2 text-[#7a7a7a] text-xs"></i>
        </div>
        <button aria-label="Carrito" class="text-white text-lg"><i class="fas fa-shopping-cart"></i></button>
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

    <!-- Section: Las leyendas detrás del sonido -->
    <section>
      <h2 class="text-lg font-semibold mb-4">Las leyendas detrás del sonido</h2>
      <ul class="flex space-x-6 overflow-x-auto scrollbar-hide pb-2" id="legendList"></ul>
    </section>

    <!-- Section: Sonidos que marcaron historia -->
    <section>
      <h2 class="text-lg font-semibold mb-4">Sonidos que marcaron historia</h2>
      <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4" id="genresDecades"></div>
    </section>

    <!-- Section: Lo más escuchado, lo más amado -->
    <section>
      <div class="flex items-center justify-between mb-4">
        <h2 class="text-lg font-semibold">Lo más escuchado, lo más amado</h2>
        <div class="flex items-center space-x-2 text-gray-400 text-sm">
          <button class="bg-gray-600 rounded px-2 py-1">Más</button>
          <button class="p-1 hover:text-white"><i class="fas fa-arrow-left"></i></button>
          <button class="p-1 hover:text-white"><i class="fas fa-arrow-right"></i></button>
        </div>
      </div>
      <div class="flex space-x-3 overflow-x-auto scrollbar-hide" id="topList"></div>
    </section>

    <!-- Section: Del baúl sonoro -->
    <section>
      <div class="flex items-center justify-between mb-4">
        <h2 class="text-lg font-semibold">Del baúl sonoro a tus oídos: vinilos que debes escuchar.</h2>
        <div class="flex items-center space-x-2 text-gray-400 text-sm">
          <button class="bg-gray-600 rounded px-2 py-1">Más</button>
          <button class="p-1 hover:text-white"><i class="fas fa-arrow-left"></i></button>
          <button class="p-1 hover:text-white"><i class="fas fa-arrow-right"></i></button>
        </div>
      </div>
      <ul class="flex space-x-4 overflow-x-auto scrollbar-hide" id="baulList"></ul>
    </section>

    <!-- Section: Nuevas joyas -->
    <section>
      <div class="flex items-center justify-between mb-4">
        <h2 class="text-lg font-semibold">Nuevas joyas con alma de clásicos.</h2>
        <div class="flex items-center space-x-2 text-gray-400 text-sm">
          <button class="bg-gray-600 rounded px-2 py-1">Más</button>
          <button class="p-1 hover:text-white"><i class="fas fa-arrow-left"></i></button>
          <button class="p-1 hover:text-white"><i class="fas fa-arrow-right"></i></button>
        </div>
      </div>
      <ul class="flex space-x-4 overflow-x-auto scrollbar-hide" id="joyasList"></ul>
    </section>

    <!-- Section: Géneros -->
    <section>
      <h2 class="text-lg font-semibold mb-4">Sonidos que marcaron historia</h2>
      <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4" id="genresList"></div>
    </section>

  </main>

  <script>
    let allVinyls = [];
    const backupImages = {
      'default': 'https://upload.wikimedia.org/wikipedia/commons/3/3c/No-album-art.png',
      'José José': 'https://upload.wikimedia.org/wikipedia/commons/7/7c/Jose_Jose_1981.jpg',
      'Pink Floyd': 'https://upload.wikimedia.org/wikipedia/en/3/3b/Dark_Side_of_the_Moon.png',
      'Queen': 'https://upload.wikimedia.org/wikipedia/en/0/05/Queen_Queen.png',
      'Juan Gabriel': 'https://upload.wikimedia.org/wikipedia/commons/1/17/Juan_Gabriel_en_concierto.jpg',
      'Rocío Dúrcal': 'https://upload.wikimedia.org/wikipedia/commons/3/35/Roc%C3%ADo_D%C3%BArcal_en_1983.jpg'
    };

    // Función para verificar si una imagen existe
    async function checkImage(url) {
      return new Promise((resolve) => {
        const img = new Image();
        img.onload = () => resolve(true);
        img.onerror = () => resolve(false);
        img.src = url;
      });
    }

    // Función para obtener la mejor imagen disponible
    async function getBestImage(primaryUrl, artistName) {
      // Primero intentamos con la imagen principal
      if (await checkImage(primaryUrl)) {
        return primaryUrl;
      }
      
      // Si falla, buscamos una imagen de respaldo para el artista
      if (artistName && backupImages[artistName]) {
        if (await checkImage(backupImages[artistName])) {
          return backupImages[artistName];
        }
      }
      
      // Si todo falla, usamos la imagen por defecto
      return backupImages['default'];
    }

    fetch('vinyls.json')
      .then(res => res.json())
      .then(data => {
        allVinyls = data.todos || [];

        // Leyendas
        renderList(data.leyendas, 'legendList', true);

        // Décadas
        renderDecades(data.decadas, 'genresDecades');

        // Más escuchados
        renderAlbums(data.mas_escuchados, 'topList');

        // Baúl
        renderBaul(data.baul, 'baulList');

        // Joyas
        renderJoyas(data.joyas, 'joyasList');

        // Géneros
        renderGenres(data.generos, 'genresList');
      });

    async function renderList(list, id, isLegend = false) {
      const container = document.getElementById(id);
      
      // Primero mostramos placeholders
      container.innerHTML = list.map(item => `
        <li class="flex flex-col items-center min-w-[72px]">
          <div class="vinyl-placeholder w-14 h-14 rounded-full mb-2"></div>
          <span class="text-xs text-white/80">${item.nombre}</span>
        </li>
      `).join('');
      
      // Luego cargamos las imágenes reales
      for (let i = 0; i < list.length; i++) {
        const item = list[i];
        const bestImage = await getBestImage(item.imagen, item.nombre);
        
        const li = container.children[i];
        if (li) {
          li.innerHTML = `
            <div class="flex flex-col items-center">
              <img src="${bestImage}" 
                   class="w-14 h-14 rounded-full object-cover mb-2" 
                   alt="${item.nombre}"
                   onerror="this.src='${backupImages['default']}'">
              <span class="text-xs text-white/80">${item.nombre}</span>
            </div>
          `;
        }
      }
    }

    function renderDecades(decades, id) {
      const container = document.getElementById(id);
      container.innerHTML = decades.map(decade => `
        <div class="flex items-center bg-[#4a4a4a] rounded-lg shadow-md overflow-hidden">
          <div class="flex items-center justify-center w-12 h-12 ${decade.color} rounded-l-lg flex-shrink-0 ml-2">
            <span class="text-xs font-semibold ${decade.color.includes('300') || decade.color.includes('400') ? 'text-gray-900' : 'text-white'}">
              ${decade.nombre}
            </span>
          </div>
        </div>
      `).join('');
    }

    async function renderAlbums(list, id) {
      const container = document.getElementById(id);
      
      // Mostrar placeholders primero
      container.innerHTML = list.map(album => `
        <div class="flex-shrink-0 w-[280px] rounded-lg overflow-hidden relative vinyl-placeholder" style="height: 128px;"></div>
      `).join('');
      
      // Luego cargar las imágenes reales
      for (let i = 0; i < list.length; i++) {
        const album = list[i];
        const bestImage = await getBestImage(album.imagen, album.artista);
        
        const div = container.children[i];
        if (div) {
          div.innerHTML = `
            <div class="w-full h-32 object-cover relative">
              <img src="${bestImage}" 
                   class="w-full h-full object-cover" 
                   alt="${album.titulo}"
                   onerror="this.src='${backupImages['default']}'">
              ${album.badge ? `
                <div class="absolute top-0 left-0 bg-blue-600 text-white text-[10px] font-bold uppercase px-2 py-1 rounded-br-lg z-10 rotate-[-90deg] origin-top-left" style="width: 100px; height: 20px; line-height: 20px;">
                  ${album.badge}
                </div>
              ` : ''}
              <div class="absolute bottom-2 left-2 flex items-center space-x-2">
                <button class="w-7 h-7 bg-gray-600 bg-opacity-70 rounded-full flex items-center justify-center hover:bg-gray-500">
                  <i class="fas fa-play text-white text-xs"></i>
                </button>
                <div>
                  <p class="text-xs font-semibold text-white">${album.titulo}</p>
                  <p class="text-xs text-white/70">${album.artista}</p>
                </div>
              </div>
            </div>
          `;
        }
      }
    }

    async function renderBaul(list, id) {
      const container = document.getElementById(id);
      
      // Placeholders primero
      container.innerHTML = list.map(item => `
        <li class="flex-shrink-0 w-20 rounded-lg overflow-hidden vinyl-placeholder" style="height: 100px;"></li>
      `).join('');
      
      // Imágenes reales después
      for (let i = 0; i < list.length; i++) {
        const item = list[i];
        const bestImage = await getBestImage(item.imagen, item.artista);
        
        const li = container.children[i];
        if (li) {
          li.innerHTML = `
            <div class="w-full h-20 rounded-lg overflow-hidden bg-[${item.color}]">
              <img src="${bestImage}" 
                   class="w-full h-20 object-cover" 
                   alt="${item.titulo}"
                   onerror="this.src='${backupImages['default']}'">
              <div class="px-1 py-1 text-xs ${item.color === '#f0f0f0' ? 'text-black' : 'text-white/90'}">
                <p class="font-semibold truncate">${item.titulo}</p>
                <p class="truncate">${item.artista}</p>
              </div>
            </div>
          `;
        }
      }
    }

    function renderGenres(genres, id) {
      const container = document.getElementById(id);
      container.innerHTML = genres.map(genre => `
        <div class="flex items-center bg-[#4a4a4a] rounded-lg shadow-md overflow-hidden">
          <div class="flex items-center justify-center w-32 h-12 ${genre.color} rounded-l-lg flex-shrink-0 ml-2">
            <span class="text-xs font-semibold ${genre.color.includes('300') || genre.color.includes('400') ? 'text-gray-900' : 'text-white'}">
              ${genre.nombre}
            </span>
          </div>
        </div>
      `).join('');
    }

    async function renderJoyas(list, id) {
      const container = document.getElementById(id);
      
      // Placeholders primero
      container.innerHTML = list.map(item => `
        <li class="flex-shrink-0 w-20 rounded-lg overflow-hidden vinyl-placeholder" style="height: 100px;"></li>
      `).join('');
      
      // Imágenes reales después
      for (let i = 0; i < list.length; i++) {
        const item = list[i];
        const bestImage = await getBestImage(item.imagen, item.artista);
        
        const li = container.children[i];
        if (li) {
          li.innerHTML = `
            <div class="w-full h-20 rounded-lg overflow-hidden bg-[${item.color}]">
              <img src="${bestImage}" 
                   class="w-full h-20 object-cover" 
                   alt="${item.titulo}"
                   onerror="this.src='${backupImages['default']}'">
              <div class="px-1 py-1 text-xs ${item.color === '#f0f0f0' ? 'text-black' : 'text-white/90'}">
                <p class="font-semibold truncate">${item.titulo}</p>
                <p class="truncate">${item.artista}</p>
              </div>
            </div>
          `;
        }
      }
    }

    document.getElementById('searchInput').addEventListener('input', function(e) {
      const query = e.target.value.toLowerCase();
      const filtered = allVinyls.filter(v =>
        v.titulo?.toLowerCase().includes(query) ||
        v.artista?.toLowerCase().includes(query)
      );
      console.log('Resultados de búsqueda:', filtered);
    });
  </script>
</body>

</html>