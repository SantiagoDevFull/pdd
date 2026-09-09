<template>
    <!--begin::Modal-->
    <div class="modal fade" id="modal_contabilidad" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content bg-warning">
                <!--begin::Form-->
                <form class="kt-form" @submit.prevent="formController(url_update, $event)">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">{{ this.voucher_type_name }} ::: {{
                            this.num_comprobante }} - {{ this.adm_payment_name }}
                        </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="kt-portlet__body">
                            <div class="row">
                                <input type="hidden" name="id" id="id" v-model="model.id">
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label class="form-control-label">Estado:</label>
                                        <select class="form-control kt-select2" name="state_1" id="state_1"
                                            v-model="model.state_1" @focus="$parent.clearErrorMsg($event)">
                                            <option value="" selected disabled>Seleccionar</option>
                                            <option value="APROBADO">APROBADO</option>
                                            <option value="DESAPROBADO">DESAPROBADO</option>
                                        </select>
                                        <div id="state_1-error" class="error invalid-feedback"></div>
                                    </div>
                                </div>
                                <div class="col-lg-4" v-if="this.adm_payment_name == 'POR RENDIR'">
                                    <div class="form-group">
                                        <label class="form-control-label">Estado:</label>
                                        <select class="form-control kt-select2" name="state_2" id="state_2"
                                            v-model="model.state_2" @focus="$parent.clearErrorMsg($event)">
                                            <option value="" selected disabled>Seleccionar</option>
                                            <option value="LIQUIDADO">LIQUIDADO</option>
                                            <option value="DESAPROBADO">DESAPROBADO</option>
                                        </select>
                                        <div id="state_2-error" class="error invalid-feedback"></div>
                                    </div>
                                </div>
                                <div class="col-lg-4" v-if="this.adm_payment_name == 'POR PAGAR'">
                                    <div class="form-group">
                                        <label class="form-control-label">Retenciones:</label>
                                        <select class="form-control kt-select2" name="retencion" id="retencion"
                                            v-model="model.retencion" @focus="$parent.clearErrorMsg($event)">
                                            <option value="" selected disabled>Seleccionar</option>
                                            <option value="SI">SI</option>
                                            <option value="NO">NO</option>
                                        </select>
                                        <div id="retencion-error" class="error invalid-feedback"></div>
                                    </div>
                                </div>
                                <div class="col-lg-4" v-if="this.adm_payment_name == 'POR PAGAR'">
                                    <div class="form-group">
                                        <label class="form-control-label">Detracciones:</label>
                                        <select class="form-control kt-select2" name="detraccion" id="detraccion"
                                            v-model="model.detraccion" @focus="$parent.clearErrorMsg($event)">
                                            <option value="" selected disabled>Seleccionar</option>
                                            <option value="SI">SI</option>
                                            <option value="NO">NO</option>
                                        </select>
                                        <div id="detraccion-error" class="error invalid-feedback"></div>
                                    </div>
                                </div>
                                <div class="col-lg-4" v-if="this.adm_payment_name == 'POR PAGAR'">
                                    <div class="form-group">
                                        <label class="form-control-label">Porcentaje:</label>
                                        <select class="form-control kt-select2" name="porcentaje" id="porcentaje"
                                            v-model="model.porcentaje" @focus="$parent.clearErrorMsg($event)">
                                            <option value="" selected disabled>Seleccionar</option>
                                            <option value="12">12%</option>
                                            <option value="10">10%</option>
                                            <option value="4">4%</option>
                                            <option value="3">3%</option>
                                            <option value="0">0%</option>
                                        </select>
                                        <div id="porcentaje-error" class="error invalid-feedback"></div>
                                    </div>
                                </div>
                                <div class="col-lg-4" v-if="this.adm_payment_name == 'POR RENDIR'">
                                    <div class="form-group">
                                        <label class="form-control-label">Situacion:</label>
                                        <select class="form-control kt-select2" name="situacion" id="situacion"
                                            v-model="model.situacion" @focus="$parent.clearErrorMsg($event)">
                                            <option value="" selected disabled>Seleccionar</option>
                                            <option value="COMPLETO">COMPLETO</option>
                                            <option value="REEMBOLSAR">REEMBOLSAR</option>
                                            <option value="POR DEVOLVER">POR DEVOLVER</option>
                                        </select>
                                        <div id="situacion-error" class="error invalid-feedback"></div>
                                    </div>
                                </div>
                                <div class="col-lg-4"
                                    v-if="(this.adm_payment_name == 'REEMBOLSO' || this.adm_payment_name == 'POR RENDIR') && this.model.state_1 == 'APROBADO'">
                                    <div class="form-group">
                                        <label class="form-control-label">Moneda:</label>
                                        <select class="form-control kt-select2" name="moneda" id="moneda"
                                            v-model="model.moneda" @focus="$parent.clearErrorMsg($event)">
                                            <option value="" selected disabled>Seleccionar</option>
                                            <option value="SOLES">SOLES</option>
                                            <option value="DOLARES">DOLARES</option>
                                        </select>
                                        <div id="moneda-error" class="error invalid-feedback"></div>
                                    </div>
                                </div>
                                <div class="col-lg-4" v-if="this.model.state_1 == 'APROBADO'">
                                    <div class="form-group">
                                        <label class="form-control-label">Importe:</label>
                                        <input type="text" class="form-control" name="import" id="import"
                                            v-model="model.import" @focus="$parent.clearErrorMsg($event)">
                                        <div id="import-error" class="error invalid-feedback"></div>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label class="form-control-label">Comentario:</label>
                                        <textarea class="form-control" name="comentario" id="comentario"
                                            v-model="model.comentario"
                                            @focus="$parent.clearErrorMsg($event)"></textarea>

                                        <div id="comentario-error" class="error invalid-feedback"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-success">
                                Registrar
                            </button>
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                        </div>
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
        url_update: {
            type: String,
            default: ''
        },
    },
    data() {
        return {
            model: {
                id: '',
                state_1: '',
                state_2: '',
                retencion: '',
                detraccion: '',
                porcentaje: '',
                situacion: '',
                moneda: '',
                import: '',
                comentario: '',
            },
            importe: '',
            voucher_type_name: '',
            num_comprobante: '',
            adm_payment_name: '',
        }
    },
    watch: {

        'model.porcentaje': function (val) {
            const importe = parseFloat(this.importe) || 0;
            const porcentaje = parseFloat(val) || 0;
            this.model.import = importe - (importe * porcentaje / 100);
        },

    },
    computed: {

    },
    created() {
        EventBus.$on('modal_contabilidad', function (id, importe, voucher_type_name, num_comprobante, adm_payment_name) {

            EventBus.$emit('clear_contabilidad');

            if (adm_payment_name == 'POR PAGAR' || adm_payment_name == 'POR RENDIR') {
                this.importe = importe;
            } else {
                this.importe = '';
            }

            this.model.id = id;
            this.model.import = this.importe;
            this.voucher_type_name = voucher_type_name;
            this.num_comprobante = num_comprobante;
            this.adm_payment_name = adm_payment_name;

            $('#modal_contabilidad').modal('show');

        }.bind(this));

        EventBus.$on('clear_contabilidad', function () {
            this.model.id = '';
            this.model.state_1 = '';
            this.model.state_2 = '';
            this.model.retencion = '';
            this.model.detraccion = '';
            this.model.porcentaje = '';
            this.model.situacion = '';
            this.model.moneda = '';
            this.model.import = '';
            this.model.comentario = '';
        }.bind(this));

    },
    mounted() {

    },
    methods: {

        formController: function (url_update, event) {
            var vm = this;

            var target = $(event.target);
            var url = url_update;
            var fd = new FormData(event.target);

            EventBus.$emit('loading', true);

            axios.post(url, fd, {
                headers: {
                    'Content-type': 'application/x-www-form-urlencoded',
                }
            }).then(response => {
                EventBus.$emit('loading', false);

                $('#modal_contabilidad').modal('hide');
                this.$parent.alertMsg(response.data);
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