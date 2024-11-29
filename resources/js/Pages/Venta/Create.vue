<template>
  <plantillanav />
  <AppLayout title="Realizar Venta">
    <template #header>
      <h2 class="font-semibold text-2xl text-gray-800 leading-tight">Realizar Venta</h2>
    </template>
    <div class="divgrande rounded-2xl mb-12" >
    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <form @submit.prevent="submitVenta" class="space-y-6">
          <div v-if="errors.length" class="alert alert-danger bg-red-100 text-red-800 p-4 rounded-md">
            <ul>
              <li v-for="error in errors" :key="error">{{ error }}</li>
            </ul>
          </div>

          <input type="hidden" v-model="productosSeleccionadosInput" />
          <input type="hidden" v-model="totalVentaInput" />

          <!-- Encargado -->
          <div class="form-group">
            <label for="encargado" class="block text-sm font-medium text-gray-700 bb">Encargado:</label>
            <input type="hidden" v-model="codEncargadoF" />
            <p class="text-lg font-semibold">{{ encargado.nombre }} {{ encargado.apellidoPaterno }} {{ encargado.apellidoMaterno }}</p>
          </div>

        <div class="form-group">
          <label for="buscarCliente" class="block text-sm font-medium text-gray-700 bb">Buscar Cliente:</label>
          <input
            type="text"
            class="mt-2 p-2 w-full border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 cc"
            v-model="clienteSearch"
            @input="searchClient"
            placeholder="Buscar cliente..."
          />
          <ul v-if="filteredClientes.length" class="mt-2 max-h-40 overflow-y-auto border border-gray-300 rounded-lg shadow-lg bb">
            <li
              v-for="cliente in filteredClientes"
              :key="cliente.carnetIdentidad"
              class="px-4 py-2 cursor-pointer"
              @click="seleccionarCliente(cliente)"
            >
              {{ cliente.nombre }} {{ cliente.apellidoPaterno }}
            </li>
          </ul>
        </div>
          <div class="form-group">
            <label for="fechaventa" class="block text-sm font-medium text-gray-700 bb">Fecha:</label>
            <input type="date" v-model="fechaVenta" class="form-input w-full p-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 cc" required />
          </div>
          <div class="flex space-x-4">
            <button type="button" class="btn btn-primary mb-3 bg-blue-500 text-white p-2 rounded-md hover:bg-blue-600" @click="openBuscarProductoModal">
              <i class="fas fa-search"></i> Buscar Producto
            </button>
            <button type="submit" class="btn btn-primary mb-3 bg-green-500 text-white p-2 rounded-md hover:bg-green-600" :disabled="productosSeleccionados.length === 0">
              Realizar Venta
            </button>
          </div>
          <div v-if="venta">
            <a :href="route('venta.show', venta.codVenta)" class="btn btn-info mb-3 bg-teal-500 text-white p-2 rounded-md hover:bg-teal-600">
              Ver Detalle de Venta
            </a>
          </div>

          <a :href="route('venta.index')" class="btn btn-secondary mb-3 bg-gray-500 text-white p-2 rounded-md hover:bg-gray-600">
            <i class="fas fa-arrow-left"></i> Volver
          </a>
        </form>
        <DialogModal :show="showBuscarProductoModal">
          <template #title>
            <h5 class="modal-title cc text-xl font-semibold text-gray-800 bb">Buscar Producto</h5>
          </template>
          <template #content>
            <div class="form-group mb-4">
              <label for="nombreProducto" class="block cc text-sm font-medium text-gray-700 bb">Buscar por Nombre:</label>
              <input
                type="text"
                v-model="nombreProducto"
                class="form-input w-full p-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                placeholder="Ingrese el nombre del producto"
              />
            </div>

            <div class="overflow-x-auto mt-4">
              <table class="table-auto w-full text-sm">
                <thead class="bg-gray-100">
                  <tr>
                    <th class="p-3 text-left">Nombre</th>
                    <th class="p-3 text-left">Precio</th>
                    <th class="p-3 text-left">Stock</th>
                    <th class="p-3 text-left">Categoría</th>
                    <th class="p-3 text-left">Imagen</th>
                    <th class="p-3 text-left">Opción</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="producto in productos" :key="producto.codProducto">
                    <td class="py-2 px-4">{{ producto.nombre }}</td>
                    <td class="py-2 px-4">{{ producto.precio }} Bs.</td>
                    <td class="py-2 px-4">{{ producto.stock }}</td>
                    <td class="py-2 px-4">{{ producto.categoria ? producto.categoria.nombre : 'Sin categoría' }}</td>
                    <td class="py-2 px-4">
                      <img
                        v-if="producto.imagen_url"
                        :src="`/storage/uploads/${producto.imagen_url}`"
                        alt="Imagen del producto"
                        class="img-thumbnail w-24 h-24 object-cover"
                      />
                      <span v-else>No tiene imagen</span>
                    </td>
                    <td class="p-3 text-center">
                      <button
                        type="button"
                        class="btn-primary text-white p-2 rounded-md "
                        @click="seleccionarProducto(producto)"
                      >
                        Seleccionar
                      </button>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </template>
          <template #footer>
            <button type="button" class="btn btn-secondary bg-gray-500 text-white p-2 rounded-md hover:bg-gray-600" @click="showBuscarProductoModal = false">
              Cerrar
            </button>
          </template>
        </DialogModal>
        <div class="mt-6">
          <h3 class="text-lg font-semibold text-gray-800 bb">Productos Seleccionados</h3>
          <div class="overflow-x-auto mt-4">
          <table class="table-auto w-full text-sm">
            <thead class="bg-gray-100">
              <tr>
                <th class="p-3 text-left">Nombre</th>
                <th class="p-3 text-left">Cantidad</th>
                <th class="p-3 text-left">Precio</th>
                <th class="p-3 text-left">Subtotal</th>
                <th class="p-3 text-left">Acción</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="producto in productosSeleccionados" :key="producto.id">
                <td class="p-3">{{ producto.nombre }}</td>
                <td class="p-3">
                  <input
                    type="number"
                    v-model="producto.cantidad"
                    min="1"
                    class="form-input w-full cc p-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                    @input="calcularTotalVenta"
                  />
                </td>
                <td class="p-3">{{ producto.precio }} Bs.</td>
                <td class="p-3">{{ (producto.cantidad * producto.precio).toFixed(2) }} Bs.</td>
                <td class="p-3">
                  <button
                    type="button"
                    class="btn-primary text-white p-2 rounded-md"
                    @click="quitarProducto(producto.id)"
                  >
                    <i class="fas fa-times"></i> Quitar
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
        </div>

        <div class="mt-6 border-solid">
          <h3 class="text-lg font-semibold text-gray-800 bb">Total de la Venta</h3>
          <p class="text-2xl font-bold bb pp">{{ totalVenta.toFixed(2) }} Bs.</p>
        </div>
      </div>
    </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, computed, watch } from 'vue';
import { router, usePage } from '@inertiajs/vue3';
import DialogModal from '@/Components/DialogModal.vue';
import plantillanav from '@/Layouts/plantillanav.vue';

const { props } = usePage();
const encargado = props.encargado;
const clientes = props.clientes;
const productos = ref(props.productos || []);
const venta = props.venta || null;
const filteredClientes = ref([]);  // Asegurándote de que siempre sea un array vacío

const clienteSearch = ref('');  // Definir clienteSearch como una variable reactiva

const showBuscarProductoModal = ref(false);
const nombreProducto = ref('');
const productosSeleccionados = ref([]);
const totalVenta = ref(0);

const codEncargadoF = ref(encargado ? encargado.carnetIdentidad : '');
const codClienteF = ref('');
const fechaVenta = ref(new Date().toISOString().split('T')[0]);

const productosSeleccionadosInput = computed(() => JSON.stringify(productosSeleccionados.value));
const totalVentaInput = computed(() => totalVenta.value.toFixed(2));

const errors = ref([]);

const seleccionarProducto = (producto) => {
const productoExistente = productosSeleccionados.value.find((p) => p.id === producto.codProducto);
if (productoExistente) {
  productoExistente.cantidad++;
} else {
  productosSeleccionados.value.push({
    id: producto.codProducto,
    nombre: producto.nombre,
    precio: producto.precio,
    cantidad: 1,
  });
}
calcularTotalVenta();
};

// Función de búsqueda de clientes
const searchClient = () => {
if (clienteSearch.value.trim() === '') {
  filteredClientes.value = []; // Si no hay texto de búsqueda, limpiamos los resultados
} else {
  // Aquí haces la búsqueda, y asignas los resultados a filteredClientes
  filteredClientes.value = clientes.filter(cliente =>
    cliente.nombre.toLowerCase().includes(clienteSearch.value.toLowerCase()) ||
    cliente.apellidoPaterno.toLowerCase().includes(clienteSearch.value.toLowerCase())
  );
}
};
const seleccionarCliente = (cliente) => {
codClienteF.value = cliente.carnetIdentidad;
clienteSearch.value = `${cliente.nombre} ${cliente.apellidoPaterno}`;
filteredClientes.value = [];  // Limpiar los resultados de búsqueda
};


const calcularTotalVenta = () => {
totalVenta.value = productosSeleccionados.value.reduce((total, producto) => {
  return total + producto.cantidad * producto.precio;
}, 0);
};

const quitarProducto = (productoId) => {
productosSeleccionados.value = productosSeleccionados.value.filter((p) => p.id !== productoId);
calcularTotalVenta();
};

const submitVenta = () => {
const data = {
  codEncargadoF: codEncargadoF.value,
  codClienteF: codClienteF.value,
  fechaVenta: fechaVenta.value,
  productosSeleccionados: productosSeleccionadosInput.value,
  totalVenta: totalVentaInput.value,
};

axios.post(route('venta.store'), data)
  .then(() => {
    router.get(route('venta.index'));
  })
  .catch((error) => {
    if (error.response) {
      errors.value = error.response.data.errors || [];
    } else {
      console.error('Error de red o problema con el servidor:', error);
      errors.value = ['Hubo un error al procesar la solicitud. Inténtalo más tarde.'];
    }
  });
};

const openBuscarProductoModal = () => {
showBuscarProductoModal.value = true;
};

watch(productosSeleccionados, calcularTotalVenta, { deep: true });
</script>


<style scoped>
.py-12 {
  margin-top: calc(60px + 1rem); 
}
</style>