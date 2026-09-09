<template>
    <!--begin::Portlet-->
    <div class="kt-portlet">
        <div class="kt-portlet__head">
            <div class="kt-portlet__head-label">
                <h3 class="kt-portlet__head-title">
                    <marquee behavior="scroll" direction="left" scrollamount="5" class="text-success">
                        Puedes cambiar de placa asignado al pedido haciendo clic en el ícono de editar en
                        la columna de opciones. <b>Siempre y cuando el pedido no haya sido despachado.</b>
                    </marquee>
                </h3>
            </div>
        </div>
        <div class="kt-portlet__head">
            <div class="kt-portlet__head-label">
                <h3 class="kt-portlet__head-title">
                    Lista de Pedidos creados por el Coordinador de Planta, que aún no han sido despachados.
                </h3>
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
        url_list: {
            type: String,
            default: ''
        },
        url_detail: {
            type: String,
            default: ''
        }
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

        this.datatable.on('kt-datatable--on-ajax-done', function () {
            EventBus.$emit('loading', false);
        });

        EventBus.$on('refresh_table', function () {

            this.datatable.destroy();
            this.fillTableX();
            this.datatable.load();

            this.datatable.on('kt-datatable--on-ajax-done', function () {
                EventBus.$emit('loading', false);
            });
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
                            url: vm.url_list,
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

                search: {
                    input: $('#generalSearch'),
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
                        width: 70,
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
                        field: 'id',
                        title: 'ID',
                        width: 20,
                        textAlign: 'center',
                    },
                    {
                        field: 'route_name',
                        title: 'RUTA',
                        width: 100,
                    },
                    {
                        field: 'issue_date',
                        title: 'FECHA EMISIÓN',
                        width: 100,
                    },
                    {
                        field: 'traslate_date',
                        title: 'FECHA DE VENTA',
                        width: 100,
                        textAlign: 'center',
                    },
                    {
                        field: 'pedido',
                        title: 'PEDIDO',
                        width: 100,
                        textAlign: 'center',
                    },
                    {
                        field: 'employee_name',
                        title: 'CHOFER',
                        width: 180,
                        textAlign: 'center',
                    },
                    {
                        field: 'vehicle_plate',
                        title: 'PLACA',
                        width: 100,
                        textAlign: 'center',
                    },
                ]
            });
            this.datatable.columns('id').visible(false);
        },
        manageActions: function (event) {
            if ($(event.target).hasClass('edit')) {
                event.preventDefault();
                let id = $(event.target).parents('tr').find('td[data-field="id"] span').html();
                EventBus.$emit('loading', true);

                axios.post(this.url_detail, {
                    id: id,
                }).then(response => {
                    EventBus.$emit('clear_modal');
                    EventBus.$emit('edit_modal', response.data);
                    EventBus.$emit('loading', false);
                    //this.datatable.load();
                }).catch(error => {
                    console.log(error);
                    console.log(error.response);
                });
            }
        },
    }
};
</script>