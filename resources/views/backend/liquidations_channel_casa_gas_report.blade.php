@extends('backend.templates.app')

@section('title', 'Casa del Gas')
@section('subtitle', 'Venta por Canales')

@section('content')
	<liquidation-channel-casa-gas-report-form
		:companies = "{{ $companies }}"
		:business_units = "{{ $business_units }}"
		:client_sectors = "{{ $client_sectors }}"
		:current_date = "'{{ $current_date }}'"
		:url = "'{{ route('dashboard.report_casa_gas.liquidations_channel.validate_form') }}'"
		:url_get_clients = "'{{ route('dashboard.report_casa_gas.liquidations_channel.get_clients') }}'"
		:url_export = "'{{ route('dashboard.report_casa_gas.liquidations_channel.list') }}'"
	></liquidation-channel-casa-gas-report-form>

	<loading></loading>
@endsection