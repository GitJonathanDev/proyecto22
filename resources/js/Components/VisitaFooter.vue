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

export default {
  name: 'VisitaFooter',
  setup() {
    const visitaCount = ref(0);

    // Función para obtener las visitas desde el backend
    const obtenerVisitas = async () => {
      try {
        // Obtener la ruta actual del navegador
        let ruta = window.location.pathname;

        // Eliminar el prefijo si está presente
        const prefijo = '/inf513/grupo01cc/proyecto22/public';
        if (ruta.startsWith(prefijo)) {
          ruta = ruta.replace(prefijo, ''); // Eliminar el prefijo
        }

        // Realizar la solicitud GET al backend
        const response = await axios.get(route('api.visitas'), {
          params: { ruta },
        });

        // Actualizar el contador de visitas
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
