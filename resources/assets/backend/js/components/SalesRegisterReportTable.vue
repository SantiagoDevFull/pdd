<template>
    <!--begin::Portlet-->
    <div class="kt-portlet kt-portlet--mobile" v-show="show_table">
        <div class="kt-portlet__head kt-portlet__head--lg">
            <div class="kt-portlet__head-label">
                <span class="kt-portlet__head-icon">
                    <i class="kt-font-brand la la-file-text"></i>
                </span>
                <h3 class="kt-portlet__head-title">
                    Resultado
                </h3>
            </div>
            <div class="kt-portlet__head-toolbar">
                <div class="kt-portlet__head-wrapper">
                    <div class="dropdown dropdown-inline">
                        <a href="#" @click.prevent="exportExcel()" class="btn btn-brand btn-icon-sm">
                            <i class="fa fa-file-excel"></i> Exportar Excel
                        </a>
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
</template>

<script>
import EventBus from '../event-bus';
export default {
    props: {
        url_list: {
            type: String,
            default: ''
        },
    },
    data() {
        return {
            datatable: undefined,
            show_table: false,
            model: {},
            export: false
        }
    },
    mounted() {
        EventBus.$on('show_table', function (response) {
            this.show_table = true;
            this.model = response.data;

            if (this.datatable != undefined) {
                this.elements = [];
                this.datatable.destroy();
                this.fillTableX();
            } else {
                this.fillTableX();
            }

        }.bind(this));

    },
    methods: {
        getInfo: function (data) {
            this.fillTableX(data);
        },
        fillTableX: function () {
            let url_list = this.url_list;
            let token = document.head.querySelector('meta[name="csrf-token"]').content;

            let vm = this;
            this.datatable = $('.kt-datatable').KTDatatable({
                // datasource definition
                data: {
                    type: 'remote',
                    source: {
                        read: {
                            url: url_list,
                            params: {
                                _token: token,
                                model: vm.model
                            }
                        },
                    },
                    pageSize: 10,
                    // serverPaging: true,
                    // serverFiltering: true,
                    // serverSorting: true,
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

                // search: {
                //     input: $('#generalSearch'),
                // },

                extensions: {
                    checkbox: {},
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
                        field: "serie_number",
                        title: "Serie",
                        width: 50,
                        textAlign: 'center',
                    },
                    {
                        field: "voucher_number",
                        title: "N°",
                        width: 50,
                        sortable: true,
                        textAlign: 'center',
                    },
                    {
                        field: "issue_date",
                        title: "Fecha de Emisión",
                        width: 80,
                        sortable: true,
                        textAlign: 'center',
                    },
                    {
                        field: "client_document_number",
                        title: "RUC/DNI/CE",
                        width: 100,
                        sortable: true,
                        textAlign: 'center',
                    },
                    {
                        field: "client_name",
                        title: "Razón Social",
                        width: 120,
                        sortable: true,
                        textAlign: 'center',
                    },
                    {
                        field: "total",
                        title: "Total Venta",
                        width: 70,
                        sortable: true,
                        textAlign: 'center',
                    },
                    {
                        field: "igv_perception_percentage",
                        title: "Percepción %",
                        width: 70,
                        sortable: true,
                        textAlign: 'center',
                    },
                    {
                        field: "percepcion",
                        title: "Percepción",
                        width: 70,
                        sortable: true,
                        textAlign: 'center',
                    },
                    {
                        field: "total_perception",
                        title: "Total Percepción",
                        width: 70,
                        sortable: true,
                        textAlign: 'center',
                    },
                    {
                        field: "credit_note_reference_serie",
                        title: "Serie de Ref.",
                        width: 50,
                        sortable: true,
                        textAlign: 'center',
                    },
                    {
                        field: "credit_note_reference_number",
                        title: "N° de Ref.",
                        width: 50,
                        sortable: true,
                        textAlign: 'center',
                    },
                    {
                        field: "issue_date",
                        title: "Fecha Ref.",
                        width: 80,
                        sortable: true,
                        textAlign: 'center',
                    },
                    {
                        field: "ose_status",
                        title: "Efact",
                        width: 80,
                        sortable: true,
                        textAlign: 'center',
                    },

                ]
            });

        },
        exportExcel: function () {
            EventBus.$emit('loading', true);
            this.export = true;
            axios.post(this.url_list, {
                model: this.model,
                export: this.export
            }, {
                responseType: 'blob',
            }).then(response => {
                EventBus.$emit('loading', false);
                console.log(response);

                const url = window.URL.createObjectURL(new Blob([response.data]));
                const link = document.createElement('a');
                link.href = url;
                link.setAttribute('download', 'reporte-de-ventas.xls'); //or any other extension
                document.body.appendChild(link);
                link.click();
            }).catch(error => {
                EventBus.$emit('loading', false);
                console.log(error);
                console.log(error.response);
            });
        },
    }
};
</script>