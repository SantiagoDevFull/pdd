<template>
    <!--begin::Modal-->
    <div class="modal fade" id="modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <!--begin::Form-->
                <form class="kt-form" @submit.prevent="formController(url, $event)">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">{{ this.button_text }} Almacén</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="kt-portlet__body">
                            <div class="row">
                                <input type="hidden" name="id" id="id" v-model="model.id">
                                <input type="hidden" name="company_id" id="company_id" v-model="model.company_id">

                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label class="form-control-label">Almacén:</label>
                                        <input type="text" class="form-control" name="name" id="name"
                                            v-model="model.name" @focus="$parent.clearErrorMsg($event)">
                                        <div id="name-error" class="error invalid-feedback"></div>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label class="form-control-label">Nombre Corto:</label>
                                        <input type="text" class="form-control" name="short_name" id="short_name"
                                            v-model="model.short_name" @focus="$parent.clearErrorMsg($event)">
                                        <div id="short_name-error" class="error invalid-feedback"></div>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label class="form-control-label">Proveedor:</label>
                                        <input type="text" class="form-control" name="business_name" id="business_name"
                                            v-model="model.business_name" @focus="$parent.clearErrorMsg($event)">
                                        <div id="business_name-error" class="error invalid-feedback"></div>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label class="form-control-label">RUC:</label>
                                        <input type="text" class="form-control" name="ruc" id="ruc" v-model="model.ruc"
                                            @focus="$parent.clearErrorMsg($event)">
                                        <div id="ruc-error" class="error invalid-feedback"></div>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label class="form-control-label">Registro Uno:</label>
                                        <input type="text" class="form-control" name="agent_uno" id="agent_uno"
                                            v-model="model.agent_uno" @focus="$parent.clearErrorMsg($event)">
                                        <div id="agent_uno-error" class="error invalid-feedback"></div>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label class="form-control-label">Agente:</label>
                                        <select class="form-control" name="agent_id" id="agent_id"
                                            v-model="model.agent_id">
                                            <option value="" selected disabled>Seleccionar</option>
                                            <option v-for="agent in agents" :value="agent.id" v-bind:key="agent.id">{{
                                                agent.activity }} - {{ agent.name }}</option>
                                        </select>
                                        <div id="agent_id-error" class="error invalid-feedback"></div>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label class="form-control-label">Registro Final:</label>
                                        <input type="text" class="form-control" name="agent_dos" id="agent_dos"
                                            v-model="model.agent_dos" @focus="$parent.clearErrorMsg($event)">
                                        <div id="agent_dos-error" class="error invalid-feedback"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">
                            {{ button_text }}
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
        agents: {
            type: Array,
            default: ''
        },
    },
    data() {
        return {
            model: {
                id: '',
                company_id: '',
                name: '',
                short_name: '',
                business_name: '',
                ruc: '',
                agent_id: '',
                agent_uno: '',
                agent_dos: '',
            },
            button_text: '',
        }
    },
    watch: {

    },
    computed: {

    },
    created() {
        EventBus.$on('create_modal', function (company_id) {
            this.button_text = 'Crear';

            this.model.id = '';
            this.model.company_id = company_id;
            this.model.name = '';
            this.model.short_name = '';
            this.model.business_name = '';
            this.model.ruc = '';
            this.model.agent_id = '';
            this.model.agent_uno = '';
            this.model.agent_dos = '';

            $('#modal').modal('show');
        }.bind(this));

        EventBus.$on('edit_modal', function (model) {
            this.model = model;
            this.button_text = 'Actualizar';
            $('#modal').modal('show');
        }.bind(this));
    },
    mounted() {
        this.newSelect2();
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
                $('#modal').modal('hide');
                this.$parent.alertMsg(response.data);

                this.model.id = '';
                this.model.company_id = company_id;
                this.model.name = '';
                this.model.short_name = '';
                this.model.business_name = '';
                this.model.ruc = '';
                this.model.agent_id = '';
                this.model.agent_uno = '';
                this.model.agent_dos = '';

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