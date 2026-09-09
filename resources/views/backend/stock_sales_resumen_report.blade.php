@extends('backend.templates.app')

@section('body-class', '')
@section('title', 'Reportes')
@if ( Route::currentRouteName() === 'dashboard.report.stock_sales_resumen' )
	@section('subtitle', 'Reporte Resumido de Compras GLP')
@endif

@if ( Route::currentRouteName() === 'dashboard.report.stock_seek_resumen_valued' )
	@section('subtitle', 'Movimiento de Existencias Valorizado')
@endif

@section('content')
	<stock-sales-resumen-report-form
		
		:companies = "{{ $companies }}"
		:current_date = "'{{ $current_date }}'"
		
		:url = "'{{ route('dashboard.report.stock_sales_resumen.validate_form') }}'"
		
	></stock-sales-resumen-report-form>

	@if ( Route::currentRouteName() === 'dashboard.report.stock_sales_resumen' )
		<stock-sales-resumen-report-table
			:url = "'{{ route('dashboard.report.stock_sales_resumen.list') }}'"
			:url_detail = "'{{ route('dashboard.report.stock_sales_resumen.detail') }}'"
		
		></stock-sales-resumen-report-table>
	@endif

	@if ( Route::currentRouteName() === 'dashboard.report.stock_seek_resumen_valued' )
		<stock-sales-resumen-report-valued-table
			:url = "'{{ route('dashboard.report.stock_seek_resumen_valued.list') }}'"
		></stock-sales-resumen-valued-table>
	@endif

	<stock-sales-resumen-report-modal
		:url = "'{{ route('dashboard.report.stock_sales_resumen.update') }}'"
	></stock-sales-resumen-report-modal>



	<loading></loading>
@endsection