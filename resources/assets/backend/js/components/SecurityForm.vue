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
        <form class="kt-form" @submit.prevent="formController(url, $event)">
            <div class="kt-portlet__body">
                <div class="row">
                    <div class="col-lg-3">
                        <div class="form-group">
                            <label class="form-control-label">Nombre:</label>
                            <input type="text" class="form-control" name="name" id="name" v-model="model.name" @focus="$parent.clearErrorMsg($event)">
                            <div id="name-error" class="error invalid-feedback"></div>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="form-group">
                            <label class="form-control-label">Usuario:</label>
                            <input type="text" class="form-control" name="user" id="user" v-model="model.user" @focus="$parent.clearErrorMsg($event)">
                            <div id="user-error" class="error invalid-feedback"></div>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="form-group">
                            <label class="form-control-label">Contraseña:</label>
                            <input type="password" class="form-control" name="password" id="password" v-model="model.password" @focus="$parent.clearErrorMsg($event)">
                            <div id="password-error" class="error invalid-feedback"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="kt-portlet__foot">
                <div class="kt-form__actions">
                    <div class="row">
                        <div class="col-12 kt-align-right">
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
            url:{
                type: String,
                default:'',
            }
        },
        data() {
            return {
                model: {
                    name:'',
                    user:'',
                    password:''
                },
            }
        },
        created() {
            EventBus.$on('clearForm', function(model) {
                this.model.name='';
                this.model.user='';
                this.model.password='';
            }.bind(this));
        },
        mounted() {
        },
        watch: {
            
        },
        computed: {
        },
        methods: {
            formController: function(url, event) {
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
                    EventBus.$emit('refresh_table');
                    EventBus.$emit('clearForm');
                }).catch(error => {
                    EventBus.$emit('loading', false);
                    console.log(error.response);
                    var obj = error.response.data.errors;
                    $('html, body').animate({
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