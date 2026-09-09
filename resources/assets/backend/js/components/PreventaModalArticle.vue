<template>
    <div v-show="show_article">
        <div class="row">
            <div class="col-12">
                <button type="button" class="btn btn-warning btn-lg btn-block" data-toggle="modal"
                    data-target="#modal_article">
                    <i class="fas fa-eye"></i> Ver Artículos
                </button>
            </div>
        </div>

        <div class="modal fade" id="modal_article" tabindex="-1" role="dialog" aria-labelledby="modalTitle"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
                <div class="modal-content shadow-lg rounded">

                    <div class="modal-header bg-primary">
                        <h5 class="modal-title  text-white" id="modalTitle">
                            <i class="fas fa-boxes"></i> Lista de Artículos
                        </h5>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <div class="modal-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="text-center">CODIGO</th>
                                        <th>ARTÍCULO</th>
                                        <th class="text-right">PREVENTA</th>
                                        <th class="text-right">RETORNO</th>
                                        <th class="text-right">VENTA</th>
                                        <th class="text-right">SALDO</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-if="articles.length === 0">
                                        <td colspan="6" class="text-center text-muted py-4">
                                            <i class="fas fa-info-circle"></i> No hay artículos disponibles
                                        </td>
                                    </tr>
                                    <tr v-for="article in articles" :key="article.article_id">
                                        <td class="text-center font-weight-bold">{{ article.article_id }}</td>
                                        <td>{{ article.article_name }}</td>
                                        <td class="text-right">{{ formatDecimal(article.preventa) }}</td>
                                        <td class="text-right text-danger">{{ formatDecimal(article.retorno) }}</td>
                                        <td class="text-right text-success">{{ formatDecimal(article.venta) }}</td>
                                        <td class="text-right font-weight-bold">{{ formatDecimal(article.saldo) }}</td>
                                    </tr>
                                </tbody>
                                <tfoot v-if="articles.length > 0" class="bg-light font-weight-bold">
                                    <tr>
                                        <td colspan="2" class="text-right">TOTAL:</td>
                                        <td class="text-right">{{ formatDecimal(totalPreventa) }}</td>
                                        <td class="text-right">{{ formatDecimal(totalRetorno) }}</td>
                                        <td class="text-right">{{ formatDecimal(totalVenta) }}</td>
                                        <td class="text-right">{{ formatDecimal(totalSaldo) }}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>

                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                            <i class="fas fa-times"></i> Cerrar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import EventBus from '../event-bus';

export default {
    data() {
        return {
            articles: [],
            show_article: false
        }
    },
    created() {
        EventBus.$on('preventa_modal_article', (articles) => {
            this.articles = articles;
            this.show_article = true;
        });
    },
    computed: {
        totalPreventa() {
            return this.articles.reduce((sum, a) => sum + parseFloat(a.preventa || 0), 0);
        },
        totalRetorno() {
            return this.articles.reduce((sum, a) => sum + parseFloat(a.retorno || 0), 0);
        },
        totalVenta() {
            return this.articles.reduce((sum, a) => sum + parseFloat(a.venta || 0), 0);
        },
        totalSaldo() {
            return this.articles.reduce((sum, a) => sum + parseFloat(a.saldo || 0), 0);
        }
    },
    methods: {
        formatDecimal(value) {
            if (value === null || value === undefined) return '0.00';
            return parseFloat(value).toFixed(2);
        }
    }
};
</script>

<style scoped>
.table thead th {
    font-size: 14px;
    text-transform: uppercase;
}

.modal-header {
    border-bottom: 2px solid #007bff;
}

.modal-footer {
    border-top: 1px solid #dee2e6;
}
</style>
