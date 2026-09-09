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
                            <label class="form-control-label">PEDIDO &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; PLACA
                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; FECHA VENTA</label>
                            <select class="form-control" name="reception_plant_id" id="reception_plant_id"
                                v-model="model.reception_plant_id" @focus="$parent.clearErrorMsg($event)">
                                <option value="" selected disabled>Seleccionar</option>
                                <option v-for="reception in reception_plants" :value="reception.id"
                                    v-bind:key="reception.id">#{{
                                        reception.pedido }} &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; {{ reception.plate }}
                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; {{ reception.traslate_date }}</option>
                            </select>
                            <div id="reception_plant_id-error" class="error invalid-feedback"></div>
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
                                <tr class="bg-success">
                                    <td colspan="2" class="text-center text-white">Cambios</td>
                                    <td><input type="text" name="cambios[5]" v-model="model.cambios[5]" readonly
                                            class="form-control" @focus="$parent.clearErrorMsg($event)"
                                            @input="model.cambios[5] = model.cambios[5].replace(/[^0-9]/g, '')">
                                        <div id="cambios_5-error" class="error invalid-feedback"></div>
                                    </td>
                                    <td><input type="text" name="cambios[10]" v-model="model.cambios[10]" readonly
                                            class="form-control" @focus="$parent.clearErrorMsg($event)"
                                            @input="model.cambios[10] = model.cambios[10].replace(/[^0-9]/g, '')">
                                        <div id="cambios_10-error" class="error invalid-feedback"></div>
                                    </td>
                                    <td><input type="text" name="cambios[15]" v-model="model.cambios[15]" readonly
                                            class="form-control" @focus="$parent.clearErrorMsg($event)"
                                            @input="model.cambios[15] = model.cambios[15].replace(/[^0-9]/g, '')">
                                        <div id="cambios_15-error" class="error invalid-feedback"></div>
                                    </td>
                                    <td><input type="text" name="cambios[45]" v-model="model.cambios[45]" readonly
                                            class="form-control" @focus="$parent.clearErrorMsg($event)"
                                            @input="model.cambios[45] = model.cambios[45].replace(/[^0-9]/g, '')">
                                        <div id="cambios_45-error" class="error invalid-feedback"></div>
                                    </td>
                                </tr>
                            </tbody>

                        </table>

                        <table class="table table-bordered">
                            <thead class="bg-warning">
                                <tr>
                                    <th class="text-center">A(pin)</th>
                                    <th class="text-center">B(o-rin)</th>
                                    <th class="text-center">C(valvula)</th>
                                    <th class="text-center">D(bajo peso)</th>
                                    <th class="text-center">E(carachoso)</th>
                                    <th class="text-center">F(hueco)</th>
                                    <th class="text-center">G(asa-base)</th>
                                    <th class="text-center">Total</th>
                                    <th class="text-center">Repintado</th>
                                    <th class="text-center">Obs</th>
                                </tr>
                            </thead>
                            <tbody class="bg-secondary">
                                <tr>
                                    <td><input type="text" name="a" v-model="model.a" class="form-control"
                                            @focus="$parent.clearErrorMsg($event)"
                                            @input="model.a = model.a.replace(/[^0-9]/g, '')">
                                        <div id="a-error" class="error invalid-feedback"></div>
                                    </td>
                                    <td><input type="text" name="b" v-model="model.b" class="form-control"
                                            @focus="$parent.clearErrorMsg($event)"
                                            @input="model.b = model.b.replace(/[^0-9]/g, '')">
                                        <div id="b-error" class="error invalid-feedback"></div>
                                    </td>
                                    <td><input type="text" name="c" v-model="model.c" class="form-control"
                                            @focus="$parent.clearErrorMsg($event)"
                                            @input="model.c = model.c.replace(/[^0-9]/g, '')">
                                        <div id="c-error" class="error invalid-feedback"></div>
                                    </td>
                                    <td><input type="text" name="d" v-model="model.d" class="form-control"
                                            @focus="$parent.clearErrorMsg($event)"
                                            @input="model.d = model.d.replace(/[^0-9]/g, '')">
                                        <div id="d-error" class="error invalid-feedback"></div>
                                    </td>
                                    <td><input type="text" name="e" v-model="model.e" class="form-control"
                                            @focus="$parent.clearErrorMsg($event)"
                                            @input="model.e = model.e.replace(/[^0-9]/g, '')">
                                        <div id="e-error" class="error invalid-feedback"></div>
                                    </td>
                                    <td><input type="text" name="f" v-model="model.f" class="form-control"
                                            @focus="$parent.clearErrorMsg($event)"
                                            @input="model.f = model.f.replace(/[^0-9]/g, '')">
                                        <div id="f-error" class="error invalid-feedback"></div>
                                    </td>
                                    <td><input type="text" name="g" v-model="model.g" class="form-control"
                                            @focus="$parent.clearErrorMsg($event)"
                                            @input="model.g = model.g.replace(/[^0-9]/g, '')">
                                        <div id="g-error" class="error invalid-feedback"></div>
                                    </td>
                                    <td><input type="text" name="total" class="form-control" readonly
                                            @focus="$parent.clearErrorMsg($event)" :value="totalCalc">
                                        <div id="total-error" class="error invalid-feedback"></div>
                                    </td>
                                    <td><input type="text" name="repintado" v-model="model.repintado"
                                            class="form-control" @focus="$parent.clearErrorMsg($event)"
                                            @input="model.repintado = model.repintado.replace(/[^0-9]/g, '')">
                                        <div id="repintado-error" class="error invalid-feedback"></div>
                                    </td>
                                    <td><input type="text" name="obs" v-model="model.obs" class="form-control"
                                            @focus="$parent.clearErrorMsg($event)">
                                        <div id="obs-error" class="error invalid-feedback"></div>
                                    </td>

                                </tr>
                            </tbody>

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
import Swal from 'sweetalert2';


export default {
    props: {
        url: { type: String, default: '' },
        url_get_reception_plants: { type: String, default: '' },
        url_get_reception_plant: { type: String, default: '' },
        client_routes: { type: Array, default: '' },
    },
    data() {
        return {
            model: {
                route_id: '',
                reception_plant_id: '',
                cambios: { 5: '0', 10: '0', 15: '0', 45: '0' },

                a: '0',
                b: '0',
                c: '0',
                d: '0',
                e: '0',
                f: '0',
                g: '0',
                total: '0',
                repintado: '0',
                obs: '0',
            },
            reception_plants: []
        }
    },
    computed: {
        totalCalc() {
            return (
                Number(this.model.a || 0) +
                Number(this.model.b || 0) +
                Number(this.model.c || 0) +
                Number(this.model.d || 0) +
                Number(this.model.e || 0) +
                Number(this.model.f || 0) +
                Number(this.model.g || 0)
            );
        }
    },
    watch: {
        'model.route_id': function (val) {

            EventBus.$emit('loading', true);
            axios.post(this.url_get_reception_plants, {
                route_id: val
            }).then(response => {
                this.model.reception_plant_id = '';
                this.reception_plants = [];
                this.reception_plants = response.data;
                EventBus.$emit('loading', false);
            }).catch(error => {
                console.log(error);
                console.log(error.response);
                EventBus.$emit('loading', false);
            });
        },
        'model.reception_plant_id': function (val) {

            if (val != '') {
                EventBus.$emit('loading', true);
                axios.post(this.url_get_reception_plant, {
                    reception_plant_id: val
                }).then(response => {

                    Object.assign(this.model, response.data);
                    this.model.partition = '';

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

    },
    methods: {
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
