<template>
    <div>
        <!--begin::Portlet-->
        <div class="kt-portlet" v-if="show_table">
            <div class="kt-portlet__head">
                <div class="kt-portlet__head-label">
                    <h3 class="kt-portlet__head-title">
                        Resultado
                    </h3>
                </div>
                <div class="kt-portlet__head-toolbar">
                    <div class="kt-portlet__head-wrapper">
                        <div class="dropdown dropdown-inline">
                            <a href="#" class="btn btn-outline-brand btn-bold btn-sm" @click.prevent="exportExcel()">
                                <i class="fa fa-file-excel"></i> Exportar Excel
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
                                placeholder="Buscar por ID, Razón Social o Telefono">
                        </div>
                    </div>
                </div>
            </div>

            <div class="kt-portlet__body kt-portlet__body--fit">
                <!--begin: Datatable -->
                <div class="kt-datatable"></div>
                <!--end: Datatable -->
            </div>
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
            default: ''
        },
    },
    data() {
        return {
            order_report_table: undefined,
            show_table: false,
            model: {},
            export_excel: false,
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
                if (vm.order_report_table == undefined) {
                    vm.fillTableX();
                } else {
                    EventBus.$emit('loading', true);
                    vm.order_report_table.setDataSourceParam('model', vm.model);
                    vm.order_report_table.load();
                }

                vm.order_report_table.on('kt-datatable--on-ajax-done', function () {
                    EventBus.$emit('loading', false);
                });
            });
        }.bind(this));

        EventBus.$on('refresh_table', function () {
            if (this.order_report_table != undefined) {
                EventBus.$emit('loading', true);
                this.order_report_table.setDataSourceParam('model', this.model);
                this.order_report_table.load();
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

            this.order_report_table = $('.kt-datatable').KTDatatable({
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
                        field: 'estado',
                        title: 'ESTADO',
                        width: 140,
                        textAlign: 'left',
                        template: function (row) {

                            let estados = {
                                'Pendiente de Entrega': {
                                    class: 'badge badge-warning'
                                },
                                'Entregado': {
                                    class: 'badge badge-success'
                                },
                                'Cancelado': {
                                    class: 'badge badge-danger'
                                }
                            };

                            if (!estados[row.estado]) {
                                return row.estado;
                            }

                            return `
                                <span class="badge ${estados[row.estado].class}">
                                    ${row.estado}
                                </span>
                            `;
                        }
                    },
                    {
                        field: 'codigo',
                        title: 'PEDIDO',
                        width: 100,
                        textAlign: 'left',
                    },
                    {
                        field: 'nom_cliente',
                        title: 'RAZON SOCIAL',
                        width: 150,
                        textAlign: 'left',
                    },
                    {
                        field: 'creacion_cli',
                        title: 'CREACION CLIENTE',
                        width: 100,
                        textAlign: 'left',
                    },
                    {
                        field: 'ruta',
                        title: 'RUTA',
                        width: 80,
                        textAlign: 'center',
                    },
                    {
                        field: 'zona',
                        title: 'ZONA',
                        width: 80,
                        textAlign: 'center',
                    },
                    {
                        field: 'sector',
                        title: 'SECTOR',
                        width: 80,
                        textAlign: 'center',
                    },
                    {
                        field: 'fecha_emision',
                        title: 'FECHA EMISION',
                        width: 80,
                        textAlign: 'center',
                    },
                    {
                        field: 'articulo',
                        title: 'PRODUCTO',
                        width: 90,
                        textAlign: 'center',
                    },
                    {
                        field: 'cantidad',
                        title: 'CANTIDAD',
                        width: 70,
                        textAlign: 'center',
                    },
                    {
                        field: 'precio',
                        title: 'PRECIO',
                        width: 70,
                        textAlign: 'center',
                    },
                    {
                        field: 'total',
                        title: 'VENTA',
                        width: 70,
                        textAlign: 'center',
                    },

                ]
            });

        },
        exportExcel: function () {
            EventBus.$emit('loading', true);
            this.export_excel = true;

            axios.post(this.url, {
                model: this.model,
                export_excel: this.export_excel,
            }, {
                responseType: 'blob',
            }).then(response => {
                EventBus.$emit('loading', false);

                let creation_date = new Date();
                let date = creation_date.getDate();
                let month = creation_date.getMonth() + 1;
                let year = creation_date.getFullYear();
                creation_date = `${year}-${month}-${date}`;

                const url = window.URL.createObjectURL(new Blob([response.data]));
                const link = document.createElement('a');
                link.href = url;
                link.setAttribute('download', `reporte-registro-pedidos-${creation_date}.xlsx`);
                document.body.appendChild(link);
                link.click();

                this.export_excel = false;
            }).catch(error => {
                console.log(error);
                console.log(error.response);
                EventBus.$emit('loading', false);
            });
        },
    }
};
</script>