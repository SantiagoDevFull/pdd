<template>
    <!--begin::Modal-->
    <div class="modal fade" id="show_sale_detail" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-primary text-center" id="exampleModalLabel">Lista de Artículos</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    </button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th class="text-center">#</th>
                                    <th class="text-center">Articulo</th>
                                    <th class="text-center">Cantidad</th>
                                    <th class="text-center">Precio</th>
                                    <th class="text-center">Valor venta</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(element, index) in sale_details" v-bind:key="element.detail_id">
                                    <th class="text-center" scope="row">{{ ++index }}</th>
                                    <td class="text-center">{{ element.article_name }}</td>
                                    <td class="text-center">{{ element.sale_detail_quantity }}</td>
                                    <td class="text-center">{{ element.sale_detail_price_igv }}</td>
                                    <td class="text-center">{{ element.sale_detail_total_perception }}</td>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th class="text-center">TOTAL</th>
                                    <th class="text-center">{{ totalPerception }}</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
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
            sale_details: [],

        }
    },
    computed: {
        totalPerception() {
            return this.sale_details.reduce((acc, item) => {
                return acc + Number(item.sale_detail_total_perception || 0);
            }, 0).toFixed(2);
        }
    },
    created() {
        EventBus.$on('show_sale_detail', function (data) {
            this.sale_details = [];
            this.sale_details = data;

            $('#show_sale_detail').modal('show');
        }.bind(this));
    },
};
</script>
