@extends('backend.templates.app')

@section('body-class', 'kt-header--fixed kt-header-mobile--fixed')
@section('title', 'Facturación')
@section('subtitle', 'Envío de Boletas como Resúmenes PUNTO')

@section('content')
    <boleta-resumen-ose-form 
        :companies="{{ $companies }}" :voucher_types="{{ $voucher_types }}"
        :url="'{{ route('dashboard.boleta.validate_voucher_form') }}'"
        :user_name="'{{ $user_name }}'">
    </boleta-resumen-ose-form>

    <boleta-resumen-ose-table 
        :url_list="'{{ route('dashboard.boleta.list') }}'"
        :url_get_vouchers_for_table="'{{ route('dashboard.boleta.get_vouchers_for_table') }}'"
        :url_send_voucher="'{{ route('dashboard.boleta.send_voucher') }}'"
        :url_get_voucher_detail="'{{ route('dashboard.boleta.get_voucher_detail') }}'"
        :user_name="'{{ $user_name }}'">
    </boleta-resumen-ose-table>

    <boleta-resumen-ose-table-modal></boleta-resumen-ose-table-modal>

    <loading></loading>
@endsection
