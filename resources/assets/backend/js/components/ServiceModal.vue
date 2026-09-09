<template>
    <!--begin::Modal-->
    <div class="modal fade" id="modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <!--begin::Form-->
                <form class="kt-form" @submit.prevent="formController(url, $event)">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">{{ this.button_text }} Servicio</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="kt-portlet__body">
                            <div class="row">
                                <input type="hidden" name="service_id" id="service_id" v-model="model.service_id">
                                <input type="hidden" name="company_id" id="company_id" v-model="model.company_id">
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label class="form-control-label">Descripción</label>
                                        <input type="text" class="form-control" name="name" id="name" placeholder="Nombre del Servicio" v-model="model.name" @focus="$parent.clearErrorMsg($event)">
                                        <div id="name-error" class="error invalid-feedback"></div>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label class="form-control-label">Costo (4 decimales)</label>
                                        <input type="text" class="form-control" name="cost" id="cost" placeholder="Costo del Servicio" v-model="model.cost" @focus="$parent.clearErrorMsg($event)">
                                        <div id="cost-error" class="error invalid-feedback"></div>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label class="form-control-label">Precio (4 decimales)</label>
                                        <input type="text" class="form-control" name="price" id="price" placeholder="Precio del Servicio" v-model="model.price" @focus="$parent.clearErrorMsg($event)">
                                        <div id="price-error" class="error invalid-feedback"></div>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label class="form-control-label">Sede:</label>
                                        <select class="form-control" name="sede_id" id="sede_id" v-model="model.sede_id" @focus="$parent.clearErrorMsg($event)">
                                            <option value="" selected disabled>Seleccionar</option>
                                            <option v-for="sede in sedes" :value="sede.id" v-bind:key="sede.id">{{ sede.name }}</option>
                                        </select>
                                        <div id="sede_id-error" class="error invalid-feedback"></div>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label class="form-control-label">Área:</label>
                                        <select class="form-control" name="area_id" id="area_id" v-model="model.area_id" @focus="$parent.clearErrorMsg($event)">
                                            <option value="" selected disabled>Seleccionar</option>
                                            <option v-for="area in areas" :value="area.id" v-bind:key="area.id">{{ area.name }} - {{ area.short_name }}</option>
                                        </select>
                                        <div id="area_id-error" class="error invalid-feedback"></div>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label class="form-control-label">Stock:</label>
                                        <input type="text" class="form-control" name="stock_good" id="stock_good" v-model="model.stock_good" @focus="$parent.clearErrorMsg($event)">
                                        <div id="stock_good-error" class="error invalid-feedback"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success" id="add_article_2">
                            {{ button_text }}
                        </button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
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
        },
        data() {
            return {
                model: {
                    service_id:'',
                    company_id:'',
                    name: '',
                    cost:'',
                    price:'',
                    stock_good:'',
                    sede_id:'',
                    area_id:'',
                },
                button_text: '',
                areas:[],
                sedes:[],
            }
        },
        watch: {
            
        },
        computed: {
            
        },
        created() {
            EventBus.$on('create_modal', function() {
                
                this.button_text = 'Crear';
                this.model.service_id = '';
                this.model.name = '';
                this.model.cost = '';
                this.model.price = '' ;
                this.model.stock_good = '' ;
                this.model.sede_id = '';
                this.model.area_id = '';

                $('#modal').modal('show');
            }.bind(this));

            EventBus.$on('modal_company', function(data , company_id) {
                this.model.company_id=company_id;
                this.areas=data.areas;
                this.sedes=data.sedes;
            }.bind(this));

            EventBus.$on('edit_modal', function(model) {
                this.model = model;
                this.button_text = 'Actualizar';
                $('#modal').modal('show');
            }.bind(this));
        },
        mounted() {
            
        },
        methods: {
            formController: function(url, event) {
                var vm = this;

                var target = $(event.target);
                var url = url;
                var fd = new FormData(event.target);

                EventBus.$emit('loading', true);

                axios.post(url, fd, { headers: {
                        'Content-type': 'application/x-www-form-urlencoded',
                    }
                }).then(response => {
                    EventBus.$emit('loading', false);
                    $('#modal').modal('hide');
                    this.$parent.alertMsg(response.data);

                    this.model.service_id = '';
                    this.model.name = '';
                    this.model.cost = '';
                    this.model.price = '' ;
                    this.model.stock_good = '' ;
                    this.model.sede_id = '';
                    this.model.area_id = '';


                    EventBus.$emit('refresh_table');

                }).catch(error => {
                    EventBus.$emit('loading', false);
                    console.log(error.response);
                    var obj = error.response.data.errors;
                    $('.modal').animate({
                        scrollTop: 0
                    }, 500, 'swing');
                    $.each(obj, function(i, item) {
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