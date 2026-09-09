<template>
    <!--begin::Modal-->
    <div class="modal fade" id="modal_money" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <!--begin::Form-->
                <form class="kt-form" @submit.prevent="formController(url_register_money, $event)">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Billetes y Monedas</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="kt-portlet__body">
                            <div class="row">
                                <input type="hidden" name="id" id="id" v-model="model.id">
                                <input type="hidden" name="plant_order_id" id="plant_order_id"
                                    v-model="model.plant_order_id">

                                <div class="col-lg-2">
                                    <div class="form-group">
                                        <label class="form-control-label">BILLETES DE 200:</label>
                                        <input type="text" class="form-control" name="billete_uno" id="billete_uno"
                                            placeholder="0.00" v-model="model.billete_uno"
                                            @focus="$parent.clearErrorMsg($event)">
                                        <div id="billete_uno-error" class="error invalid-feedback"></div>
                                    </div>
                                </div>
                                <div class="col-lg-2">
                                    <div class="form-group">
                                        <label class="form-control-label">BILLETES DE 100:</label>
                                        <input type="text" class="form-control" name="billete_dos" id="billete_dos"
                                            placeholder="0.00" v-model="model.billete_dos"
                                            @focus="$parent.clearErrorMsg($event)">
                                        <div id="billete_dos-error" class="error invalid-feedback"></div>
                                    </div>
                                </div>
                                <div class="col-lg-2">
                                    <div class="form-group">
                                        <label class="form-control-label">BILLETES DE 50:</label>
                                        <input type="text" class="form-control" name="billete_tres" id="billete_tres"
                                            placeholder="0.00" v-model="model.billete_tres"
                                            @focus="$parent.clearErrorMsg($event)">
                                        <div id="billete_tres-error" class="error invalid-feedback"></div>
                                    </div>
                                </div>
                                <div class="col-lg-2">
                                    <div class="form-group">
                                        <label class="form-control-label">BILLETES DE 20:</label>
                                        <input type="text" class="form-control" name="billete_cuatro"
                                            id="billete_cuatro" placeholder="0.00" v-model="model.billete_cuatro"
                                            @focus="$parent.clearErrorMsg($event)">
                                        <div id="billete_cuatro-error" class="error invalid-feedback"></div>
                                    </div>
                                </div>
                                <div class="col-lg-2">
                                    <div class="form-group">
                                        <label class="form-control-label">BILLETES DE 10:</label>
                                        <input type="text" class="form-control" name="billete_cinco" id="billete_cinco"
                                            placeholder="0.00" v-model="model.billete_cinco"
                                            @focus="$parent.clearErrorMsg($event)">
                                        <div id="billete_cinco-error" class="error invalid-feedback"></div>
                                    </div>
                                </div>
                                <div class="col-lg-2">
                                    <div class="form-group">
                                        <label class="form-control-label">TOTAL:</label>
                                        <input type="text" class="form-control" name="total_1" id="total_1"
                                            placeholder="0.00" v-model="model.total_1"
                                            @focus="$parent.clearErrorMsg($event)">
                                        <div id="total_1-error" class="error invalid-feedback"></div>
                                    </div>
                                </div>

                                <div class="col-lg-2">
                                    <div class="form-group">
                                        <label class="form-control-label text-success">MONEDAS DE 5.00:</label>
                                        <input type="text" class="form-control" name="moneda_uno" id="moneda_uno"
                                            placeholder="0.00" v-model="model.moneda_uno"
                                            @focus="$parent.clearErrorMsg($event)">
                                        <div id="moneda_uno-error" class="error invalid-feedback"></div>
                                    </div>
                                </div>
                                <div class="col-lg-2">
                                    <div class="form-group">
                                        <label class="form-control-label text-success">MONEDAS DE 2.00:</label>
                                        <input type="text" class="form-control" name="moneda_dos" id="moneda_dos"
                                            placeholder="0.00" v-model="model.moneda_dos"
                                            @focus="$parent.clearErrorMsg($event)">
                                        <div id="moneda_dos-error" class="error invalid-feedback"></div>
                                    </div>
                                </div>
                                <div class="col-lg-2">
                                    <div class="form-group">
                                        <label class="form-control-label text-success">MONEDAS DE 1.00:</label>
                                        <input type="text" class="form-control" name="moneda_tres" id="moneda_tres"
                                            placeholder="0.00" v-model="model.moneda_tres"
                                            @focus="$parent.clearErrorMsg($event)">
                                        <div id="moneda_tres-error" class="error invalid-feedback"></div>
                                    </div>
                                </div>
                                <div class="col-lg-2">
                                    <div class="form-group">
                                        <label class="form-control-label text-success">MONEDAS DE 0.50:</label>
                                        <input type="text" class="form-control" name="moneda_cuatro" id="moneda_cuatro"
                                            placeholder="0.00" v-model="model.moneda_cuatro"
                                            @focus="$parent.clearErrorMsg($event)">
                                        <div id="moneda_cuatro-error" class="error invalid-feedback"></div>
                                    </div>
                                </div>
                                <div class="col-lg-2">
                                    <div class="form-group">
                                        <label class="form-control-label text-success">MONEDAS DE 0.20:</label>
                                        <input type="text" class="form-control" name="moneda_cinco" id="moneda_cinco"
                                            placeholder="0.00" v-model="model.moneda_cinco"
                                            @focus="$parent.clearErrorMsg($event)">
                                        <div id="moneda_cinco-error" class="error invalid-feedback"></div>
                                    </div>
                                </div>
                                <div class="col-lg-2">
                                    <div class="form-group">
                                        <label class="form-control-label text-success">MONEDAS DE 0.10:</label>
                                        <input type="text" class="form-control" name="moneda_seis" id="moneda_seis"
                                            placeholder="0.00" v-model="model.moneda_seis"
                                            @focus="$parent.clearErrorMsg($event)">
                                        <div id="moneda_seis-error" class="error invalid-feedback"></div>
                                    </div>
                                </div>
                                <div class="col-lg-2">
                                    <div class="form-group">
                                        <label class="form-control-label text-success">TOTAL:</label>
                                        <input type="text" class="form-control" name="total_2" id="total_2"
                                            placeholder="0.00" v-model="model.total_2"
                                            @focus="$parent.clearErrorMsg($event)">
                                        <div id="total_2-error" class="error invalid-feedback"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success"> {{ button_text }} </button>
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
        url_register_money: {
            type: String,
            default: ''
        },
    },
    data() {
        return {
            model: {
                id: '',
                plant_order_id: '',
                billete_uno: 0,
                billete_dos: 0,
                billete_tres: 0,
                billete_cuatro: 0,
                billete_cinco: 0,
                total_1: 0,
                moneda_uno: 0,
                moneda_dos: 0,
                moneda_tres: 0,
                moneda_cuatro: 0,
                moneda_cinco: 0,
                moneda_seis: 0,
                total_2: 0
            },
            button_text: '',
        }
    },
    watch: {
        'model.billete_uno': 'calcTotalBilletes',
        'model.billete_dos': 'calcTotalBilletes',
        'model.billete_tres': 'calcTotalBilletes',
        'model.billete_cuatro': 'calcTotalBilletes',
        'model.billete_cinco': 'calcTotalBilletes',

        'model.moneda_uno': 'calcTotalMonedas',
        'model.moneda_dos': 'calcTotalMonedas',
        'model.moneda_tres': 'calcTotalMonedas',
        'model.moneda_cuatro': 'calcTotalMonedas',
        'model.moneda_cinco': 'calcTotalMonedas',
        'model.moneda_seis': 'calcTotalMonedas',

    },
    computed: {

    },
    created() {
        EventBus.$on('show_register_money', function (model) {

            EventBus.$emit('clear_modal');
            this.model = model;
            if (!this.model || this.model.id === undefined || this.model.id === null || this.model.id === '') {
                this.button_text = 'Registrar';
            } else {
                this.button_text = 'Actualizar';
            }

            $('#modal_money').modal('show');
        }.bind(this));

        EventBus.$on('clear_modal', function () {
            this.model.id = '';
            this.model.plant_order_id = '';
            this.model.billete_uno = 0;
            this.model.billete_dos = 0;
            this.model.billete_tres = 0;
            this.model.billete_cuatro = 0;
            this.model.billete_cinco = 0;
            this.model.total_1 = 0;
            this.model.moneda_uno = 0;
            this.model.moneda_dos = 0;
            this.model.moneda_tres = 0;
            this.model.moneda_cuatro = 0;
            this.model.moneda_cinco = 0;
            this.model.moneda_seis = 0;
            this.model.total_2 = 0;
            this.button_text = '';
        }.bind(this));
    },
    mounted() {

    },
    methods: {
        calcTotalBilletes() {
            this.model.total_1 = parseFloat(
                (
                    (Number(this.model.billete_uno) || 0) * 200 +
                    (Number(this.model.billete_dos) || 0) * 100 +
                    (Number(this.model.billete_tres) || 0) * 50 +
                    (Number(this.model.billete_cuatro) || 0) * 20 +
                    (Number(this.model.billete_cinco) || 0) * 10
                ).toFixed(2)
            );
        },

        calcTotalMonedas() {
            this.model.total_2 = parseFloat(
                (
                    (Number(this.model.moneda_uno) || 0) * 5 +
                    (Number(this.model.moneda_dos) || 0) * 2 +
                    (Number(this.model.moneda_tres) || 0) * 1 +
                    (Number(this.model.moneda_cuatro) || 0) * 0.5 +
                    (Number(this.model.moneda_cinco) || 0) * 0.2 +
                    (Number(this.model.moneda_seis) || 0) * 0.1
                ).toFixed(2)
            );
        },

        formController: function (url_register_money, event) {
            var vm = this;

            var target = $(event.target);
            var url = url_register_money;
            var fd = new FormData(event.target);

            EventBus.$emit('loading', true);

            axios.post(url, fd, {
                headers: {
                    'Content-type': 'application/x-www-form-urlencoded',
                }
            }).then(response => {
                EventBus.$emit('loading', false);

                this.$parent.alertMsg(response.data);
                $('#modal_money').modal('hide');
                EventBus.$emit('clear_modal');

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