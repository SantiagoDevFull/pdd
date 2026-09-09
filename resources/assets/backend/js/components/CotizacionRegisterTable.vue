<template>
    <!--begin::Portlet-->
    <div class="kt-portlet" v-if="show_table == 1">
        <div class="kt-portlet__head">
            <div class="kt-portlet__head-label">
                <h3 class="kt-portlet__head-title">
                    Repuestos
                </h3>
            </div>
            <div class="kt-portlet__head-toolbar">
                <div class="kt-portlet__head-actions">
                    <a href="#" @click.prevent="openModal()" class="btn btn-success">
                        <i class="fa fa-plus"></i> Agregar Repuesto
                    </a>
                </div>
            </div>
        </div>

        <!--begin::Form-->
        <form class="kt-form" @submit.prevent="formController(url, $event)">
            <div class="kt-portlet__body">
                <div class="row">
                    <div class="col-12" @click="manageActions">
                        <!--begin: Datatable -->
                        <div class="kt-datatable"></div>
                        <!--end: Datatable -->
                    </div>
                </div>
            </div>
            <div class="kt-portlet__foot">
                <div class="kt-form__actions">
                    <div class="row">
                        <div class="col-12 kt-align-right">
                            <button type="submit" class="btn btn-primary">Crear</button>
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
        url: {
            type: String,
            default: ''
        },
        url_get_article: {
            type: String,
            default: ''
        }
    },
    data() {
        return {
            show_table: 0,
            datatable: undefined,
            article_list: [],
            model: '',
            currency: '',
            item_number: 0,
        }
    },
    created() {
        EventBus.$on('show_table', function (data) {
            // console.log(data);
            this.show_table = 1;
            this.model = data.model;

            this.article_list = data.order_work_details;
            this.item_number = this.article_list.length;

            this.$nextTick(function () {
                this.fillTableX();
            });
        }.bind(this));

        EventBus.$on('sendForm', function (model) {
            EventBus.$emit('loading', true);
            this.updateOrAddArticle(model);
        }.bind(this));

    },
    mounted() {

    },
    watch: {

    },
    computed: {

    },
    methods: {
        updateOrAddArticle(model) {
            let found = false;
            for (let i = 0; i < this.article_list.length; i++) {
                
                if (this.article_list[i].item_number == model.item_number) {
                    this.article_list[i].quantity = model.quantity;
                    this.article_list[i].price = model.price;
                    this.article_list[i].import = model.import;
                    this.article_list[i].percentage = model.percentage;
                    this.article_list[i].discount = model.discount;
                    this.article_list[i].total = model.total;
                    found = true;
                    break;
                }
            }
            
            if (!found) {
                this.addArticle(model);
            } else {
                this.datatable.destroy();
                this.fillTableX();

                EventBus.$emit('cotizacion_register_modal_hide');
                EventBus.$emit('loading', false);
            }
            
        },
        formController: function (url, event) {
            var target = $(event.target);
            var url = url;
            var fd = new FormData(event.target);

            if (this.article_list != '' && this.article_list != []) {
                EventBus.$emit('loading', true);
                axios.post(this.url, {
                    model: this.model,
                    article_list: this.article_list,
                }).then(response => {
                    EventBus.$emit('loading', false);
                    console.log(response);
                    Swal.fire({
                        title: '¡Bien!',
                        text: 'Cotización creado correctamente',
                        type: "success",
                        timer: 2000,
                        heightAuto: false
                    }).then((confirmed) => {
                        window.location = '/logistica/registro-cotizacion';
                    })
                }).catch(error => {
                    EventBus.$emit('loading', false);
                    console.log(error);
                    console.log(error.response);
                    Swal.fire({
                        title: '¡Error!',
                        text: error,
                        type: "error",
                        heightAuto: false,
                    });
                });
            } else {
                Swal.fire({
                    title: '¡Error!',
                    text: 'Debes agregar al menos 1 Artículo.',
                    type: "error",
                    heightAuto: false,
                });
            }
        },
        addArticle: function (model) {
            axios.post(this.url_get_article, {
                model: model,
                item_number: this.article_list.length,
            }).then(response => {
                console.log(response.data);
                this.article_list.push(response.data);
                this.datatable.destroy();
                this.fillTableX();
                EventBus.$emit('loading', false);

                EventBus.$emit('cotizacion_register_modal_hide');
                EventBus.$emit('add_article_id', response.data.id);
            }).catch(error => {
                console.log(error);
                console.log(error.response);
            });
        },
        openModal: function () {
            EventBus.$emit('openModal');
        },
        fillTableX: function () {
            let vm = this;
            this.datatable = $('.kt-datatable').KTDatatable({
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
                        field: 'item_number',
                        title: '#',
                        width: 40,
                        textAlign: 'center',
                    },
                    {
                        field: 'article_type',
                        title: 'Tipo Servicio',
                        width: 60,
                        textAlign: 'center',
                    },
                    {
                        field: 'id',
                        title: 'Id Articulo',
                        width: 60,
                        textAlign: 'center',
                    },
                    {
                        field: 'name',
                        title: 'Descripción',
                        width: 100,
                        textAlign: 'center',
                    },
                    {
                        field: 'stock_good',
                        title: 'Stock',
                        width: 70,
                        textAlign: 'center',
                    },
                    {
                        field: 'cost',
                        title: 'Costo',
                        width: 70,
                        textAlign: 'center',
                    },

                    {
                        field: 'quantity',
                        title: 'Cantidad',
                        width: 70,
                        textAlign: 'center',
                    },
                    {
                        field: 'price',
                        title: 'Precio a vender',
                        width: 70,
                        textAlign: 'center',
                    },
                    {
                        field: 'import',
                        title: 'Importe',
                        width: 70,
                        textAlign: 'center',
                    },
                    {
                        field: 'percentage',
                        title: '% Desc.',
                        width: 60,
                        textAlign: 'center',
                    },
                    {
                        field: 'discount',
                        title: 'Descuento',
                        width: 70,
                        textAlign: 'center',
                    },
                    {
                        field: 'total',
                        title: 'Valor Venta',
                        width: 70,
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
                                    <a href="#" class="edit btn btn-info btn-sm btn-icon btn-icon-md" title="Editar">\
                                        <i class="la la-edit"></i>\
                                    </a>\
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
                let id = $(event.target).parents('tr').find('td[data-field="id"] span').html();
                let index = this.article_list.findIndex((element) => element.item_number == item_number);

                Swal.fire({
                    title: '¡Cuidado!',
                    text: '¿Seguro que desea eliminar el artículo?',
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

                        this.article_list.splice(index, 1);
                        let new_item_number = 0;
                        this.article_list.map(function (item, index) {
                            item.item_number = ++new_item_number;
                        });
                        this.datatable.destroy();
                        this.fillTableX();

                        EventBus.$emit('remove_article_id', id);
                    }
                });
            } else if ($(event.target).hasClass('edit')) {
                event.preventDefault();
                let item_number = $(event.target).parents('tr').find('td[data-field="item_number"] span').html();
                let id = $(event.target).parents('tr').find('td[data-field="id"] span').html();
                let article_type = $(event.target).parents('tr').find('td[data-field="article_type"] span').html();
                let cost = $(event.target).parents('tr').find('td[data-field="cost"] span').html();
                let quantity = $(event.target).parents('tr').find('td[data-field="quantity"] span').html();
                let price = $(event.target).parents('tr').find('td[data-field="price"] span').html();
                let stock_good = $(event.target).parents('tr').find('td[data-field="stock_good"] span').html();
                let importe = $(event.target).parents('tr').find('td[data-field="import"] span').html();
                let percentage = $(event.target).parents('tr').find('td[data-field="percentage"] span').html();
                let discount = $(event.target).parents('tr').find('td[data-field="discount"] span').html();
                let total = $(event.target).parents('tr').find('td[data-field="total"] span').html();
                let index = this.article_list.findIndex((element) => element.item_number == item_number);

                EventBus.$emit('editModal', item_number, id, article_type, cost,quantity,price,stock_good,importe,percentage,discount,total);

            }
        },

    }
};
</script>