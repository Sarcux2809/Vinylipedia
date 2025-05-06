<?php
$query = isset($_GET['query']) ? htmlspecialchars($_GET['query']) : '';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

// Configuración de la API de Discogs
$discogs_token = 'nWnZGawOdQuUikqnQWBKvXrjxeNvNmqJERKIQRGy';
$api_url = "https://api.discogs.com/database/search?q=" . urlencode($query) . "&type=release,artist&page=$page&per_page=24&key=WGntzCWnmIBNKDEtoqcp&secret=YhslOxfRtHXRVZNZwXlBxrCRUYFfgZTB";

// Función para hacer la petición a la API
function fetchFromDiscogs($url, $token) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Authorization: Discogs token=$token",
        "User-Agent: Vinylpedia/1.0"
    ]);
    $response = curl_exec($ch);
    curl_close($ch);
    return json_decode($response, true);
}

// Obtener resultados de la API
$results = [];
if (!empty($query)) {
    $results = fetchFromDiscogs($api_url, $discogs_token);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8" />
  <meta content="width=device-width, initial-scale=1" name="viewport" />
  <title>Resultados de búsqueda - Vinylpedia</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css"
    rel="stylesheet"
  />
  <link href="https://fonts.googleapis.com/css2?family=Inter&display=swap" rel="stylesheet" />
  <style>
    body {
      font-family: "Inter", sans-serif;
    }
    .dynamic-bg {
      background: linear-gradient(
        90deg,
        #4a5a4a 0%,
        #3a4a5a 20%,
        #5a4a3a 40%,
        #4a3a5a 60%,
        #3a5a4a 80%,
        #5a3a4a 100%
      );
    }
    @media (max-width: 767px) {
      .dynamic-bg {
        background: linear-gradient(135deg, #4a5a4a 0%, #3a4a5a 100%);
      }
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
<body class="bg-gradient-to-r from-[#3a2a22] via-[#4a3f3a] to-[#2a3a2a] min-h-screen flex flex-col items-center p-4">
  <div class="w-full rounded-xl p-4 md:p-6 flex flex-col gap-4 md:gap-6 shadow-[0_0_40px_10px_rgba(0,255,0,0.2)] dynamic-bg">
    <header class="flex flex-wrap items-center gap-4 md:gap-6">
      <div class="flex items-center gap-3 w-full md:w-auto justify-between">
        <div class="flex items-center gap-3">
          <img
            alt="Vinylpedia logo, stylized vinyl record icon"
            class="w-8 h-8 md:w-10 md:h-10"
            src="https://storage.googleapis.com/a1aa/image/20543dea-4330-4442-8ea1-3c4e906305c5.jpg"
          />
          <h1 class="text-white text-lg md:text-xl font-normal">Vinylpedia</h1>
        </div>
        <button class="md:hidden text-white text-xl" id="mobileMenuButton">
          <i class="fas fa-bars"></i>
        </button>
      </div>

      <nav class="hidden md:flex items-center gap-4 md:gap-6 md:ml-6 text-white text-sm w-full md:w-auto" id="mainNav">
        <a class="hover:underline" href="home_web.php">Inicio</a>
        <a class="hover:underline" href="#">Biblioteca</a>
      </nav>

      <div class="flex items-center gap-3 w-full md:w-auto">
        <form action="search_results.php" method="GET" class="relative w-full md:max-w-[220px]">
          <input
            name="query"
            class="w-full rounded-full bg-gray-600 bg-opacity-40 text-gray-300 placeholder-gray-400 text-sm py-2 pl-4 pr-10 focus:outline-none"
            placeholder="<?= htmlspecialchars($query) ?>"
            type="search"
            value="<?= htmlspecialchars($query) ?>"
            required
          />
          <button
            aria-label="Buscar"
            class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-200"
            type="submit"
          >
            <i class="fas fa-search"></i>
          </button>
        </form>
        <button
          aria-label="Carrito de compras"
          class="text-white text-xl ml-2 md:ml-4 hover:text-gray-300"
        >
          <i class="fas fa-shopping-cart"></i>
        </button>
        <button class="bg-white text-black rounded px-3 py-1 text-sm font-semibold">Invitado</button>
      </div>
    </header>

    <main class="flex flex-col md:flex-row gap-4 md:gap-6">
      <aside
        class="hidden md:block bg-gray-600 bg-opacity-40 rounded border border-gray-500 p-4 w-full md:w-[180px] lg:w-[220px] text-gray-300 text-xs"
        id="filtersSidebar"
      >
        <div class="mb-4">
          <p class="mb-2 font-normal">Filtros aplicados:</p>
          <div class="flex flex-wrap gap-2" id="appliedFilters"></div>
        </div>
        <div class="mb-4">
          <p class="mb-2 font-normal">Tipo</p>
          <div class="flex justify-between items-center mb-1">
            <label class="flex items-center gap-2 cursor-pointer">
              <input checked class="w-3 h-3 text-gray-900 bg-gray-100 border-gray-300 rounded" type="checkbox" name="type" value="release" />
              <span>Álbumes</span>
            </label>
            <span id="releaseCount"><?= isset($results['results']) ? count(array_filter($results['results'], function($item) { return $item['type'] === 'release'; })) : '0' ?></span>
          </div>
          <div class="flex justify-between items-center mb-1">
            <label class="flex items-center gap-2 cursor-pointer">
              <input checked class="w-3 h-3 text-gray-900 bg-gray-100 border-gray-300 rounded" type="checkbox" name="type" value="artist" />
              <span>Artistas</span>
            </label>
            <span id="artistCount"><?= isset($results['results']) ? count(array_filter($results['results'], function($item) { return $item['type'] === 'artist'; })) : '0' ?></span>
          </div>
        </div>
        <div class="mb-4">
          <p class="mb-2 font-normal">Formato</p>
          <label class="flex items-center gap-2 mb-1 cursor-pointer">
            <input checked class="w-3 h-3 text-gray-900 bg-gray-100 border-gray-300 rounded" type="checkbox" name="format" value="vinyl" />
            <span>Vinilo</span>
          </label>
          <label class="flex items-center gap-2 mb-1 cursor-pointer">
            <input checked class="w-3 h-3 text-gray-900 bg-gray-100 border-gray-300 rounded" type="checkbox" name="format" value="cd" />
            <span>CD</span>
          </label>
        </div>
      </aside>

      <section class="flex-1 flex flex-col gap-4">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-2">
          <h2 class="text-gray-300 font-semibold text-lg">
            Resultados para "<?= htmlspecialchars($query) ?>"
            <?php if (isset($results['pagination']['items'])): ?>
              <span class="text-sm text-gray-400">(<?= $results['pagination']['items'] ?> resultados)</span>
            <?php endif; ?>
          </h2>
          <div class="relative inline-block text-left">
            <select
              aria-label="Ordenar por"
              class="bg-gray-700 text-gray-300 text-xs rounded px-3 py-1 cursor-pointer"
              id="sortBy"
            >
              <option value="relevance" selected>Relevancia</option>
              <option value="title_asc">De la A a la Z</option>
              <option value="title_desc">De la Z a la A</option>
              <option value="year_asc">Año (más antiguo)</option>
              <option value="year_desc">Año (más reciente)</option>
            </select>
          </div>
        </div>

        <?php if (empty($query)): ?>
          <div class="text-center py-10 text-gray-400">
            <p>Por favor ingresa un término de búsqueda</p>
          </div>
        <?php elseif (!isset($results['results']) || empty($results['results'])): ?>
          <div class="text-center py-10 text-gray-400">
            <p>No se encontraron resultados para "<?= htmlspecialchars($query) ?>"</p>
          </div>
        <?php else: ?>
          <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-3 md:gap-4" id="resultsGrid">
            <?php foreach ($results['results'] as $item): ?>
              <?php if ($item['type'] === 'release' || $item['type'] === 'artist'): ?>
                <article 
                  class="bg-gray-700 rounded-md p-2 flex flex-col gap-1 w-full hover:bg-gray-600 transition-colors cursor-pointer"
                  onclick="viewDetails('<?= $item['id'] ?>', '<?= $item['type'] ?>')"
                >
                  <div class="relative">
                    <?php if ($item['type'] === 'release'): ?>
                      <img
                        alt="<?= htmlspecialchars($item['title']) ?>"
                        class="rounded-md w-full aspect-square object-cover vinyl-placeholder"
                        loading="lazy"
                        src="<?= !empty($item['cover_image']) ? $item['cover_image'] : 'https://upload.wikimedia.org/wikipedia/commons/3/3c/No-album-art.png' ?>"
                        onerror="this.src='https://upload.wikimedia.org/wikipedia/commons/3/3c/No-album-art.png'"
                      />
                      <?php if (!empty($item['year'])): ?>
                        <span class="absolute bottom-1 right-1 bg-black bg-opacity-70 text-white text-[8px] px-1 rounded">
                          <?= $item['year'] ?>
                        </span>
                      <?php endif; ?>
                    <?php else: ?>
                      <img
                        alt="<?= htmlspecialchars($item['title']) ?>"
                        class="rounded-md w-full aspect-square object-cover vinyl-placeholder"
                        loading="lazy"
                        src="<?= !empty($item['cover_image']) ? $item['cover_image'] : 'https://upload.wikimedia.org/wikipedia/commons/8/89/Portrait_Placeholder.png' ?>"
                        onerror="this.src='https://upload.wikimedia.org/wikipedia/commons/8/89/Portrait_Placeholder.png'"
                      />
                    <?php endif; ?>
                  </div>
                  <h3 class="text-white text-xs font-semibold truncate"><?= htmlspecialchars($item['title']) ?></h3>
                  <?php if ($item['type'] === 'release' && !empty($item['genre'])): ?>
                    <p class="text-gray-400 text-[9px] truncate">
                      <?= implode(', ', array_slice($item['genre'], 0, 2)) ?>
                      <?php if (count($item['genre']) > 2): ?>...<?php endif; ?>
                    </p>
                  <?php elseif ($item['type'] === 'artist'): ?>
                    <p class="text-gray-400 text-[9px] truncate">Artista</p>
                  <?php endif; ?>
                </article>
              <?php endif; ?>
            <?php endforeach; ?>
          </div>

          <?php if (isset($results['pagination']['pages']) && $results['pagination']['pages'] > 1): ?>
            <nav aria-label="Paginación" class="flex justify-center items-center gap-2 text-gray-500 text-xs mt-4">
              <?php if ($results['pagination']['page'] > 1): ?>
                <a href="search_results.php?query=<?= urlencode($query) ?>&page=<?= $results['pagination']['page'] - 1 ?>" class="hover:text-gray-300">
                  <i class="fas fa-chevron-left"></i>
                </a>
              <?php endif; ?>

              <?php 
              $start = max(1, $results['pagination']['page'] - 2);
              $end = min($results['pagination']['pages'], $results['pagination']['page'] + 2);
              
              if ($start > 1): ?>
                <a href="search_results.php?query=<?= urlencode($query) ?>&page=1" class="hover:text-gray-300">1</a>
                <?php if ($start > 2): ?>...<?php endif; ?>
              <?php endif; ?>

              <?php for ($i = $start; $i <= $end; $i++): ?>
                <a 
                  href="search_results.php?query=<?= urlencode($query) ?>&page=<?= $i ?>" 
                  class="<?= $i == $results['pagination']['page'] ? 'bg-gray-600 text-gray-300 rounded px-2 py-1 cursor-default' : 'hover:text-gray-300' ?>"
                >
                  <?= $i ?>
                </a>
              <?php endfor; ?>

              <?php if ($end < $results['pagination']['pages']): ?>
                <?php if ($end < $results['pagination']['pages'] - 1): ?>...<?php endif; ?>
                <a href="search_results.php?query=<?= urlencode($query) ?>&page=<?= $results['pagination']['pages'] ?>" class="hover:text-gray-300">
                  <?= $results['pagination']['pages'] ?>
                </a>
              <?php endif; ?>

              <?php if ($results['pagination']['page'] < $results['pagination']['pages']): ?>
                <a href="search_results.php?query=<?= urlencode($query) ?>&page=<?= $results['pagination']['page'] + 1 ?>" class="hover:text-gray-300">
                  <i class="fas fa-chevron-right"></i>
                </a>
              <?php endif; ?>
            </nav>
          <?php endif; ?>
        <?php endif; ?>
      </section>
    </main>
  </div>

  <script>
    document.getElementById('mobileMenuButton').addEventListener('click', function() {
      document.getElementById('mainNav').classList.toggle('hidden');
      document.getElementById('filtersSidebar').classList.toggle('hidden');
    });

    function viewDetails(id, type) {
      if (type === 'release') {
        window.location.href = `album_details_nologin.php?id=${id}`;
      } else if (type === 'artist') {
        window.location.href = `artist_details.php?id=${id}`;
      }
    }

    const checkboxes = document.querySelectorAll('input[type="checkbox"]');
    checkboxes.forEach(checkbox => {
      checkbox.addEventListener('change', filterResults);
    });

    document.getElementById('sortBy').addEventListener('change', sortResults);

    function filterResults() {
      const typeFilters = Array.from(document.querySelectorAll('input[name="type"]:checked')).map(el => el.value);
      const formatFilters = Array.from(document.querySelectorAll('input[name="format"]:checked')).map(el => el.value);
      
      const items = document.querySelectorAll('#resultsGrid article');
      items.forEach(item => {
        const itemType = item.querySelector('p.text-[9px]').textContent.toLowerCase().includes('artista') ? 'artist' : 'release';
        const shouldShow = typeFilters.includes(itemType);
        item.style.display = shouldShow ? 'flex' : 'none';
      });
      
      updateAppliedFilters(typeFilters, formatFilters);
    }

    function sortResults() {
      const sortBy = document.getElementById('sortBy').value;
      const container = document.getElementById('resultsGrid');
      const items = Array.from(container.children);
      
      items.sort((a, b) => {
        const titleA = a.querySelector('h3').textContent.toLowerCase();
        const titleB = b.querySelector('h3').textContent.toLowerCase();
        const yearA = parseInt(a.querySelector('span')?.textContent) || 0;
        const yearB = parseInt(b.querySelector('span')?.textContent) || 0;
        
        switch(sortBy) {
          case 'title_asc': return titleA.localeCompare(titleB);
          case 'title_desc': return titleB.localeCompare(titleA);
          case 'year_asc': return yearA - yearB;
          case 'year_desc': return yearB - yearA;
          default: return 0;
        }
      });
      
      items.forEach(item => container.appendChild(item));
    }

    function updateAppliedFilters(typeFilters, formatFilters) {
      const filtersContainer = document.getElementById('appliedFilters');
      filtersContainer.innerHTML = '';
      
      typeFilters.forEach(filter => {
        const span = document.createElement('span');
        span.className = 'bg-gray-700 rounded px-2 py-1 flex items-center gap-1';
        span.innerHTML = `
          ${filter === 'release' ? 'Álbumes' : 'Artistas'}
          <button class="text-gray-300 hover:text-white">
            <i class="fas fa-times text-[10px]"></i>
          </button>
        `;
        filtersContainer.appendChild(span);
      });
      
      formatFilters.forEach(filter => {
        const span = document.createElement('span');
        span.className = 'bg-gray-700 rounded px-2 py-1 flex items-center gap-1';
        span.innerHTML = `
          ${filter === 'vinyl' ? 'Vinilo' : 'CD'}
          <button class="text-gray-300 hover:text-white">
            <i class="fas fa-times text-[10px]"></i>
          </button>
        `;
        filtersContainer.appendChild(span);
      });
    }
  </script>
</body>
</html>