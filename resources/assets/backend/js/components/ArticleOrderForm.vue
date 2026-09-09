<template>
 <!--begin::Portlet-->
 <div class="kt-portlet">
        <div class="kt-portlet__head">
            <div class="kt-portlet__head-label">
                <h3 class="kt-portlet__head-title">
                    Crear
                </h3>
            </div>
        </div>

        <!--begin::Form-->
        <form class="kt-form" @submit.prevent="formController(url_list, $event)">
            <div class="kt-portlet__body">
                <div class="row">
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label class="form-control-label">Compañía:</label>
                            <select class="form-control" name="company_id" id="company_id"
                                v-model="model.company_id" @focus="$parent.clearErrorMsg($event)">
                                <option value="" selected disabled>Seleccionar</option>
                                <option v-for="company in companies" :value="company.id"
                                    v-bind:key="company.id">{{ company.name }}</option>
                            </select>
                            <div id="company_id-error" class="error invalid-feedback"></div>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="form-group">
                            <label class="form-control-label">Proveedor:</label>
                            <select class="form-control" name="providers_id" id="providers_id"
                                v-model="model.providers_id" @focus="$parent.clearErrorMsg($event)">
                                <option value="" selected disabled>Seleccionar</option>
                                <option v-for="providers in providers" :value="providers.id"
                                    v-bind:key="providers.id">{{ providers.business_name }}</option>
                            </select>
                            <div id="providers_id-error" class="error invalid-feedback"></div>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="form-group">
                            <label class="form-control-label">Tipo de Moneda:</label>
                            <select class="form-control" name="currency_id" id="currency_id"
                                v-model="model.currency_id" @focus="$parent.clearErrorMsg($event)" @change="State()">
                                <option value="" selected disabled>Seleccionar</option>
                                <option v-for="currency in currencies" :value="currency.id"
                                    v-bind:key="currency.id">{{ currency.name }}</option>
                            </select>
                            <div id="currency_id-error" class="error invalid-feedback"></div>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="form-group">
                            <label class="form-control-label">Tipo de Cambio:</label>
                            <input v-model="model.tc" class="form-control" type="text" name="tc" id="tc" placeholder="0.0" 
                            @focus="$parent.clearErrorMsg($event)">
                            <div id="tc-error" class="error invalid-feedback"></div>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="form-group">
                            <label class="form-control-label">Tipo de Orden:</label>
                            <select class="form-control" name="type" id="type"
                                v-model="model.type" @focus="$parent.clearErrorMsg($event)">
                                <option value="" selected disabled>Seleccionar</option>
                                <option value="ORDEN DE COMPRA">ORDEN DE COMPRA</option>
                                <option value="ORDEN DE SERVICIO">ORDEN DE SERVICIO</option>
                            </select>
                            <div id="type-error" class="error invalid-feedback"></div>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="form-group">
                            <label class="form-control-label">Área solicitante:</label>
                            <select class="form-control" name="area_id" id="area_id" v-model="model.area_id"
                                @focus="$parent.clearErrorMsg($event)">
                                <option value="" selected disabled>Seleccionar</option>
                                <option v-for="area in areas" :value="area.id" v-bind:key="area.id">
                                    {{ area.name }} - {{ area.short_name }}
                                </option>
                            </select>
                            <div id="area_id-error" class="error invalid-feedback"></div>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="form-group">
                            <label class="form-control-label">Seleccionar tipo de referencia</label>
                            <select class="form-control" name="reference_id" id="reference_id" v-model="model.reference_id"
                                @focus="$parent.clearErrorMsg($event)">
                                <option value="" selected disabled>Seleccionar</option>
                                <option value="1">PROFORMA</option>
                                <option value="2">COTIZACIÓN</option>
                            </select>
                            <div id="reference_id-error" class="error invalid-feedback"></div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label class="form-control-label">Concepto tipo de referencia:</label>
                            <input v-model="model.description" class="form-control" type="text" name="description" id="description" 
                            @focus="$parent.clearErrorMsg($event)">
                            <div id="description-error" class="error invalid-feedback"></div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label class="form-control-label">Tipo de Pago:</label>
                            <input v-model="model.type_pay" class="form-control" type="text" name="type_pay" id="type_pay" 
                            @focus="$parent.clearErrorMsg($event)">
                            <div id="type_pay-error" class="error invalid-feedback"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="kt-portlet__foot">
                <div class="kt-form__actions">
                    <div class="row">
                        <div class="col-12 kt-align-right">
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

export default{
    props: {
        companies:{
            type: Array,
            default:'',
        },
        providers:{
            type: Array,
            default:'',
        },
        currencies:{
            type: Array,
            default:'',
        },
        url_list:{
            type: String,
            default:'',
        },
        url_get_area:{
            type: String,
            default:'',
        }
    },
    data(){
        return{
            model:{
                company_id:'',
                providers_id:'',
                currency_id:'',
                tc:'',
                type:'',
                area_id:'',
                type_pay:'',
                reference_id:'',
                description:'',
            },
            areas: [],
        }
    },
    watch:{
            'model.company_id': function (val) {
                
                    EventBus.$emit('loading', true);

                    axios.post(this.url_get_area, {
                        company_id: this.model.company_id
                    }).then(response => {
                        this.areas = response.data;
                        EventBus.$emit('loading', false);
                    }).catch(error => {
                        console.log(error);
                        console.log(error.response)
                    });
        
            }
        },
    methods:{
        State: function () {
            var tm = $('#currency_id').val();

            if (tm != "") {
                if (tm == 1) {
                    $("#tc").val("1");
                    this.model.tc = '1';
                    $("#tc").prop("readonly", true);
                } else {
                    $("#tc").prop("readonly", false);
                    this.model.tc = '';
                }
            }

        },
        formController: function (url_list, event) {
            var target = $(event.target);
            var url_list = url_list;
            var fd = new FormData(event.target);

            EventBus.$emit('loading', true);
            axios.post(url_list, fd, {
                headers: {
                    'Content-type': 'application/x-www-form-urlencoded',
                }
            }).then(response => {
                EventBus.$emit('loading', false);
                target.find('select').prop('disabled', true);
                target.find('button').prop('disabled', true);
                target.find('input').prop('disabled', true);
                EventBus.$emit('show_table', response.data);
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
}

</script>