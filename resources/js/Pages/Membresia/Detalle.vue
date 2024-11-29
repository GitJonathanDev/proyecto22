<template>
  <plantillanav/>
  <div class="py-12">
    <h1 class="text-2xl font-bold text-center mb-6 text-3xl">Detalle de la Venta de Membresía</h1>
    <div v-if="detalleMembresia.length > 0">
      <div class="mb-6">
        <h5 class="text-2xl font-bold text-center mb-6 text-3xl">Total Precio Membresía:
          <span class="text-2xl font-bold text-center mb-6 text-3xl">{{ formatCurrency(detalleMembresia[0].membresia.precioTotal) }} Bs.</span>
        </h5>
      </div>
      <div class="overflow-x-auto">
        <h3 class="text-2xl font-bold text-center mb-6 text-3xl">Servicios Adquiridos</h3>
        <table class="table-auto w-full text-sm">
          <thead>
            <tr>
              <th class="p-3 text-left">Nombre</th>
              <th class="p-3 text-left">Descripción</th>
              <th class="p-3 text-left">Fecha Inicio</th>
              <th class="p-3 text-left">Fecha Fin</th>
              <th class="p-3 text-left">Subtotal</th>
              <th class="p-3 text-left">Horario</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="detalle in detalleMembresia" :key="detalle.id" class="border-b hover:bg-gray-50">
              <td class="p-3">{{ detalle.servicio.nombre }}</td>
              <td class="p-3">{{ detalle.servicio.descripcion }}</td>
              <td class="p-3">{{ formatoFecha(detalle.fechaInicio) }}</td> <!-- Mostrar Fecha Inicio con formato -->
              <td class="p-3">{{ formatoFecha(detalle.fechaFin) }}</td> <!-- Mostrar Fecha Fin con formato -->
              <td class="p-3">{{ formatCurrency(detalle.subTotal) }} Bs.</td>
              <td class="p-3">
                <div><strong>Hora Inicio:</strong> {{ detalle.servicio.horario.horaInicio }}</div>
                <div><strong>Hora Fin:</strong> {{ detalle.servicio.horario.horaFin }}</div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
    <div v-else class="text-center text-gray-500 mt-8">
      <p>No se encontraron detalles de membresía.</p>
    </div>

    <!-- Agregar margen superior a los botones para evitar superposición con la tabla -->
    <div class="text-center mt-8">
      <Link :href="route('membresia.index')" class="btn btn-secondary me-2">
        <i class="fas fa-arrow-left"></i> Atrás
      </Link>
      <Link :href="route('membresia.create')" class="mt-4 btn-primary ">
        <i class="fas fa-plus"></i> Realizar Nueva Venta de Membresía
      </Link>
    </div>
  </div>
</template>

<script>
import plantillanav from '@/Layouts/plantillanav.vue';
import { Link } from '@inertiajs/inertia-vue3';

export default {
  components: {
    plantillanav,
    Link
  },
  props: {
    detalleMembresia: Array
  },
  methods: {
    // Formatear las fechas con el formato deseado y ajustar zona horaria
    formatoFecha(fecha) {
      const date = new Date(fecha); // Convertir la fecha UTC a objeto Date
      const fechaLocal = new Date(date.getUTCFullYear(), date.getUTCMonth(), date.getUTCDate());
      // Usar `toLocaleDateString` para mostrar la fecha de acuerdo a la zona horaria local
      const options = {
        year: 'numeric',
        month: '2-digit',
        day: '2-digit',
      };
      return fechaLocal.toLocaleDateString('es-ES', options); // Retorna la fecha en formato dd/mm/yyyy
    },

    // Método para formatear el precio
    formatCurrency(amount) {
      return new Intl.NumberFormat('es-BO', {
        style: 'currency',
        currency: 'BOB',
      }).format(amount);
    }
  }
}
</script>

<style scoped>
.py-12 {
  margin-top: calc(10px + 1rem);
}
</style>
