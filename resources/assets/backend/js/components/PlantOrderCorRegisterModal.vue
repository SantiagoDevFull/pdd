<template>
    <!--begin::Modal-->
    <div class="modal fade" id="modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <!--begin::Form-->
                <form class="kt-form" @submit.prevent="formController(url_update, $event)">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">{{ this.model.serie }} - {{
                            this.model.correlative }}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="kt-portlet__body">
                            <div class="row">
                                <input type="hidden" name="id" id="id" v-model="model.id">
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label class="form-control-label">Placa:</label>
                                        <select class="form-control" name="vehicle_id" id="vehicle_id"
                                            v-model="model.vehicle_id" @focus="$parent.clearErrorMsg($event)">
                                            <option value="" selected disabled>Seleccionar</option>
                                            <option v-for="vehicle in vehicles" :value="vehicle.id"
                                                v-bind:key="vehicle.id">{{
                                                    vehicle.plate }} -------------- {{ vehicle.company_name }}</option>
                                        </select>
                                        <div id="vehicle_id-error" class="error invalid-feedback"></div>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label class="form-control-label">Chofer:</label>
                                        <select class="form-control kt-select2" name="employee_id_2" id="employee_id_2"
                                            v-model="model.employee_id_2" @focus="$parent.clearErrorMsg($event)">
                                            <option value="">Seleccionar</option>
                                        </select>
                                        <div id="employee_id_2-error" class="error invalid-feedback"></div>
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
        url_get_employees: {
            type: String,
            default: ''
        },
        url_update: {
            type: String,
            default: ''
        },
        vehicles: {
            type: Array,
            default: ''
        },
        url_get_select2: {
            type: String,
            default: ''
        }
    },
    data() {
        return {
            model: {
                id: '',
                vehicle_id: '',
                employee_id_2: '',
            },
        }
    },
    watch: {

    },
    computed: {

    },
    created() {
        EventBus.$on('edit_modal', function (model) {
            this.model = model;
            this.model.employee_id_2 = model.employee_id;

            axios.post(this.url_get_select2, {
                employee_id: this.model.employee_id_2,
            }).then(response => {

                let option = new Option(response.data.text, response.data.id, true, true);
                $('#employee_id_2').append(option).trigger('change');
                $('#employee_id_2').trigger({
                    type: 'select2:select',
                    params: {
                        data: response.data
                    }
                });

                EventBus.$emit('loading', false);
                $('#modal').modal('show');
                console.log(response);
            }).catch(error => {
                console.log(error.response);
                console.log(error.response);
            });

        }.bind(this));
        EventBus.$on('clear_modal', function () {
            this.model.id = '';
            this.model.vehicle_id = '';
            this.model.employee_id_2 = '';
        }.bind(this));
    },
    mounted() {
        this.newSelect2();
    },
    methods: {
        newSelect2: function () {

            let vm = this;
            let token = document.head.querySelector('meta[name="csrf-token"]').content;
            $("#employee_id_2").select2({
                placeholder: "Buscar",
                allowClear: true,
                language: {
                    noResults: () => 'No hay resultados',
                    searching: () => 'Buscando...',
                    inputTooShort: () => 'Ingresa 1 o más caracteres',
                    errorLoading: () => 'No se pudo cargar la información'
                },
                ajax: {
                    url: this.url_get_employees,
                    dataType: 'json',
                    delay: 250,
                    type: 'POST',
                    data: params => ({
                        q: params.term,
                        _token: token,
                    }),
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
                minimumInputLength: 1,
            }).on('select2:select', function (e) {
                vm.model.employee_id_2 = parseInt($(e.currentTarget).val());
            }).on('select2:unselect', function () {
                vm.model.employee_id_2 = '';
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
                EventBus.$emit('refresh_table');
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