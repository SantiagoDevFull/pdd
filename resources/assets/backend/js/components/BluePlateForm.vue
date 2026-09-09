<template>
    <!--begin::Portlet-->

    <div class="kt-portlet">
        <div class="kt-portlet__head">
            <div class="kt-portlet__head-label">
                <h3 class="kt-portlet__head-title">
                    Crear Vehículo
                </h3>
            </div>
        </div>

        <button class="btn btn-success" type="button" data-toggle="collapse" data-target="#collapseExample"
            aria-expanded="false" aria-controls="collapseExample">
            Abrir formulario
        </button>

        <div class="collapse" id="collapseExample">
            <div class="card card-body border border-success">
                <form class="kt-form" @submit.prevent="formController(url, $event)">
                    <div class="kt-portlet__body">
                        <div class="row">
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
                                    <label class="form-control-label">Placa:</label>
                                    <input type="text" class="form-control" name="plate" id="plate"
                                        placeholder="ABC-ABC" :maxlength="7" v-model="model.plate" @input="formato"
                                        @focus="$parent.clearErrorMsg($event)">
                                    <div id="plate-error" class="error invalid-feedback"></div>
                                </div>
                            </div>

                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label class="form-control-label">Año:</label>
                                    <select v-model="model.year_model" input-id="year_model" name="year_model"
                                        class="form-control" @focus="$parent.clearErrorMsg($event)">
                                        <option value="">Selecciona un año</option>
                                        <option value="2023">2023</option>
                                        <option value="2024">2024</option>
                                        <option value="2025">2025</option>
                                        <option value="2026">2026</option>
                                    </select>
                                    <div id="year_model-error" class="error invalid-feedback"></div>
                                </div>
                            </div>

                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label class="form-control-label">N# de Motor:</label>
                                    <input type="text" class="form-control" name="motor" id="motor"
                                        placeholder="3119H05789" :maxlength="7" v-model="model.motor" @input="formato"
                                        @focus="$parent.clearErrorMsg($event)">
                                    <div id="motor-error" class="error invalid-feedback"></div>
                                </div>
                            </div>

                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label class="form-control-label">N# de Serie:</label>
                                    <input type="text" class="form-control" name="chasis" id="chasis"
                                        placeholder="0014LLL122" :maxlength="25" v-model="model.chasis" @input="formato"
                                        @focus="$parent.clearErrorMsg($event)">
                                    <div id="chasis-error" class="error invalid-feedback"></div>
                                </div>
                            </div>


                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label class="form-control-label">N# de ejes:</label>
                                    <input type="number" class="form-control" name="eje" id="eje" placeholder="3"
                                        :maxlength="7" v-model="model.eje" @input="formato"
                                        @focus="$parent.clearErrorMsg($event)">
                                    <div id="eje-error" class="error invalid-feedback"></div>
                                </div>
                            </div>

                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label class="form-control-label">Clases:</label>
                                    <select class="form-control" name="blclase_id" id="blclase_id"
                                        v-model="model.blclase_id" @focus="$parent.clearErrorMsg($event)">
                                        <option value="">Seleccionar</option>
                                        <option v-for="bl_clase in bl_clases" :value="bl_clase.id"
                                            v-bind:key="bl_clase.id">{{
                                                bl_clase.description }}</option>
                                    </select>
                                    <div id="blclase_id-error" class="error invalid-feedback"></div>
                                </div>
                            </div>

                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label class="form-control-label">Tipos:</label>
                                    <select class="form-control" name="body_type_id" id="body_type_id"
                                        v-model="model.body_type_id" @focus="$parent.clearErrorMsg($event)">
                                        <option value="">Seleccionar</option>
                                        <option v-for="body_type in bl_body_types" :value="body_type.id"
                                            v-bind:key="body_type.id">{{
                                                body_type.description }}</option>
                                    </select>
                                    <div id="body_type_id-error" class="error invalid-feedback"></div>
                                </div>
                            </div>

                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label class="form-control-label">Marca:</label>
                                    <select class="form-control" name="brand_id" id="brand_id" v-model="model.brand_id"
                                        @focus="$parent.clearErrorMsg($event)">
                                        <option value="">Seleccionar</option>
                                        <option v-for="bl_brand in bl_brands" :value="bl_brand.id"
                                            v-bind:key="bl_brand.id">{{
                                                bl_brand.description }}</option>
                                    </select>
                                    <div id="brand_id-error" class="error invalid-feedback"></div>
                                </div>
                            </div>

                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label class="form-control-label">Modelo:</label>
                                    <select class="form-control" name="modelo_id" id="modelo_id"
                                        v-model="model.modelo_id" @focus="$parent.clearErrorMsg($event)">
                                        <option value="">Seleccionar</option>
                                        <option v-for="bl_modelo in bl_modelos" :value="bl_modelo.id"
                                            v-bind:key="bl_modelo.id">
                                            {{
                                                bl_modelo.description }}</option>
                                    </select>
                                    <div id="modelo_id-error" class="error invalid-feedback"></div>
                                </div>
                            </div>


                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label class="form-control-label">CNG:</label>
                                    <input type="text" class="form-control" name="cng" id="cng" placeholder="0014LLL122"
                                        :maxlength="25" v-model="model.cng" @input="formato"
                                        @focus="$parent.clearErrorMsg($event)">
                                    <div id="cng-error" class="error invalid-feedback"></div>
                                </div>
                            </div>

                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label class="form-control-label">CNA:</label>
                                    <input type="text" class="form-control" name="cna" id="cna" placeholder="0014LLL122"
                                        :maxlength="25" v-model="model.cna" @input="formato"
                                        @focus="$parent.clearErrorMsg($event)">
                                    <div id="cna-error" class="error invalid-feedback"></div>
                                </div>
                            </div>

                            <div class="col-lg-3">
                                <div class="form-group row align-items-end">

                                    <div class="col-lg-1 text-center">
                                        <label for="soat_pdf" class="p-2" style="cursor: pointer;">
                                            <i class="fas fa-file-upload fa-lg"></i>
                                        </label>

                                        <input type="file" id="soat_pdf" name="soat_pdf" accept="application/pdf"
                                            @change="$parent.clearErrorMsg($event)" hidden>

                                        <div id="soat_pdf-error" class="invalid-feedback d-block"></div>
                                    </div>

                                    <div class="col-lg-11">
                                        <label class="form-control-label">Fecha SOAT:</label>

                                        <div class="d-flex align-items-center">
                                            <datetime v-model="model.fecha_soat" placeholder="Selecciona una Fecha"
                                                :format="'dd-LL-yyyy'" input-id="fecha_soat" name="fecha_soat"
                                                value-zone="America/Lima" zone="America/Lima" class="form-control"
                                                :min-datetime="min_datetime" :max-datetime="max_datetime"
                                                @focus="$parent.clearErrorMsg($event)"></datetime>

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
                                        <label for="rt_pdf" class="p-2" style="cursor: pointer;">
                                            <i class="fas fa-file-upload fa-lg"></i>
                                        </label>
                                        <input type="file" id="rt_pdf" name="rt_pdf" accept="application/pdf"
                                            @change="$parent.clearErrorMsg($event)" hidden>
                                        <div id="rt_pdf-error" class="error invalid-feedback d-block"></div>
                                    </div>
                                    <div class="col-lg-11">
                                        <label class="form-control-label">Fecha RT MP 6m:</label>

                                        <div class="d-flex align-items-center">
                                            <datetime v-model="model.fecha_rt" placeholder="Selecciona una Fecha"
                                                :format="'dd-LL-yyyy'" input-id="fecha_rt" name="fecha_rt"
                                                value-zone="America/Lima" zone="America/Lima" class="form-control"
                                                :min-datetime="min_datetime" :max-datetime="max_datetime"
                                                @focus="$parent.clearErrorMsg($event)"></datetime>

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
                                        <label for="rtmg_pdf" class="p-2" style="cursor: pointer;">
                                            <i class="fas fa-file-upload fa-lg"></i>
                                        </label>
                                        <input type="file" id="rtmg_pdf" name="rtmg_pdf" accept="application/pdf"
                                            @change="$parent.clearErrorMsg($event)" hidden>
                                        <div id="rtmg_pdf-error" class="error invalid-feedback d-block"></div>
                                    </div>

                                    <div class="col-lg-11">
                                        <label class="form-control-label">Fecha RT MG 12m:</label>

                                        <div class="d-flex align-items-center">
                                            <datetime v-model="model.fecha_rtmg" placeholder="Selecciona una Fecha"
                                                :format="'dd-LL-yyyy'" input-id="fecha_rtmg" name="fecha_rtmg"
                                                value-zone="America/Lima" zone="America/Lima" class="form-control"
                                                :min-datetime="min_datetime" :max-datetime="max_datetime"
                                                @focus="$parent.clearErrorMsg($event)"></datetime>

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
                                        <label for="bon_pdf" class="p-2" style="cursor: pointer;">
                                            <i class="fas fa-file-upload fa-lg"></i>
                                        </label>
                                        <input type="file" id="bon_pdf" name="bon_pdf" accept="application/pdf"
                                            @change="$parent.clearErrorMsg($event)" hidden>
                                        <div id="bon_pdf-error" class="error invalid-feedback d-block"></div>
                                    </div>

                                    <div class="col-lg-11">
                                        <label class="form-control-label">Fecha de Bonificación:</label>

                                        <div class="d-flex align-items-center">
                                            <datetime v-model="model.fecha_bon" placeholder="Selecciona una Fecha"
                                                :format="'dd-LL-yyyy'" input-id="fecha_bon" name="fecha_bon"
                                                value-zone="America/Lima" zone="America/Lima" class="form-control"
                                                :min-datetime="min_datetime" :max-datetime="max_datetime"
                                                @focus="$parent.clearErrorMsg($event)"></datetime>

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
                                        <label for="vcto_pdf" class="p-2" style="cursor: pointer;">
                                            <i class="fas fa-file-upload fa-lg"></i>
                                        </label>
                                        <input type="file" id="vcto_pdf" name="vcto_pdf" accept="application/pdf"
                                            @change="$parent.clearErrorMsg($event)" hidden>
                                        <div id="vcto_pdf-error" class="error invalid-feedback d-block"></div>
                                    </div>

                                    <div class="col-lg-11">
                                        <label class="form-control-label">Documento DGH:</label>

                                        <div class="d-flex align-items-center">
                                            <datetime v-model="model.fecha_vcto" placeholder="Selecciona una Fecha"
                                                :format="'dd-LL-yyyy'" input-id="fecha_vcto" name="fecha_vcto"
                                                value-zone="America/Lima" zone="America/Lima" class="form-control"
                                                :min-datetime="min_datetime" :max-datetime="max_datetime"
                                                @focus="$parent.clearErrorMsg($event)"></datetime>

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
                                        <label for="tanque_pdf" class="p-2" style="cursor: pointer;">
                                            <i class="fas fa-file-upload fa-lg"></i>
                                        </label>
                                        <input type="file" id="tanque_pdf" name="tanque_pdf" accept="application/pdf"
                                            @change="$parent.clearErrorMsg($event)" hidden>
                                        <div id="tanque_pdf-error" class="error invalid-feedback d-block"></div>
                                    </div>

                                    <div class="col-lg-11">
                                        <label class="form-control-label">REVISIÓN ANUAL DE TANQUE:</label>

                                        <div class="d-flex align-items-center">
                                            <datetime v-model="model.fecha_tanque" placeholder="Selecciona una Fecha"
                                                :format="'dd-LL-yyyy'" input-id="fecha_tanque" name="fecha_tanque"
                                                value-zone="America/Lima" zone="America/Lima" class="form-control"
                                                :min-datetime="min_datetime" :max-datetime="max_datetime"
                                                @focus="$parent.clearErrorMsg($event)"></datetime>

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
                                        <label for="tanque_cil_pdf" class="p-2" style="cursor: pointer;">
                                            <i class="fas fa-file-upload fa-lg"></i>
                                        </label>
                                        <input type="file" id="tanque_cil_pdf" name="tanque_cil_pdf"
                                            accept="application/pdf" @change="$parent.clearErrorMsg($event)" hidden>
                                        <div id="tanque_cil_pdf-error" class="error invalid-feedback d-block"></div>
                                    </div>

                                    <div class="col-lg-11">
                                        <label class="form-control-label">REVISIÓN DE CILINDROS / TANQUE:</label>

                                        <div class="d-flex align-items-center">
                                            <datetime v-model="model.fecha_tanque_cil"
                                                placeholder="Selecciona una Fecha" :format="'dd-LL-yyyy'"
                                                input-id="fecha_tanque_cil" name="fecha_tanque_cil"
                                                value-zone="America/Lima" zone="America/Lima" class="form-control"
                                                :min-datetime="min_datetime" :max-datetime="max_datetime"
                                                @focus="$parent.clearErrorMsg($event)"></datetime>

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
                                        <label for="p_callao_pdf" class="p-2" style="cursor: pointer;">
                                            <i class="fas fa-file-upload fa-lg"></i>
                                        </label>
                                        <input type="file" id="p_callao_pdf" name="p_callao_pdf"
                                            accept="application/pdf" @change="$parent.clearErrorMsg($event)" hidden>
                                        <div id="p_callao_pdf-error" class="error invalid-feedback d-block"></div>
                                    </div>

                                    <div class="col-lg-11">
                                        <label class="form-control-label">PERMISO DE CALLE EN CALLAO</label>

                                        <div class="d-flex align-items-center">
                                            <datetime v-model="model.f_p_callao" placeholder="Selecciona una Fecha"
                                                :format="'dd-LL-yyyy'" input-id="f_p_callao" name="f_p_callao"
                                                value-zone="America/Lima" zone="America/Lima" class="form-control"
                                                :min-datetime="min_datetime" :max-datetime="max_datetime"
                                                @focus="$parent.clearErrorMsg($event)"></datetime>

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
                                        <label for="circulation_card_pdf" class="p-2" style="cursor: pointer;">
                                            <i class="fas fa-file-upload fa-lg"></i>
                                        </label>
                                        <input type="file" id="circulation_card_pdf" name="circulation_card_pdf"
                                            accept="application/pdf" @change="$parent.clearErrorMsg($event)" hidden>
                                        <div id="circulation_card_pdf-error" class="error invalid-feedback d-block">
                                        </div>
                                    </div>

                                    <div class="col-lg-11">
                                        <label class="form-control-label">Tarjeta de circulación</label>

                                        <div class="d-flex align-items-center">
                                            <datetime v-model="model.f_circulation_card"
                                                placeholder="Selecciona una Fecha" :format="'dd-LL-yyyy'"
                                                input-id="f_circulation_card" name="f_circulation_card"
                                                value-zone="America/Lima" zone="America/Lima" class="form-control"
                                                :min-datetime="min_datetime" :max-datetime="max_datetime"
                                                @focus="$parent.clearErrorMsg($event)"></datetime>

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
                                        <label for="p_lima_pdf" class="p-2" style="cursor: pointer;">
                                            <i class="fas fa-file-upload fa-lg"></i>
                                        </label>
                                        <input type="file" id="p_lima_pdf" name="p_lima_pdf" accept="application/pdf"
                                            @change="$parent.clearErrorMsg($event)" hidden>
                                        <div id="p_lima_pdf-error" class="error invalid-feedback d-block"></div>
                                    </div>

                                    <div class="col-lg-11">
                                        <label class="form-control-label">PERMISO DE CALLE EN LIMA</label>

                                        <div class="d-flex align-items-center">
                                            <datetime v-model="model.f_p_lima" placeholder="Selecciona una Fecha"
                                                :format="'dd-LL-yyyy'" input-id="f_p_lima" name="f_p_lima"
                                                value-zone="America/Lima" zone="America/Lima" class="form-control"
                                                :min-datetime="min_datetime" :max-datetime="max_datetime"
                                                @focus="$parent.clearErrorMsg($event)"></datetime>

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
                                        <label for="cert_insp_pdf" class="p-2" style="cursor: pointer;">
                                            <i class="fas fa-file-upload fa-lg"></i>
                                        </label>
                                        <input type="file" id="cert_insp_pdf" name="cert_insp_pdf"
                                            accept="application/pdf" @change="$parent.clearErrorMsg($event)" hidden>
                                        <div id="cert_insp_pdf-error" class="error invalid-feedback d-block"></div>
                                    </div>

                                    <div class="col-lg-11">
                                        <label class="form-control-label">
                                            CERTIFICADO DE INSPECCION INTERNA / EXTERNA
                                        </label>

                                        <div class="d-flex align-items-center">
                                            <datetime v-model="model.f_cert_insp" placeholder="Selecciona una Fecha"
                                                :format="'dd-LL-yyyy'" input-id="f_cert_insp" name="f_cert_insp"
                                                value-zone="America/Lima" zone="America/Lima" class="form-control"
                                                :min-datetime="min_datetime" :max-datetime="max_datetime"
                                                @focus="$parent.clearErrorMsg($event)"></datetime>

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
                                        <label for="curso_nor_pdf" class="p-2" style="cursor: pointer;">
                                            <i class="fas fa-file-upload fa-lg"></i>
                                        </label>
                                        <input type="file" id="curso_nor_pdf" name="curso_nor_pdf"
                                            accept="application/pdf" @change="$parent.clearErrorMsg($event)" hidden>
                                        <div id="curso_nor_pdf-error" class="error invalid-feedback d-block"></div>
                                    </div>

                                    <div class="col-lg-11">
                                        <label class="form-control-label">
                                            Tarjeta de Propiedad
                                        </label>

                                        <div class="d-flex align-items-center">
                                            <datetime v-model="model.f_curso_nor" placeholder="Selecciona una Fecha"
                                                :format="'dd-LL-yyyy'" input-id="f_curso_nor" name="f_curso_nor"
                                                value-zone="America/Lima" zone="America/Lima" class="form-control"
                                                :min-datetime="min_datetime" :max-datetime="max_datetime"
                                                @focus="$parent.clearErrorMsg($event)"></datetime>

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
                                        <label for="lib_ter_pdf" class="p-2" style="cursor: pointer;">
                                            <i class="fas fa-file-upload fa-lg"></i>
                                        </label>
                                        <input type="file" id="lib_ter_pdf" name="lib_ter_pdf" accept="application/pdf"
                                            @change="$parent.clearErrorMsg($event)" hidden>
                                        <div id="lib_ter_pdf-error" class="error invalid-feedback d-block"></div>
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
                            </div> -->


                            <div class="col-lg-3">
                                <div class="form-group row align-items-end">

                                    <div class="col-lg-1 text-center">
                                        <label for="cer_fum_pdf" class="p-2" style="cursor: pointer;">
                                            <i class="fas fa-file-upload fa-lg"></i>
                                        </label>
                                        <input type="file" id="cer_fum_pdf" name="cer_fum_pdf" accept="application/pdf"
                                            @change="$parent.clearErrorMsg($event)" hidden>
                                        <div id="cer_fum_pdf-error" class="error invalid-feedback d-block"></div>
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
                                        <label for="cubicacion_pdf" class="p-2" style="cursor: pointer;">
                                            <i class="fas fa-file-upload fa-lg"></i>
                                        </label>
                                        <input type="file" id="cubicacion_pdf" name="cubicacion_pdf" accept="application/pdf"
                                            @change="$parent.clearErrorMsg($event)" hidden>
                                        <div id="cubicacion_pdf-error" class="error invalid-feedback d-block"></div>
                                    </div>

                                    <div class="col-lg-11">
                                        <label class="form-control-label">Doc. de Cubicación</label>

                                        <div class="d-flex align-items-center">
                                            <datetime v-model="model.f_cubicacion" placeholder="Selecciona una Fecha"
                                                :format="'dd-LL-yyyy'" input-id="f_cubicacion" name="f_cubicacion"
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
                        <div class="kt-portlet__foot">
                            <div class="kt-form__actions">
                                <div class="row">
                                    <div class="col-lg-6">
                                        <button type="submit" class="btn btn-primary">Crear</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!--begin::Form-->

        <!--end::Form-->
    </div>
    <!--end::Portlet-->
</template>

<script>
import EventBus from '../event-bus';
export default {
    props: {
        companies: {
            type: Array,
            default: ''
        },
        bl_asesores: {
            type: Array,
            default: ''
        },
        bl_clients: {
            type: Array,
            default: ''
        },
        bl_services: {
            type: Array,
            default: ''
        },
        bl_clases: {
            type: Array,
            default: ''
        },
        bl_body_types: {
            type: Array,
            default: ''
        },
        bl_brands: {
            type: Array,
            default: ''
        },
        bl_modelos: {
            type: Array,
            default: ''
        },
        url: {
            type: String,
            default: ''
        },
    },
    data() {
        return {
            model: {
                company_id: '',
                plate: '',
                client_name: '',
                blclase_id: '',
                body_type_id: '',
                brand_id: '',
                modelo_id: '',
                motor: '',
                chasis: '',
                eje: '',
                cng: '',
                cna: '',

                year_model: '',
                fecha_soat: this.min_datetime,
                fecha_rt: this.min_datetime,
                fecha_rtmg: this.min_datetime,
                fecha_bon: this.min_datetime,
                fecha_vcto: this.min_datetime,
                fecha_tanque: this.min_datetime,
                fecha_tanque_cil: this.min_datetime,
                f_p_callao: this.min_datetime,
                f_p_lima: this.min_datetime,
                f_circulation_card: this.min_datetime,
                f_cert_insp: this.min_datetime,
                f_curso_nor: this.min_datetime,
                f_cer_fum: this.min_datetime,
                f_cubicacion: this.min_datetime,
            },
        }
    },
    created() {

    },
    mounted() {
    },

    watch: {

    },
    computed: {

    },
    methods: {

        formato() {
            this.model.plate = this.model.plate.trim();

            if (this.model.plate.length == 3) {

                this.model.plate = this.model.plate.substring(0, 3) + '-' + this.model.plate.substring(4, 3);
            }

        },

        formController: function (url, event) {
            var vm = this;

            var target = $(event.target);
            var url = url;
            var fd = new FormData(event.target);

            EventBus.$emit('loading', true);

            axios.post(url, fd, {
                headers: {
                    'Content-Type': 'multipart/form-data',
                }
            }).then(response => {
                EventBus.$emit('loading', false);
                this.$parent.alertMsg(response.data);
                this.model.company_id = '';
                this.model.plate = '';
                EventBus.$emit('refresh_table');
            }).catch(error => {
                EventBus.$emit('loading', false);
                console.log(error.response);
                var obj = error.response.data.errors;
                $('html, body').animate({
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