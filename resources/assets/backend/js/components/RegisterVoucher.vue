<template>
    <!--begin::Portlet-->
    <div class="kt-portlet">
        <div class="kt-portlet__head">
            <div class="kt-portlet__head-label">
                <h3 class="kt-portlet__head-title">
                    Crear
                </h3>
            </div>
        </div>

        <!--begin::Form-->
        <form class="kt-form" @submit.prevent="formController(url, $event)">
            <div class="kt-portlet__body">
                <div class="row">

                    <div class="col-lg-3">
                        <div class="form-group">
                            <label class="form-control-label">Fecha de depósito:</label>
                            <datetime v-model="model.bank_date" placeholder="Selecciona una Fecha"
                                :format="'dd-LL-yyyy'" input-id="bank_date" name="bank_date" value-zone="America/Lima"
                                zone="America/Lima" class="form-control" :max-datetime="this.max_datetime"
                                @focus="$parent.clearErrorMsg($event)">
                            </datetime>
                            <div id="bank_date-error" class="error invalid-feedback"></div>
                        </div>
                    </div>

                    <div class="col-lg-3">
                        <div class="form-group">
                            <label class="form-control-label">Compañía:</label>
                            <select class="form-control" name="company_id" id="company_id" v-model="model.company_id"
                                @focus="$parent.clearErrorMsg($event)">
                                <option value="" selected disabled>Seleccionar</option>
                                <option v-for="company in companies" :value="company.id" v-bind:key="company.id">{{
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
                            <input type="text" class="form-control" name="operation_number" id="operation_number"
                                v-model="model.operation_number" @focus="$parent.clearErrorMsg($event)">
                            <div id="operation_number-error" class="error invalid-feedback"></div>
                        </div>
                    </div>

                    <div class="col-lg-3">
                        <div class="form-group">
                            <label class="form-control-label">Saldo total:</label>
                            <input type="text" class="form-control" name="total" id="total" v-model="model.total"
                                @focus="$parent.clearErrorMsg($event)">
                            <div id="total-error" class="error invalid-feedback"></div>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="form-group">
                            <label class="form-control-label">RUTA:</label>
                            <select class="form-control kt-select2" name="route_id" id="route_id"
                                v-model="model.route_id" @focus="$parent.clearErrorMsg($event)">
                                <option value="" selected disabled>Seleccionar</option>
                                <option v-for="route in routes" :value="route.id" v-bind:key="route.id">{{ route.name }}
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
            <div class="kt-portlet__foot">
                <div class="kt-form__actions">
                    <div class="row">
                        <div class="col-12 kt-align-right">
                            <button type="submit" class="btn btn-primary">Guardar</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
        <!--end::Form-->
    </div>
    <!--end::Portlet-->
</template>

<script>
import EventBus from '../event-bus';
import Datetime from 'vue-datetime';
// You need a specific loader for CSS files
import 'vue-datetime/dist/vue-datetime.css';

Vue.use(Datetime);

export default {
    name: 'RegisterVoucher',
    props: {
        companies: {
            type: Array,
            default: ''
        },
        routes: {
            type: Array,
            default: ''
        },
        url: {
            type: String,
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
    created() {
    },
    mounted() {
    },
    watch: {
        'model.company_id': function (val) {
            if (val != '') {
                EventBus.$emit('loading', true);

                axios.post(this.url_get_bank_accounts, {
                    company_id: this.model.company_id
                }).then(response => {
                    // console.log(response);
                    this.model.bank_account_id = '';
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
    methods: {
        formController: function (url, event) {
            var target = $(event.target);
            var url = url;
            var fd = new FormData(event.target);

            EventBus.$emit('loading', true);

            axios.post(url, {
                'model': this.model,

            }).then(response => {

                EventBus.$emit('loading', false);
                Swal.fire({
                    title: '¡Ok!',
                    text: 'Se creó el registro correctamente.',
                    type: "success",
                    heightAuto: false,
                });

                EventBus.$emit('refresh_table');
                this.cleanFields();

            }).catch(error => {
                $('.invalid-feedback').each(function () {
                    $(this).text('');
                    $(this).hide();
                });

                EventBus.$emit('loading', false);

                let errors = error.response.data.errors;

                for (const field in errors) {
                    let formatted_field = field.replace('model.', '');
                    $('#' + formatted_field + '-error').show();
                    $('#' + formatted_field + '-error').text(errors[field][0]);
                }
            });
        },
        cleanFields: function () {
            this.model.bank_account_id = '';
            this.model.company_id = '';
            this.model.route_id = '';
            this.model.obs = '';
            this.model.bank_date = null;
            this.model.operation_number = null;
            this.model.total = null;
        },
    }
};
</script>s
