@extends('backend.templates.app')

@section('body-class', 'kt-header--fixed kt-header-mobile--fixed')
@section('title', 'Facturación')
@section('subtitle', 'EFACT ENVÍO OSE CORDIA')

@section('content')
    <efact-billing-ose-form 
        :companies="{{ $companies }}" 
        :voucher_types="{{ $voucher_types }}"
        :url="'{{ route('dashboard.api.cordia_efact.validate_voucher_form') }}'"
        :user_name="'{{ $user_name }}'">
    </efact-billing-ose-form>

    <efact-billing-ose-table 
        :url_list="'{{ route('dashboard.send_efact.list') }}'"
        :url_get_vouchers_for_table="'{{ route('dashboard.api.cordia_efact.get_vouchers_for_table') }}'"
        :url_send_voucher="'{{ route('dashboard.api.cordia_efact.send_voucher') }}'"
        :url_get_voucher_detail="'{{ route('dashboard.api.cordia_efact.get_voucher_detail') }}'"
        :user_name="'{{ $user_name }}'">
    </efact-billing-ose-table>

    <efact-billing-ose-table-modal></efact-billing-ose-table-modal>

    <loading></loading>
@endsection
