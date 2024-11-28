<template>
  <plantillanav />
  <div class="py-12">
      <h2 class="text-2xl font-bold text-center mb-6 text-3xl">Detalles de la Compra</h2>

      <div class="mb-6">
        <h3 class="text-1xl font-bold mb-2">Compra realizada el: {{ formatDate(compra.fechaCompra) }}</h3>
        <p class="text-1xl font-bold mb-2"><strong>Proveedor:</strong> {{ compra.proveedor ? compra.proveedor.nombre : 'Proveedor no disponible' }}</p>
        <p class="text-1xl font-bold mb-2"><strong>Encargado:</strong> {{ compra.encargado ? compra.encargado.nombre : 'Encargado no disponible' }}</p>
        <p class="text-1xl font-bold mb-2"><strong>Monto Total:</strong> {{ formatCurrency(compra.montoTotal) }}</p>
      </div>

      <div class="overflow-x-auto">
        <h3 class="text-2xl font-bold text-center mb-6 text-xl">Productos Comprados</h3>
        <table class="table-auto w-full text-sm">
          <thead>
            <tr>
              <th class="p-3 text-left">Imagen</th>
              <th class="p-3 text-left">Producto</th>
              <th class="p-3 text-left">Cantidad</th>
              <th class="p-3 text-left">Precio Unitario</th>
              <th class="p-3 text-left">Subtotal</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="detalle in detalleCompra" :key="detalle.codProducto" class="border-t border-gray-200">
              <td class="p-3 text-left">
                <img 
                  v-if="detalle.producto && detalle.producto.imagen_url"
                  :src="`/storage/uploads/${detalle.producto.imagen_url}`"
                  alt="Imagen del producto"
                  class="w-16 h-16 object-cover rounded-lg"
                />
                <span v-else class="text-gray-500">No tiene imagen</span>
              </td>
              <td class="p-3">{{ detalle.producto ? detalle.producto.nombre : 'Producto no disponible' }}</td>
              <td class="p-3">{{ detalle.cantidad }}</td>
              <td class="p-3">{{ formatCurrency(detalle.precioC) }}</td>
              <td class="p-3">{{ formatCurrency(detalle.cantidad * detalle.precioC) }}</td>
            </tr>
          </tbody>
        </table>
      </div>

      <div>
        <h3 class="text-1xl font-bold mb-2">Total de la Compra</h3>
        <p class="text-1xl font-bold mb-2"><strong>Monto Total:</strong> {{ formatCurrency(compra.montoTotal) }}</p>
      </div>

      <!-- Botones de navegación -->
      <div class="mt-6 flex justify-between space-x-4">
        <Link :href="route('compra.index')" class="px-6 py-2 bg-blue-500 text-white rounded-md shadow-md hover:bg-blue-600 transition duration-300 ease-in-out">
          Volver a la lista de compras
        </Link>

        <!-- Botón para Realizar una nueva compra -->
        <Link :href="route('compra.create')" class="px-6 py-2 bg-green-500 text-white rounded-md shadow-md hover:bg-green-600 transition duration-300 ease-in-out">
          Realizar Nueva Compra
        </Link>

        <!-- Botón para Volver atrás -->
        <button @click="goBack" class="rounded-md shadow-md btn-primary transition">
          Volver Atrás
        </button>
      </div>
    <VisitaFooter />
  </div>
</template>

<script>
import { Link } from '@inertiajs/vue3'; 
import plantillanav from '@/Layouts/plantillanav.vue';
import VisitaFooter from '@/Components/VisitaFooter.vue';

export default {
  props: {
    compra: Object,
    detalleCompra: Array
  },
  components: {
    plantillanav
  },
  methods: {
    formatDate(date) {
      const options = { year: 'numeric', month: 'long', day: 'numeric' };
      return new Date(date).toLocaleDateString('es-BO', options);
    },
    formatCurrency(value) {
      return new Intl.NumberFormat('es-BO', { style: 'currency', currency: 'BOB' }).format(value);
    },
    goBack() {
      window.history.back();
    }
  }
};
</script>

<style scoped>
.btn-primary {
  margin: inherit !important;
}
</style>
