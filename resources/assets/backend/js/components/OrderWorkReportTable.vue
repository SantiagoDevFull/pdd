<template>
    <!--begin::Portlet-->
    <div class="kt-portlet">
        <div class="kt-portlet__head">
            <div class="kt-portlet__head-label">
                <h3 class="kt-portlet__head-title">
                    ÓRDENES DE TRABAJO
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
        url_delete: {
            type: String,
            default: ''
        },
        url_aprobar: {
            type: String,
            default: ''
        },
        url_pdf: {
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

            this.flag_export = true;

            axios.post(this.url, {
                flag_export: this.flag_export
            }, {
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
                link.setAttribute('download', 'reporte-orden-trabajo_' + creation_date + '.xlsx'); //or any other extension
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
                        field: 'order_id',
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
                        field: 'asesor_name',
                        title: 'Asesor',
                        width: 80,
                        textAlign: 'center',
                    },
                    {
                        field: 'vehicle_plate',
                        title: 'Placa',
                        width: 80,
                        textAlign: 'center',
                    },
                    {
                        field: 'security_name',
                        title: 'Compañía de Seguro',
                        width: 100,
                        textAlign: 'center',
                    },
                    {
                        field: 'client_motor_name',
                        title: 'Cliente',
                        width: 100,
                        textAlign: 'center',
                    },
                    {
                        field: 'serie',
                        title: 'Serie',
                        width: 80,
                        textAlign: 'center',
                    },
                    {
                        field: 'km',
                        title: 'KM',
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
                        field: 'issue_date',
                        title: 'Fecha de Emisión',
                        width: 80,
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

                            if (row.state == 0) {
                                actions += '<a href="#" class="aprobar btn btn-sm btn-clean btn-icon btn-icon-md" title="Aprobar OT">';
                                actions += '<i class="la la-check"></i>';
                                actions += '</a>';

                                actions += '<a href="#" class="desaprobar btn btn-sm btn-clean btn-icon btn-icon-md" title="Desaprobar OT">';
                                actions += '<i class="la la-close"></i>';
                                actions += '</a>';
                            }
                            if (row.state == 1) {

                                actions += '<a href="#" class="pdf btn btn-sm btn-clean btn-icon btn-icon-md" title="Descargar PDF">';
                                actions += '<i class="la la-file-pdf-o"></i>';
                                actions += '</a>';
                            }
                            actions += '<a href="#" class="detalle btn btn-sm btn-clean btn-icon btn-icon-md" title="Ver Detalle">';
                            actions += '<i class="la la-eye"></i>';
                            actions += '</a>';

                            actions += '</div>';
                            return actions;
                        },
                    },
                ]
            });

            this.datatable.columns('order_id').visible(false);
        },
        manageActions: function (event) {
            if ($(event.target).hasClass('desaprobar')) {
                event.preventDefault();
                let order_id = $(event.target).parents('tr').find('td[data-field="order_id"] span').html();
                let serie = $(event.target).parents('tr').find('td[data-field="serie"] span').html();

                Swal.fire({
                    title: '¡Cuidado!',
                    text: '¿Seguro que desea desaprobar la OT -> ' + serie + '?',
                    type: "warning",
                    heightAuto: false,
                    showCancelButton: true,
                    confirmButtonText: 'Sí',
                    cancelButtonText: 'No'
                }).then(result => {
                    EventBus.$emit('loading', true);

                    if (result.value) {
                        axios.post(this.url_delete, {
                            order_id: order_id,
                        }).then(response => {
                            this.datatable.load();
                            EventBus.$emit('loading', false);
                            Swal.fire({
                                title: '¡Ok!',
                                text: 'OT -> ' + serie + ' desaprobado correctamente.',
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
                let order_id = $(event.target).parents('tr').find('td[data-field="order_id"] span').html();
                let serie = $(event.target).parents('tr').find('td[data-field="serie"] span').html();

                Swal.fire({
                    title: '¡Cuidado!',
                    text: '¿Seguro que desea aprobar la OT -> ' + serie + '?',
                    type: "warning",
                    heightAuto: false,
                    showCancelButton: true,
                    confirmButtonText: 'Sí',
                    cancelButtonText: 'No'
                }).then(result => {
                    EventBus.$emit('loading', true);

                    if (result.value) {
                        axios.post(this.url_aprobar, {
                            order_id: order_id,
                        }).then(response => {
                            this.datatable.load();
                            EventBus.$emit('loading', false);
                            Swal.fire({
                                title: '¡Ok!',
                                text: 'OT -> ' + serie + ' aprobado correctamente.',
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


            } else if ($(event.target).hasClass('pdf')) {

                event.preventDefault();
                let order_id = $(event.target).parents('tr').find('td[data-field="order_id"] span').html();
                let serie = $(event.target).parents('tr').find('td[data-field="serie"] span').html();

                EventBus.$emit('loading', true);

                axios.get(this.url_pdf, {
                    params: { order_id: order_id },
                    responseType: 'blob'
                }).then(response => {

                    EventBus.$emit('loading', false);
                    console.log(response.data);

                    const url = window.URL.createObjectURL(new Blob([response.data]));
                    const link = document.createElement('a');
                    link.href = url;
                    link.setAttribute('download', 'OT|' + serie + '.pdf');
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);

                    Swal.fire({
                        type: "success",
                        title: "Bien",
                        text: "Se generó correctamente el PDF de la OT seleccionada.",
                        heightAuto: false,
                    });
                }).catch(error => {
                    console.log(error);
                    console.log(error.response);
                    EventBus.$emit('loading', false);
                    Swal.fire({
                        type: "error",
                        title: "Error",
                        text: "Ocurrió un error al generar el PDF de la OT seleccionada",
                        icon: "error",
                        heightAuto: false,
                    });
                });


            } else if ($(event.target).hasClass('detalle')) {
                event.preventDefault();
                let order_id = $(event.target).parents('tr').find('td[data-field="order_id"] span').html();
                let placa = $(event.target).parents('tr').find('td[data-field="vehicle_plate"] span').html();
                let client_motor_name = $(event.target).parents('tr').find('td[data-field="client_motor_name"] span').html();

                EventBus.$emit('loading', true);

                axios.post(this.url_get_detail, {
                    order_id: order_id
                }).then(response => {
                    EventBus.$emit('loading', false);
                    EventBus.$emit('show_order_detail', response.data, placa, client_motor_name);
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