<template>

    <div class="fullscreen-container" v-show="login">
        <div class="kt-portlet">
            <div class="kt-portlet__head">
                <div class="kt-portlet__head-label">
                    <h3 class="kt-portlet__head-title">
                        Iniciar Sesión
                    </h3>
                </div>
            </div>

            <form class="kt-form kt-form-buscar" @submit.prevent="formController(url, $event)">
                <div class="kt-portlet__body">

                    <div class="mb-4">
                        <label class="form-control-label mb-3">RUTA:</label>
                        <div class="btn-group-container">
                            <button v-for="client_route in client_routes" :key="client_route.id" type="button"
                                :class="['btn', 'btn-lg', 'btn-option', model.client_route_id === client_route.id ? 'btn-primary' : 'btn-outline-primary']"
                                @click="model.client_route_id = client_route.id">
                                {{ client_route.name }}
                            </button>
                        </div>
                        <div id="client_route_id-error" class="error invalid-feedback"></div>
                    </div>

                    <div class="mb-4">
                        <label class="form-control-label">PEDIDO &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; PLACA
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; FECHA VENTA</label>
                        <select class="form-select form-select-lg custom-select" name="guia_id" id="guia_id"
                            v-model="model.guia_id" @focus="$parent.clearErrorMsg($event)">
                            <option value="" sekected disabled>Seleccionar</option>
                            <option v-for="guia in guias" :key="guia.id" :value="guia.id">
                                {{ guia.pedido }} &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; {{ guia.plate }}
                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; {{ guia.traslate_date }}
                            </option>
                        </select>
                        <div id="guia_id-error" class="error invalid-feedback"></div>
                    </div>

                    <div class="mb-4">
                        <label class="form-control-label">PIN:</label>
                        <input type="password" inputmode="numeric" class="form-control form-control-lg" name="pin"
                            id="pin" placeholder="123456" v-model="model.pin" @focus="$parent.clearErrorMsg($event)" />
                        <div id="pin-error" class="error invalid-feedback"></div>
                    </div>

                </div>

                <div class="kt-portlet__foot">
                    <div class="kt-form__actions text-center">
                        <button type="submit" class="btn btn-success btn-lg-acceder btn-block">
                            Acceder
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</template>

<script>
import Swal from 'sweetalert2';
import EventBus from '../event-bus';

export default {
    props: {
        url: {
            type: String,
            default: ''
        },
        url_get_guias: {
            type: String,
            default: ''
        },
        client_routes: {
            type: Array,
            default: ''
        },
    },
    data() {
        return {
            model: {
                client_route_id: '',
                pin: '',
                guia_id: ''
            },
            login: true,
            guias: []
        }
    },
    watch: {
        'model.client_route_id': function (val) {
            if (val != '') {
                EventBus.$emit('loading', true);

                axios.post(this.url_get_guias, {
                    route_id: this.model.client_route_id
                }).then(response => {

                    this.model.guia_id = '';
                    this.guias = [];
                    this.guias = response.data;

                    EventBus.$emit('loading', false);
                }).catch(error => {
                    console.log(error);
                    console.log(error.response)
                });
            } else {
                this.model.guia_id = '';
                this.guias = [];
            }
        },
    },
    methods: {
        formController(url, event) {
            const vm = this;
            const fd = new FormData();
            fd.append('client_route_id', vm.model.client_route_id);
            fd.append('pin', vm.model.pin);
            fd.append('guia_id', vm.model.guia_id);

            EventBus.$emit('loading', true);

            axios.post(url, fd, {
                headers: { 'Content-type': 'application/x-www-form-urlencoded' }
            }).then(response => {

                EventBus.$emit('loading', false);
                this.$parent.alertMsg(response.data);

                if (response.data.type == 1) {

                    if (response.data.temporal == 1) {
                        Swal.fire({
                            type: 'info',
                            title: 'Preventa',
                            text: 'La guia esta pre liquidado',
                        }).then(() => {
                            console.log('sales', response.data.sales);

                            EventBus.$emit('preventa_form_sale', response.data);
                            EventBus.$emit('open_cobranzas', response.data);
                            EventBus.$emit('update_preliquidation', response.data.sales);

                            this.login = false;
                        });
                    } else {
                        Swal.fire({
                            type: 'info',
                            title: 'Preventa',
                            text: 'La guia esta pendiente',
                        }).then(() => {
                            EventBus.$emit('preventa_form_sale', response.data);
                            EventBus.$emit('open_cobranzas', response.data);
                            this.login = false;
                        });

                    }

                }

            }).catch(error => {
                EventBus.$emit('loading', false);
                const obj = error.response.data.errors;
                $('.modal').animate({ scrollTop: 0 }, 500, 'swing');
                $.each(obj, function (i, item) {
                    let c_target = $("#" + i + "-error");
                    c_target.html(item).css('display', 'block');
                });
            });
        }
    }
};
</script>

<style scoped>
.fullscreen-container {
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    background: #f8f9fa;
    padding: 20px;
}

.kt-portlet {
    width: 100%;
    max-width: 800px;
    background: #fff;
    border-radius: 20px;
    box-shadow: 0px 8px 20px rgba(0, 0, 0, 0.1);
    padding: 20px;
}

.kt-portlet__head-title {
    font-size: 2.2rem;
    font-weight: bold;
    text-align: center;
    width: 100%;
}

.form-control-label {
    font-size: 1.4rem;
    font-weight: 600;
}

.btn-group-container {
    display: flex;
    flex-wrap: wrap;
    gap: 15px;
}

.btn-option {
    flex: 1 1 calc(16.66% - 15px);
    font-size: 1rem;
    padding: 1px;
    border-radius: 12px;
}

.form-control-lg {
    font-size: 1.6rem;
    padding: 20px;
    border-radius: 10px;
}

.btn-lg {
    font-size: 1rem;
    padding: 1px;
    border-radius: 12px;
    width: 100%;
}

.btn-lg-acceder {
    font-size: 2.8rem;
    padding: 4px;
    border-radius: 12px;
    width: 100%;
}

@media (max-width: 768px) {
    .btn-option {
        flex: 1 1 100%;
    }
}
</style>
