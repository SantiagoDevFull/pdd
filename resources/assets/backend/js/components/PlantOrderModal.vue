<template>
    <!--begin::Modal-->
    <div class="modal fade" id="modal-order" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
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
                                    <td class="vertical-cell" rowspan="2">Vacios</td>
                                    <td class="bg-danger text-white">Planta</td>
                                    <td><input type="text" name="planta[5]" v-model="model.planta[5]" readonly
                                            class="form-control" @focus="$parent.clearErrorMsg($event)">
                                        <div id="planta_5-error" class="error invalid-feedback"></div>
                                    </td>
                                    <td><input type="text" name="planta[10]" v-model="model.planta[10]" readonly
                                            class="form-control" @focus="$parent.clearErrorMsg($event)">
                                        <div id="planta_10-error" class="error invalid-feedback"></div>
                                    </td>
                                    <td><input type="text" name="planta[15]" v-model="model.planta[15]" readonly
                                            class="form-control" @focus="$parent.clearErrorMsg($event)">
                                        <div id="planta_15-error" class="error invalid-feedback"></div>
                                    </td>
                                    <td><input type="text" name="planta[45]" v-model="model.planta[45]" readonly
                                            class="form-control" @focus="$parent.clearErrorMsg($event)">
                                        <div id="planta_45-error" class="error invalid-feedback"></div>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="bg-danger text-white">Ajenos</td>
                                    <td><input type="text" name="ajenos[5]" v-model="model.ajenos[5]" readonly
                                            class="form-control" @focus="$parent.clearErrorMsg($event)">
                                        <div id="ajenos_5-error" class="error invalid-feedback"></div>
                                    </td>
                                    <td><input type="text" name="ajenos[10]" v-model="model.ajenos[10]" readonly
                                            class="form-control" @focus="$parent.clearErrorMsg($event)">
                                        <div id="ajenos_10-error" class="error invalid-feedback"></div>
                                    </td>
                                    <td><input type="text" name="ajenos[15]" v-model="model.ajenos[15]" readonly
                                            class="form-control" @focus="$parent.clearErrorMsg($event)">
                                        <div id="ajenos_15-error" class="error invalid-feedback"></div>
                                    </td>
                                    <td><input type="text" name="ajenos[45]" v-model="model.ajenos[45]" readonly
                                            class="form-control" @focus="$parent.clearErrorMsg($event)">
                                        <div id="ajenos_45-error" class="error invalid-feedback"></div>
                                    </td>
                                </tr>


                                <tr>
                                    <td class="vertical-cell" rowspan="5">Llenos</td>
                                    <td class="bg-primary text-white">Rinde Gas</td>
                                    <td><input type="text" name="rinde_gas[5]" v-model="model.rinde_gas[5]" readonly
                                            class="form-control" @focus="$parent.clearErrorMsg($event)">
                                        <div id="rinde_gas_5-error" class="error invalid-feedback"></div>
                                    </td>
                                    <td><input type="text" name="rinde_gas[10]" v-model="model.rinde_gas[10]" readonly
                                            class="form-control" @focus="$parent.clearErrorMsg($event)">
                                        <div id="rinde_gas_10-error" class="error invalid-feedback"></div>
                                    </td>
                                    <td><input type="text" name="rinde_gas[15]" v-model="model.rinde_gas[15]" readonly
                                            class="form-control" @focus="$parent.clearErrorMsg($event)">
                                        <div id="rinde_gas_15-error" class="error invalid-feedback"></div>
                                    </td>
                                    <td><input type="text" name="rinde_gas[45]" v-model="model.rinde_gas[45]" readonly
                                            class="form-control" @focus="$parent.clearErrorMsg($event)">
                                        <div id="rinde_gas_45-error" class="error invalid-feedback"></div>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="bg-primary text-white">Premium Gas</td>
                                    <td><input type="text" name="premium_gas[5]" v-model="model.premium_gas[5]" readonly
                                            class="form-control" @focus="$parent.clearErrorMsg($event)">
                                        <div id="premium_gas_5-error" class="error invalid-feedback"></div>
                                    </td>
                                    <td><input type="text" name="premium_gas[10]" v-model="model.premium_gas[10]"
                                            readonly class="form-control" @focus="$parent.clearErrorMsg($event)">
                                        <div id="premium_gas_10-error" class="error invalid-feedback"></div>
                                    </td>
                                    <td><input type="text" name="premium_gas[15]" v-model="model.premium_gas[15]"
                                            readonly class="form-control" @focus="$parent.clearErrorMsg($event)">
                                        <div id="premium_gas_15-error" class="error invalid-feedback"></div>
                                    </td>
                                    <td><input type="text" name="premium_gas[45]" v-model="model.premium_gas[45]"
                                            readonly class="form-control" @focus="$parent.clearErrorMsg($event)">
                                        <div id="premium_gas_45-error" class="error invalid-feedback"></div>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="bg-primary text-white">El Gas</td>
                                    <td><input type="text" name="el_gas[5]" v-model="model.el_gas[5]" readonly
                                            class="form-control" @focus="$parent.clearErrorMsg($event)">
                                        <div id="el_gas_5-error" class="error invalid-feedback"></div>
                                    </td>
                                    <td><input type="text" name="el_gas[10]" v-model="model.el_gas[10]" readonly
                                            class="form-control" @focus="$parent.clearErrorMsg($event)">
                                        <div id="el_gas_10-error" class="error invalid-feedback"></div>
                                    </td>
                                    <td><input type="text" name="el_gas[15]" v-model="model.el_gas[15]" readonly
                                            class="form-control" @focus="$parent.clearErrorMsg($event)">
                                        <div id="el_gas_15-error" class="error invalid-feedback"></div>
                                    </td>
                                    <td><input type="text" name="el_gas[45]" v-model="model.el_gas[45]" readonly
                                            class="form-control" @focus="$parent.clearErrorMsg($event)">
                                        <div id="el_gas_45-error" class="error invalid-feedback"></div>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="bg-primary text-white">Plus Gas</td>
                                    <td><input type="text" name="plus_gas[5]" v-model="model.plus_gas[5]" readonly
                                            class="form-control" @focus="$parent.clearErrorMsg($event)">
                                        <div id="plus_gas_5-error" class="error invalid-feedback"></div>
                                    </td>
                                    <td><input type="text" name="plus_gas[10]" v-model="model.plus_gas[10]" readonly
                                            class="form-control" @focus="$parent.clearErrorMsg($event)">
                                        <div id="plus_gas_10-error" class="error invalid-feedback"></div>
                                    </td>
                                    <td><input type="text" name="plus_gas[15]" v-model="model.plus_gas[15]" readonly
                                            class="form-control" @focus="$parent.clearErrorMsg($event)">
                                        <div id="plus_gas_15-error" class="error invalid-feedback"></div>
                                    </td>
                                    <td><input type="text" name="plus_gas[45]" v-model="model.plus_gas[45]" readonly
                                            class="form-control" @focus="$parent.clearErrorMsg($event)">
                                        <div id="plus_gas_45-error" class="error invalid-feedback"></div>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="bg-primary text-white">Maqui Gas</td>
                                    <td><input type="text" name="maqui_gas[5]" v-model="model.maqui_gas[5]" readonly
                                            class="form-control" @focus="$parent.clearErrorMsg($event)">
                                        <div id="maqui_gas_5-error" class="error invalid-feedback"></div>
                                    </td>
                                    <td><input type="text" name="maqui_gas[10]" v-model="model.maqui_gas[10]" readonly
                                            class="form-control" @focus="$parent.clearErrorMsg($event)">
                                        <div id="maqui_gas_10-error" class="error invalid-feedback"></div>
                                    </td>
                                    <td><input type="text" name="maqui_gas[15]" v-model="model.maqui_gas[15]" readonly
                                            class="form-control" @focus="$parent.clearErrorMsg($event)">
                                        <div id="maqui_gas_15-error" class="error invalid-feedback"></div>
                                    </td>
                                    <td><input type="text" name="maqui_gas[45]" v-model="model.maqui_gas[45]" readonly
                                            class="form-control" @focus="$parent.clearErrorMsg($event)">
                                        <div id="maqui_gas_45-error" class="error invalid-feedback"></div>
                                    </td>
                                </tr>
                            </tbody>
                            <tfoot>

                                <tr class="bg-success">
                                    <td colspan="2" class="text-center text-white">Cambios</td>
                                    <td><input type="text" name="cambios[5]" v-model="model.cambios[5]" readonly
                                            class="form-control" @focus="$parent.clearErrorMsg($event)">
                                        <div id="cambios_5-error" class="error invalid-feedback"></div>
                                    </td>
                                    <td><input type="text" name="cambios[10]" v-model="model.cambios[10]" readonly
                                            class="form-control" @focus="$parent.clearErrorMsg($event)">
                                        <div id="cambios_10-error" class="error invalid-feedback"></div>
                                    </td>
                                    <td><input type="text" name="cambios[15]" v-model="model.cambios[15]" readonly
                                            class="form-control" @focus="$parent.clearErrorMsg($event)">
                                        <div id="cambios_15-error" class="error invalid-feedback"></div>
                                    </td>
                                    <td><input type="text" name="cambios[45]" v-model="model.cambios[45]" readonly
                                            class="form-control" @focus="$parent.clearErrorMsg($event)">
                                        <div id="cambios_45-error" class="error invalid-feedback"></div>
                                    </td>
                                </tr>

                                <tr class="bg-success">
                                    <td colspan="2" class="text-center text-white">Rechazados</td>
                                    <td><input type="text" name="rechazados[5]" v-model="model.rechazados[5]" readonly
                                            class="form-control" @focus="$parent.clearErrorMsg($event)">
                                        <div id="rechazados_5-error" class="error invalid-feedback"></div>
                                    </td>
                                    <td><input type="text" name="rechazados[10]" v-model="model.rechazados[10]" readonly
                                            class="form-control" @focus="$parent.clearErrorMsg($event)">
                                        <div id="rechazados_10-error" class="error invalid-feedback"></div>
                                    </td>
                                    <td><input type="text" name="rechazados[15]" v-model="model.rechazados[15]" readonly
                                            class="form-control" @focus="$parent.clearErrorMsg($event)">
                                        <div id="rechazados_15-error" class="error invalid-feedback"></div>
                                    </td>
                                    <td><input type="text" name="rechazados[45]" v-model="model.rechazados[45]" readonly
                                            class="form-control" @focus="$parent.clearErrorMsg($event)">
                                        <div id="rechazados_45-error" class="error invalid-feedback"></div>
                                    </td>
                                </tr>

                                <tr class="bg-success">
                                    <td colspan="2" class="text-center text-white">Total</td>
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
                                    <td colspan="2" class="text-center text-white bg-info">Prestamos</td>
                                    <td class="bg-secondary"><input type="text" name="prestamo[5]" readonly
                                            v-model="model.prestamo[5]" class="form-control"
                                            @focus="$parent.clearErrorMsg($event)"
                                            @input="model.prestamo[5] = model.prestamo[5].replace(/[^0-9]/g, '')">
                                        <div id="prestamo_5-error" class="error invalid-feedback"></div>
                                    </td>
                                    <td class="bg-secondary"><input type="text" name="prestamo[10]" readonly
                                            v-model="model.prestamo[10]" class="form-control"
                                            @focus="$parent.clearErrorMsg($event)"
                                            @input="model.prestamo[10] = model.prestamo[10].replace(/[^0-9]/g, '')">
                                        <div id="prestamo_10-error" class="error invalid-feedback"></div>
                                    </td>
                                    <td class="bg-secondary"><input type="text" name="prestamo[15]" readonly
                                            v-model="model.prestamo[15]" class="form-control"
                                            @focus="$parent.clearErrorMsg($event)"
                                            @input="model.prestamo[15] = model.prestamo[15].replace(/[^0-9]/g, '')">
                                        <div id="prestamo_15-error" class="error invalid-feedback"></div>
                                    </td>
                                    <td class="bg-secondary"><input type="text" name="prestamo[45]" readonly
                                            v-model="model.prestamo[45]" class="form-control"
                                            @focus="$parent.clearErrorMsg($event)"
                                            @input="model.prestamo[45] = model.prestamo[45].replace(/[^0-9]/g, '')">
                                        <div id="prestamo_45-error" class="error invalid-feedback"></div>
                                    </td>
                                </tr>

                                <tr>
                                    <td colspan="2" class="text-center text-white bg-info">Devoluciones</td>
                                    <td class="bg-secondary"><input type="text" name="devolucion[5]" readonly
                                            v-model="model.devolucion[5]" class="form-control"
                                            @focus="$parent.clearErrorMsg($event)"
                                            @input="model.devolucion[5] = model.devolucion[5].replace(/[^0-9]/g, '')">
                                        <div id="devolucion_5-error" class="error invalid-feedback"></div>
                                    </td>
                                    <td class="bg-secondary"><input type="text" name="devolucion[10]" readonly
                                            v-model="model.devolucion[10]" class="form-control"
                                            @focus="$parent.clearErrorMsg($event)"
                                            @input="model.devolucion[10] = model.devolucion[10].replace(/[^0-9]/g, '')">
                                        <div id="devolucion_10-error" class="error invalid-feedback"></div>
                                    </td>
                                    <td class="bg-secondary"><input type="text" name="devolucion[15]" readonly
                                            v-model="model.devolucion[15]" class="form-control"
                                            @focus="$parent.clearErrorMsg($event)"
                                            @input="model.devolucion[15] = model.devolucion[15].replace(/[^0-9]/g, '')">
                                        <div id="devolucion_15-error" class="error invalid-feedback"></div>
                                    </td>
                                    <td class="bg-secondary"><input type="text" name="devolucion[45]" readonly
                                            v-model="model.devolucion[45]" class="form-control"
                                            @focus="$parent.clearErrorMsg($event)"
                                            @input="model.devolucion[45] = model.devolucion[45].replace(/[^0-9]/g, '')">
                                        <div id="devolucion_45-error" class="error invalid-feedback"></div>
                                    </td>
                                </tr>

                                <tr>
                                    <td colspan="2" class="text-center text-white bg-info">CU</td>
                                    <td class="bg-secondary"><input type="text" name="venta[5]" v-model="model.venta[5]"
                                            readonly class="form-control" @focus="$parent.clearErrorMsg($event)"
                                            @input="model.venta[5] = model.venta[5].replace(/[^0-9]/g, '')">
                                        <div id="venta_5-error" class="error invalid-feedback"></div>
                                    </td>
                                    <td class="bg-secondary"><input type="text" name="venta[10]" readonly
                                            v-model="model.venta[10]" class="form-control"
                                            @focus="$parent.clearErrorMsg($event)"
                                            @input="model.venta[10] = model.venta[10].replace(/[^0-9]/g, '')">
                                        <div id="venta_10-error" class="error invalid-feedback"></div>
                                    </td>
                                    <td class="bg-secondary"><input type="text" name="venta[15]" readonly
                                            v-model="model.venta[15]" class="form-control"
                                            @focus="$parent.clearErrorMsg($event)"
                                            @input="model.venta[15] = model.venta[15].replace(/[^0-9]/g, '')">
                                        <div id="venta_15-error" class="error invalid-feedback"></div>
                                    </td>
                                    <td class="bg-secondary"><input type="text" name="venta[45]" readonly
                                            v-model="model.venta[45]" class="form-control"
                                            @focus="$parent.clearErrorMsg($event)"
                                            @input="model.venta[45] = model.venta[45].replace(/[^0-9]/g, '')">
                                        <div id="venta_45-error" class="error invalid-feedback"></div>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>

                        <div class="col-lg-4">
                            <div class="form-group">
                                <label class="form-control-label">Burros:</label>
                                <input type="text" class="form-control" name="burros" id="burros" v-model="model.burros"
                                    readonly @input="model.burros = model.burros.replace(/[^0-9]/g, '')"
                                    @focus="$parent.clearErrorMsg($event)">
                                <div id="burros-error" class="error invalid-feedback"></div>
                            </div>
                        </div>

                        <div class="col-lg-12">
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                    Cerrar
                                </button>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--end::Modal-->
</template>

<script>
import EventBus from '../event-bus';

export default {
    props: {


    },
    data() {
        return {
            model: {
                planta: { 5: '0', 10: '0', 15: '0', 45: '0' },
                ajenos: { 5: '0', 10: '0', 15: '0', 45: '0' },

                rinde_gas: { 5: '0', 10: '0', 15: '0', 45: '0' },
                premium_gas: { 5: '0', 10: '0', 15: '0', 45: '0' },
                el_gas: { 5: '0', 10: '0', 15: '0', 45: '0' },
                plus_gas: { 5: '0', 10: '0', 15: '0', 45: '0' },
                maqui_gas: { 5: '0', 10: '0', 15: '0', 45: '0' },

                cambios: { 5: '0', 10: '0', 15: '0', 45: '0' },
                rechazados: { 5: '0', 10: '0', 15: '0', 45: '0' },
                total: { 5: '0', 10: '0', 15: '0', 45: '0' },

                prestamo: { 5: '0', 10: '0', 15: '0', 45: '0' },
                devolucion: { 5: '0', 10: '0', 15: '0', 45: '0' },
                venta: { 5: '0', 10: '0', 15: '0', 45: '0' },

                burros: '',
            },
        }
    },
    watch: {

    },
    computed: {

    },
    created() {
        EventBus.$on('view_data', function (result) {
            Object.assign(this.model, result);
        }.bind(this));

        EventBus.$on('view_modal', function () {
            $('#modal-order').modal('show');
        }.bind(this));
    },
    mounted() {

    },
    methods: {
        getTotal(size) {
            let sum = 0;
            const fields = ['planta', 'ajenos', 'rinde_gas', 'premium_gas', 'el_gas', 'plus_gas', 'maqui_gas', 'cambios'];
            fields.forEach(f => {
                sum += parseInt(this.model[f][size] || 0);
            });
            return sum;
        },
    }
};
</script>