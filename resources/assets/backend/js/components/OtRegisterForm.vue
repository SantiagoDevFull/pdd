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
                            <label class="form-control-label">Compañía de Seguro:</label>
                            <select class="form-control" name="security_id" id="security_id" v-model="model.security_id"
                                @focus="$parent.clearErrorMsg($event)">
                                <option value="" selected disabled>Seleccionar</option>
                                <option v-for="security in company_securities" :value="security.id"
                                    v-bind:key="security.id">{{
            security.name }}</option>
                            </select>
                            <div id="security_id-error" class="error invalid-feedback"></div>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="form-group">
                            <label class="form-control-label">Asesor:</label>
                            <select class="form-control" name="asesor_id" id="asesor_id" v-model="model.asesor_id"
                                @focus="$parent.clearErrorMsg($event)">
                                <option value="" selected disabled>Seleccionar</option>
                                <option v-for="asesor in asesores" :value="asesor.id" v-bind:key="asesor.id">
                                    {{ asesor.document_number }} - {{ asesor.name }}</option>
                            </select>
                            <div id="asesor_id-error" class="error invalid-feedback"></div>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="form-group">
                            <label class="form-control-label">Fecha Emisión:</label>
                            <datetime v-model="model.issue_date" placeholder="Selecciona una Fecha"
                                :format="'dd-LL-yyyy'" input-id="since_date" name="issue_date" value-zone="America/Lima"
                                zone="America/Lima" class="form-control" @focus="$parent.clearErrorMsg($event)">
                            </datetime>
                            <div id="issue_date-error" class="error invalid-feedback"></div>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="form-group">
                            <label class="form-control-label">Serie:</label>
                            <select class="form-control" name="serie" id="serie" v-model="model.serie"
                                v-on:change="getNextCorrelative()" @focus="$parent.clearErrorMsg($event)">
                                <option value="" selected disabled>Seleccionar</option>
                                <option v-for="serie in series" :value="serie.id" v-bind:key="serie.id">
                                    {{ serie.num_serie }}</option>
                            </select>
                            <div id="serie-error" class="error invalid-feedback"></div>
                        </div>
                    </div>
                    <div class="col-lg-2">
                        <div class="form-group">
                            <label class="form-control-label">Correlativo:</label>
                            <input type="text" readonly class="form-control" name="correlative" id="correlative"
                                v-model="model.correlative" @focus="$parent.clearErrorMsg($event)">
                            <div id="correlative-error" class="error invalid-feedback"></div>
                        </div>
                    </div>
                    <div class="col-lg-1">
                        <div class="form-group">
                            <label class="form-control-label">KM Vehículo:</label>
                            <input type="text" class="form-control" name="km" id="km" v-model="model.km"
                                @focus="$parent.clearErrorMsg($event)">
                            <div id="km-error" class="error invalid-feedback"></div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label class="form-control-label">Vehículo | Cliente</label>
                            <select class="form-control kt-select2" name="vehicle_id" id="vehicle_id"
                                v-model="model.vehicle_id" @focus="$parent.clearErrorMsg($event)">
                                <option value="">Seleccionar</option>
                            </select>
                            <div id="vehicle_id-error" class="error invalid-feedback"></div>
                        </div>
                    </div>
                    <div class="col-lg-12">
                        <div class="form-group">
                            <label class="form-control-label">Observaciones:</label>
                            <textarea type="text" class="form-control" name="observation" id="observation"
                                v-model="model.observation" @focus="$parent.clearErrorMsg($event)">
                            </textarea>
                            <div id="observation-error" class="error invalid-feedback"></div>
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
        company_securities: {
            type: Array,
            default: ''
        },
        asesores: {
            type: Array,
            default: ''
        },
        url_get_series: {
            type: String,
            default: ''
        },
        url_get_vehicles: {
            type: String,
            default: '',
        },
        url_get_client: {
            type: String,
            default: '',
        },
        url_next_correlative: {
            type: String,
            default: '',
        },
        url: {
            type: String,
            default: '',
        }
    },
    data() {
        return {
            model: {
                company_id: '',
                security_id: '',
                asesor_id: '',
                serie: '',
                correlative: '',
                issue_date: '',
                vehicle_id: '',
                km: '',
                observation: '',
            },
            series: []
        }
    },
    created() {

    },
    mounted() {
        this.newSelect2();
    },
    watch: {
        'model.company_id': function (val) {
            if (val != '') {
                EventBus.$emit('loading', true);

                axios.post(this.url_get_series, {
                    company_id: this.model.company_id
                }).then(response => {
                    // console.log(response);
                    this.model.serie = '';
                    this.series = response.data;
                    this.model.correlative = '';
                    this.model.client_id = '';
                    this.model.client_name = '';
                    $('#vehicle_id').val(null).trigger('change');
                    EventBus.$emit('loading', false);
                }).catch(error => {
                    console.log(error);
                    console.log(error.response)
                });
            } else {
                this.model.serie = '';
                this.series = '';
            }
        },
    },
    computed: {

    },
    methods: {
        getNextCorrelative() {
            axios.get(this.url_next_correlative, {
                params: {
                    company: $('#company_id').val(),
                    serie: $('#serie').val()
                }
            }).then((response) => {
                this.model.correlative = response.data;
            }).catch((error) => {
                console.log(error.response);
            });
        },
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