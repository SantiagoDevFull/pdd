<template>
    <!--begin::Modal-->
    <div class="modal fade" id="modal-liquidation" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <!--begin::Form-->
                <form class="kt-form" @submit.prevent="sendForm()">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Agregar item</h5>
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
                                            <option value="" selected disabled>Seleccionar</option>
                                            <option v-for="payment in payments" :value="payment.id"
                                                v-bind:key="payment.id">
                                                {{ payment.name }}</option>
                                        </select>
                                        <div id="payment_id-error" class="error invalid-feedback"></div>
                                    </div>
                                </div>
                                <div class="col-lg-3">
                                    <div class="form-group">
                                        <label class="form-control-label">Forma de Pago:</label>
                                        <select class="form-control" name="payment_method_id" id="payment_method_id"
                                            v-model="model.payment_method_id" @focus="$parent.clearErrorMsg($event)">
                                            <option value="" selected disabled>Seleccionar</option>
                                            <option v-for="payment_method in payment_methods" :value="payment_method.id"
                                                v-bind:key="payment_method.id">{{
                                                    payment_method.name }}</option>
                                        </select>
                                        <div id="payment_method_id-error" class="error invalid-feedback"></div>
                                    </div>
                                </div>
                                <div class="col-lg-3">
                                    <div class="form-group">
                                        <label class="form-control-label">Moneda:</label>
                                        <select class="form-control" name="currency_id" id="currency_id"
                                            v-model="model.currency_id" @focus="$parent.clearErrorMsg($event)">
                                            <option value="" selected disabled>Seleccionar</option>
                                            <option v-for="currency in currencies" :value="currency.id"
                                                v-bind:key="currency.id">{{ currency.name }}</option>
                                        </select>
                                        <div id="currency_id-error" class="error invalid-feedback"></div>
                                    </div>
                                </div>


                                <div class="col-lg-9" v-if="this.model.payment_method_id == 2">
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
                                    <div class="form-group">
                                        <label class="form-control-label">Monto:</label>
                                        <input type="text" class="form-control" name="amount" id="amount" min="0"
                                            @input="validateDouble($event)" v-model="model.amount"
                                            @focus="$parent.clearErrorMsg($event)">
                                        <div id="amount-error" class="error invalid-feedback"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-success">
                                    Agregar item
                                </button>
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                            </div>
                        </div>
                    </div>
                </form>
                <!--end::Form-->
            </div>
        </div>
    </div>
    <!--end::Modal-->
</template>

<script>
import EventBus from '../event-bus';

export default {
    props: {
        url: {
            type: String,
            default: ''
        },
        payments: {
            type: Array,
            default: ''
        },
        currencies: {
            type: Array,
            default: ''
        },
        url_get_payment_method: {
            type: String,
            default: ''
        },
        url_get_bank_accounts: {
            type: String,
            default: ''
        },
        deposits: {
            type: Array,
            default: ''
        },
    },
    data() {
        return {
            model: {
                payment_id: '',
                payment_name: '',
                payment_method_id: '',
                payment_method_name: '',
                currency_id: '',
                currency_name: '',
                bank_account_id: '',
                bank_account_name: '',
                operation_number: '',
                amount: '',
                deposit_id: '',
            },
            payment_methods: [],
            bank_accounts: [],
        }
    },
    watch: {
        'model.payment_id': function (val) {
            if (val != '') {

                this.payment_methods = [];
                EventBus.$emit('loading', true);

                axios.post(this.url_get_payment_method, {
                    payment_id: this.model.payment_id
                }).then(response => {
                    // console.log(response);
                    this.payment_methods = response.data;

                    EventBus.$emit('loading', false);
                }).catch(error => {
                    console.log(error);
                    console.log(error.response)
                });
            } else {
                this.payment_methods = [];
            }
        },
        'model.currency_id': function (val) {
            if (val != '') {

                this.bank_accounts = [];
                EventBus.$emit('loading', true);

                axios.post(this.url_get_bank_accounts, {
                    currency_id: this.model.currency_id
                }).then(response => {
                    // console.log(response);
                    this.bank_accounts = response.data;

                    EventBus.$emit('loading', false);
                }).catch(error => {
                    console.log(error);
                    console.log(error.response)
                });
            }
        },
        'model.quantity': function (val) {
            this.calculateValues(val, this.model.price_igv, this.model.value_type_id);
        },
        'model.price_igv': function (val) {
            this.calculateValues(this.model.quantity, val, this.model.value_type_id);
        },
        'model.value_type_id': function (val) {
            this.calculateValues(this.model.quantity, this.model.price_igv, val);
        },
    },
    computed: {

    },
    created() {
        EventBus.$on('open_modal_liquidation', function () {
            EventBus.$emit('loading', true);

            this.model = {
                payment_id: '',
                payment_method_id: '',
                currency_id: '',
                bank_account_id: '',
                operation_number: '',
                amount: '',
                deposit_id: '',

            };
            this.payment_methods = [];
            this.bank_accounts = [];

            $('#modal-liquidation').modal('show');
            EventBus.$emit('loading', false);
        }.bind(this));
    },
    mounted() {
    },
    methods: {
        validateInteger(event) {
            const input = event.target;
            input.value = input.value.replace(/[^0-9]/g, '');
            this.model.operation_number = input.value;
        },
        validateDouble(event) {
            const input = event.target;

            input.value = input.value.replace(/[^0-9.]/g, '');

            if (input.value.split('.').length > 2) {
                input.value = input.value.substring(0, input.value.length - 1);
            }

            this.model.amount = input.value;
        },
        checkPayment() {


            switch (this.model.payment_id) {
                case 1:
                    $('#modal-liquidation').find('#payment_method_id').prop('disabled', false);
                    $('#modal-liquidation').find('#currency_id').prop('disabled', false);
                    $('#modal-liquidation').find('#amount').prop('disabled', false);
                    break;
                case 2:
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
        calculateValues: function (quantity, price_igv, value_type_id) {
            let total = accounting.toFixed(quantity * price_igv, 2);
            let igv_percentage = (18 / 100) + 1;
            let sale_value = accounting.toFixed(total / igv_percentage, 4);
            let igv = accounting.toFixed(total - sale_value, 2);

            if (value_type_id == 2 || value_type_id == 3) {
                this.model.sale_value = total;
                this.model.igv = 0;
                this.model.total = total;
            } else {
                this.model.sale_value = sale_value;
                this.model.igv = igv;
                this.model.total = total;
            }
        },
        sendForm: function () {

            if (this.model.payment_id == "") {
                Swal.fire({
                    title: "Error",
                    text: "Debe seleccionar la Condición de Pago.",
                    type: "error",
                    heightAuto: false,
                })
                return;
            } else if (this.model.payment_method_id == "") {
                Swal.fire({
                    title: "Error",
                    text: "Debe seleccionar la Forma de Pago.",
                    type: "error",
                    heightAuto: false,
                })
                return;
            } else {
                if (this.model.currency_id == "") {
                    Swal.fire({
                        title: "Error",
                        text: "Debe seleccionar la Moneda.",
                        type: "error",
                        heightAuto: false,
                    })
                    return;
                } else if (this.model.amount == "") {
                    Swal.fire({
                        title: "Error",
                        text: "Debe digitar el Monto.",
                        type: "error",
                        heightAuto: false,
                    })
                    return;
                }

                if (this.model.payment_method_id == 2 || this.model.payment_method_id == 3) {
                    if (this.model.deposit_id == "") {
                        Swal.fire({
                            title: "Error",
                            text: "Debe seleccionar el Deposito.",
                            type: "error",
                            heightAuto: false,
                        })
                        return;
                    }

                }
            }

            let deposit = this.deposits.find(element => element.id == this.model.deposit_id);

            if (this.model.payment_method_id == 2 && this.model.deposit_id != '') {
                if (Number(this.model.amount) > Number(deposit.total_pend)) {
                    Swal.fire({
                        title: "Error",
                        text: "El monto no puede ser mayor al monto pendiente del deposito seleccionado.",
                        type: "error",
                        heightAuto: false,
                    });
                    return;
                }

                let bank_account = this.bank_accounts.find(element => element.id == deposit.bank_account_id);
                this.model.bank_account_name = bank_account ? bank_account.bank_short_name + '-' + bank_account.bank_account : '';

                this.model.operation_number = deposit ? deposit.operation_number : '';
                this.model.bank_account_id = deposit ? deposit.bank_account_id : '';
            }

            let payment = this.payments.find(element => element.id == this.model.payment_id);
            this.model.payment_name = payment ? payment.name : '';

            let payment_method = this.payment_methods.find(element => element.id == this.model.payment_method_id);
            this.model.payment_method_name = payment_method ? payment_method.name : '';

            let currency = this.currencies.find(element => element.id == this.model.currency_id);
            this.model.currency_name = currency ? currency.name : '';


            EventBus.$emit('sendFormLiquidation', this.model);
            $('#modal-liquidation').modal('hide');
        },
    }
};
</script>