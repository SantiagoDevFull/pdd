<template>
    <!--begin::Modal-->
    <div class="modal fade" id="modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <!--begin::Form-->
                <form class="kt-form" @submit.prevent="formController(url_update, $event)">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Actualizar Deposito</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="kt-portlet__body">
                            <div class="row">
                                <input type="hidden" name="id" id="id" v-model="model.id">
                                <div class="col-lg-3">
                                    <div class="form-group">
                                        <label class="form-control-label">Fecha de depósito:</label>
                                        <datetime v-model="model.bank_date" placeholder="Selecciona una Fecha"
                                            :format="'dd-LL-yyyy'" input-id="bank_date" name="bank_date"
                                            value-zone="America/Lima" zone="America/Lima" class="form-control"
                                            :max-datetime="this.max_datetime" @focus="$parent.clearErrorMsg($event)">
                                        </datetime>
                                        <div id="bank_date-error" class="error invalid-feedback"></div>
                                    </div>
                                </div>

                                <div class="col-lg-3">
                                    <div class="form-group">
                                        <label class="form-control-label">Compañía:</label>
                                        <select class="form-control" name="company_id" id="company_id"
                                            v-model="model.company_id" @focus="$parent.clearErrorMsg($event)">
                                            <option value="" selected disabled>Seleccionar</option>
                                            <option v-for="company in companies" :value="company.id"
                                                v-bind:key="company.id">{{
                                                    company.name }}</option>
                                        </select>
                                        <div id="company_id-error" class="error invalid-feedback"></div>
                                    </div>
                                </div>

                                <div class="col-lg-3">
                                    <div class="form-group">
                                        <label class="form-control-label">Cuenta:</label>
                                        <select class="form-control" name="bank_account_id" id="bank_account_id"
                                            v-model="model.bank_account_id" @focus="$parent.clearErrorMsg($event)">
                                            <option disabled value="">Seleccionar</option>
                                            <option v-for="bank_account in bank_accounts" :value="bank_account.id"
                                                v-bind:key="bank_account.id">{{ bank_account.bank_name }} - {{
                                                    bank_account.account_number }}
                                            </option>
                                        </select>
                                        <div id="bank_account_id-error" class="error invalid-feedback"></div>
                                    </div>
                                </div>

                                <div class="col-lg-3">
                                    <div class="form-group">
                                        <label class="form-control-label">Número de operación:</label>
                                        <input type="text" class="form-control" name="operation_number"
                                            id="operation_number" v-model="model.operation_number"
                                            @focus="$parent.clearErrorMsg($event)">
                                        <div id="operation_number-error" class="error invalid-feedback"></div>
                                    </div>
                                </div>

                                <div class="col-lg-3">
                                    <div class="form-group">
                                        <label class="form-control-label">Saldo total:</label>
                                        <input type="text" class="form-control" name="total" id="total"
                                            v-model="model.total" @focus="$parent.clearErrorMsg($event)">
                                        <div id="total-error" class="error invalid-feedback"></div>
                                    </div>
                                </div>
                                <div class="col-lg-3">
                                    <div class="form-group">
                                        <label class="form-control-label">RUTA:</label>
                                        <select class="form-control kt-select2" name="route_id" id="route_id"
                                            v-model="model.route_id" @focus="$parent.clearErrorMsg($event)">
                                            <option value="" selected disabled>Seleccionar</option>
                                            <option v-for="route in routes" :value="route.id" v-bind:key="route.id">{{
                                                route.name }}
                                            </option>
                                        </select>
                                        <div id="route_id-error" class="error invalid-feedback"></div>
                                    </div>
                                </div>

                                <div class="col-lg-6">
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
                        <button type="submit" class="btn btn-success" id="update_user">
                            Actualizar
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
        url_update: {
            type: String,
            default: ''
        },
        companies: {
            type: Array,
            default: ''
        },
        routes: {
            type: Array,
            default: ''
        },
        url_get_bank_accounts: {
            type: String,
            default: ''
        },
        max_datetime: {
            type: String,
            default: ''
        }
    },
    data() {
        return {
            model: {
                id: '',
                company_id: '',
                route_id: '',
                obs: '',
                bank_account_id: '',
                bank_date: this.max_datetime,
                operation_number: null,
                total: null
            },
            bank_accounts: [],
        }
    },
    watch: {
        'model.company_id': function (val) {

            if (val != '') {
                EventBus.$emit('loading', true);

                axios.post(this.url_get_bank_accounts, {
                    company_id: this.model.company_id
                }).then(response => {
                    // console.log(response);
                    this.bank_accounts = [];
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
        EventBus.$on('edit_modal', function (model) {
            this.model = model;
            $('#modal').modal('show');
        }.bind(this));

        EventBus.$on('clear_modal', function (model) {
            this.model.id = '';
            this.model.company_id = '';
            this.model.route_id = '';
            this.model.obs = '';
            this.model.bank_account_id = '';
            this.model.bank_date = this.max_datetime;
            this.model.operation_number = '';
            this.model.total = '';
        }.bind(this));
    },
    mounted() {

    },
    methods: {
        formController: function (url_update, event) {
            var vm = this;

            var target = $(event.target);
            var url = url_update;
            var fd = new FormData(event.target);

            EventBus.$emit('loading', true);

            axios.post(url, fd, {
                headers: {
                    'Content-type': 'application/x-www-form-urlencoded',
                }
            }).then(response => {
                EventBus.$emit('loading', false);
                $('#modal').modal('hide');
                this.$parent.alertMsg(response.data);

                this.model.id = '';
                this.model.name = '';
                this.model.user = '';
                this.model.password = '';

                EventBus.$emit('refresh_table');

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
