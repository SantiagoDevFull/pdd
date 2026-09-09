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
                            <label class="form-control-label">Tipo de moneda:</label>
                            <select class="form-control" name="currency" id="currency" v-model="model.currency"
                                @focus="$parent.clearErrorMsg($event)">
                                <option v-for="currency in currencies" :value="currency.id" v-bind:key="currency.id">{{
                                    currency.name }}</option>
                            </select>
                            <div id="currency-error" class="error invalid-feedback"></div>

                        </div>
                    </div>

                    <div class="col-lg-3">
                        <div class="form-group">
                            <label class="form-control-label">Fecha Emisión:</label>
                            <datetime v-model="model.since_date" placeholder="Selecciona una Fecha"
                                :format="'dd-LL-yyyy'" input-id="since_date" name="since_date" value-zone="America/Lima"
                                zone="America/Lima" class="form-control" :min-datetime="this.min_datetime"
                                :max-datetime="this.max_datetime" @focus="$parent.clearErrorMsg($event)">
                            </datetime>
                            <div id="since_date-error" class="error invalid-feedback"></div>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="form-group">
                            <label class="form-control-label">Tipo:</label>
                            <select class="form-control" name="warehouse_account_type_id" id="warehouse_account_type_id"
                                v-model="model.warehouse_account_type_id" @focus="$parent.clearErrorMsg($event)"
                                @change="warehouseAccountTypesChange()">
                                <option value="" disabled selected>Seleccionar</option>
                                <option v-for="warehouse_account_type in warehouseAccountTypes"
                                    :value="warehouse_account_type.id" v-bind:key="warehouse_account_type.id">{{
                                        warehouse_account_type.name }}</option>
                            </select>
                            <div id="warehouse_account_type_id-error" class="error invalid-feedback"></div>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="form-group">
                            <label class="form-control-label">Nombre o Razón Social:</label>
                            <select class="form-control kt-select2" name="warehouse_account_id"
                                id="warehouse_account_id" v-model="model.warehouse_account_id"
                                @focus="$parent.clearErrorMsg($event)">
                                <option value="">Seleccionar</option>
                            </select>
                            <div id="warehouse_account_id-error" class="error invalid-feedback"></div>
                        </div>
                    </div>

                    <div class="col-lg-3">
                        <div class="form-group">
                            <label class="form-control-label">Área:</label>
                            <select class="form-control" name="area_id" id="area_id" v-model="model.area_id"
                                @focus="$parent.clearErrorMsg($event)">
                                <option value="">Seleccionar</option>
                                <option v-for="area in areas" :value="area.id" v-bind:key="area.id">{{ area.name }}
                                </option>
                            </select>
                            <div id="area_id-error" class="error invalid-feedback"></div>
                        </div>
                    </div>

                    <div class="col-lg-3">
                        <div class="form-group">
                            <label class="form-control-label">Sub Área:</label>
                            <select class="form-control" name="subarea" id="subarea" v-model="model.subarea"
                                @focus="$parent.clearErrorMsg($event)">
                                <option disabled value="">Seleccionar</option>
                                <option value=" MAYORISTA"> MAYORISTA</option>
                                <option value="ABASTECIMIENTO">ABASTECIMIENTO</option>
                                <option value=" GRANEL"> GRANEL</option>
                                <option value="VENTA DIRECTA">VENTA DIRECTA</option>
                                <option value="CASA DEL GAS">CASA DEL GAS</option>
                            </select>
                            <div id="subarea-error" class="error invalid-feedback"></div>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="form-group">
                            <label class="form-control-label">Costo Principal:</label>
                            <select class="form-control kt-select2" name="costo_id" id="costo_id"
                                v-model="model.costo_id" @focus="$parent.clearErrorMsg($event)">
                                <option value="">Seleccionar</option>
                                <option v-for="costo in costos" :value="costo.id" v-bind:key="costo.id">{{ costo.name }}
                                </option>
                            </select>
                            <div id="costo_id-error" class="error invalid-feedback"></div>
                        </div>
                    </div>

                    <div class="col-lg-3">
                        <div class="form-group">
                            <label class="form-control-label">Costo Secundario:</label>
                            <select class="form-control kt-select2" name="costo_secundario_id" id="costo_secundario_id"
                                v-model="model.costo_secundario_id" @focus="$parent.clearErrorMsg($event)">
                                <option value="">Seleccionar</option>
                                <option v-for="costo_secundario in costo_secundarios" :value="costo_secundario.id"
                                    v-bind:key="costo_secundario.id">{{ costo_secundario.name }}</option>
                            </select>
                            <div id="costo_secundario_id-error" class="error invalid-feedback"></div>
                        </div>
                    </div>

               <!--     <div class="col-lg-3">
                        <div class="form-group">
                            <label class="form-control-label">Condición de Pago:</label>
                            <select class="form-control" name="payment_id" id="payment_id" v-model="model.payment_id"
                                @focus="$parent.clearErrorMsg($event)" readonly="readonly">
                                <option value="2">Credito</option>

                            </select>
                            <div id="payment_id-error" class="error invalid-feedback"></div>
                        </div>
                    </div>

                    <div class="col-lg-3">
                        <div class="form-group">
                            <label class="form-control-label">Días de Credito:</label>
                            <select class="form-control" name="credit_limit_id" id="credit_limit_id"
                                v-model="model.credit_limit_id" @focus="$parent.clearErrorMsg($event)">
                                <option disabled selected value="">Seleccionar</option>
                                <option value=7>7 días</option>
                                <option value=15>15 días</option>
                                <option value=30>30 días</option>
                                <option value=60>60 días</option>
                                <option value=90>90 días </option>

                            </select>
                            <div id="credit_limit_id-error" class="error invalid-feedback"></div>
                        </div>
                    </div>-->

                    <div class="col-lg-3">
                        <div class="form-group">
                            <label class="form-control-label">Presupuestado:</label>
                            <select class="form-control" name="ppto" id="ppto" v-model="model.ppto"
                                @focus="$parent.clearErrorMsg($event)">
                                <option disabled selected value="">Seleccionar</option>
                                <option value="SI">SI</option>
                                <option value="NO">NO</option>
                            </select>
                            <div id="ppto-error" class="error invalid-feedback"></div>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="form-group">
                            <label class="form-control-label">Tipo:</label>
                            <select class="form-control" name="tipo" id="tipo" v-model="model.tipo"
                                @focus="$parent.clearErrorMsg($event)">
                                <option disabled value="">Seleccionar</option>
                                <option value="BIEN">BIEN</option>
                                <option value="SERVICIO">SERVICIO</option>
                            </select>
                            <div id="tipo-error" class="error invalid-feedback"></div>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="form-group">
                            <label class="form-control-label">Tipo Referencia:</label>
                            <select class="form-control" name="referral_warehouse_document_type_id"
                                id="referral_warehouse_document_type_id"
                                v-model="model.referral_warehouse_document_type_id"
                                @focus="$parent.clearErrorMsg($event)">
                                <option value="" selected disabled>Seleccionar</option>
                                <option v-for="warehouse_document_type in warehouse_document_types"
                                    :value="warehouse_document_type.id" v-bind:key="warehouse_document_type.id">{{
                                        warehouse_document_type.name }}</option>
                            </select>
                            <div id="referral_warehouse_document_type_id-error" class="error invalid-feedback"></div>
                        </div>
                    </div>

                    <div class="col-lg-3">
                        <div class="form-group">
                            <label class="form-control-label">Serie de Referencia:</label>
                            <input type="text" class="form-control" name="referral_serie_number"
                                id="referral_serie_number" v-model="model.referral_serie_number"
                                @focus="$parent.clearErrorMsg($event)">
                            <div id="referral_serie_number-error" class="error invalid-feedback"></div>
                        </div>
                    </div>

                    <div class="col-lg-3">
                        <div class="form-group">
                            <label class="form-control-label">Número de Referencia:</label>
                            <input type="text" class="form-control" name="referral_voucher_number"
                                id="referral_voucher_number" v-model="model.referral_voucher_number"
                                @focus="$parent.clearErrorMsg($event)">
                            <div id="referral_voucher_number-error" class="error invalid-feedback"></div>
                        </div>
                    </div>
                    
                </div>
            </div>
            <div class="kt-portlet__foot">
                <div class="kt-form__actions">
                    <div class="row">
                        <div class="col-12 kt-align-right">
                            <button type="submit" class="btn btn-primary">Siguiente</button>
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
    props: {

        companies: {
            type: Array,
            default: ''
        },
        currencies: {
            type: Array,
            default: ''
        },
        current_date: {
            type: String,
            default: ''
        },
        min_datetime: {
             type: String,
             default: ''
         },
        max_datetime: {
            type: String,
            default: ''
        },
        warehouse_account_types: {
            type: Array,
            default: ''
        },
        warehouse_document_types: {
            type: Array,
            default: ''
        },
        url: {
            type: String,
            default: ''
        },
        url_get_accounts: {
            type: String,
            default: ''
        },
        url_get_areas: {
            type: String,
            default: ''
        },
        url_get_costos: {
            type: String,
            default: ''
        },
        url_get_costo_secs: {
            type: String,
            default: ''
        },
    },
    data() {
        return {
            model: {
                company_id: '',
                area_id: '',
                costo_id: '',
                costo_secundario_id: '',
                currency: 1,
                since_date: this.current_date,
                warehouse_account_type_id: 2,
                warehouse_account_id: '',
                referral_warehouse_document_type_id: '5',
                referral_serie_number: '',
                referral_voucher_number: '',
                credit_limit_id: '',
                payment_id: 2,
                subarea: '',
                ppto: '',
                tipo: '',

            },
            areas: [],
            costos: [],
            costo_secundarios: [],
        }
    },

    created() {
        EventBus.$on('reset_cochera_register', function () {
            this.model.company_id = '';
            this.model.area_id = '';
            this.model.costo_id = '';
            this.model.costo_secundario_id = '';
            this.model.currency = 1;
            this.model.since_date = this.current_date;
            this.model.warehouse_account_type_id = '';
            this.model.warehouse_account_id = '';
            this.model.referral_warehouse_document_type_id = '';
            this.model.referral_serie_number = '';
            this.model.referral_voucher_number = '';
            this.model.credit_limit_id = '';
            this.model.payment_id = '';
            this.model.subarea = '';
            this.model.ppto = '';
            this.model.tipo = '';

            $('.kt-form').find('input').prop('disabled', false);
            $('.kt-form').find('select').prop('disabled', false);
            $('.kt-form').find('button').prop('disabled', false);
        }.bind(this));
    },

    mounted() {
        this.newSelect2();
    },
    watch: {
        'model.company_id': function (val) {

            EventBus.$emit('loading', true);

            axios.post(this.url_get_areas, {
                company_id: this.model.company_id
            }).then(response => {
                this.areas = response.data;
                EventBus.$emit('loading', false);
            }).catch(error => {
                console.log(error);
                console.log(error.response)
            });

        },
        'model.area_id': function (val) {

            EventBus.$emit('loading', true);

            axios.post(this.url_get_costos, {
                area_id: this.model.area_id
            }).then(response => {
                this.costos = response.data;
                EventBus.$emit('loading', false);
            }).catch(error => {
                console.log(error);
                console.log(error.response)
            });

        },
        'model.costo_id': function (val) {

            EventBus.$emit('loading', true);

            axios.post(this.url_get_costo_secs, {
                costo_id: this.model.costo_id
            }).then(response => {
                this.costo_secundarios = response.data;
                EventBus.$emit('loading', false);
            }).catch(error => {
                console.log(error);
                console.log(error.response)
            });

        }

    },
    computed: {

        warehouseAccountTypes: function () {

            return this.warehouse_account_types;

        },
    },
    methods: {

        warehouseAccountTypesChange: function () {
            this.model.warehouse_account_id = '';
            $('#warehouse_account_id').val(null).trigger('change');
        },
        newSelect2: function () {
            let vm = this;
            let token = document.head.querySelector('meta[name="csrf-token"]').content;
            $("#warehouse_account_id").select2({
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
                    url: this.url_get_accounts,
                    dataType: 'json',
                    delay: 250,
                    type: 'POST',
                    data: function (params) {
                        var queryParameters = {
                            q: params.term,
                            company_id: vm.model.company_id,
                            warehouse_account_type_id: vm.model.warehouse_account_type_id,
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
                vm.model.warehouse_account_id = parseInt(selected_element.val());
            }).on('select2:unselect', function (e) {
                vm.model.warehouse_account_id = '';
            });
        },
        formController: function (url, event) {
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
                target.find('input').prop('disabled', true);
                target.find('select').prop('disabled', true);
                target.find('button').prop('disabled', true);
                EventBus.$emit('show_table', response.data);
                // console.log(response);
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
        },
    }
};
</script>