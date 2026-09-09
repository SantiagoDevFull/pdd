<template>
    <!--begin::Portlet-->

    <div class="kt-portlet">
        <div class="kt-portlet__head">
            <div class="kt-portlet__head-label">
                <h3 class="kt-portlet__head-title">
                    Crear Chofer
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
                                    <label class="form-control-label">Nombre:</label>
                                    <input type="text" class="form-control" name="first_name" id="first_name"
                                        placeholder="Nombres" :maxlength="50" v-model="model.first_name"
                                        @input="formato" @focus="$parent.clearErrorMsg($event)">
                                    <div id="first_name-error" class="error invalid-feedback"></div>
                                </div>
                            </div>

                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label class="form-control-label">Apellido:</label>
                                    <input type="text" class="form-control" name="last_name" id="last_name"
                                        placeholder="Apellidos" :maxlength="50" v-model="model.last_name"
                                        @input="formato" @focus="$parent.clearErrorMsg($event)">
                                    <div id="last_name-error" class="error invalid-feedback"></div>
                                </div>
                            </div>

                            <div class="col-lg-3">
                                <div class="form-group row align-items-end">

                                    <div class="col-lg-1 text-center">
                                        <label for="document_number_pdf" class="p-2" style="cursor: pointer;">
                                            <i class="fas fa-file-upload fa-lg"></i>
                                        </label>

                                        <input type="file" id="document_number_pdf" name="document_number_pdf"
                                            accept="application/pdf" @change="$parent.clearErrorMsg($event)" hidden>

                                        <div id="document_number_pdf-error" class="invalid-feedback d-block"></div>
                                    </div>

                                    <div class="col-lg-11">
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
                                        <label for="license_a3_pdf" class="p-2" style="cursor: pointer;">
                                            <i class="fas fa-file-upload fa-lg"></i>
                                        </label>

                                        <input type="file" id="license_a3_pdf" name="license_a3_pdf"
                                            accept="application/pdf" @change="$parent.clearErrorMsg($event)" hidden>

                                        <div id="license_a3_pdf-error" class="invalid-feedback d-block"></div>
                                    </div>

                                    <div class="col-lg-11">
                                        <label class="form-control-label">Licencia A3:</label>

                                        <div class="d-flex align-items-center">
                                            <datetime v-model="model.f_license_a3" placeholder="Selecciona una Fecha"
                                                :format="'dd-LL-yyyy'" input-id="f_license_a3" name="f_license_a3"
                                                value-zone="America/Lima" zone="America/Lima" class="form-control"
                                                :min-datetime="min_datetime" :max-datetime="max_datetime"
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
                                        <label for="f_license_a4_pdf" class="p-2" style="cursor: pointer;">
                                            <i class="fas fa-file-upload fa-lg"></i>
                                        </label>
                                        <input type="file" id="f_license_a4_pdf" name="f_license_a4_pdf"
                                            accept="application/pdf" @change="$parent.clearErrorMsg($event)" hidden>
                                        <div id="f_license_a4_pdf-error" class="error invalid-feedback d-block"></div>
                                    </div>
                                    <div class="col-lg-11">
                                        <label class="form-control-label">Licencia A4:</label>

                                        <div class="d-flex align-items-center">
                                            <datetime v-model="model.f_license_a4" placeholder="Selecciona una Fecha"
                                                :format="'dd-LL-yyyy'" input-id="f_license_a4" name="f_license_a4"
                                                value-zone="America/Lima" zone="America/Lima" class="form-control"
                                                :min-datetime="min_datetime" :max-datetime="max_datetime"
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
                                        <label for="f_emo_pdf" class="p-2" style="cursor: pointer;">
                                            <i class="fas fa-file-upload fa-lg"></i>
                                        </label>
                                        <input type="file" id="f_emo_pdf" name="f_emo_pdf" accept="application/pdf"
                                            @change="$parent.clearErrorMsg($event)" hidden>
                                        <div id="f_emo_pdf-error" class="error invalid-feedback d-block"></div>
                                    </div>

                                    <div class="col-lg-11">
                                        <label class="form-control-label">EMO:</label>

                                        <div class="d-flex align-items-center">
                                            <datetime v-model="model.f_emo" placeholder="Selecciona una Fecha"
                                                :format="'dd-LL-yyyy'" input-id="f_emo" name="f_emo"
                                                value-zone="America/Lima" zone="America/Lima" class="form-control"
                                                :min-datetime="min_datetime" :max-datetime="max_datetime"
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
                                        <label for="f_carnet_solgas_pdf" class="p-2" style="cursor: pointer;">
                                            <i class="fas fa-file-upload fa-lg"></i>
                                        </label>
                                        <input type="file" id="f_carnet_solgas_pdf" name="f_carnet_solgas_pdf"
                                            accept="application/pdf" @change="$parent.clearErrorMsg($event)" hidden>
                                        <div id="f_carnet_solgas_pdf-error" class="error invalid-feedback d-block">
                                        </div>
                                    </div>

                                    <div class="col-lg-11">
                                        <label class="form-control-label">CARNET SOLGAS:</label>

                                        <div class="d-flex align-items-center">
                                            <datetime v-model="model.f_carnet_solgas" placeholder="Selecciona una Fecha"
                                                :format="'dd-LL-yyyy'" input-id="f_carnet_solgas" name="f_carnet_solgas"
                                                value-zone="America/Lima" zone="America/Lima" class="form-control"
                                                :min-datetime="min_datetime" :max-datetime="max_datetime"
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
                                        <label for="f_ant_pen_pdf" class="p-2" style="cursor: pointer;">
                                            <i class="fas fa-file-upload fa-lg"></i>
                                        </label>
                                        <input type="file" id="f_ant_pen_pdf" name="f_ant_pen_pdf"
                                            accept="application/pdf" @change="$parent.clearErrorMsg($event)" hidden>
                                        <div id="f_ant_pen_pdf-error" class="error invalid-feedback d-block"></div>
                                    </div>

                                    <div class="col-lg-11">
                                        <label class="form-control-label">Antecedentes Penales</label>

                                        <div class="d-flex align-items-center">
                                            <datetime v-model="model.f_ant_pen" placeholder="Selecciona una Fecha"
                                                :format="'dd-LL-yyyy'" input-id="f_ant_pen" name="f_ant_pen"
                                                value-zone="America/Lima" zone="America/Lima" class="form-control"
                                                :min-datetime="min_datetime" :max-datetime="max_datetime"
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
                                        <label for="f_ant_pol_pdf" class="p-2" style="cursor: pointer;">
                                            <i class="fas fa-file-upload fa-lg"></i>
                                        </label>
                                        <input type="file" id="f_ant_pol_pdf" name="f_ant_pol_pdf"
                                            accept="application/pdf" @change="$parent.clearErrorMsg($event)" hidden>
                                        <div id="f_ant_pol_pdf-error" class="error invalid-feedback d-block"></div>
                                    </div>

                                    <div class="col-lg-11">
                                        <label class="form-control-label">Antecedentes Policiales:</label>

                                        <div class="d-flex align-items-center">
                                            <datetime v-model="model.f_ant_pol" placeholder="Selecciona una Fecha"
                                                :format="'dd-LL-yyyy'" input-id="f_ant_pol" name="f_ant_pol"
                                                value-zone="America/Lima" zone="America/Lima" class="form-control"
                                                :min-datetime="min_datetime" :max-datetime="max_datetime"
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
                                        <label for="curso_nor_pdf" class="p-2" style="cursor: pointer;">
                                            <i class="fas fa-file-upload fa-lg"></i>
                                        </label>
                                        <input type="file" id="curso_nor_pdf" name="curso_nor_pdf"
                                            accept="application/pdf" @change="$parent.clearErrorMsg($event)" hidden>
                                        <div id="curso_nor_pdf-error" class="error invalid-feedback d-block"></div>
                                    </div>

                                    <div class="col-lg-11">
                                        <label class="form-control-label">
                                            Curso Normativo de Transporte de Hidrocarburos
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


                            <div class="col-lg-3">
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
                            </div>

                        </div>
                        <div class="kt-portlet__foot">
                            <div class="kt-form__actions">
                                <div class="row">
                                    <div class="col-lg-6">
                                        <button type="submit" class="btn btn-success">Crear</button>
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
        url: {
            type: String,
            default: ''
        },
    },
    data() {
        return {
            model: {
                company_id: '',
                first_name: '',
                last_name: '',
                document_number: '',

                f_license_a3: this.min_datetime,
                f_license_a4: this.min_datetime,
                f_emo: this.min_datetime,
                f_carnet_solgas: this.datetime,
                f_ant_pen: this.datetime,
                f_ant_pol: this.datetime,
                f_carnet_trip: this.datetime,
                f_curso_nor: this.min_datetime,
                f_lib_ter: this.min_datetime,
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

        formController: function (url, event) {
            var vm = this;

            var target = $(event.target);
            var url = url;
            var fd = new FormData(event.target);

            EventBus.$emit('loading', true);

            axios.post(url, fd, {
                headers: {
                    'Content-type': 'application/x-www-form-urlencoded',
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