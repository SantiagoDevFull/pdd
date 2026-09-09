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
        <form class="kt-form" @submit.prevent="formController(url, $event)" id="first-step-form">
            <div class="kt-portlet__body">
                <div class="row">
                    <div class="col-lg-3">
                        <div class="form-group">
                            <label class="form-control-label">Desde:</label>
                            <datetime v-model="model.initial_date" placeholder="Selecciona una Fecha"
                                :format="'dd-LL-yyyy'" name="initial_date" value-zone="America/Lima" zone="America/Lima"
                                class="form-control" :max-datetime="this.current_date"
                                @focus="$parent.clearErrorMsg($event)">
                            </datetime>
                            <div id="initial_date-error" class="error invalid-feedback"></div>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="form-group">
                            <label class="form-control-label">Hasta:</label>
                            <datetime v-model="model.final_date" placeholder="Selecciona una Fecha"
                                :format="'dd-LL-yyyy'" name="final_date" value-zone="America/Lima" zone="America/Lima"
                                class="form-control" :max-datetime="this.current_date"
                                @focus="$parent.clearErrorMsg($event)">
                            </datetime>
                            <div id="final_date-error" class="error invalid-feedback"></div>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="form-group">
                            <label class="form-control-label">Estado</label>
                            <select class="form-control" name="state" id="state" v-model="model.state"
                                @focus="$parent.clearErrorMsg($event)">
                                <option value="">Todos</option>
                                <option value="Pendiente de Entrega">Pendiente de Entrega</option>
                                <option value="Entregado">Entregado</option>
                                <option value="Cancelado">Cancelado</option>
                            </select>
                            <div id="state-error" class="error invalid-feedback"></div>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="form-group">
                            <label class="form-control-label">Ruta:</label>
                            <select class="form-control kt-select2" name="route_id" id="route_id"
                                v-model="model.route_id" @focus="$parent.clearErrorMsg($event)">
                                <option value="" selected>Todos</option>
                                <option v-for="client_route in client_routes" v-bind:key="client_route.id"
                                    :value="client_route.id">{{
                                        client_route.short_name }}</option>
                            </select>
                            <div id="route_id-error" class="error invalid-feedback"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="kt-portlet__foot">
                <div class="kt-form__actions">
                    <div class="row">
                        <div class="col-12 kt-align-right">
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
        current_date: {
            type: String,
            default: ''
        },
        first_date: {
            type: String,
            default: ''
        },
        client_routes: {
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
                initial_date: this.first_date,
                final_date: this.current_date,
                state: '',
                route_id: '',
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
