<template>
    <!--begin::Portlet-->
    <div class="kt-portlet">
        <div class="kt-portlet__head">
            <div class="kt-portlet__head-label">
                <h3 class="kt-portlet__head-title">
                    Lista de Pedidos
                </h3>
            </div>
            <div class="kt-portlet__head-toolbar">
                <div class="kt-portlet__head-wrapper">
                    <div class="dropdown dropdown-inline">

                        <a href="#" @click.prevent="OpenModalRegister()" class="btn btn-primary" id="OpenModalRegister">
                            <i class="fa fa-file-excel"></i> Registrar Pedido
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
import Swal from 'sweetalert2';
import EventBus from '../event-bus';
export default {
    props: {
        url_list: {
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
        OpenModalRegister: function () {
            EventBus.$emit('create_modal');
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

                            actions += '<a href="#" class="delete btn btn-sm btn-clean btn-icon btn-icon-md" title="Eliminar">';
                            actions += '<i class="la la-trash"></i>';
                            actions += '</a>';
                            actions += '</div>';
                            return actions;
                        },
                    },
                    {
                        field: 'house_order_id',
                        title: 'ID',
                        width: 20,
                        textAlign: 'center',
                    },
                    {
                        field: 'house_order_state',
                        title: 'Estado',
                        width: 90,
                        textAlign: 'center',
                    },
                    {
                        field: 'voucher_number',
                        title: 'Boleta Ref.',
                        width: 100,
                    },
                    {
                        field: 'client_name',
                        title: 'Cliente',
                        width: 140,
                    },
                    {
                        field: 'origin_name',
                        title: 'Origen',
                        width: 100,
                    },
                    {
                        field: 'voucher',
                        title: 'Comprobante',
                        width: 100,
                        textAlign: 'center',
                    },
                    {
                        field: 'product_type',
                        title: 'Tipo de Producto',
                        width: 100,
                        textAlign: 'center',
                    },
                    {
                        field: 'article_name',
                        title: 'Producto',
                        width: 100,
                        textAlign: 'center',
                    },
                    {
                        field: 'house_order_price',
                        title: 'Precio',
                        width: 90,
                        textAlign: 'center',
                    },
                    {
                        field: 'house_order_quantity',
                        title: 'Cantidad',
                        width: 90,
                        textAlign: 'center',
                    },
                    {
                        field: 'house_order_total',
                        title: 'Total',
                        width: 90,
                        textAlign: 'center',
                    },
                    {
                        field: 'payment_method_name',
                        title: 'Método de pago',
                        width: 100,
                        textAlign: 'center',
                    },
                    {
                        field: 'house_order_monto',
                        title: 'Monto',
                        width: 100,
                        textAlign: 'center',
                    },
                    {
                        field: 'house_order_vuelto',
                        title: 'Vuelto',
                        width: 100,
                        textAlign: 'center',
                    },
                    {
                        field: 'house_order_turno',
                        title: 'Turno',
                        width: 100,
                        textAlign: 'center',
                    },
                    {
                        field: 'employee_name',
                        title: 'Chofer',
                        width: 100,
                        textAlign: 'center',
                    },

                    {
                        field: 'house_order_hora_llamada',
                        title: 'Hora llamada',
                        width: 90,
                        textAlign: 'center',
                    },
                    {
                        field: 'house_order_hora_entrega_balon',
                        title: 'Hora entrega balón',
                        width: 90,
                        textAlign: 'center',
                    },
                    {
                        field: 'house_order_hora_salida_pedido',
                        title: 'Hora salida pedido',
                        width: 90,
                        textAlign: 'center',
                    },
                    {
                        field: 'house_order_hora_entrega_pedido',
                        title: 'Hora entrega pedido',
                        width: 90,
                        textAlign: 'center',
                    },
                    {
                        field: 'house_order_hora_retorno',
                        title: 'Hora retorno',
                        width: 90,
                        textAlign: 'center',
                    },
                    {
                        field: 'house_order_motivo_demora',
                        title: 'Motivo de la demora',
                        width: 150,
                    },
                    {
                        field: 'house_order_obs_cli',
                        title: 'Observaciones del Cliente',
                        width: 150,
                    }
                ]
            });

            this.datatable.columns('house_order_id').visible(false);
        },
        manageActions: function (event) {
            if ($(event.target).hasClass('delete')) {
                event.preventDefault();
                let id = $(event.target).parents('tr').find('td[data-field="house_order_id"] span').html();

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
                            EventBus.$emit('loading', false);
                            this.$parent.alertMsg(response.data);
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
                let id = $(event.target).parents('tr').find('td[data-field="house_order_id"] span').html();

                EventBus.$emit('loading', true);
                axios.post(this.url_detail, {
                    id: id,
                }).then(response => {
                    EventBus.$emit('clear_modal');
                    EventBus.$emit('edit_modal', response.data);
                    EventBus.$emit('loading', false);
                }).catch(error => {
                    console.log(error);
                    console.log(error.response);
                });

            }
        },
    }
};
</script>