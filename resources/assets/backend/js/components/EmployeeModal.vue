<template>
    <!--begin::Modal-->
    <div class="modal fade" id="modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <!--begin::Form-->
                <form class="kt-form" @submit.prevent="formController(url_update, $event)">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Actualizar Chofer</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="kt-portlet__body">
                            <div class="row">
                                <input type="hidden" name="id" id="id" v-model="model.id">
                                <div class="col-lg-3">
                                    <div class="form-group">
                                        <label class="form-control-label">Tipo de Documento:</label>
                                        <select class="form-control" name="document_type_id" id="document_type_id"
                                            v-model="model.document_type_id" @focus="$parent.clearErrorMsg($event)">
                                            <option value="" selected disabled>Seleccionar</option>
                                            <option v-for="document_type_id in document_types"
                                                :value="document_type_id.id" v-bind:key="document_type_id.id">{{
                    document_type_id.name }}</option>
                                        </select>
                                        <div id="document_type_id-error" class="error invalid-feedback"></div>
                                    </div>
                                </div>
                                <div class="col-lg-3">
                                    <div class="form-group">
                                        <label class="form-control-label">N° Documento:</label>
                                        <input type="text" class="form-control" name="document_number"
                                            id="document_number" placeholder="00000000" v-model="model.document_number"
                                            @focus="$parent.clearErrorMsg($event)">
                                        <div id="document_number-error" class="error invalid-feedback"></div>
                                    </div>
                                </div>
                                <div class="col-lg-3">
                                    <div class="form-group">
                                        <label class="form-control-label">Apellidos completos:</label>
                                        <input type="text" class="form-control" name="first_name" id="first_name"
                                            v-model="model.first_name" @focus="$parent.clearErrorMsg($event)">
                                        <div id="first_name-error" class="error invalid-feedback"></div>
                                    </div>
                                </div>
                                <div class="col-lg-3">
                                    <div class="form-group">
                                        <label class="form-control-label">Nombres completos:</label>
                                        <input type="text" class="form-control" name="last_name" id="last_name"
                                            v-model="model.last_name" @focus="$parent.clearErrorMsg($event)">
                                        <div id="last_name-error" class="error invalid-feedback"></div>
                                    </div>
                                </div>
                                <div class="col-lg-3">
                                    <div class="form-group">
                                        <label class="form-control-label">Tipo:</label>
                                        <select class="form-control" name="employee_type_id" id="employee_type_id"
                                            v-model="model.employee_type_id" @focus="$parent.clearErrorMsg($event)">
                                            <option value="" selected disabled>Seleccionar</option>
                                            <option v-for="employee in employee_types" :value="employee.id"
                                                v-bind:key="employee.id">{{ employee.name }}</option>
                                        </select>
                                        <div id="employee_type_id-error" class="error invalid-feedback"></div>
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
        url: {
            type: String,
            default: ''
        },
        url_update: {
            type: String,
            default: ''
        },
        document_types: {
            type: Array,
            default: ''
        },
        employee_types: {
            type: Array,
            default: ''
        },
    },
    data() {
        return {
            model: {
                id: '',
                document_type_id: '',
                document_number: '',
                first_name: '',
                last_name: '',
                employee_type_id: '',
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
            $('#modal').modal('show');
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

            // EventBus.$emit('loading', true);
            axios.post(url, fd, {
                headers: {
                    'Content-type': 'application/x-www-form-urlencoded',
                }
            }).then(response => {
                EventBus.$emit('loading', false);
                $('#modal').modal('hide');
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