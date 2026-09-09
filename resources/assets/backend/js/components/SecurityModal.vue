<template>
    <!--begin::Modal-->
    <div class="modal fade" id="modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <!--begin::Form-->
                <form class="kt-form" @submit.prevent="formController(url_update, $event)">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Actualizar Usuario</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="kt-portlet__body">
                            <div class="row">
                                <input type="hidden" name="id" id="id" v-model="model.id">
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label class="form-control-label">Nombre:</label>
                                        <input type="text" class="form-control" name="name" id="name" v-model="model.name" @focus="$parent.clearErrorMsg($event)">
                                        <div id="name-error" class="error invalid-feedback"></div>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label class="form-control-label">Usuario::</label>
                                        <input type="text" class="form-control" name="user" id="user" v-model="model.user" @focus="$parent.clearErrorMsg($event)">
                                        <div id="user-error" class="error invalid-feedback"></div>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label class="form-control-label">Contraseña:</label>
                                        <input type="text" class="form-control" name="password" id="password" v-model="model.password" @focus="$parent.clearErrorMsg($event)">
                                        <div id="password-error" class="error invalid-feedback"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success" id="update_user">
                            Actualizar
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
            url_update: {
                type: String,
                default: ''
            },
        },
        data() {
            return {
                model: {
                    id:'',
                    user:'',
                    name: '',
                    password:'',
                },
            }
        },
        watch: {
            
        },
        computed: {
            
        },
        created() {
            EventBus.$on('edit_modal', function(model) {
                this.model = model;
                $('#modal').modal('show');
            }.bind(this));
            EventBus.$on('clear_modal', function(model) {
                this.model.id = '';
                this.model.name= '';
                this.model.user= '';
                this.model.password= '';
            }.bind(this));
        },
        mounted() {
            
        },
        methods: {
            formController: function(url_update, event) {
                var vm = this;

                var target = $(event.target);
                var url = url_update;
                var fd = new FormData(event.target);

                EventBus.$emit('loading', true);

                axios.post(url, fd, { headers: {
                        'Content-type': 'application/x-www-form-urlencoded',
                    }
                }).then(response => {
                    EventBus.$emit('loading', false);
                    $('#modal').modal('hide');
                    this.$parent.alertMsg(response.data);

                    this.model.id = '';
                    this.model.name = '';
                    this.model.user = '';
                    this.model.password = '' ;

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