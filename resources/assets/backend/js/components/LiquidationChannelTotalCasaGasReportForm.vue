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
                            <label class="form-control-label">Unidad de Negocio:</label>
                            <select class="form-control" name="business_unit_id" id="business_unit_id"
                                v-model="model.business_unit_id" @focus="$parent.clearErrorMsg($event)">
                                <option value="" selected disabled>Seleccionar</option>
                                <option v-for="business_unit in business_units" :value="business_unit.id"
                                    v-bind:key="business_unit.id">{{ business_unit.name }}</option>
                            </select>
                            <div id="business_unit_id-error" class="error invalid-feedback"></div>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="form-group">
                            <label class="form-control-label">Sector:</label>
                            <select class="form-control" name="sector_id" id="sector_id"
                                v-model="model.sector_id" @focus="$parent.clearErrorMsg($event)">
                                <option value="" selected disabled>Seleccionar</option>
                                <option v-for="client_sector in client_sectors" :value="client_sector.id"
                                    v-bind:key="client_sector.id">{{ client_sector.name }}</option>
                            </select>
                            <div id="sector_id-error" class="error invalid-feedback"></div>
                        </div>
                    </div>

                </div>
            </div>
            <div class="kt-portlet__foot">
                <div class="kt-form__actions">
                    <div class="row">
                        <div class="col-lg-6">
                            <button type="submit" class="btn btn-primary">Buscar</button>
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
        business_units: {
            type: Array,
            default: ''
        },
        client_sectors: {
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
        }
    },
    data() {
        return {
            model: {
                initial_date: '',
                final_date: '',
                business_unit_id: '3',
                sector_id: '4',
            },
        }
    },
    created() {

    },
    mounted() {

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

            axios.post(url, fd, {
                headers: {
                    'Content-type': 'application/x-www-form-urlencoded',
                }
            }).then(response => {
                // console.log(response);
                EventBus.$emit('show_table', response.data);
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