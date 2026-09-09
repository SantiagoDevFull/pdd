<template>
    <!--begin::Portlet-->
    <div class="kt-portlet">
        <div class="kt-portlet__head">
            <div class="kt-portlet__head-label">
                <h3 class="kt-portlet__head-title">
                    Lista de Vehículos
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
                            placeholder="Buscar por N° Doc. o Razón Social">
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
        url_detail: {
            type: String,
            default: ''
        },
        url_export_excel: {
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

            axios.post(this.url_export_excel, {}, {
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
                link.setAttribute('download', 'reporte_vehiculos_' + creation_date + '.xlsx'); //or any other extension
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
                        field: 'company_name',
                        title: 'Compañia',
                        width: 150,
                        textAlign: 'center',
                    },
                    {
                        field: 'plate',
                        title: 'Placa',
                        width: 80,
                        textAlign: 'center',
                    },
                    {
                        field: 'capacity',
                        title: 'Capacidad',
                        width: 80,
                        textAlign: 'center',
                    },
                    {
                        field: 'license',
                        title: 'Licencia',
                        width: 80,
                        textAlign: 'center',
                    },
                    {
                        field: 'transportist_name',
                        title: 'Transportista',
                        width: 150,
                        textAlign: 'center',
                    },
                    {
                        field: 'chasis',
                        title: 'Chasis',
                        width: 120,
                        textAlign: 'center',
                    },
                    {
                        field: 'marca',
                        title: 'Marca',
                        width: 120,
                        textAlign: 'center',
                    },
                    {
                        field: 'tipo_combustible',
                        title: 'Tipo de Combustible',
                        width: 120,
                        textAlign: 'center',
                    },
                    {
                        field: 'tipo_carroceria',
                        title: 'Tipo de Carrocería',
                        width: 120,
                        textAlign: 'center',
                    },
                    {
                        field: 'modelo',
                        title: 'Modelo',
                        width: 120,
                        textAlign: 'center',
                    },
                    {
                        field: 'color',
                        title: 'Color',
                        width: 80,
                        textAlign: 'center',
                    },
                    {
                        field: 'motor',
                        title: 'Motor',
                        width: 120,
                        textAlign: 'center',
                    },
                    {
                        field: 'nro_llantas',
                        title: 'N° de Llantas',
                        width: 70,
                        textAlign: 'center',
                    },
                    {
                        field: 'year_model',
                        title: 'Año de Modelo',
                        width: 70,
                        textAlign: 'center',
                    },
                    {
                        field: 'options',
                        title: 'Opciones',
                        sortable: false,
                        width: 80,
                        overflow: 'visible',
                        autoHide: false,
                        textAlign: 'right',
                        class: 'td-sticky',
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
                                text: 'Vehículo eliminado correctamente.',
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
                    EventBus.$emit('edit_modal', response.data);
                    EventBus.$emit('loading', false);
                    this.datatable.load();
                }).catch(error => {
                    console.log(error);
                    console.log(error.response);
                });
            }
        },
    }
};
</script>