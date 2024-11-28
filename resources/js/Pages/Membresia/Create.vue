<template>
  <plantillanav/>
  <div class="py-12">
    <div class="max-w-4xl mx-auto">
      <div class="bg-white shadow-lg rounded-lg border-t-4 border-green-500">
        <div class="bg-green-600 text-white p-4 rounded-t-lg">
          <h5 class="text-lg font-semibold">Realizar Venta de Membresía</h5>
        </div>
        <div class="p-6">
          <!-- Error handling -->
          <div v-if="errors && errors.length > 0" class="alert alert-danger mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
            <ul>
              <li v-for="error in errors" :key="error">{{ error }}</li>
            </ul>
          </div>

          <form @submit.prevent="handleSubmit">
            <input type="hidden" v-model="form.tipoServicioF">
            <input type="hidden" v-model="form.codServiciosF">
            <input type="hidden" v-model="form.precioTotal">
            <input type="hidden" v-model="form.fechaInicio">
            <input type="hidden" v-model="form.fechaFin">
            <input type="hidden" v-model="form.codClienteF">
            <input type="hidden" v-model="form.telefono">
            <input type="hidden" v-model="form.codEncargadoF">
            <input type="hidden" v-model="form.subTotal">
            <input type="hidden" v-model="form.montoTotal">


            <!-- Buscar Cliente -->
            <div class="mb-4">
              <label for="buscarCliente" class="block text-sm font-medium text-gray-700">Buscar Cliente:</label>
              <input
                type="text"
                class="mt-2 p-2 w-full border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
                v-model="form.clienteSearch"
                @input="searchClient"
                placeholder="Buscar cliente..."
              />
              <ul v-if="clientes.length" class="mt-2 max-h-40 overflow-y-auto border border-gray-300 rounded-lg shadow-lg">
                <li
                  v-for="cliente in clientes"
                  :key="cliente.carnetIdentidad"
                  class="px-4 py-2 hover:bg-gray-200 cursor-pointer"
                  @click="selectClient(cliente)"
                >
                  {{ cliente.nombre }} {{ cliente.apellidoPaterno }}
                </li>
              </ul>
            </div>

            <!-- Descripción -->
            <div class="mb-4">
              <label for="descripcion" class="block text-sm font-medium text-gray-700">Descripción:</label>
              <input
                type="text"
                id="descripcion"
                class="mt-2 p-2 w-full border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
                v-model="form.descripcion"
              />
            </div>

            <button
              type="button"
              class="btn btn-success mb-3 bg-green-600 text-white hover:bg-green-700 p-2 rounded-lg"
              @click="showServicesModal"
            >
              <i class="fas fa-search mr-2"></i> Buscar Servicio
            </button>

            <!-- Botón de envío -->
            <button
              type="submit"
              class="bg-green-600 text-white hover:bg-green-700 p-2 rounded-lg w-full"
              :disabled="isSubmitDisabled"
            >
              Realizar Venta de Membresía
            </button>

            <!-- Ver detalle de venta -->
            <a :href="`/membresia/${membresia.codMembresia}`" v-if="membresia" class="block text-center mt-4 text-blue-600 hover:underline">
              Ver Detalle de Venta de Membresía
            </a>

            <Link :href="route('membresia.index')" class="btn btn-secondary me-3">
               Atrás
            </Link>
          </form>
        </div>
      </div>
    </div>

   <!-- Modal para Buscar Servicio -->
<div v-if="showModal" class="fixed inset-0 z-50 bg-black bg-opacity-50 flex items-center justify-center">
  <div class="bg-white rounded-lg p-6 max-w-4xl w-full">
    <div class="flex justify-between items-center">
      <h1 class="text-lg font-semibold">Buscar Servicio</h1>
      <button @click="showModal = false" class="text-red-600 text-xl">&times;</button>
    </div>
    <form @submit.prevent="searchServices">
      <div class="mb-4">
        <label for="nombreServicio" class="block text-sm font-medium text-gray-700">Buscar por Nombre:</label>
        <input
          type="text"
          class="mt-2 p-2 w-full border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
          v-model="search.nombreServicio"
          placeholder="Ingrese el nombre del servicio"
        />
      </div>
    </form>
    <div class="table-responsive overflow-x-auto">
      <table class="table-auto w-full text-sm border-collapse">
        <thead class="bg-gray-100 text-left">
          <tr>
            <th class="px-4 py-2">Nombre</th>
            <th class="px-4 py-2">Descripción</th>
            <th class="px-4 py-2">Tipo</th>
            <th class="px-4 py-2">Horario</th>
            <th class="px-4 py-2">Cantidad</th>
            <th class="px-4 py-2">Fecha de Inicio</th> <!-- Nueva columna -->
            <th class="px-4 py-2">Opción</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="servicio in servicios" :key="servicio.codServicio">
            <td class="px-4 py-2">{{ servicio.nombre }}</td>
            <td class="px-4 py-2">{{ servicio.descripcion }}</td>
            <td class="px-4 py-2">
              <select
                v-model="selectedTipo[servicio.codServicio]"
                class="w-full p-2 border border-gray-300 rounded-lg"
                :disabled="isTipoSeleccionado(servicio)"
              >
                <option
                  v-for="precio in servicio.precios"
                  :key="precio.codPrecioServicio"
                  :value="precio.codPrecioServicio"
                >
                  {{ precio.tipo }} - {{ precio.precio }} Bs.
                </option>
              </select>
            </td>
            <td class="px-4 py-2">{{ servicio.horario ? servicio.horario.horaInicio + ' - ' + servicio.horario.horaFin : 'Sin horario' }}</td>
            <td class="px-4 py-2">
              <input
                type="number"
                v-model="cantidad[servicio.codServicio]"
                min="1"
                value="1"
                class="p-1 w-16 border border-gray-300 rounded-lg"
                @input="updateFechaFin(servicio)"
                :disabled="isTipoSeleccionado(servicio)" 
              />
            </td>
            <td class="px-4 py-2">
              <input
                type="date"
                v-model="fechaInicio[servicio.codServicio]"
                class="p-2 border border-gray-300 rounded-lg"
                @change="updateFechaFin(servicio)" 
                :disabled="isTipoSeleccionado(servicio)"
                :value="fechaInicio[servicio.codServicio] || currentDate()"
              />
            </td>
            <td class="px-4 py-2">
              <button
                type="button"
                class="bg-green-600 text-white p-2 rounded-lg hover:bg-green-700"
                @click="selectServicio(servicio)"
                :disabled="isTipoSeleccionado(servicio)"
              >
                Seleccionar
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
    <div class="flex justify-between mt-4">
      <button @click="showModal = false" class="bg-gray-300 text-gray-700 p-2 rounded-lg">Cerrar</button>
      <button @click="confirmSelection" class="bg-green-600 text-white p-2 rounded-lg hover:bg-green-700">Confirmar Selección</button>
    </div>
  </div>
</div>

<div class="mt-8 max-w-4xl mx-auto">
        <div class="bg-white shadow-lg rounded-lg border-t-4 border-green-500">
          <div class="bg-green-600 text-white p-4 rounded-t-lg">
            <h5 class="text-lg font-semibold">Servicios Seleccionados</h5>
          </div>
          <div class="p-6">
            <table class="table-auto w-full text-sm border-collapse">
              <thead class="bg-gray-100 text-left">
                <tr>
                  <th class="px-4 py-2">Nombre</th>
                  <th class="px-4 py-2">Descripción</th>
                  <th class="px-4 py-2">Tipo</th>
                  <th class="px-4 py-2">Precio</th>
                  <th class="px-4 py-2">Subtotal</th>
                  <th class="px-4 py-2">Fecha Fin</th>
                  <th class="px-4 py-2">Opción</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="(servicio, index) in serviciosSeleccionados" :key="index">
                  <td class="px-4 py-2">{{ servicio.nombre }}</td>
                  <td class="px-4 py-2">{{ servicio.descripcion }}</td>
                  <td class="px-4 py-2">{{ servicio.tipo }}</td>
                  <td class="px-4 py-2">{{ servicio.precio }} Bs.</td>
                  <td class="px-4 py-2">{{ (servicio.precio * servicio.cantidad).toFixed(2) }} Bs.</td>
                  <td class="px-4 py-2">{{ servicio.fechaFin }}</td>
                  <td class="px-4 py-2">
                    <button type="button" class="bg-red-600 text-white p-2 rounded-lg hover:bg-red-700" @click="removeServicio(index)">
                      Eliminar
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>
            <p class="mt-4 text-xl font-semibold">Total: Bs. {{ precioTotal.toFixed(2) }}</p>
          </div>
        </div>
      </div>
    <div class="mt-8 max-w-sm mx-auto">
      <div class="bg-white shadow-lg rounded-lg border-t-4 border-green-500">
        <div class="bg-green-600 text-white p-4 rounded-t-lg">
          <h5 class="text-lg font-semibold">Total de la Membresía</h5>
        </div>
        <div class="p-6">
          <p class="text-3xl font-bold text-center">{{ precioTotal.toFixed(2) }} Bs.</p>
        </div>
      </div>
    </div>
</div>
</template>
<script>
import { ref, reactive, computed } from "vue";
import { useForm, router } from '@inertiajs/vue3';
import plantillanav from '@/Layouts/plantillanav.vue';
import axios from "axios";

export default {
  name: "VentaMembresia",
  components: {
    plantillanav, 
  },
  setup() {
    const form = useForm({
      clienteSearch: "",
      descripcion: "",
      codClienteF: "",
      telefono: "",
      codEncargadoF: "14623330", 
      precioTotal: 0,
      fechaInicio: "",
      fechaFin: "",
      codServiciosF: "",
      tipoServicioF: "",
      subTotal: 0,
      montoTotal: 0,
      servicios: [],
    });

    const clientes = ref([]);
    const servicios = ref([]);
    const serviciosSeleccionados = ref([]);
    const showModal = ref(false);
    const search = reactive({
      nombreServicio: "",
    });
    const qrUrl = ref('');
    const selectedTipo = reactive({});
    const cantidad = reactive({});  // Mantener la cantidad de cada servicio
    const errors = ref([]);

    const isSubmitDisabled = computed(() => {
      return (
        form.descripcion.trim() === "" ||
        (typeof form.codClienteF === 'string' && form.codClienteF.trim() === "") || 
        serviciosSeleccionados.value.length === 0
      );
    });
    const submit = () => {
      form.post(route('consumirServicio'), {
        // No hacemos ninguna redirección aquí, solo enviamos el formulario.
        onSuccess: () => {
          console.log('Formulario enviado exitosamente'); // O cualquier otra acción si lo deseas.
        },
      });
    };

    const precioTotal = computed(() => {
      return serviciosSeleccionados.value.reduce(
        (total, servicio) => total + servicio.subTotal,  // Usar subTotal en lugar de precio * cantidad
        0
      );
    });

    const searchClient = async () => {
      if (form.clienteSearch.length >= 2) {
        try {
          const response = await axios.get(`/clientes/buscar?query=${form.clienteSearch}`);
          clientes.value = response.data;
        } catch (error) {
          console.error(error);
        }
      }
    };

    const selectClient = (cliente) => {
      if (cliente && cliente.carnetIdentidad) {
        form.codClienteF = cliente.carnetIdentidad;
        form.telefono = cliente.telefono;
        form.clienteSearch = `${cliente.nombre} ${cliente.apellidoPaterno}`;
        clientes.value = [];
        verificarEstadoBoton();
      }
    };

    const searchServices = async () => {
      try {
        const response = await axios.get(`/servicios/buscar?search=${search.nombreServicio}`);
        servicios.value = response.data;
      } catch (error) {
        console.error(error);
      }
    };
    
    const fechaInicio = reactive({});  // Mantener la fecha de inicio de cada servicio
      const currentDate = () => {
        const today = new Date();
        return today.toISOString().split('T')[0];  // Formato YYYY-MM-DD
      };

      const selectServicio = (servicio) => {
  // Verificar si el servicio ya está seleccionado
  const servicioExistente = serviciosSeleccionados.value.find(s => s.codServicio === servicio.codServicio);

  if (servicioExistente) {
    // Ya está seleccionado, no hacer nada
    return;
  }

  // Utilizamos la fecha de inicio seleccionada o la actual
  const fechaInicioSeleccionada = fechaInicio[servicio.codServicio] || currentDate();

  // Obtenemos el tipo de servicio seleccionado
  const tipoSeleccionado = selectedTipo[servicio.codServicio];
  const precioSeleccionado = servicio.precios.find(precio => precio.codPrecioServicio === tipoSeleccionado);
  const cantidadSeleccionada = cantidad[servicio.codServicio] || 1;
  
  // Calcular la fecha de fin
  const fechaFinCalculada = calcularFechaFin(fechaInicioSeleccionada, precioSeleccionado.tipo, cantidadSeleccionada);

  // Asignamos las fechas al formulario, pero asegurándonos de que son individuales por servicio
  form.fechaInicio = fechaInicioSeleccionada;
  form.fechaFin = fechaFinCalculada;
  form.codServiciosF = servicio.codServicio;
  form.tipoServicioF = precioSeleccionado ? precioSeleccionado.tipo : 'Desconocido';

  const subTotal = precioSeleccionado.precio * cantidadSeleccionada;

  if (isNaN(subTotal) || subTotal <= 0) {
    console.error('El subTotal no es válido:', subTotal);
    return;
  }

  form.subTotal = subTotal;

  // Asegurarnos de que la fecha de inicio y la fecha de fin son únicas por servicio
  const servicioConFecha = {
    ...servicio,
    tipo: precioSeleccionado ? precioSeleccionado.tipo : 'Desconocido',
    precio: precioSeleccionado ? precioSeleccionado.precio : 0,
    cantidad: cantidadSeleccionada,
    fechaFin: fechaFinCalculada,
    fechaInicio: fechaInicioSeleccionada, // Asegurarnos de que cada servicio tenga su propia fecha de inicio
    subTotal: subTotal,
  };

  serviciosSeleccionados.value.push(servicioConFecha);

  form.precioTotal = precioTotal.value;
  form.montoTotal = serviciosSeleccionados.value.reduce((total, servicio) => total + servicio.subTotal, 0);

  showModal.value = false;
  verificarEstadoBoton();
};

const isTipoSeleccionado = (servicio) => {
  return serviciosSeleccionados.value.some((s) => s.codServicio === servicio.codServicio);
};
    const removeServicio = (index) => {
      serviciosSeleccionados.value.splice(index, 1);
    };

    const handleSubmit = () => {
  // Preparar los datos de los servicios seleccionados para enviarlos
  const serviciosData = serviciosSeleccionados.value.map(servicio => ({
    codServicio: servicio.codServicio,
    tipoServicioF: servicio.tipo,
    fechaInicio: servicio.fechaInicio, 
    fechaFin: servicio.fechaFin,
    subTotal: servicio.subTotal
  }));

  // Añadir los servicios al formulario
  form.servicios = serviciosData;

  // Enviar el formulario con los datos estructurados
  form.post(route('membresia.store'))
    .then(() => {
      router.get(route('membresia.index'));
    })
    .catch((error) => {
      // Depurar el objeto de error
      console.error("Error completo:", error);
      if (error.response) {
        console.error("Errores en la respuesta:", error.response.data.errors);
      } else if (error.request) {
        console.error("No se recibió respuesta:", error.request);
      } else {
        console.error("Error desconocido:", error.message);
      }
    });
};


    const loadAllServices = async () => {
      try {
        const response = await axios.get('/servicios/todos'); 
        servicios.value = response.data;
      } catch (error) {
        console.error("Error al cargar los servicios:", error);
      }
    };
    const showServicesModal = () => {
      showModal.value = true;
      loadAllServices(); 
    };

    const calcularFechaFin = (fechaInicio, tipo, cantidad) => {
      const fecha = new Date(fechaInicio);  // Crea una nueva fecha a partir de la fecha de inicio
      if (tipo === "Diario") {
        fecha.setDate(fecha.getDate() + cantidad);  // Suma días
      } else if (tipo === "Mensual") {
        fecha.setMonth(fecha.getMonth() + cantidad);  // Suma meses
      } else if (tipo === "Anual") {
        fecha.setFullYear(fecha.getFullYear() + cantidad);  // Suma años
      }
      // Retorna la fecha en formato YYYY-MM-DD
      return fecha.toISOString().split("T")[0]; 
    };

    const updateFechaFin = (servicio) => {
  const tipoSeleccionado = selectedTipo[servicio.codServicio];
  const precioSeleccionado = servicio.precios.find(precio => precio.codPrecioServicio === tipoSeleccionado);
  const cantidadSeleccionada = cantidad[servicio.codServicio] || 1;
  const fechaSeleccionada = fechaInicio[servicio.codServicio] || currentDate();
  const fechaFinCalculada = calcularFechaFin(fechaSeleccionada, precioSeleccionado.tipo, cantidadSeleccionada);

  // Actualiza la fecha de fin del servicio en el array de serviciosSeleccionados
  const servicioIndex = serviciosSeleccionados.value.findIndex(s => s.codServicio === servicio.codServicio);
  if (servicioIndex !== -1) {
    serviciosSeleccionados.value[servicioIndex].fechaFin = fechaFinCalculada;
  }
};

    const verificarEstadoBoton = () => {
      const hayServiciosSeleccionados = serviciosSeleccionados.value.length > 0;
      const descripcion = form.descripcion.trim();
      const clienteSeleccionado = form.codClienteF && typeof form.codClienteF === 'string' && form.codClienteF.trim() !== "";

      if (hayServiciosSeleccionados && descripcion && clienteSeleccionado) {
        return false; 
      } else {
        return true; 
      }
    };

    return {
      form,
      clientes,
      servicios,
      showModal,
      search,
      qrUrl,
      selectedTipo,
      cantidad,
      serviciosSeleccionados,
      isSubmitDisabled,
      precioTotal,
      errors,
      handleSubmit,
      removeServicio,
      selectClient,
      searchClient,
      searchServices,
      selectServicio,
      showServicesModal,
      updateFechaFin,
      fechaInicio,
      isTipoSeleccionado,
      currentDate,
    };
  },
};
</script>

<style scoped>
.iframe-wrapper {
  margin-top: 20px;
}
.iframe-container {
  position: relative;
  width: 100%;
  padding-top: 56.25%;
}
.iframe-container iframe {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
}
.fixed {
  position: fixed;
}
.inset-0 {
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
}
.bg-black {
  background-color: rgba(0, 0, 0, 0.5);
}
.py-12 {
  margin-top: calc(60px + 1rem);
}
</style>
