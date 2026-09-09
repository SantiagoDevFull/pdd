<template>
    <!--begin::Modal-->
    <div class="modal fade" id="modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <!--begin::Form-->
                <form class="kt-form" @submit.prevent="formController(url, $event)">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">{{ this.text }}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="kt-portlet__body">
                            <div class="row">
                                <input type="hidden" name="id" id="id" v-model="model.id">
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label class="form-control-label">Artículo:</label>
                                        <select class="form-control" name="article_id" id="article_id"
                                            v-model="model.article_id" @focus="$parent.clearErrorMsg($event)">
                                            <option value="" selected disabled>Seleccionar</option>
                                            <option v-for="article in articles" :value="article.id"
                                                v-bind:key="article.id">{{
                                                    article.name }}</option>
                                        </select>
                                        <div id="article_id-error" class="error invalid-feedback"></div>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label class="form-control-label">Precio (Presentación de 10 KG):</label>
                                        <input type="text" class="form-control" name="price" id="price"
                                            v-model="model.price" @focus="$parent.clearErrorMsg($event)">
                                        <div id="price-error" class="error invalid-feedback"></div>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label class="form-control-label">Fecha Inicio:</label>
                                        <datetime v-model="model.initial_date" placeholder="Selecciona una Fecha"
                                            :format="'dd-LL-yyyy'" input-id="initial_date" name="initial_date"
                                            value-zone="America/Lima" zone="America/Lima" class="form-control"
                                            :max-datetime="this.max_datetime" @focus="$parent.clearErrorMsg($event)">
                                        </datetime>
                                        <div id="initial_date-error" class="error invalid-feedback"></div>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label class="form-control-label">Fecha Final:</label>
                                        <datetime v-model="model.final_date" placeholder="Selecciona una Fecha"
                                            :format="'dd-LL-yyyy'" input-id="final_date" name="final_date"
                                            value-zone="America/Lima" zone="America/Lima" class="form-control"
                                            :max-datetime="this.max_datetime" @focus="$parent.clearErrorMsg($event)">
                                        </datetime>
                                        <div id="final_date-error" class="error invalid-feedback"></div>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label class="form-control-label">Estado:</label>
                                        <select class="form-control" name="state" id="state" v-model="model.state"
                                            @focus="$parent.clearErrorMsg($event)">
                                            <option value="" selected disabled>Seleccionar</option>
                                            <option value="1">Activo</option>
                                            <option value="0">Inactivo</option>
                                        </select>
                                        <div id="state-error" class="error invalid-feedback"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success" id="update_user">
                            {{ this.text }}
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
        max_datetime: {
            type: String,
            default: ''
        },
        articles: {
            type: Array,
            default: ''
        },
    },
    data() {
        return {
            model: {
                id: '',
                article_id: '',
                price: '',
                initial_date: '',
                final_date: '',
                state: '',
            },
            text: '',

        }
    },
    watch: {

    },
    computed: {

    },
    created() {

        EventBus.$on('edit_modal', function (model) {
            this.model = model;
            this.text = 'Actualizar';
            $('#modal').modal('show');
        }.bind(this));

        EventBus.$on('clear_modal', function () {
            this.model.id = '';
            this.model.article_id = '';
            this.model.price = '';
            this.model.initial_date = '';
            this.model.final_date = '';
            this.model.state = '';
        }.bind(this));

        EventBus.$on('create_modal', function () {
            EventBus.$emit('clear_modal');
            this.text = 'Registrar';
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