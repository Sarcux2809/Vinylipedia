<?php
session_start();
if (!isset($_SESSION['user']) || empty($_SESSION['user']['username'])) {
  header('Location: index.html');
  exit();
}

$nombreUsuario = htmlspecialchars($_SESSION['user']['username']);
$albumId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($albumId === 0) {
  header('Location: home_web.php');
  exit();
}

// Configuración de la API de Discogs
$discogs_token = 'TU_TOKEN_DE_DISCOGS';
$album_url = "https://api.discogs.com/releases/$albumId";
$artist_albums_url = ''; // Se definirá después de obtener los datos del álbum

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

// Obtener datos del álbum
$album = fetchFromDiscogs($album_url, $discogs_token);

// Si no se encontró el álbum, redirigir
if (isset($album['message'])) {
    header('Location: home_web.php');
  exit();
}

// Obtener más álbumes del artista si está disponible
$artistAlbums = [];
if (isset($album['artists'][0]['id'])) {
    $artist_id = $album['artists'][0]['id'];
    $artist_albums_url = "https://api.discogs.com/artists/$artist_id/releases?per_page=6";
    $artistAlbums = fetchFromDiscogs($artist_albums_url, $discogs_token);
}

// Función para formatear la duración de las pistas
function formatDuration($duration) {
    if (empty($duration)) return '?:??';
    
    $parts = explode(':', $duration);
    if (count($parts) === 1) {
        return "0:{$parts[0]}";
    }
    return $duration;
}

// Función para obtener el año del álbum
function getAlbumYear($album) {
    if (isset($album['year'])) {
        return $album['year'];
    }
    if (isset($album['released'])) {
        return substr($album['released'], 0, 4);
    }
    return 'Desconocido';
}

// Función para contar canciones y calcular duración total
function getTrackInfo($album) {
    $totalTracks = 0;
    $totalDuration = 0;
    
    if (isset($album['tracklist'])) {
        $totalTracks = count($album['tracklist']);
        foreach ($album['tracklist'] as $track) {
            if (isset($track['duration'])) {
                $parts = explode(':', $track['duration']);
                $minutes = isset($parts[0]) ? (int)$parts[0] : 0;
                $seconds = isset($parts[1]) ? (int)$parts[1] : 0;
                $totalDuration += $minutes * 60 + $seconds;
            }
        }
    }
    
    $minutes = floor($totalDuration / 60);
    $seconds = $totalDuration % 60;
    
    return [
        'count' => $totalTracks,
        'duration' => "$minutes minutos" . ($seconds > 0 ? " $seconds segundos" : "")
    ];
}

$trackInfo = getTrackInfo($album);
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8"/>
  <meta content="width=device-width, initial-scale=1" name="viewport"/>
  <title><?= htmlspecialchars($album['title'] ?? 'Álbum') ?> - Vinylpedia</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet"/>
  <style>
    /* Custom scrollbar for horizontal scroll */
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
<body class="bg-gradient-to-b from-[#1a1a1f] via-[#2a2a33] to-[#0f0f12] min-h-screen p-4 font-sans text-white">
<div class="relative w-full max-w-screen-xl mx-auto ...">
    <div class="absolute top-2 left-2 bg-black bg-opacity-60 text-white text-[10px] px-2 py-[2px] rounded select-none">
    </div>
    
    <header class="flex flex-wrap items-center justify-between mb-6 gap-3">
      <div class="flex items-center gap-3 flex-shrink-0">
        <img alt="Logo de Vinylpedia" class="w-8 h-8" height="32" src="https://storage.googleapis.com/a1aa/image/de7cd6df-ec20-4283-1b38-e3ae88ee347d.jpg" width="32"/>
        <span class="text-white text-lg font-light select-none">
          Vinylpedia
        </span>
        <nav class="flex items-center gap-2 text-xs text-white/80 font-light select-none">
          <a href="home_web.php" class="bg-white text-[#a94f4a] rounded-md px-3 py-1 leading-none">
            Inicio
          </a>
          <span>
            Biblioteca
          </span>
        </nav>
      </div>
      <div class="flex items-center gap-3 flex-grow max-w-[300px] md:max-w-[400px]">
        <form action="search_results.php" method="GET" class="relative flex-grow">
          <input 
            name="query"
            class="w-full rounded-full bg-white/20 text-white placeholder-white/50 text-xs py-1 pl-4 pr-8 focus:outline-none" 
            placeholder="Buscar" 
            type="search"
          />
          <button type="submit" class="absolute right-3 top-1/2 -translate-y-1/2 text-white/50 text-xs">
            <i class="fas fa-search"></i>
          </button>
        </form>
        <div class="relative group">
          <button class="bg-white text-black text-xs rounded px-3 py-1 leading-none select-none font-semibold">
            <?= $nombreUsuario ?>
          </button>
          <div class="absolute right-0 mt-1 w-36 bg-white text-black rounded shadow-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-opacity text-xs">
            <button class="block w-full text-left px-3 py-1 font-bold hover:bg-gray-200">Mi cuenta</button>
            <button class="block w-full text-left px-3 py-1 hover:bg-gray-200">Para Artistas</button>
            <a href="logout.php" class="block w-full text-left px-3 py-1 hover:bg-gray-200">Cerrar sesión</a>
          </div>
        </div>
      </div>
    </header>
    
    <section class="flex flex-col md:flex-row gap-6 md:gap-8 mb-6">
      <img 
        alt="<?= htmlspecialchars($album['title'] ?? 'Portada del álbum') ?>" 
        class="w-44 h-44 object-cover rounded-md flex-shrink-0" 
        height="180" 
        src="<?= !empty($album['images'][0]['uri']) ? $album['images'][0]['uri'] : 'https://upload.wikimedia.org/wikipedia/commons/3/3c/No-album-art.png' ?>" 
        width="180"
        onerror="this.src='https://upload.wikimedia.org/wikipedia/commons/3/3c/No-album-art.png'"
      />
      <div class="flex flex-col text-white text-xs md:text-sm max-w-xl">
        <h2 class="text-lg md:text-xl font-bold mb-1">
          <?= htmlspecialchars($album['title'] ?? 'Título desconocido') ?>
        </h2>
        <h3 class="font-semibold mb-2">
          <?= isset($album['artists'][0]['name']) ? htmlspecialchars($album['artists'][0]['name']) : 'Artista desconocido' ?>
        </h3>
        <p class="mb-3 leading-tight text-white/80">
          <?php if (!empty($album['notes'])): ?>
            <?= nl2br(htmlspecialchars(substr($album['notes'], 0, 300) . (strlen($album['notes']) > 300 ? '...' : ''))) ?>
            <?php else: ?>
            Información detallada no disponible.
          <?php endif; ?>
        </p>
        <p class="mb-3 font-semibold text-[10px] md:text-xs">
          <?= getAlbumYear($album) ?>
          <br/>
          <?= $trackInfo['count'] ?> canciones • <?= $trackInfo['duration'] ?>
        </p>
        <div class="flex gap-2 flex-wrap">
          <button class="bg-white bg-opacity-30 text-white text-xs rounded-md px-3 py-1 leading-none select-none hover:bg-opacity-50 transition">
            Escuchar
          </button>
          <button class="bg-white bg-opacity-20 text-white text-xs rounded-md px-3 py-1 leading-none select-none hover:bg-opacity-40 transition">
            Agregar a la biblioteca
          </button>
          <?php if (!empty($album['uri'])): ?>
            <a href="<?= htmlspecialchars($album['uri']) ?>" target="_blank" class="text-red-500 text-xs rounded-md px-3 py-1 leading-none select-none hover:underline transition">
              Comprar ahora
            </a>
          <?php endif; ?>
          <button aria-label="Carrito de compras" class="bg-white text-black rounded-md px-2 py-1 leading-none select-none hover:bg-gray-200 transition">
            <i class="fas fa-shopping-cart text-xs"></i>
          </button>
        </div>
      </div>
    </section>
    
    <?php if (!empty($album['tracklist'])): ?>
      <section class="mb-6">
        <ol class="space-y-2">
          <?php foreach ($album['tracklist'] as $index => $track): ?>
            <li class="flex items-center bg-gray-700 bg-opacity-70 rounded-md p-3 text-xs md:text-sm select-none">
              <div class="flex-shrink-0 w-6 h-6 rounded-full bg-gray-500 text-center leading-6 text-white font-semibold mr-3">
                <?= $index + 1 ?>
              </div>
              <div class="flex-grow font-semibold">
                <?= htmlspecialchars($track['title']) ?>
              </div>
              <div class="w-12 text-right font-semibold">
                <?= formatDuration($track['duration'] ?? '') ?>
              </div>
              <div class="flex-shrink-0 ml-4 text-gray-400 cursor-pointer select-none">
                <i class="fas fa-ellipsis-h"></i>
              </div>
            </li>
          <?php endforeach; ?>
        </ol>
      </section>
    <?php endif; ?>
    
    <?php if (!empty($album['extraartists']) || !empty($album['labels']) || !empty($album['released'])): ?>
      <section class="text-[9px] md:text-[10px] text-gray-400 mb-8 max-w-[700px] leading-tight select-text">
        <?php if (!empty($album['labels'])): ?>
          <p>
            <?php 
            $labels = array_map(function($label) {
              return htmlspecialchars($label['name']);
            }, $album['labels']);
            echo 'Publicado por: ' . implode(', ', $labels);
            ?>
          </p>
        <?php endif; ?>
        
        <?php if (!empty($album['extraartists'])): ?>
          <p>
            Participaciones destacadas: 
            <?php
            $artists = [];
            foreach ($album['extraartists'] as $artist) {
              $artists[] = htmlspecialchars($artist['name']) . ' (' . htmlspecialchars($artist['role']) . ')';
            }
            echo implode(', ', array_slice($artists, 0, 3));
            echo count($artists) > 3 ? '...' : '';
            ?>
          </p>
        <?php endif; ?>
        
        <?php if (!empty($album['released'])): ?>
          <p>
            Fecha de lanzamiento: <?= htmlspecialchars($album['released']) ?>
          </p>
        <?php endif; ?>
        
        <?php if (!empty($album['genres'])): ?>
          <p>
            Géneros: <?= htmlspecialchars(implode(', ', $album['genres'])) ?>
          </p>
        <?php endif; ?>
        
        <?php if (!empty($album['styles'])): ?>
          <p>
            Estilos: <?= htmlspecialchars(implode(', ', $album['styles'])) ?>
          </p>
        <?php endif; ?>
        
        <p>
          Información proporcionada por Discogs.
        </p>
      </section>
    <?php endif; ?>
    
    <?php if (!empty($artistAlbums['releases'])): ?>
      <section>
        <div class="flex items-center justify-between mb-4">
          <h4 class="font-bold text-white text-sm select-none">
            Más de <?= htmlspecialchars($album['artists'][0]['name'] ?? 'este artista') ?>
          </h4>
          <?php if (isset($album['artists'][0]['id'])): ?>
            <a 
              href="artist_details.php?id=<?= $album['artists'][0]['id'] ?>" 
              class="flex items-center gap-1 text-gray-400 text-xs select-none hover:text-white"
            >
              <span class="bg-gray-600 bg-opacity-40 rounded px-2 py-1 leading-none">
                Ver todos
              </span>
            </a>
          <?php endif; ?>
        </div>
        <div class="flex gap-3 overflow-x-auto scrollbar-hide pb-2">
          <?php foreach ($artistAlbums['releases'] as $release): ?>
            <?php if ($release['id'] != $albumId): // No mostrar el mismo álbum ?>
              <article 
                class="min-w-[90px] max-w-[90px] rounded-md bg-gray-700 bg-opacity-70 p-2 flex flex-col select-none hover:bg-gray-600 transition-colors cursor-pointer"
                onclick="window.location.href='album_details.php?id=<?= $release['id'] ?>'"
              >
                <img 
                  alt="<?= htmlspecialchars($release['title'] ?? 'Álbum') ?>" 
                  class="rounded-md mb-1 w-full aspect-square object-cover" 
                  src="<?= !empty($release['thumb']) ? $release['thumb'] : 'https://upload.wikimedia.org/wikipedia/commons/3/3c/No-album-art.png' ?>"
                  onerror="this.src='https://upload.wikimedia.org/wikipedia/commons/3/3c/No-album-art.png'"
                />
                <strong class="text-[9px] text-white font-semibold leading-tight truncate">
                  <?= htmlspecialchars($release['title'] ?? 'Álbum') ?>
                </strong>
                <span class="text-[8px] text-gray-300 truncate">
                  <?= htmlspecialchars($album['artists'][0]['name'] ?? 'Artista') ?>
                </span>
              </article>
            <?php endif; ?>
          <?php endforeach; ?>
        </div>
      </section>
    <?php endif; ?>
  </div>
</body>
</html>