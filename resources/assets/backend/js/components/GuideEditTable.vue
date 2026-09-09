<template>
    <!--begin::Portlet-->
    <div class="kt-portlet">
        <div class="kt-portlet__head">
            <div class="kt-portlet__head-label">
                <h3 class="kt-portlet__head-title">
                    Facturas
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
        url_get_detail: {
            type: String,
            default: ''
        },
    },
    data() {
        return {
            datatable: undefined,
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

        EventBus.$on('refresh_table', function () {
            this.datatable.load();

            this.datatable.on('kt-datatable--on-ajax-done', function () {
                EventBus.$emit('loading', false);
            });

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
                        field: 'guide_id',
                        title: 'ID',
                        width: 50,
                        textAlign: 'center',
                    },
                    {
                        field: 'movement_type',
                        title: 'Tipo',
                        width: 100,
                        textAlign: 'center',
                    },
                    {
                        field: 'company_name',
                        title: 'Compañia',
                        width: 70,
                        textAlign: 'center',
                    },
                    {
                        field: 'guia',
                        title: 'Guía Electrónica',
                        width: 100,
                        textAlign: 'center',
                    },
                    {
                        field: 'guia_fisica',
                        title: 'Guía Física',
                        width: 100,
                        textAlign: 'center',
                    },
                    {
                        field: 'ruta',
                        title: 'Ruta',
                        width: 70,
                        textAlign: 'center',
                    },
                    {
                        field: 'sub_canal',
                        title: 'Sub Canal',
                        width: 100,
                        textAlign: 'center',
                    },
                    {
                        field: 'placa_principal',
                        title: 'Placa del Vehículo',
                        width: 100,
                        textAlign: 'center',
                    },
                    {
                        field: 'chofer',
                        title: 'Chofer',
                        width: 120,
                        textAlign: 'center',
                    },
                    {
                        field: 'fecha_remision',
                        title: 'Fecha de Emisión',
                        width: 80,
                        textAlign: 'center',
                    },
                    {
                        field: 'fecha_despacho',
                        title: 'Fecha de Traslado',
                        width: 80,
                        textAlign: 'center',
                    },
                    {
                        field: 'scop',
                        title: 'SCOP',
                        width: 120,
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

                            if (row.cerrado != 1) {
                                actions += '<a href="#" class="edit btn btn-sm btn-clean btn-icon btn-icon-md" title="Editar">';
                                actions += '<i class="la la-edit"></i>';
                                actions += '</a>';
                            }

                            actions += '</div>';
                            return actions;
                        },
                    },
                ]
            });

            this.datatable.columns('guide_id').visible(false);
        },
        manageActions: function (event) {
            if ($(event.target).hasClass('edit')) {
                event.preventDefault();
                let guide_id = $(event.target).parents('tr').find('td[data-field="guide_id"] span').html();

                EventBus.$emit('loading', true);

                axios.post(this.url_get_detail, {
                    guide_id: guide_id
                }).then(response => {
                    EventBus.$emit('loading', false);
                    EventBus.$emit('detail', response.data);
                    // console.log(response.data);
                }).catch(error => {
                    console.log(error);
                    console.log(error.response);
                });


            }
        },
    }
};
</script>
