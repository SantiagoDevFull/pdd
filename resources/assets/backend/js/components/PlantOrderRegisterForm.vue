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
                            <label class="form-control-label">PEDIDO &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; :::
                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; FECHA DE VENTA</label>
                            <select class="form-control" name="plant_order_id" id="plant_order_id"
                                v-model="model.plant_order_id" @focus="$parent.clearErrorMsg($event)">
                                <option value="" selected disabled>Seleccionar</option>
                                <option v-for="plant in plant_orders" :value="plant.id" v-bind:key="plant.id">{{
                                    plant.pedido }} &nbsp;&nbsp;&nbsp;&nbsp; ::: &nbsp;&nbsp;&nbsp;&nbsp;{{
                                        plant.traslate_date }}</option>
                            </select>
                            <div id="plant_order_id-error" class="error invalid-feedback"></div>
                        </div>
                    </div>

                </div>

                <div class="col-12" v-if="this.button">
                    <a class="btn btn-danger text-white" @click.prevent="ViewReception()">Ver Recepcion</a>
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
                                    <td class="vertical-cell" rowspan="5">Lleno</td>
                                    <td class="bg-primary text-white">Rinde Gas</td>
                                    <td><input type="text" name="rinde_gas[5]" v-model="model.rinde_gas[5]"
                                            class="form-control" @focus="$parent.clearErrorMsg($event)"
                                            @input="model.rinde_gas[5] = model.rinde_gas[5].replace(/[^0-9]/g, '')">
                                        <div id="rinde_gas_5-error" class="error invalid-feedback"></div>
                                    </td>
                                    <td><input type="text" name="rinde_gas[10]" v-model="model.rinde_gas[10]"
                                            class="form-control" @focus="$parent.clearErrorMsg($event)"
                                            @input="model.rinde_gas[10] = model.rinde_gas[10].replace(/[^0-9]/g, '')">
                                        <div id="rinde_gas_10-error" class="error invalid-feedback"></div>
                                    </td>
                                    <td><input type="text" name="rinde_gas[15]" v-model="model.rinde_gas[15]"
                                            class="form-control" @focus="$parent.clearErrorMsg($event)"
                                            @input="model.rinde_gas[15] = model.rinde_gas[15].replace(/[^0-9]/g, '')">
                                        <div id="rinde_gas_15-error" class="error invalid-feedback"></div>
                                    </td>
                                    <td><input type="text" name="rinde_gas[45]" v-model="model.rinde_gas[45]"
                                            class="form-control" @focus="$parent.clearErrorMsg($event)"
                                            @input="model.rinde_gas[45] = model.rinde_gas[45].replace(/[^0-9]/g, '')">
                                        <div id="rinde_gas_45-error" class="error invalid-feedback"></div>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="bg-primary text-white">Premium Gas</td>
                                    <td><input type="text" name="premium_gas[5]" v-model="model.premium_gas[5]"
                                            class="form-control" @focus="$parent.clearErrorMsg($event)"
                                            @input="model.premium_gas[5] = model.premium_gas[5].replace(/[^0-9]/g, '')">
                                        <div id="premium_gas_5-error" class="error invalid-feedback"></div>
                                    </td>
                                    <td><input type="text" name="premium_gas[10]" v-model="model.premium_gas[10]"
                                            class="form-control" @focus="$parent.clearErrorMsg($event)"
                                            @input="model.premium_gas[10] = model.premium_gas[10].replace(/[^0-9]/g, '')">
                                        <div id="premium_gas_10-error" class="error invalid-feedback"></div>
                                    </td>
                                    <td><input type="text" name="premium_gas[15]" v-model="model.premium_gas[15]"
                                            class="form-control" @focus="$parent.clearErrorMsg($event)"
                                            @input="model.premium_gas[15] = model.premium_gas[15].replace(/[^0-9]/g, '')">
                                        <div id="premium_gas_15-error" class="error invalid-feedback"></div>
                                    </td>
                                    <td><input type="text" name="premium_gas[45]" v-model="model.premium_gas[45]"
                                            class="form-control" @focus="$parent.clearErrorMsg($event)"
                                            @input="model.premium_gas[45] = model.premium_gas[45].replace(/[^0-9]/g, '')">
                                        <div id="premium_gas_45-error" class="error invalid-feedback"></div>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="bg-primary text-white">El Gas</td>
                                    <td><input type="text" name="el_gas[5]" v-model="model.el_gas[5]"
                                            class="form-control" @focus="$parent.clearErrorMsg($event)"
                                            @input="model.el_gas[5] = model.el_gas[5].replace(/[^0-9]/g, '')">
                                        <div id="el_gas_5-error" class="error invalid-feedback"></div>
                                    </td>
                                    <td><input type="text" name="el_gas[10]" v-model="model.el_gas[10]"
                                            class="form-control" @focus="$parent.clearErrorMsg($event)"
                                            @input="model.el_gas[10] = model.el_gas[10].replace(/[^0-9]/g, '')">
                                        <div id="el_gas_10-error" class="error invalid-feedback"></div>
                                    </td>
                                    <td><input type="text" name="el_gas[15]" v-model="model.el_gas[15]"
                                            class="form-control" @focus="$parent.clearErrorMsg($event)"
                                            @input="model.el_gas[15] = model.el_gas[15].replace(/[^0-9]/g, '')">
                                        <div id="el_gas_15-error" class="error invalid-feedback"></div>
                                    </td>
                                    <td><input type="text" name="el_gas[45]" v-model="model.el_gas[45]"
                                            class="form-control" @focus="$parent.clearErrorMsg($event)"
                                            @input="model.el_gas[45] = model.el_gas[45].replace(/[^0-9]/g, '')">
                                        <div id="el_gas_45-error" class="error invalid-feedback"></div>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="bg-primary text-white">Plus Gas</td>
                                    <td><input type="text" name="plus_gas[5]" v-model="model.plus_gas[5]"
                                            class="form-control" @focus="$parent.clearErrorMsg($event)"
                                            @input="model.plus_gas[5] = model.plus_gas[5].replace(/[^0-9]/g, '')">
                                        <div id="plus_gas_5-error" class="error invalid-feedback"></div>
                                    </td>
                                    <td><input type="text" name="plus_gas[10]" v-model="model.plus_gas[10]"
                                            class="form-control" @focus="$parent.clearErrorMsg($event)"
                                            @input="model.plus_gas[10] = model.plus_gas[10].replace(/[^0-9]/g, '')">
                                        <div id="plus_gas_10-error" class="error invalid-feedback"></div>
                                    </td>
                                    <td><input type="text" name="plus_gas[15]" v-model="model.plus_gas[15]"
                                            class="form-control" @focus="$parent.clearErrorMsg($event)"
                                            @input="model.plus_gas[15] = model.plus_gas[15].replace(/[^0-9]/g, '')">
                                        <div id="plus_gas_15-error" class="error invalid-feedback"></div>
                                    </td>
                                    <td><input type="text" name="plus_gas[45]" v-model="model.plus_gas[45]"
                                            class="form-control" @focus="$parent.clearErrorMsg($event)"
                                            @input="model.plus_gas[45] = model.plus_gas[45].replace(/[^0-9]/g, '')">
                                        <div id="plus_gas_45-error" class="error invalid-feedback"></div>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="bg-primary text-white">Maqui Gas</td>
                                    <td><input type="text" name="maqui_gas[5]" v-model="model.maqui_gas[5]"
                                            class="form-control" @focus="$parent.clearErrorMsg($event)"
                                            @input="model.maqui_gas[5] = model.maqui_gas[5].replace(/[^0-9]/g, '')">
                                        <div id="maqui_gas_5-error" class="error invalid-feedback"></div>
                                    </td>
                                    <td><input type="text" name="maqui_gas[10]" v-model="model.maqui_gas[10]"
                                            class="form-control" @focus="$parent.clearErrorMsg($event)"
                                            @input="model.maqui_gas[10] = model.maqui_gas[10].replace(/[^0-9]/g, '')">
                                        <div id="maqui_gas_10-error" class="error invalid-feedback"></div>
                                    </td>
                                    <td><input type="text" name="maqui_gas[15]" v-model="model.maqui_gas[15]"
                                            class="form-control" @focus="$parent.clearErrorMsg($event)"
                                            @input="model.maqui_gas[15] = model.maqui_gas[15].replace(/[^0-9]/g, '')">
                                        <div id="maqui_gas_15-error" class="error invalid-feedback"></div>
                                    </td>
                                    <td><input type="text" name="maqui_gas[45]" v-model="model.maqui_gas[45]"
                                            class="form-control" @focus="$parent.clearErrorMsg($event)"
                                            @input="model.maqui_gas[45] = model.maqui_gas[45].replace(/[^0-9]/g, '')">
                                        <div id="maqui_gas_45-error" class="error invalid-feedback"></div>
                                    </td>
                                </tr>
                            </tbody>
                            <tfoot class="bg-success">
                                <tr>
                                    <td colspan="2" class="text-center">Total</td>
                                    <td>
                                        <input type="text" name="total[5]" class="form-control" :value="getTotal(5)"
                                            readonly>
                                    </td>
                                    <td>
                                        <input type="text" name="total[10]" class="form-control" :value="getTotal(10)"
                                            readonly>
                                    </td>
                                    <td>
                                        <input type="text" name="total[15]" class="form-control" :value="getTotal(15)"
                                            readonly>
                                    </td>
                                    <td>
                                        <input type="text" name="total[45]" class="form-control" :value="getTotal(45)"
                                            readonly>
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
import Swal from 'sweetalert2';
import EventBus from '../event-bus';
import Datetime from 'vue-datetime';
import 'vue-datetime/dist/vue-datetime.css';

Vue.use(Datetime);

export default {
    props: {
        url: { type: String, default: '' },
        url_get_plant_orders: { type: String, default: '' },
        url_get_plant_order: { type: String, default: '' },
        client_routes: { type: Array, default: '' },

    },
    data() {
        return {
            model: {
                route_id: '',
                plant_order_id: '',
                rinde_gas: { 5: '0', 10: '0', 15: '0', 45: '0' },
                premium_gas: { 5: '0', 10: '0', 15: '0', 45: '0' },
                el_gas: { 5: '0', 10: '0', 15: '0', 45: '0' },
                plus_gas: { 5: '0', 10: '0', 15: '0', 45: '0' },
                maqui_gas: { 5: '0', 10: '0', 15: '0', 45: '0' },
                total: { 5: '0', 10: '0', 15: '0', 45: '0' },
                rechazados: { 5: '0', 10: '0', 15: '0', 45: '0' }
            },
            plant_orders: [],
            button: false
        }
    },
    watch: {
        'model.route_id': function (val) {
            if (val != '') {

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
            }
        },
        'model.plant_order_id': function (val) {

            if (val != '') {
                EventBus.$emit('loading', true);
                axios.post(this.url_get_plant_order, {
                    plant_order_id: val
                }).then(response => {
                    Object.assign(this.model, response.data.obj);

                    if (this.model.route_id == 20) {
                        this.button = true;
                    } else {
                        this.button = false;
                    }

                    EventBus.$emit('view_data', response.data.result);
                    EventBus.$emit('loading', false);
                }).catch(error => {
                    console.log(error);
                    console.log(error.response);
                    EventBus.$emit('loading', false);
                });
            } else {
                this.button = false;
                this.model.traslate_date = '';
                this.model.vehicle_id = '';
            }
        },

    },
    mounted() {

    },
    methods: {
        ViewReception() {
            EventBus.$emit('view_modal');
        },
        getTotal(size) {
            let sum = 0;
            const fields = ['rinde_gas', 'premium_gas', 'el_gas', 'plus_gas', 'maqui_gas'];
            fields.forEach(f => {
                sum += parseInt(this.model[f][size] || 0);
            });
            return sum;
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
