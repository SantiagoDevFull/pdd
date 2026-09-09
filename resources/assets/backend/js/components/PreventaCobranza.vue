<template>
    <div v-show="view_cobranzas">
        <button @click="openModalCobranza" class="btn btn-success btn-lg w-100">
            <i class="fas fa-money-bill-wave"></i> Registrar Cobranzas
        </button>

        <div class="modal fade" id="modal_cobranza" tabindex="-1" data-backdrop="static" data-keyboard="false"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
                <div class="modal-content">
                    <!--begin::Form-->
                    <form class="kt-form" @submit.prevent="search">
                        <div class="modal-header bg-primary">
                            <h5 class="modal-title text-white" id="exampleModalLabel">Registrar Cobranza</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="kt-portlet__body">
                                <div class="row">
                                    <div class="col-lg-3">
                                        <div class="form-group">
                                            <label class="form-control-label">Cliente:</label>
                                            <select class="form-control" name="client_cobranza_id"
                                                id="client_cobranza_id" v-model="model.client_cobranza_id"
                                                @focus="$parent.clearErrorMsg($event)">
                                                <option value="">Seleccionar</option>
                                            </select>
                                            <div id="client_cobranza_id-error" class="error invalid-feedback"></div>
                                        </div>
                                    </div>

                                    <div class="col-lg-3">
                                        <div class="form-group">
                                            <label class="form-control-label">Tipo de Cancelación:</label>
                                            <select class="form-control" name="payment_method_id" id="payment_method_id"
                                                @change="State()" v-model="model.payment_method_id"
                                                @focus="$parent.clearErrorMsg($event)">
                                                <option value="">Seleccionar</option>
                                                <option v-for="payment_method in payment_methods"
                                                    :value="payment_method.id" :key="payment_method.id">{{
                                                        payment_method.name }}</option>
                                            </select>
                                            <div id="payment_method_id-error" class="error invalid-feedback"></div>
                                        </div>
                                    </div>

                                    <div class="col-lg-6" v-if="this.model.payment_method_id == 2">
                                        <div class="form-group">
                                            <label class="form-control-label">Seleccionar deposito pendiente:</label>
                                            <select class="form-control" name="deposit_id" id="deposit_id"
                                                v-model="model.deposit_id" @focus="$parent.clearErrorMsg($event)">
                                                <option value="">Seleccionar</option>
                                                <option v-for="deposit in deposits" :value="deposit.id"
                                                    v-bind:key="deposit.id">{{
                                                        deposit.texto }}</option>
                                            </select>
                                            <div id="deposit_id-error" class="error invalid-feedback"></div>
                                        </div>
                                    </div>

                                    <div class="col-lg-3">
                                        <label class="form-label">Monto</label>
                                        <input type="text" class="form-control" v-model="model.amount"
                                            @input="model.amount = model.amount.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1')"
                                            @focus="$parent.clearErrorMsg($event)">
                                    </div>
                                </div>

                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary" id="boton_buscar">
                                Buscar
                            </button>
                        </div>
                    </form>
                    <!--end::Form-->

                    <div class="modal-header bg-success">
                        <h5 class="modal-title text-white" id="exampleModalLabel">Lista de Ventas Pendientes de Cobranza
                        </h5>
                    </div>
                    <div class="modal-body">
                        <div class="row mt-4" v-if="sale_cobranzas.length">
                            <div class="col-12">
                                <table class="table table-bordered table-sm">
                                    <thead>
                                        <tr class="bg-success">
                                            <th class="text-white">#</th>
                                            <th class="text-white">Tipo</th>
                                            <th class="text-white">Serie Documento</th>
                                            <th class="text-white">N° Documento</th>
                                            <th class="text-white">Fecha de Venta</th>
                                            <th class="text-white">Total</th>
                                            <th class="text-white">Saldo</th>
                                            <th class="text-white">Pagado</th>
                                            <th class="text-white">Monto a Pagar</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(cobranza, index) in sale_cobranzas" :key="index">
                                            <td>{{ index + 1 }}</td>
                                            <td>{{ cobranza.warehouse_document_type_name }}</td>
                                            <td>{{ cobranza.referral_serie_number }}</td>
                                            <td>{{ cobranza.referral_voucher_number }}</td>
                                            <td>{{ cobranza.sale_date }}</td>
                                            <td>{{ cobranza.total_perception }}</td>
                                            <td>{{ cobranza.balance }}</td>
                                            <td>{{ cobranza.paid }}</td>
                                            <td>

                                                <input type="text" class="form-control form-control-sm"
                                                    v-model="cobranza.monto_input"
                                                    :class="{ 'is-invalid': parseFloat(cobranza.monto_input) > cobranza.balance }"
                                                    @input="
                                                        cobranza.monto_input = cobranza.monto_input
                                                            .replace(/[^0-9.]/g, '')
                                                            .replace(/(\..*?)\..*/g, '$1')
                                                            .replace(/^(\d+)(\.\d{0,2})?.*$/, '$1$2');
                                                    ">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="7" class="text-right font-weight-bold">Total ingresado:</td>
                                            <td class="font-weight-bold">{{ totalIngresado }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="row mt-4" v-else>
                            <div class="col-12 text-center">
                                <p class="text-muted font-weight-bold">
                                    No hay registros para mostrar
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <div class="mt-3 text-right" v-if="sale_cobranzas.length">
                            <button type="button" class="btn btn-success" @click="registrarMontos">
                                Registrar
                            </button>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</template>

<script>
import Swal from 'sweetalert2';
import EventBus from '../event-bus';
import axios from 'axios';

export default {
    props: {
        url_get_clients_cobranzas: { type: String, default: '' },
        get_deposits: { type: String, default: '' },
        url_get_sale_cobranzas: { type: String, default: '' },
        url_update_cobranzas: { type: String, default: '' },
        payment_methods: { type: Array, default: () => [] },
        currencies: { type: Array, default: () => [] },
        csrf_token: {
            type: String,
            default: ''
        },
    },
    data() {
        return {
            model: {
                client_cobranza_id: '',
                payment_method_id: '',
                currency_id: '1',
                deposit_id: '',
                amount: '',
            },
            sale_cobranzas: [],
            guia_id: '',
            view_cobranzas: false,
            deposits: [],

        }
    },
    computed: {
        totalIngresado() {
            const total = this.sale_cobranzas.reduce((acc, c) => {
                let val = parseFloat(c.monto_input);
                if (!isNaN(val)) acc += val;
                return acc;
            }, 0);

            return Math.round(total * 100) / 100;

        },
        hayMontosInvalidos() {
            return this.sale_cobranzas.some(c => parseFloat(c.monto_input) > c.balance);
        }
    },

    created() {
        EventBus.$on('create_modal', () => {

            $('#client_cobranza_id').val(null).trigger('change');
            this.model.client_cobranza_id = '';
            this.model.payment_method_id = '';
            this.model.currency_id = '1';
            this.model.amount = '';
            this.sale_cobranzas = [];
            this.model.deposit_id = '';

            $('.kt-form').find('input').prop('disabled', false);
            $('.kt-form').find('select').prop('disabled', false);

            $('#boton_buscar').prop('disabled', false);

            $('#modal_cobranza').modal('show');

            this.view_cobranzas = true;
        });

        EventBus.$on('open_cobranzas', (model) => {

            this.view_cobranzas = true;
            this.guia_id = model.guia_id;
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
    },


    mounted() {
        this.newSelect3();

    },
    methods: {
        openModalCobranza() {
            EventBus.$emit('create_modal');
        },

        registrarMontos() {
            if (this.hayMontosInvalidos) {
                Swal.fire('Atención', 'Hay montos mayores al saldo', 'warning');
                return;
            }

            const payload = this.sale_cobranzas
                .filter(c => c.monto_input && !isNaN(c.monto_input))
                .map(c => ({
                    sale_id: c.sale_id,
                    monto: c.monto_input
                }));

            if (!payload.length) {
                Swal.fire('Atención', 'No hay montos para registrar', 'warning');
                return;
            }

            if (this.model.amount != this.totalIngresado) {
                Swal.fire('Atención', 'El total ingresado no coincide con el total a pagar', 'warning');
                return;
            }

            EventBus.$emit("loading", true);
            axios.post(this.url_update_cobranzas,
                { cobranzas: payload, model: this.model, guia_id: this.guia_id })
                .then(res => {
                    EventBus.$emit("loading", false);

                    EventBus.$emit("add_cobranza_list", res.data);

                    Swal.fire('Guardado', 'Los montos fueron registrados', 'success');
                    $('#modal_cobranza').modal('hide');
                })
                .catch(err => {
                    EventBus.$emit("loading", false);
                    console.log(err.response);
                    Swal.fire('Error', 'No se pudieron registrar los montos', 'error');
                });
        },
        newSelect3: function () {

            let vm = this;
            let token = vm.csrf_token;

            $("#client_cobranza_id").select2({
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
                    url: this.url_get_clients_cobranzas,
                    dataType: 'json',
                    delay: 250,
                    type: 'POST',
                    data: function (params) {
                        var queryParameters = {
                            q: params.term,
                            _token: token,
                            guia_id: vm.guia_id
                        }

                        return queryParameters;
                    },
                    processResults: function (data, params) {

                        return { results: data };
                    },
                    cache: true
                },
                minimumInputLength: 1,
            }).on('select2:select', function (e) {
                const data = e.params.data;
                vm.model.client_cobranza_id = parseInt(data.id);
            })
                .on('select2:unselect', function () {
                    vm.model.client_cobranza_id = '';
                    $('#client_cobranza_id').val(null).trigger('change');
                });
        },
        search() {


            if (this.model.client_cobranza_id == '') {
                Swal.fire('Atención', 'Debe seleccionar un Cliente', 'warning');
                return;
            }

            if (this.model.payment_method_id == '') {
                Swal.fire('Atención', 'Debe seleccionar un Metodo de Pago', 'warning');
                return;
            }

            if (this.model.payment_method_id == 2 && this.model.deposit_id == '') {
                Swal.fire('Atención', 'Debe seleccionar un Deposito', 'warning');
                return;
            }

            if (this.model.amount == '' || isNaN(this.model.amount) || this.model.amount <= 0 || this.model.amount == 0 || this.model.amount == 0.00) {
                Swal.fire('Atención', 'Debe ingresar un Monto valido', 'warning');
                return;
            }


            if (this.model.payment_method_id == 2) {
                const deposit = this.deposits.find(d => d.id == this.model.deposit_id);
                if (Number(deposit.total_pend) < Number(this.model.amount)) {
                    Swal.fire('Atención', 'El monto ingresado es mayor al saldo del Deposito', 'warning');
                    return;
                }
            }


            EventBus.$emit("loading", true);
            axios.post(this.url_get_sale_cobranzas, {
                client_id: this.model.client_cobranza_id,
            })
                .then((response) => {
                    EventBus.$emit("loading", false);
                    this.sale_cobranzas = response.data.map(c => {
                        c.monto_input = '';
                        return c;
                    });
                    if (!this.sale_cobranzas.length) {
                        Swal.fire('Atención', 'No se encontraron registros', 'info');

                        $('.kt-form').find('input').prop('disabled', false);
                        $('.kt-form').find('select').prop('disabled', false);

                        $('#boton_buscar').prop('disabled', false);

                    } else {
                        $('.kt-form').find('input').prop('disabled', true);
                        $('.kt-form').find('select').prop('disabled', true);

                        $('#boton_buscar').prop('disabled', true);

                    }

                })
                .catch((error) => {
                    EventBus.$emit("loading", false);
                    console.log(error.response);
                });
        }
    }
};
</script>

<style>
.is-invalid {
    border: 1px solid red !important;
    background-color: #ffe5e5 !important;
}

.select2-container {
    width: 100% !important;
}
</style>