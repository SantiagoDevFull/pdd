<template>
  <div v-show="form_liquidation_modal">
    <div class="full-container d-flex flex-column">
      <div class="cardex shadow-lg border-0 rounded-4 p-4 w-100 h-100">
        <div class="card-header text-white text-center fw-bold  mb-4"
          style="background: linear-gradient(45deg, #4f46e5, #6366f1); border-radius: 12px; font-size: 30px;">
          💰 Total a liquidar: {{ Number(total_liquidar).toFixed(2) }} <br>
          <small v-if="this.payment_id_client == 4">
            {{ this.payment_name_client }}
          </small>
          <small v-else>
            {{ this.payment_name_client }} S/ {{ Number(total_credit_limit).toFixed(2) }}
          </small>
        </div>

        <div class="text-start mb-3">
          <button type="button"
            class="btn btn-outline-secondary btn-lg px-4 fw-bold shadow-sm rounded-pill d-flex align-items-center gap-2"
            @click="goBack()">
            <i class="fas fa-arrow-left"></i> Retroceder
          </button>
        </div>

        <form class="custom-form" @submit.prevent="addLiquidation">
          <div class="row g-4">

            <div class="col-lg-3">
              <label class="form-label fw-semibold">Condición de Pago</label>
              <select class="form-select custom-select" v-model="model.payment_id"
                @focus="$parent.clearErrorMsg($event)">
                <option value="" disabled>Seleccionar</option>
                <option v-for="payment in payments" :key="payment.id" :value="payment.id">
                  {{ payment.name }}
                </option>
              </select>
            </div>

            <div class="col-lg-3" v-if="model.payment_id == 1 || model.payment_id == 4">
              <label class="form-label fw-semibold">Forma de Pago</label>
              <select class="form-select custom-select" v-model="model.payment_method_id"
                @focus="$parent.clearErrorMsg($event)">
                <option value="" disabled>Seleccionar</option>
                <option v-for="pm in payment_methods" :key="pm.id" :value="pm.id">
                  {{ pm.name }}
                </option>
              </select>
            </div>

            <div class="col-lg-6" v-if="model.payment_method_id == 2 && model.payment_id == 1">
              <label class="form-control-label">Seleccionar deposito pendiente:</label>
              <select class="form-control" name="deposit_id" id="deposit_id" v-model="model.deposit_id"
                @focus="$parent.clearErrorMsg($event)">
                <option value="">Seleccionar</option>
                <option v-for="deposit in deposits" :value="deposit.id" v-bind:key="deposit.id">{{
                  deposit.texto }}</option>
              </select>

            </div>


            <div class="col-lg-3">
              <label class="form-label fw-semibold">Monto</label>
              <input type="text" class="form-control custom-input" v-model="model.amount"
                @input="model.amount = model.amount.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1')"
                @focus="$parent.clearErrorMsg($event)">
            </div>

            <div class="col-lg-3" id="empleado">
              <div class="form-group">
                <label class="form-control-label">Empleado:</label>
                <select class="form-control" name="employee_id" id="employee_id" v-model="model.employee_id"
                  @focus="$parent.clearErrorMsg($event)">
                  <option value="">Seleccionar</option>
                </select>
                <div id="employee_id-error" class="error invalid-feedback"></div>
              </div>
            </div>

          </div>

          <div class="text-end mt-4">
            <button type="submit" class="btn btn-lg btn-primary px-5 fw-bold shadow-sm rounded-pill">
              Agregar
            </button>
          </div>
        </form>

        <div class="mt-4 text-center">
          <h2 class="fw-bold text-primary zoom-text">
            Pendiente a liquidar: S/ {{ Number(total_liquidar_saldo).toFixed(2) }}
          </h2>
          <h2 class="fw-bold text-success" v-if="this.payment_id_client == 4">
            {{ this.payment_name_client }}
          </h2>
          <h2 class="fw-bold text-success" v-else>
            {{ this.payment_name_client }} S/ {{ Number(total_credit_limit_saldo).toFixed(2) }}
          </h2>
        </div>

        <div class="mt-5">
          <h4 class="fw-bold text-secondary mb-3">🛒 Detalles de la Liquidación</h4>
          <div v-if="setDetailsLiq.length === 0" class="text-center text-muted py-4 border rounded">
            <i class="fas fa-info-circle"></i> No hay liquidaciones agregadas
          </div>
          <table v-else class="table table-hover table-bordered align-middle mt-3">
            <thead class="table-primary text-center">
              <tr>
                <th>#</th>
                <th>Condición de Pago</th>
                <th>Forma de Pago</th>
                <th>Banco</th>
                <th>N° Operación</th>
                <th>Fecha de Emisión</th>
                <th>Moneda</th>
                <th>Monto</th>
                <th>Empleado</th>
                <th>Acción</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(detail, index) in setDetailsLiq" :key="index">
                <td>{{ index + 1 }}</td>
                <td>{{ detail.payment_name }}</td>
                <td>{{ detail.payment_method_name }}</td>
                <td>{{ detail.bank_account }}</td>
                <td>{{ detail.operation_number }}</td>
                <td>{{ detail.date_emision ? new Date(detail.date_emision).toISOString().slice(0, 10) :
                  '' }}</td>
                <td>{{ detail.currency_name }}</td>
                <td>S/ {{ detail.amount.toFixed(2) }}</td>
                <td>{{ detail.employee_name }}</td>
                <td>
                  <button class="btn btn-danger btn-sm" @click="removeLiquidation(index)">
                    <i class="fas fa-trash"></i>
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
          <div class="mt-4" v-if="setDetailsLiq.length > 0">
            <button class="btn btn-success btn-lg w-100" @click.prevent="liquidation()">
              <i class="fas fa-money-bill-wave"></i> Pagar
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style>
.zoom-text {
  display: inline-block;
  animation: zoomInOut 1.5s infinite ease-in-out;
}

@keyframes zoomInOut {

  0%,
  100% {
    transform: scale(1);
  }

  50% {
    transform: scale(1.2);
  }
}

.full-container {
  width: 100vw;
  min-height: 100vh;
  padding: 20px;
  background: #f9fafb;
}

.custom-select,
.custom-input {
  height: 55px;
  font-size: 1.1rem;
  border-radius: 12px;
  border: 2px solid #d1d5db;
  padding: 10px 15px;
  transition: 0.3s;
}

.custom-select:focus,
.custom-input:focus {
  border-color: #4f46e5;
  box-shadow: 0 0 8px rgba(79, 70, 229, 0.3);
}

.cardex {
  width: 100%;
  min-height: calc(100vh - 40px);
}
</style>

<script>
import Swal from "sweetalert2";
import EventBus from "../event-bus";
import Datetime from "vue-datetime";
import "vue-datetime/dist/vue-datetime.css";

Vue.use(Datetime);

export default {
  props: {
    payments: { type: Array, default: () => [] },
    currencies: { type: Array, default: () => [] },
    bank_accounts: { type: Array, default: () => [] },
    url_get_payment_methods: { type: String, default: "" },
    get_deposits: { type: String, default: "" },
    url_verify_credit: { type: String, default: "" },
    url_get_employees: { type: String, default: "" },
    csrf_token: {
      type: String,
      default: ''
    },
  },
  data() {

    return {
      model: {
        payment_id: "",
        payment_method_id: "",
        currency_id: "1",
        bank_account_id: "",
        operation_number: "",
        date_emision: "",
        amount: "",
        employee_id: "",
        deposit_id: "",
      },
      payment_methods: [],
      liquidations: [],
      deposits: [],
      client_id: "",
      total_credit_limit: "",
      total_credit_limit_saldo: "",
      total_liquidar: "",
      total_liquidar_saldo: "",
      form_liquidation_modal: false,
      payment_id_client: "",
      payment_name_client: "",
      employee_name: "",
      totalCredito: "",
      guia_id: "",
    };
  },
  computed: {
    setDetailsLiq() {
      return this.liquidations;
    },
  },
  mounted() {

    this.newSelect5();
    $('#empleado').hide();
  },
  created() {
    EventBus.$on("preventa_liquidation", (client_id, total_liquidar, totalCredito, guia_id) => {
      this.form_liquidation_modal = true;
      this.client_id = client_id;
      this.total_liquidar = total_liquidar;
      this.total_liquidar_saldo = total_liquidar;
      this.totalCredito = totalCredito;
      this.guia_id = guia_id;

      this.verifyCredit();
    });
  },
  watch: {
    'model.payment_method_id': function (val) {
      EventBus.$emit('loading', true);
      if (val == 2) {

        axios.post(this.get_deposits, {
          guia_id: this.guia_id
        })
          .then(response => {
            EventBus.$emit('loading', false);
            this.deposits = response.data;
          }).catch(error => {
            EventBus.$emit('loading', false);


            console.log(error);
            console.log(error.response);
          });
      } else {
        EventBus.$emit('loading', false);
        this.deposits = [];
        this.model.deposit_id = '';
      }
    },
    "model.payment_id"(val) {
      if (val == 1 || val == 4) {

        if ((this.model.payment_id == 2 || this.model.payment_id == 4) && this.payment_id_client == 1) {
          this.$parent.alertMsg({
            type: 5,
            title: "Error",
            msg: "El cliente no cuenta con la Condición de Pago disponible",
          });
          this.model.payment_id = "";
          return;
        } else if ((this.model.payment_id == 4) && this.payment_id_client == 2) {
          this.$parent.alertMsg({
            type: 5,
            title: "Error",
            msg: "El cliente no cuenta con la Condición de Pago disponible",
          });
          this.model.payment_id = "";
          return;
        } else if ((this.model.payment_id == 2) && this.payment_id_client == 4) {
          this.$parent.alertMsg({
            type: 5,
            title: "Error",
            msg: "El cliente no cuenta con la Condición de Pago disponible",
          });
          this.model.payment_id = "";
          return;
        } else if (this.model.payment_id == 4) {
          this.model.payment_method_id = 10;
          $('#empleado').show();
        } else {
          this.model.employee_id = "";
          this.employee_name = "";
          $('#employee_id').val(null).trigger('change');
          $('#empleado').hide();
        }

        EventBus.$emit("loading", true);
        axios
          .post(this.url_get_payment_methods, {
            payment_id: val,
          })
          .then((response) => {
            EventBus.$emit("loading", false);
            this.payment_methods = response.data;
          })
          .catch((error) => {
            console.log(error.response);
          });
      } else {

        if (this.model.payment_id == 2 && this.payment_id_client != 2) {
          this.$parent.alertMsg({
            type: 5,
            title: "Error",
            msg: "El cliente no cuenta con la Condición de Pago disponible",
          });
          this.model.payment_id = "";
          return;
        } else {
          this.model.employee_id = "";
          this.employee_name = "";
          $('#employee_id').val(null).trigger('change');
          $('#empleado').hide();
        }

      }

      this.model.currency_id = "1";
      this.model.bank_account_id = "";
      this.model.date_emision = "";

    },
  },
  methods: {
    goBack() {

      this.form_liquidation_modal = false;
      this.liquidations = [];

      this.model.payment_id = "";
      this.model.payment_method_id = "";
      this.model.currency_id = "1";
      this.model.bank_account_id = "";
      this.model.date_emision = "";
      this.model.amount = "";
      this.model.employee_id = "";

      this.payment_methods = [];
      this.total_liquidar = "";
      this.total_liquidar_saldo = "";

      this.total_credit_limit = "";
      this.total_credit_limit_saldo = "";

      this.client_id = "";
      this.employee_id = "";
      this.employee_name = "";
      $('#empleado').hide();

      EventBus.$emit('preventa_form_sale_on');
    },
    newSelect5: function () {

      let vm = this;
      let token = vm.csrf_token;

      $("#employee_id").select2({
        placeholder: "Buscar",
        allowClear: true,
        language: {
          noResults: function () {
            return 'No hay resultados';
          },
          searching: function () {
            return 'Buscando...';
          },
          inputTooShort: function () {
            return 'Ingresa 1 o más caracteres';
          },
          errorLoading: function () {
            return 'No se pudo cargar la información'
          }
        },
        ajax: {
          url: this.url_get_employees,
          dataType: 'json',
          delay: 250,
          type: 'POST',
          data: function (params) {
            var queryParameters = {
              q: params.term,
              _token: token,
            }

            return queryParameters;
          },
          processResults: function (data, params) {
            console.log("Resultados recibidos:", data);
            return { results: data };
          },
          cache: true
        },
        minimumInputLength: 1,
      }).on('select2:select', function (e) {

        const data = e.params.data;
        vm.model.employee_id = parseInt(data.id);
        vm.employee_name = data.text;

      })
        .on('select2:unselect', function () {

          vm.model.employee_id = '';
          vm.employee_name = '';
          $('#employee_id').val(null).trigger('change');
        });
    },
    verifyCredit() {
      axios.post(this.url_verify_credit, {
        client_id: this.client_id,
      })
        .then((response) => {
          this.payment_id_client = response.data.payment_id;

          if (this.payment_id_client == 2) {
            this.payment_name_client = "Crédito disponible : ";
            this.total_credit_limit_saldo = response.data.credit_limit - this.totalCredito;
          } else if (this.payment_id_client == 4) {
            this.payment_name_client = "Descuento Personal disponible";
          } else {
            this.payment_name_client = "Sin credito y descuento disponible : ";
          }

          this.total_credit_limit = response.data.credit_limit;

        })
        .catch((error) => {
          console.log(error.response);
        });
    },
    liquidation() {

      Swal.fire({
        title: '¿Estás seguro?',
        text: 'Esta liquidación será guardada.',
        type: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sí, guardar',
        cancelButtonText: 'Cancelar'
      }).then((result) => {
        if (result.value) {

          if (parseFloat(this.total_liquidar_saldo) > 0) {
            console.log("Saldo real:", this.total_liquidar_saldo, typeof this.total_liquidar_saldo);
            this.$parent.alertMsg({
              type: 5,
              title: "Error",
              msg: "Pendiente de liquidación: S/ " + parseFloat(this.total_liquidar_saldo).toFixed(2),
            });
            return;
          }

          EventBus.$emit("preventa_add_liquidations", this.liquidations);
          this.form_liquidation_modal = false;
          this.liquidations = [];

          this.model.payment_id = "";
          this.model.payment_method_id = "";
          this.model.currency_id = "1";
          this.model.bank_account_id = "";
          this.model.date_emision = "";
          this.model.amount = "";
          this.model.employee_id = "";

          this.payment_methods = [];
          this.total_liquidar = "";
          this.total_liquidar_saldo = "";

          this.total_credit_limit = "";
          this.total_credit_limit_saldo = "";

          this.client_id = "";
          this.employee_id = "";
          this.employee_name = "";
          $('#empleado').hide();

        }
      });

    },

    removeLiquidation(index) {
      const removed = this.liquidations[index];

      Swal.fire({
        title: '¿Estás seguro?',
        text: 'Esta liquidación será eliminada.',
        type: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
      }).then((result) => {
        if (result.value) {
          if (removed.payment_id === 1) {

            this.total_liquidar_saldo = Math.round((parseFloat(this.total_liquidar_saldo) + parseFloat(removed.amount)) * 100) / 100;
          } else {
            this.total_credit_limit_saldo += removed.amount;
            this.total_liquidar_saldo = Math.round((parseFloat(this.total_liquidar_saldo) + parseFloat(removed.amount)) * 100) / 100;
          }

          this.liquidations.splice(index, 1);

          Swal.fire({
            title: 'Eliminado',
            text: 'La liquidación se eliminó correctamente.',
            type: 'success',
            timer: 1500,
            showConfirmButton: false
          });
        }
      });
    },
    addLiquidation() {

      if (this.model.payment_id == '') {
        this.$parent.alertMsg({
          type: 5,
          title: 'Error',
          msg: 'Debe seleccionar una Condición de Pago.'
        });
        return;
      }
      if ((this.model.payment_id == 1 || this.model.payment_id == 4) && this.model.payment_method_id == '') {
        this.$parent.alertMsg({
          type: 5,
          title: 'Error',
          msg: 'Debe seleccionar una Forma de Pago.'
        });
        return;
      }

      if (this.model.payment_id == 1 && this.model.payment_method_id == 2 && this.model.deposit_id == '') {
        this.$parent.alertMsg({
          type: 5,
          title: 'Error',
          msg: 'Debe seleccionar un Deposito.'
        });
        return;
      }

      if (this.model.amount == '' || this.model.amount == 0 || this.model.amount == null || this.model.amount <= 0) {
        this.$parent.alertMsg({
          type: 5,
          title: 'Error',
          msg: 'Debe ingresar un Monto válido.'
        });
        return;
      }

      if (this.model.payment_method_id == 2) {
        const deposit = this.deposits.find(d => d.id == this.model.deposit_id);
        if (Number(deposit.total_pend) < Number(this.model.amount)) {
          Swal.fire('Atención', 'El monto ingresado es mayor al saldo del Deposito', 'warning');
          return;
        }
      }

      if (this.model.payment_id == 4 && this.model.employee_id == '') {
        this.$parent.alertMsg({
          type: 5,
          title: 'Error',
          msg: 'Debe seleccionar un Empleado.'
        });
        return;
      }

      if (this.model.payment_id == 2 || this.model.payment_id == 4) {
        if (Number(this.model.amount) > Number(this.total_credit_limit_saldo) && this.model.payment_id == 2) {
          this.$parent.alertMsg({
            type: 5,
            title: 'Error',
            msg: 'El monto no puede ser mayor al monto del crédito disponible.'
          });
          return;
        } else if (Number(this.model.amount) > Number(this.total_liquidar_saldo)) {
          this.$parent.alertMsg({
            type: 5,
            title: 'Error',
            msg: 'El monto excede el saldo disponible.'
          });
          return;

        } else {

          this.total_liquidar_saldo = Math.round((parseFloat(this.total_liquidar_saldo) - parseFloat(this.model.amount)) * 100) / 100;
          this.total_credit_limit_saldo = Math.round((parseFloat(this.total_credit_limit_saldo) - parseFloat(this.model.amount)) * 100) / 100;

        }


      } else {
        if (Number(this.model.amount) > Number(this.total_liquidar_saldo)) {
          this.$parent.alertMsg({
            type: 5,
            title: 'Error',
            msg: 'El monto no puede ser mayor al saldo disponible.'
          });
          return;
        } else {

          this.total_liquidar_saldo = Math.round((parseFloat(this.total_liquidar_saldo) - parseFloat(this.model.amount)) * 100) / 100;
        }
      }

      const payment = this.payments.find(p => p.id == this.model.payment_id);
      const paymentMethod = this.payment_methods.find(pm => pm.id == this.model.payment_method_id);
      const currency = this.currencies.find(c => c.id == this.model.currency_id);

      const deposit = this.deposits.find(d => d.id == this.model.deposit_id);

      const bankAccountId = deposit ? deposit.bank_account_id : 0;
      const bankAccount = this.bank_accounts.find(b => b.id == bankAccountId);

      const newDetail = {
        payment_id: payment ? payment.id : "",
        payment_method_id: paymentMethod ? paymentMethod.id : "",
        payment_name: payment ? payment.name : "",
        payment_method_name: paymentMethod ? paymentMethod.name : "",
        currency_id: currency ? currency.id : "",
        currency_name: currency ? currency.name : "",

        bank_account_id: bankAccount ? bankAccount.id : "",
        bank_account: bankAccount ? bankAccount.account_number : "",
        date_emision: deposit ? deposit.bank_date : this.model.date_emision,
        operation_number: deposit ? deposit.operation_number : this.model.operation_number,
        amount: parseFloat(this.model.amount),
        employee_id: this.model.employee_id ? this.model.employee_id : "",
        employee_name: this.employee_name ? this.employee_name : "",
        deposit_id: this.model.deposit_id ? this.model.deposit_id : "",
      };

      this.liquidations.push(newDetail);

      this.model = {
        payment_id: "",
        payment_method_id: "",
        currency_id: "1",
        bank_account_id: "",
        date_emision: "",
        amount: "",
        operation_number: "",
        employee_id: "",
        deposit_id: "",
      };

      $('#empleado').hide();
      $('#employee_id').val(null).trigger('change');
      this.employee_name = '';

    },
  },
};
</script>