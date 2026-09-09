@extends('backend.templates.app')

@section('title', 'Reportes')
@section('subtitle', 'Reporte Consolidado de Ventas')

@section('content')
	<contabilidad-detail-total-report-form
		:companies = "{{ $companies }}"
		:current_date = "'{{ $current_date }}'"
		:url = "'{{ route('dashboard.report.contabilidad_detail_total.validate_form') }}'"
		:url_list = "'{{ route('dashboard.report.contabilidad_detail_total.list') }}'"
		:url_get_clients = "'{{ route('dashboard.report.contabilidad_detail_total.get_clients') }}'"
	></contabilidad-detail-total-report-form>
	
	<contabilidad-detail-total-report-table
		:url = "'{{ route('dashboard.report.contabilidad_detail_total.list') }}'"
	></contabilidad-detail-total-report-table>

	<loading></loading>
@endsection