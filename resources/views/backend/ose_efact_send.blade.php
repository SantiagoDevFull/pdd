@extends('backend.templates.app')

@section('body-class', 'kt-header--fixed kt-header-mobile--fixed')
@section('title', 'Facturación')
@section('subtitle', 'Envío OSE API')

@section('content')
	<api-billing-ose-form
		:companies = "{{ $companies }}"
		:voucher_types = "{{ $voucher_types }}"
		:url = "'{{ route('dashboard.api.send_efact.validate_voucher_form') }}'"
		:user_name = "'{{ $user_name }}'"
	></api-billing-ose-form>

	<api-billing-ose-table
		:url_list = "'{{ route('dashboard.send_efact.list') }}'"
		:url_get_vouchers_for_table = "'{{ route('dashboard.api.send_efact.get_vouchers_for_table') }}'"
		:url_send_voucher = "'{{ route('dashboard.api.send_efact.send_voucher') }}'"
		:url_get_voucher_detail = "'{{ route('dashboard.api.send_efact.get_voucher_detail') }}'"
		:user_name = "'{{ $user_name }}'"
	></api-billing-ose-table>

	<loading></loading>
	<api-billing-ose-table-modal></api-billing-ose-table-modal>
@endsection