<template>
  <div>
    <!-- Navbar -->
    <plantillanavcliente :userName="$page.props.auth.user.name" />

    <!-- Contenedor Principal -->
    <main class="container mt-5">
      <!-- Tabla de Productos -->
      <div class="card mb-4">
        <h1 class="text-center mb-4">Lista de tus productos que comprarás</h1>
        <div class="table-responsive">
          <table class="table table-bordered table-striped">
            <thead>
              <tr>
                <th class="font-weight-bold">Imagen</th>
                <th class="font-weight-bold">Producto</th>
                <th class="font-weight-bold">Cantidad</th>
                <th class="font-weight-bold">Precio Unitario</th>
                <th class="font-weight-bold">Total</th>
                <th class="font-weight-bold">Acciones</th>
              </tr>
            </thead>
            <tbody>
              <tr class="fila" v-for="(producto, index) in productos" :key="index">
                <td>
                  <img
                    v-if="producto.imagen_url"
                    :src="`/storage/uploads/${producto.imagen_url}`"
                    alt="Imagen del producto"
                    class="img-thumbnail"
                    style="max-width: 120px;"
                  />
                  <span v-else>No tiene imagen</span>
                </td>
                <td>{{ producto.nombre }}</td>
                <td>
                  <input
                    type="number"
                    v-model.number="producto.cantidad"
                    class="form-control form-control-sm cantidad-input"
                    :min="1"
                    :max="producto.stock"
                    @input="validarCantidad(index)"
                  />
                </td>
                <td>Bs. {{ producto.precio.toFixed(2) }}</td>
                <td>Bs. <span>{{ (producto.precio * producto.cantidad).toFixed(2) }}</span></td>
                <td>
                  <button @click="eliminarProducto(index)" class="btn btn-primary btn-sm">Quitar</button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Subtotal -->
      <div class="container mt-4 d-flex justify-content-end">
        <div class="card p-3 shadow-sm" style="max-width: 300px; width: 100%;">
          <div class="d-flex justify-content-between align-items-center">
            <div class="font-weight-bold">TOTAL:</div>
            <div class="font-weight-bold ml-3">{{ totalCarrito }} Bs.</div>
          </div>
        </div>
      </div>

      <!-- Contenedor para Formulario de Pago e iframe -->
      <div class="row mb-4">
        <div class="col-md-4 mb-4">
          <div class="card h-100">
            <div class="card-body">
              <h1 class="text-center mb-4">Realizar pago</h1>
              <form @submit.prevent="realizarPago">
                <div class="form-group">
                  <label for="tipoServicio">Tipo de Servicio</label>
                  <select v-model="tipoServicio" id="tipoServicio" class="form-control ml-4" required>
                    <option value="1">Servicio QR</option>
                    <option value="2">Tigo Money</option>
                  </select>
                </div>
                <div class="form-group">
                  <label for="telefono">Número de cuenta</label>
                  <input type="text" v-model="telefono" id="telefono" class="form-control ml-4" readonly />
                </div>

                <div class="d-flex justify-content-end mt-4">
                  <button type="button" class="btn btn-secondary mr-2" @click="volver">Atrás</button>
                  <button type="submit" class="btn btn-primary">Mostrar Qr</button>
                </div>
              </form>
            </div>
          </div>
        </div>

        <!-- Contenedor del iframe para QR -->
        <div class="col-md-6">
          <div class="d-flex justify-content-center align-items-center h-100">
            <iframe name="QrImage" class="w-100" style="height: 400px; border: 1px solid #ddd;"></iframe>
          </div>
        </div>
      </div>

      <!-- Botón para Registrar Venta -->
      <div class="d-flex justify-content-center mb-4">
        <button
          type="button"
          @click="confirmarCompra"
          class="btn btn-primary"
          :disabled="productos.length === 0"
        >
          Comprar
        </button>
      </div>
    </main>
  </div>
  <!-- Footer -->
</template>

<script>
import plantillanavcliente from '@/Layouts/plantillanavcliente.vue';
import { Inertia } from '@inertiajs/inertia';


export default {
  components: {
    plantillanavcliente,
  },
  data() {
    return {
      productos: this.$page.props.productos.map((producto) => ({
        ...producto,
        cantidad: this.$page.props.cantidades[producto.codProducto] || 1,
      })),
      tipoServicio: "1",
      telefono: "123456789",
    };
  },
  computed: {
    totalCarrito() {
      return this.productos.reduce((total, producto) => total + producto.precio * producto.cantidad, 0).toFixed(2);
    },
  },
  methods: {
    validarCantidad(index) {
      const producto = this.productos[index];
      if (producto.cantidad > producto.stock) {
        producto.cantidad = producto.stock;
        this.mostrarAlerta("La cantidad no puede ser mayor que el stock disponible.");
      }
    },
    eliminarProducto(index) {
      this.productos.splice(index, 1); // Elimina el producto
      // Verifica si no hay más productos
      if (this.productos.length === 0) {
        this.redirigirVistaCliente();
      }
    },
    redirigirVistaCliente() {
      // Usa Inertia para redirigir
      Inertia.visit(route('vista-cliente'));
    },
    mostrarAlerta(mensaje) {
      alert(mensaje);
    },
    realizarPago() {
      alert("Iniciar pago");
    },
    confirmarCompra() {
      axios
        .post("/venta/create", {
          productos: this.productos,
          tnMonto: parseFloat(this.totalCarrito),
        })
        .then((response) => {
          if (response.data.redirect) {
            window.location.href = response.data.redirect;
          } else {
            alert(response.data.success || "Compra realizada con éxito.");
          }
        })
        .catch((error) => {
          console.error(error);
          alert("Hubo un error al registrar la compra.");
        });
    },
    volver() {
      this.$router.push({ name: "cliente" });
    },
  },
};
</script>

<style scoped>
.table {
  width: 100%;
  margin-bottom: 20px;
}

.table th,
.table td {
  text-align: center;
  vertical-align: middle;
}

.font-weight-bold {
  font-weight: bold;
}

.table td img {
  display: block;
  margin: auto;
}

.card {
  margin-bottom: 25px;
}

.card-body {
  padding: 20px;
}

.container {
  margin-top: 20px;
}

.fila {
  height: 60px;
}

.table td,
.table th {
  padding: 15px;
}

.d-flex {
  display: flex;
}

.cantidad-input {
  color: black;
}

.row.mb-4 {
  display: flex;
  flex-wrap: wrap;
}

.col-md-6 {
  flex: 1;
  padding: 15px;
}

.card-body form .form-group {
  margin-bottom: 1.5rem;
}

.card-body form .form-control {
  margin-bottom: 1rem;
}

.card-body form .btn {
  padding: 0.75rem 1.5rem;
  font-size: 1rem;
}

h1 {
  font-size: 22px;
  margin-top: 10px;
  margin-bottom: 25px;
  font-weight: bold;
}
</style>
