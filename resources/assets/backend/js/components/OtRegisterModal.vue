<template>
    <!--begin::Modal-->
    <div class="modal fade" id="cotizacion-register-modal" tabindex="-1" role="dialog"
        aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <!--begin::Form-->
                <form class="kt-form" @submit.prevent="sendForm(url, $event)">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Agregar repuesto</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="kt-portlet__body">
                            <div class="row">
                                <div class="col-3">
                                    <div class="form-group">
                                        <label class="form-control-label">Tipo de Repuesto:</label>
                                        <select class="form-control kt-select2" name="article_type" id="article_type"
                                            @change="Unselect()" v-model="model.article_type"
                                            @focus="$parent.clearErrorMsg($event)">
                                            <option disabled value="">Seleccionar</option>
                                            <option value="1">Bienes</option>
                                            <option value="2">Servicios</option>
                                        </select>
                                        <div id="article_type-error" class="error invalid-feedback"></div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-group">
                                        <label class="form-control-label">Repuesto:</label>
                                        <select class="form-control kt-select2" name="article_id" id="article_id"
                                            v-model="model.article_id" @focus="$parent.clearErrorMsg($event)">
                                            <option disabled value="">Seleccionar</option>
                                            <option v-for="article in filterArticles" :value="article.id"
                                                v-bind:key="article.id">
                                                {{ article.full_name }}
                                            </option>
                                        </select>
                                        <div id="article_id-error" class="error invalid-feedback"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success" id="add_article">
                            <i class="fa fa-plus"></i> Agregar artículo
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
        url_list_article: {
            type: String,
            default: '',
        },
        url: {
            type: String,
            default: '',
        }
    },
    data() {
        return {
            model: {
                article_type: '',
                article_id: '',
            },
            articles: [],
            article_list: []
        }
    },
    mounted() {
        this.newSelect2();
    },
    watch: {
        'model.article_type': function (val) {
            if (val != '') {
                EventBus.$emit('loading', true);

                axios.post(this.url_list_article, {
                    article_type: this.model.article_type
                }).then(response => {
                    // console.log(response);
                    this.articles = response.data;

                    EventBus.$emit('loading', false);
                }).catch(error => {
                    console.log(error);
                    console.log(error.response)
                });
            } else {

            }
        }
    },
    computed: {
        filterArticles() {
            this.articles.map(element => {
                element.full_name = element.name;
            });
            return this.articles;
        }
    },
    created() {
        EventBus.$on('openModal', function () {
            $('#cotizacion-register-modal').modal('show');
        }.bind(this));

        EventBus.$on('add_article_id', function (id) {
            this.article_list.push(id);
        }.bind(this));

        EventBus.$on('cotizacion_register_modal_hide', function () {
            this.article_type = '';
            this.model.article_id = '';
            $('#article_id').val(null).trigger('change');
            $('#cotizacion-register-modal').modal('hide');

        }.bind(this));

        EventBus.$on('remove_article_id', function (id) {
            let index = this.article_list.findIndex((element) => element == id);
            this.article_list.splice(index, 1);
            console.log(index, id);
        }.bind(this));
    },
    methods: {
        Unselect: function () {
            this.articles = [];
            this.model.article_id = '';
            $('#article_id').val(null).trigger('change');
        },
        sendForm: function (url, event) {

            var target = $(event.target);
            var url = url;
            var fd = new FormData(event.target);

            EventBus.$emit('loading', true);
            axios.post(url, fd, {
                headers: {
                    'Content-type': 'application/x-www-form-urlencoded',
                }
            }).then(response => {
                EventBus.$emit('sendForm', this.model);
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
        newSelect2: function () {
            let vm = this;

            $('#article_id').select2({
                placeholder: "Selecciona un Artículo",
            }).on('select2:select', function (e) {
                var selected_element = $(e.currentTarget);
                vm.model.article_id = parseInt(selected_element.val());

                var current_article = vm.articles.find(article => article.id === vm.model.article_id);

                vm.model.article_id = current_article.id;
            }).on('select2:unselect', function (e) {
                vm.model.article_id = '';
            });
        },
    },
}

</script>