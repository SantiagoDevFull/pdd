@extends('backend.templates.app')

@section('body-class', 'kt-header--fixed kt-header-mobile--fixed')
@section('title', 'Facturación')
@section('subtitle', 'Registro de Egresos')

@section('content')
	<register-charge-first-step
		:companies = "{{ $companies }}"
		:warehouse_document_types = "{{ $warehouse_document_types }}"
		:url = "'{{ route('dashboard.voucher.register_charge.get_voucher') }}'"
	></register-charge-first-step>

	<register-charge-second-step
		:min_sale_date = "'{{ $min_sale_date }}'"
		:max_sale_date = "'{{ $max_sale_date }}'"
		:warehouse_account_types = "{{ $warehouse_account_types }}"
		:payments = "{{ $payments }}"
		:min_expiry_date = "'{{ $min_expiry_date }}'"
		:max_expiry_date = "'{{ $max_expiry_date }}'"
		:currencies = "{{ $currencies }}"
		:business_units = "{{ $business_units }}"
		:credit_note_reasons = "{{ $credit_note_reasons }}"
		:warehouse_document_types = "{{ $warehouse_document_types }}"
		:url = "'{{ route('dashboard.voucher.register_charge.validate_second_step') }}'"	
		:url_get_accounts = "'{{ route('dashboard.voucher.register_charge.get_accounts') }}'"
	></register-charge-second-step>

	<register-charge-third-step
		:url = "'{{ route('dashboard.voucher.register_charge.store') }}'"
	></register-charge-third-step>

	<register-charge-third-step-modal
		:units = "{{ $units }}"
		:value_types = "{{ $value_types }}"
		:url = "'{{ route('dashboard.voucher.register_charge.validate_third_step') }}'"
	></register-charge-third-step-modal>

	<loading></loading>
	{{-- <billing-ose-table-modal></billing-ose-table-modal> --}}
@endsection