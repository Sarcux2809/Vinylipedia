<?php
session_start();
if (!isset($_SESSION['user'])) {
  header('Location: index.html');
  exit();
}
?>
<html lang="es">
 <head>
  <meta charset="utf-8"/>
  <meta content="width=device-width, initial-scale=1" name="viewport"/>
  <title>
   Spinify
  </title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet"/>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&amp;display=swap" rel="stylesheet"/>
  <style>
   body { font-family: "Inter", sans-serif; }
  </style>
 </head>
 <body class="bg-gradient-to-b from-[#2a2520] to-[#1a1a1a] min-h-screen flex flex-col items-stretch p-0">
  <div class="w-full h-full rounded-none bg-gradient-to-b from-[#2a2520] to-[#1a1a1a] p-6 text-white">
   <!-- Header -->
   <header class="flex flex-wrap items-center justify-between mb-6 gap-3">
    <div class="flex items-center gap-3">
     <div class="w-8 h-8 bg-gray-600 rounded-full"></div>
     <span class="text-lg font-normal select-none">Spinify</span>
     <nav class="flex gap-3 text-sm font-normal select-none">
      <button aria-current="page" class="bg-white text-black rounded px-3 py-1 leading-5 cursor-default">Inicio</button>
      <button class="hover:underline">Biblioteca</button>
     </nav>
    </div>
    <div class="flex items-center gap-3 flex-wrap">
     <div class="relative">
      <input class="bg-[#4a4540] rounded-full pl-4 pr-10 py-1 text-sm placeholder:text-[#7a7a7a] focus:outline-none" placeholder="Buscar" type="search"/>
      <i class="fas fa-search absolute right-3 top-1/2 -translate-y-1/2 text-[#7a7a7a] text-xs"></i>
     </div>
     <button aria-label="Carrito de compras" class="text-white text-lg">
      <i class="fas fa-shopping-cart"></i>
     </button>
     <div class="relative group">
      <button aria-expanded="false" aria-haspopup="true" class="bg-white text-black rounded px-3 py-1 font-semibold" id="userMenuButton">Juan A</button>
      <div class="absolute right-0 mt-1 w-36 bg-white text-black rounded shadow-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-opacity text-xs">
       <button class="block w-full text-left px-3 py-1 font-bold hover:bg-gray-200">Mi cuenta</button>
       <button class="block w-full text-left px-3 py-1 hover:bg-gray-200">Para Artistas</button>
       <button class="block w-full text-left px-3 py-1 hover:bg-gray-200">Cerrar sesión</button>
      </div>
     </div>
     <button class="bg-[#4a4540] rounded px-3 py-1 text-xs font-normal hover:bg-[#5a5550]">Register</button>
    </div>
   </header>

   <!-- Secciones principales -->
   <section class="mb-6">
    <h2 class="font-semibold text-base mb-3 select-none">Las leyendas detrás del sonido</h2>
    <ul class="flex gap-6 overflow-x-auto scrollbar-hide py-1">
     <li class="flex flex-col items-center text-xs min-w-[56px] shrink-0">
      <div class="w-14 h-14 bg-gray-600 rounded-full"></div>
      <span class="mt-1 text-center">José José</span>
     </li>
     <!-- Repetir para cada artista -->
     <!-- ... -->
    </ul>
   </section>

   <section class="mb-6">
    <h3 class="font-semibold text-base mb-3 select-none">Sonidos que marcaron historia</h3>
    <div class="grid grid-cols-3 gap-3">
     <button class="flex items-center justify-between bg-[#4a4540] rounded-md p-2 text-xs font-semibold select-none">
      <div class="flex items-center gap-2">
       <div class="w-6 h-6 rounded-full bg-pink-300"></div>
       1950s
      </div>
     </button>
     <!-- Repetir para cada década -->
     <!-- ... -->
    </div>
   </section>

   <section class="mb-6">
    <h3 class="font-semibold text-base mb-3 select-none">Lo más escuchado, lo más amado</h3>
    <div class="flex items-center gap-3 overflow-x-auto scrollbar-hide py-1">
     <div class="relative shrink-0 w-[180px] h-[90px] rounded-md bg-gray-600 overflow-hidden">
      <div class="absolute bottom-1 left-2 text-xs font-semibold">
       <p>The Dark Side of the Moon</p>
       <p class="text-[10px] font-normal">Pink Floyd</p>
      </div>
     </div>
     <!-- Repetir para cada álbum -->
     <!-- ... -->
    </div>
   </section>

   <!-- Otras secciones similares -->
   <!-- ... -->

  </div>
 </body>
</html>