<template>
    <!--begin::Modal-->
    <div class="modal fade" id="detail" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <!--begin::Form-->
                <form class="kt-form" @submit.prevent="formController(url_update, $event)">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">{{ model.guia }} ::: {{ model.guia_fisica }} |
                            Chofer : {{
                                model.account_name }}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="kt-portlet__body">
                            <div class="row">

                                <input type="hidden" name="guide_id" id="guide_id" v-model="model.guide_id">

                                <div class="col-lg-3">
                                    <div class="form-group">
                                        <label class="form-control-label">SCOP:</label>
                                        <input type="text" class="form-control" name="scop_number" id="scop_number"
                                            v-model="model.scop_number" @focus="$parent.clearErrorMsg($event)">
                                        <div id="scop_number-error" class="error invalid-feedback"></div>
                                    </div>
                                </div>

                                <div class="col-lg-3">
                                    <div class="form-group">
                                        <label class="form-control-label">Cerrar:</label>
                                        <div class="kt-checkbox-inline" style="margin-top:10px;">
                                            <label class="kt-checkbox">
                                                <input type="checkbox" name="finished" v-model="model.finished">
                                                Marcar como Cerrado
                                                <span></span>
                                            </label>
                                        </div>
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
                <div style="padding: 5%;">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Artículo</th>
                                <th>Cantidad Despachado</th>
                                <th>Cantidad Retornado</th>
                                <th>Cantidad Liquidado</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="lista in detail" :key="lista.detail_id">
                                <td>{{ lista.item }}</td>
                                <td>{{ lista.article_name }}</td>
                                <td>{{ lista.digit_amount }}</td>
                                <td>{{ lista.new_stock_return }}</td>
                                <td>{{ lista.cantidad }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
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
    },
    data() {
        return {
            model: '',
            detail: []
        }
    },
    computed: {
    },
    created() {
        EventBus.$on('detail', function (data) {
            this.model = '';
            this.detail = [];

            this.model = data.guide;
            this.detail = data.detail;
            $('#detail').modal('show');
        }.bind(this));
    },
    methods: {
        formController: function (url_update, event) {
            var vm = this;

            var target = $(event.target);
            var url_update = url_update;
            var fd = new FormData(event.target);

            EventBus.$emit('loading', true);

            // EventBus.$emit('loading', true);
            axios.post(url_update, fd, {
                headers: {
                    'Content-type': 'application/x-www-form-urlencoded',
                }
            }).then(response => {
                $('#detail').modal('hide');
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
