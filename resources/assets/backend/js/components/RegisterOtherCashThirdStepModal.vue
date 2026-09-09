<template>
	<!--begin::Modal-->
	<div class="modal fade" id="modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
		aria-hidden="true">
		<div class="modal-dialog modal-dialog-centered modal-lg" role="document">
			<div class="modal-content">
				<!--begin::Form-->
				<form class="kt-form" @submit.prevent="sendFormSale()">
					<div class="modal-header">
						<h5 class="modal-title" id="exampleModalLabel">Agregar item</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						</button>
					</div>
					<div class="modal-body">
						<div class="kt-portlet__body">
							<div class="row">
								<div class="col-lg-4">
									<div class="form-group">
										<label class="form-control-label">Concepto:</label>
										<select class="form-control" name="concept" id="concept" v-model="model.concept"
											@focus="$parent.clearErrorMsg($event)">
											<option value="" selected disabled>Seleccionar</option>
											<option value="CILINDROS">CILINDROS</option>
											<option value="ESTIBA">ESTIBA</option>
											<option value="SERVICIO REPARACIÓN">SERVICIO REPARACIÓN</option>
											<option value="PAGO CUOTA">PAGO CUOTA</option>
											<option value="REPARACIÓN DE CILINDROS">REPARACIÓN DE CILINDROS</option>
											<option value="VAVULA FISHER 10K">VAVULA FISHER 10K</option>
											<option value="VAVULA PREMIUM 10K">VAVULA PREMIUM 10K</option>
										</select>
										<div id="concept-error" class="error invalid-feedback"></div>
									</div>
								</div>
								<div class="col-lg-4">
									<div class="form-group">
										<label class="form-control-label">Unidad de Medida:</label>
										<select class="form-control" name="unit_id" id="unit_id" v-model="model.unit_id"
											@focus="$parent.clearErrorMsg($event)">
											<option value="">Seleccionar</option>
											<option v-for="unit in units" :value="unit.id" v-bind:key="unit.id">{{
												unit.name }}</option>
										</select>
										<div id="unit_id-error" class="error invalid-feedback"></div>
									</div>
								</div>
								<div class="col-lg-4">
									<div class="form-group">
										<label class="form-control-label">Cantidad:</label>
										<input type="number" class="form-control" name="quantity" id="quantity"
											placeholder="0" step="any" min="0.0000" v-model="model.quantity"
											@focus="$parent.clearErrorMsg($event)">
										<div id="quantity-error" class="error invalid-feedback"></div>
									</div>
								</div>
								<div class="col-lg-4">
									<div class="form-group">
										<label class="form-control-label">Precio Unitario:</label>
										<input type="number" class="form-control" name="price_igv" id="price_igv"
											placeholder="0" step="any" min="0.0000" v-model="model.price_igv"
											@focus="$parent.clearErrorMsg($event)">
										<div id="price_igv-error" class="error invalid-feedback"></div>
									</div>
								</div>
								<div class="col-lg-4">
									<div class="form-group">
										<label class="form-control-label">Tipo de Venta:</label>
										<select class="form-control" name="value_type_id" id="value_type_id"
											v-model="model.value_type_id" @focus="$parent.clearErrorMsg($event)">
											<option value="" selected disabled>Seleccionar</option>
											<option v-for="value_type in value_types" :value="value_type.id"
												v-bind:key="value_type.id">{{ value_type.name }}</option>
										</select>
										<div id="value_type_id-error" class="error invalid-feedback"></div>
									</div>
								</div>
								<div class="col-lg-4">
									<div class="form-group">
										<label class="form-control-label">Valor:</label>
										<input type="number" class="form-control" name="sale_value" id="sale_value"
											readonly placeholder="0" step="any" min="0.0000" v-model="model.sale_value"
											@focus="$parent.clearErrorMsg($event)">
										<div id="sale_value-error" class="error invalid-feedback"></div>
									</div>
								</div>
								<div class="col-lg-4">
									<div class="form-group">
										<label class="form-control-label">IGV:</label>
										<input type="number" class="form-control" name="igv" id="igv" placeholder="0"
											readonly step="any" min="0.00" v-model="model.igv"
											@focus="$parent.clearErrorMsg($event)">
										<div id="igv-error" class="error invalid-feedback"></div>
									</div>
								</div>
								<div class="col-lg-4">
									<div class="form-group">
										<label class="form-control-label">Total:</label>
										<input type="number" class="form-control" name="total" id="total" readonly
											placeholder="0" step="any" min="0.00" v-model="model.total"
											@focus="$parent.clearErrorMsg($event)">
										<div id="total-error" class="error invalid-feedback"></div>
									</div>
								</div>
							</div>
							<div class="modal-footer">
								<button type="submit" class="btn btn-success">
									Agregar item
								</button>
								<button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
							</div>
						</div>
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
		units: {
			type: Array,
			default: ''
		},
		value_types: {
			type: Array,
			default: ''
		},
		url: {
			type: String,
			default: ''
		},
	},
	data() {
		return {
			model: {
				concept: '',
				unit_id: '',
				unit_name: '',
				quantity: 0,
				price_igv: 0,
				value_type_id: '1',
				sale_value: 0,
				igv: 0,
				total: 0,
			},
		}
	},
	watch: {
		'model.quantity': function (val) {
			this.calculateValues(val, this.model.price_igv, this.model.value_type_id);
		},
		'model.price_igv': function (val) {
			this.calculateValues(this.model.quantity, val, this.model.value_type_id);
		},
		'model.value_type_id': function (val) {
			this.calculateValues(this.model.quantity, this.model.price_igv, val);
		},
	},
	computed: {

	},
	created() {
		EventBus.$on('open_modal', function () {
			EventBus.$emit('loading', true);

			this.model = {
				concept: '',
				unit_id: '',
				unit_name: '',
				quantity: 0,
				price_igv: 0,
				value_type_id: '1',
				value_type_name: '',
				sale_value: 0,
				igv: 0,
				total: 0,
			};

			$('#modal').modal('show');
			EventBus.$emit('loading', false);
		}.bind(this));
	},
	mounted() {

	},
	methods: {
		calculateValues: function (quantity, price_igv, value_type_id) {
			let total = accounting.toFixed(quantity * price_igv, 2);
			let igv_percentage = (18 / 100) + 1;
			let sale_value = accounting.toFixed(total / igv_percentage, 4);
			let igv = accounting.toFixed(total - sale_value, 2);

			if (value_type_id == 2 || value_type_id == 3) {
				this.model.sale_value = total;
				this.model.igv = 0;
				this.model.total = total;
			} else {
				this.model.sale_value = sale_value;
				this.model.igv = igv;
				this.model.total = total;
			}
		},
		sendFormSale: function () {

			if (this.model.concept.trim() == "") {
				Swal.fire({
					title: "Error",
					text: "Debe seleccionar el concepto",
					type: "error",
					heightAuto: false,
				})
				return;
			} else if (this.model.unit_id == "") {
				Swal.fire({
					title: "Error",
					text: "Debe seleccionar la Unidad de Medida",
					type: "error",
					heightAuto: false,
				})
				return;
			} else if (this.model.quantity == "") {
				Swal.fire({
					title: "Error",
					text: "Debe digitar la cantidad",
					type: "error",
					heightAuto: false,
				})
				return;
			} else if (this.model.price_igv == "") {
				Swal.fire({
					title: "Error",
					text: "Debe digitar el Precio Unitario",
					type: "error",
					heightAuto: false,
				})
				return;
			}

			let unit = this.units.find(element => element.id == this.model.unit_id);
			this.model.unit_name = unit ? unit.name : '';

			let value_type = this.value_types.find(element => element.id == this.model.value_type_id);
			this.model.value_type_name = value_type ? value_type.name : '';


			EventBus.$emit('sendFormSale', this.model);
			$('#modal').modal('hide');
		},
	}
};
</script>