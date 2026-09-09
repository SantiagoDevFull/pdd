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

        <!--begin::Form-->
        <form class="kt-form" @submit.prevent="formController(url, $event)">
            <div class="kt-portlet__body">
                <div class="row">
                    <div class="col-lg-3">
                        <div class="form-group">
                            <label class="form-control-label">Compañía:</label>
                            <select class="form-control" name="company_id" id="company_id" v-model="model.company_id"
                                @focus="$parent.clearErrorMsg($event)">
                                <option value="" selected disabled>Seleccionar</option>
                                <option v-for="company in companies" :value="company.id" v-bind:key="company.id">{{
                                    company.name }}</option>
                            </select>
                            <div id="company_id-error" class="error invalid-feedback"></div>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="form-group">
                            <label class="form-control-label">Placa:</label>
                            <input type="text" class="form-control" name="plate" id="plate" placeholder="ABC-ABC"
                                :maxlength="7" v-model="model.plate" @input="formato"
                                @focus="$parent.clearErrorMsg($event)">
                            <div id="plate-error" class="error invalid-feedback"></div>
                        </div>
                    </div>
                    <div class="col-lg-3" v-if="model.company_id == 6">
                        <div class="form-group">
                            <label class="form-control-label">Cliente:</label>
                            <input type="text" class="form-control" name="client_name" id="client_name"
                                v-model="model.client_name" @focus="$parent.clearErrorMsg($event)">
                            <div id="client_name-error" class="error invalid-feedback"></div>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="form-group">
                            <label class="form-control-label">Capacidad Máxima en KILOS:</label>
                            <input type="text" class="form-control" name="capacity" id="capacity"
                                v-model="model.capacity" @focus="$parent.clearErrorMsg($event)">
                            <div id="capacity-error" class="error invalid-feedback"></div>
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
        </form>
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
                plate: '',
                client_name: '',
                capacity: '',
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
                    'Content-type': 'application/x-www-form-urlencoded',
                }
            }).then(response => {
                EventBus.$emit('loading', false);
                this.$parent.alertMsg(response.data);

                this.model.company_id = '',
                    this.model.plate = ''
                this.model.client_name = ''
                this.model.capacity = ''

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