@extends('backend.templates.app')

@section('body-class', '')
@section('title', 'Reportes')
@if ( Route::currentRouteName() === 'dashboard.report.movements_final' )
	@section('subtitle', 'Reporte de Guías')
@endif

@if ( Route::currentRouteName() === 'dashboard.report.movements_register_valued' )
	@section('subtitle', 'Movimiento de Existencias Valorizado')
@endif

@section('content')
	<movements-final-report-form
		:movement_classes = "{{ $movement_classes }}"
		:movement_types = "{{ $movement_types }}"
		:warehouse_types = "{{ $warehouse_types }}"
		:companies = "{{ $companies }}"
		:current_date = "'{{ $current_date }}'"
		:warehouse_account_types = "{{ $warehouse_account_types }}"
		:warehouse_document_types = "{{ $warehouse_document_types }}"
		:url = "'{{ route('dashboard.report.movements_final.validate_form') }}'"
		:url_get_accounts = "'{{ route('dashboard.report.movements_final.get_accounts') }}'"
	></movements-final-report-form>

	@if ( Route::currentRouteName() === 'dashboard.report.movements_final' )
		<movements-final-report-table
			:url = "'{{ route('dashboard.report.movements_final.list') }}'"
			:url_detail = "'{{ route('dashboard.report.movements_final.detail') }}'"
		></movements-final-report-table>
	@endif


	<movements-final-report-modal
		:url = "'{{ route('dashboard.report.movements_final.update') }}'"
	></movements-final-report-modal>

	<loading></loading>
@endsection
