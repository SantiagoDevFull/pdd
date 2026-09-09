<template>
    <!--begin::Portlet-->
    <div class="kt-portlet">
        <div class="kt-portlet__head">
            <div class="kt-portlet__head-label">
                <span class="kt-portlet__head-icon">
                    <i class="kt-font-brand la la-file-text"></i>
                </span>
                <h3 class="kt-portlet__head-title">
                    Facturas por Pagar
                </h3>
            </div>
            <div class="kt-portlet__head-toolbar">
                <div class="kt-portlet__head-wrapper">
                    <div class="dropdown dropdown-inline">
                        <a href="#" @click.prevent="exportExcel()" class="btn btn-primary disabled" id="excel_export">
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
        url_get_detail: {
            type: String,
            default: ''
        },
        url_get_file: {
            type: String,
            default: ''
        },
    },
    data() {
        return {
            datatable: undefined,
            model: {},
            flag_export: false,
            show_table: false
        }
    },
    mounted() {
        EventBus.$on('show_table', function (data) {
            this.show_table = true;
            this.model = data;
            if (this.datatable == undefined) {
                this.fillTableX();
            } else {
                this.datatable.setDataSourceParam('model', this.model);
                this.datatable.load();
            }

            this.datatable.on('kt-datatable--on-ajax-done', function () {
                EventBus.$emit('loading', false);
            });

            document.getElementById('excel_export').classList.remove('disabled');
        }.bind(this));

        EventBus.$on('refresh_table', function () {
            if (this.datatable != undefined) {
                this.datatable.load();
            }
        }.bind(this));
    },
    watch: {

    },
    computed: {

    },
    methods: {
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
                                model: vm.model,
                                export: vm.export,
                            }
                        },
                    }
                },
                pageSize: 0,
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
                        field: 'movement_id',
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
                            if (row.state == 0) {
                                actions += '<p class="badge bg-warning text-white">Pendiente</p>';
                            } else if (row.state == 1) {
                                actions += '<p class="badge bg-success text-white">Aprobado</p>';
                            }
                            else if (row.state == 2) {
                                actions += '<p class="badge bg-danger text-white">Desaprobado</p>';
                            }
                            else if (row.state == 3) {
                                actions += '<p class="badge bg-dark text-white">Liquidado</p>';
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
                        field: 'provider_name',
                        title: 'Proveedor',
                        width: 100,
                        textAlign: 'center',
                    },
                    {
                        field: 'area_name',
                        title: 'Área',
                        width: 100,
                        textAlign: 'center',
                    },
                    {
                        field: 'centro_costos',
                        title: 'Centro de Costo',
                        width: 100,
                        textAlign: 'center',
                    },
                    {
                        field: 'factura',
                        title: 'Factura',
                        width: 80,
                        textAlign: 'center',
                    },
                    {
                        field: 'currency_name',
                        title: 'Moneda',
                        width: 80,
                        textAlign: 'center',
                    },
                    {
                        field: 'total',
                        title: 'Valor Total',
                        width: 80,
                        textAlign: 'center',
                    },
                    {
                        field: 'paid',
                        title: 'Pagado',
                        width: 80,
                        textAlign: 'center',
                    },
                    {
                        field: 'balance',
                        title: 'Por pagar',
                        width: 80,
                        textAlign: 'center',
                    },
                    {
                        field: 'movement_date',
                        title: 'Fecha de Emisión',
                        width: 80,
                        textAlign: 'center',
                    },
                    {
                        field: 'fecha_pago',
                        title: 'Fecha de Vencimiento',
                        width: 80,
                        textAlign: 'center',
                    },
                    {
                        field: 'observation',
                        title: 'Observaciones',
                        width: 100,
                        textAlign: 'center',
                    },
                    {
                        field: 'options',
                        title: 'Opciones',
                        sortable: false,
                        width: 150,
                        overflow: 'visible',
                        autoHide: false,
                        textAlign: 'center',
                        class: 'td-sticky',
                        template: function (row) {
                            let actions = '<div class="actions">';

                            if (row.state == 0) {
                                actions += '<a href="#" class="aprobar btn btn-sm btn-clean btn-icon btn-icon-md" title="Aprobar">';
                                actions += '<i class="la la-check"></i>';
                                actions += '</a>';

                                actions += '<a href="#" class="desaprobar btn btn-sm btn-clean btn-icon btn-icon-md" title="Desaprobar">';
                                actions += '<i class="la la-close"></i>';
                                actions += '</a>';
                            }
                            if (row.state == 1) {
                                actions += '<a href="#" class="liquidar btn btn-sm btn-clean btn-icon btn-icon-md" title="Liquidar">';
                                actions += '<i class="la la-cart-plus"></i>';
                                actions += '</a>';
                            }
                            actions += '<a href="#" class="detalle btn btn-sm btn-clean btn-icon btn-icon-md" title="Ver Detalle">';
                            actions += '<i class="la la-eye"></i>';
                            actions += '</a>';

                            actions += '<a href="#" class="archivo btn btn-sm btn-clean btn-icon btn-icon-md" title="Descargar archivo">';
                            actions += '<i class="la la-file"></i>';
                            actions += '</a>';

                            actions += '</div>';
                            return actions;
                        },
                    },
                ]
            });

            this.datatable.columns('movement_id').visible(false);
        },
        manageActions: function (event) {
            if ($(event.target).hasClass('desaprobar')) {
                event.preventDefault();
                let movement_id = $(event.target).parents('tr').find('td[data-field="movement_id"] span').html();
                let factura = $(event.target).parents('tr').find('td[data-field="factura"] span').html();

                Swal.fire({
                    title: '¡Cuidado!',
                    text: '¿Seguro que desea desaprobar la Factura -> ' + factura + '?',
                    type: "warning",
                    heightAuto: false,
                    showCancelButton: true,
                    confirmButtonText: 'Sí',
                    cancelButtonText: 'No',
                    input: 'text',
                    inputPlaceholder: 'Ingrese el motivo de desaprobación',
                    inputValidator: (value) => {
                        if (!value) {
                            return '¡Debe ingresar un motivo para desaprobar la factura!';
                        }
                    }
                }).then(result => {
                    EventBus.$emit('loading', true);

                    if (result.value) {
                        axios.post(this.url_delete, {
                            movement_id: movement_id,
                            observation: result.value
                        }).then(response => {
                            this.datatable.load();
                            EventBus.$emit('loading', false);
                            Swal.fire({
                                title: '¡Ok!',
                                text: 'Factura -> ' + factura + ' desaprobado correctamente.',
                                type: "success",
                                heightAuto: false,
                            });
                        }).catch(error => {
                            console.log(error);
                            console.log(error.response);
                        });
                    } else if (result.dismiss == Swal.DismissReason.cancel) {
                        EventBus.$emit('loading', false);
                    }
                });

            } else if ($(event.target).hasClass('aprobar')) {

                event.preventDefault();
                let movement_id = $(event.target).parents('tr').find('td[data-field="movement_id"] span').html();
                let factura = $(event.target).parents('tr').find('td[data-field="factura"] span').html();

                Swal.fire({
                    title: '¡Cuidado!',
                    text: '¿Seguro que desea aprobar la Factura -> ' + factura + '?',
                    type: "warning",
                    heightAuto: false,
                    showCancelButton: true,
                    confirmButtonText: 'Sí',
                    cancelButtonText: 'No'
                }).then(result => {
                    EventBus.$emit('loading', true);

                    if (result.value) {
                        axios.post(this.url_aprobar, {
                            movement_id: movement_id,
                        }).then(response => {
                            this.datatable.load();
                            EventBus.$emit('loading', false);
                            Swal.fire({
                                title: '¡Ok!',
                                text: 'Factura -> ' + factura + ' aprobado correctamente.',
                                type: "success",
                                heightAuto: false,
                            });
                        }).catch(error => {
                            console.log(error);
                            console.log(error.response);
                        });
                    } else if (result.dismiss == Swal.DismissReason.cancel) {
                        EventBus.$emit('loading', false);
                    }
                });
            } else if ($(event.target).hasClass('detalle')) {
                event.preventDefault();

                let movement_id = $(event.target).parents('tr').find('td[data-field="movement_id"] span').html();
                let factura = $(event.target).parents('tr').find('td[data-field="factura"] span').html();
                let provider_name = $(event.target).parents('tr').find('td[data-field="provider_name"] span').html();

                EventBus.$emit('loading', true);

                axios.post(this.url_get_detail, {
                    movement_id: movement_id
                }).then(response => {
                    EventBus.$emit('loading', false);
                    EventBus.$emit('show_cochera_movement_detail', response.data, factura, provider_name);
                    // console.log(response.data);
                }).catch(error => {
                    console.log(error);
                    console.log(error.response);
                });
            } else if ($(event.target).hasClass('liquidar')) {

                event.preventDefault();
                let id = $(event.target).parents('tr').find('td[data-field="movement_id"] span').html();
                let factura = $(event.target).parents('tr').find('td[data-field="factura"] span').html();
                let provider_name = $(event.target).parents('tr').find('td[data-field="provider_name"] span').html();   
                let vehicle_plate = $(event.target).parents('tr').find('td[data-field="vehicle_plate"] span').html();
                let total = $(event.target).parents('tr').find('td[data-field="total"] span').html();
                let currency_name = $(event.target).parents('tr').find('td[data-field="currency_name"] span').html();

                EventBus.$emit('liquidation_modal', id, factura, provider_name, vehicle_plate, total, currency_name);

            } else if ($(event.target).hasClass('archivo')) {
                event.preventDefault();

                let movement_id = $(event.target).parents('tr').find('td[data-field="movement_id"] span').html();

                axios.post(this.url_get_file, {
                    movement_id: movement_id
                }).then(response => {
                    EventBus.$emit('loading', false);
                    window.open(response.data, '_blank');
                    // console.log(response.data);
                }).catch(error => {
                    console.log(error);
                    console.log(error.response);
                });

                

            }
        },
        exportExcel: function () {
            EventBus.$emit('loading', true);
            this.export = 1;

            axios.post(this.url, {
                model: this.model,
                export: this.export,
            }, {
                responseType: 'blob',
            }).then(response => {
                console.log(response);
                EventBus.$emit('loading', false);

                const url = window.URL.createObjectURL(new Blob([response.data]));
                const link = document.createElement('a');
                link.href = url;
                link.setAttribute('download', 'reporte-de-facturas-por-pagar-' + Date.now() + '.xlsx'); //or any other extension
                document.body.appendChild(link);
                link.click();

                this.export = '';
            }).catch(error => {
                console.log(error);
                console.log(error.response);
                EventBus.$emit('loading', false);
            });
        },

    }
};
</script>