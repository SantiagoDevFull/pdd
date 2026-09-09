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
                            <label class="form-control-label">Ruta:</label>
                            <select class="form-control" name="route_id" id="route_id" v-model="model.route_id"
                                @focus="$parent.clearErrorMsg($event)">
                                <option value="" selected disabled>Seleccionar</option>
                                <option v-for="client_route in client_routes" :value="client_route.id"
                                    v-bind:key="client_route.id">{{ client_route.name }}</option>
                            </select>
                            <div id="route_id-error" class="error invalid-feedback"></div>
                        </div>
                    </div>

                    <div class="col-lg-5">
                        <div class="form-group">
                            <label class="form-control-label text-danger">PEDIDO ----------- PLACA ----------- FECHA
                                VENTA</label>
                            <select class="form-control" name="plant_order_id" id="plant_order_id"
                                v-model="model.plant_order_id" @focus="$parent.clearErrorMsg($event)">
                                <option value="" selected disabled>{{ title_select }}</option>
                                <option style="font-size: 1.2rem; font-weight: bold;" v-for="plant in plant_orders"
                                    :value="plant.id" v-bind:key="plant.id">{{
                                        plant.pedido }} --------- {{ plant.plate }} --------- {{ plant.traslate_date
                                    }}</option>
                            </select>
                            <div id="plant_order_id-error" class="error invalid-feedback"></div>
                        </div>
                    </div>

                    <div class="col-lg-3">
                        <div class="form-group">
                            <label class="form-control-label">Tipo de Registro:</label>
                            <select class="form-control" name="movement_class_id" id="movement_class_id"
                                style="pointer-events: none; background-color: #e9ecef;"
                                v-model="model.movement_class_id" @focus="$parent.clearErrorMsg($event)">
                                <option value="" selected disabled>Seleccionar</option>
                                <option v-for="movement in movement_classes" :value="movement.id"
                                    v-bind:key="movement.id">{{
                                        movement.name }}</option>
                            </select>
                            <div id="movement_class_id-error" class="error invalid-feedback"></div>
                        </div>
                    </div>

                    <div class="col-lg-3" v-show="this.model.route_id == 20 && this.model.plant_order_id == ''">
                        <div class="form-group">
                            <label class="form-control-label">Cliente:</label>
                            <select class="form-control kt-select2" name="client_id" id="client_id"
                                v-model="model.client_id" @focus="$parent.clearErrorMsg($event)">
                                <option value="">Seleccionar</option>
                            </select>
                            <div id="client_id-error" class="error invalid-feedback"></div>
                        </div>
                    </div>

                    <div class="col-lg-3" v-show="this.model.route_id == 20 && this.model.plant_order_id == ''">
                        <div class="form-group">
                            <label class="form-control-label">Chofer:</label>
                            <select class="form-control kt-select2" name="employee_id" id="employee_id"
                                v-model="model.employee_id" @focus="$parent.clearErrorMsg($event)">
                                <option value="">Seleccionar</option>
                            </select>
                            <div id="employee_id-error" class="error invalid-feedback"></div>
                        </div>
                    </div>

                    <div class="col-lg-3" v-show="this.model.route_id == 20 && this.model.plant_order_id == ''">
                        <div class="form-group">
                            <label class="form-control-label">Placa:</label>
                            <select class="form-control" name="vehicle_id" id="vehicle_id" v-model="model.vehicle_id"
                                @focus="$parent.clearErrorMsg($event)">
                                <option value="" selected disabled>Seleccionar</option>
                                <option v-for="vehicle in vehicles" :value="vehicle.id" v-bind:key="vehicle.id">{{
                                    vehicle.plate }}</option>
                            </select>
                            <div id="vehicle_id-error" class="error invalid-feedback"></div>
                        </div>
                    </div>

                    <div class="col-lg-3" v-show="this.model.route_id == 20 && this.model.plant_order_id == ''">
                        <div class="form-group">
                            <label class="form-control-label">Fecha de Traslado:</label>
                            <datetime v-model="model.traslate_date" placeholder="Selecciona una Fecha"
                                :format="'dd-LL-yyyy'" input-id="traslate_date" name="traslate_date"
                                value-zone="America/Lima" zone="America/Lima" class="form-control"
                                :min-datetime="this.current_date" :max-datetime="this.max_datetime"
                                @focus="$parent.clearErrorMsg($event)">
                            </datetime>
                            <div id="traslate_date-error" class="error invalid-feedback"></div>
                        </div>
                    </div>

                </div>

                <div class="row mt-4">
                    <div class="col-12">
                        <table class="table table-bordered">
                            <thead class="bg-warning">
                                <tr>
                                    <th colspan="2" class="text-center">Referencia</th>
                                    <th class="text-center">5kg</th>
                                    <th class="text-center">10kg</th>
                                    <th class="text-center">15kg</th>
                                    <th class="text-center">45kg</th>
                                </tr>
                            </thead>
                            <tfoot class="bg-success">
                                <tr>
                                    <td colspan="2" class="text-center">Total</td>
                                    <td><input type="text" name="total[5]" v-model="model.total[5]" class="form-control"
                                            @focus="$parent.clearErrorMsg($event)"
                                            @input="model.total[5] = model.total[5].replace(/[^0-9]/g, '')">
                                        <div id="total_5-error" class="error invalid-feedback"></div>
                                    </td>
                                    <td><input type="text" name="total[10]" v-model="model.total[10]"
                                            class="form-control" @focus="$parent.clearErrorMsg($event)"
                                            @input="model.total[10] = model.total[10].replace(/[^0-9]/g, '')">
                                        <div id="total_10-error" class="error invalid-feedback"></div>
                                    </td>
                                    <td><input type="text" name="total[15]" v-model="model.total[15]"
                                            class="form-control" @focus="$parent.clearErrorMsg($event)"
                                            @input="model.total[15] = model.total[15].replace(/[^0-9]/g, '')">
                                        <div id="total_15-error" class="error invalid-feedback"></div>
                                    </td>
                                    <td><input type="text" name="total[45]" v-model="model.total[45]"
                                            class="form-control" @focus="$parent.clearErrorMsg($event)"
                                            @input="model.total[45] = model.total[45].replace(/[^0-9]/g, '')">
                                        <div id="total_45-error" class="error invalid-feedback"></div>
                                    </td>
                                </tr>

                                <tr>
                                    <td colspan="2" class="text-center">Rechazados</td>
                                    <td><input type="text" name="rechazados[5]" v-model="model.rechazados[5]"
                                            class="form-control" @focus="$parent.clearErrorMsg($event)"
                                            @input="model.rechazados[5] = model.rechazados[5].replace(/[^0-9]/g, '')">
                                        <div id="rechazados_5-error" class="error invalid-feedback"></div>
                                    </td>
                                    <td><input type="text" name="rechazados[10]" v-model="model.rechazados[10]"
                                            class="form-control" @focus="$parent.clearErrorMsg($event)"
                                            @input="model.rechazados[10] = model.rechazados[10].replace(/[^0-9]/g, '')">
                                        <div id="rechazados_10-error" class="error invalid-feedback"></div>
                                    </td>
                                    <td><input type="text" name="rechazados[15]" v-model="model.rechazados[15]"
                                            class="form-control" @focus="$parent.clearErrorMsg($event)"
                                            @input="model.rechazados[15] = model.rechazados[15].replace(/[^0-9]/g, '')">
                                        <div id="rechazados_15-error" class="error invalid-feedback"></div>
                                    </td>
                                    <td><input type="text" name="rechazados[45]" v-model="model.rechazados[45]"
                                            class="form-control" @focus="$parent.clearErrorMsg($event)"
                                            @input="model.rechazados[45] = model.rechazados[45].replace(/[^0-9]/g, '')">
                                        <div id="rechazados_45-error" class="error invalid-feedback"></div>
                                    </td>
                                </tr>
                            </tfoot>

                        </table>
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
import 'vue-datetime/dist/vue-datetime.css';

Vue.use(Datetime);

export default {
    props: {
        movement_classes: { type: Array, default: '' },
        client_routes: { type: Array, default: '' },
        url: { type: String, default: '' },
        url_get_plant_orders: { type: String, default: '' },
        url_get_movement_class: { type: String, default: '' },
        url_get_clients: { type: String, default: '' },
        url_get_employees: { type: String, default: '' },
        vehicles: { type: Array, default: '' },
        max_datetime: { type: String, default: '' },
        current_date: { type: String, default: '' },
    },
    data() {
        return {
            model: {
                movement_class_id: '',
                client_id: '',
                employee_id: '',
                vehicle_id: '',
                traslate_date: this.max_datetime,
                plant_order_id: '',
                route_id: '',
                total: { 5: '0', 10: '0', 15: '0', 45: '0' },
                rechazados: { 5: '0', 10: '0', 15: '0', 45: '0' }
            },
            plant_orders: [],
            title_select: 'Seleccionar',
        }
    },
    watch: {
        'model.route_id': function (val) {

            this.model.movement_class_id = '';

            if (val == 20) {
                this.title_select = 'NUEVO PEDIDO';
                this.model.movement_class_id = 1;
            } else {
                this.title_select = 'Seleccionar';
            }

            EventBus.$emit('loading', true);
            axios.post(this.url_get_plant_orders, {
                route_id: val
            }).then(response => {
                this.model.plant_order_id = '';
                this.plant_orders = [];
                this.plant_orders = response.data;
                EventBus.$emit('loading', false);
            }).catch(error => {
                console.log(error);
                console.log(error.response);
                EventBus.$emit('loading', false);
            });

        },
        'model.plant_order_id': function (val) {

            if (this.model.route_id == 20) {
                if (val != '') {
                    this.model.movement_class_id = 2;
                } else {
                    this.model.movement_class_id = 1;
                }
            } else {
                this.model.movement_class_id = '';
                EventBus.$emit('loading', true);
                axios.post(this.url_get_movement_class, {
                    plant_order_id: val
                }).then(response => {
                    this.model.movement_class_id = response.data;
                    EventBus.$emit('loading', false);
                }).catch(error => {
                    console.log(error);
                    console.log(error.response);
                    EventBus.$emit('loading', false);
                });
            }

        },


    },
    mounted() {
        this.newSelect2();
        this.newSelect3();
    },
    methods: {
        newSelect2: function () {
            let vm = this;
            let token = document.head.querySelector('meta[name="csrf-token"]').content;
            $("#client_id").select2({
                placeholder: "Buscar",
                allowClear: true,
                language: {
                    noResults: () => 'No hay resultados',
                    searching: () => 'Buscando...',
                    inputTooShort: () => 'Ingresa 1 o más caracteres',
                    errorLoading: () => 'No se pudo cargar la información'
                },
                ajax: {
                    url: this.url_get_clients,
                    dataType: 'json',
                    delay: 250,
                    type: 'POST',
                    data: params => ({
                        q: params.term,
                        route_id: vm.model.route_id,
                        _token: token,
                    }),
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
                vm.model.client_id = parseInt($(e.currentTarget).val());
            }).on('select2:unselect', function () {
                vm.model.client_id = '';
            });
        },
        newSelect3: function () {
            let vm = this;
            let token = document.head.querySelector('meta[name="csrf-token"]').content;
            $("#employee_id").select2({
                placeholder: "Buscar",
                allowClear: true,
                language: {
                    noResults: () => 'No hay resultados',
                    searching: () => 'Buscando...',
                    inputTooShort: () => 'Ingresa 1 o más caracteres',
                    errorLoading: () => 'No se pudo cargar la información'
                },
                ajax: {
                    url: this.url_get_employees,
                    dataType: 'json',
                    delay: 250,
                    type: 'POST',
                    data: params => ({
                        q: params.term,
                        route_id: vm.model.route_id,
                        _token: token,
                    }),
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
                vm.model.employee_id = parseInt($(e.currentTarget).val());
            }).on('select2:unselect', function () {
                vm.model.employee_id = '';
            });
        },
        formController: function (url, event) {
            var target = $(event.target);
            var fd = new FormData(event.target);

            Swal.fire({
                title: '¡Cuidado!',
                text: '¿Seguro que desea guardar el registro?',
                type: "warning",
                heightAuto: false,
                showCancelButton: true,
                confirmButtonText: 'Sí',
                cancelButtonText: 'No'
            }).then(result => {

                EventBus.$emit('loading', true);
                if (result.value) {
                    axios.post(url, fd, {
                        headers: { 'Content-type': 'application/x-www-form-urlencoded' }
                    }).then(response => {
                        EventBus.$emit('loading', false);

                        Swal.fire({
                            title: response.data.title,
                            text: response.data.msg,
                            type: "success",
                            heightAuto: false,
                            allowOutsideClick: true,
                            allowEscapeKey: true,
                            showConfirmButton: true
                        }).then(function (result) {
                            if (result && (result.value !== undefined || result.dismiss !== undefined)) {
                                window.location = response.data.url;
                            }
                        });

                        console.log(response.data);
                    }).catch(error => {
                        EventBus.$emit('loading', false);
                        console.log(error.response);
                        var obj = error.response.data.errors;
                        $('html, body').animate({ scrollTop: 0 }, 500, 'swing');
                        $.each(obj, function (i, item) {
                            let c_target = target.find("#" + i.replace('.', '_') + "-error");
                            if (!c_target.attr('data-required')) {
                                let p = c_target.prev();
                                p.addClass('is-invalid');
                            } else {
                                c_target.css('display', 'block');
                            }
                            c_target.html(item);
                        });
                    });
                } else if (result.dismiss == Swal.DismissReason.cancel) {
                    EventBus.$emit('loading', false);
                }
            });
        },

    }
};
</script>
