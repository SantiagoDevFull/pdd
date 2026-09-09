<template>
    <!--begin::Portlet-->
    <div class="kt-portlet kt-portlet--mobile" v-show="show_table" @click="manageActions">
        <div class="kt-portlet__head kt-portlet__head--lg">
            <div class="kt-portlet__head-label">
                <span class="kt-portlet__head-icon">
                    <i class="kt-font-brand la la-file-text"></i>
                </span>
                <h3 class="kt-portlet__head-title">
                    Lista de Ordenes de Compras y Servicios
                </h3>
            </div>
            <div class="kt-portlet__head-toolbar">
                <div class="kt-portlet__head-wrapper">
                    <div class="dropdown dropdown-inline">

                        <a href="#" @click.prevent="Exportar()" class="btn btn-outline-brand btn-bold btn-sm">
                            <i class="fa fa-file-exel"></i> Generar PDF
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="kt-portlet__body kt-portlet__body--fit">
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
        url_export: {
            type: String,
            default: ''
        },
        url_get_detail: {
            type: String,
            default: ''
        },
        user_name: {
            type: String,
            default: ''
        },
    },
    data() {
        return {
            datatable: undefined,
            show_table: false,
            elements: [],
            element_details: [],
            model: {},
            ids: [],
        }
    },
    mounted() {
        EventBus.$on('show_table', function (response) {
            this.show_table = true;
            this.model = response.data;
            // this.fillTableX();

            if (this.datatable != undefined) {
                this.elements = [];
                // this.datatable.destroy();
                // this.fillTableX();
                this.datatable.setDataSourceParam('model', this.model);
                this.datatable.load();
            } else {
                this.fillTableX();
            }

            this.datatable.on('kt-datatable--on-ajax-done', function () {
                EventBus.$emit('loading', false);
            });
        }.bind(this));

        // this.fillTableX();
    },
    methods: {
        getInfo: function (data) {
            this.fillTableX(data);
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
                            url: this.url_list,
                            params: {
                                _token: token,
                                model: vm.model
                            },
                        },
                    },
                    pageSize: 10,
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
                extensions: {
                    checkbox: {},
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
                        field: 'orders_id',
                        title: '#',
                        sortable: false,
                        width: 30,
                        selector: { class: 'kt-checkbox--solid' },
                        textAlign: 'center',
                    },
                    {
                        field: 'options',
                        title: '',
                        sortable: false,
                        width: 20,
                        overflow: 'visible',
                        autoHide: false,
                        textAlign: 'center',
                        template: function () {
                            return '\
                                <div class="actions">\
                                    <a href="#" class="view-detail btn btn-sm btn-clean btn-icon btn-icon-md" title="Ver">\
                                        <i class="la la-eye"></i>\
                                    </a>\
                                </div>\
                            ';
                        },
                    },
                    {
                        field: "orders_type",
                        title: "Tipo de Orden",
                        width: 100,
                        sortable: true,
                        textAlign: 'center',
                    },
                    {
                        field: "orders_code",
                        title: "Tipo de Orden",
                        width: 100,
                        sortable: true,
                        textAlign: 'center',
                    },
                    {
                        field: "company_name",
                        title: "Compañia",
                        width: 120,
                        sortable: true,
                        textAlign: 'center',
                    },
                    {
                        field: "provider_name",
                        title: "Proveedor",
                        width: 120,
                        sortable: true,
                        textAlign: 'center',
                    },
                    {
                        field: "currency_name",
                        title: "Moneda",
                        width: 100,
                        sortable: true,
                        textAlign: 'center',
                    },
                    {
                        field: "orders_tc",
                        title: "Cambio",
                        width: 80,
                        sortable: true,
                        textAlign: 'center',
                    },
                    {
                        field: "orders_order_date",
                        title: "Fecha creada",
                        width: 100,
                        sortable: true,
                        textAlign: 'center',
                    },
                    {
                        field: "orders_subtotal",
                        title: "Subtotal",
                        width: 100,
                        sortable: true,
                        textAlign: 'center',
                    },
                    {
                        field: "orders_igv",
                        title: "IGV",
                        width: 100,
                        sortable: true,
                        textAlign: 'center',
                    },
                    {
                        field: "orders_import",
                        title: "Importe",
                        width: 100,
                        sortable: true,
                        textAlign: 'center',
                    },
                ]
            });

            $('.kt-datatable').on('kt-datatable--on-check', function (a, e) {

                $.each(e, function (key, value) {
                    let index = vm.ids.findIndex((element) => element == value);
                    if (index < 0) {
                        vm.ids.push(value);
                    }
                });

                vm.ids.sort(function (a, b) {
                    let aNumber = parseInt(a);
                    let bNumber = parseInt(b);
                    return ((aNumber < bNumber) ? -1 : ((aNumber > bNumber) ? 1 : 0));
                });
            });

            $('.kt-datatable').on('kt-datatable--on-uncheck', function (a, e) {
                $.each(e, function (key, value) {
                    let index = vm.ids.findIndex((element) => element == value);
                    if (index >= 0) {
                        vm.ids.splice(index, 1);
                    }
                });

                vm.ids.sort(function (a, b) {
                    let aNumber = parseInt(a);
                    let bNumber = parseInt(b);
                    return ((aNumber < bNumber) ? -1 : ((aNumber > bNumber) ? 1 : 0));
                });
            });
        },
        Exportar: function () {

            let vm = this;

            this.ids.sort(function (a, b) {
                let aNumber = parseInt(a);
                let bNumber = parseInt(b);
                return ((aNumber < bNumber) ? -1 : ((aNumber > bNumber) ? 1 : 0));
            });

            let data = {
                ids: this.ids,
            };

            EventBus.$emit('loading', true);
            axios.post(this.url_export, data,{
                responseType: 'blob',
            })
            .then(response => {

                const date = new Date();
                const fecha = date.getFullYear() + '-' + (date.getMonth() + 1) + '-' + date.getDate();

                EventBus.$emit('loading', false);
                console.log(response.data);
                this.ids = [];

                const url = window.URL.createObjectURL(new Blob([response.data]));
                const link = document.createElement('a');
                link.href = url;
                link.setAttribute('download',"orden-generado|"+fecha +'.pdf');
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
                    text: "Error durante el envio.",
                    type: "error",
                    confirmButtonText: 'Ok',
                });
            });
        },
        manageActions: function (event) {

            if ($(event.target).hasClass('view-detail')) {
                event.preventDefault();
                EventBus.$emit('loading', true);

                let orders_id = $(event.target).parents('tr').find('td[data-field="orders_id"] input').val();

                axios.post(this.url_get_detail, {
                    orders_id: orders_id
                }).then(response => {
                    EventBus.$emit('loading', false);
                    EventBus.$emit('show_order_detail', response.data);
                    // console.log(response.data);
                }).catch(error => {
                    console.log(error);
                    console.log(error.response);
                });
            }
        },
        getInfoByIndex: function (target, c_dt, index) {
            var c_row = $(target).parents('tr');
            var i = c_row.index();
            var cell = c_row.find('.m-datatable__cell .m-checkbox [type="checkbox"]');
            var c_page = c_dt.getCurrentPage();
            var p_size = c_dt.getPageSize();

            var ind = ((c_page - 1) * p_size) + i;
            // c_dt.setActive(cell);
            var id = c_dt.getRecord(ind).getColumn(index).getValue();
            // c_dt.setInactive(cell);

            return id;
        },
    }
};
</script>