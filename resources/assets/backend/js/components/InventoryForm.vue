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
                            <label class="form-control-label">Compañía:</label>
                            <select class="form-control" name="company_id" id="company_id" v-model="model.company_id"
                                @focus="$parent.clearErrorMsg($event)">
                                <option value="">Seleccionar</option>
                                <option v-for="company in companies" :value="company.id" v-bind:key="company.id">{{
                                    company.name }}</option>
                            </select>
                            <div id="company_id-error" class="error invalid-feedback"></div>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="form-group">
                            <label class="form-control-label">Almacén:</label>
                            <select class="form-control" name="warehouse_type_id" id="warehouse_type_id"
                                v-model="model.warehouse_type_id" @focus="$parent.clearErrorMsg($event)">
                                <option value="">Seleccionar</option>
                                <option v-for="warehouse_type in warehouse_types" :value="warehouse_type.id"
                                    v-bind:key="warehouse_type.id">{{ warehouse_type.name }}</option>
                            </select>
                            <div id="warehouse_type_id-error" class="error invalid-feedback"></div>
                        </div>
                    </div>
                    <div class="col-lg-1">
                        <div class="form-group">
                            <label class="form-control-label">Ver Artículos</label>
                            <a href="#" @click.prevent="viewArticles()" class="btn btn-dark" title="Ver artículos">
                                <i class="la la-eye"></i>
                            </a>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="form-group">
                            <label class="form-control-label">Fecha del Inventario Físico:</label>
                            <datetime v-model="model.creation_date" placeholder="Selecciona una Fecha"
                                :format="'dd-LL-yyyy'" input-id="creation_date" name="creation_date"
                                value-zone="America/Lima" zone="America/Lima" class="form-control"
                                :max-datetime="current_date" @focus="$parent.clearErrorMsg($event)">
                            </datetime>
                            <div id="creation_date-error" class="error invalid-feedback"></div>
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
import Datetime from 'vue-datetime';
// You need a specific loader for CSS files
import 'vue-datetime/dist/vue-datetime.css';

Vue.use(Datetime);

export default {
    props: {
        companies: {
            type: Array,
            default: ''
        },
        current_date: {
            type: String,
            default: ''
        },
        url: {
            type: String,
            default: ''
        },
        url_get_warehouse_types: {
            type: String,
            default: ''
        },
        url_get_articles: {
            type: String,
            default: ''
        },
    },
    data() {
        return {
            model: {
                company_id: '',
                warehouse_type_id: '',
                creation_date: this.current_date,
            },
            warehouse_types: [],
        }
    },
    created() {

    },
    mounted() {

    },
    watch: {
        'model.company_id': function (val) {

            EventBus.$emit('loading', true);

            axios.post(this.url_get_warehouse_types, {
                company_id: this.model.company_id
            }).then(response => {
                this.warehouse_types = response.data;
                EventBus.$emit('loading', false);
            }).catch(error => {
                console.log(error);
                console.log(error.response)
            });

        }
    },
    computed: {

    },
    methods: {
        viewArticles() {

            if (this.model.warehouse_type_id == '') {
                Swal.fire({
                    title: '¡Error!',
                    text: 'Debe seleccionar el Almacén.',
                    type: "error",
                    heightAuto: false,
                });
                return;
            }

            EventBus.$emit('loading', true);
            axios.post(this.url_get_articles, {
                warehouse_type_id: this.model.warehouse_type_id
            }).then(response => {
                EventBus.$emit('loading', false);

                EventBus.$emit('list_articles', response.data);

            }).catch(error => {
                console.log(error);
                console.log(error.response)
            });


        },
        formController: function (url, event) {
            var vm = this;

            var target = $(event.target);
            var url = url;
            var fd = new FormData(event.target);

            EventBus.$emit('loading', true);

            // EventBus.$emit('loading', true);
            axios.post(url, fd, {
                headers: {
                    'Content-type': 'application/x-www-form-urlencoded',
                }
            }).then(response => {
                console.log(response);
                EventBus.$emit('show_table', vm.model);
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
                        let p = c_target.parent().find('#' + i);
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