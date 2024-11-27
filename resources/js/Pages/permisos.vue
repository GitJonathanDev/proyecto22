<template>
  <div id="admin-panel" class="p-8 max-w-7xl mx-auto bg-white shadow-xl rounded-lg">
    <h1 class="text-3xl font-semibold text-gray-800 mb-8">Gestión de Menús y Permisos</h1>

    <!-- Panel de Menús -->
    <section class="mb-8">
      <h2 class="text-2xl font-medium text-gray-700 mb-6">Menús</h2>
      <button @click="nuevoMenu" class="px-6 py-3 bg-blue-600 text-white rounded-lg shadow-md hover:bg-blue-700 transition duration-200 mb-6">+ Nuevo Menú</button>
      <table class="w-full border-collapse table-auto shadow-md bg-gray-50 rounded-lg">
        <thead class="bg-blue-100">
          <tr>
            <th class="px-6 py-3 border text-left text-sm font-semibold text-gray-600">Nombre</th>
            <th class="px-6 py-3 border text-left text-sm font-semibold text-gray-600">Ícono</th>
            <th class="px-6 py-3 border text-left text-sm font-semibold text-gray-600">Acciones</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="menu in menus" :key="menu.codMenu" class="hover:bg-gray-100 transition">
            <td class="px-6 py-4 border text-sm text-gray-700">{{ menu.nombre }}</td>
            <td class="px-6 py-4 border text-sm text-gray-700">{{ menu.icono }}</td>
            <td class="px-6 py-4 border text-sm text-gray-700">
              <button @click="editarMenu(menu)" class="px-4 py-2 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600 transition duration-200 mr-2">Editar</button>
              <button @click="eliminarMenu(menu.codMenu)" class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition duration-200 mr-2">Eliminar</button>
              <button @click="verOpciones(menu.codMenu)" class="px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition duration-200">Ver Opciones</button>
            </td>
          </tr>
        </tbody>
      </table>
    </section>

    <!-- Panel de Opciones -->
    <section v-if="menuSeleccionado" class="mb-8">
      <h2 class="text-2xl font-medium text-gray-700 mb-6">Opciones de Menú: {{ menuSeleccionado.nombre }}</h2>
      <button @click="nuevaOpcion" class="px-6 py-3 bg-blue-600 text-white rounded-lg shadow-md hover:bg-blue-700 transition duration-200 mb-6">+ Nueva Opción</button>
      <button @click="cerrarOpciones" class="px-6 py-3 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition duration-200 mb-6">Volver a Menús</button>
      <table class="w-full border-collapse table-auto shadow-md bg-gray-50 rounded-lg">
        <thead class="bg-blue-100">
          <tr>
            <th class="px-6 py-3 border text-left text-sm font-semibold text-gray-600">Nombre</th>
            <th class="px-6 py-3 border text-left text-sm font-semibold text-gray-600">Ruta</th>
            <th class="px-6 py-3 border text-left text-sm font-semibold text-gray-600">Ícono</th>
            <th class="px-6 py-3 border text-left text-sm font-semibold text-gray-600">Acciones</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="opcion in opciones" :key="opcion.codOpcion" class="hover:bg-gray-100 transition">
            <td class="px-6 py-4 border text-sm text-gray-700">{{ opcion.nombre }}</td>
            <td class="px-6 py-4 border text-sm text-gray-700">{{ opcion.ruta }}</td>
            <td class="px-6 py-4 border text-sm text-gray-700">{{ opcion.icono }}</td>
            <td class="px-6 py-4 border text-sm text-gray-700">
              <button @click="editarOpcion(opcion)" class="px-4 py-2 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600 transition duration-200 mr-2">Editar</button>
              <button @click="eliminarOpcion(opcion.codOpcion)" class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition duration-200 mr-2">Eliminar</button>
              <button @click="verPermisos(opcion.codOpcion)" class="px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition duration-200">Ver Permisos</button>
            </td>
          </tr>
        </tbody>
      </table>
    </section>

    <!-- Panel de Permisos -->
    <section v-if="opcionSeleccionada" class="mb-8">
      <h2 class="text-2xl font-medium text-gray-700 mb-6">Permisos de Opción: {{ opcionSeleccionada.nombre }}</h2>
      <button @click="nuevoPermiso" class="px-6 py-3 bg-blue-600 text-white rounded-lg shadow-md hover:bg-blue-700 transition duration-200 mb-6">+ Nuevo Permiso</button>
      <button @click="cerrarPermisos" class="px-6 py-3 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition duration-200 mb-6">Volver a Opciones</button>
      <table class="w-full border-collapse table-auto shadow-md bg-gray-50 rounded-lg">
        <thead class="bg-blue-100">
          <tr>
            <th class="px-6 py-3 border text-left text-sm font-semibold text-gray-600">Acción</th>
            <th class="px-6 py-3 border text-left text-sm font-semibold text-gray-600">Acciones</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="permiso in permisos" :key="permiso.codPermiso" class="hover:bg-gray-100 transition">
            <td class="px-6 py-4 border text-sm text-gray-700">{{ permiso.accion }}</td>
            <td class="px-6 py-4 border text-sm text-gray-700">
              <button @click="verTiposUsuario(permiso.codPermiso)" class="px-4 py-2 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600 transition duration-200 mr-2">Ver Tipos de Usuario</button>
              <button @click="eliminarPermiso(permiso.codPermiso)" class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition duration-200">Eliminar</button>
            </td>
          </tr>
        </tbody>
      </table>
    </section>

    <!-- Panel de Tipos de Usuario con Permiso -->
    <section v-if="permisoSeleccionado" class="mb-8">
      <h2 class="text-2xl font-medium text-gray-700 mb-6">Tipos de Usuario con Permiso: {{ permisoSeleccionado.accion }}</h2>
      <button @click="agregarTipoUsuario" class="px-6 py-3 bg-blue-600 text-white rounded-lg shadow-md hover:bg-blue-700 transition duration-200 mb-6">+ Agregar Tipo de Usuario</button>
      <button @click="cerrarTiposUsuario" class="px-6 py-3 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition duration-200 mb-6">Volver a Permisos</button>
      <table class="w-full border-collapse table-auto shadow-md bg-gray-50 rounded-lg">
        <thead class="bg-blue-100">
          <tr>
            <th class="px-6 py-3 border text-left text-sm font-semibold text-gray-600">Tipo de Usuario</th>
            <th class="px-6 py-3 border text-left text-sm font-semibold text-gray-600">Acciones</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="tipo in tiposUsuario" :key="tipo.codTipoUsuario" class="hover:bg-gray-100 transition">
            <td class="px-6 py-4 border text-sm text-gray-700">{{ tipo.descripcion }}</td>
            <td class="px-6 py-4 border text-sm text-gray-700">
              <button @click="quitarTipoUsuario(tipo.codTipoUsuario)" class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition duration-200">Quitar</button>
            </td>
          </tr>
        </tbody>
      </table>
    </section>

    <!-- Modal para Nuevo Menú -->
    <div v-if="modalMenu" class="fixed inset-0 bg-gray-900 bg-opacity-50 flex justify-center items-center">
      <div class="bg-white p-6 rounded-lg w-96">
        <h3 class="text-xl font-medium mb-4">Nuevo Menú</h3>
        <form @submit.prevent="crearMenu">
          <input v-model="nuevoMenuData.nombre" class="w-full p-2 mb-4 border rounded" placeholder="Nombre del Menú" required>
          <input v-model="nuevoMenuData.icono" class="w-full p-2 mb-4 border rounded" placeholder="Ícono" required>
          <div class="flex justify-end">
            <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">Guardar</button>
            <button @click="cerrarModal" type="button" class="ml-4 px-6 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition">Cancelar</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>
  
<script>
import axios from "axios";

export default {
  data() {
    return {
      menus: [],
      opciones: [],
      permisos: [],
      tiposUsuario: [],
      menuSeleccionado: null,
      opcionSeleccionada: null,
      permisoSeleccionado: null,
    };
  },
  methods: {
    // CRUD Menús
    async cargarMenus() {
      const response = await axios.get(route('menus.listar'));
      this.menus = response.data;
    },
    nuevoMenu() {
      const nombre = prompt("Nombre del menú:");
      const icono = prompt("Ícono del menú (opcional):");
      axios.post(route('menus.crear'), { nombre, icono }).then(this.cargarMenus);
    },
    editarMenu(menu) {
      const nombre = prompt("Nuevo nombre del menú:", menu.nombre);
      const icono = prompt("Nuevo ícono del menú:", menu.icono);
      axios.put(route('menus.actualizar', { codMenu: menu.codMenu }), { nombre, icono }).then(this.cargarMenus);
    },
    eliminarMenu(codMenu) {
      axios.delete(route('menus.eliminar', { codMenu })).then(this.cargarMenus);
    },
    verOpciones(codMenu) {
      axios.get(route('opciones.listar', { codMenu })).then((response) => {
        this.opciones = response.data;
        this.menuSeleccionado = this.menus.find((m) => m.codMenu === codMenu);
      });
    },

    // CRUD Opciones
    cerrarOpciones() {
      this.menuSeleccionado = null;
      this.opciones = [];
    },
    nuevaOpcion() {
      const nombre = prompt("Nombre de la opción:");
      const ruta = prompt("Ruta de la opción:");
      const icono = prompt("Ícono de la opción (opcional):");
      axios
        .post(route('opciones.crear'), {
          nombre,
          ruta,
          icono,
          codMenuF: this.menuSeleccionado.codMenu,
        })
        .then(() => this.verOpciones(this.menuSeleccionado.codMenu));
    },
    editarOpcion(opcion) {
      const nombre = prompt("Nuevo nombre de la opción:", opcion.nombre);
      const ruta = prompt("Nueva ruta de la opción:", opcion.ruta);
      const icono = prompt("Nuevo ícono de la opción:", opcion.icono);
      axios
        .put(route('opciones.actualizar', { codOpcion: opcion.codOpcion }), { nombre, ruta, icono })
        .then(() => this.verOpciones(this.menuSeleccionado.codMenu));
    },
    eliminarOpcion(codOpcion) {
      axios.delete(route('opciones.eliminar', { codOpcion })).then(() => this.verOpciones(this.menuSeleccionado.codMenu));
    },
    verPermisos(codOpcion) {
      axios.get(route('permisos.listar', { codOpcion })).then((response) => {
        this.permisos = response.data;
        this.opcionSeleccionada = this.opciones.find((o) => o.codOpcion === codOpcion);
      });
    },

    // CRUD Permisos
    cerrarPermisos() {
      this.opcionSeleccionada = null;
      this.permisos = [];
    },
    nuevoPermiso() {
      const accion = prompt("Acción del permiso:");
      axios
        .post(route('permisos.crear'), {
          accion,
          codOpcionF: this.opcionSeleccionada.codOpcion,
        })
        .then(() => this.verPermisos(this.opcionSeleccionada.codOpcion));
    },
    eliminarPermiso(codPermiso) {
      axios.delete(route('permisos.eliminar', { codPermiso })).then(() => this.verPermisos(this.opcionSeleccionada.codOpcion));
    },
    verTiposUsuario(codPermiso) {
      axios.get(route('tiposUsuario.listar', { codPermiso })).then((response) => {
        this.tiposUsuario = response.data;
        this.permisoSeleccionado = this.permisos.find((p) => p.codPermiso === codPermiso);
      });
    },

    // CRUD Tipos de Usuario
    cerrarTiposUsuario() {
      this.permisoSeleccionado = null;
      this.tiposUsuario = [];
    },
    agregarTipoUsuario() {
      const codTipoUsuarioF = prompt("ID del tipo de usuario a agregar:");
      axios
        .post(route('tiposUsuario.agregar', { codPermiso: this.permisoSeleccionado.codPermiso }), { codTipoUsuarioF })
        .then(() => this.verTiposUsuario(this.permisoSeleccionado.codPermiso));
    },
    quitarTipoUsuario(codTipoUsuario) {
      axios
        .delete(route('tiposUsuario.quitar', { codPermiso: this.permisoSeleccionado.codPermiso, codTipoUsuario }))
        .then(() => this.verTiposUsuario(this.permisoSeleccionado.codPermiso));
    },
  },
  mounted() {
    this.cargarMenus();
  },
};
</script>


<style>
/* Agrega estilos para mejorar la presentación */
table {
  width: 100%;
  border-collapse: collapse;
  margin-bottom: 20px;
}
th, td {
  border: 1px solid #ccc;
  padding: 10px;
  text-align: left;
}
button {
  margin-right: 5px;
}
</style>
