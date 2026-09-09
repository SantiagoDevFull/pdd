@extends('backend.templates.app')

@section('title', 'Casa del Gas')
@section('subtitle', 'Venta por Canales Resumido')

@section('content')
	<liquidation-channel-total-casa-gas-report-form
		:business_units = "{{ $business_units }}"
		:client_sectors = "{{ $client_sectors }}"
		:current_date = "'{{ $current_date }}'"
		:url = "'{{ route('dashboard.report_casa_gas.liquidations_channel_total.validate_form') }}'"
		:url_get_clients = "'{{ route('dashboard.report_casa_gas.liquidations_channel_total.get_clients') }}'"
	></liquidation-channel-total-casa-gas-report-form>
	
	<liquidation-channel-total-casa-gas-report-table
		:url = "'{{ route('dashboard.report_casa_gas.liquidations_channel_total.list') }}'"
	></liquidation-channel-total-casa-gas-report-table>

	<loading></loading>
@endsection