<template>
    <!--begin::Portlet-->
    <div class="kt-portlet" v-if="show_table == 1">
        <div class="kt-portlet__head">
            <div class="kt-portlet__head-label">
                <h3 class="kt-portlet__head-title">
                    Artículos
                </h3>
            </div>
            <div class="kt-portlet__head-toolbar">
                <div class="kt-portlet__head-actions">
                    <a href="#" @click.prevent="openModal()" class="btn btn-success">
                        <i class="fa fa-plus"></i> Agregar artículo
                    </a>
                </div>
            </div>
        </div>

        <!--begin::Form-->
        <form class="kt-form" @submit.prevent="formController(url_store, $event)">
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
                            <button type="button" class="btn btn-danger" @click="Reset()">CANCELAR {{ model.type }}</button>
                            <button type="submit" class="btn btn-primary">CREAR {{ model.type }}</button>
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

export default{
    props:{
        url_store:{
            type:String,
            default:'',
        },
        url_get_article:{
            type: String,
            default:'',
        },
    },
    data(){
        return{
            model:'',
            show_table: 0,
            article_list:[],
            articles:[],
        }
    }, 
    created(){
        EventBus.$on('show_table', function(data) {
                // console.log(data);
                this.show_table = 1;
                this.model=data.model;
                this.articles = data.articles;

                this.$nextTick(function() {
                    this.fillTableX();
                });
            }.bind(this));

        EventBus.$on('sendForm', function(model) {
            EventBus.$emit('loading', true);
            this.addArticle(model);
        }.bind(this));
    },
    methods:{
        Reset:function(){
            Swal.fire({
                        title: '¡Cuidado!',
                        text: '¿Seguro que desea cancelar la Orden de Compra?',
                        type: "warning",
                        heightAuto: false,
                        showCancelButton: true,
                        confirmButtonText: 'Sí',
                        cancelButtonText: 'No'
                    }).then(result => {
                        if ( result.value ) {
                            window.location = '/logistica/registro-orden-pedido';
                        }
                    });
        },
        manageActions: function(event) {
                if ( $(event.target).hasClass('delete') ) {
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
                        if ( result.value ) {
                            Swal.fire({
                                title: 'Ok!',
                                text: 'Se ha eliminado el artículo',
                                type: "success",
                                heightAuto: false,
                            });

                            this.article_list.splice(index, 1);
                            let new_item_number = 0;
                            this.article_list.map(function(item, index) {
                                item.item_number = ++new_item_number;
                            });
                            this.datatable.destroy();
                            this.fillTableX();

                            EventBus.$emit('remove_article_id', id);
                        }
                    });
                }
            },
        openModal: function() {
                EventBus.$emit('article-order-modal', this.articles);
        },
        addArticle: function(model) {
                axios.post(this.url_get_article, {
                    model: model,
                }).then(response => {
                    console.log(response.data);
                    this.article_list.push(response.data);
                    this.datatable.destroy();
                    this.fillTableX();
                    EventBus.$emit('loading', false);
                    EventBus.$emit('clear_modal');
                    EventBus.$emit('add_article_id', response.data.article_id);
                }).catch(error => {
                    console.log(error);
                    console.log(error.response);
                });
            },
        formController: function (url_store, event) {

            if ( this.article_list != '' && this.article_list != [] ) {
                var target = $(event.target);
                var url_store = url_store;
                var fd = new FormData(event.target);

                EventBus.$emit('loading', true);
                axios.post(url_store, {
                model: this.model,
                article_list: this.article_list,
                }).then(response => {
                    EventBus.$emit('loading', false);
                    target.find('select').prop('disabled', true);
                    this.$parent.alertMsg(response.data);
                }).catch(error => {
                    EventBus.$emit('loading', false);
                    console.log(error.response);
                    var obj = error.response.data.errors;
                    $('html, body').animate({
                        scrollTop: 0
                    }, 500, 'swing');
                    $.each(obj, function (i, item) {
                        let c_target = target.find("#" + i + "-error");
                        if (!c_target.attr('data-required')) {
                            let p = c_target.prev();
                            p.addClass('is-invalid');
                        } else {
                            c_target.css('display', 'block');
                        }
                        c_target.html(item);
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
        fillTableX: function() {
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
                    columns: [

                        {
                            field: 'article_id',
                            title: '#',
                            width: 50,
                            textAlign: 'center',
                        },
                        {
                            field: "warehouse_types_name",
                            title: "Almacén",
                            width: 200,
                            textAlign: 'center',
                        },
                        {
                            field: "article_name",
                            title: "Descripción",
                            width: 200,
                            textAlign: 'center',
                        }, 
                        {
                            field: "quantity",
                            title: "Cantidad",
                            width: 190,
                            textAlign: 'center',
                        },
                        {
                            field: "price_unit",
                            title: "Precio Unit.",
                            width: 190,
                            textAlign: 'center',
                        },
                        {
                            field: 'options',
                            title: 'Opciones',
                            sortable: false,
                            width: 120,
                            overflow: 'visible',
                            autoHide: false,
                            textAlign: 'center',
                            class: 'td-sticky',
                            template: function() {
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

                this.datatable.columns('article_id').visible(false);
            },
    }
}

</script>