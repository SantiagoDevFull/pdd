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
        <form class="kt-form" @submit.prevent="formController(url_validation, $event)">
            <div class="kt-portlet__body">
                <div class="row">

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
                            <button type="submit" class="btn btn-outline-brand btn-bold btn-sm">
                                <i class="fa fa-file-excel"></i> Exportar Excel
                            </button>
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
import 'vue-datetime/dist/vue-datetime.css';

export default {

    props: {
        current_date: {
            type: String,
            default: ''
        },

        url_validation: {
            type: String,
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

                final_date: '',
            }
        }
    },

    watch: {

    },
    methods: {

        formController(url_validation, event) {

            var vm = this;
            var target = $(event.target);
            var fd = new FormData(event.target);

            EventBus.$emit('loading', true);
            axios.post(url_validation, fd, {

                headers: {
                    'Content-type': 'application/x-www-form-urlencoded',
                },

            }).then(response => {

                axios.post(this.url, {
                    model: response.data,
                }, {
                    responseType: 'blob',
                }).then(response => {

                    EventBus.$emit('loading', false)

                    const tempUrl = window.URL.createObjectURL(new Blob([response.data]))
                    const link = document.createElement('a')

                    link.href = tempUrl
                    link.setAttribute('download', 'reporte-cartera-clientes-locales-' + Date.now() + '.xlsx')

                    document.body.appendChild(link)
                    link.click()

                }).catch(error => {
                    console.log(error)
                    EventBus.$emit('loading', false)
                })

            }).catch(error => {

                EventBus.$emit('loading', false)

                var obj = error.response.data.errors

                $('html, body').animate({
                    scrollTop: 0
                }, 500, 'swing')

                $.each(obj, function (i, item) {
                    let c_target = target.find("#" + i + "-error")
                    let p = c_target.parents('.form-group').find('#' + i)
                    p.addClass('is-invalid')
                    c_target.css('display', 'block')
                    c_target.html(item)
                })
            })
        }
    }
}
</script>
