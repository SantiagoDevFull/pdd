<template>
    <!--begin::Modal-->
    <div class="modal fade" id="modal-client" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <!--begin::Form-->
                <form class="kt-form" @submit.prevent="formController(url, $event)">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">{{ this.button_text }} cliente</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        </button>
                    </div>

                    <div class="modal-body">
                        <div class="kt-portlet__body">
                            <div class="row">
                                <input type="hidden" name="id" id="id" v-model="model.id">
                                <input type="hidden" name="company_id" id="company_id" v-model="model.company_id">

                                <div class="col-lg-3">
                                    <div class="form-group">
                                        <label class="form-control-label">ID:</label>
                                        <input type="text" class="form-control" name="id" id="id" v-model="model.id"
                                            @focus="$parent.clearErrorMsg($event)" readonly="readonly">
                                        <div id="id-error" class="error invalid-feedback"></div>
                                    </div>
                                </div>

                                <div class="col-lg-3">
                                    <div class="form-group">
                                        <label class="form-control-label">Tipo de Documento:</label>
                                        <select class="form-control" name="document_type_id" id="document_type_id"
                                            v-model="model.document_type_id" @focus="$parent.clearErrorMsg($event)">
                                            <option value="">Seleccionar</option>
                                            <option v-for="document_type in document_types" :value="document_type.id"
                                                v-bind:key="document_type.id">{{ document_type.name }}</option>
                                        </select>
                                        <div id="document_type_id-error" class="error invalid-feedback"></div>
                                    </div>
                                </div>

                                <div class="col-lg-3">
                                    <div class="form-group">
                                        <label class="form-control-label">Documento Cliente:</label>

                                        <input type="text" class="form-control" name="document_number"
                                            id="document_number" placeholder="123456789" v-model="model.document_number"
                                            @input="model.document_number = model.document_number.replace(/[^0-9]/g, '')"
                                            @focus="$parent.clearErrorMsg($event)">
                                        <div id="document_number-error" class="error invalid-feedback"></div>
                                    </div>
                                </div>

                                <div class="col-lg-3">
                                    <div class="form-group">
                                        <label class="form-control-label">Grupo Cliente:</label>
                                        <input type="text" class="form-control" name="grupo" id="grupo" placeholder=""
                                            v-model="model.grupo" @focus="$parent.clearErrorMsg($event)">
                                        <div id="grupo-error" class="error invalid-feedback"></div>
                                    </div>
                                </div>

                                <div class="col-lg-3">
                                    <div class="form-group">
                                        <label class="form-control-label">Nombre o Razón Social:</label>
                                        <input type="text" class="form-control" name="business_name" id="business_name"
                                            placeholder="Empresa S.A.C." v-model="model.business_name"
                                            @focus="$parent.clearErrorMsg($event)">
                                        <div id="business_name-error" class="error invalid-feedback"></div>
                                    </div>
                                </div>

                                <div class="col-lg-3">
                                    <div class="form-group">
                                        <label class="form-control-label">Dirección:</label>
                                        <input type="text" class="form-control" name="address" id="address"
                                            placeholder="Av. Ejemplo 123" v-model="model.address"
                                            @focus="$parent.clearErrorMsg($event)">
                                        <div id="address-error" class="error invalid-feedback"></div>
                                    </div>
                                </div>

                                <div class="col-lg-3">
                                    <div class="form-group">
                                        <label class="form-control-label">Referencia:</label>
                                        <input type="text" class="form-control" name="address_reference"
                                            id="address_reference" placeholder="Av. Ejemplo 123"
                                            v-model="model.address_reference" @focus="$parent.clearErrorMsg($event)">
                                        <div id="address_reference-error" class="error invalid-feedback"></div>
                                    </div>
                                </div>

                                <div class="col-lg-3">
                                    <div class="form-group">
                                        <label class="form-control-label">Ubigeo:</label>
                                        <select class="form-control kt-select2" name="ubigeo_id" id="ubigeo_id"
                                            v-model="model.ubigeo_id" @focus="$parent.clearErrorMsg($event)">
                                            <option value="">Seleccionar</option>
                                        </select>
                                        <div id="ubigeo_id-error" class="error invalid-feedback"></div>
                                    </div>
                                </div>

                                <div class="col-lg-3">
                                    <div class="form-group">
                                        <label class="form-control-label">GPS X:</label>
                                        <input type="text" class="form-control" name="gps_x" id="gps_x"
                                            placeholder="12.18435168" v-model="model.gps_x"
                                            @focus="$parent.clearErrorMsg($event)">
                                        <div id="gps_x-error" class="error invalid-feedback"></div>
                                    </div>
                                </div>

                                <div class="col-lg-3">
                                    <div class="form-group">
                                        <label class="form-control-label">GPS Y:</label>
                                        <input type="text" class="form-control" name="gps_y" id="gps_y"
                                            placeholder="12.8754354" v-model="model.gps_y"
                                            @focus="$parent.clearErrorMsg($event)">
                                        <div id="gps_y-error" class="error invalid-feedback"></div>
                                    </div>
                                </div>

                                <div class="col-lg-3">
                                    <div class="form-group">
                                        <label class="form-control-label text-danger">Email de Supervisor
                                            Factura:</label>
                                        <select class="form-control" name="manager_mail" id="manager_mail"
                                            v-model="model.manager_mail" @focus="$parent.clearErrorMsg($event)">
                                            <option selected disabled value="">Seleccionar</option>
                                            <option value="anthonyvalencia@puntodedistribucion.com">
                                                anthonyvalencia@puntodedistribucion.com</option>
                                            <option value="mhuapaya@puntodedistribucion.com">
                                                mhuapaya@puntodedistribucion.com</option>
                                            <option value="raspillaga@puntodedistribucion.com">
                                                raspillaga@puntodedistribucion.com</option>
                                            <option value="acastillo@puntodedistribucion.com">
                                                acastillo@puntodedistribucion.com
                                            </option>
                                            <option value="aatahua@puntodedistribucion.com">
                                                aatahua@puntodedistribucion.com</option>
                                            <option value="malor@puntodedistribucion.com">
                                                malor@puntodedistribucion.com</option>
                                            <option value="edelgado@puntodedistribucion.com">
                                                edelgado@puntodedistribucion.com</option>
                                            <option value="amarin@puntodedistribucion.com">
                                                amarin@puntodedistribucion.com</option>
                                        </select>
                                        <div id="manager_mail-error" class="error invalid-feedback"></div>
                                    </div>
                                </div>


                                <div class="col-lg-3">
                                    <div class="form-group">
                                        <label class="form-control-label text-danger">Email de Cliente Factura:</label>
                                        <input type="text" class="form-control" name="email" id="email"
                                            placeholder="nombre@dominio.com" v-model="model.email"
                                            @focus="$parent.clearErrorMsg($event)">
                                        <div id="email-error" class="error invalid-feedback"></div>
                                    </div>
                                </div>

                                <div class="col-lg-3">
                                    <div class="form-group">
                                        <label class="form-control-label">Email 2:</label>
                                        <input type="text" class="form-control" name="email_2" id="email_2"
                                            placeholder="nombre@dominio.com" v-model="model.email_2"
                                            @focus="$parent.clearErrorMsg($event)">
                                        <div id="email_2-error" class="error invalid-feedback"></div>
                                    </div>
                                </div>

                                <div class="col-lg-3">
                                    <div class="form-group">
                                        <label class="form-control-label">Celular 1:</label>
                                        <input type="text" class="form-control" name="phone_number_1"
                                            id="phone_number_1" placeholder="987654321" v-model="model.phone_number_1"
                                            @focus="$parent.clearErrorMsg($event)">
                                        <div id="phone_number_1-error" class="error invalid-feedback"></div>
                                    </div>
                                </div>

                                <div class="col-lg-3">
                                    <div class="form-group">
                                        <label class="form-control-label">Celular 2:</label>
                                        <input type="text" class="form-control" name="phone_number_2"
                                            id="phone_number_2" placeholder="987654321" v-model="model.phone_number_2"
                                            @focus="$parent.clearErrorMsg($event)">
                                        <div id="phone_number_2-error" class="error invalid-feedback"></div>
                                    </div>
                                </div>



                                <div class="col-lg-3">
                                    <div class="form-group">
                                        <label class="form-control-label">Teléfono:</label>
                                        <input type="text" class="form-control" name="phone_number_3"
                                            id="phone_number_3" placeholder="014754512" v-model="model.phone_number_3"
                                            @focus="$parent.clearErrorMsg($event)">
                                        <div id="phone_number_3-error" class="error invalid-feedback"></div>
                                    </div>
                                </div>

                                <div class="col-lg-3">
                                    <div class="form-group">
                                        <label class="form-control-label">Contacto 1:</label>
                                        <input type="text" class="form-control" name="contact_name_1"
                                            id="contact_name_1" placeholder="Nombre Apellido"
                                            v-model="model.contact_name_1" @focus="$parent.clearErrorMsg($event)">
                                        <div id="contact_name_1-error" class="error invalid-feedback"></div>
                                    </div>
                                </div>

                                <div class="col-lg-3">
                                    <div class="form-group">
                                        <label class="form-control-label">Contacto 2:</label>
                                        <input type="text" class="form-control" name="contact_name_2"
                                            id="contact_name_2" placeholder="Nombre Apellido"
                                            v-model="model.contact_name_2" @focus="$parent.clearErrorMsg($event)">
                                        <div id="contact_name_2-error" class="error invalid-feedback"></div>
                                    </div>
                                </div>

                                <div class="col-lg-3">
                                    <div class="form-group">
                                        <label class="form-control-label">Contacto 3:</label>
                                        <input type="text" class="form-control" name="phone_number_4"
                                            id="phone_number_4" placeholder="987654321" v-model="model.phone_number_4"
                                            @focus="$parent.clearErrorMsg($event)">
                                        <div id="phone_number_4-error" class="error invalid-feedback"></div>
                                    </div>
                                </div>


                                <div class="col-lg-3">
                                    <div class="form-group">
                                        <label class="form-control-label">Vendedor:</label>
                                        <select class="form-control" name="seller_id" id="seller_id"
                                            v-model="model.seller_id" @focus="$parent.clearErrorMsg($event)">
                                            <option disabled value="">Seleccionar</option>
                                            <option value="OFICINA">Oficina</option>
                                            <option value="DIESTRA EDWARD">DIESTRA EDWARD</option>
                                            <option value="BLACIDO ABEL">BLACIDO ABEL</option>
                                            <option value="MONTAÑEZ EZEQUIEL">MONTAÑEZ EZEQUIEL</option>
                                            <option value="ESTRADA FAUSTINO">ESTRADA FAUSTINO</option>
                                            <option value="RONCAL FRANKLIN">RONCAL FRANKLIN</option>
                                            <option value="MAYTA  EDMARTIN">MAYTA EDMARTIN</option>
                                            <option value="TRUJILLO JUAN ">TRUJILLO JUAN </option>
                                            <option value="RONCAL  ROOSEVELT">RONCAL ROOSEVELT</option>
                                            <option value="FLORES  JORGUE">FLORES JORGUE</option>
                                            <option value="PAZ HENRY ">PAZ HENRY </option>
                                            <option value="GUERRERO JORHDY">GUERRERO JORHDY</option>
                                            <option value="CERVANTES JUAN">CERVANTES JUAN</option>
                                            <option value="OLIVERA JULIO">OLIVERA JULIO</option>
                                            <option value="SILVIA DAREK">SILVIA DAREK</option>
                                            <option value="MORALES EDUARDO">MORALES EDUARDO</option>
                                            <option value="SAAVEDRA ABRAHAN">SAAVEDRA ABRAHAN</option>
                                            <option value="GUERRERO RAMON">GUERRERO RAMON</option>
                                            <option value="FABIAN WALTER">FABIAN WALTER</option>
                                            <option value="ASENCIOS POMPEYO">ASENCIOS POMPEYO</option>
                                            <option value="MUNAYA JOAQUIN">MUNAYA JOAQUIN</option>
                                            <option value="BARRETO  ANDRES">BARRETO ANDRES</option>
                                            <option value="CAYPANE SANTOS">CAYPANE SANTOS</option>
                                            <option value="AMAZIFUEN FRANK ">AMAZIFUEN FRANK </option>
                                            <option value="IPANAQUE  JULIO ">IPANAQUE JULIO </option>
                                            <option value="ALCANTARA COBAEDINSON">ALCANTARA COBAEDINSON</option>
                                            <option value="SOJO RETO">SOJO RETO</option>
                                            <option value="CRUZ CRISTOFER ">CRUZ CRISTOFER </option>
                                            <option value="MECHAN MILAGROS ">MECHAN MILAGROS </option>
                                            <option value="HUMPIRE FRANCHESKA ">HUMPIRE FRANCHESKA </option>
                                            <option value="HUAPAYA MILUSKA">HUAPAYA MILUSKA</option>
                                            <option value="VALENCIA ANTHONY">VALENCIA ANTHONY</option>
                                            <option value="CHRISTIAN HUAMANI ">CHRISTIAN HUAMANI</option>
                                            <option value="ANGEL CASTILLO">ANGEL CASTILLO</option>
                                            <option value="VENEGAS DAVID">VENEGAS DAVID</option>
                                            <option value="ATAHUA ALBERTO">ATAHUA ALBERTO</option>
                                            <option value="ALOR MASSIEL">ALOR MASSIEL</option>
                                            <option value="HUANILO PATRICIA">PATRICIA HUANILO</option>
                                            <option value="OFICINA PLANTA">OFICINA PLANTA</option>
                                            <option value="E. Morales">E. Morales</option>
                                            <option value="J. Nieves">J. Nieves</option>
                                            <option value="R. Aspillaga">R. Aspillaga</option>


                                        </select>
                                        <div id="seller_id-error" class="error invalid-feedback"></div>
                                    </div>
                                </div>

                                <div class="col-lg-3">
                                    <div class="form-group">
                                        <label class="form-control-label">DGH:</label>
                                        <input type="text" class="form-control" name="dgh" id="dgh" placeholder=""
                                            v-model="model.dgh" @focus="$parent.clearErrorMsg($event)">
                                        <div id="dgh-error" class="error invalid-feedback"></div>
                                    </div>
                                </div>

                                <div class="col-lg-3">
                                    <div class="form-group">
                                        <label class="form-control-label">Poliza:</label>
                                        <input type="text" class="form-control" name="police" id="police" placeholder=""
                                            v-model="model.police" @focus="$parent.clearErrorMsg($event)">
                                        <div id="police-error" class="error invalid-feedback"></div>
                                    </div>
                                </div>


                                <div class="col-lg-3">
                                    <div class="form-group">
                                        <label class="form-control-label">Tipo de Agente:</label>
                                        <select class="form-control" name="activity_id" id="activity_id"
                                            v-model="model.activity_id" @focus="$parent.clearErrorMsg($event)">
                                            <option value="">Seleccionar</option>
                                            <option value="1">Agente con Registro</option>
                                            <option value="2">Usuario Final Sin Registro</option>
                                        </select>
                                        <div id="activity_id-error" class="error invalid-feedback"></div>
                                    </div>
                                </div>



                                <div class="col-lg-3">
                                    <div class="form-group">
                                        <label class="form-control-label">Área:</label>
                                        <select class="form-control" name="zone_id" id="zone_id" v-model="model.zone_id"
                                            @focus="$parent.clearErrorMsg($event)">
                                            <option value="">Seleccionar</option>
                                            <option v-for="zone in client_zones" :value="zone.id" v-bind:key="zone.id">
                                                {{ zone.name }}</option>
                                        </select>
                                        <div id="zone_id-error" class="error invalid-feedback"></div>
                                    </div>
                                </div>

                                <div class="col-lg-3">
                                    <div class="form-group">
                                        <label class="form-control-label text-danger">Unidad:</label>
                                        <select class="form-control" name="business_unit_id" id="business_unit_id"
                                            v-model="model.business_unit_id" @focus="$parent.clearErrorMsg($event)">
                                            <option value="" selected disabled>Seleccionar</option>
                                            <option v-for="business_unit in business_units" :value="business_unit.id"
                                                v-bind:key="business_unit.id">{{ business_unit.name }}</option>
                                        </select>
                                        <div id="business_unit_id-error" class="error invalid-feedback"></div>
                                    </div>
                                </div>


                                <div class="col-lg-3">
                                    <div class="form-group">
                                        <label class="form-control-label text-danger">Canal:</label>
                                        <select class="form-control" name="sector_id" id="sector_id"
                                            v-model="model.sector_id" @focus="$parent.clearErrorMsg($event)">
                                            <option value="" selected disabled>Seleccionar</option>
                                            <option v-for="sector in client_sectors" :value="sector.id"
                                                v-bind:key="sector.id">{{ sector.name }}</option>
                                        </select>
                                        <div id="sector_id-error" class="error invalid-feedback"></div>
                                    </div>
                                </div>


                                <div class="col-lg-3">
                                    <div class="form-group">
                                        <label class="form-control-label text-danger">Sub Canal:</label>
                                        <select class="form-control" name="channel_id" id="channel_id"
                                            v-model="model.channel_id" @focus="$parent.clearErrorMsg($event)">
                                            <option value="" selected disabled>Seleccionar</option>
                                            <option v-for="channel in client_channels" :value="channel.id"
                                                v-bind:key="channel.id">{{ channel.name }}</option>
                                        </select>
                                        <div id="channel_id-error" class="error invalid-feedback"></div>
                                    </div>
                                </div>

                                <div class="col-lg-3">
                                    <div class="form-group">
                                        <label class="form-control-label text-danger">Ruta:</label>
                                        <select class="form-control" name="route_id" id="route_id"
                                            v-model="model.route_id" @focus="$parent.clearErrorMsg($event)">
                                            <option value="" selected disabled>Seleccionar</option>
                                            <option v-for="route in client_routes" :value="route.id"
                                                v-bind:key="route.id">{{ route.name }}</option>
                                        </select>
                                        <div id="route_id-error" class="error invalid-feedback"></div>
                                    </div>
                                </div>

                                <div class="col-lg-3">
                                    <div class="form-group">
                                        <label class="form-control-label">Negocio:</label>
                                        <select class="form-control" name="client_type" id="client_type"
                                            v-model="model.client_type" @focus="$parent.clearErrorMsg($event)">
                                            <option value="">Seleccionar</option>
                                            <option v-for="client_type in client_types" :value="client_type.id"
                                                v-bind:key="client_type.id">{{ client_type.name }}</option>
                                        </select>
                                        <div id="client_type-error" class="error invalid-feedback"></div>
                                    </div>
                                </div>

                                <div class="col-lg-3">
                                    <div class="form-group">
                                        <label class="form-control-label">Sector:</label>
                                        <input type="text" class="form-control" name="sector" id="sector"
                                            placeholder="Sector" v-model="model.sector"
                                            @focus="$parent.clearErrorMsg($event)">
                                        <div id="sector-error" class="error invalid-feedback"></div>
                                    </div>
                                </div>

                                <div class="col-lg-3">
                                    <div class="form-group">
                                        <label class="form-control-label">Zona:</label>
                                        <input type="text" class="form-control" name="zona" id="zona" placeholder="Zona"
                                            v-model="model.zona" @focus="$parent.clearErrorMsg($event)">
                                        <div id="zona-error" class="error invalid-feedback"></div>
                                    </div>
                                </div>

                                <div class="col-lg-3">
                                    <div class="form-group">
                                        <label class="form-control-label">Frecuencia Cantidad:</label>
                                        <input type="text" class="form-control" name="frecuencia" id="frecuencia"
                                            placeholder="Frecuencia" v-model="model.frecuencia"
                                            @focus="$parent.clearErrorMsg($event)">
                                        <div id="frecuencia-error" class="error invalid-feedback"></div>
                                    </div>
                                </div>


                                <div class="col-lg-3">
                                    <div class="form-group">
                                        <label class="form-control-label">Frecuencia Tiempo:</label>
                                        <select class="form-control" name="frecuencia_id" id="frecuencia_id"
                                            v-model="model.frecuencia_id" @focus="$parent.clearErrorMsg($event)">
                                            <option disabled value="">Seleccionar</option>
                                            <option value="Diario">Diario</option>
                                            <option value="Semanal">Semanal</option>
                                            <option value="Quincenal">Quincenal</option>
                                            <option value="Mensual">Mensual</option>
                                            <option value="Interdiario">Interdiario</option>
                                            <option value="2 x Semana">2 x Semana</option>
                                        </select>
                                        <div id="frecuencia_id-error" class="error invalid-feedback"></div>
                                    </div>
                                </div>

                                <div class="col-lg-3">
                                    <div class="form-group">
                                        <label class="form-control-label">Potencial (Cilindros):</label>
                                        <input type="text" class="form-control" name="potencial" id="potencial"
                                            placeholder="Solo números enteros" v-model="model.potencial"
                                            @focus="$parent.clearErrorMsg($event)">
                                        <div id="potencial-error" class="error invalid-feedback"></div>
                                    </div>
                                </div>



                                <div class="col-lg-3">
                                    <div class="form-group">
                                        <label class="form-control-label">Estado:</label>
                                        <select class="form-control" name="estado" id="estado" v-model="model.estado"
                                            @focus="$parent.clearErrorMsg($event)">
                                            <option disabled value="">Seleccionar</option>
                                            <option value="Duplicado">Duplicado</option>
                                            <option value="No tiene información">No tiene información</option>
                                            <option value="Cerrado">Cerrado</option>
                                            <option value="Traspaso Negocio">Traspaso Negocio</option>
                                            <option value="Gas Natural">Gas Natural</option>
                                        </select>
                                        <div id="estado-error" class="error invalid-feedback"></div>
                                    </div>
                                </div>

                                <div class="col-lg-3">
                                    <div class="form-group">
                                        <label class="form-control-label">Código Repetido:</label>
                                        <input type="text" class="form-control" name="codigo_rep" id="codigo_rep"
                                            placeholder="codigo" v-model="model.codigo_rep"
                                            @focus="$parent.clearErrorMsg($event)">
                                        <div id="codigo_rep-error" class="error invalid-feedback"></div>
                                    </div>
                                </div>

                                <div class="col-lg-3">
                                    <div class="form-group">
                                        <label class="form-control-label">Supervisor:</label>
                                        <select class="form-control" name="manager_id" id="manager_id"
                                            v-model="model.manager_id" @focus="$parent.clearErrorMsg($event)">
                                            <option disabled value="">Seleccionar</option>
                                            <option v-for="manager in managers" :value="manager.id"
                                                v-bind:key="manager.id">{{ manager.name }}</option>

                                        </select>
                                        <div id="manager_id-error" class="error invalid-feedback"></div>
                                    </div>
                                </div>


                                <div class="col-lg-3">
                                    <div class="form-group">
                                        <label class="form-control-label">Condición de Pago:</label>
                                        <select class="form-control" name="payment_id" id="payment_id"
                                            v-model="model.payment_id" @focus="$parent.clearErrorMsg($event)">
                                            <option value="">Seleccionar</option>
                                            <option v-for="payment in payments" :value="payment.id"
                                                v-bind:key="payment.id">{{ payment.name }}</option>
                                        </select>
                                        <div id="payment_id-error" class="error invalid-feedback"></div>
                                    </div>
                                </div>

                                <div class="col-lg-3" v-if="model.payment_id == 2">
                                    <div class="form-group">
                                        <label class="form-control-label">Límite de Crédito:</label>
                                        <input type="number" class="form-control" name="credit_limit" id="credit_limit"
                                            placeholder="1000" step="any" min="0" v-model="model.credit_limit"
                                            @focus="$parent.clearErrorMsg($event)">
                                        <div id="credit_limit-error" class="error invalid-feedback"></div>
                                    </div>
                                </div>
                                <div class="col-lg-3" v-if="model.payment_id == 2">
                                    <div class="form-group">
                                        <label class="form-control-label">Días de Crédito:</label>
                                        <input type="number" class="form-control" name="credit_limit_days"
                                            id="credit_limit_days" step="1" min="0" placeholder="10"
                                            v-model="model.credit_limit_days" @focus="$parent.clearErrorMsg($event)">
                                        <div id="credit_limit_days-error" class="error invalid-feedback"></div>
                                    </div>
                                </div>
                                <div class="col-lg-3">
                                    <div class="form-group">
                                        <label class="form-control-label">Percepción:</label>
                                        <select class="form-control" name="perception_percentage_id"
                                            id="perception_percentage_id" v-model="model.perception_percentage_id"
                                            @focus="$parent.clearErrorMsg($event)">
                                            <option value="">Seleccionar</option>
                                            <option v-for="perception in perceptions" :value="perception.id"
                                                v-bind:key="perception.id">{{ perception.value }}%</option>
                                        </select>
                                        <div id="perception_percentage_id-error" class="error invalid-feedback"></div>
                                    </div>
                                </div>

                                <div class="col-lg-3">
                                    <div class="form-group">
                                        <label class="form-control-label">Observaciones:</label>
                                        <input type="text" class="form-control" name="obs" id="business_type"
                                            placeholder="" v-model="model.obs" @focus="$parent.clearErrorMsg($event)">
                                        <div id="obs-error" class="error invalid-feedback"></div>
                                    </div>
                                </div>

                                <div class="col-lg-3">
                                    <div class="form-group">
                                        <label class="form-control-label">Clase:</label>
                                        <select class="form-control" name="clase" id="clase" v-model="model.clase"
                                            @focus="$parent.clearErrorMsg($event)">
                                            <option disabled value="">Seleccionar</option>
                                            <option value="A">A</option>
                                            <option value="B">B</option>
                                            <option value="C">C</option>
                                            <option value="D">D</option>
                                        </select>
                                        <div id="clase-error" class="error invalid-feedback"></div>
                                    </div>
                                </div>

                                <div class="col-lg-3">
                                    <div class="form-group">
                                        <label class="form-control-label">Tipo:</label>
                                        <select class="form-control" name="tipo" id="tipo" v-model="model.tipo"
                                            @focus="$parent.clearErrorMsg($event)">
                                            <option disabled value="">Seleccionar</option>
                                            <option value="Minorista">Minorista</option>
                                            <option value="Mayorista">Mayorista</option>
                                            <option value="Local de Venta">Local de Venta</option>
                                            <option value="EESS">EESS</option>
                                        </select>
                                        <div id="tipo-error" class="error invalid-feedback"></div>
                                    </div>
                                </div>

                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label class="form-control-label">Activo o Inactivo:</label>
                                        <select class="form-control" name="estado_2" id="estado_2" v-model="model.estado_2"
                                            @focus="$parent.clearErrorMsg($event)">
                                            <option selected disabled value="">Seleccionar</option>
                                            <option value="ACTIVO">ACTIVO</option>
                                            <option value="INACTIVO">INACTIVO</option>
                                        </select>
                                        <div id="estado_2-error" class="error invalid-feedback"></div>
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
import Swal from 'sweetalert2';
import EventBus from '../event-bus';

export default {
    props: {
        url: {
            type: String,
            default: ''
        },
        client_sectors: {
            type: Array,
            default: ''
        },
        client_channels: {
            type: Array,
            default: ''
        },
        client_routes: {
            type: Array,
            default: ''
        },
        url_get_client_sectors: {
            type: String,
            default: ''
        },
        url_get_client_channels: {
            type: String,
            default: ''
        },
        url_get_client_routes: {
            type: String,
            default: ''
        },
        url_get_ubigeos: {
            type: String,
            default: ''
        },
        url_get_clients: {
            type: String,
            default: ''
        },
        url_get_select2: {
            type: String,
            default: ''
        },
        document_types: {
            type: Array,
            default: ''
        },
        payments: {
            type: Array,
            default: ''
        },
        perceptions: {
            type: Array,
            default: ''
        },
        business_units: {
            type: Array,
            default: ''
        },
        client_zones: {
            type: Array,
            default: ''
        },
        client_types: {
            type: Array,
            default: ''
        },
        osi_agents: {
            type: Array,
            default: ''
        },
        managers: {
            type: Array,
            default: ''
        },
    },
    data() {
        return {
            model: {
                id: '',
                company_id: '',
                document_type_id: '',
                document_number: '',
                client_code: '',
                business_name: '',
                address: '',
                address_reference: '',
                ubigeo_id: '',
                gps_x: '',
                gps_y: '',
                contact_name_1: '',
                contact_name_2: '',
                email: '',
                email_2: '',
                phone_number_1: '',
                phone_number_2: '',
                phone_number_3: '',
                phone_number_4: '',
                link_client_id: '',
                business_unit_id: '',
                zone_id: '',
                channel_id: '',
                route_id: '',
                sector_id: '',
                seller_id: '',
                client_type: '',
                grupo: '',
                estado: '',
                codigo_rep: '',
                dgh: '',
                activity_uno: '',
                activity_id: '',
                activity_tres: '',
                manager_id: '',
                manager_mail: '',
                police: '',
                payment_id: '',
                credit_limit: '',
                credit_limit_days: '',
                perception_percentage_id: '',
                obs: '',
                frecuencia_id: '',
                frecuencia: '',
                potencial: '',
                sector: '',
                zona: '',
                clase: '',
                tipo: '',
                estado_2: '',
            },
            button_text: '',
        }
    },
    watch: {

    },
    computed: {

    },
    created() {
        EventBus.$on('create_modal', function (company_id) {

            this.button_text = 'Crear';

            this.model.id = '';
            this.model.company_id = company_id;
            this.model.document_type_id = '';
            this.model.document_number = '';
            this.model.client_code = '';
            this.model.business_name = '';
            this.model.address = '';
            this.model.address_reference = '';
            this.model.ubigeo_id = '';
            this.model.gps_x = '';
            this.model.gps_y = '';
            this.model.contact_name_1 = '';
            this.model.contact_name_2 = '';
            this.model.email = '';
            this.model.email_2 = '';
            this.model.phone_number_1 = '';
            this.model.phone_number_2 = '';
            this.model.phone_number_3 = '';
            this.model.phone_number_4 = '';
            this.model.link_client_id = '';
            this.model.business_unit_id = '';
            this.model.zone_id = '';
            this.model.channel_id = '';
            this.model.route_id = '';
            this.model.sector_id = '';
            this.model.seller_id = '';
            this.model.client_type = '';
            this.model.dgh = '';
            this.model.activity_uno = '';
            this.model.activity_id = '';
            this.model.activity_tres = '';
            this.model.grupo = '';
            this.model.estado = '';
            this.model.codigo_rep = '';
            this.model.manager_id = '';
            this.model.police = '';
            this.model.payment_id = '';
            this.model.credit_limit = '';
            this.model.credit_limit_days = '';
            this.model.obs = '';
            this.model.potencial = '';
            this.model.perception_percentage_id = '';
            this.model.frecuencia_id = '';
            this.model.frecuencia = '';
            this.model.sector = '';
            this.model.zona = '';
            this.model.clase = '';
            this.model.tipo = '';
            this.model.estado_2 = '';


            $('#ubigeo_id').val(null).trigger('change');
            $('#link_client_id').val(null).trigger('change');
            $('#modal-client').modal('show');
        }.bind(this));

        EventBus.$on('edit_modal', function (model) {
            this.model = model;
            this.model.client_code = model.code;
            this.button_text = 'Actualizar';

            axios.post(this.url_get_select2, {
                ubigeo_id: this.model.ubigeo_id,
                link_client_id: this.model.link_client_id,
            }).then(response => {
                if (response.data.ubigeo) {
                    let option = new Option(response.data.ubigeo.text, response.data.ubigeo.id, true, true);
                    $('#ubigeo_id').append(option).trigger('change');
                    $('#ubigeo_id').trigger({
                        type: 'select2:select',
                        params: {
                            data: response.data.ubigeo
                        }
                    });
                }

                if (response.data.link_client) {
                    let option = new Option(response.data.link_client.text, response.data.link_client.id, true, true);
                    $('#link_client_id').append(option).trigger('change');
                    $('#link_client_id').trigger({
                        type: 'select2:select',
                        params: {
                            data: response.data.link_client
                        }
                    });
                }

                EventBus.$emit('loading', false);
                $('#modal-client').modal('show');
                console.log(response);
            }).catch(error => {
                console.log(error.response);
                console.log(error.response);
            });
        }.bind(this));
    },
    mounted() {
        this.newSelect2();
    },
    methods: {
        newSelect2: function () {
            let vm = this;
            let token = document.head.querySelector('meta[name="csrf-token"]').content;
            $("#ubigeo_id").select2({
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
                    url: this.url_get_ubigeos,
                    dataType: 'json',
                    delay: 250,
                    type: 'POST',
                    data: function (params) {
                        var queryParameters = {
                            q: params.term,
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
                minimumInputLength: 0,
            }).on('select2:select', function (e) {
                var selected_element = $(e.currentTarget);
                vm.model.ubigeo_id = parseInt(selected_element.val());
            }).on('select2:unselect', function (e) {
                vm.model.ubigeo_id = '';
            });

            $("#link_client_id").select2({
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
                    url: this.url_get_clients,
                    dataType: 'json',
                    delay: 250,
                    type: 'POST',
                    data: function (params) {
                        var queryParameters = {
                            q: params.term,
                            company_id: vm.model.company_id,
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
                vm.model.link_client_id = parseInt(selected_element.val());
            }).on('select2:unselect', function (e) {
                vm.model.link_client_id = '';
            });
        },
        formController: function (url, event) {

            if (this.model.document_type_id == 1 && this.model.document_number.length != 11) // RUC
            {
                Swal.fire('¡Error!', 'El RUC debe tener 11 dígitos', 'error');
                return false;
            }

            if (this.model.document_type_id == 2 && this.model.document_number.length != 8) // DNI
            {
                Swal.fire('¡Error!', 'El DNI debe tener 8 dígitos', 'error');
                return false;
            }


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
                $('#modal-client').modal('hide');
                this.$parent.alertMsg(response.data);

                this.model.id = '';
                //this.model.company_id = '';
                this.model.document_type_id = '';
                this.model.document_number = '';
                this.model.client_code = '';
                this.model.business_name = '';
                this.model.address = '';
                this.model.address_reference = '';
                this.model.ubigeo_id = '';
                this.model.gps_x = '';
                this.model.gps_y = '';
                this.model.contact_name_1 = '';
                this.model.contact_name_2 = '';
                this.model.email = '';
                this.model.email_2 = '';
                this.model.phone_number_1 = '';
                this.model.phone_number_2 = '';
                this.model.phone_number_3 = '';
                this.model.phone_number_4 = '';
                this.model.link_client_id = '';
                this.model.business_unit_id = '';
                this.model.zone_id = '';
                this.model.channel_id = '';
                this.model.route_id = '';
                this.model.sector_id = '';
                this.model.seller_id = '';
                this.model.client_type = '';
                this.model.dgh = '';
                this.model.activity_uno = '';
                this.model.activity_id = '';
                this.model.activity_tres = '';
                this.model.grupo = '';
                this.model.estado = '';
                this.model.codigo_rep = '';
                this.model.manager_id = '';
                this.model.police = '';
                this.model.payment_id = '';
                this.model.credit_limit = '';
                this.model.credit_limit_days = '';
                this.model.perception_percentage_id = '';
                this.model.obs = '';
                this.model.frecuencia_id = '';
                this.model.frecuencia = '';
                this.model.potencial = '';
                this.model.sector = '';
                this.model.zona = '';
                this.model.clase = '';
                this.model.tipo = '';
                this.model.estado_2 = '';

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
