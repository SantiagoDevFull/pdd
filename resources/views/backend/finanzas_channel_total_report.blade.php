@extends('backend.templates.app')

@section('title', 'Finanzas')
@section('subtitle', 'Finanzas Canales Resumido')

@section('content')
	<finanzas-channel-total-report-form
        :business_units = "{{ $business_units }}"
		:current_date = "'{{ $current_date }}'"
		:url = "'{{ route('dashboard.report.finanzas_channel_total.validate_form') }}'"
		:url_get_clients = "'{{ route('dashboard.report.finanzas_channel_total.get_clients') }}'"
	></finanzas-channel-total-report-form>
	
	<finanzas-channel-total-report-table
		:url = "'{{ route('dashboard.report.finanzas_channel_total.list') }}'"
	></finanzas-channel-total-report-table>

	<loading></loading>
@endsection