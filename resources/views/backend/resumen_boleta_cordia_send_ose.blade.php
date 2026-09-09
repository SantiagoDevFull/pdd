@extends('backend.templates.app')

@section('body-class', 'kt-header--fixed kt-header-mobile--fixed')
@section('title', 'Facturación')
@section('subtitle', 'Envío de Boletas como Resúmenes CORDIA')

@section('content')
    <boleta-resumen-cordia-ose-form :companies="{{ $companies }}" :voucher_types="{{ $voucher_types }}"
        :url="'{{ route('dashboard.boleta.cordia.validate_voucher_form') }}'"
        :user_name="'{{ $user_name }}'">
    </boleta-resumen-cordia-ose-form>

    <boleta-resumen-cordia-ose-table :url_list="'{{ route('dashboard.boleta.cordia.list') }}'"
        :url_get_vouchers_for_table="'{{ route('dashboard.boleta.cordia.get_vouchers_for_table') }}'"
        :url_send_voucher="'{{ route('dashboard.boleta.cordia.send_voucher') }}'"
        :url_get_voucher_detail="'{{ route('dashboard.boleta.cordia.get_voucher_detail') }}'"
        :user_name="'{{ $user_name }}'">
    </boleta-resumen-cordia-ose-table>

    <boleta-resumen-cordia-ose-table-modal>

    </boleta-resumen-cordia-ose-table-modal>

    <loading></loading>
@endsection
