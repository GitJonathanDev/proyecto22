<template>
  <nav class="navbar">
    <div class="navbar-container">
      <!-- Logo personalizado con "BodyFit" -->
      <a href="/" class="logo">
        <span class="logo-body">Body</span><span class="logo-fit">Fit</span>
      </a>
  
   <!-- Menú principal simplificado -->
   <ul class="nav-links" :class="{ open: isMenuOpen }">
        <!-- Ruta modificada sin barra al principio -->
        <li class="nav-item">
          <a href="membresias">Mis Membresías</a> <!-- Quitamos la barra al principio -->
        </li>
        <li class="nav-item">
          <a href="vista-cliente">Comprar Productos</a> <!-- Igualmente, quitamos la barra -->
        </li>
      </ul>
      <!-- Icono del Carrito de Compras -->
      <div class="cart-icon" @click="toggleCartModal">
        🛒
        <span v-if="cartItemCount > 0" class="cart-count">{{ cartItemCount }}</span>
      </div>
  
      <div class="custom-select-wrapper">
        <label for="style-select">
          TEMAS <i class="fas fa-chevron-down"></i>
        </label>
        <select id="style-select" v-model="selectedStyle" @change="updateStyles">
          <option value="defecto">Cliente</option>
          <option value="ninos">Niños</option>
          <option value="jovenes">Jóvenes</option>
          <option value="adultos">Adultos</option>
        </select>
      </div>
  
      <!-- Switch de Modo Claro/Oscuro -->
      <div class="mode-switch">
        <label class="swap swap-rotate">
          <!-- Checkbox invisible que controla el estado -->
          <input type="checkbox" class="theme-controller" v-model="isDarkMode" @change="updateStyles" />
  
          <!-- Icono de sol -->
          <svg class="swap-off h-10 w-10 fill-current sol" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
            v-if="!isDarkMode">
            <path
              d="M5.64,17l-.71.71a1,1,0,0,0,0,1.41,1,1,0,0,0,1.41,0l.71-.71A1,1,0,0,0,5.64,17ZM5,12a1,1,0,0,0-1-1H3a1,1,0,0,0,0,2H4A1,1,0,0,0,5,12Zm7-7a1,1,0,0,0,1-1V3a1,1,0,0,0-2,0V4A1,1,0,0,0,12,5ZM5.64,7.05a1,1,0,0,0,.7.29,1,1,0,0,0,.71-.29,1,1,0,0,0,0-1.41l-.71-.71A1,1,0,0,0,4.93,6.34Zm12,.29a1,1,0,0,0,.7-.29l.71-.71a1,1,0,1,0-1.41-1.41L17,5.64a1,1,0,0,0,0,1.41A1,1,0,0,0,17.66,7.34ZM21,11H20a1,1,0,0,0,0,2h1a1,1,0,0,0,0-2Zm-9,8a1,1,0,0,0-1,1v1a1,1,0,0,0,2,0V20A1,1,0,0,0,12,19ZM18.36,17A1,1,0,0,0,17,18.36l.71.71a1,1,0,0,0,1.41,0,1,1,0,0,0,0-1.41ZM12,6.5A5.5,5.5,0,1,0,17.5,12,5.51,5.51,0,0,0,12,6.5Zm0,9A3.5,3.5,0,1,1,15.5,12,3.5,3.5,0,0,1,12,15.5Z" />
          </svg>
  
          <!-- Icono de luna -->
          <svg class="swap-on h-10 w-10 fill-current luna" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
            v-if="isDarkMode">
            <path
              d="M21.64,13a1,1,0,0,0-1.05-.14,8.05,8.05,0,0,1-3.37.73A8.15,8.15,0,0,1,9.08,5.49a8.59,8.59,0,0,1,.25-2A1,1,0,0,0,8,2.36,10.14,10.14,0,1,0,22,14.05,1,1,0,0,0,21.64,13Zm-9.5,6.69A8.14,8.14,0,0,1,7.08,5.22v.27A10.15,10.15,0,0,0,17.22,15.63a9.79,9.79,0,0,0,2.1-.22A8.11,8.11,0,0,1,12.14,19.73Z" />
          </svg>
        </label>
      </div>
  
      <div class="user-info">
  
        <span class="greeting">Hola, {{ userName }}</span>
        <button @click="logout" class="logout-btn">
          <i class="fas fa-sign-out-alt"></i>
          Cerrar Sesión
        </button>
      </div>
  
      <!-- Botón de menú para móviles -->
      <button @click="toggleMobileMenu" class="menu-toggle">
        {{ isMenuOpen ? 'Cerrar' : 'Desplegar' }}
      </button>
    </div>
  </nav>
</template>

<script>
export default {
  name: "NavBar",
  props: {
    cartItemCount: {
      type: Number,
      default: 0,
    },
    userName: {
      type: String,
      default: "Sin Usuario",
    },
  },
  data() {
    return {
      selectedStyle: localStorage.getItem("selectedStyle") || "defecto",
      isDarkMode: JSON.parse(localStorage.getItem("isDarkMode")) || true,
      isMenuOpen: false,
    };
  },
  methods: {
    toggleMobileMenu() {
      this.isMenuOpen = !this.isMenuOpen;
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

      const basePath = `${window.location.origin}/inf513/grupo01cc/proyecto22/public/`; 
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
    toggleCartModal() {
      this.$emit("toggle-cart-modal");
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
  },
};
</script>

<style scoped>
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

.logout-btn {
  font-weight: bold;
  border-radius: 10px;
}

.cart-icon {
  position: relative;
  font-size: 1.5em;
  cursor: pointer;
}

.cart-count {
  position: absolute;
  top: -5px;
  right: -10px;
  background: red;
  color: white;
  border-radius: 50%;
  padding: 0 5px;
  font-size: 0.8em;
}
</style>
