<template>
    <!--begin::Modal-->
    <div class="modal fade" id="modal_tesoreria" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
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
                                        <select class="form-control kt-select2" name="state_2" id="state_2"
                                            v-model="model.state_2" @focus="$parent.clearErrorMsg($event)">
                                            <option value="" selected disabled>Seleccionar</option>
                                            <option value="PAGADO">PAGADO</option>
                                            <option value="RECHAZADO">RECHAZADO</option>
                                            <option value="PROGRAMADO">PROGRAMADO</option>
                                        </select>
                                        <div id="state_2-error" class="error invalid-feedback"></div>
                                    </div>
                                </div>
                                <div class="col-lg-4"
                                    v-if="this.model.state_2 == 'PAGADO' || this.model.state_2 == 'PROGRAMADO'">
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
                                <div class="col-lg-4" v-if="this.model.medio_pago === 'BANCO'">
                                    <div class="form-group">
                                        <label class="form-control-label">Banco:</label>
                                        <select class="form-control kt-select2" name="bank_account_id"
                                            id="bank_account_id" v-model="model.bank_account_id"
                                            @focus="$parent.clearErrorMsg($event)">
                                            <option value="" selected disabled>Seleccionar</option>
                                            <option v-for="bank_account in bank_accounts" :value="bank_account.id"
                                                v-bind:key="bank_account.id">
                                                {{ bank_account.bank_name }} - {{ bank_account.account_number }}
                                            </option>
                                            <div id="bank_account_id-error" class="error invalid-feedback"></div>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-4"
                                    v-if="this.model.state_2 == 'PAGADO' || this.model.state_2 == 'PROGRAMADO'">
                                    <div class="form-group">
                                        <label class="form-control-label">Importe:</label>
                                        <input type="text" class="form-control" name="import" id="import" readonly
                                            v-model="model.import" @focus="$parent.clearErrorMsg($event)">
                                        <div id="import-error" class="error invalid-feedback"></div>
                                    </div>
                                </div>

                                <div class="col-lg-3"
                                    v-if="this.model.state_2 == 'PAGADO' || this.model.state_2 == 'PROGRAMADO'">
                                    <div class="form-group">
                                        <label class="form-control-label">Fecha de Pago:</label>
                                        <datetime v-model="model.fecha_pago" placeholder="Selecciona una Fecha"
                                            :format="'dd-LL-yyyy'" name="fecha_pago" value-zone="America/Lima"
                                            zone="America/Lima" :min-datetime="current_date" class="form-control"
                                            @focus="$parent.clearErrorMsg($event)">
                                        </datetime>
                                        <div id="fecha_pago-error" class="error invalid-feedback"></div>
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
        bank_accounts: {
            type: Array,
            default: ''
        },
        current_date: {
            type: String,
            default: ''
        }
    },
    data() {
        return {
            model: {
                id: '',
                state_2: '',
                medio_pago: '',
                bank_account_id: '',
                fecha_pago: '',
                import: '',
                comentario: '',
            },
            num_mov: '',
            condicion_pago: ''
        }
    },
    watch: {

    },
    computed: {

    },
    created() {
        EventBus.$on('modal_tesoreria', function (id, condicion_pago, num_mov, importe) {

            EventBus.$emit('clear_tesoreria');
            this.model.id = id;
            this.model.import = importe;
            this.num_mov = num_mov
            this.condicion_pago = condicion_pago;
            $('#modal_tesoreria').modal('show');

        }.bind(this));

        EventBus.$on('clear_tesoreria', function () {
            this.model.id = '';
            this.model.state_2 = '';
            this.model.medio_pago = '';
            this.model.bank_account_id = '';
            this.model.fecha_pago = '';
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

                $('#modal_tesoreria').modal('hide');
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