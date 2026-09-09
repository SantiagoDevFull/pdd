<template>
    <!--begin::Portlet-->
    <div class="kt-portlet">
        <div class="kt-portlet__head">
            <div class="kt-portlet__head-label">
                <h3 class="kt-portlet__head-title">
                    Resultado
                </h3>
            </div>
            <div class="kt-portlet__head-toolbar">
                <div class="kt-portlet__head-wrapper">
                    <div class="dropdown dropdown-inline">
                        <a href="#" @click.prevent="openModal()" class="btn btn-primary disabled" id="add_article">
                            <i class="fa fa-plus"></i> Crear presupuesto
                        </a>
                        <a href="#" @click.prevent="exportRecord()" class="btn btn-success disabled" id="exportRecord">
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
        url_detail: {
            type: String,
            default: ''
        },
        url_delete: {
            type: String,
            default: ''
        },
        url_export: {
            type: String,
            default: ''
        },
    },
    data() {
        return {
            datatable: undefined,
            anio: '',
            mes: '',
        }
    },
    created() {

    },
    mounted() {
        EventBus.$on('show_table', function (response) {
            this.anio = response.anio;
            this.mes = response.mes;
            if (this.datatable == undefined) {
                this.fillTableX();
            } else {
                this.datatable.setDataSourceParam('anio', this.anio);
                this.datatable.setDataSourceParam('mes', this.mes);
                this.datatable.load();
            }

            this.datatable.on('kt-datatable--on-ajax-done', function () {
                EventBus.$emit('loading', false);
            });

            document.getElementById('add_article').classList.remove('disabled');
            document.getElementById('exportRecord').classList.remove('disabled');
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
        openModal: function () {
            EventBus.$emit('create_modal', this.anio, this.mes);
        },
        exportRecord: function () {
            EventBus.$emit('loading', true);

            axios.post(this.url_export, {
                anio: this.anio,
                mes: this.mes,
            }, {
                responseType: 'blob',
            }).then(response => {
                EventBus.$emit('loading', false);
                console.log(response);

                const url = window.URL.createObjectURL(new Blob([response.data]));
                const link = document.createElement('a');
                link.href = url;
                link.setAttribute('download', 'reporte-de-presupuestos-ventas.xls');
                document.body.appendChild(link);
                link.click();
            }).catch(error => {
                EventBus.$emit('loading', false);
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
                                anio: vm.anio,
                                mes: vm.mes,
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
                    autoHide: true,
                },

                // columns definition
                columns: [
                    {
                        field: 'id',
                        title: 'ID',
                        width: 100,
                    },
                    {
                        field: 'anio',
                        title: 'AÑO',
                        width: 80,
                        align: 'center',
                    },
                    {
                        field: 'mes_nombre',
                        title: 'MES',
                        width: 80,
                        align: 'center',
                    },
                    {
                        field: 'business_unit_name',
                        title: 'UNIDAD',
                        width: 115,
                    },
                    {
                        field: 'client_sector_name',
                        title: 'CANAL',
                        width: 115,
                    },
                    {
                        field: 'client_channel_name',
                        title: 'SUB CANAL',
                        width: 115,
                    },
                    {
                        field: 'ruta',
                        title: 'RUTA',
                        width: 115,
                    },
                    {
                        field: 'manager_name',
                        title: 'SUPERVISOR',
                        width: 115,
                    },
                    {
                        field: 'ppto_venta',
                        title: 'PPTO VENTA',
                        width: 80,
                        align: 'right',
                    },
                    {
                        field: 'ppto_mb',
                        title: 'PPTO MB',
                        width: 80,
                        align: 'right',
                    },
                    {
                        field: 'options',
                        title: 'Opciones',
                        sortable: false,
                        width: 100,
                        overflow: 'visible',
                        autoHide: false,
                        textAlign: 'right',
                        template: function (row) {
                            let actions = '<div class="actions">';
                            actions += '<a href="#" class="edit btn btn-sm btn-clean btn-icon btn-icon-md" title="Editar">';
                            actions += '<i class="la la-edit"></i>';
                            actions += '</a>';
                            actions += '<a href="#" class="delete btn btn-sm btn-clean btn-icon btn-icon-md" title="Eliminar">';
                            actions += '<i class="la la-trash"></i>';
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
            if ($(event.target).hasClass('delete')) {
                event.preventDefault();
                let id = $(event.target).parents('tr').find('td[data-field="id"] span').html();

                Swal.fire({
                    title: '¡Cuidado!',
                    text: '¿Seguro que desea eliminar el registro?',
                    type: "warning",
                    heightAuto: false,
                    showCancelButton: true,
                    confirmButtonText: 'Sí',
                    cancelButtonText: 'No'
                }).then(result => {
                    EventBus.$emit('loading', true);

                    if (result.value) {
                        axios.post(this.url_delete, {
                            id: id,
                        }).then(response => {
                            this.datatable.load();
                            EventBus.$emit('loading', false);
                            Swal.fire({
                                title: '¡Ok!',
                                text: 'Se ha eliminado correctamente',
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
            } else if ($(event.target).hasClass('edit')) {
                event.preventDefault();
                let id = $(event.target).parents('tr').find('td[data-field="id"] span').html();
                EventBus.$emit('loading', true);

                axios.post(this.url_detail, {
                    id: id,
                }).then(response => {
                    EventBus.$emit('loading', false);
                    EventBus.$emit('edit_modal', response.data);
                }).catch(error => {
                    EventBus.$emit('loading', false);
                    console.log(error);
                    console.log(error.response);
                });
            }
        },
    }
};
</script>
