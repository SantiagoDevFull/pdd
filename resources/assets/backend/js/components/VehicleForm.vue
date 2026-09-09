<template>
    <!--begin::Portlet-->
    <div class="kt-portlet">
        <div class="kt-portlet__head">
            <div class="kt-portlet__head-label">
                <h3 class="kt-portlet__head-title">
                    Crear Vehículo
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
                    <div class="col-lg-1">
                        <div class="form-group">
                            <label class="form-control-label">Placa:</label>
                            <input type="text" class="form-control" name="plate" id="plate" placeholder="ABC-ABC" :maxlength="7"
                                v-model="model.plate" @input="formato" @focus="$parent.clearErrorMsg($event)">
                            <div id="plate-error" class="error invalid-feedback"></div>
                        </div>
                    </div>
                    <div class="col-lg-1">
                        <div class="form-group">
                            <label class="form-control-label">Capacidad:</label>
                            <input type="number" step="0.01" class="form-control" name="capacity" id="capacity" placeholder="0"
                                v-model="model.capacity" @focus="$parent.clearErrorMsg($event)">
                            <div id="capacity-error" class="error invalid-feedback"></div>
                        </div>
                    </div>
                    <div class="col-lg-2">
                        <div class="form-group">
                            <label class="form-control-label">Licencia:</label>
                            <input type="text" class="form-control" name="license" id="license" v-model="model.license"
                                @focus="$parent.clearErrorMsg($event)">
                            <div id="license-error" class="error invalid-feedback"></div>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="form-group">
                            <label class="form-control-label">Transportista::</label>
                            <select class="form-control" name="transportist_id" id="transportist_id"
                                v-model="model.transportist_id" @focus="$parent.clearErrorMsg($event)">
                                <option value="" selected disabled>Seleccionar</option>
                                <option v-for="company in companies" :value="company.id" v-bind:key="company.id">{{
            company.name }}</option>
                            </select>
                            <div id="transportist_id-error" class="error invalid-feedback"></div>
                        </div>
                    </div>
                    <div class="col-lg-2">
                        <div class="form-group">
                            <label class="form-control-label">Chasis:</label>
                            <input type="text" class="form-control" name="chasis" id="chasis" v-model="model.chasis"
                                @focus="$parent.clearErrorMsg($event)">
                            <div id="chasis-error" class="error invalid-feedback"></div>
                        </div>
                    </div>
                    <div class="col-lg-2">
                        <div class="form-group">
                            <label class="form-control-label">Marca:</label>
                            <select class="form-control" name="brand_id" id="brand_id" v-model="model.brand_id"
                                @focus="$parent.clearErrorMsg($event)">
                                <option value="" selected disabled>Seleccionar</option>
                                <option v-for="brand in brands" :value="brand.id" v-bind:key="brand.id">{{
            brand.description }}</option>
                            </select>
                            <div id="brand_id-error" class="error invalid-feedback"></div>
                        </div>
                    </div>
                    <div class="col-lg-2">
                        <div class="form-group">
                            <label class="form-control-label">Tipo de Combustible:</label>
                            <select class="form-control" name="fuel_type_id" id="fuel_type_id"
                                v-model="model.fuel_type_id" @focus="$parent.clearErrorMsg($event)">
                                <option value="" selected disabled>Seleccionar</option>
                                <option v-for="fuel_type in fuel_types" :value="fuel_type.id" v-bind:key="fuel_type.id">
                                    {{ fuel_type.description }}</option>
                            </select>
                            <div id="fuel_type_id-error" class="error invalid-feedback"></div>
                        </div>
                    </div>
                    <div class="col-lg-2">
                        <div class="form-group">
                            <label class="form-control-label">Tipo de Carrocería:</label>
                            <select class="form-control" name="body_type_id" id="body_type_id"
                                v-model="model.body_type_id" @focus="$parent.clearErrorMsg($event)">
                                <option value="" selected disabled>Seleccionar</option>
                                <option v-for="body_type in body_types" :value="body_type.id" v-bind:key="body_type.id">
                                    {{ body_type.description }}</option>
                            </select>
                            <div id="body_type_id-error" class="error invalid-feedback"></div>
                        </div>
                    </div>
                    <div class="col-lg-2">
                        <div class="form-group">
                            <label class="form-control-label">Modelo:</label>
                            <select class="form-control" name="modelo_id" id="modelo_id" v-model="model.modelo_id"
                                @focus="$parent.clearErrorMsg($event)">
                                <option value="" selected disabled>Seleccionar</option>
                                <option v-for="model in modelos" :value="model.id" v-bind:key="model.id">{{
            model.description }}</option>
                            </select>
                            <div id="modelo_id-error" class="error invalid-feedback"></div>
                        </div>
                    </div>
                    <div class="col-lg-2">
                        <div class="form-group">
                            <label class="form-control-label">Color:</label>
                            <input type="text" class="form-control" name="color" id="color" v-model="model.color"
                                @focus="$parent.clearErrorMsg($event)">
                            <div id="color-error" class="error invalid-feedback"></div>
                        </div>
                    </div>
                    <div class="col-lg-2">
                        <div class="form-group">
                            <label class="form-control-label">Motor:</label>
                            <input type="text" class="form-control" name="motor" id="motor" v-model="model.motor"
                                @focus="$parent.clearErrorMsg($event)">
                            <div id="motor-error" class="error invalid-feedback"></div>
                        </div>
                    </div>
                    <div class="col-lg-2">
                        <div class="form-group">
                            <label class="form-control-label">N° de Llantas:</label>
                            <input type="number" placeholder="0" class="form-control" name="nro_llantas"
                                id="nro_llantas" v-model="model.nro_llantas" @focus="$parent.clearErrorMsg($event)">
                            <div id="nro_llantas-error" class="error invalid-feedback"></div>
                        </div>
                    </div>
                    <div class="col-lg-2">
                        <div class="form-group">
                            <label class="form-control-label">Año de Modelo:</label>
                            <input type="number" placeholder="0" class="form-control" name="year_model" id="year_model"
                                v-model="model.year_model" @focus="$parent.clearErrorMsg($event)">
                            <div id="year_model-error" class="error invalid-feedback"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="kt-portlet__foot">
                <div class="kt-form__actions">
                    <div class="row">
                        <div class="col-lg-6">
                            <button type="submit" class="btn btn-primary">Crear</button>
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
export default {
    props: {
        companies: {
            type: Array,
            default: ''
        },
        modelos: {
            type: Array,
            default: ''
        },
        brands: {
            type: Array,
            default: ''
        },
        fuel_types: {
            type: Array,
            default: ''
        },
        body_types: {
            type: Array,
            default: ''
        },
        url: {
            type: String,
            default: ''
        },
    },
    data() {
        return {
            model: {
                company_id: '',
                plate: '',
                capacity: '',
                license: '',
                transportist_id: '',
                chasis: '',
                brand_id: '',
                fuel_type_id: '',
                body_type_id: '',
                modelo_id: '',
                color: '',
                motor: '',
                nro_llantas: '',
                year_model: '',
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
        formato() {
            this.model.plate = this.model.plate.trim();
            
            if (this.model.plate.length == 3) {

                this.model.plate = this.model.plate.substring(0, 3) + '-' + this.model.plate.substring(4, 3);
            }

        },
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
                this.$parent.alertMsg(response.data);

                this.model.company_id = '',
                    this.model.plate = '',
                    this.model.capacity = '',
                    this.model.license = '',
                    this.model.transportist_id = '',
                    this.model.chasis = '',
                    this.model.brand_id = '',
                    this.model.fuel_type_id = '',
                    this.model.body_type_id = '',
                    this.model.modelo_id = '',
                    this.model.color = '',
                    this.model.motor = '',
                    this.model.nro_llantas = '',
                    this.model.year_model = '',

                    EventBus.$emit('refresh_table');
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
    }
};
</script>