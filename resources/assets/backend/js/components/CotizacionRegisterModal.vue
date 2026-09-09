<template>
    <!--begin::Modal-->
    <div class="modal fade" id="cotizacion-register-modal" tabindex="-1" role="dialog"
        aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <!--begin::Form-->
                <form class="kt-form" @submit.prevent="sendForm(url, $event)">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">{{ text }} repuesto</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="kt-portlet__body">
                            <div class="row">
                                <div class="col-3">
                                    <div class="form-group">
                                        <input type="hidden" name="item_number" id="item_number"
                                            v-model="model.item_number">

                                        <label class="form-control-label">Tipo de Repuesto:</label>
                                        <select class="form-control kt-select2" name="article_type" id="article_type"
                                            @change="Unselect()" v-model="model.article_type" :disabled="isDisabled"
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
                                            v-model="model.article_id" :disabled="isDisabled"
                                            @focus="$parent.clearErrorMsg($event)">
                                            <option disabled value="">Seleccionar</option>
                                            <option v-for="article in filterArticles" :value="article.id"
                                                v-bind:key="article.id">
                                                {{ article.full_name }}
                                            </option>
                                        </select>
                                        <div id="article_id-error" class="error invalid-feedback"></div>
                                    </div>
                                </div>

                                <div class="col-lg-3">
                                    <div class="form-group">
                                        <label class="form-control-label">Costo:</label>
                                        <input readonly type="tel" class="form-control" name="cost" id="cost"
                                            placeholder="0" v-model="model.cost" @focus="$parent.clearErrorMsg($event)">
                                        <div id="cost-error" class="error invalid-feedback"></div>
                                    </div>
                                </div>

                                <div class="col-lg-3">
                                    <div class="form-group">
                                        <label class="form-control-label">Cantidad: <span class="text-danger">(Stock: {{
                                            this.model.stock_good }})</span></label>
                                        <input type="tel" class="form-control" name="quantity" id="quantity"
                                            placeholder="0" v-model="model.quantity" @change="CalcularDescuento()"
                                            @focus="$parent.clearErrorMsg($event)">
                                        <div id="quantity-error" class="error invalid-feedback"></div>
                                    </div>
                                </div>
                                <div class="col-lg-3">
                                    <div class="form-group">
                                        <label class="form-control-label">Precio:</label>
                                        <input type="tel" class="form-control" name="price" id="price" placeholder="0"
                                            v-model="model.price" @change="CalcularDescuento()"
                                            @focus="$parent.clearErrorMsg($event)">
                                        <div id="price-error" class="error invalid-feedback"></div>
                                    </div>
                                </div>
                                <div class="col-lg-3">
                                    <div class="form-group">
                                        <label class="form-control-label">Importe:</label>
                                        <input readonly type="tel" class="form-control" name="import" id="import"
                                            placeholder="0" v-model="model.import"
                                            @focus="$parent.clearErrorMsg($event)">
                                        <div id="import-error" class="error invalid-feedback"></div>
                                    </div>
                                </div>
                                <div class="col-lg-3">
                                    <div class="form-group">
                                        <input type="hidden">
                                    </div>
                                </div>
                                <div class="col-lg-3">
                                    <div class="form-group">
                                        <label class="form-control-label">% Descuento:</label>
                                        <input type="number" step="0.01" class="form-control" name="percentage"
                                            id="percentage" placeholder="0" @change="CalcularDescuento()"
                                            v-model="model.percentage" @focus="$parent.clearErrorMsg($event)">
                                        <div id="percentage-error" class="error invalid-feedback"></div>
                                    </div>
                                </div>
                                <div class="col-lg-3">
                                    <div class="form-group">
                                        <label class="form-control-label">Monto Descuento:</label>
                                        <input readonly type="tel" class="form-control" name="discount" id="discount"
                                            placeholder="0" v-model="model.discount"
                                            @focus="$parent.clearErrorMsg($event)">
                                        <div id="discount-error" class="error invalid-feedback"></div>
                                    </div>
                                </div>
                                <div class="col-lg-3">
                                    <div class="form-group">
                                        <label class="form-control-label">Valor Venta:</label>
                                        <input readonly type="tel" class="form-control" name="total" id="total"
                                            placeholder="0" v-model="model.total"
                                            @focus="$parent.clearErrorMsg($event)">
                                        <div id="total-error" class="error invalid-feedback"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success" id="add_article">
                            <i class="fa fa-plus"></i> {{ text }} artículo
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
                isDisabled: true,
                item_number: '',
                article_type: '',
                article_id: '',
                cost: '',
                price: '',
                stock_good: 0,
                quantity: '',
                percentage: 0,
                discount: '',
                import: '',
                total: '',
            },
            articles: [],
            article_list: [],
            text: '',
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
            $('#article_id').val(null).trigger('change');

            this.isDisabled = false;
            this.model.item_number = '';
            this.model.article_type = '';
            this.model.article_id = '';
            this.model.cost = '';
            this.model.price = '';
            this.model.stock_good = '';
            this.model.quantity = '';
            this.model.percentage = 0;
            this.model.discount = '';
            this.model.import = '';
            this.model.total = '';
            this.text = 'Agregar';

            $('#cotizacion-register-modal').modal('show');
        }.bind(this));

        EventBus.$on('editModal', function (item_number, id, article_type, cost, quantity, price, stock_good, importe, percentage, discount, total) {

            this.isDisabled = true;
            this.model.item_number = '';
            this.model.article_type = '';
            this.model.article_id = '';
            this.model.cost = '';
            this.model.price = '';
            this.model.stock_good = '';
            this.model.quantity = '';
            this.model.percentage = 0;
            this.model.discount = '';
            this.model.import = '';
            this.model.total = '';
            this.text = '';

            this.model.item_number = item_number;
            this.model.article_id = id;
            $('#article_id').val(id).trigger('change');

            if (article_type == 'Bienes') {
                this.model.article_type = 1;
            } else {
                this.model.article_type = 2;
            }

            this.model.cost = cost;
            this.model.quantity = quantity;
            this.model.price = price;
            this.model.stock_good = stock_good;
            this.model.import = importe;
            this.model.percentage = percentage ? percentage : 0;
            this.model.discount = discount;
            this.model.total = total;
            this.text = 'Actualizar';

            $('#cotizacion-register-modal').modal('show');
        }.bind(this));

        EventBus.$on('add_article_id', function (id) {
            this.article_list.push(id);
        }.bind(this));

        EventBus.$on('cotizacion_register_modal_hide', function () {
            this.article_type = '';
            this.model.article_id = '';
            this.model.cost = '';
            this.model.price = '';
            this.model.stock_good = '';
            this.model.quantity = '';
            this.model.percentage = 0;
            this.model.discount = 0;
            this.model.import = 0;
            this.model.total = 0;
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
            this.model.cost = '';
            this.model.price = '';
            this.model.stock_good = 0;
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
        CalcularDescuento: function () {
            var $percentage = $('#percentage').val();
            var $precio = $('#price').val();
            var $quantity = $('#quantity').val();

            if ($quantity > this.model.stock_good) {
                this.model.quantity = '';
                Swal.fire({
                    title: "Error",
                    text: "La Cantidad ingresada no puede ser mayor al Stock.",
                    type: "error",
                    heightAuto: false,
                })
            }

            var $base = $precio * $quantity;
            var $percentageD = $percentage / 100;
            var $montoDesc = $base * $percentageD;
            var $total = $base - $montoDesc;

            this.model.import = $base;
            this.model.discount = $montoDesc;
            this.model.total = $total;
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
                vm.model.cost = current_article.last_price;
                vm.model.price = current_article.price;
                vm.model.stock_good = current_article.stock_good;
            }).on('select2:unselect', function (e) {
                vm.model.article_id = '';
                vm.model.cost = '';
            });
        },
    },
}

</script>