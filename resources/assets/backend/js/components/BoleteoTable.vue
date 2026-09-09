<template>
    <!--begin::Portlet-->
    <div class="kt-portlet">
        <div class="kt-portlet__head">
            <div class="kt-portlet__head-label">
                <h3 class="kt-portlet__head-title">
                    Resultado
                </h3>
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
    },
    data() {
        return {
            datatable: undefined,
            model: '',
        }
    },
    created() {

    },
    mounted() {
        EventBus.$on('show_table', function (response) {
            this.model = response.data;
            if (this.datatable == undefined) {
                this.fillTableX();
            } else {
                this.datatable.setDataSourceParam('model', this.model);
                this.datatable.load();
            }

            this.datatable.on('kt-datatable--on-ajax-done', function () {
                EventBus.$emit('loading', false);
            });

        }.bind(this));

        EventBus.$on('refresh_table', function () {
            let vm = this;

            this.datatable.one('kt-datatable--on-ajax-done', function () {
                EventBus.$emit('loading', false);
            });

            this.datatable.load();
        }.bind(this));
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
                            }
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
                        field: 'options',
                        title: 'Opciones',
                        sortable: false,
                        width: 80,
                        overflow: 'visible',
                        autoHide: false,
                        textAlign: 'right',
                        template: function (row) {
                            let actions = '<div class="actions">';
                            actions += '<a href="#" class="edit btn btn-sm btn-clean btn-icon btn-icon-md" title="Editar">';
                            actions += '<i class="la la-edit"></i>';
                            actions += '</a>';
                            actions += '</div>';
                            return actions;
                        },
                    },
                    {
                        field: 'voucher_id',
                        title: 'Id',
                        width: 80,
                        textAlign: 'center',
                    },
                    {
                        field: 'fecha_emision',
                        title: 'Fecha Emisión',
                        width: 80,
                        textAlign: 'center',
                    },
                    {
                        field: 'serie',
                        title: 'Serie',
                        width: 80,
                        textAlign: 'center',
                    },
                    {
                        field: 'min_voucher',
                        title: 'Rango Inicial',
                        width: 80,
                        textAlign: 'center',
                    },
                    {
                        field: 'max_voucher',
                        title: 'Rango Final',
                        width: 80,
                        textAlign: 'center',
                    },
                    {
                        field: 'voucher_type_name',
                        title: 'Tipo de Comprobante',
                        width: 120,
                        textAlign: 'center',
                    },
                    {
                        field: 'article_name',
                        title: 'Artículo',
                        width: 200,
                        textAlign: 'center',
                    },
                    {
                        field: 'total_cantidad',
                        title: 'Cantidad',
                        width: 80,
                        textAlign: 'center',
                    },
                    {
                        field: 'total_kilos',
                        title: 'Volumen en KG',
                        width: 80,
                        textAlign: 'center',
                    },
                    {
                        field: 'price_unit',
                        title: 'Precio x KG',
                        width: 80,
                        textAlign: 'center',
                    },
                    {
                        field: 'total_venta',
                        title: 'Total Venta',
                        width: 80,
                        textAlign: 'center',
                    },
                    {
                        field: 'article_cod',
                        title: 'Total Venta',
                        width: 80,
                        textAlign: 'center',
                    },


                ]
            });

            this.datatable.columns('voucher_id').visible(false);
            this.datatable.columns('article_cod').visible(false);
        },
        manageActions: function (event) {
            if ($(event.target).hasClass('edit')) {
                event.preventDefault();
            
                let serie = $(event.target).parents('tr').find('td[data-field="serie"] span').html();
                let min_voucher = $(event.target).parents('tr').find('td[data-field="min_voucher"] span').html();
                let max_voucher = $(event.target).parents('tr').find('td[data-field="max_voucher"] span').html();
                let article_name = $(event.target).parents('tr').find('td[data-field="article_name"] span').html();
                let fecha_emision = $(event.target).parents('tr').find('td[data-field="fecha_emision"] span').html();

                EventBus.$emit('loading', true);
                EventBus.$emit('edit_modal', serie,min_voucher,max_voucher, article_name,fecha_emision);
                EventBus.$emit('loading', false);

            }
        },
    }
};
</script>