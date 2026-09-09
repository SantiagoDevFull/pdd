<template>
    <div>
        <!--begin::Portlet-->
        <div class="kt-portlet" v-if="show_table">
            <div class="kt-portlet__head">
                <div class="kt-portlet__head-label">
                    <h3 class="kt-portlet__head-title">
                        Lista de Ventas
                    </h3>
                </div>
                <div class="kt-portlet__head-toolbar">
                    <div class="kt-portlet__head-wrapper">
                        <div class="dropdown dropdown-inline">
                            <a href="#" class="btn btn-outline-success btn-bold btn-sm"
                                :disabled="selectedSales.length === 0" @click.prevent="finishLiquidation()">
                                Verificado
                            </a>
                            <a href="#" class="btn btn-outline-brand btn-bold btn-sm" @click.prevent="registerMoney()">
                                Registrar Monedas
                            </a>
                        </div>
                    </div>
                </div>
            </div>


            <div class="kt-portlet__body">
                <div class="row">
                    <div class="col-lg-3">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">
                                    <i class="flaticon flaticon-search"></i>
                                </span>
                            </div>
                            <input type="text" class="form-control" name="generalSearch" id="generalSearch"
                                placeholder="Buscar por ID o Razón Social">
                        </div>
                    </div>
                </div>
            </div>

            <div class="kt-portlet__body kt-portlet__body--fit">
                <!--begin: Datatable -->
                <div class="kt-datatable" @click="manageActions"></div>
                <!--end: Datatable -->
            </div>
        </div>
        <!--end::Portlet-->
    </div>
</template>

<script>
import Swal from 'sweetalert2';
import EventBus from '../event-bus';

export default {
    props: {
        url: {
            type: String,
            default: ''
        },
        url_sale_detail: {
            type: String,
            default: ''
        },
        url_liquidation_detail: {
            type: String,
            default: ''
        },
        url_asign_scop: {
            type: String,
            default: ''
        },
        url_export_pdf: {
            type: String,
            default: ''
        },
        url_detail_money: {
            type: String,
            default: ''
        },
        url_finish_liquidation: {
            type: String,
            default: ''
        },
    },
    data() {
        return {
            table_sales: undefined,
            show_table: false,
            model: {},
            export_excel: false,
            selectedSales: [],
        }
    },
    created() {

    },
    mounted() {
        EventBus.$on('show_table', function (response) {
            let vm = this;
            this.show_table = true;
            this.model = response;

            Vue.nextTick(function () {
                if (vm.table_sales == undefined) {
                    vm.fillTableX();
                } else {
                    vm.table_sales.setDataSourceParam('model', vm.model);
                    vm.table_sales.load();
                }

                vm.table_sales.on('kt-datatable--on-ajax-done', function () {
                    EventBus.$emit('loading', false);
                });
            });
        }.bind(this));

        EventBus.$on('refresh_table', function () {
            if (this.table_sales != undefined) {
                this.table_sales.setDataSourceParam('model', this.model);
                this.table_sales.load();
            }
        }.bind(this));
    },
    watch: {

    },
    computed: {

    },
    methods: {
        finishLiquidation: function () {

            if (this.selectedSales.length === this.table_sales.getTotalRows()) {
                Swal.fire('Aviso', 'No hay ventas seleccionadas', 'warning');
                return;
            }

            Swal.fire({
                title: '¡Cuidado!',
                text: '¿Seguro que desea Finalizar la Liquidación?',
                type: "warning",
                heightAuto: false,
                showCancelButton: true,
                confirmButtonText: 'Sí',
                cancelButtonText: 'No'
            }).then(result => {
                if (result.value) {
                    EventBus.$emit('loading', true);
                    axios.post(this.url_finish_liquidation, {
                        sales: this.selectedSales,
                        model: this.model,
                    }).then(response => {
                        EventBus.$emit('loading', false);
                        this.$parent.alertMsg(response.data);
                    }).catch(error => {
                        console.log(error);
                        console.log(error.response);
                    });
                }
            })

        },
        fillTableX: function () {
            let vm = this;
            let token = document.head.querySelector('meta[name="csrf-token"]').content;

            this.table_sales = $('.kt-datatable').KTDatatable({
                // datasource definition
                data: {
                    type: 'remote',
                    source: {
                        read: {
                            url: vm.url,
                            params: {
                                _token: token,
                                model: vm.model,
                            },
                        },
                    },
                    pageSize: 10,
                    serverPaging: true,
                    serverFiltering: true,
                    serverSorting: true,
                },

                // layout definition
                layout: {
                    scroll: true, // enable/disable datatable scroll both horizontal and vertical when needed.
                    height: 400,
                    footer: false // display/hide footer
                },

                // column sorting
                sortable: true,
                pagination: false,

                search: {
                    input: $('#generalSearch'),
                },

                translate: {
                    records: {
                        processing: 'Espere porfavor...',
                        noRecords: 'No hay registros'
                    },
                    toolbar: {
                        pagination: {
                            items: {
                                default: {
                                    first: 'Primero',
                                    prev: 'Anterior',
                                    next: 'Siguiente',
                                    last: 'Último',
                                    more: 'Más páginas',
                                    input: 'Número de página',
                                    select: 'Seleccionar tamaño de página'
                                },
                                info: 'Mostrando {{start}} - {{end}} de {{total}} registros'
                            }
                        }
                    }
                },

                rows: {
                    autoHide: false,
                },

                // columns definition
                columns: [
                    {
                        field: 'check',
                        title: '#',
                        width: 30,
                        sortable: false,
                        textAlign: 'center',
                        selector: { class: 'kt-checkbox--solid' }
                    },
                    {
                        field: 'options',
                        title: 'Opciones',
                        sortable: false,
                        width: 120,
                        overflow: 'visible',
                        autoHide: false,
                        textAlign: 'right',
                        template: function (row) {
                            let actions = '<div class="actions">';
                            actions += '<a href="#" class="sale_detail btn btn-sm btn-clean btn-icon btn-icon-md" title="Ver Artículos">';
                            actions += '<i class="fa fa-solid fa-cart-plus"></i>';
                            actions += '</a>';
                            actions += '<a href="#" class="sale_liquidation btn btn-sm btn-clean btn-icon btn-icon-md" title="Ver Liquidación">';
                            actions += '<i class="fa fa-solid fa-dollar-sign"></i>';
                            actions += '</a>';
                            actions += '<a href="#" class="sale_scop btn btn-sm btn-clean btn-icon btn-icon-md" title="Asignar Scop">';
                            actions += '<i class="la la-edit"></i>';
                            actions += '</a>';
                            actions += '</div>'
                            return actions;
                        },
                    },
                    {
                        field: 'sale_id',
                        title: 'Id',
                        width: 100,
                        textAlign: 'center',
                    },
                    {
                        field: 'document_type_id',
                        title: 'Id Documento',
                        width: 100,
                        textAlign: 'center',
                    },
                    {
                        field: 'company_name',
                        title: 'Compañía',
                        width: 100,
                        textAlign: 'center',
                    },
                    {
                        field: 'sale_date',
                        title: 'Fecha Emisión',
                        width: 100,
                        textAlign: 'center',
                    },
                    {
                        field: 'expiry_date',
                        title: 'Fecha Vencimiento',
                        width: 100,
                        textAlign: 'center',
                    },
                    {
                        field: 'client_name',
                        title: 'Cliente',
                        width: 180,
                        textAlign: 'center',
                    },
                    {
                        field: 'document_type_name',
                        title: 'Documento',
                        width: 100,
                        textAlign: 'center',
                    },
                    {
                        field: 'client_route_name',
                        title: 'Ruta',
                        width: 80,
                        textAlign: 'center',
                    },
                    {
                        field: 'comprobante',
                        title: 'Comprobante',
                        width: 100,
                        textAlign: 'center',
                    },
                    {
                        field: 'license_plate',
                        title: 'Placa',
                        width: 100,
                        textAlign: 'center',
                    },
                    {
                        field: 'total_venta',
                        title: 'Venta Total',
                        width: 100,
                        textAlign: 'center',
                    },
                    {
                        field: 'saldo_venta',
                        title: 'Saldo Pendiente',
                        width: 100,
                        textAlign: 'center',
                    },
                    {
                        field: 'pagado_venta',
                        title: 'Pagado',
                        width: 100,
                        textAlign: 'center',
                    },
                    {
                        field: 'scop_number',
                        title: 'SCOP',
                        width: 100,
                        textAlign: 'center',
                    },

                ]
            });

            this.table_sales.on('kt-datatable--on-layout-updated', function () {
                $('.kt-datatable__head th.kt-datatable__cell--check .kt-checkbox input[type="checkbox"]').remove();
                $('.kt-datatable__head th.kt-datatable__cell--check').empty();
            });

            this.table_sales.on('kt-datatable--on-check kt-datatable--on-uncheck', function (e) {

                let rows = vm.table_sales.rows('.kt-datatable__row--active').nodes();

                vm.selectedSales = [];

                rows.each(function () {
                    let $row = $(this);

                    let saleId = $row.find('td[data-field="sale_id"] span').text();
                    let docTypeId = $row.find('td[data-field="document_type_id"] span').text();
                    let scop = $row.find('td[data-field="scop_number"] span').text();

                    if (docTypeId.trim() === '5') {
                        if (!scop || scop.trim() === '' || scop.trim() === '0') {

                            Swal.fire('Aviso', 'La venta ' + saleId + ' no tiene SCOP asignado', 'warning');

                            $row.removeClass('kt-datatable__row--active');
                            $row.find('input[type="checkbox"]').prop('checked', false);
                            return;
                        }
                    }

                    vm.selectedSales.push(saleId);
                });
            });

            this.table_sales.columns('sale_id').visible(false);
            this.table_sales.columns('document_type_id').visible(false);
        },
        manageActions: function (event) {
            if ($(event.target).hasClass('sale_detail')) {
                event.preventDefault();
                let sale_id = $(event.target).parents('tr').find('td[data-field="sale_id"] span').html();

                EventBus.$emit('loading', true);
                axios.post(this.url_sale_detail, {
                    sale_id: sale_id,
                }).then(response => {
                    EventBus.$emit('loading', false);
                    EventBus.$emit('show_sale_detail', response.data);
                }).catch(error => {
                    console.log(error);
                    console.log(error.response);
                });

            } else if ($(event.target).hasClass('sale_liquidation')) {
                event.preventDefault();
                let sale_id = $(event.target).parents('tr').find('td[data-field="sale_id"] span').html();

                EventBus.$emit('loading', true);
                axios.post(this.url_liquidation_detail, {
                    sale_id: sale_id,
                }).then(response => {
                    EventBus.$emit('loading', false);
                    EventBus.$emit('show_liquidation_detail', response.data);
                }).catch(error => {
                    console.log(error);
                    console.log(error.response);
                });

            } else if ($(event.target).hasClass('sale_scop')) {
                event.preventDefault();
                let sale_id = $(event.target).parents('tr').find('td[data-field="sale_id"] span').html();

                EventBus.$emit('loading', true);
                axios.post(this.url_asign_scop, {
                    sale_id: sale_id,
                }).then(response => {
                    EventBus.$emit('loading', false);
                    EventBus.$emit('show_asign_scop', response.data);
                }).catch(error => {
                    console.log(error);
                    console.log(error.response);
                });

            }
        },
        registerMoney: function () {

            EventBus.$emit('loading', true);

            axios.post(this.url_detail_money, {
                model: this.model,
            }).then(response => {
                EventBus.$emit('loading', false);
                EventBus.$emit('show_register_money', response.data);
            }).catch(error => {
                console.log(error);
                console.log(error.response);
            });


        },
    }
};
</script>