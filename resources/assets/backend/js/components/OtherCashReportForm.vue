<template>
    <!--begin::Portlet-->
    <div class="kt-portlet">
        <div class="kt-portlet__head">
            <div class="kt-portlet__head-label">
                <h3 class="kt-portlet__head-title">
                    Buscar
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
                            <label class="form-control-label">Desde:</label>
                            <datetime v-model="model.since_date" placeholder="Selecciona una Fecha"
                                :format="'dd-LL-yyyy'" name="since_date" value-zone="America/Lima" zone="America/Lima"
                                class="form-control" :max-datetime="current_date"
                                @focus="$parent.clearErrorMsg($event)">
                            </datetime>
                            <div id="since_date-error" class="error invalid-feedback"></div>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="form-group">
                            <label class="form-control-label">Hasta:</label>
                            <datetime v-model="model.to_date" placeholder="Selecciona una Fecha" :format="'dd-LL-yyyy'"
                                name="to_date" value-zone="America/Lima" zone="America/Lima" class="form-control"
                                :max-datetime="current_date" @focus="$parent.clearErrorMsg($event)">
                            </datetime>
                            <div id="to_date-error" class="error invalid-feedback"></div>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="form-group">
                            <label class="form-control-label">Cliente:</label>
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
                            <label class="form-control-label">Tipo de reporte:</label>
                            <select class="form-control kt-select2" name="type" id="type" v-model="model.type"
                                @focus="$parent.clearErrorMsg($event)">
                                <option value="" selected disabled>Seleccionar</option>
                                <option value="1">Liquidación Detallado Otros Ingresos</option>
                                <option value="2">Cobranzas Otros Ingresos</option>
                            </select>
                            <div id="type-error" class="error invalid-feedback"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="kt-portlet__foot">
                <div class="kt-form__actions">
                    <div class="row">
                        <div class="col-lg-6">
                            <button type="submit" class="btn btn-primary">Exportar</button>
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
        current_date: {
            type: String,
            default: ''
        },
        url: {
            type: String,
            default: ''
        },
        url_export: {
            type: String,
            default: ''
        },
        url_get_accounts: {
            type: String,
            default: ''
        },
    },
    data() {
        return {
            model: {
                company_id: '1',
                since_date: '',
                to_date: '',
                warehouse_account_id: '',
                type: ''
            }
        }
    },
    mounted() {
        this.newSelect();
    },
    methods: {
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

                axios.post(this.url_export, {
                    model: response.data,
                }, {
                    responseType: 'blob',
                }).then(response => {
                    EventBus.$emit('loading', false);

                    const tempUrl = window.URL.createObjectURL(new Blob([response.data]));
                    const link = document.createElement('a');
                    link.href = tempUrl;
                    link.setAttribute('download', 'reporte-ingresos-caja-' + Date.now() + '.xls');
                    document.body.appendChild(link);
                    link.click();
                }).catch(error => {
                    console.log(error);
                    console.log(error.response);
                    EventBus.$emit('loading', false);
                });

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
        newSelect: function () {
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
    }
};
</script>