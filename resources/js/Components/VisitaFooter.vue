<template>
  <footer>
    <div class="footer-container">
      <p class="visita-text">
        Han visitado esta página <span class="visita-count">{{ visitaCount }}</span> veces.
      </p>
    </div>
  </footer>
</template>

<script>
import { ref, onMounted } from 'vue';
import axios from 'axios';

// Definir la función window.route en el propio componente Vue
if (!window.route) {
  window.route = function(name, params = {}) {
    // Aquí se asume que la ruta siempre será de tipo '/api/{name}', ajusta esto según tu aplicación.
    const routes = {
      'api.visitas': '/api/visitas',  // Mapea el nombre de la ruta 'api.visitas' a la URL
    };

    return routes[name] || '/'; // Devuelve la ruta si existe, o '/' por defecto
  };
}

export default {
  name: 'VisitaFooter',
  setup() {
    const visitaCount = ref(0);

    // Función para obtener las visitas desde el backend
    const obtenerVisitas = async () => {
      try {
        // Obtener la URL de la ruta usando window.route
        const apiUrl = window.route('api.visitas');  // 'api.visitas' es el nombre de la ruta

        // Hacer la solicitud GET pasando la ruta como parámetro
        const response = await axios.get(apiUrl, {
          params: { ruta: window.location.pathname }, // Enviar la ruta actual
        });

        // Asignar el conteo de visitas si se recibe una respuesta válida
        if (response.data && typeof response.data.visitas === 'number') {
          visitaCount.value = response.data.visitas;
        }
      } catch (error) {
        console.error('Error al obtener las visitas:', error);
      }
    };

    // Al montar el componente, obtener las visitas
    onMounted(() => {
      obtenerVisitas();
    });

    return {
      visitaCount,
    };
  },
};
</script>

<style scoped>
footer {
  position: fixed;
  bottom: 0;
  left: 0;
  width: 100%;
  background-color: #2c3e50;
  color: #ecf0f1;
  text-align: center;
  padding: 8px;
  box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.1);
  z-index: 1000;
}

.footer-container {
  display: flex;
  justify-content: center;
  align-items: center;
}

.visita-text {
  font-size: 1rem;
  font-weight: 500;
  margin: 0;
}

.visita-count {
  font-weight: bold;
  color: #e74c3c;
  font-size: 1.2rem;
}
</style>
