<template>
    <!--begin::Portlet-->
    <div class="kt-portlet">

        <!--begin::Modal-->
        <div class="modal fade" id="modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <!--begin::Form-->
                    <form class="kt-form" @submit.prevent="formController(url, $event)">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Registrar Pedido</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="kt-portlet__body">
                                <div class="row">
                                    <div class="col-lg-4">
                                        <div class="form-group">
                                            <label class="form-control-label">Cliente:
                                                <a href="#" @click.prevent="OpenModalRegisterClient()"
                                                    title="Crear nuevo Cliente"
                                                    class="btn btn-primary float-right btn-icon btn-icon-md"
                                                    style="width: 24px; height: 24px; border-radius: 50%;"
                                                    id="OpenModalRegister">
                                                    <i class="fa fa-solid fa-plus"></i>
                                                </a>
                                            </label>

                                            <select class="form-control kt-select2" name="client_id" id="client_id"
                                                v-model="model.client_id" @focus="$parent.clearErrorMsg($event)">
                                                <option value="">Seleccionar</option>
                                            </select>
                                            <div id="client_id-error" class="error invalid-feedback"></div>
                                        </div>
                                    </div>

                                    <div class="col-lg-4">
                                        <div class="form-group">
                                            <label class="form-control-label">Origen:</label>
                                            <select class="form-control kt-select2" name="origin_id" id="origin_id"
                                                v-model="model.origin_id" @focus="$parent.clearErrorMsg($event)">
                                                <option value="" selected disabled>Seleccionar</option>
                                                <option v-for="origin in origin_houses" v-bind:key="origin.id"
                                                    :value="origin.id">{{
                                                        origin.name }}</option>
                                            </select>
                                            <div id="origin_id-error" class="error invalid-feedback"></div>
                                        </div>
                                    </div>

                                    <div class="col-lg-4">
                                        <div class="form-group">
                                            <label class="form-control-label">Serie</label>
                                            <input type="text" class="form-control" name="serie" id="serie"
                                                v-model="model.serie" readonly @focus="$parent.clearErrorMsg($event)">
                                            <div id="serie-error" class="error invalid-feedback"></div>
                                        </div>
                                    </div>

                                    <div class="col-lg-4">
                                        <div class="form-group">
                                            <label class="form-control-label">N°</label>
                                            <input type="text" class="form-control" name="correlative" id="correlative"
                                                v-model="model.correlative" readonly
                                                @focus="$parent.clearErrorMsg($event)">
                                            <div id="correlative-error" class="error invalid-feedback"></div>
                                        </div>
                                    </div>

                                    <div class="col-lg-4">
                                        <div class="form-group">
                                            <label class="form-control-label">Tipo de Producto:</label>
                                            <select class="form-control kt-select2" name="product_type"
                                                id="product_type" v-model="model.product_type"
                                                @focus="$parent.clearErrorMsg($event)">
                                                <option value="" selected disabled>Seleccionar</option>
                                                <option value="1">GAS</option>
                                                <option value="2">VALVULA</option>
                                                <option value="3">CILINDRO</option>
                                            </select>
                                            <div id="product_type-error" class="error invalid-feedback"></div>
                                        </div>
                                    </div>

                                    <div class="col-lg-4">
                                        <div class="form-group">
                                            <label class="form-control-label">Producto:</label>
                                            <select class="form-control kt-select2" name="article_id" id="article_id"
                                                v-model="model.article_id" @focus="$parent.clearErrorMsg($event)">
                                                <option value="" selected disabled>Seleccionar</option>
                                                <option v-for="article in filteredArticles" v-bind:key="article.id"
                                                    :value="article.id">
                                                    {{
                                                        article.name }}</option>
                                            </select>
                                            <div id="article_id-error" class="error invalid-feedback"></div>
                                        </div>
                                    </div>

                                    <div class="col-lg-4">
                                        <div class="form-group">
                                            <label class="form-control-label">Precio:</label>
                                            <input type="text" class="form-control" name="price" id="price"
                                                placeholder="0.00" v-model="model.price"
                                                @focus="$parent.clearErrorMsg($event)">
                                            <div id="price-error" class="error invalid-feedback"></div>
                                        </div>
                                    </div>

                                    <div class="col-lg-4">
                                        <div class="form-group">
                                            <label class="form-control-label">Cantidad:</label>
                                            <input type="text" class="form-control" name="quantity" id="quantity"
                                                placeholder="0" v-model="model.quantity"
                                                @focus="$parent.clearErrorMsg($event)">
                                            <div id="quantity-error" class="error invalid-feedback"></div>
                                        </div>
                                    </div>

                                    <div class="col-lg-4">
                                        <div class="form-group">
                                            <label class="form-control-label">Total:</label>
                                            <input type="text" class="form-control" name="total" id="total" readonly
                                                v-model="model.total" @focus="$parent.clearErrorMsg($event)">
                                            <div id="total-error" class="error invalid-feedback"></div>
                                        </div>
                                    </div>

                                    <div class="col-lg-4">
                                        <div class="form-group">
                                            <label class="form-control-label">Fecha emisión:</label>
                                            <datetime v-model="model.issue_date"
                                                placeholder="Selecciona una Fecha inicial" :format="'dd-LL-yyyy'"
                                                input-id="issue_date" name="issue_date" value-zone="America/Lima"
                                                :max-datetime="current_date" zone="America/Lima" class="form-control"
                                                @focus="$parent.clearErrorMsg($event)">
                                            </datetime>
                                            <div id="issue_date-error" class="error invalid-feedback"></div>
                                        </div>
                                    </div>

                                    <div class="col-lg-4">
                                        <div class="form-group">
                                            <label class="form-control-label">Turno:</label>
                                            <select class="form-control kt-select2" name="turno" id="turno"
                                                v-model="model.turno" @focus="$parent.clearErrorMsg($event)">
                                                <option value="" selected disabled>Seleccionar</option>
                                                <option value="M">Mañana</option>
                                                <option value="N">Noche</option>
                                            </select>
                                            <div id="turno-error" class="error invalid-feedback"></div>
                                        </div>
                                    </div>

                                    <div class="col-lg-4">
                                        <div class="form-group">
                                            <label class="form-control-label">Chofer:</label>
                                            <select class="form-control kt-select2" name="employee_id" id="employee_id"
                                                v-model="model.employee_id" @focus="$parent.clearErrorMsg($event)">
                                                <option value="">Seleccionar</option>
                                            </select>
                                            <div id="employee_id-error" class="error invalid-feedback"></div>
                                        </div>
                                    </div>


                                    <div class="col-lg-4">
                                        <div class="form-group">
                                            <label class="form-control-label">Hora llamada:</label>
                                            <input type="time" class="form-control" name="hora_llamada"
                                                id="hora_llamada" v-model="model.hora_llamada"
                                                @focus="$parent.clearErrorMsg($event)">
                                            <div id="hora_llamada-error" class="error invalid-feedback"></div>
                                        </div>
                                    </div>

                                    <div class="col-lg-4">
                                        <div class="form-group">
                                            <label class="form-control-label">Hora entrega balón:</label>
                                            <input type="time" class="form-control" name="hora_entrega_balon"
                                                id="hora_entrega_balon" v-model="model.hora_entrega_balon"
                                                @focus="$parent.clearErrorMsg($event)">
                                            <div id="hora_entrega_balon-error" class="error invalid-feedback"></div>
                                        </div>
                                    </div>

                                    <div class="col-lg-4">
                                        <div class="form-group">
                                            <label class="form-control-label">¿El Cliente es Recuperado?</label>
                                            <input type="checkbox" class="form-control" name="recuperado"
                                                id="recuperado" v-model="model.recuperado"
                                                @focus="$parent.clearErrorMsg($event)" readonly>
                                            <div id="recuperado-error" class="error invalid-feedback"></div>
                                        </div>
                                    </div>

                                    <div class="col-lg-4">
                                        <div class="form-group">
                                            <label class="form-control-label">¿El Cliente es Nuevo?</label>
                                            <input type="checkbox" class="form-control" name="nuevo" id="nuevo"
                                                v-model="model.nuevo" @focus="$parent.clearErrorMsg($event)" readonly>
                                            <div id="nuevo-error" class="error invalid-feedback"></div>
                                        </div>
                                    </div>


                                </div>
                            </div>
                            <div class="kt-portlet__foot">
                                <div class="kt-form__actions">
                                    <div class="row">
                                        <div class="col-12 kt-align-right">
                                            <button type="submit" class="btn btn-primary">Crear</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <!--end::Form-->
        </div>
        <!--end::Portlet-->
    </div>
</template>

<script>
import EventBus from '../event-bus';

export default {
    props: {
        url: {
            type: String,
            default: '',
        },
        url_get_clients: {
            type: String,
            default: ''
        },
        url_get_employees: {
            type: String,
            default: ''
        },
        origin_houses: {
            type: Array,
            default: ''
        },
        articles: {
            type: Array,
            default: ''
        },
        max_correlative: {
            type: Number,
            default: ''
        },
        current_date: {
            type: String,
            default: ''
        }
    },
    data() {
        return {
            model: {
                client_id: '',
                origin_id: '',
                serie: 'P001',
                correlative: this.max_correlative,
                product_type: '',
                article_id: '',
                price: '',
                quantity: '',
                total: '0.00',
                issue_date: this.current_date,
                turno: '',
                employee_id: '',
                hora_llamada: '',
                hora_entrega_balon: '',
                nuevo: false,
                recuperado: false

            },
        }
    },
    created() {
        EventBus.$on('clearForm', function (model) {

        }.bind(this));

        EventBus.$on('create_modal', function () {
            $('#modal').modal('show');
        }.bind(this));

        EventBus.$on('asignar_cliente', function (data) {
            this.model.client_id = data.id;
            this.model.nuevo = true;
            let option = new Option(data.client_name, data.id, true, true);
            $('#client_id').append(option).trigger('change.select2');
        }.bind(this));
    },
    mounted() {

        this.newSelect2();
        this.newSelect3();
    },
    watch: {
        'model.hora_llamada': function () {
            this.calcularTiempoTotal();
        },
        'model.quantity': function (val) {
            this.calculateTotal(val, this.model.price);
        },
        'model.price': function (val) {
            this.calculateTotal(this.model.quantity, val);
        },
    },
    computed: {
        filteredArticles() {
            if (this.model.product_type == 1) {
                return this.articles.filter(article => [10385, 10386, 10387, 10388].includes(article.id));
            } else if (this.model.product_type == 2) {
                return this.articles.filter(article => [10273, 10274, 10275, 10276].includes(article.id));
            } else if (this.model.product_type == 3) {
                return this.articles.filter(article => [25, 26, 1284, 1285].includes(article.id));
            }
            return [];
        },

    },
    methods: {
        OpenModalRegisterClient: function () {
            EventBus.$emit('create_modal_client');
        },
        calculateTotal: function (quantity, price) {
            let total = accounting.toFixed(quantity * price, 2);
            this.model.total = total;

        },

        calcularTiempoTotal() {
            if (this.model.hora_llamada && this.model.hora_entrega_pedido) {
                let [h1, m1] = this.model.hora_llamada.split(':').map(Number);
                let [h2, m2] = this.model.hora_entrega_pedido.split(':').map(Number);

                let minutosInicio = h1 * 60 + m1;
                let minutosFin = h2 * 60 + m2;

                if (minutosFin < minutosInicio) {
                    minutosFin += 24 * 60;
                }

                let totalMinutos = minutosFin - minutosInicio;
                let horas = String(Math.floor(totalMinutos / 60)).padStart(2, '0');
                let minutos = String(totalMinutos % 60).padStart(2, '0');

                this.model.tiempo_total = `${horas}:${minutos}`;
            } else {
                this.model.tiempo_total = '';
            }
        },


        newSelect2: function () {
            let vm = this;
            let token = document.head.querySelector('meta[name="csrf-token"]').content;
            $("#client_id").select2({
                placeholder: "Buscar",
                allowClear: true,
                language: {
                    noResults: function () {
                        return 'No hay resultados';
                    },
                    searching: function () {
                        return 'Buscando...';
                    },
                    inputTooShort: function () {
                        return 'Ingresa 1 o más caracteres';
                    },
                    errorLoading: function () {
                        return 'No se pudo cargar la información'
                    }
                },
                ajax: {
                    url: this.url_get_clients,
                    dataType: 'json',
                    delay: 250,
                    type: 'POST',
                    data: function (params) {
                        var queryParameters = {
                            q: params.term,
                            _token: token,
                        }

                        return queryParameters;
                    },
                    processResults: function (data, params) {
                        params.page = params.page || 1;

                        return {
                            results: data,
                            pagination: {
                                more: (params.page * 30) < data.total_count
                            }
                        };
                    },
                    cache: true
                },
                minimumInputLength: 1,
            }).on('select2:select', function (e) {
                var selected_element = $(e.currentTarget);
                vm.model.client_id = parseInt(selected_element.val());
            }).on('select2:unselect', function (e) {
                vm.model.client_id = '';
            });
        },
        newSelect3: function () {
            let vm = this;
            let token = document.head.querySelector('meta[name="csrf-token"]').content;
            $("#employee_id").select2({
                placeholder: "Buscar",
                allowClear: true,
                language: {
                    noResults: function () {
                        return 'No hay resultados';
                    },
                    searching: function () {
                        return 'Buscando...';
                    },
                    inputTooShort: function () {
                        return 'Ingresa 1 o más caracteres';
                    },
                    errorLoading: function () {
                        return 'No se pudo cargar la información'
                    }
                },
                ajax: {
                    url: this.url_get_employees,
                    dataType: 'json',
                    delay: 250,
                    type: 'POST',
                    data: function (params) {
                        var queryParameters = {
                            q: params.term,
                            _token: token,
                        }

                        return queryParameters;
                    },
                    processResults: function (data, params) {
                        params.page = params.page || 1;

                        return {
                            results: data,
                            pagination: {
                                more: (params.page * 30) < data.total_count
                            }
                        };
                    },
                    cache: true
                },
                minimumInputLength: 1,
            }).on('select2:select', function (e) {
                var selected_element = $(e.currentTarget);
                vm.model.employee_id = parseInt(selected_element.val());
            }).on('select2:unselect', function (e) {
                vm.model.employee_id = '';
            });
        },
        formController: function (url, event) {
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
                EventBus.$emit('refresh_table');
                EventBus.$emit('clearForm');
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
