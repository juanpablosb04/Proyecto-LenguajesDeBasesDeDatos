document.addEventListener("DOMContentLoaded", function () {
  const navbar = `
  <nav class="bg-[#242424] p-4 border-b-[2px] border-[#ff6600]"">
      <div class="max-w-screen-xl mx-auto flex justify-between items-center">
        <div class="text-white text-xl font-bold">
          <a href="/inicio.html">Gym-Nordico</a>
        </div>
        <div class="space-x-4 relative">
          <a href="/clientes.html" class="text-white hover:bg-[#ff6600] px-4 py-2 rounded-md transition">Clientes</a>
          <a href="/membresias.html" class="text-white hover:bg-[#ff6600] px-4 py-2 rounded-md transition">Membresias</a>
          <a href="/instructores.html" class="text-white hover:bg-[#ff6600] px-4 py-2 rounded-md transition">Entrenadores</a>
          <a href="/clases.html" class="text-white hover:bg-[#ff6600] px-4 py-2 rounded-md transition">Clases</a>
          <a href="/productos.html" class="text-white hover:bg-[#ff6600] px-4 py-2 rounded-md transition">Productos</a>
          
          <div class="relative inline-block">
            <button class="text-white hover:bg-[#ff6600] px-4 py-2 rounded-md transition" id="equiposButton">
              Equipos
          </button>
          <div id="equiposDropdown" class="hidden absolute bg-white shadow-lg rounded-md mt-2 w-40">
              <a href="/equipos_gimnasio.html" class="block px-4 py-2 text-gray-700 hover:bg-blue-500">Registrar Equipos</a>
              <a href="/mantenimiento_equipos.html" class="block px-4 py-2 text-gray-700 hover:bg-blue-500">Mantenimiento</a>
          </div>
          </div>

          <div class="relative inline-block">
              <button class="text-white hover:bg-[#ff6600] px-4 py-2 rounded-md transition" id="ventasButton">
                  Ventas
              </button>
              <div id="ventasDropdown" class="hidden absolute bg-white shadow-lg rounded-md mt-2 w-40">
                  <a href="/ventas_tienda.html" class="block px-4 py-2 text-gray-700 hover:bg-blue-500">Productos</a>
                  <a href="/pagos.html" class="block px-4 py-2 text-gray-700 hover:bg-blue-500">Membresías</a>
              </div>
          </div>

          <a href="/backend/logout.php" class="text-[#ff6600] hover:bg-white hover:text-[#242424] px-4 py-2 rounded-md transition">Logout</a>
        </div>    
      </div>
  </nav>`;

  document.getElementById("navbar-container").innerHTML = navbar;

  function setupDropdown(buttonId, dropdownId) {
    const button = document.getElementById(buttonId);
    const dropdown = document.getElementById(dropdownId);
    let hideTimeout;

    button.addEventListener("mouseenter", () => {
      clearTimeout(hideTimeout);
      dropdown.classList.remove("hidden");
    });

    button.addEventListener("mouseleave", () => {
      hideTimeout = setTimeout(() => dropdown.classList.add("hidden"), 800);
    });

    dropdown.addEventListener("mouseenter", () => {
      clearTimeout(hideTimeout);
    });

    dropdown.addEventListener("mouseleave", () => {
      hideTimeout = setTimeout(() => dropdown.classList.add("hidden"), 800);
    });
  }

  setupDropdown("equiposButton", "equiposDropdown");
  setupDropdown("ventasButton", "ventasDropdown");
});
