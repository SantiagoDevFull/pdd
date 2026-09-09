<template>
    <!--begin::Modal-->
    <div class="modal fade" id="modal_voucher" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <!--begin::Form-->
                <form class="kt-form" @submit.prevent="formController(url, $event)">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel"> Registrar Voucher </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="kt-portlet__body">
                            <div class="row">
                                <input type="hidden" name="guia_id" id="guia_id" v-model="model.guia_id">

                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label class="form-control-label">Fecha de depósito:</label>
                                        <datetime v-model="model.bank_date" placeholder="Selecciona una Fecha"
                                            :format="'dd-LL-yyyy'" input-id="bank_date" name="bank_date"
                                            value-zone="America/Lima" zone="America/Lima" class="form-control"
                                            this.current_date @focus="$parent.clearErrorMsg($event)">
                                        </datetime>
                                        <div id="bank_date-error" class="error invalid-feedback"></div>
                                    </div>
                                </div>

                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label class="form-control-label">Cuenta:</label>
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

                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label class="form-control-label">Número de operación:</label>
                                        <input type="text" class="form-control" name="operation_number"
                                            id="operation_number" v-model="model.operation_number"
                                            @input="model.operation_number = model.operation_number.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1')"
                                            @focus="$parent.clearErrorMsg($event)">
                                        <div id="operation_number-error" class="error invalid-feedback"></div>
                                    </div>
                                </div>

                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label class="form-control-label">Saldo total:</label>
                                        <input type="text" class="form-control" name="total" id="total"
                                            @input="model.total = model.total.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1')"
                                            v-model="model.total" @focus="$parent.clearErrorMsg($event)">
                                        <div id="total-error" class="error invalid-feedback"></div>
                                    </div>
                                </div>

                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label class="form-control-label">Observación:</label>
                                        <input type="text" class="form-control" name="obs" id="obs" v-model="model.obs"
                                            @focus="$parent.clearErrorMsg($event)">
                                        <div id="obs-error" class="error invalid-feedback"></div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success" id="add_article_2">
                            Registrar
                        </button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
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
        bank_accounts: {
            type: Array,
            default: ''
        }

    },
    data() {
        return {
            model: {
                guia_id: '',
                bank_date: '',
                bank_account_id: '',
                operation_number: '',
                total: '',
                obs: ''
            },
        }
    },
    watch: {

    },
    computed: {

    },
    created() {
        EventBus.$on('open_voucher', function (guia_id) {

            this.model.guia_id = guia_id;
            this.model.bank_date = '';
            this.model.bank_account_id = '';
            this.model.operation_number = '';
            this.model.total = '';
            this.model.obs = '';

            $('#modal_voucher').modal('show');
        }.bind(this));
    },
    mounted() {

    },
    methods: {
        formController: function (url, event) {
            var vm = this;

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
                this.$parent.alertMsg(response.data);

                this.model.bank_date = '';
                this.model.bank_account_id = '';
                this.model.operation_number = '';
                this.model.total = '';
                this.model.obs = '';
                $('#modal_voucher').modal('hide');

            }).catch(error => {
                EventBus.$emit('loading', false);
                console.log(error.response);
                var obj = error.response.data.errors;
                $('.modal').animate({
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
        },
    }
};
</script>