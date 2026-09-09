<template>
    <!--begin::Portlet-->
    <div class="kt-portlet" v-if="show_table == 1">
        <div class="kt-portlet__head">
            <div class="kt-portlet__head-label">
                <h3 class="kt-portlet__head-title">
                    Conceptos
                </h3>
            </div>
            <div class="kt-portlet__head-toolbar">
                <div class="kt-portlet__head-actions">
                    <a href="#" @click.prevent="openModal()" class="btn btn-success">
                        <i class="fa fa-plus"></i> Agregar concepto
                    </a>
                </div>
            </div>
        </div>

        <!--begin::Form-->

        <div class="kt-portlet__body">
            <div class="row">
                <div class="col-12" @click="manageActions">
                    <!--begin: Datatable -->
                    <div class="kt-datatable_sale"></div>
                    <!--end: Datatable -->
                </div>
            </div>
        </div>
        <div class="kt-portlet__foot">
            <div class="kt-form__actions">
                <div class="row">
                    <div class="col-12 kt-align-left">
                        <h3 class="text-danger">Total venta : <span class="text-primary">{{ this.total }}</span></h3>
                    </div>
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
        url_get_article: {
            type: String,
            default: ''
        }
    },
    data() {
        return {
            show_table: 0,
            datatable_sale: undefined,
            article_list: [],
            perception_percentage: '',
            currency: '',
            articles: [],
            item_number: 0,
            total: 0
        }
    },
    created() {
        EventBus.$on('show_table', function (data) {
            this.show_table = 1;
            this.$nextTick(function () {
                this.fillTableX();
            });
        }.bind(this));

        EventBus.$on('sendFormSale', function (data) {
            EventBus.$emit('loading', true);
            this.addArticleSale(data);
            EventBus.$emit('variable_sale', data,this.article_list);

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
        openModal: function () {
            EventBus.$emit('open_modal');
        },
        addArticleSale: function (data) {
            this.total = parseFloat(this.total) + parseFloat(data.total);
            data.item_number = this.article_list.length + 1;
            this.article_list.push(data);
            this.datatable_sale.destroy();
            this.fillTableX();
        },
        fillTableX: function () {
            let vm = this;
            this.datatable_sale = $('.kt-datatable_sale').KTDatatable({
                // datasource definition
                data: {
                    type: 'local',
                    source: vm.article_list,
                },

                // layout definition
                layout: {
                    scroll: true, // enable/disable datatable scroll both horizontal and vertical when needed.
                    height: 400,
                    footer: false // display/hide footer
                },

                // column sorting
                sortable: false,
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
                        field: 'item_number',
                        title: '#',
                        width: 20,
                        textAlign: 'center',
                    },
                    {
                        field: 'concept',
                        title: 'Concepto',
                        width: 100,
                        textAlign: 'center',
                    },
                    {
                        field: 'unit_name',
                        title: 'Unidad de Medida',
                        width: 100,
                        textAlign: 'center',
                    },
                    {
                        field: 'quantity',
                        title: 'Cantidad',
                        width: 80,
                        textAlign: 'center',
                    },
                    {
                        field: 'price_igv',
                        title: 'Precio Unitario',
                        width: 80,
                        textAlign: 'center',
                    },
                    {
                        field: 'value_type_name',
                        title: 'Tipo de Venta',
                        width: 100,
                        textAlign: 'center',
                    },
                    {
                        field: 'sale_value',
                        title: 'Valor',
                        width: 80,
                        textAlign: 'center',
                    },
                    {
                        field: 'igv',
                        title: 'IGV',
                        width: 80,
                        textAlign: 'center',
                    },
                    {
                        field: 'total',
                        title: 'Total',
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
                                    <a href="#" class="delete btn btn-danger btn-sm btn-icon btn-icon-md" title="Eliminar">\
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
            if ($(event.target).hasClass('delete')) {
                event.preventDefault();
                let item_number = $(event.target).parents('tr').find('td[data-field="item_number"] span').html();
                let index = this.article_list.findIndex((element) => element.item_number == item_number);

                Swal.fire({
                    title: '¡Cuidado!',
                    text: '¿Seguro que desea eliminar el concepto?',
                    type: "warning",
                    heightAuto: false,
                    showCancelButton: true,
                    confirmButtonText: 'Sí',
                    cancelButtonText: 'No'
                }).then(result => {
                    if (result.value) {
                        Swal.fire({
                            title: 'Ok!',
                            text: 'Se ha eliminado el artículo',
                            type: "success",
                            heightAuto: false,
                        });

                        this.total = 0;
                        this.article_list.splice(index, 1);
                        let new_item_number = 0;
                        let acum = 0;
                        this.article_list.map(function (item, index) {
                            item.item_number = ++new_item_number;

                            acum = parseFloat(acum) + parseFloat(item.total);
                        });

                        EventBus.$emit('variable_sale_asign', acum,this.article_list);
                        this.total = parseFloat(acum);
                        this.datatable_sale.destroy();
                        this.fillTableX();
                    }
                });
            }
        },
    }
};
</script>