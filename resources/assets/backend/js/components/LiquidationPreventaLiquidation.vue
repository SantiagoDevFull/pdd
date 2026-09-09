<template>
    <!--begin::Modal-->
    <div class="modal fade" id="show_liquidation_detail" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-primary text-center" id="exampleModalLabel">Detalle de la Liquidación
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    </button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th class="text-center">#</th>
                                    <th class="text-center">Condición de Pago</th>
                                    <th class="text-center">Forma de pago</th>
                                    <th class="text-center">Moneda</th>
                                    <th class="text-center">Banco</th>
                                    <th class="text-center">N° Operación</th>
                                    <th class="text-center">Fecha de Emisión</th>
                                    <th class="text-center">Monto</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(element, index) in liquidation_details" v-bind:key="element.liquidation_id">
                                    <th class="text-center" scope="row">{{ ++index }}</th>
                                    <td class="text-center">{{ element.payment_name }}</td>
                                    <td class="text-center">{{ element.payment_method_name }}</td>
                                    <td class="text-center">{{ element.currency_name }}</td>
                                    <td class="text-center">{{ element.bank_account_name }}</td>
                                    <td class="text-center">{{ element.liq_operation_number }}</td>
                                    <td class="text-center">{{ element.liquidation_date }}</td>
                                    <td class="text-center">{{ element.liquidation_amount }}</td>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th></th>
                                    <th></th>
                                    <th></th>
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
            liquidation_details: [],

        }
    },
    computed: {
        totalPerception() {
            return this.liquidation_details.reduce((acc, item) => {
                return acc + Number(item.liquidation_amount || 0);
            }, 0).toFixed(2);
        }
    },
    created() {
        EventBus.$on('show_liquidation_detail', function (data) {
            this.liquidation_details = [];
            this.liquidation_details = data;

            $('#show_liquidation_detail').modal('show');
        }.bind(this));
    },
};
</script>
