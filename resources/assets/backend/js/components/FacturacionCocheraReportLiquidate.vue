<template>
    <div>
        <!--begin::Liquidation Modal-->
        <div class="modal fade" id="modal-liquidation" data-backdrop="static" data-keyboard="false" tabindex="-1"
            aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
                <div class="modal-content">
                    <form class="kt-form" @submit.prevent="sendForm(url, $event)">
                        <div class="modal-header">
                            <h5 class="modal-title" id="liquidationModal">{{ title }}<span class="text-danger">{{ saldo
                                    }}</span></h5>
                            <!--<button type="button" class="close" data-dismiss="modal" aria-label="Close"></button>-->
                        </div>
                        <div class="modal-body">
                            <div class="kt-portlet__body">
                                <div class="row">
                                    <div class="col-lg-2">
                                        <div class="form-group">
                                            <label class="form-control-label">Condición de Pago:</label>
                                            <select class="form-control" name="payment_id" id="payment_id"
                                                v-on:change="checkPayment" v-model="model.payment_id"
                                                @focus="$parent.clearErrorMsg($event)">
                                                <option value="" selected disabled>Seleccionar</option>
                                                <option v-for="payment in payments" :value="payment.id"
                                                    v-bind:key="payment.id">
                                                    {{ payment.name }}</option>
                                            </select>
                                            <div id="payment_id-error" class="error invalid-feedback"></div>
                                        </div>
                                    </div>
                                    <div class="col-lg-2">
                                        <div class="form-group">
                                            <label class="form-control-label">Forma de Pago:</label>
                                            <select class="form-control" name="payment_method_id" id="payment_method_id"
                                                v-model="model.payment_method_id"
                                                @focus="$parent.clearErrorMsg($event)">
                                                <option value="" selected disabled>Seleccionar</option>
                                                <option v-for="payment_method in payment_methods"
                                                    :value="payment_method.id" v-bind:key="payment_method.id">{{
                                                        payment_method.name }}</option>
                                            </select>
                                            <div id="payment_method_id-error" class="error invalid-feedback"></div>
                                        </div>
                                    </div>
                                    <div class="col-lg-1">
                                        <div class="form-group">
                                            <label class="form-control-label">Moneda:</label>
                                            <select class="form-control" name="currency_id" id="currency_id"
                                                @change="State()" v-model="model.currency_id"
                                                @focus="$parent.clearErrorMsg($event)">
                                                <option value="">Seleccionar</option>
                                                <option v-for="currency in currencies" :value="currency.id"
                                                    v-bind:key="currency.id">{{ currency.name }}</option>
                                            </select>
                                            <div id="currency_id-error" class="error invalid-feedback"></div>
                                        </div>
                                    </div>
                                    <div class="col-lg-1">
                                        <div class="form-group">
                                            <label class="form-control-label">Cambio:</label>
                                            <input type="text" class="form-control" name="tc" id="tc" min="0"
                                                v-model="model.tc" @focus="$parent.clearErrorMsg($event)">
                                            <div id="tc-error" class="error invalid-feedback"></div>
                                        </div>
                                    </div>
                                    <div class="col-lg-2"
                                        v-if="model.payment_method_id == 2 || model.payment_method_id == 3">
                                        <div class="form-group">
                                            <label class="form-control-label">Banco:</label>
                                            <select class="form-control" name="bank_account_id" id="bank_account_id"
                                                v-model="model.bank_account_id" @focus="$parent.clearErrorMsg($event)">
                                                <option disabled value="">Seleccionar</option>
                                                <option v-for="bank_account in bank_accounts" :value="bank_account.id"
                                                    v-bind:key="bank_account.id">{{ bank_account.account_number }}
                                                </option>

                                            </select>
                                            <div id="bank_account_id-error" class="error invalid-feedback"></div>
                                        </div>
                                    </div>


                                    <div class="col-lg-2"
                                        v-if="model.payment_method_id == 2 || model.payment_method_id == 3">
                                        <div class="form-group">
                                            <label class="form-control-label">Nº de Operación:</label>
                                            <input type="text" class="form-control" name="operation_number"
                                                id="operation_number" v-model="model.operation_number"
                                                @focus="$parent.clearErrorMsg($event)">
                                            <div id="operation_number-error" class="error invalid-feedback"></div>
                                        </div>
                                    </div>
                                    <div class="col-lg-2">
                                        <div class="form-group">
                                            <label class="form-control-label">Monto:</label>
                                            <input type="text" class="form-control" name="amount" id="amount" min="0"
                                                v-model="model.amount" @focus="$parent.clearErrorMsg($event)">
                                            <div id="amount-error" class="error invalid-feedback"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="submit" class="btn btn-success">
                                        <i class="fa fa-plus"></i> Agregar
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>

                    <div class="modal-body">
                        <div class="kt-portlet__body">
                            <div class="row">
                                <div class="col-12">
                                    <table class="table table-vertical-middle table-layout-fixed">
                                        <thead>
                                            <tr>
                                                <th style="width:100px;">Condición de Pago</th>
                                                <th style="width:150px;">Forma Pago</th>
                                                <th>Moneda</th>
                                                <th>Cambio</th>
                                                <th style="width:200px;">Banco</th>
                                                <th>Nº Ope.</th>
                                                <th style="text-align:right;width:120px;">Monto</th>
                                                <th style="text-align:right;width:120px;">Total</th>
                                                <th style="text-align:right;">Opciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="(item, index) in liquidations" v-bind:key="index">
                                                <td>{{ item.payment_name }}</td>
                                                <td>{{ item.payment_method_name }}</td>
                                                <td>{{ item.currency_name }}</td>
                                                <td>{{ item.tc }}</td>
                                                <td>{{ item.bank_account_name }}</td>
                                                <td>{{ item.operation_number }}</td>
                                                <td style="text-align:right;width:120px;">{{ item.amount }}</td>
                                                <td style="text-align:right;width:120px;">{{ item.total }}</td>
                                                <td style="text-align:right;">
                                                    <a href="#" class="btn-sm btn btn-label-danger btn-bold"
                                                        @click.prevent="removeLiquidation(index, item.total)">
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
                                                <td></td>
                                                <td></td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-body">
                        <div class="kt-portlet__body">
                            <div class="row">
                                <div class="col-12">
                                    <a href="#" @click.prevent="liquidar()" class="btn btn-primary">
                                        Liquidar
                                    </a>
                                    <a href="#" @click.prevent="cancel()" class="btn btn-danger">
                                        Cancelar
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>

import EventBus from '../event-bus';

export default {
    props: {
        url: {
            type: String,
            default: ''
        },
        url_store: {
            type: String,
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
        payments: {
            type: Array,
            default: ''
        },
        currencies: {
            type: Array,
            default: ''
        }
    },
    data() {
        return {
            model: {
                payment_id: '',
                payment_method_id: '',
                currency_id: '',
                bank_account_id: '',
                operation_number: '',
                amount: '',
                tc: '',

            },
            payment_methods: [],
            liquidations: [],
            bank_accounts: [],
            title: '',
            saldo: '',
            currency_name: ''
        }
    },
    mounted() {
        EventBus.$on('liquidation_modal', function (id, factura, provider_name, vehicle_plate, total, currency_name) {
            this.id = id;
            this.saldo = total;
            this.title = factura + " | " + provider_name + " | " + vehicle_plate + " | " + currency_name + " | TOTAL FACTURA : " + total + " | SALDO : ";
            this.currency_name = currency_name;
            $('#modal-liquidation').modal('show');
        }.bind(this));

        EventBus.$on('clear_modal', function () {
            this.model.payment_id = '';
            this.payment_methods = [];
            this.model.payment_method_id = '';
            this.model.currency_id = '';
            $("#tc").prop("readonly", false);
            this.model.tc = '';
            this.bank_accounts = [];
            this.model.bank_account_id = '';
            this.model.operation_number = '';
            this.model.amount = '';
        }.bind(this));
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
    },
    computed: {
    },
    created() {

    },
    methods: {
        State: function () {
            var tm = $('#currency_id').val();

            if (tm != "") {

                if (this.currency_name == 'Soles') {
                    if (tm == 1) {
                        $("#tc").val("1");
                        this.model.tc = '1';
                        $("#tc").prop("readonly", true);
                    } else {
                        $("#tc").prop("readonly", false);
                        this.model.tc = '';
                    }
                } else {
                    if (tm == 2) {
                        $("#tc").val("1");
                        this.model.tc = '1';
                        $("#tc").prop("readonly", true);
                    } else {
                        $("#tc").prop("readonly", false);
                        this.model.tc = '';
                    }
                }



            }

        },
        checkPayment() {
            switch (this.model.payment_id) {
                case 1:
                    $('#modal-liquidation').find('#payment_method_id').prop('disabled', false);
                    $('#modal-liquidation').find('#currency_id').prop('disabled', false);
                    $('#modal-liquidation').find('#tc').prop('disabled', false);
                    $('#modal-liquidation').find('#amount').prop('disabled', false);
                    break;
                case 2:
                    this.model.payment_method_id = '';
                    this.model.currency_id = '';
                    this.model.tc = '';
                    this.model.amount = '';
                    this.model.bank_account_id = '';
                    this.model.operation_number = '';
                    $('#modal-liquidation').find('#payment_method_id').prop('disabled', true);
                    $('#modal-liquidation').find('#currency_id').prop('disabled', true);
                    $('#modal-liquidation').find('#tc').prop('disabled', true);
                    $('#modal-liquidation').find('#amount').prop('disabled', true);
                    break;
            }

        },
        liquidar() {
            if (this.liquidations != '' && this.liquidations != []) {

                if (this.saldo != 0) {
                    Swal.fire({
                        title: '¡Error!',
                        text: 'El saldo debe terminar en 0.',
                        type: "error",
                        heightAuto: false,
                    });
                } else {
                    EventBus.$emit('loading', true);
                    axios.post(this.url_store, {
                        id: this.id,
                        liquidations: this.liquidations,
                    }).then(response => {
                        EventBus.$emit('loading', false);
                        EventBus.$emit('refresh_table');
                        this.$parent.alertMsg(response.data);
                        this.cancel();
                    }).catch(error => {
                        EventBus.$emit('loading', false);
                        console.log(error.response);
                    });
                }
            }
            else {
                Swal.fire({
                    title: '¡Error!',
                    text: 'Debes agregar al menos 1 liquidación.',
                    type: "error",
                    heightAuto: false,
                });
            }
        },
        cancel() {
            this.id = '';
            this.title = '';
            this.saldo = '';
            this.liquidations = [];

            EventBus.$emit('clear_modal');
            $('#modal-liquidation').modal('hide');
        },
        removeLiquidation(index, total) {
            this.liquidations.splice(index, 1);
            this.saldo = (parseFloat(this.saldo) + parseFloat(total)).toFixed(2);
        },
        sendForm: function (url, event) {

            if (this.model.payment_id == 1) {
                if (isNaN(this.model.amount) || parseFloat(this.model.amount) <= 0) {
                    Swal.fire({
                        title: '¡Error!',
                        text: 'El monto debe ser un número real y mayor a 0.',
                        type: "error",
                        heightAuto: false,
                    });
                    return;
                }
            }

            if (this.model.payment_id == 2 && (parseFloat(this.saldo) > parseFloat(this.limit))) {

                Swal.fire({
                    title: '¡Error!',
                    text: 'El monto máximo de crédito del Cliente es ' + this.limit,
                    type: "error",
                    heightAuto: false,
                });
                return;
            }

            if (this.model.payment_id == 1 && (parseFloat(this.model.amount) > parseFloat(this.saldo))) {
                Swal.fire({
                    title: '¡Error!',
                    text: 'El monto no puede superar al Saldo.',
                    type: "error",
                    heightAuto: false,
                });
                return;
            }

            if (this.saldo > 0) {
                var target = $(event.target);
                var url = url;
                var fd = new FormData(event.target);

                EventBus.$emit('loading', true);
                axios.post(url, fd, {
                    headers: {
                        'Content-type': 'application/x-www-form-urlencoded',
                    }
                }).then(response => {

                    EventBus.$emit('loading', false);

                    if (this.model.payment_id == 1) {

                        if (this.currency_name == 'Soles') {
                            if (this.model.currency_id == 1) {
                                this.saldo = (this.saldo - this.model.amount).toFixed(2);
                                response.data.total = this.model.amount;
                            } else {
                                this.saldo = (this.saldo - (this.model.amount * this.model.tc)).toFixed(2);
                                response.data.total = this.model.amount * this.model.tc;
                            }
                        } else {
                            if (this.model.currency_id == 2) {
                                this.saldo = (this.saldo - this.model.amount).toFixed(2);
                                response.data.total = this.model.amount;
                            } else {
                                this.saldo = (this.saldo - (this.model.amount / this.model.tc)).toFixed(2);
                                response.data.total = this.model.amount / this.model.tc;
                            }
                        }


                    } else {
                        response.data.total = this.saldo;
                        this.saldo = (this.saldo - this.saldo).toFixed(2);
                    }
                    this.liquidations.push(response.data);

                    EventBus.$emit('clear_modal');

                }).catch(error => {
                    EventBus.$emit('loading', false);
                    console.log(error.response);
                    var obj = error.response.data.errors;
                    $('html, body').animate({
                        scrollTop: 0
                    }, 500, 'swing');
                    $.each(obj, function (i, item) {
                        let c_target = target.find("#" + i + "-error");
                        if (!c_target.attr('data-required')) {
                            let p = c_target.prev();
                            p.addClass('is-invalid');
                        } else {
                            c_target.css('display', 'block');
                        }
                        c_target.html(item);
                    });
                });
            } else {
                Swal.fire({
                    title: '¡Error!',
                    text: 'Ya liquidó todo el monto.',
                    type: "error",
                    heightAuto: false,
                });
            }
        },

    }
};
</script>