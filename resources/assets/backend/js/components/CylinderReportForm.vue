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
                            <label class="form-control-label">Fecha inicial:</label>
                            <datetime v-model="model.initial_date" placeholder="Selecciona una Fecha inicial"
                                :format="'dd-LL-yyyy'" input-id="initial_date" name="initial_date" value-zone="America/Lima"
                                zone="America/Lima" class="form-control" :max-datetime="current_date"
                                @focus="$parent.clearErrorMsg($event)">
                            </datetime>
                            <div id="initial_date-error" class="error invalid-feedback"></div>
                        </div>
                    </div>

                    <div class="col-lg-3">
                        <div class="form-group">
                            <label class="form-control-label">Fecha Final:</label>
                            <datetime v-model="model.final_date" placeholder="Selecciona una Fecha inicial"
                                :format="'dd-LL-yyyy'" input-id="final_date" name="final_date" value-zone="America/Lima"
                                zone="America/Lima" class="form-control" :max-datetime="current_date"
                                @focus="$parent.clearErrorMsg($event)">
                            </datetime>
                            <div id="final_date-error" class="error invalid-feedback"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="kt-portlet__foot">
                <div class="kt-form__actions">
                    <div class="row">
                        <div class="col-lg-6">
                            <button type="submit" class="btn btn-primary">Exportar</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
        <!--end::Form-->
    </div>
    <!--end::Portlet-->
</template>

<script>
import EventBus from '../event-bus';
import Datetime from 'vue-datetime';
// You need a specific loader for CSS files
import 'vue-datetime/dist/vue-datetime.css';

Vue.use(Datetime);

export default {
    props: {
        url: {
            type: String,
            default: ''
        },
        url_export: {
            type: String,
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
                initial_date: '',
                final_date: '',
            }
        }
    },
    mounted() {
        this.newSelect();
    },
    methods: {
        formController: function (url, event) {
            var target = $(event.target);
            var url = url;
            var fd = new FormData(event.target);

            EventBus.$emit('loading', true);
            axios.post(url, fd, {
                headers: {
                    'Content-type': 'application/x-www-form-urlencoded',
                }
            }).then(response => {

                axios.post(this.url_export, {
                    model: response.data,
                }, {
                    responseType: 'blob',
                }).then(response => {
                    EventBus.$emit('loading', false);

                    let creation_date = new Date();
                    let date = creation_date.getDate();
                    let month = creation_date.getMonth() + 1;
                    let year = creation_date.getFullYear();
                    creation_date = year + '-' + month + '-' + date;

                    const tempUrl = window.URL.createObjectURL(new Blob([response.data]));
                    const link = document.createElement('a');
                    link.href = tempUrl;
                    link.setAttribute('download', 'reporte-despacho-retorno-' + creation_date + '.xlsx');
                    document.body.appendChild(link);
                    link.click();
                }).catch(error => {
                    console.log(error);
                    console.log(error.response);
                    EventBus.$emit('loading', false);
                });

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
