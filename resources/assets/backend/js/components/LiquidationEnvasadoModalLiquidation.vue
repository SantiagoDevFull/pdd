<template>

    <div>
        <!--begin::Liquidation Modal-->
        <div class="modal fade" id="modal-liquidation" tabindex="-1" role="dialog" aria-labelledby="liquidationModal"
            aria-hidden="true" data-backdrop="static" data-keyboard="false">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="liquidationModal">{{ title_text }}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="kt-portlet__body">
                            <div class="row">
                                <div class="col-lg-3">
                                    <div class="form-group">
                                        <label class="form-control-label">Condición de Pago:</label>
                                        <select class="form-control" name="payment_id" id="payment_id"
                                            v-model="model.payment_id" @focus="$parent.clearErrorMsg($event)"
                                            v-on:change="checkPayment">
                                            <option value="">Seleccionar</option>
                                            <option v-for="payment in payments" :value="payment.id"
                                                v-bind:key="payment.id">{{ payment.name }}</option>
                                        </select>
                                        <div id="payment_id-error" class="error invalid-feedback"></div>
                                    </div>
                                </div>
                                <div class="col-lg-3">
                                    <div class="form-group">
                                        <label class="form-control-label">Forma de Pago:</label>
                                        <select class="form-control" name="payment_method_id" id="payment_method_id"
                                            v-model="model.payment_method" @focus="$parent.clearErrorMsg($event)">
                                            <option value="">Seleccionar</option>
                                            <option v-for="payment_method in filteredPaymentMethods"
                                                :value="payment_method.id" v-bind:key="payment_method.id">{{
                                                    payment_method.name }}</option>
                                        </select>
                                        <div id="payment_method_id-error" class="error invalid-feedback"></div>
                                    </div>
                                </div>


                                <div class="col-lg-6" v-if="this.model.payment_method == 2">
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

                                <div class="col-lg-3" id="empleado">
                                    <div class="form-group">
                                        <label class="form-control-label">Empleado:</label>
                                        <select class="form-control kt-select2" name="employee_id" id="employee_id"
                                            v-model="model.employee_id" @focus="$parent.clearErrorMsg($event)">
                                            <option value="">Seleccionar</option>
                                        </select>
                                        <div id="employee_id-error" class="error invalid-feedback"></div>
                                    </div>
                                </div>
                                <div class="col-lg-3">
                                    <div class="form-group">
                                        <label class="form-control-label">Moneda:</label>
                                        <select class="form-control" name="currency_id" id="currency_id"
                                            v-model="model.currency" @focus="$parent.clearErrorMsg($event)">
                                            <option value="">Seleccionar</option>
                                            <option v-for="currency in currencies" :value="currency.id"
                                                v-bind:key="currency.id">{{ currency.name }}</option>
                                        </select>
                                        <div id="currency_id-error" class="error invalid-feedback"></div>
                                    </div>
                                </div>
                                <div class="col-lg-3" v-if="model.currency != '' && model.currency != 1">
                                    <div class="form-group">
                                        <label class="form-control-label">Tipo de Cambio:</label>
                                        <input type="number" class="form-control" name="exchange_rate"
                                            id="exchange_rate" min="0" v-model="model.exchange_rate"
                                            @focus="$parent.clearErrorMsg($event)">
                                        <div id="exchange_rate-error" class="error invalid-feedback"></div>
                                    </div>
                                </div>
                                <div class="col-lg-3" v-if="model.payment_method == 3">
                                    <div class="form-group">
                                        <label class="form-control-label">Banco:</label>
                                        <select class="form-control" name="bank_account_id" id="bank_account_id"
                                            v-model="model.bank_account" @focus="$parent.clearErrorMsg($event)">
                                            <option disabled value="">Seleccionar</option>
                                            <option value=1>BCP-MN 1940080375067</option>
                                            <option value=2>BBVA-001109100100172914</option>
                                            <option value=12>INTERBANK-2003004951199</option>

                                        </select>
                                        <div id="bank_account_id-error" class="error invalid-feedback"></div>
                                    </div>
                                </div>


                                <div class="col-lg-3" v-if="model.payment_method == 3">
                                    <div class="form-group">
                                        <label class="form-control-label">Nº de Operación:</label>
                                        <input type="text" class="form-control" name="operation_number"
                                            id="operation_number" v-model="model.operation_number"
                                            @focus="$parent.clearErrorMsg($event)">
                                        <div id="operation_number-error" class="error invalid-feedback"></div>
                                    </div>
                                </div>
                                <div class="col-lg-3">
                                    <div class="form-group">
                                        <label class="form-control-label">Monto:</label>
                                        <input type="number" class="form-control" name="amount" id="amount" min="0"
                                            v-model="model.amount" @focus="$parent.clearErrorMsg($event)">
                                        <div id="amount-error" class="error invalid-feedback"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 text-right">
                                    <button id="add_payment" type="submit" class="btn btn-success"
                                        @click.prevent="addLiquidation(model)">Agregar</button>
                                    <button type="button" class="btn btn-secondary"
                                        @click.prevent="resetLiquidation()">Cancelar</button>
                                    <div class="kt-separator kt-separator--space kt-separator--dashed"></div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <table class="table table-vertical-middle table-layout-fixed">
                                        <thead>
                                            <tr>
                                                <th>Forma Pago</th>
                                                <th>Moneda</th>
                                                <th>Tipo Cambio</th>
                                                <th>Banco</th>
                                                <th>Nº Ope.</th>
                                                <th style="text-align:right;width:120px;">Monto</th>
                                                <th style="text-align:right;">Opciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="(item, index) in liquidations" v-bind:key="index">
                                                <td>{{ item.payment_method.name }}</td>
                                                <td>{{ item.currency.name }}</td>
                                                <td>{{ item.exchange_rate }}</td>
                                                <td>{{ item.bank_account.name }}</td>
                                                <td>{{ item.operation_number }}</td>
                                                <td style="text-align:right;width:120px;">{{ item.amount }}</td>
                                                <td style="text-align:right;">
                                                    <a href="#" class="btn-sm btn btn-label-danger btn-bold"
                                                        @click.prevent="removeLiquidation(index)">
                                                        <i class="la la-trash-o pr-0"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td style="text-align:right;width:120px;font-weight:600;">{{ addTotals
                                                    }}</td>
                                                <td style="text-align:right;"></td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success" @click.prevent="addLiquidations()">Crear</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>
        <!--end::Modal :disabled="model.payment_method == 2" cuando el metodo de pago es deposito -->
    </div>
</template>

<script>
import EventBus from '../event-bus';
export default {
    props: {
        payment_methods: {
            type: Array,
            default: '',
        },
        deposits: {
            type: Array,
            default: '',
        },
        currencies: {
            type: Array,
            default: '',
        },
        url_get_bank_accounts: {
            type: String,
            default: ''
        },
        url_get_employees: {
            type: String,
            default: ''
        },
        url_validate_client: {
            type: String,
            default: ''
        },
        payments: {
            type: Array,
            default: [],
        },
        payment_cash: {
            type: Number,
            default: ''
        },
        payment_credit: {
            type: Number,
            default: ''
        }
    },
    data() {
        return {
            title_text: '',
            liquidation_datatable: undefined,
            model: {
                payment_method: '',
                currency: '',
                exchange_rate: '',
                bank_account: '',
                operation_number: '',
                amount: '',
                employee_id: '',
                deposit_id: ''
            },
            liquidations: [],
            bank_accounts: [],
            total: '',
        }
    },
    created() {

    },
    mounted() {
        $('#empleado').hide();
        this.newSelect2();

        EventBus.$on('liquidation_modal', function () {
            let vm = this;

            this.title_text = this.$store.state.sale.client_name + ' - Total: ' + this.$store.state.sale.total_perception;

            this.model.payment_method = '';
            this.model.currency = '';
            this.model.exchange_rate = '';
            this.model.bank_account = '';
            this.model.operation_number = '';
            this.model.amount = '';
            this.model.deposit_id = '';

            $('#modal-liquidation').modal('show');
        }.bind(this));
    },
    watch: {
        'model.payment_id': function (val) {
            if (val != '') {
                EventBus.$emit('loading', true);

                axios.post(this.url_validate_client, {
                    client_id: this.$store.state.sale.client_id,
                    payment_id: val
                }).then(response => {
                    EventBus.$emit('loading', false);

                }).catch(error => {
                    EventBus.$emit('loading', false);

                    this.model.payment_id = '';
                    this.$parent.alertMsg({
                        type: 5,
                        title: 'Error al validar cliente',
                        msg: error.response.data.message
                    });
                    console.log(error);
                    console.log(error.response);
                });
            }
        },
        'model.currency': function (val) {
            if (this.model.payment_method.id == 2 || this.model.payment_method.id == 3) {
                EventBus.$emit('loading', true);

                axios.post(this.url_get_bank_accounts, {
                    company_id: this.$store.state.model.company_id,
                    currency_id: val
                }).then(response => {
                    // console.log(response);
                    this.model.bank_account = '';
                    this.bank_accounts = response.data;

                    EventBus.$emit('loading', false);
                }).catch(error => {
                    console.log(error);
                    console.log(error.response);
                });
            }
        },
        'model.payment_method': function (val) {

            if (val == 10) {
                $('#empleado').show();
            } else {
                $('#empleado').hide();
            }

            if (val.id == 2 || val.id == 3) {
                EventBus.$emit('loading', true);

                axios.post(this.url_get_bank_accounts, {
                    company_id: this.$store.state.model.company_id,
                    currency_id: this.model.currency.id
                }).then(response => {
                    // console.log(response);
                    this.model.bank_account = '';
                    this.bank_accounts = response.data;

                    EventBus.$emit('loading', false);
                }).catch(error => {
                    console.log(error);
                    console.log(error.response);
                });
            }
        }
    },
    computed: {
        filteredPaymentMethods() {
            if (this.model.payment_id == 1 || this.model.payment_id == 3) {
                return this.payment_methods.filter(pm => pm.id !== 10);
            } else if (this.model.payment_id == 4) {
                return this.payment_methods.filter(pm => pm.id == 10);
            }

        },
        addTotals() {
            return this.liquidations.reduce((a, { amount }) => Number(a) + Number(amount), 0).toFixed(2);
        },
    },
    methods: {
        newSelect2: function () {
            let vm = this;
            let token = document.head.querySelector('meta[name="csrf-token"]').content;
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
                            employee_id: vm.model.employee_id,
                            _token: token,
                        }

                        return queryParameters;
                    },
                    processResults: function (data, params) {
                        params.page = params.page || 1;

                        return {
                            results: data,
                            pagination: {
                                more: (params.page * 30) < data.total_count
                            }
                        };
                    },

                    cache: true

                },
                minimumInputLength: 1,
            }).on('select2:select', function (e) {
                var selected_element = $(e.currentTarget);
                vm.model.employee_id = parseInt(selected_element.val());
            }).on('select2:unselect', function (e) {
                vm.model.employee_id = '';
            });
        },
        addLiquidation: function () {

            if (this.model.payment_id != '') {
                if (this.model.payment_method == 2) {

                    const row = this.deposits.find(deposit => deposit.id === this.model.deposit_id);

                    if (Number(this.model.amount) > Number(row.total_pend)) {
                        this.$parent.alertMsg({
                            type: 5,
                            title: 'Error',
                            msg: 'El monto no puede ser mayor al monto del deposito seleccionado.'
                        });

                        return;
                    }
                    this.model.operation_number = row.operation_number;
                }

                let liquidation = JSON.parse(JSON.stringify(this.model));
                let text = '';
                console.log(liquidation);


                if (liquidation.payment_method == '' && liquidation.payment_id == this.payment_cash) {
                    text = 'Debe seleccionar una Forma de Pago.';
                } else if (liquidation.currency == '' && liquidation.payment_id == this.payment_cash) {
                    text = 'Debe seleccionar una Moneda.';
                } else if ((liquidation.payment_method == 3) && liquidation.bank_account == '') {
                    text = 'Debe seleccionar un Banco.';
                } else if ((liquidation.payment_method == 3) && liquidation.operation_number == '') {
                    text = 'El Nº de Operación es obligatorio.';
                } else if ((liquidation.currency == 2 || liquidation.currency == 3) && liquidation.exchange_rate == '') {
                    text = 'El Tipo de Cambio es obligatorio.';
                } else if ((liquidation.amount == '' || liquidation.amount <= 0) && liquidation.payment_id == this.payment_cash) {
                    text = 'El Monto es obligatorio y debe ser mayor a 0.';
                } else if (this.$store.state.sale.payment_id != this.payment_credit && liquidation.payment_id == this.payment_credit) {
                    text = 'El cliente no cuenta con crédito disponible';
                } else if (liquidation.payment_id == this.payment_credit && (this.$store.state.sale.total_perception - accounting.unformat(this.addTotals)) > (this.$store.state.sale.credit_limit)) {
                    text = 'El cliente no cuenta con linea de crédito disponible coordinar con Creditos';
                }




                if (text != '') {
                    Swal.fire({
                        title: '¡Error!',
                        text: text,
                        type: "error",
                        heightAuto: false,
                        showCancelButton: false,
                        confirmButtonText: 'Ok',
                    });
                } else {
                    liquidation.exchange_rate = (liquidation.exchange_rate != '' ? accounting.toFixed(liquidation.exchange_rate, 2) : '');
                    liquidation.amount = accounting.toFixed(liquidation.amount, 2);

                    this.liquidations.push(liquidation);

                    this.model.payment_method = '';
                    this.model.currency = '';
                    this.model.exchange_rate = '';
                    this.model.bank_account = '';
                    this.model.operation_number = '';
                    this.model.amount = '';
                    this.model.deposit_id = '';
                }
            } else {
                Swal.fire({
                    title: '¡Error!',
                    text: 'Debe seleccionar una Condición de Pago.',
                    type: "error",
                    heightAuto: false,
                    showCancelButton: false,
                    confirmButtonText: 'Ok',
                });
            }
        },
        resetLiquidation: function () {
            this.model.payment_method = '';
            this.model.currency = '';
            this.model.exchange_rate = '';
            this.model.bank_account = '';
            this.model.operation_number = '';
            this.model.amount = '';
            this.model.deposit_id = '';
        },
        removeLiquidation: function (index) {
            this.liquidations.splice(index, 1);
        },
        addLiquidations: function () {
            let error = 0;

            if (this.$store.state.sale.payment_id !== 2 && accounting.unformat(this.addTotals) !== accounting.unformat(this.$store.state.sale.total_perception)) {
                error = 1;
                Swal.fire({
                    title: '¡Error!',
                    text: 'Debe liquidar por el total de la Venta.',
                    type: "error",
                    heightAuto: false,
                    showCancelButton: false,
                    confirmButtonText: 'Ok',
                });
            }


            if (this.$store.state.sale.payment_id == 2 && accounting.unformat(this.addTotals) > accounting.unformat(this.$store.state.sale.total_perception)) {
                error = 1;
                Swal.fire({
                    title: '¡Error!',
                    text: 'El Pago a cuenta no puede exceder el Total de la Venta.',
                    type: "error",
                    heightAuto: false,
                    showCancelButton: false,
                    confirmButtonText: 'Ok',
                });
            }

            if (this.$store.state.sale.payment_id !== 2 && this.liquidations < 1) {
                error = 1;
                Swal.fire({
                    title: '¡Error!',
                    text: 'Debe agregar al menos 1 Forma de Pago.',
                    type: "error",
                    heightAuto: false,
                    showCancelButton: false,
                    confirmButtonText: 'Ok',
                });
            }

            if (this.$store.state.sale.payment_id !== 1 && ((this.$store.state.sale.total_perception - accounting.unformat(this.addTotals)) > this.$store.state.sale.credit_limit)) {
                error = 1;
                Swal.fire({
                    title: '¡Error!',
                    text: 'La línea del crédito del cliente es insuficiente.',
                    type: "error",
                    heightAuto: false,
                    showCancelButton: false,
                    confirmButtonText: 'Ok',
                });
            } else if (((this.$store.state.sale.total_perception - accounting.unformat(this.addTotals)) > this.$store.state.sale.credit_limit)) {
                error = 1;
                Swal.fire({
                    title: '¡Error!',
                    text: 'La línea del crédito del cliente es insuficiente.',
                    type: "error",
                    heightAuto: false,
                    showCancelButton: false,
                    confirmButtonText: 'Ok',
                });
            }


            if (error == 0) {
                let liquidations = JSON.parse(JSON.stringify(this.liquidations));
                let sale = JSON.parse(JSON.stringify(this.$store.state.sale));

                this.$store.commit('addLiquidations', liquidations);
                this.$store.commit('addSales');

                sale.details.forEach(element => {
                    let article_id = element.article_id;
                    let quantity = element.quantity;

                    this.$store.commit('changeBalanceValue', {
                        article_id,
                        quantity
                    });
                });

                EventBus.$emit('refresh_table_sale');

                this.liquidations = [];
                this.title_text = '';
                this.model.payment_method = '';
                this.model.currency = '';
                this.model.exchange_rate = '';
                this.model.bank_account = '';
                this.model.operation_number = '';
                this.model.amount = '';

                $('#modal-liquidation').modal('hide');

                EventBus.$emit('refresh_table_liquidation');
            }
        },
        checkPayment() {
            switch (this.model.payment_id) {
                case this.payment_cash:
                    $('#modal-liquidation').find('#payment_method_id').prop('disabled', false);
                    $('#modal-liquidation').find('#currency_id').prop('disabled', false);
                    $('#modal-liquidation').find('#amount').prop('disabled', false);
                    break;
                case this.payment_credit:
                    $('#modal-liquidation').find('#payment_method_id').prop('disabled', true);
                    $('#modal-liquidation').find('#currency_id').prop('disabled', true);
                    $('#modal-liquidation').find('#amount').prop('disabled', true);
                    break;
                default:
                    $('#modal-liquidation').find('#payment_method_id').prop('disabled', false);
                    $('#modal-liquidation').find('#currency_id').prop('disabled', false);
                    $('#modal-liquidation').find('#amount').prop('disabled', false);
                    break;
            }

        },
        /*  manageOperationNumber() {
               console.log(this.model.payment_method);


               if (this.model.payment_method === 2) {
                   EventBus.$emit('loading', true);
                   $('#add_payment').prop('disabled', true);
                   $('#operation_number-error').hide();
                   $('#operation_number-error').text('');

                   axios.get('/facturacion/liquidaciones-final/get-op-number', {
                       params: {
                           operation_number: this.model.operation_number,
                           payment_method: this.model.payment_method,
                           bank_account: this.model.bank_account
                       }
                   }).then(response => {
                       console.log('bien');
                       EventBus.$emit('loading', false);
                       $('#add_payment').prop('disabled', false);
                       $('#operation_number-error').text('');
                       $('#operation_number-error').hide();
                   }).catch(error => {
                       EventBus.$emit('loading', false);
                       $('#add_payment').prop('disabled', true);
                       $('#operation_number-error').text('El Nro. de Operación del Deposito ya fue usado anteriormente');
                       $('#operation_number-error').show();
                   });
               }





   
           }*/
    }
};
</script>