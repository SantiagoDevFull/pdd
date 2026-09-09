<template>
    <!--begin::Portlet-->
    <div class="kt-portlet">
        <div class="kt-portlet__head">
            <div class="kt-portlet__head-label">
                <h3 class="kt-portlet__head-title">
                    Crear Andromeda
                </h3>
            </div>
        </div>

        <!--begin::Form-->
        <form class="kt-form" @submit.prevent="formController(url, $event)">
            <div class="kt-portlet__body">
                <div class="row">
                    <div class="col-lg-2">
                        <div class="form-group">
                            <label class="form-control-label">Tipo de Documento:</label>
                            <select class="form-control" name="document_type_id" id="document_type_id"
                                v-model="model.document_type_id" @focus="$parent.clearErrorMsg($event)">
                                <option value="" selected disabled>Seleccionar</option>
                                <option v-for="document_type_id in document_types" :value="document_type_id.id"
                                    v-bind:key="document_type_id.id">{{ document_type_id.name }}</option>
                            </select>
                            <div id="document_type_id-error" class="error invalid-feedback"></div>
                        </div>
                    </div>
                    <div class="col-lg-2">
                        <div class="form-group">
                            <label class="form-control-label">N° Documento:</label>
                            <input type="text" class="form-control" name="document_number" id="document_number"
                                placeholder="00000000" v-model="model.document_number"
                                @focus="$parent.clearErrorMsg($event)">
                            <div id="document_number-error" class="error invalid-feedback"></div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="form-group">
                            <label class="form-control-label">Nombres completos:</label>
                            <input type="text" class="form-control" name="last_name" id="last_name"
                                v-model="model.last_name" @focus="$parent.clearErrorMsg($event)">
                            <div id="last_name-error" class="error invalid-feedback"></div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="form-group">
                            <label class="form-control-label">Apellidos completos:</label>
                            <input type="text" class="form-control" name="first_name" id="first_name"
                                v-model="model.first_name" @focus="$parent.clearErrorMsg($event)">
                            <div id="first_name-error" class="error invalid-feedback"></div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-2">
                        <div class="form-group">
                            <label class="form-control-label">Tipo de Empleado:</label>
                            <select class="form-control" name="employee_type_id" id="employee_type_id"
                                v-model="model.employee_type_id" @focus="$parent.clearErrorMsg($event)">
                                <option value="" selected disabled>Seleccionar</option>
                                <option v-for="employee in employee_types" :value="employee.id"
                                    v-bind:key="employee.id">{{ employee.name }}</option>
                            </select>
                            <div id="employee_type_id-error" class="error invalid-feedback"></div>
                        </div>
                    </div>
                    <div class="col-lg-2">
                        <div class="form-group">
                            <label class="form-control-label">Compañía:</label>
                            <select class="form-control" name="company_id" id="company_id" v-model="model.company_id"
                                @focus="$parent.clearErrorMsg($event)">
                                <option value="" selected disabled>Seleccionar</option>
                                <option v-for="company in companies" :value="company.id" v-bind:key="company.id">{{
                                    company.name }}</option>
                            </select>
                            <div id="company_id-error" class="error invalid-feedback"></div>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="form-group">
                            <label class="form-control-label">Área:</label>
                            <select class="form-control" name="area_id" id="area_id" v-model="model.area_id"
                                @focus="$parent.clearErrorMsg($event)">
                                <option value="">Seleccionar</option>
                                <option v-for="area in areas" :value="area.id" v-bind:key="area.id">{{ area.name }}
                                </option>
                            </select>
                            <div id="area_id-error" class="error invalid-feedback"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="kt-portlet__foot">
                <div class="kt-form__actions">
                    <div class="row">
                        <div class="col-lg-6">
                            <button type="submit" class="btn btn-primary">Crear</button>
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
        document_types: {
            type: Array,
            default: ''
        },
        employee_types: {
            type: Array,
            default: ''
        },
        companies: {
            type: Array,
            default: ''
        },
        area: {
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
                document_type_id: '',
                document_number: '',
                first_name: '',
                last_name: '',
                employee_type_id: '',
                company_id: '',
                area_id: '',
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
                this.$parent.alertMsg(response.data);

                this.model.document_type_id = '';
                this.model.document_number = '';
                this.model.first_name = '';
                this.model.last_name = '';
                this.model.employee_type_id = '';

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
    }
};
</script>