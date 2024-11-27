<template>
  <nav class="navbar">
    <div class="navbar-container">
      <!-- Logo personalizado con "BodyFit" -->
      <a href="/" class="logo">
        <span class="logo-body">Body</span><span class="logo-fit">Fit</span>
      </a>

      <!-- Icono de hamburguesa para pantallas pequeñas -->
      <div class="hamburger-icon" @click="toggleMenu">
        <i class="fas fa-bars"></i>
      </div>

      <!-- Barra de búsqueda con ícono -->
     
  <div class="search-wrapper">
    <!-- Botón de ícono de búsqueda -->
    <button class="search-icon" @click="toggleSearchBar">
      <i class="fas fa-search"></i>
    </button>

    <!-- Barra de búsqueda -->
    <div v-if="isSearchBarOpen" class="search-bar">
      <input
        type="text"
        class="search-input"
        placeholder="Buscar..."
        v-model="searchQuery"
        @input="performSearch"
      />
      <button @click="clearSearch" class="search-clear-btn">
        <i class="fas fa-times" v-if="searchQuery"></i>
      </button>

      <!-- Resultados de búsqueda -->
      <ul v-if="filteredResults.length" class="dropdown-search-results">
        <li v-for="(result, index) in filteredResults" :key="index">
          <a :href="result.ruta">
            <i :class="result.icono"></i> {{ result.nombre }}
          </a>
        </li>
      </ul>
    </div>
  </div>

      <!-- Menú principal con opciones desplegables tipo acordeón -->
      <ul class="nav-links" :class="{ 'active': isMenuOpen }">
        <li
          class="nav-item"
          v-for="(menu, menuName) in menuOptions"
          :key="menuName"
        >
          <button
            @mouseenter="showMenu(menuName)"
            @mouseleave="hideMenu"
            class="accordion-toggle"
          >
            <i :class="menu.icono"></i> {{ menuName }}
          </button>
          <ul
            v-if="activeMenu === menuName"
            class="dropdown-content"
            @mouseenter="keepMenuOpen(menuName)"
            @mouseleave="hideMenu"
          >
          <li v-for="opcion in menu.opciones" :key="opcion.nombre">
  <a :href="opcion.ruta">
    <i :class="opcion.icono"></i> {{ opcion.nombre }}
  </a>
</li>
          </ul>
        </li>
      </ul>

      <!-- Control de estilo y modo -->
      <div class="user-controls">
        <div class="custom-select-wrapper">
          <label for="style-select">
            TEMAS <i class="fas fa-chevron-down"></i>
          </label>
          <select
            id="style-select"
            v-model="selectedStyle"
            @change="updateStyles"
          >
            <option value="defecto">Cliente</option>
            <option value="ninos">Niños</option>
            <option value="jovenes">Jóvenes</option>
            <option value="adultos">Adultos</option>
          </select>
        </div>

        <div class="mode-switch">
          <label class="swap swap-rotate">
            <input
              type="checkbox"
              class="theme-controller"
              v-model="isDarkMode"
              @change="updateStyles"
            />
            <svg
              class="swap-off h-10 w-10 fill-current sol"
              xmlns="http://www.w3.org/2000/svg"
              viewBox="0 0 24 24"
              v-if="!isDarkMode"
            >
              <path
                d="M5.64,17l-.71.71a1,1,0,0,0,0,1.41,1,1,0,0,0,1.41,0l.71-.71A1,1,0,0,0,5.64,17ZM5,12a1,1,0,0,0-1-1H3a1,1,0,0,0,0,2H4A1,1,0,0,0,5,12Zm7-7a1,1,0,0,0,1-1V3a1,1,0,0,0-2,0V4A1,1,0,0,0,12,5ZM5.64,7.05a1,1,0,0,0,.7.29,1,1,0,0,0,.71-.29,1,1,0,0,0,0-1.41l-.71-.71A1,1,0,0,0,4.93,6.34Zm12,.29a1,1,0,0,0,.7-.29l.71-.71a1,1,0,1,0-1.41-1.41L17,5.64a1,1,0,0,0,0,1.41A1,1,0,0,0,17.66,7.34ZM21,11H20a1,1,0,0,0,0,2h1a1,1,0,0,0,0-2Zm-9,8a1,1,0,0,0-1,1v1a1,1,0,0,0,2,0V20A1,1,0,0,0,12,19ZM18.36,17A1,1,0,0,0,17,18.36l.71.71a1,1,0,0,0,1.41,0,1,1,0,0,0,0-1.41ZM12,6.5A5.5,5.5,0,1,0,17.5,12,5.51,5.51,0,0,0,12,6.5Zm0,9A3.5,3.5,0,1,1,15.5,12,3.5,3.5,0,0,1,12,15.5Z"
              />
            </svg>
            <svg
              class="swap-on h-10 w-10 fill-current luna"
              xmlns="http://www.w3.org/2000/svg"
              viewBox="0 0 24 24"
              v-if="isDarkMode"
            >
              <path
                d="M21.64,13a1,1,0,0,0-1.05-.14,8.05,8.05,0,0,1-3.37.73A8.15,8.15,0,0,1,9.08,5.49a8.59,8.59,0,0,1,.25-2A1,1,0,0,0,8,2.36,10.14,10.14,0,1,0,22,14.05,1,1,0,0,0,21.64,13Zm-9.5,6.69A8.14,8.14,0,0,1,7.08,5.22v.27A10.15,10.15,0,0,0,17.22,15.63a9.79,9.79,0,0,0,2.1-.22A8.11,8.11,0,0,1,12.14,19.73Z"
              />
            </svg>
          </label>
        </div>

        <div class="user-info">
  <!-- Saludo al usuario -->
  <span class="greeting">Hola, {{ userName }}</span>
  <!-- Botón de cerrar sesión -->
  <button @click="logout" class="logout-btn">
    <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
  </button>
</div>
      </div>
    </div>
  </nav>
</template>


<script>
import axios from "axios";

export default {
  name: "NavBar",
  props: {
    userName: {
      type: String,
      default: "Sin Usuario",
    },
  },
  data() {
    return {
      searchQuery: "",
      filteredResults: [], // Resultados filtrados de búsqueda
      selectedStyle: localStorage.getItem("selectedStyle") || "defecto",
      isDarkMode: JSON.parse(localStorage.getItem("isDarkMode")) || false,
      isMenuOpen: false,
      isSearchBarOpen: false, // Estado de la barra de búsqueda
      activeMenu: null,
      menuOptions: {}, // Opciones del menú
    };
  },
  methods: {
    toggleSearchBar() {
      this.isSearchBarOpen = !this.isSearchBarOpen;
      if (!this.isSearchBarOpen) {
        this.clearSearch();
      }
    },
    clearSearch() {
      this.searchQuery = "";
      this.filteredResults = [];
    },
    toggleMenu() {
      this.isMenuOpen = !this.isMenuOpen;
    },
    showMenu(menu) {
      this.activeMenu = menu;
    },
    keepMenuOpen(menu) {
      this.activeMenu = menu;
    },
    hideMenu() {
      this.activeMenu = null;
    },
    logout() {
      axios
        .post(route("logout"))
        .then((response) => {
          console.log(response.data.message);
          window.location.href = route("login");
        })
        .catch((error) => {
          console.error("Error al cerrar sesión:", error);
        });
    },
    updateStyles() {
      localStorage.setItem("selectedStyle", this.selectedStyle);
      localStorage.setItem("isDarkMode", JSON.stringify(this.isDarkMode));

      const basePath = `${window.location.origin}/inf513/grupo01cc/proyecto22/public/`; // Ajusta según la estructura
      const selectedHref = basePath + this.currentStyles[this.selectedStyle];

      // Eliminar estilos previos
      document.querySelectorAll('link[rel="stylesheet"][data-page-style]').forEach((link) => link.remove());

      // Agregar el nuevo estilo
      const linkTag = document.createElement("link");
      linkTag.rel = "stylesheet";
      linkTag.href = selectedHref;
      linkTag.setAttribute("data-page-style", "true");
      document.head.appendChild(linkTag);
    },
    fetchMenuOptions() {
      axios
        .get(route("menus"))
        .then((response) => {
          this.menuOptions = response.data;
        })
        .catch((error) => {
          console.error("Error al obtener las opciones del menú:", error);
        });
    },
    async performSearch() {
      if (this.searchQuery.trim() === "") {
        this.filteredResults = [];
        return;
      }

      try {
        const response = await axios.get(route("search"), {
          params: {
            query: this.searchQuery,
          },
        });
        this.filteredResults = response.data;
      } catch (error) {
        console.error("Error en la búsqueda:", error);
      }
    },
  },
  computed: {
    currentStyles() {
      return {
        defecto: this.isDarkMode
          ? "css/estiloPagClienteOscuro.css"
          : "css/estiloPagClienteClaro.css",
        ninos: this.isDarkMode
          ? "css/estiloPagNinoOscuro.css"
          : "css/estiloPagNinoClaro.css",
        jovenes: this.isDarkMode
          ? "css/estiloPagJovenOscuro.css"
          : "css/estiloPagJovenClaro.css",
        adultos: this.isDarkMode
          ? "css/estiloPagAdultoOscuro.css"
          : "css/estiloPagAdultoClaro.css",
      };
    },
  },
  mounted() {
    this.updateStyles();
    if (!document.querySelector('link[href*="font-awesome"]')) {
      const faLink = document.createElement("link");
      faLink.href = "https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css";
      faLink.rel = "stylesheet";
      document.head.appendChild(faLink);
    }
    this.fetchMenuOptions();
  },
};
</script>






    <style scoped>
    /* Contenedor general de la barra de búsqueda */
.search-wrapper {
  position: relative;
  margin-left: 15px;
}

/* Botón de ícono de búsqueda */
.search-icon {
  background: none;
  border: none;
  cursor: pointer;
  font-size: 20px;
  color: #333;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: color 0.3s ease;
}

.search-icon:hover {
  color: #007bff; /* Cambia el color al pasar el cursor */
}

/* Barra de búsqueda */
.search-bar {
  display: flex;
  align-items: center;
  background-color: #f1f1f1;
  border: 1px solid #ccc;
  border-radius: 4px;
  padding: 5px 10px;
  margin-top: 25px;
  width: 100vh;
  transition: all 0.3s ease;
  position: absolute;
  top: 100%; /* Posiciona justo debajo del ícono */
  left: 0;
  z-index: 10;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

.search-input {
  flex: 1;
  border: none;
  outline: none;
  background-color: transparent;
  padding: 5px;
  font-size: 14px;
  color: #333;
}

.search-clear-btn {
  background: none;
  border: none;
  cursor: pointer;
  padding: 5px;
  color: #888;
  font-size: 16px;
  transition: color 0.3s ease;
}

.search-clear-btn:hover {
  color: #333;
}

/* Resultados desplegables */
.dropdown-search-results {
  list-style: none;
  margin: 0;
  padding: 0;
  background: #fff;
  border: 1px solid #ccc;
  border-radius: 4px;
  position: absolute;
  top: 100%;
  left: 0;
  width: 100%;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
  z-index: 10;
}

.dropdown-search-results li {
  padding: 8px 10px;
}

.dropdown-search-results li a {
  text-decoration: none;
  color: #333;
  display: block;
}

.dropdown-search-results li:hover {
  background-color: #f1f1f1;
}

.sol,
    .luna {
      cursor: pointer;
    }
    
    .theme-controller {
      position: absolute;
      opacity: 0;
      pointer-events: none;
    }
    
    .custom-select-wrapper {
      position: relative;
      display: inline-block;
      padding: 10px 20px;
      font-size: 1rem;
      cursor: pointer;
      border-radius: 5px;
      transition: background-color 0.3s, border-color 0.3s, color 0.3s;
    }
    
    .custom-select-wrapper label {
      display: flex;
      align-items: center;
      gap: 8px;
      pointer-events: none;
    }
    
    .custom-select-wrapper select {
      appearance: none;
      -webkit-appearance: none;
      background: none;
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      opacity: 0;
      cursor: pointer;
    }
    
    .custom-select-wrapper select option {
      color: #333;
      background-color: #fff;
      padding: 10px;
      font-size: 1rem;
    }
    
    .navbar-container {
      display: flex;
      justify-content: space-between;
      align-items: center;
    }
    
    .nav-links {
      display: flex;
      gap: 20px;
    }
    
    .hamburger-icon {
      display: none;
      cursor: pointer;
    }
    
    
    @media (max-width: 768px) {
      .nav-links {
        display: none;
        flex-direction: column;
        position: absolute;
        top: 60px;
        left: 0;
        width: 100%;
        padding: 20px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
      }
    
      .nav-links.active {
        display: flex;
      }
    
      .hamburger-icon {
        display: block;
      }
    
      .nav-item {
        margin: 10px 0;
      }
    
      .user-controls {
        display: none;
      }
    }
    
    .nav-item:hover .dropdown-content {
      display: flex;
    }
    
    .dropdown-content {
      position: absolute;
      top: 100%;
      left: 0;
      padding: 20px;
      border-radius: 5px;
      display: none;
      flex-direction: column;
      min-width: 220px;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
      gap: 10px;
    }
    
    .dropdown-content li {
      margin-bottom: 10px;
    }
    
    .ff:hover {
      color: red !important;
    }
    
    .dropdown-content a {
      padding: 12px 8px;
      text-decoration: none;
      font-size: 1rem;
      transition: color 0.3s ease;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }
    
    .nav-item:hover .dropdown-content {
      display: flex;
    }
    
    .user-controls {
      display: flex;
      align-items: center;
      gap: 20px;
    }
    
    .logout-btn {
      font-weight: bold;
      border-radius: 10px;
    }
</style>
