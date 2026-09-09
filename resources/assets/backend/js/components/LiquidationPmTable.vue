<template>
    <!--begin::Portlet-->
    <div class="kt-portlet">
        <div class="kt-portlet__head">
            <div class="kt-portlet__head-label">
                <h3 class="kt-portlet__head-title">
                    LIQUIDACIONES
                </h3>
            </div>
            <div class="kt-portlet__head-toolbar">
                <div class="kt-portlet__head-wrapper">
                    <div class="dropdown dropdown-inline">
                        <a href="#" @click.prevent="exportExcel()" class="btn btn-primary" id="exportRecord">
                            <i class="fa fa-file-excel"></i> Exportar excel
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
                            placeholder="Buscar...">
                    </div>
                </div>
            </div>
        </div>

        <div class="kt-portlet__body kt-portlet__body--fit" @click="manageActions">
            <!--begin: Datatable -->
            <div class="kt-datatable"></div>
            <!--end: Datatable -->
        </div>
    </div>
    <!--end::Portlet-->
</template>

<script>
import EventBus from '../event-bus';
export default {
    props: {
        url: {
            type: String,
            default: ''
        },
        url_delete: {
            type: String,
            default: ''
        },
        url_aprobar: {
            type: String,
            default: ''
        },
        url_pdf: {
            type: String,
            default: ''
        },
        url_get_detail: {
            type: String,
            default: ''
        },
    },
    data() {
        return {
            datatable: undefined,
            warehouse_type_id: '',
            flag_export: false,
        }
    },
    created() {

    },
    mounted() {
        if (this.datatable == undefined) {
            this.fillTableX();
        } else {
            this.datatable.load();
        }

        this.datatable.on('kt-datatable--on-ajax-done', function () {
            EventBus.$emit('loading', false);
        });

        EventBus.$on('refresh_table', function () {
            this.datatable.load();
        }.bind(this));
    },
    watch: {

    },
    computed: {

    },
    methods: {

        exportExcel: function () {
            EventBus.$emit('loading', true);

            this.flag_export = true;

            axios.post(this.url, {
                flag_export: this.flag_export
            }, {
                responseType: 'blob',
            }).then(response => {
                // console.log(response);
                EventBus.$emit('loading', false);

                let creation_date = new Date();
                let date = creation_date.getDate();
                let month = creation_date.getMonth() + 1;
                let year = creation_date.getFullYear();
                creation_date = year + '-' + month + '-' + date;

                const url = window.URL.createObjectURL(new Blob([response.data]));
                const link = document.createElement('a');
                link.href = url;
                link.setAttribute('download', 'reporte-orden-trabajo-' + creation_date + '.xlsx'); //or any other extension
                document.body.appendChild(link);
                link.click();
            }).catch(error => {
                console.log(error);
                console.log(error.response);
            });
        },
        fillTableX: function () {
            let vm = this;
            let token = document.head.querySelector('meta[name="csrf-token"]').content;

            this.datatable = $('.kt-datatable').KTDatatable({
                // datasource definition
                data: {
                    type: 'remote',
                    source: {
                        read: {
                            url: vm.url,
                            params: {
                                _token: token,
                            }
                        },
                    }
                },
                pageSize: 10,
                serverPaging: true,
                serverFiltering: true,
                serverSorting: true,
                // layout definition
                layout: {
                    scroll: true, // enable/disable datatable scroll both horizontal and vertical when needed.
                    height: 400,
                    footer: false // display/hide footer
                },

                // column sorting
                sortable: true,
                pagination: true,

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
                        field: 'id',
                        title: 'ID',
                        width: 50,
                        textAlign: 'center',
                    },
                    {
                        field: 'state',
                        title: 'Estado',
                        sortable: false,
                        width: 80,
                        overflow: 'visible',
                        autoHide: false,
                        textAlign: 'center',
                        template: function (row) {
                            let actions = '<div class="actions">';
                            if (row.ose == 5) {
                                actions += '<p class="badge bg-info text-white">Generado</p>';
                            } else if (row.ose == 0) {
                                actions += '<p class="badge bg-success text-white">Liquidado</p>';
                            } else if (row.ose == 1) {
                                actions += '<p class="badge bg-primary text-white">Enviado a OSE</p>';
                            }
                            else if (row.ose == 3) {
                                actions += '<p class="badge bg-danger text-white">Anulado</p>';
                            }
                            return actions;
                        },
                    },
                    {
                        field: 'company_name',
                        title: 'Compañia',
                        width: 100,
                        textAlign: 'center',
                    },
                    {
                        field: 'payment_name',
                        title: 'Condición de Pago',
                        width: 100,
                        textAlign: 'center',
                    },
                    {
                        field: 'limit',
                        title: 'Límite',
                        width: 100,
                        textAlign: 'center',
                    },
                    {
                        field: 'voucher_type_name',
                        title: 'Comprobante',
                        width: 100,
                        textAlign: 'center',
                    },
                    {
                        field: 'factura',
                        title: 'Factura',
                        width: 70,
                        textAlign: 'center',
                    },
                    {
                        field: 'cotizacion',
                        title: 'Cotizacion',
                        width: 70,
                        textAlign: 'center',
                    },
                    {
                        field: 'client_name',
                        title: 'Cliente',
                        width: 120,
                        textAlign: 'center',
                    },
                    {
                        field: 'subtotal',
                        title: 'SubTotal',
                        width: 80,
                        textAlign: 'center',
                    },
                    {
                        field: 'igv',
                        title: 'IGV',
                        width: 80,
                        textAlign: 'center',
                    },
                    {
                        field: 'total',
                        title: 'Total',
                        width: 80,
                        textAlign: 'center',
                    },
                    {
                        field: 'options',
                        title: 'Opciones',
                        sortable: false,
                        width: 110,
                        overflow: 'visible',
                        autoHide: false,
                        textAlign: 'center',
                        class: 'td-sticky',
                        template: function (row) {
                            let actions = '<div class="actions">';

                            if (row.ose == 5) {
                                actions += '<a href="#" class="liquidar btn btn-sm btn-clean btn-icon btn-icon-md" title="Liquidar">';
                                actions += '<i class="la la-cart-plus"></i>';
                                actions += '</a>';
                            }
                            actions += '<a href="#" class="detalle btn btn-sm btn-clean btn-icon btn-icon-md" title="Ver Detalle">';
                            actions += '<i class="la la-eye"></i>';
                            actions += '</a>';

                            actions += '</div>';
                            return actions;
                        },
                    },
                ]
            });

            this.datatable.columns('id').visible(false);
        },
        manageActions: function (event) {
            if ($(event.target).hasClass('detalle')) {
                event.preventDefault();
                let id = $(event.target).parents('tr').find('td[data-field="id"] span').html();
                let client = $(event.target).parents('tr').find('td[data-field="client_name"] span').html();
                let factura = $(event.target).parents('tr').find('td[data-field="factura"] span').html();
                let cotizacion = $(event.target).parents('tr').find('td[data-field="cotizacion"] span').html();

                EventBus.$emit('loading', true);

                axios.post(this.url_get_detail, {
                    id: id
                }).then(response => {
                    EventBus.$emit('loading', false);
                    EventBus.$emit('show_liquidation_detail', response.data, client, factura, cotizacion);
                    // console.log(response.data);
                }).catch(error => {
                    console.log(error);
                    console.log(error.response);
                });


            }
            if ($(event.target).hasClass('liquidar')) {
                event.preventDefault();
                let id = $(event.target).parents('tr').find('td[data-field="id"] span').html();
                let client = $(event.target).parents('tr').find('td[data-field="client_name"] span').html();
                let factura = $(event.target).parents('tr').find('td[data-field="factura"] span').html();
                let cotizacion = $(event.target).parents('tr').find('td[data-field="cotizacion"] span').html();
                let total = $(event.target).parents('tr').find('td[data-field="total"] span').html();
                let payment_name = $(event.target).parents('tr').find('td[data-field="payment_name"] span').html();
                let limit = $(event.target).parents('tr').find('td[data-field="limit"] span').html();

                EventBus.$emit('liquidation_modal',id,factura,cotizacion,client,total,payment_name,limit);

            }
        },
    }
};
</script>