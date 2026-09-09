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
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label class="form-control-label">Compañía:</label>
                                        <select class="form-control" name="company_id" id="company_id"
                                            v-model="model.company_id" @focus="$parent.clearErrorMsg($event)">
                                            <option value="" selected disabled>Seleccionar</option>
                                            <option v-for="company in companies" :value="company.id"
                                                v-bind:key="company.id">{{ company.name }}</option>
                                        </select>
                                        <div id="company_id-error" class="error invalid-feedback"></div>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label class="form-control-label">Placa:</label>
                                        <input type="text" class="form-control" name="plate" id="plate"
                                            placeholder="xxx-xxx" v-model="model.plate"
                                            @focus="$parent.clearErrorMsg($event)">
                                        <div id="plate-error" class="error invalid-feedback"></div>
                                    </div>
                                </div>
                                <div class="col-lg-6" v-if="model.company_id == 6">
                                    <div class="form-group">
                                        <label class="form-control-label">Cliente:</label>
                                        <input type="text" class="form-control" name="client_name" id="client_name"
                                            v-model="model.client_name" @focus="$parent.clearErrorMsg($event)">
                                        <div id="client_name-error" class="error invalid-feedback"></div>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label class="form-control-label">Capacidad Máxima en KILOS:</label>
                                        <input type="text" class="form-control" name="capacity" id="capacity"
                                            v-model="model.capacity" @focus="$parent.clearErrorMsg($event)">
                                        <div id="capacity-error" class="error invalid-feedback"></div>
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
        companies: {
            type: Array,
            default: ''
        },
    },
    data() {
        return {
            model: {
                company_id: '',
                plate: '',
                company_id: '',
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