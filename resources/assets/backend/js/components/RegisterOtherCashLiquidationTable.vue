<template>
    <!--begin::Portlet-->
    <div class="kt-portlet" v-if="show_table_liq == 1">
        <div class="kt-portlet__head">
            <div class="kt-portlet__head-label">
                <h3 class="kt-portlet__head-title">
                    Liquidaciones
                </h3>
            </div>
            <div class="kt-portlet__head-toolbar">
                <div class="kt-portlet__head-actions">
                    <a href="#" @click.prevent="openModalLiquidation()" class="btn btn-success">
                        <i class="fa fa-plus"></i> Agregar liquidación
                    </a>
                </div>
            </div>
        </div>

        <!--begin::Form-->
        <form class="kt-form" @submit.prevent="save()">
            <div class="kt-portlet__body">
                <div class="row">
                    <div class="col-12" @click="manageActions">
                        <!--begin: Datatable -->
                        <div class="kt-datatable_liquidation"></div>
                        <!--end: Datatable -->
                    </div>
                </div>
            </div>
            <div class="kt-portlet__foot">
                <div class="kt-form__actions">
                    <div class="row">
                        <div class="col-6 kt-align-left">
                            <h3 class="text-danger">Total liquidado : <span class="text-primary">{{ this.total_liquidation }}</span></h3>
                        </div>
                        <div class="col-6 kt-align-right">
                            <button type="submit" class="btn btn-primary">Finalizar y guardar</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
        <!--end::Form-->
    </div>
    <!--end::Portlet-->
</template>

<script>
import EventBus from '../event-bus';

export default {
    props: {
        url_store: {
            type: String,
            default: ''
        }
    },
    data() {
        return {
            show_table_liq: 0,
            liquidations_list: [],
            conceptos: [],
            model: '',
            total_liquidation: 0,
            datatable_liquidation: undefined,
            item_number_2: 0,
            variable: ''
        }
    },
    created() {
        EventBus.$on('show_table_liquidation', function (data_model) {
            // console.log(data);
            this.show_table_liq = 1;
            this.model = data_model;
            this.variable = 0;

            this.$nextTick(function () {
                this.fillTableXliquidation();
            });
        }.bind(this));

        EventBus.$on('variable_sale', function (data, article_list) {
            this.variable = parseFloat(this.variable) + parseFloat(data.total);
            this.conceptos = [];
            this.conceptos = article_list;
        }.bind(this));

        EventBus.$on('variable_sale_asign', function (data, article_list) {
            this.variable = parseFloat(data);
            this.conceptos = [];
            this.conceptos = article_list;
        }.bind(this));

        EventBus.$on('sendFormLiquidation', function (data) {

            EventBus.$emit('loading', true);
            this.addArticleLiquidation(data);
            EventBus.$emit('loading', false);
        }.bind(this));

    },
    mounted() {

    },
    watch: {

    },
    computed: {

    },
    methods: {
        save: function () {

            if (this.total_liquidation == 0) {
                Swal.fire({
                    title: "Error",
                    text: "El total liquidado no puede ser cero.",
                    type: "error",
                    heightAuto: false,
                })
                return;
            } else if (this.total_liquidation != this.variable) {
                Swal.fire({
                    title: "Error",
                    text: "El total liquidado no coincide con el Total de la venta.",
                    type: "error",
                    heightAuto: false,
                })
                return;
            }

            EventBus.$emit('loading', true);

            axios.post(this.url_store, {
                model: this.model,
                liquidations_list: this.liquidations_list,
                conceptos: this.conceptos,

            }).then(response => {
                // console.log(response);
                EventBus.$emit('loading', false);
                this.$parent.alertMsg(response.data);
            }).catch(error => {
                EventBus.$emit('loading', false);
                console.log(error);
                console.log(error.response)
            });
        },
        openModalLiquidation: function () {
            EventBus.$emit('open_modal_liquidation');
        },
        addArticleLiquidation: function (data) {
            this.total_liquidation = parseFloat(this.total_liquidation) + parseFloat(data.amount);
            data.item_number_2 = this.liquidations_list.length + 1;

            this.liquidations_list.push(data);
            this.datatable_liquidation.destroy();
            this.fillTableXliquidation();
        },
        fillTableXliquidation: function () {
            let vm = this;
            this.datatable_liquidation = $('.kt-datatable_liquidation').KTDatatable({
                // datasource definition
                data: {
                    type: 'local',
                    source: vm.liquidations_list,
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
                        field: 'item_number_2',
                        title: '#',
                        width: 20,
                        textAlign: 'center',
                    },
                    {
                        field: 'payment_name',
                        title: 'Condición de Pago',
                        width: 100,
                        textAlign: 'center',
                    },
                    {
                        field: 'payment_method_name',
                        title: 'Forma de Pago',
                        width: 100,
                        textAlign: 'center',
                    },
                    {
                        field: 'currency_name',
                        title: 'Moneda',
                        width: 80,
                        textAlign: 'center',
                    },
                    {
                        field: 'bank_account_name',
                        title: 'Banco',
                        width: 120,
                        textAlign: 'center',
                    },
                    {
                        field: 'operation_number',
                        title: 'N° de Operación',
                        width: 100,
                        textAlign: 'center',
                    },
                    {
                        field: 'amount',
                        title: 'Monto',
                        width: 80,
                        textAlign: 'center',
                    },
                    {
                        field: 'options',
                        title: 'Opciones',
                        sortable: false,
                        width: 100,
                        overflow: 'visible',
                        autoHide: false,
                        textAlign: 'center',
                        template: function () {
                            return '\
                                <div class="actions">\
                                    <a href="#" class="deleteLiq btn btn-danger btn-sm btn-icon btn-icon-md" title="Eliminar">\
                                        <i class="la la-trash"></i>\
                                    </a>\
                                </div>\
                            ';
                        },
                    },
                ]
            });

            //this.datatable.columns('id').visible(false);
        },
        manageActions: function (event) {
            if ($(event.target).hasClass('deleteLiq')) {
                event.preventDefault();
                let item_number_2 = $(event.target).parents('tr').find('td[data-field="item_number_2"] span').html();
                let index = this.liquidations_list.findIndex((element) => element.item_number_2 == item_number_2);

                Swal.fire({
                    title: '¡Cuidado!',
                    text: '¿Seguro que desea eliminar la liquidación?',
                    type: "warning",
                    heightAuto: false,
                    showCancelButton: true,
                    confirmButtonText: 'Sí',
                    cancelButtonText: 'No'
                }).then(result => {
                    if (result.value) {
                        Swal.fire({
                            title: 'Ok!',
                            text: 'Se ha eliminado la liquidación',
                            type: "success",
                            heightAuto: false,
                        });

                        this.total_liquidation = 0;
                        this.liquidations_list.splice(index, 1);
                        let new_item_number = 0;
                        let acum = 0;
                        this.liquidations_list.map(function (item, index) {
                            item.item_number_2 = ++new_item_number;

                            acum = parseFloat(acum) + parseFloat(item.amount);
                        });

                        this.total_liquidation = parseFloat(acum);
                        this.datatable_liquidation.destroy();
                        this.fillTableXliquidation();
                    }
                });
            }
        },

    }
};
</script>