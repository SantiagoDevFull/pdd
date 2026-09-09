<template>
    <!--begin::Modal-->
    <div class="modal fade" id="modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <!--begin::Form-->
                <form class="kt-form" @submit.prevent="formController(url, $event)">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">{{ this.button_text }}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="kt-portlet__body">
                            <div class="row">
                                <input type="hidden" name="id" id="id" v-model="model.id">
                                <input type="hidden" name="account_id" id="account_id" v-model="model.account_id">
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label class="form-control-label">Numero de Pedido:</label>
                                        <input type="text" class="form-control" name="referral_guide_series"
                                            id="referral_guide_series" v-model="model.referral_guide_series"
                                            @focus="$parent.clearErrorMsg($event)">
                                        <div id="referral_guide_series-error" class="error invalid-feedback"></div>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label class="form-control-label"> Guía de Remisión:</label>
                                        <input type="text" class="form-control" name="referral_guide_number"
                                            id="referral_guide_number" v-model="model.referral_guide_number"
                                            @focus="$parent.clearErrorMsg($event)">
                                        <div id="referral_guide_number-error" class="error invalid-feedback"></div>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label class="form-control-label">Fecha de Emisión:</label>
                                        <datetime v-model="model.date_emi" placeholder="Selecciona una Fecha"
                                            :format="'dd-LL-yyyy'" input-id="date_emi" name="date_emi"
                                            value-zone="America/Lima" zone="America/Lima" class="form-control"
                                            :max-datetime="current_date" @focus="$parent.clearErrorMsg($event)">
                                        </datetime>
                                        <div id="date_emi-error" class="error invalid-feedback"></div>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label class="form-control-label">Serie de Factura:</label>
                                        <input type="text" class="form-control" name="referral_serie_number"
                                            id="referral_serie_number" v-model="model.referral_serie_number"
                                            @focus="$parent.clearErrorMsg($event)">
                                        <div id="referral_serie_number-error" class="error invalid-feedback"></div>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label class="form-control-label">Número de Factura:</label>
                                        <input type="text" class="form-control" name="referral_voucher_number"
                                            id="referral_voucher_number" v-model="model.referral_voucher_number"
                                            @focus="$parent.clearErrorMsg($event)">
                                        <div id="referral_voucher_number-error" class="error invalid-feedback"></div>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label class="form-control-label">Número de SCOP:</label>
                                        <input type="text" class="form-control" name="scop_number" id="scop_number"
                                            v-model="model.scop_number" @focus="$parent.clearErrorMsg($event)">
                                        <div id="scop_number-error" class="error invalid-feedback"></div>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label class="form-control-label">Placa:</label>
                                        <input type="text" class="form-control" name="license_plate" id="license_plate"
                                            v-model="model.license_plate" @focus="$parent.clearErrorMsg($event)">
                                        <div id="license_plate-error" class="error invalid-feedback"></div>
                                    </div>
                                </div>


                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label class="form-control-label">Tracto:</label>
                                        <input type="text" class="form-control" name="license_plate_2" id="license_plate_2"
                                            v-model="model.license_plate_2" @focus="$parent.clearErrorMsg($event)">
                                        <div id="license_plate_2-error" class="error invalid-feedback"></div>
                                    </div>
                                </div>

                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label class="form-control-label">Costo Mes:</label>
                                        <input type="text" class="form-control" name="price_mes" id="price_mes"
                                            v-model="model.price_mes" @focus="$parent.clearErrorMsg($event)">
                                        <div id="price_mes-error" class="error invalid-feedback"></div>
                                    </div>
                                </div>

                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label class="form-control-label">Fecha de Ingreso a Planta:</label>
                                        <datetime v-model="model.date" placeholder="Selecciona una Fecha"
                                            :format="'dd-LL-yyyy'" input-id="date" name="date" value-zone="America/Lima"
                                            zone="America/Lima" class="form-control" :max-datetime="current_date"
                                            @focus="$parent.clearErrorMsg($event)">
                                        </datetime>
                                        <div id="date-error" class="error invalid-feedback"></div>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label class="form-control-label">Cambiar tipo de Movimiento:</label>
                                        <label class="kt-checkbox kt-checkbox--brand mt-3">
                                            <input type="checkbox" v-model="model.typing_error" name="typing_error"
                                                @change="Digitacion()" id="typing_error"> Error de Digitación
                                            <span></span>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label class="form-control-label">Cantidad a repartir: <span
                                                class="text-danger">(Stock: {{ model.converted_amount }})</span></label>

                                        <input type="text" class="form-control" name="digit2" id="digit2" placeholder="0"
                                            v-model="model.digit2" @focus="$parent.clearErrorMsg($event)"
                                            @change="ValidarDigit2()">
                                        <div id="digit2-error" class="error invalid-feedback"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success" id="btnActualizar">
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
import Datetime from 'vue-datetime';
// You need a specific loader for CSS files
import 'vue-datetime/dist/vue-datetime.css';

export default {
    props: {
        url: {
            type: String,
            default: ''
        },
    },
    data() {
        return {
            model: {
                id: '',
                account_id: '',
                referral_guide_series: '',
                referral_guide_number: '',
                referral_serie_number: '',
                referral_voucher_number: '',
                scop_number: '',
                license_plate: '',
                license_plate_2: '',
                price_mes: '',
                date: '',
                date_emi: '',
                min_datetime: '',
                max_datetime: '',
                typing_error: '',
                converted_amount: '',
                digit2: ''
            },
            button_text: ''
        }
    },
    watch: {

    },
    computed: {

    },
    created() {
        EventBus.$on('create_modal', function () {
            this.button_text = 'Crear';
            this.model = {
                id: '',
                account_id: '',
                referral_guide_series: '',
                referral_guide_number: '',
                referral_serie_number: '',
                referral_voucher_number: '',
                scop_number: '',
                license_plate: '',
                license_plate_2: '',
                date: '',
                date_emi: '',
                min_datetime: '',
                max_datetime: '',
                typing_error: '',
                converted_amount: '',
                digit2: ''
            };

            $('#modal').modal('show');
        }.bind(this));

        EventBus.$on('edit_modal', function (model) {
            this.model = model;
            this.button_text = 'Actualizar';
            $('#modal').modal('show');
        }.bind(this));
    },
    mounted() {

    },
    methods: {
        Digitacion: function () {
            if ($('#typing_error').prop('checked')) {
                $('#digit2').prop('readonly', true);
                this.model.digit2 = '0';
            } else {

                $('#digit2').prop('readonly', false);
                this.model.digit2 = '';
            }

        },
        ValidarDigit2: function () {
            if (Number(this.model.digit2) > Number(this.model.converted_amount) || Number(this.model.digit2) < 0) {
                $('input#digit2').addClass('is-invalid');
                $('#digit2-error').html('La cantidad no puede ser igual o mayor al stock o menor a cero.');
                $('#btnActualizar').prop('disabled', true);
            } else if ((Number(this.model.digit2) != '' && Number(this.model.digit2) >= 0)) {
                $('input#digit_2').removeClass('is-invalid');
                $('#digit_2-error').html('');
                $('#btnActualizar').prop('disabled', false);
            }
        },

        formController: function (url, event) {
            var vm = this;

            var target = $(event.target);
            var url = url;
            var fd = new FormData(event.target);

            EventBus.$emit('loading', true);

            // EventBus.$emit('loading', true);
            axios.post(url, fd, {
                headers: {
                    'Content-type': 'application/x-www-form-urlencoded',
                }
            }).then(response => {
                EventBus.$emit('loading', false);
                $('#modal').modal('hide');
                this.$parent.alertMsg(response.data);

                this.model = {
                    id: '',
                    account_id: '',
                    referral_guide_series: '',
                    referral_guide_number: '',
                    referral_serie_number: '',
                    referral_voucher_number: '',
                    scop_number: '',
                    license_plate: '',
                    license_plate_2: '',
                    date: '',
                    date_emi: '',
                    min_datetime: '',
                    max_datetime: '',
                    typing_error: '',
                    converted_amount: '',
                    digit2: '',
                };

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
