<template>
    <!--begin::Modal-->
    <div class="modal fade" id="modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <!--begin::Form-->
                <form class="kt-form" @submit.prevent="formController(url, $event)">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">{{ this.model.serie }} <span
                                class="text-danger">desde</span> {{ this.model.min_voucher }} <span
                                class="text-danger">hasta</span> {{
                                    this.model.max_voucher }} <span class="badge bg-danger">:::</span> {{
                                this.model.article_name }}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="kt-portlet__body">
                            <input type="hidden" v-model="model.issue_date" name="issue_date" id="issue_date">
                            <input type="hidden" v-model="model.serie" name="serie" id="serie">
                            <input type="hidden" v-model="model.min_voucher" name="min_voucher" id="min_voucher">
                            <input type="hidden" v-model="model.max_voucher" name="max_voucher" id="max_voucher">
                            <div class="row">
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label class="form-control-label">Precio KG</label>
                                        <input type="text" class="form-control" name="price_kg" id="price_kg"
                                            v-model="model.price_kg" @focus="$parent.clearErrorMsg($event)">
                                        <div id="price_kg-error" class="error invalid-feedback"></div>
                                    </div>
                                </div>


                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label class="form-control-label">Nueva Fecha:</label>
                                        <datetime v-model="model.terminal_date" placeholder="Selecciona una Fecha"
                                            :format="'dd-LL-yyyy'" name="terminal_date" value-zone="America/Lima"
                                            zone="America/Lima" class="form-control" :max-datetime="current_date"
                                            @focus="$parent.clearErrorMsg($event)">
                                        </datetime>
                                        <div id="terminal_date-error" class="error invalid-feedback"></div>
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
        current_date: {
            type: String,
            default: ''
        },

    },
    data() {
        return {
            model: {
                serie: '',
                min_voucher: '',
                max_voucher: '',
                article_name: '',
                price_kg: '',
                terminal_date: ''
            }
        }
    },
    watch: {

    },
    computed: {

    },
    created() {

        EventBus.$on('edit_modal', function (serie, min_voucher, max_voucher, article_name, fecha_emision) {

            this.model.serie = '';
            this.model.min_voucher = '';
            this.model.max_voucher = '';
            this.model.issue_date = '';
            this.model.article_name = '';
            this.model.price_kg = '';
            this.model.terminal_date = '';

            this.model.serie = serie;
            this.model.min_voucher = min_voucher;
            this.model.max_voucher = max_voucher;
            this.model.issue_date = fecha_emision;
            this.model.article_name = article_name;

            $('#modal').modal('show');

        }.bind(this));
    },
    mounted() {

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

                $('#modal').modal('hide');

                this.model.serie = '';
                this.model.min_voucher = '';
                this.model.max_voucher = '';
                this.model.issue_date = '';
                this.model.article_name = '';
                this.model.price_kg = '';
                this.model.terminal_date = '';


                EventBus.$emit('refresh_table');
                this.$parent.alertMsg(response.data);

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