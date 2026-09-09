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
                            <label class="form-control-label">Compañia:</label>
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
                            <label class="form-control-label">Area:</label>
                            <select class="form-control" name="area_all_id" id="area_all_id" v-model="model.area_all_id"
                                @focus="$parent.clearErrorMsg($event)">
                                <option value="" selected disabled>Seleccionar</option>
                                <option v-for="area in area_alls" :value="area.id" v-bind:key="area.id">{{
                                    area.name }}</option>
                            </select>
                            <div id="area_all_id-error" class="error invalid-feedback"></div>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="form-group">
                            <label class="form-control-label">Condición de Pago:</label>
                            <select class="form-control" name="adm_payment_id" id="adm_payment_id"
                                v-model="model.adm_payment_id" @focus="$parent.clearErrorMsg($event)">
                                <option value="" selected disabled>Seleccionar</option>
                                <option v-for="payment in adm_payments" :value="payment.id" v-bind:key="payment.id">{{
                                    payment.name }}</option>
                            </select>
                            <div id="adm_payment_id-error" class="error invalid-feedback"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="kt-portlet__foot">
                <div class="kt-form__actions">
                    <div class="row">
                        <div class="col-lg-6">
                            <button type="submit" class="btn btn-primary">Buscar</button>
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
        url_get_area_alls: {
            type: String,
            default: ''
        },
        adm_payments: {
            type: Array,
            default: ''
        }
    },
    data() {
        return {
            model: {
                company_id: '',
                area_all_id: '',
                adm_payment_id: '',
            },
            area_alls: [],
        }
    },
    created() {

    },
    mounted() {

    },
    watch: {
        'model.company_id': function (val) {
            if (val != '') {
                EventBus.$emit('loading', true);

                axios.post(this.url_get_area_alls, {
                    company_id: this.model.company_id
                }).then(response => {

                    this.model.area_all_id = '';
                    this.area_alls = [];
                    this.area_alls = response.data;

                    EventBus.$emit('loading', false);
                }).catch(error => {
                    console.log(error);
                    console.log(error.response)
                });
            } else {
                this.model.area_all_id = '';
                this.area_alls = [];
            }
        },
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
                EventBus.$emit('show_table', {
                    'company_id': vm.model.company_id,
                    'area_all_id': vm.model.area_all_id,
                    'adm_payment_id': vm.model.adm_payment_id
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