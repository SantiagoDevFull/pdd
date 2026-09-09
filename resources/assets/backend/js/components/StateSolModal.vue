<template>
    <div class="modal fade" id="modal-state" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
            <div class="modal-content shadow-lg">

                <div class="modal-header bg-dark">
                    <h5 class="modal-title text-white">{{ model.num_mov || '---' }}</h5>
                    <button type="button" class="close text-white" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>

                <div class="modal-body">

                    <div class="table-responsive mb-4">
                        <table class="table table-bordered table-striped align-middle">
                            <thead class="bg-info text-white text-center">
                                <tr>
                                    <th colspan="2" class="py-3 text-uppercase">
                                        Información del Requerimiento
                                    </th>
                                </tr>
                            </thead>

                            <tbody>
                                <tr>
                                    <th class="w-30 text-uppercase text-muted">Compañía</th>
                                    <td class="fw-bold">{{ model.company_name }}</td>
                                </tr>
                                <tr>
                                    <th class="text-uppercase text-muted">Área</th>
                                    <td class="fw-bold">{{ model.area_all_name }}</td>
                                </tr>
                                <tr>
                                    <th class="text-uppercase text-muted">Condición de Pago</th>
                                    <td class="fw-bold bg-danger text-white">{{ model.adm_payment_name }}</td>
                                </tr>
                                <tr>
                                    <th class="text-uppercase text-muted">Tipo</th>
                                    <td class="fw-bold">{{ model.type_name }}</td>
                                </tr>
                                <tr>
                                    <th class="text-uppercase text-muted">Sub Tipo</th>
                                    <td class="fw-bold">{{ model.sub_type_name }}</td>
                                </tr>
                                <tr>
                                    <th class="text-uppercase text-muted">Tipo de Requerimiento</th>
                                    <td class="fw-bold">{{ model.type_req_name }}</td>
                                </tr>
                                <tr>
                                    <th class="text-uppercase text-muted">Presupuesto Actual</th>
                                    <td class="fw-bold">{{ model.presupuesto }}</td>
                                </tr>
                                <tr>
                                    <th class="text-uppercase text-muted">N° Movimiento</th>
                                    <td class="fw-bold">{{ model.num_mov }}</td>
                                </tr>
                                <tr>
                                    <th class="text-uppercase text-muted">Importe</th>
                                    <td class="fw-bold">{{ model.import }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="table-responsive mb-4">
                        <table class="table table-bordered table-hover table-striped align-middle">

                            <thead class="bg-success text-white text-center">
                                <tr>
                                    <th colspan="13" class="py-3 text-uppercase">Facturas</th>
                                </tr>

                                <tr class="bg-light text-dark">
                                    <th>N° Comp.</th>
                                    <th>Principal</th>
                                    <th>Secundario</th>
                                    <th>Tipo Doc.</th>
                                    <th>F. Emisión</th>
                                    <th>F. Venc.</th>
                                    <th>RUC</th>
                                    <th>Razón Social</th>
                                    <th>Moneda</th>
                                    <th>Importe</th>
                                    <th>Descripción</th>
                                    <th>Archivo</th>
                                    <th>Opciones</th>
                                </tr>
                            </thead>

                            <tbody>
                                <tr v-for="(detail, index) in details" :key="index">
                                    <td>{{ detail.num_comprobante }}</td>
                                    <td>{{ detail.costo_pri_name }}</td>
                                    <td>{{ detail.costo_sec_name }}</td>
                                    <td>{{ detail.voucher_type_name }}</td>
                                    <td>{{ detail.issue_date }}</td>
                                    <td>{{ detail.expiry_date }}</td>
                                    <td>{{ detail.document_number }}</td>
                                    <td>{{ detail.razon_social }}</td>
                                    <td>{{ detail.currency_name }}</td>
                                    <td>{{ detail.import }}</td>
                                    <td>{{ detail.description }}</td>
                                    <td>
                                        <a v-if="detail.ruta_archivo" :href="detail.ruta_archivo" target="_blank"
                                            class="btn btn-info btn-sm">
                                            <i class="fa fa-download"></i> Descargar
                                        </a>
                                    </td>
                                    <td>
                                        <a href="#" class="btn btn-success btn-sm" v-if="model.state_id == 1 || model.state_id == 5"
                                            @click.prevent="contabilidad(detail)">
                                            <i class="fa fa-trash"></i> REGISTRO CONTABILIDAD
                                        </a>

                                    </td>
                                </tr>

                                <tr v-if="details.length === 0">
                                    <td colspan="13" class="text-center text-muted">
                                        No hay registros disponibles
                                    </td>
                                </tr>
                            </tbody>

                        </table>
                    </div>

                    <div v-for="(detail, idx) in details" :key="'plates-' + idx" class="mb-5">

                        <h5 class="text-danger mb-2">
                            Detalle de Factura: {{ detail.num_comprobante }}
                        </h5>

                        <table class="table table-bordered table-striped align-middle">
                            <thead class="bg-danger text-white text-center">
                                <tr>
                                    <th colspan="2" class="py-3 text-uppercase">
                                        Cliente y Placa
                                    </th>
                                </tr>
                                <tr class="bg-light text-dark">
                                    <th>Cliente</th>
                                    <th>Placa</th>
                                </tr>
                            </thead>

                            <tbody>
                                <tr v-for="(p, i) in detail.plates" :key="i">
                                    <td>{{ p.client }}</td>
                                    <td>{{ p.plate }}</td>
                                </tr>

                                <tr v-if="detail.plates.length === 0">
                                    <td colspan="2" class="text-center text-muted">
                                        Sin placas asociadas
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
</template>
<script>import EventBus from '../event-bus';
export default {
    props: {
        url_verify_register_cont: {
            type: String,
            default: ''
        }
    },
    data() {
        return {
            model: {},
            details: [],
            plates: [],
        };
    },
    created() {
        EventBus.$on('edit_modal', (data) => {
            this.model = data.element;
            this.details = data.details;
            this.plates = data.plates;
            $('#modal-state').modal('show');
        });
    },
    methods: {
        contabilidad(detail) {

            EventBus.$emit('loading', true);
            axios.post(this.url_verify_register_cont, {
                id: detail.id,
            }).then(response => {
                EventBus.$emit('loading', false);

                if (response.data) {
                    Swal.fire({
                        type: 'warning',
                        title: 'Ya registrado',
                        text: 'Este comprobante ya fue contabilizado.',
                    });
                    return;
                }

                EventBus.$emit('modal_contabilidad', detail.id, detail.import, detail.voucher_type_name, detail.num_comprobante, this.model.adm_payment_name);

            }).catch(error => {
                EventBus.$emit('loading', false);
                console.log(error);
                console.log(error.response);
            });


        },
    },
}; </script>
