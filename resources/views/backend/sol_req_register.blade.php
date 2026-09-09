@extends('backend.templates.app')

@section('body-class', '')
@section('title', 'Operaciones')
@section('subtitle', 'Solicitud de Requerimientos')

@section('content')
	<req-register-form
		:companies = "{{ $companies }}"
		:type_reqs = "{{ $type_reqs }}"
        :adm_payments = "{{ $adm_payments }}"
        :url_get_areas = "'{{ route('dashboard.operations.sol_req_register.get_areas') }}'"
        :url_get_types = "'{{ route('dashboard.operations.sol_req_register.get_types') }}'"
        :url_get_sub_types = "'{{ route('dashboard.operations.sol_req_register.get_sub_types') }}'"
        :url = "'{{ route('dashboard.operations.sol_req_register.store') }}'"
        :url_get_presupuesto = "'{{ route('dashboard.operations.sol_req_register.get_presupuesto') }}'"
        :url_get_costos = "'{{ route('dashboard.operations.sol_req_register.get_costos') }}'"
        :url_get_num_mov = "'{{ route('dashboard.operations.sol_req_register.get_num_mov') }}'"
	></req-register-form>

    <req-register-modal
        :currencies = "{{ $currencies }}"
        :voucher_types = "{{ $voucher_types }}"
        :current_date = "'{{ $current_date }}'"
        :url_get_costo_secs = "'{{ route('dashboard.operations.sol_req_register.get_costo_secs') }}'"
    ></req-register-modal>

	<loading></loading>
@endsection