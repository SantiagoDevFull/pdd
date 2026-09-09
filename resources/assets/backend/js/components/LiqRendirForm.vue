<template>
    <!--begin::Portlet-->
    <div class="kt-portlet">
        <div class="kt-portlet__head">
            <div class="kt-portlet__head-label">
                <h3 class="kt-portlet__head-title">
                    Buscar
                </h3>
            </div>
        </div>

        <!--begin::Form-->
        <form class="kt-form" @submit.prevent="formController(url, $event)">
            <div class="kt-portlet__body">
                <div class="row">
                    <div class="col-lg-3">
                        <div class="form-group">
                            <label class="form-control-label">N° Movimiento:</label>
                            <select class="form-control" name="id" id="id" v-model="model.id"
                                @focus="$parent.clearErrorMsg($event)">
                                <option value="" selected disabled>Seleccionar</option>
                                <option v-for="num_mov in num_movs" :value="num_mov.id" v-bind:key="num_mov.id">{{
                                    num_mov.num_mov }}</option>
                            </select> {{ model.num_mov
                            }}
                            <div id="id-error" class="error invalid-feedback"></div>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="form-group">
                            <label class="form-control-label">Compañía:</label>
                            <select class="form-control" name="company_id" id="company_id" v-model="model.company_id"
                                disabled @focus="$parent.clearErrorMsg($event)">
                                <option value="" selected disabled>Seleccionar</option>
                                <option v-for="company in companies" :value="company.id" v-bind:key="company.id">{{
                                    company.name }}</option>
                            </select>
                            <div id="company_id-error" class="error invalid-feedback"></div>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="form-group">
                            <label class="form-control-label">Area:</label>
                            <select class="form-control" name="area_all_id" id="area_all_id" v-model="model.area_all_id"
                                disabled @focus="$parent.clearErrorMsg($event)">
                                <option value="" selected disabled>Seleccionar</option>
                                <option v-for="area_all in area_alls" :value="area_all.id" v-bind:key="area_all.id">{{
                                    area_all.name }}</option>
                            </select>
                            <div id="area_all_id-error" class="error invalid-feedback"></div>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="form-group">
                            <label class="form-control-label">Condición de Pago:</label>
                            <select class="form-control" name="adm_payment_id" id="adm_payment_id" disabled
                                v-model="model.adm_payment_id" @focus="$parent.clearErrorMsg($event)">
                                <option value="" selected disabled>Seleccionar</option>
                                <option v-for="payment in adm_payments" :value="payment.id" v-bind:key="payment.id">{{
                                    payment.name }}</option>
                            </select>
                            <div id="adm_payment_id-error" class="error invalid-feedback"></div>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="form-group">
                            <label class="form-control-label">Tipo:</label>
                            <select class="form-control" name="type_id" id="type_id" v-model="model.type_id" disabled
                                @focus="$parent.clearErrorMsg($event)">
                                <option value="" selected disabled>Seleccionar</option>
                                <option v-for="type in types" :value="type.id" v-bind:key="type.id">{{
                                    type.name }}</option>
                            </select>
                            <div id="type_id-error" class="error invalid-feedback"></div>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="form-group">
                            <label class="form-control-label">Sub Tipo:</label>
                            <select class="form-control" name="sub_type_id" id="sub_type_id" v-model="model.sub_type_id"
                                disabled @focus="$parent.clearErrorMsg($event)">
                                <option value="" selected disabled>Seleccionar</option>
                                <option v-for="sub_type in sub_types" :value="sub_type.id" v-bind:key="sub_type.id">{{
                                    sub_type.name }}</option>
                            </select>
                            <div id="sub_type_id-error" class="error invalid-feedback"></div>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="form-group">
                            <label class="form-control-label">Tipo de Requerimiento:</label>
                            <select class="form-control" name="type_req_id" id="type_req_id" v-model="model.type_req_id"
                                disabled @focus="$parent.clearErrorMsg($event)">
                                <option value="" selected disabled>Seleccionar</option>
                                <option v-for="type_req in type_reqs" :value="type_req.id" v-bind:key="type_req.id">{{
                                    type_req.name }}</option>
                            </select>
                            <div id="type_req_id-error" class="error invalid-feedback"></div>
                        </div>
                    </div>

                    <div class="col-lg-3" v-if="this.model.type_req_id == 5">
                        <div class="form-group">
                            <label class="form-control-label">Presupuesto actual:</label>
                            <input type="text" class="form-control" name="presupuesto" id="presupuesto" disabled
                                placeholder="0.00" readonly step="any" v-model="model.presupuesto"
                                @focus="$parent.clearErrorMsg($event)">
                            <div id="presupuesto-error" class="error invalid-feedback"></div>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="form-group">
                            <label class="form-control-label">Importe:</label>
                            <input type="text" class="form-control" name="import" id="import" v-model="model.import"
                                disabled @focus="$parent.clearErrorMsg($event)">
                            <div id="import-error" class="error invalid-feedback"></div>
                        </div>
                    </div>
                    <div class="col-lg-12 text-danger">
                        <button class="btn btn-primary col-12" type="button" @click="addVoucher">Agregar Comprobantes
                            (Max 5)</button>
                    </div>
                    <div class="col-lg-12">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>N°</th>
                                    <th>Principal</th>
                                    <th>Secundario</th>
                                    <th>Tipo de Doc.</th>
                                    <th>Fecha Emisión</th>
                                    <th>Fecha Vencimiento</th>
                                    <th>Número de Comprobante</th>
                                    <th>RUC</th>
                                    <th>Razón Social</th>
                                    <th>Moneda</th>
                                    <th>Importe</th>
                                    <th>Descripción</th>
                                    <th>Placa</th>
                                    <th>Archivo</th>
                                    <th>Opciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-if="facturas.length == 0">
                                    <td colspan="15" class="text-center text-danger font-weight-bold">No hay facturas
                                        registradas</td>
                                </tr>
                                <tr v-for="(item, index) in facturas" :key="index">
                                    <td>{{ index + 1 }}</td>
                                    <td>{{ item.principal_name }}</td>
                                    <td>{{ item.secundario_name }}</td>
                                    <td>{{ item.voucher_type_name }}</td>
                                    <td>{{ item.issue_date }}</td>
                                    <td>{{ item.expiry_date }}</td>
                                    <td>{{ item.num_comprobante }}</td>
                                    <td>{{ item.document_number }}</td>
                                    <td>{{ item.razon_social }}</td>
                                    <td>{{ item.currency_name }}</td>
                                    <td>{{ item.import }}</td>
                                    <td>{{ item.description }}</td>
                                    <td>{{ item.plate }}</td>
                                    <td>
                                        <span v-if="item.comprobante">Si</span>
                                        <span v-else class="text-danger">No</span>
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-info btn-sm" @click="showItems(item)">
                                            Ver Items
                                        </button>
                                        <button type="button" class="btn btn-danger btn-sm"
                                            @click="removeVoucher(index)">
                                            Eliminar
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="kt-portlet__foot">
                <div class="kt-form__actions">
                    <div class="row">
                        <div class="col-12 kt-align-right">
                            <button type="submit" class="btn btn-primary">Registrar</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
        <!--end::Form-->
        <div class="modal fade" id="modalItems" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">

                    <div class="modal-header">
                        <h5 class="modal-title">Items de la Factura</h5>
                        <button type="button" class="close" data-dismiss="modal">
                            <span>&times;</span>
                        </button>
                    </div>

                    <div class="modal-body">
                        <table class="table table-bordered table-striped" v-if="itemsModal.length > 0">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Cliente</th>
                                    <th>Placa</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(it, idx) in itemsModal" :key="idx">
                                    <td>{{ idx + 1 }}</td>
                                    <td>{{ it.client }}</td>
                                    <td>{{ it.plate }}</td>
                                </tr>
                            </tbody>
                        </table>

                        <div v-else class="text-center text-danger font-weight-bold">
                            No hay items registrados
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                    </div>

                </div>
            </div>
        </div>
    </div>
    <!--end::Portlet-->
</template>

<script>

import Swal from 'sweetalert2';
import EventBus from '../event-bus';
import Datetime from 'vue-datetime';
import 'vue-datetime/dist/vue-datetime.css';

Vue.use(Datetime);

export default {
    props: {
        num_movs: {
            type: Array,
            default: ''
        },
        companies: {
            type: Array,
            default: ''
        },
        type_reqs: {
            type: Array,
            default: ''
        },
        adm_payments: {
            type: Array,
            default: ''
        },
        url: {
            type: String,
            default: ''
        },
        url_get_presupuesto: {
            type: String,
            default: ''
        },
        url_get_costos: {
            type: String,
            default: ''
        },
        url_get_aplication: {
            type: String,
            default: ''
        },
        area_alls: {
            type: Array,
            default: ''
        },
        types: {
            type: Array,
            default: ''
        },
        sub_types: {
            type: Array,
            default: ''
        },
    },
    data() {
        return {
            model: {
                id: '',
                company_id: '',
                area_all_id: '',
                adm_payment_id: '',
                type_id: '',
                sub_type_id: '',
                type_req_id: '',
                presupuesto: '',
                import: '',
            },
            facturas: [],

            itemsModal: [],

            filePreview: null,
            isImage: false,
            isPDF: false,
            selectedFileName: "",
        }
    },
    created() {
        EventBus.$on('push_voucher', function (data) {
            this.facturas.push(data);
        }.bind(this));
    },
    mounted() {

    },
    watch: {
        'model.type_req_id': function (val) {

            if (val == 5) {
                EventBus.$emit('loading', true);

                axios.post(this.url_get_presupuesto, {
                    area_all_id: this.model.area_all_id
                }).then(response => {
                    EventBus.$emit('loading', false);
                    if (response.data) {
                        this.model.presupuesto = response.data.monto;
                    } else {
                        this.model.type_req_id = '';
                        this.model.presupuesto = '';
                        Swal.fire({
                            type: 'error',
                            title: 'Sin presupuesto',
                            text: 'No se encontró un monto asignado para este área en el mes actual.',
                        });
                    }

                }).catch(error => {
                    EventBus.$emit('loading', false);
                    console.log(error);
                    console.log(error.response)
                });
            } else {
                this.model.presupuesto = '';
            }
        },
        'model.id': function (val) {
            if (val != '') {
                EventBus.$emit('loading', true);

                axios.post(this.url_get_aplication, {
                    id: val
                }).then(response => {

                    this.model.company_id = '';
                    this.model.area_all_id = '';
                    this.model.adm_payment_id = '';
                    this.model.type_id = '';
                    this.model.sub_type_id = '';
                    this.model.type_req_id = '';
                    this.model.presupuesto = '';
                    this.model.import = '';

                    Object.assign(this.model, response.data);
                    EventBus.$emit('loading', false);
                }).catch(error => {
                    console.log(error);
                    console.log(error.response)
                });
            }
        },
        'model.area_all_id': function (val) {
            if (val !== '') {

                EventBus.$emit('loading', true);

                axios.post(this.url_get_costos, {
                    area_all_id: val
                }).then(response => {
                    EventBus.$emit('loading', false);
                    EventBus.$emit('list_cots', response.data);
                }).catch(error => {
                    EventBus.$emit('loading', false);
                    console.log(error);
                    console.log(error.response)
                });

            }
        }

    },
    computed: {

    },
    methods: {
        showItems(item) {
            this.itemsModal = item.items || [];
            $("#modalItems").modal("show");
        },
        addVoucher() {

            if (this.model.id == '') {
                Swal.fire({
                    type: 'error',
                    title: 'Error',
                    text: 'Debes seleccionar una solicitud, es obligatorio.'
                })
                return;
            }

            EventBus.$emit('create_modal');
        },
        removeVoucher(index) {
            this.facturas.splice(index, 1);
        },
        showFilePreview(event) {
            const file = event.target.files[0];
            if (!file) return;

            this.selectedFileName = file.name;

            const allowedTypes = [
                'application/pdf',
                'image/jpeg',
                'image/jpg',
                'image/png',
                'image/webp',
                'image/gif',
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'application/vnd.ms-excel',
            ];

            if (!allowedTypes.includes(file.type)) {
                Swal.fire({
                    icon: 'error',
                    title: 'Archivo no permitido',
                    text: 'Solo se permiten imágenes, PDF y Excel (.xls, .xlsx).'
                });

                event.target.value = "";
                this.filePreview = null;
                return;
            }

            this.isImage = file.type.startsWith('image/');
            this.isPDF = file.type === 'application/pdf';

            if (this.isImage || this.isPDF) {
                this.filePreview = URL.createObjectURL(file);
            } else {
                this.filePreview = null;
            }
        },
        formController: function (url, event) {

            if (this.model.adm_payment_id == 5 || this.model.adm_payment_id == 6) {
                if (this.facturas.length == 0) {
                    Swal.fire({
                        type: 'error',
                        title: 'Error',
                        text: 'Debe agregar al menos una Factura.'
                    })
                    return;
                }
            }

            var target = $(event.target);
            var url = url;
            var fd = new FormData(event.target);

            fd.append('facturas', JSON.stringify(
                this.facturas.map(f => {
                    const temp = { ...f };
                    delete temp.comprobante;
                    return temp;
                })
            ));

            this.facturas.forEach((f, i) => {
                if (f.comprobante instanceof File) {
                    fd.append(`comprobante_${i}`, f.comprobante);
                }
            });

            EventBus.$emit('loading', true);
            axios.post(url, fd, {
                headers: {
                    'Content-type': 'application/x-www-form-urlencoded',
                }
            }).then(response => {
                EventBus.$emit('loading', false);

                Swal.fire({
                    title: response.data.title,
                    text: response.data.msg,
                    type: "success",
                    heightAuto: false,
                    allowOutsideClick: true,
                    allowEscapeKey: true,
                    showConfirmButton: true
                }).then(function (result) {
                    if (result && (result.value !== undefined || result.dismiss !== undefined)) {
                        window.location = response.data.url;
                    }
                });

            }).catch(error => {
                EventBus.$emit('loading', false);
                console.log(error.response);
                var obj = error.response.data.errors;
                $('html, body').animate({ scrollTop: 0 }, 500, 'swing');

                $.each(obj, function (i, item) {
                    let c_target = target.find("#" + i.replace(/\./g, "\\.") + "-error");
                    if (!c_target.attr('data-required')) {
                        let p = c_target.prev();
                        p.addClass('is-invalid');
                    } else {
                        c_target.css('display', 'block');
                    }
                    c_target.html(item);
                });
            });
        },

    }
};
</script>