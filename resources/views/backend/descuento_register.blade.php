@extends('backend.templates.app')

@section('body-class', 'kt-header--fixed kt-header-mobile--fixed')
@section('title', 'Facturación')
@section('subtitle', 'Registro de Depósitos Pendientes')

@section('content')
	<descuento-register-first-step
		:companies = "{{ $companies }}"
		:url = "'{{ route('dashboard.voucher.descuento_register.validate_first_step') }}'"
		:url_get_clients = "'{{ route('dashboard.voucher.descuento_register.get_clients') }}'"
	></descuento-register-first-step>

	<descuento-register-second-step
		:deposits = "{{ $deposits }}"
		:max_sale_date = "'{{ $max_sale_date }}'"
		:payment_methods = "{{ $payment_methods }}"
		:currencies = "{{ $currencies }}"
		:bank_accounts = "{{ $bank_accounts }}"
		:warehouse_document_types = "{{ $warehouse_document_types }}"
		:url = "'{{ route('dashboard.voucher.descuento_register.validate_second_step') }}'"
	></descuento-register-second-step>

	<descuento-register-third-step
		:url = "'{{ route('dashboard.voucher.descuento_register.get_sales') }}'"
		:url_store = "'{{ route('dashboard.voucher.descuento_register.store') }}'"
	></descuento-register-third-step>

	<descuento-register-third-step-modal
	></descuento-register-third-step-modal>

	<loading></loading>
	{{-- <billing-ose-table-modal></billing-ose-table-modal> --}}
@endsection
