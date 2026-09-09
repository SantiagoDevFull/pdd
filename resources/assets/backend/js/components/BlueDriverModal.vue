<template>
    <!--begin::Modal-->
    <div class="modal fade" id="modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <!--begin::Form-->
                <form class="kt-form" @submit.prevent="formController(url_update, $event)">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Actualizar Conductor {{
                            this.model.document_number }}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="kt-portlet__body">
                            <div class="row">
                                <input type="hidden" name="id" id="id" v-model="model.id">

                                <div class="col-lg-3">
                                    <div class="form-group">
                                        <label class="form-control-label">Compañía:</label>
                                        <select class="form-control" name="company_id" id="company_id"
                                            v-model="model.company_id" @focus="$parent.clearErrorMsg($event)">
                                            <option value="" selected disabled>Seleccionar</option>
                                            <option v-for="company in companies" :value="company.id"
                                                v-bind:key="company.id">{{
                                                    company.name }}</option>
                                        </select>
                                        <div id="company_id-error" class="error invalid-feedback"></div>
                                    </div>
                                </div>


                                <div class="col-lg-3">
                                    <div class="form-group">
                                        <label class="form-control-label">Operación:</label>
                                        <select class="form-control" name="transportist_id" id="transportist_id"
                                            v-model="model.transportist_id" @focus="$parent.clearErrorMsg($event)">
                                            <option value="" selected disabled>Seleccionar</option>
                                            <option value="1">Punto de Distribución</option>
                                            <option value="2">CORDIA</option>
                                            <!--<option value="3">Global Star</option>-->
                                            <option value="4">Blue Line</option>
                                        </select>
                                        <div id="transportist_id-error" class="error invalid-feedback"></div>
                                    </div>
                                </div>


                                <div class="col-lg-3">
                                    <div class="form-group row align-items-end">

                                        <div class="col-lg-1 text-center">
                                            <label for="f_document_number_pdf_modal" class="p-2"
                                                style="cursor: pointer;">
                                                <i class="fas fa-file-upload fa-lg"></i>
                                            </label>

                                            <input type="file" id="f_document_number_pdf_modal"
                                                name="f_document_number_pdf_modal" accept="application/pdf"
                                                @change="$parent.clearErrorMsg($event)" hidden>
                                            <span class="file-check text-success d-none">✔</span>

                                            <div id="f_document_number_pdf_modal-error"
                                                class="invalid-feedback d-block">
                                            </div>
                                        </div>

                                        <div class="col-lg-1 text-center">
                                            <a :href="this.model.url_f_document_number" target="_blank" class="p-2"
                                                style="cursor: pointer;">
                                                <i class="fas fa-eye fa-lg"></i>
                                            </a>
                                        </div>

                                        <div class="col-lg-10">
                                            <label class="form-control-label">Documento:</label>

                                            <div class="d-flex align-items-center">
                                                <input type="text" class="form-control" name="document_number"
                                                    id="document_number" placeholder="12345678" :maxlength="8"
                                                    v-model="model.document_number" @input="formato"
                                                    @focus="$parent.clearErrorMsg($event)">
                                            </div>

                                            <div id="document_number-error" class="error invalid-feedback"></div>
                                        </div>


                                    </div>
                                </div>


                                <div class="col-lg-3">
                                    <div class="form-group row align-items-end">

                                        <div class="col-lg-1 text-center">
                                            <label for="f_license_a3_pdf_modal" class="p-2" style="cursor: pointer;">
                                                <i class="fas fa-file-upload fa-lg"></i>
                                            </label>

                                            <input type="file" id="f_license_a3_pdf_modal" name="f_license_a3_pdf_modal"
                                                accept="application/pdf" @change="$parent.clearErrorMsg($event)" hidden>
                                            <span class="file-check text-success d-none">✔</span>

                                            <div id="f_license_a3_pdf_modal-error" class="invalid-feedback d-block">
                                            </div>
                                        </div>

                                        <div class="col-lg-1 text-center">
                                            <a :href="this.model.url_f_license_a3" target="_blank" class="p-2"
                                                style="cursor: pointer;">
                                                <i class="fas fa-eye fa-lg"></i>
                                            </a>
                                        </div>

                                        <div class="col-lg-10">
                                            <label class="form-control-label">LICENCIA A3:</label>

                                            <div class="d-flex align-items-center">
                                                <datetime v-model="model.f_license_a3"
                                                    placeholder="Selecciona una Fecha" :format="'dd-LL-yyyy'"
                                                    input-id="f_license_a3" name="f_license_a3"
                                                    value-zone="America/Lima" zone="America/Lima" class="form-control"
                                                    :min-datetime="this.min_datetime" :max-datetime="this.max_datetime"
                                                    @focus="$parent.clearErrorMsg($event)">
                                                </datetime>

                                                <button type="button" class="btn btn-light ms-2"
                                                    @click="model.f_license_a3 = null">
                                                    ✕
                                                </button>
                                            </div>

                                            <div id="f_license_a3-error" class="error invalid-feedback"></div>
                                        </div>


                                    </div>
                                </div>

                                <div class="col-lg-3">
                                    <div class="form-group row align-items-end">

                                        <div class="col-lg-1 text-center">
                                            <label for="f_license_a4_pdf_modal" class="p-2" style="cursor: pointer;">
                                                <i class="fas fa-file-upload fa-lg"></i>
                                            </label>
                                            <input type="file" id="f_license_a4_pdf_modal" name="f_license_a4_pdf_modal"
                                                accept="application/pdf" @change="$parent.clearErrorMsg($event)" hidden>
                                            <div id="f_license_a4_pdf_modal-error"
                                                class="error invalid-feedback d-block"></div>
                                        </div>
                                        <div class="col-lg-1 text-center">
                                            <a :href="this.model.url_f_license_a4" target="_blank" class="p-2"
                                                style="cursor: pointer;">
                                                <i class="fas fa-eye fa-lg"></i>
                                            </a>
                                        </div>
                                        <div class="col-lg-11">
                                            <label class="form-control-label">LICENCIA A4:</label>

                                            <div class="d-flex align-items-center">
                                                <datetime v-model="model.f_license_a4"
                                                    placeholder="Selecciona una Fecha" :format="'dd-LL-yyyy'"
                                                    input-id="f_license_a4" name="f_license_a4"
                                                    value-zone="America/Lima" zone="America/Lima" class="form-control"
                                                    :min-datetime="this.min_datetime" :max-datetime="this.max_datetime"
                                                    @focus="$parent.clearErrorMsg($event)">
                                                </datetime>

                                                <button type="button" class="btn btn-light ms-2"
                                                    @click="model.f_license_a4 = null">
                                                    ✕
                                                </button>
                                            </div>

                                            <div id="f_license_a4-error" class="error invalid-feedback"></div>
                                        </div>

                                    </div>
                                </div>

                                <div class="col-lg-3">
                                    <div class="form-group row align-items-end">

                                        <div class="col-lg-1 text-center">
                                            <label for="f_emo_pdf_modal" class="p-2" style="cursor: pointer;">
                                                <i class="fas fa-file-upload fa-lg"></i>
                                            </label>
                                            <input type="file" id="f_emo_pdf_modal" name="f_emo_pdf_modal"
                                                accept="application/pdf" @change="$parent.clearErrorMsg($event)" hidden>
                                            <div id="f_emo_pdf_modal-error" class="error invalid-feedback d-block">
                                            </div>
                                        </div>
                                        <div class="col-lg-1 text-center">
                                            <a :href="this.model.url_f_emo" target="_blank" class="p-2"
                                                style="cursor: pointer;">
                                                <i class="fas fa-eye fa-lg"></i>
                                            </a>
                                        </div>
                                        <div class="col-lg-11">
                                            <label class="form-control-label">EMO</label>

                                            <div class="d-flex align-items-center">
                                                <datetime v-model="model.f_emo" placeholder="Selecciona una Fecha"
                                                    :format="'dd-LL-yyyy'" input-id="f_emo" name="f_emo"
                                                    value-zone="America/Lima" zone="America/Lima" class="form-control"
                                                    :min-datetime="this.min_datetime" :max-datetime="this.max_datetime"
                                                    @focus="$parent.clearErrorMsg($event)">
                                                </datetime>

                                                <button type="button" class="btn btn-light ms-2"
                                                    @click="model.f_emo = null">
                                                    ✕
                                                </button>
                                            </div>

                                            <div id="f_emo-error" class="error invalid-feedback"></div>
                                        </div>

                                    </div>
                                </div>

                                <div class="col-lg-3">
                                    <div class="form-group row align-items-end">

                                        <div class="col-lg-1 text-center">
                                            <label for="f_carnet_solgas_pdf_modal" class="p-2" style="cursor: pointer;">
                                                <i class="fas fa-file-upload fa-lg"></i>
                                            </label>
                                            <input type="file" id="f_carnet_solgas_pdf_modal"
                                                name="f_carnet_solgas_pdf_modal" accept="application/pdf"
                                                @change="$parent.clearErrorMsg($event)" hidden>
                                            <div id="f_carnet_solgas_pdf_modal-error"
                                                class="error invalid-feedback d-block"></div>
                                        </div>
                                        <div class="col-lg-1 text-center">
                                            <a :href="this.model.url_f_carnet_solgas" target="_blank" class="p-2"
                                                style="cursor: pointer;">
                                                <i class="fas fa-eye fa-lg"></i>
                                            </a>
                                        </div>
                                        <div class="col-lg-11">
                                            <label class="form-control-label">CARNET SOLGAS</label>

                                            <div class="d-flex align-items-center">
                                                <datetime v-model="model.f_carnet_solgas"
                                                    placeholder="Selecciona una Fecha" :format="'dd-LL-yyyy'"
                                                    input-id="f_carnet_solgas" name="f_carnet_solgas"
                                                    value-zone="America/Lima" zone="America/Lima" class="form-control"
                                                    :min-datetime="this.min_datetime" :max-datetime="this.max_datetime"
                                                    @focus="$parent.clearErrorMsg($event)">
                                                </datetime>

                                                <button type="button" class="btn btn-light ms-2"
                                                    @click="model.f_carnet_solgas = null">
                                                    ✕
                                                </button>
                                            </div>

                                            <div id="f_carnet_solgas-error" class="error invalid-feedback"></div>
                                        </div>

                                    </div>
                                </div>


                                <div class="col-lg-3">
                                    <div class="form-group row align-items-end">

                                        <div class="col-lg-1 text-center">
                                            <label for="f_ant_pen_pdf_modal" class="p-2" style="cursor: pointer;">
                                                <i class="fas fa-file-upload fa-lg"></i>
                                            </label>
                                            <input type="file" id="f_ant_pen_pdf_modal" name="f_ant_pen_pdf_modal"
                                                accept="application/pdf" @change="$parent.clearErrorMsg($event)" hidden>
                                            <div id="f_ant_pen_pdf_modal-error" class="error invalid-feedback d-block">
                                            </div>
                                        </div>
                                        <div class="col-lg-1 text-center">
                                            <a :href="this.model.url_f_ant_pen" target="_blank" class="p-2"
                                                style="cursor: pointer;">
                                                <i class="fas fa-eye fa-lg"></i>
                                            </a>
                                        </div>
                                        <div class="col-lg-11">
                                            <label class="form-control-label">ANTECEDENTE PENAL</label>

                                            <div class="d-flex align-items-center">
                                                <datetime v-model="model.f_ant_pen" placeholder="Selecciona una Fecha"
                                                    :format="'dd-LL-yyyy'" input-id="f_ant_pen" name="f_ant_pen"
                                                    value-zone="America/Lima" zone="America/Lima" class="form-control"
                                                    :min-datetime="this.min_datetime" :max-datetime="this.max_datetime"
                                                    @focus="$parent.clearErrorMsg($event)">
                                                </datetime>

                                                <button type="button" class="btn btn-light ms-2"
                                                    @click="model.f_ant_pen = null">
                                                    ✕
                                                </button>
                                            </div>

                                            <div id="f_ant_pen-error" class="error invalid-feedback"></div>
                                        </div>

                                    </div>
                                </div>


                                <div class="col-lg-3">
                                    <div class="form-group row align-items-end">

                                        <div class="col-lg-1 text-center">
                                            <label for="f_ant_pol_pdf_modal" class="p-2" style="cursor: pointer;">
                                                <i class="fas fa-file-upload fa-lg"></i>
                                            </label>
                                            <input type="file" id="f_ant_pol_pdf_modal" name="f_ant_pol_pdf_modal"
                                                accept="application/pdf" @change="$parent.clearErrorMsg($event)" hidden>
                                            <div id="f_ant_pol_pdf_modal-error" class="error invalid-feedback d-block">
                                            </div>
                                        </div>
                                        <div class="col-lg-1 text-center">
                                            <a :href="this.model.url_f_ant_pol" target="_blank" class="p-2"
                                                style="cursor: pointer;">
                                                <i class="fas fa-eye fa-lg"></i>
                                            </a>
                                        </div>
                                        <div class="col-lg-11">
                                            <label class="form-control-label">ANTECEDENTE POLICIAL</label>

                                            <div class="d-flex align-items-center">
                                                <datetime v-model="model.f_ant_pol" placeholder="Selecciona una Fecha"
                                                    :format="'dd-LL-yyyy'" input-id="f_ant_pol" name="f_ant_pol"
                                                    value-zone="America/Lima" zone="America/Lima" class="form-control"
                                                    :min-datetime="this.min_datetime" :max-datetime="this.max_datetime"
                                                    @focus="$parent.clearErrorMsg($event)">
                                                </datetime>

                                                <button type="button" class="btn btn-light ms-2"
                                                    @click="model.f_ant_pol = null">
                                                    ✕
                                                </button>
                                            </div>

                                            <div id="f_ant_pol-error" class="error invalid-feedback"></div>
                                        </div>

                                    </div>
                                </div>

                                <div class="col-lg-3">
                                    <div class="form-group row align-items-end">

                                        <div class="col-lg-1 text-center">
                                            <label for="curso_nor_pdf_modal" class="p-2" style="cursor: pointer;">
                                                <i class="fas fa-file-upload fa-lg"></i>
                                            </label>
                                            <input type="file" id="curso_nor_pdf_modal" name="curso_nor_pdf_modal"
                                                accept="application/pdf" @change="$parent.clearErrorMsg($event)" hidden>
                                            <div id="curso_nor_pdf_modal-error" class="error invalid-feedback d-block">
                                            </div>
                                        </div>
                                        <div class="col-lg-1 text-center">
                                            <a :href="this.model.url_f_curso_nor" target="_blank" class="p-2"
                                                style="cursor: pointer;">
                                                <i class="fas fa-eye fa-lg"></i>
                                            </a>
                                        </div>
                                        <div class="col-lg-11">
                                            <label class="form-control-label"> Curso Normativo de Transporte de
                                                Hidrocarburos</label>

                                            <div class="d-flex align-items-center">
                                                <datetime v-model="model.f_curso_nor" placeholder="Selecciona una Fecha"
                                                    :format="'dd-LL-yyyy'" input-id="f_curso_nor" name="f_curso_nor"
                                                    value-zone="America/Lima" zone="America/Lima" class="form-control"
                                                    :min-datetime="min_datetime" :max-datetime="max_datetime"
                                                    @focus="$parent.clearErrorMsg($event)">
                                                </datetime>

                                                <button type="button" class="btn btn-light ms-2"
                                                    @click="model.f_curso_nor = null">
                                                    ✕
                                                </button>
                                            </div>

                                            <div id="f_curso_nor-error" class="error invalid-feedback"></div>
                                        </div>

                                    </div>
                                </div>


                                <div class="col-lg-3">
                                    <div class="form-group row align-items-end">

                                        <div class="col-lg-1 text-center">
                                            <label for="lib_ter_pdf_modal" class="p-2" style="cursor: pointer;">
                                                <i class="fas fa-file-upload fa-lg"></i>
                                            </label>
                                            <input type="file" id="lib_ter_pdf_modal" name="lib_ter_pdf_modal"
                                                accept="application/pdf" @change="$parent.clearErrorMsg($event)" hidden>
                                            <div id="lib_ter_pdf_modal-error" class="error invalid-feedback d-block">
                                            </div>
                                        </div>
                                        <div class="col-lg-1 text-center">
                                            <a :href="this.model.url_f_lib_ter" target="_blank" class="p-2"
                                                style="cursor: pointer;">
                                                <i class="fas fa-eye fa-lg"></i>
                                            </a>
                                        </div>

                                        <div class="col-lg-11">
                                            <label class="form-control-label">Libreta tripulante terrestre</label>

                                            <div class="d-flex align-items-center">
                                                <datetime v-model="model.f_lib_ter" placeholder="Selecciona una Fecha"
                                                    :format="'dd-LL-yyyy'" input-id="f_lib_ter" name="f_lib_ter"
                                                    value-zone="America/Lima" zone="America/Lima" class="form-control"
                                                    :min-datetime="min_datetime" :max-datetime="max_datetime"
                                                    @focus="$parent.clearErrorMsg($event)">
                                                </datetime>

                                                <button type="button" class="btn btn-light ms-2"
                                                    @click="model.f_lib_ter = null">
                                                    ✕
                                                </button>
                                            </div>

                                            <div id="f_lib_ter-error" class="error invalid-feedback"></div>
                                        </div>

                                    </div>
                                </div>

                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-success">
                                Actualizar
                            </button>
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                        </div>
                    </div>
                </form>
                <!--end::Form-->
            </div>
        </div>
    </div>
    <!--end::Modal-->
</template>

<script>

import EventBus from '../event-bus';

export default {
    props: {
        url: {
            type: String,
            default: ''
        },
        url_update: {
            type: String,
            default: ''
        },
        companies: {
            type: Array,
            default: ''
        },
    },
    data() {
        return {
            model: {
            },
        }
    },
    watch: {

    },
    computed: {

    },
    created() {

        EventBus.$on('edit_modal', function (model) {
            this.model = model;
            $('#modal').modal('show');
        }.bind(this));
    },
    mounted() {

    },
    methods: {
        formController: function (url_update, event) {
            var vm = this;

            var target = $(event.target);
            var url = url_update;
            var fd = new FormData(event.target);

            EventBus.$emit('loading', true);

            // EventBus.$emit('loading', true);
            axios.post(url, fd, {
                headers: {
                    'Content-type': 'application/x-www-form-urlencoded',
                }
            }).then(response => {
                EventBus.$emit('loading', false);
                $('#modal').modal('hide');
                this.$parent.alertMsg(response.data);
                EventBus.$emit('refresh_table');

            }).catch(error => {
                EventBus.$emit('loading', false);
                console.log(error.response);
                var obj = error.response.data.errors;
                $('.modal').animate({
                    scrollTop: 0
                }, 500, 'swing');
                $.each(obj, function (i, item) {
                    let c_target = target.find("#" + i + "-error");
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