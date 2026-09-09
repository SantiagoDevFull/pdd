<template>
    <!--begin::Modal-->
    <div class="modal fade" id="modal-sale-presupuesto" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <!--begin::Form-->
                <form class="kt-form" @submit.prevent="formController(url, $event)">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">{{ this.button_text }} Presupuesto</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="kt-portlet__body">
                            <div class="row">
                                <input type="hidden" name="id" id="id" v-model="model.id">
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label class="form-control-label">UNIDAD</label>
                                        <select class="form-control" name="business_unit_id" id="business_unit_id"
                                            v-model="model.business_unit_id" @focus="$parent.clearErrorMsg($event)">
                                            <option value="" selected disabled>Seleccionar</option>
                                            <option v-for="business_unit in business_units" :value="business_unit.id"
                                                v-bind:key="business_unit.id">{{ business_unit.name }}
                                            </option>
                                        </select>
                                        <div id="business_unit_id-error" class="error invalid-feedback"></div>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label class="form-control-label">CANAL</label>
                                        <select class="form-control" name="client_sector_id" id="client_sector_id"
                                            v-model="model.client_sector_id" @focus="$parent.clearErrorMsg($event)">
                                            <option value="" selected disabled>Seleccionar</option>
                                            <option v-for="client_sector in client_sectors" :value="client_sector.id"
                                                v-bind:key="client_sector.id">{{ client_sector.name }}
                                            </option>
                                        </select>
                                        <div id="client_sector_id-error" class="error invalid-feedback"></div>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label class="form-control-label">SUB CANAL</label>
                                        <select class="form-control" name="client_channel_id" id="client_channel_id"
                                            v-model="model.client_channel_id" @focus="$parent.clearErrorMsg($event)">
                                            <option value="" selected disabled>Seleccionar</option>
                                            <option v-for="client_channel in client_channels" :value="client_channel.id"
                                                v-bind:key="client_channel.id">{{ client_channel.name }}
                                            </option>
                                        </select>
                                        <div id="client_channel_id-error" class="error invalid-feedback"></div>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label class="form-control-label">RUTA</label>
                                        <select class="form-control" name="client_route_id" id="client_route_id"
                                            v-model="model.client_route_id" @focus="$parent.clearErrorMsg($event)">
                                            <option value="" selected disabled>Seleccionar</option>
                                            <option v-for="client_route in client_routes" :value="client_route.id"
                                                v-bind:key="client_route.id">{{ client_route.short_name }}
                                            </option>
                                        </select>
                                        <div id="client_route_id-error" class="error invalid-feedback"></div>
                                    </div>
                                </div>
                                <div class="col-lg-3">
                                    <div class="form-group"> <label class="form-control-label">AÑO:</label>
                                        <input type="text" class="form-control" name="anio" id="anio" readonly=""
                                            v-model="model.anio" @focus="$parent.clearErrorMsg($event)">
                                        <div id="anio-error" class="error invalid-feedback"></div>
                                    </div>
                                </div>
                                <div class="col-lg-3">
                                    <div class="form-group"> <label class="form-control-label">MES:</label> <input
                                            type="text" class="form-control" name="mes" id="mes" v-model="model.mes"
                                            readonly="" @focus="$parent.clearErrorMsg($event)">
                                        <div id="mes-error" class="error invalid-feedback"></div>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label class="form-control-label">Supervisor:</label>
                                        <select class="form-control" name="manager_id" id="manager_id"
                                            v-model="model.manager_id" @focus="$parent.clearErrorMsg($event)">
                                            <option value="">Seleccionar</option>
                                            <option v-for="manager in managers" :value="manager.id"
                                                v-bind:key="manager.id">{{ manager.name }}
                                            </option>
                                        </select>
                                        <div id="manager_id-error" class="error invalid-feedback"></div>
                                    </div>
                                </div>
                                <div class="col-lg-3">
                                    <div class="form-group"> <label class="form-control-label">PPTO VENTA:</label>
                                        <input type="text" class="form-control" name="ppto_venta" id="ppto_venta"
                                            v-model="model.ppto_venta" @focus="$parent.clearErrorMsg($event)">
                                        <div id="ppto_venta-error" class="error invalid-feedback"></div>
                                    </div>
                                </div>
                                <div class="col-lg-3">
                                    <div class="form-group"> <label class="form-control-label">PPTO MB:</label> <input
                                            type="text" class="form-control" name="ppto_mb" id="ppto_mb"
                                            v-model="model.ppto_mb" @focus="$parent.clearErrorMsg($event)">
                                        <div id="ppto_mb-error" class="error invalid-feedback"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success" id="add_article_2">
                            {{ button_text }}
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
        business_units: {
            type: Array,
            default: ''
        },
        client_sectors: {
            type: Array,
            default: ''
        },
        client_channels: {
            type: Array,
            default: ''
        },
        client_routes: {
            type: Array,
            default: ''
        },
        managers: {
            type: Array,
            default: ''
        },
    },
    data() {
        return {
            model: {
                id: '',
                anio: '',
                mes: '',
                business_unit_id: '',
                client_sector_id: '',
                client_channel_id: '',
                client_route_id: '',
                manager_id: '',
                ppto_venta: '',
                ppto_mb: '',

            },
            button_text: '',
        }
    },
    watch: {

    },
    computed: {

    },
    created() {
        EventBus.$on('create_modal', function (anio, mes) {

            this.button_text = 'Crear';
            this.model.id = '';
            this.model.anio = anio;

            if (mes == 1) this.model.mes = 'Enero';
            if (mes == 2) this.model.mes = 'Febrero';
            if (mes == 3) this.model.mes = 'Marzo';
            if (mes == 4) this.model.mes = 'Abril';
            if (mes == 5) this.model.mes = 'Mayo';
            if (mes == 6) this.model.mes = 'Junio';
            if (mes == 7) this.model.mes = 'Julio';
            if (mes == 8) this.model.mes = 'Agosto';
            if (mes == 9) this.model.mes = 'Septiembre';
            if (mes == 10) this.model.mes = 'Octubre';
            if (mes == 11) this.model.mes = 'Noviembre';
            if (mes == 12) this.model.mes = 'Diciembre';

            this.model.business_unit_id = '';
            this.model.client_sector_id = '';
            this.model.client_channel_id = '';
            this.model.client_route_id = '';
            this.model.manager_id = '';
            this.model.ppto_venta = '';
            this.model.ppto_mb = '';

            $('#modal-sale-presupuesto').modal({
                backdrop: 'static',
                keyboard: false
            });
        }.bind(this));

        EventBus.$on('edit_modal', function (model) {

            this.model.id = '';
            this.model.anio = anio;
            this.model.mes = mes;

            this.model.business_unit_id = '';
            this.model.client_sector_id = '';
            this.model.client_channel_id = '';
            this.model.client_route_id = '';
            this.model.manager_id = '';
            this.model.ppto_venta = '';
            this.model.ppto_mb = '';

            this.model = model;
            this.button_text = 'Actualizar';
            $('#modal-sale-presupuesto').modal({
                backdrop: 'static',
                keyboard: false
            });

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
                $('#modal-sale-presupuesto').modal('hide');
                this.$parent.alertMsg(response.data);

                this.model.id = '';
                this.model.anio = '';
                this.model.mes = '';
                this.model.client_route_id = '';
                this.model.manager_id = '';
                this.model.ppto_venta = '';
                this.model.ppto_mb = '';

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
