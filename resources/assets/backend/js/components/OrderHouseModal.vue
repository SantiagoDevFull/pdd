<template>
    <!--begin::Modal-->
    <div class="modal fade" id="modal_time" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <!--begin::Form-->
                <form class="kt-form" @submit.prevent="formController(url_update, $event)">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Actualizar Pedido {{ title }}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="kt-portlet__body">
                            <div class="row">
                                <input type="hidden" name="id" id="id" v-model="model.id">

                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label class="form-control-label">Hora llamada:</label>
                                        <input type="time" class="form-control" name="hora_llamada" id="hora_llamada"
                                            v-model="model.hora_llamada" @focus="$parent.clearErrorMsg($event)"
                                            readonly>
                                        <div id="hora_llamada-error" class="error invalid-feedback"></div>
                                    </div>
                                </div>

                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label class="form-control-label">Hora entrega balón:</label>
                                        <input type="time" class="form-control" name="hora_entrega_balon"
                                            id="hora_entrega_balon" v-model="model.hora_entrega_balon" readonly
                                            @focus="$parent.clearErrorMsg($event)">
                                        <div id="hora_entrega_balon-error" class="error invalid-feedback"></div>
                                    </div>
                                </div>

                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label class="form-control-label">Hora salida pedido:</label>
                                        <input type="time" class="form-control" name="hora_salida_pedido"
                                            id="hora_salida_pedido" v-model="model.hora_salida_pedido"
                                            @focus="$parent.clearErrorMsg($event)">
                                        <div id="hora_salida_pedido-error" class="error invalid-feedback"></div>
                                    </div>
                                </div>

                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label class="form-control-label">Hora entrega pedido:</label>
                                        <input type="time" class="form-control" name="hora_entrega_pedido"
                                            id="hora_entrega_pedido" v-model="model.hora_entrega_pedido"
                                            @focus="$parent.clearErrorMsg($event)">
                                        <div id="hora_entrega_pedido-error" class="error invalid-feedback"></div>
                                    </div>
                                </div>

                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label class="form-control-label">Hora retorno:</label>
                                        <input type="time" class="form-control" name="hora_retorno" id="hora_retorno"
                                            v-model="model.hora_retorno" @focus="$parent.clearErrorMsg($event)">
                                        <div id="hora_retorno-error" class="error invalid-feedback"></div>
                                    </div>
                                </div>

                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label class="form-control-label">Tiempo total:</label>
                                        <input type="time" class="form-control" name="tiempo_total" id="tiempo_total"
                                            v-model="model.tiempo_total" @focus="$parent.clearErrorMsg($event)"
                                            readonly>
                                        <div id="tiempo_total-error" class="error invalid-feedback"></div>
                                    </div>
                                </div>

                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label class="form-control-label">Precio:</label>
                                        <input type="text" class="form-control" name="price" id="price"
                                            placeholder="0.00" v-model="model.price"
                                            @focus="$parent.clearErrorMsg($event)">
                                        <div id="price-error" class="error invalid-feedback"></div>
                                    </div>
                                </div>

                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label class="form-control-label">Cantidad:</label>
                                        <input type="text" class="form-control" name="quantity" id="quantity"
                                            placeholder="0" v-model="model.quantity"
                                            @focus="$parent.clearErrorMsg($event)">
                                        <div id="quantity-error" class="error invalid-feedback"></div>
                                    </div>
                                </div>

                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label class="form-control-label">Total:</label>
                                        <input type="text" class="form-control" name="total" id="total" readonly
                                            v-model="model.total" @focus="$parent.clearErrorMsg($event)">
                                        <div id="total-error" class="error invalid-feedback"></div>
                                    </div>
                                </div>

                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label class="form-control-label">Metodo de pago:</label>
                                        <select class="form-control kt-select2" name="payment_method_id"
                                            id="payment_method_id" v-model="model.payment_method_id"
                                            @focus="$parent.clearErrorMsg($event)">
                                            <option value="" selected disabled>Seleccionar</option>
                                            <option v-for="payment_method in payment_methods"
                                                v-bind:key="payment_method.id" :value="payment_method.id">{{
                                                    payment_method.name }}</option>
                                        </select>
                                        <div id="payment_method_id-error" class="error invalid-feedback"></div>
                                    </div>
                                </div>

                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label class="form-control-label">Monto: <span class="text-danger">Total {{
                                            this.model.total }}</span></label>
                                        <input type="text" class="form-control" name="monto" id="monto"
                                            placeholder="0.00" v-model="model.monto"
                                            @input="model.monto = model.monto.replace(/[^0-9.]/g, '')"
                                            @focus="$parent.clearErrorMsg($event)">
                                        <div id="monto-error" class="error invalid-feedback"></div>
                                    </div>
                                </div>

                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label class="form-control-label">Vuelto:</label>
                                        <input type="text" class="form-control" name="vuelto" id="vuelto"
                                            placeholder="0.00" readonly v-model="model.vuelto"
                                            @focus="$parent.clearErrorMsg($event)">
                                        <div id="vuelto-error" class="error invalid-feedback"></div>
                                    </div>
                                </div>

                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label class="form-control-label">Estado:</label>
                                        <select class="form-control kt-select2" name="state" id="state"
                                            v-model="model.state" @focus="$parent.clearErrorMsg($event)">
                                            <option value="" selected disabled>Seleccionar</option>
                                            <option value="Pendiente de Entrega">Pendiente de Entrega</option>
                                            <option value="Entregado">Entregado</option>
                                            <option value="Cancelado">Cancelado</option>
                                        </select>
                                        <div id="state-error" class="error invalid-feedback"></div>
                                    </div>
                                </div>

                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label class="form-control-label">Fecha Próxima Compra:</label>
                                        <datetime v-model="model.fecha_proxima" readonly
                                            placeholder="Selecciona una Fecha inicial" :format="'dd-LL-yyyy'"
                                            input-id="fecha_proxima" name="fecha_proxima" value-zone="America/Lima"
                                            zone="America/Lima" class="form-control"
                                            @focus="$parent.clearErrorMsg($event)">
                                        </datetime>
                                        <div id="fecha_proxima-error" class="error invalid-feedback"></div>
                                    </div>
                                </div>

                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label class="form-control-label">¿Agregar Boleta de Referencia?</label>
                                        <input type="checkbox" class="form-control" name="res" id="res"
                                            v-model="model.res" @focus="$parent.clearErrorMsg($event)" readonly>
                                        <div id="res-error" class="error invalid-feedback"></div>
                                    </div>
                                </div>

                                <div class="col-lg-4" v-if="model.res">
                                    <div class="form-group">
                                        <label class="form-control-label">Boleta de Referencia:</label>
                                        <div class="input-group mb-3">
                                            <span class="input-group-text" id="basic-addon1">B310-</span>
                                            <input type="text" class="form-control" name="voucher" id="voucher" readonly
                                                v-model="model.voucher" @focus="$parent.clearErrorMsg($event)"
                                                @input="model.voucher = model.voucher.replace(/\D/g, '')"
                                                inputmode="numeric" pattern="[0-9]*">
                                            <div id="voucher-error" class="error invalid-feedback"></div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label class="form-control-label">Motivo de la demora:</label>
                                        <textarea class="form-control" name="motivo_demora" id="motivo_demora"
                                            v-model="model.motivo_demora"
                                            @focus="$parent.clearErrorMsg($event)"></textarea>
                                        <div id="hora_retorno-error" class="error invalid-feedback"></div>
                                    </div>
                                </div>

                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label class="form-control-label">Observaciones del Cliente:</label>
                                        <textarea class="form-control" name="obs_cli" id="obs_cli"
                                            v-model="model.obs_cli" @focus="$parent.clearErrorMsg($event)"></textarea>
                                        <div id="obs_cli-error" class="error invalid-feedback"></div>
                                    </div>
                                </div>


                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">
                            Actualizar
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
        url_update: {
            type: String,
            default: ''
        },
        url_get_voucher: {
            type: String,
            default: ''
        },
        payment_methods: {
            type: Array,
            default: ''
        },

    },
    data() {
        return {
            model: {
                id: '',
                hora_llamada: '',
                hora_entrega_balon: '',
                hora_salida_pedido: '',
                hora_entrega_pedido: '',
                hora_retorno: '',
                motivo_demora: '',
                obs_cli: '',
                state: '',
                tiempo_total: '',
                price: '',
                quantity: '',
                total: '',
                voucher: '',
                res: false,
                fecha_proxima: '',
                payment_method_id: '',
                monto: '',
                vuelto: '',
            },
            title: ''
        }
    },
    watch: {
        'model.res': function (val) {
            if (val) {
                this.model.state = 'Entregado';

                EventBus.$emit('loading', true);
                axios.post(this.url_get_voucher, {})
                    .then(response => {
                        EventBus.$emit('loading', false);
                        this.model.voucher = '';
                        this.model.voucher = response.data;
                    }).catch(error => {
                        EventBus.$emit('loading', false);
                        console.log(error);
                        console.log(error.response)
                        this.model.voucher = '';
                    })

            } else {
                this.model.state = 'Pendiente de Entrega';
            }
        },
        'model.hora_entrega_pedido': function () {
            this.calcularTiempoTotal();
        },
        'model.hora_llamada': function () {
            this.calcularTiempoTotal();
        },
        'model.monto': function () {
            if (this.model.monto) {
                this.model.vuelto = (parseFloat(this.model.monto) - parseFloat(this.model.total)).toFixed(2);
            } else {
                this.model.vuelto = '0';
            }
        },
        'model.quantity': function (val) {
            this.calculateTotal(val, this.model.price);
        },
        'model.price': function (val) {
            this.calculateTotal(this.model.quantity, val);
        },

    },
    computed: {

    },
    created() {
        EventBus.$on('edit_modal', function (model) {

            EventBus.$emit('clear_modal');
            this.model = model;


            if (model.fecha_proxima) {
                let d = new Date(model.fecha_proxima);
                this.model.fecha_proxima = d.toISOString().slice(0, 10);  // <<< ESTA ES LA CLAVE
            }

            this.title = model.serie + '-' + model.correlative;
            if (model.payment_method_id == null) {
                this.model.payment_method_id = '';
            }
            $('#modal_time').modal('show');
        }.bind(this));

        EventBus.$on('clear_modal', function () {
            this.title = '';
            this.model.id = '';
            this.model.hora_llamada = '';
            this.model.hora_entrega_balon = '';
            this.model.hora_salida_pedido = '';
            this.model.hora_entrega_pedido = '';
            this.model.hora_retorno = '';
            this.model.motivo_demora = '';
            this.model.obs_cli = '';
            this.model.state = '';
            this.model.tiempo_total = '';
            this.model.voucher = '';
            this.model.res = false;
            this.model.fecha_proxima = '';
        }.bind(this));
    },
    mounted() {

    },
    methods: {
        calculateTotal: function (quantity, price) {
            let total = accounting.toFixed(quantity * price, 2);
            this.model.total = total;

            if (this.model.monto) {
                this.model.vuelto = (parseFloat(this.model.monto) - parseFloat(this.model.total)).toFixed(2);
            }

        },
        calcularTiempoTotal() {
            if (this.model.hora_llamada && this.model.hora_entrega_pedido) {

                let [h1, m1] = this.model.hora_llamada.split(':').map(Number);

                let [h2, m2] = this.model.hora_entrega_pedido.split(':').map(Number);

                let minutosInicio = h1 * 60 + m1;
                let minutosFin = h2 * 60 + m2;

                if (minutosFin < minutosInicio) {
                    minutosFin += 24 * 60;
                }

                let totalMinutos = minutosFin - minutosInicio;

                let horas = String(Math.floor(totalMinutos / 60)).padStart(2, '0');
                let minutos = String(totalMinutos % 60).padStart(2, '0');

                this.model.tiempo_total = `${horas}:${minutos}`;
            } else {
                this.model.tiempo_total = '';
            }
        },
        formController: function (url_update, event) {
            var vm = this;

            var target = $(event.target);
            var url = url_update;
            var fd = new FormData(event.target);

            fd.append('respuesta', this.model.res ? 1 : 0);

            EventBus.$emit('loading', true);

            axios.post(url, fd, {
                headers: {
                    'Content-type': 'application/x-www-form-urlencoded',
                }
            }).then(response => {
                EventBus.$emit('loading', false);

                this.$parent.alertMsg(response.data);
                if (response.data.type != 5) {
                    $('#modal_time').modal('hide');
                    EventBus.$emit('clear_modal');
                    EventBus.$emit('refresh_table');
                }

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