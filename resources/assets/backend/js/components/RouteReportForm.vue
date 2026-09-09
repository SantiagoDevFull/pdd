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
                            <label class="form-control-label">Fecha inicial:</label>
                            <datetime v-model="model.initial_date" placeholder="Selecciona una Fecha inicial"
                                :format="'dd-LL-yyyy'" input-id="initial_date" name="initial_date"
                                value-zone="America/Lima" zone="America/Lima" class="form-control"
                                :max-datetime="current_date" @focus="$parent.clearErrorMsg($event)">
                            </datetime>
                            <div id="initial_date-error" class="error invalid-feedback"></div>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="form-group">
                            <label class="form-control-label">Fecha final:</label>
                            <datetime v-model="model.final_date" placeholder="Selecciona una Fecha final"
                                :format="'dd-LL-yyyy'" input-id="final_date" name="final_date" value-zone="America/Lima"
                                zone="America/Lima" class="form-control" :max-datetime="current_date"
                                @focus="$parent.clearErrorMsg($event)">
                            </datetime>
                            <div id="final_date-error" class="error invalid-feedback"></div>
                        </div>
                    </div>

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
                            <label class="form-control-label">RUTA:</label>
                            <select class="form-control" name="client_route_id" id="client_route_id"
                                v-model="model.client_route_id" @focus="$parent.clearErrorMsg($event)">
                                <option value="0">Todos</option>
                                <option v-for="client_route in client_routes" :value="client_route.id"
                                    v-bind:key="client_route.id">{{ client_route.name }}</option>
                            </select>
                            <div id="company_id-error" class="error invalid-feedback"></div>
                        </div>
                    </div>

                </div>
            </div>
            <div class="kt-portlet__foot">
                <div class="kt-form__actions">
                    <div class="row">
                        <div class="col-lg-6">
                            <button type="submit" class="btn btn-outline-brand btn-bold btn-sm">
                                <i class="fa fa-file-excel"></i> Exportar Excel
                            </button>
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

export default {
    props: {
        client_routes: {
            type: Array,
            default: ''
        },
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
        url_get_clients: {
            type: String,
            default: ''
        },
        url_export: {
            type: String,
            default: ''
        }
    },
    data() {
        return {
            model: {
                initial_date: '',
                final_date: '',
                client_route_id: '0',
                company_id: '',
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

        formController: function (url, event) {
            var vm = this;

            var target = $(event.target);
            var url = url;
            var fd = new FormData(event.target);

            EventBus.$emit('loading', true);
            console.log(url);
            axios.post(url, fd, {
                headers: {
                    'Content-type': 'application/x-www-form-urlencoded',
                }
            }).then(response => {
                axios.post(this.url_export, {
                    model: response.data,
                    export: 1,
                }, {
                    responseType: 'blob',
                }).then(response2 => {
                    EventBus.$emit('loading', false);

                    const tempUrl = window.URL.createObjectURL(new Blob([response2.data]));
                    const link = document.createElement('a');
                    link.href = tempUrl;
                    link.download = 'consulta-venta-canales-resumido' + Date.now() + '.xls';
                    document.body.appendChild(link);
                    link.click();

                }).catch(error => {
                    console.log(error);
                    console.log(error.response);
                    EventBus.$emit('loading', false);
                });
            }).catch(error => {
                console.log(error.response);
                EventBus.$emit('loading', false);
                var obj = error.response.data.errors;
                $('html, body').animate({
                    scrollTop: 0
                }, 500, 'swing');
                $.each(obj, function (i, item) {
                    // console.log(target);
                    let c_target = target.find("#" + i + "-error");
                    let p = c_target.parents('.form-group').find('#' + i);
                    p.addClass('is-invalid');
                    c_target.css('display', 'block');
                    c_target.html(item);
                });
            });
        },
    }
};
</script>