<template>
    <!--begin::Modal-->
    <div class="modal fade" id="show_order_detail" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
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
                                    <th colspan="8" class="text-center text-danger">{{ type }} - {{ reference_id == 1 ? 'PROFORMA' : 'COTIZACIÓN' }}</th>
                                </tr>
                                <tr>
                                    <th class="text-center">#</th>
                                    <th class="text-center">Almacén</th>
                                    <th class="text-center">Artículo</th>
                                    <th class="text-center">Cantidad</th>
                                    <th class="text-center">Medida</th>
                                    <th class="text-center">Precio</th>
                                    <th class="text-center">Valor venta</th>
                                    <th class="text-center">Valor en S/.</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(element, index) in element_details" v-bind:key="element.detail_id">
                                    <th class="text-center" scope="row">{{ ++index }}</th>
                                    <td class="text-center">{{ element.warehouse_type_name }}</td>
                                    <td class="text-center">{{ element.article_name }}</td>
                                    <td class="text-center">{{ element.detail_quantity }}</td>
                                    <td class="text-center">{{ element.detail_unit }}</td>
                                    <td class="text-right">{{ element.detail_price }}</td>
                                    <td class="text-right">{{ element.detail_import }}</td>
                                    <td class="text-right">{{ element.detail_converted }}</td>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th></th>
                                    <th>MONEDA</th>
                                    <th >{{ currency_name }}</th>
                                    <th></th>
                                    <th></th>
                                    <th>SUBTOTAL</th>
                                    <th class="text-right">{{ subtotal }}</th>
                                    <th></th>
                                </tr>
                                <tr>
                                    <th></th>
                                    <th>CAMBIO</th>
                                    <th>{{ orders_tc }}</th>
                                    <th></th>
                                    <th></th>
                                    <th>IGV 18%</th>
                                    <th class="text-right">{{ igv }}</th>
                                    <th></th>
                                </tr>
                                <tr>
                                    <th></th>
                                    <th>N° ORDEN</th>
                                    <th>{{ orders_id }}</th>
                                    <th></th>
                                    <th></th>
                                    <th>NETO</th>
                                    <th class="text-right">{{ neto }}</th>
                                    <th></th>
                                </tr>
                                <tr>
                                    <td colspan="8">{{ type_pay }}</td>
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
            title: '',
            element_details: [],
            subtotal:'',
            igv:'',
            neto:'',
            currency_name:'',
            orders_tc:'',
            orders_id:'',
            type:'',
            type_pay:'',
            reference_id:'',

        }
    },
    computed: {
    },
    created() {
        EventBus.$on('show_order_detail', function (data) {
            this.element_details = [];
            this.title='';
            this.subtotal='';
            this.igv='';
            this.neto='';
            this.currency_name='';
            this.orders_tc='';
            this.orders_id='';
            this.type='',
            this.type_pay='',
            this.reference_id='',

            this.element_details = data.detail;
            this.title=data.order.title;
            this.type=data.order.type;
            this.subtotal=data.order.subtotal;
            this.igv=data.order.igv;
            this.neto=data.order.neto;
            this.currency_name=data.order.currency_name;
            this.orders_tc=data.order.orders_tc;
            this.orders_id=data.order.orders_id;
            this.type_pay=data.order.type_pay;
            this.reference_id=data.order.reference_id;

            $('#show_order_detail').modal('show');
        }.bind(this));
    },
};
</script>
