<template>
    <!--begin::Modal-->
    <div class="modal fade" id="cochera-register-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <!--begin::Form-->
                <form class="kt-form" @submit.prevent="sendForm()">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Agregar artículo</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="kt-portlet__body">
                            <div class="row">
                                <div class="col-6">
                                    <div class="form-group">
                                        <label class="form-control-label">Artículo:</label>
                                        <input type="text" class="form-control" name="article_name" id="article_name"
                                            v-model="model.article_name" @focus="$parent.clearErrorMsg($event)">
                                        <div id="article_name-error" class="error invalid-feedback"></div>
                                    </div>
                                </div>
                                <div class="col-lg-3">
                                    <div class="form-group">
                                        <label class="form-control-label">Cantidad:</label>
                                        <input type="tel" class="form-control" name="quantity" id="quantity"
                                            placeholder="0" v-model="model.quantity"
                                            @focus="$parent.clearErrorMsg($event)">
                                        <div id="quantity-error" class="error invalid-feedback"></div>
                                    </div>
                                </div>
                                <div class="col-lg-3">
                                    <div class="form-group">
                                        <label class="form-control-label">Precio unitario:</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">
                                                    {{ this.currency.symbol }}
                                                </span>
                                            </div>
                                            <input disabled type="tel" class="form-control" name="price" id="price"
                                                placeholder="0" v-model="getPrice"
                                                @focus="$parent.clearErrorMsg($event)">
                                        </div>
                                        <div id="price-error" class="error invalid-feedback"></div>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="form-group">
                                        <label class="form-control-label">Valor Venta:</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">
                                                    {{ this.currency.symbol }}
                                                </span>
                                            </div>
                                            <input type="tel" class="form-control" name="sale_value" id="sale_value"
                                                disabled placeholder="0" v-model="getSaleValue"
                                                @focus="$parent.clearErrorMsg($event)">
                                        </div>
                                        <div id="sale_value-error" class="error invalid-feedback"></div>
                                    </div>
                                </div>

                                <div class="col">
                                    <div class="form-group">
                                        <label class="form-control-label">{{ this.igv }} % IGV:</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">
                                                    {{ this.currency.symbol }}
                                                </span>
                                            </div>
                                            <input disabled class="form-control" name="igv" id="igv" placeholder="0"
                                                v-model="getIgv" @focus="$parent.clearErrorMsg($event)">
                                        </div>
                                        <div id="igv-error" class="error invalid-feedback"></div>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="form-group">
                                        <label class="form-control-label">Total:</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">
                                                    {{ this.currency.symbol }}
                                                </span>
                                            </div>
                                            <input class="form-control" name="total" id="total" placeholder="0"
                                                v-model="model.total" @focus="$parent.clearErrorMsg($event)">
                                        </div>
                                        <div id="total-error" class="error invalid-feedback"></div>
                                    </div>
                                </div>
                                <div class="col-lg-3">
                                    <div class="form-group">
                                        <label class="form-control-label">Placa:</label>
                                        <select class="form-control kt-select2" name="cochera_vehicle_id"
                                            id="cochera_vehicle_id" v-model="model.cochera_vehicle_id"
                                            @focus="$parent.clearErrorMsg($event)">
                                            <option value="" selected disabled>Seleccionar</option>
                                            <option v-for="cochera_vehicle in cochera_vehicles"
                                                :value="cochera_vehicle.id" v-bind:key="cochera_vehicle.id">{{
                                                    cochera_vehicle.plate }}</option>
                                        </select>
                                        <div id="cochera_vehicle_id-error" class="error invalid-feedback"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success" id="add_article_2" disabled>
                            <i class="fa fa-plus"></i> Agregar artículo
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
        url_get_article: {
            type: String,
            default: ''
        },
        cochera_vehicles: {
            type: Array,
            default: ''
        },
    },
    data() {
        return {
            model: {
                article_name: '',
                quantity: '',
                price: '',
                sale_value: '',
                igv: 0,
                igv_percentage: 0,
                total: '',
                currency: 0,
                cochera_vehicle_id: ''
            },
            article_list: [],
            igv: '',
            currency: {},
            movement_type_id: '',
        }
    },
    watch: {
        'model.quantity': function (val) {
            this.model.sale_value = (val * this.model.price).toFixed(4);
            if (this.model.article_name != '' && (Number(val) != '' && Number(val) > 0) && (this.model.total > 0 || Number(this.model.total)) && this.model.cochera_vehicle_id != '') {
                $('input#quantity').removeClass('is-invalid');
                $('#quantity-error').html('');
                document.getElementById('add_article_2').disabled = false;
            } else {
                document.getElementById('add_article_2').disabled = true;
            }
        },
        'model.article_name': function (val) {
            if (val != '' && (this.model.quantity != '' || Number(this.model.quantity) > 0) && (this.model.total > 0 || Number(this.model.total)) && this.model.cochera_vehicle_id != '') {
                document.getElementById('add_article_2').disabled = false;
            } else {
                document.getElementById('add_article_2').disabled = true;
            }
        },
        'model.total': function (val) {
            if (Number(val) != '' && (this.model.quantity != '' || Number(this.model.quantity) > 0) && this.model.article_name != '' && this.model.cochera_vehicle_id != '') {
                document.getElementById('add_article_2').disabled = false;
            } else {
                document.getElementById('add_article_2').disabled = true;
            }
        },
        'model.cochera_vehicle_id': function (val) {
            if (val != '' && (this.model.quantity != '' || Number(this.model.quantity) > 0) && this.model.article_name != '' && (this.model.total != '' || Number(this.model.total) > 0)) {
                document.getElementById('add_article_2').disabled = false;
            } else {
                document.getElementById('add_article_2').disabled = true;
            }
        }
    },
    computed: {
        getIgv: function () {

            let igv = this.model.total / 1.18 * 0.18;
            this.model.igv = igv.toFixed(4);

            return this.model.igv;
        },
        getSaleValue: function () {
            this.model.sale_value = (this.model.total - this.model.igv).toFixed(4);
            return this.model.sale_value;
        },
        getPrice: function () {
            this.model.price = (this.model.sale_value / this.model.quantity).toFixed(4);
            return this.model.price;
        },
    },
    created() {
        EventBus.$on('cochera_register_modal', function (igv, currency, movement_type_id) {
            this.igv = igv.value;
            this.currency = currency;
            this.movement_type_id = movement_type_id;
            $('#cochera-register-modal').modal('show');
        }.bind(this));

        EventBus.$on('cochera_register_modal_hide', function () {
            this.model.article_name = '';
            this.model.quantity = '';
            this.model.price = '';
            this.model.sale_value = '';
            this.model.igv = 0;
            this.model.total = 0;
            this.model.cochera_vehicle_id = '';
            $('#cochera-register-modal').modal('hide');
            // document.getElementById('add_article_2').disabled = false;
        }.bind(this));

        EventBus.$on('add_article_id', function (id) {
            this.article_list.push(id);
        }.bind(this));

        EventBus.$on('remove_article_id', function (id) {
            let index = this.article_list.findIndex((element) => element == id);
            this.article_list.splice(index, 1);
            console.log(index, id);
        }.bind(this));

        EventBus.$on('reset_cochera_register', function () {
            this.model.article_name = '';
            this.model.quantity = '';
            this.model.price = '';
            this.model.sale_value = '';
            this.model.igv = 0;
            this.model.total = 0;
            this.article_list = [];
            this.igv = '';
            this.currency = {};
            this.movement_type_id = '';
            this.model.cochera_vehicle_id = '';
        }.bind(this));
    },
    mounted() {

        $('#cochera-register-modal').on('hide.bs.modal', function (e) {
            this.model.article_name = '';
            this.model.quantity = '';
            this.model.price = '';
            this.model.sale_value = '';
            this.model.igv = 0;
            this.model.total = 0;
            this.model.cochera_vehicle_id = '';
            // document.getElementById('add_article_2').disabled = false;
        }.bind(this));
    },
    methods: {
        sendForm: function () {

            document.getElementById('add_article_2').disabled = true;

            this.model.igv_percentage = this.igv;
            this.model.currency = this.currency.name;


            const selectedVehicle = this.cochera_vehicles.find(vehicle => vehicle.id === this.model.cochera_vehicle_id);
            this.model.placa = selectedVehicle.plate;

            EventBus.$emit('sendForm', this.model);
        }
    }
};
</script>