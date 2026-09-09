<template>
    <!--begin::Modal-->
    <div class="modal fade" id="modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <!--begin::Form-->
                <form class="kt-form" @submit.prevent="formController(url, $event)">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">{{ this.button_text }}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="kt-portlet__body">
                            <div class="row">
                                <input type="hidden" name="udid" id="udid" v-model="model.udid">
                                <input type="hidden" name="mes" id="mes" v-model="model.mes">
                                <input type="hidden" name="año" id="año" v-model="model.año">


                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label class="form-control-label">TM PPTO</label>
                                        <input type="text" class="form-control" name="tm_ppto" id="tm_ppto"
                                            v-model="model.tm_ppto" @focus="$parent.clearErrorMsg($event)">
                                        <div id="tm_ppto-error" class="error invalid-feedback"></div>
                                    </div>
                                </div>

                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label class="form-control-label">MB PPTO</label>
                                        <input type="text" class="form-control" name="mb_ppto" id="mb_ppto"
                                            v-model="model.mb_ppto" @focus="$parent.clearErrorMsg($event)">
                                        <div id="mb_ppto-error" class="error invalid-feedback"></div>
                                    </div>
                                </div>



                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">
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
import Datetime from 'vue-datetime';
// You need a specific loader for CSS files
import 'vue-datetime/dist/vue-datetime.css';

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
                tm_ppto: '',
                mb_ppto: '',
            },
            button_text: ''
        }
    },
    watch: {

    },
    computed: {

    },
    created() {
        EventBus.$on('create_modal', function () {
            this.button_text = 'Crear';
            this.model = {
                tm_ppto: '',
                mb_ppto: '',

            };

            $('#modal').modal('show');
        }.bind(this));

        EventBus.$on('edit_modal', function (model) {
            this.model = model;
            this.button_text = 'Actualizar';
            $('#modal').modal('show');
        }.bind(this));
    },
    mounted() {

    },
    methods: {
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
                EventBus.$emit('loading', false);
                $('#modal').modal('hide');
                this.$parent.alertMsg(response.data);

                this.model = {
                    tm_ppto: '',
                    mb_ppto: '',
                };

                EventBus.$emit('refresh_table');

            }).catch(error => {
                EventBus.$emit('loading', false);
                console.log(error.response);
                var obj = error.response.data.errors;
                $('.modal').animate({
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