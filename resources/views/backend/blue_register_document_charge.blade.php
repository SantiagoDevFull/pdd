@extends('backend.templates.app')

@section('body-class', 'kt-header--fixed kt-header-mobile--fixed')
@section('title', 'Blue Line - Administración')
@section('subtitle', 'Generación de Documentos')

@section('content')
	<blue-register-document-charge-first-step
		:companies = "{{ $companies }}"
		:warehouse_document_types = "{{ $warehouse_document_types }}"
		:url = "'{{ route('dashboard.voucher.blue_register_document_charge.get_voucher') }}'"
	></blue-register-document-charge-first-step>

	<blue-register-document-charge-second-step
		:min_sale_date = "'{{ $min_sale_date }}'"
		:max_sale_date = "'{{ $max_sale_date }}'"
		:payments = "{{ $payments }}"
		:min_expiry_date = "'{{ $min_expiry_date }}'"
		:max_expiry_date = "'{{ $max_expiry_date }}'"
		:currencies = "{{ $currencies }}"
		:business_units = "{{ $business_units }}"
		:credit_note_reasons = "{{ $credit_note_reasons }}"
		:warehouse_document_types = "{{ $warehouse_document_types }}"
		:url = "'{{ route('dashboard.voucher.blue_register_document_charge.validate_second_step') }}'"
		:url_get_clients = "'{{ route('dashboard.voucher.blue_register_document_charge.get_clients') }}'"
	></blue-register-document-charge-second-step>

	<blue-register-document-charge-third-step
		:url = "'{{ route('dashboard.voucher.blue_register_document_charge.store') }}'"
	></blue-register-document-charge-third-step>

	<blue-register-document-charge-third-step-modal
		:units = "{{ $units }}"
		:value_types = "{{ $value_types }}"
		:url = "'{{ route('dashboard.voucher.blue_register_document_charge.validate_third_step') }}'"
	></blue-register-document-charge-third-step-modal>

	<loading></loading>
	{{-- <billing-ose-table-modal></billing-ose-table-modal> --}}
@endsection