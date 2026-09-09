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


                    <div class="col-lg-3">
                        <div class="form-group">
                            <label class="form-control-label">Placa:</label>
                            <select class="form-control" name="vehicle_id" id="vehicle_id" v-model="model.vehicle_id"
                                @focus="$parent.clearErrorMsg($event)">
                                <option value="" selected disabled>Seleccionar</option>
                                <option v-for="vehicle in vehicles" :value="vehicle.id" v-bind:key="vehicle.id">{{
                                    vehicle.plate }} -------------- {{ vehicle.company_name }}</option>
                            </select>
                            <div id="vehicle_id-error" class="error invalid-feedback"></div>
                        </div>
                    </div>

                    <div class="col-lg-3">
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

                    <div class="col-lg-3">
                        <div class="form-group">
                            <label class="form-control-label">Chofer:</label>
                            <select class="form-control kt-select2" name="employee_id" id="employee_id"
                                v-model="model.employee_id" @focus="$parent.clearErrorMsg($event)">
                                <option value="">Seleccionar</option>
                            </select>
                            <div id="employee_id-error" class="error invalid-feedback"></div>
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
import Swal from 'sweetalert2';
import EventBus from '../event-bus';
import Datetime from 'vue-datetime';
import 'vue-datetime/dist/vue-datetime.css';

Vue.use(Datetime);

export default {
    props: {
        url_get_employees: { type: String, default: '' },
        max_datetime: { type: String, default: '' },
        current_date: { type: String, default: '' },
        url: { type: String, default: '' },
        vehicles: { type: Array, default: '' },
        client_routes: { type: Array, default: '' },
    },
    data() {
        return {
            model: {
                route_id: '',
                plant_order_id: '',
                traslate_date: this.max_datetime,
                employee_id: '',
                vehicle_id: '',
            },
        }
    },
    watch: {

    },
    mounted() {
        this.newSelect2();

        EventBus.$on('clear_form', function () {
            this.model.route_id = '';
            this.model.plant_order_id = '';
            this.model.traslate_date = this.max_datetime;
            this.model.employee_id = '';
            this.model.vehicle_id = '';

            $('#employee_id').val(null).trigger('change');

        }.bind(this));
    },
    methods: {
        newSelect2: function () {
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
                    EventBus.$emit('loading', true);
                    axios.post(url, fd, {
                        headers: { 'Content-type': 'application/x-www-form-urlencoded' }
                    }).then(response => {
                        EventBus.$emit('loading', false);

                        this.$parent.alertMsg(response.data);
                        EventBus.$emit('refresh_table');
                        EventBus.$emit('clear_form');

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

<style scoped>
.vertical-cell {
    width: 40px;
    text-align: center;
    vertical-align: middle;
    writing-mode: vertical-lr;
    text-orientation: upright;
    font-weight: 600;
    padding: 8px;
}

.table td,
.table th {
    padding: .75rem;
}
</style>
