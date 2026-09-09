@extends('backend.templates.app')

@section('body-class', 'kt-header--fixed kt-header-mobile--fixed')
@section('title', 'Reportes')
@section('subtitle', 'Consolidado de Cobranzas')

@section('content')
	<collection-total-report-form
		:companies = "{{ $companies }}"
		:max_datetime = "'{{ $max_datetime }}'"
		:warehouse_document_types = "{{ $warehouse_document_types }}"
		:business_units = "{{ $business_units }}"
		:url = "'{{ route('dashboard.report.collection_total_report.validate_form') }}'"
		:url_get_clients = "'{{ route('dashboard.report.collection_total_report.get_clients') }}'"
	></collection-total-report-form>

	<collection-total-report-table
		:url = "'{{ route('dashboard.report.collection_total_report.list') }}'"
	></collection-total-report-table>

	<loading></loading>
@endsection