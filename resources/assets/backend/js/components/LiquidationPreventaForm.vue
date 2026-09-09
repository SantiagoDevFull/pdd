<template>
    <!--begin::Portlet-->
    <div class="kt-portlet">
        <div class="kt-portlet__head">
            <div class="kt-portlet__head-label">
                <h3 class="kt-portlet__head-title">
                    Buscar
                </h3>
            </div>
        </div>

        <!--begin::Form-->
        <form class="kt-form" @submit.prevent="formController(url, $event)">
            <div class="kt-portlet__body">
                <div class="row">
                    <input type="hidden" name="warehouse_type_id" id="warehouse_type_id"
                        v-model="model.warehouse_type_id">
                    <div class="col-lg-3">
                        <div class="form-group">
                            <label class="form-control-label">Ruta:</label>
                            <select class="form-control" name="client_route_id" id="client_route_id"
                                v-model="model.client_route_id" @focus="$parent.clearErrorMsg($event)">
                                <option value="">Seleccionar</option>
                                <option v-for="client_route in client_routes" :value="client_route.id"
                                    v-bind:key="client_route.id">{{ client_route.name }}</option>
                            </select>
                            <div id="client_route_id-error" class="error invalid-feedback"></div>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="form-group">
                            <label class="form-control-label">PEDIDO &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; PLACA
                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; FECHA VENTA</label>
                            <select class="form-control" name="warehouse_movement_id" id="warehouse_movement_id"
                                v-model="model.warehouse_movement_id" @focus="$parent.clearErrorMsg($event)">
                                <option value="">Seleccionar</option>
                                <option v-for="warehouse_movement in warehouse_movements" :value="warehouse_movement.id"
                                    v-bind:key="warehouse_movement.id">{{ warehouse_movement.pedido }}
                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; {{
                                        warehouse_movement.plate }} &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; {{
                                    warehouse_movement.traslate_date }} </option>
                            </select>
                            <div id="warehouse_movement_id-error" class="error invalid-feedback"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="kt-portlet__foot">
                <div class="kt-form__actions">
                    <div class="row">
                        <div class="col-lg-6">
                            <button type="submit" class="btn btn-primary">Buscar</button>
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
        client_routes: {
            type: Array,
            default: ''
        },
        url: {
            type: String,
            default: ''
        },
        url_get_warehouse_movements: {
            type: String,
            default: ''
        },
    },
    data() {
        return {
            model: {
                client_route_id: '',
                warehouse_movement_id: '',
            },
            warehouse_movements: [],
        }
    },
    created() {

    },
    mounted() {
        EventBus.$on('clear_form_sale', function () {
            $('.kt-form').find('input').prop('disabled', false);
            $('.kt-form').find('select').prop('disabled', false);
            $('.kt-form').find('button').prop('disabled', false);

            this.model = {
                client_route_id: '',
                warehouse_movement_id: '',
            }
        }.bind(this));
    },
    watch: {
        'model.client_route_id': function (val) {
            if (val != '') {
                EventBus.$emit('loading', true);

                axios.post(this.url_get_warehouse_movements, {
                    client_route_id: this.model.client_route_id

                }).then(response => {
                    // console.log(response);
                    this.model.warehouse_movement_id = '';
                    this.warehouse_movements = response.data;

                    EventBus.$emit('loading', false);
                }).catch(error => {
                    console.log(error);
                    console.log(error.response)
                });
            } else {
                this.model.warehouse_movement_id = '';
                this.warehouse_movements = '';
            }
        }
    },
    computed: {

    },
    methods: {
        formController: function (url, event) {
            var vm = this;

            var target = $(event.target);
            var url = url;
            var fd = new FormData(event.target);

            EventBus.$emit('loading', true);
            this.$store.commit('resetState');

            axios.post(url, fd, {
                headers: {
                    'Content-type': 'application/x-www-form-urlencoded',
                }
            }).then(response => {
                console.log(response);
                EventBus.$emit('loading', false);
                target.find('input').prop('disabled', true);
                target.find('select').prop('disabled', true);
                target.find('button').prop('disabled', true);
                EventBus.$emit('show_table', response.data);
            }).catch(error => {
                EventBus.$emit('loading', false);
                console.log(error.response);
                var obj = error.response.data.errors;
                $('html, body').animate({
                    scrollTop: 0
                }, 500, 'swing');
                $.each(obj, function (i, item) {
                    // console.log(target);
                    let c_target = target.find("#" + i + "-error");
                    let p = c_target.parents('.form-group').find('#' + i);
                    p.addClass('is-invalid');
                    c_target.css('display', 'block');
                    c_target.html(item);
                });
            });
        },
    }
};
</script>