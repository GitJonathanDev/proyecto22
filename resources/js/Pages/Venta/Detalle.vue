<template>
  <plantillanav />
  <div class="py-12">
      <h2 class="text-2xl font-bold text-center mb-6 text-3xl">Detalles de la Venta</h2>

      <div class="mb-6">
        <h3 class="text-1xl font-bold mb-2">Venta #{{ venta.codVenta }}</h3>
        <p class="text-1xl font-bold mb-2"><strong>Fecha de Venta:</strong> {{ formatDate(venta.fechaVenta) }}</p>
        <p class="text-1xl font-bold mb-2"><strong>Cliente:</strong> {{ venta.cliente.nombre }} {{ venta.cliente.apellidoPaterno }} {{ venta.cliente.apellidoMaterno }}</p>
        <p class="text-1xl font-bold mb-2"><strong>Encargado:</strong> {{ venta.encargado.nombre }} {{ venta.encargado.apellidoPaterno }} {{ venta.encargado.apellidoMaterno }}</p>
        <p class="text-1xl font-bold mb-2"><strong>Monto Total:</strong> {{ venta.montoTotal }} Bs.</p>
      </div>

      <div class="overflow-x-auto">
        <h3 class="text-2xl font-bold text-center mb-6 text-xl">Productos Vendidos</h3>
        <table class="table-auto w-full text-sm">
          <thead>
            <tr>
              <th class="p-3 text-left">Producto</th>
              <th class="p-3 text-left">Cantidad</th>
              <th class="p-3 text-left">Precio</th>
              <th class="p-3 text-left">Total</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="producto in detalleVenta" :key="producto.codProducto" class="border-t border-gray-200">
              <td class="p-3">{{ producto.producto ? producto.producto.nombre : 'Producto no disponible' }}</td>
              <td class="p-3">{{ producto.cantidad }}</td>
              <td class="p-3">{{ producto.precioV }} Bs.</td>
              <td class="p-3">{{ (producto.cantidad * producto.precioV).toFixed(2) }} Bs.</td>
            </tr>
          </tbody>
        </table>
      </div>

      <div>
        <p class="text-1xl font-bold mb-2"><strong>Monto Pagado:</strong> {{ pago.monto }} Bs.</p>
        <p class="text-1xl font-bold mb-2"><strong>Estado:</strong> {{ pago.estado }}</p>
      </div>
      <div class="mt-6 flex justify-between space-x-4">
        <Link :href="route('venta.index')" class="px-6 py-2 bg-blue-500 text-white rounded-md shadow-md hover:bg-blue-600 transition duration-300 ease-in-out">
          Volver a la lista de ventas
        </Link>
        <Link :href="route('venta.create')" class="px-6 py-2 bg-green-500 text-white rounded-md shadow-md hover:bg-green-600 transition duration-300 ease-in-out">
          Realizar Nueva Venta
        </Link>
        <button @click="goBack" class="rounded-md shadow-md btn-primary transition">
          Volver Atrás
        </button>
      </div>
    <VisitaFooter />
  </div>
</template>

<script>
import { Link } from '@inertiajs/vue3';  // Importa Link de Inertia
import plantillanav from '@/Layouts/plantillanav.vue';
import VisitaFooter from '@/Components/VisitaFooter.vue';

export default {
  props: {
    venta: Object,
    detalleVenta: Array,
    pago: Object
  },
  components: {
    plantillanav
  },
  methods: {
    // Método para formatear las fechas
    formatDate(date) {
      const options = { year: 'numeric', month: 'long', day: 'numeric' };
      return new Date(date).toLocaleDateString('es-BO', options);
    },
    // Método para volver a la página anterior
    goBack() {
      window.history.back();
    }
  }
};
</script>

<style scoped>
.btn-primary{
  margin: inherit !important;
}
</style>
