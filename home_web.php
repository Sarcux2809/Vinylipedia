<?php
session_start();
if (!isset($_SESSION['user']) || empty($_SESSION['user']['username'])) {
    header('Location: index.html');
    exit();
}
$nombreUsuario = htmlspecialchars($_SESSION['user']['username']);

// Función para obtener vinilos de Discogs sin autenticación
function searchVinyls($artist_name) {
    $url = "https://api.discogs.com/database/search?q=" . urlencode($artist_name) . "&type=release&format=Vinyl";

    // Realizar la solicitud HTTP usando cURL
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE); // Obtener el código de estado HTTP
    curl_close($ch);

    // Verificar si hubo un error en la solicitud
    if ($http_code != 200) {
        echo "Error: La solicitud a la API falló con el código de estado HTTP $http_code.";
        return [];
    }

    // Depuración: mostrar la respuesta de la API
    echo "<pre>Respuesta de la API: " . htmlspecialchars($response) . "</pre>";

    // Procesar la respuesta JSON
    $data = json_decode($response, true);
    
    // Verificar si la respuesta es válida
    if (empty($data) || !isset($data['results'])) {
        echo "No se encontraron vinilos o hubo un error en la API.";
        return [];
    }

    $vinyls = [];
    foreach ($data['results'] as $vinyl) {
        $vinyls[] = [
            'id' => $vinyl['id'],
            'title' => $vinyl['title'] ?? 'Sin título',
            'cover_image' => $vinyl['cover_image'] ?? 'https://via.placeholder.com/200'
        ];
    }

    return $vinyls;
}

// Aquí puedes elegir el nombre del artista para obtener los vinilos (Ejemplo: 'Jose Jose')
$vinyls = searchVinyls('Jose Jose');
?>

<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="utf-8" />
  <meta content="width=device-width, initial-scale=1" name="viewport" />
  <title>Spinify</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet" />
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet" />
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
  </style>
</head>

<body class="bg-gradient-to-b from-[#2a2520] to-[#1a1a1a] min-h-screen flex flex-col items-stretch p-0">
  <div class="w-full h-full rounded-none bg-gradient-to-b from-[#2a2520] to-[#1a1a1a] p-6 text-white">

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
          <input class="bg-[#4a4540] rounded-full pl-4 pr-10 py-1 text-sm placeholder:text-[#7a7a7a] focus:outline-none" placeholder="Buscar" type="search" />
          <i class="fas fa-search absolute right-3 top-1/2 -translate-y-1/2 text-[#7a7a7a] text-xs"></i>
        </div>
        <button aria-label="Carrito de compras" class="text-white text-lg">
          <i class="fas fa-shopping-cart"></i>
        </button>
        <div class="relative group">
          <button class="bg-white text-black rounded px-3 py-1 font-semibold" id="userMenuButton">
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

    <!-- Contenido principal -->
    <section class="mb-6">
      <h2 class="font-semibold text-base mb-3 select-none">Las leyendas detrás del sonido</h2>
      <ul class="flex gap-6 overflow-x-auto scrollbar-hide py-1">
        <?php if (!empty($vinyls)): ?>
          <?php foreach ($vinyls as $vinyl): ?>
            <li class="flex flex-col items-center text-xs min-w-[56px] shrink-0">
              <div class="w-14 h-14 rounded-full">
                <img src="<?= $vinyl['cover_image'] ?>" alt="<?= $vinyl['title'] ?>" class="w-full h-full object-cover rounded">
              </div>
              <span class="mt-1 text-center"><?= $vinyl['title'] ?></span>
            </li>
          <?php endforeach; ?>
        <?php else: ?>
          <li class="text-center text-white">No se encontraron vinilos.</li>
        <?php endif; ?>
      </ul>
    </section>

    <!-- Lo más escuchado -->
    <section class="mb-6">
      <h3 class="font-semibold text-base mb-3 select-none">Lo más escuchado, lo más amado</h3>
      <div class="flex items-center gap-3 overflow-x-auto scrollbar-hide py-1">
        <?php if (!empty($vinyls)): ?>
          <?php foreach ($vinyls as $vinyl): ?>
            <div class="relative shrink-0 w-[180px] h-[90px] rounded-md bg-gray-600 overflow-hidden">
              <img src="<?= $vinyl['cover_image'] ?>" alt="<?= $vinyl['title'] ?>" class="w-full h-full object-cover rounded">
              <div class="absolute bottom-1 left-2 text-xs font-semibold">
                <p><?= $vinyl['title'] ?></p>
              </div>
            </div>
          <?php endforeach; ?>
        <?php else: ?>
          <div class="text-center text-white">No se encontraron vinilos.</div>
        <?php endif; ?>
      </div>
    </section>

  </div>
</body>

</html>
