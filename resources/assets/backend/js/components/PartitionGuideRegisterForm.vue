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
                                <option value="" selected disabled>Seleccionar</option>
                                <option style="font-size: 1.2rem; font-weight: bold;" v-for="plant in plant_orders"
                                    :value="plant.id" v-bind:key="plant.id">{{
                                        plant.pedido }} --------- {{ plant.plate }} --------- {{ plant.traslate_date
                                    }}</option>
                            </select>
                            <div id="plant_order_id-error" class="error invalid-feedback"></div>
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
                            <tbody class="bg-secondary">




                                <tr>
                                    <td class="vertical-cell" rowspan="5">Llenos</td>
                                    <td class="bg-warning text-white">Rinde Gas</td>
                                    <td><input type="text" name="rinde_gas[5]" v-model="model.rinde_gas[5]" readonly
                                            class="form-control bg-white">
                                    </td>
                                    <td><input type="text" name="rinde_gas[10]" v-model="model.rinde_gas[10]" readonly
                                            class="form-control bg-white">
                                    </td>
                                    <td><input type="text" name="rinde_gas[15]" v-model="model.rinde_gas[15]" readonly
                                            class="form-control bg-white">
                                    </td>
                                    <td><input type="text" name="rinde_gas[45]" v-model="model.rinde_gas[45]" readonly
                                            class="form-control bg-white">
                                    </td>
                                </tr>
                                <tr>
                                    <td class="bg-warning text-white">Premium Gas</td>
                                    <td><input type="text" name="premium_gas[5]" v-model="model.premium_gas[5]" readonly
                                            class="form-control bg-white">
                                    </td>
                                    <td><input type="text" name="premium_gas[10]" v-model="model.premium_gas[10]"
                                            readonly class="form-control bg-white">
                                    </td>
                                    <td><input type="text" name="premium_gas[15]" v-model="model.premium_gas[15]"
                                            readonly class="form-control bg-white">
                                    </td>
                                    <td><input type="text" name="premium_gas[45]" v-model="model.premium_gas[45]"
                                            readonly class="form-control bg-white">
                                    </td>
                                </tr>
                                <tr>
                                    <td class="bg-warning text-white">El Gas</td>
                                    <td><input type="text" name="el_gas[5]" v-model="model.el_gas[5]" readonly
                                            class="form-control bg-white">
                                    </td>
                                    <td><input type="text" name="el_gas[10]" v-model="model.el_gas[10]" readonly
                                            class="form-control bg-white">
                                    </td>
                                    <td><input type="text" name="el_gas[15]" v-model="model.el_gas[15]" readonly
                                            class="form-control bg-white">
                                    </td>
                                    <td><input type="text" name="el_gas[45]" v-model="model.el_gas[45]" readonly
                                            class="form-control bg-white">
                                    </td>
                                </tr>
                                <tr>
                                    <td class="bg-warning text-white">Plus Gas</td>
                                    <td><input type="text" name="plus_gas[5]" v-model="model.plus_gas[5]" readonly
                                            class="form-control bg-white">
                                    </td>
                                    <td><input type="text" name="plus_gas[10]" v-model="model.plus_gas[10]" readonly
                                            class="form-control bg-white">
                                    </td>
                                    <td><input type="text" name="plus_gas[15]" v-model="model.plus_gas[15]" readonly
                                            class="form-control bg-white">
                                    </td>
                                    <td><input type="text" name="plus_gas[45]" v-model="model.plus_gas[45]" readonly
                                            class="form-control bg-white">
                                    </td>
                                </tr>
                                <tr>
                                    <td class="bg-warning text-white">Maqui Gas</td>
                                    <td><input type="text" name="maqui_gas[5]" v-model="model.maqui_gas[5]" readonly
                                            class="form-control bg-white">
                                    </td>
                                    <td><input type="text" name="maqui_gas[10]" v-model="model.maqui_gas[10]" readonly
                                            class="form-control bg-white">
                                    </td>
                                    <td><input type="text" name="maqui_gas[15]" v-model="model.maqui_gas[15]" readonly
                                            class="form-control bg-white">
                                    </td>
                                    <td><input type="text" name="maqui_gas[45]" v-model="model.maqui_gas[45]" readonly
                                            class="form-control bg-white">
                                    </td>
                                </tr>
                            </tbody>
                            <tfoot>

                                <tr class="bg-warning">
                                    <th colspan="2" class="text-center">Total</th>
                                    <th>
                                        <input type="text" name="total[5]" class="form-control bg-white"
                                            v-model="model.total[5]" readonly>
                                    </th>
                                    <th>
                                        <input type="text" name="total[10]" class="form-control bg-white"
                                            v-model="model.total[10]" readonly>
                                    </th>
                                    <th>
                                        <input type="text" name="total[15]" class="form-control bg-white"
                                            v-model="model.total[15]" readonly>
                                    </th>
                                    <th>
                                        <input type="text" name="total[45]" class="form-control bg-white"
                                            v-model="model.total[45]" readonly>
                                    </th>
                                </tr>
                                <tr class="bg-warning">
                                    <td colspan="2" class="text-center text-white">Rechazados</td>
                                    <td><input type="text" name="rechazados[5]" v-model="model.rechazados[5]" readonly
                                            class="form-control bg-white">
                                    </td>
                                    <td><input type="text" name="rechazados[10]" v-model="model.rechazados[10]" readonly
                                            class="form-control bg-white">
                                    </td>
                                    <td><input type="text" name="rechazados[15]" v-model="model.rechazados[15]" readonly
                                            class="form-control bg-white">
                                    </td>
                                    <td><input type="text" name="rechazados[45]" v-model="model.rechazados[45]" readonly
                                            class="form-control bg-white">
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                <div class="col-lg-3">
                    <div class="form-group">
                        <label class="form-control-label">Seleccionar N° Partición:</label>
                        <select class="form-control" name="partition" id="partition" v-model="model.partition"
                            @mousedown.prevent @keydown.prevent @focus="$parent.clearErrorMsg($event)">
                            <option value="" selected disabled>Seleccionar</option>
                            <option value="1">1</option>
                            <option value="2">2</option>
                        </select>
                        <div id="partition-error" class="error invalid-feedback"></div>
                    </div>
                </div>

                <div class="col-lg-12">
                    <div class="form-group">
                        <hr class="border border-danger">
                    </div>
                </div>

                <div class="row mt-4">
                    <div class="col-lg-2">
                        <div class="form-group">
                            <label class="form-control-label">Capacidad del Camion KG:</label>
                            <input type="text" class="form-control" name="capacity_camion" id="capacity_camion"
                                v-model="model.capacity_camion" readonly>
                            <div id="capacity_camion-error" class="error invalid-feedback"></div>
                        </div>
                    </div>

                    <div class="col-lg-2">
                        <div class="form-group">
                            <label class="form-control-label">Total KG Registrado:</label>
                            <input type="text" class="form-control" name="capacity_permitida" id="capacity_permitida"
                                v-model="model.capacity_permitida" readonly>
                            <div id="capacity_permitida-error" class="error invalid-feedback"></div>
                        </div>
                    </div>

                    <div class="col-lg-2">
                        <div class="form-group">
                            <label class="form-control-label">Registro de Hidrocarburos:</label>
                            <input type="text" class="form-control" name="activity" id="activity"
                                v-model="model.activity" @focus="$parent.clearErrorMsg($event)">
                            <div id="activity-error" class="error invalid-feedback"></div>
                        </div>
                    </div>

                    <div class="col-lg-2">
                        <div class="form-group">
                            <label class="form-control-label">Código de Autorización:</label>
                            <input type="text" class="form-control" name="scop" id="scop" v-model="model.scop"
                                @focus="$parent.clearErrorMsg($event)">
                            <div id="scop-error" class="error invalid-feedback"></div>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div class="form-group">
                            <label class="form-control-label">Ubigeo:</label>
                            <input type="hidden" class="form-control" name="ubigeo_id" id="ubigeo_id"
                                v-model="model.ubigeo_id" readonly>
                            <input type="text" class="form-control" name="ubigeo_name" id="ubigeo_name"
                                v-model="model.ubigeo_name" readonly>
                            <div id="ubigeo_name-error" class="error invalid-feedback"></div>
                        </div>
                    </div>

                    <div class="col-lg-12">
                        <div class="form-group">
                            <hr class="border border-danger">
                        </div>
                    </div>

                    <div class="col-12">
                        <div v-for="(partition, index) in partitions" :key="partition.id" class="mt-5">
                            <h5 class="text-primary">Guía Electrónica {{ index + 1 }}</h5>
                            <table class="table table-bordered">
                                <thead class="bg-info">
                                    <tr>
                                        <th colspan="2" class="text-center">Referencia</th>
                                        <th class="text-center">5kg</th>
                                        <th class="text-center">10kg</th>
                                        <th class="text-center">15kg</th>
                                        <th class="text-center">45kg</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-secondary">
                                    <tr>
                                        <td rowspan="5" class="vertical-cell">Llenos</td>
                                        <td class="bg-primary text-white">Rinde Gas</td>
                                        <td><input type="text" v-model="partition.rinde_gas[5]" class="form-control"
                                                readonly="">
                                        </td>
                                        <td><input type="text" v-model="partition.rinde_gas[10]" class="form-control"
                                                readonly="">
                                        </td>
                                        <td><input type="text" v-model="partition.rinde_gas[15]" class="form-control"
                                                readonly="">
                                        </td>
                                        <td><input type="text" v-model="partition.rinde_gas[45]" class="form-control"
                                                readonly="">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="bg-primary text-white">Premium Gas</td>
                                        <td><input type="text" v-model="partition.premium_gas[5]" class="form-control"
                                                readonly="">
                                        </td>
                                        <td><input type="text" v-model="partition.premium_gas[10]" class="form-control"
                                                readonly="">
                                        </td>
                                        <td><input type="text" v-model="partition.premium_gas[15]" class="form-control"
                                                readonly="">
                                        </td>
                                        <td><input type="text" v-model="partition.premium_gas[45]" class="form-control"
                                                readonly="">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="bg-primary text-white">El Gas</td>
                                        <td><input type="text" v-model="partition.el_gas[5]" class="form-control"
                                                readonly=""></td>
                                        <td><input type="text" v-model="partition.el_gas[10]" class="form-control"
                                                readonly=""></td>
                                        <td><input type="text" v-model="partition.el_gas[15]" class="form-control"
                                                readonly=""></td>
                                        <td><input type="text" v-model="partition.el_gas[45]" class="form-control"
                                                readonly=""></td>
                                    </tr>
                                    <tr>
                                        <td class="bg-primary text-white">Plus Gas</td>
                                        <td><input type="text" v-model="partition.plus_gas[5]" class="form-control"
                                                readonly="">
                                        </td>
                                        <td><input type="text" v-model="partition.plus_gas[10]" class="form-control"
                                                readonly="">
                                        </td>
                                        <td><input type="text" v-model="partition.plus_gas[15]" class="form-control"
                                                readonly="">
                                        </td>
                                        <td><input type="text" v-model="partition.plus_gas[45]" class="form-control"
                                                readonly="">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="bg-primary text-white">Maqui Gas</td>
                                        <td><input type="text" v-model="partition.maqui_gas[5]" class="form-control"
                                                readonly="">
                                        </td>
                                        <td><input type="text" v-model="partition.maqui_gas[10]" class="form-control"
                                                readonly="">
                                        </td>
                                        <td><input type="text" v-model="partition.maqui_gas[15]" class="form-control"
                                                readonly="">
                                        </td>
                                        <td><input type="text" v-model="partition.maqui_gas[45]" class="form-control"
                                                readonly="">
                                        </td>
                                    </tr>
                                    <tr class="bg-success">
                                        <td colspan="2" class="text-white text-center">Total</td>
                                        <td>{{ getTotal(partition, 5) }}</td>
                                        <td>{{ getTotal(partition, 10) }}</td>
                                        <td>{{ getTotal(partition, 15) }}</td>
                                        <td>{{ getTotal(partition, 45) }}</td>
                                    </tr>
                                    <tr class="table-secondary font-weight-bold">
                                        <td colspan="5" class="text-right">
                                            TOTAL PARTICION:
                                        </td>
                                        <td class="text-right">
                                            <span style="font-size: 1.2rem;">
                                                {{ calcularTotalParticion(partition) }}
                                            </span> KG
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
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
import Swal from 'sweetalert2';


export default {
    props: {
        url: { type: String, default: '' },
        url_get_plant_orders: { type: String, default: '' },
        url_get_ubigeos: { type: String, default: '' },
        url_get_plant: { type: String, default: '' },
        client_routes: { type: Array, default: '' },
        current_date: { type: String, default: '' },
        max_datetime: { type: String, default: '' },
    },
    data() {
        return {
            model: {
                route_id: '',
                plant_order_id: '',
                ubigeo_id: '',
                ubigeo_name: '',

                scop: '',

                rinde_gas: { 5: 0, 10: 0, 15: 0, 45: 0 },
                premium_gas: { 5: 0, 10: 0, 15: 0, 45: 0 },
                el_gas: { 5: 0, 10: 0, 15: 0, 45: 0 },
                plus_gas: { 5: 0, 10: 0, 15: 0, 45: 0 },
                maqui_gas: { 5: 0, 10: 0, 15: 0, 45: 0 },

                total: { 5: 0, 10: 0, 15: 0, 45: 0 },
                rechazados: { 5: 0, 10: 0, 15: 0, 45: 0 },

                capacity_camion: '',
                capacity_permitida: '',
                activity: '',
                partition: ''
            },
            partitions: [],
            plant_orders: []
        }
    },
    watch: {

        'model.route_id': function (val) {

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

            if (val != '') {
                EventBus.$emit('loading', true);
                axios.post(this.url_get_plant, {
                    plant_order_id: val
                }).then(response => {

                    EventBus.$emit('loading', false);

                    if (response.data.capacity_camion == null || response.data.capacity_camion == '' || response.data.capacity_camion <= 0) {
                        Swal.fire('Atención', 'La capacidad del camion es requerida', 'warning');
                        this.model.plant_order_id = '';
                    } else {
                        Object.assign(this.model, response.data);
                        this.crearParticionesAutomaticas();
                    }

                }).catch(error => {
                    console.log(error);
                    console.log(error.response);
                    EventBus.$emit('loading', false);
                });
            }
        },
    },

    mounted() {

    },
    methods: {
        getTotal(partition, kg) {
            let total = 0;
            ['rinde_gas', 'premium_gas', 'el_gas', 'plus_gas', 'maqui_gas'].forEach(marca => {
                total += Number(partition[marca][kg]) || 0;
            });
            return total;
        },
        calcularTotalParticion(partition) {

            const marcas = ['rinde_gas', 'premium_gas', 'el_gas', 'plus_gas', 'maqui_gas'];
            const pesos = [5, 10, 15, 45];

            let total = 0;

            marcas.forEach(marca => {
                pesos.forEach(kg => {
                    total += Number(partition[marca][kg] || 0) * kg;
                });
            });

            return total;
        },
        nuevaParticion() {
            return {
                rinde_gas: { 5: 0, 10: 0, 15: 0, 45: 0 },
                premium_gas: { 5: 0, 10: 0, 15: 0, 45: 0 },
                el_gas: { 5: 0, 10: 0, 15: 0, 45: 0 },
                plus_gas: { 5: 0, 10: 0, 15: 0, 45: 0 },
                maqui_gas: { 5: 0, 10: 0, 15: 0, 45: 0 },
                total: { 5: 0, 10: 0, 15: 0, 45: 0 }
            };
        },
        crearParticionesAutomaticas() {

            const capacidadMax = Number(this.model.capacity_camion);
            let capacidadUsada = 0;

            const p1 = this.nuevaParticion();
            const p2 = this.nuevaParticion();

            const marcas = ['rinde_gas', 'premium_gas', 'el_gas', 'plus_gas', 'maqui_gas'];
            const pesos = [45, 15, 10, 5];

            marcas.forEach(marca => {
                pesos.forEach(kg => {

                    let cantidad = Number(this.model[marca][kg] || 0);

                    while (cantidad > 0) {
                        if (capacidadUsada + kg <= capacidadMax) {
                            p1[marca][kg]++;
                            capacidadUsada += kg;
                        } else {
                            p2[marca][kg]++;
                        }
                        cantidad--;
                    }

                });
            });

            this.partitions = [p1];

            const tieneSegunda = pesos.some(
                kg => marcas.some(m => p2[m][kg] > 0)
            );

            if (tieneSegunda) {
                this.partitions.push(p2);
                this.model.partition = 2;
            } else {
                this.model.partition = 1;
            }

            const totalesPorParticion = this.partitions.map(particion => {
                const total = {};
                marcas.forEach(marca => {
                    total[marca] = {};
                    pesos.forEach(kg => {
                        total[marca][kg] = particion[marca][kg];
                    });
                });
                return total;
            });
            console.log(totalesPorParticion);
        },

        formController: function (url, event) {

            var target = $(event.target);
            var fd = new FormData(event.target);

            fd.append('partitions', JSON.stringify(this.partitions));

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
