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
                                <option value="">Seleccionar</option>
                                <option v-for="company in companies" :value="company.id" v-bind:key="company.id">{{
            company.name }}</option>
                            </select>
                            <div id="company_id-error" class="error invalid-feedback"></div>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="form-group">
                            <label class="form-control-label">Fecha Emisión:</label>
                            <datetime v-model="model.fac_date" placeholder="Selecciona una Fecha" :format="'dd-LL-yyyy'"
                                input-id="since_date" name="fac_date" value-zone="America/Lima" zone="America/Lima"
                                class="form-control" @focus="$parent.clearErrorMsg($event)">
                            </datetime>
                            <div id="fac_date-error" class="error invalid-feedback"></div>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="form-group">
                            <label class="form-control-label">Fecha Vigencia:</label>
                            <datetime v-model="model.traslate_date" placeholder="Selecciona una Fecha"
                                :format="'dd-LL-yyyy'" input-id="since_date" name="traslate_date"
                                value-zone="America/Lima" zone="America/Lima" class="form-control"
                                @focus="$parent.clearErrorMsg($event)">
                            </datetime>
                            <div id="traslate_date-error" class="error invalid-feedback"></div>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="form-group">
                            <label class="form-control-label">Vehículo | Cliente :</label>
                            <select class="form-control kt-select2" name="vehicle_id" id="vehicle_id"
                                v-model="model.vehicle_id" @focus="$parent.clearErrorMsg($event)">
                                <option value="">Seleccionar</option>
                            </select>
                            <div id="vehicle_id-error" class="error invalid-feedback"></div>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="form-group">
                            <label class="form-control-label">Tipo de Moneda:</label>
                            <select class="form-control" name="currency_id" id="currency_id" v-model="model.currency_id"
                                @focus="$parent.clearErrorMsg($event)" @change="State()">
                                <option value="" selected disabled>Seleccionar</option>
                                <option v-for="currency in currencies" :value="currency.id" v-bind:key="currency.id">{{
            currency.name }}</option>
                            </select>
                            <div id="currency_id-error" class="error invalid-feedback"></div>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="form-group">
                            <label class="form-control-label">Tipo de Cambio:</label>
                            <input v-model="model.tc" class="form-control" type="text" name="tc" id="tc"
                                placeholder="0.0" @focus="$parent.clearErrorMsg($event)">
                            <div id="tc-error" class="error invalid-feedback"></div>
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
            default: '',
        },
        currencies: {
            type: Array,
            default: ''
        },
        url: {
            type: String,
            default: '',
        },
        url_get_vehicles: {
            type: String,
            default: '',
        },
        current_date: {
            type: String,
            default: ''
        },
    },
    data() {
        return {
            model: {
                company_id: '',
                fac_date: this.current_date,
                traslate_date:  this.current_date,
                currency_id: '',
                tc: '',
                vehicle_id: '',
            },
        }
    },
    created() {

    },
    mounted() {
        this.newSelect2();
    },
    watch: {

    },
    computed: {

    },
    methods: {
        newSelect2: function () {
            let vm = this;
            let token = document.head.querySelector('meta[name="csrf-token"]').content;
            $("#vehicle_id").select2({
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
                    url: this.url_get_vehicles,
                    dataType: 'json',
                    delay: 250,
                    type: 'POST',
                    data: function (params) {
                        var queryParameters = {
                            q: params.term,
                            company_id: vm.model.company_id,
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
                vm.model.vehicle_id = parseInt(selected_element.val());
            }).on('select2:unselect', function (e) {
                vm.model.vehicle_id = '';
            });
        },
        State: function () {
            var tm = $('#currency_id').val();

            if (tm != "") {
                if (tm == 1) {
                    $("#tc").val("1");
                    this.model.tc = '1';
                    $("#tc").prop("readonly", true);
                } else {
                    $("#tc").prop("readonly", false);
                    this.model.tc = '';
                }
            }

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