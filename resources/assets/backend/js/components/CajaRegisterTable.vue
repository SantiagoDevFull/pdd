<template>
    <!--begin::Portlet-->
    <div class="kt-portlet" v-if="show_table == 1">
        <div class="kt-portlet__head">
            <div class="kt-portlet__head-label">
                <h3 class="kt-portlet__head-title">
                    Artículos
                </h3>
            </div>
            <div class="col-6">
                <div class="custom-file mt-2">
                    <input type="file" class="form-control" id="file" name="file" @change="validateFile" />
                </div>
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
                            <button type="submit" class="btn btn-primary" id="crear" disabled>Crear</button>
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
        igv: {
            type: Object,
            default: ''
        },
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
        }
    },
    created() {
        EventBus.$on('show_table', function (data) {
            this.show_table = 1;

            this.model = data.model;
            this.currency = data.currency;

            this.$nextTick(function () {
                this.fillTableX();
            });
        }.bind(this));

        EventBus.$on('sendForm', function (model) {
            EventBus.$emit('loading', true);


            this.addArticle(model, this.igv.value);
        }.bind(this));

        EventBus.$on('reset_cochera_register', function () {
            this.show_table = 0;
            this.datatable = undefined;
            this.article_list = [];
            this.model = '';
            this.currency = '';
        }.bind(this));
    },
    mounted() {

    },
    watch: {

    },
    computed: {

    },
    methods: {
        validateFile: function (event) {
            let fileInput = event.target;
            let file = fileInput.files[0];

            if (file) {
                const allowedExtensions = ['pdf', 'jpeg', 'jpg', 'png'];
                const fileExtension = file.name.split('.').pop().toLowerCase();

                if (!allowedExtensions.includes(fileExtension)) {
                    fileInput.classList.add('is-invalid');
                    fileInput.classList.remove('is-valid');

                    document.getElementById('crear').disabled = true;
                    Swal.fire({
                        title: '¡Error!',
                        text: 'El archivo debe ser de tipo [.pdf] [.jpeg] [.jpg] [.png]',
                        type: "error",
                        heightAuto: false,
                    });
                } else {

                    document.getElementById('crear').disabled = false;
                    fileInput.classList.remove('is-invalid');
                    fileInput.classList.add('is-valid');
                }
            }
        },
        openModal: function () {
            EventBus.$emit('cochera_register_modal', this.igv, this.currency, this.model.movement_type_id);
        },
        addArticle: function (model, igv_percentage) {

            model.item_number = this.article_list.length + 1;

            const newArticle = JSON.parse(JSON.stringify(model));
            this.article_list.push(newArticle);

            this.datatable.destroy();
            this.fillTableX();
            EventBus.$emit('loading', false);

            EventBus.$emit('cochera_register_modal_hide');
            EventBus.$emit('add_article_id', model.item_number);
        },
        fillTableX: function () {

            this.datatable = $('.kt-datatable').KTDatatable({
                // datasource definition
                data: {
                    type: 'local',
                    source: this.article_list,
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
                        field: 'options',
                        title: 'Opciones',
                        sortable: false,
                        width: 120,
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
                    {
                        field: 'item_number',
                        title: '#',
                        width: 20,
                        textAlign: 'center',
                    },
                    {
                        field: "article_name",
                        title: "Descripción",
                        width: 120,
                        textAlign: 'center',
                    },
                    {
                        field: 'quantity',
                        title: 'Cantidad convertida',
                        width: 80,
                        textAlign: 'right',
                    },
                    
                    {
                        field: "price",
                        title: "Precio unitario",
                        width: 80,
                        textAlign: 'center',
                    },
                    {
                        field: "sale_value",
                        title: "Valor venta",
                        width: 80,
                        textAlign: 'center',
                    },
                    {
                        field: "igv",
                        title: "IGV",
                        width: 80,
                        textAlign: 'center',
                    },
                    {
                        field: "total",
                        title: "Total",
                        width: 80,
                        textAlign: 'center',
                    },
                    {
                        field: "igv_percentage",
                        title: "% IGV",
                        width: 60,
                        textAlign: 'center',
                    },
                    {
                        field: 'currency',
                        title: 'Moneda',
                        textAlign: 'center',
                        width: 60,
                    },
                    {
                        field: 'placa',
                        title: 'Placa',
                        textAlign: 'center',
                        width: 60,
                    },
                    {
                        field: 'id',
                        title: 'ID',
                        width: 0,
                        overflow: 'hidden',
                        responsive: {
                            hidden: 'sm',
                            hidden: 'md',
                            hidden: 'lg',
                            hidden: 'xl'
                        }
                    },

                ]
            });

        },
        manageActions: function (event) {
            if ($(event.target).hasClass('delete')) {
                event.preventDefault();
                let item_number = $(event.target).parents('tr').find('td[data-field="item_number"] span').html();
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

                    }
                });
            }
        },

        formController: function (url, event) {

            if (this.article_list != '' && this.article_list.length > 0) {
                EventBus.$emit('loading', true);

                let formData = new FormData();

                formData.append('model', JSON.stringify(this.model));
                formData.append('article_list', JSON.stringify(this.article_list));

                let fileInput = document.querySelector('#file');
                if (fileInput && fileInput.files.length > 0) {
                    formData.append('file', fileInput.files[0]);
                }

                axios.post(this.url, formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                }).then(response => {
                    EventBus.$emit('loading', false);
                    this.$parent.alertMsg(response.data);
                    console.log(response);

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
        }
    }
};
</script>