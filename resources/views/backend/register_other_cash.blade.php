@extends('backend.templates.app')

@section('body-class', 'kt-header--fixed kt-header-mobile--fixed')
@section('title', 'Facturación')
@section('subtitle', 'Registro de Ingresos')

@section('content')
	<register-other-cash-first-step
		:companies = "{{ $companies }}"
		:url = "'{{ route('dashboard.voucher.register_other_cash.validate_first_step') }}'"
		:url_get_accounts = "'{{ route('dashboard.voucher.register_other_cash.get_accounts') }}'"
		:currencies = "{{ $currencies }}"
		:min_sale_date = "'{{ $min_sale_date }}'"
		:max_sale_date = "'{{ $max_sale_date }}'"
	></register-other-cash-first-step>

	<register-other-cash-third-step
	></register-other-cash-third-step>

	<register-other-cash-third-step-modal
		:units = "{{ $units }}"
		:value_types = "{{ $value_types }}"
		:url = "'{{ route('dashboard.voucher.register_other_cash.validate_third_step') }}'"
	></register-other-cash-third-step-modal>

	<register-other-cash-liquidation-table
		:url_store = "'{{ route('dashboard.voucher.register_other_cash.store') }}'"
	></register-other-cash-liquidation-table>

	<register-other-cash-liquidation-modal
	    :payments = "{{ $payments }}"
	    :currencies = "{{ $currencies }}"
		:url_get_payment_method = "'{{ route('dashboard.voucher.list.register_other_cash.method') }}'"
		:url_get_bank_accounts = "'{{ route('dashboard.voucher.list.register_other_cash.bank_account') }}'"
		:deposits = "{{ $deposits }}"
	></register-other-cash-liquidation-modal>

	<loading></loading>

@endsection