<template>
    <!--begin::Portlet-->
    <div class="kt-portlet" v-if="show_table == 1">
        <!--begin::Form-->

        <div class="kt-portlet__body">
            <div class="row">
                <div class="col-12" @click="manageActions">
                    <!--begin: Datatable -->
                    <div class="kt-datatable"></div>
                    <!--end: Datatable -->
                </div>
            </div>
        </div>

        <!--end::Form-->
    </div>
    <!--end::Portlet-->
</template>

<script>
import EventBus from '../event-bus';

export default {
    props: {
        url: {
            type: String,
            default: '',
        },

        url_detail: {
            type: String,
            default: '',
        },

        url_fact: {
            type: String,
            default: '',
        },
        url_export_cot: {
            type: String,
            default: '',
        },
        url_export_fact: {
            type: String,
            default: '',
        },
        url_aprob: {
            type: String,
            default: '',
        }
    },
    data() {
        return {
            show_table: 0,
            datatable: undefined,
            issue_date_1: '',
            issue_date_2: '',
        }
    },
    created() {
        EventBus.$on('show_table', function (data) {

            let vm = this;
            this.show_table = 1;
            this.issue_date_1 = data.fecha1;
            this.issue_date_2 = data.fecha2;

            Vue.nextTick(function () {
                if (vm.datatable != undefined) {
                    vm.datatable.destroy();
                    vm.fillTableX();
                    vm.datatable.load();
                } else {

                    vm.fillTableX();

                }
            });

        }.bind(this));

        EventBus.$on('refresh_table', function (data) {

            this.datatable.load();

        }.bind(this));


    },
    mounted() {

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
                                issue_date_1: vm.issue_date_1,
                                issue_date_2: vm.issue_date_2,
                            }
                        },
                    }
                },

                // layout definition
                layout: {
                    scroll: true, // enable/disable datatable scroll both horizontal and vertical when needed.
                    height: 400,
                    footer: false // display/hide footer
                },

                // column sorting
                sortable: true,
                pagination: false,

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
                        field: 'status',
                        title: 'Estado',
                        sortable: false,
                        width: 70,
                        overflow: 'visible',
                        autoHide: false,
                        textAlign: 'center',
                        template: function (row) {
                            let actions = '<div class="actions">';
                            if (row.status == 0) {
                                actions += '<p class="badge bg-warning text-white">Pendientes</p>';
                            } else if (row.status == 1) {
                                actions += '<p class="badge bg-success text-white">Aprobado</p>';
                            } else if (row.status == 2) {
                                actions += '<p class="badge bg-primary text-white">Facturado</p>';
                            }
                            return actions;
                        },
                    },
                    {
                        field: 'cots_id',
                        title: 'Id',
                        width: 40,
                        textAlign: 'center',
                    },
                    {
                        field: 'serie_cot',
                        title: 'COT',
                        width: 50,
                        textAlign: 'center',
                    },
                    {
                        field: 'serie_ot',
                        title: 'OT',
                        width: 70,
                        textAlign: 'center',
                    },
                    {
                        field: 'cots_client_document_number',
                        title: 'N° Documento',
                        width: 80,
                        textAlign: 'center',
                    },
                    {
                        field: 'cots_client_name',
                        title: 'Cliente',
                        width: 150,
                        textAlign: 'center',
                    },
                    {
                        field: 'placa',
                        title: 'Vehículo',
                        width: 50,
                        textAlign: 'center',
                    },
                    {
                        field: 'cots_issue_date',
                        title: 'Fecha de Emisión',
                        width: 80,
                        textAlign: 'center',
                    },
                    {
                        field: 'cots_expiry_date',
                        title: 'Fecha de Vigencia',
                        width: 80,
                        textAlign: 'center',
                    },
                    {
                        field: 'currency_name',
                        title: 'Moneda',
                        width: 80,
                        textAlign: 'center',
                    },
                    {
                        field: 'subtotal',
                        title: 'SubTotal',
                        width: 75,
                        textAlign: 'center',
                    },
                    {
                        field: 'igv',
                        title: 'IGV',
                        width: 75,
                        textAlign: 'center',
                    },
                    {
                        field: 'total',
                        title: 'Total',
                        width: 75,
                        textAlign: 'center',
                    },
                    
                    {
                        field: 'options',
                        title: 'Opciones',
                        sortable: false,
                        width: 100,
                        overflow: 'visible',
                        autoHide: false,
                        class: 'td-sticky',
                        textAlign: 'center',
                        template: function (row) {
                            let actions = '<div class="actions">';
                            if (row.status == 0) {
                                actions += '<a href="#" class="aprobar btn btn-sm btn-clean btn-icon btn-icon-md" title="Aprobar Cotización">';
                                actions += '<i class="la la-check"></i>';
                                actions += '</a>';
                                actions += '<a href="#" class="detail btn btn-sm btn-clean btn-icon btn-icon-md" title="Ver detalle">';
                                actions += '<i class="la la-eye"></i>';
                                actions += '</a>';
                            } else if (row.status == 1) {
                                actions += '<a href="#" class="generar btn btn-sm btn-clean btn-icon btn-icon-md" title="Generar Factura">';
                                actions += '<i class="la la-paper-plane"></i>';
                                actions += '</a>';
                            } else if (row.status == 2) {
                                actions += '<a href="#" class="factura btn btn-sm btn-clean btn-icon btn-icon-md" title="Imprimir Factura">';
                                actions += '<i class="la la-download"></i>';
                                actions += '</a>';
                            }
                            actions += '<a href="#" class="cotizar btn btn-sm btn-clean btn-icon btn-icon-md" title="Imprimir Cotización">';
                            actions += '<i class="la la-download"></i>';
                            actions += '</a>';



                            actions += '</div>';
                            return actions;
                        },
                    },
                ]
            });

            this.datatable.columns('cots_id').visible(false);
        },
        manageActions: function (event) {

            if ($(event.target).hasClass('aprobar')) {
                event.preventDefault();
                let id = $(event.target).parents('tr').find('td[data-field="cots_id"] span').html();

                Swal.fire({
                    title: '¡Cuidado!',
                    text: '¿Seguro que desea aprobar la Cotización?',
                    type: "warning",
                    heightAuto: false,
                    showCancelButton: true,
                    confirmButtonText: 'Sí',
                    cancelButtonText: 'No'
                }).then(result => {
                    EventBus.$emit('loading', true);

                    if (result.value) {
                        axios.post(this.url_aprob, {
                            id: id,
                        }).then(response => {
                            this.datatable.load();

                            EventBus.$emit('loading', false);
                            Swal.fire({
                                title: '¡APROBADO!',
                                text: 'Cotización Aprobado exitosamente.',
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

            } else if ($(event.target).hasClass('generar')) {

                event.preventDefault();
                let id = $(event.target).parents('tr').find('td[data-field="cots_id"] span').html();

                Swal.fire({
                    title: '¡Cuidado!',
                    text: '¿Seguro que desea generar su Factura?',
                    type: "warning",
                    heightAuto: false,
                    showCancelButton: true,
                    confirmButtonText: 'Sí',
                    cancelButtonText: 'No'
                }).then(result => {
                    EventBus.$emit('loading', true);

                    if (result.value) {
                        axios.post(this.url_fact, {
                            id: id,
                        }).then(response => {
                            this.datatable.load();

                            EventBus.$emit('loading', false);
                            Swal.fire({
                                title: '¡APROBADO!',
                                text: 'Factura Generado exitosamente.',
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


            } else if ($(event.target).hasClass('factura')) {

                EventBus.$emit('loading', true);
                event.preventDefault();
                let id = $(event.target).parents('tr').find('td[data-field="cots_id"] span').html();



                axios.post(this.url_export_fact, { 'id': id }, {
                    responseType: 'blob',
                })
                    .then(response => {
                        EventBus.$emit('loading', false);

                        const date = new Date();
                        const fecha = date.getFullYear() + '-' + (date.getMonth() + 1) + '-' + date.getDate();

                        console.log(response.data);
                        this.ids = [];

                        const url = window.URL.createObjectURL(new Blob([response.data]));
                        const link = document.createElement('a');
                        link.href = url;
                        link.setAttribute('download', "factura_" + fecha + '.pdf');
                        document.body.appendChild(link);
                        link.click();

                        Swal.fire({
                            title: 'Factura generado exitosamente',
                            html: "Bien",
                            type: "success",
                            confirmButtonText: 'Ok',
                            timer: 2000,
                        }).then((result) => {
                            // this.datatable.destroy();
                            // this.fillTableX();
                            this.datatable.load();
                        });
                    }).catch(error => {
                        EventBus.$emit('loading', false);
                        console.log(error);
                        console.log(error.response);

                        this.ids = [];

                        Swal.fire({
                            title: 'Error',
                            text: "Error al generar la factura.",
                            type: "error",
                            confirmButtonText: 'Ok',
                        });
                    });

            } else if ($(event.target).hasClass('cotizar')) {

                EventBus.$emit('loading', true);
                event.preventDefault();
                let id = $(event.target).parents('tr').find('td[data-field="cots_id"] span').html();



                axios.post(this.url_export_cot, { 'id': id }, {
                    responseType: 'blob',
                })
                    .then(response => {
                        EventBus.$emit('loading', false);

                        const date = new Date();
                        const fecha = date.getFullYear() + '-' + (date.getMonth() + 1) + '-' + date.getDate();

                        console.log(response.data);
                        this.ids = [];

                        const url = window.URL.createObjectURL(new Blob([response.data]));
                        const link = document.createElement('a');
                        link.href = url;
                        link.setAttribute('download', "cotizacion_" + fecha + '.pdf');
                        document.body.appendChild(link);
                        link.click();

                        Swal.fire({
                            title: 'Archivo PDF generado exitosamente',
                            html: "Bien",
                            type: "success",
                            confirmButtonText: 'Ok',
                            timer: 2000,
                        }).then((result) => {
                            // this.datatable.destroy();
                            // this.fillTableX();
                            this.datatable.load();
                        });
                    }).catch(error => {
                        EventBus.$emit('loading', false);
                        console.log(error);
                        console.log(error.response);

                        this.ids = [];

                        Swal.fire({
                            title: 'Error',
                            text: "Error al generar Archivo PDF.",
                            type: "error",
                            confirmButtonText: 'Ok',
                        });
                    });
            } else if ($(event.target).hasClass('detail')) {

                EventBus.$emit('loading', true);
                event.preventDefault();

                let id = $(event.target).parents('tr').find('td[data-field="cots_id"] span').html();
                let serie = $(event.target).parents('tr').find('td[data-field="serie_cot"] span').html();

                axios.post(this.url_detail, {
                    id: id
                }).then(response => {
                    EventBus.$emit('loading', false);
                    EventBus.$emit('show_modal', response.data, serie);
                    // console.log(response.data);
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