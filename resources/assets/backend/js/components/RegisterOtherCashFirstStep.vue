<template>
    <!--begin::Portlet-->
    <div class="kt-portlet">
        <div class="kt-portlet__head">
            <div class="kt-portlet__head-label">
                <h3 class="kt-portlet__head-title">
                    Registro
                </h3>
            </div>
        </div>

        <!--begin::Form-->
        <form class="kt-form" @submit.prevent="formController(url, $event)" id="first-step-form">
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
                            <label class="form-control-label">Tipo de Documento:</label>
                            <select class="form-control" name="warehouse_document_type_id"
                                id="warehouse_document_type_id" v-model="model.warehouse_document_type_id"
                                @focus="$parent.clearErrorMsg($event)">
                                <option disabled value="">Seleccionar</option>
                                <option value="22">Otros Ingresos de Caja</option>
                            </select>
                            <div id="warehouse_document_type_id-error" class="error invalid-feedback"></div>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="form-group">
                            <label class="form-control-label">Fecha de Emisión:</label>
                            <datetime v-model="model.issue_date" placeholder="Selecciona una Fecha"
                                :format="'dd-LL-yyyy'" input-id="issue_date" name="issue_date" value-zone="America/Lima"
                                zone="America/Lima" class="form-control" :min-datetime="min_sale_date"
                                :max-datetime="max_sale_date" @focus="$parent.clearErrorMsg($event)">
                            </datetime>
                            <div id="issue_date-error" class="error invalid-feedback"></div>
                        </div>
                    </div>


                    <div class="col-lg-3">
                        <div class="form-group">
                            <label class="form-control-label">Tipo:</label>
                            <select class="form-control" name="warehouse_account_type_id" id="warehouse_account_type_id"
                                v-model="model.warehouse_account_type_id" @focus="$parent.clearErrorMsg($event)">
                                <option disabled value="" selected>Seleccionar</option>
                                <option value="1">Cliente</option>
                            </select>
                            <div id="warehouse_account_type_id-error" class="error invalid-feedback"></div>
                        </div>
                    </div>

                    <div class="col-lg-3">
                        <div class="form-group">
                            <label class="form-control-label">Nombre o Razón Social:</label>
                            <select class="form-control kt-select2" name="warehouse_account_id"
                                id="warehouse_account_id" v-model="model.warehouse_account_id"
                                @focus="$parent.clearErrorMsg($event)">
                                <option value="">Seleccionar</option>
                            </select>
                            <div id="warehouse_account_id-error" class="error invalid-feedback"></div>
                        </div>
                    </div>


                    <div class="col-lg-3">
                        <div class="form-group">
                            <label class="form-control-label">Moneda:</label>
                            <select class="form-control" name="currency_id" id="currency_id" v-model="model.currency_id"
                                @focus="$parent.clearErrorMsg($event)">
                                <option value="" selected disabled>Seleccionar</option>
                                <option v-for="currency in currencies" :value="currency.id" v-bind:key="currency.id">{{
                                    currency.name }}</option>
                            </select>
                            <div id="currency_id-error" class="error invalid-feedback"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="kt-portlet__foot">
                <div class="kt-form__actions">
                    <div class="row">
                        <div class="col-12 kt-align-right">
                            <!-- <button type="submit" class="btn btn-primary" v-if="model.voucher_number">Modificar</button> -->
                            <button type="submit" class="btn btn-primary">Siguiente</button>
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
        currencies: {
            type: Array,
            default: ''
        },
        url: {
            type: String,
            default: ''
        },
        url_get_accounts: {
            type: String,
            default: ''
        },
        min_sale_date: {
            type: String,
            default: ''
        },
        max_sale_date: {
            type: String,
            default: ''
        },
    },
    data() {
        return {
            model: {
                company_id: '1',
                warehouse_document_type_id: '22',
                issue_date: this.min_sale_date,
                warehouse_account_type_id: '1',
                warehouse_account_id: '',
                currency_id: '1',
            },
        }
    },
    created() {

    },
    mounted() {
        this.newSelect2();
    },
    watch: {

    },
    computed: {

    },
    methods: {
        newSelect2: function () {
            let vm = this;
            let token = document.head.querySelector('meta[name="csrf-token"]').content;

            $("#warehouse_account_id").select2({
                placeholder: "Buscar",
                allowClear: true,
                language: {
                    noResults: function () {
                        return 'No hay resultados';
                    },
                    searching: function () {
                        return 'Buscando...';
                    },
                    inputTooShort: function () {
                        return 'Ingresa 1 o más caracteres';
                    },
                    errorLoading: function () {
                        return 'No se pudo cargar la información'
                    }
                },
                ajax: {
                    url: this.url_get_accounts,
                    dataType: 'json',
                    delay: 250,
                    type: 'POST',
                    data: function (params) {
                        var queryParameters = {
                            q: params.term,
                            company_id: vm.model.company_id,
                            warehouse_account_type_id: vm.model.warehouse_account_type_id,
                            _token: token,
                        }

                        return queryParameters;
                    },
                    processResults: function (data, params) {
                        params.page = params.page || 1;

                        return {
                            results: data,
                            pagination: {
                                more: (params.page * 30) < data.total_count
                            }
                        };
                    },
                    cache: true
                },
                minimumInputLength: 1,
            }).on('select2:select', function (e) {
                var selected_element = $(e.currentTarget);
                vm.model.warehouse_account_id = parseInt(selected_element.val());
            }).on('select2:unselect', function (e) {
                vm.model.warehouse_account_id = '';
            });
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
                $('#first-step-form').find('input').prop('disabled', true);
                $('#first-step-form').find('select').prop('disabled', true);
                $('#first-step-form').find('button').prop('disabled', true);

                EventBus.$emit('loading', false);
                EventBus.$emit('show_table');
                EventBus.$emit('show_table_liquidation', this.model);

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