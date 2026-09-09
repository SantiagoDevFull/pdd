@extends('backend.templates.app')

@section('body-class', 'kt-header--fixed kt-header-mobile--fixed')
@section('title', 'Reportes')
@section('subtitle', 'Relación de Egresos de caja')

@section('content')
	<un-document-report-form
		:companies = "{{ $companies }}"
		:max_datetime = "'{{ $max_datetime }}'"
		:warehouse_document_types = "{{ $warehouse_document_types }}"
		:business_units = "{{ $business_units }}"
		:url = "'{{ route('dashboard.voucher.un_document_report.validate_form') }}'"
		:url_get_clients = "'{{ route('dashboard.voucher.un_document_report.get_clients') }}'"
	></un-document-report-form>

	<un-document-report-table
		:url = "'{{ route('dashboard.voucher.un_document_report.list') }}'"
	></un-document-report-table>

	<loading></loading>
@endsection
