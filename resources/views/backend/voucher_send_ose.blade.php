@extends('backend.templates.app')

@section('body-class', 'kt-header--fixed kt-header-mobile--fixed')
@section('title', 'Facturación')
@section('subtitle', 'Envío OSE')

@section('content')
	<send-ose-form
		:companies = "{{ $companies }}"
		:voucher_types = "{{ $voucher_types }}"
		:url = "'{{ route('dashboard.voucher.send_ose.validate_voucher_form') }}'"
		:user_name = "'{{ $user_name }}'"
	></send-ose-form>

	<send-ose-table
		:url_list = "'{{ route('dashboard.voucher.send_ose.list') }}'"
		:url_get_vouchers_for_table = "'{{ route('dashboard.voucher.send_ose.get_vouchers_for_table') }}'"
		:url_send_voucher = "'{{ route('dashboard.voucher.send_ose.send_voucher') }}'"
		:url_get_voucher_detail = "'{{ route('dashboard.voucher.send_ose.get_voucher_detail') }}'"
		:user_name = "'{{ $user_name }}'"
	></send-ose-table>

	<loading></loading>
	<send-ose-table-modal></send-ose-table-modal>
@endsection