<template>
    <div class="modal fade" id="modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="">Cotización {{ text }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    </button>
                </div>

                <div class="modal-body">
                    <div class="kt-portlet__body">

                        <a href="#" @click.prevent="openModal()" class="btn btn-success">
                            <i class="fa fa-plus"></i> Agregar Repuesto/Servicio
                        </a>

                        <hr>

                        <div class="row">
                            <div class="col-12">
                                <table class="table table-vertical-middle table-layout-fixed">
                                    <thead>
                                        <tr>
                                            <th class="2">Tipo</th>
                                            <th class="2">Descripción</th>
                                            <th class="1">Costo</th>
                                            <th class="1">Cantidad</th>
                                            <th class="1">Precio</th>
                                            <th class="1">Importe</th>
                                            <th class="1">% Desc.</th>
                                            <th class="1">Desc.</th>
                                            <th class="1">Total</th>
                                            <th class="1">Opciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="article in article_list" :key="article.id">
                                            <td>{{ article.type }}</td>
                                            <td>{{ article.name }}</td>
                                            <td>{{ article.cost }}</td>
                                            <td>{{ article.quantity }}</td>
                                            <td>{{ article.price }}</td>
                                            <td>{{ article.import }}</td>
                                            <td>{{ article.desc_percentage }}</td>
                                            <td>{{ article.desc }}</td>
                                            <td>{{ article.neto }}</td>
                                            <td>
                                                <a href="#" @click.prevent="editArticle(article.id)"
                                                    class="edit btn btn-info btn-sm btn-icon btn-icon-md"
                                                    title="Editar">
                                                    <i class="la la-edit"></i>
                                                </a>
                                                <a href="#" v-if="article_list.length > 1"
                                                    @click.prevent="deleteArticle(article.id, article.type, article.name)"
                                                    class="delete btn btn-danger btn-sm btn-icon btn-icon-md"
                                                    title="Eliminar">
                                                    <i class="la la-trash"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import EventBus from '../event-bus';

export default {
    props: {
        url_detail_article: {
            type: String,
            default: '',
        },
        url_delete_detail_article: {
            type: String,
            default: '',
        }
    },
    data() {
        return {
            show_table: 0,
            datatable: undefined,
            article_list: [],
            text: ''
        }
    },
    created() {
        EventBus.$on('show_modal', function (data, serie) {

            this.text = serie;
            this.article_list = data;

            EventBus.$emit('asignCotId', data[0].cots_id);

            $('#modal').modal('show');

        }.bind(this));

        EventBus.$on('articleListUpdated', function (data) {

            this.article_list = data;

        }.bind(this));
    },
    methods: {

        editArticle(id) {

            EventBus.$emit('loading', true);

            axios.post(this.url_detail_article, {
                id: id
            }).then(response => {
                EventBus.$emit('loading', false);
                EventBus.$emit('editModal', response.data);
                // console.log(response.data);
            }).catch(error => {
                EventBus.$emit('loading', false);
                console.log(error);
                console.log(error.response);
            });

        },
        deleteArticle(id, type, name) {

            Swal.fire({
                title: '¡Cuidado!',
                text: '¿Seguro que desea eliminar ' + type + ' ' + name + '?',
                type: "warning",
                heightAuto: false,
                showCancelButton: true,
                confirmButtonText: 'Sí',
                cancelButtonText: 'No'
            }).then(result => {

                if (result.value) {
                    EventBus.$emit('loading', true);
                    axios.post(this.url_delete_detail_article, {
                        id_detail: id,
                        cots_id: this.article_list[0].cots_id
                    }).then(response => {

                        EventBus.$emit('loading', false);
                        EventBus.$emit('articleListUpdated', response.data.cot_detail);
                        EventBus.$emit('refresh_table');

                        Swal.fire({
                            title: 'Ok!',
                            text: 'Se eliminó ' + type + ' ' + name + '?',
                            type: "success",
                            heightAuto: false,
                        });
                    }).catch(error => {
                        EventBus.$emit('loading', false);
                        console.log(error);
                        console.log(error.response);
                    })
                } else {
                    EventBus.$emit('loading', false);
                }
            });

        },
        openModal() {

            EventBus.$emit('openModal');
        }

    }
};
</script>