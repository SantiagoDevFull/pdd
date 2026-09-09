<template>
    <!--begin::Modal-->
    <div class="modal fade" id="modalArticles" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">
                        <span class="badge bg-danger text-white">{{ total }}</span> Artículos encontrados en el almacén
                        <span class="badge bg-danger text-white">{{ warehouse_type_name }}</span>
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-6">
                            <ul>
                                <li v-for="(article, index) in articlesLeft" :key="index">
                                    {{ article.item_number }} - {{ article.name }}
                                </li>
                            </ul>
                        </div>
                        <div class="col-6">
                            <ul>
                                <li v-for="(article, index) in articlesRight" :key="index">
                                    {{ article.item_number }} - {{ article.name }}
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <!--end::Modal-->
</template>

<script>
import EventBus from '../event-bus';

export default {
    props: {
    },
    data() {
        return {
            model: {

            },
            articles: [],
            total: '',
            warehouse_type_name: ''
        }
    },
    computed: {
        articlesLeft() {
            return this.articles.slice(0, Math.ceil(this.articles.length / 2));
        },
        articlesRight() {
            return this.articles.slice(Math.ceil(this.articles.length / 2));
        }
    },
    created() {
        EventBus.$on('list_articles', function (data) {

            this.articles = [];
            this.total = '';
            this.warehouse_type_name = '';

            this.articles = data.articles;
            this.total = data.total;
            this.warehouse_type_name = data.warehouse_type_name;
            $('#modalArticles').modal('show');

        }.bind(this));
    },
    methods: {

    }
};
</script>
