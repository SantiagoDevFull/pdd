<template>
    <!--begin::Modal-->
    <div class="modal fade" id="modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <!--begin::Form-->
                <form class="kt-form" @submit.prevent="formController(url_update, $event)">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Actualizar Efectivo</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="kt-portlet__body">
                            <div class="row">
                                <input type="hidden" name="id" id="id" v-model="model.id">

                                <div class="col-lg-3">
                                    <div class="form-group">
                                        <label class="form-control-label">Recepción de Efectivo:</label>
                                        <datetime v-model="model.bank_date" placeholder="Selecciona una Fecha"
                                            :format="'dd-LL-yyyy'" input-id="bank_date" name="bank_date"
                                            value-zone="America/Lima" zone="America/Lima" class="form-control"
                                            :min-datetime="this.min_datetime" :max-datetime="this.max_datetime"
                                            @focus="$parent.clearErrorMsg($event)">
                                        </datetime>
                                        <div id="bank_date-error" class="error invalid-feedback"></div>
                                    </div>
                                </div>

                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label class="form-control-label">N° Parte:</label>
                                        <select class="form-control kt-select2" name="warehouse_movement_id_2"
                                            id="warehouse_movement_id_2" v-model="model.warehouse_movement_id_2"
                                            @focus="$parent.clearErrorMsg($event)">
                                            <option value="">Seleccionar</option>
                                        </select>
                                        <div id="warehouse_movement_id_2-error" class="error invalid-feedback"></div>
                                    </div>
                                </div>

                                <div class="col-lg-3">
                                    <div class="form-group">
                                        <label class="form-control-label">Saldo total:</label>
                                        <input type="text" class="form-control" name="total" id="total"
                                            v-model="model.total" @focus="$parent.clearErrorMsg($event)">
                                        <div id="total-error" class="error invalid-feedback"></div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success" id="update_user">
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
        min_datetime: {
            type: String,
            default: ''
        },
        max_datetime: {
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
                id: '',
                bank_date: this.max_datetime,
                warehouse_movement_id_2: '',
                total: '',
            },
        }
    },
    watch: {

    },
    computed: {

    },
    created() {
        EventBus.$on('edit_modal', function (data) {

            EventBus.$emit('clear_modal');
            this.model = data.detail;

            let option = new Option(data.warehouse_movement.text, data.warehouse_movement.id, true, true);
            $('#warehouse_movement_id_2').append(option).trigger('change');
            $('#warehouse_movement_id_2').trigger({
                type: 'select2:select',
                params: {
                    data: data.warehouse_movement
                }
            });

            $('#modal').modal('show');
        }.bind(this));

        EventBus.$on('clear_modal', function () {
            this.model.id = '';
            this.model.bank_date = this.max_datetime;
            this.model.warehouse_movement_id_2 = '';
            this.model.total = '';

            $('#warehouse_movement_id_2').val(null).trigger('change');
        }.bind(this));
    },
    mounted() {
        this.newSelect2();
    },
    methods: {
        newSelect2: function () {
            let vm = this;
            let token = document.head.querySelector('meta[name="csrf-token"]').content;
            $("#warehouse_movement_id_2").select2({
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
                    url: this.url_get_warehouse_movements,
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
                vm.model.warehouse_movement_id_2 = parseInt(selected_element.val());
            }).on('select2:unselect', function (e) {
                vm.model.warehouse_movement_id_2 = '';
            });

        },
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
                $('#modal').modal('hide');
                this.$parent.alertMsg(response.data);

                EventBus.$emit('clear_modal');
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
