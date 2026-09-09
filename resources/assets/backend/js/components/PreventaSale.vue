<template>
    <div class="container-fluid py-4" v-show="form_sale">

        <div class="modal fade" id="modal_article" tabindex="-1" role="dialog" aria-labelledby="modalTitle"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
                <div class="modal-content shadow-lg rounded">

                    <div class="modal-header bg-primary">
                        <h5 class="modal-title  text-white" id="modalTitle">
                            <i class="fas fa-boxes"></i> Lista de Artículos
                        </h5>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <div class="modal-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="text-center">CODIGO</th>
                                        <th>ARTÍCULO</th>
                                        <th class="text-right">PREVENTA</th>
                                        <th class="text-right">RETORNO</th>
                                        <th class="text-right">VENTA</th>
                                        <th class="text-right">SALDO</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-if="articles.length === 0">
                                        <td colspan="6" class="text-center text-muted py-4">
                                            <i class="fas fa-info-circle"></i> No hay artículos disponibles
                                        </td>
                                    </tr>
                                    <tr v-for="article in articles" :key="article.article_id">
                                        <td class="text-center font-weight-bold">{{ article.article_id }}</td>
                                        <td>{{ article.article_name }}</td>
                                        <td class="text-right">{{ formatDecimal(article.preventa) }}</td>
                                        <td class="text-right text-danger">{{ formatDecimal(article.retorno) }}</td>
                                        <td class="text-right text-success">{{ formatDecimal(article.venta) }}</td>
                                        <td class="text-right font-weight-bold">{{ formatDecimal(article.saldo) }}</td>
                                    </tr>
                                </tbody>
                                <tfoot v-if="articles.length > 0" class="bg-light font-weight-bold">
                                    <tr>
                                        <td colspan="2" class="text-right">TOTAL:</td>
                                        <td class="text-right">{{ formatDecimal(totalPreventa) }}</td>
                                        <td class="text-right">{{ formatDecimal(totalRetorno) }}</td>
                                        <td class="text-right">{{ formatDecimal(totalVenta) }}</td>
                                        <td class="text-right">{{ formatDecimal(totalSaldo) }}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>

                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                            <i class="fas fa-times"></i> Cerrar
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex gap-3">
            <div class="mt-3 text-end">
                <button type="button" class="btn btn-success btn-lg" @click="openVoucher()">
                    <i class="fas fa-credit-card"></i> Registrar voucher
                </button>
            </div>
            <div class="mt-3 text-end">
                <button type="button" class="btn btn-danger btn-lg" data-toggle="modal" data-target="#modal_article">
                    <i class="fas fa-eye"></i> Articulos a vender
                </button>
            </div>
        </div>

        <div class="text-center mb-4">
            <h2 class="fw-bold text-primary">💰 Liquidación de Venta</h2>
            <p class="text-muted fs-6">Complete la información y agregue artículos para proceder al pago</p>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-12 col-md-6">
                <div class="card shadow-sm p-3">
                    <label class="form-label fw-semibold fs-5">Cliente:</label>
                    <select class="form-select form-select-lg" name="client_id" id="client_id" v-model="sale.client_id"
                        :disabled="clientLocked" @focus="$parent.clearErrorMsg($event)">
                    </select>
                    <div id="client_id-error" class="error invalid-feedback"></div>
                    <button v-if="clientLocked" type="button" class="btn btn-warning btn-lg mt-3 w-100"
                        @click="resetClient">
                        Cambiar Cliente
                    </button>
                </div>
            </div>

            <div class="col-12 col-md-6">
                <div class="mb-3">
                    <label class="form-label fw-semibold fs-5">Tipo Referencia:</label>
                    <select class="form-select form-select-lg custom-select" name="warehouse_document_type_id"
                        id="warehouse_document_type_id" v-model="sale.warehouse_document_type_id"
                        :disabled="referenceLocked" @focus="$parent.clearErrorMsg($event)">
                        <option value="">Seleccionar</option>
                        <option v-for="type in warehouse_document_types" :key="type.id" :value="type.id">
                            {{ type.name }}
                        </option>
                    </select>
                    <div id="warehouse_document_type_id-error" class="error invalid-feedback"></div>
                </div>
            </div>
        </div>

        <form @submit.prevent="addArticle()">
            <div class="card shadow-sm p-3 mb-4">
                <h4 class="mb-3 text-primary fw-bold">➕ Agregar Artículo</h4>
                <div class="row g-3">
                    <div class="col-12 col-md-12">
                        <label class="form-label fs-5">Artículo:</label>
                        <select class="form-select form-select-lg py-3 w-100" name="article_id" id="article_id"
                            v-model="model.article_id" @focus="$parent.clearErrorMsg($event)">
                            <option value="">Seleccionar artículo</option>
                            <option v-for="article in articles" :key="article.article_id" :value="article.article_id">
                                {{ article.article_name }} - Saldo: {{ article.saldo }}
                            </option>
                        </select>
                        <div id="article_id-error" class="error invalid-feedback"></div>
                    </div>

                    <div class="col-6 col-md-6">
                        <label class="form-label fs-5">Precio:</label>
                        <input type="text" class="form-control form-control-lg text-end" name="price_igv" id="price_igv"
                            v-model="model.price_igv" readonly>
                        <div id="price_igv-error" class="error invalid-feedback"></div>
                    </div>

                    <div class="col-6 col-md-6">
                        <label class="form-label fs-5">Cantidad:</label>
                        <input type="text" class="form-control form-control-lg text-end" name="quantity" id="quantity"
                            v-model="model.quantity">
                        <div id="quantity-error" class="error invalid-feedback"></div>
                    </div>
                </div>

                <div class="text-center mt-4">
                    <button type="submit" class="btn btn-primary btn-lg px-5 w-100 w-md-auto">
                        <i class="fas fa-plus"></i> Agregar Artículo
                    </button>
                </div>
            </div>
        </form>

        <div class="mb-4">
            <h4 class="fw-bold text-primary mb-3">::: Detalles de Venta :::</h4>

            <div v-if="setDetails.length === 0" class="text-center text-muted py-4 border rounded">
                <i class="fas fa-info-circle"></i> No hay artículos agregados
            </div>

            <div v-else class="table-responsive">
                <table class="table table-bordered table-hover align-middle shadow-sm rounded-3">
                    <thead class="table-primary text-center">
                        <tr>
                            <th>#</th>
                            <th>Artículo</th>
                            <th>Código</th>
                            <th>Precio (S/)</th>
                            <th>Cantidad</th>
                            <th>Valor Venta (S/)</th>
                            <th>Percepción (S/)</th>
                            <th class="fw-bold text-success">Total (S/)</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(detail, index) in setDetails" :key="index">
                            <td class="text-center">{{ index + 1 }}</td>
                            <td>{{ detail.article_name }}</td>
                            <td class="text-center">{{ detail.article_id }}</td>
                            <td class="text-right">{{ detail.article_price.toFixed(2) }}</td>
                            <td class="text-right">{{ detail.article_quantity.toFixed(2) }}</td>
                            <td class="text-right">{{ detail.article_sale_value.toFixed(2) }}</td>
                            <td class="text-right">{{ detail.article_igv_perception.toFixed(2) }}</td>
                            <td class="text-right fw-bold text-success">{{ detail.article_total_perception.toFixed(2) }}
                            </td>
                            <td class="text-center">
                                <button type="button" class="btn btn-danger btn-sm" @click="removeDetail(index)">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr class="table-primary fw-bold fs-5">
                            <th colspan="4" class="text-right">Total</th>
                            <td class="text-right">{{ totals.quantity.toFixed(2) }}</td>
                            <td class="text-right">{{ totals.sale_value.toFixed(2) }}</td>
                            <td class="text-right">{{ totals.perception.toFixed(2) }}</td>
                            <td class="text-right">{{ totals.total.toFixed(2) }}</td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
                <div class="mt-3">
                    <button class="btn btn-success btn-lg w-100" @click.prevent="liquidationSale()">
                        <i class="fas fa-money-bill-wave"></i> Iniciar Liquidación
                    </button>
                </div>
            </div>
        </div>

        <div class="mb-4">
            <h4 class="fw-bold text-primary mb-3">::: LISTA DE VENTAS :::</h4>

            <div v-if="sales.length === 0" class="text-center text-muted py-4 border rounded">
                <i class="fas fa-info-circle"></i> No hay Ventas
            </div>

            <div v-else class="table-responsive">
                <table class="table table-bordered table-hover align-middle shadow-sm rounded-3">
                    <thead class="table-primary text-center">
                        <tr>
                            <th>#</th>
                            <th>Cliente</th>
                            <th>Tipo de Referencia</th>
                            <th>Venta</th>
                            <th>Percepción</th>
                            <th>Total</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(sale, index) in sales" :key="index">
                            <td class="text-end">{{ index + 1 }}</td>
                            <td>{{ sale.client_name }}</td>
                            <td class="text-end">{{ sale.warehouse_document_type_name }}</td>
                            <td class="text-right">S/ {{ Number(sale.sale_value).toFixed(2) }}</td>
                            <td class="text-right">S/ {{ Number(sale.perception).toFixed(2) }}</td>
                            <td class="text-right">S/ {{ Number(sale.total).toFixed(2) }}</td>
                            <td class="text-center">
                                <button class="btn btn-info btn-sm me-1" @click="viewDetails(sale)">
                                    <i class="fas fa-list"></i> Detalle
                                </button>
                                <button class="btn btn-primary btn-sm me-1" @click="viewLiquidation(sale)">
                                    <i class="fas fa-file-invoice"></i> Liquidación
                                </button>

                                <button class="btn btn-danger btn-sm me-1" @click="removeSale(index)">
                                    <i class="fas fa-trash"></i> Eliminar
                                </button>
                            </td>
                        </tr>
                    </tbody>
                    <tfoot v-if="sales.length > 0" class="table-primary text-center">
                        <tr>
                            <td colspan="5" class="text-right">TOTAL:</td>
                            <td class="text-right">S/ {{ formatDecimal(totalGeneralVenta) }}</td>
                            <td class="text-right"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <button type="button" class="btn btn-danger btn-lg btn-block" @click="finishSale()">
                    <i class="fas fa-eye"></i> Finalizar
                </button>
            </div>
        </div>

        <div class="mb-4 mt-4">
            <h4 class="fw-bold text-success mb-3">::: LISTA DE COBRANZAS :::</h4>

            <div v-if="list_cobranzas.length === 0" class="text-center text-muted py-4 border rounded">
                <i class="fas fa-info-circle"></i> No hay Cobranzas Registradas
            </div>

            <div v-else class="table-responsive">
                <table class="table table-bordered table-hover align-middle shadow-sm rounded-3">
                    <thead class="table-primary text-center">
                        <tr class="bg-success">
                            <th class="text-white">#</th>
                            <th class="text-white">Cliente</th>
                            <th class="text-white">Serie Documento</th>
                            <th class="text-white">N° Documento</th>
                            <th class="text-white">Tipo</th>
                            <th class="text-white">Venta Original</th>
                            <th class="text-white">Saldo Pendiente</th>
                            <th class="text-white">Total Pagado</th>
                            <th class="text-white">Metodo de Pago</th>
                            <th class="text-white">Banco</th>
                            <th class="text-white">N° Operación</th>
                            <th class="text-white">Fecha de Emisión</th>
                            <th class="text-white">Monto que pagó</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(cobranza, index) in list_cobranzas" :key="index">
                            <td>{{ index + 1 }}</td>
                            <td>{{ cobranza.client_name }}</td>
                            <td>{{ cobranza.referral_serie_number }}</td>
                            <td>{{ cobranza.referral_voucher_number }}</td>
                            <td>{{ cobranza.warehouse_document_type_name }}</td>
                            <td>{{ cobranza.total_perception }}</td>
                            <td>{{ cobranza.balance }}</td>
                            <td>{{ cobranza.paid }}</td>
                            <td>{{ cobranza.payment_method_name }}</td>
                            <td>{{ cobranza.bank_account_name }}</td>
                            <td>{{ cobranza.operation_number }}</td>
                            <td>{{ cobranza.liquidation_date }}</td>
                            <td class="text-right">{{ cobranza.liquidation_amount }}</td>
                        </tr>
                    </tbody>
                    <tfoot v-if="list_cobranzas.length > 0" class="table-success text-center">
                        <tr>
                            <td colspan="12" class="text-right">TOTAL:</td>
                            <td class="text-right">{{ formatDecimal(totalCobranzaPagado) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

    </div>
</template>


<script>
import EventBus from '../event-bus';
import Swal from 'sweetalert2';

export default {
    props: {
        url: {
            type: String,
            default: '',
        },
        url_store: {
            type: String,
            default: '',
        },
        url_store_pre: {
            type: String,
            default: '',
        },
        url_get_clients: {
            type: String,
            default: ''
        },
        url_get_price: {
            type: String,
            default: ''
        },
        url_get_list_cobranzas: {
            type: String,
            default: ''
        },
        csrf_token: {
            type: String,
            default: ''
        },
        warehouse_document_types: {
            type: Array,
            default: ''
        }
    },
    data() {
        return {
            sale: {
                client_id: '',
                client_name: '',
                warehouse_document_type_id: '',
                warehouse_document_type_name: '',
                details: [],
                liquidations: [],
                perception: '',
                sale_value: '',
                total: 0
            },
            model: {
                article_id: '',
                price_igv: '',
                quantity: '',
                perception: '',
                sale_value: '',
                igv_perception: '',
                total_perception: ''
            },
            articles: [],
            clientLocked: false,
            referenceLocked: false,
            form_sale: false,
            sales: [],
            idsLiq: [],
            list_cobranzas: [],
            guia_id: ''

        }
    },
    created() {
        EventBus.$on('preventa_form_sale', function (data) {
            console.log(data);
            this.articles = data.articles;
            this.guia_id = data.guia_id;
            this.form_sale = true;
        }.bind(this));

        EventBus.$on('update_preliquidation', function (sales) {
            this.sales = sales;

            this.sales.forEach(sale => {
                sale.details.forEach(detail => {

                    const article = this.articles.find(a => a.article_id === detail.article_id);
                    if (article) {
                        article.saldo = parseFloat(article.saldo) - parseFloat(detail.article_quantity);
                    }
                });
            });

        }.bind(this));

        EventBus.$on('preventa_form_sale_on', function () {
            this.form_sale = true;
        }.bind(this));

        EventBus.$on('add_cobranza_list', function (idsLiq) {

            this.idsLiq = this.idsLiq.concat(idsLiq);
            this.llenarListCobranzas();

        }.bind(this));

        EventBus.$on('preventa_add_liquidations', function (liquidations) {

            this.sale.liquidations = liquidations;

            const warehouse_document_type_name = this.warehouse_document_types.find(element => element.id == this.sale.warehouse_document_type_id);
            this.sale.warehouse_document_type_name = warehouse_document_type_name.name;
            this.sale.perception = this.totals.perception;
            this.sale.sale_value = this.totals.sale_value;
            this.sale.total = this.totals.total;
            console.log(this.sales);

            this.sales.push({
                ...this.sale,
                details: JSON.parse(JSON.stringify(this.sale.details)),
                liquidations: JSON.parse(JSON.stringify(this.sale.liquidations))
            });

            this.sale = {
                client_id: '',
                warehouse_document_type_id: '',
                perception_percentage: '',
                details: [],
                liquidations: []
            };

            this.model = {
                article_id: '',
                price_igv: '',
                quantity: '',
                perception: '',
                sale_value: '',
                igv_perception: '',
                total_perception: ''
            };

            this.clientLocked = false;
            this.referenceLocked = false;

            $('#client_id').val(null).trigger('change');


            this.form_sale = true;
        }.bind(this));

        EventBus.$on('create_modal', function () {
            $('#modal').modal('show');
        }.bind(this));
    },
    mounted() {
        this.newSelect2();
    },
    watch: {

        'model.article_id': function (val) {

            if (val != '') {
                if (this.sale.client_id == '') {
                    this.model.article_id = '';
                    this.model.price_igv = '';

                    Swal.fire({
                        title: '¡Error!',
                        text: 'Debe seleccionar el Cliente.',
                        type: "error",
                        heightAuto: false,
                        showCancelButton: false,
                        confirmButtonText: 'Ok',
                    });
                } else if (this.sale.warehouse_document_type_id == '') {
                    this.model.article_id = '';
                    this.model.price_igv = '';

                    Swal.fire({
                        title: '¡Error!',
                        text: 'Debe seleccionar el Tipo de Referencia.',
                        type: "error",
                        heightAuto: false,
                        showCancelButton: false,
                        confirmButtonText: 'Ok',
                    });
                } else {
                    EventBus.$emit('loading', true);

                    axios.post(this.url_get_price, {
                        client_id: this.sale.client_id,
                        article_id: val,
                        guia_id: this.guia_id
                    }).then(response => {
                        EventBus.$emit('loading', false);
                        this.model.price_igv = response.data.price_igv;
                        this.model.igv = response.data.article.igv;
                    }).catch(error => {
                        EventBus.$emit('loading', false);
                        Swal.fire({
                            title: '¡Error!',
                            text: 'El Artículo no cuenta con un precio para este Cliente.',
                            type: "error",
                            heightAuto: false,
                            showCancelButton: false,
                            confirmButtonText: 'Ok',
                        });
                        this.model.article_id = '';
                        this.model.price_igv = '';

                    });

                }
            }

        },
        'sale.warehouse_document_type_id': function (val) {
            if (val != '') {
                if (this.sale.client_id == '') {
                    this.sale.warehouse_document_type_id = '';
                    Swal.fire({
                        title: 'Aviso',
                        text: 'Debe seleccionar al Cliente.',
                        type: 'warning',
                        heightAuto: false,
                        showCancelButton: false,
                        confirmButtonText: 'Ok',
                    })
                    return;
                } else if (val == 5 && this.sale.document_type_id != 1) {
                    this.sale.warehouse_document_type_id = '';
                    Swal.fire({
                        title: 'Aviso',
                        text: 'El cliente no cuenta con RUC para facturar.',
                        type: 'warning',
                        heightAuto: false,
                        showCancelButton: false,
                        confirmButtonText: 'Ok',
                    })
                    return;
                }
                this.referenceLocked = true;
            }
        }
    },
    computed: {
        totalPreventa() {
            return this.articles.reduce((sum, a) => sum + parseFloat(a.preventa || 0), 0);
        },
        totalRetorno() {
            return this.articles.reduce((sum, a) => sum + parseFloat(a.retorno || 0), 0);
        },
        totalVenta() {
            return this.articles.reduce((sum, a) => sum + parseFloat(a.venta || 0), 0);
        },
        totalGeneralVenta() {
            return this.sales.reduce((sum, a) => sum + parseFloat(a.total || 0), 0);
        },
        totalCobranzaPagado() {
            return this.list_cobranzas.reduce((sum, a) => sum + parseFloat(a.liquidation_amount || 0), 0);
        },
        totalSaldo() {
            return this.articles.reduce((sum, a) => sum + parseFloat(a.saldo || 0), 0);
        },
        setDetails() {
            return this.sale.details;
        },
        totals() {
            return this.sale.details.reduce((acc, item) => {
                acc.quantity += item.article_quantity;
                acc.sale_value += item.article_sale_value;
                acc.perception += item.article_igv_perception;
                acc.total += item.article_total_perception;
                return acc;
            }, { quantity: 0, sale_value: 0, perception: 0, total: 0 });
        }
    },
    methods: {
        llenarListCobranzas() {
            axios.post(this.url_get_list_cobranzas, {
                ids_liq: this.idsLiq
            }).then(response => {
                EventBus.$emit('loading', false);
                this.list_cobranzas = [];
                this.list_cobranzas = response.data;
            }).catch(error => {
                EventBus.$emit('loading', false);
                console.log(error);
            });
        },
        openVoucher() {
            EventBus.$emit('open_voucher', this.guia_id);
        },
        removeDetail(index) {
            Swal.fire({
                title: '¿Estás seguro?',
                text: 'El artículo será eliminado de la venta.',
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.value) {

                    let detail = this.sale.details[index];

                    let article = this.articles.find(a => a.article_id === detail.article_id);

                    if (article) {
                        article.saldo += Number(detail.article_quantity);
                    }

                    this.sale.details.splice(index, 1);

                    Swal.fire('Eliminado', 'El artículo fue eliminado y el saldo recuperado.', 'success');
                }
            })
        },

        finishSale() {

            const totalCantidad = this.sales.reduce((acc, sale) => {
                return acc + sale.details.reduce((sum, item) => sum + Number(item.article_quantity || 0), 0);
            }, 0);

            if (this.totalVenta !== totalCantidad) {
                Swal.fire({
                    title: '¡Error!',
                    text: `Faltan ${this.totalVenta - totalCantidad} artículos por liquidar. `,
                    type: "question",
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Pre liquidar',
                    cancelButtonText: 'Cancelar',
                }).then((result) => {
                    if (result.value) {

                        let totalSales = this.sales.length;

                        if (totalSales > 0) {

                            EventBus.$emit('loading', true);
                            axios.post(this.url_store_pre, {
                                sales: this.sales,
                                guia_id: this.guia_id

                            }).then(response => {
                                EventBus.$emit('loading', false);
                                this.$parent.alertMsg(response.data);
                            }).catch(error => {
                                EventBus.$emit('loading', false);
                                console.log(error);
                            });

                        } else {
                            Swal.fire({
                                title: 'No se encontraron ventas, debe ingresar al menos una venta.',
                                type: 'error',
                                confirmButtonColor: '#3085d6',
                                confirmButtonText: 'Ok',
                            })
                        }

                    }
                });
                return;
            }

            Swal.fire({
                title: '¿Desea finalizar la venta?',
                type: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Si, Finalizar',
                cancelButtonText: 'No, Cancelar'
            }).then((result) => {
                if (result.value) {

                    EventBus.$emit('loading', true);
                    axios.post(this.url_store, {
                        sales: this.sales,
                        guia_id: this.guia_id

                    }).then(response => {
                        EventBus.$emit('loading', false);
                        if (response.data.pdfUrl) {
                            window.open(response.data.pdfUrl, '_blank');
                        }
                        this.$parent.alertMsg(response.data);
                    }).catch(error => {
                        EventBus.$emit('loading', false);
                        console.log(error);
                    });
                }

            })
        },
        viewDetails(sale) {
            Swal.fire({
                title: `Detalle de la venta`,
                html: `
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Artículo</th>
                        <th>Código</th>
                        <th>Precio</th>
                        <th>Cantidad</th>
                        <th>Valor venta</th>
                        <th>Percepción</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    ${sale.details.map(d => `
                        <tr>
                            <td>${d.article_name}</td>
                            <td>${d.article_id}</td>
                            <td>S/ ${Number(d.article_price).toFixed(2)}</td>
                            <td>${d.article_quantity}</td>
                            <td>S/ ${Number(d.article_sale_value).toFixed(2)}</td>
                            <td>S/ ${Number(d.article_igv_perception).toFixed(2)}</td>
                            <td>S/ ${Number(d.article_total_perception).toFixed(2)}</td>
                        </tr>`).join('')}
                </tbody>
            </table>
        `,
                width: "1200px",
                confirmButtonText: "Cerrar"
            });
        },

        viewLiquidation(sale) {

            Swal.fire({
                title: `Detalle de la Liquidación`,
                html: `
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Condición de Pago</th>
                        <th>Forma de Pago</th>
                        <th>Moneda</th>
                        <th>Banco</th>
                        <th>N° Operación</th>
                        <th>Fecha de Emisión</th>
                        <th>Monto</th>
                        <th>Empleado</th>
                    </tr>
                </thead>
                <tbody>
                    ${sale.liquidations.map(d => `
                        <tr>
                            <td>${d.payment_name}</td>
                            <td>${d.payment_method_name}</td>
                            <td>${d.currency_name}</td>
                            <td>${d.bank_account}</td>
                            <td>${d.operation_number}</td>
                            <td>${d.date_emision ? new Date(d.date_emision).toISOString().slice(0, 10) : ''}</td>
                            <td class="text-right">S/ ${Number(d.amount).toFixed(2)}</td>
                            <td>${d.employee_name}</td>
                        </tr>`).join('')}
                </tbody>
            </table>
        `,
                width: "1200px",
                confirmButtonText: "Cerrar"
            });
        },
        formatDecimal(value) {
            if (value === null || value === undefined) return '0.00';
            return parseFloat(value).toFixed(2);
        },
        removeSale(index) {
            Swal.fire({
                title: '¿Estás seguro?',
                text: 'Esta venta será eliminada.',
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.value) {
                    const removedSale = this.sales[index];

                    removedSale.details.forEach(d => {
                        const article = this.articles.find(a => a.article_id === d.article_id);
                        if (article) {
                            article.saldo = parseFloat(article.saldo) + parseFloat(d.article_quantity);
                        }
                    });
                    this.sales.splice(index, 1);
                    Swal.fire({
                        title: 'Eliminado',
                        text: 'La venta se eliminó correctamente y los saldos fueron restaurados.',
                        type: 'success',
                        timer: 1500,
                        showConfirmButton: false
                    });
                }
            });
        },


        liquidationSale() {
            if (this.sale.warehouse_document_type_id == '') {
                Swal.fire({
                    title: '¡Error!',
                    text: 'Debe seleccionar el Tipo de Documento.',
                    type: "error",
                    heightAuto: false,
                    showCancelButton: false,
                    confirmButtonText: 'Ok',
                });
                return;
            }
            if (this.sale.details.length === 0) {
                Swal.fire({
                    title: '¡Error!',
                    text: 'Debe liquidar todos los productos.',
                    type: "error",
                    heightAuto: false,
                    showCancelButton: false,
                    confirmButtonText: 'Ok',
                });
                return;
            } else {
                Swal.fire({
                    title: '¿Estás seguro?',
                    text: 'La venta será liquidada.',
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Sí, liquidar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.value) {
                        this.form_sale = false;

                        const ventasCliente = this.sales.filter(
                            sale => sale.client_id === this.sale.client_id
                        );

                        let totalDescuento = 0;
                        let totalCredito = 0;
                        ventasCliente.forEach(sale => {
                            sale.liquidations.forEach(l => {
                                if (l.payment_id === 2) {
                                    totalCredito += Number(l.amount);
                                }
                            });
                        });

                        EventBus.$emit('preventa_liquidation', this.sale.client_id, this.totals.total, totalCredito, this.guia_id);
                    }
                })
            }
        },

        resetClient() {
            Swal.fire({
                title: '¿Estás seguro?',
                text: 'Los datos del Cliente serán limpiados y los artículos serán eliminados.',
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.value) {

                    this.sale.details.forEach(d => {
                        const article = this.articles.find(a => a.article_id === d.article_id);
                        if (article) {
                            article.saldo = parseFloat(article.saldo) + parseFloat(d.article_quantity);
                        }
                    });

                    this.model.article_id = '';
                    this.model.price_igv = '';
                    this.model.quantity = '';

                    this.sale.client_id = '';
                    this.sale.client_name = '';
                    this.sale.warehouse_document_type_id = '';
                    this.sale.details = [];

                    this.clientLocked = false;
                    this.referenceLocked = false;

                    $('#client_id').val(null).trigger('change');

                    Swal.fire({
                        title: 'Eliminado',
                        text: 'Los datos del Cliente fueron limpiados y los artículos fueron restaurados.',
                        type: 'success',
                        timer: 1500,
                        showConfirmButton: false
                    });
                }
            });
        },

        newSelect2: function () {

            let vm = this;
            let token = vm.csrf_token;

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
                            guia_id: vm.guia_id,
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
                const data = e.params.data;
                vm.sale.client_id = parseInt(data.id);
                vm.sale.perception_percentage = data.perception_percentage.value ? data.perception_percentage.value : 0;
                vm.sale.document_type_id = data.document_type_id;
                vm.sale.client_name = data.text;
                vm.model.article_id = '';
                vm.clientLocked = true;

            }).on('select2:unselect', function (e) {
                vm.sale.client_id = '';
                vm.sale.client_name = '';
                vm.model.article_id = '';
                vm.clientLocked = false;
                vm.referenceLocked = false;

                $('#client_id').val(null).trigger('change');

                vm.sale.details = [];
                vm.sale.warehouse_document_type_id = '';
                vm.sale.warehouse_document_type_name = '';
                vm.sale.perception_percentage = 0;

            });
        },

        addArticle: function () {
            if (this.sale.client_id == '') {
                Swal.fire({
                    title: '¡Error!',
                    text: 'Debe seleccionar un Cliente.',
                    type: "error",
                    heightAuto: false,
                    confirmButtonText: 'Ok',
                });
            } else if (this.model.article_id == '') {
                Swal.fire({
                    title: '¡Error!',
                    text: 'Debe seleccionar un Artículo.',
                    type: "error",
                    heightAuto: false,
                    confirmButtonText: 'Ok',
                });
            } else if (this.model.price_igv == '') {
                Swal.fire({
                    title: '¡Error!',
                    text: 'El Artículo no cuenta con un precio para este Cliente.',
                    type: "error",
                    heightAuto: false,
                    confirmButtonText: 'Ok',
                });
            } else if (this.model.quantity <= 0) {
                Swal.fire({
                    title: '¡Error!',
                    text: 'La Cantidad no puede estar vacía o ser igual 0.',
                    type: "error",
                    heightAuto: false,
                    confirmButtonText: 'Ok',
                });
            } else {

                const selectedArticle = this.articles.find(a => a.article_id === this.model.article_id);

                if (!selectedArticle) {
                    Swal.fire({
                        title: '¡Error!',
                        text: 'Artículo no encontrado.',
                        type: "error",
                        heightAuto: false,
                        confirmButtonText: 'Ok',
                    });
                    return;
                }

                const requestedQuantity = parseFloat(this.model.quantity) || 0;
                const availableStock = parseFloat(selectedArticle.saldo) || 0;

                if (requestedQuantity > availableStock) {
                    Swal.fire({
                        title: '¡Error!',
                        text: `La cantidad solicitada (${requestedQuantity}) supera el saldo disponible (${availableStock}).`,
                        type: "error",
                        heightAuto: false,
                        confirmButtonText: 'Ok',
                    });
                    return;
                }

                const article_price = parseFloat(this.model.price_igv) || 0;
                const article_sale_value = article_price * requestedQuantity;
                const article_igv_perception = this.sale.warehouse_document_type_id == 5 ? article_sale_value * (parseFloat(this.sale.perception_percentage / 100)) : 0;
                const article_total_perception = article_sale_value + article_igv_perception;

                const formatTwoDecimals = value => parseFloat(value.toFixed(2));


                this.sale.details.push({

                    article_id: this.model.article_id,
                    article_name: selectedArticle.article_name,
                    article_price: formatTwoDecimals(article_price),
                    article_igv: formatTwoDecimals(selectedArticle.article_igv),
                    article_quantity: formatTwoDecimals(requestedQuantity),
                    article_sale_value: formatTwoDecimals(article_sale_value),
                    article_igv_perception: formatTwoDecimals(article_igv_perception),
                    article_total_perception: formatTwoDecimals(article_total_perception)
                });

                selectedArticle.saldo = (availableStock - requestedQuantity);

                this.model.article_id = '';
                this.model.price_igv = '';
                this.model.quantity = '';
                this.model.igv = '';

            }
        }

    }
};
</script>
<style>
.custom-select {
    border-radius: 1rem;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    padding: 0.8rem 1rem;
    font-size: 1.1rem;

    border: 1px solid #ced4da;
    transition: all 0.3s ease;
}

.custom-select:focus {
    outline: none;
    box-shadow: 0 4px 12px rgba(0, 123, 255, 0.3);
    border-color: #80bdff;
}
</style>