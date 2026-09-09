<template>
    <!--begin::Modal-->
    <div class="modal fade" id="show_movement_detail" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-primary text-center" id="exampleModalLabel">{{ title }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    </button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th class="text-center">#</th>
                                    <th class="text-center">Artículo</th>
                                    <th class="text-center">Cantidad Digita</th>
                                    <th class="text-center">Cantidad Convertida</th>
                                    <th class="text-center">Precio Unitario</th>
                                    <th class="text-center">Valor Venta</th>
                                    <th class="text-center">IGV</th>
                                    <th class="text-center">Total</th>
                                    <th class="text-center">Placa</th>

                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(element, index) in element_details" v-bind:key="element.id">
                                    <th class="text-center" scope="row">{{ ++index }}</th>
                                    <td class="text-center">{{ element.article_name }}</td>
                                    <td class="text-center">{{ element.digit_amount }}</td>
                                    <td class="text-center">{{ element.converted_amount }}</td>
                                    <td class="text-center">{{ element.price }}</td>
                                    <td class="text-center">{{ element.sale_value }}</td>
                                    <td class="text-center">{{ element.igv }}</td>
                                    <td class="text-center">{{ element.total }}</td>
                                    <td class="text-center">{{ element.plate }}</td>
                                </tr>
                            </tbody>
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
            title: '',
            element_details: [],

        }
    },
    computed: {
    },
    created() {
        EventBus.$on('show_cochera_movement_detail', function (data, factura, provider_name) {
            this.element_details = [];
            this.title = '';
            this.element_details = data;
            this.title = factura + "  |  " + provider_name;

            $('#show_movement_detail').modal('show');
        }.bind(this));
    },
};
</script>
