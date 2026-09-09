@extends('backend.templates.app')

@section('body-class', '')
@section('title', 'Operaciones')
@section('subtitle', 'Subida de Documentos')

@section('content')
	<liq-rendir-form
		:num_movs = "{{ $num_movs }}"
		:companies = "{{ $companies }}"
		:type_reqs = "{{ $type_reqs }}"
        :adm_payments = "{{ $adm_payments }}"
        :area_alls = "{{ $area_alls }}"
        :types = "{{ $types }}"
        :sub_types = "{{ $sub_types }}"
        :url = "'{{ route('dashboard.operations.liq_rendir.store') }}'"
        :url_get_presupuesto = "'{{ route('dashboard.operations.liq_rendir.get_presupuesto') }}'"
        :url_get_costos = "'{{ route('dashboard.operations.liq_rendir.get_costos') }}'"
        :url_get_aplication = "'{{ route('dashboard.operations.liq_rendir.get_aplication') }}'"
	></liq-rendir-form>

    <liq-rendir-modal
        :currencies = "{{ $currencies }}"
        :voucher_types = "{{ $voucher_types }}"
        :current_date = "'{{ $current_date }}'"
        :url_get_costo_secs = "'{{ route('dashboard.operations.liq_rendir.get_costo_secs') }}'"
    ></liq-rendir-modal>

	<loading></loading>
@endsection