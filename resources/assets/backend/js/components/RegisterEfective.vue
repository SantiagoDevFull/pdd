<template>
    <!--begin::Portlet-->
    <div class="kt-portlet">
        <div class="kt-portlet__head">
            <div class="kt-portlet__head-label">
                <h3 class="kt-portlet__head-title">
                    Crear
                </h3>
            </div>
        </div>

        <!--begin::Form-->
        <form class="kt-form" @submit.prevent="formController(url, $event)">
            <div class="kt-portlet__body">
                <div class="row">
                    <div class="col-lg-3">
                        <div class="form-group">
                            <label class="form-control-label">Recepción de Efectivo:</label>
                            <datetime v-model="model.bank_date" placeholder="Selecciona una Fecha"
                                :format="'dd-LL-yyyy'" input-id="bank_date" name="bank_date" value-zone="America/Lima"
                                zone="America/Lima" class="form-control" :min-datetime="this.min_datetime"
                                :max-datetime="this.max_datetime" @focus="$parent.clearErrorMsg($event)">
                            </datetime>
                            <div id="bank_date-error" class="error invalid-feedback"></div>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div class="form-group">
                            <label class="form-control-label">N° Parte:</label>
                            <select class="form-control kt-select2" name="warehouse_movement_id"
                                id="warehouse_movement_id" v-model="model.warehouse_movement_id"
                                @focus="$parent.clearErrorMsg($event)">
                                <option value="">Seleccionar</option>
                            </select>
                            <div id="warehouse_movement_id-error" class="error invalid-feedback"></div>
                        </div>
                    </div>

                    <div class="col-lg-3">
                        <div class="form-group">
                            <label class="form-control-label">Saldo total:</label>
                            <input type="text" class="form-control" name="total" id="total" v-model="model.total"
                                @focus="$parent.clearErrorMsg($event)">
                            <div id="total-error" class="error invalid-feedback"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="kt-portlet__foot">
                <div class="kt-form__actions">
                    <div class="row">
                        <div class="col-12 kt-align-right">
                            <button type="submit" class="btn btn-primary">Guardar</button>
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
import Datetime from 'vue-datetime';
// You need a specific loader for CSS files
import 'vue-datetime/dist/vue-datetime.css';

Vue.use(Datetime);

export default {
    name: 'RegisterEfective',
    props: {
        url: {
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
                bank_date: this.max_datetime,
                warehouse_movement_id: '',
                total: '',
            },
        }
    },
    created() {
        EventBus.$on('clear_form', function () {
            this.model.bank_date = this.max_datetime;
            this.model.warehouse_movement_id = '';
            this.model.total = '';

            $('#warehouse_movement_id').val(null).trigger('change');
        }.bind(this));
    },
    mounted() {
        this.newSelect2();
    },

    watch: {

    },
    computed: {

    },
    methods: {
        newSelect2: function () {
            let vm = this;
            let token = document.head.querySelector('meta[name="csrf-token"]').content;
            $("#warehouse_movement_id").select2({
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
                vm.model.warehouse_movement_id = parseInt(selected_element.val());
            }).on('select2:unselect', function (e) {
                vm.model.warehouse_movement_id = '';
            });

        },
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

                this.$parent.alertMsg(response.data);

                EventBus.$emit('clear_form');
                EventBus.$emit('refresh_table');

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
        cleanFields: function () {

            this.model.bank_date = this.max_datetime;
            this.model.total = null;
            this.model.warehouse_movement_id = '';
            this.warehouse_movements = [];

        },
    }
};
</script>
