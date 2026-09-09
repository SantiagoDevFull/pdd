@extends('backend.templates.app')

@section('body-class', 'kt-header--fixed kt-header-mobile--fixed')
@section('title', 'Facturación')
@section('subtitle', 'Registro de Vouchers')

@section('content')
	<register-voucher
		:companies = "{{ $companies }}"
		:routes = "{{ $routes }}"
		:max_datetime = "'{{ $max_datetime }}'"
		:url = "'{{ route('dashboard.voucher.register_voucher.store') }}'"
		:url_get_bank_accounts = "'{{ route('dashboard.voucher.register_voucher.get_bank_accounts') }}'"
	></register-voucher>

	<register-voucher-table
		:url_list = "'{{ route('dashboard.voucher.register_voucher.list') }}'"
		:url_delete = "'{{ route('dashboard.voucher.register_voucher.delete') }}'"
		:url_detail = "'{{ route('dashboard.voucher.register_voucher.detail') }}'"
	></register-voucher-table>

	<register-voucher-modal
		:companies = "{{ $companies }}"
		:routes = "{{ $routes }}"
		:max_datetime = "'{{ $max_datetime }}'"
		:url_get_bank_accounts = "'{{ route('dashboard.voucher.register_voucher.get_bank_accounts') }}'"
		:url_update = "'{{ route('dashboard.voucher.register_voucher.update') }}'"
	></register-voucher-modal>

	<loading></loading>
@endsection
