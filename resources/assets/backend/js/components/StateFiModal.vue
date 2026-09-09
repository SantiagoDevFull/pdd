<template>
    <!--begin::Modal-->
    <div class="modal fade" id="modal_finanzas" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <!--begin::Form-->
                <form class="kt-form" @submit.prevent="formController(url_update, $event)">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">{{ this.condicion_pago }} {{
                            this.num_mov }}
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
                                <div class="col-lg-4" v-if="this.model.state_1 == 'APROBADO'">
                                    <div class="form-group">
                                        <label class="form-control-label">Medio de Pago:</label>
                                        <select class="form-control kt-select2" name="medio_pago" id="medio_pago"
                                            v-model="model.medio_pago" @focus="$parent.clearErrorMsg($event)">
                                            <option value="" selected disabled>Seleccionar</option>
                                            <option value="BANCO">BANCO</option>
                                            <option value="CAJA">CAJA</option>
                                        </select>
                                        <div id="medio_pago-error" class="error invalid-feedback"></div>
                                    </div>
                                </div>
                                <div class="col-lg-4" v-if="this.model.state_1 == 'APROBADO'">
                                    <div class="form-group">
                                        <label class="form-control-label">Importe:</label>
                                        <input type="text" class="form-control" name="import" id="import" readonly
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
                medio_pago: '',
                import: '',
                comentario: '',
            },
            num_mov: '',
            condicion_pago: '',
        }
    },
    watch: {


    },
    computed: {

    },
    created() {
        EventBus.$on('modal_finanzas', function (id, condicion_pago, num_mov, importe) {

            EventBus.$emit('clear_finanzas');
            this.model.id = id;
            this.model.import = importe;

            this.num_mov = num_mov
            this.condicion_pago = condicion_pago;
            $('#modal_finanzas').modal('show');

        }.bind(this));

        EventBus.$on('clear_finanzas', function () {
            this.model.id = '';
            this.model.state_1 = '';
            this.model.medio_pago = '';
            this.model.import = '';
            this.model.comentario = '';

            this.num_mov = '';
            this.condicion_pago = '';

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

                $('#modal_finanzas').modal('hide');
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