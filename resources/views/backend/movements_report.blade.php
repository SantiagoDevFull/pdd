@extends('backend.templates.app')

@section('body-class', '')
@section('title', 'Reportes')
@if ( Route::currentRouteName() === 'dashboard.report.movements' )
	@section('subtitle', 'Movimiento de Existencias')
@endif

@if ( Route::currentRouteName() === 'dashboard.report.movements_valued' )
	@section('subtitle', 'Movimiento de Existencias Valorizado')
@endif

@section('content')
	<movements-report-form
		:movement_classes = "{{ $movement_classes }}"
		:movement_types = "{{ $movement_types }}"
		:warehouse_types = "{{ $warehouse_types }}"
		:companies = "{{ $companies }}"
		:current_date = "'{{ $current_date }}'"
		:warehouse_account_types = "{{ $warehouse_account_types }}"
		:warehouse_document_types = "{{ $warehouse_document_types }}"
		:url = "'{{ route('dashboard.report.movements.validate_form') }}'"
		:url_get_accounts = "'{{ route('dashboard.report.movements.get_account') }}'"
	></movements-report-form>

	@if ( Route::currentRouteName() === 'dashboard.report.movements' )
		<movements-report-table
			:url = "'{{ route('dashboard.report.movements.list') }}'"
			:url_detail = "'{{ route('dashboard.report.movements.detail') }}'"
		></stock-log-register-report-table>
	@endif

	@if ( Route::currentRouteName() === 'dashboard.report.movements_valued' )
		<movements-report-valued-table
			:url = "'{{ route('dashboard.report.movements.list') }}'"
		></movements-report-valued-table>
	@endif

	<movements-report-modal
		:url = "'{{ route('dashboard.report.movements.update') }}'"
	></movements-report-modal>

	<loading></loading>
@endsection