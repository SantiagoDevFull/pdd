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
                        <a href="#" @click.prevent="openModal()" class="btn btn-success"
                            :class="{ 'disabled': isDisabled }" id="add_article">
                            <i class="fa fa-plus"></i> Crear Almacén
                        </a>
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
    },
    data() {
        return {
            datatable: undefined,
            company_id: '',
            isDisabled: true,
        }
    },
    created() {

    },
    mounted() {
        EventBus.$on('show_table', function (response) {
            this.company_id = response.data;
            if (this.datatable == undefined) {
                this.fillTableX();
            } else {
                this.datatable.setDataSourceParam('company_id', this.company_id);
                this.datatable.load();
            }

            this.datatable.on('kt-datatable--on-ajax-done', function () {
                EventBus.$emit('loading', false);
            });
            this.isDisabled = false;
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
            EventBus.$emit('create_modal', this.company_id);
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
                                company_id: vm.company_id,
                            }
                        },
                    },
                    pageSize: 10,
                    serverPaging: true,
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
                    autoHide: true,
                },

                // columns definition
                columns: [
                    {
                        field: 'warehouse_type_id',
                        title: 'Código',
                        width: 80,
                    },
                    {
                        field: 'warehouse_type_name',
                        title: 'Almacén',
                        width: 120,
                    },
                    {
                        field: 'warehouse_type_short_name',
                        title: 'Nombre corto',
                        width: 80,
                    },
                    {
                        field: 'warehouse_type_business_name',
                        title: 'Proveedor',
                        width: 150,
                    },
                    {
                        field: 'warehouse_type_ruc',
                        title: 'RUC',
                        width: 100,
                    },
                    {
                        field: 'agent_name',
                        title: 'Agente',
                        width: 150,
                    },
                    {
                        field: 'warehouse_type_agent_uno',
                        title: 'Registro Uno',
                        width: 100,
                    },
                    {
                        field: 'warehouse_type_agent_dos',
                        title: 'Registro Final',
                        width: 100,
                    },

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
                            actions += '<a href="#" class="delete btn btn-sm btn-clean btn-icon btn-icon-md" title="Eliminar">';
                            actions += '<i class="la la-trash"></i>';
                            actions += '</a>';
                            actions += '</div>';

                            return actions;
                        },
                    },
                ]
            });

            this.datatable.columns('warehouse_type_id').visible(false);
        },
        manageActions: function (event) {
            if ($(event.target).hasClass('delete')) {
                event.preventDefault();
                let id = $(event.target).parents('tr').find('td[data-field="warehouse_type_id"] span').html();

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
                let id = $(event.target).parents('tr').find('td[data-field="warehouse_type_id"] span').html();
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