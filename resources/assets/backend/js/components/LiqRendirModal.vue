<template>
    <!--begin::Modal-->
    <div class="modal fade" id="modal_register" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <!--begin::Form-->

                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Nueva Solicitud</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    </button>
                </div>
                <div class="modal-body">
                    <div class="kt-portlet__body">
                        <div class="row">
                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label class="form-control-label">Principal:</label>
                                    <select class="form-control" name="principal" id="principal"
                                        v-model="model.principal" @focus="$parent.clearErrorMsg($event)">
                                        <option value="" selected disabled>Seleccionar</option>
                                        <option v-for="principal in costs" :value="principal.id"
                                            v-bind:key="principal.id">
                                            {{ principal.name }}</option>
                                    </select>
                                    <div id="principal-error" class="error invalid-feedback"></div>
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label class="form-control-label">Secundario:</label>
                                    <select class="form-control" name="secundario" id="secundario"
                                        v-model="model.secundario" @focus="$parent.clearErrorMsg($event)">
                                        <option value="" selected disabled>Seleccionar</option>
                                        <option v-for="secundario in costs_2" :value="secundario.id"
                                            v-bind:key="secundario.id">
                                            {{ secundario.name }}</option>
                                    </select>
                                    <div id="secundario-error" class="error invalid-feedback"></div>
                                </div>
                            </div>
                            <div class="col-lg-12 text-danger">
                                <hr class="border border-danger">
                            </div>
                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label class="form-control-label">Tipo de Documento:</label>
                                    <select class="form-control" name="voucher_type_id" id="voucher_type_id"
                                        v-model="model.voucher_type_id" @focus="$parent.clearErrorMsg($event)">
                                        <option value="" selected disabled>Seleccionar</option>
                                        <option v-for="voucher_type in voucher_types" :value="voucher_type.id"
                                            v-bind:key="voucher_type.id">{{
                                                voucher_type.name }}</option>
                                    </select>
                                    <div id="voucher_type_id-error" class="error invalid-feedback"></div>
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label class="form-control-label">Fecha Emisión:</label>
                                    <datetime v-model="model.issue_date" placeholder="Selecciona una Fecha"
                                        :format="'dd-LL-yyyy'" name="issue_date" value-zone="America/Lima"
                                        zone="America/Lima" :min-datetime="current_date" class="form-control"
                                        @focus="$parent.clearErrorMsg($event)">
                                    </datetime>
                                    <div id="issue_date-error" class="error invalid-feedback"></div>
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label class="form-control-label">Fecha Vencimiento:</label>
                                    <datetime v-model="model.expiry_date" placeholder="Selecciona una Fecha"
                                        :format="'dd-LL-yyyy'" name="expiry_date" value-zone="America/Lima"
                                        zone="America/Lima" :min-datetime="current_date" class="form-control"
                                        @focus="$parent.clearErrorMsg($event)">
                                    </datetime>
                                    <div id="expiry_date-error" class="error invalid-feedback"></div>
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label class="form-control-label">N° de Comprobante:</label>
                                    <input type="text" class="form-control" name="num_comprobante" id="num_comprobante"
                                        v-model="model.num_comprobante" @focus="$parent.clearErrorMsg($event)">
                                    <div id="num_comprobante-error" class="error invalid-feedback"></div>
                                </div>
                            </div>

                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label class="form-control-label">RUC:</label>
                                    <input type="text" class="form-control" name="document_number" id="document_number"
                                        v-model="model.document_number" @focus="$parent.clearErrorMsg($event)">

                                    <div id="document_number-error" class="error invalid-feedback"></div>
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label class="form-control-label">Razón Social:</label>
                                    <input type="text" class="form-control" name="razon_social" id="razon_social"
                                        v-model="model.razon_social" @focus="$parent.clearErrorMsg($event)">

                                    <div id="razon_social-error" class="error invalid-feedback"></div>
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label class="form-control-label">Moneda:</label>
                                    <select class="form-control" name="currency_id" id="currency_id"
                                        v-model="model.currency_id" @focus="$parent.clearErrorMsg($event)">
                                        <option value="" selected disabled>Seleccionar</option>
                                        <option v-for="currency in currencies" :value="currency.id"
                                            v-bind:key="currency.id">{{
                                                currency.name }}</option>
                                    </select>
                                    <div id="currency_id-error" class="error invalid-feedback"></div>
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label class="form-control-label">Importe:</label>
                                    <input type="text" class="form-control" name="import" id="import" placeholder="0"
                                        @input="model.import = model.import.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1')"
                                        step="any" min="0.00" v-model="model.import"
                                        @focus="$parent.clearErrorMsg($event)">
                                    <div id="import-error" class="error invalid-feedback"></div>
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label class="form-control-label">Descripción:</label>
                                    <textarea class="form-control" name="description" id="description"
                                        v-model="model.description" @focus="$parent.clearErrorMsg($event)"></textarea>
                                    <div id="description-error" class="error invalid-feedback"></div>
                                </div>
                            </div>
                            <div class="col-lg-12 text-danger">
                                <hr class="border border-danger">
                            </div>
                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label class="form-control-label">Cliente:</label>
                                    <input type="text" class="form-control" name="client" id="client" v-model="client"
                                        @focus="$parent.clearErrorMsg($event)">
                                    <div id="client-error" class="error invalid-feedback"></div>
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label class="form-control-label">Placa:</label>
                                    <input type="text" class="form-control" name="plate" id="plate"
                                        placeholder="123-456" v-model="plate" @focus="$parent.clearErrorMsg($event)">
                                    <div id="plate-error" class="error invalid-feedback"></div>
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label for="">&nbsp;</label>
                                    <button type="button" class="btn btn-success" @click="addItem">Agregar</button>
                                </div>
                            </div>

                            <div class="col-lg-12">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>N°</th>
                                            <th>Cliente</th>
                                            <th>Placa</th>
                                            <th>Opciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-if="this.model.items.length == 0">
                                            <td colspan="4" class="text-center text-danger font-weight-bold">No hay
                                                registros
                                            </td>
                                        </tr>
                                        <tr v-for="(item, index) in this.model.items" :key="index">
                                            <td>{{ index + 1 }}</td>
                                            <td>
                                                {{ item.client }}
                                            </td>
                                            <td>
                                                {{ item.plate }}
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-danger btn-sm"
                                                    @click="removeItem(index)">
                                                    Eliminar
                                                </button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label class="form-control-label">Subir Comprobante:</label>
                                    <input type="file" class="form-control" name="comprobante" id="comprobante"
                                        accept=".pdf, image/*, .xlsx, .xls" @focus="$parent.clearErrorMsg($event)"
                                        @change="showFilePreview">
                                    <div id="comprobante-error" class="error invalid-feedback"></div>

                                    <div v-if="filePreview" class="mt-3">
                                        <embed v-if="isPDF" :src="filePreview" type="application/pdf" width="100%"
                                            height="500px" />

                                        <img v-else-if="isImage" :src="filePreview" alt="Vista previa del comprobante"
                                            class="img-thumbnail" style="max-width: 100%; max-height: 500px;">

                                        <p v-else>
                                            Archivo seleccionado: <b>{{ selectedFileName }}</b> (no disponible vista
                                            previa)
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success" @click.prevent="addVoucher">
                            Agregar
                        </button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                    </div>
                </div>
                <!--end::Form-->
            </div>
        </div>
    </div>
    <!--end::Modal-->
</template>

<script>
import EventBus from '../event-bus';
import { DateTime } from "luxon";

export default {
    props: {
        url_get_costo_secs: {
            type: String,
            default: ''
        },
        currencies: {
            type: Array,
            default: ''
        },
        voucher_types: {
            type: Array,
            default: ''
        },
        current_date: {
            type: String,
            default: ''
        }
    },
    data() {
        return {
            model: {
                principal: '',
                principal_name: '',
                secundario: '',
                secundario_name: '',
                voucher_type_id: '',
                voucher_type_name: '',
                issue_date: '',
                expiry_date: '',
                num_comprobante: '',
                document_number: '',
                razon_social: '',
                currency_id: '',
                currency_name: '',
                import: '',
                description: '',
                comprobante: '',
                items: [],
            },

            client: '',
            plate: '',

            costs: [],
            costs_2: [],

            filePreview: null,
            isImage: false,
            isPDF: false,
        }
    },
    watch: {
        'model.issue_date'(val) {
            if (val) {
                this.model.issue_date = DateTime.fromISO(val).toFormat('yyyy-LL-dd');
            }
        },
        'model.expiry_date'(val) {
            if (val) {
                this.model.expiry_date = DateTime.fromISO(val).toFormat('yyyy-LL-dd');
            }
        },
        'model.principal': function (val) {
            if (val !== '') {

                EventBus.$emit('loading', true);

                axios.post(this.url_get_costo_secs, {
                    costo_pri_id: val
                }).then(response => {
                    EventBus.$emit('loading', false);
                    this.costs_2 = [];
                    this.costs_2 = response.data;
                }).catch(error => {
                    EventBus.$emit('loading', false);
                    console.log(error);
                    console.log(error.response)
                });

            } else {
                this.model.secundario = '';
            }
        },
    },
    computed: {

    },
    created() {

        EventBus.$on('list_cots', function (data) {

            this.costs = [];
            this.costs_2 = [];

            this.costs = data;

        }.bind(this));

        EventBus.$on('create_modal', function (id) {

            EventBus.$emit('clear_modal');
            this.model.aplication_id = id;
            $('#modal_register').modal('show');

        }.bind(this));

        EventBus.$on('clear_modal', function () {

            this.model = {
                principal: '',
                secundario: '',
                voucher_type_id: '',
                issue_date: '',
                expiry_date: '',
                num_comprobante: '',
                document_number: '',
                razon_social: '',
                currency_id: '',
                import: '',
                description: '',
                comprobante: '',
                items: [],
            };

            this.client = '';
            this.plate = '';

            const fileInput = document.getElementById('comprobante');
            if (fileInput) {
                fileInput.value = '';
            }

            this.filePreview = null;
            this.isImage = false;
            this.isPDF = false;

        }.bind(this));

    },
    mounted() {

    },
    methods: {
        addItem() {

            if (this.client == '' || this.client == null || this.client.trim().length == 0) {
                Swal.fire({
                    type: 'error',
                    title: 'Error',
                    text: 'El campo cliente es obligatorio.'
                })
                return;
            }

            if (this.plate == '' || this.plate == null || this.plate.trim().length == 0) {
                Swal.fire({
                    type: 'error',
                    title: 'Error',
                    text: 'El campo placa es obligatorio.'
                })
                return;
            }

            this.model.items.push({
                client: this.client,
                plate: this.plate
            });

            this.client = '';
            this.plate = '';

        },
        removeItem(index) {
            this.model.items.splice(index, 1);
        },
        addVoucher() {

            const p = this.costs.find(x => x.id == this.model.principal);
            this.model.principal_name = p ? p.name : '';

            const s = this.costs_2.find(x => x.id == this.model.secundario);
            this.model.secundario_name = s ? s.name : '';

            const v = this.voucher_types.find(x => x.id == this.model.voucher_type_id);
            this.model.voucher_type_name = v ? v.name : '';

            const c = this.currencies.find(x => x.id == this.model.currency_id);
            this.model.currency_name = c ? c.name : '';


            EventBus.$emit('push_voucher', this.model);
            $('#modal_register').modal('hide');
        },
        showFilePreview(event) {
            const file = event.target.files[0];
            if (!file) return;

            this.model.comprobante = file;

            const fileType = file.type;
            this.isImage = fileType.startsWith('image/');
            this.isPDF = fileType === 'application/pdf';

            this.filePreview = URL.createObjectURL(file);
        }
    }
};
</script>