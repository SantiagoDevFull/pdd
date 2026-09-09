<template>
    <!--begin::Modal-->
    <div class="modal fade" id="article-order-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <!--begin::Form-->
                <form class="kt-form" @submit.prevent="sendForm(url_validate_modal,$event)">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Agregar artículo</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="kt-portlet__body">
                            <div class="row">
                                <div class="col-8">
                                    <div class="form-group">
                                        <label class="form-control-label">Artículo:</label>
                                        <select class="form-control kt-select2" name="article" id="article" 
                                        v-model="model.article_id" @focus="$parent.clearErrorMsg($event)">
                                            <option disabled value="">Seleccionar</option>
                                            <option 
												v-for="article in filterArticles"
												:value="article.article_id"
												v-bind:key="article.article_id"
												>
												{{ article.warehouse_types_name }} - {{ article.article_name }}
											</option>
                                        </select>
                                        <div id="article-error" class="error invalid-feedback"></div>
                                    </div>
                                </div>
                                <div class="col-lg-2">
                                    <div class="form-group">
                                        <label class="form-control-label">Cantidad:</label>
                                        <input type="texto" class="form-control" name="quantity" id="quantity" placeholder="0" 
                                        v-model="model.quantity" @focus="$parent.clearErrorMsg($event)">
                                        <div id="quantity-error" class="error invalid-feedback"></div>
                                    </div>
                                </div>
                                <div class="col-lg-2">
                                    <div class="form-group">
                                        <label class="form-control-label">Precio unitario:</label>
                                        <input type="texto" class="form-control" name="price_unit" id="price_unit" placeholder="0" 
                                        v-model="model.price_unit" @focus="$parent.clearErrorMsg($event)">
                                        <div id="price_unit-error" class="error invalid-feedback"></div>
                                    </div>
                                </div>
                            </div> 
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success" id="add_article_2" disabled>
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
            url_validate_modal:{
                type: String,
                default:'',
            }
        },
        watch:{
            'model.article_id': function() {
                if  (this.model.article_id != '') {
                    document.getElementById('add_article_2').disabled = false;
                } else {
                    document.getElementById('add_article_2').disabled = true;
                }
            }
        },
        data() {
            return {
                model: {
                    article_id: '',
                    quantity:'',
                    price_unit:'',
                },
                articles: [],
                article_list:[],
            }
        },
        computed: {
            filterArticles() {
				this.articles.map( element => {
						element.full_name = element.article_name;
					});
				return this.articles;
			}
        },
        created() {
            EventBus.$on('remove_article_id', function(id) {
                let index = this.article_list.findIndex((element) => element == id);
                this.article_list.splice(index, 1);
                console.log(index, id);
            }.bind(this));
            
            EventBus.$on('article-order-modal', function(articles) {
                this.articles = articles;
                $('#article-order-modal').modal('show');
            }.bind(this));
            EventBus.$on('clear_modal',function(){
                this.model.article_id='',
                this.model.quantity=''
                this.model.price_unit='',
                $('#article').val(null).trigger('change');
                $('#article-order-modal').modal('hide');
            }.bind(this));
            EventBus.$on('add_article_id', function(id) {
                this.article_list.push(id);
            }.bind(this));
        },
        mounted(){
            this.newSelect2();
        },
        methods: {
            newSelect2: function() {
                let vm = this;

                $('#article').select2({
                    placeholder: "Selecciona un Artículo",
                }).on('select2:select', function(e) {
                    var selected_element = $(e.currentTarget);
                    vm.model.article_id = parseInt(selected_element.val());

                    var current_article = vm.articles.find(article => article.article_id === vm.model.article_id);

                    if ( vm.article_list.includes(current_article.article_id) ) {
                        Swal.fire({
                            title: '¡Error!',
                            text: 'Este artículo ya se encuentra en el registro.',
                            type: "error",
                            heightAuto: false,
                        });

                        vm.model.article_id = '';
                        $('#article').val(null).trigger('change');

                        document.getElementById('add_article_2').disabled = true;
                    } else {
                        document.getElementById('add_article_2').disabled = false;
                    }
                }).on('select2:unselect', function(e) {
                    vm.model.article_id = '';
                });
            },
            sendForm: function(url_validate_modal,event) {
				
                EventBus.$emit('loading', true);
                var target = $(event.target);
                var url_validate_modal = url_validate_modal;
                var fd = new FormData(event.target);

                EventBus.$emit('loading', true);
                axios.post(url_validate_modal, fd, {
                    headers: {
                        'Content-type': 'application/x-www-form-urlencoded',
                    }
                }).then(response => {
                    EventBus.$emit('loading', false);
                    document.getElementById('add_article_2').disabled = false;
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
            }
        }
    };
</script>