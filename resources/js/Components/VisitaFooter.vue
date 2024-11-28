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

    // Definir la URL completa de la ruta del backend
    const apiVisitasUrl = '/api/visitas'; // Ruta exacta definida en Laravel

    const obtenerVisitas = async () => {
      try {
        // Solicitud GET al backend usando la URL de la ruta
        const response = await axios.get(apiVisitasUrl, {
          params: { ruta: window.location.pathname }, // Enviar la ruta actual como parámetro
        });

        // Asignar el conteo de visitas si la respuesta es válida
        if (response.data && typeof response.data.visitas === 'number') {
          visitaCount.value = response.data.visitas;
        }
      } catch (error) {
        console.error('Error al obtener las visitas:', error);
      }
    };

    // Llamar al backend al montar el componente
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
