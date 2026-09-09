<template>
    <!--begin::Portlet-->
    <div class="kt-portlet">
        <div class="kt-portlet__head">
            <div class="kt-portlet__head-label">
                <h3 class="kt-portlet__head-title">
                    Resultado
                </h3>
            </div>
        </div>

        <!--
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
                            placeholder="Buscar por ID o Razón Social">
                    </div>
                </div>
            </div>
        </div>
        -->
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
        url: {
            type: String,
            default: ''
        },
        url_detail: {
            type: String,
            default: ''
        },
        url_get_import_cont: {
            type: String,
            default: ''
        },
        url_get_import_fi: {
            type: String,
            default: ''
        },
        url_aprobar_cont: {
            type: String,
            default: ''
        },
        url_desaprobar_cont: {
            type: String,
            default: ''
        },
    },
    data() {
        return {
            datatable: undefined,
            company_id: '',
            area_all_id: '',
            adm_payment_id: '',
        }
    },
    created() {

    },
    mounted() {
        EventBus.$on('show_table', function (response) {
            this.company_id = response.company_id;
            this.area_all_id = response.area_all_id;
            this.adm_payment_id = response.adm_payment_id;
            if (this.datatable == undefined) {
                this.fillTableX();
            } else {
                this.datatable.setDataSourceParam('company_id', this.company_id);
                this.datatable.setDataSourceParam('area_all_id', this.area_all_id);
                this.datatable.setDataSourceParam('adm_payment_id', this.adm_payment_id);
                this.datatable.load();
            }

            this.datatable.on('kt-datatable--on-ajax-done', function () {
                EventBus.$emit('loading', false);
            });

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
                                area_all_id: vm.area_all_id,
                                adm_payment_id: vm.adm_payment_id
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
                        field: 'options',
                        title: 'Opciones',
                        sortable: false,
                        width: 100,
                        overflow: 'visible',
                        autoHide: false,
                        textAlign: 'center',
                        template: function (row) {
                            let actions = '<div class="actions">';

                            if (row.adm_payment_id == 7) {

                                if (row.state_id == 5) {
                                    actions += '<a href="#" class="edit btn btn-sm btn-clean btn-icon btn-icon-md" title="Ver toda la información">';
                                    actions += '<i class="fa fa-solid fa-eye"></i>';
                                    actions += '</a>';
                                }

                            } else {
                                actions += '<a href="#" class="edit btn btn-sm btn-clean btn-icon btn-icon-md" title="Ver toda la información">';
                                actions += '<i class="fa fa-solid fa-eye"></i>';
                                actions += '</a>';
                            }



                            actions += '</div>';

                            return actions;
                        },
                    },
                    {
                        field: 'name_state',
                        title: 'Estado',
                        width: 100,
                        template: function (row) {
                            let actions = '<div class="actions">';
                            if (row.name_state == 'REGISTRADO') {
                                actions += '<p class="badge bg-success text-white">REGISTRADO</p>';
                            } else if (row.name_state == 'VB CONT') {
                                actions += '<p class="badge bg-success text-white">VB CONT</p>';
                            } else if (row.name_state == 'VB FIN') {
                                actions += '<p class="badge bg-success text-white">VB FIN</p>';
                            } else if (row.name_state == 'VB TES') {
                                actions += '<p class="badge bg-success text-white">VB TES</p>';
                            } else if (row.name_state == 'DOCUMENTOS SUBIDOS') {
                                actions += '<p class="badge bg-success text-white">DOC. SUBIDOS</p>';
                            } else if (row.name_state == 'CIERRE FIN') {
                                actions += '<p class="badge bg-success text-white">CIERRE FIN</p>';
                            } else if (row.name_state == 'CIERRE TES') {
                                actions += '<p class="badge bg-success text-white">CIERRE TES</p>';
                            } else if (row.name_state == 'DS CONT') {
                                actions += '<p class="badge bg-danger text-white">DS CONT</p>';
                            } else if (row.name_state == 'DS FIN') {
                                actions += '<p class="badge bg-danger text-white">DS FIN</p>';
                            } else if (row.name_state == 'RZ TES') {
                                actions += '<p class="badge bg-danger text-white">RZ TES</p>';
                            }

                            return actions;
                        },
                    },
                    {
                        field: 'id',
                        title: 'ID',
                        width: 100,
                    },
                    {
                        field: 'fecha_registro',
                        title: 'Fecha de Registro',
                        width: 80,
                    },
                    {
                        field: 'company_name',
                        title: 'Compañia',
                        width: 100,
                    },
                    {
                        field: 'state_id',
                        title: 'ID ESTADO',
                        width: 100,
                    },
                    {
                        field: 'lleno',
                        title: 'COMPLETADO ?',
                        width: 100,
                    },
                    {
                        field: 'num_mov',
                        title: 'N° Movimiento',
                        width: 180,
                    },
                    {
                        field: 'condicion_pago',
                        title: 'Condición de Pago',
                        width: 100,
                    },
                    {
                        field: 'tipo',
                        title: 'Tipo',
                        width: 100,
                    },
                    {
                        field: 'sub_tipo',
                        title: 'Sub Tipo',
                        width: 100,
                    },
                    {
                        field: 'tipo_req',
                        title: 'Tipo de Requerimiento',
                        width: 100,
                    },
                    {
                        field: 'import',
                        title: 'Importe Bruto',
                        width: 100,
                    },
                    {
                        field: 'neto',
                        title: 'Importe Neto',
                        width: 100,
                    },
                    {
                        field: 'state',
                        title: 'Opciones',
                        sortable: false,
                        width: 130,
                        overflow: 'visible',
                        autoHide: false,
                        textAlign: 'center',
                        template: function (row) {

                            let actions = '<div class="actions">';

                            if (row.adm_payment_id == 5 || row.adm_payment_id == 6) {
                                if (row.state_id == 1) {
                                    actions += '<a href="#" class="registro_state_cont btn btn-sm btn-clean btn-icon btn-icon-md" title="REGISTRO CONTABILIDAD">';
                                    actions += '<i class="fa fa-solid fa-check"></i>';
                                    actions += '</a>';
                                } else if (row.state_id == 2) {
                                    actions += '<a href="#" class="registro_fi btn btn-sm btn-clean btn-icon btn-icon-md" title="REGISTRO FINANZAS">';
                                    actions += '<i class="fa fa-solid fa-chart-line"></i>';
                                    actions += '</a>';
                                } else if (row.state_id == 3) {
                                    actions += '<a href="#" class="registro_tes btn btn-sm btn-clean btn-icon btn-icon-md" title="REGISTRO TESORERIA">';
                                    actions += '<i class="fa fa-solid fa-stamp"></i>';
                                    actions += '</a>';
                                }
                            } else if (row.adm_payment_id == 7) {
                                if (row.state_id == 5) {
                                    actions += '<a href="#" class="registro_state_cont btn btn-sm btn-clean btn-icon btn-icon-md" title="REGISTRO CONTABILIDAD">';
                                    actions += '<i class="fa fa-solid fa-check"></i>';
                                    actions += '</a>';
                                } else if (row.state_id == 1 || row.state_id == 2) {
                                    actions += '<a href="#" class="registro_fi btn btn-sm btn-clean btn-icon btn-icon-md" title="REGISTRO FINANZAS">';
                                    actions += '<i class="fa fa-solid fa-chart-line"></i>';
                                    actions += '</a>';
                                } else if (row.state_id == 3 || row.state_id == 6) {
                                    actions += '<a href="#" class="registro_tes btn btn-sm btn-clean btn-icon btn-icon-md" title="REGISTRO TESORERIA">';
                                    actions += '<i class="fa fa-solid fa-stamp"></i>';
                                    actions += '</a>';
                                }
                            }

                            return actions;
                        },
                    },
                ]
            });
            this.datatable.columns('id').visible(false);
            this.datatable.columns('state_id').visible(false);
            this.datatable.columns('lleno').visible(false);
        },
        manageActions: function (event) {

            event.preventDefault();

            let id = $(event.target).parents('tr').find('td[data-field="id"] span').html();
            let condicion_pago = $(event.target).parents('tr').find('td[data-field="condicion_pago"] span').html();
            let num_mov = $(event.target).parents('tr').find('td[data-field="num_mov"] span').html();
            let neto = $(event.target).parents('tr').find('td[data-field="neto"] span').html();

            if ($(event.target).hasClass('edit')) {

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

            } else if ($(event.target).hasClass('registro_state_cont')) {

                Swal.fire({
                    title: 'El importe neto es: ' + neto,
                    text: '¿Desea aprobar por contabilidad?',
                    type: "warning",
                    heightAuto: false,
                    showCancelButton: true,
                    confirmButtonText: 'Aprobar',
                    cancelButtonText: 'Desaprobar'
                }).then(result => {
                    if (result.value) {
                        EventBus.$emit('loading', true);
                        axios.post(this.url_aprobar_cont, {
                            id: id,
                        }).then(response => {
                            EventBus.$emit('loading', false);

                            this.$parent.alertMsg(response.data);
                            EventBus.$emit('refresh_table');

                        }).catch(error => {
                            EventBus.$emit('loading', false);
                            console.log(error);
                            console.log(error.response);
                        });
                    } else {
                        EventBus.$emit('loading', true);
                        axios.post(this.url_desaprobar_cont, {
                            id: id,
                        }).then(response => {
                            EventBus.$emit('loading', false);

                            this.$parent.alertMsg(response.data);
                            EventBus.$emit('refresh_table');

                        }).catch(error => {
                            EventBus.$emit('loading', false);
                            console.log(error);
                            console.log(error.response);
                        });
                    }
                });


            } else if ($(event.target).hasClass('registro_fi')) {

                EventBus.$emit('modal_finanzas', id, condicion_pago, num_mov, neto);

            } else if ($(event.target).hasClass('registro_tes')) {

                EventBus.$emit('modal_tesoreria', id, condicion_pago, num_mov, neto);

            }
        },
    }
};
</script>