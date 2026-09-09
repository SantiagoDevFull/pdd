<template>
    <!--begin::Modal-->
    <div class="modal fade" id="show_liquidation_detail" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
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
                                    <th class="text-center">Tipo</th>
                                    <th class="text-center">Descripción</th>
                                    <th class="text-center">Cantidad</th>
                                    <th class="text-center">Precio</th>
                                    <th class="text-center">Importe</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(element, index) in element_details" v-bind:key="element.id">
                                    <th class="text-center" scope="row">{{ ++index }}</th>
                                    <td class="text-center">{{ element.type_article }}</td>
                                    <td class="text-center">{{ element.name }}</td>
                                    <td class="text-center">{{ element.quantity }}</td>
                                    <td class="text-center">{{ element.unit_price }}</td>
                                    <td class="text-center">{{ element.sale_value }}</td>
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
        EventBus.$on('show_liquidation_detail', function (data, client, factura, cotizacion) {
            this.element_details = [];
            this.title = '';

            this.element_details = data;
            this.title = factura + " | " + cotizacion + " | " + client;

            $('#show_liquidation_detail').modal('show');
        }.bind(this));
    },
};
</script>
