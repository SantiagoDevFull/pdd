<template>
    <!--begin::Modal-->
    <div class="modal fade" id="modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <!--begin::Form-->
                <form class="kt-form" @submit.prevent="formController(url_update, $event)">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Actualizar Placa {{ this.model.plate }}</h5>
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
                                            <label for="soat_pdf_modal" class="p-2" style="cursor: pointer;">
                                                <i class="fas fa-file-upload fa-lg"></i>
                                            </label>

                                            <input type="file" id="soat_pdf_modal" name="soat_pdf_modal"
                                                accept="application/pdf" @change="$parent.clearErrorMsg($event)" hidden>
                                            <span class="file-check text-success d-none">✔</span>

                                            <div id="soat_pdf_modal-error" class="invalid-feedback d-block"></div>
                                        </div>

                                        <div class="col-lg-1 text-center">
                                            <a :href="this.model.url_fecha_soat" target="_blank" class="p-2"
                                                style="cursor: pointer;">
                                                <i class="fas fa-eye fa-lg"></i>
                                            </a>
                                        </div>

                                        <div class="col-lg-10">
                                            <label class="form-control-label">Fecha SOAT:</label>

                                            <div class="d-flex align-items-center">
                                                <datetime v-model="model.fecha_soat" placeholder="Selecciona una Fecha"
                                                    :format="'dd-LL-yyyy'" input-id="fecha_soat" name="fecha_soat"
                                                    value-zone="America/Lima" zone="America/Lima" class="form-control"
                                                    :min-datetime="min_datetime" :max-datetime="max_datetime"
                                                    @focus="$parent.clearErrorMsg($event)">
                                                </datetime>

                                                <button type="button" class="btn btn-light ms-2"
                                                    @click="model.fecha_soat = null">
                                                    ✕
                                                </button>
                                            </div>

                                            <div id="fecha_soat-error" class="error invalid-feedback"></div>
                                        </div>


                                    </div>
                                </div>

                                <div class="col-lg-3">
                                    <div class="form-group row align-items-end">

                                        <div class="col-lg-1 text-center">
                                            <label for="rt_pdf_modal" class="p-2" style="cursor: pointer;">
                                                <i class="fas fa-file-upload fa-lg"></i>
                                            </label>
                                            <input type="file" id="rt_pdf_modal" name="rt_pdf_modal"
                                                accept="application/pdf" @change="$parent.clearErrorMsg($event)" hidden>
                                            <div id="rt_pdf_modal-error" class="error invalid-feedback d-block"></div>
                                        </div>
                                        <div class="col-lg-1 text-center">
                                            <a :href="this.model.url_fecha_rt" target="_blank" class="p-2"
                                                style="cursor: pointer;">
                                                <i class="fas fa-eye fa-lg"></i>
                                            </a>
                                        </div>
                                        <div class="col-lg-11">
                                            <label class="form-control-label">Fecha RT MP 6m:</label>

                                            <div class="d-flex align-items-center">
                                                <datetime v-model="model.fecha_rt" placeholder="Selecciona una Fecha"
                                                    :format="'dd-LL-yyyy'" input-id="fecha_rt" name="fecha_rt"
                                                    value-zone="America/Lima" zone="America/Lima" class="form-control"
                                                    :min-datetime="min_datetime" :max-datetime="max_datetime"
                                                    @focus="$parent.clearErrorMsg($event)">
                                                </datetime>

                                                <button type="button" class="btn btn-light ms-2"
                                                    @click="model.fecha_rt = null">
                                                    ✕
                                                </button>
                                            </div>

                                            <div id="fecha_rt-error" class="error invalid-feedback"></div>
                                        </div>

                                    </div>
                                </div>


                                <div class="col-lg-3">
                                    <div class="form-group row align-items-end">

                                        <div class="col-lg-1 text-center">
                                            <label for="rtmg_pdf_modal" class="p-2" style="cursor: pointer;">
                                                <i class="fas fa-file-upload fa-lg"></i>
                                            </label>
                                            <input type="file" id="rtmg_pdf_modal" name="rtmg_pdf_modal"
                                                accept="application/pdf" @change="$parent.clearErrorMsg($event)" hidden>
                                            <div id="rtmg_pdf_modal-error" class="error invalid-feedback d-block"></div>
                                        </div>
                                        <div class="col-lg-1 text-center">
                                            <a :href="this.model.url_fecha_rtmg" target="_blank" class="p-2"
                                                style="cursor: pointer;">
                                                <i class="fas fa-eye fa-lg"></i>
                                            </a>
                                        </div>
                                        <div class="col-lg-11">
                                            <label class="form-control-label">Fecha RT MG 12m:</label>

                                            <div class="d-flex align-items-center">
                                                <datetime v-model="model.fecha_rtmg" placeholder="Selecciona una Fecha"
                                                    :format="'dd-LL-yyyy'" input-id="fecha_rtmg" name="fecha_rtmg"
                                                    value-zone="America/Lima" zone="America/Lima" class="form-control"
                                                    :min-datetime="min_datetime" :max-datetime="max_datetime"
                                                    @focus="$parent.clearErrorMsg($event)">
                                                </datetime>

                                                <button type="button" class="btn btn-light ms-2"
                                                    @click="model.fecha_rtmg = null">
                                                    ✕
                                                </button>
                                            </div>

                                            <div id="fecha_rtmg-error" class="error invalid-feedback"></div>
                                        </div>

                                    </div>
                                </div>

                                <div class="col-lg-3">
                                    <div class="form-group row align-items-end">

                                        <div class="col-lg-1 text-center">
                                            <label for="bon_pdf_modal" class="p-2" style="cursor: pointer;">
                                                <i class="fas fa-file-upload fa-lg"></i>
                                            </label>
                                            <input type="file" id="bon_pdf_modal" name="bon_pdf_modal"
                                                accept="application/pdf" @change="$parent.clearErrorMsg($event)" hidden>
                                            <div id="bon_pdf_modal-error" class="error invalid-feedback d-block"></div>
                                        </div>
                                        <div class="col-lg-1 text-center">
                                            <a :href="this.model.url_fecha_bon" target="_blank" class="p-2"
                                                style="cursor: pointer;">
                                                <i class="fas fa-eye fa-lg"></i>
                                            </a>
                                        </div>
                                        <div class="col-lg-11">
                                            <label class="form-control-label">Fecha de Bonificación:</label>

                                            <div class="d-flex align-items-center">
                                                <datetime v-model="model.fecha_bon" placeholder="Selecciona una Fecha"
                                                    :format="'dd-LL-yyyy'" input-id="fecha_bon" name="fecha_bon"
                                                    value-zone="America/Lima" zone="America/Lima" class="form-control"
                                                    :min-datetime="min_datetime" :max-datetime="max_datetime"
                                                    @focus="$parent.clearErrorMsg($event)">
                                                </datetime>

                                                <button type="button" class="btn btn-light ms-2"
                                                    @click="model.fecha_bon = null">
                                                    ✕
                                                </button>
                                            </div>

                                            <div id="fecha_bon-error" class="error invalid-feedback"></div>
                                        </div>

                                    </div>
                                </div>


                                <div class="col-lg-3">
                                    <div class="form-group row align-items-end">

                                        <div class="col-lg-1 text-center">
                                            <label for="vcto_pdf_modal" class="p-2" style="cursor: pointer;">
                                                <i class="fas fa-file-upload fa-lg"></i>
                                            </label>
                                            <input type="file" id="vcto_pdf_modal" name="vcto_pdf_modal"
                                                accept="application/pdf" @change="$parent.clearErrorMsg($event)" hidden>
                                            <div id="vcto_pdf_modal-error" class="error invalid-feedback d-block"></div>
                                        </div>
                                        <div class="col-lg-1 text-center">
                                            <a :href="this.model.url_fecha_vcto" target="_blank" class="p-2"
                                                style="cursor: pointer;">
                                                <i class="fas fa-eye fa-lg"></i>
                                            </a>
                                        </div>
                                        <div class="col-lg-11">
                                            <label class="form-control-label">Documento DGH:</label>

                                            <div class="d-flex align-items-center">
                                                <datetime v-model="model.fecha_vcto" placeholder="Selecciona una Fecha"
                                                    :format="'dd-LL-yyyy'" input-id="fecha_vcto" name="fecha_vcto"
                                                    value-zone="America/Lima" zone="America/Lima" class="form-control"
                                                    :min-datetime="min_datetime" :max-datetime="max_datetime"
                                                    @focus="$parent.clearErrorMsg($event)">
                                                </datetime>

                                                <button type="button" class="btn btn-light ms-2"
                                                    @click="model.fecha_vcto = null">
                                                    ✕
                                                </button>
                                            </div>

                                            <div id="fecha_vcto-error" class="error invalid-feedback"></div>
                                        </div>

                                    </div>
                                </div>


                                <div class="col-lg-3">
                                    <div class="form-group row align-items-end">

                                        <div class="col-lg-1 text-center">
                                            <label for="tanque_pdf_modal" class="p-2" style="cursor: pointer;">
                                                <i class="fas fa-file-upload fa-lg"></i>
                                            </label>
                                            <input type="file" id="tanque_pdf_modal" name="tanque_pdf_modal"
                                                accept="application/pdf" @change="$parent.clearErrorMsg($event)" hidden>
                                            <div id="tanque_pdf_modal-error" class="error invalid-feedback d-block">
                                            </div>
                                        </div>
                                        <div class="col-lg-1 text-center">
                                            <a :href="this.model.url_fecha_tanque" target="_blank" class="p-2"
                                                style="cursor: pointer;">
                                                <i class="fas fa-eye fa-lg"></i>
                                            </a>
                                        </div>
                                        <div class="col-lg-11">
                                            <label class="form-control-label">REVISIÓN ANUAL DE TANQUE:</label>

                                            <div class="d-flex align-items-center">
                                                <datetime v-model="model.fecha_tanque"
                                                    placeholder="Selecciona una Fecha" :format="'dd-LL-yyyy'"
                                                    input-id="fecha_tanque" name="fecha_tanque"
                                                    value-zone="America/Lima" zone="America/Lima" class="form-control"
                                                    :min-datetime="min_datetime" :max-datetime="max_datetime"
                                                    @focus="$parent.clearErrorMsg($event)">
                                                </datetime>

                                                <button type="button" class="btn btn-light ms-2"
                                                    @click="model.fecha_tanque = null">
                                                    ✕
                                                </button>
                                            </div>

                                            <div id="fecha_tanque-error" class="error invalid-feedback"></div>
                                        </div>

                                    </div>
                                </div>

                                <div class="col-lg-3">
                                    <div class="form-group row align-items-end">

                                        <div class="col-lg-1 text-center">
                                            <label for="tanque_cil_pdf_modal" class="p-2" style="cursor: pointer;">
                                                <i class="fas fa-file-upload fa-lg"></i>
                                            </label>
                                            <input type="file" id="tanque_cil_pdf_modal" name="tanque_cil_pdf_modal"
                                                accept="application/pdf" @change="$parent.clearErrorMsg($event)" hidden>
                                            <div id="tanque_cil_pdf_modal-error" class="error invalid-feedback d-block">
                                            </div>
                                        </div>
                                        <div class="col-lg-1 text-center">
                                            <a :href="this.model.url_fecha_tanque_cil" target="_blank" class="p-2"
                                                style="cursor: pointer;">
                                                <i class="fas fa-eye fa-lg"></i>
                                            </a>
                                        </div>
                                        <div class="col-lg-11">
                                            <label class="form-control-label">REVISIÓN DE CILINDROS / TANQUE:</label>

                                            <div class="d-flex align-items-center">
                                                <datetime v-model="model.fecha_tanque_cil"
                                                    placeholder="Selecciona una Fecha" :format="'dd-LL-yyyy'"
                                                    input-id="fecha_tanque_cil" name="fecha_tanque_cil"
                                                    value-zone="America/Lima" zone="America/Lima" class="form-control"
                                                    :min-datetime="min_datetime" :max-datetime="max_datetime"
                                                    @focus="$parent.clearErrorMsg($event)">
                                                </datetime>

                                                <button type="button" class="btn btn-light ms-2"
                                                    @click="model.fecha_tanque_cil = null">
                                                    ✕
                                                </button>
                                            </div>

                                            <div id="fecha_tanque_cil-error" class="error invalid-feedback"></div>
                                        </div>

                                    </div>
                                </div>

                                <div class="col-lg-3">
                                    <div class="form-group row align-items-end">

                                        <div class="col-lg-1 text-center">
                                            <label for="p_callao_pdf_modal" class="p-2" style="cursor: pointer;">
                                                <i class="fas fa-file-upload fa-lg"></i>
                                            </label>
                                            <input type="file" id="p_callao_pdf_modal" name="p_callao_pdf_modal"
                                                accept="application/pdf" @change="$parent.clearErrorMsg($event)" hidden>
                                            <div id="p_callao_pdf_modal-error" class="error invalid-feedback d-block">
                                            </div>
                                        </div>
                                        <div class="col-lg-1 text-center">
                                            <a :href="this.model.url_f_p_callao" target="_blank" class="p-2"
                                                style="cursor: pointer;">
                                                <i class="fas fa-eye fa-lg"></i>
                                            </a>
                                        </div>
                                        <div class="col-lg-11">
                                            <label class="form-control-label">PERMISO DE CALLE EN CALLAO</label>

                                            <div class="d-flex align-items-center">
                                                <datetime v-model="model.f_p_callao" placeholder="Selecciona una Fecha"
                                                    :format="'dd-LL-yyyy'" input-id="f_p_callao" name="f_p_callao"
                                                    value-zone="America/Lima" zone="America/Lima" class="form-control"
                                                    :min-datetime="min_datetime" :max-datetime="max_datetime"
                                                    @focus="$parent.clearErrorMsg($event)">
                                                </datetime>

                                                <button type="button" class="btn btn-light ms-2"
                                                    @click="model.f_p_callao = null">
                                                    ✕
                                                </button>
                                            </div>

                                            <div id="f_p_callao-error" class="error invalid-feedback"></div>
                                        </div>

                                    </div>
                                </div>

                                <div class="col-lg-3">
                                    <div class="form-group row align-items-end">

                                        <div class="col-lg-1 text-center">
                                            <label for="circulation_card_pdf_modal" class="p-2"
                                                style="cursor: pointer;">
                                                <i class="fas fa-file-upload fa-lg"></i>
                                            </label>
                                            <input type="file" id="circulation_card_pdf_modal"
                                                name="circulation_card_pdf_modal" accept="application/pdf"
                                                @change="$parent.clearErrorMsg($event)" hidden>
                                            <div id="circulation_card_pdf_modal-error"
                                                class="error invalid-feedback d-block">
                                            </div>
                                        </div>
                                        <div class="col-lg-1 text-center">
                                            <a :href="this.model.url_f_circulation_card" target="_blank" class="p-2"
                                                style="cursor: pointer;">
                                                <i class="fas fa-eye fa-lg"></i>
                                            </a>
                                        </div>
                                        <div class="col-lg-11">
                                            <label class="form-control-label">Tarjeta de circulación</label>

                                            <div class="d-flex align-items-center">
                                                <datetime v-model="model.f_circulation_card"
                                                    placeholder="Selecciona una Fecha" :format="'dd-LL-yyyy'"
                                                    input-id="f_circulation_card" name="f_circulation_card"
                                                    value-zone="America/Lima" zone="America/Lima" class="form-control"
                                                    :min-datetime="min_datetime" :max-datetime="max_datetime"
                                                    @focus="$parent.clearErrorMsg($event)">
                                                </datetime>

                                                <button type="button" class="btn btn-light ms-2"
                                                    @click="model.f_circulation_card = null">
                                                    ✕
                                                </button>
                                            </div>

                                            <div id="f_circulation_card-error" class="error invalid-feedback"></div>
                                        </div>

                                    </div>
                                </div>

                                <div class="col-lg-3">
                                    <div class="form-group row align-items-end">

                                        <div class="col-lg-1 text-center">
                                            <label for="p_lima_pdf_modal" class="p-2" style="cursor: pointer;">
                                                <i class="fas fa-file-upload fa-lg"></i>
                                            </label>
                                            <input type="file" id="p_lima_pdf_modal" name="p_lima_pdf_modal"
                                                accept="application/pdf" @change="$parent.clearErrorMsg($event)" hidden>
                                            <div id="p_lima_pdf_modal-error" class="error invalid-feedback d-block">
                                            </div>
                                        </div>
                                        <div class="col-lg-1 text-center">
                                            <a :href="this.model.url_f_p_lima" target="_blank" class="p-2"
                                                style="cursor: pointer;">
                                                <i class="fas fa-eye fa-lg"></i>
                                            </a>
                                        </div>
                                        <div class="col-lg-11">
                                            <label class="form-control-label">PERMISO DE CALLE EN LIMA</label>

                                            <div class="d-flex align-items-center">
                                                <datetime v-model="model.f_p_lima" placeholder="Selecciona una Fecha"
                                                    :format="'dd-LL-yyyy'" input-id="f_p_lima" name="f_p_lima"
                                                    value-zone="America/Lima" zone="America/Lima" class="form-control"
                                                    :min-datetime="min_datetime" :max-datetime="max_datetime"
                                                    @focus="$parent.clearErrorMsg($event)">
                                                </datetime>

                                                <button type="button" class="btn btn-light ms-2"
                                                    @click="model.f_p_lima = null">
                                                    ✕
                                                </button>
                                            </div>

                                            <div id="f_p_lima-error" class="error invalid-feedback"></div>
                                        </div>

                                    </div>
                                </div>

                                <div class="col-lg-3">
                                    <div class="form-group row align-items-end">

                                        <div class="col-lg-1 text-center">
                                            <label for="cert_insp_pdf_modal" class="p-2" style="cursor: pointer;">
                                                <i class="fas fa-file-upload fa-lg"></i>
                                            </label>
                                            <input type="file" id="cert_insp_pdf_modal" name="cert_insp_pdf_modal"
                                                accept="application/pdf" @change="$parent.clearErrorMsg($event)" hidden>
                                            <div id="cert_insp_pdf_modal-error" class="error invalid-feedback d-block">
                                            </div>
                                        </div>
                                        <div class="col-lg-1 text-center">
                                            <a :href="this.model.url_f_cert_insp" target="_blank" class="p-2"
                                                style="cursor: pointer;">
                                                <i class="fas fa-eye fa-lg"></i>
                                            </a>
                                        </div>
                                        <div class="col-lg-11">
                                            <label class="form-control-label">CERTIFICADO DE INSPECCIÓN INTERNA /
                                                EXTERNA</label>

                                            <div class="d-flex align-items-center">
                                                <datetime v-model="model.f_cert_insp" placeholder="Selecciona una Fecha"
                                                    :format="'dd-LL-yyyy'" input-id="f_cert_insp" name="f_cert_insp"
                                                    value-zone="America/Lima" zone="America/Lima" class="form-control"
                                                    :min-datetime="min_datetime" :max-datetime="max_datetime"
                                                    @focus="$parent.clearErrorMsg($event)">
                                                </datetime>

                                                <button type="button" class="btn btn-light ms-2"
                                                    @click="model.f_cert_insp = null">
                                                    ✕
                                                </button>
                                            </div>

                                            <div id="f_cert_insp-error" class="error invalid-feedback"></div>
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
                                            <label class="form-control-label">Tarjeta de Propiedad</label>

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


                                <!--   <div class="col-lg-3">
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
                                </div>-->


                                <div class="col-lg-3">
                                    <div class="form-group row align-items-end">

                                        <div class="col-lg-1 text-center">
                                            <label for="cer_fum_pdf_modal" class="p-2" style="cursor: pointer;">
                                                <i class="fas fa-file-upload fa-lg"></i>
                                            </label>
                                            <input type="file" id="cer_fum_pdf_modal" name="cer_fum_pdf_modal"
                                                accept="application/pdf" @change="$parent.clearErrorMsg($event)" hidden>
                                            <div id="cer_fum_pdf_modal-error" class="error invalid-feedback d-block">
                                            </div>
                                        </div>
                                        <div class="col-lg-1 text-center">
                                            <a :href="this.model.url_f_cer_fum" target="_blank" class="p-2"
                                                style="cursor: pointer;">
                                                <i class="fas fa-eye fa-lg"></i>
                                            </a>
                                        </div>
                                        <div class="col-lg-11">
                                            <label class="form-control-label">Certificado de Fumigación</label>

                                            <div class="d-flex align-items-center">
                                                <datetime v-model="model.f_cer_fum" placeholder="Selecciona una Fecha"
                                                    :format="'dd-LL-yyyy'" input-id="f_cer_fum" name="f_cer_fum"
                                                    value-zone="America/Lima" zone="America/Lima" class="form-control"
                                                    :min-datetime="min_datetime" :max-datetime="max_datetime"
                                                    @focus="$parent.clearErrorMsg($event)">
                                                </datetime>

                                                <button type="button" class="btn btn-light ms-2"
                                                    @click="model.f_cer_fum = null">
                                                    ✕
                                                </button>
                                            </div>

                                            <div id="f_cer_fum-error" class="error invalid-feedback"></div>
                                        </div>

                                    </div>

                                </div>

                                <div class="col-lg-3">
                                    <div class="form-group row align-items-end">

                                        <div class="col-lg-1 text-center">
                                            <label for="cubicacion_pdf_modal" class="p-2" style="cursor: pointer;">
                                                <i class="fas fa-file-upload fa-lg"></i>
                                            </label>
                                            <input type="file" id="cubicacion_pdf_modal" name="cubicacion_pdf_modal"
                                                accept="application/pdf" @change="$parent.clearErrorMsg($event)" hidden>
                                            <div id="cubicacion_pdf_modal-error" class="error invalid-feedback d-block">
                                            </div>
                                        </div>
                                        <div class="col-lg-1 text-center">
                                            <a :href="this.model.url_f_cubicacion" target="_blank" class="p-2"
                                                style="cursor: pointer;">
                                                <i class="fas fa-eye fa-lg"></i>
                                            </a>
                                        </div>
                                        <div class="col-lg-11">
                                            <label class="form-control-label">Doc. de Cubicación</label>

                                            <div class="d-flex align-items-center">
                                                <datetime v-model="model.f_cubicacion"
                                                    placeholder="Selecciona una Fecha" :format="'dd-LL-yyyy'"
                                                    input-id="f_cubicacion" name="f_cubicacion"
                                                    value-zone="America/Lima" zone="America/Lima" class="form-control"
                                                    :min-datetime="min_datetime" :max-datetime="max_datetime"
                                                    @focus="$parent.clearErrorMsg($event)">
                                                </datetime>

                                                <button type="button" class="btn btn-light ms-2"
                                                    @click="model.f_cubicacion = null">
                                                    ✕
                                                </button>
                                            </div>

                                            <div id="f_cubicacion-error" class="error invalid-feedback"></div>
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
                    'Content-Type': 'multipart/form-data',
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