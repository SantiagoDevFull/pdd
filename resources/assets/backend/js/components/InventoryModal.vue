<template>
    <!--begin::Modal-->
    <div class="modal fade" id="modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <!--begin::Form-->
                <form class="kt-form" @submit.prevent="formController(url, $event)">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">{{ this.button_text }} artículo</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="kt-portlet__body">
                            <div class="row">
                                <input type="hidden" name="id" id="id" v-model="model.id">
                                <input type="hidden" name="company_id" id="company_id" v-model="model.company_id">
                                <input type="hidden" name="creation_date" id="creation_date"
                                    v-model="model.creation_date">
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label class="form-control-label">Total contado:</label>
                                        <input type="tel" class="form-control" name="found_stock_good"
                                            id="found_stock_good" placeholder="0.00" v-model="model.found_stock_good"
                                            @focus="$parent.clearErrorMsg($event)">
                                        <div id="found_stock_good-error" class="error invalid-feedback"></div>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label class="form-control-label">Observaciones:</label>
                                        <textarea class="form-control" name="observations" id="observations"
                                            placeholder="" size="20" v-model="model.observations"
                                            @focus="$parent.clearErrorMsg($event)"></textarea>
                                        <div id="observations-error" class="error invalid-feedback"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success" id="add_article_2">
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
    },
    data() {
        return {
            model: {
                id: '',
                company_id: '',
                creation_date: '',
                found_stock_good: '',
                observations: '',
            },
            button_text: '',
        }
    },
    watch: {

    },
    computed: {

    },
    created() {

        EventBus.$on('edit_modal', function (model) {
            this.model.id = model.id;
            this.model.found_stock_good = model.found_stock_good;
            this.model.observations = model.observations;
            this.button_text = 'Actualizar';
            $('#modal').modal('show');
            EventBus.$emit('loading', false);

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
                EventBus.$emit('loading', false);
                $('#modal').modal('hide');
                this.$parent.alertMsg(response.data);

                this.model.id = '';
                this.model.found_stock_good = '';
                this.model.observations = '';

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
                        let p = c_target.parent().find('#' + i);
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