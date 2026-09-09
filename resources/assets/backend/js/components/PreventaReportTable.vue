<template>
    <!--begin::Portlet-->
    <div class="kt-portlet">
        <!--begin::Form-->

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
                            placeholder="Buscar por Serie y/o Correlativo">
                    </div>
                </div>
            </div>
        </div>

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
        url_list: {
            type: String,
            default: '',
        },
        url_download: {
            type: String,
            default: '',
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
    },
    watch: {

    },
    computed: {

    },
    methods: {

        fillTableX: function () {
            let vm = this;
            let token = document.head.querySelector('meta[name="csrf-token"]').content;

            // Define todas las columnas necesarias
            let columns = [
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
                        actions += '<a href="#" class="descargar btn btn-sm btn-clean btn-icon btn-icon-md" title="Descargar">';
                        actions += '<i class="la la-download"></i>';
                        actions += '</div>';
                        return actions;
                    },
                },
                {
                    field: 'id',
                    title: 'Id',
                    width: 40,
                    textAlign: 'center',
                },
                {
                    field: 'movement_number',
                    title: 'N° Mov.',
                    width: 100,
                    textAlign: 'center',
                },
                {
                    field: 'route_name',
                    title: 'Ruta',
                    width: 100,
                    textAlign: 'center',
                },
                {
                    field: 'company_name',
                    title: 'Compañía',
                    width: 100,
                    textAlign: 'center',
                },
                {
                    field: 'movement_type_name',
                    title: 'Tipo de Movimiento',
                    width: 100,
                    textAlign: 'center',
                },
                {
                    field: 'referral_serie_number',
                    title: 'Serie',
                    width: 80,
                    textAlign: 'center',
                },
                {
                    field: 'referral_voucher_number',
                    title: 'Correlativo',
                    width: 80,
                    textAlign: 'center',
                },
                {
                    field: 'chofer',
                    title: 'Chofer',
                    width: 100,
                    textAlign: 'center',
                },
            ];

            this.datatable = $('.kt-datatable').KTDatatable({
                data: {
                    type: 'remote',
                    source: {
                        read: {
                            url: vm.url_list,
                            params: {
                                _token: token,
                            }
                        },
                    },
                    pageSize: 10,
                    serverPaging: true,
                    serverFiltering: true,
                    serverSorting: true,
                },

                layout: {
                    scroll: true,
                    height: 400,
                    footer: false
                },

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

                columns: columns
            });

            this.datatable.columns('id').visible(false);
        },
        manageActions: function (event) {

            if ($(event.target).hasClass('descargar')) {

                EventBus.$emit('loading', true);
                event.preventDefault();
                let id = $(event.target).parents('tr').find('td[data-field="id"] span').html();



                axios.post(this.url_download, { 'id': id }, {
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
                        link.setAttribute('download', "movimiento-" + fecha + '.pdf');
                        document.body.appendChild(link);
                        link.click();

                        Swal.fire({
                            title: 'PDF descargado.',
                            html: "Bien",
                            type: "success",
                            confirmButtonText: 'Ok',
                            timer: 2000,
                        }).then((result) => {
                            this.datatable.load();
                        });
                    }).catch(error => {
                        EventBus.$emit('loading', false);
                        console.log(error);
                        console.log(error.response);

                        this.ids = [];

                        Swal.fire({
                            title: 'Error',
                            text: "Error al descargar el PDF.",
                            type: "error",
                            confirmButtonText: 'Ok',
                        });
                    });

            }
        },

    }
};
</script>