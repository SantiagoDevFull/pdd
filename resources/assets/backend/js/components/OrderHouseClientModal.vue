<template>
    <!--begin::Modal-->
    <div class="modal fade" id="modal-client" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <!--begin::Form-->
                <form class="kt-form" @submit.prevent="formController(url, $event)">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Crear cliente</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        </button>
                    </div>

                    <div class="modal-body">
                        <div class="kt-portlet__body">
                            <div class="row">

                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label class="form-control-label">Compañia:</label>
                                        <select class="form-control" name="company_id" id="company_id"
                                            v-model="model.company_id" @focus="$parent.clearErrorMsg($event)">
                                            <option value="" selected disabled>Seleccionar</option>
                                            <option v-for="company in companies" :value="company.id"
                                                v-bind:key="company.id">{{ company.name }}</option>
                                        </select>
                                        <div id="company_id-error" class="error invalid-feedback"></div>
                                    </div>
                                </div>

                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label class="form-control-label">Tipo de Documento:</label>
                                        <select class="form-control" name="document_type_id" id="document_type_id"
                                            v-model="model.document_type_id" @focus="$parent.clearErrorMsg($event)">
                                            <option value="">Seleccionar</option>
                                            <option v-for="document_type in document_types" :value="document_type.id"
                                                v-bind:key="document_type.id">{{ document_type.name }}</option>
                                        </select>
                                        <div id="document_type_id-error" class="error invalid-feedback"></div>
                                    </div>
                                </div>

                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label class="form-control-label">Documento Cliente:</label>
                                        <input type="text" class="form-control" name="document_number"
                                            id="document_number" placeholder="123456789" v-model="model.document_number"
                                            @focus="$parent.clearErrorMsg($event)">
                                        <div id="document_number-error" class="error invalid-feedback"></div>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label class="form-control-label">Nombre o Razón Social:</label>
                                        <input type="text" class="form-control" name="business_name" id="business_name"
                                            placeholder="Empresa S.A.C." v-model="model.business_name"
                                            @focus="$parent.clearErrorMsg($event)">
                                        <div id="business_name-error" class="error invalid-feedback"></div>
                                    </div>
                                </div>

                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label class="form-control-label">Dirección:</label>
                                        <input type="text" class="form-control" name="address" id="address"
                                            placeholder="Av. Ejemplo 123" v-model="model.address"
                                            @focus="$parent.clearErrorMsg($event)">
                                        <div id="address-error" class="error invalid-feedback"></div>
                                    </div>
                                </div>

                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label class="form-control-label">Referencia:</label>
                                        <input type="text" class="form-control" name="address_reference"
                                            id="address_reference" placeholder="Av. Ejemplo 123"
                                            v-model="model.address_reference" @focus="$parent.clearErrorMsg($event)">
                                        <div id="address_reference-error" class="error invalid-feedback"></div>
                                    </div>
                                </div>

                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label class="form-control-label">Ubigeo:</label>
                                        <select class="form-control kt-select2" name="ubigeo_id" id="ubigeo_id"
                                            v-model="model.ubigeo_id" @focus="$parent.clearErrorMsg($event)">
                                            <option value="">Seleccionar</option>
                                        </select>
                                        <div id="ubigeo_id-error" class="error invalid-feedback"></div>
                                    </div>
                                </div>

                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label class="form-control-label">GPS X:</label>
                                        <input type="text" class="form-control" name="gps_x" id="gps_x"
                                            placeholder="12.18435168" v-model="model.gps_x"
                                            @focus="$parent.clearErrorMsg($event)">
                                        <div id="gps_x-error" class="error invalid-feedback"></div>
                                    </div>
                                </div>

                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label class="form-control-label">GPS Y:</label>
                                        <input type="text" class="form-control" name="gps_y" id="gps_y"
                                            placeholder="12.8754354" v-model="model.gps_y"
                                            @focus="$parent.clearErrorMsg($event)">
                                        <div id="gps_y-error" class="error invalid-feedback"></div>
                                    </div>
                                </div>

                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label class="form-control-label">Celular 1:</label>
                                        <input type="text" class="form-control" name="phone_number_1"
                                            id="phone_number_1" placeholder="987654321" v-model="model.phone_number_1"
                                            @focus="$parent.clearErrorMsg($event)">
                                        <div id="phone_number_1-error" class="error invalid-feedback"></div>
                                    </div>
                                </div>

                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label class="form-control-label">Contacto 1:</label>
                                        <input type="text" class="form-control" name="contact_name_1"
                                            id="contact_name_1" placeholder="Nombre Apellido"
                                            v-model="model.contact_name_1" @focus="$parent.clearErrorMsg($event)">
                                        <div id="contact_name_1-error" class="error invalid-feedback"></div>
                                    </div>
                                </div>

                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label class="form-control-label">Vendedor:</label>
                                        <select class="form-control" name="seller_id" id="seller_id"
                                            v-model="model.seller_id" @focus="$parent.clearErrorMsg($event)">
                                            <option disabled value="">Seleccionar</option>
                                            <option value="OFICINA" selected>Oficina</option>


                                        </select>
                                        <div id="seller_id-error" class="error invalid-feedback"></div>
                                    </div>
                                </div>

                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label class="form-control-label">Unidad:</label>
                                        <select class="form-control" name="business_unit_id" id="business_unit_id"
                                            v-model="model.business_unit_id" @focus="$parent.clearErrorMsg($event)">
                                            <option value="">Seleccionar</option>
                                            <option v-for="business_unit in business_units" :value="business_unit.id"
                                                v-bind:key="business_unit.id">{{ business_unit.name }}</option>
                                        </select>
                                        <div id="business_unit_id-error" class="error invalid-feedback"></div>
                                    </div>
                                </div>

                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label class="form-control-label">Área:</label>
                                        <select class="form-control" name="zone_id" id="zone_id" v-model="model.zone_id"
                                            @focus="$parent.clearErrorMsg($event)">
                                            <option value="" selected disabled>Seleccionar</option>
                                            <option v-for="zone in client_zones" :value="zone.id" v-bind:key="zone.id">
                                                {{ zone.name }}</option>
                                        </select>
                                        <div id="zone_id-error" class="error invalid-feedback"></div>
                                    </div>
                                </div>

                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label class="form-control-label">Canal:</label>
                                        <select class="form-control" name="sector_id" id="sector_id"
                                            v-model="model.sector_id" @focus="$parent.clearErrorMsg($event)">
                                            <option value="" selected disabled>Seleccionar</option>
                                            <option v-for="sector in client_sectors" :value="sector.id"
                                                v-bind:key="sector.id">{{ sector.name }}</option>
                                        </select>
                                        <div id="sector_id-error" class="error invalid-feedback"></div>
                                    </div>
                                </div>

                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label class="form-control-label">Sub Canal:</label>
                                        <select class="form-control" name="channel_id" id="channel_id"
                                            v-model="model.channel_id" @focus="$parent.clearErrorMsg($event)">
                                            <option value="" selected disabled>Seleccionar</option>
                                            <option v-for="channel in client_channels" :value="channel.id"
                                                v-bind:key="channel.id">{{ channel.name }}</option>
                                        </select>
                                        <div id="channel_id-error" class="error invalid-feedback"></div>
                                    </div>
                                </div>

                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label class="form-control-label">Ruta:</label>
                                        <select class="form-control" name="route_id" id="route_id"
                                            v-model="model.route_id" @focus="$parent.clearErrorMsg($event)">
                                            <option value="" selected disabled>Seleccionar</option>
                                            <option v-for="route in client_routes" :value="route.id"
                                                v-bind:key="route.id">{{ route.name }}</option>
                                        </select>
                                        <div id="route_id-error" class="error invalid-feedback"></div>
                                    </div>
                                </div>

                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label class="form-control-label">Negocio:</label>
                                        <select class="form-control" name="client_type" id="client_type"
                                            v-model="model.client_type" @focus="$parent.clearErrorMsg($event)">
                                            <option value="">Seleccionar</option>
                                            <option v-for="client_type in client_types" :value="client_type.id"
                                                v-bind:key="client_type.id">{{ client_type.name }}</option>
                                        </select>
                                        <div id="client_type-error" class="error invalid-feedback"></div>
                                    </div>
                                </div>

                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label class="form-control-label">Sector:</label>
                                        <select class="form-control" name="sector" id="sector" placeholder="Sector"
                                            v-model="model.sector" @focus="$parent.clearErrorMsg($event)">
                                            <option disabled value="">Seleccionar</option>
                                            <option value="S1">S1</option>
                                            <option value="S2">S2</option>
                                            <option value="S3">S3</option>
                                            <option value="S4">S4</option>
                                            <option value="S5">S5</option>
                                            <option value="S6">S6</option>
                                            <option value="S7">S7</option>
                                            <option value="S8">S8</option>
                                            <option value="S9">S9</option>
                                            <option value="S10">S10</option>
                                            <option value="S11">S11</option>
                                            <option value="S12">S12</option>
                                        </select>
                                        <div id="sector-error" class="error invalid-feedback"></div>
                                    </div>
                                </div>

                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label class="form-control-label">Zona:</label>
                                        <select class="form-control" name="zona" id="zona" placeholder="Zona"
                                            v-model="model.zona" @focus="$parent.clearErrorMsg($event)">
                                            <option disabled value="">Seleccionar</option>
                                            <option value="Z1">Z1</option>
                                            <option value="Z2">Z2</option>
                                            <option value="Z3">Z3</option>
                                            <option value="Z4">Z4</option>
                                        </select>
                                        <div id="zona-error" class="error invalid-feedback"></div>
                                    </div>
                                </div>

                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label class="form-control-label">Frecuencia Cantidad:</label>
                                        <input type="text" class="form-control" name="frecuencia" id="frecuencia"
                                            placeholder="Frecuencia" v-model="model.frecuencia"
                                            @focus="$parent.clearErrorMsg($event)">
                                        <div id="frecuencia-error" class="error invalid-feedback"></div>
                                    </div>
                                </div>

                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label class="form-control-label">Frecuencia Tiempo:</label>
                                        <select class="form-control" name="frecuencia_id" id="frecuencia_id"
                                            v-model="model.frecuencia_id" @focus="$parent.clearErrorMsg($event)">
                                            <option disabled value="">Seleccionar</option>
                                            <option value="Diario">Diario</option>
                                            <option value="Semanal">Semanal</option>
                                            <option value="Quincenal">Quincenal</option>
                                            <option value="Mensual">Mensual</option>
                                        </select>
                                        <div id="frecuencia_id-error" class="error invalid-feedback"></div>
                                    </div>
                                </div>

                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label class="form-control-label">Supervisor:</label>
                                        <select class="form-control" name="manager_id" id="manager_id"
                                            v-model="model.manager_id" @focus="$parent.clearErrorMsg($event)">
                                            <option selected disabled value="">Seleccionar</option>
                                            <option value="18">Oficina</option>
                                        </select>
                                        <div id="manager_id-error" class="error invalid-feedback"></div>
                                    </div>
                                </div>

                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label class="form-control-label">Condición de Pago:</label>
                                        <select class="form-control" name="payment_id" id="payment_id"
                                            v-model="model.payment_id" @focus="$parent.clearErrorMsg($event)">
                                            <option value="" selected disabled>Seleccionar</option>
                                            <option v-for="payment in payments" :value="payment.id"
                                                v-bind:key="payment.id">{{ payment.name }}</option>
                                        </select>
                                        <div id="payment_id-error" class="error invalid-feedback"></div>
                                    </div>
                                </div>

                                <div class="col-lg-4" v-if="model.payment_id == 2">
                                    <div class="form-group">
                                        <label class="form-control-label">Límite de Crédito:</label>
                                        <input type="number" class="form-control" name="credit_limit" id="credit_limit"
                                            placeholder="1000" step="any" min="0" v-model="model.credit_limit"
                                            @focus="$parent.clearErrorMsg($event)">
                                        <div id="credit_limit-error" class="error invalid-feedback"></div>
                                    </div>
                                </div>
                                <div class="col-lg-4" v-if="model.payment_id == 2">
                                    <div class="form-group">
                                        <label class="form-control-label">Días de Crédito:</label>
                                        <input type="number" class="form-control" name="credit_limit_days"
                                            id="credit_limit_days" step="1" min="0" placeholder="10"
                                            v-model="model.credit_limit_days" @focus="$parent.clearErrorMsg($event)">
                                        <div id="credit_limit_days-error" class="error invalid-feedback"></div>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label class="form-control-label">Percepción:</label>
                                        <select class="form-control" name="perception_percentage_id"
                                            id="perception_percentage_id" v-model="model.perception_percentage_id"
                                            @focus="$parent.clearErrorMsg($event)">
                                            <option value="" selected disabled>Seleccionar</option>
                                            <option v-for="perception in perceptions" :value="perception.id"
                                                v-bind:key="perception.id">{{ perception.value }}%</option>
                                        </select>
                                        <div id="perception_percentage_id-error" class="error invalid-feedback"></div>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label class="form-control-label">Estado:</label>
                                        <select class="form-control" name="estado" id="estado" v-model="model.estado"
                                            @focus="$parent.clearErrorMsg($event)">
                                            <option selected disabled value="">Seleccionar</option>
                                            <option value="ACTIVO">ACTIVO</option>
                                            <option value="INACTIVO">INACTIVO</option>
                                        </select>
                                        <div id="estado-error" class="error invalid-feedback"></div>
                                    </div>
                                </div>
                                <div class="col-lg-4" v-if="this.model.estado === 'INACTIVO'">
                                    <div class="form-group">
                                        <label class="form-control-label">Razón:</label>
                                        <select class="form-control" name="razon" id="razon" v-model="model.razon"
                                            @focus="$parent.clearErrorMsg($event)">
                                            <option selected disabled value="">Seleccionar</option>
                                            <option value="APAGADO">APAGADO</option>
                                            <option value="NO EXISTE">NO EXISTE</option>
                                            <option value="OTRO PROVEEDOR">OTRO PROVEEDOR</option>
                                            <option value="CALIDDA">CÁLIDDA</option>
                                            <option value="CAMBIO RESIDENCIA">CAMBIO RESIDENCIA</option>
                                            <option value="FUERA DE COBERTURA">FUERA DE COBERTURA</option>
                                        </select>
                                        <div id="razon-error" class="error invalid-feedback"></div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">
                            Crear Cliente
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
        url_get_ubigeos: {
            type: String,
            default: ''
        },
        document_types: {
            type: Array,
            default: ''
        },
        payments: {
            type: Array,
            default: ''
        },
        companies: {
            type: Array,
            default: ''
        },
        perceptions: {
            type: Array,
            default: ''
        },
        business_units: {
            type: Array,
            default: ''
        },
        client_zones: {
            type: Array,
            default: ''
        },
        client_channels: {
            type: Array,
            default: ''
        },
        client_types: {
            type: Array,
            default: ''
        },
        client_routes: {
            type: Array,
            default: ''
        },
        client_sectors: {
            type: Array,
            default: ''
        },
        osi_agents: {
            type: Array,
            default: ''
        },
    },
    data() {
        return {
            model: {
                company_id: '2',
                document_type_id: '',
                document_number: '',
                client_code: '',
                business_name: '',
                address: '',
                address_reference: '',
                ubigeo_id: '',
                gps_x: '',
                gps_y: '',
                contact_name_1: '',
                phone_number_1: '',
                phone_number_2: '',
                business_unit_id: '',
                zone_id: '',
                channel_id: '',
                route_id: '',
                sector_id: '',
                seller_id: '',
                client_type: '',
                activity_uno: '',
                activity_tres: '',
                manager_id: '',
                payment_id: '',
                credit_limit: '',
                credit_limit_days: '',
                perception_percentage_id: '',
                frecuencia_id: '',
                frecuencia: '',
                sector: '',
                zona: '',
                estado: '',
                razon: ''
            },
        }
    },
    watch: {
        'model.channel_id': function (val) {
            if (val == 7) {
                this.model.frecuencia_id = 'Mensual';
                this.model.frecuencia = 1;
            } else if (val == 8) {
                this.model.frecuencia_id = 'Quincenal';
                this.model.frecuencia = 1;
            } else {
                this.model.frecuencia_id = '';
                this.model.frecuencia = '';
            }
        },

    },
    computed: {

    },
    created() {
        EventBus.$on('create_modal_client', function () {
            this.model.company_id = '2';
            this.model.document_type_id = '';
            this.model.document_number = '';
            this.model.client_code = '';
            this.model.business_name = '';
            this.model.address = '';
            this.model.address_reference = '';
            this.model.ubigeo_id = '';
            this.model.gps_x = '';
            this.model.gps_y = '';
            this.model.contact_name_1 = '';
            this.model.phone_number_1 = '';
            this.model.phone_number_2 = '';
            this.model.business_unit_id = '3';
            this.model.zone_id = '5';
            this.model.channel_id = '';
            this.model.route_id = '';
            this.model.sector_id = '4';
            this.model.seller_id = 'OFICINA';
            this.model.client_type = '';
            this.model.dgh = '';
            this.model.activity_uno = '';
            this.model.activity_tres = '';
            this.model.manager_id = '18';
            this.model.payment_id = '1';
            this.model.credit_limit = '';
            this.model.credit_limit_days = '';
            this.model.perception_percentage_id = '3';
            this.model.frecuencia_id = '';
            this.model.frecuencia = '';
            this.model.sector = '';
            this.model.zona = '';
            this.model.estado = 'ACTIVO';
            this.model.razon = '';

            $('#ubigeo_id').val(null).trigger('change');
            $('#modal-client').modal('show');
        }.bind(this));

    },
    mounted() {
        this.newSelect2();
    },
    methods: {
        newSelect2: function () {
            let vm = this;
            let token = document.head.querySelector('meta[name="csrf-token"]').content;
            $("#ubigeo_id").select2({
                placeholder: "Buscar",
                allowClear: true,
                language: {
                    noResults: function () {
                        return 'No hay resultados';
                    },
                    searching: function () {
                        return 'Buscando...';
                    },
                    inputTooShort: function () {
                        return 'Ingresa 1 o más caracteres';
                    },
                    errorLoading: function () {
                        return 'No se pudo cargar la información'
                    }
                },
                ajax: {
                    url: this.url_get_ubigeos,
                    dataType: 'json',
                    delay: 250,
                    type: 'POST',
                    data: function (params) {
                        var queryParameters = {
                            q: params.term,
                            _token: token,
                        }

                        return queryParameters;
                    },
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
                minimumInputLength: 0,
            }).on('select2:select', function (e) {
                var selected_element = $(e.currentTarget);
                vm.model.ubigeo_id = parseInt(selected_element.val());
            }).on('select2:unselect', function (e) {
                vm.model.ubigeo_id = '';
            });
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
                $('#modal-client').modal('hide');

                EventBus.$emit('asignar_cliente', response.data);
                this.$parent.alertMsg(response.data);

                this.model.company_id = '';
                this.model.document_type_id = '';
                this.model.document_number = '';
                this.model.client_code = '';
                this.model.business_name = '';
                this.model.address = '';
                this.model.address_reference = '';
                this.model.ubigeo_id = '';
                this.model.gps_x = '';
                this.model.gps_y = '';
                this.model.contact_name_1 = '';
                this.model.phone_number_1 = '';
                this.model.phone_number_2 = '';
                this.model.business_unit_id = '';
                this.model.zone_id = '';
                this.model.channel_id = '';
                this.model.route_id = '';
                this.model.sector_id = '';
                this.model.seller_id = '';
                this.model.client_type = '';
                this.model.dgh = '';
                this.model.activity_uno = '';
                this.model.activity_tres = '';
                this.model.manager_id = '';
                this.model.payment_id = '';
                this.model.credit_limit = '';
                this.model.credit_limit_days = '';
                this.model.perception_percentage_id = '';
                this.model.frecuencia_id = '';
                this.model.frecuencia = '';
                this.model.sector = '';
                this.model.zona = '';
                this.model.estado = '';
                this.model.razon = '';

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
